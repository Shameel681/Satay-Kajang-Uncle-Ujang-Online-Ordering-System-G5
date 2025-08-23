// Get all necessary elements from the DOM
const menuItems = document.querySelectorAll('.menu-item');
const productModal = document.getElementById('productModal');
const closeBtn = productModal.querySelector('.close');
const addToCartBtn = document.getElementById('add-to-cart-btn');
const quantityInput = document.getElementById('quantity-input');
const plusBtn = document.getElementById('plus-btn');
const minusBtn = document.getElementById('minus-btn');

const checkoutBtn = document.getElementById('checkout-btn');
const customMinimumModal = document.getElementById('custom-minimum-modal');
const customModalCloseBtn = document.getElementById('custom-modal-close-btn');

let cart = {};

// Open product modal when a menu item is clicked
menuItems.forEach(item => {
    item.addEventListener('click', () => {
        // ... (your existing code to populate the product modal)
        productModal.style.display = 'block';
    });
});

// Close product modal
closeBtn.addEventListener('click', () => {
    productModal.style.display = 'none';
});

// Close product modal if user clicks outside of it
window.addEventListener('click', (event) => {
    if (event.target == productModal) {
        productModal.style.display = 'none';
    }
});

// Quantity controls
plusBtn.addEventListener('click', () => {
    quantityInput.value = parseInt(quantityInput.value) + 1;
});

minusBtn.addEventListener('click', () => {
    if (parseInt(quantityInput.value) > 1) {
        quantityInput.value = parseInt(quantityInput.value) - 1;
    }
});

// Add to cart functionality
addToCartBtn.addEventListener('click', () => {
    const itemName = document.getElementById('modal-title').textContent;
    const itemPrice = parseFloat(document.getElementById('modal-price').textContent.replace('RM ', ''));
    const quantity = parseInt(quantityInput.value);

    // Update cart object
    if (cart[itemName]) {
        cart[itemName].quantity += quantity;
    } else {
        cart[itemName] = {
            price: itemPrice,
            quantity: quantity
        };
    }

    // Update cart summary display
    updateCartSummary();

    // Close the product modal
    productModal.style.display = 'none';
});

function updateCartSummary() {
    const cartItemsList = document.getElementById('cart-items');
    let totalPrice = 0;
    cartItemsList.innerHTML = ''; // Clear previous items

    for (const item in cart) {
        const totalItemPrice = cart[item].price * cart[item].quantity;
        totalPrice += totalItemPrice;
        
        const li = document.createElement('li');
        li.textContent = `${item} x${cart[item].quantity} - RM ${totalItemPrice.toFixed(2)}`;
        cartItemsList.appendChild(li);
    }
    
    document.getElementById('total-price').textContent = `RM ${totalPrice.toFixed(2)}`;
}

// ** This is the fix for the minimum order modal **
checkoutBtn.addEventListener('click', () => {
    let totalSkewers = 0;
    // Assuming satay items are 'Satay Ayam', 'Satay Daging', 'Satay Perut', 'Satay Kambing'
    const satayItems = ['Satay Ayam', 'Satay Daging', 'Satay Perut', 'Satay Kambing'];

    for (const item in cart) {
        if (satayItems.includes(item)) {
            totalSkewers += cart[item].quantity;
        }
    }

    if (totalSkewers < 5) {
        customMinimumModal.style.display = 'block'; // Show the modal
    } else {
        // Proceed with checkout (e.g., redirect to checkout page)
        alert('Proceeding to checkout!');
    }
});

customModalCloseBtn.addEventListener('click', () => {
    customMinimumModal.style.display = 'none'; // Close the modal
});