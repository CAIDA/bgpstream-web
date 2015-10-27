BGPStream Framework Overview
============================

<h1 class="text-danger">TODO: UPDATE THIS DOCUMENT</h1>

Both BGP Stream and this documentation are still under active development and
features will likely change between versions.
Please contact [bgpstream-info@caida.org](mailto:bgpstream-info@caida.org) with any questions and/or suggestions.

The BGP Stream framework is organized in three layers.
From bottom up, these are:

* [data and meta-data access](#layer1)

* [records extraction and packaging](#layer2)

* [record processing](#layer3) 


<br>

<div class="thumbnail">
<img src="{{ asset('bundles/caidabgpstreamwebhomepage/images/components_schema.png') }}" style="max-width: 80%;">
</div>

<br>

1. All the tools and libraries are available for **download** at
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'download'}) }}">  this page</a>.

2. The **installation** istructions are listed 
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'install'}) }}">  here</a>.

3. We provide a set of  **tutorials** to get familiar with the libBGPStream and
pyBGPStream APIs as well as the BGPReader and BGPCorsaro tools at
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'tutorials'}) }}">  this page</a>.

4. The **APIs** are documented at
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api'})}}">  this page</a>,
the **tools** usage instructions are described at
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools'})}}">  this page</a>.


<br>

## Data and meta-data access layer   {% verbatim %}{#layer1}{% endverbatim %}

The data and meta-data access layer provides to the upper layer
information about BGP data availability as data annotations.
We provide a unified query interface to retrieve streams of data from
different 
<a href="{{ path('caida_bgpstream_web_homepage', {'page': 'data'})}}">data providers</a>
(e.g. Route Views, RIPE RIS) through a web service called
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':'broker'})}}">BGPStream Broker</a>
 which provides the following functionalities:

 * Provide meta-data to libBGPStream
 * Load balancing
 * Response windowing for overload protection
 * Support for live data processing
 
CAIDA operates a publicly accessible instance of the Broker that
libBGPStream is configured to query by default, the query APIs details
are available at the
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':'broker'})}}">Broker HTTP API page</a>.



<br>

## Record extraction and packaging layer  {% verbatim %}{#layer2}{% endverbatim %}
The record extraction and packaging layer is implemented by
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':'libbgpstream'})}}">  libBGPStream</a>,
the core library of the framework.

The C library offers the following functionalities:

* Transparent access to concurrent dumps
   * from multiple collectors, 
   * of different collector projects, and
   * of both RIB and Updates type
* Live data processing
* Data extraction, annotation and error checking
* Generation of a sorted (by timestamp) stream of BGP measurement data
* An API through which the user can specify and receive a stream

The libBGPStream user API provides the essential functions to
configure and consume a sorted stream of BGP measurement data and a
systematic organization of the BGP information into data structures. 

* The complete libBGPStream documentation is available at 
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page':'api', 'subpage':'libbgpstream'})}}">  this page</a>

* The libBGPStream tutorials are available
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page':'tutorials', 'subpage':'libbgpstream'})}}">  here </a>


<br>

## Record processing  {% verbatim %}{#layer3}{% endverbatim %}

The record processing layer consists of all the components that use the 
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':'libbgpstream'})}}"> libBGPStream’s API</a>.

We distribute BGPStream with the following independent modules:

* [**BGPReader**](#bgpreader), a command-line tool that outputs the requested BGP data
in ASCII format

* [**pyBGPStream**](#pybgpstream), Python bindings to the libBGPStream API

* [**BGPCorsaro**](#bgpcorsaro), a tool that uses a modular plugin architecture to
extract statistics or aggregate data that are output at regular time
bins

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

<!--

@@ notes for us:

this is the main page of the documentation. it should point people to everything they need to know

Things to describe (or create subpages for)

 - Architecture
 - Broker
 - libBGPStream
 - BGPCorsaro

Things to reference:

 - installation instructions
 - tools (bgpreader/bgpcorsaro)
 - API reference manuals
 - tutorials
 
 -->

