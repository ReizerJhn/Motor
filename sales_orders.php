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
        <div id="sidebar" class="sidebar">
        <?php include 'sidebar.php'; ?>
        </div>
        <div id="overlay" class="fixed inset-0 bg-black opacity-50 z-10 hidden md:hidden"></div>

        <main class="flex-1 p-4 md:p-6 overflow-auto ml-0 md:ml-64">
            <button id="menuBtn" class="md:hidden p-2 text-white z-30">
                <i data-lucide="menu"></i>
            </button>
            <div class="flex flex-col md:flex-row justify-between items-center mb-6">
                <h1 class="text-2xl font-bold mb-4 md:mb-0">Sales and Orders Management</h1>
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

            

            <div class="bg-gray-800 p-4 md:p-6 rounded-lg mt-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                    <div class="flex flex-col md:flex-row md:items-center gap-4 w-full">
                        <h2 class="text-xl font-semibold whitespace-nowrap">Recent Orders</h2>
                        <!-- Filters for Orders -->
                        <div class="flex flex-col md:flex-row gap-4 w-full">
                            <input 
                                type="date" 
                                id="orderDateFilter" 
                                class="bg-gray-700 text-white rounded-md px-3 py-2"
                            >
                            <select 
                                id="orderStatusFilter" 
                                class="bg-gray-700 text-white rounded-md px-3 py-2"
                            >
                                <option value="">All Statuses</option>
                                <option value="Pending">Pending</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <button id="newOrderBtn" class="bg-blue-600 hover:bg-blue-700 py-2 px-4 rounded-md flex items-center whitespace-nowrap">
                        <i data-lucide="shopping-cart" class="mr-2"></i> New Order
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <div class="max-h-[400px] overflow-y-auto">
                        <table class="min-w-full">
                            <thead class="sticky top-0 bg-gray-800 z-10">
                                <tr>
                                    <th class="text-left py-3 px-4 text-white">Order ID</th>
                                    <th class="text-left py-3 px-4 text-white">Customer Name</th>
                                    <th class="text-left py-3 px-4 text-white">Order Date</th>
                                    <th class="text-left py-3 px-4 text-white">Status</th>
                                    <th class="text-left py-3 px-4 text-white">Products Ordered</th>
                                    <th class="text-left py-3 px-4 text-white">Total Amount</th>
                                    <th class="text-left py-3 px-4 text-white">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTableBody">
                                <!-- Orders will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-gray-800 p-4 md:p-6 rounded-lg mt-6">
                <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                    <div class="flex flex-col md:flex-row md:items-center gap-4 w-full">
                        <h2 class="text-xl font-semibold whitespace-nowrap">Inventory Stock In History</h2>
                        <!-- Filters for Stock In -->
                        <div class="flex flex-col md:flex-row gap-4 w-full">
                            <input 
                                type="date" 
                                id="stockInDateFilter" 
                                class="bg-gray-700 text-white rounded-md px-3 py-2"
                            >
                            <select 
                                id="productFilter" 
                                class="bg-gray-700 text-white rounded-md px-3 py-2"
                            >
                                <option value="">All Products</option>
                                <!-- Will be populated dynamically -->
                            </select>
                        </div>
                    </div>
                    <button id="newStockInBtn" class="bg-green-600 hover:bg-green-700 py-2 px-4 rounded-md flex items-center whitespace-nowrap">
                        <i data-lucide="arrow-down" class="mr-2"></i> New Stock In
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <div class="max-h-[400px] overflow-y-auto">
                        <table class="min-w-full">
                            <thead class="sticky top-0 bg-gray-800 z-10">
                                <tr>
                                    <th class="text-left py-3 px-4 text-white">Date & Time</th>
                                    <th class="text-left py-3 px-4 text-white">Product Name</th>
                                    <th class="text-left py-3 px-4 text-white">Quantity Added</th>
                                    <th class="text-left py-3 px-4 text-white">Status</th>
                                    <th class="text-left py-3 px-4 text-white">Added By</th>
                                    <th class="text-left py-3 px-4 text-white">Notes</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsTableBody">
                                <!-- Transactions will be dynamically inserted here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Add this new modal for order details -->
            <div id="orderDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
                <div class="bg-white p-6 rounded-lg w-[90vw] max-w-4xl max-h-[90vh] overflow-y-auto text-gray-900">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <div class="flex items-center gap-4">
                                <img src="logo.png" alt="Company Logo" class="w-12 h-12">
                                <div>
                                    <h2 class="text-xl font-bold">ERM Motorparts</h2>
                                    <p class="text-sm">7000 Zamboanga City</p>
                                    <p class="text-sm">Governor Alvarez Street</p>
                                    <p class="text-sm">(+63)970 870 1567</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="mb-2">Date: <span id="orderDate" class="font-medium"></span></p>
                            <p class="mb-2">Order #: <span id="orderNumber" class="font-medium"></span></p>
                            <p>Customer: <span id="customerName" class="font-medium"></span></p>
                        </div>
                    </div>

                    <!-- Parts Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-3">Parts</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2">Qty</th>
                                        <th class="text-left py-2">Description</th>
                                        <th class="text-right py-2">Unit Price</th>
                                        <th class="text-right py-2">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="orderItemsList">
                                    <!-- Items will be inserted here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Labor Section -->
                    <div class="mb-6">
                        <h3 class="text-lg font-bold mb-3">Labor</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2">Hrs</th>
                                        <th class="text-left py-2">Operation</th>
                                        <th class="text-right py-2">Rate</th>
                                        <th class="text-right py-2">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td id="laborHoursView" class="py-2"></td>
                                        <td class="py-2">Labor</td>
                                        <td id="laborRateView" class="text-right py-2"></td>
                                        <td id="laborTotalView" class="text-right py-2"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Totals Section -->
                    <div class="flex justify-end">
                        <div class="w-64">
                            <div class="flex justify-between py-2">
                                <span>Labor:</span>
                                <span id="totalLabor"></span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span>Parts:</span>
                                <span id="totalParts"></span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span>Tax:</span>
                                <span id="totalTax"></span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span>Discount:</span>
                                <span id="totalDiscount"></span>
                            </div>
                            <div class="flex justify-between py-2 font-bold border-t border-gray-200 mt-2">
                                <span>Total:</span>
                                <span id="grandTotal"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button id="printOrder" class="bg-blue-600 text-white px-4 py-2 rounded mr-2">Print</button>
                        <button id="closeOrderDetailsModal" class="bg-gray-300 text-gray-800 px-4 py-2 rounded">Close</button>
                    </div>
                </div>
            </div>

         
    <!-- Add Item Modal -->
    <div id="addItemModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-[90vw] max-w-4xl max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Add New Inventory Item</h2>
            <form id="addItemForm" class="space-y-4" enctype="multipart/form-data">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" id="itemName" name="name" placeholder="Item Name" class="w-full p-2 border rounded text-gray-900">
                    <input type="text" id="itemSKU" name="sku" placeholder="SKU" class="w-full p-2 border rounded text-gray-900">
                    <input type="text" id="itemCategory" name="category" placeholder="Category" class="w-full p-2 border rounded text-gray-900">
                    <input type="number" id="itemQuantity" name="quantity" placeholder="Quantity" class="w-full p-2 border rounded text-gray-900">
                    <input type="text" id="itemUnit" name="unit" placeholder="Unit of Measurement" class="w-full p-2 border rounded text-gray-900">
                    <input type="number" id="itemPurchasePrice" name="purchase_price" placeholder="Purchase Price" step="0.01" class="w-full p-2 border rounded text-gray-900">
                    <input type="number" id="itemSellingPrice" name="selling_price" placeholder="Selling Price" step="0.01" class="w-full p-2 border rounded text-gray-900">
                    <select id="itemSupplierID" name="supplier_id" class="w-full p-2 border rounded text-gray-900">
                        <option value="">Select a supplier</option>
                        <!-- Options will be populated dynamically -->
                    </select>
                    <input type="number" id="itemReorderLevel" name="reorder_level" placeholder="Reorder Level" class="w-full p-2 border rounded text-gray-900">
                    <input type="text" id="itemBrand" name="brand" placeholder="Brand" class="w-full p-2 border rounded text-gray-900">
                </div>
                <div id="dropArea" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer">
                    <p>Drag & drop an image here, or click to select</p>
                    <input type="file" id="itemImage" name="image" accept="image/*" class="hidden">
                </div>
                <textarea id="itemDescription" name="description" placeholder="Item Description" class="w-full p-2 border rounded text-gray-900"></textarea>
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
            <form id="editItemForm" class="space-y-4" enctype="multipart/form-data">
                <input type="hidden" id="editItemId" name="id">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" id="editItemName" name="name" placeholder="Item Name" class="w-full p-2 border rounded text-gray-900">
                    <input type="text" id="editItemSKU" name="sku" placeholder="SKU" class="w-full p-2 border rounded text-gray-900">
                    <input type="text" id="editItemCategory" name="category" placeholder="Category" class="w-full p-2 border rounded text-gray-900">
                    <input type="number" id="editItemQuantity" name="quantity" placeholder="Quantity" class="w-full p-2 border rounded text-gray-900">
                    <input type="text" id="editItemUnit" name="unit" placeholder="Unit of Measurement" class="w-full p-2 border rounded text-gray-900">
                    <input type="number" id="editItemPurchasePrice" name="purchase_price" placeholder="Purchase Price" step="0.01" class="w-full p-2 border rounded text-gray-900">
                    <input type="number" id="editItemSellingPrice" name="selling_price" placeholder="Selling Price" step="0.01" class="w-full p-2 border rounded text-gray-900">
                    <select id="editItemSupplierID" name="supplier_id" class="w-full p-2 border rounded text-gray-900">
                        <option value="">Select a supplier</option>
                        <!-- Options will be populated dynamically -->
                    </select>
                    <input type="number" id="editItemReorderLevel" name="reorder_level" placeholder="Reorder Level" class="w-full p-2 border rounded text-gray-900">
                    <input type="text" id="editItemBrand" name="brand" placeholder="Brand" class="w-full p-2 border rounded text-gray-900">
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
                <button id="updateItemBtn" class=""></button>
                <button id="deleteItemBtn" class=""></button>
                <button id="closeItemDetailsModal" class="bg-gray-300 text-gray-800 p-2 rounded hover:bg-gray-400">Close</button>
            </div>
        </div>
    </div>

    <!-- Stock In Modal -->
    <div id="stockInModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-[90vw] max-w-2xl">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Stock In</h2>
            <form id="stockInForm" class="space-y-4">
                <div id="stockInItems" class="space-y-4 max-h-[400px] overflow-y-auto">
                    <!-- Stock in rows will be added here -->
                </div>
                
                <button type="button" id="addStockInRow" class="bg-green-500 text-white px-4 py-2 rounded-md flex items-center">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Add Item
                </button>

                <div class="flex justify-end space-x-2 mt-4">
                    <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Submit All</button>
                    <button type="button" id="closeStockInModal" class="bg-gray-300 text-gray-800 p-2 rounded hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sales Modal -->
    <div id="salesModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-[90vw] max-w-4xl">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Create New Order</h2>
            <form id="salesForm" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer Name</label>
                        <input type="text" id="salesCustomerName" name="customer_name" class="w-full p-2 border rounded text-gray-900" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date</label>
                        <input type="date" id="orderDate" name="order_date" class="w-full p-2 border rounded text-gray-900" required>
                    </div>
                </div>

                <div class="mt-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Parts</h3>
                    <div id="productDropdowns" class="space-y-4 max-h-[200px] overflow-y-auto">
                        <!-- Product rows will be added here dynamically -->
                    </div>
                    <button type="button" id="addProductDropdown" class="mt-4 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md flex items-center">
                        <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                        Add Product
                    </button>
                </div>

                <div class="mt-4">
                    <h3 class="font-semibold text-gray-900 mb-2">Labor</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hours</label>
                            <input type="number" 
                                   id="laborHours" 
                                   name="labor_hours" 
                                   step="0.5" 
                                   min="0" 
                                   value="0"
                                   class="w-full p-2 border rounded text-gray-900">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Rate per Hour</label>
                            <input type="number" 
                                   id="laborRate" 
                                   name="labor_rate" 
                                   step="0.01" 
                                   min="0" 
                                   value="0"
                                   class="w-full p-2 border rounded text-gray-900">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Parts Total</label>
                        <input type="number" id="partsTotal" readonly class="w-full p-2 border rounded text-gray-900 bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Labor Total</label>
                        <input type="number" id="laborTotal" readonly class="w-full p-2 border rounded text-gray-900 bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tax Rate (%)</label>
                        <input type="number" id="taxRate" name="tax_rate" step="0.01" min="0" class="w-full p-2 border rounded text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tax Amount</label>
                        <input type="number" id="taxAmount" readonly class="w-full p-2 border rounded text-gray-900 bg-gray-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Discount</label>
                        <input type="number" id="discount" name="discount" step="0.01" min="0" class="w-full p-2 border rounded text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Amount</label>
                        <input type="number" id="totalAmount" name="total_amount" readonly class="w-full p-2 border rounded text-gray-900 bg-gray-100">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 mt-4">
                    <button type="submit" class="bg-blue-600 text-white p-2 rounded hover:bg-blue-700">Create Order</button>
                    <button type="button" id="closeSalesModal" class="bg-gray-300 text-gray-800 p-2 rounded hover:bg-gray-400">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            fetchRecentOrders();
            fetchInventoryTransactions();

            // Set current date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('orderDate').value = today;

            // Initialize New Order button
            document.getElementById('newOrderBtn').addEventListener('click', () => {
                // Reset form
                document.getElementById('salesForm').reset();
                document.getElementById('productDropdowns').innerHTML = '';
                
                // Set current date
                document.getElementById('orderDate').value = today;
                
                // Add initial product row
                addProductRow();
                
                // Show modal
                document.getElementById('salesModal').classList.remove('hidden');
                document.getElementById('salesModal').classList.add('flex');
                
                // Initialize calculations
                setTimeout(() => {
                    initializeCalculations();
                }, 100);
            });

            // Initialize New Stock In button
            document.getElementById('newStockInBtn').addEventListener('click', () => {
                // Reset form
                document.getElementById('stockInForm').reset();
                document.getElementById('stockInItems').innerHTML = '';
                
                // Add initial stock in row
                addStockInRow();
                
                // Show modal
                document.getElementById('stockInModal').classList.remove('hidden');
                document.getElementById('stockInModal').classList.add('flex');
            });

            // Initialize filters
            document.getElementById('orderDateFilter').addEventListener('change', filterOrders);
            document.getElementById('orderStatusFilter').addEventListener('change', filterOrders);
            document.getElementById('stockInDateFilter').addEventListener('change', filterStockIn);
            document.getElementById('productFilter').addEventListener('change', filterStockIn);

            // Populate product filter
            populateProductFilter();

            // Initialize calculations
            initializeCalculations();

            // Add box shadow to sticky headers when scrolling
            const tableContainers = document.querySelectorAll('.overflow-y-auto');
            tableContainers.forEach(container => {
                container.addEventListener('scroll', () => {
                    const header = container.querySelector('thead');
                    if (container.scrollTop > 0) {
                        header.classList.add('shadow-lg');
                    } else {
                        header.classList.remove('shadow-lg');
                    }
                });
            });

            // Initialize filters with empty values
            document.getElementById('orderDateFilter').value = '';
            document.getElementById('orderStatusFilter').value = '';
            document.getElementById('stockInDateFilter').value = '';
            document.getElementById('productFilter').value = '';
        });

        async function fetchRecentOrders() {
            try {
                const response = await fetch('sales_orders_api.php?action=get_orders');
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to fetch orders');
                }
                
                renderRecentOrders(data.orders);
            } catch (error) {
                console.error('Error fetching recent orders:', error);
                document.getElementById('ordersTableBody').innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-2 text-center text-red-500">
                            Failed to fetch recent orders: ${error.message}
                        </td>
                    </tr>
                `;
            }
        }

        function renderRecentOrders(orders) {
            const ordersTableBody = document.getElementById('ordersTableBody');
            
            if (!Array.isArray(orders) || orders.length === 0) {
                ordersTableBody.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-2 text-center">No orders found</td>
                    </tr>
                `;
                return;
            }

            ordersTableBody.innerHTML = orders.map(order => `
                <tr class="hover:bg-gray-700">
                    <td class="px-4 py-2">${order.order_number || 'N/A'}</td>
                    <td class="px-4 py-2">${order.customer_name || 'N/A'}</td>
                    <td class="px-4 py-2">${new Date(order.order_date).toLocaleString()}</td>
                    <td class="px-4 py-2">${order.status || 'N/A'}</td>
                    <td class="px-4 py-2">${order.products ? order.products.join(', ') : 'N/A'}</td>
                    <td class="px-4 py-2">₱${parseFloat(order.total_amount).toFixed(2)}</td>
                    <td class="px-4 py-2">
                        <button class="view-order-btn bg-blue-500 hover:bg-blue-600 text-white py-1 px-2 rounded" 
                                data-id="${order.id}">
                            View Details
                        </button>
                    </td>
                </tr>
            `).join('');

            // Add event listeners to view order buttons
            document.querySelectorAll('.view-order-btn').forEach(btn => {
                btn.addEventListener('click', () => openOrderDetailsModal(btn.dataset.id));
            });
        }

        function adjustMenuForUserRole(role) {
            const menuItems = document.querySelectorAll('nav ul li');
            menuItems.forEach(item => {
                const link = item.querySelector('a');
                if (role === 'Manager') {
                    if (link.textContent.trim() === 'Users' || 
                        link.textContent.trim() === 'Inventory' || 
                        link.textContent.trim() === 'Suppliers') {
                        item.style.display = 'none';
                    }
                }
            });
        }

        // Function to get user role from session storage
        function getUserRole() {
            return sessionStorage.getItem('userRole') || 'staff'; // Default to 'staff' if no role is set
        }

        // Call this function when the page loads
        document.addEventListener('DOMContentLoaded', () => {
            const userRole = getUserRole();
            adjustMenuForUserRole(userRole);
        });

        // Add Product Dropdown Button
        document.getElementById('addProductDropdown').addEventListener('click', addProductRow);

        // Make sure these modal close handlers are present
        document.getElementById('closeStockInModal').addEventListener('click', () => {
            document.getElementById('stockInModal').classList.add('hidden');
            document.getElementById('stockInModal').classList.remove('flex');
        });

        document.getElementById('closeSalesModal').addEventListener('click', () => {
            document.getElementById('salesModal').classList.add('hidden');
            document.getElementById('salesModal').classList.remove('flex');
        });

        // Stock In Form Submit Handler
        document.getElementById('stockInForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            try {
                const items = [];
                const quantities = [];
                
                // Collect data from all stock-in rows
                document.querySelectorAll('.stock-in-row').forEach(row => {
                    const itemSelect = row.querySelector('select[name="items[]"]');
                    const quantityInput = row.querySelector('input[name="quantities[]"]');
                    
                    if (itemSelect.value && quantityInput.value) {
                        items.push(itemSelect.value);
                        quantities.push(quantityInput.value);
                    }
                });

                if (items.length === 0) {
                    throw new Error('Please add at least one item');
                }

                const formData = new FormData();
                formData.append('action', 'stock_in');
                formData.append('items', JSON.stringify(items));
                formData.append('quantities', JSON.stringify(quantities));
                
                const response = await fetch('sales_orders_api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to add stock');
                }
                
                // Show success message
                showSuccess('Stock added successfully');
                
                // Close modal and reset form
                document.getElementById('stockInModal').classList.add('hidden');
                document.getElementById('stockInModal').classList.remove('flex');
                document.getElementById('stockInForm').reset();
                
                // Refresh transactions table
                fetchInventoryTransactions();
                
            } catch (error) {
                console.error('Error:', error);
                showError(error.message);
            }
        });

        // Sales Form Submit Handler
        document.getElementById('salesForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            try {
                const formData = new FormData();
                formData.append('action', 'create_order');
                formData.append('customer_name', document.getElementById('salesCustomerName').value);
                formData.append('order_date', document.getElementById('orderDate').value);
                formData.append('labor_hours', document.getElementById('laborHours').value);
                formData.append('labor_rate', document.getElementById('laborRate').value);
                formData.append('tax_rate', document.getElementById('taxRate').value);
                formData.append('discount', document.getElementById('discount').value);
                formData.append('total_amount', document.getElementById('totalAmount').value);
                
                // Get all product data
                const products = [];
                const quantities = [];
                const prices = [];
                
                document.querySelectorAll('.product-row').forEach(row => {
                    const productSelect = row.querySelector('select[name="products[]"]');
                    const quantityInput = row.querySelector('input[name="quantities[]"]');
                    const priceInput = row.querySelector('input[name="prices[]"]');
                    
                    if (productSelect.value && quantityInput.value) {
                        products.push(productSelect.value);
                        quantities.push(quantityInput.value);
                        prices.push(priceInput.value);
                    }
                });

                if (products.length === 0) {
                    throw new Error('Please add at least one product');
                }
                
                formData.append('products', JSON.stringify(products));
                formData.append('quantities', JSON.stringify(quantities));
                formData.append('prices', JSON.stringify(prices));
                
                const response = await fetch('sales_orders_api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to create order');
                }
                
                // Show success message
                showSuccess('Order created successfully');
                
                // Close modal and reset form
                document.getElementById('salesModal').classList.add('hidden');
                document.getElementById('salesModal').classList.remove('flex');
                document.getElementById('salesForm').reset();
                
                // Refresh orders table
                fetchRecentOrders();
                
            } catch (error) {
                console.error('Error:', error);
                showError(error.message);
            }
        });

        // Add event listener for product and quantity changes
        document.getElementById('productDropdowns').addEventListener('change', (e) => {
            if (e.target.matches('select[name="products[]"]') || e.target.matches('input[name="quantities[]"]')) {
                calculateTotalAmount();
            }
        });

        // Show Error Message
        function showError(message) {
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = message;
            errorMessage.classList.remove('hidden');
            setTimeout(() => {
                errorMessage.classList.add('hidden');
            }, 5000);
        }

        // Show Success Message
        function showSuccess(message) {
            const successDiv = document.createElement('div');
            successDiv.className = 'bg-green-500 text-white p-4 rounded-lg mb-4';
            successDiv.textContent = message;
            const errorMessage = document.getElementById('errorMessage');
            errorMessage.parentNode.insertBefore(successDiv, errorMessage);
            setTimeout(() => {
                successDiv.remove();
            }, 5000);
        }

        function recalculateAll() {
            // Debug: Log the actual elements and their values
            const hoursElement = document.getElementById('laborHours');
            const rateElement = document.getElementById('laborRate');
            
            console.log('Hours element:', hoursElement);
            console.log('Rate element:', rateElement);
            console.log('Hours value:', hoursElement ? hoursElement.value : 'Element not found');
            console.log('Rate value:', rateElement ? rateElement.value : 'Element not found');

            // Get labor values and ensure they are valid numbers
            const hours = parseFloat(hoursElement ? hoursElement.value : 0) || 0;
            const rate = parseFloat(rateElement ? rateElement.value : 0) || 0;
            const laborTotal = hours * rate;
            
            console.log('Calculated values:', { hours, rate, laborTotal });

            // Update labor total display with proper formatting
            const laborTotalElement = document.getElementById('laborTotal');
            if (laborTotalElement) {
                laborTotalElement.value = laborTotal.toFixed(2);
            }
            
            // Calculate parts total
            let partsTotal = 0;
            document.querySelectorAll('.product-row').forEach(row => {
                const price = parseFloat(row.querySelector('[name="prices[]"]').value) || 0;
                const quantity = parseInt(row.querySelector('[name="quantities[]"]').value) || 0;
                partsTotal += price * quantity;
            });
            
            // Update parts total display
            document.getElementById('partsTotal').value = partsTotal.toFixed(2);
            
            // Calculate final amounts
            const subtotal = partsTotal + laborTotal;
            const taxRate = parseFloat(document.getElementById('taxRate').value) || 0;
            const taxAmount = (subtotal * taxRate) / 100;
            const discount = parseFloat(document.getElementById('discount').value) || 0;
            
            // Calculate final total
            const finalTotal = subtotal + taxAmount - discount;
            
            // Update all displays
            document.getElementById('taxAmount').value = taxAmount.toFixed(2);
            document.getElementById('totalAmount').value = finalTotal.toFixed(2);
            
            // For debugging
            console.log({
                hours,
                rate,
                laborTotal,
                partsTotal,
                subtotal,
                taxAmount,
                discount,
                finalTotal
            });
        }

        // Update product row template
        function addProductRow() {
            const row = document.createElement('div');
            row.className = 'product-row bg-gray-50 p-4 rounded-lg';
            row.innerHTML = `
                <div class="grid grid-cols-12 gap-4 items-center">
                    <div class="col-span-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product</label>
                        <select name="products[]" class="w-full p-2 border rounded text-gray-900" required>
                            <option value="">Select a product</option>
                        </select>
                    </div>
                    <div class="col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" name="quantities[]" min="1" value="1" 
                               class="w-full p-2 border rounded text-gray-900" required>
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Price</label>
                        <input type="text" name="prices[]" readonly 
                               class="w-full p-2 border rounded text-gray-900 bg-gray-100">
                    </div>
                    <div class="col-span-1 flex items-end">
                        <button type="button" class="remove-product bg-red-500 text-white p-2 rounded hover:bg-red-600 mt-5">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            `;

            // Clear existing products before fetching new ones
            const select = row.querySelector('select');
            select.innerHTML = '<option value="">Select a product</option>';

            // Fetch and populate products
            fetch('sales_orders_api.php?action=get_products')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.products.forEach(product => {
                            const option = new Option(
                                `${product.name} (Stock: ${product.quantity})`, 
                                product.id
                            );
                            option.dataset.price = product.selling_price;
                            select.add(option);
                        });
                    }
                })
                .catch(error => console.error('Error fetching products:', error));

            // Add event listeners
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const priceInput = row.querySelector('[name="prices[]"]');
                if (selectedOption.value) {
                    priceInput.value = parseFloat(selectedOption.dataset.price).toFixed(2);
                } else {
                    priceInput.value = '';
                }
                recalculateAll();
            });

            row.querySelector('[name="quantities[]"]').addEventListener('input', recalculateAll);
            row.querySelector('.remove-product').addEventListener('click', () => {
                row.remove();
                recalculateAll();
            });

            document.getElementById('productDropdowns').appendChild(row);
            lucide.createIcons(); // Refresh icons
            recalculateAll();

            // Make sure the labor section exists and has the correct structure
          
        }

        // Function to open order details modal
        async function openOrderDetailsModal(orderId) {
            try {
                const response = await fetch(`sales_orders_api.php?action=get_order_details&order_id=${orderId}`);
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to fetch order details');
                }
                
                const order = data.order;
                
                // Fill in basic order information
                document.getElementById('orderDate').textContent = new Date(order.order_date).toLocaleDateString();
                document.getElementById('orderNumber').textContent = order.order_number;
                document.getElementById('customerName').textContent = order.customer_name;
                
                // Fill in parts list
                const orderItemsList = document.getElementById('orderItemsList');
                orderItemsList.innerHTML = order.items.map(item => `
                    <tr class="border-b">
                        <td class="py-2">${item.quantity}</td>
                        <td class="py-2">${item.product_name}</td>
                        <td class="text-right py-2">₱${parseFloat(item.price).toFixed(2)}</td>
                        <td class="text-right py-2">₱${(item.quantity * item.price).toFixed(2)}</td>
                    </tr>
                `).join('');
                
                // Fill in labor details
                document.getElementById('laborHoursView').textContent = order.labor_hours || '0';
                document.getElementById('laborRateView').textContent = `₱${parseFloat(order.labor_rate || 0).toFixed(2)}`;
                const laborTotal = (order.labor_hours || 0) * (order.labor_rate || 0);
                document.getElementById('laborTotalView').textContent = `₱${laborTotal.toFixed(2)}`;
                
                // Fill in totals
                document.getElementById('totalLabor').textContent = `₱${laborTotal.toFixed(2)}`;
                
                const partsTotal = order.items.reduce((sum, item) => sum + (item.quantity * item.price), 0);
                document.getElementById('totalParts').textContent = `₱${partsTotal.toFixed(2)}`;
                
                const taxAmount = ((partsTotal + laborTotal) * (order.tax_rate || 0)) / 100;
                document.getElementById('totalTax').textContent = `₱${taxAmount.toFixed(2)}`;
                
                // Add discount display
                const discount = parseFloat(order.discount || 0);
                document.getElementById('totalDiscount').textContent = `₱${discount.toFixed(2)}`;
                
                const grandTotal = partsTotal + laborTotal + taxAmount - discount;
                document.getElementById('grandTotal').textContent = `₱${grandTotal.toFixed(2)}`;
                
                // Show the modal
                document.getElementById('orderDetailsModal').classList.remove('hidden');
                document.getElementById('orderDetailsModal').classList.add('flex');
            } catch (error) {
                console.error('Error:', error);
                showError(error.message);
            }
        }

        // Print functionality
        document.getElementById('printOrder').addEventListener('click', () => {
            window.print();
        });

        // Close modal
        document.getElementById('closeOrderDetailsModal').addEventListener('click', () => {
            document.getElementById('orderDetailsModal').classList.add('hidden');
            document.getElementById('orderDetailsModal').classList.remove('flex');
        });

        // Add print styles
        const style = document.createElement('style');
        style.textContent = `
            @media print {
                body * {
                    visibility: hidden;
                }
                #orderDetailsModal,
                #orderDetailsModal * {
                    visibility: visible;
                }
                #orderDetailsModal {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }
                #closeOrderDetailsModal,
                #printOrder {
                    display: none;
                }
            }
        `;
        document.head.appendChild(style);

        function initializeCalculations() {
            // Set current date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('orderDate').value = today;

            // Add event listeners for all inputs that affect calculations
            const inputs = [
                'laborHours',
                'laborRate',
                'taxRate',
                'discount'
            ];

            inputs.forEach(inputId => {
                const element = document.getElementById(inputId);
                if (element) {
                    element.addEventListener('input', recalculateAll);
                    // Ensure non-negative values
                    element.addEventListener('change', function() {
                        if (this.value < 0) this.value = 0;
                    });
                }
            });

            // Set initial values to 0 if empty
            document.getElementById('laborHours').value = document.getElementById('laborHours').value || '0';
            document.getElementById('laborRate').value = document.getElementById('laborRate').value || '0';
            document.getElementById('taxRate').value = document.getElementById('taxRate').value || '0';
            document.getElementById('discount').value = document.getElementById('discount').value || '0';

            // Initial calculation
            recalculateAll();
        }

        function addStockInRow() {
            const row = document.createElement('div');
            row.className = 'stock-in-row bg-gray-50 p-4 rounded-lg';
            row.innerHTML = `
                <div class="grid grid-cols-12 gap-4 items-end">
                    <div class="col-span-6">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Item</label>
                        <select name="items[]" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-gray-900" required>
                            <option value="">Select an item</option>
                        </select>
                    </div>
                    <div class="col-span-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" name="quantities[]" min="1" class="w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 text-gray-900" required>
                    </div>
                    <div class="col-span-2">
                        <button type="button" class="remove-stock-in-row bg-red-500 text-white p-2 rounded hover:bg-red-600 w-full">
                            <i data-lucide="trash-2" class="w-4 h-4 mx-auto"></i>
                        </button>
                    </div>
                </div>
            `;

            // Populate the select with products
            const select = row.querySelector('select');
            fetch('sales_orders_api.php?action=get_products')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        data.products.forEach(product => {
                            const option = new Option(
                                `${product.name} (Current stock: ${product.quantity})`,
                                product.id
                            );
                            select.add(option);
                        });
                    }
                })
                .catch(error => console.error('Error fetching products:', error));

            // Add remove button functionality
            row.querySelector('.remove-stock-in-row').addEventListener('click', () => {
                row.remove();
            });

            document.getElementById('stockInItems').appendChild(row);
            lucide.createIcons(); // Refresh icons
        }

        // Add event listener for the add row button
        document.getElementById('addStockInRow').addEventListener('click', addStockInRow);

        async function fetchInventoryTransactions() {
            try {
                const response = await fetch('sales_orders_api.php?action=get_purchase_transactions');
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.error || 'Failed to fetch transactions');
                }
                
                renderInventoryTransactions(data.transactions);
            } catch (error) {
                console.error('Error fetching transactions:', error);
                document.getElementById('transactionsTableBody').innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-red-500">
                            Failed to fetch transactions: ${error.message}
                        </td>
                    </tr>
                `;
            }
        }

        function renderInventoryTransactions(transactions) {
            const tbody = document.getElementById('transactionsTableBody');
            
            if (!Array.isArray(transactions) || transactions.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center">No stock in transactions found</td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = transactions.map(txn => {
                // Determine status color
                const statusColor = txn.status === 'Completed' ? 'text-green-400' : 'text-yellow-400';
                
                // Format quantity with + indicator
                const quantityDisplay = `<span class="text-green-400">+${txn.quantity_change}</span>`;

                // Use user_full_name if available, fallback to username
                const addedBy = txn.user_full_name || txn.created_by_name;

                return `
                    <tr class="hover:bg-gray-700 border-t border-gray-700">
                        <td class="px-4 py-3">${new Date(txn.created_at).toLocaleString()}</td>
                        <td class="px-4 py-3">${txn.product_name}</td>
                        <td class="px-4 py-3">${quantityDisplay}</td>
                        <td class="px-4 py-3 ${statusColor}">${txn.status}</td>
                        <td class="px-4 py-3">${addedBy}</td>
                        <td class="px-4 py-3">${txn.notes || ''}</td>
                    </tr>
                `;
            }).join('');
        }

        // Function to populate product filter
        async function populateProductFilter() {
            try {
                const response = await fetch('sales_orders_api.php?action=get_products');
                const data = await response.json();
                if (data.success) {
                    const productFilter = document.getElementById('productFilter');
                    data.products.forEach(product => {
                        const option = new Option(product.name, product.id);
                        productFilter.add(option);
                    });
                }
            } catch (error) {
                console.error('Error loading products:', error);
            }
        }

        // Filter functions
        async function filterOrders() {
            const date = document.getElementById('orderDateFilter').value;
            const status = document.getElementById('orderStatusFilter').value;
            
            try {
                const queryParams = new URLSearchParams();
                if (date) queryParams.append('date', date);
                if (status) queryParams.append('status', status);
                queryParams.append('action', 'get_orders');
                
                const response = await fetch(`sales_orders_api.php?${queryParams}`);
                const data = await response.json();
                
                if (data.success) {
                    renderRecentOrders(data.orders);
                } else {
                    throw new Error(data.error || 'Failed to filter orders');
                }
            } catch (error) {
                console.error('Error filtering orders:', error);
                showError(error.message);
            }
        }

        async function filterStockIn() {
            const date = document.getElementById('stockInDateFilter').value;
            const productId = document.getElementById('productFilter').value;
            
            try {
                const queryParams = new URLSearchParams();
                if (date) queryParams.append('date', date);
                if (productId) queryParams.append('product_id', productId);
                queryParams.append('action', 'get_purchase_transactions');
                
                const response = await fetch(`sales_orders_api.php?${queryParams}`);
                const data = await response.json();
                
                if (data.success) {
                    renderInventoryTransactions(data.transactions);
                } else {
                    throw new Error(data.error || 'Failed to filter transactions');
                }
            } catch (error) {
                console.error('Error filtering stock in:', error);
                showError(error.message);
            }
        }

        // Add this helper function for showing success messages if not already present
        function showSuccess(message) {
            const successDiv = document.createElement('div');
            successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded shadow-lg z-50';
            successDiv.textContent = message;
            document.body.appendChild(successDiv);
            
            setTimeout(() => {
                successDiv.remove();
            }, 3000);
        }

        // Add this helper function for showing error messages if not already present
        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded shadow-lg z-50';
            errorDiv.textContent = message;
            document.body.appendChild(errorDiv);
            
            setTimeout(() => {
                errorDiv.remove();
            }, 3000);
        }
    </script>
</body>
</html>
