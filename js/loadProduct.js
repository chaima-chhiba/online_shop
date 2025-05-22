// Prevent multiple initializations
if (window.loadProductScriptLoaded) {
    console.warn('loadProduct.js already loaded');
} else {
    window.loadProductScriptLoaded = true;

    let currentPage = 1; 
    let totalPages = 1;
    let currentCategory = 'all';

    // Enhanced loadProducts function
    async function loadProducts(page = 1, category = currentCategory) {
        try {
            let url = `../php/loadProduct.php?page=${page}`;
            if (category !== 'all') {
                url += `&category=${encodeURIComponent(category)}`;
            }
            
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            const products = data.products;
            totalPages = data.totalPages; 

            const gridContainer = document.getElementById("productGrid");
            if (!gridContainer) {
                console.error('Product grid container not found');
                return;
            }
            
            gridContainer.innerHTML = ""; 

            if (products.length === 0) {
                gridContainer.innerHTML = "<p>No products available in this category.</p>";
            } else {
                products.forEach((product) => {
                    const productCard = document.createElement("div");
                    productCard.classList.add("product-card");
                    productCard.innerHTML = `
                        <a href="../html/product-details.html?id=${product.product_id}" onclick="handleProductClick(event, '${product.product_id}')">
                            <img src="${product.product_location}" alt="${product.product_name}">
                        </a>
                        <h3>${product.product_name}</h3>
                        <p>TND ${product.product_price}</p>
                        <div class="product-buttons">
                            <button 
                                class="view-details-button" 
                                onclick="handleDetailsClick('${product.product_id}')">
                                View Details
                            </button>
                            <button 
                                class="add-to-cart-button" 
                                data-product-id="${product.product_id}" 
                                data-product-name="${product.product_name}">
                                Add to Cart
                            </button>
                        </div>
                    `;
                    gridContainer.appendChild(productCard);
                });

                attachAddToCartHandlers();
            }

            checkPagination();
        } catch (error) {
            console.error("Error loading products:", error);
            const gridContainer = document.getElementById("productGrid");
            if (gridContainer) {
                gridContainer.innerHTML = "<p>Failed to load products. Please try again later.</p>";
            }
            disablePaginationButtons();
        }
    }

    // Safe product click handler
    function handleProductClick(event, productId) {
        event.preventDefault();
        handleDetailsClick(productId);
    }

    // Safe details click handler
    function handleDetailsClick(productId) {
        // Clear any existing content to prevent duplication
        if (document.body) {
            document.body.style.opacity = '0.5';
        }
        
        // Navigate safely
        if (window.parent && window.parent !== window) {
            // If in iframe, navigate parent window
            window.parent.location.href = `html/product-details.html?id=${productId}`;
        } else {
            // Direct navigation
            window.location.href = `../html/product-details.html?id=${productId}`;
        }
    }

    // Make functions global for onclick handlers
    window.handleProductClick = handleProductClick;
    window.handleDetailsClick = handleDetailsClick;

    // Function to filter products by category
    function filterByCategory(category) {
        currentCategory = category;
        currentPage = 1;
        loadProducts(currentPage, currentCategory);
    }

    // Function to attach add-to-cart event handlers
    function attachAddToCartHandlers() {
        const buttons = document.querySelectorAll(".add-to-cart-button");

        buttons.forEach((button) => {
            // Remove existing event listeners by cloning
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            newButton.addEventListener("click", function (e) {
                e.preventDefault();
                e.stopPropagation();
                
                const productId = newButton.getAttribute("data-product-id");
                const productName = newButton.getAttribute("data-product-name");
                const quantity = 1;

                // Show notification in parent window if possible
                try {
                    if (window.parent && window.parent.showToast && window.parent !== window) {
                        window.parent.showToast(`${productName} added to cart!`);
                    } else if (typeof addToCartAlert === 'function') {
                        addToCartAlert(productName);
                    }
                } catch (e) {
                    console.log('Could not show toast notification');
                }

                // Save to cart
                try {
                    if (typeof saveToCookie === 'function') {
                        saveToCookie(productId, quantity);
                    }
                    if (typeof sendToServer === 'function') {
                        sendToServer(productId, quantity);
                    }
                } catch (e) {
                    console.error('Error adding to cart:', e);
                }
            });
        });
    }

    function checkPagination() {
        const prevBtn = document.getElementById("prevPage");
        const nextBtn = document.getElementById("nextPage");
        
        if (prevBtn) prevBtn.disabled = currentPage === 1;
        if (nextBtn) nextBtn.disabled = currentPage === totalPages;
    }

    function disablePaginationButtons() {
        const prevBtn = document.getElementById("prevPage");
        const nextBtn = document.getElementById("nextPage");
        
        if (prevBtn) prevBtn.disabled = true;
        if (nextBtn) nextBtn.disabled = true;
    }

    // Pagination event listeners with safety checks
    function initializePagination() {
        const prevBtn = document.getElementById("prevPage");
        const nextBtn = document.getElementById("nextPage");
        
        if (prevBtn && !prevBtn.hasAttribute('data-initialized')) {
            prevBtn.setAttribute('data-initialized', 'true');
            prevBtn.addEventListener("click", () => {
                if (currentPage > 1) {
                    currentPage--;
                    loadProducts(currentPage, currentCategory);
                }
            });
        }
        
        if (nextBtn && !nextBtn.hasAttribute('data-initialized')) {
            nextBtn.setAttribute('data-initialized', 'true');
            nextBtn.addEventListener("click", () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    loadProducts(currentPage, currentCategory);
                }
            });
        }
    }

    // Listen for messages from parent window
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'filter') {
            filterByCategory(event.data.category);
        }
    });

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initializePagination();
            loadProducts(currentPage, currentCategory);
        });
    } else {
        initializePagination();
        loadProducts(currentPage, currentCategory);
    }

    // Handle page visibility for cleanup
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Page is being hidden, clean up
            const buttons = document.querySelectorAll('.add-to-cart-button');
            buttons.forEach(btn => {
                btn.style.pointerEvents = 'none';
            });
        } else {
            // Page is visible again, re-enable
            const buttons = document.querySelectorAll('.add-to-cart-button');
            buttons.forEach(btn => {
                btn.style.pointerEvents = 'auto';
            });
        }
    });

    // Export functions for external use
    window.loadProducts = loadProducts;
    window.filterByCategory = filterByCategory;
}