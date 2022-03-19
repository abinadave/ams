<template>
    <div style="margin: 30px" class="row">
       
       <div class="col-md-4">
           <form @submit.prevent="SaveSupplier">
              <div class="form-group">
                <label>Supplier Name</label> <b style="font-size: 15px" class="text-danger">(required)*</b>
                <input v-model="form.name" type="text" class="form-control supplier-name" placeholder="Enter supplier name">
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
              </div>
              <div class="form-group">
                <label>Supplier Address</label><b class="text-primary" style="font-size: 15px">(Optional)*</b>
                <input v-model="form.address" type="text" class="form-control" placeholder="Address">
              </div>
              <div class="form-group">
                <label>Supplier Contact Number</label><b class="text-primary" style="font-size: 15px">(Optional)*</b>
                <input v-model="form.contact_no" type="text" class="form-control" placeholder="Contact Number">
              </div>
              <button type="submit" class="btn btn-primary">Submit</button>
            </form>
       </div>

        <div class="col-md-8" style="font-size: 12px">
            <table class="table">
              <thead class="thead-dark">
                <tr>
                  <th scope="col">Supplier name</th>
                  <th scope="col">Address</th>
                  <th scope="col">Contact Number</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="sup in suppliers">
                  <th scope="row">{{ sup.name }}</th>
                  <td>{{ sup.address }}</td>
                  <td>{{ sup.contact_no }}</td>
                </tr>
              </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    import {mapActions, mapState} from 'vuex'
    import alertify from 'alertify.js'
    export default {
        mounted() {
            let self = this;
            setTimeout(function(){
                self.fetch_suppliers();
            }, 1000);
        },
        data(){
            return {
                form: {
                    name: '',
                    address: '',
                    contact_no: ''
                }
            }
        },
        methods: {
            ...mapActions([
                'save_supplier',
                'fetch_suppliers'
            ]),
            SaveSupplier(){
                let self = this;
                self.save_supplier(self.form).then((response) => {
                    if (response.status == 422) {
                        // console.log()
                        alertify.alert('Error: ' + response.data.errors.name[0]);
                    }else if(response.status == 200) {
                        self.form.name = '';
                        self.form.address = '';
                        self.form.contact_no = '';
                        alertify.success('Supplier successfully added from the list');
                        setTimeout(function(){
                            $(".supplier-name").focus();
                        }, 800);
                    }
                })
            }
        },
        computed: {
            ...mapState([
                'suppliers'
            ])
        }
    }
</script>
