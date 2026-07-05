// Base URL for API
const API_URL = 'http://localhost:3000/api';

// ========== AUTHENTICATION ==========

function setToken(token, user) {
    localStorage.setItem('token', token);
    localStorage.setItem('user', JSON.stringify(user));
}

function getToken() {
    return localStorage.getItem('token');
}

function getUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
}

function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    window.location.reload();
}

function updateAuthUI() {
    const user = getUser();
    const loginBtn = document.getElementById('login-btn');
    const logoutBtn = document.getElementById('logout-btn');
    const greeting = document.getElementById('user-greeting');

    if (user) {
        if(loginBtn) loginBtn.style.display = 'none';
        if(logoutBtn) logoutBtn.style.display = 'inline-block';
        if(greeting) {
            greeting.style.display = 'inline-block';
            greeting.innerText = `Hello, ${user.name}`;
        }
    } else {
        if(loginBtn) loginBtn.style.display = 'inline-block';
        if(logoutBtn) logoutBtn.style.display = 'none';
        if(greeting) greeting.style.display = 'none';
    }
}

// ========== PRODUCTS ==========

async function loadTrendingProducts() {
    const container = document.getElementById('trending-container');
    if (!container) return; // Only run on home page

    try {
        const res = await fetch(`${API_URL}/products/trending`);
        const products = await res.json();

        container.innerHTML = products.map(p => `
            <div class="product-card">
                <img src="${p.image}" alt="${p.name}" onerror="this.src='https://via.placeholder.com/250x250?text=Perfume'">
                <h3 class="product-name">${p.name}</h3>
                <p class="product-price">$${p.price.toFixed(2)}</p>
                <button class="btn btn-primary" onclick="addToCart(${p.id}, '${p.name}', ${p.price}, '${p.image}')">Add to Cart</button>
            </div>
        `).join('');
    } catch (err) {
        console.error("Failed to load trending items", err);
    }
}

async function loadAllProducts(category = 'All', search = '') {
    const container = document.getElementById('products-container');
    if (!container) return;

    try {
        let url = `${API_URL}/products?`;
        if (category && category !== 'All') url += `category=${encodeURIComponent(category)}&`;
        if (search) url += `search=${encodeURIComponent(search)}`;

        const res = await fetch(url);
        const products = await res.json();

        if(products.length === 0) {
            container.innerHTML = `<p>No products found.</p>`;
            return;
        }

        container.innerHTML = products.map(p => `
            <div class="product-card">
                <img src="${p.image}" alt="${p.name}" onerror="this.src='https://via.placeholder.com/250x250?text=Perfume'">
                <h3 class="product-name">${p.name}</h3>
                <p class="product-price">$${p.price.toFixed(2)}</p>
                <button class="btn btn-primary" onclick="addToCart(${p.id}, '${p.name}', ${p.price}, '${p.image}')">Add to Cart</button>
            </div>
        `).join('');
    } catch (err) {
        console.error("Failed to load products", err);
    }
}

// ========== CART ==========

function getCart() {
    const cart = localStorage.getItem('cart');
    return cart ? JSON.parse(cart) : [];
}

function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
}

function updateCartCount() {
    const cartCountEl = document.getElementById('cart-count');
    if (cartCountEl) {
        const cart = getCart();
        const count = cart.reduce((acc, item) => acc + item.quantity, 0);
        cartCountEl.innerText = count;
    }
}

function addToCart(id, name, price, image) {
    const cart = getCart();
    const existing = cart.find(i => i.id === id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ id, name, price, image, quantity: 1 });
    }
    saveCart(cart);
    alert(`${name} added to cart!`);
}

function removeFromCart(id) {
    let cart = getCart();
    cart = cart.filter(i => i.id !== id);
    saveCart(cart);
    renderCart(); // Call if on cart page
}

function updateQuantity(id, change) {
    const cart = getCart();
    const index = cart.findIndex(i => i.id === id);
    if(index !== -1) {
        cart[index].quantity += change;
        if(cart[index].quantity <= 0) {
            cart.splice(index, 1);
        }
        saveCart(cart);
        renderCart();
    }
}

function renderCart() {
    const container = document.getElementById('cart-items');
    const totalEl = document.getElementById('cart-total-price');
    if (!container) return;

    const cart = getCart();
    if(cart.length === 0) {
        container.innerHTML = "<p>Your cart is empty.</p>";
        totalEl.innerText = "0.00";
        return;
    }

    let total = 0;
    container.innerHTML = cart.map(item => {
        const itemTotal = item.price * item.quantity;
        total += itemTotal;
        return `
            <div class="cart-item">
                <div style="display:flex; align-items:center; gap: 1rem;">
                    <img src="${item.image}" alt="${item.name}" width="50" onerror="this.src='https://via.placeholder.com/50'">
                    <h4>${item.name}</h4>
                </div>
                <div>
                    <button class="btn btn-outline" style="padding: 0.2rem 0.5rem;" onclick="updateQuantity(${item.id}, -1)">-</button>
                    ${item.quantity}
                    <button class="btn btn-outline" style="padding: 0.2rem 0.5rem;" onclick="updateQuantity(${item.id}, 1)">+</button>
                </div>
                <div>$${itemTotal.toFixed(2)}</div>
                <button class="btn btn-primary" onclick="removeFromCart(${item.id})">Remove</button>
            </div>
        `;
    }).join('');

    totalEl.innerText = total.toFixed(2);
}

// ========== CHECKOUT ==========
async function placeOrder() {
    const user = getUser();
    if(!user) {
        alert("Please login first to place an order.");
        window.location.href = "login.html";
        return;
    }

    const cart = getCart();
    if(cart.length === 0) {
        alert("Cart is empty.");
        return;
    }

    const total = cart.reduce((acc, item) => acc + (item.price * item.quantity), 0);
    const items = cart.map(i => ({ id: i.id, quantity: i.quantity }));

    try {
        const res = await fetch(`${API_URL}/orders`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${getToken()}`
            },
            body: JSON.stringify({ total, items })
        });
        
        if(res.ok) {
            alert("Order placed successfully!");
            localStorage.removeItem('cart');
            window.location.href = "index.html";
        } else {
            const data = await res.json();
            alert("Error: " + data.error);
        }
    } catch(e) {
        console.error("Order failed", e);
    }
}
