export function changeIcon() {
    const eye = document.querySelector('.eye');
    const passwordInput = document.querySelector('input[name="password"]');
    const img = document.querySelector('.eye');

    eye.addEventListener('click', () => {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            img.src = img.src.replace('/eye-close.png', '/eye-open.png'); // Remplace "/eye-close.png" par "/eye-open.png"
            passwordInput.style.letterSpacing = '0.5rem';
        } else {
            passwordInput.type = 'password';
            img.src = img.src.replace('/eye-open.png', '/eye-close.png'); // Remplace "/eye-open.png" par "/eye-close.png"
        }
    });
}
