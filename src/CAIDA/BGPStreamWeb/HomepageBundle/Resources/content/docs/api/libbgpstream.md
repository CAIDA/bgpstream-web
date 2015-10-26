libBGPStream (C API)
====================

libBGPStream is the core of the BGPStream framework; all [tools]({{
path('caida_bgpstream_web_homepage_docs', {'page': 'tools'})}}), as well as the
[Python bindings]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api',
'subpage': 'pybgpstream'})}}) use libBGPStream.

libBGPStream provides functions to create, configure, and consume a stream of
BGP data records. Since libBGPStream is implemented in C, it is best suited for
use cases that require high performance. Users without such performance
restrictions may be interested in using the [PyBGPStream Python bindings]({{
path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':
'pybgpstream'})}}).

For new users, consider taking the [libBGPStream tutorial] which provides
step-by-step instructions about using the API, as well as fully-working example
code.

<div class="row" markdown="1">
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'tutorials',
'subpage': 'libbgpstream'})}}"
    class="btn btn-primary btn-md">
    Take the libBGPStream tutorial &raquo;
</a>
</div>

For more experienced users, we provide an extensive API reference manual,
documenting the public API.

<div class="row" markdown="1">
<a href="{{ path('caida_bgpstream_web_homepage_docs_apis', {'page': 'libbgpstream',
'subpage': 'bgpstream.h.html'})}}"
    class="btn btn-primary btn-md">
    Read the API reference manual &raquo;
</a>
</div>

We also make all the libBGPStream code available on GitHub, and encourage
community contributions via Pull Requests.

<div class="row" markdown="1">
<a href="https://github.com/caida/bgpstream"
    class="btn btn-primary btn-md">
    Browse the GitHub repository &raquo;
</a>
</div>
