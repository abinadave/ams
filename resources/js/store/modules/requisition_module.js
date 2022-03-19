export const requisition_module = {
    namespaced: true,
    state: {
        autocomplete_division: [],
        autocomplete_office: []
    },
    mutations: {
        SET_REQUISITION_AUTOCOMPLETE_DIVISION(state, payload){
            state.autocomplete_division = payload;
        },
        SET_AUTOCOMPLETE_PROP(state, payload){
            state['autocomplete_'+payload.prop] = payload.mapValues;
        }
    },
    actions: {
        dbSearchAutoCompleteForKey({commit, state}, payload){
            return new Promise((resolve, reject) => {
                axios
                    .post('/autocomplete/requisition_form', payload)
                    .then((response) => {
                        resolve(response);
                    }).catch((err) => {
                        console.log(err.response);
                    });
            });
        }
    },
    getters: {}
};