

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.store('theme', {
    isDark: document.documentElement.classList.contains('dark'),

    toggle() {
        this.isDark = ! this.isDark;
        document.documentElement.classList.toggle('dark', this.isDark);
        localStorage.setItem('sn-store-theme', this.isDark ? 'dark' : 'light');
    },
});

Alpine.start();
