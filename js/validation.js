

// validate signup
document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  const mobileInput = document.getElementById("mobile");
  const nameInput = document.getElementById("name");
  const emailInput = document.getElementById("email");
  const addressInput = document.getElementById("address");
  const dobInput = document.getElementById("dob");
  const passwordInput = document.getElementById("password");
  const confirmPasswordInput = document.getElementById("confirm-password");

  form.addEventListener("submit", (event) => {
    let isValid = true;

    // Validate mobile number
    const mobile = mobileInput.value.trim();
    const mobileRegex = /^[0-9]{11}$/; // 10-digit numeric only
    if (!mobileRegex.test(mobile)) {
      isValid = false;
    }

    // Validate name
    const name = nameInput.value.trim();
    const nameRegex = /^[a-zA-Z ]+$/; // Letters and spaces only
    if (!nameRegex.test(name)) {
      isValid = false;
    }

    // Validate email
    const email = emailInput.value.trim();
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // Standard email format
    if (!emailRegex.test(email)) {
      isValid = false;
    }

    // Validate address
    const address = addressInput.value.trim();
    if (address.length < 5) {
      isValid = false;
    }

    // Validate password
    const password = passwordInput.value.trim();
    const passwordRegex = /^.{4,}$/;
    if (!passwordRegex.test(password)) {
      isValid = false;
    }

    // Validate confirm password
    const confirmPassword = confirmPasswordInput.value.trim();
    if (password !== confirmPassword) {
      isValid = false;
    }

    // Prevent form submission if validation fails
    if (!isValid) {
      event.preventDefault();
      alert("Invalid information try again");
    } else {
      alert("successfully signup");
    }
  });
});

//emplyevalidation

document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form");
  const employeeIdInput = document.getElementById("employee_id");
  const passwordInput = document.getElementById("employee_password");
  const mobileInput = document.getElementById("employee_mobile_number");
  const emailInput = document.getElementById("employee_email");
  const nameInput = document.getElementById("employee_name");
  const addressInput = document.getElementById("employee_address");
  const salaryInput = document.getElementById("employee_salary");

  form.addEventListener("submit", (event) => {
    let isValid = true;

    // Validate employee ID
    const employeeId = employeeIdInput.value.trim();
    const employeeIdRegex = /^[0-9]+$/; // Numbers only
    if (!employeeIdRegex.test(employeeId)) {

      // alert("Employee ID must contain only numbers.");
      isValid = false;
    }

    const password = passwordInput.value.trim();
    const passwordRegex = /^.{4,}$/; // Matches any string with 4 or more characters
    if (!passwordRegex.test(password)) {

      isValid = false;
    }
    



    // Validate mobile number
    const mobile = mobileInput.value.trim();
    const mobileRegex = /^[0-9]{11}$/; // 10-digit numeric only
    if (!mobileRegex.test(mobile)) {

      isValid = false;
    }

    // Validate email
    const email = emailInput.value.trim();
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/; // Standard email format
    if (!emailRegex.test(email)) {

      isValid = false;
    }

    // Validate name
    const name = nameInput.value.trim();
    const nameRegex = /^[a-zA-Z ]+$/; // Letters and spaces only
    if (!nameRegex.test(name)) {

      isValid = false;
    }

    // Validate address
    const address = addressInput.value.trim();
    if (address.length < 4) {
      isValid = false;
    }

    // Validate salary
    const salary = salaryInput.value.trim();
    if (isNaN(salary) || salary <= 0) {
      isValid = false;

    }

    // If any validation fails, prevent form submission
    if (!isValid) {
      event.preventDefault();
      alert("Invalid information try again !");
    } else {
      alert("successfully added");
    }
  });
});

// PRODUCT VALIDATION
// validation.js

// validation.js
document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("productForm");
  
  if (!form) {
      console.error("Form not found!");
      return;
  }

  // Form elements
  const productIdInput = document.getElementById("product_id");
  const categoryInput = document.getElementById("product_category");
  const nameInput = document.getElementById("product_name");
  const priceInput = document.getElementById("product_price");
  const quantityInput = document.getElementById("product_quantity");
  const imageInput = document.getElementById("product_image");

  // Check if elements exist
  if (!productIdInput || !categoryInput || !nameInput || !priceInput || !quantityInput || !imageInput) {
      console.error("One or more form elements are missing!");
      return;
  }

  form.addEventListener("submit", (event) => {
      event.preventDefault();
      let isValid = true;
      
      // Clear previous errors
      document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
      document.querySelectorAll('input, select').forEach(el => el.classList.remove('error-field'));
      
      // Helper functions
      const isEmptyOrSpaces = (str) => str.trim().length === 0;
      const showError = (inputElement, message) => {
          const errorElement = document.getElementById(`${inputElement.id}_error`);
          errorElement.textContent = message;
          inputElement.classList.add('error-field');
          isValid = false;
      };

      // Validate product ID
      const productId = productIdInput.value.trim();
      const productIdRegex = /^[0-9]+$/;
      if (isEmptyOrSpaces(productId)) {
          showError(productIdInput, "Product ID cannot be empty");
      } else if (!productIdRegex.test(productId)) {
          showError(productIdInput, "Product ID must contain only numbers");
      }

      // Validate product category
      const category = categoryInput.value;
      if (category === "") {
          showError(categoryInput, "Please select a category");
      }

      // Validate product name
      const name = nameInput.value.trim();
      if (isEmptyOrSpaces(name)) {
          showError(nameInput, "Product name cannot be empty");
      } else if (name.length < 3 || name.length > 100) {
          showError(nameInput, "Product name must be 3-100 characters");
      }

      // Validate product price
      const price = parseFloat(priceInput.value);
      if (isNaN(price)) {
          showError(priceInput, "Price must be a number");
      } else if (price <= 0) {
          showError(priceInput, "Price must be greater than 0");
      }

      // Validate product quantity
      const quantity = parseInt(quantityInput.value);
      if (isNaN(quantity)) {
          showError(quantityInput, "Quantity must be a whole number");
      } else if (quantity <= 0) {
          showError(quantityInput, "Quantity must be greater than 0");
      } else if (!Number.isInteger(quantity)) {
          showError(quantityInput, "Quantity must be a whole number (no decimals)");
      }

      // Validate product image
      const image = imageInput.files[0];
      if (!image) {
          showError(imageInput, "Please upload a product image");
      } else {
          const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
          if (!validTypes.includes(image.type)) {
              showError(imageInput, "Please upload a valid image (JPEG, PNG, GIF)");
          }
          if (image.size > 2 * 1024 * 1024) { // 2MB limit
              showError(imageInput, "Image size must be less than 2MB");
          }
      }

      if (isValid) {
          alert("Product successfully validated!");
          form.submit();
      }
  });
});