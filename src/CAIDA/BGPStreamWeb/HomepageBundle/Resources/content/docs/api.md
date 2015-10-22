BGPStream APIs
==============

As well as providing [tools]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools'})}}) to process BGP measurement data, BGPStream also includes programming APIs. 

<div class="row" markdown="1">
<div class="col-md-6" markdown="1">
<h2>libBGPStream (C API)</h2>
<div class="label-group">
<span class="label label-2"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Develop efficient code</span>
</div>
<div class="label-group">
<span class="label label-4"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Rapid prototyping</span>
<span class="label label-4"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Ad-hoc analysis</span>
</div>
<p markdown="1">
libBGPStream is the central library of the BGPStream framework. It is written
in C and presents a simple API for configuring and reading a stream of BGP
measurement data. All BGPStream tools as well as the PyBGPStream API make use
of libBGPStream.
</p>
<div>
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage': 'libbgpstream'})}}" class="btn btn-primary btn-md">Learn about libBGPStream &raquo;</a>
</div>
</div>
<div class="col-md-6" markdown="1">
<h2>PyBGPStream (Python API)</h2>
<div class="label-group">
<span class="label label-2"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Develop Python code</span>
</div>
<div class="label-group">
<span class="label label-4"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Rapid prototyping</span>
<span class="label label-4"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Ad-hoc analysis</span>
</div>
<div>
<p markdown="1">
PyBGPStream is Python package that provides bindings to the libBGPStream
library, allowing Python scripts to configure and read a stream of BGP
measurement data.
</p>
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage': 'pybgpstream'})}}" class="btn btn-primary btn-md">Learn about PyBGPStream &raquo;</a>
</div>
</div>
</div>
<br>
<div class="row" markdown="1">
<div class="col-md-6" markdown="1">
<h2>Metadata Broker (HTTP API)</h2>
<div class="label-group">
<span class="label label-2"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Access data-provider metadata</span>
</div>
<div class="label-group">
<span class="label label-4"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Track available data</span>
<span class="label label-4"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Add support for new data sources</span>
</div>
<div>
<p markdown="1">
The BGPStream Broker is a web application that provides a unified HTTP query
interface to retrieve metadata about data available from different data
providers.
</p>
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage': 'libbgpstream'})}}" class="btn btn-primary btn-md">Learn about the Broker HTTP API &raquo;</a>
</div>
</div>
</div>

