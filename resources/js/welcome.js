// ── Hamburger Menu
const hamburgerBtn = document.getElementById('hamburgerBtn');
const mobileMenu = document.getElementById('mobileMenu');
const mobileOverlay = document.getElementById('mobileOverlay');

function toggleMobileMenu() {
    hamburgerBtn.classList.toggle('active');
    mobileMenu.classList.toggle('open');
    mobileOverlay.classList.toggle('open');
    document.body.style.overflow = mobileMenu.classList.contains('open') ? 'hidden' : '';
}

function closeMobileMenu() {
    hamburgerBtn.classList.remove('active');
    mobileMenu.classList.remove('open');
    mobileOverlay.classList.remove('open');
    document.body.style.overflow = '';
}

hamburgerBtn.addEventListener('click', toggleMobileMenu);
mobileOverlay.addEventListener('click', closeMobileMenu);

// ── Navbar scroll effect
const navbar = document.getElementById('mainNavbar');
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 60);
    const hero = document.querySelector('.hero');
    if (hero) {
        const opacity = Math.max(0, 1 - (window.scrollY / (window.innerHeight * 0.6)));
        hero.style.opacity = opacity;
    }
}, { passive: true });

// ── Scroll Reveal
const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
revealElements.forEach(el => revealObserver.observe(el));

// ── Counter Animation
function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-target'));
    if (!target) return;
    const suffix = el.getAttribute('data-suffix') || '+';
    let start = 0;
    const duration = 1800;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
        start = Math.min(start + step, target);
        el.textContent = Math.floor(start) + suffix;
        if (start >= target) clearInterval(timer);
    }, 16);
}

const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounter(entry.target);
            counterObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });
document.querySelectorAll('[data-target]').forEach(el => counterObserver.observe(el));

// ── Menu Populer
const rupiah = n => 'Rp ' + Number(n).toLocaleString('id-ID');
const mpContainer = document.getElementById('menuPopulerContainer');
const placeholderImg = document.getElementById('placeholderImg')?.value || window.location.origin + '/images/placeholder.svg';
if (mpContainer) {
    const populerRoute = document.getElementById('populerRoute')?.value || '/menu-meja/menu-populer';
    fetch(populerRoute, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(res => {
            if (!res.success || !res.data.length) return;
            mpContainer.innerHTML = res.data.map(m => `
                <div class="col-6 col-md-3">
                    <div class="menu-card">
                        <div class="mc-img">
                            <img src="${m.foto_url || placeholderImg}" alt="${m.nama}" loading="lazy" onerror="this.src='${placeholderImg}'">
                            <span class="mc-category">${m.kategori?.nama || 'Menu'}</span>
                        </div>
                        <div class="mc-body">
                            <div class="mc-name">${m.nama} ${m.is_best_seller ? '<span class="mc-badge">Best</span>' : ''}</div>
                            <div class="mc-price">${rupiah(m.harga)}</div>
                        </div>
                    </div>
                </div>
            `).join('');
        })
        .catch(() => {});
}

// ── Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        if (href === '#') return;
        e.preventDefault();
        const target = document.querySelector(href);
        if (target) {
            target.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });
});
