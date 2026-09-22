<template>
    <div class="test-view-page">
        <div v-if="loading" class="loading-state">
            <div class="shimmer-card shimmer" style="height: 200px; border-radius: 16px;"></div>
        </div>

        <template v-else-if="test">
            <!-- Print Header (Gizli, Sadece Yazdırırken Çıkar) -->
            <div class="print-only-header">
                <div class="print-header-left">
                    <span class="print-brain">🧠</span>
                    <div class="print-brand-text">SmartTest <span>AI</span></div>
                </div>
                <div class="print-header-right">
                    <h2>{{ test.title }}</h2>
                    <p>{{ formatDate(test.created_at) }}</p>
                </div>
            </div>
            
            <!-- Test Header -->
            <div class="test-header glass-card fade-in">
                <div class="test-header-info">
                    <h1>{{ test.title }}</h1>
                    <p v-if="!test.completed_at">Soruları dikkatlice oku ve doğru şıkkı seç.</p>
                    <p v-else>Test tamamlandı! Sonucunu aşağıda görebilirsin.</p>
                </div>
                <div class="test-header-actions" style="display: flex; gap: 1rem; align-items: center;">
                    <button @click="printTest" class="btn-secondary btn-sm print-btn">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
                        Yazdır
                    </button>
                    <div v-if="test.completed_at" class="test-score">
                        <span class="score-value gradient-text">%{{ test.score }}</span>
                        <span class="score-label">Başarı</span>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="questions-list">
                <div v-for="(question, index) in questions" :key="index" class="question-block glass-card slide-up" :style="{ animationDelay: (index * 0.1) + 's' }">
                    <div class="question-number">
                        <span>Soru {{ index + 1 }}</span>
                        <span v-if="question.is_original" class="badge badge-primary" style="font-size:0.7rem">Orijinal Soru</span>
                        <span v-if="submitted" :class="['badge', answers[index] === getCorrect(question) ? 'badge-success' : 'badge-danger']">
                            {{ answers[index] === getCorrect(question) ? 'Doğru' : 'Yanlış' }}
                        </span>
                    </div>
                    
                    <!-- AI Generated Raw SVG (New) -->
                    <div v-if="question.visual_svg" class="question-svg-container" v-html="question.visual_svg"></div>

                    <!-- Original Image -->
                    <div v-if="question.original_image" class="question-visual-container">
                        <img :src="question.original_image" alt="Orijinal Soru" class="original-img" />
                    </div>

                    <!-- Question text: hide for originals with image (image already shows the question) -->
                    <p v-if="!(question.is_original && question.original_image)" class="question-text">{{ question.question_text || question.question }}</p>
                    <div class="options-list">
                        <label
                            v-for="(option, optIndex) in question.options"
                            :key="optIndex"
                            :class="[
                                'option-item',
                                {
                                    'option-selected': answers[index] === optIndex,
                                    'option-correct': submitted && optIndex === getCorrect(question),
                                    'option-wrong': submitted && answers[index] === optIndex && optIndex !== getCorrect(question),
                                    'option-disabled': submitted,
                                }
                            ]"
                        >
                            <input
                                type="radio"
                                :name="'q_' + index"
                                :value="optIndex"
                                v-model="answers[index]"
                                :disabled="submitted"
                                class="option-radio"
                            />
                            <span class="option-letter">{{ ['A', 'B', 'C', 'D'][optIndex] }}</span>
                            <span class="option-text">{{ option }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div v-if="!submitted" class="submit-area">
                <button @click="submitTest" class="btn-primary btn-lg" :disabled="submitting || !allAnswered">
                    {{ submitting ? 'Değerlendiriliyor...' : 'Testi Bitir' }}
                </button>
                <p v-if="!allAnswered" class="submit-hint">Tüm soruları cevapladığından emin ol</p>
            </div>

            <div v-else class="result-area glass-card">
                <h2>Test <span class="gradient-text">Sonucu</span></h2>
                <div class="result-stats">
                    <div class="result-stat">
                        <span class="result-stat-value" style="color: var(--color-success-400)">{{ correctCount }}</span>
                        <span class="result-stat-label">Doğru</span>
                    </div>
                    <div class="result-stat">
                        <span class="result-stat-value" style="color: var(--color-danger-400)">{{ wrongCount }}</span>
                        <span class="result-stat-label">Yanlış</span>
                    </div>
                    <div class="result-stat">
                        <span class="result-stat-value gradient-text">%{{ test.score }}</span>
                        <span class="result-stat-label">Başarı</span>
                    </div>
                </div>
                <router-link to="/tests" class="btn-secondary">Testlerime Dön</router-link>
            </div>
        </template>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/api.js';

const route = useRoute();
const test = ref(null);
const questions = ref([]);
const answers = ref({});
const loading = ref(true);
const submitting = ref(false);
const submitted = ref(false);

const allAnswered = computed(() => {
    return questions.value.every((_, i) => answers.value[i] !== undefined);
});

const correctCount = computed(() => {
    return questions.value.filter((q, i) => answers.value[i] === getCorrect(q)).length;
});

const getCorrect = (q) => {
    return q.correct_answer !== undefined ? q.correct_answer : q.correct;
};

const wrongCount = computed(() => {
    return questions.value.length - correctCount.value;
});

const formatDate = (dateStr) => {
    if (!dateStr) return '';
    const date = new Date(dateStr);
    return date.toLocaleDateString('tr-TR', { day: '2-digit', month: '2-digit', year: 'numeric' });
};

const fetchTest = async () => {
    try {
        const res = await api.get(`/tests/${route.params.id}`);
        test.value = res.data.data;
        const data = typeof test.value.questions_data === 'string'
            ? JSON.parse(test.value.questions_data)
            : test.value.questions_data;
        questions.value = data || [];
        if (test.value.completed_at) {
            submitted.value = true;
            if (test.value.user_answers) {
                const ua = typeof test.value.user_answers === 'string'
                    ? JSON.parse(test.value.user_answers)
                    : test.value.user_answers;
                answers.value = ua || {};
            }
        }
    } catch (e) {
        console.error(e);
    } finally {
        loading.value = false;
    }
};

const submitTest = async () => {
    submitting.value = true;
    try {
        const res = await api.post(`/tests/${route.params.id}/submit`, {
            answers: answers.value,
        });
        test.value = res.data.data;
        submitted.value = true;
    } catch (e) {
        alert(e.response?.data?.message || 'Gönderilirken hata oluştu.');
    } finally {
        submitting.value = false;
    }
};

const printTest = () => {
    window.print();
};

onMounted(fetchTest);
</script>

<style scoped>
.test-view-page { max-width: 800px; margin: 0 auto; }
.test-header { display: flex; justify-content: space-between; align-items: center; padding: 1.5rem; margin-bottom: 1.5rem; }
.test-header h1 { font-size: 1.25rem; font-weight: 800; color: white; margin-bottom: 0.25rem; }
.test-header p { color: var(--color-dark-400); font-size: 0.85rem; }
.test-score { text-align: center; }
.score-value { font-size: 2.5rem; font-weight: 900; display: block; }
.score-label { font-size: 0.75rem; color: var(--color-dark-500); }
.questions-list { display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem; }
.question-block { padding: 1.5rem; }
.question-number { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; font-size: 0.8rem; font-weight: 700; color: var(--color-primary-400); }
.question-visual-container {
    margin: 1.5rem 0;
    text-align: center;
    background: var(--bg-body);
    padding: 1rem;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-light);
}

.question-svg-container {
    margin: 1.5rem 0;
    display: flex;
    justify-content: center;
    background: white;
    padding: 1rem;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-light);
    box-shadow: inset 0 0 10px rgba(0,0,0,0.02);
}

.question-svg-container:deep(svg) {
    max-width: 100%;
    height: auto;
    max-height: 300px;
}

.original-img {
    max-width: 100%;
    max-height: 400px;
    border-radius: 8px;
    object-fit: contain;
}
.question-text { font-size: 0.95rem; color: var(--text-primary); line-height: 1.7; margin-bottom: 1.5rem; font-weight: 500; }
.options-list { display: flex; flex-direction: column; gap: 0.5rem; }
.option-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; border-radius: 10px; border: 1px solid rgba(148, 163, 184, 0.15); cursor: pointer; transition: all 0.2s ease; }
.option-item:hover:not(.option-disabled) { border-color: var(--color-primary-500); background: rgba(108, 71, 255, 0.05); }
.option-selected { border-color: var(--color-primary-500) !important; background: rgba(108, 71, 255, 0.1) !important; }
.option-correct { border-color: var(--color-success-500) !important; background: rgba(34, 197, 94, 0.1) !important; }
.option-wrong { border-color: var(--color-danger-500) !important; background: rgba(239, 68, 68, 0.1) !important; }
.option-disabled { cursor: default; }
.option-radio { display: none; }
.option-letter { width: 28px; height: 28px; border-radius: 8px; background: rgba(148, 163, 184, 0.1); display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; color: var(--color-dark-400); flex-shrink: 0; }
.option-text { font-size: 0.85rem; color: var(--color-dark-300); }
.submit-area { text-align: center; margin-bottom: 2rem; }
.btn-lg { padding: 0.875rem 2.5rem; }
.submit-hint { color: var(--color-dark-500); font-size: 0.8rem; margin-top: 0.75rem; }
.result-area { padding: 2rem; text-align: center; margin-bottom: 2rem; }
.result-area h2 { font-size: 1.5rem; font-weight: 800; color: white; margin-bottom: 1.5rem; }
.result-stats { display: flex; justify-content: center; gap: 3rem; margin-bottom: 2rem; }
.result-stat { display: flex; flex-direction: column; align-items: center; }
.result-stat-value { font-size: 2rem; font-weight: 900; }
.result-stat-label { font-size: 0.75rem; color: var(--color-dark-500); margin-top: 0.25rem; }
.loading-state { max-width: 800px; margin: 0 auto; }
.btn-sm { padding: 0.5rem 1rem; display: flex; align-items: center; gap: 0.5rem; }

.print-only-header { display: none; }
</style>

<style>
/* Global Print Styles (Unscoped to hide shells and format page) */
@media print {
    @page {
        margin: 10mm; /* A4 sayfa, en dar kenar boşlukları */
        size: A4;
    }
    body { background: white !important; }
    body * {
        visibility: hidden;
    }
    
    .test-view-page, .test-view-page * {
        visibility: visible;
    }
    
    .test-view-page {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        margin: 0;
        padding: 0;
        color: black !important;
    }
    
    /* Hiding Shells using Global Classes */
    .sidebar, .top-header, .mobile-tabs, .mobile-menu-btn, .mobile-drawer, .header-actions { display: none !important; }
    
    /* Yazdırılırken gizlenecek bileşen içindeki alanlar */
    .test-header, .print-btn, .submit-area, .result-area, .badge {
        display: none !important;
    }
    
    /* Print Header (Logo ve Başlık) */
    .print-only-header {
        display: flex !important;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        padding-bottom: 4px;
        border-bottom: 2px solid #e5e7eb;
    }
    .print-header-left {
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .print-brain {
        background: #f3e8ff;
        border-radius: 6px;
        padding: 3px;
        font-size: 14pt;
        display: inline-flex;
        border: 1px solid #d8b4fe;
    }
    .print-brand-text {
        font-size: 13pt;
        font-weight: 800;
        color: #1e293b !important;
    }
    .print-brand-text span {
        color: #7c3aed !important;
        font-size: 7.5pt;
        background: #f3e8ff;
        padding: 2px 4px;
        border-radius: 4px;
        vertical-align: super;
    }
    .print-header-right {
        text-align: right;
    }
    .print-header-right h2 {
        font-size: 10.5pt;
        color: #000 !important;
        margin: 0 0 1px 0;
        font-weight: bold;
    }
    .print-header-right p {
        font-size: 8.5pt;
        color: #475569 !important;
        margin: 0;
    }

    /* 2 Sütunlu Sayfa Düzeni */
    .questions-list {
        column-count: 2;
        column-gap: 15px;
        margin-bottom: 0;
    }

    /* Soru Kartı */
    .question-block {
        break-inside: avoid;
        border: none !important; box-shadow: none !important; padding: 0 !important; margin-bottom: 6px !important; background: transparent !important;
        page-break-inside: avoid;
    }
    
    /* Soru Metni ve Numarası */
    .question-number {
        font-size: 8.5pt !important; font-weight: bold !important; margin-bottom: 2px !important; color: black !important;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 2px;
    }
    .question-text {
        font-size: 8pt !important; color: black !important; line-height: 1.15 !important; margin-bottom: 3px !important; font-weight: normal !important;
    }

    /* Görsel/SVG */
    .question-svg-container, .question-visual-container {
        margin: 2px 0 !important; padding: 0 !important; border: none !important; box-shadow: none !important; background: transparent !important;
        display: flex; justify-content: flex-start;
    }
    .question-svg-container:deep(svg) { max-height: 70px !important; width: auto !important; }
    .original-img { max-height: 70px !important; width: auto !important; }

    /* Şıklar */
    .options-list {
        display: grid !important;
        grid-template-columns: 1fr 1fr;
        gap: 2px 4px !important;
    }
    .option-item {
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        background: transparent !important;
        gap: 3px !important;
        align-items: flex-start !important;
    }
    .option-letter {
        width: 12px !important; height: 12px !important; font-size: 7pt !important; border-radius: 50% !important; border: 1px solid black !important; background: transparent !important; display: inline-flex !important; flex-shrink: 0; margin-top: 1px;
        justify-content: center; align-items: center;
    }
    .option-text {
        font-size: 7.5pt !important; color: black !important; line-height: 1.1 !important; padding-top: 1px;
    }
    
    /* İşaretlenmiş şık formatı */
    .option-selected { background: transparent !important; border-color: transparent !important; }
    .option-selected .option-letter { background: #dcdcdc !important; }
}
</style>
