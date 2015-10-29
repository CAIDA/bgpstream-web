APIs
====

The BGPStream framework includes two programming APIs for accessing a stream of
BGP data from within other programs:
[libBGPStream]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage': 'libbgpstream'})}}),
a C API, and
[PyBGPStream]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage': 'pybgpstream'})}})
 a Python API.

libBGPStream
------------

libBGPStream is the core of the BGPStream framework; all [tools]({{
path('caida_bgpstream_web_homepage_docs', {'page': 'tools'})}}), as well as the
[Python bindings]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api',
'subpage': 'pybgpstream'})}}) use libBGPStream. libBGPStream is written in C,
making it very efficient. The API has also been designed to be as simple as
possible.

<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage': 'libbgpstream'})}}"
    class="btn btn-primary btn-md">
    Read more about libBGPStream &raquo;
</a>

PyBGPStream
-----------

PyBGPStream is our Python bindings to the libBGPStream C API. It offers the same
functionality (and much of the same efficiency) as the C API, but also the
flexibility of a Python module, allowing rapid prototyping and integration.

<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage': 'pybgpstream'})}}"
    class="btn btn-primary btn-md">
    Read more about PyBGPStream &raquo;
</a>
