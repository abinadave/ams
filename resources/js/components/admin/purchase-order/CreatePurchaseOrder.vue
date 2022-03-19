<template>    
    <div style="margin: 40px" class="col-md-7">
        <p>po_updating: {{ po_updating }}</p>
      <i>Essential information is marked with an asterisk (<b class="text-danger">*</b>)</i><br><br>
        <form class="row">

          <div class="form-group col-md-4">
            <label>Supplier name</label> <b class="text-danger">*</b>   
            <select @change="supplierIdChanged" v-model="form.supplier_id" class="form-control">
                <option value="" selected disabled>Choose Supplier</option>
                <option :value="sup.id" v-for="sup in suppliers" :disabled="sup.id == 1">
                    {{ sup.name }} 
                </option>
            </select>
            <small class="form-text text-danger warning-errors" v-show="errors.supplier_id">
              <b>{{ errors.supplier_id }}</b>
            </small>
          </div>
          <div class="form-group col-md-4">
            <label>Supplier Address</label>
            <input v-model="supplier_name" type="text" class="form-control">
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
          <div class="form-group col-md-4">
            <label>P.O (Year)</label> <b class="text-danger">*</b>
            <input :disabled="ifSupplierIsDBM" v-model="form.po_year" type="text" class="form-control" placeholder="Enter year: YYYY">
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
            <input :disabled="ifSupplierIsDBM" @keyup="changeInPoNumber" v-model="form.po_number" type="number" class="form-control">
            <small class="form-text text-danger warning-errors" v-show="errors.po_number">
              <b>{{ errors.po_number }}</b>
            </small>
          </div>
          <div class="form-group col-md-4">
            <label>Complete P.O Number</label>
            <input type="text" class="form-control" v-model="getCompletePoNumber" disabled="">
          </div>
          <div class="col-md-12">
              <hr>
          </div>
          
          <div class="form-group col-md-4">
            <label>Date of P.O (dd/mm/yyyy)</label> <b class="text-danger">*</b>
            <input v-model="form.date" type="date" class="form-control">
            <small class="form-text text-danger warning-errors" v-show="errors.date">
              <b>{{ errors.date }}</b>
            </small>
          </div>
         
          <div class="form-group col-md-4">
            <label>P.O Category</label> <b class="text-danger">*</b>
            <select v-model="form.po_category" class="form-control">
                <option value="" selected disabled>Choose Category</option>
                <option :value="po_cat.id" v-for="po_cat in po_categories">
                    {{ po_cat.name }}
                </option>
            </select>
            <small class="form-text text-danger warning-errors" v-show="errors.po_category">
              <b>{{ errors.po_category }}</b>
            </small>
          </div>
         
          <div class="form-group col-md-4">
            <label>Mode of Procurement</label>
            <input v-model="mode_of_procurement" type="text" class="form-control" disabled>
           </div>
        </form>
        
        <CreatePoItem />
        <div v-show="!po_updating">
            <button :disabled="whileSaving" @click="SavePurchaseOrder" class="btn btn-primary">
                <span v-if="whileSaving">
                    <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i> <span>Saving Please wait.....</span>
                </span>
                <span v-else>
                    SUBMIT
                </span>
            </button>
        </div>

        <div v-show="po_updating">
            <button @click="updatePurchaseOrder" class="btn btn-info">
                <span v-if="whileUpdating">
                    <i class="fa fa-spinner fa-pulse fa-2x fa-fw"></i> <span>Saving Please wait.....</span>
                </span>
                <span v-else>
                    UPDATE
                </span>
            </button>
        </div>

        <div v-show="errors_all.length" style="margin-top: 30px">
          <div class="alert alert-danger" role="alert">
            <div v-for="error in errors_all" >
                {{ error }}
            </div>
          </div>
        </div>

        <div v-show="local_item_errors.length" style="margin-top: 30px">
          <div class="alert alert-warning" role="alert">
            <div v-for="error in local_item_errors" >
                {{ error }}
            </div>
          </div>
        </div>
    </div>

</template>
<style scoped>
    .warning-errors {
        font-weight: bolder;
    }
</style>
<script>
    import {mapActions, mapState, mapGetters, mapMutations} from 'vuex'
    import CreatePoItem from './po_items/CreatePOItem'
    import alertify from 'alertify.js'
    export default {
        mounted() {
            let self = this;
            setTimeout(function(){
                self.fetch_POCategories();
            }, 2000);
        },
        data(){
            return {
                supplier_name: '',
                whileSaving: false,
                whileUpdating: false,
                form: {
                   supplier_id: '',
                   
                   po_year: '2019',
                   po_month: 1,
                   po_number: '',

                   date: '',
                   po_category: '',
                },
                errors: {
                   supplier_id: 0,
                   po_number: '',
                   date: '',
                   po_category: 0,
                },
                errors_all: [],
                local_item_errors: []
            }
        },
        methods: {
            ...mapActions([
                'fetch_POCategories',
                'save_purchase_order',
                'save_purchase_items',
                'validate_po_number',
                'fetch_purchase_orders'
            ]),
            ...mapMutations([
                'clear_local_items'
            ]),
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
            updatePurchaseOrder(){
                let self = this;
                // self.
            },
            changeInPoNumber(e){
                let self = this;
                clearTimeout(self.timer);
                self.timer = setTimeout(function(){
                    let po_number = self.form.po_number;
                    if (isNaN(po_number)) {
                        alert('not a number')
                    }else {
                        self.validate_po_number(po_number).then((response) => {
                            if(response.status == 422){
                                let errors = response.data.errors;
                                let arr_errors = [];
                                $.each(errors, function(index, val) {
                                    self.errors[index] = val[0];
                                    arr_errors.push(val[0]);
                                });
                                self.errors_all = arr_errors;
                            }else if(response.status == 200){
                                self.errors.po_number = '';
                            }
                        });
                    }
                }, 750);
                
            },
            clearForm(){
                let self = this;
                self.form.supplier_id = '';
                self.form.po_number = '';
                self.form.date = '';
                self.form.po_category = '';
                self.form.supplier_name = '';
            },
            validateLocalItems(){
                let self = this;
                self.local_item_errors = [];
                if(!self.local_items.length){
                    self.local_item_errors.unshift('No valid item selected');
                }else if(!self.getTotalLocalItems){
                    self.local_item_errors.unshift('No valid qty, unit_cost are given');
                }

                // let item = {}, amount = 0;
                // for (var i = self.local_items.length - 1; i >= 0; i--) {
                //     item = self.local_items[i];
                //     amount = Number(item.qty) * Number(item.unit_cost);
                // };
            },
            supplierIdChanged(){
                let self = this;
                let supplier_id = self.form.supplier_id;

                let rsSuppliers = _.filter(self.suppliers, {id: Number(supplier_id)});

                if (rsSuppliers.length) {
                    let sup = _.first(rsSuppliers);
                    self.supplier_name = sup.address;
                }
            },
            clearErrors(){
                let self = this;
                self.errors_all = [];
                self.errors.supplier_id = '';
                self.errors.po_number = '';
                self.errors.date = '';
                self.errors.po_category = '';
            },
            SavePurchaseOrder(){
                let self = this;
                self.whileSaving = true;
                self.clearErrors();
                self.validateLocalItems();
                if (self.local_item_errors.length) {
                    alertify.alert(self.local_item_errors);
                    setTimeout(function(){
                      self.whileSaving = false;
                    }, 350);
                }else {
                    self.save_purchase_order(self.form).then((response) => {
                        if(response.status == 422){
                            let errors = response.data.errors;
                            let arr_errors = [];
                            $.each(errors, function(index, val) {
                                self.errors[index] = val[0];
                                arr_errors.push(val[0]);
                            });
                            self.errors_all = arr_errors;
                        }else if(response.status == 200){
                            self.save_purchase_items(response.data).then((response) => {
                                self.fetch_purchase_orders();
                                if (response.status === 200) {
                                    self.clear_local_items();
                                    self.clearForm();
                                    alertify.success('Purchase Order Information Saved');
                                };
                            });
                        }
                        setTimeout(function(){
                            self.whileSaving = false;
                        }, 1000);
                    })
                }
            },
            CheckModeOfProcurement(n){
                let self = this;
                if (Number(n) > 50000) {
                    return 'SMALL-VALUE';
                }else {
                    return 'SHOPPING';
                }
            }, 
        },
        computed: {
            ...mapState([
                'po_categories',
                'suppliers',
                'local_items',
                'po_updating',
                'purchase_order_form'
            ]),
            ...mapGetters([
              'getTotalLocalItems'
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
            mode_of_procurement(){
                let self = this;
                let total = 0, item = {};
                for (var i = self.local_items.length - 1; i >= 0; i--) {
                    item = self.local_items[i];
                    total += Number(item.qty) * Number(item.unit_cost);
                }
                
                return self.CheckModeOfProcurement(total);
            }
        },

        components: {
            CreatePoItem
        },
        watch: {
            'po_updating': function(newVal, oldVal){
                let self = this;
                setTimeout(function(){
                    self.form.po_number = self.purchase_order_form.po_number;
                    self.form.po_category = self.purchase_order_form.po_category;
                    self.form.date = self.purchase_order_form.date;
                    self.form.supplier_id = self.purchase_order_form.supplier_id;
                    let rsSuppliers = _.filter(self.suppliers, {id: Number(self.purchase_order_form.supplier_id)});
                    if (rsSuppliers.length) {
                        let supplier = _.first(rsSuppliers);
                        self.supplier_name = supplier.name;
                    };
                    if (!newVal) {
                        self.form.po_number = '';
                        self.form.po_category = '';
                        self.form.date = '';
                        self.form.supplier_id = '';
                        self.supplier_name = '';
                    };
                }, 2000);

            }
        }
    }
</script>
