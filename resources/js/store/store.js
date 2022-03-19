import Vue from "vue";
import Vuex from "vuex";
import alertify from "alertify.js";
import moment from "moment";
Vue.use(Vuex);

import { global_var_module } from "./modules/global_var_module.js";
import { requisition_module } from "./modules/requisition_module.js";
import { user_module } from "./modules/user_module.js";
export const store = new Vuex.Store({
    strict: false,
    modules: {
        global_var_module: global_var_module,
        requisition_module: requisition_module,
        user_module: user_module
    },
    state: {
        categories: [],
        units: [],
        items: [],
        po_categories: [],
        suppliers: [],
        local_items: [],
        total_count_items: 0,
        while_searching_items: false,
        purchase_orders: [],
        po_updating: false,
        local_po_items: [],
        local_ditems: [],
        deliver_forms: [],
        unit_views: [],
        po_pagination: {
            current_page: 0,
            from: 0,
            last_page: 0,
            per_page: 0,
            to: 0,
            total: 0
        },
        purchase_order_form: {
            supplier_id: "",
            po_number: "",
            date: "",
            po_category: "",
            apr_no: ""
        },
        po_selection: [],
        current_supplier: 0,
        current_po: {},
        deliver_form_views: [],
        deliver_item_views: [],
        current_deliver_form: {},
        local_deliveries: [],
        local_dforms: [],
        request_form_views: [],
        current_request_form: {},
        current_r_items: [],
        current_item: {},
        stock_card_report: [],
        cloned_dtr_report: "",
        supplier_address: "",
        no_item_was_found: false,
        initial_balance: {
            id: 0,
            item_id: 0,
            balance: 0,
            encoder: 0,
            bal_remarks: "",
            created_at: "",
            updated_at: ""
        },
        purchase_item_views: [],
        update_item: {},
        updating_item: false,
        rsmi: [],
        rsmi_dates: {
            d1: "",
            d2: ""
        },
        cloned_html: "",
        snapshot_cloned_html: "",
        max_ris_number: 0,
        ris_updating: false,
        apr_form_views: [],
        current_apr_items: [],
        current_apr_form: {},
        updating_apr: false,
        inventory_balance_report: [],
        local_apr_items: [],
        current_apr_balance: [],
        /** current delivery is P.O **/
        c_delivery_is_po: true,
        current_selected_item: {},
        actual_stocks: [],
        physical_report: [],
        physical_report_date: "",
        item_physical: [],
        no_item_was_found: false
    },
    actions: {
        db_fetch_items_physical_force_all({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/items_force_all")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            // console.log(json)
                            commit("set_items_physical", json);
                        }
                        resolve(response);
                        state.while_searching_items = false;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        },
        db_fetch_all_items_force({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/items_force_all")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            // console.log(json)
                            commit("set_items", json);
                        }
                        resolve(response);
                        state.while_searching_items = false;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        },
        db_search_physical_count_report_items_by_date({ commit, state }, date) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/get_physical_balance_report", {
                        date: date
                    })
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json);
                        commit("set_physical_report", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        search_request_form_ris_no({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/search_ris_using_ris_no", payload)
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json);
                        commit("set_request_form_views", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_find_stock_card_balances({ commit, state }, ids) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/get_actual_items_balance", {
                        ids: ids
                    })
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json);
                        commit("set_actual_stocks", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_add_ris_item({ commit, state }, item) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/add_ris_item_to_existing", {
                        item: item,
                        request_form: state.current_request_form
                    })
                    .then(function(response) {
                        let json = response.data;
                        let clone_item = item;
                        clone_item.id = json.id;
                        commit("add_local_items", clone_item);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_remove_ris_item({ commit, state }, ris_item) {
            return new Promise((resolve, reject) => {
                axios
                    .delete("/request_item", {
                        data: {
                            id: ris_item.id,
                            request_form_id: state.current_request_form.id
                        }
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            if (Number(json.removed)) {
                                alertify.success(
                                    "Item: " +
                                        ris_item.name +
                                        " succesfully removed"
                                );
                            } else {
                                alertify.success(
                                    "There was a problem removing the item, error: 00123"
                                );
                            }
                            commit("remove_local_item", ris_item);
                            // commit('update_one_apr_form_view', json.apr_form_view);
                            console.log(json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        update_po_delivery_completed({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/db_set_po_delivery_completed", {
                        current_po: state.current_po
                    })
                    .then(function(response) {
                        let json = response.data;
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_save_po_item_dr_item({ commit, state }, payload) {
            // Payload: qty: 4, unit_cost: 25
            return new Promise((resolve, reject) => {
                axios
                    .post("/save_po_item_dr_item", {
                        item: state.current_selected_item,
                        current_po: state.current_po,
                        payload: payload
                    })
                    .then(function(response) {
                        let json = response.data;
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_fetch_deliveries_by_apr_no({ commit, state }, apr_form) {
            // console.log('db_fetch_deliveries_by_apr_no');
            // alert(1)
            return new Promise((resolve, reject) => {
                axios
                    .post("/fetch_deliveries_by_apr_no", {
                        id: apr_form.id
                    })
                    .then(function(response) {
                        let json = response.data;
                        commit("set_local_dforms", json);
                        console.log(json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_add_one_purchase_item({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/add_one_purchase_item", {
                        item: state.current_selected_item,
                        current_po: state.current_po
                    })
                    .then(function(response) {
                        let json = response.data;
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_fetch_apr_balance({ commit, state }) {
            // console.log(state.current_apr_form)
            return new Promise((resolve, reject) => {
                axios
                    .post("/apr_balances", state.current_apr_form)
                    .then(function(response) {
                        let json = response.data;
                        commit("set_current_apr_balances", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_save_df_apr({ commit, state }, form) {
            form.apr_no = state.current_apr_form.apr_no;
            form.apr_id = state.current_apr_form.id;
            return new Promise((resolve, reject) => {
                axios
                    .post("/insert_df_apr", form)
                    .then(function(response) {
                        let json = response.data;
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_fetch_apr_items({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .put("/fetch_apr_items_byid", payload)
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json)
                        commit("update_one_apr_form_view", json.apr_form_view);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_delete_apr_item({ commit, state }, item) {
            return new Promise((resolve, reject) => {
                axios
                    .delete("/apr_item", {
                        data: { id: item.primary_id }
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("remove_local_item", item);
                            commit(
                                "update_one_apr_form_view",
                                json.apr_form_view
                            );
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_update_apr_item({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .put("/apr_item", payload)
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json)
                        commit("update_one_apr_form_view", json.apr_form_view);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_update_apr_form({ commit, state }, form) {
            return new Promise((resolve, reject) => {
                axios
                    .put("/apr_form", form)
                    .then(function(response) {
                        let json = response.data;
                        commit("update_one_apr_form", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_check_duplicate_apr_no({ commit, state }, apr_no) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/validate_apr_no_duplicate", { apr_no: apr_no })
                    .then(function(response) {
                        let json = response.data;
                        // commit('update_one_delivery_form', json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_insert_deliver_items({ commit, state }, payload) {
            // console.log(payload)
            // let form = payload.form;
            // form.items = payload.incomplete_items;
            // form.items = payload.items;
            return new Promise((resolve, reject) => {
                axios
                    .post("/save_po_deliver_items", payload)
                    .then(function(response) {
                        let json = response.data;
                        commit("update_one_delivery_form", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_insert_apr_deliver_items({ commit, state }, form) {
            commit("set_local_po_items_apr_no", form.apr_no);
            form.items = state.local_po_items;
            return new Promise((resolve, reject) => {
                axios
                    .post("/save_apr_deliver_items", form)
                    .then(function(response) {
                        let json = response.data;
                        // commit('update_one_delivery_form', json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_save_po_delivery_form({ commit, state }, form) {
            form.po_id = state.current_po.id;
            return new Promise((resolve, reject) => {
                axios
                    .post("/save_po_delivery_form", form)
                    .then(function(response) {
                        let json = response.data;
                        // commit('set_inventory_balance_report', json.items);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_fetch_all_inventory_balance({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/fetch_all_inventory_balance")
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json)
                        commit("set_actual_stocks", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_get_apr_items_by_formid({ commit, state }, form) {
            commit("set_current_apr_form", form);
            alert(1);
            return new Promise((resolve, reject) => {
                axios
                    .post("/fetch_apr_items_byid", {
                        apr_id: form.id
                    })
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json)
                        // console.log(response)
                        commit("set_local_po_items", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_search_apr({ commit, state }, search) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/search_apr_form_views", {
                        search: search
                    })
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json);
                        commit("set_apr_form_views", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_apr_forms({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/apr_form_views")
                    .then(function(response) {
                        let json = response.data;
                        commit("set_apr_form_views", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_save_apr_items({ commit, state }, data) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/apr_items", {
                        apr_id: data.id,
                        items: state.local_items
                    })
                    .then(function(response) {
                        let json = response.data;
                        commit("add_one_apr_form", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_dave_apr_form({ commit, state }, form) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/apr_form", form)
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json);
                        // commit('update_one_request_form', json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        update_requisition_form({ commit, state }, form) {
            return new Promise((resolve, reject) => {
                axios
                    .put("/request_form", form)
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json);
                        commit("update_one_request_form", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        update_request_item({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .put("/request_item", payload)
                    .then(function(response) {
                        let json = response.data;
                        // console.log(json);
                        // commit('set_ris_max_number', json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        get_ris_last_insert_id({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/ris_last_insert_id")
                    .then(function(response) {
                        let json = response.data;

                        commit("set_ris_max_number", json);
                        alertify.success(
                            "Last Requisition control no is: " +
                                state.max_ris_number
                        );
                        // resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_rsmi_report({ commit, state }, dates) {
            commit("set_rsmi_dates", dates);
            return new Promise((resolve, reject) => {
                axios
                    .post("/rsmi", dates)
                    .then(function(response) {
                        let json = response.data;
                        commit("set_rsmi", json);
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_validate_ris({ commit, state }, form) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/validate_ris_form", form)
                    .then(function(response) {
                        resolve(response);
                        // commit('set_updating_item', false);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_update_item({ commit, state }, form) {
            return new Promise((resolve, reject) => {
                axios
                    .put("/item", form)
                    .then(function(response) {
                        resolve(response);
                        commit("set_updating_item", false);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_po_item_views({ commit, state }, po) {
            // alert(po.id)
            return new Promise((resolve, reject) => {
                axios
                    .post("/purchase_item_views_by_po_id", {
                        po_id: po.id
                    })
                    .then(function(response) {
                        resolve(response);
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_po_item_views", json);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_initial_balance({ commit, state }, item_id) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/init_balance_item_id", {
                        item_id: item_id
                    })
                    .then(function(response) {
                        resolve(response);
                        if (response.status === 200) {
                            let object = response.data;
                            if (_.has(object, "id")) {
                                commit("set_init_balance", object);
                            }
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_supplier_name({ commit, state }, po) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/fetch_supplier_name", {
                        po: po
                    })
                    .then(function(response) {
                        resolve(response);
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_supplier_address", json.address);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },

        fetch_stock_card_report({ commit, state }, item_id) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/stock_card_report", {
                        item_id
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_stock_card_report", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_current_r_items({ commit, state }, request_form_id) {
            // alert("hit");
            return new Promise((resolve, reject) => {
                axios
                    .post("/request_item_by_form_id", {
                        request_form_id
                    })
                    .then(function(response) {
                        console.log(response);
                        if (response.status === 200) {
                            let json = response.data;
                            // console.log(json);
                            commit("set_current_r_items", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        search_requesitions({ commit, state }, search) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/search_requisitions", {
                        search: search
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_request_form_views", json);
                            // resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_requisitions({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/request_forms")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_request_form_views", json);
                            resolve(json);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        db_save_request_items({ commit, state }, request_form) {
            // alert('calling again to update request items')
            return new Promise((resolve, reject) => {
                axios
                    .post("/request_items", {
                        request_form: request_form,
                        items: state.local_items,
                        ris_updating: state.ris_updating
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            // commit('set_delivery_form_views', json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        save_requisition_form({ commit, state }, form) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/requisition_form", form)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            // commit('set_delivery_form_views', json);
                            resolve(response);
                            // console.log(json);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        advanced_search_po({ commit, state }, form) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/advanced_search_po", form)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            // commit('set_delivery_form_views', json);
                            // resolve(response);
                            console.log(json);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        advanced_search_delivery({ commit, state }, form) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/advanced_search_delivery", form)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_delivery_form_views", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_po_deliveries({ commit, state }, po) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/fetch_po_deliveries", po)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_local_dforms", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        get_delivery_qty_balance({ commit, state }, ids) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/get_delivery_balance", {
                        ids: ids
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            // console.log(json);
                            commit("set_local_deliveries", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        get_deliver_items({ commit, state }, delivery_form_id) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/deliver_items_by_form_id", {
                        form_id: delivery_form_id
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_deliver_item_views", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_deliver_forms_view({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/deliver_form_views")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_delivery_form_views", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        search_po_selections({ commit, state }, supplier_id) {
            commit("set_current_supplier", supplier_id);
            return new Promise((resolve, reject) => {
                axios
                    .post("/purchse_orders/delivery", {
                        supplier_id: supplier_id
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_po_selection", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        insert_ditems({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/delivery_items", {
                        items: state.local_po_items,
                        is_dbm: payload.is_dbm,
                        deliver_form_id: payload.deliver_form_id
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("empty_local_po_items");

                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        edit_unit({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .put("/edit_unit", payload)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("update_unit_in_views", {
                                unit_id: payload.unit.id,
                                newUnitName: payload.newUnitName
                            });
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        remove_unit({ commit, state }, unit_id) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/delete_unit", {
                        unit_id: unit_id
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("delete_unit_in_views", unit_id);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        search_apr_no({ commit, state }, APR_NO) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/search_apr_no_for_delivery", {
                        apr_no: APR_NO
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_local_po_items", json.purchase_items);
                        }
                        resolve(response);
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_unit_views({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/unit_views")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_unit_views", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        save_unit({ commit, state }, unit_name) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/unit", {
                        unit_name: unit_name
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("add_one_unit", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        save_delivery_form({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/delivery_form", payload)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            if (json.id > 0) {
                                commit("add_one_deliver_form", json);
                            }
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        search_po_number({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/search_po_number", payload)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_local_po_items", json.purchase_items);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        update_unit_cost_purchase_item({ commit, state }, item) {
            return new Promise((resolve, reject) => {
                axios
                    .put("/purchase_item_unit_cost", item)
                    .then(function(response) {
                        resolve(response);
                        if (response.status === 200) {
                            let json = response.data;
                            commit("update_total_sum_purchase_orders", json);
                            alertify.success(
                                "Unit Cost, Successfully updated!"
                            );
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        update_qty_purchase_item({ commit, state }, item) {
            return new Promise((resolve, reject) => {
                axios
                    .put("/purchase_item_qty", item)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("update_total_sum_purchase_orders", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        delete_purchase_item({ commit, state }, item) {
            return new Promise((resolve, reject) => {
                axios
                    .delete("/purchase_item", {
                        data: { id: item.primary_id }
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_purchase_item_by_id({ commit, state }, id) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/purchase_items_by_po_id", {
                        po_id: id
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        validate_po_number({ commit, state }, po_number) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/validate/po_number", {
                        po_number: po_number
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        search_purchase_order({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/search_purcase_order", {
                        search: payload
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_purchase_orders", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_purchase_orders({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/purchase_order")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            // alert('done')
                            // console.log(json)
                            commit("set_purchase_orders", json.data);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        nextpage() {
            axios
                .get("purchase_order?page=" + 2)
                .then(response => {
                    // this.posts = response.data.data.data;
                    // this.pagination = response.data.pagination;
                    let data = response.data.data.data;
                })
                .catch(error => {
                    console.log(error.response.data);
                });
        },
        save_purchase_items({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/purchase_items", {
                        items: state.local_items,
                        purchase_order: payload
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        save_purchase_order({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/purchase_order", payload)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_suppliers({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/supplier")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_suppliers", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        save_supplier({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/supplier", payload)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("add_supplier", json);
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        },
        fetch_POCategories({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/p_o_categories")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_po_categories", json);
                            resolve();
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        },
        search_for_items({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/search_items", {
                        search: payload
                    })
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            //   alert(json.length)
                            if (!json.length) {
                                state.items = [];
                                alertify.log(
                                    "No data was found for: " + payload
                                );
                            } else {
                                commit("set_items", json);
                            }
                            resolve(response);
                        } else {
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        },
        count_total_items({ commit, state }) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/count_total_items")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_total_count_items", json);
                            resolve();
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        },
        fetch_items({ commit, state }) {
            commit("set_updating_item", false);
            return new Promise((resolve, reject) => {
                axios
                    .get("/items")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_items", json);
                        }
                        resolve(response);
                        state.while_searching_items = false;
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        },
        fetch_categories({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/categories")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_cats", json);
                            resolve();
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        },
        fetch_units({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .get("/units")
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            commit("set_units", json);
                            resolve();
                        }
                    })
                    .catch(function(error) {
                        console.log(error);
                    });
            });
        },
        save_item({ commit, state }, payload) {
            return new Promise((resolve, reject) => {
                axios
                    .post("/item", payload)
                    .then(function(response) {
                        if (response.status === 200) {
                            let json = response.data;
                            if (json.id > 0) {
                                alertify.success("Item successfully added");
                                commit("add_item", {
                                    item: json
                                });
                                commit("set_no_item_was_found", false);
                            }
                            resolve(response);
                        }
                    })
                    .catch(function(error) {
                        resolve(error.response);
                    });
            });
        }
    },
    mutations: {
        clear_item_physical(state) {
            state.item_physical = [];
            state.physical_report = [];
            console.log("item_physical: cleared!");
        },
        set_items_physical(state, json) {
            // alert('hit in set_items_physical')
            state.item_physical = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.item_physical.unshift(json[i]);
            }
            console.log("item_physical: " + state.item_physical.length);
        },
        set_physical_report_date(state, date) {
            state.physical_report_date = date;
            console.log("new physical_report_date: " + date);
        },
        set_physical_report(state, json) {
            state.physical_report = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.physical_report.unshift(json[i]);
            }
            console.log("physical_report: " + state.physical_report.length);
        },
        set_actual_stocks(state, stocks) {
            state.actual_stocks = [];
            for (var i = stocks.length - 1; i >= 0; i--) {
                state.actual_stocks.unshift(stocks[i]);
            }
        },
        update_local_request_items(state, names) {
            let request_form = _.find(state.request_form_views, {
                id: Number(state.current_request_form.id)
            });
            request_form.item_list = names.join(", ");
            request_form.count_items = names.length;
            // alert('total items: ' +names.length + 1);
        },
        update_one_purchase_order(state, new_po) {
            let purchase_order = _.find(state.purchase_orders, {
                id: Number(new_po.id)
            });
            $.each(new_po, function(index, val) {
                purchase_order[index] = val;
            });
        },
        set_current_selected_item(state, item) {
            state.current_selected_item = item;
        },
        set_po_delivery_completed(state, payload) {
            let purchase_order = _.find(state.purchase_orders, {
                id: payload.po_id
            });
            purchase_order.delivery_completed = payload.bool;
        },
        set_purchase_order_delivery_completed(state, payload) {
            let purchase_order = _.find(state.purchase_orders, {
                id: payload.po_id
            });
            console.log("purchase_order");
            console.log(purchase_order);
            purchase_order.delivery_completed = 0;
        },
        set_c_delivery_is_po(state, bool) {
            state.c_delivery_is_po = bool;
        },
        update_one_apr_forms(state, json) {
            /** updating one apr_form **/
            let apr_form = _.find(state.apr_form_views, {
                apr_no: json.apr_no
            });
            $.each(json.apr_view, function(index, val) {
                apr_form[index] = val;
            });
        },
        set_current_apr_balances(state, json) {
            state.current_apr_balance = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.current_apr_balance.unshift(json[i]);
            }
        },
        set_local_po_items_apr_no(state, apr_no) {
            let item = {};
            for (var i = state.local_po_items.length - 1; i >= 0; i--) {
                item = state.local_po_items[i];
                item.apr_no = apr_no;
            }
        },
        update_one_apr_form_view(state, db_form) {
            let client_form = _.find(state.apr_form_views, {
                id: Number(db_form.id)
            });
            $.each(db_form, function(index, val) {
                client_form[index] = val;
            });
        },
        update_one_apr_form(state, json) {
            let id = json.id;
            // let index = _.findIndex(state.apr_form_views, { id: Number(id)});
            let apr_form = _.find(state.apr_form_views, { id: Number(id) });
            apr_form.apr_no = json.apr_no;
            apr_form.apr_date = json.apr_date;
        },
        set_updating_apr(state, bool) {
            state.updating_apr = bool;
        },
        update_one_delivery_form(state, json) {
            let index = _.findIndex(state.purchase_orders, {
                id: Number(json.po_id)
            });
            state.purchase_orders.splice(index, 1);
            state.purchase_orders.unshift(json.po_view);
        },
        set_inventory_balance_report(state, json) {
            state.inventory_balance_report = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.inventory_balance_report.unshift(json[i]);
            }
        },
        set_current_apr_form(state, form) {
            state.current_apr_form = form;
        },
        set_current_apr_items(state, json) {
            state.current_apr_items = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.current_apr_items.unshift(json[i]);
            }
        },
        add_one_apr_form(state, json) {
            state.apr_form_views.unshift(json.apr_form);
        },
        set_apr_form_views(state, json) {
            state.apr_form_views = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.apr_form_views.unshift(json[i]);
            }
        },
        update_one_request_form(state, json) {
            let rsRequestForm = _.filter(state.request_form_views, {
                id: Number(json.id)
            });
            if (rsRequestForm.length) {
                let request_form = _.first(rsRequestForm);
                let isEqual = _.isEqual(request_form, json);
                if (isEqual) {
                    alert("No changes was made");
                } else {
                    $.each(json, function(index, val) {
                        request_form[index] = val;
                        /* UPDATE ONE ITEM ON THE REQUISITION LIST WITHOUT RELOAD */
                    });
                }
            }
        },
        set_ris_updating(state, bool) {
            state.ris_updating = bool;
        },
        set_ris_max_number(state, payload) {
            state.max_ris_number = payload.max_ris_number;
        },
        crystal_report_1(state, payload) {
            state.cloned_html = payload.cloned_html;
            state.snapshot_cloned_html = $(state.cloned_html).clone();
            $("#app").hide();
            $(state.cloned_html).appendTo("#report-output");
            // $('#report-output').css({
            // 	width: '80%'
            // }).addClass('container');
            setTimeout(function() {
                window.print();
                setTimeout(function() {
                    $("#app").show();
                    $("#report-output").empty();
                }, 700);
            }, 600);
        },
        set_rsmi_dates(state, dates) {
            state.rsmi_dates.d1 = dates.d1;
            state.rsmi_dates.d2 = dates.d2;
        },
        set_rsmi(state, json) {
            state.rsmi = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.rsmi.unshift(json[i]);
            }
        },
        update_one_item(state, form) {
            // let index = _.findIndex(state.items, { id: Number(form.id)});
            let item = _.find(state.items, { id: Number(form.id) });
            $.each(form, function(index, val) {
                /* iterate through array or object */
                item[index] = val;
            });
        },
        set_updating_item(state, bool) {
            state.updating_item = bool;
        },
        set_update_item(state, item) {
            state.update_item = item;
        },
        set_po_item_views(state, json) {
            state.purchase_item_views = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.purchase_item_views.unshift(json[i]);
            }
        },
        set_init_balance(state, init_bal) {
            state.initial_balance = init_bal;
        },
        set_no_item_was_found(state, payload) {
            state.no_item_was_found = payload;
            if (state.items.length > 0) {
                state.no_item_was_found = false;
            }
        },
        set_supplier_address(state, address) {
            state.supplier_address = address;
        },
        set_stock_card_report(state, json) {
            let arr = {},
                arr1 = {};
            let arr2 = {};
            state.stock_card_report = [];
            // _.each(json.deliver_arr, function(model) {
            // 	model.is_po = 1;
            // 	state.stock_card_report.unshift(model);
            // });
            // _.each(json.ris_arr, function(model) {
            // 	model.is_po = 0;
            // 	state.stock_card_report.unshift(model);
            // });
            for (var i = json.deliver_arr.length - 1; i >= 0; i--) {
                arr = json.deliver_arr[i];
                if (arr.delivery_type == "po") {
                    arr.timestamp = arr.date_of_po;
                } else {
                    arr.timestamp = arr.date_of_apr;
                }
                arr.is_po = 1;
                state.stock_card_report.unshift(arr);
            }
            for (var i = json.ris_arr.length - 1; i >= 0; i--) {
                arr = json.ris_arr[i];
                arr.is_po = 0;
                arr.delivery_type = "ris";
                state.stock_card_report.unshift(arr);
            }
            state.stock_card_report = _.sortBy(
                state.stock_card_report,
                "timestamp"
            );
        },
        set_current_item(state, item) {
            state.current_item = item;
        },
        set_current_r_items(state, json) {
            state.current_r_items = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.current_r_items.unshift(json[i]);
            }
        },
        set_current_request_form(state, form) {
            state.current_request_form = form;
        },
        set_request_form_views(state, json) {
            state.request_form_views = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.request_form_views.unshift(json[i]);
            }
        },
        delete_local_items(state) {
            state.local_items = [];
        },
        set_local_dforms(state, json) {
            state.local_dforms = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.local_dforms.unshift(json[i]);
            }
        },
        set_local_deliveries(state, json) {
            state.local_deliveries = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.local_deliveries.unshift(json[i]);
            }
        },
        set_current_deliver_form(state, json) {
            state.current_deliver_form = json;
        },
        set_deliver_item_views(state, json) {
            state.deliver_item_views = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.deliver_item_views.unshift(json[i]);
            }
        },
        set_delivery_form_views(state, json) {
            state.deliver_form_views = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.deliver_form_views.unshift(json[i]);
            }
        },
        empty_local_po_items(state) {
            state.local_po_items = [];
        },
        set_current_purchase_order(state, po) {
            state.current_po = po;
        },
        set_po_selection(state, json) {
            state.po_selection = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.po_selection.unshift(json[i]);
            }
        },
        set_current_supplier(state, supplier_id) {
            state.current_supplier = supplier_id;
        },
        set_local_ditems(state, payload) {
            let item = {};
            state.local_ditems = [];
            for (var i = payload.purchase_items.length - 1; i >= 0; i--) {
                item = payload.purchase_items[i];
                state.local_ditems.unshift(item);
            }
        },
        update_unit_in_views(state, payload) {
            let rsUnits = _.filter(state.unit_views, {
                id: Number(payload.unit_id)
            });
            if (rsUnits.length) {
                let unit = _.first(rsUnits);
                unit.unit_name = payload.newUnitName;
            }
        },
        delete_unit_in_views(state, unit_id) {
            let index = _.findIndex(state.unit_views, { id: unit_id });
            state.unit_views.splice(index, 1);
        },
        set_unit_views(state, json) {
            state.unit_views = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.unit_views.unshift(json[i]);
            }
        },
        add_one_unit(state, unit) {
            unit.count_items = 0;
            unit.created_by = "";
            state.unit_views.unshift(unit);
        },
        add_one_deliver_form(state, form) {
            state.deliver_forms.unshift(form);
        },
        set_local_po_items(state, items) {
            state.local_po_items = [];
            for (var i = items.length - 1; i >= 0; i--) {
                state.local_po_items.unshift(items[i]);
            }
        },
        update_total_sum_purchase_orders(state, payload) {
            let total_sum = payload.total_sum;
            let purchase_item = payload.purchase_item;
            let purchaseOrder = _.find(state.purchase_orders, {
                id: Number(purchase_item.po_id)
            });
            if (typeof purchaseOrder != "undefined") {
                purchaseOrder.total_amount = Number(total_sum);
            } else {
                alert(
                    "cant find specific purchase_item, please try to reload page and repeat the same process"
                );
            }
        },
        set_purchase_order_form(state, form) {
            state.purchase_order_form.supplier_id = form.supplier_id;
            state.purchase_order_form.po_number = form.po_number;
            state.purchase_order_form.date = form.date;
            state.purchase_order_form.supplier_id = form.supplier_id;
            state.purchase_order_form.po_category = form.po_category;
            state.purchase_order_form.apr_no = form.apr_no;
        },
        purchase_order_updating(state, p) {
            state.po_updating = p;
        },
        set_purchase_orders(state, json) {
            state.purchase_orders = [];
            for (var i = json.length - 1; i >= 0; i--) {
                state.purchase_orders.unshift(json[i]);
            }
        },
        clear_local_items(state) {
            state.local_items = [];
        },
        remove_local_item(state, item) {
            let id = item.id;
            let rsLocalItems = _.filter(state.local_items, { id: Number(id) });
            if (rsLocalItems.length) {
                let index = _.findIndex(state.local_items, { id: Number(id) });
                state.local_items.splice(index, 1);
            }
        },
        add_local_items(state, item) {
            state.local_items.push({
                id: item.id,
                name: item.name,
                unit: item.unit_name,
                qty: Number(item.qty) > 0 ? item.qty : 0,
                unit_cost: Number(item.unit_cost) > 0 ? item.unit_cost : 0,
                primary_id: item.primary_id > 0 ? item.primary_id : 0,
                stock_no:
                    item.rca_code + "-" + item.rca_cat + "-" + item.rca_no,
                running_balance: item.running_balance,
                description: item.description,

                /* for requisition */
                requested_qty:
                    _.has(item, "requested_qty") == true
                        ? item.requested_qty
                        : 0,
                remarks: _.has(item, "remarks") == true ? item.remarks : ""
            });
        },
        add_supplier(state, payload) {
            state.suppliers.unshift(payload);
        },
        set_suppliers(state, payload) {
            state.suppliers = [];
            for (var i = payload.length - 1; i >= 0; i--) {
                state.suppliers.unshift(payload[i]);
            }
        },
        set_po_categories(state, payload) {
            state.po_categories = [];
            for (var i = payload.length - 1; i >= 0; i--) {
                state.po_categories.unshift(payload[i]);
            }
        },
        searching_items(state, payload) {
            state.while_searching_items = true;
        },
        set_total_count_items(state, payload) {
            state.total_count_items = payload.count;
        },
        set_items(state, payload) {
            state.items = [];
            for (var i = payload.length - 1; i >= 0; i--) {
                state.items.unshift(payload[i]);
            }
            console.log("done setting items, " + state.items.length);
        },
        add_item(state, payload) {
            let item = payload.item;
            // console.log(item);
            let rsCats = _.filter(state.categories, {
                id: Number(item.cat_id)
            });
            if (rsCats.length) {
                let cat = _.first(rsCats);
                payload.item.cat_name = cat.cat_name;
            }
            let rsUnits = _.filter(state.unit_views, {
                id: Number(item.unit_id)
            });
            if (rsUnits.length) {
                let unit = _.first(rsUnits);
                payload.item.unit_name = unit.unit_name;
            }
            state.items.unshift(payload.item);
        },
        set_cats(state, payload) {
            state.categories = [];
            for (var i = payload.length - 1; i >= 0; i--) {
                state.categories.unshift(payload[i]);
            }
        },
        set_units(state, payload) {
            state.units = [];
            for (var i = payload.length - 1; i >= 0; i--) {
                state.units.unshift(payload[i]);
            }
        }
    },
    getters: {
        STORE_GETTERS_getItemActualBalance: state => item_id => {
            let rsStocks = _.filter(state.actual_stocks, { item_id: item_id });
            if (rsStocks.length) {
                let first = _.first(rsStocks);
                return first.actual_stock;
            } else {
                return 0;
            }
        },
        chunkedRsmi: state => {
            return _.chunk(state.rsmi, 15);
        },
        getTotalOfChunks: state => chunks => {
            return _.reduce(
                chunks,
                function(sum, model) {
                    return sum + model.requested_qty;
                },
                0
            );
        },
        getRsmiDates: state => {
            let d1 = moment(state.rsmi_dates.d1).format("MMM DD, YYYY");
            let d2 = moment(state.rsmi_dates.d2).format("MMM DD, YYYY");
            return d1 + " to " + d2;
        },
        getTotalLocalItems: state => {
            let item = {},
                total = 0;
            for (var i = state.local_items.length - 1; i >= 0; i--) {
                item = state.local_items[i];
                if (Number(item.qty) > 0 && Number(item.unit_cost) > 0) {
                    total += Number(item.qty) * Number(item.unit_cost);
                }
            }
            return total;
        },
        get_current_supplier_name: state => {
            return state.current_supplier;
        }
    }
});
