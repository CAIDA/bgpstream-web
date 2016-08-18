import std;

backend bgpstream {
    .host = "192.172.226.208";
    .port = "8080";
}

//acl comet {
//    "198.202.112.0/21"; // comet
//}

//acl caida {
//    "192.172.226.0/24"; // caida-net
//    "198.202.64.0/18";  // sdsc
//}

sub bgpstream_vcl_recv {
    set req.backend_hint = bgpstream;

    # no cookies needed!
    unset req.http.Cookie;

    if (req.http.X-Forwarded-For !~ ",") {
        set req.http.xff = req.http.X-Forwarded-For;
    } else {
        set req.http.xff = regsub(req.http.X-Forwarded-For,
                                  "^(.*),.+$", "\1");
    }

    //if (std.ip(req.http.xff, "0.0.0.0") ~ caida) {
        set req.http.X-bgp-archive = "caida";
    //} else {
    //    set req.http.X-bgp-archive = "public";
    //}
}

sub bgpstream_vcl_hash {
    hash_data(req.http.X-bgp-archive);
}

sub bgpstream_vcl_deliver {
    set resp.http.X-bgp-archive = req.http.X-bgp-archive;
}
