
// ================================
// Menu Script for Satay Kajang Uncle Ujang
// ================================

document.addEventListener("DOMContentLoaded", function () {
    // Selectors
    const menuItems = document.querySelectorAll(".menu-item");
    const modal = document.getElementById("productModal");
    const modalTitle = document.getElementById("modal-title");
    const modalImage = document.getElementById("modal-image");
    const modalDescription = document.getElementById("modal-description");
    const modalPrice = document.getElementById("modal-price");
    const quantityInput = document.getElementById("quantity-input");
    const addToCartBtn = document.getElementById("add-to-cart-btn");
    const closeModal = document.querySelector(".modal .close");

    const cartSummary = document.getElementById("cart-summary");
    const cartItemsList = document.getElementById("cart-items");
    const totalPriceEl = document.getElementById("total-price");
    const checkoutBtn = document.getElementById("checkout-btn");

    // Minimum order modal
    const minModal = document.getElementById("custom-minimum-modal");
    const minModalClose = document.getElementById("custom-modal-close-btn");

    // Cart storage
    let cart = [];
    let currentItem = null;

    // ================================
    // 1️⃣ Open Product Modal when item clicked
    // ================================
    menuItems.forEach(item => {
        item.addEventListener("click", function () {
            const name = this.dataset.name;
            const price = parseFloat(this.dataset.price);
            const description = this.dataset.description;
            const imagePath = this.dataset.image;

            currentItem = { name, price, description, imagePath };

            // Fill modal content
            modalTitle.textContent = name;
            modalDescription.textContent = description;
            modalPrice.textContent = "RM " + price.toFixed(2);

            // ✅ Image path fix — ensure correct relative path
            if (imagePath.startsWith("../image/")) {
                modalImage.src = imagePath;
            } else {
                modalImage.src = "../image/" + imagePath;
            }

            modalImage.alt = name;

            // Reset quantity
            quantityInput.value = 1;

            // Show modal
            modal.style.display = "block";
        });
    });

    // ================================
    // 2️⃣ Close modal
    // ================================
    closeModal.addEventListener("click", () => {
        modal.style.display = "none";
    });

    window.addEventListener("click", (e) => {
        if (e.target === modal) modal.style.display = "none";
    });

    // ================================
    // 3️⃣ Quantity increase/decrease
    // ================================
    document.getElementById("plus-btn").addEventListener("click", () => {
        quantityInput.value = parseInt(quantityInput.value) + 1;
    });

    document.getElementById("minus-btn").addEventListener("click", () => {
        if (parseInt(quantityInput.value) > 1) {
            quantityInput.value = parseInt(quantityInput.value) - 1;
        }
    });

    // ================================
    // 4️⃣ Add to Cart button
    // ================================
    addToCartBtn.addEventListener("click", () => {
        if (!currentItem) return;

        const quantity = parseInt(quantityInput.value);
        const minOrder = 5;

        // Custom rule: Minimum order of 5 skewers
        if (quantity < minOrder) {
            modal.style.display = "none";
            minModal.style.display = "block";
            return;
        }

        // Check if item already in cart
        const existingItem = cart.find(item => item.name === currentItem.name);
        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            cart.push({
                ...currentItem,
                quantity: quantity
            });
        }

        updateCartSummary();

        // Close modal
        modal.style.display = "none";
    });

    // ================================
    // 5️⃣ Update Cart Summary
    // ================================
    function updateCartSummary() {
        cartItemsList.innerHTML = "";
        let total = 0;

        cart.forEach(item => {
            const li = document.createElement("li");
            li.innerHTML = `
                <strong>${item.name}</strong> x ${item.quantity} 
                - RM ${(item.price * item.quantity).toFixed(2)}
            `;
            cartItemsList.appendChild(li);

            total += item.price * item.quantity;
        });

        totalPriceEl.textContent = "RM " + total.toFixed(2);

        // Show/hide cart summary based on cart content
        cartSummary.style.display = cart.length > 0 ? "block" : "none";
    }

    // ================================
    // 6️⃣ Minimum Order Modal (custom)
    // ================================
    minModalClose.addEventListener("click", () => {
        minModal.style.display = "none";
        modal.style.display = "block"; // Return to main modal
    });

    window.addEventListener("click", (e) => {
        if (e.target === minModal) minModal.style.display = "none";
    });

    // ================================
    // 7️⃣ Checkout Button
    // ================================
    checkoutBtn.addEventListener("click", () => {
        if (cart.length === 0) {
            alert("Your cart is empty!");
            return;
        }

        let message = "You have ordered:\n";
        cart.forEach(item => {
            message += `- ${item.name} x${item.quantity} = RM ${(item.price * item.quantity).toFixed(2)}\n`;
        });
        message += `\nTotal: RM ${totalPriceEl.textContent.replace("RM ", "")}`;
        alert(message);
    });
});
