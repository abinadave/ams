<template>
    <div>
       <div class="modal fade" id="ModalStockCardReport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">STOCK CARD REPORT</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body modal-stockcardreport">
                <h5 class="text-center" style="margin-top: 40px">STOCK CARD REPORT</h5>
                <h6>
                    ENTITY NAME: <b>Department of the Interior & Local Government 
                    <br> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                     &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Regional Office 08, Tacloban City</b>
                </h6>
                <h6 style="margin-top: -38px" class="text-right"><b>Fund Cluster 01</b></h6>
                <br />
                <table class="table table-bordered table-condensed tbl-stock-card-report" id="tbl-scr-1">
                    <thead>
                        <tr>
                            <td>ITEM: 
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <b>
                                    {{ item.name }}
                                </b>
                            </td>
                            <td width="500">STOCK NO: 
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <b>
                                    {{ item.rca_code }}-{{ item.rca_cat }}-{{ item.rca_no }}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td>DESCRIPTION: 
                                 &nbsp;&nbsp;&nbsp;&nbsp;
                                <b>
                                    {{ item.description }}
                                </b>
                            </td>
                            <td>REORDER POINT: 
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <b>
                                    {{ item.reorder_point }}
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td>UNIT OF MEASUREMENT: 
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <b>
                                    {{ item.unit_name }}
                                </b>
                            </td>
                            <td>CURRENT BALANCE
                                &nbsp;&nbsp;&nbsp;&nbsp;
                                <b>
                                    {{ getActualBalanceOfItem(item.id) }}
                                </b>
                            </td>
                        </tr>
                    </thead>
                    
                </table>
                <table class="table table-bordered table-condensed tbl-stock-card-report"> 
                    <!-- <caption>Life Expectancy By Current Age</caption>  -->
                    <tr> 
                        <th width="150" rowspan="2" class="text-center">DATE</th> 
                        <th width="200" rowspan="2" class="text-center">REFERENCE</th> 
                        <th colspan="3" class="text-center">RECEIPT</th> 
                        <th colspan="2" class="text-center">ISSUANCE</th> 
                        <th>BALANCE</th>
                        <th rowspan="2" width="100">NO. OF DAYS TO CONSUME</th>
                    </tr> 
                        <tr> 
                            <!-- START RECEIPT -->
                            <th width="70">QTY</th> 
                            <th width="100">UNIT PRICE</th> 
                            <th width="300">SUPPLIER</th> 
                            <!-- END RECEIPT -->

                            <!-- START ISSUANCE -->
                            <th width="70">QTY</th> 
                            <th width="180">OFFICE</th> 
                            <!-- END ISSUANCE -->

                            <!-- START balance -->
                            <th width="70">QTY</th>
                            <!-- end balance -->
                        </tr> 

                        <!-- START initial balance -->
                        <tr>
                            <td>
                                {{ initial_balance.bal_remarks }}
                            </td>
                            <td>
                               
                            </td>

                            <td>
                               
                            </td>
                            <td>
                                
                            </td> 
                            <td> 
                                
                            </td> 

                            <td>
                               
                            </td>
                            <td>
                                
                            </td>

                            <td>
                               {{ initial_balance.balance }}
                               <!--  -->
                            </td>

                            <td></td>
                        </tr>   
                        <!-- END initial balance -->

                        <tr v-for="(stockcard, index) in stock_cards"> 
                            <td>
                                {{ stockcard.timestamp | format_date }}
                            </td>
                            <td>
                                <!-- <span v-if="stockcard.is_po"> -->
                                    {{getStockCardNo(stockcard)}}
                               <!--  <span v-if="stockcard.delivery_type == 'po'"></span>
                                    PO No. {{ stockcard.po_year }}-{{stockcard.po_month}}-{{stockcard.po_number}}
                                </span>
                                <span v-else>
                                    RIS No. {{ stockcard.ris_year }}-{{stockcard.ris_month}}-{{stockcard.ris_number}}
                                </span> -->
                            </td>

                            <td>
                                <span v-show="stockcard.is_po">
                                    {{ stockcard.delivered_qty }}
                                </span>
                            </td>
                            <td>
                                <span v-show="stockcard.is_po">
                                    {{ stockcard.unit_cost }}
                                </span>
                            </td> 
                            <td> 
                                <span v-show="stockcard.is_po">
                                    {{ stockcard.supplier_name }}
                                </span>
                            </td> 

                            <td>
                                <span v-show="!stockcard.is_po">
                                    {{ stockcard.requested_qty }}
                                </span>
                            </td>
                            <td>
                                <span v-show="!stockcard.is_po">
                                    {{ stockcard.office }}
                                </span>
                            </td>

                            <td>
                                <!-- {{ getBalanceQty(stockcard, index) }} -->
                                {{ getbal(stockcard, index) }}
                                <span v-if="stockcard.is_po">
                                    <!-- {{stockcard.qty_before_deliver + stockcard.delivered_qty}} -->
                                    
                                </span>
                                <span v-else>
                                    <!-- {{stockcard.qty_before_withdraw - stockcard.requested_qty}} -->
                                </span>
                            </td>

                            <td></td>
                        </tr> 
                        <!-- <tr v-for=""></tr> -->
                        <tr>
                            <td></td>
                            <td></td>
                            <th colspan="4" style="color: red !important;">
                                ***************
                                    Nothing Follows  
                                ***************
                            </th>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <!-- <tr v-for="thisitems in td_left - stock_cards.length">
                            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                            <td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
                        </tr> -->
                </table>
                <!-- <div v-show="stock_cards.length" class="text-center"> -->
                    <!-- <h5 class="text-danger">**** Nothing Follows  ****</h5> -->
                <!-- </div> -->
               <!--  <div v-if="stock_cards.length == 0">
                    <div class="alert alert-primary" role="alert">
                      No report was found for this item: <b>{{ item.name }}, {{ item.description }}</b> 
                    </div>
                </div> -->
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button  class="btn btn-primary" @click="printStockCard"><i class="fa fa-print"></i> Print</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>
<style scoped>
  .modal-lg {
      max-width: 90% !important;
  }
  .modal-stockcardreport {
    font-size: 12px;

  }
  .tbl-stock-card-report td {
    padding: 2px;
    text-align: center;
    color: black;
    border: 1px solid black !important;
  }
  .tbl-stock-card-report th {
    padding: 2px;
    text-align: center;
    color: black;
    border: 1px solid black !important;
  }
  #tbl-scr-1 td{
    text-align: left;
  }
</style>
<script>
    import {mapState, mapActions, mapMutations} from 'vuex'
    import moment from 'moment'
    export default {
        mounted() {

        },
        data(){
            return {
                td_left: 15,
                last_total_bal: 0,
                prev_data: 0
            }
        },
        computed: {
            ...mapState({
                'item': 'current_item',
                'stock_cards': 'stock_card_report',
                'initial_balance': 'initial_balance',
                'actual_stocks': 'actual_stocks'
            })
        },
        filters: {
            format_date(d){
                return moment(d).format('MMM DD, YYYY');
            }
        },
        watch: {
            'stock_cards': function(newVal, oldVal){
                let self = this;
                let stock = {};
                for (var i = newVal.length - 1; i >= 0; i--) {
                    stock = newVal[i];
                    if (stock.delivery_type == 'apr') {
                        console.log('this is APR')
                        console.log(stock);
                    }else if(stock.delivery_type == 'po'){
                        console.log('this is P.O');
                        console.log(stock)
                    }
                }
            }
        },
        methods: {
            ...mapMutations([
                'crystal_report_1'
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
            getStockCardNo(stockcard){
                let self = this;
                let type = stockcard.delivery_type;
                if (type == 'po') {
                    // return 'po';{{ stockcard.po_year }}-{{stockcard.po_month}}-{{stockcard.po_number}}
                    return 'PO: ' + stockcard.po_year + '-' + stockcard.po_month + '-' + stockcard.po_number;
                }else if(type == 'apr'){
                    // return 'apr';
                    return 'APR: ' +stockcard.apr_no;
                }else {
                    // {{ stockcard.ris_year }}-{{stockcard.ris_month}}-{{stockcard.ris_number}}
                    return 'RIS: ' + stockcard.ris_year + '-' + stockcard.ris_month + '-' + stockcard.ris_number;
                }
            },
            getbal(stockcard, index){
                let self = this;
                // self.prev_total = 0;
                let total = 0;
                if (!index) {
                    /** will just run once **/
                    
                    if (stockcard.delivery_type == 'po') {
                        total = Number(self.initial_balance.balance) + Number(stockcard.delivered_qty)
                    }else if(stockcard.delivery_type == 'apr'){
                        total = Number(self.initial_balance.balance) + Number(stockcard.delivered_qty)
                    }else if(stockcard.delivery_type == 'ris'){
                        total = Number(self.initial_balance.balance) - Number(stockcard.requested_qty)
                    }
                }else {
                    /** Main Process **/
                    if (stockcard.delivery_type == 'po') {
                        total = Number(self.prev_total) + Number(stockcard.delivered_qty);
                    }else if(stockcard.delivery_type == 'apr'){
                        total = Number(self.prev_total) + Number(stockcard.delivered_qty);
                    }else if(stockcard.delivery_type == 'ris'){
                        total =  Number(self.prev_total) - Number(stockcard.requested_qty);
                    }
                }
                
                /** Always set the Previous Total for reference **/
                self.prev_total = total;

                return total;
            },
            getBalanceQty(stockcard, index){
                let self = this, last_bal = 0;
                let total_bal = 0;
                self.prev_qty = 0;

                if (stockcard.is_po == 1) {
                    // PURCHASE ORDER , P.O FLOW
                    if (index == 0) {
                        // for first item only
                        last_bal = Number(self.initial_balance.balance);
                        total_bal = Number(last_bal) + Number(stockcard.delivered_qty);
                        self.last_total_bal = total_bal;
                        self.prev_qty = Number(stockcard.requested_qty);
                    }else {
                        // for other deliveries not 0 index
                        // return 'apr';
                        
                        if (stockcard.delivery_type == 'apr' || stockcard.delivery_type == 'po') {
                            // total_bal += Number(self.prev);
                            // self.last_total_bal = total_bal;
                        }else if(stockcard.is_po == 0) {
                           
                        }

                    }
                }else {
                    //  RIS, Requisition FLOW
                    if (index == 0) {
                        // for first item only
                        last_bal = Number(self.initial_balance.balance);
                        total_bal = Number(last_bal) - Number(stockcard.requested_qty);
                        self.last_total_bal = total_bal;
                    }else {
                        // for other deliveries not 0 index
                        
                        let newIndex = Number(index) - 1;
                        let itemBefore = self.stock_cards[newIndex];
                        if (itemBefore.is_po == 1) {
                            /** if index - 1 is P.O **/
                            // last_bal = Number(itemBefore.delivered_qty);
                            // total_bal = Number(self.last_total_bal) - Number(stockcard.requested_qty);
                            // self.last_total_bal = total_bal;
                        }else {
                            /** if index - 1 is R.I.S **/
                            total_bal = Number(self.last_total_bal) - Number(stockcard.requested_qty);
                            self.last_total_bal = total_bal;
                        }
                    }
                }
                return total_bal;
            },
            setLastQty(n){
                let self = this;
                
            },
            printStockCard(){
                let self = this;
                $('#ModalStockCardReport').modal('hide');
                if (self.stock_cards.length) {
                    // $('#ModalStockCardReport').modal('hide');
                    setTimeout(function(){
                        self.crystal_report_1({
                            cloned_html: $('.modal-stockcardreport').clone()
                        });
                    }, 1500);
                }else {
                    let conf = confirm('No data was found for this item, Continue to print');
                    if (conf) {
                        setTimeout(function(){
                            self.crystal_report_1({
                                cloned_html: $('.modal-stockcardreport').clone()
                            });
                        }, 1500);
                    }
                    
                }
                
            }
        }
    }
</script>
