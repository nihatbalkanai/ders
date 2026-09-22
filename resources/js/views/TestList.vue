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

        <!-- Üst İstatistikler -->
        <div v-if="!loading && tests.length > 0" class="stats-row fade-in">
            <div class="stat-chip">
                <span class="stat-chip-value">{{ tests.length }}</span>
                <span class="stat-chip-label">Toplam Test</span>
            </div>
            <div class="stat-chip">
                <span class="stat-chip-value">{{ completedCount }}</span>
                <span class="stat-chip-label">Çözülen</span>
            </div>
            <div class="stat-chip">
                <span class="stat-chip-value">{{ pendingCount }}</span>
                <span class="stat-chip-label">Bekleyen</span>
            </div>
            <div class="stat-chip">
                <span class="stat-chip-value" :class="avgScore >= 70 ? 'text-success' : avgScore >= 40 ? 'text-warning' : 'text-danger'">
                    %{{ avgScore }}
                </span>
                <span class="stat-chip-label">Ort. Başarı</span>
            </div>
        </div>

        <!-- Filtre -->
        <div v-if="!loading && tests.length > 0" class="filter-row fade-in">
            <button :class="['filter-btn', activeFilter === 'all' && 'active']" @click="activeFilter = 'all'">
                Tümü <span class="filter-count">{{ tests.length }}</span>
            </button>
            <button :class="['filter-btn', activeFilter === 'pending' && 'active']" @click="activeFilter = 'pending'">
                Çözülmedi <span class="filter-count">{{ pendingCount }}</span>
            </button>
            <button :class="['filter-btn', activeFilter === 'completed' && 'active']" @click="activeFilter = 'completed'">
                Tamamlandı <span class="filter-count">{{ completedCount }}</span>
            </button>
        </div>

        <!-- Loading -->
        <div v-if="loading" class="loading-grid">
            <div v-for="i in 4" :key="i" class="shimmer-card shimmer"></div>
        </div>

        <!-- Boş durum -->
        <div v-else-if="tests.length === 0" class="empty-state glass-card">
            <div class="empty-icon-wrap">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="empty-icon"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            </div>
            <h3>Henüz test yok</h3>
            <p>Önce birkaç soru yükle, sonra test oluştur!</p>
        </div>

        <!-- Test listesi -->
        <div v-else-if="paginatedTests.length > 0" class="tests-grid">
            <div v-for="t in paginatedTests" :key="t.id" class="test-card glass-card slide-up">
                <router-link :to="{ name: 'test-view', params: { id: t.id } }" class="test-card-link">
                    <!-- Durum şeridi -->
                    <div :class="['card-status-bar', t.completed_at ? 'status-done' : 'status-pending']"></div>
                    
                    <div class="card-body">
                        <div class="card-top-row">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span :class="['status-dot', t.completed_at ? 'dot-done' : 'dot-pending']"></span>
                                <span class="card-date">{{ formatDate(t.created_at) }}</span>
                            </div>
                        </div>

                        <h3 class="card-title">{{ t.title || 'Başlıksız Test' }}</h3>

                        <!-- Örnek Soru Önizlemesi -->
                        <div class="card-preview" v-if="previewQuestion(t)">
                            <p><strong>Örnek Soru:</strong> "{{ truncateText(previewQuestion(t), 80) }}"</p>
                        </div>

                        <div class="card-info-row">
                            <div class="info-chip">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                                {{ questionCount(t) }} Soru
                            </div>
                            <div class="info-chip" v-if="t.completed_at">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                Çözüldü
                            </div>
                            <div class="info-chip" v-else>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                Bekliyor
                            </div>
                        </div>

                        <!-- Skor -->
                        <div v-if="t.score !== null && t.completed_at" class="card-score-section">
                            <div class="score-bar-track">
                                <div class="score-bar-fill" :class="scoreClass(t.score)" :style="{ width: t.score + '%' }"></div>
                            </div>
                            <span class="score-text" :class="scoreClass(t.score)">%{{ t.score }}</span>
                        </div>
                    </div>
                </router-link>

                <button class="card-delete-btn" @click.prevent="deleteTest(t.id)" title="Testi Sil">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
            </div>
        </div>

        <!-- Filtreli boş durum -->
        <div v-else class="empty-state glass-card">
            <p>Bu filtrede gösterilecek test yok.</p>
        </div>

        <!-- Sayfalama -->
        <div v-if="totalPages > 1" class="pagination fade-in">
            <button class="page-btn" :disabled="currentPage === 1" @click="currentPage--">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button
                v-for="page in visiblePages"
                :key="page"
                :class="['page-btn', currentPage === page && 'page-active']"
                @click="currentPage = page"
            >{{ page }}</button>
            <button class="page-btn" :disabled="currentPage === totalPages" @click="currentPage++">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRouter } from 'vue-router';
import Swal from 'sweetalert2';
import api from '@/api.js';

const router = useRouter();
const tests = ref([]);
const loading = ref(true);
const generating = ref(false);
const activeFilter = ref('all');
const currentPage = ref(1);
const perPage = 8;

// Filtre değiştiğinde sayfa 1'e dön
watch(activeFilter, () => { currentPage.value = 1; });

const filteredTests = computed(() => {
    if (activeFilter.value === 'completed') return tests.value.filter(t => t.completed_at);
    if (activeFilter.value === 'pending') return tests.value.filter(t => !t.completed_at);
    return tests.value;
});

const totalPages = computed(() => Math.ceil(filteredTests.value.length / perPage));

const paginatedTests = computed(() => {
    const start = (currentPage.value - 1) * perPage;
    return filteredTests.value.slice(start, start + perPage);
});

const visiblePages = computed(() => {
    const pages = [];
    const total = totalPages.value;
    const cur = currentPage.value;
    let start = Math.max(1, cur - 2);
    let end = Math.min(total, cur + 2);
    if (end - start < 4) {
        if (start === 1) end = Math.min(total, start + 4);
        else start = Math.max(1, end - 4);
    }
    for (let i = start; i <= end; i++) pages.push(i);
    return pages;
});

const completedCount = computed(() => tests.value.filter(t => t.completed_at).length);
const pendingCount = computed(() => tests.value.filter(t => !t.completed_at).length);
const avgScore = computed(() => {
    const scored = tests.value.filter(t => t.score !== null && t.completed_at);
    if (scored.length === 0) return 0;
    return Math.round(scored.reduce((sum, t) => sum + t.score, 0) / scored.length);
});

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
    } catch { return 0; }
};

const previewQuestion = (test) => {
    try {
        const data = typeof test.questions_data === 'string' ? JSON.parse(test.questions_data) : test.questions_data;
        if (Array.isArray(data) && data.length > 0) {
            return data[0].question_text || data[0].question || '';
        }
    } catch { return ''; }
    return '';
};

const truncateText = (text, maxLength) => {
    if (!text) return '';
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
};

const scoreClass = (score) => {
    if (score >= 70) return 'score-high';
    if (score >= 40) return 'score-mid';
    return 'score-low';
};

const deleteTest = async (id) => {
    const result = await Swal.fire({
        title: 'Emin misiniz?',
        text: 'Bu testi silmek istediğinize emin misiniz?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#475569',
        confirmButtonText: 'Sil',
        cancelButtonText: 'İptal',
        background: '#1e293b',
        color: '#f8fafc',
    });
    if (result.isConfirmed) {
        try {
            await api.delete(`/tests/${id}`);
            tests.value = tests.value.filter(t => t.id !== id);
            Swal.fire({ title: 'Silindi!', icon: 'success', timer: 1200, showConfirmButton: false, background: '#1e293b', color: '#f8fafc' });
        } catch (e) {
            Swal.fire({ title: 'Hata!', text: 'Silme sırasında sorun oluştu.', icon: 'error', background: '#1e293b', color: '#f8fafc' });
        }
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    return new Date(dateStr).toLocaleDateString('tr-TR', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
};

onMounted(fetchTests);
</script>

<style scoped>
.tests-page { max-width: 900px; }

/* Header */
.page-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem; gap: 1rem; flex-wrap: wrap; }
.page-header h1 { font-size: 1.75rem; font-weight: 800; color: var(--text-primary, #1e293b); margin-bottom: 0.25rem; }
.page-header p { color: var(--color-dark-400, #94a3b8); font-size: 0.85rem; }

/* İstatistikler */
.stats-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 1.25rem;
}
.stat-chip {
    background: var(--bg-card, #fff);
    border: 1px solid rgba(148, 163, 184, 0.15);
    border-radius: 12px;
    padding: 0.75rem;
    text-align: center;
    display: flex;
    flex-direction: column;
    gap: 0.2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.stat-chip-value { font-size: 1.35rem; font-weight: 800; color: #1e293b; }
.stat-chip-label { font-size: 0.68rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
.text-success { color: #16a34a !important; }
.text-warning { color: #d97706 !important; }
.text-danger { color: #dc2626 !important; }

/* Filtreler */
.filter-row {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 1.25rem;
    flex-wrap: wrap;
}
.filter-btn {
    padding: 0.4rem 0.85rem;
    border-radius: 20px;
    border: 1px solid rgba(148, 163, 184, 0.2);
    background: transparent;
    color: #64748b;
    font-size: 0.78rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.4rem;
}
.filter-btn:hover { border-color: var(--color-primary-500); color: var(--color-primary-500); }
.filter-btn.active {
    background: var(--color-primary-500);
    color: white;
    border-color: var(--color-primary-500);
}
.filter-count {
    background: rgba(100,116,139,0.12);
    padding: 0.1rem 0.4rem;
    border-radius: 10px;
    font-size: 0.7rem;
}
.filter-btn.active .filter-count { background: rgba(255,255,255,0.25); color: white; }

/* Loading */
.loading-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; }
.shimmer-card { height: 140px; border-radius: 14px; }

/* Test Grid */
.tests-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 0.75rem;
}

/* Test Kartı */
.test-card {
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    transition: all 0.25s ease;
    background: var(--bg-card, #fff);
    border: 1px solid rgba(148, 163, 184, 0.12);
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}
.test-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    border-color: var(--color-primary-400, #a78bfa);
}
.test-card-link {
    display: block;
    text-decoration: none;
    color: inherit;
}

/* Durum çizgisi */
.card-status-bar {
    height: 3px;
    width: 100%;
}
.status-done { background: linear-gradient(90deg, #22c55e, #16a34a); }
.status-pending { background: linear-gradient(90deg, #f59e0b, #d97706); }

.card-body { padding: 1rem 1rem 0.85rem; }

.card-top-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}
.status-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}
.dot-done { background: #22c55e; box-shadow: 0 0 6px rgba(34,197,94,0.4); }
.dot-pending { background: #f59e0b; box-shadow: 0 0 6px rgba(245,158,11,0.4); }
.card-date { font-size: 0.7rem; color: #94a3b8; }

.card-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.4rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-preview {
    font-size: 0.75rem;
    color: #475569;
    margin-bottom: 0.7rem;
    background: #f8fafc;
    padding: 0.5rem;
    border-radius: 6px;
    border-left: 3px solid var(--color-primary-400, #a78bfa);
    font-style: italic;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.card-info-row {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.6rem;
    flex-wrap: wrap;
}
.info-chip {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    font-size: 0.7rem;
    color: #64748b;
    background: rgba(100,116,139,0.08);
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
}

/* Skor */
.card-score-section {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.score-bar-track {
    flex: 1;
    height: 5px;
    background: rgba(148,163,184,0.12);
    border-radius: 3px;
    overflow: hidden;
}
.score-bar-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.5s ease;
}
.score-bar-fill.score-high { background: linear-gradient(90deg, #22c55e, #16a34a); }
.score-bar-fill.score-mid { background: linear-gradient(90deg, #f59e0b, #d97706); }
.score-bar-fill.score-low { background: linear-gradient(90deg, #ef4444, #dc2626); }
.score-text { font-size: 0.78rem; font-weight: 800; min-width: 32px; text-align: right; }
.score-text.score-high { color: #16a34a; }
.score-text.score-mid { color: #d97706; }
.score-text.score-low { color: #dc2626; }

/* Sil butonu — sağ alt köşeye taşındı, durum çizgisiyle çakışmaz */
.card-delete-btn {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(220, 38, 38, 0.08);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.15);
    border-radius: 6px;
    width: 26px;
    height: 26px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    opacity: 0;
    z-index: 2;
}
.test-card:hover .card-delete-btn { opacity: 1; }
.card-delete-btn:hover { background: #ef4444; color: white; transform: scale(1.1); }

/* Boş durum */
.empty-state { padding: 3rem; text-align: center; }
.empty-icon-wrap { margin-bottom: 1rem; }
.empty-icon { width: 48px; height: 48px; color: #94a3b8; }
.empty-state h3 { color: #1e293b; font-size: 1rem; font-weight: 700; margin-bottom: 0.4rem; }
.empty-state p { color: #64748b; font-size: 0.85rem; }

/* Sayfalama */
.pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 0.4rem;
    margin-top: 1.5rem;
    padding-bottom: 2rem;
}
.page-btn {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    border: 1px solid rgba(148,163,184,0.15);
    background: rgba(148,163,184,0.05);
    color: #64748b;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.page-btn:hover:not(:disabled) { border-color: var(--color-primary-500); color: var(--color-primary-500); }
.page-btn:disabled { opacity: 0.3; cursor: default; }
.page-active {
    background: var(--color-primary-500) !important;
    color: white !important;
    border-color: var(--color-primary-500) !important;
}

/* Responsive */
@media (max-width: 640px) {
    .tests-grid { grid-template-columns: 1fr; }
    .stats-row { grid-template-columns: repeat(2, 1fr); }
    .loading-grid { grid-template-columns: 1fr; }
}
</style>
