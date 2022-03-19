<template>
    <div >
        <input v-model="search" type="text" class="search-items form-control form-control-sm" style="border-radius: 15px; width: 250px; float: left; margin-top: 10px; margin-bottom: 10px" placeholder="Search items here.." autofocus><br>
      
        <div >
            <table id="tbl-item-list" class="table table-hover table-bordered table-condensed" 
            >
              <thead class="thead-dark">
                <tr>
                  <th width="50">Item_ID</th>
                  <th width="120">Stock No</th>
                  <th width="300">Item name</th>
                  <th width="100">Category</th>
                  <th width="60" class="text-center">Unit</th>
                  <th width="60" class="text-center">Running Balance</th>
                  <th width="200" class="text-center">Running Balance Remarks</th>
                  <th width="70" class="text-center">Reorder point</th>
                  <th>Additional Description</th>
                  <th colspan="2" width="80"></th>
                </tr>
              </thead>
              <tbody>
                <tr :key="item.id" v-for="item in items">
                  <td class="text-center">{{item.id}}</td>
                  <td>{{ item.rca_code }}-{{item.rca_cat}}-{{item.rca_no}}</td>
                  <th>{{ item.name }}</th>
                  <td>{{ item.cat_name }}</td>
                  <td class="text-center">{{ item.unit_name }}</td>
                  <td class="text-center">{{ getActualBalanceOfItem(item.id) }}</td>
                  <td class="text-center">{{ item.running_bal_remarks }}</td>
                  <td class="text-center">{{ item.reorder_point }}</td>
                  <td>{{ item.description }}</td>
                  <td width="40">
                    <a class="text-primary btn btn-outline-primary btn-sm" style="height:15px; font-size: 7px; cursor: pointer"  @click="showStockCardReport(item)">report</a>
                    <!-- <button style="font-size: 8px" class="btn btn-outline-primary btn-sm">View Report</button> -->
                  </td>
                  <td width="10">
                      <a @click="editItem(item)" style="font-size: 12px; cursor: pointer" class="fa fa-edit"></a>
                  </td>
                </tr>
              </tbody>
             <!--  <tr>
                
              </tr> -->
            </table>
            
        </div>       
        <ModalStockCardReport />
        <!-- {{actual_stocks}} -->
       
    </div>
</template>
<style scoped>
    
    .search-items {
        margin-top: -150px;
    }
    #tbl-item-list td  {
      padding: 2px;
      font-size: 12px;
    }
    #tbl-item-list th {
      padding: 4px;
      font-size: 12px;
    }
</style>
<script>
    import {mapState, mapActions, mapMutations} from 'vuex'
    import ModalStockCardReport from './ModalStockCardReport'
    export default {
        mounted() {
            let self = this;
            self.count_total_items();
        },
        data(){
            return {
                search: ''
            }
        },
        components: {
            ModalStockCardReport
        },
        computed: {
            ...mapState([
                'items',
                'total_count_items',
                'actual_stocks',
            ])
        },
        methods: {
            ...mapActions([
                'count_total_items',
                'search_for_items',
                'fetch_stock_card_report',
                'fetch_initial_balance',
                'db_find_stock_card_balances'
            ]),
            ...mapMutations([
                'searching_items',
                'set_current_item',
                'set_update_item',
                'set_updating_item'
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
            editItem(item){
                let self = this;
                $('#profile-tab').trigger('click');
                self.set_updating_item(true);
                self.set_update_item(item);
            },
            showStockCardReport(item){
                let self = this;
                self.fetch_stock_card_report(item.id);
                self.fetch_initial_balance(item.id);
                self.set_current_item(item);
                $('#ModalStockCardReport').modal('show');
            }
        },
        watch: {
            'search': function(newVal, oldVal){
                let self = this;
                clearTimeout(self.timer);
                self.timer = setTimeout(function(){
                    self.search_for_items(self.search).then((response) => {
                        if (response.status == 200) {
                            let json = response.data;
                            let ids = _.map(self.items, 'id');
                            self.db_find_stock_card_balances(ids).then((response) => {
                                console.log('done db_find_stock_card_balances')
                                console.log(response)
                            });
                        }else {
                            alert('There was a problem while searching for items, ItemList.vue, Please contact System Administrator');
                        }
                    })  
                }, 1000);
            }
        }
    }
</script>
