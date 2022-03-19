<template>
  <div>
    <div
      class="modal fade"
      id="ModalShowRequisitions"
      tabindex="-1"
      role="dialog"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">
              VIEW REQUISITION SLIP
            </h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="body-requisition-slip">
            <!-- form {{ form }} -->
            <img src="/app/dilg-logo.png" alt="" width="50" />
            <h4 class="text-center" style="margin-top: -25px">
              REQUISITION AND ISSUE SLIP
            </h4>
            <br />
            <h6>
              Entity Name:
              <b style="text-decoration: underline">
                &nbsp; &nbsp;DILG Region 08, Tacloban City
                &nbsp;&nbsp;&nbsp;&nbsp;</b
              >
              <br />
              &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
            </h6>
            <h6 style="margin-top: -38px" class="text-right">
              <b
                >Fund Cluster
                <span style="text-decoration: underline">
                  &nbsp;&nbsp;&nbsp;&nbsp; 01 &nbsp;&nbsp;&nbsp;&nbsp;
                </span>
              </b>
            </h6>
            <!-- <br /> -->
            <table
              class="table table-bordered table-condensed"
              id="tbl-requisition-form"
            >
              <thead>
                <tr>
                  <th width="167">Division</th>
                  <th width="400">{{ form.division }}</th>
                  <th width="200">Responsibility Center Code</th>
                  <th width="400">{{ form.resp_center_code }}</th>
                </tr>
                <tr>
                  <th>Office</th>
                  <th>{{ form.office }}</th>
                  <th>RIS No.</th>
                  <th style="font-size: 14px">
                    <b
                      >{{ form.ris_year }}-{{ form.ris_month }}-{{
                        form.ris_number
                      }}</b
                    >
                  </th>
                </tr>
                <tr>
                  <th>Purpose</th>
                  <th colspan="3">{{ form.purpose }}</th>
                </tr>
              </thead>
            </table>

            <div style="margin-top: -40px; width: 100%">
              <RequestItemsView />
            </div>

            <div style="margin-top: -17px">
              <table
                class="table table-bordered table-condensed"
                id="tbl-requisition-form"
              >
                <thead>
                  <tr>
                    <th width="140" height="25">Signature</th>
                    <th>Requested by:</th>
                    <th>Approved by:</th>
                    <th>Issued by:</th>
                    <th>Received by:</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th height="25">Printed name:</th>
                    <td class="the-names item-td">{{ form.requested_by }}</td>
                    <td class="the-names item-td">{{ form.approved_by }}</td>
                    <td class="the-names item-td">{{ form.issued_by }}</td>
                    <td class="the-names item-td">{{ form.received_by }}</td>
                  </tr>
                  <tr>
                    <th>Designation</th>
                    <td height="25"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                  <tr>
                    <th>Date Requested</th>
                    <th height="20" style="font-size: 14px">
                      &nbsp;&nbsp;{{ form.date_requested | format }}
                    </th>
                    <td></td>
                    <td></td>
                    <td></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Close <i class="fa fa-remove"></i>
            </button>
            <button
              @click="printRequisitionSlip"
              type="button"
              class="btn btn-primary"
            >
              Print <i class="fa fa-print"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<style scoped>
.modal-lg {
  max-width: 60% !important;
}
#tbl-requisition-form th {
  padding: 2px;
  border: 2px solid black !important;
  font-size: 14px;
}
#tbl-requisition-form td {
  padding: 2px;
  border: 2px solid black !important;
  font-size: 14px;
}
.the-names {
  font-size: 14px;
}
.item-td {
  font-weight: bolder;
}
@page {
  size: letter portrait;
}
</style>
<script>
import RequestItemsView from "./RequestItemsView";
import { mapState, mapActions, mapMutations } from "vuex";
import moment from "moment";
export default {
  mounted() {},
  data() {
    return {};
  },
  filters: {
    format_date(d) {
      return moment(d).format("MMM DD, YYYY hh:mm a");
    },
    format(d) {
      return moment(d).format("MMMM DD, YYYY");
    },
    date_from_now(d) {
      return moment(d).fromNow();
    },
  },
  components: {
    RequestItemsView,
  },
  methods: {
    ...mapMutations(["crystal_report_1"]),
    printRequisitionSlip() {
      let self = this;
      $("#ModalShowRequisitions").modal("hide");
      // alert(1)
      setTimeout(function () {
        self.crystal_report_1({
          // cloned_html: '#body-requisition-slip'
          cloned_html: "#body-requisition-slip",
        });
      }, 700);
    },
  },
  computed: {
    ...mapState({
      form: "current_request_form",
    }),
  },
};
</script>
