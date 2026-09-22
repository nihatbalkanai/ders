<template>
    <div class="questions-page">
        <div class="page-header fade-in">
            <h1>Soru <span class="gradient-text">Geçmişim</span></h1>
            <p>Yüklediğin tüm soruları burada görebilirsin</p>
        </div>

        <div v-if="loading" class="loading-grid">
            <div v-for="i in 4" :key="i" class="shimmer-card shimmer"></div>
        </div>

        <div v-else-if="questions.length === 0" class="empty-state glass-card">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="empty-icon"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            <p>Henüz soru yüklemedin.</p>
            <router-link to="/upload" class="btn-primary">İlk Sorunu Yükle</router-link>
        </div>

        <template v-else>
            <!-- Filter Tabs -->
            <div class="filter-tabs fade-in">
                <button 
                    :class="['filter-tab', { 'active': selectedFilterSubject === 'ALL' }]" 
                    @click="selectSubjectFilter('ALL')">
                    Tüm Dersler
                </button>
                <button 
                    v-for="sub in uniqueSubjects" 
                    :key="sub.id" 
                    :class="['filter-tab', { 'active': selectedFilterSubject === sub.id }]"
                    @click="selectSubjectFilter(sub.id)">
                    {{ sub.name }}
                </button>
            </div>

            <!-- Focus Filter Tabs -->
            <div class="filter-tabs fade-in sub-filter" v-if="selectedFilterSubject !== 'ALL' && uniqueOutcomesForSubject.length > 0">
                <button 
                    :class="['filter-tab', 'tab-sm', { 'active': selectedFilterOutcome === 'ALL' }]" 
                    @click="selectedFilterOutcome = 'ALL'">
                    Tüm Kazanımlar
                </button>
                <button 
                    v-for="out in uniqueOutcomesForSubject" 
                    :key="out.id" 
                    :class="['filter-tab', 'tab-sm', { 'active': selectedFilterOutcome === out.id }]"
                    @click="selectedFilterOutcome = out.id">
                    {{ out.name }}
                </button>
            </div>

            <div v-if="filteredQuestions.length === 0" class="empty-state glass-card mt-4">
                <p>Belirtilen kriterlere uygun soru bulunamadı.</p>
            </div>

            <div v-else class="questions-grid mt-4">
                <div v-for="q in filteredQuestions" :key="q.id" class="question-card glass-card slide-up">
                <div class="card-header-actions">
                    <button class="btn-edit-q" @click="openEditModal(q)" title="Düzenle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </button>
                    <button class="btn-delete-q" @click="deleteQuestion(q.id)" title="Soruyu Sil">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </div>
                <!-- Selection Overlay -->
                <div class="selection-overlay" @click.stop="toggleSelection(q.id)">
                    <div class="checkbox-circle" :class="{ 'is-selected': isSelected(q.id) }">
                        <svg v-if="isSelected(q.id)" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    </div>
                </div>
                <div class="question-image-container" v-if="q.image_url">
                    <img :src="q.image_url" alt="Soru" class="question-thumb" @click="viewImage(q.image_url)" />
                </div>
                <div class="question-info">
                    <div class="question-badges">
                        <span class="badge badge-primary" v-if="q.subject">{{ q.subject.name }}</span>
                        <span :class="['badge', q.status === 'analyzed' ? 'badge-success' : 'badge-warning']">
                            {{ q.status === 'analyzed' ? 'Analiz Edildi' : 'Bekliyor' }}
                        </span>
                        <span class="badge badge-success" v-if="q.correct_answer">Cevap: {{ q.correct_answer }}</span>
                    </div>
                    <h3 v-if="q.outcome">{{ q.outcome.name }}</h3>
                    <p v-if="q.ocr_text" class="ocr-preview">{{ q.ocr_text.substring(0, 100) }}...</p>
                    <span class="question-date">{{ formatDate(q.created_at) }}</span>
                </div>
            </div>
            </div>
        </template>

        <!-- Floating Selection Bar -->
        <transition name="slide-up-fade">
            <div v-if="selectedQuestions.length > 0" class="floating-selection-bar glass-card">
                <div class="selection-info">
                    <span class="selection-count">{{ selectedQuestions.length }}</span> soru seçildi
                </div>
                <div class="selection-actions">
                    <button class="btn-secondary btn-sm" @click="selectedQuestions = []">İptal</button>
                    <button class="btn-primary" @click="generateCustomTest" :disabled="generatingTest">
                        <svg v-if="generatingTest" class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="12" cy="12" r="10"/></svg>
                        <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><polyline points="14 2 14 8 20 8"/><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                        {{ generatingTest ? 'Üretiliyor...' : 'Test Üret' }}
                    </button>
                </div>
            </div>
        </transition>

        <!-- Edit Modal Overlay -->
        <div v-if="editingQuestion" class="modal-overlay" @click.self="closeEditModal">
            <div class="modal-content glass-card slide-up">
                <h2>Soruyu Düzenle</h2>
                
                <div class="form-group mb-4">
                    <label class="input-label">Ders</label>
                    <select v-model="editForm.subject_id" class="input-field" @change="fetchEditOutcomes">
                        <option value="" disabled>Ders seçin...</option>
                        <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>
                
                <div class="form-group mb-4">
                    <label class="input-label">Kazanım / Ünite</label>
                    <select v-model="editForm.outcome_id" class="input-field">
                        <option value="" disabled>Kazanım seçin...</option>
                        <option v-for="o in editOutcomes" :key="o.id" :value="o.id">{{ o.name }}</option>
                    </select>
                </div>

                <div class="modal-actions">
                    <button class="btn-secondary" @click="closeEditModal">İptal</button>
                    <button class="btn-primary" @click="updateQuestion" :disabled="savingEdit || !editForm.subject_id || !editForm.outcome_id">
                        {{ savingEdit ? 'Kaydediliyor...' : 'Kaydet' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import api from '@/api.js';

const router = useRouter();
const questions = ref([]);
const loading = ref(true);

// Filter logic
const selectedFilterSubject = ref('ALL');
const selectedFilterOutcome = ref('ALL');

const selectSubjectFilter = (id) => {
    selectedFilterSubject.value = id;
    selectedFilterOutcome.value = 'ALL';
};

const uniqueSubjects = computed(() => {
    const subs = [];
    const map = new Map();
    questions.value.forEach(q => {
        if (q.subject && !map.has(q.subject.id)) {
            map.set(q.subject.id, true);
            subs.push({ id: q.subject.id, name: q.subject.name });
        }
    });
    return subs;
});

const uniqueOutcomesForSubject = computed(() => {
    if (selectedFilterSubject.value === 'ALL') return [];
    const outs = [];
    const map = new Map();
    questions.value.forEach(q => {
        if (q.subject_id === selectedFilterSubject.value && q.outcome && !map.has(q.outcome.id)) {
            map.set(q.outcome.id, true);
            outs.push({ id: q.outcome.id, name: q.outcome.name });
        }
    });
    return outs;
});

const filteredQuestions = computed(() => {
    let filtered = questions.value;
    if (selectedFilterSubject.value !== 'ALL') {
        filtered = filtered.filter(q => q.subject_id === selectedFilterSubject.value);
    }
    if (selectedFilterOutcome.value !== 'ALL') {
        filtered = filtered.filter(q => q.outcome_id === selectedFilterOutcome.value);
    }
    return filtered;
});

// Selection logic
const selectedQuestions = ref([]);
const generatingTest = ref(false);

// Edit Modal variables
const editingQuestion = ref(null);
const editForm = ref({ subject_id: '', outcome_id: '' });
const subjects = ref([]);
const editOutcomes = ref([]);
const savingEdit = ref(false);

const fetchQuestions = async () => {
    try {
        const res = await api.get('/questions');
        questions.value = res.data.data || [];
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const toggleSelection = (id) => {
    const index = selectedQuestions.value.indexOf(id);
    if (index === -1) {
        selectedQuestions.value.push(id);
    } else {
        selectedQuestions.value.splice(index, 1);
    }
};

const isSelected = (id) => {
    return selectedQuestions.value.includes(id);
};

const generateCustomTest = async () => {
    if (selectedQuestions.value.length === 0) return;
    generatingTest.value = true;
    try {
        const res = await api.post('/tests/generate-selected', {
            question_ids: selectedQuestions.value
        });
        
        const tests = res.data.data;
        const testCount = Array.isArray(tests) ? tests.length : 1;
        
        // Reset selection
        selectedQuestions.value = [];
        
        Swal.fire({
            title: 'Testler Oluşturuldu!',
            text: `${testCount} adet test başarıyla oluşturuldu (her biri 10 soru).`,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false,
            background: '#1e293b',
            color: '#f8fafc',
        });

        // Navigate to tests list page
        router.push('/tests');
    } catch (e) {
        const msg = e.response?.data?.message || 'Test oluşturulurken bir hata oluştu.';
        Swal.fire('Hata!', msg, 'error');
    } finally {
        generatingTest.value = false;
    }
};

const fetchSubjects = async () => {
    if (subjects.value.length > 0) return;
    try {
        const res = await api.get('/subjects');
        subjects.value = res.data.data;
    } catch (e) {
        console.error('Dersler yüklenemedi', e);
    }
};

const fetchEditOutcomes = async () => {
    editForm.value.outcome_id = '';
    editOutcomes.value = [];
    if (!editForm.value.subject_id) return;
    try {
        const res = await api.get(`/subjects/${editForm.value.subject_id}/outcomes`);
        editOutcomes.value = res.data.data;
    } catch (e) {
        console.error('Kazanımlar yüklenemedi', e);
    }
};

const openEditModal = async (q) => {
    await fetchSubjects();
    editingQuestion.value = q;
    editForm.value.subject_id = q.subject_id;
    // Load outcomes for this subject directly
    try {
        const res = await api.get(`/subjects/${q.subject_id}/outcomes`);
        editOutcomes.value = res.data.data;
    } catch (e) {}
    editForm.value.outcome_id = q.outcome_id;
};

const closeEditModal = () => {
    editingQuestion.value = null;
};

const updateQuestion = async () => {
    if (!editingQuestion.value || !editForm.value.subject_id || !editForm.value.outcome_id) return;
    savingEdit.value = true;
    try {
        const res = await api.put(`/questions/${editingQuestion.value.id}`, {
            subject_id: editForm.value.subject_id,
            outcome_id: editForm.value.outcome_id
        });
        
        // Update the item in the list
        const index = questions.value.findIndex(q => q.id === editingQuestion.value.id);
        if (index !== -1) {
            questions.value[index] = res.data.data;
        }

        Swal.fire({
            title: 'Güncellendi!',
            text: 'Soru bilgileri başarıyla kaydedildi.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            background: '#1e293b',
            color: '#f8fafc',
        });
        closeEditModal();
    } catch (e) {
        console.error(e);
        Swal.fire('Hata!', 'Soru güncellenirken bir hata oluştu.', 'error');
    } finally {
        savingEdit.value = false;
    }
};

const viewImage = (url) => {
    Swal.fire({
        imageUrl: url,
        imageAlt: 'Soru Resmi',
        background: '#1e293b',
        showConfirmButton: false,
        showCloseButton: true,
        width: 'auto',
        customClass: {
            popup: 'swal-fullscreen-popup',
            image: 'swal-fullscreen-image'
        }
    });
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('tr-TR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const deleteQuestion = async (id) => {
    const result = await Swal.fire({
        title: 'Emin misiniz?',
        text: 'Bu soruyu geçmişinizden kalıcı olarak silmek istediğinize emin misiniz?',
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
            await api.delete(`/questions/${id}`);
            questions.value = questions.value.filter(q => q.id !== id);
            
            Swal.fire({
                title: 'Silindi!',
                text: 'Soru başarıyla silindi ve sunucudan kaldırıldı.',
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
                text: 'Soru silinirken bir hata oluştu.',
                icon: 'error',
                background: '#1e293b',
                color: '#f8fafc',
            });
        }
    }
};

onMounted(fetchQuestions);
</script>

<style scoped>
.questions-page {
    width: 100%;
}

.page-header {
    margin-bottom: 2rem;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.page-header p {
    color: var(--color-dark-400);
}

.filter-tabs {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
    scrollbar-width: none; /* Firefox */
}
.filter-tabs::-webkit-scrollbar {
    display: none; /* Chrome/Safari */
}

.filter-tab {
    padding: 0.5rem 1rem;
    border-radius: 100px;
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    color: var(--text-secondary);
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    white-space: nowrap;
}

.filter-tab:hover {
    background: var(--bg-body);
}

.filter-tab.active {
    background: var(--color-primary-500);
    color: white;
    border-color: var(--color-primary-500);
    box-shadow: var(--shadow-md);
}

.sub-filter {
    margin-top: -0.5rem;
    margin-bottom: 1.5rem;
}

.tab-sm {
    padding: 0.35rem 0.85rem;
    font-size: 0.8rem;
    background: transparent;
    border-color: transparent;
    color: var(--text-tertiary);
}

.tab-sm:hover {
    background: rgba(108, 71, 255, 0.05);
}

.tab-sm.active {
    background: rgba(108, 71, 255, 0.1);
    color: var(--color-primary-600);
    border-color: rgba(108, 71, 255, 0.2);
    box-shadow: none;
}

.mt-4 { margin-top: 1rem; }

.loading-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
}

.shimmer-card {
    height: 200px;
    border-radius: 16px;
}

.questions-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
}

@media (max-width: 900px) {
    .questions-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 500px) {
    .questions-grid { grid-template-columns: 1fr; }
}

.question-card {
    overflow: hidden;
    padding: 0;
    position: relative;
}

.card-header-actions {
    display: flex;
    gap: 0.5rem;
    z-index: 10;
}

.btn-delete-q {
    background: rgba(220, 38, 38, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
    border-radius: var(--radius-sm);
    width: 32px;
    height: 32px;
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
    width: 16px;
    height: 16px;
}

.btn-edit-q {
    background: rgba(99, 102, 241, 0.1);
    color: var(--color-primary-600);
    border: 1px solid rgba(99, 102, 241, 0.2);
    border-radius: var(--radius-sm);
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-edit-q:hover {
    background: var(--color-primary-600);
    color: white;
    transform: scale(1.05);
}

.btn-edit-q svg {
    width: 16px;
    height: 16px;
}

.question-image-container {
    height: 180px;
    overflow: hidden;
    background: var(--bg-body);
    position: relative;
}

.question-thumb {
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: zoom-in;
    transition: transform 0.3s;
}

.question-thumb:hover {
    transform: scale(1.02);
}

.card-header-actions {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    z-index: 10;
    display: flex;
    gap: 0.5rem;
}

.question-info {
    padding: 1rem;
}

.question-badges {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    flex-wrap: wrap;
}

.question-info h3 {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.ocr-preview {
    font-size: 0.75rem;
    color: var(--color-dark-500);
    line-height: 1.5;
    margin-bottom: 0.5rem;
}

.question-date {
    font-size: 0.7rem;
    color: var(--color-dark-600);
}

.empty-state {
    padding: 3rem;
    text-align: center;
}

.empty-icon {
    width: 48px;
    height: 48px;
    color: var(--color-dark-600);
    margin-bottom: 1rem;
}

.empty-state p {
    color: var(--color-dark-400);
    margin-bottom: 1rem;
}

/* Selection overlay on cards */
.selection-overlay {
    position: absolute;
    top: 0.5rem;
    left: 0.5rem;
    z-index: 10;
    cursor: pointer;
    padding: 0.25rem;
}

.checkbox-circle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 2px solid rgba(255, 255, 255, 0.8);
    background: rgba(0, 0, 0, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.checkbox-circle:hover {
    background: rgba(0, 0, 0, 0.5);
    transform: scale(1.1);
}

.checkbox-circle.is-selected {
    background: var(--color-primary-500);
    border-color: var(--color-primary-500);
}

.checkbox-circle svg {
    width: 14px;
    height: 14px;
}

/* Floating Selection Bar */
.floating-selection-bar {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%);
    z-index: 50;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.5rem;
    border-radius: 100px;
    width: 90%;
    max-width: 600px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    background: var(--bg-header);
    backdrop-filter: blur(12px);
    border: 1px solid var(--color-primary-200);
}

.selection-info {
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.95rem;
}

.selection-count {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--color-primary-500);
    color: white;
    font-weight: 700;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    margin-right: 0.5rem;
}

.selection-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
}

.btn-sm {
    padding: 0.4rem 0.75rem;
    font-size: 0.8rem;
}

.slide-up-fade-enter-active,
.slide-up-fade-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-up-fade-enter-from,
.slide-up-fade-leave-to {
    opacity: 0;
    transform: translate(-50%, 2rem);
}

.spinner {
    animation: spin 1s linear infinite;
}
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Modal Stylings */
.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
}

.modal-content {
    background: var(--bg-card);
    width: 90%;
    max-width: 500px;
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
}

.modal-content h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1.5rem;
}

.mb-4 {
    margin-bottom: 1rem;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
}
</style>

<style>
/* Global styles for SweetAlert which attaches to body outside of Vue scope */
img.swal-fullscreen-image {
    max-height: 80vh !important;
    max-width: 100% !important;
    object-fit: contain !important;
    border-radius: 8px !important;
}
.swal2-popup.swal-fullscreen-popup {
    width: auto !important;
    max-width: 90vw !important;
    padding: 1rem !important;
}
</style>
