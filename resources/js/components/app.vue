<template>
    <router-view></router-view>
</template>

<script setup>
import { onMounted } from 'vue';
import store from '../store/store';

onMounted(async () => {
    try {
        // Only load user data if cache is invalid
        if (!store.getters.isCacheValid) {
            await store.dispatch('loadUserData');
        }
    } catch (error) {
        console.error('Failed to load user data:', error);
    }
});
</script>