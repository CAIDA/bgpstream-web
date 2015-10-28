Record extraction and packaging layer
===========================

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

<br>
The libBGPStream user API provides the essential functions to
configure and consume a sorted stream of BGP measurement data and a
systematic organization of the BGP information into data structures. 

* The complete libBGPStream documentation is available at 
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page':'api', 'subpage':'libbgpstream'})}}">  this page</a>

* The libBGPStream tutorials are available
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page':'tutorials', 'subpage':'libbgpstream'})}}">  here </a>


<br>
