<template>
    <div style="padding: 20px">
        <form class="row">
         <div class="form-group col-md-3">
            <label>APR No.</label>
            <input v-model="form.apr_no" type="text" class="form-control" placeholder="APR-xxxx">
            <small v-show="errors.apr_no" class="form-text text-danger">
                {{ errors.apr_no }}
                <span v-if=""></span>
            </small>
          </div>
          <div class="form-group col-md-3">
            <label>APR Date</label>
            <input v-model="form.apr_date" type="date" class="form-control" >
            <small v-show="errors.apr_date" class="form-text text-danger">
                {{ errors.apr_date }}
            </small>
          </div>
          <div class="form-group col-md-3">
            <label>Supplier</label>
            <input value="Procurement Services" type="text" class="form-control" disabled>
          </div>
          <CreateAprItems class="col-md-9" />
          <div v-show="local_items.length" class="form-group col-md-9">
            <span v-if="updating_apr">
                <button :disabled="whileSaving" @click.prevent="updateForm" class="btn btn-success">UPDATE</button>
            </span>
            <span v-else>
                <button :disabled="whileSaving" @click.prevent="submitForm" class="btn btn-success">SUBMIT</button>
            </span>
          </div>
        </form>
    </div>
</template>

<script>
    import CreateAprItems from './CreateAprItems'
    import {mapState, mapActions, mapMutations } from 'vuex'
    import alertify from 'alertify.js'
    export default {
        mounted() {
            
        },
        data(){
            return {
                whileSaving: false,
                form: {
                    apr_no: '',
                    apr_date: ''
                },
                errors: {
                    apr_no: '',
                    apr_date: ''
                }
            }
        },
        methods: {
            ...mapActions([
                'db_dave_apr_form',
                'db_save_apr_items',
                'db_check_duplicate_apr_no',
                'db_update_apr_form'
            ]),
            ...mapMutations([
                'clear_local_items'
            ]),
            updateForm(){
                let self = this;
                self.form.id = self.current_apr_form.id;
                self.db_update_apr_form(self.form).then((response) => {
                    if (response.status == 200) {
                        let json = response.data;
                        alertify.success('Apr Form Successfully Updated!');
                        $('.panel-apr').find('#home-tab').trigger('click');
                    }
                });
            },
            submitForm(){
                let self = this;
                self.clearErrors();
                let item_errors = self.validateItems();
                self.whileSaving = true;
                if (item_errors) {
                    alertify.alert('Please enter correct item QTY and UNIT COST');
                    // alertify.log('Please enter correct item QTY and UNIT COST');
                    setTimeout(function(){
                        self.whileSaving = false;
                    }, 3000);
                }else {
                    self.db_dave_apr_form(self.form).then((response) => {
                        if (response.status == 422) {
                            alertify.alert('Please complete required fields');
                            self.add_errors(response.data);
                            self.whileSaving = false;
                        }else if(response.status == 200){
                            self.db_save_apr_items(response.data).then((response) => {
                                if (response.status == 200) {
                                    let json = response.data;
                                    self.afterSave();
                                }
                            })
                        }
                    })
                }
                
            },
            afterSave(){
                let self = this;
                alertify.alert('Information Saved!');
                alertify.success('Information Saved!');
                self.clear_local_items();
                self.clearForms();
                setTimeout(function(){
                    $('#apr-div').find('#home-tab').trigger('click');
                    self.whileSaving = false;
                }, 1000);
            },
            clearForms(){
                let self = this;
                self.form.apr_no = '';
                self.form.apr_date = '';
            },
            clearErrors(){
                let self = this;
                $.each(self.errors, function(index, val) {
                     /* iterate through array or object */
                     self.errors[index] = '';
                });
            },
            add_errors(data){
                let self = this;
                let errors = data.errors;
                $.each(errors, function(index, val) {
                     /* iterate through array or object */
                     self.errors[index] = val[0];
                });
            },
            validateItems(){
                let self = this;
                let item_error = 0;
                let item = {};
                for (var i = self.local_items.length - 1; i >= 0; i--) {
                    item = self.local_items[i];
                    if (!Number(item.qty) || !Number(item.unit_cost)) {
                        ++item_error;
                    };
                };
                if (!self.local_items.length) {
                    ++item_error;
                };
                return item_error;
            }
        },
        computed: {
            ...mapState([
                'local_items',
                'updating_apr',
                'current_apr_form'
            ])
        },
        components: {
            CreateAprItems
        },
        watch: {
            'updating_apr': function(newVal, oldVal){
                let self = this;
                if (self.updating_apr == true) {
                    self.form.apr_no = self.current_apr_form.apr_no;
                    self.form.apr_date = self.current_apr_form.apr_date;
                };
            },
            'form.apr_no': function(newVal, oldVal){
                let self = this;
                clearTimeout(self.timer);
                if (!self.updating_apr) {
                    self.timer = setTimeout(function(){
                        self.db_check_duplicate_apr_no(newVal).then((response) => {
                            if (response.status == 200) {
                                let json = response.data;
                                if (json.count) {
                                    alert('Duplicate Apr No. for: ' + newVal);
                                    self.errors.apr_no = 'Apr No. ' + newVal + ' is already taken';
                                }else {
                                    self.errors.apr_no = '';
                                }
                            };
                        });
                    }, 1500);
                }
                
            }
        }
    }
</script>
