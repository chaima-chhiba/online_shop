document.addEventListener("DOMContentLoaded", () => {
    const cartItemsContainer = document.querySelector("#cart-items-container tbody");
    const subTotalElement = document.getElementById("sub-total");
    const shippingCostElement = document.getElementById("shipping-cost");
    const totalAmountElement = document.getElementById("total-amount");

    const SHIPPING_COST = 10.00; // Fixed shipping cost

    const fetchProductDetails = async (productId) => {
        try {
            const response = await fetch("../json/products.json");
            const productData = await response.json();
            return productData.find(item => item.product_id === productId) || null;
        } catch (error) {
            console.error("Error fetching product details:", error);
            return null;
        }
    };

    // Get cart from cookies
    const getCartFromCookie = () => {
        const cartCookie = document.cookie.match(/cart=([^;]*)/);
        return cartCookie ? JSON.parse(decodeURIComponent(cartCookie[1])) : [];
    };

    const saveCartToCookie = (cart) => {
        document.cookie = `cart=${encodeURIComponent(JSON.stringify(cart))}; path=/; max-age=31536000`;
    };

    
    const renderCart = async () => {
        const cart = getCartFromCookie();
        let subTotal = 0;
        cartItemsContainer.innerHTML = "";

        for (const item of cart) {
            const { productId, quantity } = item;
            const productDetails = await fetchProductDetails(productId);

            if (!productDetails) continue;

            const productPrice = parseFloat(productDetails.product_price || 0);
            const itemTotal = productPrice * quantity;
            subTotal += itemTotal;

            // Render row
            const row = document.createElement("tr");
            row.innerHTML = `
            <td>
                <img src="${productDetails.product_location}" alt="Product Image" width="50">
                ${productDetails.product_name}
            </td>
            <td>TND${productPrice.toFixed(2)}</td>
            <td>
                <button class="decrement" data-product-id="${productId}">-</button>
                <span>${quantity}</span>
                <button class="increment" data-product-id="${productId}">+</button>
            </td>
            <td>TND${itemTotal.toFixed(2)}</td>
            <td>
                <button class="remove" data-product-id="${productId}">Remove</button>
            </td>
        `;
            cartItemsContainer.appendChild(row);
        }

        // Update totals
        subTotalElement.textContent = subTotal.toFixed(2);
        shippingCostElement.textContent = subTotal > 0 ? SHIPPING_COST.toFixed(2) : "0.00";
        totalAmountElement.textContent = (subTotal + (subTotal > 0 ? SHIPPING_COST : 0)).toFixed(2);

        // Save subtotal to cookie
        document.cookie = `cost=${subTotal.toFixed(2)}; path=/; max-age=31536000;`;
    };


    cartItemsContainer.addEventListener("click", async (event) => {
        const target = event.target;
        const productId = target.getAttribute("data-product-id");
        let cart = getCartFromCookie();

        if (target.classList.contains("remove")) {
            cart = cart.filter(item => item.productId !== productId);
        } else if (target.classList.contains("increment")) {
            const productDetails = await fetchProductDetails(productId);
            if (!productDetails) {
                alert("Product details not found.");
                return;
            }

            // Check stock availability
            const cartItem = cart.find(item => item.productId === productId);
            if (cartItem.quantity < productDetails.product_quantity) {
                cart = cart.map(item => item.productId === productId ? { ...item, quantity: item.quantity + 1 } : item);
            } else {
                alert("Cannot add more items. Stock limit reached.");
                return;
            }
        } else if (target.classList.contains("decrement")) {
            cart = cart.map(item => item.productId === productId && item.quantity > 1 ? { ...item, quantity: item.quantity - 1 } : item);
        }

        saveCartToCookie(cart);
        renderCart();
    });

    renderCart();
});
