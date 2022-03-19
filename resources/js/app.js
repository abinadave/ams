
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router'
import VueResource from 'vue-resource'
import Vuex from 'vuex'
import { store } from './store/store.js' 

import ExampleComponent from './components/ExampleComponent'
import InventoryComponent from './components/admin/inventory/inventory'
import PurchaseOrder from './components/admin/purchase-order/PurchaseOrder'
import DeliveryComponent from './components/admin/delivery/delivery'
import RequisitionComponent from './components/admin/requisition/Requisition'
import APRComponent from './components/admin/apr/apr'
import {mapActions, mapState } from 'vuex'
import { BootstrapVue, IconsPlugin } from 'bootstrap-vue'
import ReportsComponent from './components/admin/reports/ReportsComponent.vue'

Vue.use(VueRouter);
Vue.use(VueResource);
Vue.use(Vuex);
Vue.use(BootstrapVue);

// import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example-component', require('./components/ExampleComponent.vue'));

Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#csrf-token').getAttribute('content');

window.router = new VueRouter({
    routes: [
      { 
        path: '/', 
        redirect: '/Inventory'
      },
      {
        path: '/Inventory',
        component: InventoryComponent
      },
      {
        path: '/PurchaseOrder',
        component: PurchaseOrder
      },
      {
        path: '/Delivery',
        component: DeliveryComponent
      },
      {
        path: '/Requisition',
        component: RequisitionComponent
      },
      {
        path: '/APR',
        component: APRComponent
      },
      {
        path: '/Reports',
        component: ReportsComponent
      }
    ]
});

const app = new Vue({
    router,
    store,
    methods: {
        ...mapActions([
           
        ])
    },
    components: {
       
    },
    computed: {
      ...mapState([
         'cloned_html',
         'snapshot_cloned_html'
      ])
    },
    mounted(){
        let self = this;
        window.onafterprint = (event) => {
          console.log('After print');
          // alert('after print')
          if (window.router.currentRoute.path == "/Requisition"){
              // alert('done printing requisition..')
              console.log(self.snapshot_cloned_html);
              // $(self.cloned_html).
              let exist = $(self.cloned_html).length;
              let visible = $(self.cloned_html).is(':visible');
              console.log('elem exist: ' + exist);
              console.log('elem visible: ' + visible);

              setTimeout(function(){
                location.reload();
              }, 1500);
          }
        };
    },
    watch: {
    	
    }
}).$mount('#app');



jQuery(document).ready(function($) {
    $('.nav-uls').find('.nav-item').click(function(event) {
        /* Act on the event */
        $('.nav-uls .nav-item').removeClass('active color1');

        $(this).addClass('active color1');
        // $(this).addClass('color1')
    });
});


