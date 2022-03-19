<template>
    <div class="row">
        
        <div class="form-group col-md-3">
          <label for="usr">CHOOSE DATE</label>
          <input :disabled="!item_physical.length" v-model="date" type="date" class="form-control form-control-sm">
        </div>
        <div class="form-group col-md-3">
          <label for="usr">Property Custodian</label>
          <input v-model="property_custodian" type="text" class="form-control form-control-sm">
        </div>

        <div class="form-group col-md-3">
          <label for="usr">Regional Director</label>
          <input v-model="regional_director" type="text" class="form-control form-control-sm">
        </div>
        <hr>
        <br>
    </div>
</template>
<script>
    import moment from 'moment'
    import alertify from 'alertify.js'
    import {mapActions, mapMutations, mapState} from 'vuex'
    export default {
        mounted() {
            console.log('Component mounted.');
        },
        data(){
            return {
                date: '',
                property_custodian: '-',
                regional_director: '-'
            }
        },
        methods: {
            ...mapActions([
                'db_search_physical_count_report_items_by_date'
            ]),
            ...mapActions({
                'dbUpdateOrAddPropertyCustodian': 'global_var_module/dbUpdateOrAddPropertyCustodian'
            }),
            ...mapMutations([
                'set_physical_report_date'
            ])
        },
        computed: {
            ...mapState([
                'item_physical',
                'physical_report'
            ]),
            ...mapState({
                head_names: state => state.global_var_module.head_names
            })
        },
        watch: {
            'date': function(newVal, oldVal){
                let self = this;
                self.set_physical_report_date(newVal);
                let formated = moment(newVal).format('MMMM DD, YYYY');
                alertify.success('filter all items up to this date: ' + formated);
                self.db_search_physical_count_report_items_by_date(newVal);
            },
            'property_custodian': function(newVal, oldVal){
                const self = this;
                clearTimeout(self.timer);
                self.timer = setTimeout(() => {
                    self.dbUpdateOrAddPropertyCustodian({
                        name: self.property_custodian,
                        position: 'property_custodian'
                    });
                }, 1000);
            },
            'regional_director': function(newVal, oldVal){
                const self = this;
                clearTimeout(self.timer);
                self.timer = setTimeout(() => {
                    self.dbUpdateOrAddPropertyCustodian({
                        name: self.regional_director,
                        position: 'regional_director'
                    });
                }, 1000);
            },
            'head_names': function(newVal, oldVal){
                const self = this;
                for (let index = 0; index < newVal.length; index++) {
                    const head_name = newVal[index];
                    self[head_name.position] = head_name.name;
                };
            }
        }
    }
</script>
