## Data and meta-data access layer   {% verbatim %}{#layer1}{% endverbatim %}

The data and meta-data access layer provides to the upper layer
information about BGP data availability as data annotations.
We provide a unified query interface to retrieve streams of data from
different 
<a href="{{ path('caida_bgpstream_web_homepage', {'page': 'data'})}}">data providers</a>
(e.g. Route Views, RIPE RIS) through a web service called
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':'broker'})}}">BGPStream Broker</a>
 which provides the following functionalities:

 * Provide meta-data to libBGPStream
 * Load balancing
 * Response windowing for overload protection
 * Support for live data processing
 
CAIDA operates a publicly accessible instance of the Broker that
libBGPStream is configured to query by default, the query APIs details
are available at the
<a href="{{ path('caida_bgpstream_web_homepage_docs', {'page': 'api', 'subpage':'broker'})}}">Broker HTTP API page</a>.



<br>
