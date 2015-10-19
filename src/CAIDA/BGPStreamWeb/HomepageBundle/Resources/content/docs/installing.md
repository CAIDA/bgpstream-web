Installing BGPStream
====================

BGPStream
---------

The BGPStream C library (_libBGPStream_) is the core of the BGPStream framework.
It is used by [PyBGPStream](@@maybe-overview) (see install instructions below), as well as the
[BGPReader](@@maybe-overview) and [BGPCorsaro](@@maybe-overview) tools (installed with the library).

### Installing from Platform-specific package

BGPStream can be installed using platform-specific packages. See the
[download page]({{ path('caida_bgpstream_web_homepage', {'page': 'download'}) }})
for a list of operating systems currently supported (this is a work-in-progress).

#### FreeBSD <small>Coming Soon...</small>

BGPStream is available from FreeBSD ports at `net/bgpstream`.

The port will install all required dependencies (usually just _libcurl_ and _libwandio_).

#### Ubuntu/Debian <small>Coming Soon...</small>

Install BGPStream by running:
~~~
# apt-get install bgpstream-dev bgpstream-tools
~~~

If you are planning to only use the PyBGPStream Python bindings, then you need
not install the `bgpstream-tools` package.

### Installing from source

#### Dependencies

##### Required Libraries

 - [wandio](http://research.wand.net.nz/software/libwandio.php)
 
However, several libraries are required before installing wandio:

   - bzip2 (libbz2)
   - gzip (zlib)
   - HTTP (libcurl &gt; 7.18.0)
   
On FreeBSD, the only wandio dependency not installed as part of the base OS is _libcurl_ which can be found in ports at `ftp/curl`.

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

On Ubuntu/Debian you will need to run `ldconfig` after the `make install` step.

Alternatively, FreeBSD users can install wandio from ports (`devel/wandio`). Distributions for other operating systems will be released shortly.


##### Optional Libraries

 - [sqlite3](https://www.sqlite.org/), required for the SQLite data interface<br>
Available in FreeBSD Ports as `databases/sqlite3`, and in Ubuntu/Debian apt repositories as `libsqlite3-dev`

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

On Ubuntu/Debian you will need to run `ldconfig` after the `make install` step.

PyBGPStream
------------

### Installing from PyPI

The simplest and recommended way to install PyBGPStream is from PyPI using `pip`:
~~~
$ pip install pybgpstream
~~~

__Note:__ The BGPStream C library _must_ be installed prior to installing pybgpstream.
If you see an error like the following during installation, then you may not
have libBGPStream installed (see above for instructions).
~~~
In file included from src/_pybgpstream_module.c:27:
src/_pybgpstream_bgprecord.h:29:10: fatal error: 'bgpstream.h' file not found
~~~

If you have installed libBGPStream to a non-standard location
(e.g., `/path/to/libbgpstream`), then you will need to provide more information to
pip as follows:
~~~
pip install \
    --global-option build_ext \
    --global-option '--include-dir /path/to/libbgpstream/include' \
    --global-option '--library-dir /path/to/libbgpstream/lib' \
    bgpstream
~~~
In this case you may also have to tell Python where to find libBGPStream at run time like so:
~~~
LD_LIBRARY_PATH=/path/to/libbgpstream/lib python my_bgpstream_app.py
~~~

### Installing from source

If you prefer to install PyBGPStream from source, then first obtain a copy of
the tarball from the
[download page](({{ path('caida_bgpstream_web_homepage', {'page': 'download'}) }})).

Then, run the following commands:
~~~
$ tar zxf pybgpstream-1.0.0.tar.gz
$ cd pybgpstream-1.0.0
$ python setup.py build_ext
# python setup.py install
~~~

Use `python setup.py install --user` to install the library in your home directory.

If libBGPStream has been installed in a non-standard location, then the
`build_ext` step will fail with an error similar to:
~~~
error goes here
~~~
In this case, provide the path to the library as follows:
~~~
$ python setup.py build_ext --include-dirs=/path/to/libbgpstream/include \
                            --library-dirs=/path/to/libbgpstream/lib
~~~
In this case you may also have to tell Python where to find libBGPStream at run time like so:
~~~
LD_LIBRARY_PATH=/path/to/libbgpstream/lib python my_bgpstream_app.py
~~~

### Testing the installation

To check if pybgpstream is installed correctly run the tutorial print script:
~~~
$ cd examples
$ ./tutorial_print.py
valid ris.rrc06 1427846570
	W 202.249.2.185 25152 W {'prefix': '144.104.37.0/24'}
valid ris.rrc06 1427846573
	A 2001:200:0:fe00::6249:0 25152 A {'next-hop': '2001:200:0:fe00::9c1:0', 'prefix': '2a00:bdc0:e004::/48', 'as-path': '25152 2497 6939 47541 28709'}
...
~~~
