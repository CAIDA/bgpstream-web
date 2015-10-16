
#include <stdio.h>
#include <inttypes.h>
#include <stdint.h>
#include "bgpstream.h"

static char buffer[1024];

int main() 
{
  /* Define the prefix to monitor e.g. 62.149.64.0/18 */
  bgpstream_pfx_storage_t my_pfx; 
  my_pfx.mask_len = 18;
  my_pfx.address.version = BGPSTREAM_ADDR_VERSION_IPV4;
  inet_pton(BGPSTREAM_ADDR_VERSION_IPV4, "62.149.64.0", &my_pfx.address.ipv4);

  /* Define the vantage point to consider */
  uint32_t vp_asn = 6079;
  
  /* Allocate memory for a bgpstream instance */ 
  bgpstream_t *bs = bs = bgpstream_create();
  /* Allocate memory for a re-usable bgprecord instance */
  bgpstream_record_t *bs_record = bgpstream_record_create(); 
  int get_next_ret;
  /* Declare a bgpelem pointer to read BGP information from a bgprecord */
  bgpstream_elem_t *bs_elem = NULL;
  
  /* Configure the sqlite interface and options */
  bgpstream_data_interface_id_t datasource_id;
  datasource_id = bgpstream_get_data_interface_id_by_name(bs,"broker");
  bgpstream_set_data_interface(bs, datasource_id);

  /* Set metadata filters */
  bgpstream_add_filter(bs, BGPSTREAM_FILTER_TYPE_COLLECTOR, "route-views.isc");
  // time interval -> Feb. 14th 2009
  bgpstream_add_interval_filter(bs,1234569600,1234656000); 

  /* Start the stream */
  bgpstream_start(bs);

  /* Read the stream of records */
  do
    {
      /* get next record */
      get_next_ret = bgpstream_get_next_record(bs, bs_record);
      if(get_next_ret && bs_record->status == BGPSTREAM_RECORD_STATUS_VALID_RECORD)
        {
          /* extract elems from the current record */
          while((bs_elem = bgpstream_record_get_next_elem(bs_record)) != NULL)
            {
              /* select only RIB entris, announcements and withdrawals */
              if(bs_elem->type == BGPSTREAM_ELEM_TYPE_RIB ||
                 bs_elem->type == BGPSTREAM_ELEM_TYPE_ANNOUNCEMENT ||
                 bs_elem->type == BGPSTREAM_ELEM_TYPE_WITHDRAWAL)
                {
                  /* select only elems that carry information for the selected vp */
                  if(bs_elem->peer_asnumber == vp_asn)
                    {
                      /* select only elems that carry information for the selected pfx */
                      if(bgpstream_pfx_storage_equal(&my_pfx, &bs_elem->prefix))
                        {
                          /* print the BGP information */
                          if(bgpstream_elem_snprintf(buffer, 1024, bs_elem) != NULL)
                            {
                              fprintf(stdout, "%s\n", buffer);
                            }
                        }
                    }
                }
            }          
        }
    } while(get_next_ret > 0);
    
  /* de-allocate memory for the bgpstream */
  bgpstream_destroy(bs);

  /* de-allocate memory for the bgpstream record */
  bgpstream_record_destroy(bs_record);

  return 0;
}
