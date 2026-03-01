<template>
    <div class="app-shell">
        <!-- Decorative blobs -->
        <div class="blob-bg blob-1"></div>
        <div class="blob-bg blob-2"></div>

        <!-- Desktop Sidebar (hidden on mobile) -->
        <aside class="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                </div>
                <span class="brand-name">SmartTest <span class="brand-ai">AI</span></span>
            </div>

            <nav class="sidebar-nav">
                <router-link to="/dashboard" class="nav-link" active-class="nav-active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
                    <span>Ana Sayfa</span>
                </router-link>

                <router-link to="/questions" class="nav-link" active-class="nav-active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>Sorularım</span>
                </router-link>
                <router-link to="/tests" class="nav-link" active-class="nav-active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                    <span>Testlerim</span>
                </router-link>
                <router-link to="/profile" class="nav-link" active-class="nav-active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <span>Profilim</span>
                </router-link>
                <router-link to="/ai-settings" class="nav-link" active-class="nav-active">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2A10 10 0 1 0 22 12 10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/><path d="M12 6v6l4 2"/></svg>
                    <span>Yapay Zeka Ayarları</span>
                </router-link>
                <!-- Return to landing page -->
                <router-link to="/" class="nav-link" exact-active-class="nav-active" style="margin-top: 1rem; border-top: 1px dashed var(--border-light); border-radius: 0; padding-top: 1rem;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Web Sitesine Dön</span>
                </router-link>
            </nav>

            <div class="sidebar-footer">
                <div class="user-pill">
                    <div class="user-avatar-sm">{{ authStore.userInitials }}</div>
                    <div class="user-meta">
                        <span class="user-name-sm">{{ authStore.userName }}</span>
                        <span class="user-grade-sm">{{ authStore.user?.grade_level }}. Sınıf</span>
                    </div>
                </div>
                <button @click="handleLogout" class="logout-btn" title="Çıkış yap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="main-area">
            <!-- Top Header (Desktop: right side, Mobile: full width) -->
            <header class="top-header">
                <!-- Mobile Hamburger -->
                <button class="mobile-menu-btn" @click="toggleMobileMenu">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                </button>
                <div class="header-brand-mobile">
                    <div class="brand-icon-sm">🧠</div>
                    <span class="brand-name-sm">SmartTest</span>
                </div>

                <div class="header-actions">
                    <!-- Upload Button -->
                    <button class="header-upload-btn" @click="openUploadModal" title="Soru Yükle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17,8 12,3 7,8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        <span>Soru Yükle</span>
                    </button>
                    <!-- Camera Button -->
                    <label class="header-cam-btn pulse-glow" title="Fotoğraf Çek">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                        <input type="file" accept="image/*" capture="environment" @change="handleCapture" class="sr-only" />
                    </label>
                    <!-- Gallery Button -->
                    <label class="header-gallery-btn" title="Galeriden Seç">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21,15 16,10 5,21"/></svg>
                        <input type="file" accept="image/*" @change="handleCapture" class="sr-only" />
                    </label>
                    <!-- Profile -->
                    <router-link to="/profile" class="header-avatar">
                        {{ authStore.userInitials }}
                    </router-link>
                </div>
            </header>

            <main class="page-content">
                <router-view v-slot="{ Component }">
                    <transition name="page" mode="out-in">
                        <component :is="Component" />
                    </transition>
                </router-view>
            </main>
        </div>

        <!-- Mobile Bottom Tabs (visible only on mobile) -->
        <nav class="mobile-tabs">
            <router-link to="/dashboard" class="mtab" active-class="mtab-active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg>
                <span>Ana Sayfa</span>
            </router-link>
            <router-link to="/questions" class="mtab" active-class="mtab-active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/></svg>
                <span>Sorular</span>
            </router-link>
            <!-- Camera FAB -->
            <label class="mtab-fab">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                <input type="file" accept="image/*" capture="environment" @change="handleCapture" class="sr-only" />
            </label>
            <router-link to="/tests" class="mtab" active-class="mtab-active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                <span>Testler</span>
            </router-link>
            <router-link to="/profile" class="mtab" active-class="mtab-active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                <span>Profil</span>
            </router-link>
        </nav>

        <!-- Mobile Drawer -->
        <div v-if="mobileMenuOpen" class="mobile-overlay" @click="mobileMenuOpen = false"></div>
        <Transition name="drawer">
            <aside v-if="mobileMenuOpen" class="mobile-drawer">
                <div class="drawer-header">
                    <div class="brand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    </div>
                    <span class="brand-name">SmartTest <span class="brand-ai">AI</span></span>
                </div>
                <nav class="drawer-nav">
                    <router-link v-for="item in navItems" :key="item.to" :to="item.to" class="drawer-link" active-class="drawer-active" @click="mobileMenuOpen = false">
                        <component :is="item.icon" />
                        <span>{{ item.label }}</span>
                    </router-link>
                    <!-- hardcoded fallback since navItems isn't in setup -->
                    <router-link to="/dashboard" class="drawer-link" active-class="drawer-active" @click="mobileMenuOpen = false">
                        <span>Ana Sayfa</span>
                    </router-link>
                    <router-link to="/questions" class="drawer-link" active-class="drawer-active" @click="mobileMenuOpen = false">
                        <span>Sorularım</span>
                    </router-link>
                    <router-link to="/tests" class="drawer-link" active-class="drawer-active" @click="mobileMenuOpen = false">
                        <span>Testlerim</span>
                    </router-link>
                    <router-link to="/ai-settings" class="drawer-link" active-class="drawer-active" @click="mobileMenuOpen = false">
                        <span>AI Ayarları</span>
                    </router-link>
                    <router-link to="/" class="drawer-link" @click="mobileMenuOpen = false" style="margin-top: 1rem; border-top: 1px dashed var(--border-light); border-radius: 0; padding-top: 1rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        <span>Web Sitesine Dön</span>
                    </router-link>
                </nav>
                <button @click="handleLogout" class="drawer-logout">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16,17 21,12 16,7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                    Çıkış Yap
                </button>
            </aside>
        </Transition>

        <!-- Upload Modal -->
        <Transition name="modal">
            <div v-if="showUploadModal" class="modal-overlay" @click.self="closeModal">
                <div class="upload-modal glass-card slide-up">
                    <div class="modal-top">
                        <h3>📸 Soru Yükle</h3>
                        <button @click="closeModal" class="modal-x">&times;</button>
                    </div>
                    <div class="modal-content">
                        <!-- Step 1: Subject Selection -->
                        <div class="modal-form-group">
                            <label class="input-label">Ders Seçin</label>
                            <select v-model="selectedSubjectId" class="input-field" @change="handleSubjectChange">
                                <option value="" disabled>Lütfen bir ders seçin...</option>
                                <option value="AUTO">✨ Yapay Zeka Karar Versin</option>
                                <option v-for="sub in subjects" :key="sub.id" :value="sub.id">{{ sub.name }}</option>
                                <option value="NEW">+ Yeni Ders Ekle</option>
                            </select>
                        </div>

                        <!-- Step 2: Outcome Selection -->
                        <div v-if="selectedSubjectId && selectedSubjectId !== 'NEW' && selectedSubjectId !== 'AUTO'" class="modal-form-group">
                            <label class="input-label">Kazanım Seçin</label>
                            <select v-model="selectedOutcomeId" class="input-field" @change="handleOutcomeChange">
                                <option value="" disabled>Lütfen bir kazanım seçin...</option>
                                <option v-for="out in outcomes" :key="out.id" :value="out.id">{{ out.name }}</option>
                                <option value="NEW">+ Yeni Kazanım Ekle</option>
                            </select>
                        </div>

                        <!-- Step 3: Image Upload -->
                        <div v-if="canShowImageUpload" class="modal-form-group">
                            <img v-if="previewUrl" :src="previewUrl" class="modal-preview" alt="Soru" />
                            <div v-if="!previewUrl" class="modal-upload-zone">
                                <label class="btn-primary" style="margin-right:0.5rem">
                                    📷 Kamera
                                    <input type="file" accept="image/*" capture="environment" @change="handleCapture" class="sr-only" />
                                </label>
                                <label class="btn-secondary">
                                    🖼️ Galeri
                                    <input type="file" accept="image/*" @change="handleCapture" class="sr-only" />
                                </label>
                            </div>
                        </div>

                        <!-- Analysis Result -->
                        <div v-if="analysisResult" class="modal-result">
                            <div class="result-pill"><span class="rp-label">Ders:</span><span class="badge badge-primary">{{ analysisResult.subject }}</span></div>
                            <div class="result-pill"><span class="rp-label">Kazanım:</span><span class="badge badge-success">{{ analysisResult.outcome }}</span></div>
                        </div>

                        <!-- Actions -->
                        <button v-if="previewUrl && !analysisResult && !uploading" @click="uploadImage" class="btn-primary" style="width:100%">
                            🔍 Yükle ve Analiz Et
                        </button>
                        <div v-if="uploading" class="uploading-box">
                            <div class="loader"></div>
                            <p>AI soruyu analiz ediyor...</p>
                        </div>
                        <button v-if="analysisResult" @click="closeModal" class="btn-primary" style="width:100%">✓ Tamam</button>
                    </div>
                </div>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.js';
import Swal from 'sweetalert2';
import api from '@/api.js';

const router = useRouter();
const authStore = useAuthStore();
const mobileMenuOpen = ref(false);

const showUploadModal = ref(false);
const previewUrl = ref(null);
const selectedFile = ref(null);
const uploading = ref(false);
const analysisResult = ref(null);

// Subject / Outcome
const subjects = ref([]);
const outcomes = ref([]);
const selectedSubjectId = ref('');
const selectedOutcomeId = ref('');

const canShowImageUpload = computed(() => {
    if (selectedSubjectId.value === 'AUTO') return true;
    return selectedSubjectId.value && selectedOutcomeId.value && selectedSubjectId.value !== 'NEW' && selectedOutcomeId.value !== 'NEW';
});

const fetchSubjects = async () => {
    try {
        const res = await api.get('/subjects');
        subjects.value = res.data.data;
    } catch (e) { console.error(e); }
};

const fetchOutcomes = async () => {
    selectedOutcomeId.value = '';
    outcomes.value = [];
    if (!selectedSubjectId.value || selectedSubjectId.value === 'NEW') return;
    try {
        const res = await api.get(`/subjects/${selectedSubjectId.value}/outcomes`);
        outcomes.value = res.data.data;
    } catch (e) { console.error(e); }
};

const handleSubjectChange = async () => {
    if (selectedSubjectId.value === 'NEW') {
        selectedSubjectId.value = '';
        const { value: name } = await Swal.fire({
            title: 'Yeni Ders Ekle', input: 'text', inputPlaceholder: 'Örn: Matematik, Fizik...',
            showCancelButton: true, confirmButtonText: 'Ekle', cancelButtonText: 'İptal',
            inputValidator: v => { if (!v) return 'Ders adı boş olamaz!'; }
        });
        if (name) {
            try {
                const res = await api.post('/subjects', { name });
                subjects.value.push(res.data.data);
                selectedSubjectId.value = res.data.data.id;
                fetchOutcomes();
            } catch (e) { Swal.fire('Hata', 'Ders eklenemedi.', 'error'); }
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
        const { value: name } = await Swal.fire({
            title: 'Yeni Kazanım Ekle', input: 'text', inputPlaceholder: 'Örn: Üslü Sayılar...',
            showCancelButton: true, confirmButtonText: 'Ekle', cancelButtonText: 'İptal',
            inputValidator: v => { if (!v) return 'Kazanım adı boş olamaz!'; }
        });
        if (name) {
            try {
                const res = await api.post(`/subjects/${selectedSubjectId.value}/outcomes`, { name });
                outcomes.value.push(res.data.data);
                selectedOutcomeId.value = res.data.data.id;
            } catch (e) { Swal.fire('Hata', 'Kazanım eklenemedi.', 'error'); }
        }
    }
};

const toggleMobileMenu = () => { mobileMenuOpen.value = !mobileMenuOpen.value; };

const openUploadModal = () => {
    showUploadModal.value = true;
    if (subjects.value.length === 0) fetchSubjects();
};

const handleCapture = (event) => {
    const file = event.target.files[0];
    if (file) {
        selectedFile.value = file;
        previewUrl.value = URL.createObjectURL(file);
        analysisResult.value = null;
        // If modal not open yet (camera/gallery buttons), open it
        if (!showUploadModal.value) {
            showUploadModal.value = true;
            if (subjects.value.length === 0) fetchSubjects();
            selectedSubjectId.value = 'AUTO';
        }
    }
    event.target.value = '';
};

const uploadImage = async () => {
    if (!selectedFile.value) return;
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
        analysisResult.value = response.data.data.analysis;
    } catch (e) {
        alert(e.response?.data?.message || 'Hata oluştu.');
    } finally {
        uploading.value = false;
    }
};

const closeModal = () => {
    showUploadModal.value = false;
    previewUrl.value = null;
    selectedFile.value = null;
    analysisResult.value = null;
    selectedSubjectId.value = '';
    selectedOutcomeId.value = '';
};

const handleLogout = async () => {
    await authStore.logout();
    router.push('/login');
};

onMounted(fetchSubjects);
</script>

<style scoped>
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); border: 0; }

/* ═══ Shell ═══ */
.app-shell {
    display: flex;
    min-height: 100vh;
}

/* ═══ Desktop Sidebar ═══ */
.sidebar {
    width: 250px;
    background: var(--bg-sidebar);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
    border-right: 1px solid var(--border-light);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0; bottom: 0; left: 0;
    z-index: 40;
    background-image: var(--gradient-sidebar);
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.5rem 1.25rem;
    border-bottom: 1px solid var(--border-light);
}

.brand-icon {
    width: 36px; height: 36px;
    background: var(--gradient-primary);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    color: white; padding: 7px;
    box-shadow: 0 0 15px rgba(124,58,237,0.25);
}

.brand-name {
    font-size: 1.15rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;
}

.brand-ai {
    font-size: 0.55rem; font-weight: 700;
    background: var(--color-primary-100);
    color: var(--color-primary-700); padding: 2px 6px; border-radius: var(--radius-sm); vertical-align: super;
    letter-spacing: 0.05em;
}

.sidebar-nav {
    flex: 1;
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.7rem 0.9rem;
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 500;
    transition: all 0.2s ease;
    position: relative;
}

.nav-link svg { width: 19px; height: 19px; flex-shrink: 0; opacity: 0.7; }

.nav-link:hover {
    background: var(--color-primary-50);
    color: var(--color-primary-700);
}
.nav-link:hover svg { opacity: 1; color: var(--color-primary-600); }

.nav-active {
    background: var(--color-primary-50) !important;
    color: var(--color-primary-700) !important;
    font-weight: 600;
}
.nav-active svg { opacity: 1; color: var(--color-primary-600); }
.nav-active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 3px;
    height: 60%;
    background: var(--gradient-primary);
    border-radius: 0 4px 4px 0;
}

.sidebar-footer {
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.user-pill { display: flex; align-items: center; gap: 0.7rem; }

.user-avatar-sm {
    width: 34px; height: 34px;
    border-radius: var(--radius-full);
    background: var(--color-primary-100);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; font-weight: 700; color: var(--color-primary-700);
}

.user-name-sm { font-size: 0.78rem; font-weight: 600; color: var(--text-primary); display: block; }
.user-grade-sm { font-size: 0.62rem; color: var(--text-muted); }

.logout-btn {
    background: none; border: none; cursor: pointer;
    color: var(--text-muted); padding: 6px; border-radius: 8px;
    transition: all 0.2s;
}
.logout-btn svg { width: 17px; height: 17px; }
.logout-btn:hover { color: var(--color-danger); background: rgba(248,113,113,0.1); }

/* ═══ Main Area ═══ */
.main-area {
    flex: 1;
    margin-left: 250px;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

/* ═══ Top Header ═══ */
.top-header {
    height: 56px;
    background: var(--bg-header);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--border-light);
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 1.5rem;
    position: sticky;
    top: 0;
    z-index: 30;
}

.mobile-menu-btn { display: none; }
.header-brand-mobile { display: none; }

.header-actions {
    display: flex;
    align-items: center;
    gap: 0.6rem;
}

.header-cam-btn, .header-gallery-btn {
    width: 36px; height: 36px;
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all 0.25s;
    padding: 8px;
}

.header-upload-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: var(--radius-md);
    font-family: var(--font-sans);
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s;
    box-shadow: 0 2px 8px rgba(124,58,237,0.25);
}
.header-upload-btn svg { width: 16px; height: 16px; flex-shrink: 0; }
.header-upload-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(124,58,237,0.35);
}

.header-cam-btn {
    background: var(--gradient-primary);
    color: white;
    box-shadow: 0 0 12px rgba(124,58,237,0.3);
}
.header-cam-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 18px rgba(124,58,237,0.4); }

.header-gallery-btn {
    background: #f1f5f9;
    color: var(--text-secondary);
    border: 1px solid var(--border-light);
}
.header-gallery-btn:hover { background: #e2e8f0; color: var(--text-primary); border-color: var(--border-hover); }

.header-avatar {
    width: 34px; height: 34px;
    border-radius: var(--radius-full);
    background: var(--color-primary-100);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.8rem; font-weight: 700; color: var(--color-primary-700);
    text-decoration: none;
    transition: all 0.2s;
    border: 1px solid var(--color-primary-200);
}
.header-avatar:hover { background: var(--color-primary-200); border-color: var(--color-primary-300); }

/* ═══ Page Content ═══ */
.page-content {
    flex: 1;
    padding: 2rem;
    width: 100%;
}

/* ═══ Mobile Bottom Tabs ═══ */
.mobile-tabs { display: none; }

/* ═══ Mobile Drawer ═══ */
.mobile-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.3);
    backdrop-filter: blur(4px);
    z-index: 60;
}

.mobile-drawer {
    position: fixed; top: 0; left: 0; bottom: 0;
    width: 270px;
    background: var(--bg-card-solid);
    z-index: 70;
    padding: 1.25rem;
    box-shadow: var(--shadow-lg);
    display: flex;
    flex-direction: column;
}

.drawer-header {
    display: flex; align-items: center; gap: 0.6rem;
    margin-bottom: 1.5rem;
}

.drawer-nav { flex: 1; display: flex; flex-direction: column; gap: 2px; }

.drawer-link {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.7rem; border-radius: var(--radius-md);
    color: var(--text-muted); text-decoration: none;
    font-size: 0.85rem; font-weight: 500; transition: all 0.2s;
}
.drawer-link svg { width: 19px; height: 19px; }
.drawer-link:hover { background: var(--color-primary-50); color: var(--color-primary-700); }
.drawer-active { background: var(--color-primary-50) !important; color: var(--color-primary-700) !important; }

.drawer-logout {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 0.7rem; border-radius: var(--radius-md);
    background: none; border: 1px solid rgba(248,113,113,0.15);
    color: var(--color-danger); cursor: pointer;
    font-family: var(--font-sans); font-size: 0.85rem; font-weight: 600;
}
.drawer-logout svg { width: 17px; height: 17px; }

.drawer-enter-active, .drawer-leave-active { transition: transform 0.3s ease; }
.drawer-enter-from, .drawer-leave-to { transform: translateX(-100%); }

/* ═══ Upload Modal ═══ */
.modal-overlay {
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(8px);
    z-index: 100;
    display: flex; align-items: center; justify-content: center;
    padding: 1rem;
}

.upload-modal {
    width: 100%; max-width: 420px; max-height: 85vh; overflow-y: auto;
    background: var(--bg-card-solid);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-xl);
}

.modal-top {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-light);
}
.modal-top h3 { font-size: 1rem; font-weight: 700; color: var(--text-primary); }
.modal-x {
    background: none; border: none; font-size: 1.5rem;
    color: var(--text-muted); cursor: pointer; line-height: 1;
}
.modal-x:hover { color: var(--text-primary); }

.modal-content { padding: 1.5rem; }

.modal-preview {
    width: 100%; max-height: 280px;
    object-fit: contain; border-radius: var(--radius-md);
    margin-bottom: 1rem; background: #f8fafc;
}

.modal-result { margin-bottom: 1rem; }

.modal-form-group {
    margin-bottom: 1rem;
}

.modal-upload-zone {
    display: flex;
    justify-content: center;
    gap: 0.5rem;
    padding: 1.5rem;
    border: 2px dashed var(--border-light);
    border-radius: var(--radius-md);
    margin-bottom: 1rem;
}

.result-pill {
    display: flex; align-items: center; gap: 0.5rem;
    margin-bottom: 0.5rem;
}
.rp-label { font-size: 0.8rem; color: var(--text-muted); min-width: 60px; }

.uploading-box { text-align: center; padding: 2rem 0; }
.uploading-box p { color: var(--text-muted); font-size: 0.85rem; margin-top: 0.75rem; }

.loader {
    width: 36px; height: 36px;
    border: 3px solid var(--border-light);
    border-top-color: var(--color-primary-500);
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    margin: 0 auto;
}
@keyframes spin { to { transform: rotate(360deg); } }

.modal-enter-active, .modal-leave-active { transition: opacity 0.2s; }
.modal-enter-from, .modal-leave-to { opacity: 0; }

/* ═══ Page Transitions ═══ */
.page-enter-active, .page-leave-active { transition: opacity 0.15s, transform 0.15s; }
.page-enter-from { opacity: 0; transform: translateY(6px); }
.page-leave-to { opacity: 0; }

/* ═══════════════════════════════════════
   MOBILE RESPONSIVE (768px ve altı)
   ═══════════════════════════════════════ */
@media (max-width: 768px) {
    .sidebar { display: none; }
    .main-area { margin-left: 0; }

    .top-header { justify-content: space-between; }

    .mobile-menu-btn {
        display: flex;
        background: none; border: none;
        color: var(--text-secondary); cursor: pointer;
        padding: 6px; border-radius: 8px;
    }
    .mobile-menu-btn svg { width: 22px; height: 22px; }

    .header-brand-mobile {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 1rem; font-weight: 800;
        color: var(--text-primary);
    }
    .brand-icon-sm { font-size: 1.2rem; }
    .brand-name-sm { font-weight: 800; }

    .page-content {
        padding: 1rem;
        padding-bottom: 80px;
    }

    /* Show Mobile Bottom Tabs */
    .mobile-tabs {
        display: flex;
        position: fixed;
        bottom: 0; left: 0; right: 0;
        height: 64px;
        background: rgba(255,255,255,0.97);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-top: 1px solid var(--border-light);
        align-items: center;
        justify-content: space-around;
        z-index: 50;
        padding-bottom: env(safe-area-inset-bottom, 0);
    }

    .mtab {
        display: flex; flex-direction: column;
        align-items: center; gap: 2px;
        text-decoration: none;
        color: var(--text-muted);
        font-size: 0.58rem; font-weight: 600;
        padding: 4px 10px;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .mtab svg { width: 21px; height: 21px; }
    .mtab-active { color: var(--color-primary-600) !important; }

    .mtab-fab {
        width: 50px; height: 50px;
        background: var(--gradient-primary);
        border-radius: var(--radius-full);
        display: flex; align-items: center; justify-content: center;
        color: white; cursor: pointer;
        margin-top: -22px;
        box-shadow: 0 0 20px rgba(124,58,237,0.35);
        padding: 13px;
        transition: transform 0.2s;
        border: 3px solid var(--bg-body);
    }
    .mtab-fab:active { transform: scale(0.93); }
}
</style>