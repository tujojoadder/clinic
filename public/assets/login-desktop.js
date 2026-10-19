'use strict';

document.addEventListener('DOMContentLoaded', function () {
    const password = document.getElementById('password');
    const toggle = document.getElementById('toggle-password');
    const warning = document.getElementById('caps-warning');

    if (!password || !toggle) {
        return;
    }

    toggle.hidden = false;

    function hidePassword() {
        password.type = 'password';
        toggle.textContent = 'দেখুন';
        toggle.setAttribute('aria-pressed', 'false');
        toggle.setAttribute('aria-label', 'পাসওয়ার্ড দেখান');
    }

    toggle.addEventListener('click', function () {
        const reveal = password.type === 'password';

        password.type = reveal ? 'text' : 'password';
        toggle.textContent = reveal ? 'লুকান' : 'দেখুন';
        toggle.setAttribute('aria-pressed', String(reveal));
        toggle.setAttribute(
            'aria-label',
            reveal ? 'পাসওয়ার্ড লুকান' : 'পাসওয়ার্ড দেখান'
        );
    });

    password.addEventListener('keyup', function (event) {
        if (warning && typeof event.getModifierState === 'function') {
            warning.hidden = !event.getModifierState('CapsLock');
        }
    });

    password.addEventListener('blur', function () {
        if (warning) {
            warning.hidden = true;
        }
    });

    document.addEventListener('visibilitychange', function () {
        if (document.hidden) {
            hidePassword();
        }
    });

    if (password.form) {
        password.form.addEventListener('submit', hidePassword);
    }
});
