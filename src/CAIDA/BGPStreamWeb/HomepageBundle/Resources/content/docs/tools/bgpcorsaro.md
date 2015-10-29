BGPCorsaro
==========

BGPCorsaro is a command line tool that allow the user to process a BGP
stream of data using plugins.  The tool computes time intervals of
fixed length (see the `-i` option) starting from a sorted stream of BGP
records and systematically sends the following signals to the active
plugins: 

* interval-start - a new interval starts,
* new record - there is a new BGP record to process that belongs to
the current interval,
* interval-end - the current interval is finished.

BGPCorsaro is an interval driven tool with a modular architecture
based on plugins (that can be run in cascade).

Plugins can be either:

* Stateless: e.g., performing classification and tagging of BGP
  records;  plugins following in the pipeline can use such tags to
  inform their processing. 

* Stateful: e.g., extracting statistics or aggregating data that are
  output at the end of each time bin. Since libBGPStream provides a
  sorted stream of records, BGPCorsaro can easily recognize the end of
  a time bin even when processing data from multiple collectors. 
  
See the
[BGPStream technical report]({{ path('caida_bgpstream_web_homepage', {'page': 'pubs'}) }}#bgpstream-tech-rep)
for an in-depth discussion of the architecture of BGPCorsaro

Usage
-----

The BGPCorsaro tools requires the user to specify the stream time interval and
a template to generate output files.

~~~
usage: bgpcorsaro -w <start>[,<end>] -O outfile [<options>]
~~~

*data interface and stream filter options*
~~~
   -d <interface> use the given bgpstream data interface to find available data
                   available data interfaces are:
       broker       Retrieve metadata information from the BGPStream Broker service (default)
       singlefile   Read a single mrt data file (a RIB and/or an update)
       csvfile      Retrieve metadata information from a csv file
       sqlite       Retrieve metadata information from a sqlite database
   -o <option-name,option-value>*
                  set an option for the current data interface.
                  use '-o ?' to get a list of available options for the current
                  data interface. (data interface can be selected
                  using -d)
   -p <project>   process records from only the given project (routeviews, ris)*
   -c <collector> process records from only the given collector*
   -t <type>      process records with only the given type (ribs, updates)*
   -w <start>[,<end>]
                  process records within the given time window
                    (omitting the end parameter enables live mode)*
   -P <period>    process a rib files every <period> seconds (bgp time)
   -l             enable live mode (make blocking requests for BGP records)
                  allows bgpcorsaro to be used to process data in real-time
~~~

The **default** data interface is the **broker**.
Information about available **collectors** and the associated
**time intervals** are available at the
[Data Providers]({{ path('caida_bgpstream_web_homepage', {'page': 'data'})}}) page.

*interval options*
~~~
   -i <interval>  distribution interval in seconds (default: 60)
   -a             align the end time of the first interval
   -g <gap-limit> maximum allowed gap between packets (0 is no limit) (default: 0)
   -L             disable logging to a file
~~~

*plugin options*
~~~
    -x <plugin>    enable the given plugin (default: all)*
                   available plugins:
                    - pfxmonitor
                    - pacifier
                   use -p "<plugin_name> -?" to see plugin options
~~~

*logging options*
~~~
   -n <name>      monitor name (default: localhost)
   -O <outfile>   use <outfile> as a template for file names.
                   - %X => plugin name
                   - %N => monitor name
                   - see man strftime(3) for more options
   -r <intervals> rotate output files after n intervals
   -R <intervals> rotate bgpcorsaro meta files after n intervals
~~~

(The `*` denotes an option that can be given multiple times.)

<br>

{#
The user can modify
the following data interface parameters (in case she/he is running a
private instance of the BGP Stream broker) using the **-o** option:

~~~
Data interface options for 'broker':
   url            Broker URL (default: "https://bgpstream.caida.org/broker")
   param          Additional Broker GET parameter*
~~~
#}

{#
Below we provide more details about:

* [BGPCorsaro output](#output)
* [Available plugins](#plugins)

<br>

## BGPCorsaro Output {% verbatim %}{#output}{% endverbatim %}

By default, BGPCorsaro generates two output files:

* log.txt 
* outfile-pattern - logs the BGP start and ending time of each interval


### Example

In this example, BGPCorsaro processes 10 minutes of BGP data observed
from the Route Views Jinx collector; it bins the time in intervals of 5
seconds. Each BGP record is processed by the only active plugin, **pfxmonitor**.
~~~
$ bgpcorsaro -c route-views.jinx  -w1445306400,1445307000 \
   -x "pfxmonitor -l 192.0.43.0/24  -n 1" -i 5  -L -O ./%X.txt

[23:15:40:852] bgpcorsaro_set_interval: setting interval length to 5
[23:15:40:852] bgpcorsaro_plugin_enable_plugin: enabling pfxmonitor
[23:15:40:853] bgpcorsaro_set_stream: setting stream pointer
bgp.pfxmonitor.ip-space.prefixes_cnt 1 1445306400
bgp.pfxmonitor.ip-space.origin_ASns_cnt 1 1445306400
...
bgp.pfxmonitor.ip-space.prefixes_cnt 1 1445306985
bgp.pfxmonitor.ip-space.origin_ASns_cnt 1 1445306985
~~~

<br>

In this specific case, the outfile name pattern specified is
<plugin-name>.txt.
In *pfxmonitor.txt*, we find the list of processed intervals:

~~~
# BGPCORSARO_INTERVAL_START 0 1445306400
# BGPCORSARO_INTERVAL_END 0 1445306404
...
# BGPCORSARO_INTERVAL_START 116 1445306980
# BGPCORSARO_INTERVAL_END 116 1445306984
# BGPCORSARO_INTERVAL_START 117 1445306985
# BGPCORSARO_INTERVAL_END 117 1445306989
# BGPCORSARO_INTERVAL_START 118 1445306990
~~~

<br>
#}

## Available Plugins {% verbatim %}{#plugins}{% endverbatim %}

### Prefix Monitor `-x pfxmonitor`

Prefix Monitor is a stateful plugin that monitors prefixes overlapping with a
given set of IP address ranges. For each BGPStream record, the plugin:

 1. selects only the RIB and Updates dump records related to prefixes
     that overlap with the given IP address ranges.
 1. tracks, for each <prefix, VP> pair, the ASN that originated the
     route to the prefix. At the end of each time bin, the plugin outputs
     the timestamp of the current bin, the number of unique prefixes
     identified and, the number of unique origin ASNs observed by all the VPs.

The pfxmonitor plugin requires the user to specify one or more
prefixes to monitor. Such prefixes can be provided using the `-l`
command line option repeatedly, or they can be given in a file using
`-L` (one prefix per line).

~~~
plugin usage: pfxmonitor -l <pfx> -L<prefix-file>
       -l <prefix>        prefix to monitor*
       -L <prefix-file>   read the prefixes to monitor from file*
       -M                 consider only more specifics (default: false)
       -n <peer_cnt>   minimum number of unique peers' ASNs to declare prefix visible (default: 10)
       -m <prefix>        metric prefix (default: bgp)
       -i <name>          IP space name (default: ip-space)
~~~

By default, pfxmonitor keeps track of all the prefixes that overlap
with at least one of the prefixes provided in input. If the user is
interested only in the cases in which the same prefixes are announced
or more specifics are announced, then she/he should use the **-M**
option.

The `-n` option specifies the minimum number of unique peers' ASNs
to declare prefix visible. In detail, a pair *<prefix,origin ASn>* is
taken into account (for the computation of the output metrics) if and
only if the same pair *<prefix,origin ASn>* is observed by at least
**peer_cnt** unique peer ASNs.

<br>

`-m` and `-i` plugin options modify the metrics generated by the
plugin, specifically, they change the following fields:

~~~
<metric-prefix>.pfxmonitor.<ip-space-name>.prefixes_cnt 1 1445306400
<metric-prefix>.pfxmonitor.<ip-space-name>.origin_ASns_cnt 1 1445306400
~~~

<br>
### Pacifier `-x pacifier`

Documentation coming soon...


