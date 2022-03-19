<template>
    <div>
       <div class="modal fade" id="CreateDeliveryForPurchaseOrder" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg-po-delivery" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Purchase Order Deliveries</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                    
                    <div class="row" style="font-size: 12px">
                           <!-- Delivery form -->
                           <div class="form-group col-md-4">
                            <label>Choose Supplier Name (1)</label>
                            <!-- <input v-model="supplier_name" type="text" class="form-control" disabled> -->
                            <select  v-model="form.supplier_id"  class="form-control" disabled>
                                <option value="">Choose One Supplier</option>
                                <option :value="supplier.id" v-for="supplier in suppliers">
                                    {{ supplier.name }}
                                </option>
                            </select>
                          </div>
                         <!--   <div class="form-group col-md-4">
                            <label>P.O Number</label>
                            <input @keyup="changeInPo" v-model="form.po_number" type="number" class="form-control po-no" placeholder="Enter P.O Number">
                            <small class="form-text text-danger warning-errors" v-show="errors.po_number">
                              <b>{{ errors.po_number }}</b>
                            </small>
                          </div> -->
                          
                          <div class="form-group col-md-4">
                            <label>Charge Invoice Number</label>
                            <input v-model="form.charge_invoice_no" type="number" class="form-control" placeholder="Charge invoice number">
                            <small class="form-text text-danger warning-errors" v-show="errors.charge_invoice_no">
                              <b>{{ errors.charge_invoice_no }}</b>
                            </small>
                          </div>

                           <div class="form-group col-md-4">
                            <label>Date of Invoice {date-mm-yyyy}</label>
                            <input v-model="form.date_of_invoice" type="date" class="form-control" placeholder="Date of Invoice">
                            <small class="form-text text-danger warning-errors" v-show="errors.date_of_invoice">
                              <b>{{ errors.date_of_invoice }}</b>
                            </small>
                          </div>

                          <div class="col-md-4">
                              <!-- <hr> -->
                          </div>
                          <div class="col-md-12">
                              <h5>
                                <span class="badge badge-secondary">PURCHASE ORDER DETAILS</span></h5>
                              <hr>
                          </div>

                              <!-- This are will be hidden once supplier is PROCUREMENT SERVICES -->
                              <div class="form-group col-md-4">
                                <label>P.O (Year)</label> <b class="text-danger">*</b>
                                <input v-model="form.po_year" type="text" class="form-control" disabled>
                              </div>
                              <div class="form-group col-md-4">
                                <label>P.O (Month) </label> <b class="text-danger">*</b>
                                <select v-model="form.po_month" class="form-control" disabled>
                                    <option v-bind:value="n" v-for="n in 12">
                                        {{ n }}
                                    </option>
                                </select>
                              </div>
                              <div class="form-group col-md-4">
                                <label>P.O Number</label> <b class="text-danger">*</b>
                                <input v-model="form.po_number" type="text" class="form-control" disabled>
                              </div>
                          
                          <div class="form-group col-md-4">
                            <label>Date of Delivery {date-mm-yyyy}</label>
                            <input v-model="form.date_of_delivery" type="date" class="form-control">
                            <small class="form-text text-danger warning-errors" v-show="errors.date_of_delivery">
                              <b>{{ errors.date_of_delivery }}</b>
                            </small>
                          </div>
                           <div class="form-group col-md-4">
                            <label>Date Received {date-mm-yyyy}</label>
                            <input v-model="form.date_received" type="date" class="form-control">
                            <small class="form-text text-danger warning-errors" v-show="errors.date_received">
                              <b>{{ errors.date_received }}</b>
                            </small>
                          </div>
                          <div class="form-group col-md-4">
                            <label>IAR No <i style="font-size: 10px">(When Complete delivery)</i></label>
                            <input v-model="form.iar_no" type="number" class="form-control">
                          </div>
                          <div class="form-group col-md-4">
                            <label>Purpose</label>
                            <input v-model="form.purpose" type="text" class="form-control" placeholder="Purpose">
                            <small class="form-text text-danger warning-errors" v-show="errors.purpose">
                              <b>{{ errors.purpose }}</b>
                            </small>
                          </div>
                        </div>
                        <PurchaseitemList />

                        <div v-show="qty_errors.length">
                            <!-- ERRORS: {{qty_errors.length}} -->
                            <div v-for="err in qty_errors">
                               <p class="text-danger">{{err}} </p>
                            </div>
                        </div>
                        <!-- {{form}} -->
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button :disabled="whileSaving" type="button" class="btn btn-primary" @click="savePoDelivery">SUBMIT</button>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<style scoped>
    .modal-lg-po-delivery {
        max-width: 50%;
    }
</style>
<script>
    import {mapState, mapActions, mapMutations} from 'vuex'
    import PurchaseitemList from './PurchaseItemList'
    import alertify from 'alertify.js'
    import moment from 'moment'
    export default {
        mounted() {
            let self = this;
            self.fetch_suppliers();
            $('#CreateDeliveryForPurchaseOrder').on('shown.bs.modal', function (e) {
              // do something...
                setTimeout(function(){
                    // yyyy-MM-dd

                    self.form.iar_no = 1234
                    self.form.charge_invoice_no = _.random(1,100000),
                    self.form.date_of_invoice = '2019-06-30',
                    self.form.date_of_delivery = '2019-06-30',
                    self.form.date_received = '2019-06-30',
                    self.form.purpose = 'Test purpose' + _.random(1,1111);
                }, 1000);
              // alert('shown')
            });            
        },
        computed: {
            ...mapState([
                'suppliers',
                'current_po',
                'local_po_items',
                'local_deliveries'
            ]),
        },
        components: {
            PurchaseitemList
        },
        methods: {
            ...mapActions([
                'fetch_suppliers',
                'db_save_po_delivery_form',
                'db_insert_deliver_items',
                'get_delivery_qty_balance',
                'update_po_delivery_completed'
            ]),
            ...mapMutations([
                'set_po_delivery_completed'
            ]),
            getIncompleteItems(){
                let self = this;
                let item = {}, accurate_bal = 0;
                let arr = [];
                for (var i = self.local_po_items.length - 1; i >= 0; i--) {
                    let item = self.local_po_items[i];
                    accurate_bal = self.getActualBalanceOfPurchaseItem(item);
                    // console.log('accurate_bal: ' + accurate_bal + ', for item: ' + item.name);
                    if (Number(accurate_bal) > 0) {
                        arr.push(item);
                    }
                }
                return arr;
            },
            clearForms(){
                let self = this;
                $.each(self.form, function(index, val) {
                     /* iterate through array or object */
                     self.form[index] = '';
                });
            },
            clearErrors(){
                let self = this;
                $.each(self.errors, function(index, val) {
                     /* iterate through array or object */
                     self.errors[index] = '';
                });
            },
            getActualBalanceOfPurchaseItem(item){
                let self = this;
                let po_item_id = item.id;
                let rsDeliveries = _.filter(self.local_deliveries, { purchase_item_id: Number(po_item_id)});
                let total_balance = Number(item.qty);
                if (rsDeliveries.length) {
                    let ditem = {};
                    for (var i = rsDeliveries.length - 1; i >= 0; i--) {
                        let ditem = rsDeliveries[i];
                        total_balance -= Number(ditem.delivered_qty);
                    }
                }
                return total_balance;
            },
            getBalanceOfPurchaseItem(item){
                let self = this;
                let po_item_id = item.id;
                let rsDeliveries = _.filter(self.local_deliveries, { purchase_item_id: Number(po_item_id)});
                let total_balance = Number(item.qty);
                if (rsDeliveries.length) {
                    let ditem = {};
                    for (var i = rsDeliveries.length - 1; i >= 0; i--) {
                        let ditem = rsDeliveries[i];
                        total_balance -= Number(ditem.delivered_qty);
                    }
                }
                if (item.received > 0) {
                    total_balance -= Number(item.received);
                }
                return total_balance;
            },
            validateDeliveryItems(){
              /** validation of items qty balance if correctly inputed **/
                let self = this;
                let arr = [], item = {}, po_item_id = 0, qty = 0, balance = 0, accurate_bal = 0;
                let incompleteItems = self.getIncompleteItems(); 
                alertify.log('incompleteItems: ' + incompleteItems.length)
                for (var i = incompleteItems.length - 1; i >= 0; i--) {
                    balance = 0; qty = 0; accurate_bal = 0;
                    item = incompleteItems[i];
                    po_item_id = item.id;
                    qty = Number(item.temp_received);
                    let rsDeliveries = _.filter(self.local_deliveries, { purchase_item_id: Number(po_item_id)});
                    // console.log('rsDeliveries length: ' + rsDeliveries.length)
                    if(rsDeliveries.length){
                        balance = self.getBalanceOfPurchaseItem(item);
                            // console.log('banance: ' + balance);
                            // console.log('received qty: ' + qty)
                        if (isNaN(qty)) {
                            arr.unshift('Unable to proceed without input for item: ' + item.name + ' and received qty is: 0');
                        }
                        if (Number(balance) < 0) {
                            accurate_bal = self.getActualBalanceOfPurchaseItem(item);;
                            arr.unshift('Invalid Qty received, exceeds the total balance for item: ' + item.name + ', balance: ' + accurate_bal + ', received qty: ' + qty);
                            // item.received = 0;
                        }
                    }else {
                        if (qty > Number(item.qty)) {
                            arr.unshift('Invalid Qty received, exceeds the total qty, for item: ' + item.name);
                            // item.received = 0;
                        };
                    };
                }
                // console.log(arr)
                return arr;
            },
            afterSaveDeliveryFormAndItems(){
                let self = this;
                let ids = _.map(self.local_po_items, 'id');
                self.get_delivery_qty_balance(ids).then((response) => {
                    if(response.status==200){
                        let incompleteItems = self.getIncompleteItems();
                        if (!incompleteItems.length) {
                            self.update_po_delivery_completed().then((response) => {
                                if (response.status == 200) {
                                    let json = response.data;
                                    self.set_po_delivery_completed({
                                        po_id: self.current_po.id,
                                        bool: 1
                                    });
                                    alertify.success('All items are delivered for this P.O');
                                }
                            });
                        }
                    }else {
                        alertify.log('Failed in fetching data');
                    }
                });
            },
            savePoDelivery(){
                let self = this;
                self.whileSaving = true;
                let arr_errors = self.validateDeliveryItems();
                if (!arr_errors.length) {   
                    self.qty_errors = [];                 
                    self.db_save_po_delivery_form(self.form).then((response) => {
                        if (response.status == 200) {
                            let json = response.data;
                            if (Number(json.id) > 0) {
                                self.db_insert_deliver_items({
                                    deliver_form: json,
                                    incomplete_items: self.getIncompleteItems()
                                }).then((response) => {

                                    // alert('incomplete_items: ' + self.getIncompleteItems().length);
                                    $('#CreateDeliveryForPurchaseOrder').modal('hide');
                                    self.clearForms();
                                    alertify.success('Information Saved');
                                    self.afterSaveDeliveryFormAndItems();
                                })
                                // alert('Information Saved');
                            }
                        }else if(response.status == 422) {
                            let errors_db = response.data.errors;
                            $.each(errors_db, function(index, val) {
                                 /* iterate through array or object */
                                 self.errors[index] = val[0];
                            });
                            alert('Please complete required fields.');
                        }
                        setTimeout(function(){
                            self.whileSaving = false;
                        }, 1000);
                    });
                }else {
                    self.qty_errors = arr_errors;
                    alertify.log('Pleave verify your Qty inputs it maybe invalid or unaccepted');
                    alert('Pleave verify your Qty inputs it maybe invalid or unaccepted');
                    setTimeout(function(){
                        self.whileSaving = false;
                    }, 3000);
                }
            }
        },
        watch: {
            'current_po': function(newVal, oldVal){
                let self = this;
                let current_po = newVal;
                // $.each(current_po, function(index, val) {
                     /* iterate through array or object */
                     // console.log('index: ' + index, ' value: ' + val)
                     self.form.supplier_id = current_po.supplier_id;
                     self.form.po_year = current_po.po_year;
                     self.form.po_month = current_po.po_month;
                     self.form.po_number = current_po.po_number;
                     self.form.po_year = current_po.po_year;
                // });
            }
        },
        data(){
            return {
                whileSaving: false,
                po_errors: [],
                form: {
                    supplier_id: '',
                    po_number: '',
                    po_year: '',
                    po_month: '',
                    charge_invoice_no: '',
                    date_of_invoice: '',
                    date_of_delivery: '',
                    date_received: '',
                    iar_no: '',
                    purpose: ''
                },
                supplier_name: '',
                /** this are the errors object  **/
                qty_errors: [],
                errors: {
                    po_number: '',
                    purpose: '',
                    po_year: '',
                    po_month: '',
                    charge_invoice_no: '',
                    date_of_invoice: '',
                    date_of_delivery: '',
                    date_received: '',
                    purpose: ''

                }
            }
        },
    }
</script>
