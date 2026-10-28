import { Controller } from '@hotwired/stimulus';

// Menu principal : ouverture mobile, sous-menus (clic, et survol sur desktop), fermeture au clic
// à l'extérieur ou avec Échap, fond du header au défilement
export default class extends Controller {
    static targets = ['toggle', 'nav'];

    connect() {
        // Sur desktop les sous-menus s'ouvrent aussi au survol (CSS) : on garde aria-expanded à jour
        this.hoverMenus = window.matchMedia('(hover: hover) and (pointer: fine) and (min-width: 1151px)');
        this.onScroll();
    }

    toggle() {
        const open = this.navTarget.classList.toggle('is-open');
        this.toggleTarget.setAttribute('aria-expanded', open);
        this.toggleTarget.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
        this.toggleTarget.classList.toggle('is-active', open);
        document.body.classList.toggle('nav-open', open);
    }

    toggleDropdown(event) {
        event.preventDefault();
        const item = event.currentTarget.closest('.has-dropdown');
        const open = !item.classList.contains('is-open');
        this.openItems().forEach((other) => {
            if (other !== item) {
                this.setDropdown(other, false);
            }
        });
        this.setDropdown(item, open);
    }

    hoverIn(event) {
        if (this.hoverMenus.matches) {
            this.dropdownToggle(event.currentTarget).setAttribute('aria-expanded', 'true');
        }
    }

    hoverOut(event) {
        const item = event.currentTarget;
        this.dropdownToggle(item).setAttribute('aria-expanded', item.classList.contains('is-open'));
    }

    close() {
        this.navTarget.classList.remove('is-open');
        this.toggleTarget.setAttribute('aria-expanded', 'false');
        this.toggleTarget.setAttribute('aria-label', 'Ouvrir le menu');
        this.toggleTarget.classList.remove('is-active');
        document.body.classList.remove('nav-open');
        this.openItems().forEach((item) => this.setDropdown(item, false));
    }

    clickOutside(event) {
        if (!this.navTarget.contains(event.target) && !this.toggleTarget.contains(event.target)) {
            this.close();
        }
    }

    // Échap : on referme et on rend le focus au bouton qui avait ouvert le menu mobile ou le sous-menu
    escape() {
        const active = document.activeElement;
        const openItem = active?.closest('.has-dropdown.is-open');
        let returnTo = null;
        if (this.navTarget.classList.contains('is-open') && this.navTarget.contains(active)) {
            returnTo = this.toggleTarget;
        } else if (openItem) {
            returnTo = this.dropdownToggle(openItem);
        }
        this.close();
        returnTo?.focus();
    }

    onScroll() {
        this.element.classList.toggle('is-scrolled', window.scrollY > 12);
    }

    openItems() {
        return this.navTarget.querySelectorAll('.has-dropdown.is-open');
    }

    dropdownToggle(item) {
        return item.querySelector('.dropdown-toggle');
    }

    setDropdown(item, open) {
        item.classList.toggle('is-open', open);
        this.dropdownToggle(item).setAttribute('aria-expanded', open);
    }
}
