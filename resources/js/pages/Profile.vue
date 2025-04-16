<template>
  <!-- No changes to template section -->
</template>

<script setup>
import { useRouter } from 'vue-router';
import { useWindowSize } from '@vueuse/core';
import { ref, computed, onMounted, watch } from 'vue';
import { useStore } from 'vuex';

const router = useRouter();
const { width } = useWindowSize();
const store = useStore();

// State
const isLoading = ref(false);
const error = ref(null);

// Computed
const showSidebar = computed(() => store.getters.getShowSidebar);
const user = computed(() => {
    const storedUser = store.getters.getUser;
    if (!storedUser) return null;

    // Ensure image URL is properly formatted
    const imageUrl = storedUser.image ? 
        (storedUser.image.startsWith('http') ? 
            storedUser.image : 
            `${window.location.origin}/storage/${storedUser.image.replace(/^\/?(storage\/app\/public\/|storage\/|\/storage\/)/g, '')}`) :
        `${window.location.origin}/storage/default-avatar.png`;

    return {
        ...storedUser,
        image: imageUrl
    };
});
const dashboardStats = computed(() => store.getters.getDashboardStats);
const sectionClasses = computed(() => [
    (showSidebar.value && width.value > 1180) ? 'pl-[22rem]' : 
    (!showSidebar.value || (showSidebar.value && width.value < 1180)) ? 'pl-[2rem]' : '',
    'pt-[2rem] pr-[1rem] bg-background min-h-[calc(127.5vh-20rem)] [@media(max-width:550px)]:pl-[.5rem] [@media(max-width:550px)]:pr-[.5rem]'
]);

// Methods
const loadUserData = async () => {
    try {
        isLoading.value = true;
        error.value = null;

        // Use store data if available
        if (user.value) {
            // Still load fresh stats even if user data is available
            await store.dispatch('loadUserData');
            return;
        }

        // Load user data from store
        await store.dispatch('loadUserData');
    } catch (err) {
        console.error('Error loading user data:', err);
        error.value = 'Failed to load profile data';
    } finally {
        isLoading.value = false;
    }
};

// Watchers
watch(() => user.value?.id, async (newId) => {
    if (newId) {
        await store.dispatch('loadUserData');
    }
});

// Lifecycle
onMounted(async () => {
    await loadUserData();
});
</script>

<style>
  /* No changes to style section */
</style> 