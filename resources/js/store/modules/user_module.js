export const user_module = {
    namespaced: true,
    state: {
        user: {}
    },
    mutations: {
        SET_CURRENT_USER(state, payload) {
            state.user = payload;
        }
    },
    actions: {
        dbGetCurrentUser({commit}){
            return new Promise((resolve, reject) => {
                axios.get('/current/user')
                .then((response) => {
                    if(response.status == 200){
                        commit('SET_CURRENT_USER', response.data);
                    }
                }).catch((err) => {
                    
                });
            });
        }
    },
    getters: {}
};