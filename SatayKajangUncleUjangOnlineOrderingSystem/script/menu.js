
    document.addEventListener('DOMContentLoaded', function () {
        const loginAlertModal = document.getElementById('login-alert-modal');
        const loginAlertCloseBtn = document.getElementById('login-alert-close-btn');
        const checkoutSuccessModal = document.getElementById('checkout-success-modal');
        const checkoutSuccessCloseBtn = document.getElementById('checkout-success-close-btn');
        const clearCartModal = document.getElementById('clear-cart-modal');
        const clearCartCancelBtn = document.getElementById('clear-cart-cancel-btn');
        const clearCartConfirmBtn = document.getElementById('clear-cart-confirm-btn');

        // ========== CART STORAGE ==========
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        function saveCart() {
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
        }

        function updateCartUI() {
            const cartItemsContainer = document.getElementById('cart-items');
            const totalPriceElement = document.getElementById('total-price');
            const cartSummary = document.getElementById('cart-summary');

            if (!cartItemsContainer || !totalPriceElement) return;

            cartItemsContainer.innerHTML = '';
            let total = 0;

            if (cart.length === 0) {
                cartSummary.style.display = 'none';
                return;
            }

            cartSummary.style.display = 'block';
            cart.forEach(item => {
                const li = document.createElement('li');
                li.textContent = `${item.name} x${item.quantity} - RM ${item.price.toFixed(2)}`;
                cartItemsContainer.appendChild(li);
                total += item.price * item.quantity;
            });

            totalPriceElement.textContent = `RM ${total.toFixed(2)}`;
        }

        // ========== LOGIN POPUP ==========
        if (loginAlertCloseBtn) {
            loginAlertCloseBtn.addEventListener('click', function () {
                window.location.href = '../register.php';
            });
        }

        // ========== MODAL ELEMENTS ==========
        const productModal = document.getElementById('productModal');
        const modalImage = document.getElementById('modal-image');
        const modalTitle = document.getElementById('modal-title');
        const modalDesc = document.getElementById('modal-description');
        const modalPrice = document.getElementById('modal-price');
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        const minusBtn = document.getElementById('minus-btn');
        const plusBtn = document.getElementById('plus-btn');
        const qtyInput = document.getElementById('quantity-input');
        const modalClose = document.querySelector('#productModal .close');

        let currentItem = null;

        // ========== ADD TO CART FLOW ==========
        document.querySelectorAll('.add-to-cart').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();

                if (!isLoggedIn) {
                    loginAlertModal.style.display = 'block';
                    return;
                }

                // Show modal with item info
                const item = btn.closest('.menu-item');
                currentItem = {
                    id: item.dataset.id,
                    name: item.dataset.name,
                    price: parseFloat(item.dataset.price),
                    image: item.dataset.image,
                    description: item.dataset.description
                };

                modalImage.src = currentItem.image;
                modalTitle.textContent = currentItem.name;
                modalDesc.textContent = currentItem.description;
                modalPrice.textContent = `RM ${currentItem.price.toFixed(2)}`;
                qtyInput.value = 1;

                productModal.style.display = 'block';
            });
        });

        // Quantity controls
        minusBtn.addEventListener('click', () => {
            let val = parseInt(qtyInput.value);
            if (val > 1) qtyInput.value = val - 1;
        });
        plusBtn.addEventListener('click', () => {
            let val = parseInt(qtyInput.value);
            qtyInput.value = val + 1;
        });

        // Confirm Add to Cart
        addToCartBtn.addEventListener('click', function () {
            const qty = parseInt(qtyInput.value);
            if (!currentItem) return;

            // Check if already in cart
            const existing = cart.find(i => i.id === currentItem.id);
            if (existing) {
                existing.quantity += qty;
            } else {
                cart.push({ ...currentItem, quantity: qty });
            }

            saveCart();
            productModal.style.display = 'none';
        });

        // Close modal
        modalClose.addEventListener('click', () => {
            productModal.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target === productModal) {
                productModal.style.display = 'none';
            }
            if (event.target === loginAlertModal) {
                loginAlertModal.style.display = 'none';
            }
            if (event.target === checkoutSuccessModal) {
                checkoutSuccessModal.style.display = 'none';
            }
            if (event.target === clearCartModal) {
                clearCartModal.style.display = 'none';
            }
        });

        // Load cart when page loads
        updateCartUI();

        // Checkout Button
        const checkoutBtn = document.getElementById('checkout-btn');
        if (checkoutBtn) {
            checkoutBtn.addEventListener('click', async function () {
                if (!isLoggedIn) {
                    loginAlertModal.style.display = 'block';
                    return;
                }

                if (cart.length === 0) {
                    alert("Your cart is empty.");
                    return;
                }

                try {
                    const response = await fetch('customer_checkout.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ cart })
                    });
                    const data = await response.json();

                    if (data.status === 'success') {
                        // Show success modal with order details
                        const orderIdElement = document.getElementById('order-id');
                        orderIdElement.textContent = `ORDER-${Math.floor(Math.random() * 10000) + 10000}`; // Simulated order ID
                        checkoutSuccessModal.style.display = 'block';
                        cart = [];
                        localStorage.removeItem('cart');
                        updateCartUI();
                    } else {
                        alert(data.message || 'Failed to save order.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Error communicating with the server.');
                }
            });
        }

        // ========== CLEAR CART BUTTON ==========
        const clearCartBtn = document.getElementById('clear-cart-btn');
        if (clearCartBtn) {
            clearCartBtn.addEventListener('click', function () {
                clearCartModal.style.display = 'block';
            });
        }

        // Clear Cart Confirmation
        if (clearCartCancelBtn) {
            clearCartCancelBtn.addEventListener('click', function () {
                clearCartModal.style.display = 'none';
            });
        }

        if (clearCartConfirmBtn) {
            clearCartConfirmBtn.addEventListener('click', function () {
                cart = [];
                localStorage.removeItem('cart');
                updateCartUI();
                clearCartModal.style.display = 'none';
            });
        }

        // Close Success Modal
        if (checkoutSuccessCloseBtn) {
            checkoutSuccessCloseBtn.addEventListener('click', function () {
                checkoutSuccessModal.style.display = 'none';
                window.location.reload(); // Optional: Refresh page for clean slate
            });
        }
    });
 