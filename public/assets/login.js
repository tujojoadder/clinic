document.addEventListener('DOMContentLoaded', function () {
    const passwordInput = document.getElementById('password');
    const toggleButton = document.getElementById('toggle-password');

    if (!passwordInput || !toggleButton) {
        return;
    }

    toggleButton.addEventListener('click', function () {
        const showPassword = passwordInput.type === 'password';

        passwordInput.type = showPassword ? 'text' : 'password';

        toggleButton.textContent = showPassword ? 'লুকান' : 'দেখুন';

        toggleButton.setAttribute(
            'aria-pressed',
            String(showPassword)
        );

        toggleButton.setAttribute(
            'aria-label',
            showPassword ? 'পাসওয়ার্ড লুকান' : 'পাসওয়ার্ড দেখান'
        );
    });
});