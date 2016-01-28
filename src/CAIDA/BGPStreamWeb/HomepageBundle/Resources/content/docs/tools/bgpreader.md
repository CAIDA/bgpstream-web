BGPReader
=========

BGPReader is a command line tool that prints to standard output
information about the BGP records and the BGP elems that are part of a
BGP stream. 

Usage
-----

BGPReader requires the user to specify the stream time interval, and
it accepts the following command line options: 

~~~
usage: bgpreader -w <start>[,<end>] [<options>]
~~~

*data interface options*
~~~
   -d <interface> use the given data interface to find available data
                  available data interfaces are:
       broker         Retrieve metadata information from the BGPStream Broker service (default)
       singlefile     Read a single mrt data file (a RIB and/or an update)
       csvfile        Retrieve metadata information from a csv file
       sqlite         Retrieve metadata information from a sqlite database
   -o <option-name,option-value>*
                  set an option for the current data interface.
                  use '-o ?' to get a list of available options for the current
                  data interface. (data interface can be selected using -d)
~~~

The **default** data interface is the **broker** which allows BGPReader to
provide out-of-the-box access to Route Views and RIPE RIS data.
Data interface parameters can be set using the `-o` option.

{#
~~~
Data interface options for 'broker':
   url               Broker URL (default: "https://bgpstream.caida.org/broker")
   param          Additional Broker GET parameter*
~~~
#}

*stream filters options*
~~~
   -p <project>   process records from only the given project (routeviews, ris)*
   -c <collector> process records from only the given collector*
   -t <type>      process records with only the given type (ribs, updates)*
   -w <start>[,<end>]
                  process records within the given time window
                  (omitting the end parameter enables live mode)*
   -P <period>   process a rib files every <period> seconds (bgp
                        time)
   -j <peer ASN>  return valid elems originated by a specific peer ASN*
   -k <prefix>    return valid elems associated with a specific prefix*
   -y <community> return valid elems with the specified community*
                  (format: asn:value, the '*' metacharacter is recognized)
   -l             enable live mode (make blocking requests for BGP records)
                  allows bgpstream to be used to process data in
                  real-time
~~~

Information about available **collectors** and the associated **time
intervals** are available at the
[Data Providers]({{ path('caida_bgpstream_web_homepage', {'page': 'data'})}}) page.

*output format  options* 
~~~
   -e             print info for each element of a valid BGP record (default)
   -m             print info for each BGP valid record in bgpdump -m format
   -r             print info for each BGP record (used mostly for debugging BGPStream)
   -i             print format information before output
~~~

(The `*` denotes an option that can be given multiple times.)


ASCII Output Formats
--------------------

Below we provide details about the following formats:

* [BGP Elem](#elem) `-e` (default format)
* [BGPdump](#bgpdump) `-m`
* [BGP Record](#record) `-r`

### BGP Elem Format (default) {% verbatim %}{#elem}{% endverbatim %}

~~~
<dump-type>|<elem-type>|<record-ts>|<project>|<collector>|<peer-ASn>|<peer-IP>|<prefix>|<next-hop-IP>|<AS-path>|<origin-AS>|<communities>|<old-state>|<new-state>
~~~

The **dump-type** field is one of:

* **R** - RIB
* **U** - Update

The **elem-type** field is one of:

* **R** - RIB
* **A** - announcement
* **W** - withdrawal
* **S** - state message

When the stream contains RIB data, we also provide *RIB control
messages* to notify the beginning and the end of a RIB. The control
messages have the following format:

~~~
<dump-type>|<dump-pos>|<record-ts>|<project>|<collector>
~~~

Where the **dump-type** field is always set to **R**, and the
**dump-pos** is either:

* **B** - begin
* **E** - end

#### Example:

~~~
$ bgpreader -w 1445306400,1445306402 -c route-views.sfmix
R|B|1445306400|routeviews|route-views.sfmix
R|R|1445306400|routeviews|route-views.sfmix|32354|206.197.187.5|1.0.0.0/24|206.197.187.5|32354 15169|15169|||
...
R|R|1445306401|routeviews|route-views.sfmix|14061|2001:504:30::ba01:4061:1|2c0f:ffd8::/32|2001:504:30::ba01:4061:1|14061 1299 33762|33762|1299:30000||
R|R|1445306401|routeviews|route-views.sfmix|32354|2001:504:30::ba03:2354:1|2c0f:ffd8::/32|2001:504:30::ba00:6939:1|32354 6939 37105 33762|33762|||
R|R|1445306401|routeviews|route-views.sfmix|14061|2001:504:30::ba01:4061:1|3803:b600::/32|2001:504:30::ba01:4061:1|14061 2914 3549 27751|27751|2914:420 2914:1008 2914:2000 2914:3000||
R|E|1445306401|routeviews|route-views.sfmix
U|A|1445306401|routeviews|route-views.sfmix|32354|2001:504:30::ba03:2354:1|2402:ef35::/32|2001:504:30::ba03:2354:1|32354 6939 6453 4755 7633|7633|||
U|A|1445306401|routeviews|route-views.sfmix|14061|2001:504:30::ba01:4061:1|2a02:158:200::/39|2001:504:30::ba01:4061:1|14061 2914 44946|44946|2914:410 2914:1201 2914:2202 2914:3200||
...
~~~

<br>

### BGPdump output format `-m` {% verbatim %}{#bgpdump}{% endverbatim %}

BGPReader supports the bgpdump *one-line per entry with unix timestamps*
output format. See the
<a href="https://bitbucket.org/ripencc/bgpdump/wiki/Home" target="_blank">BGPdump website</a>
for more information.

#### Example:

~~~
$ bgpreader -w 1445306400,1445306402 -p ris -m
BGP4MP|1445306400|W|2001:504:1::a502:4482:1|24482|2a03:5080::/32
BGP4MP|1445306400|A|193.232.245.109|24482|208.74.216.0/21|24482 7029 40377|IGP|193.232.245.109|0|8000|7029:260 7029:1001 7029:1002 24482:2 24482:13020 24482:13021 24482:65302|NAG||
BGP4MP|1445306400|A|198.32.176.20|6939|212.22.66.0/24|6939 12389 41938 8359 50618 35189 201432|IGP|198.32.176.20|0|0||NAG||
BGP4MP|1445306400|A|146.228.1.3|1836|212.22.66.0/24|1836 6939 12389 41938 8359 50618 35189 201432|IGP|146.228.1.3|0|0|1836:120 1836:3100 1836:3110|NAG||
BGP4MP|1445306400|W|217.29.66.125|6939|212.22.66.0/24
...
BGP4MP|1445306402|A|2001:504:1::a501:3030:1|13030|2001:7fb:fe05::/48|13030 6939 12654|IGP|2001:504:1::a501:3030:1|0|1|13030:61 13030:1619 13030:51903|NAG||
BGP4MP|1445306402|A|2001:504:1::a501:3030:1|13030|2001:7fb:fe0e::/48|13030 6939 12654|IGP|2001:504:1::a501:3030:1|0|1|13030:61 13030:1619 13030:51903|NAG||
BGP4MP|1445306402|A|2001:504:1::a501:3030:1|13030|2001:7fb:fe10::/48|13030 6939 12654|IGP|2001:504:1::a501:3030:1|0|1|13030:61 13030:1619 13030:51903|NAG||
BGP4MP|1445306402|A|2001:504:1::a501:3030:1|13030|2001:7fb:fe04::/48|13030 20965 513 12654|IGP|2001:504:1::a501:3030:1|0|1|13030:61 13030:1611|NAG||
BGP4MP|1445306402|W|198.32.160.129|251|84.205.67.0/24
~~~

<br>

### BGP Record output format `-r` {% verbatim %}{#record}{% endverbatim %}

_Note:_ The BGPRecord format is mostly useful for debugging BGPStream since it
contains low-level information about the validity of records read from dump
files.

~~~
<dump-type>|<dump-pos>|<project>|<collector>|<status>|<dump-time>
~~~

The **dump-type** field can be:

* **R** - RIB
* **U** - Update

The **dump-pos** field is one of:

* **B** - begin
* **M** - middle
* **E** - end

The **status** field is one of:

* **V** - valid record
* **E** - empty (it signals an empty dump file)
* **R** - corrupted record
* **S** - corrupted source (the entire dump is corrupted)

#### Example:

~~~
$ bgpreader -w 1445306400,1445306402 -c route-views.sfmix
R|B|1445306400|routeviews|route-views.sg|V|1445306400
R|M|1445306400|routeviews|route-views.sg|V|1445306400
R|M|1445306400|routeviews|route-views.sg|V|1445306400
...
R|M|1445306400|routeviews|route-views.sg|V|1445306400
U|M|1445306400|routeviews|route-views.sg|V|1445305500
U|M|1445306400|ris|rrc11|V|1445306100
U|M|1445306400|ris|rrc00|V|1445306100
U|B|1445306400|ris|rrc00|V|1445306400
U|B|1445306400|ris|rrc11|V|1445306400
U|M|1445306400|routeviews|route-views.sg|V|1445305500
...
U|M|1445306402|ris|rrc11|V|1445306400
U|M|1445306402|ris|rrc11|V|1445306400
~~~

<br>

## Alternative data interfaces  {% verbatim %}{#interfaces}{% endverbatim %}

### Single-File `-d singlefile`

Options:
~~~
Data interface options for 'singlefile':
   rib-file       rib mrt file to read (default: "not-set")
   upd-file       updates mrt file to read (default: "not-set")
~~~


### CSV File `-d csvfile`

Options:
~~~
Data interface options for 'csvfile':
   csv-file       csv file listing the mrt data to read  (default: "not-set")
~~~

### SQLite DB `-d sqlite`

Options:
~~~
Data interface options for 'sqlite':
   db-file        sqlite database (default: "not-set")
~~~
