Data &amp; Meta-data Access
===========================

The data and meta-data access layer provides information about available BGP data.
It also provides data annotations, e.g., the collection project and router
that obtained the data.

{#
For each dump, we annotate:

* the path to the dump (local or remote)
* the project name
* the collector name 
* the BGP type (i.e. ribs or updates)
* the nominal initial timestamp
* the duration (i.e. the time interval covered by the dump)
* the time the dump was published to the Data Provider
#}

We provide four data interfaces to identify BGP data available for processing:

 - [BGPStream Broker](#broker), the default data interface, which provides
  out-out-of-the box seamless access to public
  [Data Providers]({{ path('caida_bgpstream_web_homepage', {'page': 'data'})}})
 - [Single File](#singlefile), a data interface that provides access to individual MRT dump files (either local or via HTTP).
 - [CSV File](#csvfile), a psuedo-database suitable for using BGPStream with locally-available (e.g., private) data files.
 - [SQLite DB](#sqlite), like CSV file, but meta-data is stored in an SQLite DB, providing better scalability.

## BGPStream Broker {% verbatim %}{#broker}{% endverbatim %}

The BGPStream Broker is a web service that provides a unified query interface
to retrieve streams of data from different public
[Data Providers]({{ path('caida_bgpstream_web_homepage', {'page': 'data'})}})
(i.e., Route Views, RIPE RIS). The broker interface enables several key features of BGPStream:

 * Out-of-the-box access to Route Views and RIPE RIS data.
 * Load balancing between data archive mirrors.
 * Support for live data processing.
 
CAIDA operates a publicly accessible instance of the Broker that
libBGPStream is configured to query by default. See the 
[query API documentation]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':'broker'})}})
for information about using the broker from third-party applications.


## Single-File {% verbatim %}{#singlefile}{% endverbatim %}

Single file allows the user to access a single local or remote (via HTTP) RIB
dump and/or Update dump (similar to how the bgpdump tool operates). See
[BGPReader]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools', 'subpage':'bgpreader'})}})
for more information about processing a single dump file.


## CSV File {% verbatim %}{#csvfile}{% endverbatim %}

For users with a small set of local/private MRT dump files, the CSV file
interface may be appropriate. Simply create a CSV file in the following format
(with one line per file):

~~~
<dump-path>,<project>,<bgp-type>,<collector>,<dump-ts>,<duration>,<insertion-ts>
~~~

### Example

The following line provides the meta-data for Route Views Updates dump
which is stored in a local directory. The dump was generated by the
Jinx collector and contains updates in the interval 1427846400 to
1427846400 + 900 (i.e. 15 minutes worth of updates).

~~~
routeviews.route-views.jinx.updates.1427846400.bz2,routeviews,updates,route-views.jinx,1427846400,900,1430438400
~~~

## SQLite {% verbatim %}{#sqlite}{% endverbatim %}

The SQLite interface is similar to the CSV file interface, but can be used for
larger data sets, as well as providing better support for live mode (that is,
 updating the database with new files as they are available).

In order to facilitate the insertion of data into an SQLite database with
an approprate schema, we provide a script
([tools/bgpstream_sqlite_mgmt.py](https://github.com/CAIDA/bgpstream/blob/master/tools/bgpstream_sqlite_mgmt.py))
that adds new dump meta-data to the database.

_bgpstream_sqlite_mgmt.py_ inserts new dump's meta-data each time it
 is invoked, creating the database file if it does not exist.

~~~
usage: bgpstream_sqlite_mgmt.py [-h] [-l] [-M ADD_MRT_FILE] [-p PROJ]
                                [-c COLL] [-t BGP_TYPE] [-T FILE_TIME]
                                [-u UPDATES_TIME_SPAN]
                                sqlite_db
positional arguments:
  sqlite_db             file containing the sqlite database

optional arguments:
  -h, --help            show this help message and exit
  -l, --list_files      list the mrt files in the database
  -M ADD_MRT_FILE, --add_mrt_file ADD_MRT_FILE
                        path to the mrt file to add to the database
  -p PROJ, --proj PROJ  bgp project
  -c COLL, --coll COLL  bgp collector
  -t BGP_TYPE, --bgp_type BGP_TYPE
                        bgp type
  -T FILE_TIME, --file_time FILE_TIME
                        time associated with the mrt file
  -u UPDATES_TIME_SPAN, --updates_time_span UPDATES_TIME_SPAN
                        updates time span
~~~