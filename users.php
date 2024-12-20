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
                <h1 class="text-2xl font-bold mb-4 md:mb-0">Users Management</h1>
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
                    <h2 class="text-xl font-semibold mb-4 md:mb-0">User List</h2>
                    <button id="addUserBtn" class="bg-[#6366f1] hover:bg-[#4f46e5] py-2 px-4 rounded-md flex items-center">
                        <i data-lucide="plus-circle" class="mr-2 h-4 w-4"></i> Add User
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="text-left py-3 px-4 text-white">Name</th>
                                <th class="text-left py-3 px-4 text-white">Email</th>
                                <th class="text-left py-3 px-4 text-white">Role</th>
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

    <!-- Add User Modal -->
    <div id="addUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-gray-800 p-6 rounded-lg w-96">
            <h2 class="text-xl font-semibold mb-4">Add New User</h2>
            <form id="addUserForm" class="space-y-4" enctype="multipart/form-data">
                <input id="newUserUsername" type="text" name="username" placeholder="Username" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                <input id="newUserName" type="text" name="name" placeholder="Name" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                <input id="newUserEmail" type="email" name="email" placeholder="Email" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                <select id="newUserRole" name="role" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
                    <option value="">Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Manager">Manager</option>
                    <option value="Staff">Staff</option>
                </select>
                <div id="dropArea" class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center cursor-pointer">
                    <p>Drag & drop an image here, or click to select</p>
                    <input type="file" id="newUserPhoto" name="profile_picture" accept="image/*" class="hidden">
                </div>
                <button type="submit" id="submitNewUser" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] py-2 px-4 rounded-md">Add User</button>
                <button type="button" id="closeAddUserModal" class="w-full mt-2 bg-gray-700 hover:bg-gray-600 py-2 px-4 rounded-md">Cancel</button>
            </form>
        </div>
    </div>

   <!-- Edit User Modal -->
   <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
    <div class="bg-gray-800 p-6 rounded-lg w-96">
        <h2 class="text-xl font-semibold mb-4">Edit User</h2>
        <input id="editUserName" type="text" placeholder="Name" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
        <input id="editUserEmail" type="email" placeholder="Email" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
        <select id="editUserRole" class="w-full mb-4 p-2 bg-gray-700 border border-gray-600 text-white rounded">
            <option value="">Select Role</option>
            <option value="Admin">Admin</option>
            <option value="Manager">Manager</option>
            <option value="Staff">Staff</option>
        </select>
        <button id="submitEditUser" class="w-full bg-[#6366f1] hover:bg-[#4f46e5] py-2 px-4 rounded-md">Update User</button>
        <button id="closeEditUserModal" class="w-full mt-2 bg-gray-700 hover:bg-gray-600 py-2 px-4 rounded-md">Cancel</button>
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
        const addUserBtn = document.getElementById('addUserBtn');
        const addUserModal = document.getElementById('addUserModal');
        const closeAddUserModal = document.getElementById('closeAddUserModal');
        const submitNewUser = document.getElementById('submitNewUser');
        const editUserModal = document.getElementById('editUserModal');
        const closeEditUserModal = document.getElementById('closeEditUserModal');
        const submitEditUser = document.getElementById('submitEditUser');
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

        // Fetch users
        async function fetchUsers() {
            try {
                const response = await fetch('fetch_users_data.php');
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();
                if (data.error) {
                    throw new Error(data.error);
                }
                renderUsers(data.users);
            } catch (error) {
                console.error('Error fetching users:', error);
                alert('Failed to fetch users data. Check the console for more details.');
            }
        }
        // Render users
        function renderUsers(users) {
            userTableBody.innerHTML = users.map(user => `
                <tr>
                    <td class="py-2 px-4">${user.name}</td>
                    <td class="py-2 px-4">${user.email}</td>
                    <td class="py-2 px-4">${user.role}</td>
                    <td class="py-2 px-4 space-x-2">
                        <button class="edit-user" data-id="${user.id}">
                            <i data-lucide="pencil" class="h-4 w-4 text-[#6366f1]"></i>
                        </button>
                        <button class="reset-password" data-id="${user.id}">
                            <i data-lucide="key" class="h-4 w-4 text-[#6366f1]"></i>
                        </button>
                        <button class="delete-user" data-id="${user.id}">
                            <i data-lucide="trash-2" class="h-4 w-4 text-[#6366f1]"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
            lucide.createIcons();
            addUserEventListeners();
        }
        // Add event listeners to user actions
        function addUserEventListeners() {
            document.querySelectorAll('.edit-user').forEach(btn => {
                btn.addEventListener('click', () => openEditUserModal(btn.dataset.id));
            });
            document.querySelectorAll('.reset-password').forEach(btn => {
                btn.addEventListener('click', () => openResetPasswordModal(btn.dataset.id));
            });
            document.querySelectorAll('.delete-user').forEach(btn => {
                btn.addEventListener('click', () => deleteUser(btn.dataset.id));
            });
        }


        // Add user
        addUserBtn.addEventListener('click', () => {
            addUserModal.classList.remove('hidden');
            addUserModal.classList.add('flex');
        });

        closeAddUserModal.addEventListener('click', () => {
            addUserModal.classList.add('hidden');
            addUserModal.classList.remove('flex');
        });

        // Update the add user functionality
        const addUserForm = document.getElementById('addUserForm');
        const dropArea = document.getElementById('dropArea');
        const fileInput = document.getElementById('newUserPhoto');

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

        // Add user form submission
        addUserForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Get values from form inputs
            const userUsername = document.getElementById('newUserUsername').value; // Corrected ID
            const userName = document.getElementById('newUserName').value;
            const userEmail = document.getElementById('newUserEmail').value;
            const userRole = document.getElementById('newUserRole').value;

            // Check if all fields are filled
            if (!userName || !userEmail || !userRole || !userUsername) {
                alert('Please fill in all fields');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'add_user_with_picture');
            formData.append('username', userUsername); // Corrected ID
            formData.append('name', userName);
            formData.append('email', userEmail);
            formData.append('role', userRole);
            formData.append('profile_picture', fileInput.files[0]);

            fetch('api.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                } else {
                    alert(data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An unexpected error occurred. Please check the console for details.');
            });
        });

        // Edit user
        async function openEditUserModal(userId) {
            try {
                const response = await fetch(`api.php?action=get_user&id=${userId}`);
                if (!response.ok) throw new Error('Network response was not ok');
                const user = await response.json();
                if (user.error) throw new Error(user.error);

                const editUserName = document.getElementById('editUserName');
                const editUserEmail = document.getElementById('editUserEmail');
                const editUserRole = document.getElementById('editUserRole');

                if (!editUserName || !editUserEmail || !editUserRole) {
                    throw new Error('Edit user form elements not found');
                }

                editUserName.value = user.name;
                editUserEmail.value = user.email;
                editUserRole.value = user.role;
                editUserModal.dataset.userId = userId;

                editUserModal.classList.remove('hidden');
                editUserModal.classList.add('flex');
            } catch (error) {
                console.error('Error fetching user data:', error);
                alert('Failed to fetch user data. Please try again.');
            }
        }

        closeEditUserModal.addEventListener('click', () => {
            editUserModal.classList.add('hidden');
            editUserModal.classList.remove('flex');
        });

        submitEditUser.addEventListener('click', async () => {
            const name = document.getElementById('editUserName').value;
            const email = document.getElementById('editUserEmail').value;
            const role = document.getElementById('editUserRole').value;
            const userId = editUserModal.dataset.userId;

            if (!name || !email || !role) {
                alert('Please fill in all fields');
                return;
            }

            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'update_user', id: userId, name, email, role }),
                });
                if (!response.ok) throw new Error('Network response was not ok');
                const result = await response.json();
                if (result.error) throw new Error(result.error);
                alert('User updated successfully');
                fetchUsers();
                editUserModal.classList.add('hidden');
                editUserModal.classList.remove('flex');
            } catch (error) {
                console.error('Error updating user:', error);
                alert('Failed to update user. Please try again.');
            }
        });

        // Reset password
        function openResetPasswordModal(userId) {
            resetPasswordModal.dataset.userId = userId;
            document.getElementById('resetPasswordMessage').textContent = `Are you sure you want to reset the password for user ID ${userId}?`;
            resetPasswordModal.classList.remove('hidden');
            resetPasswordModal.classList.add('flex');
        }

        closeResetPasswordModal.addEventListener('click', () => {
            resetPasswordModal.classList.add('hidden');
            resetPasswordModal.classList.remove('flex');
        });

        confirmResetPassword.addEventListener('click', async () => {
            const userId = resetPasswordModal.dataset.userId;
            try {
                const response = await fetch('api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'reset_password', id: userId }),
                });
                if (!response.ok) throw new Error('Network response was not ok');
                const result = await response.json();
                if (result.error) throw new Error(result.error);
                alert(`Password has been reset successfully. New password: ${result.new_password}`);
                resetPasswordModal.classList.add('hidden');
                resetPasswordModal.classList.remove('flex');
            } catch (error) {
                console.error('Error resetting password:', error);
                alert('Failed to reset password. Please try again.');
            }
        });

        
        // Delete user
        async function deleteUser(userId) {
            if (confirm('Are you sure you want to delete this user?')) {
                try {
                    const response = await fetch('api.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ action: 'delete_user', id: userId }),
                    });
                    if (!response.ok) throw new Error('Network response was not ok');
                    const result = await response.json();
                    if (result.error) throw new Error(result.error);
                    alert('User deleted successfully');
                    fetchUsers();
                } catch (error) {
                    console.error('Error deleting user:', error);
                    alert('Failed to delete user. Please try again.');
                }
            }
        }

        // Initial fetch
        fetchUsers();
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