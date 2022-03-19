<template>
    <div>
        <div class="modal fade" id="ModalQuestionPoItemDelivery" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
          <div class="modal-dialog" role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Dialog Box</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                    Do you want to add Item to existing P.O with Delivery ?
              </div>
              <div class="modal-footer">
                <button :disabled="whileAddingPoOnly" @click="addPurchaseItem()" type="button" class="btn btn-danger">
                    <span v-if="whileAddingPoOnly">
                        Adding P.O <i class="fa fa-spinner fa-pulse fa-fw"></i>
                    </span>
                    <span v-else>
                        Add P.O
                    </span>
                </button>
                <button v-show="local_dforms.length" :disabled="whileAddingPoAndDr" @click="addPOandDeliveryItem()" type="button" class="btn btn-primary">
                    <span v-if="whileAddingPoAndDr">
                        Adding P.O & Delivery <i class="fa fa-spinner fa-pulse fa-fw"></i>
                    </span>
                    <span v-else>
                        P.O & Delivery
                    </span>
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    import {mapActions, mapState, mapMutations} from 'vuex'
    export default {
        mounted() {
            // console.log('Component mounted.')
        },
        data(){
            return {
                whileAddingPoOnly: false,
                whileAddingPoAndDr: false
            }
        },
        computed: {
            ...mapState([
                'local_dforms'
            ])
        },
        methods: {
            ...mapActions([
                'db_add_one_purchase_item'
            ]),
            ...mapMutations([
                'add_local_items',
                'set_purchase_order_delivery_completed'
            ]),
            addPurchaseItem(){
                let self = this;
                // console.log('adding P.O item only')
                self.whileAddingPoOnly = true;
                self.db_add_one_purchase_item().then((resp) => {
                    // console.log(response);
                    if (resp.status == 200) {
                        let json = resp.data;
                        // alert('done adding: db_add_one_purchase_item');
                        self.set_purchase_order_delivery_completed({
                            po_id: json.po_id,
                            val: 0
                        });
                        self.add_local_items({
                          id: json.item_id,
                          primary_id: json.id,
                          qty: 0,
                          unit_cost: 0,
                          remarks: '',
                          requested_qty: 0,
                          name: json.name,
                          unit: json.unit_name,
                        });
                        setTimeout(function(){
                            self.whileAddingPoOnly = false;
                            setTimeout(function(){
                                $('#ModalQuestionPoItemDelivery').modal('hide');
                            }, 150);
                        }, 1000);
                    }
                })
            },
            addPOandDeliveryItem(){
                let self = this;
                self.whileAddingPoAndDr = true;
                // console.log('adding P.O and Delivery Items');
                setTimeout(() => self.whileAddingPoAndDr = false , 2000);
                $('#ModalInputQtyForPoAndDrItem').modal('show');
            }
        }
    }
</script>
