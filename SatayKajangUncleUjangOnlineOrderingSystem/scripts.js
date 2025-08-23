let currentItem = { type: '', name: '', price: 0, image: '', description: '' };
let order = {
    chicken: { 
        quantity: 0, 
        name: 'Satay Ayam', 
        nameEnglish: 'Chicken Satay', 
        price: 1.40, 
        image: 'image/satay ayam.png', 
        description: 'Satay Ayam, bersama kuah kacang dan sayur timun serta bawang',
        descriptionEnglish: 'Juicy chicken marinated in our secret family recipe' 
    },
    beef: { 
        quantity: 0, 
        name: 'Satay Daging', 
        nameEnglish: 'Beef Satay', 
        price: 1.40, 
        image: 'image/satay daging.jpg', 
        description: 'Satay Daging lembu yang lembut dengan campuran rempah yang sempurna, bersama kuah kacang dan sayur timun serta bawang',
        descriptionEnglish: 'Tender beef with the perfect blend of spices' 
    },
    tripe: { 
        quantity: 0, 
        name: 'Satay Perut', 
        nameEnglish: 'Tripe Satay', 
        price: 1.60, 
        image: 'image/satay perut.jpg', 
        description: 'Sate perut yang unik dan penuh rasa, bersama kuah kacang dan sayur timun serta bawang',
        descriptionEnglish: 'Unique and flavorful tripe skewers' 
    },
    mutton: { 
        quantity: 0, 
        name: 'Satay Kambing', 
        nameEnglish: 'Mutton Satay', 
        price: 2.00, 
        image: 'image/Satay kambing.jpg', 
        description: 'Sate Kambing premium dengan herba aromatik, bersama kuah kacang dan sayur timun serta bawang',
        descriptionEnglish: 'Premium mutton with aromatic herbs' 
    },
    'nasi-impit': { 
        quantity: 0, 
        name: 'Nasi Impit', 
        nameEnglish: 'Nasi Impit', 
        price: 3.00, 
        image: 'image/Nasi Impit.jpg', 
        description: 'Nasi impit dengan kepadatan teksturnya',
        descriptionEnglish: 'Traditional accompaniment to satay' 
    },
    'peanut-sauce': { 
        quantity: 0, 
        name: 'Kuah Kacang', 
        nameEnglish: 'Peanut Sauce', 
        price: 2.99, 
        image: 'image/Kuah kacang.jpg', 
        description: 'Kuah Kacang yang Kaya dengan rempah ratus Tradisional',
        descriptionEnglish: 'Our signature sauce' 
    },
    'family-starter': { 
        quantity: 0, 
        name: 'Pembuka Selera', 
        nameEnglish: 'Family Starter Combo', 
        price: 37.99, 
        image: 'image/familycombo.jpg', 
        description: '20 pieces (5 Ayam, 5 Daging, 5 Perut, 5 Kambing), 3 Nasi Impit, Kuah Kacang',
        descriptionEnglish: '20 pieces (5 Chicken, 5 Beef, 5 Tripe, 5 Mutton), 2 Nasi Impit, 1 Peanut Sauce' 
    },
    'mixed-delight': { 
        quantity: 0, 
        name: 'Santapan Mewah', 
        nameEnglish: 'Mixed Delight Combo', 
        price: 58.49, 
        image: 'image/dekuxe.jpg', 
        description: '30 pieces (10 Ayam, 10 Daging, 10 Perut, 5 Kambing), 6 Nasi Impit, Kuah Kacang',
        descriptionEnglish: '30 pieces (10 Chicken, 10 Beef, 10 Tripe), 3 Nasi Impit, 2 Peanut Sauce, 1 Cucumber Salad' 
    },
    'ultimate-feast': { 
        quantity: 0, 
        name: 'Jamuan Bersama', 
        nameEnglish: 'Ultimate Feast Combo', 
        price: 98.47, 
        image: 'image/jumbosatay.jpg', 
        description: '50 pieces (20 Ayam, 20 Daging, 10 Perut, 10 Kambing), 10 Nasi Impit, Kuah Kacang',
        descriptionEnglish: '50 pieces (15 Chicken, 15 Beef, 10 Tripe, 10 Mutton), 5 Nasi Impit, 3 Peanut Sauce, 2 Cucumber Salad' 
    }
};

let longPressInterval = null;
const LONG_PRESS_DELAY = 500; // Delay before long press starts
const LONG_PRESS_INTERVAL = 100; // Interval for continuous increment/decrement

function openOrderModal(itemName, itemType, price, image, description = '') {
    console.log(`Opening modal for ${itemName} (${itemType})`);
    try {
        currentItem = { type: itemType, name: itemName, price: price, image: image, description: description || 'Tiada penerangan tersedia' };
        const modalTitle = document.getElementById('modal-title');
        const modalImage = document.getElementById('modal-image');
        const modalDescription = document.getElementById('modal-description');
        const modalPrice = document.getElementById('modal-price');
        const modalQuantity = document.getElementById('modal-quantity');
        const orderModal = document.getElementById('order-modal');

        if (!modalTitle || !modalImage || !modalDescription || !modalPrice || !modalQuantity || !orderModal) {
            console.error('One or more modal elements not found:', {
                modalTitle: !!modalTitle,
                modalImage: !!modalImage,
                modalDescription: !!modalDescription,
                modalPrice: !!modalPrice,
                modalQuantity: !!modalQuantity,
                orderModal: !!orderModal
            });
            return;
        }

        modalTitle.textContent = `Pesan ${itemName}`;
        modalImage.src = image;
        modalImage.alt = itemName;
        modalDescription.textContent = description;
        modalPrice.textContent = `Harga: RM${price.toFixed(2)}`;
        modalQuantity.value = order[itemType].quantity || 0;
        orderModal.style.display = 'block';
        console.log('Modal should now be visible');

        // Add event listener for direct quantity input
        modalQuantity.addEventListener('input', handleQuantityInput);
    } catch (error) {
        console.error('Error in openOrderModal:', error);
    }
}

function handleQuantityInput(event) {
    let quantity = parseInt(event.target.value) || 0;
    quantity = Math.max(0, quantity); // Ensure non-negative
    event.target.value = quantity;
    console.log(`Direct input quantity for ${currentItem.name} to ${quantity}`);
}

function closeOrderModal() {
    const orderModal = document.getElementById('order-modal');
    const modalQuantity = document.getElementById('modal-quantity');
    if (orderModal) {
        orderModal.style.display = 'none';
        // Remove event listener to prevent memory leaks
        modalQuantity.removeEventListener('input', handleQuantityInput);
        console.log('Modal closed');
    } else {
        console.error('Order modal not found');
    }
}

function adjustQuantity(change) {
    let quantityInput = document.getElementById('modal-quantity');
    let quantity = parseInt(quantityInput.value) || 0;
    quantity = Math.max(0, quantity + change);
    quantityInput.value = quantity;
    console.log(`Adjusted quantity for ${currentItem.name} to ${quantity}`);
}

function startAdjustQuantity(change) {
    adjustQuantity(change); // Immediate change on click
    longPressInterval = setTimeout(() => {
        longPressInterval = setInterval(() => {
            adjustQuantity(change);
        }, LONG_PRESS_INTERVAL);
    }, LONG_PRESS_DELAY);
}

function stopAdjustQuantity() {
    if (longPressInterval) {
        clearInterval(longPressInterval);
        clearTimeout(longPressInterval);
        longPressInterval = null;
    }
}

function addToOrder() {
    const quantity = parseInt(document.getElementById('modal-quantity').value) || 0;
    console.log(`Adding to order: ${currentItem.name}, Quantity: ${quantity}`);
    if (quantity >= 0 || currentItem.type in ['family-starter', 'mixed-delight', 'ultimate-feast']) {
        order[currentItem.type].quantity = quantity;
        animateToCart(currentItem.type);
    } else {
        console.log(`No quantity added for ${currentItem.name}, skipping.`);
    }
    closeOrderModal();
}

function animateToCart(itemType) {
    const item = document.querySelector(`.menu-item[onclick*="${itemType}"] .menu-image`);
    const cart = document.querySelector('.cart-icon');
    if (item && cart) {
        const itemRect = item.getBoundingClientRect();
        const cartRect = cart.getBoundingClientRect();

        const clone = item.cloneNode();
        clone.style.position = 'absolute';
        clone.style.width = itemRect.width + 'px';
        clone.style.height = itemRect.height + 'px';
        clone.style.top = itemRect.top + window.scrollY + 'px';
        clone.style.left = itemRect.left + window.scrollX + 'px';
        clone.style.zIndex = 1000;
        clone.style.transition = 'all 0.8s ease-out';
        document.body.appendChild(clone);

        requestAnimationFrame(() => {
            clone.style.top = (cartRect.top + window.scrollY) + 'px';
            clone.style.left = (cartRect.left + window.scrollX) + 'px';
            clone.style.width = '0px';
            clone.style.height = '0px';
            clone.style.opacity = '0';
        });

        clone.addEventListener('transitionend', () => {
            clone.remove();
            updateCart();
        });
    } else {
        console.log(`Animation failed: Item or cart not found for ${itemType}`);
        updateCart();
    }
}

function updateCart() {
    const total = Object.values(order).reduce((sum, item) => sum + item.quantity, 0);
    const cartCount = document.getElementById('cart-count');
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');

    cartCount.textContent = total;
    cartItems.innerHTML = '';
    let totalPrice = 0;
    for (let item of Object.values(order)) {
        if (item.quantity > 0) {
            const li = document.createElement('li');
            li.className = 'cart-item';
            li.innerHTML = `
                <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                <span>${item.name}: ${item.quantity} x RM${item.price.toFixed(2)} = RM${(item.quantity * item.price).toFixed(2)}</span>
                <button onclick="removeItem('${Object.keys(order).find(key => order[key] === item)}')">-</button>
            `;
            cartItems.appendChild(li);
            totalPrice += item.quantity * item.price;
        }
    }
    cartTotal.textContent = totalPrice.toFixed(2);
    console.log(`Cart updated: Total items: ${total}, Total price: RM${totalPrice.toFixed(2)}`);
}

function removeItem(itemType) {
    order[itemType].quantity = 0;
    console.log(`Removed ${itemType} from cart`);
    updateCart();
}

function toggleCartModal() {
    const cartModal = document.getElementById('cart-modal');
    if (cartModal.style.display === 'block') {
        closeCartModal();
    } else {
        updateCart();
        cartModal.style.display = 'block';
    }
}

function closeCartModal() {
    document.getElementById('cart-modal').style.display = 'none';
}

function checkout() {
    const total = Object.values(order).reduce((sum, item) => sum + item.quantity, 0);
    const hasCombo = Object.values(order).some(item => ['family-starter', 'mixed-delight', 'ultimate-feast'].includes(Object.keys(order).find(key => order[key] === item)) && item.quantity > 0);

    if (total < 10 && !hasCombo) {
        // Ensure the CSS file is loaded
        if (!document.querySelector('link[href="minimum-modal.css"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'minimum-modal.css';
            document.head.appendChild(link);
        }

        // Create and show custom centered modal
        let modal = document.createElement('div');
        modal.id = 'custom-minimum-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-overlay"></div>
                <h2>Peringatan Pesanan!</h2>
                <p>Tidak dapat membuat pesanan: Pesanan minimum adalah 10 batang atau satu set kombo.</p>
                <button onclick="document.getElementById('custom-minimum-modal').remove()">OK</button>
            </div>
        `;
        document.body.appendChild(modal);
        console.log(`Minimum order warning shown: Total items (${total}) less than 10, no combo set included.`);
    } else {
        const totalPrice = Object.values(order).reduce((sum, item) => sum + (item.quantity * item.price), 0);
        
        // Construct WhatsApp message
        let message = `*Pesanan Baru dari Laman Web Satay Kajang Uncle Ujang*\n\n*Order Details:*\n`;
        for (let item of Object.values(order)) {
            if (item.quantity > 0) {
                message += `- ${item.name}: ${item.quantity} x RM${item.price.toFixed(2)} = RM${(item.quantity * item.price).toFixed(2)}\n`;
            }
        }
        message += `\n*Total Item:* ${total}\n*Total Harga:* RM${totalPrice.toFixed(2)}\n`;
        message += `\nIni sahaja pesanan saya, jumpa di gerai nanti!!. Terima kasih!`;

        // Encode message for URL
        const encodedMessage = encodeURIComponent(message);
        const whatsappNumber = '+601162226128'; // Your number in international format
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;

        // Open WhatsApp
        window.open(whatsappUrl, '_blank');
        console.log(`Order sent to WhatsApp: Total items: ${total}, Total price: RM${totalPrice.toFixed(2)}`);

        // Reset order
        order = {
            chicken: { 
                quantity: 0, 
                name: 'Satay Ayam', 
                nameEnglish: 'Chicken Satay', 
                price: 1.40, 
                image: 'image/satay ayam.png', 
                description: 'Satay Ayam, bersama kuah kacang dan sayur timun serta bawang',
                descriptionEnglish: 'Juicy chicken marinated in our secret family recipe' 
            },
            beef: { 
                quantity: 0, 
                name: 'Satay Daging', 
                nameEnglish: 'Beef Satay', 
                price: 1.40, 
                image: 'image/satay daging.jpg', 
                description: 'Satay Daging lembu yang lembut dengan campuran rempah yang sempurna, bersama kuah kacang dan sayur timun serta bawang',
                descriptionEnglish: 'Tender beef with the perfect blend of spices' 
            },
            tripe: { 
                quantity: 0, 
                name: 'Satay Perut', 
                nameEnglish: 'Tripe Satay', 
                price: 1.60, 
                image: 'image/satay perut.jpg', 
                description: 'Sate perut yang unik dan penuh rasa, bersama kuah kacang dan sayur timun serta bawang',
                descriptionEnglish: 'Unique and flavorful tripe skewers' 
            },
            mutton: { 
                quantity: 0, 
                name: 'Satay Kambing', 
                nameEnglish: 'Mutton Satay', 
                price: 2.00, 
                image: 'image/Satay kambing.jpg', 
                description: 'Sate Kambing premium dengan herba aromatik, bersama kuah kacang dan sayur timun serta bawang',
                descriptionEnglish: 'Premium mutton with aromatic herbs' 
            },
            'nasi-impit': { 
                quantity: 0, 
                name: 'Nasi Impit', 
                nameEnglish: 'Nasi Impit', 
                price: 3.00, 
                image: 'image/Nasi Impit.jpg', 
                description: 'Nasi impit dengan kepadatan teksturnya',
                descriptionEnglish: 'Traditional accompaniment to satay' 
            },
            'peanut-sauce': { 
                quantity: 0, 
                name: 'Kuah Kacang', 
                nameEnglish: 'Peanut Sauce', 
                price: 2.99, 
                image: 'image/Kuah kacang.jpg', 
                description: 'Kuah Kacang yang Kaya dengan rempah ratus Tradisional',
                descriptionEnglish: 'Our signature sauce' 
            },
            'family-starter': { 
                quantity: 0, 
                name: 'Pembuka Selera', 
                nameEnglish: 'Family Starter Combo', 
                price: 37.99, 
                image: 'image/familycombo.jpg', 
                description: '20 pieces (5 Ayam, 5 Daging, 5 Perut, 5 Kambing), 3 Nasi Impit, Kuah Kacang',
                descriptionEnglish: '20 pieces (5 Chicken, 5 Beef, 5 Tripe, 5 Mutton), 2 Nasi Impit, 1 Peanut Sauce' 
            },
            'mixed-delight': { 
                quantity: 0, 
                name: 'Santapan Mewah', 
                nameEnglish: 'Mixed Delight Combo', 
                price: 58.49, 
                image: 'image/dekuxe.jpg', 
                description: '30 pieces (10 Ayam, 10 Daging, 10 Perut, 5 Kambing), 6 Nasi Impit, Kuah Kacang',
                descriptionEnglish: '30 pieces (10 Chicken, 10 Beef, 10 Tripe), 3 Nasi Impit, 2 Peanut Sauce, 1 Cucumber Salad' 
            },
            'ultimate-feast': { 
                quantity: 0, 
                name: 'Jamuan Bersama', 
                nameEnglish: 'Ultimate Feast Combo', 
                price: 98.47, 
                image: 'image/jumbosatay.jpg', 
                description: '50 pieces (20 Ayam, 20 Daging, 10 Perut, 10 Kambing), 10 Nasi Impit, Kuah Kacang',
                descriptionEnglish: '50 pieces (15 Chicken, 15 Beef, 10 Tripe, 10 Mutton), 5 Nasi Impit, 3 Peanut Sauce, 2 Cucumber Salad' 
            }
        };
        closeCartModal();
        updateCart();
    }
}

// Smooth scrolling for the "Lihat Garis Masa Kami" button
document.querySelector('.scroll-to-timeline a')?.addEventListener('click', function(e) {
    e.preventDefault();
    document.querySelector('#timeline-section')?.scrollIntoView({ behavior: 'smooth' });
});

// Function to check if an element is in the viewport
function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.top <= (window.innerHeight || document.documentElement.clientHeight)
    );
}

// Add animation class to timeline items when they come into view
function animateTimelineItems() {
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach(item => {
        if (isElementInViewport(item) && !item.classList.contains('swipe-in')) {
            item.classList.add('swipe-in');
        }
    });
}

// Run animation check on scroll and load
window.addEventListener('scroll', animateTimelineItems);
window.addEventListener('load', animateTimelineItems);

document.getElementById('contactForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const message = document.getElementById('message').value.trim();
    let isValid = true;

    // Reset errors
    document.getElementById('nameError').textContent = '';
    document.getElementById('emailError').textContent = '';
    document.getElementById('messageError').textContent = '';

    if (!name) {
        document.getElementById('nameError').textContent = 'Sila masukkan nama anda.';
        isValid = false;
    }
    if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
        document.getElementById('emailError').textContent = 'Sila masukkan e-mel yang sah.';
        isValid = false;
    }
    if (!message) {
        document.getElementById('messageError').textContent = 'Sila masukkan mesej.';
        isValid = false;
    }

    if (isValid) {
        // Ensure the CSS file is loaded
        if (!document.querySelector('link[href="minimum-modal.css"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'minimum-modal.css';
            document.head.appendChild(link);
        }

        // Construct WhatsApp message
        let messageText = `*Pesanan Baru dari Laman Web Satay Kajang Uncle Ujang*\n\n*Message Details:*\n`;
        messageText += `- Nama: ${name}\n`;
        messageText += `- E-mel: ${email}\n`;
        messageText += `- Mesej: ${message}\n`;
        messageText += `\nTerima kasih!`;

        // Encode message for URL
        const encodedMessage = encodeURIComponent(messageText);
        const whatsappNumber = '+601162226128'; // Same number as order checkout
        const whatsappUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;

        // Open WhatsApp
        window.open(whatsappUrl, '_blank');
        console.log(`Contact message sent to WhatsApp for ${name}`);

        // Create and show custom success modal
        let modal = document.createElement('div');
        modal.id = 'custom-success-modal';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-overlay"></div>
                <h2>Terima Kasih!</h2>
                <p>Mesej anda telah dihantar dengan jayanya. Kami akan menghubungi anda tidak lama lagi.</p>
                <button onclick="document.getElementById('custom-success-modal').remove()">OK</button>
            </div>
        `;
        document.body.appendChild(modal);
        console.log('Contact form submitted successfully, success modal shown.');
        this.reset();
    }
});