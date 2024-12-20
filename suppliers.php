<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotorParts Dashboard - Users Management</title>
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
                <h1 class="text-2xl font-bold mb-4 md:mb-0">Suppliers Management</h1>
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
                <div class="flex flex-col md:flex-row justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold mb-4 md:mb-0">Supplier List</h2>
                    <button id="addSupplierBtn" class="bg-[#6366f1] hover:bg-[#4f46e5] py-2 px-4 rounded-md flex items-center">
                        <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i> Add Supplier
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left py-3 px-4 text-white">Logo</th>
                                <th class="text-left py-3 px-4 text-white">Company Name</th>
                                <th class="text-left py-3 px-4 text-white">Contact Person</th>
                                <th class="text-left py-3 px-4 text-white">Phone Number</th>
                                <th class="text-left py-3 px-4 text-white">Email</th>
                                <th class="text-left py-3 px-4 text-white">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody">
                            <!-- User rows will be dynamically inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Supplier Modal -->
    <div id="addSupplierModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-gray-800 p-6 rounded-lg w-96">
            <h2 class="text-xl font-semibold mb-4">Add New Supplier</h2>
            <form id="addSupplierForm" class="space-y-4" enctype="multipart/form-data">
                <input id="newCompanyName" type="text" name="company_name" placeholder="Company Name" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                <input id="newContactName" type="text" name="contact_name" placeholder="Contact Name" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                <input id="newEmail" type="email" name="email" placeholder="Email" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                <input id="newPhone" type="text" name="phone" placeholder="Phone" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                <textarea id="newAddress" name="address" placeholder="Address" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded"></textarea>
                <select id="newCategory" name="category" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                    <option value="" disabled selected>Select Category</option>
                    <option value="Engine Parts">Engine Parts</option>
                    <option value="Brake Systems">Brake Systems</option>
                    <option value="Electrical Components">Electrical Components</option>
                    <!-- Add more categories as needed -->
                </select>
                <!-- File Upload -->
                <input id="newLogo" type="file" name="logo" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                <button type="submit" id="submitNewSupplier" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] py-2 px-4 rounded-md">Add Supplier</button>
                <button type="button" id="closeAddSupplierModal" class="w-full mt-2 bg-gray-700 hover:bg-gray-600 py-2 px-4 rounded-md">Cancel</button>
            </form>
        </div>
    </div>

   <!-- Edit Supplier Modal -->
   <div id="editSupplierModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-gray-800 p-6 rounded-lg w-96">
        <h2 class="text-xl font-semibold mb-4">Edit Supplier</h2>
        <form id="editSupplierForm" class="space-y-4" enctype="multipart/form-data">
            <input id="editCompanyName" name="company_name" type="text" placeholder="Company Name" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
            <input id="editContactPerson" name="contact_person" type="text" placeholder="Contact Person" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
            <input id="editPhoneNumber" name="phone_number" type="text" placeholder="Phone Number" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
            <input id="editEmail" name="email" type="email" placeholder="Email" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
            <textarea id="editAddress" name="address" placeholder="Address" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded"></textarea>
            <select id="editCategory" name="category" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                <option value="" disabled>Select Category</option>
                <option value="Engine Parts">Engine Parts</option>
                <option value="Brake Systems">Brake Systems</option>
                <option value="Electrical Components">Electrical Components</option>
                <!-- Add more categories as needed -->
            </select>
            <div id="logoUploadArea" class="w-full mb-4 p-4 bg-gray-700 border border-gray-600 text-white rounded text-center cursor-pointer">
                <p>Drag and drop a logo here or click to select a file</p>
                <input type="file" id="editLogo" name="logo" accept="image/*" class="hidden">
            </div>
            <div id="logoPreview" class="hidden mb-4">
                <img id="logoPreviewImage" src="" alt="Logo Preview" class="max-w-full h-auto">
            </div>
            <button type="submit" id="submitEditSupplier" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] py-2 px-4 rounded-md">Update Supplier</button>
            <button type="button" id="closeEditSupplierModal" class="w-full mt-2 bg-gray-700 hover:bg-gray-600 py-2 px-4 rounded-md">Cancel</button>
        </form>
    </div>
</div>

    <!-- Reset Password Modal -->
    <div id="resetPasswordModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-gray-800 p-6 rounded-lg w-96">
            <h2 class="text-xl font-semibold mb-4">Reset Password</h2>
            <p id="resetPasswordMessage" class="mb-4"></p>
            <button id="confirmResetPassword" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] py-2 px-4 rounded-md">Reset Password</button>
            <button id="closeResetPasswordModal" class="w-full mt-2 bg-gray-700 hover:bg-gray-600 py-2 px-4 rounded-md">Cancel</button>
        </div>
    </div>

    <!-- Supplier Details Modal -->
    <div id="supplierDetailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-6 rounded-lg w-[90vw] max-w-3xl max-h-[90vh] overflow-y-auto">
            <h2 class="text-xl font-semibold mb-4 text-gray-900">Supplier Details</h2>
            <div id="supplierDetailsContent" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Supplier details will be dynamically inserted here -->
            </div>
            <div class="mt-6 flex justify-end space-x-2">
                <button id="updateSupplierBtn" class="bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update Supplier</button>
                <button id="deleteSupplierBtn" class="bg-red-500 text-white p-2 rounded hover:bg-red-600">Delete Supplier</button>
                <button id="closeSupplierDetailsModal" class="bg-gray-300 text-gray-800 p-2 rounded hover:bg-gray-400">Close</button>
            </div>
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
        const userTableBody = document.getElementById('userTableBody');
        const addSupplierBtn = document.getElementById('addSupplierBtn');
        const addSupplierModal = document.getElementById('addSupplierModal');
        const closeAddSupplierModal = document.getElementById('closeAddSupplierModal');
        const submitNewSupplier = document.getElementById('submitNewSupplier');
        const editSupplierModal = document.getElementById('editSupplierModal');
        const closeEditSupplierModal = document.getElementById('closeEditSupplierModal');
        const submitEditSupplier = document.getElementById('submitEditSupplier');
        const resetPasswordModal = document.getElementById('resetPasswordModal');
        const closeResetPasswordModal = document.getElementById('closeResetPasswordModal');
        const confirmResetPassword = document.getElementById('confirmResetPassword');

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

        // Fetch suppliers
        async function fetchSuppliers() {
            try {
                const response = await fetch('fetch_suppliers_data.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (!data.success) {
                    throw new Error(data.error);
                }
                renderSuppliers(data.suppliers);
            } catch (error) {
                console.error('Error fetching suppliers:', error);
                alert('Failed to fetch suppliers data: ' + error.message);
            }
        }

        // Render suppliers
        function renderSuppliers(suppliers) {
            userTableBody.innerHTML = suppliers.map(supplier => `
                <tr class="cursor-pointer hover:bg-gray-700" data-id="${supplier.id}">
                    <td class="py-2 px-4">
                        <img src="${getImagePath(supplier.logo)}" alt="${supplier.company_name}" class="w-10 h-10 object-cover rounded-full">
                    </td>
                    <td class="py-2 px-4">${supplier.company_name}</td>
                    <td class="py-2 px-4">${supplier.contact_person}</td>
                    <td class="py-2 px-4">${supplier.phone_number}</td>
                    <td class="py-2 px-4">${supplier.email}</td>
                    <td class="py-2 px-4 space-x-2">
                        <button class="edit-supplier" data-id="${supplier.id}">
                            <i data-lucide="pencil" class="h-4 w-4 text-[#6366f1]"></i>
                        </button>
                        <button class="delete-supplier" data-id="${supplier.id}">
                            <i data-lucide="trash-2" class="h-4 w-4 text-[#6366f1]"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            lucide.createIcons();
            addSupplierEventListeners();
        }

        // Add event listeners to supplier actions
        function addSupplierEventListeners() {
            document.querySelectorAll('.edit-supplier').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    openEditSupplierModal(btn.dataset.id);
                });
            });
            document.querySelectorAll('.delete-supplier').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    deleteSupplier(btn.dataset.id);
                });
            });
            document.querySelectorAll('tr[data-id]').forEach(row => {
                row.addEventListener('click', () => {
                    openSupplierDetailsModal(row.dataset.id);
                });
            });
        }

        // Helper function to get image path
        function getImagePath(imagePath) {
            if (imagePath && imagePath.startsWith('http')) {
                return imagePath;
            }
            return imagePath ? imagePath : '/images/default-supplier-logo.png'; // Fallback image
        }

        // Open supplier details modal
        function openSupplierDetailsModal(supplierId) {
            const supplier = suppliers.find(s => s.id === parseInt(supplierId));
            if (supplier) {
                const supplierDetailsContent = document.getElementById('supplierDetailsContent');
                supplierDetailsContent.innerHTML = `
                    <div>
                        <img src="${getImagePath(supplier.logo)}" alt="${supplier.company_name}" class="w-full h-auto rounded-lg">
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-900">${supplier.company_name}</h3>
                        <p class="text-gray-500 mb-4">Contact: ${supplier.contact_person}</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-gray-500">Phone</p>
                                <p class="text-gray-900">${supplier.phone_number}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Email</p>
                                <p class="text-gray-900">${supplier.email}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Address</p>
                                <p class="text-gray-900">${supplier.address}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Category</p>
                                <p class="text-gray-900">${supplier.category}</p>
                            </div>
                        </div>
                    </div>
                `;
                document.getElementById('supplierDetailsModal').classList.remove('hidden');
                document.getElementById('supplierDetailsModal').classList.add('flex');
            }
        }

        // Close supplier details modal
        document.getElementById('closeSupplierDetailsModal').addEventListener('click', () => {
            document.getElementById('supplierDetailsModal').classList.add('hidden');
            document.getElementById('supplierDetailsModal').classList.remove('flex');
        });

        // Update supplier button in details modal
        document.getElementById('updateSupplierBtn').addEventListener('click', () => {
            const supplierId = document.querySelector('#supplierDetailsModal tr').dataset.id;
            openEditSupplierModal(supplierId);
            document.getElementById('supplierDetailsModal').classList.add('hidden');
            document.getElementById('supplierDetailsModal').classList.remove('flex');
        });

        // Delete supplier button in details modal
        document.getElementById('deleteSupplierBtn').addEventListener('click', () => {
            const supplierId = document.querySelector('#supplierDetailsModal tr').dataset.id;
            deleteSupplier(supplierId);
            document.getElementById('supplierDetailsModal').classList.add('hidden');
            document.getElementById('supplierDetailsModal').classList.remove('flex');
        });

        // Add supplier
        addSupplierBtn.addEventListener('click', () => {
            addSupplierModal.classList.remove('hidden');
            addSupplierModal.classList.add('flex');
        });

        closeAddSupplierModal.addEventListener('click', () => {
            addSupplierModal.classList.add('hidden');
            addSupplierModal.classList.remove('flex');
        });

        // Add supplier form submission
        const addSupplierForm = document.getElementById('addSupplierForm');
        addSupplierForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(addSupplierForm);
            formData.append('action', 'add_supplier');

            try {
                const response = await fetch('upload_image.php', {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                
                if (data.success) {
                    alert(data.message);
                    await fetchSuppliers();
                    addSupplierModal.classList.add('hidden');
                    addSupplierModal.classList.remove('flex');
                    addSupplierForm.reset();
                } else {
                    throw new Error(data.error);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred: ' + error.message);
            }
        });

        // Edit supplier
        async function openEditSupplierModal(supplierId) {
            try {
                const response = await fetch(`fetch_suppliers_data.php?action=get_supplier&id=${supplierId}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                if (!data.success) throw new Error(data.error || 'Failed to fetch supplier data');
                const supplier = data.supplier;

                document.getElementById('editCompanyName').value = supplier.company_name;
                document.getElementById('editContactPerson').value = supplier.contact_person;
                document.getElementById('editPhoneNumber').value = supplier.phone_number;
                document.getElementById('editEmail').value = supplier.email;
                document.getElementById('editAddress').value = supplier.address || '';
                document.getElementById('editCategory').value = supplier.category || '';
                editSupplierModal.dataset.supplierId = supplierId;

                const logoPreview = document.getElementById('logoPreview');
                const logoPreviewImage = document.getElementById('logoPreviewImage');

                if (supplier.logo) {
                    logoPreviewImage.src = supplier.logo;
                    logoPreview.classList.remove('hidden');
                } else {
                    logoPreview.classList.add('hidden');
                }

                editSupplierModal.classList.remove('hidden');
                editSupplierModal.classList.add('flex');
            } catch (error) {
                console.error('Error fetching supplier data:', error);
                alert('Failed to fetch supplier data. Please try again.');
            }
        }

        closeEditSupplierModal.addEventListener('click', () => {
            editSupplierModal.classList.add('hidden');
            editSupplierModal.classList.remove('flex');
        });

        submitEditSupplier.addEventListener('click', async () => {
            const supplierId = editSupplierModal.dataset.supplierId;
            const supplierName = document.getElementById('editSupplierName').value;
            const companyName = document.getElementById('editCompanyName').value;
            const contactPerson = document.getElementById('editContactPerson').value;
            const phoneNumber = document.getElementById('editPhoneNumber').value;
            const email = document.getElementById('editEmail').value;
            const address = document.getElementById('editAddress').value;
            const website = document.getElementById('editWebsite').value;

            try {
                const response = await fetch('fetch_suppliers_data.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=update_supplier&id=${supplierId}&name=${encodeURIComponent(supplierName)}&company_name=${encodeURIComponent(companyName)}&contact_person=${encodeURIComponent(contactPerson)}&phone_number=${encodeURIComponent(phoneNumber)}&email=${encodeURIComponent(email)}&address=${encodeURIComponent(address)}&website=${encodeURIComponent(website)}`
                });
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                if (!data.success) throw new Error(data.error || 'Failed to update supplier');

                await fetchSuppliers();
                editSupplierModal.classList.add('hidden');
                editSupplierModal.classList.remove('flex');
                alert('Supplier updated successfully');
            } catch (error) {
                console.error('Error updating supplier:', error);
                alert('Failed to update supplier. Please try again.');
            }
        });

        // Delete supplier
        async function deleteSupplier(supplierId) {
            if (confirm('Are you sure you want to delete this supplier?')) {
                try {
                    const response = await fetch('fetch_suppliers_data.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `action=delete_supplier&id=${supplierId}`
                    });
                    if (!response.ok) throw new Error('Network response was not ok');
                    const data = await response.json();
                    if (!data.success) throw new Error(data.error || 'Failed to delete supplier');
                    
                    await fetchSuppliers();
                    alert('Supplier deleted successfully');
                } catch (error) {
                    console.error('Error deleting supplier:', error);
                    alert('Failed to delete supplier. Please try again.');
                }
            }
        }

        // Logo upload functionality
        const logoUploadArea = document.getElementById('logoUploadArea');
        const editLogo = document.getElementById('editLogo');
        const logoPreview = document.getElementById('logoPreview');
        const logoPreviewImage = document.getElementById('logoPreviewImage');

        logoUploadArea.addEventListener('click', () => editLogo.click());

        logoUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            logoUploadArea.classList.add('border-blue-500');
        });

        logoUploadArea.addEventListener('dragleave', () => {
            logoUploadArea.classList.remove('border-blue-500');
        });

        logoUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            logoUploadArea.classList.remove('border-blue-500');
            if (e.dataTransfer.files.length) {
                editLogo.files = e.dataTransfer.files;
                updateLogoPreview(e.dataTransfer.files[0]);
            }
        });

        editLogo.addEventListener('change', (e) => {
            if (e.target.files.length) {
                updateLogoPreview(e.target.files[0]);
            }
        });

        function updateLogoPreview(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                logoPreviewImage.src = e.target.result;
                logoPreview.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }

        // Update supplier form submission
        const editSupplierForm = document.getElementById('editSupplierForm');
        editSupplierForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const supplierId = editSupplierModal.dataset.supplierId;
            const formData = new FormData(editSupplierForm);
            formData.append('action', 'update_supplier');
            formData.append('id', supplierId);

            // Log form data for debugging
            for (let [key, value] of formData.entries()) {
                console.log(key, value);
            }

            try {
                const response = await fetch('fetch_suppliers_data.php', {
                    method: 'POST',
                    body: formData
                });
                if (!response.ok) throw new Error('Network response was not ok');
                const data = await response.json();
                if (!data.success) throw new Error(data.error || 'Failed to update supplier');

                await fetchSuppliers();
                editSupplierModal.classList.add('hidden');
                editSupplierModal.classList.remove('flex');
                alert('Supplier updated successfully');
            } catch (error) {
                console.error('Error updating supplier:', error);
                alert('Failed to update supplier. Please try again.');
            }
        });

        // Initial fetch
        fetchSuppliers();
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
</html>
