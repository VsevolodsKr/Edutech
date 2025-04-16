<template>
    <router-view></router-view>
</template>

<script setup>
import { onMounted } from 'vue';
import store from '../store/store';

onMounted(async () => {
    try {
        await store.dispatch('loadUserData');
    } catch (error) {
        console.error('Failed to load user data:', error);
    }
});
</script>