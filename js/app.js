document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
});

async function loadProducts() {
    const grid = document.getElementById('product-grid');
    try {
        const response = await fetch('/api/products.php');
        const products = await response.json();

        if (products.length === 0) {
            grid.innerHTML = '<p style="text-align:center; grid-column: 1/-1;">No products found yet.</p>';
            return;
        }

        grid.innerHTML = products.map(product => `
            <div class="product-card" onclick="window.location.href='product.php?id=${product.id}'" style="cursor: pointer;">
                <img src="${product.image}" alt="${product.title}" class="product-image">
                <div class="product-info">
                    <span class="product-category">${product.category}</span>
                    <h3 class="product-title">${product.title}</h3>
                    <div class="product-price-row">
                        <span class="product-price">${product.price} DZD</span>
                        <span class="product-shipping">+${product.shippingPrice} DZD Shipping</span>
                    </div>
                    <a href="product.php?id=${product.id}" class="btn-order" onclick="event.stopPropagation();">Order Now (COD)</a>
                </div>
            </div>
        `).join('');
    } catch (error) {
        console.error('Error loading products:', error);
        grid.innerHTML = '<p style="text-align:center; grid-column: 1/-1;">Error loading products. Please try again later.</p>';
    }
}
