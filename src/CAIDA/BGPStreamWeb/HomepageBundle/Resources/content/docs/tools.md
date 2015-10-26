Tools
=====

The BGPStream framework includes two tools for processing BGP data:
[BGPReader]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools',
'subpage': 'bgpreader'})}})
and
[BGPCorsaro]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools',
'subpage': 'bgpcorsaro'})}}).
These tools are both built using the
[libBGPStream]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'api'})}}) API.

<br>
BGPReader
---------

BGPReader is a simple command-line tool for generating ASCII output from a
stream of BGP data: perfect for eyeballing raw data, building shell one-liners
for exploratory analysis, and piping into external scripts.

Users familiar with the BGPdump tool should find using BGPReader easy; BGPReader
even supports the BGPdump `-m` output format, so in some cases BGPReader can be
used as a drop-in replacement for BGPdump. Moreover, since BGPReader provides
seamless out-of-the-box access to both the Route Views and RIS data archives,
users no longer need to manually acquire data, simply provide a time interval to
BGPReader and BGPStream will do the rest.

<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools',
'subpage': 'bgpreader'})}}"
    class="btn btn-primary btn-md">
    Get started with BGPReader &raquo;
</a>

<br>
BGPCorsaro
----------

BGPCorsaro is a powerful tool for continuous monitoring of BGPdata. BGPCorsaro
runs a pipeline of plugins that process data and extract derived information in
regular time bins. Plugins are easily extensible, so users with a specific
monitoring requirement can create a plugin tailored for their needs, simplifying
the task of creating production-quality realtime monitoring applications.

<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools', 'subpage': 'bgpcorsaro'})}}"
    class="btn btn-primary btn-md">
    Get started with BGPCorsaro &raquo;
</a>
