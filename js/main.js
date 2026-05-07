const API = {
  getUsers: async () => {
    try {
      let users = localStorage.getItem('lumiere_users');
      if (!users) {
        // Hardcoded defaults to ensure it works even if fetch fails (e.g. local file CORS)
        const defaults = [
          {
            "id": "user-1",
            "email": "eleanor.v@lumiere.com",
            "password": "password123",
            "name": "Eleanor Vance",
            "role": "patron",
            "tier": "Silver Screen Member",
            "avatar": "https://api.dicebear.com/7.x/notionists/svg?seed=Eleanor&backgroundColor=e8735a"
          },
          {
            "id": "admin-1",
            "email": "arthur@lumiere.com",
            "password": "admin",
            "name": "Arthur Pendelton",
            "role": "admin",
            "designation": "Chief Operator",
            "avatar": "https://api.dicebear.com/7.x/notionists/svg?seed=Arthur&backgroundColor=d4a853"
          }
        ];

        try {
          const resp = await fetch('js/users.json');
          if (resp.ok) {
            users = await resp.json();
          } else {
            users = defaults;
          }
        } catch (err) {
          console.warn('Fetch failed, using default users.', err);
          users = defaults;
        }
        localStorage.setItem('lumiere_users', JSON.stringify(users));
      } else {
        users = JSON.parse(users);
      }
      return users;
    } catch (e) {
      console.error('Error fetching users:', e);
      return [];
    }
  },
  getMovies: async () => {
    try {
      let movies = localStorage.getItem('lumiere_movies');
      if (!movies) {
        const resp = await fetch('js/movies.json');
        if (!resp.ok) throw new Error('Failed to fetch movies');
        movies = await resp.json();
        localStorage.setItem('lumiere_movies', JSON.stringify(movies));
      } else {
        movies = JSON.parse(movies);
      }
      return movies;
    } catch (e) {
      console.error('Error fetching movies:', e);
      return [];
    }
  },
  updateMovies: (movies) => {
    localStorage.setItem('lumiere_movies', JSON.stringify(movies));
  },
  login: async (email, password) => {
    const users = await API.getUsers();
    const user = users.find(u => u.email === email && u.password === password);
    if (user) {
      localStorage.setItem('lumiere_session', JSON.stringify(user));
      return user;
    }
    return null;
  },
  getCurrentUser: () => {
    const session = localStorage.getItem('lumiere_session');
    return session ? JSON.parse(session) : null;
  },
  logout: () => {
    localStorage.removeItem('lumiere_session');
    window.location.href = 'index-login.html';
  }
};

window.API = API;

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

  const transEl = document.getElementById('pageTransition');
  if (transEl) {
    const removeTransition = () => {
      transEl.classList.add('fade-out');
      setTimeout(() => {
        transEl.style.display = 'none';
        transEl.classList.remove('active', 'fade-out');
      }, 600);
    };

    // Safety fallback
    const safetyTimeout = setTimeout(removeTransition, 2000);

    requestAnimationFrame(() => {
      requestAnimationFrame(() => {
        clearTimeout(safetyTimeout);
        removeTransition();
      });
    });
  }
});

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

document.addEventListener('click', e => {
  const link = e.target.closest('a[href$=".html"]');
  if (link && !link.hasAttribute('data-no-transition')) {
    e.preventDefault();
    triggerPageTransition(link.getAttribute('href'));
  }
});

