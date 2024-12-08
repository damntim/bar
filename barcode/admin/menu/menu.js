

// Toggle Product Modal
function toggleProductModal(categoryId = null) {
  const modal = document.getElementById('productModal');
  const categoryIdInput = document.getElementById('categoryId');
  if (categoryId) categoryIdInput.value = categoryId;
  modal.classList.toggle('hidden');
}

// Toggle fields based on stock status
function toggleProductFields() {
  const productInStock = document.getElementById('productInStock').value;
  const productSelection = document.getElementById('productSelection');
  const productFields = document.getElementById('productFields');

  if (productInStock === 'yes') {
    // Show product selection dropdown
    productSelection.classList.remove('hidden');
    // Initially hide the product fields until a product is selected
    productFields.classList.add('hidden');
  } else {
    // Hide product selection, show all manual entry fields
    productSelection.classList.add('hidden');
    productFields.classList.remove('hidden');
    // Clear all fields
    document.getElementById('productNameField').value = '';
    document.getElementById('productDetails').value = '';
    document.getElementById('productImage').value = '';
    document.getElementById('preparingTime').value = '';
    document.getElementById('price').value = '';
  }
}

// Auto-fill product details when a product is selected
function autoFillProductDetails() {
  const productId = document.getElementById('productName').value;
  const productFields = document.getElementById('productFields');
  const imagePreview = document.getElementById('imagePreview');
  const existingImagePreview = document.getElementById('existingImagePreview');
  const existingImage = document.getElementById('existingImage');

  if (productId) {
    // Show all fields once a product is selected
    productFields.classList.remove('hidden');

    fetch(`get_product_details.php?id=${productId}`)
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          // Auto-fill product details from database
          document.getElementById('productNameField').value = data.product.name;
          document.getElementById('productDetails').value = data.product.details;

          // Handle image display
          if (data.product.image) {
            existingImage.value = data.product.image; // Store image path in hidden input
            existingImagePreview.src = data.product.image; // Set image source
            imagePreview.classList.remove('hidden'); // Show image preview
            document.getElementById('productImage').value = ''; // Clear file input
          } else {
            imagePreview.classList.add('hidden');
            existingImage.value = '';
          }

          // Clear price and preparing time for manual entry
          document.getElementById('preparingTime').value = '';
          document.getElementById('price').value = '';
        }
      })
      .catch((error) => console.error('Error:', error));
  } else {
    // If no product is selected, hide the fields
    productFields.classList.add('hidden');
    imagePreview.classList.add('hidden');
  }
}

// Add this function to handle form submission
function handleFormSubmit(event) {
  event.preventDefault();
  const formData = new FormData(event.target);

  // If there's no new image uploaded, use the existing image
  if (!formData.get('product_image').size && formData.get('existing_image')) {
    formData.set('product_image', formData.get('existing_image'));
  }

  fetch('add_product.php', {
    method: 'POST',
    body: formData
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      alert('Product added successfully!');
      location.reload();
    } else {
      alert(data.message || 'Failed to add product.');
    }
  })
  .catch(error => console.error('Error:', error));
}

// Add event listener for form submission
document.getElementById('productForm').addEventListener('submit', handleFormSubmit);
// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
  // Set initial state
  toggleProductFields();

  // Load products for dropdown if "Yes" is selected
  const productInStock = document.getElementById('productInStock');
  if (productInStock.value === 'yes') {
    fetch('get_products.php')
      .then((response) => response.json())
      .then((data) => {
        const productNameSelect = document.getElementById('productName');
        productNameSelect.innerHTML = '<option value="">Select a product</option>';
        data.products.forEach((product) => {
          const option = document.createElement('option');
          option.value = product.id;
          option.textContent = product.name;
          productNameSelect.appendChild(option);
        });
      })
      .catch((error) => console.error('Error:', error));
  }
});







  // Function to toggle modal visibility
  function toggleModal() {
    const modal = document.getElementById('categoryModal');
    modal.classList.toggle('hidden');
  }

 // Toggle Subcategory Modal
function toggleSubCategoryModal(categoryId = null) {
  const modal = document.getElementById('subCategoryModal');
  const categoryIdInput = document.getElementById('parentCategoryId');
  if (categoryId) categoryIdInput.value = categoryId; // Set parent category ID
  modal.classList.toggle('hidden');
}



// Handle Subcategory Form Submission
document.getElementById('subCategoryForm').addEventListener('submit', function (event) {
  event.preventDefault();

  const formData = new FormData(this);

  fetch('add_sub_category.php', {
    method: 'POST',
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert('Subcategory added successfully!');
        location.reload(); // Reload the page to reflect changes
      } else {
        alert(data.message || 'Failed to add subcategory.');
      }
    })
    .catch((error) => console.error('Error:', error));
});


function deleteCategory(id) {
  if (confirm("Are you sure you want to delete this category?")) {
    fetch("delete_category.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id }),
    })
      .then(response => response.json())
      .then(data => {
        if (data.status === "success") {
          alert(data.message);
          location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while deleting the category.");
      });
  }
}


function editCategory(id, currentName) {
  const newName = prompt("Enter the new category name:", currentName);

  if (newName && newName.trim() !== "") {
    fetch("edit_category.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id, name: newName.trim() }),
    })
      .then(response => {
        console.log("Response Status:", response.status); // Check the HTTP status
        return response.json(); // Parse the response
      })
      .then(data => {
        console.log("Response Data:", data); // Log the server response
        if (data.status === "success") {
          alert(data.message);
          location.reload(); // Reload to reflect changes
        } else {
          alert(data.message); // Show error message
        }
      })
      .catch(error => {
        console.error("Error:", error); // Log any network or parsing errors
        alert("An error occurred while updating the category.");
      });
  }
}



function editSubCategory(id, currentName) {
  const newName = prompt("Enter the new subcategory name:", currentName);

  if (newName && newName.trim() !== "") {
    fetch("edit_subcategory.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id, name: newName.trim() }),
    })
      .then(response => response.json())
      .then(data => {
        if (data.status === "success") {
          alert(data.message);
          location.reload(); // Reload the page to reflect changes
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while updating the subcategory.");
      });
  }
}


function deleteSubCategory(id) {
  if (confirm("Are you sure you want to delete this subcategory?")) {
    fetch("delete_subcategory.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({ id }),
    })
      .then(response => response.json())
      .then(data => {
        console.log("Response from delete_subcategory.php:", data);
        if (data.status === "success") {
          alert(data.message);
          location.reload();
        } else {
          alert(data.message);
        }
      })
      .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while deleting the subcategory.");
      });
  }
}


function openEditProductModal(productId) {
    // Show the modal
    document.getElementById('editProductModal').classList.remove('hidden');
    
    // Fetch product data using AJAX
    fetch(`get_product.php?id=${productId}`)
        .then(response => response.json())
        .then(product => {
            document.getElementById('edit_product_id').value = product.id;
            document.getElementById('edit_sub_category_id').value = product.sub_category_id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_details').value = product.details;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_preparing_time').value = product.preparing_time;
        });
}

function closeEditModal() {
    document.getElementById('editProductModal').classList.add('hidden');
}

function confirmDeleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        // Send delete request
        fetch(`delete_product.php?id=${productId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove the product element from the DOM
                const productElement = document.querySelector(`[data-product-id="${productId}"]`).parentNode.parentNode;
                productElement.remove();
            } else {
                alert('Error deleting product');
            }
        });
    }
}
document.getElementById('edit_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image_preview');
            preview.classList.remove('hidden');
            preview.querySelector('img').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

