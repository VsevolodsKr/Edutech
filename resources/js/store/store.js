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
            comments: [],
            latestPlaylists: [],
            latestPlaylistsLoading: false,
            latestPlaylistsError: null,
            courses: [],
            coursesLoading: false,
            coursesError: null,
            teachers: [],
            teachersLoading: false,
            teachersError: null
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
        },
        getLatestPlaylists: (state) => state.latestPlaylists,
        getLatestPlaylistsLoading: (state) => state.latestPlaylistsLoading,
        getLatestPlaylistsError: (state) => state.latestPlaylistsError,
        getCourses: (state) => state.courses,
        getCoursesLoading: (state) => state.coursesLoading,
        getCoursesError: (state) => state.coursesError,
        getTeachers: (state) => state.teachers,
        getTeachersLoading: (state) => state.teachersLoading,
        getTeachersError: (state) => state.teachersError
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
        },
        setLatestPlaylists(state, playlists) {
            state.latestPlaylists = playlists;
        },
        setLatestPlaylistsLoading(state, loading) {
            state.latestPlaylistsLoading = loading;
        },
        setLatestPlaylistsError(state, error) {
            state.latestPlaylistsError = error;
        },
        setCourses(state, courses) {
            state.courses = courses;
        },
        setCoursesLoading(state, loading) {
            state.coursesLoading = loading;
        },
        setCoursesError(state, error) {
            state.coursesError = error;
        },
        setTeachers(state, teachers) {
            state.teachers = teachers;
        },
        setTeachersLoading(state, loading) {
            state.teachersLoading = loading;
        },
        setTeachersError(state, error) {
            state.teachersError = error;
        }
    },

    actions: {
        async loadUserData({ commit, state }) {
            const token = localStorage.getItem('token');
            if (!token) return;

            try {
                commit('setIsLoading', true);
                const response = await axios.get('/api/user', {
                    headers: { Authorization: `Bearer ${token}` }
                });

                // Process user image URL
                let userImage = response.data.image;
                if (userImage) {
                    // Clean up the image path
                    userImage = userImage
                        .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                        .replace(/^\//, '');
                    userImage = `/storage/${userImage}`;
                } else {
                    userImage = '/storage/default-avatar.png';
                }

                const userData = {
                    ...response.data,
                    image: userImage
                };

                commit('setUser', userData);
                
                // Always load fresh stats, even if user data is already loaded
                try {
                    const [likes, bookmarks, comments] = await Promise.all([
                        axios.get(`/api/likes/count_user/${response.data.id}`),
                        axios.get(`/api/bookmarks/count_user/${response.data.id}`),
                        axios.get(`/api/comments/count_user/${response.data.id}`)
                    ]);

                    commit('setDashboardStats', {
                        likes: likes.data?.data || 0,
                        playlists: bookmarks.data?.data || 0,
                        comments: comments.data?.data || 0
                    });
                } catch (statsError) {
                    console.error('Error loading user stats:', statsError);
                    // Set default values if stats loading fails
                    commit('setDashboardStats', {
                        likes: 0,
                        playlists: 0,
                        comments: 0
                    });
                }

                // Only load admin-specific data if user is an admin
                if (response.data.profession) {
                    await Promise.all([
                        this.dispatch('loadPlaylists', response.data.id),
                        this.dispatch('loadContents', response.data.id),
                        this.dispatch('loadComments', response.data.id)
                    ]);
                }
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

        async loadUserStats({ commit }, userId) {
            try {
                const [likes, bookmarks, comments] = await Promise.all([
                    axios.get(`/api/likes/count_user/${userId}`),
                    axios.get(`/api/bookmarks/count_user/${userId}`),
                    axios.get(`/api/comments/count_user/${userId}`)
                ]);

                commit('setDashboardStats', {
                    likes: likes.data?.data || 0,
                    playlists: bookmarks.data?.data || 0,
                    comments: comments.data?.data || 0
                });
            } catch (error) {
                console.error('Error loading user stats:', error);
                // Set default values if stats loading fails
                commit('setDashboardStats', {
                    likes: 0,
                    playlists: 0,
                    comments: 0
                });
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
        },

        async loadLatestPlaylists({ commit, state }) {
            // If we already have latest playlists and they're not too old (e.g., less than 5 minutes old)
            if (state.latestPlaylists.length > 0 && 
                Date.now() - (state.latestPlaylists[0]?.timestamp || 0) < 300000) {
                return;
            }

            try {
                commit('setLatestPlaylistsLoading', true);
                commit('setLatestPlaylistsError', null);
                
                const response = await axios.get('/api/playlists/latest');
                
                if (!Array.isArray(response.data)) {
                    throw new Error('Invalid response format');
                }

                const processedPlaylists = await Promise.all(
                    response.data.map(async (playlist) => {
                        const processed = { ...playlist, timestamp: Date.now() };

                        // Handle thumbnail
                        if (processed.thumb) {
                            const cleanPath = processed.thumb
                                .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                                .replace(/^\//, '');
                            processed.thumb = `/storage/${cleanPath}`;
                        } else {
                            processed.thumb = '/storage/default-thumbnail.png';
                        }

                        // Fetch teacher data if we have teacher_id
                        if (processed.teacher_id) {
                            try {
                                const teacherResponse = await axios.get(`/api/teachers/find/${processed.teacher_id}`);
                                
                                if (teacherResponse.data.data) {
                                    const teacherData = teacherResponse.data.data;
                                    let teacherImage = teacherData.image;
                                    if (teacherImage) {
                                        const cleanTeacherPath = teacherImage
                                            .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                                            .replace(/^\//, '');
                                        teacherImage = `/storage/${cleanTeacherPath}`;
                                    }
                                    
                                    processed.teacher = {
                                        ...teacherData,
                                        name: teacherData.name || 'Unknown Teacher',
                                        image: teacherImage || '/storage/default-avatar.png'
                                    };
                                }
                            } catch (teacherError) {
                                console.error('Error fetching teacher:', teacherError);
                                processed.teacher = {
                                    name: 'Unknown Teacher',
                                    image: '/storage/default-avatar.png'
                                };
                            }
                        } else {
                            processed.teacher = {
                                name: 'Unknown Teacher',
                                image: '/storage/default-avatar.png'
                            };
                        }

                        // Get content count
                        try {
                            const contentResponse = await axios.get(`/api/contents/playlist/${processed.id}/amount`);
                            processed.content_count = contentResponse.data || 0;
                        } catch (contentError) {
                            console.error('Error fetching content count:', contentError);
                            processed.content_count = 0;
                        }

                        return processed;
                    })
                );

                commit('setLatestPlaylists', processedPlaylists.filter(Boolean));
            } catch (err) {
                console.error('Error loading playlists:', err);
                commit('setLatestPlaylistsError', 'Failed to load latest courses. Please try again.');
            } finally {
                commit('setLatestPlaylistsLoading', false);
            }
        },

        async loadCourses({ commit, state }) {
            // If we already have courses and they're not too old (e.g., less than 5 minutes old)
            if (state.courses.length > 0 && 
                Date.now() - (state.courses[0]?.timestamp || 0) < 300000) {
                return;
            }

            try {
                commit('setCoursesLoading', true);
                commit('setCoursesError', null);

                const response = await axios.get('/api/playlists/active');
                if (!Array.isArray(response.data)) {
                    throw new Error('Invalid response format');
                }

                const processedPlaylists = await Promise.all(
                    response.data.map(async (playlist) => {
                        const processed = { ...playlist, timestamp: Date.now() };

                        // Handle thumbnail
                        if (processed.thumb) {
                            const cleanPath = processed.thumb
                                .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                                .replace(/^\//, '');
                            processed.thumb = `/storage/${cleanPath}`;
                        } else {
                            processed.thumb = '/storage/default-thumbnail.png';
                        }

                        // Fetch teacher data if we have teacher_id
                        if (processed.teacher_id) {
                            try {
                                const teacherResponse = await axios.get(`/api/teachers/find/${processed.teacher_id}`);
                                
                                if (teacherResponse.data?.data) {
                                    const teacherData = teacherResponse.data.data;
                                    let teacherImage = teacherData.image;
                                    if (teacherImage) {
                                        const cleanTeacherPath = teacherImage
                                            .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                                            .replace(/^\//, '');
                                        teacherImage = `/storage/${cleanTeacherPath}`;
                                    }
                                    
                                    processed.teacher = {
                                        ...teacherData,
                                        name: teacherData.name || 'Unknown Teacher',
                                        image: teacherImage || '/storage/default-avatar.png'
                                    };
                                }
                            } catch (teacherError) {
                                console.error('Error fetching teacher:', teacherError);
                                processed.teacher = {
                                    name: 'Unknown Teacher',
                                    image: '/storage/default-avatar.png'
                                };
                            }
                        } else {
                            processed.teacher = {
                                name: 'Unknown Teacher',
                                image: '/storage/default-avatar.png'
                            };
                        }

                        // Get content count
                        try {
                            const contentResponse = await axios.get(`/api/contents/playlist/${processed.id}/amount`);
                            processed.content_count = contentResponse.data || 0;
                        } catch (contentError) {
                            console.error('Error fetching content count:', contentError);
                            processed.content_count = 0;
                        }

                        return processed;
                    })
                );

                commit('setCourses', processedPlaylists.filter(Boolean));
            } catch (err) {
                console.error('Error loading courses:', err);
                commit('setCoursesError', 'Failed to load courses. Please try again.');
            } finally {
                commit('setCoursesLoading', false);
            }
        },

        async loadTeachers({ commit, state }) {
            // If we already have teachers and they're not too old (e.g., less than 5 minutes old)
            if (state.teachers.length > 0 && 
                Date.now() - (state.teachers[0]?.timestamp || 0) < 300000) {
                return;
            }

            try {
                commit('setTeachersLoading', true);
                commit('setTeachersError', null);

                const response = await axios.get('/api/teachers/all');
                if (!Array.isArray(response.data)) {
                    throw new Error('Invalid response format');
                }

                const processedTeachers = await Promise.all(
                    response.data.map(async (teacher) => {
                        const processed = { ...teacher, timestamp: Date.now() };

                        // Handle teacher image
                        if (processed.image) {
                            const cleanPath = processed.image
                                .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                                .replace(/^\//, '');
                            processed.image = `/storage/${cleanPath}`;
                        } else {
                            processed.image = '/storage/default-avatar.png';
                        }

                        // Get playlist and content counts in parallel
                        try {
                            const [playlistCount, contentCount] = await Promise.all([
                                axios.get(`/api/playlists/amount/${processed.id}`),
                                axios.get(`/api/contents/amount/${processed.id}`)
                            ]);
                            
                            processed.playlist_count = playlistCount.data.data || 0;
                            processed.content_count = contentCount.data || 0;
                        } catch (err) {
                            console.error(`Error fetching counts for teacher ${processed.id}:`, err);
                            processed.playlist_count = 0;
                            processed.content_count = 0;
                        }

                        return processed;
                    })
                );

                commit('setTeachers', processedTeachers);
            } catch (err) {
                console.error('Error loading teachers:', err);
                commit('setTeachersError', 'Failed to load teachers. Please try again.');
            } finally {
                commit('setTeachersLoading', false);
            }
        },

        async searchCourses({ commit }, searchQuery) {
            try {
                commit('setCoursesLoading', true);
                commit('setCoursesError', null);

                const formData = new FormData();
                formData.append('name', searchQuery.trim());

                const response = await axios.post('/api/playlists/search', formData);
                if (!Array.isArray(response.data)) {
                    throw new Error('Invalid response format');
                }

                const processedPlaylists = await Promise.all(
                    response.data.map(async (playlist) => {
                        const processed = { ...playlist, timestamp: Date.now() };

                        // Handle thumbnail
                        if (processed.thumb) {
                            const cleanPath = processed.thumb
                                .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                                .replace(/^\//, '');
                            processed.thumb = `/storage/${cleanPath}`;
                        } else {
                            processed.thumb = '/storage/default-thumbnail.png';
                        }

                        // Fetch teacher data if we have teacher_id
                        if (processed.teacher_id) {
                            try {
                                const teacherResponse = await axios.get(`/api/teachers/find/${processed.teacher_id}`);
                                
                                if (teacherResponse.data?.data) {
                                    const teacherData = teacherResponse.data.data;
                                    let teacherImage = teacherData.image;
                                    if (teacherImage) {
                                        const cleanTeacherPath = teacherImage
                                            .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                                            .replace(/^\//, '');
                                        teacherImage = `/storage/${cleanTeacherPath}`;
                                    }
                                    
                                    processed.teacher = {
                                        ...teacherData,
                                        name: teacherData.name || 'Unknown Teacher',
                                        image: teacherImage || '/storage/default-avatar.png'
                                    };
                                }
                            } catch (teacherError) {
                                console.error('Error fetching teacher:', teacherError);
                                processed.teacher = {
                                    name: 'Unknown Teacher',
                                    image: '/storage/default-avatar.png'
                                };
                            }
                        } else {
                            processed.teacher = {
                                name: 'Unknown Teacher',
                                image: '/storage/default-avatar.png'
                            };
                        }

                        // Get content count
                        try {
                            const contentResponse = await axios.get(`/api/contents/playlist/${processed.id}/amount`);
                            processed.content_count = contentResponse.data || 0;
                        } catch (contentError) {
                            console.error('Error fetching content count:', contentError);
                            processed.content_count = 0;
                        }

                        return processed;
                    })
                );

                commit('setCourses', processedPlaylists.filter(Boolean));
            } catch (err) {
                console.error('Error searching courses:', err);
                commit('setCoursesError', 'Failed to search courses. Please try again.');
            } finally {
                commit('setCoursesLoading', false);
            }
        },

        async searchTeachers({ commit }, searchQuery) {
            try {
                commit('setTeachersLoading', true);
                commit('setTeachersError', null);

                const formData = new FormData();
                formData.append('name', searchQuery.trim());

                const response = await axios.post('/api/teachers/search', formData);
                if (!Array.isArray(response.data)) {
                    throw new Error('Invalid response format');
                }

                const processedTeachers = await Promise.all(
                    response.data.map(async (teacher) => {
                        const processed = { ...teacher, timestamp: Date.now() };

                        // Handle teacher image
                        if (processed.image) {
                            const cleanPath = processed.image
                                .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                                .replace(/^\//, '');
                            processed.image = `/storage/${cleanPath}`;
                        } else {
                            processed.image = '/storage/default-avatar.png';
                        }

                        // Get playlist and content counts in parallel
                        try {
                            const [playlistCount, contentCount] = await Promise.all([
                                axios.get(`/api/playlists/amount/${processed.id}`),
                                axios.get(`/api/contents/amount/${processed.id}`)
                            ]);
                            
                            processed.playlist_count = playlistCount.data.data || 0;
                            processed.content_count = contentCount.data || 0;
                        } catch (err) {
                            console.error(`Error fetching counts for teacher ${processed.id}:`, err);
                            processed.playlist_count = 0;
                            processed.content_count = 0;
                        }

                        return processed;
                    })
                );

                commit('setTeachers', processedTeachers);
            } catch (err) {
                console.error('Error searching teachers:', err);
                commit('setTeachersError', 'Failed to search teachers. Please try again.');
            } finally {
                commit('setTeachersLoading', false);
            }
        }
    }
})