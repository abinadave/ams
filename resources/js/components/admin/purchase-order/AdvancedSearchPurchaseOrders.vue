<template>
    <div class="col-md-7">
      <p>
        <a data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
          Advanced Search
        </a>
      </p>

      <div class="collapse" id="collapseExample">
        <div class="card card-body">
             <form @submit.prevent="searchPo" class="row">

              <div class="form-group col-md-4">
                <label>Supplier name</label> <b class="text-danger">*</b>   
                <select @change="supplierIdChanged" v-model="form.supplier_id" class="form-control">
                    <option value="" selected disabled>Choose Supplier</option>
                    <option :value="sup.id" v-for="sup in suppliers">
                        {{ sup.name }}
                    </option>
                </select>
                
              </div>
              <div class="form-group col-md-4">
                <label>Supplier Address</label>
                <input v-model="supplier_name" type="text" class="form-control" disabled>
              </div>

              <br>
              <div class="col-md-4">
                  <!-- Po Detials -->
                  <!-- <hr> -->
              </div>
              <div class="col-md-12">
                  <h5> <span class="badge badge-secondary">Purchase Order Details</span></h5>
                  <hr>
              </div>
              <div @click="checkIfDisabledPo" class="form-group col-md-4">
                <label>P.O (Year)</label> <b class="text-danger">*</b>
                <input :disabled="ifSupplierIsDBM" v-model="form.po_year" type="text" class="form-control" placeholder="Enter year: YYYY">
              </div>
              <div @click="checkIfDisabledPo" class="form-group col-md-4">
                <label>P.O (Month 1-12)</label> <b class="text-danger">*</b>
               <!--  <select :disabled="ifSupplierIsDBM" v-model="form.po_month" class="form-control">
                    <option v-bind:value="n" v-for="n in 12">
                        {{ n }}
                    </option>
                </select> -->
                 <input :disabled="ifSupplierIsDBM" v-model="form.po_month" type="text" class="form-control">
              </div>
              <div @click="checkIfDisabledPo" class="form-group col-md-4">
                <label>P.O Number</label> <b class="text-danger">*</b>
                <input :disabled="ifSupplierIsDBM" v-model="form.po_number" type="number" class="form-control">
               
              </div>
              <div class="form-group col-md-4">
                <label>Complete P.O Number</label>
                <input type="text" class="form-control" v-model="getCompletePoNumber" disabled="">
              </div>
              <div class="col-md-12">
                  <hr>
              </div>
              
              
              <div class="form-group col-md-4">
                <label>Date of P.O</label> <b class="text-danger">*</b>
                <input v-model="form.date" type="date" class="form-control">
               
              </div>
            
              <div class="form-group col-md-4">
                <label>P.O Category</label> <b class="text-danger">*</b>
                <select v-model="form.po_category" class="form-control">
                    <option value="" selected disabled>Choose Category</option>
                    <option :value="po_cat.id" v-for="po_cat in po_categories">
                        {{ po_cat.name }}
                    </option>
                </select>
                
              </div>

              <div @click="checkIfDisabledAprNo" class="form-group col-md-4">
                <label>APR No.</label>
                <input :disabled="!ifSupplierIsDBM" v-model="form.apr_no" type="text" class="form-control">
              </div>

              <div class="form-group col-md-4">
                <label>Mode of Procurement</label>
                <input type="text" class="form-control" disabled>
              </div>
              <div class="form-group col-md-9">
               <button class="btn btn-primary">SEARCH</button>
              </div>
              
            </form>
        </div>
        <br>
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
                supplier_name: '',
                whileSaving: false,
                whileUpdating: false,
                form: {
                   supplier_id: '',
                   
                   po_year: '',
                   po_month: '',
                   po_number: '',

                   date: '',
                   po_category: '',
                   apr_no: ''
                }
            }
        },
        methods: {
            ...mapActions([
                'advanced_search_po',
                'fetch_deliver_forms_view'
            ]),
            supplierIdChanged(){
                let self = this;
                let supplier_id = self.form.supplier_id;
                if (supplier_id == 1) {
                    self.form.po_number = '';
                    self.form.po_year = '';
                    self.form.po_month = '';
                }else {
                    self.form.apr_no = '';
                }
            },
            checkIfDisabledAprNo(){
                let self = this;
                let rsSuppliers = _.filter(self.suppliers, {id: Number(self.form.supplier_id)});
                if (rsSuppliers.length) {
                    let supplier = _.first(rsSuppliers);
                    if (supplier.id > 1) {
                        alert('APR No. is disabled');
                    }
                }else {
                    // console.log('nothing was found');
                }
            },
            checkIfDisabledPo(){
                let self = this;
                let rsSuppliers = _.filter(self.suppliers, {id: Number(self.form.supplier_id)});
                if (rsSuppliers.length) {
                    let supplier = _.first(rsSuppliers);
                    if (supplier.id == 1) {
                        alertify.alert('P.O Number is disabled because Procurement Service [Supplier] is selected');
                    }else {
                        return false;
                    }
                }else {
                    console.log('nothing was found')
                }
            },
            clearAllForm(){
                // let self = this;
                // self.form.supplier_id = '';
                // self.form.po_number = '';
                // self.form.po_year = '';
                // self.form.po_month = '';
                // self.form.charge_invoice_no = '';
                // self.form.date_of_invoice = '';
                // self.form.apr_no = '';
                // self.form.date_of_apr = '';
                // self.form.date_of_delivery = '';
                // self.form.date_received = '';
                // self.form.iar_no = '';
                // self.form.purpose = '';
                // alertify.log('All form inputs cleared');
                // self.fetch_deliver_forms_view();
            },
            searchPo(){
                let self = this;
                self.advanced_search_po(self.form);
            }
        },
        computed: {
            ...mapState([
                'suppliers',
                'po_categories'
            ]),
            getCompletePoNumber(){
                let self = this;
                if (self.form.po_year && self.form.po_month && self.form.po_number) {
                    return self.form.po_year + '-' + self.form.po_month + '-' + self.form.po_number
                }else {
                    return 'Invalid P.O Number';    
                }
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
        }
    }
</script>
