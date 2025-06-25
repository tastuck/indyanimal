// wait until everything on the page is ready
document.addEventListener("DOMContentLoaded", () => {
    // get the sign-in form
    const form = document.getElementById("signin-form");

    // grab the loading emote (should be hidden by default in CSS)
    const loading = document.querySelector(".img-loading-container");

    // when the form gets submitted
    form.addEventListener("submit", (e) => {
        // get the email and password values
        const email = form.email.value.trim();
        const password = form.password.value.trim();

        // if either field is empty, stop the form and show a message
        if (!email || !password) {
            e.preventDefault();
            alert("Please fill out both fields.");
            return;
        }

        // if the fields are good, show the loading emote
        if (loading) {
            loading.style.display = "block";
        }
    });
});

