import axios from 'axios'
import { createStore } from 'vuex'

export default createStore({
    state(){
        return {
            showSidebar: true,
            user: null,
            searchPlaylist: '',
            isLoading: false,
            dashboardStats: {
                playlists: 0,
                contents: 0,
                likes: 0,
                comments: 0,
                engagement: null,
                popularContents: []
            },
            playlists: [],
            contents: [],
            comments: []
        }
    },

    getters: {
        getShowSidebar: function (state){
            return state.showSidebar
        },
        getUser: function (state){
            return state.user
        },
        getSearchPlaylist: function (state){
            return state.searchPlaylist
        },
        getIsLoading: function (state){
            return state.isLoading
        },
        getDashboardStats: function (state){
            return state.dashboardStats
        },
        getPlaylists: function (state){
            return state.playlists
        },
        getContents: function (state){
            return state.contents
        },
        getComments: function (state){
            return state.comments
        }
    },

    mutations: {
        setShowSidebar: function (state, newValue){
            state.showSidebar = newValue
        },
        setUser: function (state, newUser){
            state.user = newUser
        },
        setSearchPlaylist: function (state, newSearchPlaylist){
            state.searchPlaylist = newSearchPlaylist
        },
        setIsLoading: function (state, value){
            state.isLoading = value
        },
        setDashboardStats: function (state, stats){
            state.dashboardStats = stats
        },
        setPlaylists: function (state, playlists){
            state.playlists = playlists
        },
        setContents: function (state, contents){
            state.contents = contents
        },
        setComments: function (state, comments){
            state.comments = comments
        }
    },

    actions: {
        async loadUserData({ commit, state }) {
            if (state.user) return;

            const token = localStorage.getItem('token');
            if (!token) return;

            try {
                commit('setIsLoading', true);
                const response = await axios.get('/api/user', {
                    headers: { Authorization: `Bearer ${token}` }
                });

                if (!response.data.profession) {
                    throw new Error('User does not have admin access');
                }

                commit('setUser', response.data);
                
                // Load all data in parallel
                await Promise.all([
                    this.dispatch('loadDashboardStats', response.data.id),
                    this.dispatch('loadPlaylists', response.data.id),
                    this.dispatch('loadContents', response.data.id),
                    this.dispatch('loadComments', response.data.id)
                ]);
            } catch (error) {
                console.error('Error loading user data:', error);
                if (error.response?.status === 401) {
                    localStorage.removeItem('token');
                    commit('setUser', null);
                }
                throw error;
            } finally {
                commit('setIsLoading', false);
            }
        },

        async loadDashboardStats({ commit }, userId) {
            try {
                const [playlists, contents, likes, comments, engagement, popular] = await Promise.all([
                    axios.get(`/api/playlists/amount/${userId}`),
                    axios.get(`/api/contents/amount/${userId}`),
                    axios.get(`/api/likes/count_teacher/${userId}`),
                    axios.get(`/api/comments/count_teacher/${userId}`),
                    axios.get(`/api/engagement/teacher/${userId}`),
                    axios.get(`/api/contents/popular/${userId}`)
                ]);

                commit('setDashboardStats', {
                    playlists: playlists.data.data || 0,
                    contents: contents.data || 0,
                    likes: likes.data.data || 0,
                    comments: comments.data.data || 0,
                    engagement: engagement.data,
                    popularContents: popular.data
                });
            } catch (error) {
                console.error('Error loading dashboard stats:', error);
                throw error;
            }
        },

        async loadPlaylists({ commit }, userId) {
            try {
                const response = await axios.get(`/api/playlists/teacher_playlists/${userId}`);
                if (response.data && response.data.data) {
                    commit('setPlaylists', response.data.data);
                }
            } catch (error) {
                console.error('Error loading playlists:', error);
                throw error;
            }
        },

        async loadContents({ commit }, userId) {
            try {
                const response = await axios.get(`/api/contents/${userId}`);
                commit('setContents', response.data);
            } catch (error) {
                console.error('Error loading contents:', error);
                throw error;
            }
        },

        async loadComments({ commit }, userId) {
            try {
                const response = await axios.get(`/api/comments/teacher/${userId}`);
                const processedComments = response.data.comments.map((comment, index) => ({
                    ...comment,
                    user: {
                        ...response.data.users[index],
                        image: response.data.users[index]?.image 
                            ? `/storage/${response.data.users[index].image.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')}`
                            : '/storage/default-avatar.png'
                    },
                    content: response.data.contents[index]
                })).sort((a, b) => new Date(b.date) - new Date(a.date));
                
                commit('setComments', processedComments);
            } catch (error) {
                console.error('Error loading comments:', error);
                throw error;
            }
        }
    }
})