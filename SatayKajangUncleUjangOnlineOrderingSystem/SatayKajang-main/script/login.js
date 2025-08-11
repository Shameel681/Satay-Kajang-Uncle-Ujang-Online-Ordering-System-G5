document.addEventListener('DOMContentLoaded', function() {
    const messageContainer = document.getElementById('message-container');
    
    // Check for a message in the URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const message = urlParams.get('message');
    const messageType = urlParams.get('type');

    if (message && messageContainer) {
        // Create the message box element
        const messageBox = document.createElement('div');
        messageBox.className = `message-box ${messageType}`;
        messageBox.textContent = decodeURIComponent(message);
        
        // Add the message box to the container
        messageContainer.appendChild(messageBox);
        
        // Remove the message after 5 seconds
        setTimeout(() => {
            messageBox.remove();
        }, 5000);
    }
});