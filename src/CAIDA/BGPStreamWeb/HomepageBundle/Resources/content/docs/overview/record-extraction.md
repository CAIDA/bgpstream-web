Record Extraction &amp; Packaging
=================================

The record extraction and packaging layer is implemented by
[libbgpstream]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':'libbgpstream'})}}),
the core library of the framework, which provides the following features:

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

Further Reading
---------------
* See the
  [libBGPStream API reference manual]({{ path('caida_bgpstream_web_homepage_docs', {'page':'api', 'subpage':'libbgpstream'})}})
* Take the
  [libBGPStream tutorial]({{ path('caida_bgpstream_web_homepage_docs', {'page':'tutorials', 'subpage':'libbgpstream'})}})
* Learn about the
  [record processing]({{ path('caida_bgpstream_web_homepage_docs', {'page':'overview', 'subpage':'record-processing'})}})
  components that use libBGPStream.
