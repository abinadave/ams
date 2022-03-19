<template>
    <div>
       <div class="modal fade" id="ModalPurchaseOrderDetails" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">PURCHASE ORDER DETAILS</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
        <form class="row">

          <div class="form-group col-md-4">
            <label>Supplier name</label> <b class="text-danger">*</b>   
            <select v-model="form.supplier_id" class="form-control">
                <option value="" selected disabled>Choose Supplier</option>
                <option :value="sup.id" v-for="sup in suppliers">
                    {{ sup.name }}
                </option>
            </select>
          </div>
          <div class="form-group col-md-4">
            <label>Supplier Address</label>
            <input v-model="supplier_address" type="text" class="form-control">
          </div>

          <br>
          <div class="col-md-4">
              <!-- Po Detials -->
              <!-- <hr> -->
          </div>
          <div v-show="form.supplier_id != 1" class="col-md-12">
              <h5> <span class="badge badge-secondary">Purchase Order Details</span></h5>
              <hr>
          </div>
          <div v-show="form.supplier_id != 1" class="form-group col-md-4">
            <label>P.O (Year)</label> <b class="text-danger">*</b>
            <input v-model="form.po_year" type="text" class="form-control" placeholder="Enter year: YYYY">
          </div>
          <div v-show="form.supplier_id != 1" class="form-group col-md-4">
            <label>P.O (Month)</label> <b class="text-danger">*</b>
            <select v-model="form.po_month" class="form-control">
                <option v-bind:value="n" v-for="n in 12">
                    {{ n }}
                </option>
            </select>
          </div>
          <div v-show="form.supplier_id != 1"  class="form-group col-md-4">
            <label>P.O Number</label> <b class="text-danger">*</b>
            <input v-model="form.po_number" type="number" class="form-control">
          </div>
          <div v-show="form.supplier_id != 1" class="form-group col-md-4">
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
          <!-- <div class="form-group col-md-4">
            <label>Mode of Procurement</label>
            <input type="text" class="form-control" value="procurement">
          </div> -->
          <div class="form-group col-md-4">
            <label>P.O Category</label> <b class="text-danger">*</b>
            <select v-model="form.po_category" class="form-control">
                <option value="" selected disabled>Choose Category</option>
                <option :value="po_cat.id" v-for="po_cat in po_categories">
                    {{ po_cat.name }}
                </option>
            </select>
          </div>
          <div v-show="form.supplier_id == 1"  class="form-group col-md-4">
            <label>APR No.</label>
            <input v-model="form.apr_no" type="text" class="form-control">
          </div>
          <div v-show="form.supplier_id == 1" class="form-group col-md-4">
            <label>APR Scanned file (Attachment)</label>
            <input class="form-control" type="file">
          </div>
          <div class="form-group col-md-4">
            <label>Mode of Procurement</label>
            <input v-model="mode_of_procurement" type="text" class="form-control" disabled>
           </div>
        </form>

        <PurchaseItemViews />

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
              </div>
            </div>
          </div>
        </div>
    </div>
</template>
<style scoped>
  .modal-lg {
      max-width: 60% !important;
  }
</style>
<script>
    import {mapState, mapActions} from 'vuex'
    import PurchaseItemViews from './PurchaseItemViews'
    export default {
        mounted() {
            let self = this;
            setTimeout(function(){
                // self.fetch_POCategories();
            }, 2000);
        },
        components: {
            PurchaseItemViews
        },
        data(){
            return {
                supplier_name: '',
                whileSaving: false,
                whileUpdating: false,
                errors: {
                   supplier_id: 0,
                   po_number: '',
                   date: '',
                   po_category: 0,
                   apr_no: ''
                },
                errors_all: [],
                local_item_errors: []
            }
        },
        methods: {
            ...mapActions([
                'fetch_POCategories '
            ]),
             CheckModeOfProcurement(n){
                let self = this;
                if (Number(n) > 50000) {
                    return 'SMALL-VALUE';
                }else {
                    return 'SHOPPING';
                }
            }
        },
        computed: {
            ...mapState([
                'suppliers',
                'po_categories',
                'local_items',
                'supplier_address'
            ]),
            ...mapState({
                'form': 'current_po'
            }),
            getCompletePoNumber(){
                let self = this;
                if (self.form.po_year && self.form.po_month && self.form.po_number) {
                    return self.form.po_year + '-' + self.form.po_month + '-' + self.form.po_number
                }else {
                    return 'Invalid P.O Number';    
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
        }
    }
</script>
