Overview
========

<h1 class="text-danger">TODO: UPDATE THIS DOCUMENT</h1>

Both BGP Stream and this documentation are still under active development and
features will likely change between versions.
Please contact [bgpstream-info@caida.org](mailto:bgpstream-info@caida.org) with any questions and/or suggestions.


Download
--------
bgpstream 1.0.0 can be downloaded [here]({{ path('caida_bgpstream_web_homepage', {'page': 'download'}) }}).



Quick Start
-----------

In order to install bgpstream you need to follow the list of instructions below:

~~~
$ ./configure
$ make
$ make check
$ (sudo) make install
~~~


Use the `--prefix` option to install bgpstream and bgpreader in a local directory.

The procedure above builds and installs:
- the *bgpstream* library
- the *bgpreader* tool
- the *bgpcorsaro* tool

If you are interested in installing the python bindings, then continue
the installation following the instructions [here]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'installing'}) }}).

The distribution contains a python tool for the rapid
creation/population of an sqlite database. The script is located in
the *tools* folder, for more information run:

~~~
$ python tools/bgpstream_sqlite_mgmt.py -h
~~~


bgpreader tool usage
--------------------
bgp reader is a command line tool that prints to standard output
information about a bgp stream.

<h3 class="text-danger">TODO: Consider moving this section to it's own file (docs/usage, perhaps)</h3>

~~~
usage: bgpreader -d <interface> [<options>]

    -d <interface>    use the given data interface to find available data
                                available data interfaces are:
        singlefile     Read a single mrt data file (a RIB and/or an update)
        csvfile        Retrieve metadata information from a csv file
        sqlite         Retrieve metadata information from a sqlite database
        mysql          Retrieve metadata information from the bgparchive mysql database

    -o <option-name,option-value>*    set an option for the current data interface.
                                                              use '-o ?' to get a list of available
                                                              options for the current data interface.
                                                              (data interface must be selected using -d)
    -P <project>   process records from only the given project (routeviews, ris)*
    -C <collector> process records from only the given collector*
    -T <type>      process records with only the given type (ribs, updates)*
    -W <start,end> process records only within the given time window*
    -R <period>    process a rib files every <period> seconds (bgp time)
    -b             make blocking requests for BGP records
                    allows bgpstream to be used to process data in real-time
    -r             print info for each BGP record (in bgpstream format) [default]
    -m             print info for each BGP valid record (in bgpdump -m format)
    -e             print info for each element of a valid BGP record
    -h             print this help menu
    * denotes an option that can be given multiple times

     Data interface options for 'singlefile':
              rib-file       rib mrt file to read (default: not-set)
              upd-file       updates mrt file to read (default: not-set)

     Data interface options for 'csvfile':
              csv-file       csv file listing the mrt data to read (default: not-set)

     Data interface options for 'sqlite':
              db-file        sqlite database (default: not-set)

     Data interface options for 'mysql':
              db-name        name of the mysql database to use (default: bgparchive)
              db-user        mysql username to use (default: bgpstream)
              db-password    mysql password to use (default: not-set)
              db-host        hostname/IP of the mysql server (default: not-set)
              db-port        port of the mysql server (default: not-set)
              db-socket      Unix socket of the mysql server (default: not-set)
              ris-path       Prefix path of RIS data (default: not-set)
              rv-path        prefix path of RouteViews data (default: not-set)
~~~

Example:

print out all the bgp elems observed in the
*routeviews.route-views.jinx.updates.1427846400.bz2* file from
00:00:15 to 00:01:30

~~~
$ bgpreader -dsinglefile -oupd-file,./test/routeviews.route-views.jinx.updates.1427846400.bz2 -W1427846415,1427846490 -e

1427846430|196.223.14.55|30844|W|185.75.149.0/24|||||
1427846430|196.223.14.55|30844|W|103.47.62.0/23|||||
1427846430|196.223.14.55|30844|W|132.8.48.0/20|||||
1427846430|196.223.14.55|30844|W|132.8.64.0/18|||||
1427846430|196.223.14.55|30844|W|132.22.0.0/16|||||
1427846430|196.223.14.55|30844|W|131.34.0.0/16|||||
1427846430|196.223.14.55|30844|W|129.54.0.0/16|||||
1427846430|196.223.14.55|30844|A|41.159.135.0/24|196.223.14.55|30844 6939 12956 6713 16058|16058||
1427846430|196.223.14.55|30844|A|84.205.73.0/24|196.223.14.55|30844 6939 12654|12654||
~~~




API Documentation
-----------------
This online [BGP Stream API]() is currently the best source of information
about using BGP Stream, please look at the installed headers:

- [bgpstream.h]()
- [bgpstream_record.h]()
- [bgpstream_elem.h]()
