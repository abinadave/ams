<template>
    <div style="padding: 20px" >
        <div class="row">
           <!-- Delivery form -->

           <div class="form-group col-md-4">
            <label>Delivery Options</label>
            <!-- <input v-model="supplier_name" type="text" class="form-control" disabled> -->
            <select  class="form-control">
                <option disabled selected>Select</option>
                <option>Purchase Order</option>
                <option>APR</option>
            </select>
          </div>

           <div class="form-group col-md-4">
            <label>Choose Supplier Name (1)</label>
            <!-- <input v-model="supplier_name" type="text" class="form-control" disabled> -->
            <select v-model="form.supplier_id" @change="changeInSupplierId" class="form-control">
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
          </div>
           <div class="form-group col-md-4">
            <label>Date of Invoice</label>
            <input v-model="form.date_of_invoice" type="date" class="form-control" placeholder="Date of Invoice">

          </div>

          <!-- Apr No will show if supplier is procurement services or 1 -->
              <div v-show="form.supplier_id == 1" class="form-group col-md-9">
                 <span class="badge badge-secondary">APR DETAILS</span></h5>
              </div>

              

              <div v-show="form.supplier_id == 1" class="form-group col-md-4">
                <label>APR No</label>
                <input  @keyup="changedInAprNo" :disabled="!ifSupplierIsDBM"v-model="form.apr_no" type="number" class="form-control" disabled >
              </div>
              <div v-show="form.supplier_id == 1" class="form-group col-md-4">
                <label>Date of APR</label>
                <input :disabled="!ifSupplierIsDBM" v-model="form.date_of_apr" type="date" class="form-control">
              </div>
          <!-- Apr No will show if supplier is procurement services or 1 -->

          <div class="col-md-4">
              <!-- <hr> -->
          </div>
          <div class="col-md-12">
              <h5>
                <span class="badge badge-secondary">PURCHASE ORDER DETAILS</span></h5>
              <hr>
          </div>

              <!-- This are will be hidden once supplier is PROCUREMENT SERVICES -->
              <div v-show="form.supplier_id != 1" class="form-group col-md-4">
                <label>P.O (Year)</label> <b class="text-danger">*</b>
                <!-- <input :disabled="ifSupplierIsDBM" v-model="form.po_year" type="text" class="form-control" > -->
                <input v-model="form.po_year" type="text" class="form-control" disabled>
              </div>
              <div v-show="form.supplier_id != 1" class="form-group col-md-4">
                <label>P.O (Month)</label> <b class="text-danger">*</b>
                <select v-model="form.po_month" class="form-control" disabled>
                    <option v-bind:value="n" v-for="n in 12">
                        {{ n }}
                    </option>
                </select>
              </div>
              <div v-show="form.supplier_id != 1" class="form-group col-md-4">
                <label>P.O Number</label> <b class="text-danger">*</b>
                <input v-model="form.po_number" type="text" class="form-control" disabled>
              </div>
          
          <div class="form-group col-md-4">
            <label>Date of Delivery</label>
            <input v-model="form.date_of_delivery" type="date" class="form-control">
          </div>
           <div class="form-group col-md-4">
            <label>Date Received</label>
            <input v-model="form.date_received" type="date" class="form-control">
          </div>
          <div class="form-group col-md-4">
            <label>IAR No</label>
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
        <PurchaseItemList /><hr>
        <div v-for="error in po_errors">
            <div v-show="error" class="alert alert-primary" role="alert">
                {{error}}
            </div>
        </div>
        <div v-for="error in errors">
            <div v-show="error" class="alert alert-danger" role="alert">
                {{error}}
            </div>
        </div>
        <button :disabled="whileSaving" class="btn btn-success" @click="createDelivery">
            <span v-if="whileSaving">
                Saving please wait...
            </span>
            <span v-else>
                SUBMIT
            </span>
        </button>
    </div>
</template>

<script>
    import {mapActions, mapState, mapMutations, mapGetters } from 'vuex'
    import alertify from 'alertify.js'
    import PurchaseItemList from './PurchaseItemList'
    export default {
        mounted() {
            // first load whats up?
            this.fetch_suppliers();
        },
        components: {
            PurchaseItemList
        },
        computed: {
            ...mapState([
                'deliver_forms',
                'local_po_items',
                'suppliers',
                'current_po'
            ]),
            getSuppliername(){
                return 'good';
            },
            ifSupplierIsDBM(){
                let self = this;
                let rsSuppliers = _.filter(self.suppliers, {id: Number(self.form.supplier_id)});
                if (rsSuppliers.length) {
                    let supplier = _.first(rsSuppliers);
                    if (supplier.id == 1) {
                        return true;
                    }else {
                        return false;
                    }
                }else {
                    console.log('nothing was found')
                }
            },
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

                    apr_no: '',
                    date_of_apr: '',
                    
                    date_of_delivery: '',
                    date_received: '',
                    iar_no: '',
                    purpose: ''
                },
                supplier_name: '',
                errors: {
                    po_number: '',
                    purpose: ''
                }
            }
        },
        watch: {
            'current_po': function(newVal, oldVal){
                let po = newVal;
                let self = this;
                if(po.supplier_id == 1){
                    // procurement services
                    // with APR NO
                    self.form.apr_no = po.apr_no;
                    self.form.po_number = '';
                    self.form.po_year = '';
                    self.form.po_month = '';
                    self.search_apr_no(po.apr_no);
                }else {
                    // private suppliers
                    // WITH P.O Number
                    self.form.apr_no = '';
                    self.form.date_of_apr = '';

                    self.form.po_number = po.po_number;
                    self.form.po_year = po.po_year;
                    self.form.po_month = po.po_month;
                    self.search_po_number({
                        po_year: self.form.po_year,
                        po_month: self.form.po_month,
                        po_number: self.form.po_number
                    });
                }
            }
        },
        methods: {
            ...mapActions([
                'search_po_number',
                'save_delivery_form',
                'fetch_suppliers',
                'search_apr_no',
                'insert_ditems',
                'search_po_selections',
                'search_apr_no',
                'fetch_deliver_forms_view'
            ]),

            changedInAprNo(){
                let self = this;
                clearTimeout(self.timer);
                self.timer = setTimeout(function(){
                    let input = Number(self.form.apr_no);
                    if (input != null) {
                        self.search_apr_no(input).then(response => {
                            if (response.status == 200) {
                                let json = response.data;
                                alertify.success(json.purchase_items.length + ' item(s) was found for: APR NO:' + input);
                            }
                        });
                    };
                }, 750);
            },
            changeInSupplierId(){
                let self = this;
                self.search_po_selections(self.form.supplier_id).then((response) => {
                    if (response.status == 200) {
                        let json = response.data;
                        if (json.length) {
                            $('#modalPOSelection').modal('show');
                        }else {
                            alertify.alert('No P.O / APR N.O was found selected supplier');
                        }
                    }
                })
                if (Number(self.form.supplier_id) == 1){
                    self.form.po_year = '';
                    self.form.po_month = '';
                    self.form.po_number = '';
                    // alert('noli me tengene')
                    // GET THE A.P.R NUMBER
                    // var input = prompt("Please enter APR_NO", "APR_NO");

                    // if (input != null) {
                    //     self.search_apr_no(input).then(response => {

                    //     })
                    // };
                }else {
                    self.form.apr_no = '';
                }
            },
            validateLocalPoItems(){
                let self = this;
                let errors = [], item = {}, has_valid_received_qty = 0;
                for (var i = self.local_po_items.length - 1; i >= 0; i--) {
                    item = self.local_po_items[i];
                    if (Number(item.received) > 0) {
                        ++has_valid_received_qty;
                        if (Number(item.received) > Number(item.qty)) {
                            errors.unshift('Received qty should not be greater than the actual P.O item qty(s)');
                            item.valid = true;
                        }else {
                            item.valid = false;
                        }
                    }else {
                        item.valid = false;
                    }
                }
                if (!has_valid_received_qty) {
                    errors.unshift('0 or No valid item(s) received / qty received, Please enter received item qty');
                };
                return errors;
            },
            createDelivery(){
                let self = this;
                self.whileSaving = true;
                self.clearErrors();
                let errors = self.validateLocalPoItems();
                self.po_errors = errors;
                if (!errors.length) {
                    self.save_delivery_form(self.form).then(response => {
                        if(response.status == 200){
                            let json = response.data;
                            if (json.id > 0) {
                                self.save_deliver_items(json);
                            }
                        }else if(response.status == 422){
                            let errors = response.data.errors;

                            $.each(errors, function(index, val) {
                                self.errors[index] = val[0];
                            });
                        }
                        setTimeout(function(){
                            self.whileSaving = false;
                        }, 2000);
                    });
                }else {
                    
                    setTimeout(function(){
                        self.whileSaving = false;
                    }, 5000);
                }
                
                
            },
            save_deliver_items(json){
                let self = this;
                self.insert_ditems({
                    is_dbm: self.ifSupplierIsDBM,
                    deliver_form_id: json.id
                }).then((response) => {
                    // alert('done');
                    self.fetch_deliver_forms_view;
                })
                alertify.alert('Information saved!');
                self.clearForm();

            },
            clearForm(){
                let self = this;
                self.supplier_name = '';
                $.each(self.form, function(index, val) {
                    self.form[index] = '';
                });
                setTimeout(function(){
                    $('.po-no').focus();
                }, 1000);
            },
            clearErrors(){
                let self = this;
                self.errors.po_number = '';
                self.errors.purpose = '';
            },
            changeInPo(e){
                let self = this;
                let po_number = Number(e.target.value);
                clearTimeout(self.timer);
                self.timer = setTimeout(function(){
                    self.search_po_number(po_number).then((response) => {
                        if (response.status === 200) {
                            let json = response.data;
                            if (json.count > 0) {
                                let po = json.po;
                                // self.supplier_name = po.supplier_name;
                                self.supplier_name = po.supplier_name;
                                self.form.apr_no = po.apr_no;

                                //clear po_number error
                                self.errors.po_number = '';
                            }else {
                                setTimeout(function(){
                                    self.errors.po_number = 'No P.O record was associated with P.O Number: ' + po_number;
                                }, 500);
                            }
                        }
                    })
                }, 750);
            }
        }
    }
</script>
