<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorParts Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        /* Custom styles */
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
            
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                <h1 class="text-2xl font-bold mb-2 md:mb-0">Admin Dashboard</h1>
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

            <!-- Dashboard Content -->
            <div id="content" class="space-y-4">
                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-[#6366f1] to-[#8b5cf6] p-4 rounded-lg flex items-center">
                        <h1 class="text-3xl font-bold mr-3">₱</h1>
                        <div>
                            <h3 class="text-sm mb-1">Total Inventory Value</h3>
                            <p class="text-xl font-bold">${data.inventoryStatus.totalValue ? data.inventoryStatus.totalValue.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'}</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-[#8b5cf6] to-[#d946ef] p-4 rounded-lg flex items-center">
                        <i data-lucide="package" class="mr-3" style="width: 32px; height: 32px;"></i>
                        <div>
                            <h3 class="text-sm mb-1">Out of Stock Items</h3>
                            <p class="text-xl font-bold">${data.inventoryStatus.outOfStockCount}</p>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-[#d946ef] to-[#f43f5e] p-4 rounded-lg flex items-center">
                        <i data-lucide="alert-triangle" class="mr-3" style="width: 32px; height: 32px;"></i>
                        <div>
                            <h3 class="text-sm mb-1">Low Stock Items</h3>
                            <p class="text-xl font-bold">${data.inventoryStatus.lowStockCount}</p>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Inventory Levels Chart -->
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold mb-3">Inventory Levels Over Time</h3>
                        <div style="height: 200px;">
                            <canvas id="inventoryChart"></canvas>
                        </div>
                    </div>

                    <!-- Parts by Type Chart -->
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold mb-3">Parts by Type</h3>
                        <div style="height: 200px;">
                            <canvas id="partTypeChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Weekly Order Volume -->
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold mb-3">Weekly Order Volume</h3>
                        <div style="height: 200px;">
                            <canvas id="orderVolumeChart"></canvas>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <div class="bg-gray-800 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold mb-3">Recent Orders</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-gray-400">
                                        <th class="px-3 py-2 text-left">Order ID</th>
                                        <th class="px-3 py-2 text-left">Customer</th>
                                        <th class="px-3 py-2 text-left">Total</th>
                                    </tr>
                                </thead>
                                <tbody id="recentOrdersTable"></tbody>
                            </table>
                        </div>
                    </div>
                </div>
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
        const content = document.getElementById('content');

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

        // Fetch dashboard data
        async function fetchDashboardData() {
            try {
                const response = await fetch('fetch_dashboard_data.php');
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                return data;
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
                throw error; // Re-throw the error to be handled by the calling function
            }
        }

        // Render dashboard content
        async function renderDashboard() {
            try {
                const data = await fetchDashboardData();
                
                // Update the Total Inventory Value section
                content.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                        <div class="bg-gradient-to-br from-[#6366f1] to-[#8b5cf6] p-6 rounded-lg flex items-center">
                            <h1 class="text-5xl font-bold mr-4">₱</h1>
                            <div>
                                <h3 class="text-lg mb-2">Total Inventory Value</h3>
                                <p class="text-3xl font-bold">${data.inventoryStatus.totalValue ? data.inventoryStatus.totalValue.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '0.00'}</p>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-[#8b5cf6] to-[#d946ef] p-6 rounded-lg flex items-center">
                            <i data-lucide="package" class="mr-4" style="width: 48px; height: 48px;"></i>
                            <div>
                                <h3 class="text-lg mb-2">Out of Stock Items</h3>
                                <p class="text-3xl font-bold">${data.inventoryStatus.outOfStockCount}</p>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-[#d946ef] to-[#f43f5e] p-6 rounded-lg flex items-center">
                            <i data-lucide="alert-triangle" class="mr-4" style="width: 48px; height: 48px;"></i>
                            <div>
                                <h3 class="text-lg mb-2">Low Stock Items</h3>
                                <p class="text-3xl font-bold">${data.inventoryStatus.lowStockCount}</p>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-800 p-6 rounded-lg">
                            <h3 class="text-lg mb-4">Inventory Levels Over Time</h3>
                            <div style="height: 300px;">
                                <canvas id="inventoryChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-gray-800 p-6 rounded-lg">
                            <h3 class="text-lg mb-4">Parts by Type</h3>
                            <div style="height: 300px;">
                                <canvas id="partTypeChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-gray-800 p-6 rounded-lg">
                            <h3 class="text-lg mb-4">Weekly Order Volume</h3>
                            <canvas id="orderVolumeChart" width="400" height="200"></canvas>
                        </div>
                        <div class="bg-gray-800 p-6 rounded-lg">
                            <h3 class="text-lg mb-4">Recent Orders</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left">Order ID</th>
                                            <th class="px-4 py-2 text-left">Customer</th>
                                            <th class="px-4 py-2 text-left">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="recentOrdersTable"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                `;

                // Add console logs to check data
                console.log('Inventory Data:', data.inventoryData);
                console.log('Part Type Data:', data.partTypeData);
                console.log('Recent Orders:', data.recentOrders);

                // Render charts only if data is available
                if (data.inventoryData && data.inventoryData.length > 0) {
                    renderInventoryChart(data.inventoryData);
                } else {
                    console.log('No inventory data available');
                }

                if (data.partTypeData && data.partTypeData.length > 0) {
                    renderPartTypeChart(data.partTypeData);
                } else {
                    console.log('No part type data available');
                }

                if (data.weeklyOrderVolume && data.weeklyOrderVolume.length > 0) {
                    renderOrderVolumeChart(data.weeklyOrderVolume);
                } else {
                    console.log('No weekly order volume data available');
                    document.getElementById('orderVolumeChart').innerHTML = '<p class="text-center text-gray-500">No data available</p>';
                }

                if (data.recentOrders && data.recentOrders.length > 0) {
                    renderRecentOrders(data.recentOrders);
                } else {
                    console.log('No recent orders available');
                }

                // Re-initialize Lucide icons for dynamically added content
                lucide.createIcons();

            } catch (error) {
                content.innerHTML = `
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">Error:</strong>
                        <span class="block sm:inline"> Failed to fetch dashboard data. Please try again later or contact support.</span>
                    </div>
                `;
                console.error('Error rendering dashboard:', error);
            }
        }

        // Render Inventory Levels Chart
        function renderInventoryChart(data) {
            const ctx = document.getElementById('inventoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.name),
                    datasets: [
                        {
                            label: 'In Stock',
                            data: data.map(item => item.in_stock),
                            backgroundColor: '#8884d8',
                        },
                        {
                            label: 'Out of Stock',
                            data: data.map(item => item.out_of_stock),
                            backgroundColor: '#82ca9d',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            stacked: true,
                            ticks: {
                                autoSkip: false,
                                maxRotation: 90,
                                minRotation: 90
                            }
                        },
                        y: {
                            stacked: true,
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });
        }

        // Render Parts by Type Chart
        function renderPartTypeChart(data) {
            const ctx = document.getElementById('partTypeChart').getContext('2d');
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.map(item => item.type),
                    datasets: [{
                        data: data.map(item => item.total_quantity),
                        backgroundColor: ['#0088FE', '#00C49F', '#FFBB28', '#FF8042', '#8884D8']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12
                            }
                        }
                    }
                }
            });
        }

        // Render Weekly Order Volume Chart
        function renderOrderVolumeChart(data) {
            const ctx = document.getElementById('orderVolumeChart').getContext('2d');
            
            // Check if data is available
            if (!data || data.length === 0) {
                console.log('No weekly order volume data available');
                document.getElementById('orderVolumeChart').innerHTML = '<p class="text-center text-gray-500">No data available</p>';
                return;
            }

            // Format dates and ensure all data points are numbers
            const formattedData = data.map(item => ({
                order_date: new Date(item.order_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric' }),
                order_count: parseInt(item.order_count),
                total_amount: parseFloat(item.total_amount)
            }));

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: formattedData.map(item => item.order_date),
                    datasets: [{
                        label: 'Order Count',
                        data: formattedData.map(item => item.order_count),
                        backgroundColor: '#8884d8',
                        yAxisID: 'y'
                    }, {
                        label: 'Total Amount',
                        data: formattedData.map(item => item.total_amount),
                        backgroundColor: '#82ca9d',
                        type: 'line',
                        yAxisID: 'y1'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Order Count'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Total Amount (₱)'
                            },
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                callback: function(value, index, values) {
                                    return '₱' + value.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.datasetIndex === 1) {
                                        label += '₱' + context.parsed.y.toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                                    } else {
                                        label += context.parsed.y;
                                    }
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        }

        function renderRecentOrders(orders) {
    // Sort orders by ID in descending order (most recent first)
    const sortedOrders = orders.sort((a, b) => b.id - a.id); // This line ensures sorting by ID

    const tableBody = document.getElementById('recentOrdersTable');
    tableBody.innerHTML = sortedOrders.map(order => `
        <tr>
            <td class="px-4 py-2">${order.id}</td>
            <td class="px-4 py-2">${order.customer}</td>
            <td class="px-4 py-2">₱ ${parseFloat(order.total).toLocaleString('en-PH', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
        </tr>
    `).join('');
}

        // Initial render
        renderDashboard();
    </script>

    <!-- Add this script block just before the closing </body> tag -->
    <script>
        // Function to hide menu items based on user role
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
    </script>
</body>
</html>
