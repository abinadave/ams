<template>
  <div>
    <br />
    <table class="tbl-req-items table table-bordered" id="tbl2883">
      <thead>
        <tr>
          <th colspan="4" class="text-center">Requisition</th>
          <th colspan="2" class="text-center" width="150">Stock Available?</th>
          <th colspan="2" class="text-center">Issue</th>
        </tr>
        <tr>
          <th class="text-center" width="150">Stock No.</th>
          <th class="text-center" width="90">Unit</th>
          <th width="400">Description</th>
          <th class="text-center">Quantity</th>
          <th class="text-center" width="50">Yes</th>
          <th class="text-center" width="50">No</th>
          <th class="text-center" width="80">Quantity</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        <!-- {{current_r_items}} -->
        <tr :key="item.id" v-for="item in items">
          <td class="text-center item-td">{{ item.rca_code }}-{{item.rca_cat}}-{{item.rca_no}}</td>
          <td class="text-center item-td">{{ item.unit_name }}</td>
          <td class="item-td">{{ item.name }} - {{ item.description }}</td>
          <td class="text-center item-td" width="70">
            <b>{{ item.requested_qty }}</b>
          </td>
          <!-- <td class="text-center">{{ item.qty_before_withdraw }}</td> -->
          <td class="text-center">
            <i class="fa fa-check"></i>
          </td>
          <td></td>
          <td class="text-center">
            <b>{{ item.requested_qty }}</b>
          </td>
          <td width="150">{{ item.remarks }}</td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <th colspan="4" style="color: red !important;" class="text-center">
            ***********
            NOTHING FOLLOWS
            ***********
          </th>

          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
    <style>
.tbl-req-items td,
th {
  padding: 5px;
}
.tbl-req-items input {
  height: 25px;
}
.item-td {
  font-weight: bolder;
}
#tbl2883 th {
  padding: 2px;
  border: 2px solid black !important;
  font-size: 14px;
}
#tbl2883 td {
  padding: 2px;
  border: 2px solid black !important;
  font-size: 14px;
}
</style>
<script>
import { mapState, mapMutations } from "vuex";
export default {
  mounted() {},
  computed: {
    ...mapState({
      items: "current_r_items",
    }),
  },
  data() {
    return {
      td_left: 6,
    };
  },
};
</script>
