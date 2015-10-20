#!/usr/bin/env python

from _pybgpstream import BGPStream, BGPRecord, BGPElem
from collections import defaultdict


# create a new bgpstream instance and a reusable bgprecord instance
stream = BGPStream()
rec = BGPRecord()

# For each prefix, associate the list of announcing ASes
prefix_origin = defaultdict(set)

# configure the datasource interface
stream.set_data_interface('broker')

# set up filters
stream.add_filter('collector','route-views.sg')

stream.add_filter('record-type','ribs')
# time interval -> Sat, 01 Aug 2015 7:50:00 GMT -  08:10:00 GMT
stream.add_interval_filter(1438386600+8*3600,1438387800+8*3600)

# start the stream
stream.start()


while(stream.get_next_record(rec)):
    coll = rec.collector
    elem = rec.get_next_elem()
    while(elem):
        pfx = elem.fields['prefix']
        ases = elem.fields['as-path'].split(" ")
        if len(ases) > 0:
            origin = ases[-1]
            if pfx not in prefix_origin:
                prefix_origin[pfx].add(origin)
            else:
                if origin not in prefix_origin[pfx]:
                    prefix_origin[pfx].add(origin)
        elem = rec.get_next_elem()


for pfx in prefix_origin:
    if len(prefix_origin[pfx]) > 1:
        print pfx, ",".join(prefix_origin[pfx])



# for pfx in moas_prefixes:
#     for asn in prefix_origin[pfx]:
#         if asn not in moas_origin_info:
#             moas_origin_info[asn] = dict()
#             moas_origin_info[asn]["pfx"] = len(origin_prefix[asn])
#             moas_origin_info[asn]["moas-pfx"] = 1
#             moas_origin_info[asn]["moas-asns"] = set()
#             for other in prefix_origin[pfx]:
#                 if(other != asn):
#                     moas_origin_info[asn]["moas-asns"].add(other)
#         else:
#             moas_origin_info[asn]["moas-pfx"] += 1
#             for other in prefix_origin[pfx]:
#                 if(other != asn):
#                     moas_origin_info[asn]["moas-asns"].add(other)


# # Output results
# print "Origin prefix", len(origin_prefix)
# print "Prefix origin", len(prefix_origin)
# print "MOAS prefixes", len(moas_prefixes)

# print "MOAS ASns info:"
# for asn in moas_origin_info:
#     print asn, moas_origin_info[asn]["pfx"], moas_origin_info[asn]["moas-pfx"], len(moas_origin_info[asn]["moas-asns"])


