document.addEventListener("DOMContentLoaded", () => {
    const editBtn = document.getElementById("editProfileBtn");
    const editForm = document.getElementById("editProfileForm");
    const profileDetails = document.getElementById("profileDetails");
    const cancelEditBtn = document.getElementById("cancelEditBtn");
  
    editBtn.addEventListener("click", () => {
      profileDetails.style.display = "none";
      editBtn.style.display = "none";
      editForm.style.display = "block";
    });
  
    cancelEditBtn.addEventListener("click", () => {
      editForm.style.display = "none";
      profileDetails.style.display = "block";
      editBtn.style.display = "inline-block";
    });
  });
  