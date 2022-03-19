<template>
    <div>
       <form @submit.prevent="saveItem" style="margin-top: 20px" class="row">
          <div class="col-md-9">
             <h5><span class="badge badge-secondary">Stock No Details.</span></h5>
          </div>
          <div class="col-md-4">
            <label>RCA CODE</label>
            <input v-model="form.rca_code" type="number" class="form-control first-input">
          </div>
          <div class="col-md-3">
            <label>CATEGORY</label>
            <input v-model="form.rca_cat" type="number" class="form-control first-input">
          </div>
          <div class="col-md-3">
            <label>NO.</label>
            <input v-model="form.rca_no" type="number" class="form-control first-input">
          </div>
          <div class="col-md-9">
             <br>
          </div>
		  <div class="col-md-4">
		    <label>Item name</label>
		    <input v-model="form.name" type="text" class="form-control first-input" placeholder="Enter item unique name">
		    <small v-show="errors.name" class="form-text text-danger" style="font-weight: bolder">{{ errors.name }}</small>
		  </div>
		  <div class="col-md-4">
		    <label>Item Category</label>
		    <select v-model="form.cat_id"  class="form-control">
		    	<option :value="cat.id" v-for="cat in categories">
		    		{{cat.cat_name}}
		    	</option>
		    </select>
		    <small v-show="errors.cat_id" class="form-text text-danger" style="font-weight: bolder">Category field is required</small>
		  </div>
		  <div class="col-md-4">
		    <label>Select Unit</label>
		    <select v-model="form.unit_id"  class="form-control">
		    	<option value="" selected disabled>Select unit</option>
		    	<option :value="unit.id" v-for="unit in unit_views">
		    		{{unit.unit_name}}
		    	</option>
		    </select>
		    <small v-show="errors.unit_id" class="form-text text-danger" style="font-weight: bolder">Unit field is required</small>
		  </div>
		  <div class="col-md-4">
		    <label>Reorder Point</label>
		    <input v-model="form.reorder_point" type="number" class="form-control" value="0">
		    <small v-show="errors.reorder_point" class="form-text text-danger" style="font-weight: bolder">{{ errors.reorder_point }}</small>
		  </div>
		  <div class="col-md-4">
		    <label>Running balance</label>
		    <input type="number" class="form-control" v-model="form.running_balance">
		  </div>
          <div class="col-md-4">
            <label>Remarks for (Running Balance)</label>
            <input type="text" class="form-control" v-model="form.running_bal_remarks">
          </div>
          <div class="col-md-6">
            <label>Description</label>
            <textarea v-model="form.description" class="form-control" placeholder="Item description"></textarea>
            <small v-show="errors.description" class="form-text text-danger" style="font-weight: bolder">{{ errors.description }}</small>
          </div><br>
          <div class="col-md-9" style="margin-top: 30px">
              <button  :disabled="whileSaving" type="submit" class="btn btn-primary">
                    <span v-if="whileSaving">
                        Saving..
                    </span>
                    <span v-else>
                        <span v-if="updating_item">
                            UPDATE ITEM
                        </span>
                        <span v-else>
                            SAVE ITEM
                        </span>
                    </span>
              </button>
          </div>
		</form>		
    </div>
</template>
<style scoped>
	small {
		font-size: 18px;
	}
</style>
<script>
	import {mapActions, mapState, mapMutations } from 'vuex'
	import alertify from 'alertify.js'
    export default {
        mounted() {
        	let self = this;
        },
        data(){
        	return {
        		whileSaving: false,
        		form: {
                    rca_code: '10401010',
                    rca_cat: '',
                    rca_no: '',
        			name: '',
        			description: '',
        			cat_id: 2,
        			unit_id: '',
        			reorder_point: '',
                    running_balance: 0,
                    running_bal_remarks: ''
        		},
        		errors: {
        			name: '',
        			description: '',
        			cat_id: 0,
        			unit_id: '',
        			reorder_point: ''
        		}
        	}
        },
        methods: {
        	...mapActions([
        		'save_item',
                'db_update_item'
        	]),
            ...mapMutations([
                'update_one_item'
            ]),
        	clearErrors(){
        		let self = this;
        		$.each(self.errors, function(index, val) {
        			 self.errors[index] = '';
        		});
        	},
        	clearForms(){
        		let self = this;
        		$.each(self.form, function(index, val) {
        			self.form[index] = '';
        		});
                self.form.rca_code = '10401010';
        		setTimeout(function(){
        			$('.first-input').focus();
        		}, 3500);
        	},
        	saveItem(){
        		let self = this;
        		self.whileSaving = true;
        		self.clearErrors();
                if (self.updating_item) {
                    self.save_existing_item();
                }else {
                    self.save_item(self.form).then((response) => {
                        if (response.status === 422) {
                            alertify.log('Incomplete data entry');
                            let errors = response.data.errors
                            $.each(errors, function(index, val) {
                                 /* iterate through array or object */
                                 self.errors[index] = val[0];
                            });
                        }else if(response.status === 200){
                            self.clearErrors();
                            self.clearForms();
                        }
                        setTimeout(function(){
                            self.whileSaving = false;
                        }, 2000);
                    })
                }
        	},
            save_existing_item(){
                let self = this;
                self.db_update_item(self.form).then((response) => {
                    if (response.status === 422) {
                        console.log(response.data)
                        alertify.log('Incomplete data entry');
                        let errors = response.data.errors
                        $.each(errors, function(index, val) {
                             self.errors[index] = val[0];
                        });
                    }else if(response.status === 200){
                        let json = response.data;
                        if (json.updated) {
                            alertify.success('Item successfully updated!');
                            $('#home-tab').trigger('click');
                            self.update_one_item(json.item);
                        }
                        // self.clearErrors();
                        // self.clearForms();
                    }
                    setTimeout(function(){
                        self.whileSaving = false;
                    }, 2000);
                })
            }
        },
        computed: {
        	...mapState([
        		'unit_views',
        		'categories',
                'update_item',
                'updating_item'
        	])
        },
        watch: {
            'updating_item': function(newVal, oldVal){
                let self = this;
                let item = self.update_item;
                self.form.running_balance = '';
                self.form.rca_code = item.rca_code;
                self.form.rca_cat = item.rca_cat;
                self.form.rca_no = item.rca_no;
                self.form.name = item.name;
                self.form.cat_id = item.cat_id;
                self.form.unit_id = item.unit_id;
                self.form.reorder_point = item.reorder_point;
                self.form.description = item.description;
                self.form.running_balance = item.running_balance;
                self.form.running_bal_remarks = item.running_bal_remarks;
                self.form.id = item.id;
                if (!newVal) {
                    $.each(self.form, function(index, val) {
                         /* iterate through array or object */
                         if (index != 'rca_code') {
                            self.form[index] = '';
                         }
                    });
                }
            }
        }
    }
</script>
