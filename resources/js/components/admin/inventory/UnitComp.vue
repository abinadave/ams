<template>
    <div style="margin-top: 20px">
       
        <div class="card-body"> 
            <form @submit.prevent="submitForm" class="col-md-4">
                <label>Unit name</label>
                <input v-model="unit_name"  type="text" class="form-control unit-name" autofocus>
                <button :disabled="whileSaving" type="submit" style="margin-top: 10px" class="btn btn-primary">SUBMIT</button>
            </form>
            <br>
            <table class="table" style="font-size: 12px">
              <thead>
                <tr>
                  <th scope="col">Unit ID</th>
                  <th @click="sortUnitsTable" class="links" scope="col">Unit Name <i class="fa fa-sort" aria-hidden="true"></i></th>
                  <th scope="col">Total items</th>
                  <th scope="col">Created by</th>
                  <th colspan="2"></th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="unit in unit_views">
                  <th scope="row">{{ unit.id}}</th>
                  <td>{{ unit.unit_name }}</td>
                  <td>{{ unit.count_items}}</td>
                  <td>{{ unit.name }}</td>
                  <td>
                      <a class="links text-primary" @click="editUnit(unit)"><i class="fa fa-pencil"></i></a>
                  </td>
                  <td>
                      <a  class="links text-danger" @click="removeUnit(unit)"><i class="fa fa-remove"></i></a>
                  </td>
                </tr>
              </tbody>
            </table>
        </div>
    </div>
</template>

<style scoped>
    .links {
        cursor: pointer;
    }
</style>
<script>
    import {mapActions, mapState} from 'vuex'
    import alertify from 'alertify.js'
    export default {
        mounted() {
            let self = this;
            setTimeout(function(){
                self.fetch_unit_views();
            }, 3000);
        },
        data(){
            return {
                unit_name: '',
                whileSaving: false
            }
        },
        methods: {
            ...mapActions([
                'save_unit',
                'fetch_unit_views',
                'remove_unit',
                'edit_unit'
            ]),
            sortUnitsTable(){
                let self = this;
                alert('comming soon please wait..')
            },
            editUnit(unit){
                let self = this;
                var newUnitName = prompt("Please enter new unit name", unit.unit_name);
                if (newUnitName != null) {
                    self.edit_unit({
                        unit: unit,
                        newUnitName: newUnitName
                    }).then((response) => {
                        if (response.status == 200) {
                            alertify.success('Information updated');
                        }
                    })
                }
            },
            removeUnit(unit){
                let self = this;
                let conf = confirm('Are you sure you want to remove this unit from the list?');
                if (conf) {
                    if (unit.count_items == 0) {
                        self.remove_unit(unit.id).then((response) => {
                            alertify.log('Unit deleted: ' + unit.unit_name);
                        });
                    }else {
                        alertify.alert("Can't remove unit because, " + unit.count_items + " items will be affected");
                    }
                }
                
            },
            submitForm(e){
                let self = this;
                self.whileSaving = true;
                self.save_unit(self.unit_name).then((response) => {
                    if (response.status === 200) {
                        alertify.success('Information saved!');
                        setTimeout(function(){
                            self.unit_name = '';
                            $('.unit-name').focus();

                        }, 750);
                    }
                    self.whileSaving = false;
                })
            }
        },
        computed: {
            ...mapState([
                'units',
                'unit_views'
            ])
        }
    }
</script>
