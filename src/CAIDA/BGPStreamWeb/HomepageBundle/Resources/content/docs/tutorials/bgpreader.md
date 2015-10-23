BGPReader Tutorial
==================

<h1 class="text-danger">TODO: REVIEW THIS DOCUMENT</h1>


BGPReader is a command-line tool that is installed together with libBGPStream.
More details are available at @@link to bgpreader overview, whereas
the complete documentation is available at @@link to bgpreader docs.

The most used command-line options are:

~~~
$ bgpreader -w<start>[,<stop>] [-p <project] [-t <type] [-c collector] [-m]
~~~

<br>

Below we provide the following tutorials:

* [Replace bgpdump with bgpreader](#dump2reader)
* [1 second of Route Views Linx updates using the bgpdump format](#sample1sec)
* [BGP Stream elems observed by RIS collectors updates in 2 minutes](#sample2min)
* [RRC00 in real-time](#samplert)
* [A day of RIS RRC04 and RRC05 BGP records](#sampleday)
* [Other data interfaces: how to use singlefile](#other)


<br>

## Replace bgpdump with bgpreader  ##  {% verbatim %}{#dump2reader}{% endverbatim %}


**bgpreader** is a drop in replacement for **bgpdump**.

Suppose you want to process all the BGP information associated with
BGP records  generated by ___RIS RRC04___ and ___Route Views LINX___
in a ___20___ minutes time interval,  ___Sat, 10 Oct 2015 15:50:00
GMT___ to  ___Sat, 10 Oct 2015 16:10:00 GMT___.

With _bgpdump_ the user has to download each dump file that may contain
information associated with the two collectors within the time
interval desired (10 files, 80 MB). The processing script, ___my_script.pl___
receives an unsorted flow of data, also, it has implement some filter
in order to ignore the data that are outside the desired interval.

~~~
$ cd raw-data
$ wget http://data.ris.ripe.net/rrc04/2015.10/bview.20151010.1600.gz
$ wget http://data.ris.ripe.net/rrc04/2015.10/updates.20151010.1610.gz
$ wget http://data.ris.ripe.net/rrc04/2015.10/updates.20151010.1605.gz
$ wget http://data.ris.ripe.net/rrc04/2015.10/updates.20151010.1600.gz
$ wget http://data.ris.ripe.net/rrc04/2015.10/updates.20151010.1555.gz
$ wget http://data.ris.ripe.net/rrc04/2015.10/updates.20151010.1550.gz
$ wget http://archive.routeviews.org/route-views.linx/bgpdata/2015.10/RIBS/rib.20151010.1600.bz2
$ wget http://archive.routeviews.org/route-views.linx/bgpdata/2015.10/UPDATES/updates.20151010.1545.bz2
$ wget http://archive.routeviews.org/route-views.linx/bgpdata/2015.10/UPDATES/updates.20151010.1600.bz2
$ wget http://archive.routeviews.org/route-views.linx/bgpdata/2015.10/UPDATES/updates.20151010.1615.bz2
$ cd ..
$ find ./raw-data/ -type f | xargs -n 1 | bgpdump -m  | perl my_script.pl
~~~

<br>

With _bgpreader_ the same operation is accomplished with a single
command. The processing script, ___my_script.pl___ receives a sorted
flow of data that contains only the information associated with the
desired time interval. 

~~~
$ bgpreader -w1444492200,1444493400 -c rrc04-c route-views.linx  -m | perl my_script.pl
~~~

<br>

## 1 second of Route Views Linx updates using the bgpdump format {% verbatim %}{#sample1sec}{% endverbatim %}

The following command outputs (using the bgpdump -m format) the BGPStream elems contained in the
BGPStream records that comply with the following filters:

 * are contained in ___Updates dumps___ generated by the ___Route Views
   LINX collector___
 * their timestamp is exactly  ___Sat, 10 Oct 2015 17:34:02 GMT___ 

~~~
$ bgpreader -w1444498442,1444498442 -c route-views.linx -m
BGP4MP|1444498442|A|195.66.224.175|13030|46.219.122.0/24|13030 5580 21011 31148 31148 31148|IGP|195.66.224.175|0|1|65123:276 65123:2000 65123:2002 65123:10016 13030:1 13030:7208 13030:50000 13030:51107|AG|31148 94.76.105.10|
BGP4MP|1444498442|W|195.66.224.138|2914|209.212.8.0/24
BGP4MP|1444498442|W|195.66.224.138|2914|205.151.210.0/23
...
BGP4MP|1444498442|A|195.66.236.175|13030|118.193.51.0/24|13030 2828 10026 17444 17444 133115|IGP|195.66.236.175|0|1|13030:2 13030:2828 13030:51903 13030:7215|NAG||
BGP4MP|1444498442|A|195.66.236.175|13030|177.154.84.0/22|13030 12989 28640 262401 262401 262401 262401 262401 262401 262401 262401 262949|IGP|195.66.236.175|0|1|13030:1 13030:3 13030:50000 13030:51502 13030:7209|NAG||
BGP4MP|1444498442|A|195.66.236.175|13030|177.154.80.0/22|13030 12989 28640 262401 262401 262401 262401 262401 262401 262401 262401 262949|IGP|195.66.236.175|0|1|13030:1 13030:3 13030:50000 13030:51502 13030:7209|NAG||
~~~

<br>

The above command outputs 114  announcements,  and 11 withdrawals as
observed by 14 unique peer ASns.

<br>

## BGP Stream elems observed by RIS collectors updates in 2 minutes {% verbatim %}{#sample2min}{% endverbatim %}

The following command outputs the BGPStream elems contained in the
BGPStream records that comply with the following filters:

 * are contained in ___Updates dumps___ generated by ___RIS collectors___
 * their timestamp is in the interval ___Sat, 10 Oct 2015 17:34:00 GMT___ -  ___Sat, 10 Oct 2015 17:36:00 GMT___

~~~
$ bgpreader -w1444498440,1444498560 -p ris -t updates
U|A|1444498440|ris|rrc12|13237|80.81.192.74|212.93.166.0/24|80.81.192.74|13237 1299 174 39386 39386 39386 39386 39919|39919||
U|A|1444498440|ris|rrc12|13237|80.81.192.74|91.151.162.0/24|80.81.192.74|13237 1299 174 39386 39386 39386 39386 39919|39919||
U|A|1444498440|ris|rrc12|13237|80.81.192.74|212.93.177.0/24|80.81.192.74|13237 1299 174 39386 39386 39386 39386 39919|39919||
...
U|A|1444498560|ris|rrc12|13237|80.81.192.74|214.26.240.0/24|80.81.192.74|13237 1299 209 721 27065 1733 27067 5800|5800||
U|A|1444498560|ris|rrc12|25220|80.81.194.140|214.26.240.0/24|80.81.194.140|25220 3356 209 721 27065 1733 27067 5800|5800||
U|A|1444498560|ris|rrc12|25220|80.81.194.140|214.13.75.0/24|80.81.194.140|25220 3356 209 721 27065 1733 27067 5800|5800|| 
~~~

<br>

The above command outputs 181,118 announcements,  151 state messages,
and 7,882 withdrawals as observed by 13 collectors (and 209 unique
peer ASns).


<br>

## RRC00 in real-time {% verbatim %}{#samplert}{% endverbatim %}

The following command outputs the BGPStream elems contained in the
BGPStream records that comply with the following filters:

 * are contained in ___RIBs___ and ___ Updates dumps___ generated by    the ___RIS RRC00 collector___
 * their timestamp is greater or equal to  ___Sat, 15 Oct 2015 17:12:00 GMT___ 

~~~
$ bgpreader -w1444929120 -c rrc00 
U|A|1444929120|ris|rrc00|1836|2a01:2a8::3|2c0f:fe90::/32|2a01:2a8::3|1836 174 6453 30844 37105 37105 37105 36943|36943||
U|A|1444929120|ris|rrc00|1836|2a01:2a8::3|2c0f:fe90::/32|2a01:2a8::3|1836 6939 30844 37105 37105 37105 36943|36943||
U|A|1444929120|ris|rrc00|1836|146.228.1.3|168.128.104.0/21|146.228.1.3|1836 3356 2914 44568 44568|44568||
...
~~~

<br>

The above command continuosly (i.e. as soon as data are available to
the BGPStream broker) outputs BGPStream elem associated with RRC 00
generated BGPStream records. 

<br>

## A day of RIS RRC04 and RRC05 BGP records {% verbatim %}{#sampleday}{% endverbatim %}

The following command outputs information about the BGPStream records that comply with the following filters:

 * are contained in ___RIBs___ and ___ Updates dumps___ generated by
   the ___RIS RRC04___ and ___RIS RRC05 collectors___
 * their timestamp is in the interval ___Sat, 10 Oct 2015 00:00:00  GMT___ -  ___Sat, 10 Oct 2015 23:59:59 GMT___

~~~
$ bgpreader -w1444435200,1444521599 -c rrc04 -c rrc05 -r
U|E|1444434900|ris|rrc05|F|1444434900
R|B|1444435200|ris|rrc05|V|1444435200
R|B|1444435200|ris|rrc04|V|1444435200
R|M|1444435200|ris|rrc05|V|1444435200
...
U|M|1444521599|ris|rrc04|V|1444521300
U|M|1444521599|ris|rrc04|V|1444521300
U|M|1444521599|ris|rrc04|V|1444521300
U|M|1444521599|ris|rrc04|V|1444521300
~~~
<br>

The above command outputs information about 3,524,048 BGPStream
records of type rib and 4,384,306 BGPStream records of type update.

<br>
## Other data interfaces: how to use singlefile {% verbatim %}{#other}{% endverbatim %}

The following command outputs the BGPStream elems contained in the
BGPStream records that comply with the following filters:

 * are contained in ___http://archive.routeviews.org/bgpdata/2015.10/UPDATES/updates.20151016.1630.bz2___
 * their timestamp is in the interval ___Fri, 16 Oct 2015 16:30:00 GMT___ -  ___Fri, 16 Oct 2015 16:42:35 GMT___

~~~

$ bgpreader -d singlefile -oupd-file,http://archive.routeviews.org/bgpdata/2015.10/UPDATES/updates.20151016.1630.bz2  -w1445013000,1445013755
U|A|1445013000|singlefile_ds|singlefile_ds|13030|213.144.128.203|212.22.66.0/24|213.144.128.203|13030 12389 12389 12389 12389 12389 12389 41938 8359 50618 35189 201432|201432||
U|A|1445013000|singlefile_ds|singlefile_ds|3130|147.28.7.2|76.191.107.0/24|147.28.7.2|3130 11404 22059|22059||
U|A|1445013000|singlefile_ds|singlefile_ds|3130|147.28.7.2|64.34.125.0/24|147.28.7.2|3130 2914 7922 11404 22059|22059||
...
U|W|1445013755|singlefile_ds|singlefile_ds|2914|129.250.0.11|137.10.0.0/16|||||
U|A|1445013755|singlefile_ds|singlefile_ds|1299|80.91.255.137|185.13.64.0/22|80.91.255.137|1299 3356 44141|44141||
U|A|1445013755|singlefile_ds|singlefile_ds|1299|80.91.255.137|91.236.153.0/24|80.91.255.137|1299 174 202140|202140||
~~~

<br>

The above command outputs 114  announcements,  and 11 withdrawals as
observed by 14 unique peer ASns.
