<template>
    <router-view />
</template>

<script setup>
import { onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth.js';

const authStore = useAuthStore();

onMounted(async () => {
    const token = localStorage.getItem('auth_token');
    if (token) {
        await authStore.fetchUser();
    }
});
</script>
