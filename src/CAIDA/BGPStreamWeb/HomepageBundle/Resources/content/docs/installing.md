Installing BGPStream
====================

<h1 class="text-danger">TODO: UPDATE THIS DOCUMENT</h1>

BGPStream C Library
-------------------

Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa
Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa
Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa

### Dependencies

BGP Stream is written in C and should compile with any ANSI compliant C Compiler
which supports the C99 standard. Please email bgpstream-info@caida.org with any
issues.

Building BGP Stream requires:
- [libtrace](http://research.wand.net.nz/software/libtrace.php)
version 3.0.14 or higher
    - a working version of libtrace is available at http://research.wand.net.nz/software/libtrace/libtrace-3.0.22.tar.bz2 

- [sqlite3](https://www.sqlite.org/) c library
     - a working version of sqlite3 is available at https://www.sqlite.org/2015/sqlite-autoconf-3081002.tar.gz

- [mysql](https://www.mysql.com/) c library, version 5.5  or higher
  - a working version of mysql is available at http://downloads.mysql.com/archives/get/file/mysql-connector-c-6.1.5-src.tar.gz

If the above libraries are not in the system library paths, then you
can specify their paths at configuration time:

~~~
$ ./configure CPPFLAGS='-I/path_to_libs/include' LDFLAGS='-L/path_to_libs/lib'
~~~

PyBGPStream
------------

Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa
Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa
Donec sed odio dui. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Vestibulum id ligula porta felis euismod semper. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa
