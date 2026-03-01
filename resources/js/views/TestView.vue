<template>
    <div class="test-view-page">
        <div v-if="loading" class="loading-state">
            <div class="shimmer-card shimmer" style="height: 200px; border-radius: 16px;"></div>
        </div>

        <template v-else-if="test">
            <!-- Test Header -->
            <div class="test-header glass-card fade-in">
                <div class="test-header-info">
                    <h1>{{ test.title }}</h1>
                    <p v-if="!test.completed_at">Soruları dikkatlice oku ve doğru şıkkı seç.</p>
                    <p v-else>Test tamamlandı! Sonucunu aşağıda görebilirsin.</p>
                </div>
                <div v-if="test.completed_at" class="test-score">
                    <span class="score-value gradient-text">%{{ test.score }}</span>
                    <span class="score-label">Başarı</span>
                </div>
            </div>

            <!-- Questions -->
            <div class="questions-list">
                <div v-for="(question, index) in questions" :key="index" class="question-block glass-card slide-up" :style="{ animationDelay: (index * 0.1) + 's' }">
                    <div class="question-number">
                        <span>Soru {{ index + 1 }}</span>
                        <span v-if="submitted" :class="['badge', answers[index] === question.correct ? 'badge-success' : 'badge-danger']">
                            {{ answers[index] === question.correct ? 'Doğru' : 'Yanlış' }}
                        </span>
                    </div>
                    
                    <!-- SVG Visual Renderer -->
                    <div v-if="question.visual_svg" class="question-visual-container" v-html="question.visual_svg"></div>

                    <p class="question-text">{{ question.question }}</p>
                    <div class="options-list">
                        <label
                            v-for="(option, optIndex) in question.options"
                            :key="optIndex"
                            :class="[
                                'option-item',
                                {
                                    'option-selected': answers[index] === optIndex,
                                    'option-correct': submitted && optIndex === question.correct,
                                    'option-wrong': submitted && answers[index] === optIndex && optIndex !== question.correct,
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
    return questions.value.filter((q, i) => answers.value[i] === q.correct).length;
});

const wrongCount = computed(() => {
    return questions.value.length - correctCount.value;
});

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
            // Restore answers
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
.question-visual-container { width: 100%; display: flex; justify-content: center; margin-bottom: 1.5rem; background: var(--bg-body); padding: 1rem; border-radius: 8px; border: 1px solid rgba(148, 163, 184, 0.1); }
:deep(.question-visual-container svg) { max-width: 100%; max-height: 250px; height: auto; display: block; }
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
</style>
