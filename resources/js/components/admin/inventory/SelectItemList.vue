<template>
    <div style="overflow: auto; height: 500px">
        <!-- This is the ModalSelectItem table selection list of all inventory/materials/items -->
        
        <table class="table table-hover table-bordered tbl-select-item table-striped">
              <thead>
                <tr>
                  <th width="10"></th>
                  <th>Item Code</th>
                  <th scope="col">Item name</th>
                  <th class="text-center" scope="col">Category</th>
                  <th class="text-center" scope="col">Unit</th>
                  <th class="text-center" scope="col">Stock</th>
                  <th class="text-center" width="60" scope="col">Reorder point</th>
                  <th scope="col">Description</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item in items">
                  <td>
                    <!-- :checked!duplicate purpose:
                         to uncheck unselected items when removed from the po list
                    -->
                    <input :checked="!duplicateItem(item) == false" v-show="duplicateItem(item) == false" @change="inputSelected($event, item)" type="checkbox">
                  </td>
                  <th class="text-center">{{ item.rca_code }}-{{item.rca_cat}}-{{item.rca_no}}</th>
                  <th>{{ item.name }}</th>
                  <td class="text-center">{{ item.cat_name }}</td>
                  <td class="text-center">{{ item.unit_name }}</td>
                  <td class="text-center">{{ getActualBalanceOfItem(item.id) }}</td>
                  <td class="text-center">{{ item.reorder_point }}</td>
                  <td>{{ item.description }}</td>
                </tr>
              
              </tbody>
            </table>
    </div>
</template>
<style scoped>
    .tbl-select-item {
        font-size: 12px;
    }
    .search-box {
        margin-bottom: 10px;
    }
</style>
<script>
    import {mapState, mapActions, mapMutations} from 'vuex'
    export default {
        mounted() {
            let self = this;
            setTimeout(function(){
                self.fetch_items().then((response) => {
                    if (response.status == 200) {
                        let json = response.data;
                        // alert('done')
                        // alert(1)
                        self.getBalances();
                    }
                })
            }, 2000);
        },
        methods: {
            ...mapActions([
                'fetch_items',
                'db_add_one_purchase_item',
                'db_add_ris_item',
                'db_save_request_items',
                'db_find_stock_card_balances'
            ]),
            ...mapMutations([
                'add_local_items',
                'remove_local_item',
                'set_purchase_order_delivery_completed',
                'set_current_selected_item',
                'update_local_request_items'
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
            getBalances(){
                let self = this;
                let ids = _.map(self.items, 'id');
                // console.log(ids);
                self.db_find_stock_card_balances(ids).then((response) => {
                    if (response.status == 200) {
                        let json = response.data;
                        // console.log(self.actual_stocks)
                    }
                });

            },
            duplicateItem(item){
                let self = this;
                let rs = _.filter(self.local_items, {id: Number(item.id) });
                if (rs.length) {
                    return true;
                }else {
                    return false;
                }
            },
            inputSelected(event, item){
                let self = this;

                if (event.target.checked) {
                    /* checked */
                    // alert(self.$route.query.page)
                    if (router.currentRoute.path == '/Requisition') {
                        
                        if (self.ris_updating) {
                            self.db_add_ris_item(item).then(() => {
                                self.db_save_request_items(self.current_request_form).then((response) => {
                                    if (response.status == 200) {
                                        let json =response.data;
                                        self.update_local_request_items(json.names);
                                    }
                                })
                            })
                        }else {
                             self.add_local_items(item);
                        }
                    }else {
                        if (self.po_updating) {
                          
                          // let conf = confirm('Do you want to add: '+item.name+' to this P.O?');
                          $('#ModalQuestionPoItemDelivery').modal();
                          // alert('set_current_selected_item')
                          self.set_current_selected_item(item);
                          
                          
                      }else {
                          self.add_local_items(item);
                      }
                    }
                    

                }else {
                    self.remove_local_item(item);
                }
            }
        },
        computed: {
            ...mapState([
                'items',
                'local_items',
                'po_updating',
                'ris_updating',
                'current_po',
                'current_request_form',
                'actual_stocks'
            ])
        }
    }
</script>
