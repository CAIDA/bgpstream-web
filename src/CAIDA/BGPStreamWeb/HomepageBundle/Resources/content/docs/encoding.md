Data Encoding
========================

The following are a few data encoding practices we apply in the design and implementation of libBGPStream and PyBGPStream.

## AS Path 

Each AS path contains a list of AS path segment separated by space.

Each AS Path segment is represented in following String format:

- If the segment is a simple ASN (`BGPSTREAM_AS_PATH_SEG_ASN`), then the string
  will be the decimal representation of the ASN (not dotted-decimal).
- If the segment is an AS Set (`BGPSTREAM_AS_PATH_SEG_SET`), then the string
  will be a comma-separated list of ASNs, enclosed in braces. E.g.,
  "{12345,6789}".
- If the segment is an AS Confederation Set
  (`BGPSTREAM_AS_PATH_SEG_CONFED_SET`), then the string will be a
  comma-separated list of ASNs, enclosed in brackets. E.g., "[12345,6789]".
- If the segment is an AS Confederation Sequence
  (`BGPSTREAM_AS_PATH_SEG_CONFED_SEQ`), then the string will be a
  space-separated list of ASNs, enclosed in parentheses.
  E.g., "(12345 6789)".
- If the segment is an unknown type (this should not happen), then the
  string will be a space-separated list of ASNs, enclosed in angle
  brackets.  E.g., "<12345 6789>".
  
*Note:* it is possible to have a set/sequence with only a single element.

## Prefix

IP prefix is represented normally as `NETWORK_ADDR/MASK` for both IPv4 and IPv6 prefixes.

For example, you may see announcements from Google with prefixes of `8.8.8.0/24` or `2001:4860::/32`.

## Community Value

Community values (see [RFC1997](https://tools.ietf.org/html/rfc1997) and [RFC8642](https://tools.ietf.org/html/rfc8642)) in BGP announcements are represented by a number of community value segment separated by space.
Each community value segment is represented as `ASN:VALUE`, where `ASN` is the AS number of the 
AS that originally set the community value, and `VALUE` is the actual community value.
Both `ASN` and `VALUE` are represented as 16 bit numbers.

For example, a community value `10000:65535` means `AS10000` originally set `NO_EXPORT` community value,
and this update should only be propagated internally within the destination AS via iBGP.

## Record Type, Position, and Status

In BGPStream we have two types of records: `RIB` (BGP RIB table dump) and `UPDATE` (BGP update), represented as the following:

- `R`: BGP RIB dump
- `U`: BGP update

For BGP RIB dump, we also represent the position of the dump as:

- `B`: start of dump
- `M`: middle of dump
- `E`: end of dump

Each record has a status, represented as:

- `V`: valid record
- `F`: filtered source
- `E`: empty source
- `O`: outside time interval
- `S`: corrupted source
- `R`: corrupted record
- `U`: unsupported record

In most of the cases, you will likely to see valid record (`V`) in your stream.

For example, from the bgpreader tutorial, you can see the following output:
```
$ bgpreader -w 1445306400,1445306402 -c route-views.sfmix -r
R|B|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|B|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|M|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
R|M|1445306400.000000|routeviews|route-views.sfmix|||V|1445306400
```
This command outputs all the records collected by `route-views.sfmix` between `1445306400` and `1445306402`.
The first column shows the record type, which is a RIB dump (`R`).
The second column shows the position of the record in the resource, which we see it starts as start (`B`), and the middle (`M`).
The second to last column shows the status of the record, which all are valid (`V`) in this example .

## Element Type

Each record may contain multiple elements. For example, an BGP update message may contain announcements,
withdrawals all in the same message.

Each element can be of the following type:

- `R`: RIB table entry
- `A`: prefix announcement
- `W`: prefix withdrawal
- `S`: peer state change

## Peer States

A peer of a BGP route collectors can have one of the following states:

- `IDLE`
- `CONNECT`
- `ACTIVE`
- `OPENSENT`
- `OPENCONFIRM`
- `ESTABLISHED`
- `CLEARING`
- `DELETED`

For a peer state update, BGPStream shows both the new and old states, represented as above.

## Resource

Each resource used in BGPStream can be identified by the following unique string representation:
`PROJECT.COLLECTOR.RECORD_TYPE.INITIAL_TIME.DURATION`.

- `PROJECT`: project of the resource (e.g. routeviews or rrc) 
- `COLLECTOR`: name of the collector (e.g. rrc02)
- `RECORD_TYPE`: the type of records this resource contains `ribs` or `updates`
- `INITIAL_TIME`: the start time of the resource, represented by the Unixtime in integer
- `DURATION`: the duration of data this resource includes, represented by number of seconds
