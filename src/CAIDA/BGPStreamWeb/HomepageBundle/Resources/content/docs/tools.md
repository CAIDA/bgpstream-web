Tools
=====

The BGPStream framework includes two tools for processing BGP measurement data: _BGPReader_ and _BGPCorsaro_.

<div class="row" markdown="1">
<div class="col-md-6" markdown="1">
<h2>BGPReader</h2>
<div class="label-group">
<span class="label label-2"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> ASCII output</span>
</div>
<div class="label-group">
<span class="label label-8"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> BGPdump support</span>
<span class="label label-3"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Ad-hoc analysis</span>
</div>
<p markdown="1">
BGPReader is the simplest interface to BGPStream: a command-line tool for
extracting BGP measurement data in ASCII format. It can also be used as a drop-in
replacement for the legacy [bgpdump](@@) tool.
</p>
<div>
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools', 'subpage': 'bgpreader'})}}" class="btn btn-primary btn-md">Learn about BGPReader &raquo;</a>
</div>
</div>
<div class="col-md-6" markdown="1">
<h2>BGPCorsaro</h2>
<div class="label-group">
<span class="label label-2"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Continuous Monitoring</span>
<span class="label label-2"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Extensible plugins</span>
</div>
<div class="label-group">
<span class="label label-3"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Extensible</span>
<span class="label label-4"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Operational Monitoring</span>
<span class="label label-5"><span class="glyphicon glyphicon-tags" aria-hidden="true"></span> Efficient</span>
</div>
<p markdown="1">
BGPCorsaro is a powerful tool to continuously extract derived
data from a BGP stream in regular time bins. Its extensible
architecture is based on a pipeline of plugins, which
continuously process BGPStream records.
</p>
<div>
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools', 'subpage': 'bgpcorsaro'})}}" class="btn btn-primary btn-md">Learn about BGPCorsaro &raquo;</a>
</div>
</div>
</div>

