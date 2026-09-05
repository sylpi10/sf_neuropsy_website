document.addEventListener('DOMContentLoaded', function () {
  initHeader();
  initHero();
});

function initHeader() {
  var header = document.querySelector('.site-header');
  var toggle = document.getElementById('navToggle');
  var nav = document.getElementById('primaryNav');
  if (!header || !toggle || !nav) return;

  toggle.addEventListener('click', function () {
    var isOpen = nav.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
    toggle.classList.toggle('is-active', isOpen);
    document.body.classList.toggle('nav-open', isOpen);
  });

  var dropdownToggles = nav.querySelectorAll('.dropdown-toggle');
  dropdownToggles.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      var item = btn.closest('.has-dropdown');
      var isOpen = item.classList.contains('is-open');
      nav.querySelectorAll('.has-dropdown.is-open').forEach(function (other) {
        if (other !== item) {
          other.classList.remove('is-open');
          other.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
        }
      });
      item.classList.toggle('is-open', !isOpen);
      btn.setAttribute('aria-expanded', String(!isOpen));
    });
  });

  document.addEventListener('click', function (e) {
    if (!nav.contains(e.target) && !toggle.contains(e.target)) {
      closeNav();
    }
  });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeNav();
  });

  nav.querySelectorAll('.primary-nav > ul > li > a').forEach(function (link) {
    link.addEventListener('click', closeNav);
  });

  function closeNav() {
    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.classList.remove('is-active');
    document.body.classList.remove('nav-open');
    nav.querySelectorAll('.has-dropdown.is-open').forEach(function (item) {
      item.classList.remove('is-open');
      item.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
    });
  }

  var onScroll = function () {
    header.classList.toggle('is-scrolled', window.scrollY > 12);
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });
}

function initHero() {
  var hero = document.querySelector('.hero-slider');
  if (!hero) return;
  var slides = hero.querySelectorAll('.hero-slide');
  var dots = hero.querySelectorAll('.hero-dot');
  if (slides.length < 2) return;

  var current = 0;
  var delay = 6500;
  var timer = null;

  function show(index) {
    slides[current].classList.remove('is-active');
    dots[current] && dots[current].classList.remove('is-active');
    current = (index + slides.length) % slides.length;
    slides[current].classList.add('is-active');
    dots[current] && dots[current].classList.add('is-active');
  }

  function next() {
    show(current + 1);
  }

  function restart() {
    clearInterval(timer);
    timer = setInterval(next, delay);
  }

  dots.forEach(function (dot, i) {
    dot.addEventListener('click', function () {
      show(i);
      restart();
    });
  });

  restart();
}
