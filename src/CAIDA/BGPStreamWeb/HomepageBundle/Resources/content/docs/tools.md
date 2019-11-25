Tools
=====

The BGPStream framework includes a tool for processing BGP data:
[BGPReader]({{ path('caida_bgpstream_web_homepage_docs', {'page': 'tools',
'subpage': 'bgpreader'})}}).
It is built using the
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
