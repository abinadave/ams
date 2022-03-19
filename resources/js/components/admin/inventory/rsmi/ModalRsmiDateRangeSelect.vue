<template>
    <div>
        <div class="modal fade" id="ModalRsmiDateRangeSelect" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">RSMI Reporting (Select Range of Date)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                        <form @submit.prevent="submitForm">
                              <div class="form-group fg-1">
                                <label >Start Date</label><small style="font-weight: bolder"> | ({{ form.date1 | format_date }})</small>
                                <input v-model="form.date1" type="date" class="form-control">
                                <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                              </div>
                              <div class="form-group fg-1">
                                <label >End Date </label><small style="font-weight: bolder"> | ({{ form.date2 | format_date }})</small>
                                <input v-model="form.date2" type="date" class="form-control">
                                <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                              </div>
                              <div class="form-group fg-1">
                                <label >Selected dates: {{ getRangeOfDates }}</label>
                              </div>
                              <button :disabled="whileLoading" type="submit" class="btn btn-primary float-right btn-8837">
                                    <span v-if="whileLoading">
                                        Generating Report <i class="fa fa-spinner fa-pulse fa-fw"></i>
                                    </span>
                                    <span v-else>
                                        GENERATE RSMI
                                    </span>
                              </button>
                        </form>
                  </div>
                  <div class="modal-footer">
                    <!-- <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button> -->
                    <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
                  </div>
                </div>
              </div>
            </div>
    </div>
</template>
<style scoped>
    
    .btn-8837 {
        margin-right: 45px;
    }
    .modal-lg {
        max-width: 25% !important;
    }
</style>
<script>
    import moment from 'moment'
    import {mapActions} from 'vuex'
    export default {
        mounted() {
            let self = this;
            $(function() {
                // $('#ModalRsmiDateRangeSelect').on('shown.bs.modal', function(){
                    // self.form.
                // });
            });
        },
        data(){
            return {
                form: {
                    date1: '2019-10-01',
                    date2: '2019-10-31'
                },
                // d1: "2019-02-01"
                // d2: "2019-02-28"
                whileLoading: false
            }
        },
        computed: {
            getRangeOfDates(){
                let self = this;
                let m1, m2 = '';
                m1 = moment(self.form.date1).format('MMM');
                m2 = moment(self.form.date2).format('MMM');
                if (m1 == m2) {
                    return moment(self.form.date1).format('MMM DD ') + ' - ' + moment(self.form.date2).format('DD, YYYY');
                }else {
                    return moment(self.form.date1).format('MMM DD ') + ' - ' + moment(self.form.date2).format('MMM DD, YYYY');
                }
            }
        },
        filters: {
            format_date(d){
                let self = this;
                return moment(d).format('MMM DD, YYYY')
            }
        },
        methods: {
            ...mapActions([
                'db_rsmi_report'
            ]),
            submitForm(){
                let self = this;
                let f1 = moment(self.form.date1).format('MMMM DD, YYYY');
                let f2 = moment(self.form.date2).format('MMMM DD, YYYY');
                let d1 = self.form.date1;
                let d2 = self.form.date2;
                if (f1 == 'Invalid date' || f1 == 'Invalid date') {
                    alert('Please specify correct date, Example: January 01, 2019 - January 30, 2019');
                }else {
                    self.whileLoading = true;
                    self.db_rsmi_report({
                        d1, 
                        d2
                    }).then((response) => {
                        if (response.status == 200) {
                            $('#ModalRsmiDateRangeSelect').modal('hide');

                            setTimeout(function(){
                                $('#ModalRsmiReport').modal('show');
                                self.whileLoading = false;
                            }, 700);
                        }else {
                            alert('There was a problem on Retreiving RSMI Data, please try again.');
                            setTimeout(function(){
                                self.whileLoading = false;
                            }, 3000);
                        }

                    });
                }
            }
        }
    }
</script>
