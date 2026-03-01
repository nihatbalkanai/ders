<template>
    <div class="profile-page">
        <div class="page-header fade-in">
            <h1>Profil<span class="gradient-text">im</span></h1>
        </div>

        <div class="profile-card glass-card slide-up">
            <div class="profile-avatar">
                <span>{{ authStore.userInitials }}</span>
            </div>
            <div class="profile-info">
                <h2>{{ authStore.user?.name }}</h2>
                <span class="badge badge-primary">{{ authStore.user?.grade_level }}. Sınıf</span>
                <p class="profile-email">{{ authStore.user?.email }}</p>
            </div>
        </div>

        <div class="profile-form glass-card slide-up" style="animation-delay: 0.1s">
            <h3>Profil Bilgilerini Güncelle</h3>
            <form @submit.prevent="updateProfile">
                <div class="form-group">
                    <label class="input-label">Ad Soyad</label>
                    <input v-model="form.name" type="text" class="input-field" />
                </div>
                <div class="form-group">
                    <label class="input-label">Sınıf Seviyesi</label>
                    <div class="grade-selector">
                        <button v-for="g in [5,6,7,8]" :key="g" type="button" :class="['grade-btn', { 'grade-btn-active': form.grade_level === g }]" @click="form.grade_level = g">
                            {{ g }}. Sınıf
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-primary" :disabled="saving">
                    {{ saving ? 'Kaydediliyor...' : 'Kaydet' }}
                </button>
                <span v-if="saved" class="save-success">✓ Kaydedildi</span>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth.js';
import api from '@/api.js';

const authStore = useAuthStore();
const saving = ref(false);
const saved = ref(false);

const form = reactive({
    name: '',
    grade_level: 5,
});

onMounted(() => {
    if (authStore.user) {
        form.name = authStore.user.name;
        form.grade_level = authStore.user.grade_level;
    }
});

const updateProfile = async () => {
    saving.value = true;
    saved.value = false;
    try {
        const res = await api.put('/profile', form);
        authStore.user = res.data.user;
        saved.value = true;
        setTimeout(() => saved.value = false, 3000);
    } catch (e) {
        alert(e.response?.data?.message || 'Güncelleme başarısız.');
    } finally {
        saving.value = false;
    }
};
</script>

<style scoped>
.profile-page { max-width: 600px; }
.page-header { margin-bottom: 2rem; }
.page-header h1 { font-size: 1.75rem; font-weight: 800; color: white; }
.profile-card { display: flex; align-items: center; gap: 1.5rem; padding: 2rem; margin-bottom: 1.5rem; }
.profile-avatar { width: 72px; height: 72px; border-radius: 18px; background: linear-gradient(135deg, var(--color-primary-600), var(--color-primary-400)); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 800; color: white; flex-shrink: 0; }
.profile-info h2 { font-size: 1.25rem; font-weight: 800; color: white; margin-bottom: 0.5rem; }
.profile-email { font-size: 0.8rem; color: var(--color-dark-500); margin-top: 0.25rem; }
.profile-form { padding: 2rem; }
.profile-form h3 { font-size: 1rem; font-weight: 700; color: white; margin-bottom: 1.5rem; }
.profile-form form { display: flex; flex-direction: column; gap: 1.25rem; }
.form-group { display: flex; flex-direction: column; }
.grade-selector { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; }
.grade-btn { padding: 0.625rem; border-radius: 10px; border: 1px solid rgba(148, 163, 184, 0.2); background: rgba(15, 23, 42, 0.6); color: var(--color-dark-400); font-size: 0.8rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; font-family: var(--font-sans); }
.grade-btn:hover { border-color: var(--color-primary-500); color: var(--color-primary-400); }
.grade-btn-active { background: rgba(108, 71, 255, 0.15); border-color: var(--color-primary-500); color: var(--color-primary-400); }
.save-success { color: var(--color-success-400); font-size: 0.85rem; font-weight: 600; margin-left: 1rem; }
</style>
