<template>
    <div>
        <div class="modal fade" id="ModalDeliverItems" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delivery Record</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                 <form>
                    <DeliverFormDetails />
                 </form>
                 <table class="table table-condensed table-bordered tbl-dr-items" style="font-size: 14px">
                  <thead>
                    <tr>
                      
                      <th width="250" scope="col">Item name</th>
                      <th class="text-left" width="150">Item No</th>
                      <th scope="col">Unit</th>
                      <th class="text-center" scope="col">Delivered Qty</th>
                      <th class="text-right" scope="col">Unit Cost</th>
                      <th class="text-right" scope="col">Total Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="item in deliver_item_views">

                      <th scope="row" class="text-primary">{{ item.name }}</th>
                      <td class="text-left">{{ item.rca_code }}-{{item.rca_cat}}-{{item.rca_no}}</td>
                      <td>{{ item.unit_name }}</td>
                      <td class="text-center">{{ item.delivered_qty }}</td>
                      <td class="text-right">{{ item.unit_cost }}</td>
                      <td class="text-right">{{ item.delivered_qty * item.unit_cost | money_format }}</td>
                    </tr>
                    <tr style="font-size: 17px">
                        <td class="text-center" colspan="5">TOTAL</td>
                        <th class="text-right">P {{totalAmount}}</th>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div class="modal-footer">
                <!-- {{current_deliver_form}} -->
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
              </div>
            </div>
          </div>
        </div>
    </div>
</template>
<style scoped>
  .modal-lg {
      max-width: 60% !important;
  }
  .tbl-dr-items td, th{
    padding: 3px;
  }
</style>
<script>
    import accounting from 'accounting'
    import {mapState} from 'vuex'
    import DeliverFormDetails from './DeliverFormDetails'
    export default {
        mounted() {

        },
        components: {
            DeliverFormDetails
        },
        computed: {
            ...mapState([
                'deliver_item_views',
                'current_deliver_form'
            ]),
            totalAmount(){
                let self = this;
                let item = {}, total = 0;
                for (var i = self.deliver_item_views.length - 1; i >= 0; i--) {
                    item = self.deliver_item_views[i];
                    total += item.delivered_qty * item.unit_cost;
                };
                return accounting.formatNumber(total);
            }
        },
        filters: {
            money_format(n){
                return accounting.formatNumber(n);
            }
        }
    }
</script>
