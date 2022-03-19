
<template>
    <div>
       <div class="modal fade" id="ModalInputQtyForPoAndDrItem" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">ADDITIONAL ITEM FOR P.O AND DELIVERED ITEMS</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
               <!-- {{item}} -->
               <label>Delivery I.D</label>
               dr_id: {{dr_id}}
               <select v-model="dr_id">
                 <option :value="dform.id" v-for="dform in dforms">
                    {{dform.id}}
                 </option>
               </select>
               <table class="table table-hover table-bordered">
                  <thead>
                      <tr>
                          <th>Item name</th>
                          <th width="150">Item Code</th>
                          <th>Qty</th>
                          <th>Unit Cost</th>
                          <th >Total</th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                          <td>{{item.name}}</td>
                          <td>{{item.rca_code}}-{{item.rca_cat}}-{{item.rca_no}}</td>
                          <td>
                              <input style="width: 70px" type="number" @keyup="qtyChanged($event)" @change="qtyChanged($event)">
                          </td>
                          <td>
                              <input style="width: 100px" type="number" @keyup="unitCostChanged($event)"  @change="unitCostChanged($event)">
                          </td>
                          <th class="text-danger">{{ qty * unit_cost | format_number }}</th>
                      </tr>
                  </tbody>
               </table>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button :disabled="whileSavingItems" type="button" class="btn btn-primary" @click="savingChanges">Save changes</button>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<style scoped>
    .modal-lg {
        max-width: 50%;
    }
</style>
<script>
    import {mapState, mapActions, mapMutations} from 'vuex'
    import accounting from 'accounting'
    import alertify from 'alertify.js'
    export default {
        mounted() {
            // console.log('Component mounted.')
        },
        filters: {
            format_number(n){
                if (Number(n) > 0) {
                  return accounting.formatMoney(n, 'Php ', 2)
                }else {
                  return 0;
                }
                
            }
        },
        data(){
            return {
                dr_id: '',
                qty: 0,
                unit_cost: 0.0,
                whileSavingItems: false
            }
        },
        computed: {
            ...mapState({
              'item': 'current_selected_item',
              'dforms': 'local_dforms'
            })
        },
        methods: {
            ...mapActions([
                'db_save_po_item_dr_item'
            ]),
            ...mapMutations([
                'add_local_items',
                'update_one_purchase_order'
            ]),
            savingChanges(){
                let self = this;
                if (Number(self.dr_id)) {
                  self.whileSavingItems = true;
                  self.db_save_po_item_dr_item({
                      qty: self.qty,
                      unit_cost: self.unit_cost,
                      dr_id: self.dr_id
                  }).then((response) => {
                      setTimeout(function(){
                          self.whileSavingItems = false;
                      }, 3000);
                      if (response.status == 200) {
                          alertify.success('Item successfully added!');
                          let json = response.data;
                          let item = json.item
                          self.add_local_items({
                              id: item.id,
                              primary_id: json.purchase_item.id, /** This is the Primary ID: Purchase_item.id **/
                              qty: json.purchase_item.qty,
                              unit_cost: json.purchase_item.unit_cost,
                              remarks: '',
                              requested_qty: 0,
                              name: item.name,
                              unit: item.unit_name,
                          });
                          self.qty = '';
                          self.unit_cost = '';
                          self.dr_id = 0;

                          $('#ModalInputQtyForPoAndDrItem').modal('hide');
                          setTimeout(function(){
                            $('#ModalQuestionPoItemDelivery').modal('hide');
                            setTimeout(function(){
                                $('#modalItemSelection').modal('hide');
                            }, 400);
                          }, 400);    

                          self.update_one_purchase_order(json.purchase_order);
                      }
                      
                  })
                }else {
                    alert('Please select Delivery ID: (dr_ID) to proceed!');
                    alertify.log('Please select Delivery ID: (dr_ID) to proceed!');
                }
            },
            qtyChanged(event){
                let self = this;
                let qty = Number(event.target.value);
                if (qty > 0) {
                    self.qty = qty;
                }else {
                    alert('Invalid input! less than zero ? Do you sure?');
                    self.qty = 0;
                }
            },
            unitCostChanged(event){
                let self = this;
                let unit_cost = Number(event.target.value);
                if (unit_cost > 0) {
                    self.unit_cost = unit_cost;
                }else {
                    alert('Invalid input! less than zero ? Do you sure?');
                    self.unit_cost = 0;
                }
            }
        }
    }
</script>
