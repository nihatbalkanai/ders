<template>
    <div class="upload-page">
        <div class="upload-header fade-in">
            <h1>Soru <span class="gradient-text">Yükle</span></h1>
            <p>Soru türünü seçerek fotoğrafını yükle ve ardı ardına hızlıca kaydet.</p>
        </div>

        <!-- Manual Selection (Always visible until analysisResult) -->
        <div v-if="!analysisResult" class="manual-selection fade-in slide-up">
            <div class="form-group">
                <label class="input-label">Ders Seçin</label>
                <select v-model="selectedSubjectId" class="input-field" @change="handleSubjectChange">
                    <option value="" disabled>Lütfen bir ders seçin veya otomatik bırakın...</option>
                    <option value="AUTO" class="auto-option">✨ Yapay Zeka Karar Versin (Otomatik)</option>
                    <option v-for="subject in subjects" :key="subject.id" :value="subject.id">
                        {{ subject.name }}
                    </option>
                    <option value="NEW" class="new-option">+ Yeni Ders Ekle</option>
                </select>
            </div>
            
            <div class="form-group" v-if="selectedSubjectId && selectedSubjectId !== 'NEW' && selectedSubjectId !== 'AUTO'">
                <label class="input-label">Kazanım / Ünite Seçin</label>
                <select v-model="selectedOutcomeId" class="input-field" @change="handleOutcomeChange">
                    <option value="" disabled>Lütfen bir kazanım seçin...</option>
                    <option v-for="outcome in outcomes" :key="outcome.id" :value="outcome.id">
                        {{ outcome.name }}
                    </option>
                    <option value="NEW" class="new-option">+ Yeni Kazanım Ekle</option>
                </select>
            </div>
        </div>

        <div v-if="(selectedSubjectId === 'AUTO') || (selectedSubjectId && selectedOutcomeId && selectedSubjectId !== 'NEW' && selectedOutcomeId !== 'NEW')" class="upload-area glass-card slide-up mt-4">
            <!-- Preview -->
            <div v-if="preview" class="preview-container">
                <img :src="preview" alt="Soru resmi" class="preview-image" />
                <button @click="clearImage" class="clear-btn">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    Kaldır
                </button>
            </div>

            <!-- Upload zone -->
            <div v-else class="upload-zone" @click="triggerFileInput" @dragover.prevent @drop.prevent="handleDrop">
                <div class="upload-icon floating">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                </div>
                <h3>Soruyu Yükle (<span class="text-primary-400">{{ getSelectedSubjectName() }}</span>)</h3>
                <p>Fotoğraf çek, dosya seç veya sürükle bırak</p>
                
                <div class="upload-buttons">
                    <label class="btn-primary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1-2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        Kamera ile Çek
                        <input type="file" accept="image/*" capture="environment" @change="handleFileSelect" class="hidden-input" />
                    </label>
                    <label class="btn-secondary">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21,15 16,10 5,21"/></svg>
                        Galeriden Seç
                        <input type="file" accept="image/*" @change="handleFileSelect" class="hidden-input" />
                    </label>
                </div>
            </div>

            <!-- Upload button -->
            <div v-if="preview && !analysisResult" class="upload-actions">
                <button @click="uploadImage" class="btn-primary btn-lg" :disabled="uploading || (selectedSubjectId !== 'AUTO' && (!selectedSubjectId || !selectedOutcomeId))">
                    <svg v-if="uploading" class="spinner" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><circle cx="12" cy="12" r="10"/></svg>
                    <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    {{ uploading ? 'Kaydediliyor...' : 'Yükle ve Kaydet' }}
                </button>
            </div>

            <!-- Upload another -->
            <div v-if="analysisResult" class="upload-actions">
                <button @click="resetUpload" class="btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18"><polyline points="1,4 1,10 7,10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/></svg>
                    Yeni Soru Yükle
                </button>
                <router-link to="/tests" class="btn-secondary">
                    Testlerime Git
                </router-link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Swal from 'sweetalert2';
import api from '@/api.js';

const selectedFile = ref(null);
const preview = ref(null);
const uploading = ref(false);
const analysisResult = ref(null);

const subjects = ref([]);
const outcomes = ref([]);
const selectedSubjectId = ref('');
const selectedOutcomeId = ref('');

const getSelectedSubjectName = () => {
    if (selectedSubjectId.value === 'AUTO') return 'Yapay Zeka Analizi';
    const sub = subjects.value.find(s => s.id === selectedSubjectId.value);
    return sub ? sub.name : '';
};

const fetchSubjects = async () => {
    try {
        const res = await api.get('/subjects');
        subjects.value = res.data.data;
    } catch (e) { console.error('Dersler yüklenemedi:', e); }
};

const fetchOutcomes = async () => {
    selectedOutcomeId.value = '';
    outcomes.value = [];
    if (!selectedSubjectId.value || selectedSubjectId.value === 'NEW') return;
    
    try {
        const res = await api.get(`/subjects/${selectedSubjectId.value}/outcomes`);
        outcomes.value = res.data.data;
    } catch (e) { console.error('Kazanımlar yüklenemedi:', e); }
};

const handleSubjectChange = async () => {
    if (selectedSubjectId.value === 'NEW') {
        selectedSubjectId.value = '';
        const { value: subjectName } = await Swal.fire({
            title: 'Yeni Ders Ekle',
            input: 'text',
            inputPlaceholder: 'Örn: Matematik, Fizik...',
            background: '#1e293b',
            color: '#f8fafc',
            showCancelButton: true,
            confirmButtonText: 'Ekle',
            cancelButtonText: 'İptal',
            inputValidator: (value) => {
                if (!value) return 'Ders adı boş olamaz!';
            }
        });

        if (subjectName) {
            try {
                const res = await api.post('/subjects', { name: subjectName });
                subjects.value.push(res.data.data);
                selectedSubjectId.value = res.data.data.id;
                fetchOutcomes();
                
                Swal.fire({
                    icon: 'success',
                    title: 'Eklendi',
                    text: `${subjectName} başarıyla eklendi!`,
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#1e293b',
                    color: '#f8fafc',
                });
            } catch (e) {
                Swal.fire('Hata', 'Ders eklenirken bir hata oluştu.', 'error');
            }
        }
    } else if (selectedSubjectId.value === 'AUTO') {
        selectedOutcomeId.value = '';
        outcomes.value = [];
    } else {
        fetchOutcomes();
    }
};

const handleOutcomeChange = async () => {
    if (selectedOutcomeId.value === 'NEW') {
        selectedOutcomeId.value = '';
        const { value: outcomeName } = await Swal.fire({
            title: 'Yeni Kazanım Ekle',
            input: 'text',
            inputPlaceholder: 'Örn: Üslü Sayılar, Dinamiğin Temel Prensibi...',
            background: '#1e293b',
            color: '#f8fafc',
            showCancelButton: true,
            confirmButtonText: 'Ekle',
            cancelButtonText: 'İptal',
            inputValidator: (value) => {
                if (!value) return 'Kazanım adı boş olamaz!';
            }
        });

        if (outcomeName) {
            try {
                const res = await api.post(`/subjects/${selectedSubjectId.value}/outcomes`, { name: outcomeName });
                outcomes.value.push(res.data.data);
                selectedOutcomeId.value = res.data.data.id;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Eklendi',
                    text: `${outcomeName} başarıyla eklendi!`,
                    timer: 1500,
                    showConfirmButton: false,
                    background: '#1e293b',
                    color: '#f8fafc',
                });
            } catch (e) {
                Swal.fire('Hata', 'Kazanım eklenirken bir hata oluştu.', 'error');
            }
        }
    }
};

onMounted(fetchSubjects);

const triggerFileInput = () => {
    // Clicking the upload zone is handled by the label buttons
};

const handleFileSelect = (event) => {
    const file = event.target.files[0];
    if (file) {
        selectedFile.value = file;
        preview.value = URL.createObjectURL(file);
        analysisResult.value = null;
    }
};

const handleDrop = (event) => {
    const file = event.dataTransfer.files[0];
    if (file && file.type.startsWith('image/')) {
        selectedFile.value = file;
        preview.value = URL.createObjectURL(file);
        analysisResult.value = null;
    }
};

const clearImage = () => {
    selectedFile.value = null;
    preview.value = null;
    analysisResult.value = null;
    // We purposefully do NOT clear selectedSubjectId and selectedOutcomeId
    // so the user can rapidly take photos without re-selecting strings.
};

const uploadImage = async () => {
    if (!selectedFile.value) return;
    if (selectedSubjectId.value !== 'AUTO' && (!selectedSubjectId.value || !selectedOutcomeId.value)) return;
    
    uploading.value = true;
    try {
        const formData = new FormData();
        formData.append('image', selectedFile.value);
        if (selectedSubjectId.value !== 'AUTO') {
            formData.append('subject_id', selectedSubjectId.value);
            formData.append('outcome_id', selectedOutcomeId.value);
        }

        const response = await api.post('/questions/upload', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        
        analysisResult.value = true; // Signals success
    } catch (e) {
        alert(e.response?.data?.message || 'Yükleme sırasında bir hata oluştu.');
    } finally {
        uploading.value = false;
    }
};

const resetUpload = () => {
    clearImage();
};
</script>

<style scoped>
.upload-page {
    max-width: 700px;
    margin: 0 auto;
}

.upload-header {
    margin-bottom: 2rem;
}

.upload-header h1 {
    font-size: 1.75rem;
    font-weight: 800;
    color: white;
    margin-bottom: 0.5rem;
}

.upload-header p {
    color: var(--color-dark-400);
    font-size: 0.9rem;
}

.upload-area {
    padding: 2rem;
}

.upload-zone {
    border: 2px dashed rgba(148, 163, 184, 0.2);
    border-radius: 16px;
    padding: 3rem 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
}

.upload-zone:hover {
    border-color: var(--color-primary-500);
    background: rgba(108, 71, 255, 0.03);
}

.upload-icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 1rem;
    background: rgba(108, 71, 255, 0.1);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--color-primary-400);
    padding: 14px;
}

.upload-zone h3 {
    font-size: 1.125rem;
    font-weight: 700;
    color: white;
    margin-bottom: 0.5rem;
}

.upload-zone p {
    color: var(--color-dark-500);
    font-size: 0.85rem;
    margin-bottom: 1.5rem;
}

.upload-buttons {
    display: flex;
    justify-content: center;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.hidden-input {
    display: none;
}

.preview-container {
    position: relative;
    text-align: center;
    margin-bottom: 1rem;
}

.preview-image {
    max-width: 100%;
    max-height: 400px;
    border-radius: 12px;
    object-fit: contain;
}

.clear-btn {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 6px 12px;
    font-size: 0.75rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 4px;
    transition: background 0.2s;
}

.clear-btn:hover {
    background: rgba(239, 68, 68, 0.8);
}

.mt-4 {
    margin-top: 1.5rem;
}

.new-option {
    font-weight: bold;
    color: var(--color-primary-400);
}

.auto-option {
    font-weight: bold;
    color: var(--color-success-400);
}

.text-primary-400 {
    color: var(--color-primary-400);
}

.manual-selection {
    margin-bottom: 1.5rem;
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
    background: var(--bg-card);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    border: 1px solid var(--border-light);
}

.form-group {
    display: flex;
    flex-direction: column;
}

.input-label {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: 0.4rem;
}

.input-field {
    padding: 0.75rem 1rem;
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    background: var(--bg-body);
    font-family: inherit;
    font-size: 0.95rem;
    color: var(--text-primary);
    transition: all 0.2s;
}

.input-field:focus {
    outline: none;
    border-color: var(--color-primary-500);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.upload-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 1.5rem;
    flex-wrap: wrap;
}

.btn-lg {
    padding: 0.875rem 2rem;
}

.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
</style>
