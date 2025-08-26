document.addEventListener("DOMContentLoaded", function () {
    const editBtn = document.getElementById("edit-btn");
    const saveBtn = document.getElementById("save-btn");
    const cancelBtn = document.getElementById("cancel-btn");
    const inputs = document.querySelectorAll(".profile-details input, .profile-details textarea");

    const changePassBtn = document.getElementById("change-pass-btn");
    const changePassSection = document.getElementById("change-password-section");

    // --- Edit Profile toggle ---
    if (editBtn) {
        editBtn.addEventListener("click", function () {
            inputs.forEach(input => input.removeAttribute("readonly"));
            saveBtn.style.display = "inline-block";
            cancelBtn.style.display = "inline-block";
            editBtn.style.display = "none";
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener("click", function () {
            inputs.forEach(input => input.setAttribute("readonly", true));
            saveBtn.style.display = "none";
            cancelBtn.style.display = "none";
            editBtn.style.display = "inline-block";
        });
    }

    // --- Change Password toggle ---
    if (changePassBtn) {
        changePassBtn.addEventListener("click", function () {
            if (changePassSection.style.display === "none") {
                changePassSection.style.display = "block";
            } else {
                changePassSection.style.display = "none";
            }
        });
    }
});
