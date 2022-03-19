<template>
    <div style="margin: 20px;" class="col-md-8 border " id="requisition-form">
        <br>
        
        <form  class="row border-primary">
          <h6 class="col-md-12" style="margin-top: 10px; font-size: 9px">Essential information is marked with an asterisk(*) (Required)</h6><br>
          <div class="form-group col-md-2">
            <label>RIS Year</label> <i class="fa fa-asterisk text-danger" aria-hidden="true"></i>
            <input v-on:click="onClickRisYear" v-model="form.ris_year" type="number" class="form-control form-control-sm">
            <small v-show="errors.ris_year" class="form-text text-danger">
                {{errors.ris_year}}
            </small>
            <small v-show="errors.duplicate_ris" class="form-text text-danger">
                 R.I.S number already exist: ({{form.ris_year}}-{{form.ris_month}}-{{form.ris_number}})
            </small>
          </div>
          <div class="form-group col-md-2">
            <label>RIS Month</label> <i class="fa fa-asterisk text-danger" aria-hidden="true"></i>
            <input  v-on:click="onClickRisMonth" v-model="form.ris_month" type="number" class="form-control form-control-sm">
            <small v-show="errors.ris_month" class="form-text text-danger">
                {{errors.ris_month}}
            </small>
          </div>
          <div class="form-group col-md-2">
            <label>RIS number</label> <i class="fa fa-asterisk text-danger" aria-hidden="true"></i>
            <input v-model="form.ris_number" type="number" class="form-control form-control-sm">
            <small v-show="errors.ris_number" class="form-text text-danger">
                {{errors.ris_number}}
            </small>
          </div>
          <div class="form-group col-md-3">
            <label>Date Requested</label> <i class="fa fa-asterisk text-danger" aria-hidden="true"></i>
            <input @change="changeinDateRequested" v-model="form.date_requested" type="date" class="form-control form-control-sm">
            <small v-show="errors.date_requested" class="form-text text-danger">
                {{errors.date_requested}}
            </small>
          </div>
          <div class="col-md-3">
              
          </div>
          <div class="form-group col-md-3">
            <label>Division</label> <i class="fa fa-asterisk text-danger" aria-hidden="true"></i>
            <input id="division-tags" v-model="form.division" type="text" class="form-control form-control-sm">
            <small v-show="errors.division" class="form-text text-danger">
                {{errors.division}}
            </small>
          </div>
          <div class="form-group col-md-3">
            <label>Office</label>
            <input id="office-tags" v-model="form.office" type="text" class="form-control form-control-sm">
          </div>
          <div class="form-group col-md-3">
            <label>Responsibility Center Code</label>
            <input v-model="form.resp_center_code" type="text" class="form-control form-control-sm">
          </div>
          <div class="form-group col-md-3">
            <label>Fund Cluster</label>
            <input v-model="form.fund_cluster" type="text" class="form-control form-control-sm" >
          </div>
           <div class="form-group col-md-3">
            <label>Requested by </label> <i class="fa fa-asterisk text-danger" aria-hidden="true"></i>
            <input id="requested_by-tags" v-model="form.requested_by" type="text" class="form-control form-control-sm">
            <small v-show="errors.requested_by" class="form-text text-danger">
                {{errors.requested_by}}
            </small>
          </div>
          <div class="form-group col-md-3">
            <label>Approved by</label>
            <input id="approved_by-tags" v-model="form.approved_by" type="text" class="form-control form-control-sm">
          </div>
          <div class="form-group col-md-3">
            <label>Issued by</label>
            <input id="issued_by-tags" v-model="form.issued_by" type="text" class="form-control form-control-sm">
          </div>
           <div class="form-group col-md-3">
            <label>Received by</label>
            <input id="received_by-tags" v-model="form.received_by" type="text" class="form-control form-control-sm">
          </div>
           <div v-if="ris_updating == false" class="form-group col-md-3">
            <button @click.prevent="submitFormNow" :disabled="whileSaving || errors.duplicate_ris" type="submit" class="btn btn-primary btn-sm">
                <span v-if="whileSaving">
                    Saving ...
                </span>
                <span v-else>
                    SUBMIT REQUEST
                </span>
            </button>
          </div>
          <div v-else>
              <button @click.prevent="updateRequisition" :disabled="whileSaving" type="submit" class="btn btn-primary btn-sm">
                <span v-if="whileSaving">
                    Updating ...
                </span>
                <span v-else>
                    UPDATE RIS
                </span>
            </button>
          </div>

        </form>
        <button style="margin-top: -50px" @click="showItemSelection" class="float-right btn btn-link"><i class="fa fa-plus"></i> Add Items</button>
        <div class="item-errors col-md-7" style="margin-left: -20px">
            <div :key="e" v-for="e in item_errors">
                <div class="alert alert-warning" role="alert">
                  {{ e }}
                </div>
            </div>
        </div>
        
    </div>
</template>

<script>
    import alertify from 'alertify.js'
    import {mapActions, mapState, mapMutations } from 'vuex'
    import moment from 'moment'
    import 'jquery-ui/ui/widgets/autocomplete';
    import 'jquery-ui/themes/base/autocomplete.css';
    import 'jquery-ui/themes/base/all.css';
    import 'jquery-ui/themes/base/base.css';
    import { toast_mixin } from "../../../mixin/toast_mixin.js";
    export default {
        mixins: [toast_mixin],
        mounted() {
            const self = this;
            setTimeout(() => {                
                self.fetchPropAutoComplete(['division','office','requested_by']);
            }, 3000);

            setTimeout(() => {                
                self.fetchPropAutoComplete(['approved_by','issued_by', 'received_by']);
            }, 6000);
            self.dbGetCurrentUser();
        },
        data(){
            return {
                whileSaving: false,
                item_errors: [],
                form: {
                    id: 0,
                    fund_cluster: '01',
                    ris_year: '',
                    ris_month: '',
                    ris_number: '0',
                    division: '',
                    office: '',
                    resp_center_code: '',
                    requested_by: '',
                    approved_by: '',
                    issued_by: '',
                    received_by: '',
                    date_requested: ''
                },
                errors: {
                    division: '',
                    ris_year: '',
                    ris_month: '',
                    ris_number: '',
                    ris_year: '',
                    requested_by: '',
                    duplicate_ris: false,
                    date_requested: ''
                },
            }
        },
        computed: {
            ...mapState([
               'local_items',
               'max_ris_number',
               'ris_updating',
               'current_request_form',
               'current_r_items',
               'actual_stocks'
            ]),
            ...mapState({
                autocomplete_division: state => state.requisition_module.autocomplete_division,
                user: state=> state.user_module.user
            })
        },
        methods: {
            ...mapActions([
                'save_requisition_form',
                'db_save_request_items',
                'fetch_requisitions',
                'db_validate_ris',
                'update_requisition_form',
            ]),
            ...mapActions({
                'dbSearchAutoCompleteForKey': 'requisition_module/dbSearchAutoCompleteForKey',
                'dbGetCurrentUser': 'user_module/dbGetCurrentUser'
            }),
            ...mapMutations([
                'delete_local_items',
                'clear_local_items',
                'set_ris_max_number',
                'set_ris_updating'
            ]),
            ...mapMutations({
                'SET_REQUISITION_AUTOCOMPLETE_DIVISION': 'requisition_module/SET_REQUISITION_AUTOCOMPLETE_DIVISION',
                'SET_AUTOCOMPLETE_PROP': 'requisition_module/SET_AUTOCOMPLETE_PROP'
           }),
            changeinDateRequested(){
                const self = this;
                let d_today = moment().format('MMMM DD, YYYY');
                let d_requested = moment(self.form.date_requested).format('MMMM DD, YYYY');
                let isAfter = moment(d_requested).isAfter(d_today, 'day');
                if(isAfter){
                    self.makeToast({
                        append: false,
                        variant: 'danger',
                        title: 'This is the Title',
                        content: 'Invalid input date, Please verify/Re-check date entered!',
                        toaster: 'b-toaster-top-center'
                    });
                    self.form.date_requested = '';
                }
               
            },
            fetchPropAutoComplete(prop){
                const self = this;
                self.dbSearchAutoCompleteForKey({
                    prop: prop,
                    val: ''
                }).then((response) => {
                    let json = response.data;
                    if(Array.isArray(prop)){
                        json.autocomplete_props.forEach(element => {
                            // {office: Array(15), prop: "office"}
                            let keys = Object.keys(element);

                            let values = element[element.prop];
                            let mapValues = _.map(values, element.prop);
                            self.SET_AUTOCOMPLETE_PROP({
                                prop: element.prop,
                                mapValues: mapValues
                            });
                            
                            self.applyAutoComplete({
                                id: '#' + element.prop + '-tags',
                                tags: mapValues,
                                prop: element.prop
                            });
                        });

                    }
                    
                   
                    // self.applyAutoComplete({
                    //     id: '#' + prop + '-tags',
                    //     tags: mapValues,
                    //     prop: prop
                    // });
                });

            },
            applyAutoComplete(payload){
                const self = this;
                $(payload.id).autocomplete({
                    source: payload.tags,
                    minLength: 0,
                    select: function(event, ui){
                        let value = ui.item.value;
                        self.form[payload.prop] = value;
                    }
                }).click(function() {
                    $(this).autocomplete('search', $(this).val())
                });
            },
            onClickRisMonth(){
                let self = this;
                let month = moment().format('MM')
                self.form.ris_month = month;
            },  
            onClickRisYear(){
                let self = this;
                // var dt = new Date();
                var year2 = moment().year();
                self.form.ris_year = year2;
            },
            onClickRisYear(){
                let self = this;
                // var dt = new Date();
                var year2 = moment().year();
                self.form.ris_year = year2;
            },
            getActualBalanceOfItem(item_id){
                let self = this;
                let rsStocks = _.filter(self.actual_stocks, {item_id: item_id});
                if (rsStocks.length) {
                    let first = _.first(rsStocks);
                    return first.actual_stock;
                }else{
                    // alertify.log('0 item was found for getActualBalanceOfItem: RequisitionItems.vue');
                    return 0;
                }
            },
            append_r_items(){
                let self = this;
                console.log('adding items')
            },
            updateRequisition(){
                let self = this;
                self.whileSaving = true;
                self.form.id = self.current_request_form.id;
                self.clearErrors();
                self.update_requisition_form(self.form).then((response) => {
                    if(response.status === 422){
                        alertify.log('Please complete Essential Informations / Inputs');
                        let errors = response.data.errors;
                        self.populateErrors(errors);
                        setTimeout(function(){
                            self.whileSaving = false;
                        }, 2000);
                    }else if(response.status === 200){
                        self.whileSaving = false;
                        let data = response.data;
                        alertify.alert('Information Saved!');
                        self.set_ris_updating(false);
                        self.clear_local_items();
                        self.set_ris_max_number({
                            max_ris_number: ''
                        });
                        setTimeout(function(){
                            $('a#home-tab').trigger('click');
                        }, 1000);
                    };
                })
            },
            update_form(){
                let self = this;
                self.form.ris_year = self.current_request_form.ris_year;
                self.form.ris_month = self.current_request_form.ris_month;
                self.form.ris_number = self.current_request_form.ris_number;
                self.form.division = self.current_request_form.division;
                self.form.office = self.current_request_form.office;
                self.form.ris_year = self.current_request_form.ris_year;
                self.form.resp_center_code = self.current_request_form.resp_center_code;
                self.form.fund_cluster = self.current_request_form.fund_cluster;
                self.form.requested_by = self.current_request_form.requested_by;
                self.form.approved_by = self.current_request_form.approved_by;
                self.form.issued_by = self.current_request_form.issued_by;
                self.form.received_by = self.current_request_form.received_by;
                self.form.date_requested = self.current_request_form.date_requested;
            },
            showItemSelection(){
                let self = this;
                $('#modalItemSelection').modal('show');
            },
            submitFormNow(){
                let self = this;

                if(!self.local_items.length){
                    alertify.alert('No item was selected');
                }else {
                    self.whileSaving = true;
                    let errors = self.validateRequestQty();
                    if (errors.length) {
                        errors.forEach(function(error){
                            self.item_errors.unshift(error);
                        });
                        setTimeout(function(){
                            self.whileSaving = false;
                        }, 2000);
                    }else {
                        let conf = confirm('Are you sure you want to proceed?');
                        if(conf){
                            self.clearErrors();
                            self.save_requisition_form(self.form).then((response) => {
                                if(response.status === 422){
                                    alertify.log('Please complete Essential Informations / Inputs');
                                    let errors = response.data.errors;
                                    self.populateErrors(errors);
                                }else if(response.status === 200){
                                    let data = response.data;
                                    self.save_request_items(data);
                                };
                            })
                            
                        }
                    }
                    setTimeout(function(){
                        self.whileSaving = false;
                    }, 5000);
                }
            },
            save_request_items(data){
                let self = this;
                self.db_save_request_items(data).then( (response) => {
                    if (response.status == 200) {
                        let json = response.data;
                        if (json.request_items.length == self.local_items.length) {
                            alertify.alert('Information saved!');
                            self.clearAll();
                            self.whileSaving = false;
                            self.fetch_requisitions();
                        };
                    };
                })
            },
            clearAll(){
                let self = this;
                self.delete_local_items();
                // fund_cluster: '01',
                //     ris_year: '',
                //     ris_month: '',
                //     ris_number: '',
                //     division: '',
                //     office: '',
                //     resp_center_code: '',
                //     requested_by: '',
                //     approved_by: '',
                //     issued_by: '',
                //     received_by: ''
                $.each(self.form, function(index, val) {
                    self.form[index] = '';
                });
            },
            validateRequestQty(){
                let self = this;
                self.item_errors = [];
                let errors = [], item = {};
                let qty = 0;
                for (var i = self.local_items.length - 1; i >= 0; i--) {
                    item = self.local_items[i];
                    qty = Number(item.requested_qty);
                    if (Number(qty) > Number(self.getActualBalanceOfItem(item.id))) {
                        errors.push('Insufficient stock available for item: ' + item.name + ', Requested qty: ' + item.requested_qty + ', Stock: ' + item.running_balance);
                    }
                    if (qty <= 0) {
                        errors.push('Invalid qty: ' + qty + ' for: '+ self.getActualBalanceOfItem(item.id));
                    };
                    if (_.has(item, 'remarks') == false) {
                        item.remarks = "";
                    }
                };
                return errors;
            },
            clearErrors(){
                let self = this;
                self.errors.division = '';
                self.errors.ris_year = '';
                self.errors.ris_month = '';
                self.errors.ris_number = '';
                self.errors.requested_by = '';
            },
            populateErrors(errors){
                let self = this;
                $.each(errors, function(index, val) {
                     /* iterate through array or object */
                     self.errors[index] = val[0];
                });
            },
            changedRis(){
                let self = this;
                if (!self.ris_updating) {
                    if (self.form.ris_year && self.form.ris_month && self.form.ris_number) {
                        clearTimeout(self.timer);
                        self.timer = setTimeout(function(){
                            self.db_validate_ris(self.form).then((response) => {
                                if (response.status == 200) {
                                    let json = response.data;
                                    if (json.count == 1) {
                                        if($('#requisition-form').is(':visible')) {
                                           alertify.alert('RIS number already exist');
                                        }
                                        self.errors.duplicate_ris = true;
                                    }else {
                                        self.errors.duplicate_ris = false;
                                    }
                                }
                            })
                        }, 1000);
                    }
                }
            }
        },
        watch: {
            // 'form.date_requested': function(newVal, oldVal) {
           
            // },
            'user': function(newVal, oldVal){
                let self = this;
                if(_.has(newVal, 'name')){
                    let name = newVal.name;
                    self.form.issued_by = name;
                }
            },
            'form.division': function(newVal, oldVal){
               
            },
            'form.ris_year': function(newVal, oldVal){
                this.changedRis();
            },
            'form.ris_number': function(newVal, oldVal){
                this.changedRis();
            },
            'form.ris_month': function(newVal, oldVal){
                this.changedRis();
            },
            'max_ris_number': function(newVal, oldVal){
                let self = this;
                self.form.ris_number = Number(newVal) + 1;
            },
            'ris_updating': function(newVal, oldVal){
                let self = this;
                if (newVal) {
                    // if ris_updating is TRUE
                    self.update_form();
                }else {
                    $.each(self.form, function(index, val) {
                         /* iterate through array or object */
                         
                         if (index != 'ris_number') {
                            
                         }
                         self.form[index] = '';
                    });
                }
            },
            
            
        }
    }
</script>
