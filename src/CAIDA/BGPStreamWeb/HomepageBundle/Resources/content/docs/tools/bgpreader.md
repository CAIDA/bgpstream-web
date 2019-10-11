BGPReader
=====


BGPReader is a command line tool that prints to standard output
information about the BGP records and the BGP elems that are part of a
BGP stream. 

Users familiar with the BGPdump tool should find using BGPReader easy; BGPReader
even supports the BGPdump `-m` output format, so in some cases BGPReader can be
used as a drop-in replacement for BGPdump. Moreover, since BGPReader provides
seamless out-of-the-box access to both the Route Views and RIS data archives,
users no longer need to manually acquire data, simply provide a time interval to
BGPReader and BGPStream will do the rest.

## Usage


BGPReader provides a helpful usage prompt when you just type `bgpreader`.
```
Usage: bgpreader [<options>]
```

Here we will break down the provided options:

### *data interface options*
~~~
 -d, --data-interface  <interface>      use the given data interface to find available data. Available values are:
                       broker           Retrieve metadata information from the BGPStream Broker service (default)
                       singlefile       Read a single mrt data file (RIB and/or updates)
                       kafka            Read updates in real-time from an Apache Kafka topic
                       csvfile          Retrieve metadata information from a csv file
                       sqlite           Retrieve metadata information from an SQLite database
 -o, --data-interface-option
                       <option-name>=<option-value>*
                                        set an option for the current data interface. Use '-o?' to get a list of available options for the current data interface (as selected with -d)
~~~

The **default** data interface is the **broker** which allows BGPReader to
provide out-of-the-box access to Route Views and RIPE RIS data.
Data interface parameters can be set using the `-o` option.

### *stream filters options*
~~~
 -w, --time-window     <start>[,<end>]  process records within the given time window.  <start> and <end> may be in 'Y-m-d [H:M[:S]]' format (in UTC) or in unix epoch time.  Omitting <end> enables live mode.
 -f, --filter          <filterstring>   filter records and elements using the rules described in the given filter string
 -l, --live                             enable live mode (make blocking requests for BGP records); allows bgpstream to be used to process data in real-time

 -I, --interval        <num> <unit>     process records that were received the last <num> <unit>s of time, where <unit> is one of 's', 'm', 'h', 'd' (seconds, minutes, hours, days).
 -n, --count           <rec-cnt>        process at most <rec-cnt> records
 -p, --project         <project>        process records from only the given project (routeviews, ris)*
 -c, --collector       <collector>      process records from only the given collector*
 -R, --router          <router>         process records from only the given router*
 -t, --record-type     <type>           process records with only the given type (ribs, updates)*
 -T, --resource-type   <resource-type>  process records from only the given resource type (stream, batch)*
 -P, --rib-period      <period>         process a rib files every <period> seconds (bgp time)

 -j, --peer-asn        <peer ASN>       return elems received by a given peer ASN*
 -a, --origin-asn      <origin ASN>     return elems originated by a given origin ASN*
 -k, --prefix          <prefix>         return elems associated with a given prefix*
 -y, --community       <community>      return elems with the specified community* (format: asn:value. the '*' metacharacter is recognized)
 -A, --aspath          <regex>          return elems that match the aspath regex*
~~~

Information about available **collectors** and the associated **time
intervals** are available at the
[Data Providers]({{ path('caida_bgpstream_web_homepage', {'page': 'data'})}}) page.

### *output format  options* 
~~~
 -e, --output-elems                     print info for each element of a BGP record (default)
 -m, --output-bgpdump                   print info for each BGP record in bgpdump -m format
 -r, --output-records                   print info for each BGP record (used mostly for debugging BGPStream)
 -i, --output-headers                   print format information before output
~~~

(The `*` denotes an option that can be given multiple times.)

## ASCII Output Formats

Below we provide details about the following formats:

* [BGP Elem](#elem) `-e` (default format)
* [BGPdump](#bgpdump) `-m`
* [BGP Record](#record) `-r`

### BGP Elem Format (default) {% verbatim %}{#elem}{% endverbatim %}

~~~
<dump-type>|<elem-type>|<record-ts>|<project>|<collector>|<router-name>|<router-ip>|<peer-ASn>|<peer-IP>|<prefix>|<next-hop-IP>|<AS-path>|<origin-AS>|<communities>|<old-state>|<new-state>
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
<dump-type>|<dump-pos>|<record-ts>|<project>|<collector>|<router-name>|<router-ip>|<record-status>|<dump-time>
~~~

Where the **dump-type** field is always set to **R**, and the
**dump-pos** is either:

* **B** - begin
* **E** - end

#### Example:

```
$ bgpreader -w 1445306400,1445306402 -c route-views.sfmix
R|B|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|R|1445306400.000000|routeviews|route-views.sfmix|||32354|206.197.187.5|1.0.0.0/24|206.197.187.5|32354 15169|15169|||
R|R|1445306400.000000|routeviews|route-views.sfmix|||14061|206.197.187.10|1.0.0.0/24|206.197.187.10|14061 15169|15169|||
...
R|R|1445306401.000000|routeviews|route-views.sfmix|||14061|2001:504:30::ba01:4061:1|3803:b600::/32|2001:504:30::ba01:4061:1|14061 2914 3549 27751|27751|2914:420 2914:1008 2914:2000 2914:3000||
R|E|1445306401.000000|routeviews|route-views.sfmix|||V|1445306400
U|A|1445306401.000000|routeviews|route-views.sfmix|||32354|2001:504:30::ba03:2354:1|2402:ef35::/32|2001:504:30::ba03:2354:1|32354 6939 6453 4755 7633|7633|||
U|A|1445306401.000000|routeviews|route-views.sfmix|||14061|2001:504:30::ba01:4061:1|2a02:158:200::/39|2001:504:30::ba01:4061:1|14061 2914 44946|44946|2914:410 2914:1201 2914:2202 2914:3200||
...
```

<br>

### BGPdump output format `-m` {% verbatim %}{#bgpdump}{% endverbatim %}

BGPReader supports the bgpdump *one-line per entry with unix timestamps*
output format. See the
<a href="https://bitbucket.org/ripencc/bgpdump/wiki/Home" target="_blank">BGPdump website</a>
for more information.

#### Example:

~~~
$ bgpreader -w 1445306400,1445306402 -p ris -m
BGP4MP|1445306400|A|146.228.1.3|1836|212.22.66.0/24|1836 6939 12389 41938 8359 50618 35189 201432|IGP|146.228.1.3|0|0|1836:120 1836:3100 1836:3110|NAG||
BGP4MP|1445306400|A|146.228.1.3|1836|177.154.84.0/22|1836 12989 28640 262401 262401 262401 262401 262401 262401 262401 262401 262949|IGP|146.228.1.3|0|0|1836:120 1836:3100 1836:3110|NAG||
BGP4MP|1445306400|A|146.228.1.3|1836|209.212.8.0/24|1836 174 7922 33659 21669|IGP|146.228.1.3|0|0|1836:110 1836:6000 1836:6031|AG|21669 209.212.31.21|
BGP4MP|1445306400|W|12.0.1.63|7018|193.221.122.0/24
BGP4MP|1445306400|A|146.228.1.3|1836|177.154.84.0/22|1836 12989 52840 262949 262949 262949 262949 262949 262949 262949 262949 262949 262949 262949 262949|IGP|146.228.1.3|0|0|1836:120 1836:3100 1836:3110|NAG||
BGP4MP|1445306400|A|146.228.1.3|1836|212.22.66.0/24|1836 6939 12389 41938 8359 50618 35189 201432|IGP|146.228.1.3|0|0|1836:110 1836:3000 1836:3020|NAG||
BGP4MP|1445306400|A|213.200.87.254|3257|194.36.165.0/24|3257 3356 3356 24724 41930|IGP|213.200.87.254|0|10|3257:8093 3257:30049 3257:50002 3257:51100 3257:51102|NAG||
BGP4MP|1445306400|W|2001:504:1::a502:4482:1|24482|2a03:5080::/32
BGP4MP|1445306400|A|198.32.160.242|24482|208.74.216.0/21|24482 7029 40377|IGP|198.32.160.242|0|0|7029:260 7029:1001 7029:1002 24482:2 24482:13020 24482:13021 24482:65302|NAG||
BGP4MP|1445306400|W|198.32.160.242|24482|193.221.122.0/24
...
~~~

<br>

### BGP Record output format `-r` {% verbatim %}{#record}{% endverbatim %}

_Note:_ The BGPRecord format is mostly useful for debugging BGPStream since it
contains low-level information about the validity of records read from dump
files.

~~~
<dump-type>|<dump-pos>|<record-ts>|<project>|<collector>|<router-name>|<router-ip>|<record-status>|<dump-time>
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
$ bgpreader -w 1445306400,1445306402 -c route-views.sfmix -r
R|B|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|B|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|M|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|M|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|M|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|M|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|M|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|M|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|M|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|M|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
...
~~~

<br>

## Alternative data interfaces  {% verbatim %}{#interfaces}{% endverbatim %}

### Single-File `-d singlefile`

Options:
~~~
Data interface options for 'singlefile':
   rib-file       rib mrt file to read (default: "not-set")
   rib-type       rib file type (mrt/bmp) (default: mrt)
   upd-file       updates mrt file to read (default: "not-set")
   upd-type       update file type (mrt/bmp/ris-live) (default: mrt)
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
