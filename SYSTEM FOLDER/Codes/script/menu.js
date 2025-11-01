// Check for logged in status passed from PHP
const isLoggedIn = typeof isLoggedInPHP !== 'undefined' ? isLoggedInPHP : false;

// --- Modal Elements ---
const productModal = document.getElementById('productModal');
const customMinimumModal = document.getElementById('custom-minimum-modal');
const loginAlertModal = document.getElementById('login-alert-modal');
const clearCartModal = document.getElementById('clear-cart-modal');
const checkoutSuccessModal = document.getElementById('checkout-success-modal');

// --- Buttons/Inputs ---
const closeButtons = document.querySelectorAll('.modal .close');
const customModalCloseBtn = document.getElementById('custom-modal-close-btn');
const loginAlertCloseBtn = document.getElementById('login-alert-close-btn');
const minusBtn = document.getElementById('minus-btn');
const plusBtn = document.getElementById('plus-btn');
const quantityInput = document.getElementById('quantity-input');
const addToCartBtn = document.getElementById('add-to-cart-btn');
const clearCartBtn = document.getElementById('clear-cart-btn');
const checkoutBtn = document.getElementById('checkout-btn');
const clearCartConfirmBtn = document.getElementById('clear-cart-confirm-btn');
const clearCartCancelBtn = document.getElementById('clear-cart-cancel-btn');
const checkoutSuccessCloseBtn = document.getElementById('checkout-success-close-btn');
const makePaymentBtn = document.getElementById('make-payment-btn');

// --- Cart Elements ---
const cartSummary = document.getElementById('cart-summary');
const cartItemsList = document.getElementById('cart-items');
const totalPriceSpan = document.getElementById('total-price');

// --- Global Cart Variable ---
let cart = {}; // Initialize as empty, will be loaded from session

let selectedItem = null; // Stores the item currently open in the product modal

// ===================================
// Helper Functions for Server Sync
// ===================================

/**
 * Syncs the local cart state with the server session.
 */
async function syncCartToServer() {
    try {
        await fetch('../customer/cart_session_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ cart: cart }),
        });
        // Error handling omitted for brevity, assume success
    } catch (error) {
        console.error('Error syncing cart to server:', error);
    }
}

/**
 * Loads the cart state from the server session on page load.
 */
async function loadCartFromServer() {
    try {
        const response = await fetch('../customer/cart_session_handler.php', {
            method: 'GET',
        });
        const result = await response.json();
        if (result.cart) {
            cart = result.cart;
            updateCartSummary();
        }
    } catch (error) {
        console.error('Error loading cart from server:', error);
    }
}

// ===================================
// Helper Functions for Modal
// ===================================

/**
 * Updates the quantity in the product modal, applying minimum quantity logic.
 * @param {number} delta - The change in quantity (+1 or -1)
 * @param {boolean} isInitial - True if called during modal open, skips minimum alert
 */
function updateModalQuantity(delta, isInitial = false) {
    let currentQuantity = parseInt(quantityInput.value) || 0;
    let newQuantity = currentQuantity + delta;

    // Determine minimum quantity: 5 for satay, 1 for others (based on item name check)
    const isSatay = selectedItem && (selectedItem.name.toLowerCase().includes('satay'));
    const minQuantity = isSatay ? 5 : 1;
    
    if (newQuantity < minQuantity) {
        newQuantity = minQuantity;
        if (!isInitial && newQuantity !== currentQuantity) {
            customMinimumModal.style.display = 'block';
        }
    } else if (newQuantity < 1) { // Ensure minimum is at least 1 for non-satay
        newQuantity = 1;
    }

    quantityInput.value = newQuantity;
}

// ===================================
// Cart Core Functions (Updated for Server Sync)
// ===================================

/**
 * Renders the cart summary with interactive controls.
 */
function updateCartSummary() {
    let total = 0;
    cartItemsList.innerHTML = '';
    
    const itemIds = Object.keys(cart);

    if (itemIds.length === 0) {
        cartSummary.style.display = 'none';
        syncCartToServer(); // Sync empty cart to server
        return;
    }

    itemIds.forEach(id => {
        const item = cart[id];
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        const isSatay = item.name.toLowerCase().includes('satay');
        const minQuantity = isSatay ? 5 : 1;

        const listItem = document.createElement('li');
        listItem.classList.add('cart-item');
        listItem.setAttribute('data-id', id);

        // Item details (Name and Unit Price)
        const detailsDiv = document.createElement('div');
        detailsDiv.classList.add('cart-item-details');
        detailsDiv.innerHTML = `
            <strong>${item.name}</strong> 
            <span class="text-muted">(RM ${item.price.toFixed(2)}/stick)</span> 
            <br>
            <span class="fw-bold">Subtotal:</span> <span class="fw-bold text-primary">RM ${(itemTotal).toFixed(2)}</span>
        `;
        listItem.appendChild(detailsDiv);

        // Controls (-, quantity display, +, remove)
        const controlsDiv = document.createElement('div');
        controlsDiv.classList.add('cart-item-controls');
        
        // Minus Button
        const minusButton = document.createElement('button');
        minusButton.textContent = '–';
        minusButton.classList.add('btn', 'btn-quantity');
        // Disable minus button if quantity is at minimum
        if (item.quantity === minQuantity) {
            minusButton.disabled = true;
        }
        minusButton.addEventListener('click', () => decrementQuantity(id));
        controlsDiv.appendChild(minusButton);

        // Quantity Display
        const quantitySpan = document.createElement('span');
        quantitySpan.classList.add('item-quantity-display', 'mx-1');
        quantitySpan.textContent = item.quantity;
        controlsDiv.appendChild(quantitySpan);

        // Plus Button
        const plusButton = document.createElement('button');
        plusButton.textContent = '+';
        plusButton.classList.add('btn', 'btn-quantity');
        plusButton.addEventListener('click', () => incrementQuantity(id));
        controlsDiv.appendChild(plusButton);
        
        // Remove button
        const removeButton = document.createElement('button');
        removeButton.innerHTML = '<i class="fa-solid fa-trash-can"></i>';
        removeButton.classList.add('btn', 'btn-remove-item', 'ms-2');
        removeButton.title = 'Remove item';
        removeButton.addEventListener('click', () => removeItem(id));
        controlsDiv.appendChild(removeButton);

        listItem.appendChild(controlsDiv);
        cartItemsList.appendChild(listItem);
    });

    totalPriceSpan.textContent = RM ${total.toFixed(2)};
    cartSummary.style.display = 'block';
    syncCartToServer(); // Sync cart state to server whenever it updates
}

/**
 * Adds an item from the modal to the cart.
 * @param {object} item - The item object from the modal (selectedItem)
 * @param {number} quantity - The quantity to add
 */
function addToCart(item, quantity) {
    if (!item || quantity <= 0) return;

    const id = item.id;
    const name = item.name;
    const price = parseFloat(item.price);
    const qty = parseInt(quantity);

    if (cart[id]) {
        // Item exists, update quantity
        cart[id].quantity += qty;
    } else {
        // New item
        cart[id] = { id, name, price, quantity: qty };
    }

    updateCartSummary();
    productModal.style.display = 'none';
}

/**
 * Increases the quantity of a specific item in the cart.
 * @param {string} id - The food_id of the item to increment.
 */
function incrementQuantity(id) {
    if (cart[id]) {
        cart[id].quantity += 1;
        updateCartSummary();
    }
}

/**
 * Decreases the quantity of a specific item in the cart, checking for minimum order.
 * @param {string} id - The food_id of the item to decrement.
 */
function decrementQuantity(id) {
    if (cart[id]) {
        const isSatay = cart[id].name.toLowerCase().includes('satay');
        const minQuantity = isSatay ? 5 : 1;

        if (cart[id].quantity > minQuantity) {
            cart[id].quantity -= 1;
            updateCartSummary();
        } else {
             if (isSatay && cart[id].quantity === minQuantity) {
                 customMinimumModal.style.display = 'block';
             }
        }
    }
}

/**
 * Removes a specific item from the cart.
 * @param {string} id - The food_id of the item to remove.
 */
function removeItem(id) {
    if (cart[id]) {
        delete cart[id];
        updateCartSummary();
    }
}

/**
 * Clears the entire cart.
 */
function clearCart() {
    cart = {};
    updateCartSummary();
    clearCartModal.style.display = 'none';
    // Server clear is implicitly handled by syncCartToServer() in updateCartSummary
}

/**
 * Handles the checkout process (Real AJAX to PHP).
 */
async function handleCheckout() {
    if (!isLoggedIn) {
        loginAlertModal.style.display = 'block';
        return;
    }

    if (Object.keys(cart).length === 0) {
        alert('Your cart is empty!');
        return;
    }

    // Convert cart object to an array for easier PHP handling
    const cartArray = Object.values(cart);

    try {
        const response = await fetch('customer_checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ cart: cartArray }),
        });

        const result = await response.json();

        if (result.status === 'success') {
            // Server returned success, clear the local cart and session
            cart = {};
            updateCartSummary(); // Will sync empty cart to server session
            
            // Show success modal with the actual order ID from the server
            document.getElementById('order-id').textContent = result.order_id;
            checkoutSuccessModal.style.display = 'block';
            
        } else {
            alert(Checkout failed: ${result.message});
        }
    } catch (error) {
        console.error('Network or server error during checkout:', error);
        alert('An error occurred during checkout. Please check the console for details.');
    }
}

// ===================================
// Event Listeners Initialization
// ===================================

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initial cart load from server
    loadCartFromServer();

    // 2. Attach event listeners to all 'Add to Cart' buttons in the menu
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', (e) => {
            const itemElement = e.target.closest('.menu-item');
            
            // Set selectedItem to populate modal
            selectedItem = {
                id: itemElement.getAttribute('data-id'),
                name: itemElement.getAttribute('data-name'),
                // Ensure price is parsed correctly
                price: parseFloat(itemElement.getAttribute('data-price')), 
                image: itemElement.getAttribute('data-image'),
                description: itemElement.getAttribute('data-description')
            };

            // Populate modal
            document.getElementById('modal-title').textContent = selectedItem.name;
            document.getElementById('modal-description').textContent = selectedItem.description;
            document.getElementById('modal-price').textContent = Price: RM ${selectedItem.price.toFixed(2)};
            document.getElementById('modal-image').src = selectedItem.image;

            // Set initial quantity and apply minimum if applicable
            const isSatay = selectedItem.name.toLowerCase().includes('satay');
            const initialQuantity = isSatay ? 5 : 1;
            quantityInput.value = initialQuantity;

            productModal.style.display = 'block';
        });
    });

    // 3. Close Modals (X buttons and clicking outside)
    closeButtons.forEach(btn => btn.addEventListener('click', () => {
        productModal.style.display = 'none';
        selectedItem = null; // Clear selected item on close
    }));
    window.addEventListener('click', (event) => {
        if (event.target === productModal) {
            productModal.style.display = 'none';
            selectedItem = null; // Clear selected item on close
        }
    });

    // 4. Quantity Controls in Product Modal
    plusBtn.addEventListener('click', () => updateModalQuantity(1));
    minusBtn.addEventListener('click', () => updateModalQuantity(-1));
    
    // Re-validate on manual input change
    quantityInput.addEventListener('change', () => updateModalQuantity(0)); 

    // 5. Final Add To Cart Button in Modal
    addToCartBtn.addEventListener('click', () => {
        const quantity = parseInt(quantityInput.value); 
        
        if (selectedItem && quantity > 0) {
            addToCart(selectedItem, quantity);
        } else {
             console.error("Error: Item not selected or invalid quantity.");
        }
        
        selectedItem = null; 
    });

    // 6. Cart Action Buttons (Summary)
    clearCartBtn.addEventListener('click', () => {
        clearCartModal.style.display = 'block';
    });

    checkoutBtn.addEventListener('click', handleCheckout);

    // 7. Confirmation/Alert Modals Handlers
    customModalCloseBtn.addEventListener('click', () => customMinimumModal.style.display = 'none');
    loginAlertCloseBtn.addEventListener('click', () => loginAlertModal.style.display = 'none');
    
    // Clear Cart Confirmation
    clearCartConfirmBtn.addEventListener('click', clearCart);
    clearCartCancelBtn.addEventListener('click', () => clearCartModal.style.display = 'none');

    // 8. Checkout Success Actions
    checkoutSuccessCloseBtn.addEventListener('click', () => checkoutSuccessModal.style.display = 'none');
    makePaymentBtn.addEventListener('click', () => {
        const orderId = document.getElementById('order-id').textContent;
        // Redirect to a payment page with the order ID
        window.location.replace(payment.php?order_id=${orderId}); 
    });
});