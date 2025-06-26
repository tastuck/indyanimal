document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("signin-form");
    const loading = document.querySelector(".img-loading-container");

    form.addEventListener("submit", (e) => {
        const email = form.email.value.trim();
        const password = form.password.value.trim();

        if (!email || !password) {
            e.preventDefault();
            alert("Please fill out both fields.");
            return;
        }

        if (loading) {
            loading.style.display = "block";
        }
    });
});


