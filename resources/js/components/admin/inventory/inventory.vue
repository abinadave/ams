<template>
    <div style="padding: 5px; margin-top: -28px">
        <div class="row justify-content-center" >
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-default" style="color:black; font-weight: bolder">
                      <i class="text-primary">Manage Inventories (DILG Regional Office 08)</i>
                      <a class="float-right" style="font-size: 12px; cursor: pointer; margin-left: 30px" @click="rsmBtnClicked"  >
                          <i class="fa fa-bar-chart" aria-hidden="true"></i> RSMI 
                      </a>
                      <a class="float-right" style="font-size: 12px; cursor: pointer" @click="inventoryReportClicked"  >
                          <i class="fa fa-bar-chart" aria-hidden="true"></i> Inventory Report &nbsp;&nbsp;&nbsp;&nbsp;|
                      </a>&nbsp;&nbsp;&nbsp;&nbsp;
                      <a class="float-right" style="font-size: 12px; cursor: pointer; margin-right: 20px" @click="PhysicalReportClicked"  >
                          <i class="fa fa-bar-chart" aria-hidden="true"></i> Physical Count of Inventory &nbsp;&nbsp;&nbsp;&nbsp;|
                      </a>
                    </div>

                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                          <li @click="itemListClicked" class="nav-item li-item-list">
                            
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Item list</a>
                            
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Create Item</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="units-tab" data-toggle="tab" href="#units" role="tab" aria-controls="units" aria-selected="false">Unit(s)</a>
                          </li>

                        </ul>
                        <div class="tab-content" id="myTabContent">
                          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                              
                              <!-- <div v-if="no_item_was_found">
                                 <div style="margin-top: 50px" class="alert alert-dark col-md-4" role="alert">
                                      No record was found for now.
                                  </div>
                              </div>
                              <div v-else> -->
                                <ItemList />
                              <!-- </div> -->
                          </div>
                          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                              <AddItem class="col-md-8" />
                          </div>
                          <div class="tab-pane fade" id="units" role="tabpanel" aria-labelledby="units-tab">
                              <UnitComp class="col-md-8" />
                          </div>
                         
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ModalRsmiDateRangeSelect />
        <ModalRsmiReport />
        <ModalShowBalanceReport />
        <ModalPhysicalCountOfInventory />
    </div>
</template>

<script>
    import ItemList from './ItemList'
    import AddItem from './AddItem'
    import UnitComp from './UnitComp'
    import {mapActions, mapState, mapMutations} from 'vuex'
    import ModalRsmiDateRangeSelect from './rsmi/ModalRsmiDateRangeSelect'
    import ModalRsmiReport from './rsmi/ModalRsmiReport'
    import ModalShowBalanceReport from './balance_report/ModalShowBalanceReport'
    import ModalPhysicalCountOfInventory from './balance_report/ModalPhysicalCountOfInventory'
    export default {
        mounted() {
            let self = this;
            self.fetch_units();
            self.fetch_categories();
            self.searching_items();
            setTimeout(function(){
                self.fetch_items().then((response) => {
                    if (response.status == 200) {
                        let json = response.data;
                        if (!json.length) {
                            self.set_no_item_was_found(true);
                        }else {
                            self.set_no_item_was_found(false);
                        }
                        let ids = _.map(self.items, 'id');
                        self.db_find_stock_card_balances(ids);
                    }
                })
            }, 2000);
            $('#units-tab').click(function(){
                setTimeout(function(){
                     $('.unit-name').focus();
                }, 700);
            });
        },
        data(){
            return {
                // NO_DATA_WAS_FOUND: false
            }
        },
        methods: {
            ...mapActions([
                'fetch_items',
                'fetch_units',
                'fetch_categories',
                'db_fetch_all_inventory_balance',
                'db_find_stock_card_balances',
                'db_fetch_all_items_force'
            ]),
            ...mapMutations([
                'searching_items',
                'set_no_item_was_found',
                'set_updating_item'
            ]),
            PhysicalReportClicked(){
                let self = this;
                $('#ModalPhysicalCountOfInventory').modal('show')
            },
            inventoryReportClicked(){
                let self = this;
                self.db_fetch_all_items_force().then(() => {
                    self.db_fetch_all_inventory_balance().then((response) => {
                        // if (response.status == 200) {
                            // let json = response.data;
                            $('#ModalShowBalanceReport').modal('show');
                        // }
                    })
                })
                
            },
            rsmBtnClicked(){
                let self = this;
                $('#ModalRsmiDateRangeSelect').modal('show');
            },
            itemListClicked(){
                let self = this;
                self.set_updating_item(false);
            }
        },
        computed: {
            ...mapState([
                'no_item_was_found',
                'items'
            ])
        },
        components: {
            ItemList,
            AddItem,
            UnitComp,
            ModalRsmiDateRangeSelect,
            ModalRsmiReport,
            ModalShowBalanceReport,
            ModalPhysicalCountOfInventory
        }
    }
</script>
