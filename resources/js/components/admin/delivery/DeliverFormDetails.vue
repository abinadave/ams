<template>
    <div>
        <div class="row input-dform-details">
           <!-- Delivery form -->
           <div v-show="current_deliver_form.type == 'po'" class="form-group col-md-4">
            <label>Choose Supplier Name</label>
            <!-- <input v-model="supplier_name" type="text" class="form-control form-control-sm" disabled> -->
            <select  v-model="form.supplier_id" class="form-control form-control-sm">
                <option value="">Choose One Supplier</option>
                <option :value="supplier.id" v-for="supplier in suppliers">
                    {{ supplier.name }}
                </option>
            </select>
          </div>
       
          <div class="form-group col-md-4">
            <label>Charge Invoice Number</label>
            <input v-model="form.charge_invoice_no" type="number" class="form-control form-control-sm" placeholder="Charge invoice number">
          </div>
           <div class="form-group col-md-4">
            <label>Date of Invoice</label>
            <input v-model="form.date_of_invoice" type="date" class="form-control form-control-sm" placeholder="Date of Invoice">

          </div>
          <div v-show="current_deliver_form.type == 'apr'" class="form-group col-md-9">
             <span class="badge badge-secondary">APR DETAILS</span></h5>
          </div>
          <div v-show="current_deliver_form.type == 'apr'" class="form-group col-md-4">
            <label>APR No</label>
            <input v-model="form.apr_no" type="number" class="form-control form-control-sm" >
          </div>
          <div v-show="current_deliver_form.type == 'apr'" class="form-group col-md-4">
            <label>Date of APR</label>
            <input v-model="form.date_of_apr" type="date" class="form-control form-control-sm">
          </div>


          <div class="col-md-4">
              <!-- <hr> -->
          </div>
          <div v-show="current_deliver_form.type == 'po'" class="col-md-12">
              <h5>
                <span class="badge badge-secondary">PURCHASE ORDER DETAILS</span></h5>
              <hr>
          </div>
          <div v-show="current_deliver_form.type == 'po'" class="form-group col-md-4">
            <label>P.O (Year)</label> <b class="text-danger">*</b>
            <input v-model="form.po_year" type="text" class="form-control form-control-sm" >
          </div>
          <div v-show="current_deliver_form.type == 'po'" class="form-group col-md-4">
            <label>P.O (Month)</label> <b class="text-danger">*</b>
            <select v-model="form.po_month" class="form-control form-control-sm">
                <option v-bind:value="n" v-for="n in 12">
                    {{ n }}
                </option>
            </select>
          </div>
          <div v-show="current_deliver_form.type == 'po'" class="form-group col-md-4">
            <label>P.O Number</label> <b class="text-danger">*</b>
            <input v-model="form.po_number" type="text" class="form-control form-control-sm">
          </div>

          <div class="form-group col-md-4">
            <label>Date of Delivery</label>
            <input v-model="form.date_of_delivery" type="date" class="form-control form-control-sm">
          </div>
           <div class="form-group col-md-4">
            <label>Date Received</label>
            <input v-model="form.date_received" type="date" class="form-control form-control-sm">
          </div>
          <div class="form-group col-md-4">
            <label>IAR No</label>
            <input v-model="form.iar_no" type="number" class="form-control form-control-sm">
          </div>
          <div class="form-group col-md-4">
            <label>Purpose</label>
            <input v-model="form.purpose" type="text" class="form-control form-control-sm" placeholder="Purpose">
           
          </div>
          <div class="form-group col-md-4">
            <label>Date & Time of Transaction (Timestamp)</label>
            <input v-model="get_timestamp" type="text" class="form-control form-control-sm" >
           
          </div>
           <div class="form-group col-md-4">
            <label>Personnel</label>
            <input v-model="form.transact" type="text" class="form-control form-control-sm" >
           
          </div>
        </div>
    </div>
</template>

<script>
    import {mapState, mapActions} from 'vuex'
    import moment from 'moment'
    export default {
        mounted() {
            $(".input-dform-details :input").attr("disabled", "disabled");
        },
        data(){
            return {
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
            }
        },
        methods: {
            ...mapActions([
              'fetch_suppliers',
            ])
        },
        computed: {
            ...mapState([
              'suppliers',
              'current_deliver_form'
            ]),
            get_timestamp(){
                let self = this;
                return moment(self.form.created_at).format('MMM DD, YYYY hh:mm a')
            }
        },
        watch: {
            'current_deliver_form': function(newVal, oldVal){
                let self = this;
                $.each(newVal, function(index, val) {
                   /* iterate through array or object */
                   // console.log(index + ': ' + val)
                   self.form[index] = val;
                });
            }
        }
    }
</script>
