<template>
    <div>
       <div class="modal fade" id="ModalRsmiReport" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">REPORT OF SUPPLIES AND MATERIALS ISSUED, 
                    {{ getRsmiDates }}, &nbsp;&nbsp;&nbsp;&nbsp; <a @click="printRsmi" style="cursor: pointer" class="text-right fa fa-print fa-2x text-primary"> </a>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body print-rsmi" id="all-data">
                    
                <div v-for="(chunk, index) in chunkedRsmi"> <!-- START OF FOR LOOP -->

                <div class="text-center">
                    <img src="/app/dilg-logo.png" class="dilg-logo">
                    <h4>REPORT OF SUPPLIES AND MATERIALS ISSUED</h4>
                            <br>
                    <h5>DEPARTMENT OF THE INTERIOR AND LOCAL GOVERNMENT</h5>
                    <i>Regional Office No. 8</i>
                    <br>
                    <i>Kanhuraw Hill, Tacloban City</i><br><br>
                    <p class="float-right">Date: {{ getRsmiDates }}</p>
                    <p class="float-left">Fund Cluster: <b>01</b></p>
                </div>

                    <table class="table table-bordered table-condensed tbl-rsmi-report">
                        <thead >
                            <tr>
                                <th colspan="6" class="align-middle"><i>To be filled up by the Supply and/or Property Division/Unit</i></th>
                                <th colspan="2"><i>To be filled up by the</i> <br> <i>Accounting Division/Unit</i></th>
                            </tr>
                            <tr style="font-size: 10px">
                                <th class="bordered align-middle" width="130">R.I.S No.</th>
                                <th class="bordered align-middle" width="200">Reponsibility Code</th>
                                <th class="bordered align-middle" width="150">Stock No.</th>
                                <th class="text-left bordered align-middle" width="320">Item</th>
                                <th class="bordered align-middle" width="100">Unit</th>
                                <th class="bordered align-middle" width="90">Quantity Issued</th>
                                <th class="bordered align-middle" width="100">Unit Cost</th>
                                <th class="bordered align-middle" width="90">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="request_item in chunk">
                                <td class="text-center">{{ request_item.ris_year }}-{{ request_item.ris_month }}-{{ request_item.ris_number }}</td>
                                <td class="text-center">{{ request_item.division }} - {{ request_item.office}}</td>
                                <td class="text-center">{{ request_item.rca_code }}-{{ request_item.rca_cat }}-{{ request_item.rca_no }}</td>
                                <th class="text-left">{{ request_item.name }}</th>
                                <td class="text-center">{{ request_item.unit_name }}</td>
                                <td class="text-center">{{ request_item.requested_qty }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="tr-extra" v-for="i in extra_rows - chunk.length">
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                            </tr>
                            <tr class="tr-extra">
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td id="totalRsmi" style="color: black !important">{{ getTotalOfChunks(chunk) }}</td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                                <td><span style="opacity: 0.0 !important">-</span></td>
                            </tr>
                        </tbody>
                        <tbody>
                            <tr>
                                <td class="bordered align-middle"></td>
                                <td class="bordered align-middle" colspan="2" style="font-size: 10px"><b>Recapitulation:</b></td>
                                <td class="bordered align-middle"></td>
                                <td class="bordered align-middle"></td>
                                <td colspan="3" class="bordered align-middle" style="font-size: 10px"><b>Recapitulation:</b></td>
                            </tr>
                            <tr>
                                <td class="bordered align-middle"></td>
                                <td class="bordered align-middle" style="font-size: 10px"><b>Stock No.</b></td>
                                <td class="bordered align-middle" style="font-size: 10px"><b>Quantity</b></td>
                                <td class="bordered align-middle" style="font-size: 10px"><b>Item Name</b></td>
                                <td class="bordered align-middle" style="font-size: 10px"><b>Unit Name</b></td>
                                <td class="bordered align-middle"></td>
                                <td class="bordered align-middle"></td>
                                <td class="bordered align-middle"></td>
                            </tr>
                            <tr v-for="uniq in filterUniqChunked(chunk)">
                                <td></td>
                                <td class="text-center">{{ uniq.rca_code }}-{{ uniq.rca_cat }}-{{ uniq.rca_no }}</td>
                                <td class="text-center">{{ uniq.new_qty }}</td>
                                <td>{{ uniq.name }}</td>
                                <td class="text-center">{{ uniq.unit_name }}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr style="font-size: 10px">
                                <td colspan="3"  class="text-center">
                                    <span class="float-left" style="font-size: 10px;">
                                        I hereby certify to the correctness of the above
                                    </span><br>
                                    <span class="float-left marginTop" style="font-size: 10px">information.</span>
                                    <br>
                                    <b style="margin-top: -50px">{{getHeadName('property_custodian')}}</b><br>
                                    <span style="font-size: 10px; margin-top: -50px" class="text-center">
                                        Signature over printer name of Supply and/or
                                    </span>
                                    <br>
                                    <span style="font-size: 10px; margin-top: -50px">
                                        Property Custodian
                                    </span>
                                </td>
                                <td colspan="2" class="text-center" style="border-left-color: white">
                                    <!-- <span class="float-left" style="font-size: 12px">
                                        NOTED BY:
                                    </span>
                                    <br>
                                    <br>
                                    <b>ATTY. FERNANDO B. ALONZO</b><br>
                                    <span style="font-size: 12px; margin-top: -50px" class="text-center">
                                        LGOO V / OIC FAD
                                    </span> -->
                                </td>
                                <td style="padding: 2px" colspan="3">
                                    <span class="float-left" style="font-size: 10px;">
                                        Posted By:
                                    </span>
                                    <br>
                                    <span style="font-size: 9px">
                                        Signature over Printed
                                    </span>
                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                    &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                                    
                                    <span style="font-size: 14px">Date</span>
                                    <br>  
                                    <span style="font-size: 9px">
                                        Name of Designated 
                                    </span>
                                    <br>
                                    <span style="font-size: 9px">
                                        Accounting Staff
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p style="font-size: 12px; font-style: italic;" class="float-left">Page {{ index + 1 }} of {{ chunkedRsmi.length }}</p>
                    <div style="page-break-after: always"></div>


                </div> <!-- END OF FOR LOOP -->

              </div>
              <div class="modal-footer">
                  <!-- head_names:{{head_names.length}} -->
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button @click="printRsmi" type="button" class="btn btn-primary">Print <i class="fa fa-print"></i></button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>
<style scoped>
   
    .marginTop {
        margin-top: -10px;
    }
    .modal-lg {
        max-width: 60% !important;
    }
    .tbl-rsmi-report {
        font-size: 12px;

    }
    .tbl-rsmi-report th {
        text-align: center;
        padding: 1px;
        border-style: solid;
        border-width: 1px;
        border-color: black;
    }
    .tbl-rsmi-report td {
        padding: 1px;
        border-style: solid;
        border-width: 1px;
        border-color: black;
    }
    .tr-extra td {  
        color: white !important;
    }
    #totalRsmi {
        color: black;
        text-align: center;
        font-weight: bolder;
    }
    .bordered {
        border-style: solid;
        border-width: 1px;
        border-color: black;
        text-align: center;
    }
    .dilg-logo {
        width: 80px !important;
        text-align: left;
        float: left;
    }

    table {
      border-collapse: collapse !important;
    }

    table, th, td {
      border: 1px solid black !important;
    }
</style>
<script>
    import {mapGetters, mapState, mapMutations, mapActions } from 'vuex'
    export default {
        mounted() {
            const self = this;
            $('#ModalRsmiReport').on('shown.bs.modal', function (e) {
                self.dbFetchHeadNames()
                    .then((response) => {
                       
                    });
            });
        },
        data(){
            return {
                extra_rows: 20
            }
        },
        methods: {
            ...mapMutations([
              'crystal_report_1'
            ]),
            ...mapActions({
                'dbFetchHeadNames': 'global_var_module/dbFetchHeadNames'
            }),
            printRsmi(){
                let self = this;
                $('#ModalRsmiReport').modal('hide');
                // screen.lockOrientation('portrait');
                setTimeout(function(){
                    self.crystal_report_1({
                        cloned_html: $('#all-data').clone()
                    });
                }, 1000)
                
            },
            filterUniqChunked(chunks){
                let self = this;
                let uniq_items = [], uniq_item = {};
                let rsUniqueItems = [], qty = 0;
                let rsDuplicateItems = [], new_qty = 0, duplicate_item = {};
                chunks.forEach(function(chunk){
                    qty = 0;
                    new_qty = 0;
                    rsUniqueItems = _.filter(uniq_items, {item_id: Number(chunk.item_id)});
                    // console.log(uniq_items.length)
                    if (rsUniqueItems.length) {
                        // FOUND
                        // update goes here
                        uniq_item = _.first(uniq_items, {item_id: Number(chunk.item_id)});
                        qty = uniq_item.requested_qty + chunk.requested_qty;
                        
                        // rsDuplicateItems = _.filter(chunks, { item_id: Number(chunk.item_id)});
                        
                        // console.log('This item already exist: ' + chunk.name + ', qty: ' + chunk.requested_qty);
                        // console.log(rsDuplicateItems);
                        // for (var i = rsDuplicateItems.length - 1; i >= 0; i--) {
                        //     duplicate_item = rsDuplicateItems[i];
                        //     // console.log(duplicate_item.name);
                        //     new_qty += duplicate_item.requested_qty;
                        // }
                        // uniq_item.new_qty = new_qty;
                    }else {
                        // NOT FOUND
                        // dont touch this
                        uniq_item = chunk;
                        rsDuplicateItems = _.filter(chunks, { item_id: Number(chunk.item_id)});
                        if (rsDuplicateItems.length == 1) {
                            uniq_item.new_qty = uniq_item.requested_qty;
                        }else {
                            new_qty = 0;
                            for (var i = rsDuplicateItems.length - 1; i >= 0; i--) {
                                duplicate_item = rsDuplicateItems[i];
                                // console.log(duplicate_item.name);
                                new_qty += duplicate_item.requested_qty;
                            }
                            uniq_item.new_qty = new_qty;
                        }
                        
                        uniq_items.unshift(uniq_item);
                    }
                });
                console.log('-------------------------------------------------------------------------')
                return _.orderBy(uniq_items, [
                  function (item) { return item.rca_cat; },
                  function (item) { return item.rca_no; }
                ], ["asc", "asc"]);
            }
        },
        computed: {
            /* Getters [] and {} */
                ...mapGetters([
                    'getRsmiDates',
                    'getTotalOfChunks',
                    'chunkedRsmi'
                ]),
                ...mapGetters({
                    'getHeadName': 'global_var_module/getHeadName'
                }),
            /* Getters  [] and {} */

            /* States [] and {} */
                ...mapState([
                    'rsmi'
                ]),
                ...mapState({
                    head_names: state => state.global_var_module.head_names
                })
            /* States [] and {} */
        }
    }
</script>
