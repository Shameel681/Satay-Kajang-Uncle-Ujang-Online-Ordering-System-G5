document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('edit-btn');
    const saveBtn = document.getElementById('save-btn');
    const cancelBtn = document.getElementById('cancel-btn');
    const formFields = document.querySelectorAll('.profile-details input, .profile-details textarea');
    
    // Store the original values of the form fields to revert on cancel
    const originalValues = {};
    formFields.forEach(field => {
        originalValues[field.id] = field.value;
    });

    editBtn.addEventListener('click', function() {
        // Hide the Edit button
        editBtn.style.display = 'none';
        // Show the Save and Cancel buttons
        saveBtn.style.display = 'inline-block';
        cancelBtn.style.display = 'inline-block';
        
        // Make all form fields editable
        formFields.forEach(field => {
            field.removeAttribute('readonly');
        });
    });

    cancelBtn.addEventListener('click', function() {
        // Restore original values
        formFields.forEach(field => {
            field.value = originalValues[field.id];
            field.setAttribute('readonly', 'readonly');
        });
        
        // Hide the Save and Cancel buttons
        saveBtn.style.display = 'none';
        cancelBtn.style.display = 'none';
        // Show the Edit button
        editBtn.style.display = 'inline-block';
    });
});