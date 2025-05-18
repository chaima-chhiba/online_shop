let currentPage = 1; 
let totalPages = 1;
let currentCategory = 'all'; // Track the current category

async function loadProducts(page = 1, category = currentCategory) {
  try {
    // Add category to the query parameters if it's not 'all'
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
    gridContainer.innerHTML = ""; 

    if (products.length === 0) {
      gridContainer.innerHTML = "<p>No products available in this category.</p>";
    } else {
      // Add products to the grid
      products.forEach((product) => {
        const productCard = document.createElement("div");
        productCard.classList.add("product-card");
        productCard.innerHTML = `
                    <img src="${product.product_location}" alt="${product.product_name}">
                    <h3>${product.product_name}</h3>
                    <p>TND ${product.product_price}</p>
                    <button 
                        class="add-to-cart-button" 
                        data-product-id="${product.product_id}" 
                        data-product-name="${product.product_name}">
                        Add to Cart
                    </button>
                `;
        gridContainer.appendChild(productCard);
      });

      attachAddToCartHandlers();
    }

    window.scrollTo(0, 0);

    checkPagination();
  } catch (error) {
    console.error("Error loading products:", error);
    document.getElementById("productGrid").innerHTML =
      "<p>Failed to load products. Please try again later.</p>";
    disablePaginationButtons();
  }
}

// Function to filter products by category
function filterByCategory(category) {
  currentCategory = category;
  currentPage = 1; // Reset to first page when changing categories
  loadProducts(currentPage, currentCategory);
}

// Function to attach add-to-cart event handlers to buttons
function attachAddToCartHandlers() {
  const buttons = document.querySelectorAll(".add-to-cart-button");

  buttons.forEach((button) => {
    button.addEventListener("click", function () {
      const productId = button.getAttribute("data-product-id"); // Get product ID
      const productName = button.getAttribute("data-product-name"); // Get product name
      const quantity = 1; // Default quantity is 1

      addToCartAlert(productName); // Show alert
      saveToCookie(productId, quantity); // Save product to cookie
      sendToServer(productId, quantity); // Send product info to server
    });
  });
}

function checkPagination() {
  document.getElementById("prevPage").disabled = currentPage === 1;
  document.getElementById("nextPage").disabled = currentPage === totalPages;
}

// Disable both pagination buttons
function disablePaginationButtons() {
  document.getElementById("prevPage").disabled = true;
  document.getElementById("nextPage").disabled = true;
}

document.getElementById("prevPage").addEventListener("click", () => {
  if (currentPage > 1) {
    currentPage--;
    loadProducts(currentPage, currentCategory); // Load the previous page with current category
  }
});

document.getElementById("nextPage").addEventListener("click", () => {
  if (currentPage < totalPages) {
    currentPage++;
    loadProducts(currentPage, currentCategory); // Load the next page with current category
  }
});

// Listen for messages from the parent window (index.html)
window.addEventListener('message', function(event) {
  // Check if the message is a filter request
  if (event.data && event.data.type === 'filter') {
    filterByCategory(event.data.category);
  }
});

window.onload = () => loadProducts(currentPage, currentCategory);