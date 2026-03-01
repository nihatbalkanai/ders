<template>
    <div class="dashboard">
        <!-- Welcome Section -->
        <div class="welcome-section fade-in">
            <div class="welcome-left">
                <h1 class="welcome-title">Hoş Geldiniz, <span class="gradient-text">{{ firstName }}</span></h1>
                <p class="welcome-sub">{{ gradeLabel }} • {{ todayLabel }}</p>
            </div>
            <div class="welcome-badge">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                <span class="wb-count">{{ stats.totalQuestions }}</span>
                <span class="wb-label">soru</span>
            </div>
        </div>

        <!-- Hero CTA Card -->
        <router-link to="/upload" class="hero-cta slide-up">
            <div class="hero-bg"></div>
            <div class="hero-content">
                <div class="hero-icon floating">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
                </div>
                <div class="hero-text">
                    <h2>Soru Analizi Başlat</h2>
                    <p>Soruyu fotoğrafla, AI anında çözsün ve benzer sorular üretsin.</p>
                </div>
                <div class="hero-arrow">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </div>
            </div>
        </router-link>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon-wrap si-purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div class="stat-data">
                    <span class="stat-value">{{ stats.totalQuestions }}</span>
                    <span class="stat-label">Toplam Soru</span>
                </div>
                <div class="stat-accent sa-purple"></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrap si-cyan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/></svg>
                </div>
                <div class="stat-data">
                    <span class="stat-value">{{ stats.totalTests }}</span>
                    <span class="stat-label">Tamamlanan Test</span>
                </div>
                <div class="stat-accent sa-cyan"></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrap si-green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div class="stat-data">
                    <span class="stat-value">%{{ stats.avgScore }}</span>
                    <span class="stat-label">Başarı Oranı</span>
                </div>
                <div class="stat-accent sa-green"></div>
            </div>
            <div class="stat-card">
                <div class="stat-icon-wrap si-orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                </div>
                <div class="stat-data">
                    <span class="stat-value">{{ stats.weakOutcomes }}</span>
                    <span class="stat-label">Kazanım</span>
                </div>
                <div class="stat-accent sa-orange"></div>
            </div>
        </div>

        <!-- Quick Actions -->
        <h3 class="section-heading">Hızlı İşlemler</h3>
        <div class="actions-grid">
            <div class="action-card" @click="generateTest" style="cursor:pointer">
                <div class="ac-icon ac-purple">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <span class="ac-label">Test Oluştur</span>
                <span class="ac-desc">AI ile otomatik test</span>
            </div>
            <router-link to="/questions" class="action-card">
                <div class="ac-icon ac-cyan">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </div>
                <span class="ac-label">Sorularım</span>
                <span class="ac-desc">Tüm sorularını gör</span>
            </router-link>
            <router-link to="/tests" class="action-card">
                <div class="ac-icon ac-green">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                </div>
                <span class="ac-label">Testlerim</span>
                <span class="ac-desc">Çözdüğün testler</span>
            </router-link>
            <router-link to="/profile" class="action-card">
                <div class="ac-icon ac-orange">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <span class="ac-label">Profilim</span>
                <span class="ac-desc">Hesap ayarları</span>
            </router-link>
        </div>

        <!-- Recent Uploads -->
        <template v-if="recentQuestions.length > 0">
            <h3 class="section-heading">Son Yüklenenler</h3>
            <div class="recent-list">
                <div v-for="q in recentQuestions" :key="q.id" class="recent-item">
                    <div :class="['ri-dot', q.status === 'analyzed' ? 'dot-ok' : 'dot-wait']"></div>
                    <div class="ri-info">
                        <span class="ri-subj">{{ q.subject?.name || 'Analiz ediliyor...' }}</span>
                        <span class="ri-out">{{ q.outcome?.name || 'Bekliyor' }}</span>
                    </div>
                    <span class="ri-time">{{ timeAgo(q.created_at) }}</span>
                </div>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.js';
import api from '@/api.js';

const authStore = useAuthStore();
const router = useRouter();

const stats = ref({ totalQuestions: 0, totalTests: 0, avgScore: 0, weakOutcomes: 0 });
const recentQuestions = ref([]);

const firstName = computed(() => (authStore.userName || '').split(' ')[0]);
const gradeLabel = computed(() => `${authStore.user?.grade_level || 5}. Sınıf`);
const todayLabel = computed(() => new Date().toLocaleDateString('tr-TR', { weekday: 'long', day: 'numeric', month: 'long' }));

const fetchDashboard = async () => {
    try {
        const [qRes, tRes] = await Promise.all([api.get('/questions'), api.get('/tests')]);
        const questions = qRes.data.data || [];
        const tests = tRes.data.data || [];
        stats.value.totalQuestions = questions.length;
        stats.value.totalTests = tests.filter(t => t.completed_at).length;
        const done = tests.filter(t => t.score !== null);
        if (done.length) stats.value.avgScore = Math.round(done.reduce((s, t) => s + t.score, 0) / done.length);
        const oc = {};
        questions.forEach(q => { if (q.outcome_id) oc[q.outcome_id] = 1; });
        stats.value.weakOutcomes = Object.keys(oc).length;
        recentQuestions.value = questions.slice(0, 5);
    } catch (e) { console.error(e); }
};

const generateTest = async () => {
    try {
        const res = await api.post('/tests/generate');
        router.push({ name: 'test-view', params: { id: res.data.data.id } });
    } catch (e) { alert(e.response?.data?.message || 'Test oluşturulamadı.'); }
};

const timeAgo = (d) => {
    if (!d) return '';
    const m = Math.floor((Date.now() - new Date(d)) / 60000);
    if (m < 1) return 'Az önce';
    if (m < 60) return `${m} dk`;
    const h = Math.floor(m / 60);
    if (h < 24) return `${h} sa`;
    return `${Math.floor(h / 24)} gün`;
};

onMounted(fetchDashboard);
</script>

<style scoped>
.dashboard { max-width: 100%; }

/* ═══ Welcome ═══ */
.welcome-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}
.welcome-title {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--text-primary);
    letter-spacing: -0.02em;
}
.welcome-sub {
    font-size: 0.82rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

.welcome-badge {
    display: flex;
    align-items: center;
    gap: 0.4rem;
    background: var(--color-primary-50);
    border: 1px solid var(--color-primary-200);
    border-radius: var(--radius-full);
    padding: 0.4rem 0.85rem;
}
.welcome-badge svg { width: 14px; height: 14px; color: var(--color-primary-500); }
.wb-count { font-size: 0.95rem; font-weight: 800; color: var(--color-primary-700); }
.wb-label { font-size: 0.72rem; color: var(--color-primary-500); font-weight: 600; }

/* ═══ Hero CTA ═══ */
.hero-cta {
    display: block;
    position: relative;
    border-radius: var(--radius-xl);
    overflow: hidden;
    text-decoration: none;
    margin-bottom: 1.75rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(124,58,237,0.2);
}
.hero-cta:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(124,58,237,0.3);
}

.hero-bg {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, #7c3aed, #6366f1, #2563eb);
    background-size: 200% 200%;
    animation: gradientShift 6s ease infinite;
}
@keyframes gradientShift {
    0%   { background-position: 0% 50%; }
    50%  { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.hero-content {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 1.25rem;
    padding: 1.5rem 1.75rem;
    color: white;
}

.hero-icon {
    width: 52px; height: 52px;
    flex-shrink: 0;
    background: rgba(255,255,255,0.2);
    border-radius: var(--radius-lg);
    display: flex; align-items: center; justify-content: center;
    border: 1px solid rgba(255,255,255,0.25);
}
.hero-icon svg { width: 26px; height: 26px; }

.hero-text { flex: 1; }
.hero-text h2 { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.15rem; }
.hero-text p { font-size: 0.85rem; opacity: 0.9; line-height: 1.4; }

.hero-arrow {
    width: 32px; height: 32px;
    background: rgba(255,255,255,0.2);
    border-radius: var(--radius-full);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: transform 0.3s;
}
.hero-arrow svg { width: 18px; height: 18px; }
.hero-cta:hover .hero-arrow { transform: translateX(4px); }

/* ═══ Stats Grid ═══ */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    padding: 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-card);
    position: relative;
    overflow: hidden;
}
.stat-card:hover {
    border-color: var(--border-hover);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Top accent line */
.stat-accent {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    border-radius: 3px 3px 0 0;
}
.sa-purple { background: var(--gradient-primary); }
.sa-cyan   { background: linear-gradient(90deg, #06b6d4, #22d3ee); }
.sa-green  { background: linear-gradient(90deg, #10b981, #34d399); }
.sa-orange { background: linear-gradient(90deg, #f97316, #fb923c); }

.stat-icon-wrap {
    width: 40px; height: 40px;
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.stat-icon-wrap svg { width: 19px; height: 19px; }

.si-purple { background: var(--color-primary-50); color: var(--color-primary-600); }
.si-cyan   { background: #ecfeff; color: #0891b2; }
.si-green  { background: #ecfdf5; color: #059669; }
.si-orange { background: #fff7ed; color: #ea580c; }

.stat-data { display: flex; flex-direction: column; }
.stat-value { font-size: 1.3rem; font-weight: 800; color: var(--text-primary); line-height: 1.2; }
.stat-label { font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; font-weight: 600; margin-top: 1px; }

/* ═══ Section ═══ */
.section-heading {
    font-size: 0.88rem;
    font-weight: 700;
    color: var(--text-secondary);
    margin-bottom: 0.75rem;
    letter-spacing: -0.01em;
}

/* ═══ Actions Grid ═══ */
.actions-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-bottom: 2rem;
}

.action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1.25rem 1rem;
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-lg);
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: var(--shadow-card);
}
.action-card:hover {
    border-color: var(--border-hover);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.ac-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-primary);
}
.ac-desc {
    font-size: 0.7rem;
    color: var(--text-muted);
    font-weight: 500;
}

.ac-icon {
    width: 44px; height: 44px;
    border-radius: var(--radius-lg);
    display: flex; align-items: center; justify-content: center;
}
.ac-icon svg { width: 21px; height: 21px; }

.ac-purple { background: var(--color-primary-50); color: var(--color-primary-600); }
.ac-cyan   { background: #ecfeff; color: #0891b2; }
.ac-green  { background: #ecfdf5; color: #059669; }
.ac-orange { background: #fff7ed; color: #ea580c; }

/* ═══ Recent List ═══ */
.recent-list { display: flex; flex-direction: column; gap: 0.4rem; }
.recent-item {
    display: flex; align-items: center; gap: 0.75rem;
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    padding: 0.75rem 1rem;
    transition: all 0.2s;
    box-shadow: var(--shadow-xs);
}
.recent-item:hover { border-color: var(--border-hover); box-shadow: var(--shadow-sm); }

.ri-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dot-ok { background: var(--color-success); box-shadow: 0 0 6px rgba(16,185,129,0.3); }
.dot-wait { background: var(--color-warning); box-shadow: 0 0 6px rgba(245,158,11,0.3); }
.ri-info { flex: 1; }
.ri-subj { display: block; font-size: 0.82rem; font-weight: 600; color: var(--text-primary); }
.ri-out { font-size: 0.7rem; color: var(--text-muted); }
.ri-time { font-size: 0.65rem; color: var(--text-muted); flex-shrink: 0; font-weight: 500; }

/* ═══ Responsive ═══ */
@media (max-width: 768px) {
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .actions-grid { grid-template-columns: repeat(2, 1fr); }
    .welcome-title { font-size: 1.3rem; }
    .hero-content { padding: 1.25rem; gap: 1rem; }
}
</style>
