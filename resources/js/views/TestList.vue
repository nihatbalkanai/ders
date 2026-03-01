<template>
    <div class="tests-page">
        <div class="page-header fade-in">
            <div>
                <h1>Test<span class="gradient-text">lerim</span></h1>
                <p>Oluşturulan kişisel testlerini burada görebilir ve çözebilirsin</p>
            </div>
            <button @click="generateTest" class="btn-primary" :disabled="generating">
                {{ generating ? 'Oluşturuluyor...' : '+ Yeni Test Oluştur' }}
            </button>
        </div>

        <div v-if="loading" class="loading-grid">
            <div v-for="i in 3" :key="i" class="shimmer-card shimmer"></div>
        </div>

        <div v-else-if="tests.length === 0" class="empty-state glass-card">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="empty-icon"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
            <p>Henüz test oluşturulmadı. Önce birkaç soru yükle, sonra test oluştur!</p>
        </div>

        <div v-else class="tests-list">
            <div v-for="t in tests" :key="t.id" class="test-card-container">
                <router-link :to="{ name: 'test-view', params: { id: t.id } }" class="test-card glass-card slide-up">
                    <div class="test-card-header">
                        <h3>{{ t.title }}</h3>
                        <div class="test-header-actions">
                            <span :class="['badge', t.completed_at ? 'badge-success' : 'badge-warning']">
                                {{ t.completed_at ? 'Tamamlandı' : 'Çözülmedi' }}
                            </span>
                            <button class="btn-delete-q" @click.prevent="deleteTest(t.id)" title="Testi Sil">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class="test-card-meta">
                        <span>{{ questionCount(t) }} soru</span>
                        <span v-if="t.score !== null">Puan: %{{ t.score }}</span>
                        <span>{{ formatDate(t.created_at) }}</span>
                    </div>
                    <div v-if="t.score !== null" class="progress-bar">
                        <div class="progress-bar-fill" :style="{ width: t.score + '%' }"></div>
                    </div>
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import api from '@/api.js';

const router = useRouter();
const tests = ref([]);
const loading = ref(true);
const generating = ref(false);

const fetchTests = async () => {
    try {
        const res = await api.get('/tests');
        tests.value = res.data.data || [];
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const generateTest = async () => {
    generating.value = true;
    try {
        const res = await api.post('/tests/generate');
        router.push({ name: 'test-view', params: { id: res.data.data.id } });
    } catch (e) {
        alert(e.response?.data?.message || 'Test oluşturulamadı.');
    } finally {
        generating.value = false;
    }
};

const questionCount = (test) => {
    try {
        const data = typeof test.questions_data === 'string' ? JSON.parse(test.questions_data) : test.questions_data;
        return Array.isArray(data) ? data.length : 0;
    } catch {
        return 0;
    }
};

const deleteTest = async (id) => {
    const result = await Swal.fire({
        title: 'Emin misiniz?',
        text: 'Bu test geçmişini silmek istediğinize emin misiniz? Bu işlem geri alınamaz.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#475569',
        confirmButtonText: 'Evet, Sileyim',
        cancelButtonText: 'İptal',
        background: '#1e293b',
        color: '#f8fafc',
    });

    if (result.isConfirmed) {
        try {
            await api.delete(`/tests/${id}`);
            tests.value = tests.value.filter(t => t.id !== id);
            
            Swal.fire({
                title: 'Silindi!',
                text: 'Test başarıyla silindi.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                background: '#1e293b',
                color: '#f8fafc',
            });
        } catch (e) {
            console.error(e);
            Swal.fire({
                title: 'Hata!',
                text: 'Silme işlemi sırasında bir sorun oluştu.',
                icon: 'error',
                background: '#1e293b',
                color: '#f8fafc',
            });
        }
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('tr-TR', { day: 'numeric', month: 'long', year: 'numeric' });
};

onMounted(fetchTests);
</script>

<style scoped>
.tests-page { max-width: 800px; }
.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem; gap: 1rem; flex-wrap: wrap; }
.page-header h1 { font-size: 1.75rem; font-weight: 800; color: white; margin-bottom: 0.5rem; }
.page-header p { color: var(--color-dark-400); font-size: 0.9rem; }
.loading-grid { display: flex; flex-direction: column; gap: 1rem; }
.shimmer-card { height: 100px; border-radius: 16px; }
.tests-list { display: flex; flex-direction: column; gap: 1rem; }
.test-card { padding: 1.25rem; text-decoration: none; color: inherit; display: block; position: relative; }
.test-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; }
.test-header-actions { display: flex; align-items: center; gap: 0.5rem; }
.test-card-header h3 { font-size: 0.95rem; font-weight: 700; color: white; }
.test-card-meta { display: flex; gap: 1.5rem; font-size: 0.8rem; color: var(--color-dark-500); margin-bottom: 0.75rem; }
.empty-state { padding: 3rem; text-align: center; }
.empty-icon { width: 48px; height: 48px; color: var(--color-dark-600); margin-bottom: 1rem; }
.empty-state p { color: var(--color-dark-400); font-size: 0.9rem; }

.btn-delete-q {
    background: rgba(220, 38, 38, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
    border-radius: var(--radius-sm);
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-delete-q:hover {
    background: #ef4444;
    color: white;
    transform: scale(1.05);
}

.btn-delete-q svg {
    width: 14px;
    height: 14px;
}
.empty-state { padding: 3rem; text-align: center; }
.empty-icon { width: 48px; height: 48px; color: var(--color-dark-600); margin-bottom: 1rem; }
.empty-state p { color: var(--color-dark-400); font-size: 0.9rem; }
</style>
