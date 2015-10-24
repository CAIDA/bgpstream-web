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

<br>

Below we provide the following tutorials:

* [Using the pfxmonitor plugin](#pfxmonitor)
* [Creating a new plugin](#newplugin)

<br>


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


### Step 1: collecting the list of prefixes to monitor (GET OUTPUT FROM THOR)

We can generate the list of prefixes announced by AS 137 by
redirecting the output of bgpreader to a file. We can focus on the
prefixes announced by AS137 on  Jan, 1 2015.

~~~

$ bgpreader -w1420070400,1420156799  | awk -F"|" '(($2=="R" || $2=="A") && $11=="137"){print $8}' |  sort -u
  130.136.0.0/16
  130.186.0.0/19
  130.192.0.0/16
  ...
  193.43.18.0/23
  193.43.97.0/24
  194.119.192.0/19
  212.189.128.0/17
  90.147.0.0/16
  TODO!
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

TODO: run 

<br>

## Creating a new plugin ##   {% verbatim %}{#newplugin}{% endverbatim %}


This tutorial gives some background for the BGPCorsaro plugin
architecture and describes how one could go about designing and
implementing a new plugin.

Here we show how to implement a new plugin, *elemcounter*,
that counts the number of BGP elems processed each interval and
outputs such counters for each BGP elem type.

This tutorial consists of two steps:

 1. Create new files for the new plugin and adding references to the
 newly created plugin;
 2. Developing the new plugin logic.

### Creating Boilerplate Code

In order to develop a new plugin, it is necessary to edit 6 different
files. We use *elemcounter* as the name of the new plugin that we are
goin to develop. 

<br>

#### 1. Edit configure.ac

We add a new line to the configure file to add the new plugin to the
configuration. The last *[yes]* parameter indicates that the plugin is
going to be available by default, if set to *[no]* the user can make
it available at configuration time using the option *--with-plugin-elemcounter*.

~~~.ac
ED_WITH_PLUGIN([bgpcorsaro_pfxmonitor],[pfxmonitor],[PFXMONITOR],[yes])
ED_WITH_PLUGIN([bgpcorsaro_pacifier],[pacifier],[PACIFIER],[yes])

ED_WITH_PLUGIN([bgpcorsaro_elemcounter],[elemcounter],[ELEMCOUNTER],[yes])

# this MUST go after all the ED_WITH_PLUGIN macro calls
AC_DEFINE_UNQUOTED([ED_PLUGIN_INIT_ALL_ENABLED], $ED_PLUGIN_INIT_ALL_ENABLED,
		   [plugins to call the init macro for in bgpcorsaro_plugin.c])
~~~

<br>

#### 2. Edit bgpcorsaro/lib/bgpcorsaro_plugin.h

Add a new ID for the new plugin, and, if necessary, update the maximum
plugin ID to contain the maximum plugin ID value.


~~~.language-c
typedef enum bgpcorsaro_plugin_id
{
  /** Prefix Monitor plugin */
  BGPCORSARO_PLUGIN_ID_PFXMONITOR       = 1,

  /** Pacifier plugin */
  BGPCORSARO_PLUGIN_ID_PACIFIER         = 2,

  /** Elem Counter plugin */
  BGPCORSARO_PLUGIN_ID_ELEMCOUNTER      = 3,

  /** Maximum plugin ID assigned */
  BGPCORSARO_PLUGIN_ID_MAX              = BGPCORSARO_PLUGIN_ID_ELEMCOUNTER
} bgpcorsaro_plugin_id_t;
~~~

<br>

#### 3. Edit bgpcorsaro/lib/bgpcorsaro_plugin.c

Include the plugin's header file in the *bgpcorsaro_plugin.c* file,
i.e. add the following lines:

~~~.language-c
#ifdef WITH_PLUGIN_ELEMCOUNTER
#include "bgpcorsaro_elemcounter.h"
#endif
~~~

<br>

#### 4. Edit bgpcorsaro/lib/Makefile.am
Include the plugin's header and source files to the Makefile so that
it gets built with bgpcorsaro.

~~~.am
if WITH_PLUGIN_ELEMCOUNTER
PLUGIN_SRC+=bgpcorsaro_elemcounter.c bgpcorsaro_elemcounter.h
endif
~~~

<br>

#### 5. Create bgpcorsaro/lib/plugins/bgpcorsaro_elemcounter.h

Create the header file ([code]({{ asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/bgpcorsaro_elemcounter.h') }}))

~~~ .language-c
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/public/docs/tutorials/code/bgpcorsaro_elemcounter.h' %}
~~~

<br>

#### 6. Create bgpcorsaro/lib/plugins/bgpcorsaro_elemcounter.c

Below is the minimum set of functions that need to be defined for the
plugin to work properly. This file is a working template, i.e. no
operations are performed are performed by the plugin. A complete
implementation will be described in the next
[section](#implementation).
The template can be downloaded ([here]({{
asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/bgpcorsaro_elemcounter-template.c')
}})).


~~~ .language-c
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/public/docs/tutorials/code/bgpcorsaro_elemcounter-template.c' %}
~~~

<br>

#### 7. Build and run

In order to build the bgpcorsaro with the elemcounter plugin, it is
necessary to run the following commands:

~~~ 
$ ./autogen.sh
$ ./configure
   ...
   configure: ---- BGPCorsaro configuration ---- 
   checking for the monitor name to use... chiara-mbp.caida.org
   checking whether to monitor plugin runtimes... no
   checking whether to build with pfxmonitor plugin... yes
   checking whether to build with pacifier plugin... yes
   checking whether to build with elemcounter plugin... yes
   configure: ----------------------------------
   ...
$ make
$ make install
~~~

<br>

Autogen and configure are required as we modified the configure.ac and
the Makefile.am files. From now on, if we just modify the
*bgpcorsaro_elemcounter.c*  file, re-compiling requires only two
steps:

~~~
$ make
$ make install
~~~

<br>

We can verify that the elemcounter plugin is available by taking a
look at the output of the usage function: 

~~~
$ bgpcorsaro -h 
   ...
   -x <plugin>    enable the given plugin (default: all)*
                   available plugins:
                    - pfxmonitor
                    - pacifier
                    - elemcounter
                    use -p "<plugin_name> -?" to see plugin options
   ... 
~~~

If we run bgpcorsaro with *elemcounter* activated, no operations are
performed. Let's see in next [section](#implementation) how to
implement a useful plugin.

<br>

### Developing the elemcounter plugin {% verbatim %}{#implementation}{%endverbatim %}

First of all, let's define in a more precise way the output expected
from the plugin:

*At the end of each interval we want to output the number of RIB,
 announcement, withdrawal, and state message elems observed within the
 interval. We support two output formats, i.e.: single line (all
 counters written in a single line, default) and multiline (one line per
 counter type).*

Below we discuss the datastructures that we maintain in the state and
how we modify them each time the interval starts, ends, or a new
record is available.

<br>

#### elemcounter state: allocation, initialization, deallocation

We maintain two variables in the elemcounter state structure:

 * an array of int that will store the number of elems observed in
 each interval (for each type)
 * a flag to remember the output format specified by the user.

~~~.language-c
struct bgpcorsaro_elemcounter_state_t {

  /* elem counter array, one counter per 
   * BGPStream elem type*/
  int elem_cnt[5];

  /* 0 if the preferred output is singleline (default)
   * 1 if the preferred output is multiline */
  int multiline_flag;
  
};
~~~

<br>

The *elemcounter state*  is allocated and initialized using the
*bgpcorsaro_elemcounter_init_output* function, the state can also be
changed by the *parse_args* function that read the arguments passed to
the plugin.

~~~.language-c
/* in int bgpcorsaro_elemcounter_init_output(bgpcorsaro_t *bgpcorsaro) */
 ... 
/* allocate memory for state variables:
 * elemcounter does not have any dynamic memory variable
 * so no malloc is required */
  
/* initialize state variables */
int i;
for(i=0; i< 5; i++)
  {
    state->elem_cnt[i] = 0;
  }

/* single line is the default output format */
state->multiline_flag = 0; 
...
~~~

<br>

~~~.language-c
/* in static int parse_args(bgpcorsaro_t *bgpcorsaro) */
... 
while((opt = getopt(plugin->argc, plugin->argv, ":m?")) >= 0)
{
 switch(opt)
 {
   case 'm':
     state->multiline_flag = 1;
     break;
   case '?':
   case ':':
   default:
     usage(plugin);
	 return -1;
  }
 }
...
~~~

<br>

The *elemcounter state* is deallocated using the *bgpcorsaro_elemcounter_close_output* function.

~~~.language-c
/* in int bgpcorsaro_elemcounter_close_output(bgpcorsaro_t *bgpcorsaro) */
 ... 
if(state != NULL)
 {
  /* deallocate dynamic memory in state:
   * elemcounter does not have any dynamic memory variable
   * so no free is required  */
  bgpcorsaro_plugin_free_state(bgpcorsaro->plugin_manager,  PLUGIN(bgpcorsaro));
 }
...
~~~

<br>

#### elemcounter start interval, end interval and process record

At the beginning of each interval, we reset the counters.

~~~.language-c
int bgpcorsaro_elemcounter_start_interval(bgpcorsaro_t *bgpcorsaro,
                                          bgpcorsaro_interval_t *int_start)
{
  struct bgpcorsaro_elemcounter_state_t *state = STATE(bgpcorsaro);
  int i;
  /* reset counters */
  for(i=0; i< 5; i++)
    {
      state->elem_cnt[i] = 0;
    }
  return 0;
}
~~~

<br>

Each time bgpcorsaro provides a new BGP Stream record to process, we
extract the BGP Stream elems that it contains, and we increment the
counters.

~~~.language-c
int bgpcorsaro_elemcounter_process_record(bgpcorsaro_t *bgpcorsaro,
                                          bgpcorsaro_record_t *record)
{
  struct bgpcorsaro_elemcounter_state_t *state = STATE(bgpcorsaro);
  bgpstream_record_t *bs_record = BS_REC(record);
  bgpstream_elem_t *elem;

  /* consider only valid records */
  if(bs_record->status != BGPSTREAM_RECORD_STATUS_VALID_RECORD)
    {
      return 0;
    }
      
  while((elem = bgpstream_record_get_next_elem(bs_record)) != NULL)
    {
     /* increment the specific type counter */
     state->elem_cnt[elem->type]++;
     }
return 0;
}
~~~

<br>

At the end of the interval we output the counters using the format
specified from command line.

~~~.language-c
int bgpcorsaro_elemcounter_end_interval(bgpcorsaro_t *bgpcorsaro,
                                        bgpcorsaro_interval_t *int_end)
{
  struct bgpcorsaro_elemcounter_state_t *state = STATE(bgpcorsaro);

  /* int_end->time is a uint32_t time in epoch */
  
  /* single line output */
  if(state->multiline_flag == 0)
    {
      printf("%"PRIu32" R: %d A: %d W: %d S: %d\n", int_end->time,
             state->elem_cnt[BGPSTREAM_ELEM_TYPE_RIB],
             state->elem_cnt[BGPSTREAM_ELEM_TYPE_ANNOUNCEMENT],
             state->elem_cnt[BGPSTREAM_ELEM_TYPE_WITHDRAWAL],
             state->elem_cnt[BGPSTREAM_ELEM_TYPE_PEERSTATE]);
    }
  else
    { /* multi line output */
      printf("%"PRIu32" R: %d\n", int_end->time,
             state->elem_cnt[BGPSTREAM_ELEM_TYPE_RIB]);
      printf("%"PRIu32" A: %d\n", int_end->time,
             state->elem_cnt[BGPSTREAM_ELEM_TYPE_ANNOUNCEMENT]);
      printf("%"PRIu32" W: %d\n", int_end->time,
             state->elem_cnt[BGPSTREAM_ELEM_TYPE_WITHDRAWAL]);
      printf("%"PRIu32" S: %d\n", int_end->time,
             state->elem_cnt[BGPSTREAM_ELEM_TYPE_PEERSTATE]);
    }
  return 0;
}
~~~

<br>


#### Complete example

The complete example is available for ([download]({{ asset('bundles/caidabgpstreamwebhomepage/docs/tutorials/code/bgpcorsaro_elemcounter.c') }})).

~~~ .language-c
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/public/docs/tutorials/code/bgpcorsaro_elemcounter.c' %}
~~~

<br>

#### Compile and run

To compile the *elemcounter* plugin, use:

~~~
$ make
$ make install
~~~

<br>


Here we show how to run *elemcounter*  on the stream of RIBs and
Updates of Route Views Singapore for an hour, specifing the multiline
format, using an interval size of 5 seconds.

~~~
$ bgpcorsaro -w1420070395,1420073995 -c route-views.sg -i 1 -x"elemcounter -m " -O./%X.txt
1420070399 R: 0
1420070399 A: 65
1420070399 W: 6
1420070399 S: 0
1420070404 R: 1127127
1420070404 A: 23
1420070404 W: 5
1420070404 S: 0
...
~~~

<br>

There are 65 announcements, and 6 withdrawals observed in the first
interval, 1,127,127 RIB entries, 23 announcements, and 5 withdrawals
in the second interval.





