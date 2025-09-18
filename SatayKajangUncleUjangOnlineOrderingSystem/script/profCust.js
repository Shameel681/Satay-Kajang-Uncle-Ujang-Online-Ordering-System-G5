 const editBtn = document.getElementById("edit-btn");
    const saveBtn = document.getElementById("save-btn");
    const cancelBtn = document.getElementById("cancel-btn");
    const inputs = document.querySelectorAll("#profile-form input, #profile-form textarea, #profile-form select");

    // Simpan value asal
    let originalValues = {};
    inputs.forEach(input => {
        originalValues[input.name] = input.value;
    });

    // Klik Edit → enable semua field
    editBtn.addEventListener("click", () => {
        inputs.forEach(input => input.disabled = false);
        editBtn.style.display = "none";
        saveBtn.style.display = "inline-block";
        cancelBtn.style.display = "inline-block";
    });

    // Klik Cancel → reset balik ke value asal
    cancelBtn.addEventListener("click", () => {
        inputs.forEach(input => {
            input.value = originalValues[input.name];
            input.disabled = true;
        });
        editBtn.style.display = "inline-block";
        saveBtn.style.display = "none";
        cancelBtn.style.display = "none";
    });



  