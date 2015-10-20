BGPCorsaro
==========

@@ things things things things things

Usage
-----

~~~
usage: bgpcorsaro -w <start>[,<end>] -O outfile -B back-end [<options>]
Available options are:
   -d <interface> use the given bgpstream data interface to find available data
                   available data interfaces are:
       broker       Retrieve metadata information from the BGPStream Broker service (default)
       singlefile   Read a single mrt data file (a RIB and/or an update)
       csvfile      Retrieve metadata information from a csv file
   -o <option-name,option-value>*
                  set an option for the current data interface.
                  use '-o ?' to get a list of available options for the current
                  data interface. (data interface can be selected using -d)
   -p <project>   process records from only the given project (routeviews, ris)*
   -c <collector> process records from only the given collector*
   -t <type>      process records with only the given type (ribs, updates)*
   -w <start>[,<end>]
                  process records within the given time window
                    (omitting the end parameter enables live mode)*
   -P <period>    process a rib files every <period> seconds (bgp time)
   -l             enable live mode (make blocking requests for BGP records)
                  allows bgpcorsaro to be used to process data in real-time

   -i <interval>  distribution interval in seconds (default: 0)
   -a             align the end time of the first interval
   -g <gap-limit> maximum allowed gap between packets (0 is no limit) (default: 60)
   -L             disable logging to a file

   -x <plugin>    enable the given plugin (default: all)*
                   available plugins:
                    - pfxmonitor
                    - pacifier
                   use -p "<plugin_name> -?" to see plugin options
   -n <name>      monitor name (default: gibi.caida.org)
   -O <outfile>   use <outfile> as a template for file names.
                   - %X => plugin name
                   - %N => monitor name
                   - see man strftime(3) for more options
   -r <intervals> rotate output files after n intervals
   -R <intervals> rotate bgpcorsaro meta files after n intervals

   -h             print this help menu
* denotes an option that can be given multiple times
~~~

Plugins
-------

more things things things

Example
-------

even more things things things
