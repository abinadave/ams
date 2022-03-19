<template>
    <div style="padding: 5px; margin-top: -28px">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card card-default ">
                    <div class="card-header bg-primary" style="color: white; font-weight: bolder">Manage Deliveries</div>

                     <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                          <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Deliveries</a>
                          </li>
                          <li class="nav-item">
                            <!-- <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Create</a> -->
                          </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                          <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                              <DeliveryList />
                          </div>
                          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                             <CreateDelivery class="col-10" />
                          </div>
                          <div class="tab-pane fade" id="supplier" role="tabpanel" aria-labelledby="supplier-tab">
                             <!-- <Supplier /> -->
                          </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <PurchaseOrderSelection />
        <ModalDeliverItems />
    </div>
</template>

<script>
    import CreateDelivery from './CreateDelivery'
    import PurchaseOrderSelection from '../purchase-order/PurchaseOrderSelection'
    import DeliveryList from './DeliveryList'
    import ModalDeliverItems from './ModalDeliverItems'
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        components: {
            CreateDelivery,
            PurchaseOrderSelection,
            DeliveryList,
            ModalDeliverItems
        }
    }
</script>
