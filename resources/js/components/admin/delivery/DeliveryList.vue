<template>
    <div style="margin-top: 30px">
      <AdvanceSearchDelivery />
       <table class="table table-dforms table-bordered table-condensed table-hover" style="font-size: 12px">
          <thead class="thead-dark">
            <tr>
              <th class="text-center" title="Delivery number" width="50">DR #</th>
              <th width="60"></th>
              <th scope="col" width="250">SUPPLIER</th>
              <th scope="col" class="text-center">APR NO/P.O NO</th>
              <th scope="col">PURPOSE</th>
              <th scope="col" width="60">TOTAL ITEMS</th>
              <th scope="col" class="text-center">IAR #</th>
              <th scope="col" class="text-center">DATE OF DELIVERY</th>
              <th scope="col" class="text-center">DATE RECEIVED</th>
              <th scope="col" class="text-center">CHARGE INVO. #</th>
              <th scope="col" class="text-center">DATE OF INVOICE</th>
              <th class="text-right" scope="col">DELIVERY AMOUNT</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="deliver_form in deliver_form_views">
              <td title="Delivery number" class="text-center">{{deliver_form.id}}</td>
              <th class="text-center">
                <a @click="showDeliveryItems(deliver_form)" style="cursor: pointer" class="text-primary"><i class="fa fa-file-text" aria-hidden="true"></i> <span style="font-size: 10px">{{deliver_form.count_items}}</span></a>
              </th>
              <th scope="row">
                <span v-if="deliver_form.type == 'apr'">
                    PROCUREMENT SERVICES
                </span>
                <span v-else>
                    {{deliver_form.name}}
                </span>
              </th>
              <td class="text-left">
                  <span v-if="deliver_form.type == 'po'">
                      PO: <b>{{deliver_form.po_year}}-{{deliver_form.po_month}}-{{deliver_form.po_number}}</b>
                  </span>
                  <span v-else>
                      APR: <b>{{ deliver_form.apr_no }}</b>
                  </span>
              </td>
              <td>{{ deliver_form.purpose }}</td>
              <td class="text-center">{{ deliver_form.count_items }}</td>
              <td class="text-center">{{ deliver_form.iar_no }}</td>
              <td class="text-center">{{ deliver_form.date_of_delivery | date_format }}</td>
              <td class="text-center">{{ deliver_form.date_received | date_format }}</td>
              <td class="text-center">{{ deliver_form.charge_invoice_no }}</td>
              <td class="text-center">{{ deliver_form.date_of_invoice | date_format }}</td>
              <td class="text-right">{{ deliver_form.delivery_total_amount | money_format }}</td>
            </tr>
          </tbody>
        </table>
    </div>
</template>
<style scoped>
    .table-dforms td {
        padding: 4px;
    }
    .table-dforms th {
        padding: 4px;
    }
</style>
<script>
    import {mapActions, mapState, mapMutations} from 'vuex'
    import accounting from 'accounting'
    import moment from 'moment'
    import AdvanceSearchDelivery from './AdvanceSearchDelivery'
    export default {
        mounted() {
            let self = this;
            self.fetch_deliver_forms_view();
        },
        components: {
            AdvanceSearchDelivery
        },
        methods: {
            ...mapActions([
                'fetch_deliver_forms_view',
                'get_deliver_items'
            ]),
            ...mapMutations([
                'set_current_deliver_form'
            ]),
            showDeliveryItems(form){
                let self = this;
                $('#ModalDeliverItems').modal('show');
                self.get_deliver_items(form.id);
                self.set_current_deliver_form(form);
            }
        },
        filters: {
            money_format(m){
                return accounting.formatNumber(m);
            },
            date_format(d){
                return moment(d).format('MMM DD, YYYY')
            }
        },
        computed: {
            ...mapState([
                'deliver_form_views'
            ])
        }
    }
</script>
