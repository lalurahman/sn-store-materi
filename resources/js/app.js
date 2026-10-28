

import Alpine from 'alpinejs';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

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

document.addEventListener('submit', async (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || ! form.matches('[data-confirm-delete]')) {
        return;
    }

    if (form.dataset.confirmed === 'true') {
        delete form.dataset.confirmed;
        return;
    }

    event.preventDefault();

    if (form.dataset.confirming === 'true') {
        return;
    }

    form.dataset.confirming = 'true';

    try {
        const isDark = document.documentElement.classList.contains('dark');
        const result = await Swal.fire({
            title: 'Hapus kategori?',
            text: `Kategori "${form.dataset.categoryName}" akan dihapus permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#b91c1c',
            cancelButtonColor: '#71717a',
            reverseButtons: true,
            focusCancel: true,
            background: isDark ? '#18181b' : '#ffffff',
            color: isDark ? '#f4f4f5' : '#18181b',
        });

        if (result.isConfirmed) {
            form.dataset.confirmed = 'true';
            form.requestSubmit();
        }
    } finally {
        delete form.dataset.confirming;
    }
});
