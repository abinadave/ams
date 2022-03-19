<template>
    <div>
       <div id="accordion" class="col-md-8">
          <div>
            <button style="margin-top: -30px; font-size: 12px" class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                  Advance Search <i class="fa fa-plus"></i>
                </button>
            <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
              
              <div class="card-body">
                  <div class="row">
           <!-- Delivery form -->
           <div class="form-group col-md-4">
            <label>Choose Supplier Name</label>
            <!-- <input v-model="supplier_name" type="text" class="form-control" disabled> -->
            <select v-model="form.supplier_id" class="form-control">
                <option value="">Choose One Supplier</option>
                <option :value="supplier.id" v-for="supplier in suppliers">
                    {{ supplier.name }}
                </option>
            </select>
          </div>
          
          <div class="form-group col-md-4">
            <label>Charge Invoice Number</label>
            <input v-model="form.charge_invoice_no" type="number" class="form-control" placeholder="Charge invoice number">
          </div>
           <div class="form-group col-md-4">
            <label>Date of Invoice</label>
            <input v-model="form.date_of_invoice" type="date" class="form-control" placeholder="Date of Invoice">

          </div>
          <div class="form-group col-md-9">
             <span class="badge badge-secondary">APR DETAILS</span></h5>
          </div>
          <div class="form-group col-md-4">
            <label>APR No</label>
            <input :disabled="!ifSupplierIsDBM"v-model="form.apr_no" type="number" class="form-control" >
          </div>
          <div class="form-group col-md-4">
            <label>Date of APR</label>
            <input :disabled="!ifSupplierIsDBM" v-model="form.date_of_apr" type="date" class="form-control">
          </div>


          <div class="col-md-4">
              <!-- <hr> -->
          </div>
          <div class="col-md-12">
              <h5>
                <span class="badge badge-secondary">PURCHASE ORDER DETAILS</span></h5>
              <hr>
          </div>

          <div class="form-group col-md-4">
            <label>P.O (Year)</label> <b class="text-danger">*</b>
            <input :disabled="ifSupplierIsDBM" v-model="form.po_year" type="text" class="form-control" >
          </div>
          <div class="form-group col-md-4">
            <label>P.O (Month)</label> <b class="text-danger">*</b>
            <select :disabled="ifSupplierIsDBM" v-model="form.po_month" class="form-control">
                <option v-bind:value="n" v-for="n in 12">
                    {{ n }}
                </option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label>P.O Number</label> <b class="text-danger">*</b>
            <input :disabled="ifSupplierIsDBM" v-model="form.po_number" type="text" class="form-control">
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
          <div style="margin-left: -13px" class="form-group col-md-4">
              <button @click="clearAllForm" class="btn btn-default" style="margin-right: 1p0x">CLEAR ALL</button>
              <button @click="searchDeliveryForm" class="btn btn-primary">SEARCH</button>
          </div>
              </div>

            </div>
          </div>
        </div>
    </div>
</template>

<script>
    import alertify from 'alertify.js'
    import {mapState, mapActions} from 'vuex'
    export default {
        mounted() {

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
        methods: {
            ...mapActions([
                'advanced_search_delivery',
                'fetch_deliver_forms_view'
            ]),
            clearAllForm(){
                let self = this;
                self.form.supplier_id = '';
                self.form.po_number = '';
                self.form.po_year = '';
                self.form.po_month = '';
                self.form.charge_invoice_no = '';
                self.form.date_of_invoice = '';
                self.form.apr_no = '';
                self.form.date_of_apr = '';
                self.form.date_of_delivery = '';
                self.form.date_received = '';
                self.form.iar_no = '';
                self.form.purpose = '';
                alertify.log('All form inputs cleared');
                self.fetch_deliver_forms_view();
            },
            searchDeliveryForm(){
                let self = this;
                self.advanced_search_delivery(self.form);
            }
        },
        computed: {
            ...mapState([
                'suppliers'
            ]),
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
        }
    }
</script>
