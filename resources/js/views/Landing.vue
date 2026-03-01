<template>
    <div class="landing">
        <!-- Navbar -->
        <nav class="landing-nav">
            <div class="nav-brand">
                <div class="brand-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                </div>
                <span class="nav-name">SmartTest <span class="brand-ai">AI</span></span>
            </div>
            <div class="nav-actions">
                <router-link v-if="authStore.isAuthenticated" to="/dashboard" class="btn-primary btn-sm">Panele Dön</router-link>
                <template v-else>
                    <router-link to="/login" class="btn-secondary btn-sm">Giriş Yap</router-link>
                    <router-link to="/register" class="btn-primary btn-sm">Kayıt Ol</router-link>
                </template>
            </div>
        </nav>

        <!-- Hero -->
        <section class="hero">
            <div class="hero-content fade-in">
                <span class="hero-badge">5-8. Sınıf Öğrencileri İçin Profesyonel Eğitim Platformu</span>
                <h1>Eksik Kazanımlarınızı <br><span>Yapay Zeka</span> ile Tamamlayın</h1>
                <p>Çözemediğiniz soruları sisteme yükleyin, gelişmiş analiz motoru zayıf olduğunuz konuları tespit edip size özel gelişim testleri hazırlasın.</p>
                <div class="hero-btns">
                    <router-link to="/register" class="btn-primary btn-lg">Ücretsiz Başlayın</router-link>
                </div>
            </div>
        </section>

        <!-- How it Works -->
        <section class="how-section">
            <h2>Sistem Nasıl Çalışır?</h2>
            <div class="steps-grid">
                <div class="step-card glass-card slide-up" style="animation-delay: 0.1s">
                    <div class="step-num">1</div>
                    <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg></div>
                    <h3>Soruyu Yükleyin</h3>
                    <p>Çözemediğiniz sorunun fotoğrafını platforma yükleyin veya kameradan anında taratın.</p>
                </div>
                <div class="step-card glass-card slide-up" style="animation-delay: 0.2s">
                    <div class="step-num">2</div>
                    <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                    <h3>AI Analiz Etsin</h3>
                    <p>Yapay zeka motoru, sorunun ait olduğu dersi ve MEB müfredatına uygun kazanımı anında tespit eder.</p>
                </div>
                <div class="step-card glass-card slide-up" style="animation-delay: 0.3s">
                    <div class="step-num">3</div>
                    <div class="step-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></div>
                    <h3>Kişisel Testinizi Çözün</h3>
                    <p>Eksik olduğunuz kazanımlardan oluşan, seviyenize özel deneme sınavları ve testler ile gelişiminizi hızlandırın.</p>
                </div>
            </div>
        </section>

        <!-- Subjects -->
        <section class="subjects-section">
            <h2>Müfredata Tam Uyumlu İçerik</h2>
            <div class="subjects-grid">
                <div class="subject-chip">Matematik</div>
                <div class="subject-chip">Türkçe</div>
                <div class="subject-chip">İngilizce</div>
                <div class="subject-chip">Fen Bilimleri</div>
                <div class="subject-chip">Sosyal Bilgiler</div>
            </div>
        </section>

        <!-- CTA -->
        <section class="cta-section">
            <div class="cta-box glass-card">
                <h2>Öğrenme Serüveninize Bugün Başlayın</h2>
                <p>Ücretsiz hesap oluşturarak platformun tüm analiz araçlarını deneyimleyin.</p>
                <router-link to="/register" class="btn-primary btn-lg">Ücretsiz Kayıt Ol</router-link>
            </div>
        </section>

        <!-- Footer -->
        <footer class="landing-footer">
            <p>© 2026 SmartTest AI • 5-8. Sınıf Öğrencileri İçin Akıllı Test Platformu</p>
        </footer>
    </div>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth.js';
const authStore = useAuthStore();
</script>

<style scoped>
.landing {
    min-height: 100vh;
    background: var(--bg-body);
    position: relative; overflow: hidden;
}

/* Nav */
.landing-nav {
    display: flex; justify-content: space-between; align-items: center;
    padding: 1.5rem 2rem;
    max-width: 1200px; margin: 0 auto;
}
.nav-brand { display: flex; align-items: center; gap: 0.75rem; }
.brand-icon {
    width: 32px; height: 32px;
    background: var(--color-primary-600);
    border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center;
    color: white; padding: 6px;
}
.nav-name { font-size: 1.25rem; font-weight: 700; color: var(--text-primary); letter-spacing:-0.01em; }
.brand-ai {
    font-size: 0.6rem; font-weight: 600;
    background: var(--color-primary-100);
    color: var(--color-primary-700); padding: 2px 6px; border-radius: var(--radius-sm); vertical-align: super;
}
.nav-actions { display: flex; gap: 0.5rem; }
.btn-sm { padding: 0.5rem 1rem; font-size: 0.8rem; }

/* Hero */
.hero {
    text-align: center;
    padding: 4rem 2rem 3rem;
    max-width: 700px; margin: 0 auto;
}
.hero-badge {
    display: inline-block;
    background: rgba(124, 58, 237, 0.08);
    color: var(--color-primary-600);
    padding: 0.35rem 1rem;
    border-radius: 100px;
    font-size: 0.8rem; font-weight: 700;
    margin-bottom: 1.5rem;
}
.hero h1 {
    font-size: 2.75rem; font-weight: 800; color: var(--text-primary);
    line-height: 1.2; margin-bottom: 1.25rem; letter-spacing:-0.02em;
}
.hero h1 span { color: var(--color-primary-600); }
.hero p { font-size: 1rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 2rem; }
.hero-btns { display: flex; justify-content: center; gap: 0.75rem; flex-wrap: wrap; }
.btn-lg { padding: 0.9rem 2rem; font-size: 1rem; }

/* How it works */
.how-section {
    max-width: 900px; margin: 0 auto;
    padding: 3rem 2rem;
    text-align: center;
}
.how-section h2 { font-size: 1.5rem; font-weight: 900; color: var(--text-primary); margin-bottom: 2rem; }

.steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }

.step-card {
    padding: 2.5rem 1.5rem;
    text-align: center;
    position: relative;
    border: 1px solid var(--border-light);
}
.step-num {
    position: absolute; top: -14px; left: 50%; transform: translateX(-50%);
    width: 28px; height: 28px;
    background: var(--color-primary-100);
    color: var(--color-primary-700); border-radius: var(--radius-full);
    font-size: 0.85rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    border: 3px solid var(--bg-body);
}
.step-icon {
    width: 48px; height: 48px; margin: 0 auto 1.25rem;
    background: var(--color-primary-50); border-radius: var(--radius-md);
    display: flex; align-items: center; justify-content: center; color: var(--color-primary-600);
}
.step-icon svg { width: 24px; height: 24px; }
.step-card h3 { font-size: 1.05rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.75rem; }
.step-card p { font-size: 0.85rem; color: var(--text-secondary); line-height: 1.6; }

/* Subjects */
.subjects-section {
    text-align: center;
    padding: 2rem; max-width: 700px; margin: 0 auto;
}
.subjects-section h2 { font-size: 1.5rem; font-weight: 900; margin-bottom: 1.5rem; }

.subjects-grid { display: flex; justify-content: center; gap: 0.6rem; flex-wrap: wrap; }

.subject-chip {
    background: var(--bg-card);
    border: 1px solid var(--border-light);
    border-radius: 100px;
    padding: 0.6rem 1.25rem;
    font-size: 0.9rem; font-weight: 700;
    color: var(--text-secondary);
    box-shadow: var(--shadow-sm);
}

/* CTA */
.cta-section { padding: 4rem 2rem; max-width: 800px; margin: 0 auto; }
.cta-box {
    text-align: center; padding: 4rem 2rem;
    background: var(--color-primary-50);
    border: 1px solid var(--color-primary-100);
}
.cta-box h2 { font-size: 1.75rem; font-weight: 800; margin-bottom: 0.75rem; color: var(--text-primary); }
.cta-box p { font-size: 1rem; color: var(--text-secondary); margin-bottom: 2rem; }

/* Footer */
.landing-footer {
    text-align: center;
    padding: 2rem;
    color: var(--text-muted); font-size: 0.8rem;
    border-top: 1px solid var(--border-light);
}

@media (max-width: 768px) {
    .hero h1 { font-size: 1.75rem; }
    .steps-grid { grid-template-columns: 1fr; max-width: 350px; margin: 0 auto; }
    .landing-nav { padding: 1rem; }
}
</style>
