<template>
    <div>
        <div class="modal fade" id="ModalPoDeliveries" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delivery for this P.O/APR</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                      <div>
                         <form class="row">
                           <!--  <div class="form-group col-md-3">
                              <label>APR No.</label>
                              <input v-model="current_po.apr_no" type="text" class="form-control">
                            </div> -->
                            <div class="col-md-12 row" v-if="c_delivery_is_po">
                                <div class="form-group col-md-3">
                                  <label>P.O Number</label>
                                  <input v-model="get_po_number" type="text" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                  <label>P.O Total Amount</label>
                                  <input v-model="current_total_amount" type="text" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                  <label>P.O Date</label>
                                  <input v-model="get_current_po_date" type="text" class="form-control">
                                </div>
                               
                                <div class="form-group col-md-3">
                                  <label>Category of P.O</label>
                                  <input v-model="current_po.po_cat_name" type="text" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                  <label>Mode of Procurement</label>
                                  <input v-model="get_mode_of_procurement" type="text" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12 row" v-else>
                                  <div class="form-group col-md-3">
                                    <label>APR No.</label>
                                    <input v-model="current_apr_form.apr_no" type="text" class="form-control">
                                  </div>
                                  <div class="form-group col-md-3">
                                    <label>APR Date</label>
                                    <input :value="current_apr_form.apr_date | date_format" type="text" class="form-control">
                                  </div>
                                  <div class="form-group col-md-3">
                                    <label>Date Encoded (System)</label>
                                    <input :value="current_apr_form.created_at | date_time_format" type="text" class="form-control">
                                  </div>
                            </div>
                         </form>
                      </div>
                      <div style="overflow: auto">
                          <!-- <div class="col-md-6" style="overflow: auto"> -->
                               <table class="table table-dforms table-bordered table-condensed table-hover" style="font-size: 12px; width: 1500px">
                                    <thead class="thead-dark">
                                      <tr>
                                        <th scope="col">SUPPLIER</th>
                                        <th scope="col">
                                              <span v-if="c_delivery_is_po">
                                                  P.O Number
                                              </span>
                                              <span v-else>
                                                  APR Number
                                              </span>
                                        </th>
                                        <th scope="col">PURPOSE</th>
                                        <th scope="col">TOTAL ITEMS</th>
                                        <th scope="col">IAR #</th>
                                        <th scope="col">DATE OF DELIVERY</th>
                                        <th scope="col">DATE RECEIVED</th>
                                        <th scope="col">CHARGE INVO. #</th>
                                        <th scope="col">DATE OF INVOICE</th>
                                        <th scope="col">DELIVERY AMOUNT</th>
                                        <th width="100"></th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                      <tr v-for="deliver_form in local_dforms">
                                        <th scope="row">
                                        
                                          <span v-if="c_delivery_is_po">
                                                {{deliver_form.name}}
                                          </span>
                                          <span v-else>
                                              PROCUREMENT SERVICES
                                          </span>
                                        </th>
                                        <td >
                                            <span v-if="!c_delivery_is_po">
                                                {{ deliver_form.apr_no }}
                                            </span>
                                            <span v-else>
                                                {{deliver_form.po_year}}-{{deliver_form.po_month}}-{{deliver_form.po_number}}
                                            </span>
                                        </td>
                                        <td>{{ deliver_form.purpose }}</td>
                                        <td>{{ deliver_form.count_items }}</td>
                                        <td>{{ deliver_form.iar_no }}</td>
                                        <td>{{ deliver_form.date_of_delivery | date_format }}</td>
                                        <td>{{ deliver_form.date_received | date_format }}</td>
                                        <td>{{ deliver_form.charge_invoice_no }}</td>
                                        <td>{{ deliver_form.date_of_invoice | date_format }}</td>
                                        <th><b>{{ deliver_form.delivery_total_amount | money_format }}</b></th>
                                        <td>
                                            <button @click="showDeliveryItems(deliver_form)" type="button" class="btn btn-primary btn-sm">view items</button>
                                        </td>
                                      </tr>
                                     
                                    </tbody>
                               </table>
                          <!-- </div> -->

                      </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
  .table-dforms td {
        padding: 4px;
    }
    .table-dforms th {
        padding: 4px;
    }
</style>
<script>
    import moment from 'moment'
    import accounting from 'accounting'
    import {mapState, mapMutations, mapActions} from 'vuex'
    export default {
        mounted() {

        },
        methods: {
            ...mapMutations([
                'set_current_deliver_form'
            ]),
            ...mapActions([
                'get_deliver_items'
            ]),
            showDeliveryItems(form){
                let self = this;
                $('#ModalDeliverItems').modal('show');
                self.get_deliver_items(form.id);
                self.set_current_deliver_form(form);
            }
        },
        computed: {
            ...mapState([
                'local_dforms',
                'current_po',
                'c_delivery_is_po',
                'current_apr_form'
            ]),
            get_mode_of_procurement(){
                let self = this;
                if (self.current_po.total_amount > 50000) {
                    return 'SMALL VALUE';
                }else {
                  return 'SHOPPING';
                }
            },
            current_total_amount(){
                let self = this;
                return accounting.formatNumber(self.current_po.total_amount);
            },
            get_current_po_date(){
                let self = this;
                return moment(self.current_po.date).format('MMM DD, YYYY');
            },
            get_po_number(){
                let self = this;
                if (self.current_po.supplier_id != 1) {
                    return self.current_po.po_year + '-' + self.current_po.po_month + '-' + self.current_po.po_number;
                }else {
                    return 'Not-applicable';
                }
            }
        },
        filters: {
            money_format(m){
                return accounting.formatNumber(m);
            },
            date_format(d){
                return moment(d).format('MMM DD, YYYY');
            },
            date_time_format(d){
                return moment(d).format('MMM DD, YYYY HH:mm a');
            }
        },
    }
</script>
