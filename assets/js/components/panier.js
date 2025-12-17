// صفحة السلة
document.addEventListener('DOMContentLoaded', function() {
    loadCart();
});

async function loadCart() {
    showLoading(true);
    
    try {
        const result = await fetchAPI('get_cart.php');
        
        if (result.success) {
            displayCart(result.data);
        } else {
            showLoginRequired();
        }
    } catch (error) {
        showAlert('خطأ في تحميل السلة', 'error');
    } finally {
        showLoading(false);
    }
}

function displayCart(cartData) {
    const container = document.getElementById('cart-items');
    const emptyMsg = document.getElementById('empty-cart');
    const summary = document.getElementById('cart-summary');
    
    if (!container) return;
    
    if (cartData.items.length === 0) {
        if (emptyMsg) emptyMsg.style.display = 'block';
        if (summary) summary.style.display = 'none';
        container.innerHTML = '';
        return;
    }
    
    if (emptyMsg) emptyMsg.style.display = 'none';
    if (summary) summary.style.display = 'block';
    
    container.innerHTML = cartData.items.map(item => `
        <div class="cart-item" data-cart-id="${item.id}">
            <img src="${item.image || '../assets/images/courses/default.png'}" 
                 alt="${item.titre}">
            
            <div class="item-details">
                <h3>${item.titre}</h3>
                <p class="instructor">${item.instructor_name}</p>
                <p class="duration">${item.duree}h</p>
            </div>
            
            <div class="item-price">
                <span>${formatPrice(item.prix)}</span>
            </div>
            
            <div class="item-actions">
                <button class="btn-remove" onclick="removeFromCart(${item.id})">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
    
    updateCartSummary(cartData.total, cartData.count);
}

function updateCartSummary(total, count) {
    const subtotal = document.getElementById('subtotal');
    const totalEl = document.getElementById('total');
    const itemCount = document.getElementById('item-count');
    
    if (subtotal) subtotal.textContent = formatPrice(total);
    if (totalEl) totalEl.textContent = formatPrice(total);
    if (itemCount) itemCount.textContent = `${count} formation(s)`;
}

async function removeFromCart(cartId) {
    if (!confirm('هل تريد حذف هذا التكوين من السلة؟')) return;
    
    const result = await fetchAPI('remove_from_cart.php', 'POST', { 
        cart_id: cartId 
    });
    
    if (result.success) {
        showAlert('تم الحذف', 'success');
        loadCart();
    } else {
        showAlert(result.message, 'error');
    }
}

function proceedToCheckout() {
    window.location.href = 'paiement.php';
}

function showLoginRequired() {
    const container = document.getElementById('cart-items');
    if (container) {
        container.innerHTML = `
            <div class="login-required">
                <i class="fa fa-lock fa-3x"></i>
                <h2>يجب تسجيل الدخول</h2>
                <p>يرجى تسجيل الدخول لعرض سلة التسوق</p>
                <a href="login.php" class="btn-primary">تسجيل الدخول</a>
            </div>
        `;
    }
}