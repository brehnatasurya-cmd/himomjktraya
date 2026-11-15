/**
 * HIMO Mojokerto Raya — Navigation & Shared Interactions
 * Handles: navbar scroll state, mobile menu, active page detection
 */
(function () {
  'use strict';

  const header = document.getElementById('site-header');
  const hamburger = document.getElementById('nav-hamburger');
  const mobileNav = document.getElementById('nav-mobile');

  if (!header) return;

  // ---------------------------------------------------------------
  // 1. Navbar scroll behavior
  // Landing page: transparent → 50% opacity on scroll → check JS
  // Other pages:  always solid (data-transparent="false")
  // ---------------------------------------------------------------
  const isTransparentPage = header.getAttribute('data-transparent') === 'true';
  let ticking = false;

  function updateNavState() {
    const scrollY = window.scrollY;
    if (!isTransparentPage) return; // non-landing pages stay solid

    if (scrollY < 10) {
      // At top — fully transparent
      header.classList.remove('scrolling', 'scrolled');
    } else if (scrollY < 80) {
      // Beginning to scroll — 50% opacity + blur
      header.classList.add('scrolling');
      header.classList.remove('scrolled');
    } else {
      // Scrolled far enough — fully solid
      header.classList.add('scrolled');
      header.classList.remove('scrolling');
    }
    ticking = false;
  }

  if (isTransparentPage) {
    window.addEventListener('scroll', function () {
      if (!ticking) {
        window.requestAnimationFrame(updateNavState);
        ticking = true;
      }
    }, { passive: true });
    updateNavState(); // initial check
  }

  // ---------------------------------------------------------------
  // 2. Mobile menu toggle
  // ---------------------------------------------------------------
  if (hamburger && mobileNav) {
    hamburger.addEventListener('click', function () {
      const isOpen = hamburger.getAttribute('aria-expanded') === 'true';
      const nextState = !isOpen;

      hamburger.setAttribute('aria-expanded', String(nextState));

      if (nextState) {
        mobileNav.removeAttribute('hidden');
        // Focus first link in mobile menu
        const firstLink = mobileNav.querySelector('a');
        if (firstLink) firstLink.focus();
      } else {
        mobileNav.setAttribute('hidden', '');
        hamburger.focus();
      }
    });

    // Close mobile menu on Escape
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') {
        const isOpen = hamburger.getAttribute('aria-expanded') === 'true';
        if (isOpen) {
          mobileNav.setAttribute('hidden', '');
          hamburger.setAttribute('aria-expanded', 'false');
          hamburger.focus();
        }
      }
    });

    // Close mobile menu when a link is clicked
    mobileNav.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        mobileNav.setAttribute('hidden', '');
        hamburger.setAttribute('aria-expanded', 'false');
      });
    });

    // Close mobile menu on outside click
    document.addEventListener('click', function (e) {
      const isOpen = hamburger.getAttribute('aria-expanded') === 'true';
      if (isOpen && !header.contains(e.target)) {
        mobileNav.setAttribute('hidden', '');
        hamburger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ---------------------------------------------------------------
  // 3. Active page detection
  // Sets aria-current="page" and .active class on matching links
  // ---------------------------------------------------------------
  const currentFile = window.location.pathname.split('/').pop() || 'index.html';

  function markActive(linkSelector) {
    document.querySelectorAll(linkSelector).forEach(function (link) {
      const href = link.getAttribute('href');
      if (!href) return;

      const linkFile = href.split('/').pop().split('#')[0] || 'index.html';

      if (linkFile === currentFile) {
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
      }
    });
  }

  markActive('.nav-link');
  markActive('.nav-mobile-link');

  // ---------------------------------------------------------------
  // 4. Count-up animation (reusable, used on index + statistics)
  // ---------------------------------------------------------------
  const countUpEls = document.querySelectorAll('[data-countup]');

  if (countUpEls.length > 0) {
    function animateCount(el) {
      const target = parseInt(el.getAttribute('data-countup'), 10);
      const suffix = el.getAttribute('data-suffix') || '';
      const duration = 1600;
      const startTime = performance.now();

      function step(now) {
        const elapsed = now - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Ease-out cubic
        const eased = 1 - Math.pow(1 - progress, 3);
        const current = Math.floor(eased * target);
        el.textContent = current.toLocaleString('id-ID') + suffix;
        if (progress < 1) requestAnimationFrame(step);
        else el.textContent = target.toLocaleString('id-ID') + suffix;
      }

      requestAnimationFrame(step);
    }

    const observer = new IntersectionObserver(function (entries, obs) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          animateCount(entry.target);
          obs.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });

    countUpEls.forEach(function (el) { observer.observe(el); });
  }

})();
