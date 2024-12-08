<?php
require_once __DIR__ . '/fetching.php';

?>
<main class="lg:ml-64 pt-20 px-4 space-y-8">
  <div class="flex justify-between items-center">
    <h1 class="text-xl font-semibold">Manage Menu</h1>
    <button
      class="bg-green-500 text-white py-2 px-4 rounded shadow hover:bg-green-600"
      onclick="toggleModal()"
    >
      Add Category
    </button>
  </div>
<!-- Categories List -->
<div class="bg-white shadow rounded-lg p-4">
  <?php if (!empty($categories)): ?>
    <ul>
      <?php foreach ($categories as $category): ?>
        <li>
          <!-- Category Header -->
          <div class="flex justify-between items-center py-2 border-b category-header" data-id="<?= $category['id'] ?>">
            <span class="text-lg font-medium flex items-center cursor-pointer">
              <i class="fas fa-chevron-right chevron-icon me-2"></i>
              <?= htmlspecialchars($category['name']) ?>
            </span>
            <div class="space-x-2">
              <button class="bg-blue-500 text-white py-1 px-2 rounded hover:bg-blue-600"
                      onclick="toggleSubCategoryModal(<?= $category['id'] ?>)">
                <i class="fa fa-plus"></i>
              </button>
              <button class="bg-yellow-500 text-white py-1 px-2 rounded hover:bg-yellow-600"
                      onclick="editCategory(<?= $category['id'] ?>, '<?= htmlspecialchars($category['name']) ?>')">
                <i class="fa fa-edit"></i>
              </button>
              <button class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600"
                      onclick="deleteCategory(<?= $category['id'] ?>)">
                <i class="fa fa-trash"></i>
              </button>
            </div>
          </div>

          <!-- Subcategories Container (Initially Hidden) -->
          <div class="subcategories-container" id="sub-<?= $category['id'] ?>" style="display: none;">
            <?php if (!empty($subCategoriesByCategory[$category['id']])): ?>
              <ul class="ml-6 mt-2">
                <?php foreach ($subCategoriesByCategory[$category['id']] as $subCategory): ?>
                  <li>
                    <!-- Subcategory Header -->
                    <div class="flex justify-between items-center py-2 border-b subcategory-header" data-id="<?= $subCategory['id'] ?>">
                      <span class="cursor-pointer flex items-center">
                        <i class="fas fa-chevron-right chevron-icon me-2"></i>
                        <?= htmlspecialchars($subCategory['name']) ?>
                      </span>
                      <div class="space-x-2">
                        <button class="bg-green-500 text-white py-1 px-2 rounded hover:bg-green-600"
                                onclick="toggleProductModal(<?= $subCategory['id'] ?>)">
                          <i class="fa fa-cart-plus"></i>
                        </button>
                        <button class="bg-yellow-500 text-white py-1 px-2 rounded hover:bg-yellow-600"
                                onclick="editSubCategory(<?= $subCategory['id'] ?>, '<?= htmlspecialchars($subCategory['name']) ?>')">
                          <i class="fa fa-edit"></i>
                        </button>
                        <button class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600"
                                onclick="deleteSubCategory(<?= $subCategory['id'] ?>)">
                          <i class="fa fa-trash"></i>
                        </button>
                      </div>
                    </div>

                    <!-- Products Container (Initially Hidden) -->
                    <div class="products-container" id="prod-<?= $subCategory['id'] ?>" style="display: none;">
                      <?php if (!empty($menuProductBysubCategories[$subCategory['id']])): ?>
                        <ul class="ml-6 mt-1">
                          <?php foreach ($menuProductBysubCategories[$subCategory['id']] as $product): ?>
                            <li class="flex justify-between items-center py-1 border-b">
                              <span><?= htmlspecialchars($product['name']) ?></span>
                              <div class="space-x-2">
                                <button class="bg-yellow-500 text-white py-1 px-2 rounded hover:bg-yellow-600"
                                        onclick="openEditProductModal(<?= $product['id'] ?>)">
                                  <i class="fa fa-edit"></i>
                                </button>
                                <button class="bg-red-500 text-white py-1 px-2 rounded hover:bg-red-600"
                                        onclick="confirmDeleteProduct(<?= $product['id'] ?>)">
                                  <i class="fa fa-trash"></i>
                                </button>
                              </div>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      <?php else: ?>
                        <p class="text-gray-500 ml-6">No products available for this subcategory.</p>
                      <?php endif; ?>
                    </div>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="text-gray-500 ml-6">No subcategories available for this category.</p>
            <?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php else: ?>
    <p class="text-gray-500">No categories available. Add a new category to get started.</p>
  <?php endif; ?>
</div>

<script>
// Add click handlers for categories
document.querySelectorAll('.category-header').forEach(header => {
  header.querySelector('span').addEventListener('click', function() {
    const categoryId = header.dataset.id;
    const container = document.getElementById(`sub-${categoryId}`);
    const chevron = header.querySelector('.chevron-icon');
    
    container.style.display = container.style.display === 'none' ? 'block' : 'none';
    chevron.style.transform = container.style.display === 'none' ? 'rotate(0deg)' : 'rotate(90deg)';
  });
});

// Add click handlers for subcategories
document.querySelectorAll('.subcategory-header').forEach(header => {
  header.querySelector('span').addEventListener('click', function() {
    const subcategoryId = header.dataset.id;
    const container = document.getElementById(`prod-${subcategoryId}`);
    const chevron = header.querySelector('.chevron-icon');
    
    container.style.display = container.style.display === 'none' ? 'block' : 'none';
    chevron.style.transform = container.style.display === 'none' ? 'rotate(0deg)' : 'rotate(90deg)';
  });
});

// Prevent event propagation for buttons
document.querySelectorAll('button').forEach(button => {
  button.addEventListener('click', (e) => e.stopPropagation());
});
</script>

  <!-- Modal -->
  <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
      <h2 class="text-xl font-semibold mb-4">Add New Category</h2>
      <form action="add_category.php" method="POST">
        <div class="mb-4">
          <label for="categoryName" class="block text-sm font-medium text-gray-700">Category Name</label>
          <input
            type="text"
            id="categoryName"
            name="category_name"
            required
            class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          />
        </div>
        <div class="flex justify-end space-x-4">
          <button
            type="button"
            class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400"
            onclick="toggleModal()"
          >
            Cancel
          </button>
          <button
            type="submit"
            class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600"
          >
            Save
          </button>
        </div>
      </form>
    </div>
  </div>


<!-- Subcategory Modal -->
<div id="subCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
  <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
    <h2 class="text-xl font-semibold mb-4">Add New Subcategory</h2>
    <form id="subCategoryForm">
      <input type="hidden" id="parentCategoryId" name="category_id" />
      <div class="mb-4">
        <label for="subCategoryName" class="block text-sm font-medium text-gray-700">Subcategory Name</label>
        <input
          type="text"
          id="subCategoryName"
          name="sub_category_name"
          required
          class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
        />
      </div>
      <div class="flex justify-end space-x-4">
        <button
          type="button"
          class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400"
          onclick="toggleSubCategoryModal()"
        >
          Cancel
        </button>
        <button
          type="submit"
          class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600"
        >
          Save
        </button>
      </div>
    </form>
  </div>
</div>
<!-- Product Modal -->
<div id="productModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden p-4 md:p-6">
  <div class="bg-white rounded-lg shadow-lg w-full md:w-1/3 max-h-[90vh] flex flex-col">
    <!-- Fixed Header -->
    <div class="p-6 border-b">
      <h2 class="text-xl font-semibold">Add New Product</h2>
    </div>
    <input type="hidden" id="subCategoryId" name="sub_category_id" value="1" />

    <!-- Scrollable Content -->
    <div class="p-6 overflow-y-auto flex-1">
      <form id="productForm">
        <input type="hidden" id="categoryId" name="category_id" />

        <!-- Ask if product is in stock -->
        <div class="mb-4">
          <label for="productInStock" class="block text-sm font-medium text-gray-700">Is the product in stock?</label>
          <select
            id="productInStock"
            name="product_in_stock"
            class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            onchange="toggleProductFields()"
          >
            <option value="yes">Yes</option>
            <option value="no">No</option>
          </select>
        </div>

        <!-- Product selection if in stock -->
        <div id="productSelection" class="mb-4 hidden">
          <label for="productName" class="block text-sm font-medium text-gray-700">Select Product</label>
          <select
            id="productName"
            name="product_name"
            class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            onchange="autoFillProductDetails()"
          >
            <!-- Product options will be populated dynamically -->
          </select>
        </div>

        <!-- Fields to fill if product is not in stock -->
        <div id="productFields" class="mb-4">
          <label for="productName" class="block text-sm font-medium text-gray-700" require>Product Name</label>
          <input
            type="text"
            id="productNameField"
            name="product_name"
            required
            class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          />

          <label for="productDetails" class="block text-sm font-medium text-gray-700 mt-4" require>Product Details</label>
          <textarea
            id="productDetails"
            name="product_details"
            required
            class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          ></textarea>

          <div class="mb-4">
            <label for="productImage" class="block text-sm font-medium text-gray-700 mt-4" >Product Image</label>
            <input type="hidden" id="existingImage" name="existing_image" />
            <div id="imagePreview" class="mt-2 hidden">
              <img id="existingImagePreview" src="" alt="Product Image" class="max-w-xs h-auto" />
            </div>
            <input
              type="file"
              id="productImage"
              name="product_image"
              class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
            />
          </div>

          <label for="preparingTime" class="block text-sm font-medium text-gray-700 mt-4" require>Preparing Time (in minutes)</label>
          <input
            type="number"
            id="preparingTime"
            name="preparing_time"
            required
            class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          />

          <label for="price" class="block text-sm font-medium text-gray-700 mt-4" require>Price</label>
          <input
            type="number"
            id="price"
            name="price"
            required
            class="w-full mt-1 p-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
          />
        </div>
        <input type="hidden" id="subCategoryId" name="sub_category_id" value=" <?= $subCategory['id'] ?>" />
      </form>
    </div>

    <!-- Fixed Footer -->
    <div class="p-6 border-t bg-white">
      <div class="flex justify-end space-x-4">
        <button
          type="button"
          class="bg-gray-300 text-gray-800 py-2 px-4 rounded hover:bg-gray-400"
          onclick="toggleProductModal()"
        >
          Cancel
        </button>
        <button
          type="submit"
          form="productForm"
          class="bg-green-500 text-white py-2 px-4 rounded hover:bg-green-600"
        >
          Save
        </button>
      </div>
    </div>
  </div>
</div>
<div id="editProductModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <!-- Changed width and added margin-left to avoid sidebar -->
    <div class="relative top-10 mx-auto p-4 border w-[700px] shadow-lg rounded-md bg-white ml-[360px]">
        <div class="mt-2">
            <!-- Reduced margin-bottom -->
            <div class="flex justify-between items-center mb-4">
                <!-- Reduced text size -->
                <h3 class="text-lg font-semibold text-gray-900">Edit Product</h3>
                <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <form id="editProductForm" method="POST" action="update_product.php" enctype="multipart/form-data">
                <input type="hidden" id="edit_product_id" name="product_id">
                
                <!-- Reduced gap -->
                <div class="grid grid-cols-2 gap-4">
                    <!-- Left Column -->
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <!-- Reduced text size and margin -->
                        <h4 class="text-base font-medium text-gray-700 mb-3">Basic Information</h4>
                        
                        <!-- Reduced spacing -->
                        <div class="space-y-3">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Sub Category</label>
                                <input type="text" id="edit_sub_category_id" name="sub_category_id" readonly
                                    class="bg-gray-100 shadow appearance-none border rounded w-full py-1.5 px-2 text-sm text-gray-700">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Name</label>
                                <input type="text" id="edit_name" name="name" readonly
                                    class="bg-gray-100 shadow appearance-none border rounded w-full py-1.5 px-2 text-sm text-gray-700">
                            </div>

                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Details</label>
                                <textarea id="edit_details" name="details" readonly rows="3"
                                    class="shadow appearance-none border rounded w-full py-1.5 px-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="bg-gray-50 p-3 rounded-lg">
                        <h4 class="text-base font-medium text-gray-700 mb-3">Additional Details</h4>
                        
                        <div class="space-y-3">
                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Price</label>
                                <div class="relative">
                                    <span class="absolute left-2 top-1.5 text-gray-600 text-sm">$</span>
                                    <input type="number" id="edit_price" name="price"
                                        class="pl-6 shadow appearance-none border rounded w-full py-1.5 px-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Preparing Time</label>
                                <div class="relative">
                                    <input type="text" id="edit_preparing_time" name="preparing_time"
                                        class="shadow appearance-none border rounded w-full py-1.5 px-2 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <span class="absolute right-2 top-1.5 text-gray-600 text-sm">minutes</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-gray-700 text-xs font-bold mb-1">Image</label>
                                <div class="mt-1 flex items-center">
                                    <div class="w-full">
                                        <label class="cursor-pointer flex items-center justify-center px-3 py-1.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                            <span>Upload Image</span>
                                            <input type="file" id="edit_image" name="image" class="sr-only">
                                        </label>
                                    </div>
                                </div>
                                <div id="image_preview" class="mt-2 hidden">
                                    <img src="" alt="Preview" class="h-24 w-24 object-cover rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="mt-4 flex justify-end space-x-2 border-t pt-3">
                    <button type="button" onclick="closeEditModal()"
                        class="bg-gray-500 text-white px-4 py-1.5 rounded-md hover:bg-gray-600 transition-colors text-sm">
                        Cancel
                    </button>
                    <button type="submit"
                        class="bg-blue-500 text-white px-4 py-1.5 rounded-md hover:bg-blue-600 transition-colors text-sm">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>





</main>
<script src="menu.js"></script>

