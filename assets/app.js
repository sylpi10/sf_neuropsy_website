import './stimulus_bootstrap.js';
import * as Turbo from '@hotwired/turbo';

// Turbo Drive : les liens chargent la page en fetch et ne remplacent que le <body> (effet SPA).
// Les formulaires restent en envoi classique : Turbo exigerait un statut 422 sur chaque formulaire invalide.
Turbo.config.forms.mode = 'optin';

// Après une navigation Turbo, le lecteur d'écran n'est pas prévenu du changement de page :
// on place le focus sur le titre de la nouvelle page (ou sur la cible de l'ancre).
let firstLoad = true;
document.addEventListener('turbo:load', () => {
    if (firstLoad) {
        firstLoad = false;
        return;
    }
    focusNewPage();
});

function focusNewPage() {
    let target = null;
    if (window.location.hash) {
        try {
            target = document.getElementById(decodeURIComponent(window.location.hash.slice(1)));
        } catch {
            target = null;
        }
    }
    // Ancres invisibles (<span class="anchor-offset">) : on prend le titre de la section qui suit
    if (target && getComputedStyle(target).visibility === 'hidden' && target.nextElementSibling) {
        const section = target.nextElementSibling;
        target = section.querySelector('h2, h3') || section;
    }
    target ??= document.querySelector('#main-content h1') || document.getElementById('main-content');
    if (!target) {
        return;
    }
    if (!target.hasAttribute('tabindex')) {
        target.setAttribute('tabindex', '-1');
    }
    target.focus({ preventScroll: true });
}
