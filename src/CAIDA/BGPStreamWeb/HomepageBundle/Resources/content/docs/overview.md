BGPStream Framework Overview
============================

<br>

<div class="thumbnail">
<img src="{{ asset('bundles/caidabgpstreamwebhomepage/images/components_schema.png') }}" style="max-width: 80%;">
</div>

The above image shows the layout of the components of the BGPStream framework.
<br>
There are three conceptual layers which are (from top-down):
<div class="row" style="padding-bottom: 15px;">
<div class="col-md-4">
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'overview', 'subpage':'record-processing'})}}"
    class="btn btn-primary btn-md" style="display: block;">
    Record Processing &raquo;
</a>
</div>
<div class="col-md-8">
Components that process BGP data using libBGPStream. E.g. BGPReader, BGPCorsaro, PyBGPStream.
</div>
</div>
<div class="row" style="padding-bottom: 15px;">
<div class="col-md-4">
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'overview', 'subpage':'record-extraction'})}}"
    class="btn btn-primary btn-md" style="display: block;">
    Record Extraction &amp; Packaging &raquo;
</a>
</div>
<div class="col-md-8">
The core of the BGPStream framework, implemented by libBGPStream.
</div>
</div>
<div class="row">
<div class="col-md-4">
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'overview', 'subpage':'data-access'})}}"
    class="btn btn-primary btn-md" style="display: block;">
    Data Access &raquo;
</a>
</div>
<div class="col-md-8">
Components providing access to diverse BGP data sources. Read about supported
data access interfaces if you want to use local/private dump files with BGPStream.
</div>
</div>

<br>

## Further Reading
Learn more about how to use BGPStream by reading the [tutorials]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'tutorials'}) }}).

See the
[BGPStream technical report]({{ path('caida_bgpstream_web_homepage', {'page': 'pubs'}) }}#bgpstream-tech-rep)
for an in-depth discussion of the architecture of BGPStream.

{#
## Further Reading

 - All the tools and libraries are released as open source, see the
 [download]({{ path('caida_bgpstream_web_homepage', {'page': 'download'}) }})
 page.
 - See the
 [installation]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'install'}) }})
 instructions for more information about getting BGPStream.
 - We provide a set of
 [tutorials]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'tutorials'}) }})
 to get familiar with the libBGPStream and PyBGPStream APIs as well as the BGPReader and BGPCorsaro tools.
 - To learn more about the APIs, see the
 [reference documentation]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api'})}}).
 - Also see the
 [usage information]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools'})}})
 for the BGPReader and BGPCorsaro tools.
 - We make available several
 [publications and presentations]({{ path('caida_bgpstream_web_homepage', {'page': 'pubs'}) }})
 about BGPStream.

<br>

## Note

Both BGP Stream and this documentation are still under active development and
features will likely change between versions.

Please contact [bgpstream-info@caida.org](mailto:bgpstream-info@caida.org) with any questions and/or suggestions.
#}