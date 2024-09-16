import { createStore } from 'vuex'

export default createStore({
    state(){
        return {
            showSidebar: true
        }
    },

    getters: {
        getShowSidebar: function (state){
            return state.showSidebar
        }
    },

    mutations: {
        setShowSidebar: function (state, newValue){
            state.showSidebar = newValue
        }
    }
})