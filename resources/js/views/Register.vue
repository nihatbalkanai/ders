<template>
    <div class="register-page">
        <div class="blob-bg blob-1"></div>
        <div class="blob-bg blob-2"></div>

        <div class="auth-card glass-card slide-up">
            <div class="auth-brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                </div>
                <span class="brand-txt">SmartTest <span class="brand-ai">AI</span></span>
            </div>
            <h1>Kayıt Ol</h1>
            <p class="auth-subtitle">Hemen ücretsiz hesabını oluştur</p>

            <div v-if="authStore.errors?.general" class="auth-error">
                {{ authStore.errors.general[0] }}
            </div>

            <form @submit.prevent="handleRegister" class="auth-form">
                <div class="form-group">
                    <label class="input-label">Ad Soyad</label>
                    <input v-model="form.name" type="text" class="input-field" placeholder="Ali Yılmaz" required />
                    <span v-if="authStore.errors?.name" class="field-error">{{ authStore.errors.name[0] }}</span>
                </div>
                <div class="form-group">
                    <label class="input-label">E-posta</label>
                    <input v-model="form.email" type="email" class="input-field" placeholder="ali@email.com" required />
                    <span v-if="authStore.errors?.email" class="field-error">{{ authStore.errors.email[0] }}</span>
                </div>
                <div class="form-group">
                    <label class="input-label">Sınıf Seviyesi</label>
                    <div class="grade-grid">
                        <button v-for="g in [5,6,7,8]" :key="g" type="button"
                            :class="['grade-btn', form.grade_level === g && 'grade-active']"
                            @click="form.grade_level = g">
                            {{ g }}. Sınıf
                        </button>
                    </div>
                </div>
                <div class="form-group">
                    <label class="input-label">Şifre</label>
                    <input v-model="form.password" type="password" class="input-field" placeholder="En az 8 karakter" required />
                    <span v-if="authStore.errors?.password" class="field-error">{{ authStore.errors.password[0] }}</span>
                </div>
                <div class="form-group">
                    <label class="input-label">Şifre Tekrar</label>
                    <input v-model="form.password_confirmation" type="password" class="input-field" placeholder="Şifreyi tekrar gir" required />
                </div>
                <button type="submit" class="btn-primary btn-full" :disabled="authStore.loading">
                    {{ authStore.loading ? 'Kaydediliyor...' : '🚀 Kayıt Ol' }}
                </button>
            </form>

            <p class="auth-footer">
                Zaten hesabın var mı? <router-link to="/login" class="auth-link">Giriş Yap</router-link>
            </p>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/auth.js';

const router = useRouter();
const authStore = useAuthStore();
const form = reactive({ name: '', email: '', password: '', password_confirmation: '', grade_level: 5 });

const handleRegister = async () => {
    const ok = await authStore.register(form);
    if (ok) router.push('/dashboard');
};
</script>

<style scoped>
.register-page {
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    padding: 1rem;
    background: var(--bg-body);
    position: relative; overflow: hidden;
}

.auth-card { width: 100%; max-width: 440px; padding: 2rem 2.5rem; text-align: center; }

.auth-brand { margin-bottom: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem; }
.brand-icon {
    width: 32px; height: 32px;
    background: var(--color-primary-600);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    color: white; padding: 6px;
}
.brand-txt { font-size: 1.25rem; font-weight: 700; color: var(--text-primary); letter-spacing: -0.01em; }
.brand-ai {
    font-size: 0.6rem; font-weight: 600;
    background: var(--color-primary-100);
    color: var(--color-primary-700); padding: 2px 6px; border-radius: var(--radius-sm); vertical-align: super;
}

.auth-card h1 { font-size: 1.5rem; font-weight: 900; color: var(--text-primary); margin-bottom: 0.25rem; }
.auth-subtitle { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.25rem; }

.auth-form { display: flex; flex-direction: column; gap: 0.9rem; text-align: left; }

.form-group { display: flex; flex-direction: column; }

.grade-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.4rem; }

.grade-btn {
    padding: 0.6rem;
    border-radius: var(--radius-sm);
    border: 1px solid var(--border-light);
    background: var(--bg-card);
    color: var(--text-secondary);
    font-family: var(--font-sans);
    font-size: 0.875rem; font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}
.grade-btn:hover { border-color: var(--border-hover); background: #f8fafc; }
.grade-active {
    background: var(--color-primary-50) !important;
    border-color: var(--color-primary-600) !important;
    color: var(--color-primary-700) !important;
}

.field-error { font-size: 0.75rem; color: var(--color-danger); margin-top: 0.25rem; }

.btn-full { width: 100%; padding: 0.85rem; font-size: 0.95rem; }

.auth-footer { margin-top: 1.25rem; font-size: 0.85rem; color: var(--text-muted); }
</style>
