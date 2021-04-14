PyBGPStream Tutorial
====================

[PyBGPStream]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage': 'pybgpstream'}) }})
provides a Python interface to the libBGPStream C library.

Below we provide the following tutorials:

* [Get familiar with the API](#print) ([code]({{ asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-moas.py') }}))
* [Print the MOAS prefixes](#moas) ([code]({{ asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-moas.py') }}))
* [Measuring the extent of AS path inflation](#aspath) ([code]({{ asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-aspath.py') }}))
* [Studying the communities](#communities) ([code]({{ asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-communities.py') }}))
* [Accessing live-stream data sources](#live-streams) (
[routeviews-stream code]({{ asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-routeviews-stream.py')}})
[ris-live code]({{ asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-ris-live.py')}})
)

<br>

## Get familiar with the API ##   {% verbatim %}{#print}{% endverbatim %}

As a first example, we use pybgpstream to output the information extracted
from BGP records and BGP elems. We provide a step by step description
and the link to download the script at the end of the section.
The example is fully functioning and it can be  run using the following command:

~~~
$ python pybgpstream-print.py
update|A|1499385779.000000|routeviews|route-views.eqix|None|None|11666|206.126.236.24|210.180.224.0/19|206.126.236.24|11666 3356 3786|11666:1000 3356:3 3356:2003 3356:575 3786:0 3356:22 11666:1002 3356:666 3356:86|None|None
update|A|1499385779.000000|routeviews|route-views.eqix|None|None|11666|206.126.236.24|210.180.0.0/19|206.126.236.24|11666 3356 3786|11666:1000 3356:3 3356:2003 3356:575 3786:0 3356:22 11666:1002 3356:666 3356:86|None|None
update|A|1499385788.000000|routeviews|route-views.eqix|None|None|11666|206.126.236.24|210.180.64.0/19|206.126.236.24|11666 6939 4766 4766|11666:2000 11666:2001|None|None
...
~~~

<br>

### Step by step description

The first step in each pybgpstream script is to import the Python modules and
create a new BGPStream instance.

~~~ .language-python
import pybgpstream
stream = pybgpstream.BGPStream(
    from_time="2017-07-07 00:00:00", until_time="2017-07-07 00:10:00 UTC",
    collectors=["route-views.sg", "route-views.eqix"],
    record_type="updates",
    filter="peer 11666 and prefix more 210.180.0.0/16"
)
~~~

During the creation of the BGPStream instance, we also added a few filters to narrow the stream.

- `from_time` and `until_time` specifies the beginning and ending time of the stream.
- `collectors` narrows the stream to have records from the specified collectors
- `record_type="updates"` indicating we want only updates (i.e. not RIB dumps)
- lastly, `filter` string specifies more flexible and power filter conditions. [More on filtering.](https://github.com/CAIDA/libbgpstream/blob/master/FILTERING)


At this point we can start the stream, and repeatedly ask for new
BGP elems. Each time a valid record is read, we
extract from it the elems that it contains and print the record and
elem fields. If a non-valid record is found, we do not attempt to
extract elems. 

~~~ .language-python
for elem in stream:
    # record fields can be accessed directly from elem
    # e.g. elem.time
    # or via elem.record
    # e.g. elem.record.time
    print(elem)
~~~

<br>

### Complete Example

Get the code: [pybgpstream-print.py]({{
asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-print.py')
}}).

~~~ .language-python
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/public/docs/tutorials/code/pybgpstream-print.py' %}
~~~

<br>

## Cache the MRT files ##   {% verbatim %}{#cache}{% endverbatim %}

Data files processed by the broker can now be cached to a local directory which is checked before downloading a dump file.
Previously, when using BGPStream to repeatedly process the same data (e.g., when testing/debugging code), poor network connectivity could add overhead to processing time.
The caching implementation is thread safe and can support parallel instances of BGPStream (either as threads or separate processes).
The cache can be enabled by setting the  cache-dir parameter of the "broker" data interface by calling 
~~~
stream.set_data_interface_option("broker", "cache-dir", "/path/to/cache")
~~~

<br>

## Print the MOAS prefixes ##   {% verbatim %}{#moas}{% endverbatim %}

In this second tutorial we show  how to use pybgpstream to output
the MOAS prefixes and their origin ASes. The example is fully
functioning and it can be  run using the following command:

~~~
$ python pybgpstream-moas.py
('194.68.55.0/24', '43893,30893')
('199.45.53.0/24', '701,65403')
('207.188.170.0/24', '13332,26640')
('8.6.245.0/24', '11096,10490')
('65.111.243.0/24', '20193,30691')
('195.246.126.0/23', '6714,49258')
('63.139.84.0/24', '65000,53286')
('219.232.108.0/24', '4808,4847')
...
~~~

<br>

The program parses the BGP elems extracted from the
BGP records that match the filters (collectors, record type, and
time), saves in a hash map the list of unique origin ASns for each
prefix, and outputs those that have multiple origin ASns. 

<br>


### Step by step description

In this case the stream is configured to return the BGP records read from
a RIBs dump generated by the Route View Singapore collector,
having a timestamp in the interval 7:50:00 - 08:10:00 Sat, 01 Aug
2015 GMT.

~~~ .language-python
stream = pybgpstream.BGPStream(
    from_time="2015-08-01 07:50:00", until_time="2015-08-01 08:10:00",
    collectors=["route-views.sg"],
    record_type="ribs",
)
~~~

<br>

We  use a dictionary to associate a list of origin ASns with each
prefix observed in the RIB dump.

~~~ .language-python
from collections import defaultdict

prefix_origin = defaultdict(set)
~~~

<br>

Each time a new BGP elem is extracted, the program extracts the prefix
and the origin ASn and updates the *prefix_origin* dictionary.
Prefix and AS-path  are string fields that are present in any
BGP elem of type RIB. The split function converts the AS path string
into an array of strings, each one representing an AS hop, the last
hop is the origin AS.

~~~ .language-python
pfx = elem.fields["prefix"]
ases = elem.fields["as-path"].split(" ")
if len(ases) > 0:
    origin = ases[-1]
    prefix_origin[pfx].add(origin)
~~~

<br>

### Complete Example

Get the [code]({{
asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-moas.py')
}}).

~~~ .language-python
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/public/docs/tutorials/code/pybgpstream-moas.py' %}
~~~

<br>

## Measuring the extent of AS path inflation ##   {% verbatim %}{#aspath}{% endverbatim %}

In this example, we show how to use pybgpstream to
measure the extent of AS path inflation, i.e. measure how many AS
paths are longer than the shortest path between two ASes due to the
adoption of routing policies. The example is fully functioning and it
can be  run using the following command: 

~~~
$ python pybgpstream-aspath.py
   ...
   3549 27316 6 5
   3549 27314 3 3
   3549 27313 3 3
   3549 27310 3 3
   3549 27311 3 3
   3549 45834 4 4
   3549 27318 4 3
   3549 27319 5 4
   3549 18173 4 4
...
~~~

<br>

The program  reads a RIB dump as originated by the RIS
RRC00 collector, it computes the number of AS hops between the
peer ASn and the origin AS, and it compares it to the shortest path
between the same AS pairs in an simple undirected graph built using
the AS path adjacencies. The output complies with the following
format:

~~~
<monitor ASn> <destination ASn> <#AS hops in BGP> <#AS hops in undirected graph>
~~~

<br>

### Step by step description

In this case the stream is configured to return the BGP records read from
a RIBs dump generated by RIS RRC00 collector, having a timestamp in
the interval 7:50:00 - 08:10:00 Sat, 01 Aug 2015 GMT.

~~~ .language-python
stream = pybgpstream.BGPStream(
    from_time="2015-08-01 07:50:00", until_time="2015-08-01 08:10:00",
    collectors=["rrc00"],
    record_type="ribs",
)
~~~

<br>

The script uses the [NetworkX](https://networkx.github.io/) package
utilities to generate a simple undirected graph (i.e. a graph that
does not have loops or self-edges).  A dictionary of dictionaries is
used to maintain the shortest path between the peer ASn and the origin ASn
as observed in BGP.

~~~ .language-python
import networkx as nx
from collections import defaultdict

as_graph = nx.Graph()

bgp_lens = defaultdict(lambda: defaultdict(lambda: None))
~~~

<br>

Each time a new BGP elem is extracted, the program removes the ASns
that are repeatedly prepended in the AS path (using the groupby
function),  counts the number of AS hops between the peer and the
destination AS (i.e. the origin AS), and saves this information in the
*bgp_lens*  dictionary. Each adjacency in the reduced AS path is used
to add a new link to the NetworkX graph. 


~~~ .language-python
hops = [k for k, g in groupby(elem.fields['as-path'].split(" "))]
if len(hops) > 1 and hops[0] == peer:
            origin = hops[-1]
            for i in range(0,len(hops)-1):
                as_graph.add_edge(hops[i],hops[i+1])
            bgp_lens[peer][origin] = min(filter(bool,[bgp_lens[peer][origin],len(hops)]))
~~~

<br>

Finally, for each peer and origin pair, the script uses the NetworkX
utility functions to compute the length of the shortest path between
the two nodes in the simple undirected graph. The output juxtaposes the
minimum length observed in BGP and the shortest path computed in the
simple undirected graph.

~~~ .language-python
for peer in bgp_lens:
    for origin in bgp_lens[peer]:
       nxlen = len(nx.shortest_path(as_graph, peer, origin))
        print(peer, origin, bgp_lens[peer][origin], nxlen)
~~~

<br>

### Complete Example

Get the [code]({{
asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-aspath.py')
}}).

~~~ .language-python
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/public/docs/tutorials/code/pybgpstream-aspath.py' %}
~~~

<br>

## Studying the communities ##   {% verbatim %}{#communities}{% endverbatim %}

In this example, we show how to use pybgpstream to extract information
the prefixes that are associated with a specific type of communities.
Specifically we use the bgpstream filtering options to select a
specific set of prefixes of interest, as well as a specific peer ASn,
and any message having at least one community with 3400 as value.
The example is fully functioning and it can be  run using the following command: 

~~~
$ python pybgpstream-communities.py
Community: 2914:3400 ==> 185.84.167.0/24,185.84.166.0/24,185.84.166.0/23
Community: 2914:2406 ==> 185.84.167.0/24,185.84.166.0/24,185.84.166.0/23
Community: 2914:410 ==> 185.84.167.0/24,185.84.166.0/24,185.84.166.0/23
Community: 2914:3475 ==> 185.84.167.0/24,185.84.166.0/24,185.84.166.0/23
Community: 2914:1405 ==> 185.84.167.0/24,185.84.166.0/24,185.84.166.0/23
~~~

<br>

The program  reads a RIB dump as originated by the RIS
RRC06 collector, it selects messages originated by the 25152 peer that
are associated with 185.84.166.0/23 (or more specifics), and have at
least one community that has 3400 as value.
 The output complies with the following format:

~~~
Community: <ASn>:<value> ==> <prefixes affected by the community>
~~~

<br>

### Step by step description

In this case the stream is configured to return the BGP records read from
a RIBs dump generated by RIS RRC06 collector, having a timestamp in
the interval 7:50:00 - 08:10:00 Sat, 01 Aug 2015 GMT. The elems are
filtered considering three conditions: the originating peer AS number,
the prefix announced, and the presence of at least one community
having 3400 as value.

~~~ .language-python
stream = pybgpstream.BGPStream(
    from_time="2015-08-01 07:50:00", until_time="2015-08-01 08:10:00",
    collectors=["rrc06"],
    record_type="ribs",
    filter="peer 25152 and prefix more 185.84.166.0/23 and community *:3400"
)
~~~

<br>

A dictionary of sets maintains the list of prefixes affected by a
specific community.

~~~ .language-python
from collections import defaultdict

community_prefix = defaultdict(set)
~~~

<br>

Each time a new BGP elem is extracted, the program build a string with
the ASn and value fields of each community, and add the prefix to the
set.


~~~ .language-python
pfx = elem.fields['prefix']
communities = elem.fields['communities']
for c in communities:
    community_prefix[c].add(pfx)
~~~

<br>

Finally, the dictionary is written to standard output.

~~~ .language-python
for ct in community_prefix:
    print("Community:", ct, "==>", ",".join(community_prefix[ct]))
~~~

<br>

### Complete Example

Get the [code]({{
asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-communities.py')
}}).

~~~ .language-python
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/public/docs/tutorials/code/pybgpstream-communities.py' %}
~~~


## Accessing live stream data sources ##   {% verbatim %}{#live-streams}{% endverbatim %}

In this example, we show how to use pybgpstream to access live data streams from
Route Views and RIPE RIS. The example programs print out real-time BGP updates received from
Route Views BMP Stream (`routeivews-stream`) and RIPE RIS Live (`ris-live`).

Accessing these live stream data sources is as simple as setting the `project` or `projects` field to 
`routeviews-stream` or `ris-live` when initiating a `BGPStream` object in your script.

### Route Views Stream 


Route Views Stream [code]({{
asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-routeviews-stream.py')
}}).

~~~ .language-python
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/public/docs/tutorials/code/pybgpstream-routeviews-stream.py' %}
~~~

### RIPE RIS Live

RIPE RIS Live [code]({{
asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/pybgpstream-ris-live.py')
}}).

~~~ .language-python
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/public/docs/tutorials/code/pybgpstream-ris-live.py' %}
~~~
