<template>
    <div>
       <table class="table table-condensed table-bordered tbl-apr-items" style="font-size: 12px">
           <thead class="thead-dark">
            <tr>
              <th class="text-center" width="60" scope="col">Balance </th>
              <th width="30" scope="col">Received</th>
              <th scope="col">ITEM / DESCRIPTION</th>
              <th class="text-center" width="60" scope="col">QTY</th>
              <th class="text-right" scope="col">UNIT COST</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="item in local_po_items">
              <td class="text-center">{{getBalance(item)}}</td>
              <td class="text-center">
                <input v-model="item.received" type="text" class="foundrm-control-sm text-center" style="width: 50px; height: 26px; border-radius: 5px; padding: 5px">
              </td>
              <td>{{ item.name }}</td>
              <th class="text-center" scope="row">{{item.qty}}</th>
              <td width="200" class="text-right">{{item.unit_cost}}</td>
            </tr>
          </tbody>
          <tbody v-show="!local_po_items.length">
              <tr>
                  <td colspan="5">0 item was found</td>
              </tr>
          </tbody>
        </table>
    </div>
</template>
<style scoped>
  .tbl-apr-items th {
      padding: 4px;
  }
  .tbl-apr-items td {
      padding: 4px;
  }
</style>
<script>
    import {mapState, mapActions} from 'vuex'
    export default {
        mounted() {

        },
        methods: {
            getBalance(item){
                let self = this;
                let rsItems = _.filter(self.current_apr_balance, {item_id: Number(item.item_id)});
                let total_delivered = 0; let c_item = {};
                for (var i = rsItems.length - 1; i >= 0; i--) {
                    c_item = rsItems[i];
                    total_delivered+= Number(c_item.delivered_qty);
                }
                return Number(item.qty) - Number(total_delivered);
            }
        },
        computed: {
           ...mapState([
              'local_po_items',
              'current_apr_balance'
           ])
        },
       
    }
</script>
