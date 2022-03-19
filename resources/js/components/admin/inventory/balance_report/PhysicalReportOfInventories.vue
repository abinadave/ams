<template>
    <div>
        <table class="table table-bordered " id="tbl-physical-report-inventories">
            <thead>
                <tr>
                    <th class="text-center" rowspan="2" width="200">Article</th>
                    <th class="text-center" rowspan="2" width="300">Description</th>
                    <th class="text-center" width="130" rowspan="2">Stock Number</th>
                    <th class="text-center" width="80" rowspan="2">Unit of Measure</th>
                    <th class="text-right" width="90" rowspan="2">Unit Value</th>
                    <th class="text-right" width="70">Balance Per Card</th>
                    <th class="text-center" width="70">On Hand Per Count</th>
                    <th class="text-center" colspan="2">Shortage/Overage</th>
                    <th class="text-center" rowspan="2">Remarks</th>
                </tr>
                <tr class="rowspan-phys-report">
                    <th class="text-center">(Quantity)</th>
                    <th class="text-center">(Quantity)</th>
                    <th class="text-center" width="100">(Quantity)</th>
                    <th class="text-center">Value</th>
                    <!-- <th>qty</th> -->
                </tr>
            </thead>
            <tbody>
                <tr :key="physical.id" v-for="physical in physical_report">
                    <td>{{ getName(physical) }}</td>
                    <td>{{ getDescription(physical) }}</td>
                    <td class="text-center">{{ getStockNo(physical) }}</td>
                    <td class="text-center">{{ getUnitName(physical) }}</td>
                    <td class="text-right">{{ physical.unit_cost | format_number }}</td>
                    <td class="text-right">
                        {{ physical.actual_stock }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr> 
            </tbody>
            <tfoot>
                <tr style="font-size: 16px">
                                <td colspan="2"  class="text-center">
                                    <span class="float-left" style="font-size: 12px;">
                                        I hereby certify to the correctness of the above
                                    </span><br>
                                    <span class="float-left marginTop" style="font-size: 12px">information.</span>
                                    <br>
                                    <b style="margin-top: -50px; font-size: 14px">
                                        {{getHeadName('property_custodian')}}
                                    </b><br>
                                    <span style="font-size: 12px; margin-top: -50px" class="text-center">
                                        Signature over printer name of Supply and/or
                                    </span>
                                    <br>
                                    <span style="font-size: 12px; margin-top: -50px">
                                        Property Custodian
                                    </span>
                                </td>
                                <td colspan="4" class="text-center" style="border-left-color: white">
                                    <span class="float-left" style="font-size: 12px;">
                                        RECOMMENDING APPROVAL
                                    </span><br>
                                    <!-- <span class="float-left marginTop" style="font-size: 12px">information.</span> -->
                                    <br>
                                    
                                    <br>
                                    <span style="font-size: 12px; margin-top: -50px">
                                        <!-- Property Custodian -->
                                    </span>
                                </td>
                                <td style="padding: 2px" colspan="5">
                                    <span class="float-left" style="font-size: 12px;">
                                        APPROVED BY
                                    </span><br>
                                    <!-- <span class="float-left marginTop" style="font-size: 12px">information.</span> -->
                                    <br>
                                    <b style="margin-top: -50px; font-size: 15px">{{getHeadName('regional_director')}}</b><br>
                                    <span style="font-size: 12px; margin-top: -50px" class="text-center">
                                        REGIONAL DIRECTOR, DILG8
                                    </span>
                                    <br>
                                    <span style="font-size: 12px; margin-top: -50px">
                                        <!-- Property Custodian -->
                                    </span>
                                </td>
                            </tr>
            </tfoot>
        </table>

    </div>
</template>
<style scoped>

    #tbl-physical-report-inventories td {
        padding: 2px;
        border: 2px solid black !important;
        font-size: 12px;
        font-weight: bolder;
    }

    #tbl-physical-report-inventories th {
        padding: 2px;
        border: 2px solid black !important;
        font-size: 12px;
        font-weight: bolder;
    }
    .rowspan-phys-report th {
        font-size: 8px;
    }
</style>
<script>
    import accounting from 'accounting'
    import {mapState} from 'vuex'
    export default {
        mounted() {
            
        },
        filters: {
            format_number(n){
                if (n == "N/A") {
                    return "N/A";
                }else {
                    return accounting.formatNumber(n ,2);
                }
            }
        },
        methods: {
            getHeadName(position){
                const self = this;
                let rs = _.filter(self.head_names, {position: position});
                if(rs.length){
                    let head = _.first(rs);
                    return head.name;
                }
            },
            getDescription(physical){
                let self = this;
                let item = _.find(self.item_physical, {id: Number(physical.item_id)});
                return item.description;
            },
            getName(physical){
                let self = this;
                let rsItems = _.filter(self.item_physical, {id: Number(physical.item_id)});
                if (rsItems.length) {
                    let item = _.first(rsItems);
                    return item.name;
                }else {
                    return 'item not found';
                }
                
            },
            getStockNo(physical){
                let self = this;
                let item = _.find(self.item_physical, {id: Number(physical.item_id)});
                let stock_no = item.rca_code + '-' + item.rca_cat + '-' + item.rca_no;
                return stock_no;
            },
            getUnitName(physical){
                let self = this;
                let item = _.find(self.item_physical, {id: Number(physical.item_id)});
                return item.unit_name;
            }
        },
        computed: {
            ...mapState([
                'physical_report',
                'item_physical'
            ]),
            ...mapState({
                head_names: state => state.global_var_module.head_names
            })
        }
    }
</script>
