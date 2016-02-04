vcl 4.0;

#in /etc/rc.conf:
#varnishd_enable="YES"
#varnishd_config="/usr/local/etc/varnish/default.vcl"
#varnishd_listen=":8080"
#varnishd_storage="malloc,10G"

include "/usr/local/etc/varnish/sites-enabled/bgpstream.vcl";
include "/usr/local/etc/varnish/sites-enabled/charthouse.vcl";

sub vcl_recv {
    if (!req.http.Host) {
        return (synth(404, "Missing Host header"));
    }
    #set req.http.Host = regsub(req.http.Host, "^www\.", "");
    set req.http.X-Conf-Host = regsub(req.http.Host, ":\d+", "");

    if (req.http.X-Conf-Host == "charthouse.caida.org" ||
        req.http.X-Conf-Host == "charthouse-dev.caida.org") {
        call charthouse_vcl_recv;
    } else if (req.http.X-Conf-Host == "bgpstream.caida.org" ||
        req.http.X-Conf-Host == "bgpstream-dev.caida.org") {
        call bgpstream_vcl_recv;
    } else {
        return (synth(404, "Invalid Host header"));
    }
}

sub vcl_backend_response {
    if (bereq.http.X-Conf-Host == "charthouse.caida.org" ||
        bereq.http.X-Conf-Host == "charthouse-dev.caida.org") {
        call charthouse_vcl_backend_response;
    }
}

sub vcl_deliver {
    if (req.http.X-Conf-Host == "charthouse.caida.org" ||
        req.http.X-Conf-Host == "charthouse-dev.caida.org") {
        call charthouse_vcl_deliver;
    } else if (req.http.X-Conf-Host == "bgpstream.caida.org" ||
              req.http.X-Conf-Host == "bgpstream-dev.caida.org") {
        call bgpstream_vcl_deliver;
    }
}

sub vcl_hash {
    if (req.http.X-Conf-Host == "bgpstream.caida.org" ||
        req.http.X-Conf-Host == "bgpstream-dev.caida.org") {
        call bgpstream_vcl_hash;
    }
}
