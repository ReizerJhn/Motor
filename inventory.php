<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorParts Dashboard - Inventory Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        .sidebar {
            transition: transform 0.2s ease-in-out;
        }
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
        }
    </style>
</head>
<body class="bg-gray-900 text-white">
    <div class="flex flex-col md:flex-row h-screen">
      
        <?php include 'sidebar.php'; ?>
        
        <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-10 hidden md:hidden"></div>

        <main class="flex-1 p-4 md:p-6 overflow-auto ml-0 md:ml-64">
            <button id="menuBtn" class="md:hidden p-2 text-white z-30">
                <i data-lucide="menu"></i>
            </button>
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <h1 class="text-2xl font-bold mb-4 md:mb-0">Inventory Management</h1>
                <div class="flex items-center space-x-4">
                    <form id="searchForm" class="relative w-full md:w-auto">
                        <input
                            type="text"
                            placeholder="Search..."
                            id="searchInput"
                            class="w-full md:w-auto bg-gray-800 text-white rounded-full py-2 px-4 pl-10 focus:outline-none focus:ring-2 focus:ring-purple-600"
                        />
                        <i data-lucide="search" class="absolute left-3 top-2.5 text-gray-400"></i>
                    </form>
                    <button class="relative">
                        <i data-lucide="bell" class="text-gray-400 hover:text-white transition-colors"></i>
                        <span class="absolute -top-1 -right-1 bg-red-500 rounded-full w-4 h-4 text-xs flex items-center justify-center">3</span>
                    </button>
                    <button>
                        <i data-lucide="database" class="text-gray-400 hover:text-white transition-colors"></i>
                    </button>
                </div>
            </div>

            <div class="bg-gray-800 p-4 md:p-6 rounded-lg">
                <div id="errorMessage" class="bg-red-500 text-white p-4 rounded-lg mb-4 hidden"></div>
                <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Inventory Items</h2>
                    
                    <!-- Filter section -->
                    <div class="flex-1 mx-4 flex space-x-2">
                        <select id="categoryFilter" class="bg-gray-700 text-white rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-600">
                            <option value="">All Categories</option>
                        </select>
                        <select id="stockFilter" class="bg-gray-700 text-white rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-600">
                            <option value="">All Stock Status</option>
                            <option value="in_stock">In Stock</option>
                            <option value="low_stock">Low Stock</option>
                            <option value="out_of_stock">Out of Stock</option>
                        </select>
                        <select id="supplierFilter" class="bg-gray-700 text-white rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-600">
                            <option value="">All Suppliers</option>
                        </select>
                        <select id="brandFilter" class="bg-gray-700 text-white rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-purple-600">
                            <option value="">All Brands</option>
                        </select>
                    </div>

                    <!-- Action buttons -->
                    <div class="flex space-x-2">
                        <button id="addItemBtn" class="bg-purple-600 hover:bg-purple-700 py-2 px-4 rounded-md flex items-center">
                            <i data-lucide="plus" class="mr-2"></i> Add New Item
                        </button>
                        <button id="addCategoryBtn" class="bg-green-600 hover:bg-green-700 py-2 px-4 rounded-md flex items-center">
                            <i data-lucide="folder-plus" class="mr-2"></i> Add Category
                        </button>
                        <button id="addBrandBtn" class="bg-blue-600 hover:bg-blue-700 py-2 px-4 rounded-md flex items-center">
                            <i data-lucide="tag" class="mr-2"></i> Add Brand
                        </button>
                    </div>
                </div>
                
                <!-- Add this section for inventory status -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Total Items</h3>
                        <p id="totalItems" class="text-2xl font-bold">0</p>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Low Stock</h3>
                        <p id="lowStockCount" class="text-2xl font-bold text-yellow-500">0</p>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Out of Stock</h3>
                        <p id="outOfStockCount" class="text-2xl font-bold text-red-500">0</p>
                    </div>
                    <div class="bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Total Value</h3>
                        <p id="totalValue" class="text-2xl font-bold">₱0.00</p>
                    </div>
                </div>
                <!-- End of inventory status section -->

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left py-3 px-4 text-white">Name</th>
                                <th class="text-left py-3 px-4 text-white">SKU</th>
                                <th class="text-left py-3 px-4 text-white">Category</th>
                                <th class="text-left py-3 px-4 text-white">Quantity</th>
                                <th class="text-left py-3 px-4 text-white">Unit</th>
                                <th class="text-left py-3 px-4 text-white">Purchase Price</th>
                                <th class="text-left py-3 px-4 text-white">Selling Price</th>
                                <th class="text-left py-3 px-4 text-white">Supplier ID</th>
                                <th class="text-left py-3 px-4 text-white">Reorder Level</th>
                                <th class="text-left py-3 px-4 text-white">Date Added</th>
                                <th class="text-left py-3 px-4 text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inventoryTableBody">
                            <!-- Inventory items will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Item Modal -->
    <div id="addItemModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-[90vw] max-w-4xl max-h-[90vh] overflow-y-auto" 
             onclick="event.stopPropagation()">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Add New Inventory Item</h2>
            <form id="addItemForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Item Name</label>
                        <input type="text" id="itemName" name="name" required 
                            class="w-full p-2 border rounded text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">SKU</label>
                        <input type="text" id="itemSKU" name="sku" required 
                            class="w-full p-2 border rounded text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <select id="itemCategory" name="category_id" required 
                            class="w-full p-2 border rounded text-gray-900">
                            <option value="">Select Category</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Quantity</label>
                        <input type="number" id="itemQuantity" name="quantity" required min="0"
                            class="w-full p-2 border rounded text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Unit</label>
                        <select id="itemUnit" name="unit" required 
                            class="w-full p-2 border rounded text-gray-900">
                            <option value="">Select Unit</option>
                            <option value="piece">Piece</option>
                            <option value="set">Set</option>
                            <option value="unit">Unit</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Purchase Price</label>
                        <input type="number" id="itemPurchasePrice" name="purchase_price" required min="0" step="0.01"
                            class="w-full p-2 border rounded text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Selling Price</label>
                        <input type="number" id="itemSellingPrice" name="selling_price" required min="0" step="0.01"
                            class="w-full p-2 border rounded text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Supplier</label>
                        <select id="itemSupplierID" name="supplier_id" required 
                            class="w-full p-2 border rounded text-gray-900">
                            <option value="">Select Supplier</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Brand</label>
                        <select id="itemBrand" name="brand_id" required 
                            class="w-full p-2 border rounded text-gray-900">
                            <option value="">Select Brand</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reorder Level</label>
                        <input type="number" id="itemReorderLevel" name="reorder_level" required min="0"
                            class="w-full p-2 border rounded text-gray-900">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Product Image</label>
                    <div id="dropArea" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer">
                        <p class="text-gray-600">Drag & drop an image here, or click to select</p>
                        <input type="file" id="itemImage" name="image" accept="image/*" class="hidden">
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="submit" class="bg-purple-600 text-white p-2 rounded hover:bg-purple-700">Add Item</button>
                    <button type="button" id="closeAddItemModal" class="bg-gray-300 text-gray-800 p-2 rounded hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Item Modal -->
    <div id="editItemModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-[90vw] max-w-4xl max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Edit Inventory Item</h2>
            <form id="editItemForm" class="space-y-4">
                <input type="hidden" id="editItemId" name="id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" id="editItemName" name="name" placeholder="Item Name" class="w-full p-2 border rounded text-gray-900">
                    <input type="text" id="editItemSKU" name="sku" placeholder="SKU" class="w-full p-2 border rounded text-gray-900">
                    <select id="editItemCategory" name="category" required class="w-full p-2 border rounded text-gray-900">
                        <option value="">Select Category</option>
                    </select>
                    <input type="number" id="editItemQuantity" name="quantity" placeholder="Quantity" class="w-full p-2 border rounded text-gray-900">
                    <select id="editItemUnit" name="unit" required class="w-full p-2 border rounded text-gray-900">
                        <option value="">Select Unit</option>
                        <option value="piece">By Piece</option>
                        <option value="set">By Set</option>
                    </select>
                    <input type="number" id="editItemPurchasePrice" name="purchase_price" placeholder="Purchase Price" step="0.01" class="w-full p-2 border rounded text-gray-900">
                    <input type="number" id="editItemSellingPrice" name="selling_price" placeholder="Selling Price" step="0.01" class="w-full p-2 border rounded text-gray-900">
                    <select id="editItemSupplierID" name="supplier_id" required 
                        class="w-full p-2 border rounded text-gray-900">
                        <option value="">Select Supplier</option>
                    </select>
                    <input type="number" id="editItemReorderLevel" name="reorder_level" placeholder="Reorder Level" class="w-full p-2 border rounded text-gray-900">
                    <select id="editItemBrand" name="brand_id" required 
                        class="w-full p-2 border rounded text-gray-900">
                        <option value="">Select Brand</option>
                    </select>
                </div>
                <div id="editDropArea" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer">
                    <p>Drag & drop an image here, or click to select</p>
                    <input type="file" id="editItemImage" name="image" accept="image/*" class="hidden">
                </div>
                <textarea id="editItemDescription" name="description" placeholder="Item Description" class="w-full p-2 border rounded text-gray-900"></textarea>
                <div class="flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Update Item</button>
                    <button type="button" id="closeEditItemModal" class="bg-gray-300 text-gray-800 p-2 rounded hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Item Details Modal -->
    <div id="itemDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-[90vw] max-w-3xl max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Product Details</h2>
            <div id="itemDetailsContent" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Item details will be dynamically inserted here -->
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button id="updateItemBtn" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update Item</button>
                <button id="deleteItemBtn" class="bg-red-500 text-white p-2 rounded hover:bg-red-600">Delete Item</button>
                <button id="closeItemDetailsModal" class="bg-gray-300 text-gray-800 p-2 rounded hover:bg-gray-400">Close</button>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="addCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-[90vw] max-w-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Add New Category</h2>
            <form id="addCategoryForm" class="space-y-4">
                <div>
                    <label for="categoryName" class="block text-sm font-medium text-gray-700">Category Name</label>
                    <input type="text" id="categoryName" name="name" required 
                        class="mt-1 block w-full p-2 border rounded text-gray-900">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="submit" class="bg-green-600 text-white p-2 rounded hover:bg-green-700">Add Category</button>
                    <button type="button" id="closeCategoryModal" class="bg-gray-300 text-gray-800 p-2 rounded hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Brand Modal -->
    <div id="addBrandModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-[90vw] max-w-md">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Add New Brand</h2>
            <form id="addBrandForm" class="space-y-4">
                <div>
                    <label for="brandName" class="block text-sm font-medium text-gray-700">Brand Name</label>
                    <input type="text" id="brandName" name="name" required 
                        class="mt-1 block w-full p-2 border rounded text-gray-900">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Add Brand</button>
                    <button type="button" id="closeBrandModal" class="bg-gray-300 text-gray-800 p-2 rounded hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // DOM elements
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuBtn = document.getElementById('menuBtn');
        const logoutBtn = document.getElementById('logoutBtn');
        const searchForm = document.getElementById('searchForm');
        const searchInput = document.getElementById('searchInput');
        const inventoryTableBody = document.getElementById('inventoryTableBody');
        const addItemBtn = document.getElementById('addItemBtn');
        const addItemModal = document.getElementById('addItemModal');
        const closeAddItemModal = document.getElementById('closeAddItemModal');
        const addItemForm = document.getElementById('addItemForm');
        const itemDetailsModal = document.getElementById('itemDetailsModal');
        const itemDetailsContent = document.getElementById('itemDetailsContent');
        const closeItemDetailsModal = document.getElementById('closeItemDetailsModal');
        const updateItemBtn = document.getElementById('updateItemBtn');
        const deleteItemBtn = document.getElementById('deleteItemBtn');
        const errorMessage = document.getElementById('errorMessage');
        const addCategoryBtn = document.getElementById('addCategoryBtn');
        const addCategoryModal = document.getElementById('addCategoryModal');
        const closeCategoryModal = document.getElementById('closeCategoryModal');
        const addCategoryForm = document.getElementById('addCategoryForm');
        const itemCategorySelect = document.getElementById('itemCategory');
        const addBrandBtn = document.getElementById('addBrandBtn');
        const addBrandModal = document.getElementById('addBrandModal');
        const closeBrandModal = document.getElementById('closeBrandModal');
        const addBrandForm = document.getElementById('addBrandForm');

        let items = [];
        let selectedItem = null;

        // Toggle sidebar
        menuBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('hidden');
        });

        // Close sidebar when clicking outside
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('open');
            overlay.classList.add('hidden');
        });

        // Logout functionality
        logoutBtn.addEventListener('click', () => {
            console.log('Logging out...');
            // Implement logout logic here
        });

        // Search functionality
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            console.log('Searching for:', searchInput.value);
            // Implement search logic here
        });

        // Fetch inventory items
        async function fetchInventory() {
            try {
                const response = await fetch('fetch_inventory_data.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                console.log('Fetched data:', data); // Debug log

                if (data.error) {
                    throw new Error(data.error);
                }
                
                // Store items globally
                items = data.products || [];
                
                // Update inventory status
                updateInventoryStatus(data.inventoryStatus);
                
                // Render inventory table
                renderInventoryTable();
            } catch (error) {
                console.error('Error fetching inventory:', error);
                showError('Error fetching inventory: ' + error.message);
            }
        }

        // Render inventory table
        function renderInventoryTable() {
            if (!Array.isArray(items)) {
                console.error('Items is not an array:', items);
                return;
            }

            inventoryTableBody.innerHTML = items.map(item => `
                <tr class="border-b border-gray-700 hover:bg-gray-700" data-id="${item.id}">
                    <td class="py-2 px-4">${item.name || ''}</td>
                    <td class="py-2 px-4">${item.sku || ''}</td>
                    <td class="py-2 px-4">${Array.isArray(item.categories) ? item.categories.join(', ') : ''}</td>
                    <td class="py-2 px-4 ${getQuantityColorClass(parseInt(item.quantity), parseInt(item.reorder_level))}">${item.quantity || 0}</td>
                    <td class="py-2 px-4">${item.unit || ''}</td>
                    <td class="py-2 px-4">₱${parseFloat(item.purchase_price || 0).toFixed(2)}</td>
                    <td class="py-2 px-4">₱${parseFloat(item.selling_price || 0).toFixed(2)}</td>
                    <td class="py-2 px-4">${item.supplier_name || ''}</td>
                    <td class="py-2 px-4">${item.reorder_level || 0}</td>
                    <td class="py-2 px-4">${formatDate(item.date_added)}</td>
                    <td class="py-2 px-4">
                        <div class="flex space-x-2">
                            <button class="edit-btn text-blue-500 hover:text-blue-600">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </button>
                            <button class="delete-btn text-red-500 hover:text-red-600">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
            
            lucide.createIcons();
            addInventoryEventListeners();
        }

        // Add this helper function for quantity color coding
        function getQuantityColorClass(quantity, reorderLevel) {
            if (quantity === 0) {
                return 'text-red-500 font-bold'; // Out of stock
            } else if (quantity <= reorderLevel) {
                return 'text-yellow-500 font-bold'; // Low stock
            }
            return 'text-white'; // Normal stock
        }

        // Update the inventory status display function
        function updateInventoryStatus(status) {
            if (!status) return;
            
            document.getElementById('totalItems').textContent = status.totalItems || 0;
            document.getElementById('lowStockCount').textContent = status.lowStockCount || 0;
            document.getElementById('outOfStockCount').textContent = status.outOfStockCount || 0;
            document.getElementById('totalValue').textContent = `₱${parseFloat(status.totalValue || 0).toFixed(2)}`;
        }

        // Update the formatDate function to match the image format
        function formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            return date.toISOString().slice(0, 19).replace('T', ' ');
        }

        // Add this debug function
        function debugInventoryData() {
            console.log('Current items:', items);
            console.log('Sample item:', items[0]);
        }

        // Add new item
        addItemForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(addItemForm);
            formData.append('action', 'add_item');

            // Get selected category ID
            const categorySelect = document.getElementById('itemCategory');
            const selectedCategory = categorySelect.value;
            
            if (!selectedCategory) {
                showError('Please select a category');
                return;
            }

            // Ensure we're sending the category ID as an array
            formData.append('category_ids', JSON.stringify([parseInt(selectedCategory)]));

            try {
                const response = await fetch('inventory_api.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error('Failed to add item');
                }
                
                const result = await response.json();
                if (result.error) {
                    throw new Error(result.error);
                }
                
                await fetchInventory();
                addItemModal.classList.add('hidden');
                addItemModal.classList.remove('flex');
                addItemForm.reset();
                showSuccess('Item added successfully');
            } catch (error) {
                console.error('Error adding item:', error);
                showError('Error adding item: ' + error.message);
            }
        });

        // Open item details modal
        function openItemDetailsModal(itemId) {
            selectedItem = items.find(item => item.id === parseInt(itemId));
            if (selectedItem) {
                itemDetailsContent.innerHTML = `
                    <div>
                        <img src="${getImagePath(selectedItem.image)}" alt="${selectedItem.name}" class="w-full h-auto rounded-lg">
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-900">${selectedItem.name}</h3>
                        <p class="text-gray-500 mb-4">Brand: ${selectedItem.brand}</p>
                        <p class="mb-4 text-gray-700">${selectedItem.description}</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500">SKU</p>
                                <p class="text-gray-900">${selectedItem.sku}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Category</p>
                                <p class="text-gray-900">${selectedItem.category}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Quantity</p>
                                <p class="text-gray-900">${selectedItem.quantity} ${selectedItem.unit}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Reorder Level</p>
                                <p class="text-gray-900">${selectedItem.reorder_level}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Purchase Price</p>
                                <p class="text-gray-900">₱${parseFloat(selectedItem.purchase_price).toFixed(2)}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Selling Price</p>
                                <p class="text-gray-900">₱${parseFloat(selectedItem.selling_price).toFixed(2)}</p>
                            </div>
                            <div class="col-span-2">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Supplier Information</h4>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-gray-500">Supplier Name</p>
                                        <p class="text-gray-900">${selectedItem.supplier_name || 'Not assigned'}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Contact Person</p>
                                        <p class="text-gray-900">${selectedItem.contact_name || 'N/A'}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Email</p>
                                        <p class="text-gray-900">${selectedItem.email || 'N/A'}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Phone</p>
                                        <p class="text-gray-900">${selectedItem.phone || 'N/A'}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                itemDetailsModal.classList.remove('hidden');
                itemDetailsModal.classList.add('flex');
            }
        }

        // Close item details modal
        closeItemDetailsModal.addEventListener('click', () => {
            itemDetailsModal.classList.add('hidden');
            itemDetailsModal.classList.remove('flex');
            selectedItem = null;
        });

        // Update item
        updateItemBtn.addEventListener('click', async () => {
            if (!selectedItem) return;

            const formData = new FormData();
            formData.append('action', 'update_item');
            formData.append('id', selectedItem.id);

            // Append all form fields from the modal
            for (const [key, value] of Object.entries(selectedItem)) {
                formData.append(key, value);
            }

            try {
                const response = await fetch('inventory_api.php', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) {
                    throw new Error('Failed to update item');
                }
                const result = await response.json();
                if (result.error) {
                    throw new Error(result.error);
                }
                await fetchInventory();
                itemDetailsModal.classList.add('hidden');
                itemDetailsModal.classList.remove('flex');
                selectedItem = null;
                showSuccess('Item updated successfully');
            } catch (error) {
                console.error('Error updating item:', error);
                showError('Error updating item: ' + error.message);
            }
        });

        // Delete item
        async function deleteItem(itemId) {
            if (!confirm('Are you sure you want to delete this item?')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'delete_item');
                formData.append('id', itemId);

                const response = await fetch('inventory_api.php', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) {
                    throw new Error('Failed to delete item');
                }
                const result = await response.json();
                if (result.error) {
                    throw new Error(result.error);
                }
                
                await fetchInventory();
                showSuccess('Item deleted successfully');
            } catch (error) {
                console.error('Error deleting item:', error);
                showError('Error deleting item: ' + error.message);
            }
        }

        deleteItemBtn.addEventListener('click', () => {
            if (selectedItem) {
                deleteItem(selectedItem.id);
            }
        });

        // Helper function to get image path
        function getImagePath(imagePath) {
            if (imagePath.startsWith('http')) {
                return imagePath;
            }
            return '/images/sample.png'; // Fallback image
        }

        // Show error message
        function showError(message) {
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = message;
            errorMessage.classList.remove('hidden');
            setTimeout(() => errorMessage.classList.add('hidden'), 5000);
        }

        // Helper function to format date
        function formatDate(dateString) {
            const options = { year: 'numeric', month: 'short', day: 'numeric' };
            return new Date(dateString).toLocaleDateString(undefined, options);
        }

        // Show success message
        function showSuccess(message) {
            const successMessage = document.createElement('div');
            successMessage.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded shadow-lg z-50';
            successMessage.textContent = message;
            document.body.appendChild(successMessage);
            setTimeout(() => successMessage.remove(), 3000);
        }

        // DOM elements for edit modal
        const editItemModal = document.getElementById('editItemModal');
        const editItemForm = document.getElementById('editItemForm');
        const closeEditItemModal = document.getElementById('closeEditItemModal');

        // Function to open edit modal
        function openEditItemModal(itemId) {
            const item = items.find(item => item.id === parseInt(itemId));
            if (item) {
                document.getElementById('editItemId').value = item.id;
                document.getElementById('editItemName').value = item.name;
                document.getElementById('editItemSKU').value = item.sku;
                document.getElementById('editItemCategory').value = item.category;
                document.getElementById('editItemQuantity').value = item.quantity;
                document.getElementById('editItemUnit').value = item.unit;
                document.getElementById('editItemPurchasePrice').value = item.purchase_price;
                document.getElementById('editItemSellingPrice').value = item.selling_price;
                document.getElementById('editItemSupplierID').value = item.supplier_name;
                document.getElementById('editItemReorderLevel').value = item.reorder_level;
                document.getElementById('editItemDescription').value = item.description;
                document.getElementById('editItemBrand').value = item.brand;

                // Update the edit drop area text if an image exists
                const editDropArea = document.getElementById('editDropArea');
                const editDropAreaText = editDropArea.querySelector('p');
                if (item.image) {
                    editDropAreaText.textContent = `Current image: ${item.image}`;
                } else {
                    editDropAreaText.textContent = 'Drag & drop an image here, or click to select';
                }

                editItemModal.classList.remove('hidden');
                editItemModal.classList.add('flex');
            }
        }

        // Close edit modal
        closeEditItemModal.addEventListener('click', () => {
            editItemModal.classList.add('hidden');
            editItemModal.classList.remove('flex');
        });

        // Handle edit form submission
        editItemForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(editItemForm);
            formData.append('action', 'update_item');

            try {
                const response = await fetch('inventory_api.php', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) {
                    throw new Error('Failed to update item');
                }
                const result = await response.json();
                if (result.error) {
                    throw new Error(result.error);
                }
                await fetchInventory();
                editItemModal.classList.add('hidden');
                editItemModal.classList.remove('flex');
                showSuccess('Item updated successfully');
            } catch (error) {
                console.error('Error updating item:', error);
                showError('Error updating item: ' + error.message);
            }
        });

        // File drag and drop functionality
        const dropArea = document.getElementById('dropArea');
        const fileInput = document.getElementById('itemImage');
        const editDropArea = document.getElementById('editDropArea');
        const editFileInput = document.getElementById('editItemImage');

        function setupDragAndDrop(dropArea, fileInput) {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropArea.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropArea.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropArea.classList.add('bg-gray-100');
            }

            function unhighlight(e) {
                dropArea.classList.remove('bg-gray-100');
            }

            dropArea.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                fileInput.files = files;
                updateDropAreaText(dropArea, files[0].name);
            }

            dropArea.addEventListener('click', () => fileInput.click());

            fileInput.addEventListener('change', () => {
                if (fileInput.files.length > 0) {
                    updateDropAreaText(dropArea, fileInput.files[0].name);
                }
            });
        }

        function updateDropAreaText(dropArea, fileName) {
            const p = dropArea.querySelector('p');
            p.textContent = `File selected: ${fileName}`;
        }

        setupDragAndDrop(dropArea, fileInput);
        setupDragAndDrop(editDropArea, editFileInput);

        // Initial fetch
        fetchInventory();
        debugInventoryData();
        fetchCategories();
        fetchSuppliers();
        fetchBrands();

        // Open category modal
        addCategoryBtn.addEventListener('click', () => {
            addCategoryModal.classList.remove('hidden');
            addCategoryModal.classList.add('flex');
        });

        // Close category modal
        closeCategoryModal.addEventListener('click', () => {
            addCategoryModal.classList.add('hidden');
            addCategoryModal.classList.remove('flex');
        });

        // Handle category form submission
        addCategoryForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(addCategoryForm);
            formData.append('action', 'add_category');

            try {
                const response = await fetch('inventory_api.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                if (result.error) {
                    throw new Error(result.error);
                }
                
                await fetchCategories(); // Refresh the categories list
                addCategoryModal.classList.add('hidden');
                addCategoryModal.classList.remove('flex');
                addCategoryForm.reset();
                showSuccess('Category added successfully');
            } catch (error) {
                console.error('Error adding category:', error);
                showError('Error adding category: ' + error.message);
            }
        });

        // Fetch categories
        async function fetchCategories() {
            try {
                const response = await fetch('inventory_api.php?action=get_categories');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                const categories = data.categories;
                const categoryOptions = categories.map(cat => `
                    <option value="${cat.id}">${cat.name}</option>
                `).join('');
                
                // Update all category dropdowns including filter
                ['itemCategory', 'editItemCategory', 'categoryFilter'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        const defaultText = id === 'categoryFilter' ? 'All Categories' : 'Select Category';
                        element.innerHTML = `<option value="">${defaultText}</option>${categoryOptions}`;
                    }
                });
                
            } catch (error) {
                console.error('Error fetching categories:', error);
                showError('Error fetching categories: ' + error.message);
            }
        }

        // Call fetchCategories when page loads
        document.addEventListener('DOMContentLoaded', fetchCategories);

        // Fetch suppliers
        async function fetchSuppliers() {
            try {
                const response = await fetch('inventory_api.php?action=get_suppliers');
                if (!response.ok) {
                    throw new Error('Failed to fetch suppliers');
                }
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                
                const suppliers = data.suppliers;
                const supplierOptions = suppliers.map(sup => `
                    <option value="${sup.id}">${sup.name}</option>
                `).join('');
                
                // Update all supplier dropdowns including filter
                ['itemSupplierID', 'editItemSupplierID', 'supplierFilter'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        const defaultText = id === 'supplierFilter' ? 'All Suppliers' : 'Select Supplier';
                        element.innerHTML = `<option value="">${defaultText}</option>${supplierOptions}`;
                    }
                });
                
            } catch (error) {
                console.error('Error fetching suppliers:', error);
                showError('Error fetching suppliers: ' + error.message);
            }
        }

        // Call fetchSuppliers along with other initial fetches
        document.addEventListener('DOMContentLoaded', () => {
            fetchInventory();
            fetchCategories();
            fetchSuppliers();
        });

        // Open brand modal
        addBrandBtn.addEventListener('click', () => {
            addBrandModal.classList.remove('hidden');
            addBrandModal.classList.add('flex');
        });

        // Close brand modal
        closeBrandModal.addEventListener('click', () => {
            addBrandModal.classList.add('hidden');
            addBrandModal.classList.remove('flex');
        });

        // Handle brand form submission
        addBrandForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(addBrandForm);
            formData.append('action', 'add_brand');

            try {
                const response = await fetch('inventory_api.php', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) {
                    throw new Error('Failed to add brand');
                }
                const result = await response.json();
                if (result.error) {
                    throw new Error(result.error);
                }
                await fetchBrands();
                addBrandModal.classList.add('hidden');
                addBrandModal.classList.remove('flex');
                addBrandForm.reset();
                showSuccess('Brand added successfully');
            } catch (error) {
                console.error('Error adding brand:', error);
                showError('Error adding brand. Please try again.');
            }
        });

        // Fetch brands
        async function fetchBrands() {
            try {
                const response = await fetch('inventory_api.php?action=get_brands');
                if (!response.ok) {
                    throw new Error('Failed to fetch brands');
                }
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                
                const brands = data.brands;
                const brandOptions = brands.map(brand => `
                    <option value="${brand.id}">${brand.name}</option>
                `).join('');
                
                // Update all brand dropdowns including filter
                ['itemBrand', 'editItemBrand', 'brandFilter'].forEach(id => {
                    const element = document.getElementById(id);
                    if (element) {
                        const defaultText = id === 'brandFilter' ? 'All Brands' : 'Select Brand';
                        element.innerHTML = `<option value="">${defaultText}</option>${brandOptions}`;
                    }
                });
                
            } catch (error) {
                console.error('Error fetching brands:', error);
                showError('Error fetching brands: ' + error.message);
            }
        }

        // Add fetchBrands to the initial load
        document.addEventListener('DOMContentLoaded', () => {
            fetchInventory();
            fetchCategories();
            fetchSuppliers();
            fetchBrands();
        });

        // Add search functionality
        searchInput.addEventListener('input', (e) => {
            const searchTerm = e.target.value.toLowerCase();
            const filteredItems = items.filter(item => 
                item.name.toLowerCase().includes(searchTerm) ||
                item.sku.toLowerCase().includes(searchTerm) ||
                (item.categories && item.categories.some(cat => cat.toLowerCase().includes(searchTerm)))
            );
            
            renderFilteredInventory(filteredItems);
        });

        function renderFilteredInventory(filteredItems) {
            const currentItems = filteredItems || items;
            renderInventoryTable(currentItems);
        }

        // Add inventory event listeners
        function addInventoryEventListeners() {
            // Add click listeners for edit buttons
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const itemId = e.target.closest('tr').dataset.id;
                    openEditItemModal(itemId);
                });
            });

            // Add click listeners for delete buttons
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const itemId = e.target.closest('tr').dataset.id;
                    deleteItem(itemId);
                });
            });

            // Add click listeners for rows to show details
            document.querySelectorAll('tr[data-id]').forEach(row => {
                row.addEventListener('click', (e) => {
                    if (!e.target.closest('button')) {  // Only if not clicking a button
                        const itemId = row.dataset.id;
                        openItemDetailsModal(itemId);
                    }
                });
            });
        }

        // Replace or add these event listeners for the Add Item Modal
        document.addEventListener('DOMContentLoaded', function() {
            // Add Item Modal functionality
            const addItemBtn = document.getElementById('addItemBtn');
            const addItemModal = document.getElementById('addItemModal');
            const closeAddItemModal = document.getElementById('closeAddItemModal');
            const addItemForm = document.getElementById('addItemForm');

            // Open modal and fetch categories
            addItemBtn.addEventListener('click', async () => {
                console.log('Opening add item modal'); // Debug log
                await fetchCategories(); // Wait for categories to be fetched
                addItemModal.classList.remove('hidden');
                addItemModal.classList.add('flex');
            });

            // Close modal
            closeAddItemModal.addEventListener('click', () => {
                console.log('Closing add item modal'); // Debug log
                addItemModal.classList.add('hidden');
                addItemModal.classList.remove('flex');
                addItemForm.reset();
            });

            // Close modal when clicking outside
            addItemModal.addEventListener('click', (e) => {
                if (e.target === addItemModal) {
                    addItemModal.classList.add('hidden');
                    addItemModal.classList.remove('flex');
                    addItemForm.reset();
                }
            });

            // Initialize other event listeners
            fetchInventory();
            fetchCategories();
            fetchSuppliers();
            fetchBrands();
        });

        // Add these filter functions after your existing JavaScript code
        function initializeFilters() {
            const filters = ['categoryFilter', 'stockFilter', 'supplierFilter', 'brandFilter'];
            filters.forEach(filterId => {
                const element = document.getElementById(filterId);
                if (element) {
                    element.addEventListener('change', () => {
                        console.log(`${filterId} changed`);
                        applyFilters();
                    });
                }
            });
            
            searchInput.addEventListener('input', () => {
                console.log('Search input changed');
                applyFilters();
            });
        }

        function applyFilters() {
            const searchTerm = searchInput.value.toLowerCase();
            const categoryFilter = document.getElementById('categoryFilter').value;
            const stockFilter = document.getElementById('stockFilter').value;
            const supplierFilter = document.getElementById('supplierFilter').value;
            const brandFilter = document.getElementById('brandFilter').value;

            const filteredItems = items.filter(item => {
                // Search filter
                const matchesSearch = !searchTerm || 
                    item.name?.toLowerCase().includes(searchTerm) ||
                    item.sku?.toLowerCase().includes(searchTerm) ||
                    (Array.isArray(item.categories) && item.categories.some(cat => 
                        cat.toLowerCase().includes(searchTerm)
                    ));

                // Category filter
                const matchesCategory = !categoryFilter || 
                    (Array.isArray(item.categories) && item.categories.includes(categoryFilter));

                // Stock status filter
                const matchesStock = (() => {
                    if (!stockFilter) return true;
                    const quantity = parseInt(item.quantity);
                    const reorderLevel = parseInt(item.reorder_level);
                    
                    switch(stockFilter) {
                        case 'out_of_stock':
                            return quantity === 0;
                        case 'low_stock':
                            return quantity > 0 && quantity <= reorderLevel;
                        case 'in_stock':
                            return quantity > reorderLevel;
                        default:
                            return true;
                    }
                })();

                // Supplier filter
                const matchesSupplier = !supplierFilter || 
                    item.supplier_id.toString() === supplierFilter;

                // Brand filter
                const matchesBrand = !brandFilter || 
                    item.brand_id.toString() === brandFilter;

                return matchesSearch && matchesCategory && matchesStock && 
                       matchesSupplier && matchesBrand;
            });

            renderInventoryTable(filteredItems);
            updateFilteredCount(filteredItems.length);
        }

        function updateFilteredCount(filteredCount) {
            const totalItems = document.getElementById('totalItems');
            const totalCount = items.length;
            
            if (filteredCount === totalCount) {
                totalItems.textContent = totalCount;
            } else {
                totalItems.textContent = `${filteredCount} / ${totalCount}`;
            }
        }

        // Update the renderInventoryTable function to handle filtered data
        function renderInventoryTable(filteredItems = null) {
            const itemsToRender = filteredItems || items;
            
            if (!Array.isArray(itemsToRender)) {
                console.error('Items is not an array:', itemsToRender);
                return;
            }

            inventoryTableBody.innerHTML = itemsToRender.map(item => `
                <tr class="border-b border-gray-700 hover:bg-gray-700" data-id="${item.id}">
                    <td class="py-2 px-4">${item.name || ''}</td>
                    <td class="py-2 px-4">${item.sku || ''}</td>
                    <td class="py-2 px-4">${Array.isArray(item.categories) ? item.categories.join(', ') : ''}</td>
                    <td class="py-2 px-4 ${getQuantityColorClass(parseInt(item.quantity), parseInt(item.reorder_level))}">${item.quantity || 0}</td>
                    <td class="py-2 px-4">${item.unit || ''}</td>
                    <td class="py-2 px-4">₱${parseFloat(item.purchase_price || 0).toFixed(2)}</td>
                    <td class="py-2 px-4">₱${parseFloat(item.selling_price || 0).toFixed(2)}</td>
                    <td class="py-2 px-4">${item.supplier_name || ''}</td>
                    <td class="py-2 px-4">${item.reorder_level || 0}</td>
                    <td class="py-2 px-4">${formatDate(item.date_added)}</td>
                    <td class="py-2 px-4">
                        <div class="flex space-x-2">
                            <button class="edit-btn text-blue-500 hover:text-blue-600">
                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                            </button>
                            <button class="delete-btn text-red-500 hover:text-red-600">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `).join('');
            
            lucide.createIcons();
            addInventoryEventListeners();
        }

        // Make sure to initialize filters when the page loads
        document.addEventListener('DOMContentLoaded', () => {
            initializeFilters();
            fetchInventory();
            fetchCategories();
            fetchSuppliers();
            fetchBrands();
        });
    </script>
</body>
</html>