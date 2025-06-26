document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("signup-form");
    const loading = document.querySelector(".img-loading-container");

    form.addEventListener("submit", (e) => {
        const email = form.email.value.trim();
        const password = form.password.value.trim();
        const invite = form.invite.value.trim();

        if (!email || !password || !invite) {
            e.preventDefault();
            alert("Please fill out all fields.");
            return;
        }

        if (loading) {
            loading.style.display = "block";
        }
    });
});


