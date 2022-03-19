<template>
    <div style="width: 70%">
        <button @click="showModalSelectItem"  type="button" class="btn btn-sm btn-dark float-right">Select Items</button><br>
       Items selected: {{local_items.length}}
       <table v-show="local_items.length" class="table tbl-apr-item-local">
           <thead>
               <tr>
                   <th width="500">Item / Description</th>
                   <th>Unit name</th>
                   <th width="50">Qty</th>
                   <th width="50">Unit Cost</th>
                   <th>Amount</th>
                   <th width="10"></th>
               </tr>
           </thead>
           <tbody>
               <tr v-for="item in local_items">
                   <td>{{ item.name }}- {{item.description}}</td>
                   <td>{{ item.unit }}</td>
                   <td>
                       <input @keyup="updateQtyCost($event, item, 'qty')" width="50" type="number" v-model="item.qty">
                   </td>
                   <td>
                       <input @keyup="updateQtyCost($event, item, 'unit_cost')" width="50" type="number" v-model="item.unit_cost">
                   </td>
                   <td>
                       {{ getAmount(item) | format_money }}
                   </td>
                   <td><a @click="removeItem(item)" style="cursor: pointer" class="fa fa-remove"></a></td>
               </tr>
               <tr>
                   <td colspan="4" class="text-center">TOTAL AMOUNT</td>
                   <th>{{ overAllTotal | format_money }}</th>
               </tr>
           </tbody>
       </table>
       <!-- {{ local_items }} -->
    </div>
</template>
<style scoped>
    .tbl-apr-item-local {
        font-size: 13px;
    }
    .tbl-apr-item-local td, th{
        padding: 3px;
    }
</style>
<script>
    import {mapState, mapMutations, mapActions} from 'vuex'
    import accounting from 'accounting'
    import alertify from 'alertify.js'
    export default {
        mounted() {

        },
        methods: {
            ...mapActions([
                'db_update_apr_item',
                'db_delete_apr_item'
            ]),
            ...mapMutations([
                'remove_local_item',
                'add_local_items'
            ]),
            updateQtyCost(e, item, prop){
                let self = this;
                clearTimeout(self.timer);
                self.timer = setTimeout(function(){
                    self.db_update_apr_item({
                        item,
                        prop
                    }).then((response) => {
                        if (response.status == 200) {
                            let json = response.data;
                            if (json.rs_updated) {
                                let val = (json.prop == 'qty') ? json.apr_item_view.qty : json.apr_item_view.unit_cost;
                                alertify.success(json.prop + ', successfully updated to: ' + val);
                            }else {
                                alertify.log('Unable to contact server, Please inform your Database Administrator');
                            };
                        }
                    });
                }, 1000);
            },
            removeItem(item){
                let self = this;
                if (self.updating_apr) {
                  let conf = confirm('This APR item will be removed permanently, [' + item.name + '], ' + item.description + ', are you sure?');
                  if (conf) {
                      self.db_delete_apr_item(item).then((response) => {
                          if (response.status == 200) {
                              let json = response.data;
                              if(json.deleted){
                                  alertify.log('Item permanently deleted!');
                              }else {
                                  alertify.log("Can't Delete item, unable to contact server, Please contact System/Database Administrator");
                              }
                          }
                      })
                  }
                }else {
                  let conf = confirm('Are your sure you want to remove item: [' + item.name + '], ' + item.description);
                  if (conf) {
                      self.remove_local_item(item);
                  }
                }
            },
            getAmount(item){
                let self = this;
                let total = 0;
                total = Number(item.qty) * Number(item.unit_cost);
                if (total > 0) {
                    return total;
                }else {
                    return 0;
                }
            },
            createAprItems(){
                let self = this;
                console.log('creating items pls wait..');
            },
            showModalSelectItem(){
                $('#modalItemSelection').modal('show');
                setTimeout(function(){
                    $('.search-box').focus();
                }, 2000);
            }
        },
        computed: {
            ...mapState([
                'local_items',
                'updating_apr',
                'current_apr_items'
            ]),
            overAllTotal(){
                let self = this;
               return  _.reduce(self.local_items, function(sum, model) {
                  return sum + Number(model.qty) * Number(model.unit_cost);
                }, 0);    
            }
        },
        filters: {
            format_money(n){
                return accounting.formatMoney(n, " " , 2);
            }
        },
        watch: {
            'updating_apr': function(newVal, oldVal){
                let self = this;
                
            }
        }
    }
</script>
