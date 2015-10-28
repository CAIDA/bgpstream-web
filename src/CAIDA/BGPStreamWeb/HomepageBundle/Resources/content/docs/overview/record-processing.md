Record Processing
=================

The record processing layer consists of all components that use the
[libBGPStream API]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':'libbgpstream'})}}).

We distribute BGPStream with several record processing components.

* _BGPReader_, a command-line tool to output BGP data in ASCII format
* _BGPCorsaro_, a tool that uses a modular plugin architecture to
extract statistics or aggregate data in regular time bins
* _PyBGPStream_, Python bindings to the libBGPStream API

<a href="{{ path('caida_bgpstream_web_homepage', {'page': 'components'}) }}"
    class="btn btn-primary btn-md">
    Learn more about the record processing components &raquo;
</a>

{# AK REMOVED BELOW AS IT SEEMS BETTER IN THE COMPONENTS PAGE

Below we compare these tools based on their efficiency and ease of use.

<br>

<div class="thumbnail">
<img src="{{ asset('bundles/caidabgpstreamwebhomepage/images/record-processing.png') }}" style="max-width: 80%;">
</div>


<br>

### BGPReader   {% verbatim %}{#bgpreader}{% endverbatim %}

<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools'})}}">BGPReader</a>
is a tool to output in ASCII format the BGPStream records
and elems matching a set of filters given via command-line
options. This tool is meant to support exploratory or ad-hoc analysis
using command line and scripting tools for parsing ASCII data.
Also, it can be thought of as a drop-in replacement of the analogous
<a href="https://bitbucket.org/ripencc/bgpdump/wiki/Home"
target="_blank">bgpdump</a> tool. 

* The complete BGPReader documentation is available at 
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page':'tools', 'subpage':'bgpreader'})}}">  this page</a>

* The BGPReader tutorials are available
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page':'tutorials', 'subpage':'bgpreader'})}}">  here </a>


<br>

### pyBGPStream   {% verbatim %}{#pybgpstream}{% endverbatim %}
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':'pybgpstream'})}}">pyBGPStream</a>
is a Python package that exports all the functions and data structures
provided by the libBGPStream C API. Even if an application implemented
in Python using pyBGPStream would not achieve the same performance as
an equivalent C implementation, pyBGPStream is an effective solution
for: rapid prototyping, implementing programs that are not
computationally demanding, or programs that are meant to be run
offline (i.e., there are no time constraints associated with a live
stream of data).


* The complete pyBGPStream documentation is available at 
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page':'api', 'subpage':'pybgpstream'})}}">  this page</a>

* The pyBGPStream tutorials are available
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page':'tutorials', 'subpage':'pybgpstream'})}}">  here </a>


<br>

### BGPCorsaro   {% verbatim %}{#bgpcorsaro}{% endverbatim %}
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools'})}}">BGPCorsaro</a>
is a a tool to continuously extract derived data from a BGP stream in
regular time bins. Its architecture is based on a pipeline of plugins,
which continuously process BGPStream records. BGPCorsaro is written in
C in order to support high-speed analysis of historical or live data
streams. 


* The complete BGPCorsaro documentation is available at 
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page':'tools', 'subpage':'bgpcorsaro'})}}">  this page</a>

* The BGPCorsaro tutorials are available
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page':'tutorials', 'subpage':'bgpcorsaro'})}}">  here </a>


<br>

#}