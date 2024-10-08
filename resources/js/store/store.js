import axios from 'axios'
import { createStore } from 'vuex'

export default createStore({
    state(){
        return {
            showSidebar: true,
            user: {
                data: {},
                token: localStorage.getItem('token')
            }
        }
    },

    getters: {
        getShowSidebar: function (state){
            return state.showSidebar
        },
        getUser: function (state){
            return state.user
        }
    },

    mutations: {
        setShowSidebar: function (state, newValue){
            state.showSidebar = newValue
        },
        setUser: function (state, newUser){
            state.user.data = newUser
        }
    }
})