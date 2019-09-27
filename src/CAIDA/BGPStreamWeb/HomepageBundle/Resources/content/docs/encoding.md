Data Encoding
========================

The following are a few data encoding practices we apply in the design and implementation of libBGPStream and PyBGPStream.

## AS Path 

### AS Path Segment String Representation

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