export const global_var_module = {
    namespaced: true,
    state: {
        head_names: []
    },
    mutations: {
        SET_HEADS(state, payload){
            state.head_names = payload;
        },
        UPDATE_ONE_HEAD(state, head){
            console.log('updating one head');
            console.log(head)
            let model = _.find(state.head_names,{id: head.id});
            model.name = head.name;
        }
    },
    actions: {
        dbFetchHeadNames({commit}){
            return new Promise((resolve, reject) => {
                axios
                    .get('/fetch/head_names')
                    .then((resp) => {
                        commit('SET_HEADS', resp.data);
                        resolve(resp);
                    }).catch((err) => {
                        resolve(err.response);
                    });
            });
        },

        dbUpdateOrAddPropertyCustodian({commit}, payload){
            return new Promise((resolve, reject) => {
                axios.post('/add_or_update/head_name', {
                    position: payload.position,
                    name: payload.name
                })
                    .then(function (response) {
                        if (response.status === 200) {
                            let json = response.data;
                            if(json.type == 'update'){
                                commit('UPDATE_ONE_HEAD', json.resp_head);
                            }
                            resolve(json);
                        };
                        resolve(response);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            });
        }
    },

    getters: {
        getHeadName: (state) => (position) => {
            const self = this;
            let rs = _.filter(state.head_names, { position: position });
            if (rs.length) {
                let head = _.first(rs);
                return head.name;
            }else {
                return 'not-found: ' + position + ',Please try reloading page..'; 
            }
        },
    }
};