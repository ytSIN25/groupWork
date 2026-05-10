/* ================================================
     LUMIÈRE - Main JS
     ================================================ */

const API = {
    // Get current user from PHP session
    getCurrentUser: async () => {
        try {
            const resp = await fetch('api_session.php');
            const data = await resp.json();
            return data.success ? data.user : null;
        } catch (e) {
            console.error('Session check failed:', e);
            return null;
        }
    },

    // Login via PHP
    login: async (email, password) => {
        try {
            const resp = await fetch('api_login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email, password })
            });
            const data = await resp.json();
            return data.success ? data.user : null;
        } catch (e) {
            console.error('Login failed:', e);
            return null;
        }
    },

    // Register via PHP
    register: async (name, email, password) => {
        try {
            const resp = await fetch('api_register.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, email, password })
            });
            const data = await resp.json();
            return data;
        } catch (e) {
            console.error('Register failed:', e);
            return { success: false, message: 'Network error' };
        }
    },

    // Logout via PHP
    logout: async () => {
        try {
            await fetch('api_logout.php', { method: 'POST' });
        } catch (e) {
            console.error('Logout failed:', e);
        }
        window.location.href = 'index_login.php';
    }
};

window.API = API;


// Animations - intersection observer for scroll fx
document.addEventListener('DOMContentLoaded', () => {
    const animatedEls = document.querySelectorAll('.fade-up, .fade-left, .fade-right, .scale-in, .skew-up, .blur-in, .text-reveal');
    if (animatedEls.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const delay = parseInt(entry.target.dataset.delay) || 0;
                    setTimeout(() => entry.target.classList.add('visible'), delay);
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
        animatedEls.forEach(el => observer.observe(el));
    }

    // Page transition overlay
    const transEl = document.getElementById('pageTransition');
    if (transEl) {
        const removeTransition = () => {
            transEl.classList.add('fade-out');
            setTimeout(() => {
                transEl.style.display = 'none';
                transEl.classList.remove('active', 'fade-out');
            }, 600);
        };

        const safetyTimeout = setTimeout(removeTransition, 2500);

        window.addEventListener('load', () => {
            clearTimeout(safetyTimeout);
            removeTransition();
        });

        window.addEventListener('pageshow', (e) => {
            if (e.persisted) {
                clearTimeout(safetyTimeout);
                removeTransition();
            }
        });
    }
});

// Trigger animated page transition
window.triggerPageTransition = function(url) {
    const transEl = document.getElementById('pageTransition');
    if (transEl) {
        transEl.style.display = 'flex';
        transEl.classList.remove('fade-out');
        void transEl.offsetWidth;
        transEl.classList.add('active');
        setTimeout(() => { window.location.href = url; }, 700);
    } else {
        window.location.href = url;
    }
};

// Intercept .html and .php link clicks
document.addEventListener('click', e => {
    const link = e.target.closest('a[href$=".html"], a[href$=".php"]');
    if (link && !link.hasAttribute('data-no-transition')) {
        e.preventDefault();
        triggerPageTransition(link.getAttribute('href'));
    }
});
