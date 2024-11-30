<?php
// Get the current page filename
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside id="sidebar" class="sidebar fixed inset-y-0 left-0 w-64 bg-gradient-to-br from-[#6366f1] via-[#8b5cf6] to-[#d946ef] p-6 flex flex-col md:rounded-tr-[50px] md:rounded-br-[50px] z-20">
            <h2 class="text-2xl font-bold mb-6">MotorParts Dashboard</h2>
            <nav class="flex-grow">
                <ul class="space-y-2">
                    <li class="py-2 px-4 rounded <?php echo ($current_page === 'dashboard.php') ? 'bg-white/20' : 'hover:bg-white/10'; ?>">
                        <a href="dashboard.php" class="flex items-center w-full">
                            <i data-lucide="package" class="mr-2"></i>
                            Overview
                        </a>
                    </li>
                
                    <li class="py-2 px-4 rounded <?php echo ($current_page === 'users.php') ? 'bg-white/20' : 'hover:bg-white/10'; ?>" data-role="admin">
                        <a href="users.php" class="flex items-center w-full">
                            <i data-lucide="users" class="mr-2"></i>
                            Users
                        </a>
                    </li>
                    <li class="py-2 px-4 rounded <?php echo ($current_page === 'inventory.php') ? 'bg-white/20' : 'hover:bg-white/10'; ?>" data-role="admin">
                        <a href="inventory.php" class="flex items-center w-full">
                            <i data-lucide="box" class="mr-2"></i>
                            Inventory
                        </a>
                    </li>
                    <li class="py-2 px-4 rounded <?php echo ($current_page === 'sales_orders.php') ? 'bg-white/20' : 'hover:bg-white/10'; ?>">
                        <a href="sales_orders.php" class="flex items-center w-full">
                            <i data-lucide="plus-circle" class="mr-2"></i>
                            Sales and Orders
                        </a>
                    </li>
                    <li class="py-2 px-4 rounded <?php echo ($current_page === 'reports.php') ? 'bg-white/20' : 'hover:bg-white/10'; ?>">
                        <a href="reports.php" class="flex items-center w-full">
                            <i data-lucide="file-text" class="mr-2"></i>
                            Reports
                        </a>
                    </li>
                    <li class="py-2 px-4 rounded <?php echo ($current_page === 'suppliers.php') ? 'bg-white/20' : 'hover:bg-white/10'; ?>" data-role="admin">
                        <a href="suppliers.php" class="flex items-center w-full">
                            <i data-lucide="truck" class="mr-2"></i>
                            Suppliers
                        </a>
                    </li>
                    <li class="py-2 px-4 rounded hover:bg-white/10">
                        <a href="#" class="flex items-center w-full">
                            <i data-lucide="settings" class="mr-2"></i>
                            Settings
                        </a>
                    </li>
                    <li class="py-2 px-4 rounded hover:bg-white/10">
                        <a href="#" class="flex items-center w-full">
                            <i data-lucide="clipboard-list" class="mr-2"></i>
                            Logs
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="mt-auto">
                <a href="#" class="block py-2 px-4 hover:bg-white/10 rounded flex items-center">
                    <i data-lucide="user" class="mr-2"></i>
                    User Account
                </a>
                <button id="logoutBtn" class="w-full text-left block py-2 px-4 hover:bg-white/10 rounded flex items-center text-red-200">
                <a href="login.php" class="flex items-center w-full">
                    <i data-lucide="login.php" class="mr-2"></i>
                    Log Out
                </button>
                </a>
            </div>
        </aside>
