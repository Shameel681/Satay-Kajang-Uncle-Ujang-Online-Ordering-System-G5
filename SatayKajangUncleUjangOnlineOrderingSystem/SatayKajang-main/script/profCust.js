document.addEventListener("DOMContentLoaded", () => {
    const editBtn = document.getElementById("editProfileBtn");
    const editForm = document.getElementById("editProfileForm");
    const profileDetails = document.getElementById("profileDetails");
    const cancelEditBtn = document.getElementById("cancelEditBtn");
    const toast = document.getElementById("toast");

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

    // Function to show the toast message and hide it automatically
    function showToast() {
      if (toast) {
        toast.classList.add('show');
        
        // Hide the toast after 4 seconds to account for the 0.6s CSS transition
        setTimeout(() => {
          toast.classList.remove('show');
        }, 4000); 
      }
    }

    // Call showToast when the page loads if the toast element exists
    showToast();
});