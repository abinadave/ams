<template>
    <div>
        <input type="text" v-model="search" class="form-control search" placeholder="Search here.." style="display: inline">
        <button class="btn btn-sm control-form-sm btn-info" @click="deliverAprForm">Deliver</button>
        <i v-show="whileSearching" style="display: inline">Searching <i class="fa fa-spinner fa-pulse fa-fw"></i></i>
            <table class="table table-condensed table-bordered tbl-apr-forms table-hover">
              <thead class="thead-light">
                <tr>
                  <th colspan="2" width="50"></th>
                  <th scope="col">APR NO</th>
                  <th scope="col">APR DATE</th>
                  <th scope="col">DATE ENCODED (System)</th>
                  <th scope="col">ENCODER</th>
                  <th scope="col" width="50">NO OF ITEMS</th>
                  <th scope="col" width="50">Deliveries</th>
                  <th class="text-center" width="100">Delivery Complete?</th>
                  <th scope="col">ITEMS</th>
                  <th scope="col" class="text-right">TOTAL AMOUNT</th>
                </tr>
              </thead>
              <tbody>
                <tr @click="setCurrentAprForm(apr_form)"  v-for="apr_form in apr_form_views">
                  <td width="60" class="text-center">
                      <i style="cursor: pointer" @click="showModalAprItems(apr_form)" class="text-primary fa fa-book fa-fw" aria-hidden="true"></i>
                      {{ apr_form.count_items }}
                  </td>
                  <td width="10">
                      <i style="cursor: pointer" @click="editAprFormAndItems(apr_form)" class="text-danger fa fa-pencil fa-fw" aria-hidden="true"></i>
                  </td>
                  <th scope="row">{{ apr_form.apr_no }}</th>
                  <td>{{ apr_form.apr_date | format_date}}</td>
                  <td>{{ apr_form.created_at | date_encoded_date }}</td>
                  <td>{{ apr_form.name}}</td>
                  <td>
                      {{ apr_form.count_items }}
                  </td>
                  <td class="text-center text-primary">
                    <i ></i>
                    <i @click="showAprDeliveries(apr_form)" style="cursor: pointer" v-if="apr_form.count_items > 0">
                        {{ apr_form.count_deliveries }}
                    </i>
                    <i v-else>
                        {{ apr_form.count_deliveries }}
                    </i>
                  </td>
                  <td class="text-center" width="50">
                    <b class="fa fa-check text-primary" v-if="apr_form.delivery_completed == 1" style="cursor: pointer">
                        <!-- yes -->
                    </b>
                    <b class="text-danger" v-else>
                        No
                    </b>
                  </td>
                  <td>
                    <span v-if="apr_form.items.length > 130">
                        <a :title="apr_form.items" style="cursor: pointer;" class="text-primary">{{apr_form.count_items}} items</a>
                    </span>
                    <span v-else>
                        {{ apr_form.items }}
                    </span>
                  </td>
                  <td class="text-right">{{ apr_form.total_amount | format_money }}</td>
                </tr>
              </tbody>
            </table>
            <ModalShowAprItems />
    </div>
</template>
<style>
    .tbl-apr-forms th {
        font-size: 12px;
        padding: 6px;
    }
    .tbl-apr-forms td {
        font-size: 12px;
        padding: 6px;
    }
    .search {
        border-radius: 15px;
        width: 300px;
        margin: 10px;
    }
</style>
<script>
	import {mapActions, mapMutations, mapState} from 'vuex'
  import moment from 'moment'
  import accounting from 'accounting'
  import ModalShowAprItems from './ModalShowAprItems'

  import alertify from 'alertify.js'
    export default {
        mounted() {
        	let self = this;
        	self.fetch_apr_forms().then((response) => {
              if (response.status == 200) {
                  self.addJqueryEvents();
              }
          })
        },
        data(){
            return {
                search: '',
                whileSearching: false
            }
        },
        methods: {
          	...mapActions([
          		'fetch_apr_forms',
              'db_search_apr',
              'db_get_apr_items_by_formid',
              'db_fetch_apr_items',
              'db_fetch_apr_balance',
              'db_fetch_deliveries_by_apr_no',
          	]),
            ...mapMutations([
                'set_current_apr_form',
                'set_updating_apr',
                'add_local_items',
                'clear_local_items',
                'set_c_delivery_is_po',
            ]),
            showAprDeliveries(apr_form){
                let self = this;
                // alert(2)
                self.set_current_apr_form(apr_form);
                console.log(apr_form)
                self.db_fetch_deliveries_by_apr_no(apr_form);
                self.set_c_delivery_is_po(false);
                // self.set_current_apr_form(apr_form);
                $('#ModalPoDeliveries').modal('show');
            },
            deliverAprForm(){
                let self = this;
                // alert(1)
                $('#CreateDeliveryForApr').modal('show');
                self.db_get_apr_items_by_formid(self.current_apr_form);
                // self.db_get_delivered_apr_items();
                self.db_fetch_apr_balance();
                setTimeout(function(){
                    $('#CreateDeliveryForApr').find('.charge-invoice').focus();
                }, 1000);
            },
            setCurrentAprForm(form){
                let self = this;
                self.set_current_apr_form(form);
            },
            addJqueryEvents(form){
                let self = this;
                $(function() {
                    let $tbl = $(".tbl-apr-forms");
                    $tbl.find('tbody').find('tr').click(function(event) {
                      /* Act on the event */
                        $tbl.find('tbody tr').removeClass('text-primary');
                        $(this).addClass('text-primary');
                    });
                });
            },
            editAprFormAndItems(form){
                let self = this;
                self.set_current_apr_form(form);
                self.set_updating_apr(true);
                self.clear_local_items();
                self.db_get_apr_items_by_formid(form).then(() => {
                    if (self.updating_apr) {
                        let item = {};
                        for (var i = self.current_apr_items.length - 1; i >= 0; i--) {
                            // console.log(self.current_apr_items[i]);
                            item = self.current_apr_items[i];
                            item.primary_id = item.id;
                            self.add_local_items(item);
                        }
                    }else {
                      alertify.log('not updating, please contact system admin');
                    };
                })
                $('.panel-apr').find('#profile-tab').click();
            },
            showModalAprItems(apr_form){
                let self = this;

                self.db_get_apr_items_by_formid(apr_form);
                $('#ModalShowAprItems').modal('show');
            }
        },
        computed: {
            ...mapState([
                'apr_form_views',
                'current_apr_form',
                'updating_apr',
                'current_apr_items'
            ])
        },
        filters: {
            format_date(d){
                return moment(d).format('MMMM DD, YYYY');
            },
            date_encoded_date(d){
                return moment(d).format('MMMM DD, YYYY hh:mm a');
            },
            format_money(m){
                return accounting.formatNumber(m, 2);
            }
        },
        watch: {
            'search': function(newVal, oldVal){
                let self = this;
                clearTimeout(self.timer);
                self.whileSearching = true;
                self.timer = setTimeout(function(){
                    self.db_search_apr(newVal).then((response) => {
                        setTimeout(function(){
                            self.whileSearching = false;
                        }, 100);
                        if (response.status != 200) {
                            alertify.log('Unable to connect to server, Please try to reload the page or Simply Contact the Netadmin/DB Admin');
                        };
                    })
                }, 1500);
            }
        },
        components: {
            ModalShowAprItems
        }
    }
</script>
