<template>
    <div><hr>
       <!-- <h4>Purchased Items </h4> -->
       <table id="tbl-po-item-views" class="table table-bordered table-hover">
           <thead>
               <tr>
                   <th class="text-center" width="50">QTY</th>
                   <th class="text-center">UNIT</th>
                   <th width="500">ITEM NAME</th>
                   <th class="text-right" width="100">UNIT COST</th>
                   <th class="text-right">AMOUNT</th>
               </tr>
           </thead>
           <tbody>
               <tr v-for="item in purchase_item_views">
                   <td class="text-center">{{ item.qty }}</td>
                   <td class="text-center">{{ item.unit_name }}</td>
                   <td>{{ item.name }}</td>
                   <td class="text-right">{{ item.unit_cost | format_money }}</td>
                   <th class="text-right">{{ calculateAmount(item) }}</th>
               </tr>
               <tr>
                   <th colspan="4" class="text-center">TOTAL</th>
                   <th style="font-size: 16px" class="text-danger text-right">{{ calculateOverAllTotal }}</th>
               </tr>
           </tbody>
       </table>
    </div>
</template>
<style>
    #tbl-po-item-views {
        font-size: 12px;
    }
    #tbl-po-item-views th {
        padding: 4px;
    }
    #tbl-po-item-views td {
        padding: 4px;
    }
</style>
<script>
    import {mapState} from 'vuex'
    import {accounting} from 'accounting'
    export default {
        mounted() {

        },
        methods: {
            calculateAmount(item){
                let self = this;
                let total = Number(item.qty) * Number(item.unit_cost);
                if (total > 0) {
                    return accounting.formatMoney(total, " ", 2);
                }else {
                    return '0.00';
                }
            }
        },
        computed: {
            ...mapState([
                'purchase_item_views'
            ]),
            calculateOverAllTotal(){
                let self = this;
                let total = 0;
                let item = {};
                for (var i = self.purchase_item_views.length - 1; i >= 0; i--) {
                    item = self.purchase_item_views[i];
                    total += Number(item.qty) * Number(item.unit_cost);
                };
                return accounting.formatMoney(total, "Php ", 2);
            }
        },
        filters: {
            format_money(m){
                return accounting.formatNumber(m);
            }
        }
    }
</script>
