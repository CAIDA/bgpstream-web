PyBGPStream Tutorial
====================

<h1 class="text-danger">TODO: UPDATE THIS DOCUMENT</h1>

pyBGPStream provides a Python interface to the libBGPStream C library.
More details are available at @@link to pybgpstream overview, whereas
the complete documentation is available at @@link to pybgpstream docs.

Below we provide the following tutorials:

* [Get familiar with the API: print the MOAS prefixes](#api1)
* [Prefix monitoring](#api2)


### Get familiar with the API: print the MOAS prefixes ##   {% verbatim %}{#api1}{% endverbatim %}

[link to code]({{ asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/moas.py') }})



Below is a simple example that shows how to use pybgpstream to output
the MOAS prefixes and their origin ASes. The example is fully
functioning and it can be  run using the following command:

~~~
$ python moas.py
   194.68.55.0/24 43893,30893
   199.45.53.0/24 701,65403
   207.188.170.0/24 13332,26640
   ...
   193.232.141.0/24 42385,31649
   84.16.224.0/19 28753,16265
   125.208.1.0/24 4808,4847
~~~


The program instantiate an instance of BGPStream that uses the CAIDA
broker as data interface, it parses the BGP elems extracted from the
BGP records that match the filters (collectors, record type, and
time), and saves in a hash map the list of unique origin ASns for each
prefix. 


<br>

#### Step by step description

The first step in each program is to import the Python modules and
create a new BGPStream instance as well as a re-usable BGP record
instance.

<br>

~~~ .language-python
from _pybgpstream import BGPStream, BGPRecord, BGPElem

stream = BGPStream()
rec = BGPRecord()
~~~

<br>


The second step consists in customiziing

is a re-usable memory allocation that is going to contain the most
recent record read. 



<!--

Below are some examples that show how to get started with the
bgpstream python bindings.

Print the stream
----------------

Select the records from a single MRT file provided to the 'singlefile'
datasource that comply with the time interval filter provided and
print to standard output the information contained in the records and in the elems.

~~~ .language-python
from _pybgpstream import BGPStream, BGPRecord, BGPElem

# create a new bgpstream instance
stream = BGPStream()

# create a reusable bgprecord instance
rec = BGPRecord()

# select the datasource interface
stream.set_data_interface('singlefile')

# select the MRT file to be read by the singlefile datasource
stream.set_data_interface_option('singlefile', 'upd-file','./ris.rrc06.updates.1427846400.gz')

# select the time interval to process  Wed Apr  1 00:02:50 UTC 2015 -> Wed Apr  1 00:04:30
stream.add_interval_filter(1427846570, 1427846670)

# start the stream
stream.start()

# print the stream
while(stream.get_next_record(rec)):
  print rec.status, rec.project +"."+ rec.collector, rec.time
  elem = rec.get_next_elem()
  while(elem):
      print "\t", elem.type, elem.peer_address, elem.peer_asn, elem.type, elem.fields
      elem = rec.get_next_elem()
~~~
              

The code above generate the following output:

~~~
$ cd examples/
$ python tutorial_print.py
...
singlefile_ds.singlefile_ds 1427846668 A 202.249.2.185 25152 A {'next-hop': '202.249.2.185', 'prefix': '131.255.48.0/24', 'as-path': '25152 2914 3549 262717 262278'}
singlefile_ds.singlefile_ds 1427846668 A 202.249.2.185 25152 A {'next-hop': '202.249.2.185', 'prefix': '131.255.49.0/24', 'as-path': '25152 2914 3549 262717 262278'}
singlefile_ds.singlefile_ds 1427846668 A 202.249.2.185 25152 A {'next-hop': '202.249.2.185', 'prefix': '131.255.50.0/24', 'as-path': '25152 2914 3549 262717 262278'}
singlefile_ds.singlefile_ds 1427846668 A 202.249.2.185 25152 A {'next-hop': '202.249.2.185', 'prefix': '131.255.51.0/24', 'as-path': '25152 2914 3549 262717 262278'}
singlefile_ds.singlefile_ds 1427846668 A 202.249.2.185 25152 A {'next-hop': '202.249.2.185', 'prefix': '199.38.164.0/23', 'as-path': '25152 2914 174 13789 53563'}
singlefile_ds.singlefile_ds 1427846668 A 202.249.2.185 25152 A {'next-hop': '202.249.2.185', 'prefix': '192.58.232.0/24', 'as-path': '25152 6939 11164 10886 6629'}
singlefile_ds.singlefile_ds 1427846668 A 202.249.2.185 25152 A {'next-hop': '202.249.2.185', 'prefix': '117.121.204.0/24', 'as-path': '25152 2914 174 7713 46029'}
singlefile_ds.singlefile_ds 1427846668 A 202.249.2.185 25152 A {'next-hop': '202.249.2.185', 'prefix': '177.10.158.0/24', 'as-path': '25152 2914 3549 28250 61894 61894 61894 61894 61894 61894 61894'}
singlefile_ds.singlefile_ds 1427846668 W 202.249.2.185 25152 W {'prefix': '207.133.114.0/24'}
...
~~~


Using different datasources
----------------------------------------------------


bgpstream supports four different datasources:

- singlefile: a single file (one RIB, one update, or both) is provided
- csvfile: a list of files paths with associated metadata  is provided
- sqlite: a database containing files paths and their associated metadata is provided
- mysql: a database containing files paths and their associated metadata is provided

**singlefile**: bgpstream reads the MRT information contained in a RIB file or/and an
update file

~~~ .language-python
# select the datasource interface
stream.set_data_interface('singlefile')

# select a RIB MRT file
stream.set_data_interface_option('singlefile', 'rib-file','./ris.rrc06.ribs.1427846400.gz')

# select an update MRT file
stream.set_data_interface_option('singlefile', 'upd-file','./ris.rrc06.updates.1427846400.gz')
~~~

**csvfile**: bgpstream reads information about the available mrt data from a CSV
file that complies with the following format:

<file-path>, <project-name>, <bgp-type>, <collector-name>, <bgp-time-begin>, <duration>, <insertion-timestamp>

See 'examples/bgp_data.csv' for a complete example.

~~~ .language-python
# select the datasource interface
stream.set_data_interface('csvfile')

# path to the file containing the sqlite database
stream.set_data_interface_option('csvfile', 'csv-file','./bgp_data.csv')
~~~

**sqlite**: bgpstream reads information about the available mrt data
from a SQLite database. A compliant sqlite database can be
automatically generated using the *bgpstream_sqlite_mgmt.py* utility
released with the bgpstream c library (bgpstream/tools/bgpstream_sqlite_mgmt.py).

See 'examples/bgp_data.db'.

~~~ .language-python
# select the datasource interface
stream.set_data_interface('sqlite')

# path to the file containing the sqlite database
stream.set_data_interface_option('sqlite', 'db-file','./bgp_data.db')
~~~

**mysql**: bgpstream reads information about the available mrt data
from a MySQL database. MySQL documentation is a work in progress.

~~~ .language-python
# select the datasource interface
stream.set_data_interface('mysql')

# setup mysql options
stream.set_data_interface_option('mysql', 'db-name','bgparchive')
stream.set_data_interface_option('mysql', 'db-user','bgpstream')
stream.set_data_interface_option('mysql', 'db-password','thisismypassword')
stream.set_data_interface_option('mysql', 'db-host','127.0.0.1')
...
~~~

Filter the stream
--------------------------

A bgpstream instance can be configured so that the stream of bgpstream
records is filtered.

If no filter is set, all the data are processed.

**Example 1**: select updates files generated by collectors of the RIS
project that have been generated on Wed Apr 1 2015, between
00:02:50 and  00:04:30 (UTC time).

~~~ .language-python
# select the records from time interval  Wed Apr  1 00:02:50 UTC 2015 -> Wed Apr  1 00:04:30
stream.add_interval_filter(1427846570, 1427846670)

# get data from all collectors of the RIS project
stream.add_filter('project','ris')

# get updates
stream.add_filter('record-type','updates')
~~~

**Example 2**: select ribs and updates files generated by  rrc00 (RIS
collector) and route-views2 (RouteViews collector).

~~~ .language-python
# select collectors
stream.add_filter('collector','rrc00')
stream.add_filter('collector','route-views2')

# select ribs and updates
stream.add_filter('record-type','ribs')
stream.add_filter('record-type','updates')

# record-type filtering could have been avoided in this
# case, when all types are requested no filter is necessary
~~~


A more complex example: get the AS topology
----------------------------------------------------

In this example, we read a RIB file and we build the AS topology
(i.e. the list of adjacent ASes) analyzing the AS path attached to
each RIB entry

~~~ .language-python
from _pybgpstream import BGPStream, BGPRecord, BGPElem

stream = BGPStream()
rec = BGPRecord()

as_topology = set()
rib_entries = 0

# Select datasource
stream.set_data_interface('mysql')
stream.set_data_interface_option('mysql', 'db-host', 'server.caida.org')
stream.set_data_interface_option('mysql', 'db-port', '3306')
stream.set_data_interface_option('mysql', 'db-user', 'bgpstream')

# Apply filters
#stream.add_filter('collector', 'rrc00')
stream.add_filter('record-type', 'ribs')
# Wed, 29 Apr 2015 23:50:00 GMT -> Thu, 30 Apr 2015 00:10:00 GMT
stream.add_interval_filter(1430351400, 1430352600)

stream.start()

# Process data
while(stream.get_next_record(rec)):
  elem = rec.get_next_elem()
  while(elem):
      rib_entries += 1
      # get the AS path
      path = elem.fields['as-path']
      # get the list of ASes in the path
      ases = path.split(" ")
      for i in range(0,len(ases)-1):
          # avoid multiple prepended ASes
          if(ases[i] != ases[i+1]):
              as_topology.add(tuple(sorted([ases[i],ases[i+1]])))
      # get next elem        
      elem = rec.get_next_elem()
      
# Output results
print "Processed ", rib_entries, " rib entries"
print "Found ", len(as_topology), " AS adjacencies"
~~~

A similar example is included in the distribution (examples/topology.py):

~~~
$ cd examples
$ python topology.py 
Processed  558680  rib entries
Found  72031  AS adjacencies
~~~

-->
