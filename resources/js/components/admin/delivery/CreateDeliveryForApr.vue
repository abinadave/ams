
<template>
    <div>
       <div class="modal fade" id="CreateDeliveryForApr" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg-apr-delivery" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Procurement Services Delivery</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <h5>APR DETAILS</h5>
                <div class="row">

                  <div class="form-group col-md-3">
                    <label>Supplier</label>
                    <select class="form-control" disabled>
                        <option value="">Procurement Services</option>
                    </select>
                  </div>
                  <div class="form-group col-md-3">
                    <label>Apr No.</label>
                    <input v-model="current_apr_form.apr_no" type="text" class="form-control" disabled>
                  </div>
                  <div class="form-group col-md-3">
                    <label>Apr Date</label>
                    <input :value="current_apr_form.apr_date | format_date" type="text" class="form-control" disabled>
                  </div>
                  <div class="form-group col-md-3">
                    <label>APR Total Amount</label>
                    <input :value="current_apr_form.total_amount |
                    format_money" type="text" class="form-control" disabled>
                  </div>
              
                </div>
                <hr>
                <h5 style="display: inline">Delivery Details | &nbsp;&nbsp;</h5><b class="text-danger" style="font-size: 14px; display: inline">(*) Required fields</b>
                <div class="row" style="margin-top: 20px">
                  <div class="form-group col-md-3">
                    <label>*Charge Invoice</label>
                    <input v-model="form.charge_invoice_no" type="text" class="form-control charge-invoice">
                    <small class="form-text text-danger" v-show="errors.charge_invoice_no">{{errors.charge_invoice_no}}</small>
                  </div>
                  <div class="form-group col-md-3">
                    <label>*Date of Invoice 
                        <i v-show="notInvalidDate(form.date_of_invoice) != 'Invalid date'" style="font-size: 10px">({{ form.date_of_invoice | format_date }})</i>
                    </label>
                    <input v-model="form.date_of_invoice" type="date" class="form-control">
                    <small class="form-text text-danger" v-show="errors.date_of_invoice">{{errors.date_of_invoice}}</small>
                  </div>
                  <div class="form-group col-md-3">
                    <label>*Date of Delivery
                        <i v-show="notInvalidDate(form.date_of_delivery) != 'Invalid date'" style="font-size: 10px">({{ form.date_of_delivery | format_date }})</i>
                    </label>
                    <input v-model="form.date_of_delivery" type="date" class="form-control">
                    <small class="form-text text-danger" v-show="errors.date_of_delivery">{{errors.date_of_delivery}}</small>
                  </div>
                  <div class="form-group col-md-3">
                    <label>*Date of Received
                        <i v-show="notInvalidDate(form.date_received) != 'Invalid date'" style="font-size: 10px">({{ form.date_received | format_date }})</i>
                    </label>
                    <input v-model="form.date_received" type="date" class="form-control">
                    <small class="form-text text-danger" v-show="errors.date_received">{{errors.date_received}}</small>
                  </div>
                  <div class="form-group col-md-3">
                    <label>IAR No. <i style="font-size: 8px">(When delivery is Complete)</i></label>
                    <input v-model="form.iar_no" type="text" class="form-control">
                    <small class="form-text text-danger" v-show="errors.iar_no">{{errors.iar_no}}</small>
                  </div>
                  <div class="form-group col-md-3">
                    <label>*Purpose</label>
                    <input v-model="form.purpose" type="text" class="form-control">
                    <small class="form-text text-danger" v-show="errors.purpose">{{errors.purpose}}</small>
                  </div>
                </div>
                <AprItemListForDelivery />
                <div v-for="(error) in form_errors" class="alert alert-primary float-left" role="alert">
                  {{error}}
                </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button :disabled="whileSaving" @click="submitForm" type="button" class="btn btn-primary">Deliver APR</button>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<style scoped>
    .modal-lg-apr-delivery{
        max-width: 70%;
    }
    small {
        font-size: 12px;
    }
</style>
<script>
    import {mapState, mapActions, mapMutations} from 'vuex'
    import accounting from 'accounting'
    import moment from 'moment'
    import AprItemListForDelivery from './AprItemListForDelivery'
    import alertify from 'alertify.js'
    export default {
        mounted() {

        },
        data(){
            return {
                form_errors: [],
                form: {
                    charge_invoice_no: _.random(10000),
                    date_of_invoice: '2019-01-02',
                    date_of_delivery: '2019-01-03',
                    date_received: '2019-01-04',
                    iar_no: _.random(10000),
                    purpose: 'Purpose is ' + _.random(10000)
                },
                errors: {
                    charge_invoice_no: '',
                    date_of_invoice: '',
                    date_of_delivery: '',
                    date_received: '',
                    iar_no: '',
                    purpose: ''
                },
                whileSaving: false
            }
        },
        methods: {
            ...mapActions([
                'db_save_df_apr',
                'db_insert_apr_deliver_items'     ,         
            ]),
            ...mapMutations([
                'update_one_apr_forms'
            ]),
            validateItems(){
                let self = this, errors = [], item = {}, qty = 0;
                // alert('validating form');
                for (var i = self.local_po_items.length - 1; i >= 0; i--) {
                    item = self.local_po_items[i];
                    qty = Number(item.received);
                    if (Number(item.received) > Number(item.qty)) {
                        errors.push('Invalid quantity for item: ' + item.name + ', qty received, greater than actual qty');
                    }
                    if (isNaN(qty)) {
                        errors.unshift('Unable to proceed without input for item: ' + item.name + ' and received qty is: 0');
                    }
                }
                return errors;
            },
            notInvalidDate(d){
                let self = this;
                let format = moment(d).format('MMMM DD, YYYY');
                return format;
            },
            clearForm(){
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
            submitForm(){
                let self = this;
                // console.log(self.form);
                self.whileSaving = true;
                self.clearErrors();
                let arr_error = self.validateItems();
                // console.log(self.validateItems());
                if (arr_error.length) {
                    // console.log(arr_error);
                    self.form_errors = [];
                    for (var i = arr_error.length - 1; i >= 0; i--) {
                        self.form_errors.unshift(arr_error[i]);
                    }
                    // for (var i = err_error.length - 1; i >= 0; i--) {
                    //     alertify.log(err_error[i])
                    // }

                    setTimeout(function(){
                        self.whileSaving = false;
                    }, 3000);
                }else {

                    self.db_save_df_apr(self.form).then((response) => {
                        // alert('done')
                        console.log(response)
                        if (response.status == 200) {
                            // alert('response 200')
                            let json = response.data;
                            if (json.id) {
                                console.log('deliver form json >>>>>>>>>>>>>');
                                console.log(json)
                                self.db_insert_apr_deliver_items(json).then((response) => {                               
                                  $('#CreateDeliveryForApr').modal('hide');
                                  alertify.success('Information saved!');
                                  self.clearForm();
                                  let json = response.data;
                                  self.update_one_apr_forms(json);
                                });
                            }
                        }else if(response.status == 422){
                            let data = response.data;
                            $.each(data.errors, function(index, val) {
                                 self.errors[index] = val[0];
                            });
                            alertify.log('Please complete required fields with (*)');
                            // alert('Please complete required fields (*)');
                        }
                        setTimeout(function(){
                            self.whileSaving = false;
                        }, 2000);
                    })
                }
                
            }
        },
        computed: {
            ...mapState([
                'current_apr_form',
                'local_po_items'
            ])
        },
        components: {
            AprItemListForDelivery
        },
        filters: {
            format_money(n){
                return accounting.formatMoney(n, "", 2);
            },
            format_date(d){
                let date = moment(d).format('MMMM DD, YYYY');
                if (date != 'Invalid date') {
                    return date;
                }else { return ''; };
            }
        }
    }
</script>
