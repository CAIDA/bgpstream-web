Tutorials
=========

<h1 class="text-danger">TODO: UPDATE THIS DOCUMENT</h1>

libBGPStream C library tutorial
-------------------------------

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
{% include '@CAIDABGPStreamWebHomepageBundle/Resources/content/docs/code/bgpstream-tutorial.c' %}
~~~
