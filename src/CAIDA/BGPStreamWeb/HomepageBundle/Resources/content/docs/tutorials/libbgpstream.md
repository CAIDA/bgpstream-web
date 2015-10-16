libBGPStream Tutorial
=====================

<h1 class="text-danger">TODO: UPDATE THIS DOCUMENT</h1>

Below is a simple example that shows how to use most of the C library
API functions. The example is fully functioning and it can be run within the *test*
folder included in the distribution.

    $ cd bgpstream-1.0.0/test
    $ gcc ./tutorial.c  -lbgpstream -o ./tutorial
    $ ./tutorial
     Read 1544 elems


The program reads information from the example sqlite
database provided with the distribution and it counts the elems that match
the filters (collectors, record type, and time).

~~~ .language-c
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/content/docs/tutorials/code/bgpstream-tutorial.c' %}
~~~


Example 1: prefix logging
-------------------------------

In the following example, the program uses the broker service to get all the BGP information collected by
RouteViews' route-views.isc collector, and it prints out all the RIB entries, announcements, and withdrawals
related to 62.149.64.0/18 prefix as observed by AS 6079 on February, 14th 2009.

    $ gcc -o bgpstream-pfx-log -L/path-to-bgpstream/lib -lbgpstream bgpstream-pfx-log.c -I/path-to-bgpstream/include
    $ ./bgpstream-pfx-log
     # 01:18 RIB
     1234574297|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 03:18 RIB
     1234581530|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 05:19 RIB
     1234588763|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 07:19 RIB
     1234595997|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 09:20 RIB
     1234603231|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 11:10:08 announcement (hijack starts)
     1234609808|198.32.176.126|6079|A|62.149.64.0/18|198.32.176.126|6079 5400 8895|8895||
     # 11:21 RIB
     1234610463|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 5400 8895|8895||
     # 12:42:02 announcement (hijack ends)
     1234615322|198.32.176.126|6079|A|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 13:21 RIB
     1234617696|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 15:22 RIB
     1234624930|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 17:22 RIB
     1234632162|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 19:23 RIB
     1234639395|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 21:23 RIB
     1234646628|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||
     # 23:24 RIB
     1234653862|198.32.176.126|6079|R|62.149.64.0/18|198.32.176.126|6079 1273 39386 39386 25019|25019||

The output shows the state of 62.149.64.0/18 over time. We highlighted the two announcements that signals
the beginning and the end of one of the hijack events studied in
http://www.cs.arizona.edu/~bzhang/paper/12-imc-hijack.pdf


~~~ .language-c
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/content/tutorials/code/bgpstream-pfx-log.c' %}
~~~


Example 2: monitor to prefix AS PATHs
-------------------------------------

In the following example, the program uses the broker service to get a RIB collected by rrc00, 
a collector that establishes multi-hop BGP peering session with X monitors all around the globe,
in order to characterized the AS PATHs between each monitor and a prefix


    $ gcc -o bgpstream-pfx-aspaths -L/path-to-bgpstream/lib -lbgpstream bgpstream-pfx-aspaths.c -I/path-to-bgpstream/include
    $ ./bgpstream-pfx-aspaths
     # 37989 -> 195 
     1442275206|2405:fc00::6|37989|R|2001:48d0::/35|2405:fc00::6|37989 4844 6939 2152 2153 195 195|195||
     # 7018 -> 195 (6 hops)
     1442275206|2001:1890:111d:1::63|7018|R|2001:48d0::/35|2001:1890:111d:1::63|7018 6939 2152 2153 195 195|195||
     # 22652 -> 195 (6 hops)
     1442275206|2607:fad8::1:9|22652|R|2001:48d0::/35|2607:fad8::1:9|22652 6939 2152 2153 195 195|195||
     # 29608 -> 195 (6 hops)
     1442275206|2a01:678::2|29608|R|2001:48d0::/35|2a01:678::2|29608 6939 2152 2153 195 195|195||
     # 57381 -> 195 (7 hops)
     1442275206|2001:67c:24e4:1::1|57381|R|2001:48d0::/35|2001:67c:24e4:1::1|57381 42708 6939 2152 2153 195 195|195||
     # 6881 -> 195 (6 hops)
     1442275206|2a02:38::2|6881|R|2001:48d0::/35|2a02:38::2|6881 6939 2152 2153 195 195|195||
     # 50304 -> 195 (6 hops)
     1442275206|2a02:20c8:1f:1::4|50304|R|2001:48d0::/35|2a02:20c8:1f:1::4|50304 6939 2152 2153 195 195|195||
     # 57821 -> 195 (6 hops)
     1442275206|2001:67c:26f4::1|57821|R|2001:48d0::/35|2001:67c:26f4::1|57821 6939 2152 2153 195 195|195||
     # 1836 -> 195 (6 hops)
     1442275206|2a01:2a8::3|1836|R|2001:48d0::/35|2a01:2a8::3|1836 6939 2152 2153 195 195|195||
     # 8758 -> 195 (7 hops)
     1442275206|2001:8e0:0:ffff::9|8758|R|2001:48d0::/35|2001:8e0:0:ffff::9|8758 8758 6939 2152 2153 195 195|195||
     
The output shows the reachability of 2001:48d0::/35. 
