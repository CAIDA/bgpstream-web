set :instance, ENV['instance'] || 'dev'

puts "Deploying bgpstream-#{instance}"

set :application, "bgpstream-#{instance}"
set :domain,      "bgpstream-app-1.caida.org"
set :deploy_to,   "/db/bgp/#{application}"
set :app_path,    "app"
set :web_path,    "web"

set :repository,  "bgpstream-web-github:CAIDA/bgpstream-web.git"
set :scm,         :git
set :deploy_via,  :rsync_with_remote_cache

set :model_manager, "doctrine"

role :web,        domain                         # Your HTTP server, Apache/etc
role :app,        domain, :primary => true       # This may be the same as your `Web` server

# General config stuff
set :keep_releases,  10
#set :shared_files,      [] # these files are kept across deploys
set :shared_children,   [app_path + "/logs", web_path + "/uploads", "vendor"]
set :use_composer, true
set :composer_options, "--verbose --prefer-dist --optimize-autoloader --no-progress --no-interaction"
set :dump_assetic_assets, true
set :group_writable, false
# do not remove app_dev.php
set :clear_controllers, false
# Confirmations will not be requested from the command line.
set :interactive_mode, false# Be more verbose by uncommenting the following line
logger.level = Logger::MAX_LEVEL

# per-instance settings
set :branch, ENV['branch'] || (instance.to_s == "prod" ? "production" : "master")
set :instance_files,    [
                         "web/.htaccess",          # for app_dev.php in test
#                         "web/app_dev.php",        # for no-ip auth on test
                         "app/config/parameters.yml.dist",
                         ]

# ssh settings
set :user, "bgp"
set :use_sudo, false
ssh_options[:keys] = [File.join(ENV["HOME"], ".ssh", "id_rsa")]

# Run migrations before warming the cache
#before "symfony:cache:warmup", "symfony:doctrine:migrations:migrate"
before "symfony:cache:warmup", "symfony:doctrine:cache:clear_metadata"
before "symfony:cache:warmup", "symfony:doctrine:cache:clear_query"
before "symfony:cache:warmup", "symfony:doctrine:cache:clear_result"

# Custom(ised) tasks
namespace :deploy do
  task :mkdirs do
    run "mkdir -p #{deploy_to}/shared"
    run "mkdir -p #{deploy_to}/releases"
  end

  # Apache needs to be restarted to make sure that the APC cache is cleared.
  # This overwrites the :restart task in the parent config which is empty.
  desc "Fix ownership/permissions and restart Apache"
  task :restart, :except => { :no_release => true }, :roles => :app do
    run "mkdir -p #{deploy_to}/current/#{app_path}/cache/dev/annotations"
    run "mkdir -p #{deploy_to}/current/#{app_path}/cache/prod"
    run "chmod -fR g+w #{deploy_to}/current/#{app_path}/cache"
    run "touch #{deploy_to}/shared/#{app_path}/logs/dev.log"
    run "chmod -fR g+w #{deploy_to}/shared/#{app_path}/logs"
    run "chmod -fR g+w #{deploy_to}/shared/#{web_path}/uploads"
    puts "Ownership/permissions set"
    run "sudo service php-fpm reload"
    #run "killall php" # cause the watcher to restart
    #		puts "--> Apache successfully restarted".green
  end

  # move per-instance files into their places
  task :mv_instance_files do
    puts "--> Moving instance files".green
    instance_files.each do |file|
      run "mv #{release_path}/#{file}.#{instance} #{release_path}/#{file}"
    end
  end
end

before "deploy:update_code", "deploy:mkdirs"
before "deploy:share_childs", "deploy:mv_instance_files"
