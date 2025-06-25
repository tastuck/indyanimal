// run when page loads
document.addEventListener("DOMContentLoaded", () => {
    // get the form
    const form = document.getElementById("signup-form");

    // get the loading spinner
    const loading = document.querySelector(".img-loading-container");

    // on form submit
    form.addEventListener("submit", (e) => {
        // check the required fields
        const email = form.email.value.trim();
        const password = form.password.value.trim();
        const invite = form.invite.value.trim();

        // if any are empty, block submit
        if (!email || !password || !invite) {
            e.preventDefault();
            alert("Please fill out all fields.");
            return;
        }

        // show spinner
        if (loading) {
            loading.style.display = "block";
        }
    });
});

