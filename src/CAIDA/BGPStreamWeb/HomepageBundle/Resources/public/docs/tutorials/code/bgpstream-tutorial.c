#include <stdio.h>
#include "bgpstream.h"

int
main()
{
  /* Allocate memory for a bgpstream instance */
  bgpstream_t *bs = bs = bgpstream_create();
  if(!bs) {
    fprintf(stderr, "ERROR: Could not create BGPStream instance\n");
    return -1;
  }

  bgpstream_record_t *rec;

  /* The broker interface is set by default */

  /* Select bgp data from RRC06 and route-views.jinx collectors only */
  bgpstream_add_filter(bs, BGPSTREAM_FILTER_TYPE_COLLECTOR, "rrc06");
  bgpstream_add_filter(bs, BGPSTREAM_FILTER_TYPE_COLLECTOR, "route-views.jinx");

  /* Process updates only */
  bgpstream_add_filter(bs, BGPSTREAM_FILTER_TYPE_RECORD_TYPE, "updates");

  /* Select a time interval to process:
   * Sun, 10 Oct 2010 10:10:10 GMT -  Sun, 10 Oct 2010 11:11:11 GMT */
  bgpstream_add_interval_filter(bs,1286705410,1286709071);

  /* Start bgpstream */
  if(bgpstream_start(bs) < 0) {
    fprintf(stderr, "ERROR: Could not init BGPStream\n");
    return -1;
  }

  int ret;
  int get_next_ret = 0;
  int elem_counter = 0;

  /* Pointer to a bgpstream elem, memory is borrowed from bgpstream,
   * use the elem_copy function to own the memory */
  bgpstream_elem_t *elem;

  /* Read the stream of records */
  while((ret = bgpstream_get_next_record(bs, &rec))>0)
    {
      /* Get next record */
      /* Check if there are new records and if they are valid records */
      if(rec->status == BGPSTREAM_RECORD_STATUS_VALID_RECORD)
        {
          /* Get next elem in the record */
          while(bgpstream_record_get_next_elem (rec, &elem)>0)
            {
              elem_counter++;
            }
        }
    }

  /* Print the number of elems */
  printf("\tRead %d elems\n", elem_counter);

  /* De-allocate memory for the bgpstream */
  bgpstream_destroy(bs);

  return 0;
}
