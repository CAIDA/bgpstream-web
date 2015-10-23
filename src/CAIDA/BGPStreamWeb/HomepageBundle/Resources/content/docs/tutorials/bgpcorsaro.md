BGPCorsaro Tutorial
===================

<h1 class="text-danger">TODO: UPDATE THIS DOCUMENT</h1>

BGPCorsaro is a command-line tool  to continuously extract derived
data from a BGP stream in regular time bins.Its architecture is based
on a pipeline of plugins, which continuously process BGPStream records.
More details are available at @@link to bgpcorsaro overview, whereas
the complete documentation is available at @@link to bgpcorsaro docs.


The most used command-line options are:

~~~

$ bgpcorsaro -w <start>[,<end>] -x "plugin-name [<plugin-options>]" -O outfile-template [<options>]

~~~


Below we provide the following tutorials:

* [Using the pfxmonitor plugin](#pfxmonitor)
* [Creating a new plugin](#newplugin)


## Using the pfxmonitor plugin ##   {% verbatim %}{#pfxmonitor}{%endverbatim %}

pfxmonitor is a stateful plugin that monitors prefixes overlapping with a given set of IP address
ranges. For each BGPStream record, the plugin:

 1. selects only the RIB and Updates dump records related to prefixes
     that overlap with the given IP address ranges.
 2. tracks, for each <prefix, VP> pair, the ASN that originated the
     route to the prefix. At the end of each time bin, the plugin outputs
     the timestamp of the current bin, the number of unique prefixes
     identified and, the number of unique origin ASNs observed by all the VPs.

@@move the above to documentation?!

Below we describe how to use the pfxmonitor plugin to monitor the
prefixes that are usually originated by AS137 (GARR, the Italian
Academic and Research Network) over a period of one month.


### Step 1: collecting the list of prefixes to monitor

We can generate the list of prefixes announced by AS 137 by
redirecting the output of bgpreader to a file. We can focus on the
prefixes announced by AS137 on  Jan, 1 2015.

~~~

$ bgpreader -w1420070400,1420156799 -e | awk ... {print ...}
  130.136.0.0/16
  130.186.0.0/19
  130.192.0.0/16
  130.251.0.0/16
  131.114.0.0/16
  131.154.0.0/16
  131.175.0.0/16
  137.204.0.0/16
  ...
  193.43.18.0/23
  193.43.97.0/24
  194.119.192.0/19
  212.189.128.0/17
  90.147.0.0/16
~~~

Let's save the list of prefixes in a file *garr_pfxs_20150101.txt*.

### Step 2: running bgpcorsaro

Now that we have a list of prefixes to monitor we can run bgpcorsaro
using the following command:

~~~
$ bgpcorsaro -w1420070400,1422748800  -x"pfxmonitor -L ./garr_pfxs_20150101.txt -n 5 -M" -i 300 -O ./%X.txt
~~~

<br>

We configure bgpcorsaro to process BGPStream records from all
available collectors and all kinds of dumps (both RIBs and Updates)
for the entire month of January 2015.
~~~
-w1420070400,1422748800
~~~

<br>

We activate *pfxmonitor* , and we configure the plugin to:

* read the list of prefixes to monitor from *garr_pfxs_20150101.txt*
* consider a <prefix, origin ASn> pair only if it has been seen by at
   least *5* different peer ASns.
* consider in the analysis only the prefixes that match exactly one of the prefixes in
the list or if they are more specific.

~~~
-x"pfxmonitor -L ./garr_pfxs_20150101.txt -n 5 -M" 
~~~

<br>

We set the interval @@link to documentation to be *5 minutes*, and the
output file to be *pfxmonitor.txt*
~~~
-i 300 -O ./%X.txt
~~~



### Step 3: taking a look at the results



## Creating a new plugin ##   {% verbatim %}{#newplugin}{% endverbatim %}


