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
    toggle.setAttribute('aria-label', isOpen ? 'Fermer le menu' : 'Ouvrir le menu');
    toggle.classList.toggle('is-active', isOpen);
    document.body.classList.toggle('nav-open', isOpen);
  });

  // Sur desktop les sous-menus s'ouvrent aussi au survol (CSS) : on garde aria-expanded à jour.
  var hoverMenus = window.matchMedia('(hover: hover) and (pointer: fine) and (min-width: 1151px)');
  var dropdownToggles = nav.querySelectorAll('.dropdown-toggle');
  dropdownToggles.forEach(function (btn) {
    var parent = btn.closest('.has-dropdown');
    parent.addEventListener('mouseenter', function () {
      if (hoverMenus.matches) btn.setAttribute('aria-expanded', 'true');
    });
    parent.addEventListener('mouseleave', function () {
      btn.setAttribute('aria-expanded', String(parent.classList.contains('is-open')));
    });

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
    if (e.key !== 'Escape') return;
    // On rend le focus au bouton qui avait ouvert le sous-menu ou le menu mobile.
    var active = document.activeElement;
    var openItem = active && active.closest ? active.closest('.has-dropdown.is-open') : null;
    var returnTo = null;
    if (nav.classList.contains('is-open') && nav.contains(active)) {
      returnTo = toggle;
    } else if (openItem) {
      returnTo = openItem.querySelector('.dropdown-toggle');
    }
    closeNav();
    if (returnTo) returnTo.focus();
  });

  nav.querySelectorAll('.primary-nav > ul > li > a').forEach(function (link) {
    link.addEventListener('click', closeNav);
  });

  function closeNav() {
    nav.classList.remove('is-open');
    toggle.setAttribute('aria-expanded', 'false');
    toggle.setAttribute('aria-label', 'Ouvrir le menu');
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

function loadDeferredSlides(slides) {
  function apply() {
    var isMobile = window.matchMedia('(max-width: 768px)').matches;
    slides.forEach(function (slide) {
      var bg = slide.getAttribute('data-bg');
      if (!bg) return;
      var bgMobile = slide.getAttribute('data-bg-mobile');
      if (bgMobile && isMobile) bg = bgMobile;
      slide.style.backgroundImage = 'url("' + bg + '")';
      slide.removeAttribute('data-bg');
      slide.removeAttribute('data-bg-mobile');
    });
  }

  function schedule() {
    if ('requestIdleCallback' in window) {
      requestIdleCallback(apply, { timeout: 2000 });
    } else {
      setTimeout(apply, 500);
    }
  }

  if (document.readyState === 'complete') {
    schedule();
  } else {
    window.addEventListener('load', schedule);
  }
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
    slide.setAttribute('aria-hidden', String(i !== current));
  });

  loadDeferredSlides(slides);

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
    outgoing.setAttribute('aria-hidden', 'true');
    incoming.setAttribute('aria-hidden', 'false');
    if (dots[current]) {
      dots[current].classList.remove('is-active');
      dots[current].removeAttribute('aria-current');
    }
    if (dots[next]) {
      dots[next].classList.add('is-active');
      dots[next].setAttribute('aria-current', 'true');
    }

    current = next;
  }

  function next() {
    goTo(current + 1);
  }

  var pauseBtn = hero.querySelector('.hero-pause');
  // Pas de défilement automatique si l'utilisateur a demandé moins d'animations.
  var userPaused = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  function restart() {
    clearInterval(timer);
    timer = null;
    if (userPaused) return;
    timer = setInterval(next, delay);
  }

  function syncPauseButton() {
    if (!pauseBtn) return;
    pauseBtn.classList.toggle('is-paused', userPaused);
    pauseBtn.setAttribute('aria-label', userPaused ? 'Relancer le défilement' : 'Mettre le défilement en pause');
  }

  if (pauseBtn) {
    pauseBtn.addEventListener('click', function () {
      userPaused = !userPaused;
      syncPauseButton();
      restart();
    });
  }

  dots.forEach(function (dot, i) {
    dot.addEventListener('click', function () {
      goTo(i);
      restart();
    });
  });

  syncPauseButton();
  restart();
}
