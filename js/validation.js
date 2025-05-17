

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
  // Debug to verify script is running
  console.log("DOM loaded, starting validation setup");
  
  // Get the form - check if it exists before proceeding
  const form = document.querySelector("form");
  if (!form) {
    console.error("Employee form not found! Make sure this script is loaded on the correct page.");
    return; // Exit if no form is found
  }
  
  
  // Get all form elements and check if they exist
  const employeeIdInput = document.getElementById("employee_id");
  const passwordInput = document.getElementById("employee_password");
  const mobileInput = document.getElementById("employee_mobile_number");
  const emailInput = document.getElementById("employee_email");
  const nameInput = document.getElementById("employee_name");
  const genderInput = document.getElementById("employee_gender");
  const dobInput = document.getElementById("employee_dob");
  const addressInput = document.getElementById("employee_address");
  const roleInput = document.getElementById("employee_role");
  const joiningDateInput = document.getElementById("employee_joining_date");
  const salaryInput = document.getElementById("employee_salary");
  
  
  

  

  // Helper function to display error
  const showError = (element, message) => {
    if (!element) return; // Skip if element doesn't exist
    
    const formGroup = element.closest('.form-group');
    if (!formGroup) return; // Skip if no parent found
    
    // Remove any existing error message
    const existingError = formGroup.querySelector('.error-message');
    if (existingError) {
      existingError.remove();
    }
    
    // Create and append error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = 'red';
    errorDiv.style.fontSize = '12px';
    errorDiv.style.marginTop = '5px';
    errorDiv.textContent = message;
    formGroup.appendChild(errorDiv);
    
    // Highlight the input field
    element.style.borderColor = 'red';
  };

  // Helper function to clear error
  const clearError = (element) => {
    if (!element) return; // Skip if element doesn't exist
    
    const formGroup = element.closest('.form-group');
    if (!formGroup) return; // Skip if no parent found
    
    const errorMessage = formGroup.querySelector('.error-message');
    if (errorMessage) {
      errorMessage.remove();
    }
    element.style.borderColor = '';
  };

  // Add validation on form submission
  form.addEventListener("submit", (event) => {
    console.log("Form submission attempted");
    
    // Prevent default form submission initially
    event.preventDefault();
    
    // Clear all previous errors
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('input, select').forEach(el => el.style.borderColor = '');
    
    let isValid = true;

    // Only validate fields that exist
    // Validate employee ID (numbers only)
    if (employeeIdInput) {
      const employeeId = employeeIdInput.value.trim();
      const employeeIdRegex = /^[0-9]+$/;
      if (!employeeIdRegex.test(employeeId)) {
        showError(employeeIdInput, "Employee ID must contain only numbers");
        isValid = false;
      }
    }

    // Validate password (at least 4 characters)
    if (passwordInput) {
      const password = passwordInput.value.trim();
      if (password.length < 4) {
        showError(passwordInput, "Password must be at least 4 characters long");
        isValid = false;
      }
    }

    // Validate mobile number (8 digits)
    if (mobileInput) {
      const mobile = mobileInput.value.trim();
      const mobileRegex = /^[0-9]{8}$/;
      if (!mobileRegex.test(mobile)) {
        showError(mobileInput, "Mobile number must be 8 digits");
        isValid = false;
      }
    }

    // Validate email
    if (emailInput) {
      const email = emailInput.value.trim();
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      if (!emailRegex.test(email)) {
        showError(emailInput, "Please enter a valid email address");
        isValid = false;
      }
    }

    // Validate name (letters and spaces only)
    if (nameInput) {
      const name = nameInput.value.trim();
      const nameRegex = /^[a-zA-Z ]+$/;
      if (!nameRegex.test(name)) {
        showError(nameInput, "Name must contain only letters and spaces");
        isValid = false;
      }
    }

    // Validate gender selection
    if (genderInput && genderInput.value === "") {
      showError(genderInput, "Please select a gender");
      isValid = false;
    }

    // Validate date of birth
    if (dobInput) {
      if (dobInput.value === "") {
        showError(dobInput, "Please select a date of birth");
        isValid = false;
      } else {
        // Check if the person is at least 18 years old
        const dob = new Date(dobInput.value);
        const today = new Date();
        const age = today.getFullYear() - dob.getFullYear();
        const monthDiff = today.getMonth() - dob.getMonth();
        
        if (age < 18 || (age === 18 && monthDiff < 0) || (age === 18 && monthDiff === 0 && today.getDate() < dob.getDate())) {
          showError(dobInput, "Employee must be at least 18 years old");
          isValid = false;
        }
      }
    }

    // Validate address (at least 4 characters)
    if (addressInput) {
      const address = addressInput.value.trim();
      if (address.length < 4) {
        showError(addressInput, "Address must be at least 4 characters long");
        isValid = false;
      }
    }

    // Validate role selection
    if (roleInput && roleInput.value === "") {
      showError(roleInput, "Please select a role");
      isValid = false;
    }

    // Validate joining date
    if (joiningDateInput) {
      if (joiningDateInput.value === "") {
        showError(joiningDateInput, "Please select a joining date");
        isValid = false;
      } else {
        // Check if joining date is not in the future
        const joiningDate = new Date(joiningDateInput.value);
        const today = new Date();
        if (joiningDate > today) {
          showError(joiningDateInput, "Joining date cannot be in the future");
          isValid = false;
        }
      }
    }

    // Validate salary (positive number)
    if (salaryInput) {
      const salary = parseFloat(salaryInput.value);
      if (isNaN(salary) || salary <= 0) {
        showError(salaryInput, "Salary must be a positive number");
        isValid = false;
      }
    }

    // Submit the form if all validations pass
    if (isValid) {
      console.log("Validation passed, submitting form");
      alert("Employee successfully added!");
      form.submit();
    } else {
      console.log("Validation failed");
    }
  });

  // Only add event listeners to elements that exist
  if (employeeIdInput) {
    employeeIdInput.addEventListener('input', () => {
      const employeeId = employeeIdInput.value.trim();
      const employeeIdRegex = /^[0-9]+$/;
      
      if (employeeId !== '' && !employeeIdRegex.test(employeeId)) {
        showError(employeeIdInput, "Employee ID must contain only numbers");
      } else {
        clearError(employeeIdInput);
      }
    });
  }

  if (mobileInput) {
    mobileInput.addEventListener('input', () => {
      const mobile = mobileInput.value.trim();
      
      if (mobile !== '' && !/^[0-9]*$/.test(mobile)) {
        showError(mobileInput, "Mobile number must contain only digits");
      } else if (mobile.length =8) {
        showError(mobileInput, "Mobile number must be 8 digits");
      } else {
        clearError(mobileInput);
      }
    });
  }

  if (emailInput) {
    emailInput.addEventListener('input', () => {
      const email = emailInput.value.trim();
      const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
      
      if (email !== '' && !emailRegex.test(email)) {
        showError(emailInput, "Please enter a valid email address");
      } else {
        clearError(emailInput);
      }
    });
  }

  if (nameInput) {
    nameInput.addEventListener('input', () => {
      const name = nameInput.value.trim();
      const nameRegex = /^[a-zA-Z ]*$/;
      
      if (name !== '' && !nameRegex.test(name)) {
        showError(nameInput, "Name must contain only letters and spaces");
      } else {
        clearError(nameInput);
      }
    });
  }

  if (salaryInput) {
    salaryInput.addEventListener('input', () => {
      const salary = parseFloat(salaryInput.value);
      
      if (salaryInput.value !== '' && (isNaN(salary) || salary <= 0)) {
        showError(salaryInput, "Salary must be a positive number");
      } else {
        clearError(salaryInput);
      }
    });
  }
  
  // Add autocomplete attribute to password field
  if (passwordInput) {
    passwordInput.setAttribute('autocomplete', 'new-password');
  }

  
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