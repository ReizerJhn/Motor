<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorParts Dashboard - Reports and Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <h1 class="text-2xl font-bold mb-4 md:mb-0">Reports and Analytics</h1>
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

            <div class="bg-gray-800 p-4 rounded-lg mb-6">
                <div class="flex space-x-4" role="tablist">
                    <button class="py-2 px-4 rounded bg-gray-700 text-white" role="tab" data-tab="inventory">Inventory</button>
                    <button class="py-2 px-4 rounded text-white" role="tab" data-tab="sales">Sales</button>
               
                    <button class="py-2 px-4 rounded text-white" role="tab" data-tab="inventoryLogs">Inventory Logs</button>
                    <button class="py-2 px-4 rounded text-white" role="tab" data-tab="custom">Custom Reports</button>
                 
                </div>
            </div>

            <div id="inventoryTab" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Inventory Status Report</h3>
                        <div style="height: 300px;">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Top Selling Products</h3>
                        <div style="height: 300px;">
                            <canvas id="topSellingChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-lg font-semibold mb-4">Low Stock Report</h3>
                        <div class="overflow-y-auto" style="height: 300px;">
                            <table class="min-w-full">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-left">Item</th>
                                        <th class="px-4 py-2 text-left">Current Stock</th>
                                        <th class="px-4 py-2 text-left">Reorder Point</th>
                                    </tr>
                                </thead>
                                <tbody id="lowStockTable">
                                    <!-- Low stock items will be dynamically inserted here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div id="salesTab" class="space-y-4 hidden">
                <div class="bg-gray-800 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Sales Performance Report</h3>
                    <div style="height: 400px;">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>

            <div id="ordersTab" class="space-y-4 hidden">
                <div class="bg-gray-800 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Order Status Report</h3>
                    <div style="height: 400px;">
                        <canvas id="orderChart"></canvas>
                    </div>
                </div>
            </div>

            <div id="customTab" class="space-y-4 hidden">
                <div class="bg-gray-800 p-4 rounded-lg">
                    <h3 class="text-lg font-semibold mb-4">Custom Report Generator</h3>
                    <form class="space-y-4">
                        <div>
                            <label for="reportType" class="block mb-2">Report Type</label>
                            <select id="reportType" class="w-full bg-gray-700 border-gray-600 text-white rounded p-2">
                                <option value="">Select report type</option>
                                <option value="inventory">Inventory</option>
                                <option value="sales">Sales</option>
                                <option value="orders">Orders</option>
                            </select>
                        </div>
                        <div>
                            <label for="dateRange" class="block mb-2">Date Range</label>
                            <div class="flex items-center space-x-2">
                                <input type="date" id="startDate" class="bg-gray-700 border-gray-600 text-white rounded p-2">
                                <span>to</span>
                                <input type="date" id="endDate" class="bg-gray-700 border-gray-600 text-white rounded p-2">
                            </div>
                        </div>
                        <div>
                            <label for="filters" class="block mb-2">Filters</label>
                            <div class="flex items-center space-x-2">
                                <input type="text" id="filters" placeholder="Add filters..." class="flex-grow bg-gray-700 border-gray-600 text-white rounded p-2">
                                <button type="button" class="bg-gray-700 hover:bg-gray-600 p-2 rounded">
                                    <i data-lucide="filter" class="h-4 w-4"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="bg-[#6366f1] hover:bg-[#4f46e5] text-white py-2 px-4 rounded">Generate Report</button>
                    </form>
                </div>
            </div>

            <div id="inventoryLogsTab" class="space-y-4 hidden">
                <div class="bg-gray-800 p-4 rounded-lg">
                    <div class="flex flex-wrap gap-4 mb-4">
                        <div class="flex items-center space-x-2">
                            <label for="logsProductFilter" class="text-sm">Product:</label>
                            <select id="logsProductFilter" class="bg-gray-700 border-gray-600 text-white rounded p-2">
                                <option value="">All Products</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <label for="logsUserFilter" class="text-sm">Created By:</label>
                            <select id="logsUserFilter" class="bg-gray-700 border-gray-600 text-white rounded p-2">
                                <option value="">All Users</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <label for="logsStartDate" class="text-sm">From:</label>
                            <input type="date" id="logsStartDate" class="bg-gray-700 border-gray-600 text-white rounded p-2">
                        </div>
                        <div class="flex items-center space-x-2">
                            <label for="logsEndDate" class="text-sm">To:</label>
                            <input type="date" id="logsEndDate" class="bg-gray-700 border-gray-600 text-white rounded p-2">
                        </div>
                        <button id="logsClearFilters" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded">
                            Clear Filters
                        </button>
                    </div>
                    <table class="min-w-full bg-gray-800 rounded-lg">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left">Product</th>
                                <th class="px-4 py-2 text-left">Quantity Change</th>
                                <th class="px-4 py-2 text-left">Transaction Type</th>
                                <th class="px-4 py-2 text-left">Status</th>
                                <th class="px-4 py-2 text-left">Date</th>
                                <th class="px-4 py-2 text-left">Created By</th>
                                <th class="px-4 py-2 text-left">Notes</th>
                            </tr>
                        </thead>
                        <tbody id="logsTable">
                            <!-- Logs will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <button class="bg-gray-800 text-white hover:bg-gray-700 py-2 px-4 rounded flex items-center">
                    <i data-lucide="download" class="mr-2 h-4 w-4"></i>
                    Export Report
                </button>
                <button class="bg-gray-800 text-white hover:bg-gray-700 py-2 px-4 rounded flex items-center">
                    <i data-lucide="calendar" class="mr-2 h-4 w-4"></i>
                    Schedule Report
                </button>
            </div>
        </main>
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
        const tabs = document.querySelectorAll('[role="tab"]');
        const tabContents = document.querySelectorAll('[id$="Tab"]');

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
            // Redirect to the login page
            window.location.href = 'Login.html';
        });

        // Search functionality
        searchForm.addEventListener('submit', (e) => {
            e.preventDefault();
            console.log('Searching for:', searchInput.value);
            // Implement search logic here
        });

        // Tab functionality
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('bg-gray-700'));
                tab.classList.add('bg-gray-700');
                const tabName = tab.getAttribute('data-tab');
                tabContents.forEach(content => {
                    content.classList.add('hidden');
                    if (content.id === `${tabName}Tab`) {
                        content.classList.remove('hidden');
                        // Initialize inventory logs when switching to that tab
                        if (tabName === 'inventoryLogs') {
                            initializeInventoryLogs();
                        }
                    }
                });
            });
        });

        // Modify chart options for better responsiveness
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: 'white' }
                },
                x: {
                    ticks: { color: 'white' }
                }
            },
            plugins: {
                legend: {
                    labels: { color: 'white' }
                }
            }
        };

        // Create charts with updated options
        const inventoryChart = new Chart(document.getElementById('inventoryChart'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'In Stock',
                        data: [],
                        borderColor: '#8884d8',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2
                    },
                    {
                        label: 'Out of Stock',
                        data: [],
                        borderColor: '#82ca9d',
                        backgroundColor: 'transparent',
                        tension: 0.4,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            color: 'white',
                            precision: 0,
                            stepSize: 5
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        },
                        suggestedMax: 30
                    },
                    x: {
                        ticks: { color: 'white' },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.1)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'start',
                        labels: { 
                            color: 'white',
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });

        const salesChart = new Chart(document.getElementById('salesChart'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Revenue',
                    data: [],
                    backgroundColor: '#8884d8',
                    order: 2
                }, {
                    label: 'Orders',
                    data: [],
                    type: 'line',
                    borderColor: '#82ca9d',
                    backgroundColor: 'transparent',
                    order: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: 'white' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        ticks: { color: 'white' },
                        grid: { display: false }
                    },
                    x: {
                        ticks: { color: 'white' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: 'white' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label === 'Revenue') {
                                    return `Revenue: $${context.raw.toLocaleString()}`;
                                }
                                return `Orders: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        const orderChart = new Chart(document.getElementById('orderChart'), {
            type: 'pie',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8']
                }]
            },
            options: {
                ...chartOptions,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { color: 'white' }
                    }
                }
            }
        });

        async function fetchReportData() {
            try {
                const response = await fetch('fetch_reports_data.php');
                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                if (result.success && result.data) {
                    updateCharts(result.data);
                } else {
                    console.error('Unexpected data structure:', result);
                }
            } catch (error) {
                console.error('Error fetching report data:', error);
                alert(`Failed to fetch report data: ${error.message}`);
            }
        }

        function updateCharts(data) {
            // Update inventory chart
            if (data.inventoryData && data.inventoryData.length > 0) {
                const labels = data.inventoryData.map(item => item.category);
                const inStockData = data.inventoryData.map(item => parseInt(item.in_stock));
                const outStockData = data.inventoryData.map(item => parseInt(item.out_of_stock));

                inventoryChart.data.labels = labels;
                inventoryChart.data.datasets[0].data = inStockData;
                inventoryChart.data.datasets[1].data = outStockData;
                inventoryChart.update();
            }

            // Update sales chart
            if (data.salesData && data.salesData.length > 0) {
                const labels = data.salesData.map(d => {
                    const date = new Date(d.month + '-01');
                    return date.toLocaleDateString('default', { month: 'short', year: 'numeric' });
                });
                
                salesChart.data.labels = labels;
                salesChart.data.datasets[0].data = data.salesData.map(d => parseFloat(d.net_revenue));
                salesChart.data.datasets[1].data = data.salesData.map(d => parseInt(d.order_count));
                salesChart.update();
            }

            // Update top selling products chart
            if (data.topSellingProducts && data.topSellingProducts.length > 0) {
                topSellingChart.data.labels = data.topSellingProducts.map(d => d.name);
                topSellingChart.data.datasets[0].data = data.topSellingProducts.map(d => parseInt(d.total_sold));
                topSellingChart.data.datasets[1].data = data.topSellingProducts.map(d => parseFloat(d.total_revenue));
                topSellingChart.update();

                // Update top selling products table if it exists
                const topSellingTable = document.getElementById('topSellingTable');
                if (topSellingTable) {
                    topSellingTable.innerHTML = data.topSellingProducts.map(product => `
                        <tr class="hover:bg-gray-700">
                            <td class="px-4 py-2">${product.name}</td>
                            <td class="px-4 py-2">${product.sku}</td>
                            <td class="px-4 py-2">${product.total_sold}</td>
                            <td class="px-4 py-2">$${parseFloat(product.total_revenue).toLocaleString()}</td>
                            <td class="px-4 py-2">${product.order_count}</td>
                            <td class="px-4 py-2">$${parseFloat(product.average_price).toLocaleString()}</td>
                            <td class="px-4 py-2">${new Date(product.last_sold_date).toLocaleDateString()}</td>
                        </tr>
                    `).join('');
                }
            }

            // Update low stock table
            if (data.lowStockItems && data.lowStockItems.length > 0) {
                const lowStockTable = document.getElementById('lowStockTable');
                lowStockTable.innerHTML = data.lowStockItems.map(item => `
                    <tr class="hover:bg-gray-700">
                        <td class="px-4 py-2">${item.name}</td>
                        <td class="px-4 py-2 ${parseInt(item.current_stock) === 0 ? 'text-red-500' : 'text-yellow-500'}">${item.current_stock}</td>
                        <td class="px-4 py-2">${item.reorder_point}</td>
                    </tr>
                `).join('');
            } else {
                const lowStockTable = document.getElementById('lowStockTable');
                lowStockTable.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-4 py-2 text-center">No low stock items found</td>
                    </tr>
                `;
            }

            // Update inventory logs if they exist in the data
            if (data.inventoryLogs) {
                updateInventoryLogs(data.inventoryLogs);
            }
        }

        // Create top selling products chart
        const topSellingChart = new Chart(document.getElementById('topSellingChart'), {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Units Sold',
                    data: [],
                    backgroundColor: '#4C51BF'
                }, {
                    label: 'Revenue',
                    data: [],
                    backgroundColor: '#48BB78',
                    hidden: true
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { color: 'white' },
                        grid: { color: 'rgba(255,255,255,0.1)' }
                    },
                    y: {
                        ticks: { color: 'white' },
                        grid: { display: false }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: { color: 'white' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.dataset.label === 'Revenue') {
                                    return `Revenue: $${context.raw.toLocaleString()}`;
                                }
                                return `Units Sold: ${context.raw}`;
                            }
                        }
                    }
                }
            }
        });

        // Initial fetch
        fetchReportData();

        // Fetch data every 5 minutes
        setInterval(fetchReportData, 5 * 60 * 1000);

        // Fetch and display inventory logs
        async function fetchLogs(filters = {}) {
            try {
                const params = new URLSearchParams();
                if (filters.product_id) params.append('product_id', filters.product_id);
                if (filters.created_by) params.append('created_by', filters.created_by);
                if (filters.start_date) params.append('start_date', filters.start_date);
                if (filters.end_date) params.append('end_date', filters.end_date);

                const url = `fetch_inventory_logs.php?${params.toString()}`;
                const response = await fetch(url);
                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                // Update users dropdown if it hasn't been populated
                if (result.users) {
                    const userFilter = document.getElementById('logsUserFilter');
                    if (userFilter.options.length <= 1) {
                        userFilter.innerHTML = '<option value="">All Users</option>' +
                            result.users.map(user => 
                                `<option value="${user.id}">${user.name}</option>`
                            ).join('');
                    }
                }

                const logsTable = document.getElementById('logsTable');
                if (result.success && result.logs && result.logs.length > 0) {
                    logsTable.innerHTML = result.logs.map(log => {
                        const isValidType = ['Purchase', 'Sale'].includes(log.transaction_type);
                        const transactionClass = log.transaction_type === 'Sale' ? 'text-red-500' : 'text-green-500';
                        const quantityPrefix = log.transaction_type === 'Sale' ? '-' : '+';
                        
                        return `
                            <tr class="hover:bg-gray-700">
                                <td class="px-4 py-2">${log.product_name}</td>
                                <td class="px-4 py-2 ${transactionClass}">
                                    ${isValidType ? `${quantityPrefix}${Math.abs(log.quantity_change)}` : log.quantity_change}
                                </td>
                                <td class="px-4 py-2">${isValidType ? log.transaction_type : 'Invalid Type'}</td>
                                <td class="px-4 py-2">${log.status}</td>
                                <td class="px-4 py-2">${new Date(log.created_at).toLocaleString()}</td>
                                <td class="px-4 py-2">${log.created_by || 'System'}</td>
                                <td class="px-4 py-2">${log.notes || '-'}</td>
                            </tr>
                        `;
                    }).join('');
                } else {
                    logsTable.innerHTML = `
                        <tr>
                            <td colspan="7" class="px-4 py-2 text-center">
                                No inventory logs found for the selected filters
                            </td>
                        </tr>`;
                }
            } catch (error) {
                console.error('Error fetching logs:', error);
                document.getElementById('logsTable').innerHTML = 
                    `<tr><td colspan="7" class="px-4 py-2 text-center text-red-500">
                        Error loading logs: ${error.message}
                    </td></tr>`;
            }
        }

        // Fetch product list for the dropdown
        async function fetchProductList() {
            try {
                const response = await fetch('fetch_products.php');
                const result = await response.json();

                if (result.error) {
                    throw new Error(result.error);
                }

                if (result.success && result.products) {
                    const productFilter = document.getElementById('productFilter');
                    result.products.forEach(product => {
                        const option = document.createElement('option');
                        option.value = product.id;
                        option.textContent = product.name;
                        productFilter.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error fetching product list:', error);
            }
        }

        // Add event listeners for filters
        document.addEventListener('DOMContentLoaded', () => {
            const productFilter = document.getElementById('productFilter');
            const userFilter = document.getElementById('userFilter');
            const startDate = document.getElementById('startDate');
            const endDate = document.getElementById('endDate');
            const clearFilters = document.getElementById('clearFilters');

            function getFilters() {
                return {
                    product_id: productFilter.value,
                    created_by: userFilter.value,
                    start_date: startDate.value,
                    end_date: endDate.value
                };
            }

            function applyFilters() {
                fetchLogs(getFilters());
            }

            productFilter.addEventListener('change', applyFilters);
            userFilter.addEventListener('change', applyFilters);
            startDate.addEventListener('change', applyFilters);
            endDate.addEventListener('change', applyFilters);

            clearFilters.addEventListener('click', () => {
                productFilter.value = '';
                userFilter.value = '';
                startDate.value = '';
                endDate.value = '';
                fetchLogs();
            });
        });

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

        // Add this after your existing JavaScript code
        function initializeInventoryLogs() {
            const productFilter = document.getElementById('logsProductFilter');
            const userFilter = document.getElementById('logsUserFilter');
            const startDate = document.getElementById('logsStartDate');
            const endDate = document.getElementById('logsEndDate');
            const clearFilters = document.getElementById('logsClearFilters');

            // Initialize date inputs with default values
            const today = new Date();
            const thirtyDaysAgo = new Date(today);
            thirtyDaysAgo.setDate(today.getDate() - 30);
            
            startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
            endDate.value = today.toISOString().split('T')[0];

            // Fetch initial product list
            fetch('fetch_products.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.products) {
                        productFilter.innerHTML = '<option value="">All Products</option>' +
                            data.products.map(product => 
                                `<option value="${product.id}">${product.name}</option>`
                            ).join('');
                    }
                })
                .catch(error => console.error('Error fetching products:', error));

            function getFilters() {
                return {
                    product_id: productFilter.value,
                    created_by: userFilter.value,
                    start_date: startDate.value,
                    end_date: endDate.value
                };
            }

            function applyFilters() {
                fetchLogs(getFilters());
            }

            // Add event listeners
            productFilter.addEventListener('change', applyFilters);
            userFilter.addEventListener('change', applyFilters);
            startDate.addEventListener('change', applyFilters);
            endDate.addEventListener('change', applyFilters);

            clearFilters.addEventListener('click', () => {
                productFilter.value = '';
                userFilter.value = '';
                startDate.value = thirtyDaysAgo.toISOString().split('T')[0];
                endDate.value = today.toISOString().split('T')[0];
                applyFilters();
            });

            // Initial fetch
            applyFilters();
        }

        // Add this function definition before the updateCharts function
        function updateInventoryLogs(logs) {
            const logsTable = document.getElementById('logsTable');
            if (!logs || logs.length === 0) {
                logsTable.innerHTML = `
                    <tr>
                        <td colspan="7" class="px-4 py-2 text-center">No inventory logs found</td>
                    </tr>`;
                return;
            }

            logsTable.innerHTML = logs.map(log => {
                const isValidType = ['Purchase', 'Sale'].includes(log.transaction_type);
                const transactionClass = log.transaction_type === 'Sale' ? 'text-red-500' : 'text-green-500';
                const quantityPrefix = log.transaction_type === 'Sale' ? '-' : '+';
                
                return `
                    <tr class="hover:bg-gray-700">
                        <td class="px-4 py-2">${log.product_name}</td>
                        <td class="px-4 py-2 ${transactionClass}">
                            ${isValidType ? `${quantityPrefix}${Math.abs(log.quantity_change)}` : log.quantity_change}
                        </td>
                        <td class="px-4 py-2">${isValidType ? log.transaction_type : 'Invalid Type'}</td>
                        <td class="px-4 py-2">${log.status}</td>
                        <td class="px-4 py-2">${new Date(log.created_at).toLocaleString()}</td>
                        <td class="px-4 py-2">${log.created_by || 'System'}</td>
                        <td class="px-4 py-2">${log.notes || '-'}</td>
                    </tr>
                `;
            }).join('');
        }
    </script>
</body>
</html>
