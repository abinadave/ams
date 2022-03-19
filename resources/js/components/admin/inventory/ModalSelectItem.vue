<template>
    <div>
        <div class="modal fade" id="modalItemSelection" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
          <div class="modal-dialog modal-lg" role="document" style="max-width: 70% !important;">
            <div class="modal-content">
              <div class="modal-header">
                <div>
                    <input style="display: inline-block; width: 300px" v-model="search" placeholder="Search item/material/equipment here" type="text" class="form-control form-control-sm search-box">
                    <i v-show="while_searching_items" style="display: inline-block" class="fa fa-spinner fa-pulse fa-spin fa-fw"></i>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                    <div>
                        <SelectItemList />
                    </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
    </div>
</template>

<script>
    import {mapState, mapActions, mapMutations} from 'vuex'
    import SelectItemList from './SelectItemList'
    export default {
        mounted() {
            // console.log('Component mounted.')
        },
        components: {
            SelectItemList
        },
        data(){
            return {
                search: '',
            }
        },
        methods: {
            ...mapActions([
                'search_for_items',
                'db_find_stock_card_balances'
            ]),
            ...mapMutations([
                'searching_items'
            ]),
            getBalances(){
                let self = this;
                let ids = _.map(self.items, 'id');
                // console.log(ids);
                self.db_find_stock_card_balances(ids).then((response) => {
                    if (response.status == 200) {
                        let json = response.data;
                        // console.log(self.actual_stocks)
                    }
                });
            }
        },
        computed: {
            ...mapState([
                'items',
                'while_searching_items',
                'actual_stocks'
            ])
        },
        watch: {
            'search': function(newVal, oldVal){
                let self = this;
                self.searching_items();
                clearTimeout(self.timer);
                self.timer = setTimeout(function(){
                    self.search_for_items(self.search).then((response) => {
                        console.log('done searching....');
                        self.getBalances();
                    })
                }, 1000);
            }
        } 
    }
</script>
