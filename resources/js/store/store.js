import axios from 'axios'
import { createStore } from 'vuex'

export default createStore({
    state(){
        return {
            showSidebar: true,
            user: null,
            userLoaded: false,
            searchPlaylist: '',
            isLoading: false,
            dashboardStats: {
                playlists: 0,
                contents: 0,
                likes: 0,
                comments: 0,
                engagement: {
                    labels: [],
                    likes: [],
                    comments: []
                },
                popularContents: []
            },
            dashboardStatsLoaded: false,
            isLoadingDashboard: false,
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
        getTeachersError: (state) => state.teachersError,
        isUserLoaded: state => state.userLoaded
    },

    mutations: {
        setShowSidebar: function (state, value){
            state.showSidebar = value
        },
        setUser: function (state, value){
            state.user = value
        },
        setUserLoaded: function (state, value){
            state.userLoaded = value
        },
        setSearchPlaylist: function (state, value){
            state.searchPlaylist = value
        },
        setIsLoading: function (state, value){
            state.isLoading = value
        },
        setDashboardStats: function (state, value) {
            if (value === null) {
                state.dashboardStats = {
                    playlists: 0,
                    contents: 0,
                    likes: 0,
                    comments: 0,
                    engagement: {
                        labels: [],
                        likes: [],
                        comments: []
                    },
                    popularContents: []
                };
            } else {
                state.dashboardStats = {
                    ...state.dashboardStats,
                    ...value
                };
            }
        },
        setDashboardLoading: function (state, value){
            state.isLoadingDashboard = value
        },
        setPlaylists: function (state, value){
            state.playlists = value
        },
        setContents: function (state, value){
            state.contents = value
        },
        setComments: function (state, value){
            state.comments = value
        },
        clearState: function (state) {
            state.user = null;
            state.userLoaded = false;
            state.dashboardStats = {
                playlists: 0,
                contents: 0,
                likes: 0,
                comments: 0,
                engagement: {
                    labels: [],
                    likes: [],
                    comments: []
                },
                popularContents: []
            };
            state.dashboardStatsLoaded = false;
            state.isLoadingDashboard = false;
            state.playlists = [];
            state.contents = [];
            state.comments = [];
            state.latestPlaylists = [];
            state.latestPlaylistsError = null;
        },
        initializeDashboardStats: function(state, stats) {
            if (stats) {
                state.dashboardStats = stats;
                state.dashboardStatsLoaded = true;
            }
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
        },
        updateUserStats(state, { type, value }) {
            if (state.dashboardStats) {
                state.dashboardStats[type] = value;
            }
        },
        incrementStat(state, type) {
            if (state.dashboardStats && typeof state.dashboardStats[type] === 'number') {
                state.dashboardStats[type]++;
            }
        },
        decrementStat(state, type) {
            if (state.dashboardStats && typeof state.dashboardStats[type] === 'number' && state.dashboardStats[type] > 0) {
                state.dashboardStats[type]--;
            }
        }
    },

    actions: {
        async clearAndLoadUserData({ commit, dispatch }) {
            commit('clearState');
            commit('setIsLoading', true);
            
            try {
                await dispatch('loadUserData');
                
                const user = this.state.user;
                if (user?.encrypted_id) {
                    await dispatch('loadUserStats', user.encrypted_id);
                }
            } catch (error) {
                console.error('Error in clearAndLoadUserData:', error);
                commit('setUser', null);
            } finally {
                commit('setIsLoading', false);
            }
        },
        async loadUserData({ commit, dispatch, state }) {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    console.log('No token found, clearing user data');
                    commit('setUser', null);
                    commit('setUserLoaded', true);
                    return;
                }

                console.log('Loading user data with token');
                const response = await axios.get('/api/user', {
                    headers: { 
                        Authorization: `Bearer ${token}`,
                        Accept: 'application/json'
                    }
                });

                if (!response.data) {
                    console.error('No user data received from API');
                    commit('setUser', null);
                    commit('setUserLoaded', true);
                    return;
                }

                const userData = response.data;
                console.log('Received user data:', userData);

                // Store user data
                commit('setUser', userData);
                commit('setUserLoaded', true);

                // Load user stats if we have encrypted_id
                if (userData.encrypted_id) {
                    console.log('Loading stats for user with encrypted_id:', userData.encrypted_id);
                    await dispatch('loadUserStats', userData.encrypted_id);
                } else {
                    console.error('User data missing encrypted_id:', userData);
                }
            } catch (error) {
                console.error('Error in loadUserData:', error);
                commit('setUser', null);
                commit('setUserLoaded', true);
            } finally {
                commit('setIsLoading', false);
            }
        },

        async loadDashboardStats({ commit, state }, teacherId) {
            // Prevent multiple simultaneous loads
            if (state.isLoadingDashboard) {
                console.log('Dashboard stats already loading, skipping...');
                return;
            }

            try {
                commit('setDashboardLoading', true);

                const token = localStorage.getItem('token');
                const headers = { Authorization: `Bearer ${token}` };
                
                const [playlistsAmount, contentsAmount, commentsAmount, engagementData, popularContents] = await Promise.all([
                    axios.get(`/api/playlists/amount/${teacherId}`, { headers }),
                    axios.get(`/api/contents/amount/${teacherId}`, { headers }),
                    axios.get(`/api/comments/count_teacher/${teacherId}`, { headers }),
                    axios.get(`/api/engagement/teacher/${teacherId}`, { headers }),
                    axios.get(`/api/contents/popular/${teacherId}`, { headers })
                ]);

                const stats = {
                    playlists: playlistsAmount.data?.data || 0,
                    contents: contentsAmount.data?.data || 0,
                    comments: commentsAmount.data?.data || 0,
                    popularContents: popularContents.data || [],
                    engagement: engagementData.data || {
                        labels: [],
                        likes: [],
                        comments: []
                    }
                };

                commit('setDashboardStats', stats);
            } catch (error) {
                console.error('Error loading dashboard stats:', error);
                // Don't reset stats on error, just keep existing data
            } finally {
                commit('setDashboardLoading', false);
            }
        },

        async loadUserStats({ commit, state }, encryptedId) {
            if (!encryptedId) {
                console.error('No encrypted ID provided for loadUserStats');
                return;
            }

            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    console.error('No token available for loadUserStats');
                    return;
                }

                const headers = { 
                    Authorization: `Bearer ${token}`,
                    Accept: 'application/json'
                };

                console.log('Loading stats for user:', encryptedId);

                // Make API calls one by one for better error tracking
                const likesRes = await axios.get(`/api/likes/count_user/${encryptedId}`, { headers });
                console.log('Likes response:', likesRes.data);

                const bookmarksRes = await axios.get(`/api/bookmarks/count_user/${encryptedId}`, { headers });
                console.log('Bookmarks response:', bookmarksRes.data);

                const commentsRes = await axios.get(`/api/comments/count_user/${encryptedId}`, { headers });
                console.log('Comments response:', commentsRes.data);

                // Extract the actual count values
                const stats = {
                    likes: parseInt(likesRes.data?.data) || 0,
                    playlists: parseInt(bookmarksRes.data?.data) || 0,
                    comments: parseInt(commentsRes.data?.data) || 0
                };

                console.log('Setting dashboard stats:', stats);
                commit('setDashboardStats', stats);
                return stats;
            } catch (error) {
                console.error('Error loading user stats:', error);
                if (error.response) {
                    console.error('Error response:', error.response.data);
                }
            }
        },

        async loadPlaylists({ commit }, userId) {
            try {
                const response = await axios.get(`/api/playlists/teacher_playlists/${userId}`);
                const playlists = response.data?.data || [];
                
                // Process playlists to ensure correct image paths
                const processedPlaylists = playlists.map(playlist => ({
                    ...playlist,
                    thumb: playlist.thumb ? `/storage/${playlist.thumb.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')}` : '/storage/default-thumbnail.png'
                }));
                
                commit('setPlaylists', processedPlaylists);
            } catch (error) {
                console.error('Error loading playlists:', error);
                commit('setPlaylists', []);
            }
        },

        async loadContents({ commit }, userId) {
            try {
                const token = localStorage.getItem('token');
                if (!token) {
                    throw new Error('No authentication token found');
                }

                const headers = { Authorization: `Bearer ${token}` };
                const response = await axios.get(`/api/contents/${userId}`, { headers });
                
                if (!response.data) {
                    commit('setContents', []);
                    return;
                }

                const contents = Array.isArray(response.data) ? response.data : [];
                
                // Process contents to ensure correct video and thumbnail paths
                const processedContents = contents.map(content => {
                    let processedContent = { ...content };

                    // Handle video URL
                    if (content.video) {
                        if (content.video_source_type === 'youtube') {
                            processedContent.video = content.video; // Keep YouTube URLs as is
                        } else {
                            // Process local video path
                            let videoPath = content.video;
                            videoPath = videoPath.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '');
                            videoPath = videoPath.replace(/^\/+/, '');
                            videoPath = videoPath.split('/').filter(Boolean).join('/');
                            processedContent.video = `/storage/${videoPath}`;
                        }
                    }

                    // Handle thumbnail
                    if (content.thumb) {
                        // Check if it's a YouTube thumbnail
                        if (content.thumb.includes('youtube.com') || content.thumb.includes('youtu.be')) {
                            processedContent.thumb = content.thumb; // Keep YouTube thumbnail URLs as is
                        } else {
                            // Process local thumbnail path
                            let thumbPath = content.thumb;
                            thumbPath = thumbPath.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '');
                            thumbPath = thumbPath.replace(/^\/+/, '');
                            thumbPath = thumbPath.split('/').filter(Boolean).join('/');
                            processedContent.thumb = `/storage/${thumbPath}`;
                        }
                    } else {
                        processedContent.thumb = '/storage/default-thumbnail.png';
                    }

                    return processedContent;
                });
                
                commit('setContents', processedContents);
            } catch (error) {
                console.error('Error loading contents:', error);
                commit('setContents', []); // Set empty array on error
            }
        },

        async loadComments({ commit }, teacherId) {
            if (!teacherId) {
                console.error('Teacher ID is required for loading comments');
                commit('setComments', []);
                return;
            }

            try {
                const token = localStorage.getItem('token');
                const headers = { Authorization: `Bearer ${token}` };
                const response = await axios.get(`/api/comments/teacher/${teacherId}`, { headers });
                
                if (!response.data?.comments) {
                    commit('setComments', []);
                    return;
                }

                const processedComments = response.data.comments.map((comment, index) => ({
                    ...comment,
                    user: response.data.users[index] ? {
                        ...response.data.users[index],
                        image: response.data.users[index].image 
                            ? `/storage/${response.data.users[index].image.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')}`
                            : '/storage/default-avatar.png'
                    } : null,
                    content: response.data.contents[index] ? {
                        ...response.data.contents[index],
                        thumb: response.data.contents[index].thumb 
                            ? `/storage/${response.data.contents[index].thumb.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')}`
                            : '/storage/default-thumbnail.png'
                    } : null
                })).sort((a, b) => new Date(b.date) - new Date(a.date));
                
                commit('setComments', processedComments);
            } catch (error) {
                console.error('Error loading comments:', error);
                commit('setComments', []);
            }
        },

        async loadLatestPlaylists({ commit, state }) {
            try {
                commit('setLatestPlaylistsLoading', true);
                commit('setLatestPlaylistsError', null);

                console.log('Fetching latest playlists...');
                const response = await axios.get('/api/playlists/latest');
                
                if (!response.data || !Array.isArray(response.data)) {
                    console.error('Invalid response format:', response.data);
                    throw new Error('Invalid response format');
                }

                console.log('Raw playlists data:', response.data);

                const processedPlaylists = await Promise.all(
                    response.data
                        .filter(playlist => playlist.teacher && playlist.teacher.status === 'aktīvs')
                        .map(async (playlist) => {
                            try {
                                const processed = { 
                                    ...playlist,
                                    timestamp: Date.now(),
                                    encrypted_id: playlist.encrypted_id || playlist.id
                                };

                                // Handle thumbnail
                                if (processed.thumb) {
                                    if (processed.thumb.startsWith('http')) {
                                        // Keep external URLs as is
                                        processed.thumb = processed.thumb;
                                    } else {
                                        // Clean and format local paths
                                        const cleanPath = processed.thumb
                                            .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                                            .replace(/^\//, '');
                                        processed.thumb = `/storage/${cleanPath}`;
                                    }
                                } else {
                                    processed.thumb = '/storage/default-thumbnail.png';
                                }

                                // Format teacher data if available
                                if (processed.teacher) {
                                    let teacherImage = processed.teacher.formatted_image || processed.teacher.image;
                                    
                                    if (teacherImage) {
                                        if (teacherImage.startsWith('http')) {
                                            // Keep external URLs as is
                                            teacherImage = teacherImage;
                                        } else {
                                            // Clean and format local paths
                                            const cleanTeacherPath = teacherImage
                                                .replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')
                                                .replace(/^\//, '');
                                            teacherImage = `/storage/${cleanTeacherPath}`;
                                        }
                                    }
                                    
                                    processed.teacher = {
                                        ...processed.teacher,
                                        name: processed.teacher.name || 'Unknown Teacher',
                                        image: teacherImage || '/storage/default-avatar.png'
                                    };
                                } else {
                                    processed.teacher = {
                                        name: 'Unknown Teacher',
                                        image: '/storage/default-avatar.png'
                                    };
                                }

                                // Get content count
                                try {
                                    const contentResponse = await axios.get(`/api/contents/playlist/${processed.encrypted_id}/amount`);
                                    processed.content_count = contentResponse.data || 0;
                                    console.log('Content count for playlist:', processed.content_count);
                                } catch (contentError) {
                                    console.error('Error fetching content count:', contentError);
                                    processed.content_count = 0;
                                }

                                return processed;
                            } catch (error) {
                                console.error('Error processing playlist:', error);
                                return null;
                            }
                        })
                );

                const validPlaylists = processedPlaylists.filter(Boolean);
                console.log('Processed playlists:', validPlaylists);

                commit('setLatestPlaylists', validPlaylists);
            } catch (err) {
                console.error('Error loading latest playlists:', err);
                commit('setLatestPlaylistsError', 'Failed to load latest playlists. Please try again.');
                commit('setLatestPlaylists', []);
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
                        const processed = { 
                            ...playlist, 
                            timestamp: Date.now(),
                            encrypted_id: playlist.encrypted_id || playlist.id
                        };

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
                                const teacherResponse = await axios.get(`/api/teachers/find_teacher/${processed.teacher_id}`);
                                
                                if (teacherResponse.data?.data) {
                                    const teacherData = teacherResponse.data.data;
                                    let teacherImage = teacherData.formatted_image || teacherData.image;
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
                            const contentResponse = await axios.get(`/api/contents/playlist/${processed.encrypted_id}/amount`);
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

                const processedTeachers = response.data.map(teacher => {
                    // Handle the image path
                    let imagePath = teacher.formatted_image || teacher.image;
                    if (imagePath) {
                        // Remove any storage/app/public prefixes
                        imagePath = imagePath.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '');
                        // Ensure it starts with /storage/
                        imagePath = '/storage/' + imagePath;
                    } else {
                        imagePath = '/storage/default-avatar.png';
                    }

                    return { 
                        ...teacher, 
                        timestamp: Date.now(),
                        image: imagePath
                    };
                });

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
                                const teacherResponse = await axios.get(`/api/teachers/find_teacher/${processed.teacher_id}`);
                                
                                if (teacherResponse.data?.data) {
                                    const teacherData = teacherResponse.data.data;
                                    let teacherImage = teacherData.formatted_image || teacherData.image;
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
                            const contentResponse = await axios.get(`/api/contents/playlist/${processed.encrypted_id}/amount`);
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

                const response = await axios.get(`/api/teachers/search?query=${encodeURIComponent(searchQuery)}`);
                if (!Array.isArray(response.data)) {
                    commit('setTeachers', []);
                    return;
                }

                const processedTeachers = response.data.map(teacher => {
                    // Handle the image path
                    let imagePath = teacher.formatted_image || teacher.image;
                    if (imagePath) {
                        // Remove any storage/app/public prefixes
                        imagePath = imagePath.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '');
                        // Ensure it starts with /storage/
                        imagePath = '/storage/' + imagePath;
                    } else {
                        imagePath = '/storage/default-avatar.png';
                    }

                    return { 
                        ...teacher, 
                        timestamp: Date.now(),
                        image: imagePath
                    };
                });

                commit('setTeachers', processedTeachers);
            } catch (err) {
                console.error('Error searching teachers:', err);
                commit('setTeachers', []);
            } finally {
                commit('setTeachersLoading', false);
            }
        },

        // Add new action to refresh stats after user actions
        async refreshUserStats({ dispatch, state }) {
            if (state.user?.encrypted_id) {
                await dispatch('loadUserStats', state.user.encrypted_id);
            }
        }
    }
})