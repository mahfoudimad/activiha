// Check authentication
const token = localStorage.getItem('admin_token');
if (!token) {
    window.location.href = 'index.php';
}

document.addEventListener('DOMContentLoaded', () => {
    loadOrders();
    loadProducts();
    loadOrders();
    loadProducts();
    loadPages();
    loadSettings();
    loadStats();

    // Add filter listeners
    ['filter-from', 'filter-to', 'filter-status', 'filter-search'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            const event = id === 'filter-search' ? 'input' : 'change';
            el.addEventListener(event, loadOrders);
        }
    });
});

function showTab(tab) {
    document.getElementById('orders-tab').style.display = tab === 'orders' ? 'block' : 'none';
    document.getElementById('products-tab').style.display = tab === 'products' ? 'block' : 'none';
    document.getElementById('abandoned-tab').style.display = tab === 'abandoned' ? 'block' : 'none';
    document.getElementById('pages-tab').style.display = tab === 'pages' ? 'block' : 'none';
    document.getElementById('marketing-tab').style.display = tab === 'marketing' ? 'block' : 'none';
    document.getElementById('settings-tab').style.display = tab === 'settings' ? 'block' : 'none';

    document.querySelectorAll('.sidebar-nav a').forEach(a => a.classList.remove('active'));
    document.getElementById(`nav-${tab}`).classList.add('active');
}

// Update clock
function updateClock() {
    const now = new Date();
    document.getElementById('current-time').innerText = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', second: '2-digit', hour12: true });
}
setInterval(updateClock, 1000);
updateClock();

// Order Management
async function loadOrders() {
    try {
        const res = await fetch('/api/orders', {
            headers: { 'Authorization': `Bearer ${token}` }
        });
        const allOrders = await res.json();

        // Cache for edit function
        localStorage.setItem('orders_cache', JSON.stringify(allOrders));

        // Apply Filters
        let filteredOrders = [...allOrders];
        const fromDate = document.getElementById('filter-from')?.value;
        const toDate = document.getElementById('filter-to')?.value;
        const status = document.getElementById('filter-status')?.value;
        const search = document.getElementById('filter-search')?.value.toLowerCase();

        if (fromDate) {
            filteredOrders = filteredOrders.filter(o => new Date(o.createdAt) >= new Date(fromDate));
        }
        if (toDate) {
            const upTo = new Date(toDate);
            upTo.setHours(23, 59, 59, 999);
            filteredOrders = filteredOrders.filter(o => new Date(o.createdAt) <= upTo);
        }
        if (status && status !== 'all') {
            filteredOrders = filteredOrders.filter(o => o.status === status);
        }
        if (search) {
            filteredOrders = filteredOrders.filter(o =>
                o.customer.fullName.toLowerCase().includes(search) ||
                o.customer.phone.includes(search) ||
                o.id.toString().includes(search)
            );
        }

        // Update stats based on filtered orders (excluding abandoned from revenue/success unless filtered)
        document.getElementById('stat-total-orders').innerText = filteredOrders.length;

        const revOrders = filteredOrders.filter(o => o.status !== 'abandoned');
        const totalRev = revOrders.reduce((sum, o) => sum + o.totalPrice, 0);
        document.getElementById('stat-total-revenue').innerText = totalRev.toLocaleString();

        const confirmed = filteredOrders.filter(o => o.status === 'confirmed' || o.status === 'delivered').length;
        document.getElementById('stat-confirmed-orders').innerText = confirmed;

        const canceled = filteredOrders.filter(o => o.status === 'canceled').length;
        document.getElementById('stat-canceled-orders').innerText = canceled;

        const successRate = revOrders.length > 0 ? ((confirmed / revOrders.length) * 100).toFixed(1) : 0;
        document.getElementById('stat-success-rate').innerText = `${successRate}%`;


        const tbody = document.getElementById('orders-table-body');
        tbody.innerHTML = filteredOrders.map(order => `
            <tr>
                <td style="font-size:0.75rem; font-family:monospace; color:#64748b;">#${order.id.toString().slice(-6)}</td>
                <td style="font-weight:600;">${order.customer.fullName}</td>
                <td>
                    <div style="font-size:0.875rem;">${order.customer.phone}</div>
                    <div style="font-size:0.75rem; color:#6b7280;">${order.customer.city}, ${order.customer.address}</div>
                </td>
                <td>${order.product.title} (x${order.quantity})</td>
                <td style="font-weight:600;">${order.totalPrice} DZD</td>
                <td><span class="status-badge status-${order.status}">${order.status}</span></td>
                <td style="font-size:0.75rem;">
                    ${new Date(order.createdAt).toLocaleDateString()}
                    <div style="color: #64748b;">${new Date(order.createdAt).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</div>
                </td>
                <td>
                    <select onchange="updateOrderStatus('${order.id}', this.value)" style="padding:0.25rem; font-size:0.75rem;">
                        <option value="pending" ${order.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="confirmed" ${order.status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                        <option value="delivered" ${order.status === 'delivered' ? 'selected' : ''}>Delivered</option>
                        <option value="canceled" ${order.status === 'canceled' ? 'selected' : ''}>Canceled</option>
                        <option value="abandoned" ${order.status === 'abandoned' ? 'selected' : ''}>Abandoned</option>
                    </select>
                    <button class="btn btn-sm" onclick="editOrder('${order.id}')" style="margin-left:0.5rem;" title="Edit"><i class="fas fa-edit"></i></button>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        console.error('Error loading orders:', err);
    }
}

function exportToCSV() {
    const orders = JSON.parse(localStorage.getItem('orders_cache') || '[]');
    if (orders.length === 0) {
        alert('No orders to export');
        return;
    }

    const headers = ['Order ID', 'Customer Name', 'Phone', 'City', 'Address', 'Product', 'Quantity', 'Total Price', 'Status', 'Date', 'Time'];
    const rows = orders.map(order => {
        const date = new Date(order.createdAt);
        return [
            order.id,
            `"${order.customer.fullName}"`,
            order.customer.phone,
            `"${order.customer.city}"`,
            `"${order.customer.address}"`,
            `"${order.product.title}"`,
            order.quantity,
            order.totalPrice,
            order.status,
            date.toLocaleDateString(),
            date.toLocaleTimeString()
        ];
    });

    let csvContent = "data:text/csv;charset=utf-8," + headers.join(",") + "\n" + rows.map(e => e.join(",")).join("\n");
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `orders_export_${new Date().toISOString().split('T')[0]}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

async function updateOrderStatus(id, status) {
    try {
        const res = await fetch(`/api/orders/${id}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify({ status })
        });
        if (res.ok) loadOrders();
    } catch (err) {
        alert('Failed to update status');
    }
}

async function loadStats() {
    try {
        const res = await fetch('/api/stats.php');
        const stats = await res.json();
        document.getElementById('stat-page-views').innerText = stats.pageViews || 0;
    } catch (err) {
        console.error('Error loading stats:', err);
    }
}

// Abandoned Carts Management merged into Orders

// Product Management
async function loadProducts() {
    try {
        const res = await fetch('/api/products');
        let products = await res.json();
        const search = document.getElementById('product-search').value.toLowerCase();

        if (search) {
            products = products.filter(p =>
                p.title.toLowerCase().includes(search) ||
                p.category.toLowerCase().includes(search)
            );
        }

        // Cache for edit function
        localStorage.setItem('products_cache', JSON.stringify(products));

        const tbody = document.getElementById('products-table-body');
        tbody.innerHTML = products.map(product => `
            <tr>
                <td><img src="${product.image}" style="width:40px; height:40px; border-radius:4px; object-fit:cover;"></td>
                <td style="font-weight:500;">${product.title}</td>
                <td>${product.category}</td>
                <td>${product.price} DZD</td>
                <td style="font-size:0.75rem;">${product.createdAt ? new Date(product.createdAt).toLocaleDateString() : 'N/A'}</td>
                <td>
                    <button class="btn btn-sm" onclick="window.open('/product.php?id=${product.id}', '_blank')" title="View"><i class="fas fa-external-link-alt"></i></button>
                    <button class="btn btn-sm" onclick="duplicateProduct('${product.id}')" title="Duplicate"><i class="fas fa-copy"></i></button>
                    <button class="btn btn-sm" onclick="editProduct('${product.id}')" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm" onclick="deleteProduct('${product.id}')" style="color:#ef4444;" title="Delete"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        console.error('Error loading products:', err);
    }
}

async function duplicateProduct(id) {
    if (!confirm('Duplicate this product?')) return;
    try {
        const res = await fetch(`/api/products.php?action=duplicate&id=${id}`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.ok) {
            loadProducts();
        } else {
            alert('Failed to duplicate product');
        }
    } catch (err) {
        alert('Error duplicating product');
    }
}

function editProduct(id) {
    const products = JSON.parse(localStorage.getItem('products_cache') || '[]');
    const product = products.find(p => p.id === id);

    if (!product) {
        // Fetch from API if not in cache
        fetch(`/api/products/${id}`)
            .then(res => res.json())
            .then(product => {
                populateProductForm(product);
            })
            .catch(err => alert('Failed to load product details'));
        return;
    }

    populateProductForm(product);
}

function populateProductForm(product) {
    document.getElementById('modal-title').innerText = 'Edit Product';
    document.getElementById('product-id').value = product.id;
    document.querySelector('[name="title"]').value = product.title;
    document.querySelector('[name="price"]').value = product.price;
    document.querySelector('[name="shippingPrice"]').value = product.shippingPrice || 0;
    document.querySelector('[name="category"]').value = product.category;
    document.querySelector('[name="description"]').value = product.description || '';

    // Make image optional for edit
    const imageInput = document.querySelector('[name="image"]');
    imageInput.removeAttribute('required');

    openProductModal();
}

function openProductModal() {
    document.getElementById('product-modal').classList.add('active');
}

function closeProductModal() {
    document.getElementById('product-modal').classList.remove('active');
    document.getElementById('product-form').reset();
    document.getElementById('product-id').value = '';
    document.getElementById('modal-title').innerText = 'Add New Product';
    document.querySelector('[name="image"]').setAttribute('required', 'required');
}

document.getElementById('product-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const productId = document.getElementById('product-id').value;

    try {
        const url = productId ? `/api/products/${productId}` : '/api/products';
        const method = productId ? 'PUT' : 'POST';

        const res = await fetch(url, {
            method,
            body: formData
        });

        if (res.ok) {
            closeProductModal();
            loadProducts();
            e.target.reset();
        } else {
            alert('Failed to save product');
        }
    } catch (err) {
        alert('Error saving product');
    }
});

// Order Edit Functions
function openOrderModal(isEdit = true) {
    const modal = document.getElementById('order-modal');
    const title = document.getElementById('order-modal-title');
    const submitBtn = document.getElementById('order-submit-btn');
    const productSelect = document.getElementById('order-product-select-container');

    // Populate product dropdown
    const products = JSON.parse(localStorage.getItem('products_cache') || '[]');
    products.map(p => `<option value="${p.id}">${p.title} (${p.price} DZD)</option>`).join('');

    if (isEdit) {
        title.innerText = 'Order Details';
        submitBtn.innerText = 'Save Changes';
        productSelect.style.display = 'none'; // Summary card replaces select on edit
    } else {
        document.getElementById('order-form').reset();
        document.getElementById('order-id').value = '';
        document.getElementById('order-items-list').innerHTML = '';
        title.innerText = 'Add Manual Order';
        submitBtn.innerText = 'Create Order';
        productSelect.style.display = 'block';
    }

    modal.classList.add('active');
}

function closeOrderModal() {
    document.getElementById('order-modal').classList.remove('active');
    document.getElementById('order-form').reset();
}

async function editOrder(id) {
    const orders = JSON.parse(localStorage.getItem('orders_cache') || '[]');
    const order = orders.find(o => o.id == id);
    if (!order) return;

    openOrderModal(true);
    document.getElementById('order-id').value = order.id;
    const form = document.getElementById('order-form');
    form.querySelector('[name="fullName"]').value = order.customer.fullName;
    form.querySelector('[name="phone"]').value = order.customer.phone;
    form.querySelector('[name="city"]').value = order.customer.city;
    form.querySelector('[name="address"]').value = order.customer.address || '';

    // Create Summary Cards (Reference Design)
    const summaryContainer = document.getElementById('order-items-list');
    summaryContainer.innerHTML = `
        <div class="summary-card">
            <div>
                <div class="summary-label">${order.product.title} (Quantity: ${order.quantity})</div>
                <div style="font-size: 0.75rem; color: #64748b;">Premium Item</div>
            </div>
            <div class="summary-value">${order.product.price} DZD</div>
        </div>
        <div class="summary-card" style="border-style: dashed; background: #fafafa;">
            <div class="summary-label">Standard Shipping</div>
            <div class="summary-value">0 DZD</div>
        </div>
        <div class="total-section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span style="font-weight: 600; color: #64748b;">Subtotal</span>
                <span>${order.totalPrice} DZD</span>
            </div>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 1.1rem; font-weight: 800; color: #0f172a;">Total Amount</span>
                <span style="font-size: 1.1rem; font-weight: 800; color: #6366f1;">${order.totalPrice} DZD</span>
            </div>
        </div>
    `;
}

document.getElementById('order-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    const orderId = document.getElementById('order-id').value;

    const payload = {
        customer: {
            fullName: data.fullName,
            phone: data.phone,
            city: data.city,
            address: data.address
        }
    };

    if (!orderId) {
        payload.productId = data.productId;
        payload.quantity = 1;
    }

    try {
        const url = orderId ? `/api/orders/${orderId}` : '/api/orders';
        const method = orderId ? 'PATCH' : 'POST';

        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(payload)
        });

        if (res.ok) {
            closeOrderModal();
            loadOrders();
        } else {
            alert('Failed to save order');
        }
    } catch (err) {
        alert('Error saving order');
    }
});

async function deleteProduct(id) {
    if (!confirm('Are you sure you want to delete this product?')) return;
    try {
        const res = await fetch(`/api/products/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.ok) loadProducts();
    } catch (err) {
        alert('Failed to delete');
    }
}

// Pages CMS Management
async function loadPages() {
    try {
        const res = await fetch('/api/pages');
        const pages = await res.json();
        const tbody = document.getElementById('pages-table-body');
        tbody.innerHTML = pages.map(page => `
            <tr>
                <td style="font-weight:600; color: #1e293b;">${page.titleAr}</td>
                <td>${page.titleFr}</td>
                <td style="font-family: monospace; color: #4f46e5;">${page.slug}</td>
                <td style="display:flex; gap:0.5rem;">
                    <a href="/page.html?slug=${page.slug}" target="_blank" class="btn btn-sm" title="View"><i class="fas fa-external-link-alt"></i></a>
                    <button class="btn btn-sm" onclick="editPage('${page.id}')" title="Edit"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm" onclick="deletePage('${page.id}')" style="color:#ef4444;" title="Delete"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `).join('');
    } catch (err) {
        console.error('Error loading pages:', err);
    }
}

function openPageModal(isEdit = false) {
    const modal = document.getElementById('page-modal');
    modal.classList.add('active');
    document.getElementById('page-modal-title').innerText = isEdit ? 'Edit Page' : 'Add New Page';
    if (!isEdit) {
        document.getElementById('page-form').reset();
        document.getElementById('page-id').value = '';
    }
}

function closePageModal() {
    document.getElementById('page-modal').classList.remove('active');
}

async function editPage(id) {
    try {
        const res = await fetch(`/api/pages`);
        const pages = await res.json();
        const page = pages.find(p => p.id === id);
        if (page) {
            openPageModal(true);
            const form = document.getElementById('page-form');
            form.id.value = page.id;
            form.titleAr.value = page.titleAr;
            form.titleFr.value = page.titleFr;
            form.slug.value = page.slug;
            form.contentAr.value = page.contentAr;
            form.contentFr.value = page.contentFr;
        }
    } catch (err) {
        alert('Failed to load page details');
    }
}

document.getElementById('page-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('page-id').value;
    const data = {
        titleAr: e.target.titleAr.value,
        titleFr: e.target.titleFr.value,
        contentAr: e.target.contentAr.value,
        contentFr: e.target.contentFr.value,
        slug: e.target.slug.value
    };

    try {
        const url = id ? `/api/pages/${id}` : '/api/pages';
        const method = id ? 'PUT' : 'POST';
        const res = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data)
        });

        if (res.ok) {
            closePageModal();
            loadPages();
            e.target.reset();
        } else {
            alert('Failed to save page');
        }
    } catch (err) {
        alert('Error saving page');
    }
});

async function deletePage(id) {
    if (!confirm('Are you sure you want to delete this page?')) return;
    try {
        const res = await fetch(`/api/pages/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${token}` }
        });
        if (res.ok) loadPages();
    } catch (err) {
        alert('Failed to delete');
    }
}

function logout() {
    localStorage.removeItem('admin_token');
    window.location.href = 'index.php';
}


// Settings Management
async function loadSettings() {
    try {
        const response = await fetch('/api/settings');
        const settings = await response.json();

        // Load Marketing settings too
        loadMarketingSettings();

        // Populate form
        document.getElementById('form-language').value = settings.language || 'ar';
        document.getElementById('theme-color').value = settings.themeColor || '#2563eb';
        document.getElementById('header-color').value = settings.headerColor || '#f97316';

        const lang = settings.language || 'ar';

        // Populate custom labels
        document.getElementById('label-mainCta').value = settings.customLabels?.mainCta?.[lang] || '';
        document.getElementById('label-fullName').value = settings.customLabels?.fullName?.[lang] || '';
        document.getElementById('label-phone').value = settings.customLabels?.phone?.[lang] || '';
        document.getElementById('label-wilaya').value = settings.customLabels?.wilaya?.[lang] || '';
        document.getElementById('label-city').value = settings.customLabels?.city?.[lang] || '';
        document.getElementById('label-total').value = settings.customLabels?.total?.[lang] || '';

        // Set visibility checkboxes
        document.getElementById('visible-fullName').checked = settings.fieldVisibility?.fullName !== false;
        document.getElementById('visible-phone').checked = settings.fieldVisibility?.phone !== false;
        document.getElementById('visible-wilaya').checked = settings.fieldVisibility?.wilaya !== false;
        document.getElementById('visible-city').checked = settings.fieldVisibility?.city !== false;
        document.getElementById('visible-quantity').checked = settings.fieldVisibility?.quantity !== false;

        // Integration settings
        document.getElementById('wc-url').value = settings.integrations?.woocommerce?.url || '';
        document.getElementById('wc-key').value = settings.integrations?.woocommerce?.key || '';
        document.getElementById('wc-secret').value = settings.integrations?.woocommerce?.secret || '';
        document.getElementById('gs-url').value = settings.integrations?.googlesheets?.url || '';
    } catch (error) {
        console.error('Failed to load settings:', error);
    }
}

// Update label inputs when language changes
document.addEventListener('DOMContentLoaded', () => {
    const langSelector = document.getElementById('form-language');
    if (langSelector) {
        langSelector.addEventListener('change', async () => {
            await loadSettings();
        });
    }
});

// Handle settings form submission
if (document.getElementById('settings-form')) {
    document.getElementById('settings-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const lang = document.getElementById('form-language').value;

        const settings = {
            language: lang,
            themeColor: document.getElementById('theme-color').value,
            headerColor: document.getElementById('header-color').value,
            customLabels: {
                mainCta: {
                    ar: lang === 'ar' ? document.getElementById('label-mainCta').value : 'أطلب الآن - الدفع عند الاستلام',
                    fr: lang === 'fr' ? document.getElementById('label-mainCta').value : 'Commander Maintenant - COD'
                },
                fullName: {
                    ar: lang === 'ar' ? document.getElementById('label-fullName').value : 'الإسم الكامل',
                    fr: lang === 'fr' ? document.getElementById('label-fullName').value : 'Nom Complet'
                },
                phone: {
                    ar: lang === 'ar' ? document.getElementById('label-phone').value : 'رقم الهاتف',
                    fr: lang === 'fr' ? document.getElementById('label-phone').value : 'Numéro de Téléphone'
                },
                wilaya: {
                    ar: lang === 'ar' ? document.getElementById('label-wilaya').value : 'الولاية',
                    fr: lang === 'fr' ? document.getElementById('label-wilaya').value : 'Wilaya'
                },
                city: {
                    ar: lang === 'ar' ? document.getElementById('label-city').value : 'البلدية / المدينة',
                    fr: lang === 'fr' ? document.getElementById('label-city').value : 'Commune / Ville'
                },
                total: {
                    ar: lang === 'ar' ? document.getElementById('label-total').value : 'المجموع النهائي',
                    fr: lang === 'fr' ? document.getElementById('label-total').value : 'Total Final'
                }
            },
            fieldVisibility: {
                fullName: document.getElementById('visible-fullName').checked,
                phone: document.getElementById('visible-phone').checked,
                wilaya: document.getElementById('visible-wilaya').checked,
                city: document.getElementById('visible-city').checked,
                quantity: document.getElementById('visible-quantity').checked
            },
            integrations: {
                woocommerce: {
                    url: document.getElementById('wc-url').value,
                    key: document.getElementById('wc-key').value,
                    secret: document.getElementById('wc-secret').value
                },
                googlesheets: {
                    url: document.getElementById('gs-url').value
                }
            }
        };

        try {
            const response = await fetch('/api/settings', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(settings)
            });

            if (response.ok) {
                alert('Settings saved successfully!');
                loadSettings();
            } else {
                alert('Failed to save settings');
            }
        } catch (error) {
            console.error('Error saving settings:', error);
            alert('Failed to save settings');
        }
    });
}

async function loadMarketingSettings() {
    try {
        const res = await fetch('/api/marketing.php');
        const marketing = await res.json();
        document.getElementById('fb-pixel').value = marketing.fbPixel || '';
        document.getElementById('tiktok-pixel').value = marketing.tiktokPixel || '';
        document.getElementById('google-analytics').value = marketing.googleAnalytics || '';
        document.getElementById('google-search-console').value = marketing.googleSearchConsole || '';
    } catch (err) { console.error('Failed to load marketing settings'); }
}

async function saveMarketingSettings() {
    const data = {
        fbPixel: document.getElementById('fb-pixel').value,
        tiktokPixel: document.getElementById('tiktok-pixel').value,
        googleAnalytics: document.getElementById('google-analytics').value,
        googleSearchConsole: document.getElementById('google-search-console').value
    };

    try {
        const res = await fetch('/api/marketing.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${token}`
            },
            body: JSON.stringify(data)
        });
        if (res.ok) alert('Marketing settings saved!');
    } catch (err) { alert('Failed to save marketing settings'); }
}
