<template>
    <div >
        <hr>
        <span class="badge badge-primary">Total item(s) {{ local_items.length }}</span>
        <button @click="showModalSelectItem" class="btn btn-secondary btn-sm float-right"><i class="fa fa-plus" aria-hidden="true"></i> Add item </button><br>
            <table class="table table-striped tbl-po-item">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">Item</th>
                  <th scope="col">Unit</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Unit Cost</th>
                  <th  width="100" scope="col">Amount</th>
                  <th width="20"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in local_items">
                  <th scope="row">{{ item.name }}</th>
                  <td>{{item.unit}}</td>
                  <td>
                    <input v-model="item.qty" @keyup="changeQty($event, item)" class="qty-inputs form-control form-control-sm inputs" type="number">
                  </td>
                  <td>
                    <input v-model="item.unit_cost" @keyup="changeUnitCost($event, item)" class="form-control form-control-sm inputs" type="number">
                  </td>
                  <td>
                      <b class="text-danger">{{ getAmount(item) }}</b>
                  </td>
                  <td>
                    <a @click="removeLocalItem(item)" style="cursor: pointer"><i class="fa fa-remove"></i></a>
                  </td>
                </tr>
                <tr>
                    <td colspan="4" class="text-center">P.O TOTAL AMOUNT</td>
                    <td class="text-primary">
                        <b>{{localItemsTotalAmount}}</b>
                    </td>
                </tr>
              </tbody>
            </table>
        <hr>
    </div>
</template>
<style scoped>
    .tbl-po-item {
        font-size: 14px;
    }
    .tbl-po-item td, th {
        padding: 3px;
    }
    .inputs { 
        width: 100px;
     }
</style>
<script>
    import {mapState, mapMutations, mapActions} from 'vuex'
    import accounting from 'accounting'
    import alertify from 'alertify.js'
    export default {
        mounted() {

        },
        computed: {
            ...mapState([
                'local_items',
                'po_updating'
            ]),
            localItemsTotalAmount(){
                let self = this, item = {}, total = 0;
                for (var i = self.local_items.length - 1; i >= 0; i--) {
                    item = self.local_items[i];
                    if (Number(item.qty) > 0 && Number(item.unit_cost) > 0) {
                      total += Number(item.qty) * Number(item.unit_cost);
                    }
                }
                return accounting.formatMoney(total, "Php ", 2);
            }
        },
        methods: {
            ...mapActions([
                'delete_purchase_item',
                'update_qty_purchase_item',
                'update_unit_cost_purchase_item'
            ]),
            ...mapMutations([
                'remove_local_item',
            ]),
            removeLocalItem(item){
                let self = this;
                if (self.po_updating) {
                    let conf = confirm('Are you sure you want to Permanently delete this item?');
                    if (conf) {
                        self.delete_purchase_item(item).then((response) => {
                            if (response.status === 200) {
                                let json = response.data;
                                if (json.deleted) {
                                    self.remove_local_item(item);
                                    alertify.alert('Item successfully deleted');
                                }
                            }
                        });
                    }
                }else {
                    self.remove_local_item(item);
                }
                
            },
            getAmount(item){
                let self = this;
                let totalAmount = item.qty * item.unit_cost;
                return accounting.formatMoney(totalAmount, " " ,2);
            },
            changeQty(e, item){
                let self = this;
                item.qty = Number(e.target.value)
                if (self.po_updating) {
                    let self = this;
                    let i = Number(e.target.value);
                    if (i<=0) {
                        alert('Invalid input, Please enter number greater than zero');
                        // ++error;
                    }else {
                        self.update_qty_purchase_item(item).then((response) => {
                            if(response.data == 200){
                                let json = response.data;
                                let total_sum = json.total_sum;
                                let purchase_item = json.purchase_item;
                                
                            }
                        });
                    }
                };
                
            },
            validateNumber(a){
                let self = this;
                let i = Number(a);

            },
            changeUnitCost(e, item){
                let self = this;
                item.unit_cost = Number(e.target.value)
                // clearTimeout(self.timer);
                if (self.po_updating) {
                    let self = this;
                    let i = Number(e.target.value);
                    if (i<=0) {
                        alert('Invalid input, Please enter number greater than zero');
                        // ++error;
                    }else {
                        clearTimeout(self.timer1);
                        self.timer1 = setTimeout(function(){
                            self.update_unit_cost_purchase_item(item).then((response) => {
                                if(response.data == 200){
                                    let json = response.data;
                                    alert('done')
                                }
                            });
                        }, 1000);
                        
                    }
                };
                
            },
            showModalSelectItem(){
                let self = this;
                $('#modalItemSelection').modal('show');

                setTimeout(function(){
                    $('.search-box').focus();
                }, 2000);
            }
        },
        watch: {
            'local_items': function(newVal, oldVal){
                let self = this;
                console.log(newVal)
            }
        }
    }
</script>
