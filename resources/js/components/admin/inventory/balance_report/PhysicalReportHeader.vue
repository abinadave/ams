<template>
  <div class="text-center">
    <h5>DEPARTMENT OF THE INTERIOR AND LOCAL GOVERNMENT</h5>
    <h5 style="margin-top: -10px">REGION 08, TACLOBAN CITY</h5>
    <br />
    <h5 style="margin-top: -15px">
      REPORT ON THE PHYSICAL COUNT OF INVENTORIES
    </h5>
    <h5 style="margin-top: -10px">OFFICE SUPPLIES</h5>
    <br />
    <p>As of {{ physical_report_date | format_date }}</p>
    <p class="text-left" style="font-weight: bolder">
      Fund Cluster: _______________________________________
    </p>
    <p class="text-left" style="font-weight: bolder; margin-top: 10px">
      For which {{ getHeadName("regional_director") }} REGIONAL DIRECTOR, DILG
      RO VIII is accountable, having assumed such accountability on June 9, 2020
    </p>
    <span
      @click="editPhysicalReportHeader1"
      style="margin-top: -35px; cursor: pointer"
      class="fa fa-pencil pull-right"
    ></span>
  </div>
</template>

<script>
import moment from "moment";
import { mapState } from "vuex";
export default {
  mounted() {
    let self = this;

    console.log("Component mounted.");
  },
  data() {
    return {
      current_date: moment().format("MMM DD, YYYY"),
    };
  },
  computed: {
    ...mapState(["physical_report_date"]),
    ...mapState({
      head_names: (state) => state.global_var_module.head_names,
    }),
  },
  methods: {
    editPhysicalReportHeader1() {
      let self = this;
      alert("editing pls ewait...");
    },
    getHeadName(position) {
      const self = this;
      let rs = _.filter(self.head_names, { position: position });
      if (rs.length) {
        let head = _.first(rs);
        return head.name;
      }
    },
  },
  filters: {
    format_date(d) {
      let f_date = moment(d).format("MMMM DD, YYYY");
      if (f_date != "Invalid date") {
        return f_date;
      } else {
        return "---";
      }
    },
  },
};
</script>
