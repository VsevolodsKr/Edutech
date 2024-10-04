import axios from 'axios'
import { createStore } from 'vuex'

export default createStore({
    state(){
        return {
            showSidebar: true,
            user: {
                data: {},
                tokrn: localStorage.getItem('token')
            }
        }
    },

    getters: {
        getShowSidebar: function (state){
            return state.showSidebar
        },
    },

    mutations: {
        setShowSidebar: function (state, newValue){
            state.showSidebar = newValue
        },
    }
})