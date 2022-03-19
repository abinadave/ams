<template>
    <div class="col-md-9">
        <!-- Local items selected: {{local_items.length}} -->
        <br>
        <p v-show="ris_updating">
            type new QTY or REMARKS to update the item
        </p>
        <table class="tbl-req-items table table-bordered" style="font-size: 12px">
            <thead>
                <tr>
                    <th class="text-center" width="140">Stock No.</th>
                    <th class="text-center">Unit</th>
                    <th>Item name</th>
                    <th>Quantity</th>
                    <th class="text-center">Yes</th>
                    <th class="text-center">No</th>
                    <th width="100">Available Stock</th>
                    <th>Remarks</th>
                    <th width="1"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in local_items">
                    <td class="text-center">{{ item.stock_no }}</td>
                    <td class="text-center">{{ item.unit }}</td>
                    <td>{{ item.name }} - {{ item.description }}</td>
                    <td width="70">
                        <input @keyup="changeQty($event, item)" v-model="item.requested_qty" type="text" class="form-control text-center">
                    </td>
                    <td class="text-center"> 
                        <span v-show="item.running_balance > item.requested_qty">
                            <i class="fa fa-check text-success" aria-hidden="true"></i>
                        </span>
                         <span v-show="item.running_balance == item.requested_qty">
                            <i class="fa fa-check text-success" aria-hidden="true"></i>
                        </span>
                    </td>
                    <td class="text-center">
                        <span v-show="getActualBalanceOfItem(item.id) < item.requested_qty">
                            <i class="fa fa-check text-danger" aria-hidden="true"></i>
                        </span>
                    </td>
                    <td class="text-center">{{ getActualBalanceOfItem(item.id) }}</td>
                    <td width="150"><input v-model="item.remarks" type="text" class="form-control"></td>
                    <td>
                        <a class="fa fa-remove text-danger" @click="removeItem(item)" style="cursor: pointer"></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
    <style scoped>
        .tbl-req-items td, th {
            padding: 5px;
        }
        .tbl-req-items input {
            height: 25px;
        }
    </style>
<script>
    import {mapState, mapMutations, mapActions} from 'vuex'
    import alertify from 'alertify.js'
    export default {
        mounted() {

        },
        computed: {
            ...mapState([
                'local_items',
                'items',
                'current_r_items',
                'ris_updating',
                'current_request_form',
                'actual_stocks'
            ])
        },
        methods: {

            ...mapMutations([
                'remove_local_item',
                'add_local_items',
                'update_local_request_items',
            ]),
            ...mapActions([
                'db_find_stock_card_balances',
                'update_request_item',
                'db_remove_ris_item',
                'db_save_request_items',
            ]),
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
            changeQty(e, item){
                let self = this;
                let newQty = Number(e.target.value)
                clearTimeout(self.timer);
                self.timer = setTimeout(function(){
                    if (self.ris_updating) {
                        self.update_request_item({
                            item: item,
                            new_qty: newQty
                        }).then((response) => {
                            if (response.status == 200) {
                                let json = response.data;
                                if (json.updated == 1) {
                                    alertify.success('item: '+item.name+', successfully updated the QTY to ' + newQty);
                                }
                            }
                        })
                    }
                }, 1000);
            },
            removeItem(item){
                let self = this;
                if (self.ris_updating) {
                    let conf = confirm('Are you sure you want to remove item: [' + item.name.toUpperCase() + '] permanently?');
                    if(conf){
                        self.db_remove_ris_item(item).then(() => {
                            // alert('done removing ris_item')
                            self.db_save_request_items(self.current_request_form).then((response) => {
                                if (response.status == 200) {
                                    let json =response.data;
                                    self.update_local_request_items(json.names);
                                }
                            })
                        })
                    }
                }else {
                    // alert('not ris updating')
                    self.remove_local_item(item);
                }
                
            },
            getStockNo(item){
                let self = this;
                console.log(item);
            }
        },
        watch: {
            'local_items': function(newVal, oldVal){
                let self = this;
                let ids = _.map(newVal, 'id');
                self.db_find_stock_card_balances(ids);
            },
            'current_r_items': function(newVal, oldVal){
                let self = this;
                if (self.ris_updating) {
                    for (var i = self.current_r_items.length - 1; i >= 0; i--) {
                        self.add_local_items(self.current_r_items[i]);
                    }
                }
            }
        }
    }
</script>
