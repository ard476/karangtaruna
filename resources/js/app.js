import './bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('admin-menu-toggle');
    const sheet = document.getElementById('admin-menu-sheet');
    const backdrop = document.getElementById('admin-menu-backdrop');

    if (!toggle || !sheet || !backdrop) {
        return;
    }

    const open = () => {
        sheet.classList.remove('translate-y-full');
        backdrop.classList.remove('hidden');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.classList.add('overflow-hidden');
    };

    const close = () => {
        sheet.classList.add('translate-y-full');
        backdrop.classList.add('hidden');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('overflow-hidden');
    };

    toggle.addEventListener('click', () => {
        if (sheet.classList.contains('translate-y-full')) {
            open();
        } else {
            close();
        }
    });

    backdrop.addEventListener('click', close);

    sheet.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', close);
    });
});
