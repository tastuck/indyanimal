window.addEventListener('DOMContentLoaded', () => {
    fetch('/signout')
        .then(() => window.location.href = '/signin');
});