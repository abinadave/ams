<template>
    <div>
        <div class="modal fade" id="ModalShowAprItems" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">APR Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                    <form class="row">
                      <div class="form-group col-md-4">
                        <label>APR NO.</label>
                        <input v-model="apr_form.apr_no" type="text" class="form-control">
                      </div>
                      <div class="form-group col-md-4">
                        <label>APR DATE</label>
                        <input v-model="apr_form.apr_date" type="text" class="form-control">
                      </div>
                      <div class="form-group col-md-4">
                        <label>DATE ENCODED (SYSTEM)</label>
                        <input v-model="apr_form.created_at" type="text" class="form-control">
                      </div>
                      <div class="form-group col-md-4">
                        <label>NO OF ITEMS</label>
                        <input v-model="apr_form.count_items" type="text" class="form-control">
                      </div>
                      <div class="form-group col-md-4">
                        <label>TOTAL AMOUNT</label>
                        <input v-model="apr_form.total_amount" type="text" class="form-control">
                      </div>
                    </form>
                    <hr>
                    <table class="table table-hover table-bordered table-condensed tbl-apr-items">
                        <thead>
                            <tr class="text-primary">
                                <th class="text-center" width="50">Qty</th>
                                <th>Item / details</th>
                                <td class="text-center" width="150">Item No.</td>
                                <th width="150" class="text-right">Unit Cost</th>
                                <th width="150" class="text-right">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in apr_items">
                                <td class="text-center">{{ item.qty }}</td>
                                <td>{{ item.name }} </td>
                                <td class="text-center">{{ item.rca_code }}-{{item.rca_cat}}-{{item.rca_no}}</td>
                                <td class="text-right">{{item.unit_cost}}</td>
                                <td class="text-right">{{ item.qty * item.unit_cost | format_number }}</td>
                            </tr>
                            <tr>
                                <td colspan="4">TOTAL</td>
                                <th style="font-size: 16px" class="text-right text-danger">{{apr_form.total_amount | format_money }}</th>
                            </tr>
                        </tbody>
                    </table>
                    <!-- {{apr_items}} -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
    .tbl-apr-items td, th{
        padding: 5px;
        font-size: 14px;
    }
</style>
<script>
    import accounting from 'accounting'
    import {mapState} from 'vuex'
    export default {
        mounted() {

        },
        computed: {
            ...mapState({
                'apr_items': 'local_po_items',
                'apr_form': 'current_apr_form'
            })
        },
        filters: {
            format_number(n){
                let self = this;
                return accounting.formatNumber(n);
            },
            format_money(n){
                return accounting.formatMoney(n, "Php ", 2);
            }
        },
        watch: {

        }
    }
</script>
