import './bootstrap';

// Every rendered surface loads this bundle, so the platform-owned visual theme
// stays consistent without duplicating per-page asset and typography rules.
if (! document.querySelector('link[data-platform-theme]')) {
    const platformTheme = document.createElement('link');
    platformTheme.rel = 'stylesheet';
    platformTheme.href = '/platform-theme.css';
    platformTheme.dataset.platformTheme = 'true';
    document.head.appendChild(platformTheme);
}
import Alpine from 'alpinejs';

// Expose Alpine globally BEFORE start so inline x-data and $store work
window.Alpine = Alpine;

window.asyncMessageThread = function () {
    return {
        sending: false,
        error: '',
        init() {
            this.$nextTick(() => this.scrollToLatest());
            const bookingId = this.$el.dataset.bookingId;
            const currentUserId = this.$el.dataset.currentUserId;
            if (bookingId && window.Echo) {
                window.Echo.private(`booking.${bookingId}`).listen('.booking.message.created', (message) => {
                    if (String(message.sender_id) !== String(currentUserId)) this.appendMessage(message, false);
                });
            }
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
        sendOnEnter(event) {
            if (event.isComposing || event.shiftKey) return;

            event.preventDefault();
            event.currentTarget.form?.requestSubmit();
        },
        appendMessage(message, mine = true) {
            if (this.$refs.messages?.querySelector(`[data-message-id="${message.id}"]`)) return;
            this.$refs.emptyState?.remove();

            const article = document.createElement('article');
            article.className = mine ? 'flex justify-end' : 'flex justify-start';
            article.dataset.messageId = message.id;

            const bubble = document.createElement('div');
            bubble.className = mine
                ? 'max-w-[85%] rounded-2xl rounded-tr-sm bg-orange-600 px-5 py-3 text-sm text-white shadow-sm'
                : 'max-w-[85%] rounded-2xl rounded-tl-sm bg-slate-100 px-5 py-3 text-sm text-slate-700 shadow-sm';

            const sender = document.createElement('p');
            sender.className = mine ? 'font-bold text-orange-100' : 'font-bold text-slate-500';
            sender.textContent = mine ? 'You' : (message.sender || 'New message');

            const content = document.createElement('p');
            content.className = 'mt-1 whitespace-pre-wrap leading-6';
            content.textContent = message.content;

            const time = document.createElement('p');
            time.className = mine ? 'mt-2 text-right text-[10px] text-orange-100' : 'mt-2 text-right text-[10px] text-slate-400';
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

window.bookingBrowser = function (initialFilters = {}) {
    return {
        q: initialFilters.q || '',
        from: initialFilters.from || '',
        to: initialFilters.to || '',
        status: initialFilters.status || '',
        sort: initialFilters.sort || 'newest',
        rangeOpen: false,
        loading: false,
        error: '',
        async refresh(url = null) {
            if (this.loading) return;

            this.loading = true;
            this.error = '';
            const params = new URLSearchParams({ ajax: '1', sort: this.sort });
            if (this.q) params.set('q', this.q);
            if (this.from) params.set('from', this.from);
            if (this.to) params.set('to', this.to);
            if (this.status) params.set('status', this.status);

            const requestUrl = url || `${window.location.pathname}?${params.toString()}`;

            try {
                const response = await fetch(requestUrl, {
                    headers: { Accept: 'application/json' },
                    credentials: 'same-origin',
                });
                const payload = await response.json().catch(() => ({}));
                if (!response.ok) throw new Error(payload.message || 'Could not update your bookings.');

                const documentFragment = new DOMParser().parseFromString(payload.html, 'text/html');
                const results = documentFragment.querySelector('[data-booking-results]');
                if (!results) throw new Error('Could not update your bookings.');

                this.$refs.results.innerHTML = results.innerHTML;
                window.history.replaceState({}, '', `${window.location.pathname}?${params.toString().replace('ajax=1&', '')}`);
            } catch (error) {
                this.error = error.message || 'Could not update your bookings. Please try again.';
            } finally {
                this.loading = false;
            }
        },
        handleResultsClick(event) {
            const link = event.target.closest('a[href]');
            if (!link || !link.href.includes('page=')) return;

            event.preventDefault();
            this.refresh(link.href);
        },
        rangeLabel() {
            if (!this.from && !this.to) return 'Select dates';

            const format = (value) => new Intl.DateTimeFormat('en-NG', {
                day: 'numeric', month: 'short', year: 'numeric',
            }).format(new Date(`${value}T00:00:00`));

            return this.from && this.to
                ? `${format(this.from)} – ${format(this.to)}`
                : (this.from ? `From ${format(this.from)}` : `Until ${format(this.to)}`);
        },
        clearDateRange() {
            this.from = '';
            this.to = '';
            this.rangeOpen = false;
            this.refresh();
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
