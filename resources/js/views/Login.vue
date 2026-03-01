<template>
    <div class="login-page">
        <div class="blob-bg blob-1"></div>
        <div class="blob-bg blob-2"></div>

        <div class="auth-card glass-card slide-up">
            <div class="auth-brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                </div>
                <span class="brand-txt">SmartTest <span class="brand-ai">AI</span></span>
            </div>
            <h1>Giriş Yap</h1>
            <p class="auth-subtitle">Hesabına giriş yaparak devam et</p>

            <div v-if="authStore.errors?.general" class="auth-error">
                {{ authStore.errors.general[0] }}
            </div>

            <form @submit.prevent="handleLogin" class="auth-form">
                <div class="form-group">
                    <label class="input-label">E-posta</label>
                    <input v-model="form.email" type="email" class="input-field" placeholder="ornek@email.com" required />
                </div>
                <div class="form-group">
                    <label class="input-label">Şifre</label>
                    <input v-model="form.password" type="password" class="input-field" placeholder="••••••••" required />
                </div>
                <button type="submit" class="btn-primary btn-full" :disabled="authStore.loading">
                    {{ authStore.loading ? 'Giriş yapılıyor...' : 'Giriş Yap' }}
                </button>
            </form>

            <p class="auth-footer">
                Hesabın yok mu? <router-link to="/register" class="auth-link">Ücretsiz Kayıt Ol</router-link>
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
const form = reactive({ email: '', password: '' });

const handleLogin = async () => {
    const ok = await authStore.login(form);
    if (ok) router.push('/dashboard');
};
</script>

<style scoped>
.login-page {
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    padding: 1rem;
    background: var(--bg-body);
    position: relative; overflow: hidden;
}

.auth-card {
    width: 100%; max-width: 420px;
    padding: 2.5rem;
    text-align: center;
}

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
.auth-subtitle { font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1.5rem; }

.auth-form { display: flex; flex-direction: column; gap: 1rem; text-align: left; }

.form-group { display: flex; flex-direction: column; }

.btn-full { width: 100%; padding: 0.85rem; font-size: 0.95rem; }

.auth-footer { margin-top: 1.5rem; font-size: 0.85rem; color: var(--text-muted); }
</style>
