<template>
    <div style="padding: 5px; margin-top: -28px">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header">Requisition</div>

                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                          <li @click="RISListclick" class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">List of Requisitions</a>
                          </li>
                          <li  class="nav-item">
                            <a @click="createRequisitionClick" class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Create Requisition</a>
                          </li>
                         
                        </ul>
                        <div class="tab-content" id="myTabContent">
                          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                              <RequisitionList />
                          </div>
                          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                              <CreateRequisition />
                              <RequisitionItems />
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ModalSelectItem />
        <ModalShowRequisitions />
    </div>
</template>

<script>
    import CreateRequisition from './CreateRequisition'
    import ModalSelectItem from '../inventory/ModalSelectItem'
    import RequisitionItems from './RequisitionItems'
    import RequisitionList from './RequisitionList'
    import ModalShowRequisitions from './ModalShowRequisitions'
    import {mapActions, mapMutations} from 'vuex'
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        components: {
            CreateRequisition,
            ModalSelectItem,
            RequisitionItems,
            RequisitionList,
            ModalShowRequisitions
        },
        methods: {
            ...mapActions([
                'get_ris_last_insert_id'
            ]),
            ...mapMutations([
                'set_ris_updating',
                'set_ris_max_number',
                'clear_local_items'
            ]),
            createRequisitionClick(){
                let self = this;
                self.get_ris_last_insert_id();
                self.set_ris_updating(false);
            },
            RISListclick(){
                let self = this;
                self.set_ris_updating(false);
                self.clear_local_items();
                self.set_ris_max_number({
                    max_ris_number: ''
                });
            }
        }
    }
</script>
