import './bootstrap';
import Alpine from 'alpinejs';

// Expose Alpine globally BEFORE start so inline x-data and $store work
window.Alpine = Alpine;

// Register all stores BEFORE Alpine.start()
Alpine.store('modals', {
    showLoginModal: false,
    showSignupModal: false,
    openLogin() {
        this.showLoginModal = true;
        this.showSignupModal = false;
    },
    openSignup() {
        this.showSignupModal = true;
        this.showLoginModal = false;
    },
    closeAll() {
        this.showLoginModal = false;
        this.showSignupModal = false;
    }
});

// Start Alpine
Alpine.start();

// Toast helper
window.showToast = function(message, duration = 3000) {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add('show');
    setTimeout(() => toast.classList.remove('show'), duration);
};

// Header scroll + scroll reveal
document.addEventListener('DOMContentLoaded', function () {
    const header = document.getElementById('site-header');
    if (header) {
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 10);
        });
    }

    document.querySelectorAll('.reveal').forEach(el => el.classList.add('reveal-ready'));
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.06, rootMargin: '0px 0px -40px 0px' });

    document.querySelectorAll('.reveal-ready').forEach(el => {
        el.getBoundingClientRect().top < window.innerHeight
            ? el.classList.add('visible')
            : observer.observe(el);
    });
});
