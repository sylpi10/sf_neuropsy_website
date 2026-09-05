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
  var slides = Array.prototype.slice.call(hero.querySelectorAll('.hero-slide'));
  var dots = Array.prototype.slice.call(hero.querySelectorAll('.hero-dot'));
  var count = slides.length;
  if (count < 2) return;

  var current = 0;
  var delay = 6500;
  var timer = null;

  slides.forEach(function (slide, i) {
    slide.style.transform = 'translateX(' + (i === current ? 0 : 100) + '%)';
  });

  function shortestDirection(from, to) {
    var diff = to - from;
    if (diff > count / 2) diff -= count;
    if (diff < -count / 2) diff += count;
    return diff < 0 ? -1 : 1;
  }

  function goTo(index) {
    var next = (index + count) % count;
    if (next === current) return;

    var direction = shortestDirection(current, next);
    var incoming = slides[next];
    var outgoing = slides[current];

    incoming.style.transition = 'none';
    incoming.style.transform = 'translateX(' + direction * 100 + '%)';
    void incoming.offsetWidth;
    incoming.style.transition = '';

    requestAnimationFrame(function () {
      outgoing.style.transform = 'translateX(' + -direction * 100 + '%)';
      incoming.style.transform = 'translateX(0)';
    });

    outgoing.classList.remove('is-active');
    incoming.classList.add('is-active');
    dots[current] && dots[current].classList.remove('is-active');
    dots[next] && dots[next].classList.add('is-active');

    current = next;
  }

  function next() {
    goTo(current + 1);
  }

  function restart() {
    clearInterval(timer);
    timer = setInterval(next, delay);
  }

  dots.forEach(function (dot, i) {
    dot.addEventListener('click', function () {
      goTo(i);
      restart();
    });
  });

  restart();
}
