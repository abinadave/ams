
<template>
    <div>
        <b-row align-h="start">
            <b-col cols="3">
                <b-list-group>
                    <b-list-group-item>
                        <router-link to="/foo">Go to Foo</router-link>
                    </b-list-group-item>
                    <b-list-group-item :active="true">Per Month</b-list-group-item>
                    <b-list-group-item>Daily</b-list-group-item>
                    <b-list-group-item>Yearly</b-list-group-item>
                </b-list-group>
            </b-col>
            <b-col cols="12">Output</b-col>
        </b-row>
    </div>
</template>

<script>
export default {};
</script>

<style>

</style>