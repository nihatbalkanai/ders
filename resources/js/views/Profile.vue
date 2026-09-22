<template>
    <div class="profile-page">
        <div class="page-header fade-in">
            <h1 class="profile-title">Profil<span class="gradient-text">im</span></h1>
            <p class="page-subtitle">Hesap bilgilerini görüntüle ve düzenle</p>
        </div>

        <!-- Profile Card -->
        <div class="profile-card slide-up">
            <div class="profile-card-bg"></div>
            <div class="profile-card-content">
                <div class="profile-avatar">
                    <span>{{ authStore.userInitials }}</span>
                </div>
                <div class="profile-info">
                    <h2>{{ authStore.user?.name }}</h2>
                    <p class="profile-email">{{ authStore.user?.email }}</p>
                    <div class="profile-badges">
                        <span class="grade-badge">{{ authStore.user?.grade_level }}. Sınıf</span>
                        <span class="role-badge">🧠 Öğrenci</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="profile-stats slide-up" style="animation-delay: 0.1s">
            <div class="stat-card">
                <span class="stat-icon">📝</span>
                <span class="stat-value">{{ stats.questions }}</span>
                <span class="stat-label">Yüklenen Soru</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon">📋</span>
                <span class="stat-value">{{ stats.tests }}</span>
                <span class="stat-label">Oluşturulan Test</span>
            </div>
            <div class="stat-card">
                <span class="stat-icon">🏆</span>
                <span class="stat-value">%{{ stats.avg_score }}</span>
                <span class="stat-label">Ort. Başarı</span>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="profile-form-card slide-up" style="animation-delay: 0.2s">
            <div class="form-header">
                <h3>Profil Bilgilerini Güncelle</h3>
                <span v-if="saved" class="save-success">✓ Kaydedildi!</span>
            </div>
            <form @submit.prevent="updateProfile">
                <div class="form-group">
                    <label class="form-label">Ad Soyad</label>
                    <input v-model="form.name" type="text" class="form-input" placeholder="Adınızı girin" />
                </div>
                <div class="form-group">
                    <label class="form-label">Sınıf Seviyesi</label>
                    <div class="grade-selector">
                        <button v-for="g in [5,6,7,8]" :key="g" type="button" :class="['grade-btn', { 'grade-btn-active': form.grade_level === g }]" @click="form.grade_level = g">
                            <span class="grade-number">{{ g }}</span>
                            <span class="grade-text">. Sınıf</span>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-save" :disabled="saving">
                    <span v-if="!saving">💾 Kaydet</span>
                    <span v-else>⏳ Kaydediliyor...</span>
                </button>
            </form>
        </div>
    </div>
</template>

<script setup>
import { reactive, ref, onMounted, watch } from 'vue';
import { useAuthStore } from '@/stores/auth.js';
import api from '@/api.js';
import Swal from 'sweetalert2';

const authStore = useAuthStore();
const saving = ref(false);
const saved = ref(false);

const stats = reactive({
    questions: 0,
    tests: 0,
    avg_score: 0,
});

const form = reactive({
    name: '',
    grade_level: 5,
});

// Populate form when user data becomes available
const populateForm = () => {
    if (authStore.user) {
        form.name = authStore.user.name || '';
        form.grade_level = authStore.user.grade_level || 5;
    }
};

watch(() => authStore.user, populateForm, { immediate: true });

onMounted(async () => {
    // If user not loaded yet, fetch it first
    if (!authStore.user && authStore.token) {
        await authStore.fetchUser();
    }
    populateForm();

    try {
        const [qRes, tRes] = await Promise.all([
            api.get('/questions'),
            api.get('/tests'),
        ]);
        stats.questions = qRes.data.data?.length || 0;
        const tests = tRes.data.data || [];
        stats.tests = tests.length;
        const completed = tests.filter(t => t.score !== null);
        stats.avg_score = completed.length > 0
            ? Math.round(completed.reduce((s, t) => s + t.score, 0) / completed.length)
            : 0;
    } catch (e) { /* silent */ }
});

const updateProfile = async () => {
    if (!form.name || !form.name.trim()) {
        Swal.fire({ icon: 'warning', title: 'Dikkat', text: 'Ad Soyad alanı boş bırakılamaz.', confirmButtonColor: '#7c3aed' });
        return;
    }
    saving.value = true;
    saved.value = false;
    try {
        const res = await api.put('/profile', { name: form.name.trim(), grade_level: form.grade_level });
        authStore.user = res.data.user;
        saved.value = true;
        Swal.fire({ icon: 'success', title: 'Başarılı!', text: 'Profil bilgilerin güncellendi.', timer: 2000, showConfirmButton: false });
        setTimeout(() => saved.value = false, 3000);
    } catch (e) {
        const msg = e.response?.data?.message || 'Güncelleme başarısız.';
        Swal.fire({ icon: 'error', title: 'Hata', text: msg, confirmButtonColor: '#7c3aed' });
    } finally {
        saving.value = false;
    }
};
</script>

<style scoped>
.profile-page { max-width: 600px; }
.page-header { margin-bottom: 1.5rem; }
.profile-title { font-size: 1.75rem; font-weight: 800; color: #1e293b; }
.page-subtitle { font-size: 0.85rem; color: #64748b; margin-top: 0.25rem; }

/* Profile Card */
.profile-card {
    position: relative; overflow: hidden; margin-bottom: 1.5rem;
    background: #ffffff; border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
}
.profile-card-bg {
    height: 85px;
    background: linear-gradient(135deg, #7c3aed, #a78bfa, #c084fc);
    width: 100%;
}
.profile-card-content {
    position: relative; display: flex; align-items: flex-start; gap: 1.5rem;
    padding: 0 2rem 2rem 2rem;
}
.profile-avatar {
    width: 84px; height: 84px; border-radius: 20px;
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem; font-weight: 800; color: white; flex-shrink: 0;
    box-shadow: 0 4px 16px rgba(124, 58, 237, 0.35);
    border: 4px solid #ffffff;
    margin-top: -30px; /* Overlaps background nicely */
}
.profile-info { 
    padding-top: 0.85rem; 
}
.profile-info h2 { font-size: 1.3rem; font-weight: 800; color: #1e293b; margin-bottom: 0.15rem; }
.profile-email { font-size: 0.85rem; color: #64748b; margin-bottom: 0.75rem; }
.profile-badges { display: flex; gap: 0.5rem; flex-wrap: wrap; }
.grade-badge {
    background: linear-gradient(135deg, #7c3aed, #a78bfa); color: #ffffff;
    padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.72rem; font-weight: 700;
}
.role-badge {
    background: #f3e8ff; color: #7c3aed;
    padding: 0.35rem 0.85rem; border-radius: 20px; font-size: 0.72rem; font-weight: 600;
}

/* Stats */
.profile-stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1.5rem; }
.stat-card {
    display: flex; flex-direction: column; align-items: center; padding: 1.25rem 0.75rem;
    text-align: center; transition: transform 0.2s, box-shadow 0.2s;
    background: #ffffff; border-radius: 14px; border: 1px solid #e5e7eb;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(124, 58, 237, 0.1); }
.stat-icon { font-size: 1.5rem; margin-bottom: 0.5rem; }
.stat-value { font-size: 1.5rem; font-weight: 900; color: #1e293b; line-height: 1; }
.stat-label { font-size: 0.7rem; color: #64748b; margin-top: 0.35rem; font-weight: 500; }

/* Form Card */
.profile-form-card {
    padding: 1.75rem 2rem;
    background: #ffffff; border-radius: 16px; border: 1px solid #e5e7eb;
    box-shadow: 0 4px 24px rgba(0,0,0,0.06);
}
.form-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
.form-header h3 { font-size: 1.1rem; font-weight: 800; color: #1e293b; }
.profile-form-card form { display: flex; flex-direction: column; gap: 1.25rem; }
.form-group { display: flex; flex-direction: column; }
.form-label { font-size: 0.8rem; font-weight: 700; color: #374151; margin-bottom: 0.4rem; }
.form-input {
    padding: 0.85rem 1rem; border-radius: 10px;
    border: 1.5px solid #e5e7eb; background: #f8fafc;
    color: #1e293b; font-size: 0.9rem; font-weight: 500;
    font-family: var(--font-sans); transition: all 0.2s;
    outline: none;
}
.form-input:focus { border-color: #7c3aed; box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1); }

.grade-selector { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.5rem; }
.grade-btn {
    padding: 0.75rem 0.5rem; border-radius: 12px;
    border: 1.5px solid #e5e7eb;
    background: #f8fafc;
    color: #64748b; font-size: 0.8rem; font-weight: 600;
    cursor: pointer; transition: all 0.2s ease; font-family: var(--font-sans);
    display: flex; align-items: center; justify-content: center; gap: 0.25rem;
}
.grade-btn:hover { border-color: #a78bfa; color: #7c3aed; background: #faf5ff; }
.grade-btn-active {
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
    border-color: transparent; color: #ffffff;
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.25);
}
.grade-number { font-size: 1.1rem; font-weight: 800; }
.grade-text { font-size: 0.75rem; }

.btn-save {
    width: 100%; padding: 0.875rem; font-size: 0.95rem; margin-top: 0.5rem;
    background: linear-gradient(135deg, #7c3aed, #a78bfa);
    color: white; border: none; border-radius: 12px;
    font-weight: 700; cursor: pointer; font-family: var(--font-sans);
    transition: all 0.2s; box-shadow: 0 4px 14px rgba(124, 58, 237, 0.25);
}
.btn-save:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(124, 58, 237, 0.35); }
.btn-save:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }

.save-success {
    color: #10b981; font-size: 0.85rem; font-weight: 700;
    animation: fadeIn 0.3s ease;
    background: #d1fae5; padding: 0.2rem 0.6rem; border-radius: 6px;
}
@keyframes fadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: none; } }

@media (max-width: 480px) {
    .profile-stats { grid-template-columns: repeat(3, 1fr); gap: 0.5rem; }
    .stat-card { padding: 1rem 0.5rem; }
    .profile-card-content { flex-direction: column; text-align: center; gap: 0.75rem; }
    .profile-info { padding-top: 0; }
    .profile-avatar { margin: -40px auto 0 auto; }
    .profile-badges { justify-content: center; }
    .grade-selector { grid-template-columns: repeat(2, 1fr); }
}
</style>
