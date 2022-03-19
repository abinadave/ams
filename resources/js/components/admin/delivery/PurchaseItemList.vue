<template>
    <div>
       <!-- Item(s): {{local_po_items.length}}, -->

       <h2 class="text-danger">Incomplete</h2>
       <table class="table table-condensed table-bordered tbl-pitems-po" style="font-size: 12px">
           <thead class="thead-dark">
            <tr>
              <!-- <th width="50">ID</th> -->
              <th width="80" scope="col">Received</th>
              <th class="text-center" width="60" scope="col">Balance </th>
              <th scope="col">ITEM / DESCRIPTION</th>
              <th class="text-center" width="60" scope="col">QTY</th>
              <th class="text-right" scope="col">UNIT COST</th>
              
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in InCompletedDeliveries">
              <td class="text-center">
                <input @keyup="changeInQtyReceived($event, item)" v-model="item.temp_received" type="text" class="form-contorl foundrm-control-sm text-center" style="width: 50px; height: 20px; border-radius: 5px; padding: 5px">
              </td>
              <td class="text-center">{{ getBalanceOfPurchaseItem(item) }}</td>
              <td>{{ item.name }}</td>
              <th class="text-center" scope="row">{{ item.qty }}</th>
              <td class="text-right">{{ item.unit_cost }}</td>
            </tr>
          </tbody>
          <tbody v-show="!InCompletedDeliveries.length">
              <tr>
                  <td colspan="3">0 item was found for incomplete deliveries</td>
              </tr>
          </tbody>
        </table>

        <h2 class="text-primary">Completed</h2>
        <table class="table table-condensed table-bordered tbl-pitems-po" style="font-size: 12px">
           <thead class="thead-dark">
            <tr>
              <!-- <th width="50">ID</th> -->
              <th width="30" scope="col">Received</th>
              <th scope="col">ITEM / DESCRIPTION</th>
              <th class="text-center" width="60" scope="col">QTY</th>
              <th class="text-right" scope="col">UNIT COST</th>
              <th class="text-center" width="60" scope="col">Balance </th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in completedDeliveries">
              <td class="text-center">
               <!--  <input @keyup="changeInQtyReceived($event, item)" v-model="item.received" type="text" class="form-contorl foundrm-control-sm text-center" style="width: 50px; height: 20px; border-radius: 5px; padding: 5px"> -->
               <i class="fa fa-check"></i>
              </td>
              <td>{{ item.name }}</td>
              <th class="text-center" scope="row">{{ item.qty }}</th>
              <td class="text-right">{{ item.unit_cost }}</td>
              <td class="text-center">{{ getBalanceOfPurchaseItem(item) }}</td>
            </tr>
          </tbody>
          <tbody v-show="!completedDeliveries.length">
              <tr>
                  <td colspan="3">There are no Completed items Delivered as of the moment</td>
              </tr>
          </tbody>
        </table>
    </div>
</template>
<style scoped>
  .tbl-pitems-po td {
    padding: 4px;
  }
  .tbl-pitems-po th {
    padding: 4px;
  }
</style>
<script>
    import {mapState} from 'vuex'
    import {mapActions} from 'vuex'
    export default {
        mounted() {
            // console.log('Component mounted.')
        },
        computed: {
            ...mapState([
                'local_po_items',
                'local_deliveries'
            ]),
            completedDeliveries(){
                let self = this;
                let arrComplete = [], item = {}, rsDeliveries = [], po_item_id = 0;
                for (var i = self.local_po_items.length - 1; i >= 0; i--) {
                    item = self.local_po_items[i];
                    po_item_id = item.id;
                    let rsDeliveries = _.filter(self.local_deliveries, { purchase_item_id: Number(po_item_id)});
                    if(rsDeliveries.length){
                        let balance = self.getBalanceOfPurchaseItem(item);
                        if (Number(balance) == 0) {
                            arrComplete.push(item);
                        }
                    }
                };
                return arrComplete;
            },
            InCompletedDeliveries(){
                let self = this;
                let arrIncomplete = [], item = {}, rsDeliveries = [], po_item_id = 0;
                for (var i = self.local_po_items.length - 1; i >= 0; i--) {
                    item = self.local_po_items[i];
                    po_item_id = item.id;
                    let rsDeliveries = _.filter(self.local_deliveries, { purchase_item_id: Number(po_item_id)});
                    if(rsDeliveries.length){
                        let balance = self.getBalanceOfPurchaseItem(item);
                        if (Number(balance) > 0) {
                            arrIncomplete.push(item);
                        }
                    }else {
                        arrIncomplete.push(item);
                    }
                };
                return arrIncomplete;
            }
        },
        methods: {
            ...mapActions([
                'get_delivery_qty_balance'
            ]),
            randomonly(){
                let self = this;
                let rand = _.random(0,1);
                if (rand == 1){
                  return true;
                }else {return false;};
            },
            changeInQtyReceived(e, item){
                let self = this;
                clearTimeout(self.timer);
                // console.log(item)
                self.timer = setTimeout(function(){
                  let po_item_id = item.id;
                  let qty = Number(item.received);
                  let rsDeliveries = _.filter(self.local_deliveries, { purchase_item_id: Number(po_item_id)});
                  if(rsDeliveries.length){
                      let balance = self.getBalanceOfPurchaseItem(item);
                      if (Number(balance) < 0) {
                          alert('Invalid Qty received, exceeds the total balance, Please verify your inputs.');
                          // item.received = 0;
                      }
                  }else {
                      if (qty > Number(item.qty)) {
                          alert('Invalid Qty received, exceeds the total qty, Please verify your inputs.');
                          // item.received = 0;
                      };
                  };
                }, 1000);
                
            },
            getBalanceOfPurchaseItem(item){
                let self = this;
                let po_item_id = item.id;
                let rsDeliveries = _.filter(self.local_deliveries, { purchase_item_id: Number(po_item_id)});
                let total_balance = Number(item.qty);
                if (rsDeliveries.length) {
                    let ditem = {};
                    for (var i = rsDeliveries.length - 1; i >= 0; i--) {
                        let ditem = rsDeliveries[i];
                        total_balance -= Number(ditem.delivered_qty);
                    }
                }
                if (item.received > 0) {
                    total_balance -= Number(item.received);
                }
                return total_balance;
            }
        },
        watch: {
            'local_po_items': function(newVal, oldVal){
                let self = this;
                let ids = _.map(newVal, 'id');
                self.get_delivery_qty_balance(ids);
            }
        }
    }
</script>
