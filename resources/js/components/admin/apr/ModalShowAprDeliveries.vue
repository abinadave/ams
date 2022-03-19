<template>
    <div>
        <div class="modal fade" id="ModalShowAprDeliveries" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg"  role="document">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Deliveries for APR NO: 
                    <b class="text-primary">{{current_apr_form.apr_no}}</b>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                ...
              </div>
              <div class="modal-footer">
                {{current_apr_form}}
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
              </div>
            </div>
          </div>
        </div>

    </div>
</template>
<style scoped>
    .modal-lg {
      width: 750px;
      /*margin: auto;*/
    }
</style>
<script>
    import {mapState} from 'vuex'
    export default {
        mounted() {
            console.log('Component mounted.')
        },
        computed: {
            ...mapState([
                'current_apr_form'
            ])
        }
    }
</script>
