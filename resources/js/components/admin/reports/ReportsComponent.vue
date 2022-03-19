<template>
    <div>
        <b-card no-body>
            <b-tabs pills card>
            <b-tab title="Requisitions" active>
                <b-card-text>
                    <requisition-report></requisition-report>
                </b-card-text>
            </b-tab>
            <b-tab title="Inventory"><b-card-text>Tab contents 2</b-card-text></b-tab>
            <b-tab title="Purchase Orders"><b-card-text>Tab contents 2</b-card-text></b-tab>
            </b-tabs>
        </b-card>
    </div>
</template>

<script>
    import RequisitionReport from "./RequisitionReport"
    export default {
        mounted() {
            
        },
        components: {
            RequisitionReport
        }
    }
</script>
