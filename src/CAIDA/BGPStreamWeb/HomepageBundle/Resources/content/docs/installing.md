Installing BGPStream
====================

BGPStream C Library and BGPCorsaro
----------------------------------

Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa
Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa
Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa

### Dependencies

#### Required Libraries

BGPStream requires the [wandio](http://research.wand.net.nz/software/libwandio.php) library, which itself depends on several libraries:

   - bzip2 (libbz2)
   - gzip (zlib)
   - HTTP (libcurl &gt; 7.18.0)
   
On FreeBSD, the only library not installed as part of the base OS is _libcurl_ which can be found in ports at `ftp/curl`.

On Ubuntu/Debian, these libraries can be installed by running:
~~~
# apt-get install zlib1g-dev libbz2-dev libcurl4-openssl-dev
~~~

Once these dependencies are met, wandio can be installed as follows:
~~~
$ tar zxf wandio-X.X.X.tar.gz
$ cd wandio-X.X.X/
$ ./configure
$ make
# make install
~~~
__Note:__ Ensure that the last lines from `configure` show a _Yes_ result for at least zlib, bz2, and libcurl like the following:
~~~
configure: WANDIO version 1.0.3
configure: Compiled with compressed file (zlib) support: Yes
configure: Compiled with compressed file (bz2) support: Yes
configure: Compiled with compressed file (lzo write only) support: No
configure: Compiled with compressed file (lzma) support: No
configure: Compiled with http read (libcurl) support: Yes
~~~

Alternatively, FreeBSD users can install wandio from ports (`devel/wandio`). Distributions for other operating systems will be released shortly.


#### Optional Libraries

Optional dependencies are:

- [sqlite3](https://www.sqlite.org/) C library (required for the SQLite data interface plugin)
  - available in FreeBSD Ports as `databases/sqlite3`, and in Ubuntu/Debian apt repositories as `libsqlite3-dev`

### Building from source

BGP Stream is written in C and should compile with any ANSI compliant C Compiler
which supports the C99 standard. We have tested BGPStream on FreeBSD, Linux and
Mac OSX using a variety of common compiler versions. Please open an issue on our
[GitHub page](https://github.com/caida/bgpstream/issues) with any problems you encounter.

First, obtain a copy of BGPStream from the
[download page]({{ path('caida_bgpstream_web_homepage', {'page': 'download'}) }}).

Then, once the dependencies described above have been satisfied, BGPStream can
be built by running:
~~~
$ tar zxf bgpstream-1.0.0.tar.gz
$ cd bgpstream-1.0.0
$ ./configure
$ make
# make install
~~~

If required libraries are not in the system library paths, specify their paths when running `configure` as follows:
~~~
$ ./configure CPPFLAGS='-I/path/to/deps/include' LDFLAGS='-L/path/to/deps/lib'
~~~

### Installing from Platform-specific package

xxx

PyBGPStream
------------

pybgpstream is still in active development so is not available in PyPI, pip,
etc.

It can be downloaded `here 
<http://www.caida.org/~chiara/bgpstream-doc/_pybgpstream-1.0.tar.gz>`_.

To install, you will first need to have built and installed
bgpstream. Then, you will need to unzip the pybgpstream distribution and do
something like this:

    python setup.py build
    python setup.py install

Use `python setup.py install --user` to install the library in your home directory.

To check if pybgpstream is installed correctly run the tutorial print script:

~~~
$ cd examples
$ python tutorial_print.py
valid singlefile_ds.singlefile_ds 1427846570
W 202.249.2.185 25152 W {'prefix': '144.104.37.0/24'}
valid singlefile_ds.singlefile_ds 1427846573
A 2001:200:0:fe00::6249:0 25152 A {'next-hop': '2001:200:0:fe00::9c1:0', 'prefix': '2a00:bdc0:e004::/48', 'as-path': '25152 2497 6939 47541 28709'}
  ...
~~~

The tutorial print script reads the MRT updates file
provided with the distribution ris.rrc06.updates.1427846400.gz and
outputs part of the file based on filters.

### Tips
If  libbgpstream is not installed into a directory where Python can
find it, you can do something like:

    python setup.py build_ext --include-dirs=$HOME/testing/bgpstream/include --library-dirs=$HOME/testing/bgpstream/lib
    python setup.py install [--user]

If you receive this error when running a script:

    ImportError: libbgpstream.so.1: cannot open shared object file: No such file or directory

then add bgpstream library path to the LD_LIBRARY PATH:

    $ export LD_LIBRARY_PATH=$HOME/testing/bgpstream/lib:$LD_LIBRARY_PATH
    $python a_bgpstream_script.py
