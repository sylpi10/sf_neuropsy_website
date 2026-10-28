import { Controller } from '@hotwired/stimulus';

const DELAY = 6500;

// Slider de l'accueil : défilement automatique (sauf si moins d'animations demandé), points de navigation,
// bouton pause, images des slides suivantes chargées en différé (data-bg / data-bg-mobile)
export default class extends Controller {
    static targets = ['slide', 'dot', 'pause'];

    connect() {
        this.current = 0;
        if (this.slideTargets.length < 2) {
            return;
        }

        this.slideTargets.forEach((slide, i) => {
            slide.style.transform = `translateX(${i === this.current ? 0 : 100}%)`;
            slide.setAttribute('aria-hidden', i !== this.current);
        });
        this.loadDeferredSlides();

        this.paused = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        this.syncPauseButton();
        this.restart();
    }

    disconnect() {
        this.stop();
        window.cancelIdleCallback?.(this.idleCallback);
        clearTimeout(this.idleTimeout);
    }

    show({ params: { index } }) {
        this.goTo(index);
        this.restart();
    }

    togglePause() {
        this.paused = !this.paused;
        this.syncPauseButton();
        this.restart();
    }

    next() {
        this.goTo(this.current + 1);
    }

    goTo(index) {
        const count = this.slideTargets.length;
        const next = (index + count) % count;
        if (next === this.current) {
            return;
        }

        const direction = this.shortestDirection(this.current, next);
        const incoming = this.slideTargets[next];
        const outgoing = this.slideTargets[this.current];

        // la slide entrante est placée sans animation du côté d'où elle arrive
        incoming.style.transition = 'none';
        incoming.style.transform = `translateX(${direction * 100}%)`;
        void incoming.offsetWidth;
        incoming.style.transition = '';

        requestAnimationFrame(() => {
            outgoing.style.transform = `translateX(${-direction * 100}%)`;
            incoming.style.transform = 'translateX(0)';
        });

        outgoing.classList.remove('is-active');
        incoming.classList.add('is-active');
        outgoing.setAttribute('aria-hidden', 'true');
        incoming.setAttribute('aria-hidden', 'false');

        const previousDot = this.dotTargets[this.current];
        const nextDot = this.dotTargets[next];
        previousDot?.classList.remove('is-active');
        previousDot?.removeAttribute('aria-current');
        nextDot?.classList.add('is-active');
        nextDot?.setAttribute('aria-current', 'true');

        this.current = next;
    }

    shortestDirection(from, to) {
        const count = this.slideTargets.length;
        let diff = to - from;
        if (diff > count / 2) diff -= count;
        if (diff < -count / 2) diff += count;
        return diff < 0 ? -1 : 1;
    }

    stop() {
        clearInterval(this.timer);
        this.timer = null;
    }

    restart() {
        this.stop();
        if (!this.paused) {
            this.timer = setInterval(() => this.next(), DELAY);
        }
    }

    syncPauseButton() {
        if (!this.hasPauseTarget) {
            return;
        }
        this.pauseTarget.classList.toggle('is-paused', this.paused);
        this.pauseTarget.setAttribute('aria-label', this.paused ? 'Relancer le défilement' : 'Mettre le défilement en pause');
    }

    // La 1re slide est préchargée dans le <head> ; les autres images attendent que la page soit chargée et le navigateur libre
    loadDeferredSlides() {
        const apply = () => {
            const isMobile = window.matchMedia('(max-width: 768px)').matches;
            this.slideTargets.forEach((slide) => {
                const bg = (isMobile && slide.dataset.bgMobile) || slide.dataset.bg;
                if (!bg) {
                    return;
                }
                slide.style.backgroundImage = `url("${bg}")`;
                delete slide.dataset.bg;
                delete slide.dataset.bgMobile;
            });
        };
        const schedule = () => {
            if ('requestIdleCallback' in window) {
                this.idleCallback = requestIdleCallback(apply, { timeout: 2000 });
            } else {
                this.idleTimeout = setTimeout(apply, 500);
            }
        };

        if (document.readyState === 'complete') {
            schedule();
        } else {
            window.addEventListener('load', schedule, { once: true });
        }
    }
}
