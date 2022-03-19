<template>
    <div style="padding: 0px; margin-top: -29px">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default ">
                    <div class="card-header bg-danger" style="color: white; font-weight: bolder">Manage Purchase Orders</div>

                     <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                          <li class="nav-item">
                            <a @click="purchaseOrderListClicked" class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Purchase Orders</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Create</a>
                          </li>
                          <li class="nav-item">
                            <a class="nav-link" id="supplier-tab" data-toggle="tab" href="#supplier" role="tab" aria-controls="supplier" aria-selected="false">Suppliers</a>
                          </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                              <PurchaseOrderList />
                          </div>
                          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                             <CreatePurchaseOrder class="col-9" />
                          </div>
                          <div class="tab-pane fade" id="supplier" role="tabpanel" aria-labelledby="supplier-tab">
                             <Supplier />
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <ModalSelectItem />
        <ModalEditPo />
        <ModalPoAprDeliveryList />
        <ModalDeliverItems />
        <ModalQuestionPoItemDelivery />
        <ModalInputQtyForPoAndDrItem />
    </div>
</template>

<script>
    import ModalDeliverItems from '../delivery/ModalDeliverItems'
    import CreatePurchaseOrder from './CreatePurchaseOrder'
    import Supplier from '../supplier/Supplier'
    import ModalSelectItem from '../inventory/ModalSelectItem'
    import PurchaseOrderList from './PurchaseOrderList'
    import ModalEditPo from './ModalEditPo'
    import ModalPoAprDeliveryList from '../delivery/ModalPoAprDeliveryList'
    import ModalQuestionPoItemDelivery from './po_items/ModalQuestionPoItemDelivery'
    import ModalInputQtyForPoAndDrItem from './po_items/ModalInputQtyForPoAndDrItem'
    import {mapState, mapMutations} from 'vuex'
    export default {
        mounted() {
            
        },
        components: {
            CreatePurchaseOrder,
            Supplier,
            ModalSelectItem,
            PurchaseOrderList,
            ModalEditPo,
            ModalPoAprDeliveryList,
            ModalDeliverItems,
            ModalQuestionPoItemDelivery,
            ModalInputQtyForPoAndDrItem
        },
        computed: {
            
        },
        methods: {
            ...mapMutations([
                'purchase_order_updating',
                'clear_local_items'
            ]),
            purchaseOrderListClicked(){
                let self = this;
                self.purchase_order_updating(false);
                self.clear_local_items();
            }
        }
    }
</script>
