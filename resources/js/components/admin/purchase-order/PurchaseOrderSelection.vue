
<template>
    <div>
       <div class="modal fade" id="modalPOSelection" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">SUPPLIER: ----</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body" style="overflow: auto">
                  Total Purchase orders: {{ po_selection.length }}

                   <table class="table-po-selection table table-bordered" style="font-size: 12px; padding: 20px; width: 1500px">
                       <thead>
                           <tr>
                               <th width="70"></th>
                               <th>Supplier Name</th>
                               <th>Date</th>
                               <th>P.O Number</th>
                               <th>APR NO</th>
                               <th>P.O Category</th>
                               <th>Mode of Procurement</th>
                               <th>Total items</th>
                               <th>Total Amount</th>
                               <th>Items</th>
                           </tr>
                       </thead>
                       <tbody>
                           <tr v-for="po in po_selection">
                               <td class="text-center">
                                   <button style="padding: 4px" @click="selectThisPoForDelivery(po)" type="button" class="btn btn-secondary btn-sm">   
                                        Select
                                   </button>
                               </td>
                               <td>{{ po.supplier_name }}</td>
                               <td>{{ po.date | format_date }}</td>
                               <td>
                                  <span v-if="po.supplier_id > 1">
                                      {{ po.po_year }}-{{ po.po_month }}-{{ po.po_number }}
                                  </span>
                                  <span v-else>
                                      Not applicable
                                  </span>
                               </td>
                               <td>
                                  <span v-if="po.supplier_id == 1">
                                      {{ po.apr_no }}
                                  </span>
                                  <span v-else>
                                      Not applicable
                                  </span>
                               </td>
                               <td>{{ po.po_cat_name}}</td>
                               <td>
                                  <span v-if="po.total_amount > 50000">
                                    SMALL VALUE
                                  </span>
                                  <span v-else>
                                    SHOPPING
                                  </span>
                               </td>
                               <td>{{ po.count_items }}</td>
                               <td>{{ po.total_amount | convert_money }}</td>
                               <td :title="po.items">{{ po.items | substring_filter }}</td>
                           </tr>
                       </tbody>
                   </table>


            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-primary">Select</button>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<style scoped>
  .modal-lg {
      max-width: 80% !important;
  }
  .table-po-selection td {
        padding: 4px;
    }
  .table-po-selection th {
      padding: 4px;
  }
</style>
<script>
    import {mapActions, mapState, mapMutations } from 'vuex'
    import moment from 'moment'
    import accounting from 'accounting'
    export default {
        mounted() {
            // console.log('Component mounted.')
        },
        methods: {
            ...mapMutations([
                'set_current_purchase_order'
            ]),
            selectThisPoForDelivery(po){
                let self = this;
                self.set_current_purchase_order(po);
                $('#modalPOSelection').modal('hide');
            }
        },
        computed: {
            ...mapState([
              'po_selection'
            ])
        },
        filters: {
            convert_money(m){
                return accounting.formatNumber(m);
            },
            format_date(d){
                return moment(d).format('MMM DD, YYYY');
            },
            substring_filter(items){
                let self = this;
                if (items.length >= 100) {
                    return items.substring(0, 70) + '....';
                }else {
                    return items;
                }
            }
        },
    }
</script>
