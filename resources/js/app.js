import './bootstrap';
import Alpine from 'alpinejs';

// Expose Alpine globally BEFORE start so inline x-data and $store work
window.Alpine = Alpine;

window.asyncMessageThread = function () {
    return {
        sending: false,
        error: '',
        init() {
            this.$nextTick(() => this.scrollToLatest());
        },
        async send(event) {
            if (this.sending) return;

            const form = event.currentTarget;
            const input = form.querySelector('[name="content"]');
            const content = input?.value.trim() || '';
            if (content.length < 2) {
                this.error = 'Please enter at least two characters.';
                input?.focus();
                return;
            }

            this.sending = true;
            this.error = '';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': form.querySelector('[name="_token"]')?.value || '',
                    },
                    credentials: 'same-origin',
                    body: new FormData(form),
                });
                const payload = await response.json().catch(() => ({}));

                if (!response.ok) {
                    throw new Error(payload.message || Object.values(payload.errors || {})?.[0]?.[0] || 'Message could not be sent.');
                }

                this.appendMessage(payload.message);
                form.reset();
                input?.focus();
            } catch (error) {
                this.error = error.message || 'Message could not be sent. Please try again.';
            } finally {
                this.sending = false;
            }
        },
        appendMessage(message) {
            this.$refs.emptyState?.remove();

            const article = document.createElement('article');
            article.className = 'flex justify-end';
            article.dataset.messageId = message.id;

            const bubble = document.createElement('div');
            bubble.className = 'max-w-[85%] rounded-2xl rounded-tr-sm bg-orange-600 px-5 py-3 text-sm text-white shadow-sm';

            const sender = document.createElement('p');
            sender.className = 'font-bold text-orange-100';
            sender.textContent = message.sender || 'You';

            const content = document.createElement('p');
            content.className = 'mt-1 whitespace-pre-wrap leading-6';
            content.textContent = message.content;

            const time = document.createElement('p');
            time.className = 'mt-2 text-right text-[10px] text-orange-100';
            time.textContent = message.time;

            bubble.append(sender, content, time);
            article.appendChild(bubble);
            this.$refs.messages.appendChild(article);
            this.$nextTick(() => this.scrollToLatest());
        },
        scrollToLatest() {
            const container = this.$refs.messages;
            if (container) container.scrollTop = container.scrollHeight;
        },
    };
};

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

Alpine.store('favourites', {
    count: Number(document.documentElement.dataset.favouritesCount || 0),
    increment() {
        this.count += 1;
    },
    decrement() {
        this.count = Math.max(0, this.count - 1);
    },
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
