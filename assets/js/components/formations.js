// صفحة التكوينات - محدثة
document.addEventListener('DOMContentLoaded', function() {
    loadFormations();
    setupSearch();
});

async function loadFormations(filters = {}) {
    showLoading(true);
    
    try {
        // بناء URL مع الفلاتر
        let url = 'get_formations.php';
        const params = new URLSearchParams();
        
        if (filters.category) params.append('category', filters.category);
        if (filters.level) params.append('level', filters.level);
        if (filters.price) params.append('price', filters.price);
        
        if (params.toString()) {
            url += '?' + params.toString();
        }
        
        const result = await fetchAPI(url);
        
        if (result.success) {
            displayFormations(result.data);
        } else {
            showAlert(result.message, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('خطأ في تحميل التكوينات', 'error');
    } finally {
        showLoading(false);
    }
}

function displayFormations(formations) {
    const container = document.getElementById('formations-container');
    
    if (!container) {
        console.error('Container #formations-container not found');
        return;
    }
    
    if (formations.length === 0) {
        container.innerHTML = '<p class="no-data">لا توجد تكوينات متاحة بهذه المعايير</p>';
        return;
    }
    
    container.innerHTML = formations.map(formation => `
        <div class="formation-card" data-id="${formation.id}">
            <div class="formation-image">
                <img src="../${formation.image || 'assets/images/courses/default.png'}" 
                     alt="${formation.titre}"
                     onerror="this.src='../assets/images/courses/course01.png'">
                <span class="level-badge ${formation.niveau}">${formation.niveau}</span>
            </div>
            
            <div class="formation-content">
                <span class="category">${formation.categorie_id || 'Formation'}</span>
                <h3>${formation.titre}</h3>
                <p class="description">${(formation.description || '').substring(0, 100)}...</p>
                
                <div class="formation-meta">
                    <div class="meta-item">
                        <i class="fa fa-clock"></i>
                        <span>${formation.duree || '0'}h</span>
                    </div>
                    <div class="meta-item">
                        <i class="fa fa-user"></i>
                        <span>${formation.instructor_name || 'Formateur'}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fa fa-users"></i>
                        <span>${formation.places_disponibles || 0} places</span>
                    </div>
                </div>
                
                <div class="formation-footer">
                    <span class="price">${formatPrice(formation.prix)}</span>
                    <div class="actions">
                        <button class="btn-details" onclick="viewDetails(${formation.id})">
                            Détails
                        </button>
                        <button class="btn-add-cart" onclick="addToCart(${formation.id})">
                            <i class="fa fa-cart-plus"></i> Panier
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function setupSearch() {
    const searchInput = document.getElementById('search-input');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(async function() {
            const searchTerm = this.value.trim();
            
            if (searchTerm.length < 2) {
                loadFormations();
                return;
            }
            
            showLoading(true);
            try {
                const result = await fetchAPI(`search_formations.php?q=${encodeURIComponent(searchTerm)}`);
                if (result.success) {
                    displayFormations(result.data);
                }
            } catch (error) {
                showAlert('خطأ في البحث', 'error');
            } finally {
                showLoading(false);
            }
        }, 500));
    }
}

function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func.apply(this, args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

function viewDetails(formationId) {
    window.location.href = `fromation-details.php?id=${formationId}`;
}

async function addToCart(formationId) {
    const result = await fetchAPI('add_to_cart.php', 'POST', { 
        formation_id: formationId 
    });
    
    if (result.success) {
        showAlert('✅ تمت الإضافة إلى السلة', 'success');
        updateCartBadge();
    } else {
        if (result.message.includes('تسجيل الدخول')) {
            if (confirm('يجب تسجيل الدخول أولاً. الانتقال لصفحة تسجيل الدخول؟')) {
                window.location.href = 'login.php';
            }
        } else {
            showAlert(result.message, 'error');
        }
    }
}

async function updateCartBadge() {
    try {
        const result = await fetchAPI('get_cart.php');
        
        if (result.success && result.data.count > 0) {
            const badge = document.querySelector('.cart-badge');
            if (badge) {
                badge.textContent = result.data.count;
                badge.style.display = 'inline-block';
                badge.style.cssText += `
                    background: #f44336;
                    color: white;
                    padding: 2px 8px;
                    border-radius: 10px;
                    font-size: 11px;
                    margin-left: 5px;
                `;
            }
        }
    } catch (error) {
        console.log('Could not update cart badge');
    }
}