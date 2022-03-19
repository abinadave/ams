<template>
    <div style="overflow: auto">
       <input @keyup="searchKeyup" v-model="search" type="text" class="form-control form-control-sm"
        style="margin: 10px; width: 300px; border-radius: 25px; display: inline"
        placeholder="Search anything here.." autofocus>

        <button @click="deliverItem" class="btn btn-info btn-sm" style="display: inline">Deliver</button>

        <AdvancedSearchPurchaseOrders style="font-size: 10px" />
       <table class="table table-po table-bordered table-hover" style="font-size: 12px; padding: 20px">
           <thead class="thead-dark">
               <tr>
                   <th colspan="2"></th>
                   <th>P.O Number</th>
                   <th>Supplier Name</th>
                   <th class="text-center">Date of P.O</th>
                   <th width="80" class="text-center">Deliveries</th>
                   <th class="text-center" width="100">Delivery Complete?</th>
                   <th>P.O Category</th>
                   <th width="120">Mode of Procurement</th>
                   <th width="50" class="text-center">Total items</th>
                   <th class="text-right">Total Amount</th>
                   <!-- <th>Items</th> -->
               </tr>
           </thead>
           <tbody>
               <tr @click="setCurrentPo(po)" v-for="po in purchase_orders">
                   <td>
                     <a @click="showPoDetails(po)" style="cursor: pointer" class="text-primary"><i class="fa fa-file-text" aria-hidden="true"></i> <span style="font-size: 10px">{{po.count_items}}</span></a>
                   </td>
                   <td>
                      <a @click="editPurchaseOrder(po)" class="links text-primary"><i class="fa fa-pencil text-danger"></i></a>
                   </td>
                   <td>
                      {{ po.po_year }}-{{ po.po_month }}-{{ po.po_number }}
                   </td>
                   <td>{{ po.supplier_name }}</td>
                   <td class="text-center">{{ po.date | format_date }}</td>                  
                   <td class="text-center">
                      <b>
                        <a class="text-primary" @click="showDeliveriesofPo(po)" style="cursor: pointer">{{ po.count_deliveries }}</a>
                      </b>
                   </td>
                   <td v-if="po.delivery_completed == 1" class="text-center" width="50">
                        <span style="font-size: 16px" class="fa fa-check text-primary"></span>
                   </td>
                   <td v-else="po.delivery_completed == 1" class="text-center" width="50">
                        <b class="text-danger" title="Will be checked after IAR NO. is filled up on Delivery form or once delivery is complete">No</b>
                   </td>
                   <td>{{ po.po_cat_name}}</td>
                   <td>
                      <span v-if="po.total_amount > 50000">
                        SMALL VALUE
                      </span>
                      <span v-else>
                        SHOPPING
                      </span>
                   </td>
                   <td class="text-center">{{ po.count_items }}</td>
                   <th class="text-right text-danger" style="font-size: 13px">{{ po.total_amount | convert_money }}</th>
               </tr>
           </tbody>
       </table>
       <!-- <button type="button" @click="paginateNext">NEXT</button> -->
       <ModalPurchaseOrderDetails />
       <CreateDeliveryForPurchaseOrder />
    </div>
</template>
<style scoped>
    .links {
        cursor: pointer;
    }
    .table-po td {
        padding: 4px;
    }
    .table-po th {
        padding: 4px;
    }
</style>
<script>
    import {mapActions, mapState, mapMutations} from 'vuex'
    import moment from 'moment'
    import accounting from 'accounting'
    import AdvancedSearchPurchaseOrders from './AdvancedSearchPurchaseOrders'
    import ModalPurchaseOrderDetails from './ModalPurchaseOrderDetails'
    import CreateDeliveryForPurchaseOrder from '../delivery/CreateDeliveryForPurchaseOrder'
    import alertify from 'alertify.js'
    export default {
        mounted() {
            let self = this;
            self.fetch_purchase_orders().then(() => {
                setTimeout(function(){
                    self.add_jquery_event_click();
                }, 1000)
            })
            self.clear_local_items();
            self.purchase_order_updating(false);
        },
        components: {
            AdvancedSearchPurchaseOrders,
            ModalPurchaseOrderDetails,
            CreateDeliveryForPurchaseOrder
        },
        methods: {
            ...mapActions([
                'fetch_purchase_orders',
                'search_purchase_order',
                'fetch_purchase_item_by_id',
                'nextpage',
                'fetch_po_deliveries',
                'fetch_supplier_name',
                'fetch_po_item_views',
                'search_po_number'
            ]),
            ...mapMutations([
                'purchase_order_updating',
                'set_purchase_order_form',
                'add_local_items',
                'clear_local_items',
                'purchase_order_updating',
                'set_current_purchase_order',
                'set_c_delivery_is_po'
            ]),
            deliverItem(){
                let self = this;
                if (typeof self.current_po.id  == 'undefined') {
                    alert('Please select atleast one P.O below');
                }else {
                    // self.fetch_
                    if (Number(self.current_po.delivery_completed) == 1) {
                        alertify.alert('Delivery Completed');
                    }else {
                        self.search_po_number(self.current_po);
                        $('#CreateDeliveryForPurchaseOrder').modal('show');
                    }
                    
                }
            },
            showPoDetails(po){
                let self = this;
                self.set_current_purchase_order(po);
                self.fetch_supplier_name(po);
                self.fetch_po_item_views(po);
                $('#ModalPurchaseOrderDetails').modal('show');
            },
            showDeliveriesofPo(po){
                let self = this;
                if (po.count_deliveries > 0) {
                   self.set_current_purchase_order(po);
                   self.set_c_delivery_is_po(true);
                   self.fetch_po_deliveries(po).then(() => {
                      $('#ModalPoDeliveries').modal('show');
                   })
                 }else {
                    alert('zero deliveries for this P.O');
                 }
            },
            setCurrentPo(po){
                let self = this;
                self.set_current_purchase_order(po);
            },
            add_jquery_event_click(){
                let self = this;
                jQuery(document).ready(function($) {
                    let $table = $('.table-po tbody');
                    $table.find('tr').click(function(){
                        $table.find('tr').removeClass('text-primary');
                        $(this).addClass('text-primary');
                    })  
                });
            },
            paginateNext(){
                let self = this;
                self.nextpage();
            },
            editPurchaseOrder(po){
                let self = this;
                self.purchase_order_updating(true);

                self.fetch_po_deliveries(po).then(() => {
                  // alert('done');
                  console.log('done fetching ')
                });

                setTimeout(function(){
                    jQuery('#profile-tab')[0].click();
                    self.set_purchase_order_form(po);
                    self.fetch_purchase_item_by_id(po.id).then((response) => {
                        if (response.status === 200) {
                            let json = response.data;
                            let item = {}, obj = {};
                            for (var i = json.length - 1; i >= 0; i--) {
                                item = json[i];
                                obj = {
                                      id: item.item_id,
                                      name: item.name,
                                      qty: item.qty,
                                      unit_cost: item.unit_cost,
                                      primary_id: item.id
                                };
                                self.add_local_items(obj);
                            }
                        }
                    });
                }, 500);
            },
            searchKeyup(){
                let self = this;
                clearTimeout(self.timer);
                self.timer = setTimeout(function(){
                    self.search_purchase_order(self.search);
                }, 750);
            }
        },
        computed: {
            ...mapState([
                'purchase_orders',
                'current_po'
            ])
        },
        filters: {
            convert_money(m){
                return accounting.formatMoney(m, '', 2);
            },
            format_date(d){
                return moment(d).format('MMMM DD, YYYY');
            },
            substring_filter(items){
                let self = this;
                if (items.length >= 100) {
                    return items.substring(0, 70) + '....';
                }else {
                    return items;
                }
            }
        },
        data(){
            return {
                search: ''
            }
        }
    }
</script>
