<template>
    <div>
        <div class="modal fade" id="ModalPhysicalCountOfInventory" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Physical Count of Inventories Report
                  &nbsp;&nbsp;<span title="print page" class="fa fa-print text-primary fa-2x" style="cursor: pointer" @click="printPhysicalOfInventory"></span>
                </h5>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                 <!-- <h3>test only</h3> -->
                 <FilterSearchPhysicalCountReport />   
                 <!-- Start of Print  --> 
                 <div class="clone-physical-report">           
                   <PhysicalReportHeader />
                   <PhysicalReportOfInventories />
                 </div>    
                 <!-- End of Print  --> 
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                <div id="paste-here"></div>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>
<style scoped>
    .modal-lg {
        max-width: 80% !important;
    }
    @media print{@page {size: landscape}}

</style>
<script>

    import FilterSearchPhysicalCountReport from './FilterSearchPhysicalCountReport'
    import PhysicalReportHeader from './PhysicalReportHeader'
    import PhysicalReportOfInventories from './PhysicalReportOfInventories'
    import {mapActions,mapMutations} from 'vuex'
    import printJS from 'print-js'
    export default {
        mounted() {
            let self = this;
            $('#ModalPhysicalCountOfInventory').on('shown.bs.modal', function (e) {
                self.db_fetch_items_physical_force_all();
                self.dbFetchHeadNames()
                    .then((response) => {
                        // console.log(response);
                    });
            });
            $('#ModalPhysicalCountOfInventory').on('hide.bs.modal', function (e) {
                self.clear_item_physical();
            });
        },
        components: {
            FilterSearchPhysicalCountReport,
            PhysicalReportHeader,
            PhysicalReportOfInventories
        },
        methods: {
            ...mapActions([
                'db_fetch_items_physical_force_all'
            ]),
            ...mapActions({
                'dbFetchHeadNames': 'global_var_module/dbFetchHeadNames'
            }),
            ...mapMutations([
                'clear_item_physical',
                'crystal_report_1'
            ]),
            printPhysicalOfInventory(){
                let self = this;
                let cloned = $('.clone-physical-report').clone()


                $('#ModalPhysicalCountOfInventory').modal('hide');

                setTimeout(function(){
                  $('#app').hide();
                  $('#report-output2').empty();
                  // .appendTo('#report-output2');
                  $('#report-output2').append(cloned)
                    window.print();
                    setTimeout(function(){
                        $('#app').show();
                        $('#report-output2').empty();
                    }, 1000);
                }, 750);
                  
            }
        }
    }
</script>
