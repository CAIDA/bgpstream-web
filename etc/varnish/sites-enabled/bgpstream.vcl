backend bgpstream {
    .host = "192.172.226.208";
    .port = "8080";
}

sub bgpstream_vcl_recv {
    set req.backend_hint = bgpstream;

    # no cookies needed!
    unset req.http.Cookie;
}