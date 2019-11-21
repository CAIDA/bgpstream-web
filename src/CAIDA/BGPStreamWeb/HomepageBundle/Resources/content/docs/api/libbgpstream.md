C/C++ API (libBGPStream)
====================

libBGPStream is the core of the BGPStream framework; all
[tools]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools'})}}),
as well as the
[Python bindings]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage': 'pybgpstream'})}})
use libBGPStream.

libBGPStream provides functions to create, configure, and consume a stream of
BGP data records. Since libBGPStream is implemented in C, it is best suited for
use cases that require high performance. Users without such performance
restrictions may be interested in using the
[PyBGPStream Python bindings]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage': 'pybgpstream'})}}).

<br>
<div class="row">
<div class="col-md-4">
<p>
For <b>new users</b>, consider taking the libBGPStream tutorial which provides
step-by-step instructions about using the API, as well as fully-working example
code.
</p>
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'tutorials', 'subpage': 'libbgpstream'})}}"
    class="btn btn-primary btn-md">
    Take the libBGPStream tutorial &raquo;
</a>
</div>
<div class="col-md-4">
<p>
For more <b>experienced users</b>, we provide an extensive API reference manual,
documenting the public API.
</p>
<a href="{{ path('caida_bgpstream_web_homepage_docs_api', {'project': 'libbgpstream', 'file': 'bgpstream.h.html'})}}"
    class="btn btn-primary btn-md">
    Read the API reference manual &raquo;
</a>
</div>
<div class="col-md-4">
<p>
We also make all the libBGPStream code available on GitHub, and encourage
community contributions via Pull Requests.
</p>
<a href="https://github.com/caida/libbgpstream" target="_blank"
    class="btn btn-primary btn-md">
    Browse the GitHub repository &raquo;
</a>
</div>
</div>
