<template>
    <div class="test-page">
        <div class="page-header fade-in">
            <h1>Yapay Zeka <span class="gradient-text">Ayarları</span></h1>
            <p>Sistemin metin üretimi ve görsel analizi için kullanacağı yapay zeka modellerini buradan seçebilirsiniz.</p>
        </div>

        <div v-if="loading" class="loading-grid mt-4">
            <div v-for="i in 2" :key="i" class="shimmer-card shimmer"></div>
        </div>

        <div v-else class="providers-grid mt-4">
            <div v-for="provider in providers" :key="provider.id" class="provider-card glass-card slide-up" :class="{ 'is-active': provider.is_active_text || provider.is_active_image }">
                <div class="provider-header">
                    <div class="provider-title">
                        <div class="provider-icon">
                            <svg v-if="provider.identifier === 'openai'" viewBox="0 0 24 24" fill="currentColor"><path d="M22.28 11.41c-.13-3.66-2.5-6.85-6.04-8.08-3.53-1.22-7.46.03-9.52 3.01L6 7.4A8.99 8.99 0 0 0 2.22 15c.13 3.66 2.5 6.85 6.04 8.08 3.53 1.22 7.46-.03 9.52-3.01l.73-1.07A8.99 8.99 0 0 0 22.28 11.41zM12 21a9 9 0 1 1 0-18 9 9 0 0 1 0 18z" opacity=".3"/><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2zm0 18a8 8 0 1 1 8-8 8 8 0 0 1-8 8z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg v-else-if="provider.identifier === 'gemini'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L15 9l7 3-7 3-3 7-3-7-7-3 7-3z"/></svg>
                            <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </div>
                        <h2>{{ provider.name }}</h2>
                    </div>
                    <div class="status-badge" :class="(provider.is_active_text || provider.is_active_image) ? 'badge-active' : 'badge-inactive'">
                        <span v-if="provider.is_active_text && provider.is_active_image">Tümü Aktif</span>
                        <span v-else-if="provider.is_active_text">Metin Aktif</span>
                        <span v-else-if="provider.is_active_image">Görsel Aktif</span>
                        <span v-else>Pasif</span>
                    </div>
                </div>

                <div class="provider-body">
                    <p class="description">
                        {{ getRoleDescription(provider.identifier) }}
                    </p>

                    <form @submit.prevent="saveKey(provider)" class="api-key-form">
                        <label class="input-label">API Anahtarı (Key)</label>
                        <div class="input-group">
                            <input 
                                :type="showKeys[provider.id] ? 'text' : 'password'" 
                                v-model="provider.api_key" 
                                class="input-field" 
                                placeholder="sk-..."
                            >
                            <button type="button" class="btn-icon" @click="toggleKeyVisibility(provider.id)">
                                <svg v-if="!showKeys[provider.id]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>

                        <div class="action-row" style="flex-wrap: wrap;">
                            <button type="submit" class="btn-secondary btn-sm" :disabled="savingKey === provider.id">
                                {{ savingKey === provider.id ? 'Kaydediliyor...' : 'Anahtarı Kaydet' }}
                            </button>
                            <button 
                                v-if="!provider.is_active_text" 
                                type="button"
                                class="btn-primary btn-sm" 
                                @click="makeActive(provider.id, 'text')"
                                :disabled="togglingActive"
                            >
                                Metin Üretimi İçin Etkinleştir
                            </button>
                            <button 
                                v-if="!provider.is_active_image" 
                                type="button"
                                class="btn-primary btn-sm" 
                                @click="makeActive(provider.id, 'image')"
                                :disabled="togglingActive"
                            >
                                Görsel Analiz İçin Etkinleştir
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/api.js';
import Swal from 'sweetalert2';

const providers = ref([]);
const loading = ref(true);
const savingKey = ref(null);
const togglingActive = ref(false);
const showKeys = ref({});

const getRoleDescription = (identifier) => {
    switch (identifier) {
        case 'openai': return "Sektör lideri ChatGPT (GPT-4o) görüş ve metin yeteneklerini kullanır.";
        case 'gemini': return "Google Gemini 1.5 Pro veya Flash vizyon modellerini kullanır.";
        case 'deepseek': return "Açık kaynak destekli, güçlü ve uygun maliyetli dil modeli. (Sadece metin).";
        default: return "Özel yapay zeka modeli.";
    }
};

const toggleKeyVisibility = (id) => {
    showKeys.value[id] = !showKeys.value[id];
};

const fetchProviders = async () => {
    loading.value = true;
    try {
        const res = await api.get('/ai-providers');
        providers.value = res.data.data;
    } catch (e) {
        Swal.fire('Hata!', 'Yapay zeka modelleri yüklenemedi.', 'error');
    } finally {
        loading.value = false;
    }
};

const saveKey = async (provider) => {
    savingKey.value = provider.id;
    try {
        await api.put(`/ai-providers/${provider.id}`, { api_key: provider.api_key });
        Swal.fire({
            title: 'Kaydedildi!',
            text: 'API Anahtarı başarıyla güncellendi.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            background: '#1e293b',
            color: '#f8fafc',
        });
    } catch (e) {
        Swal.fire('Hata!', 'Anahtar kaydedilirken hata oluştu.', 'error');
    } finally {
        savingKey.value = null;
    }
};

const makeActive = async (id, type) => {
    togglingActive.value = true;
    try {
        const res = await api.post(`/ai-providers/${id}/set-active`, { type });
        providers.value = res.data.data; // Refresh list
        Swal.fire({
            title: 'Başarılı!',
            text: res.data.message,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false,
            background: '#1e293b',
            color: '#f8fafc',
        });
    } catch (e) {
        Swal.fire('Hata!', 'Model aktif edilirken hata oluştu.', 'error');
    } finally {
        togglingActive.value = false;
    }
};

onMounted(fetchProviders);
</script>

<style scoped>
.test-page {
    max-width: 800px;
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

.providers-grid {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.provider-card {
    border: 2px solid transparent;
    transition: all 0.3s;
}

.provider-card.is-active {
    border-color: var(--color-primary-500);
    box-shadow: 0 0 20px rgba(108, 71, 255, 0.15);
}

.provider-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--border-light);
}

.provider-title {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.provider-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: rgba(108, 71, 255, 0.1);
    color: var(--color-primary-500);
    display: flex;
    align-items: center;
    justify-content: center;
}

.provider-icon svg {
    width: 24px;
    height: 24px;
}

.is-active .provider-icon {
    background: var(--color-primary-500);
    color: white;
}

.provider-title h2 {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.badge-active {
    background: rgba(16, 185, 129, 0.1);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.2);
}

.badge-inactive {
    background: var(--bg-body);
    color: var(--color-dark-400);
    border: 1px solid var(--border-light);
}

.description {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
}

.api-key-form {
    background: var(--bg-body);
    padding: 1.25rem;
    border-radius: var(--radius-md);
    border: 1px solid var(--border-light);
}

.input-label {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--color-dark-300);
    margin-bottom: 0.5rem;
}

.input-group {
    display: flex;
    align-items: center;
    background: var(--bg-input);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-md);
    overflow: hidden;
    margin-bottom: 1rem;
}

.input-group:focus-within {
    border-color: var(--color-primary-400);
    box-shadow: 0 0 0 3px rgba(108, 71, 255, 0.1);
}

.input-group .input-field {
    flex: 1;
    border: none;
    box-shadow: none;
    background: transparent;
    padding-right: 0;
}

.input-group .input-field:focus {
    box-shadow: none;
}

.btn-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: transparent;
    border: none;
    color: var(--color-dark-400);
    cursor: pointer;
    transition: color 0.2s;
}

.btn-icon:hover {
    color: var(--color-primary-500);
}

.btn-icon svg {
    width: 20px;
    height: 20px;
}

.action-row {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.85rem;
}

.loading-grid {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.shimmer-card {
    height: 250px;
    border-radius: var(--radius-lg);
}
</style>
