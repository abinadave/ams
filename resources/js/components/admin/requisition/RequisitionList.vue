<template>
  <div style="overflow: auto">
    <input
      v-model="search"
      class="form-control form-control-sm"
      type="text"
      style="width: 300px; border-radius: 15px; margin: 10px"
      placeholder="Search RIS Number, Division, Office .."
    />
    <!-- current_r_items: {{current_r_items.length}} -->
    <table
      class="table table-striped tbl-requests table-bordered"
      style="font-size: 12px"
    >
      <thead>
        <tr>
          <!-- <th></th> -->
          <th width="80" colspan="3" class="text-center">CONTROLS</th>
          <th class="text-center" scope="col">RIS NUMBER</th>
          <th scope="col" class="text-center">DATE REQUESTED</th>
          <th class="text-center">ITEMS</th>
          <th scope="col" class="text-center">DIVISION</th>
          <th scope="col" class="text-center">OFFICE</th>
          <th scope="col" width="80">RESP. CODE</th>
          <th scope="col" width="80">FUND CLUSTER</th>
          <th scope="col">REQUESTED BY</th>
          <th scope="col">ISSUED BY</th>
          <th scope="col">APPROVED BY</th>
          <th scope="col">RECEIVED BY</th>
          <th scope="col" width="300">ITEM LIST</th>
        </tr>
      </thead>
      <tbody>
        <tr v-bind:key="form.id" v-for="form in request_form_views">
          <!-- <th>{{ form.id }}</th> -->
          <th width="10">
            <a
              @click="printModal(form)"
              class="a-print fa fa-print text-primary"
              style="cursor: pointer; font-size: 15px"
            ></a>
          </th>
          <th class="text-center" width="10">
            <i
              @click="showRequestedItems(form)"
              class="fa fa-file-text"
              aria-hidden="true"
              style="cursor: pointer; font-size: 15px"
            ></i>
            <a
              @click="showRequestedItems(form)"
              class="text-primary"
              style="cursor: pointer"
            ></a>
          </th>
          <th width="10">
            <i
              style="cursor: pointer"
              @click="editRequest(form)"
              class="fa fa-pencil text-danger"
            ></i>
          </th>
          <th scope="row">
            {{ form.ris_year }}-{{ form.ris_month }}-{{ form.ris_number }}
          </th>
          <th class="text-center">{{ form.date_requested | format_date }}</th>
          <td class="text-center">{{ form.count_items }}</td>
          <td class="text-center">{{ form.division }}</td>
          <td class="text-center">{{ form.office }}</td>
          <td>{{ form.resp_center_code }}</td>
          <td>{{ form.fund_cluster }}</td>
          <td>{{ form.requested_by }}</td>
          <td>{{ form.issued_by }}</td>
          <td>{{ form.approved_by }}</td>
          <td>{{ form.received_by }}</td>
          <td>{{ form.item_list }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
<style type="text/css">
.tbl-requests {
  font-size: 12px;
  width: 1600px;
}
.tbl-requests td {
  padding: 4px;
}
.tbl-requests th {
  padding: 4px;
}
</style>
<script>
import { mapActions, mapState, mapMutations } from "vuex";
import moment from "moment";
export default {
  mounted() {
    this.fetch_requisitions().then(() => {
      jQuery(document).ready(function ($) {
        $(".a-print").click(function () {
          let a = $(this);
          $(this).removeClass("fa-print");
          $(this).empty();
          $(this).addClass("fa fa-spinner fa-pulse fa-3x fa-fw");
          setTimeout(function () {
            a.removeClass();
            a.addClass("a-print fa fa-print text-primary");
          }, 1000);
        });
      });
    });
  },
  data() {
    return {
      search: "",
      whileSearching: false,
    };
  },
  filters: {
    format_date(d) {
      let date = moment(d).format("MMM DD, YYYY");
      if (date != "Invalid date") {
        return date;
      }
    },
  },
  methods: {
    ...mapActions([
      "fetch_requisitions",
      "search_requesitions",
      "fetch_current_r_items",
      "search_request_form_ris_no",
    ]),
    ...mapMutations(["set_current_request_form", "set_ris_updating"]),
    editRequest(form) {
      let self = this;
      self.set_current_request_form(form);
      self.fetch_current_r_items(form.id);
      self.set_ris_updating(true);
      $("a#profile-tab").click();
    },
    printModal(form) {
      let self = this;
      console.log("printing..");
      self.set_current_request_form(form);
      self.fetch_current_r_items(form.id).then(() => {
        $("#btnPrintStockCard").trigger("click");
      });
      $("#ModalShowRequisitions").modal("hide");
    },
    showRequestedItems(form) {
      let self = this;
      self.fetch_current_r_items(form.id);
      self.set_current_request_form(form);
      $("#ModalShowRequisitions").modal("show");
    },
  },
  computed: {
    ...mapState(["request_form_views", "current_r_items"]),
  },
  watch: {
    search: function (newVal, oldVal) {
      let self = this;
      clearTimeout(self.timer);
      let str = self.search.split("-");
      if (str.length == 3) {
        self.search_request_form_ris_no({
          str: str,
          search: self.search,
        });
      } else {
        self.timer = setTimeout(function () {
          self.whileSearching = true;
          self.search_requesitions(self.search);
        }, 1500);
      }
    },
  },
};
</script>
