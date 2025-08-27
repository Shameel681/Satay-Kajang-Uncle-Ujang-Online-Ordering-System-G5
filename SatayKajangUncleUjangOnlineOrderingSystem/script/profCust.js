  const editBtn = document.getElementById("edit-btn");
  const saveBtn = document.getElementById("save-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const inputs = document.querySelectorAll("#profile-form input");

  // Simpan value asal
  let originalValues = {};
  inputs.forEach(input => {
    originalValues[input.name] = input.value;
  });

  editBtn.addEventListener("click", () => {
    inputs.forEach(input => input.disabled = false);
    editBtn.style.display = "none";
    saveBtn.style.display = "inline-block";
    cancelBtn.style.display = "inline-block";
  });

  cancelBtn.addEventListener("click", () => {
    // Reset balik value asal
    inputs.forEach(input => {
      input.value = originalValues[input.name];
      input.disabled = true;
    });

    editBtn.style.display = "inline-block";
    saveBtn.style.display = "none";
    cancelBtn.style.display = "none";
  });