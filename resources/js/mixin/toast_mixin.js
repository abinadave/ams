export const toast_mixin = {
    data() {
        return {
            toastCount: 0
        }
    },
    methods: {
        makeToast(payload) {
            this.toastCount++
            this.$bvToast.toast(payload.content, {
                title: payload.title,
                autoHideDelay: 5000,
                appendToast: payload.append,
                variant: payload.variant,
                toaster: payload.toaster
            });
        }
    }
}