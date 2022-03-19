<template>
       <div id="apr-div" style="padding: 5px; margin-top: -28px">
        <div class="row justify-content-center panel-apr">
            <div class="col-md-12">
                <div class="card card-default">
                    <div class="card-header bg-warning">Procurement Requests</div>

                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">APR List</a>
                          </li>
                          <li  class="nav-item">
                            <a  class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Create A.P.R</a>
                          </li>
                         
                        </ul>
                        <div class="tab-content" id="myTabContent">
                          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <AprList />
                          </div>
                          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <CreateApr />
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ModalSelectItem />
        <CreateDeliveryForApr />
        <ModalPoAprDeliveryList />
    </div>
</template>

<script>
    import CreateApr from './CreateApr'
    import AprList   from './AprList'
    import ModalSelectItem from '../inventory/ModalSelectItem'
    import {mapMutations} from 'vuex'
    import CreateDeliveryForApr from '../delivery/CreateDeliveryForApr'
    import ModalPoAprDeliveryList from '../delivery/ModalPoAprDeliveryList'
    import alertify from 'alertify.js'
    export default {
        mounted() {
            let self = this;
            alertify.log('This module is still ON PROGRESS');
            console.log('Component mounted.');
            self.clear_local_items();
            jQuery(document).ready(function($) {
                $('.panel-apr').find('#home-tab').on('click', function(event) {
                    self.set_updating_apr(false);
                });
            });
        },
        methods: {
            ...mapMutations([
                'clear_local_items',
                'set_updating_apr'
            ])
        },
        components: {
            CreateApr,
            AprList,
            ModalSelectItem,
            CreateDeliveryForApr,
            ModalPoAprDeliveryList
        }
    }
</script>
