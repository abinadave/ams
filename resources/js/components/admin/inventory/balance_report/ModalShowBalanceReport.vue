<template>
    <div>
       <div class="modal fade" id="ModalShowBalanceReport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="heading1">Inventory Balance Report 
                    <b><i @click="printReport" style="cursor: pointer" class="text-primary fa fa-print"></i></b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body balance-report">
                    <img src="/app/dilg-logo.png" width="50">
                    <h2 id="heading2" class="text-center">INVENTORY BALANCE REPORT</h2>
                    <h2 id="heading3" class="text-center">{{ getDate }}</h2>
                     <br><h6>
                        ENTITY NAME: <b>Department of the Interior & Local Government 
                        <br> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                         &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Regional Office 08, Tacloban City</b>
                    </h6>
                    <table class="table table-bordered table-hover table-condensed tbl-inventory-balance">
                        <thead>
                            <tr>
                                <th width="30">uid</th>
                                <th class="text-center" width="170">Category</th>
                                <th width="500">Item Name / Description</th>
                                <th class="text-center">Unit</th>
                                <th class="text-center">Running Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in items">
                                <td class="text-center">{{item.id}}</td>
                                <td class="text-center">{{ item.rca_code }}-{{ item.rca_cat }}-{{ item.rca_no }}</td>
                                <td>{{ item.name }} {{ item.description }}</td>
                                <td class="text-center">{{ item.unit_name }}</td>
                                <td class="text-center">{{ getActualBalanceOfItem(item) }}</td>
                            </tr>
                        </tbody>
                    </table>
              </div>
              <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close Report</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>
<style scope>
    .modal-lg {
        max-width: 60% !important;
    }
    .tbl-inventory-balance td {
        padding: 5px;
        font-size: 14px;
        border-style: solid;
        border-width: 1px;
        border-color: black;
    }
    .tbl-inventory-balance th {
        text-align: center;
        padding: 2px;
        border-style: solid;
        border-width: 1px;
        border-color: black;
    }
    #heading1 {
        font-family: "Times New Roman", Times, serif;
        font-size: 16px;
        font-weight: bolder;
    }
    #heading2 {
        font-family: "Times New Roman", Times, serif;
        font-size: 16px;
        font-weight: bolder;
        margin-top: -40px;
    }
    #heading3 {
        font-family: "Times New Roman", Times, serif;
        font-size: 16px;
        font-weight: bolder;
        margin-top: -5px;
    }
</style>
<script>
    import {mapState, mapMutations, mapGetters } from 'vuex'
    import moment from 'moment'
    export default {
        mounted() {
            
        },
        computed: {
            ...mapState([
                'items',
                'actual_stocks'
            ]),
            getDate(){
                let self = this;
                return moment().format('MMMM DD, YYYY hh:mm a');
            },

        },
        methods: {
            ...mapMutations([
                'crystal_report_1'
            ]),
            getActualBalanceOfItem(item){
                let self = this;
                let item_id = item.id;
                
                let rsStocks = _.filter(self.actual_stocks, {item_id: Number(item_id)});
                // return rsStocks.length;
                if (rsStocks.length) {
                    let first = _.first(rsStocks);
                    return first.actual_stock;
                }else{
                    return 0;
                }
            },  
            printReport(){
                let self = this;
                let $cloned = $('.balance-report').clone();
                let $tbl = $('#tbl-inventory-balance');
                $('#ModalShowBalanceReport').modal('hide');
                $tbl.find('td').css({
                    fontSize: '12px',
                    padding: '3px'
                });
                setTimeout(function(){
                    self.crystal_report_1({
                        cloned_html: $cloned
                    });
                }, 2000);
            }
        }
    }
</script>
