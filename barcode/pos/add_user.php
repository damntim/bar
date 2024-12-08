<?php
session_start();


require_once 'db.php';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate input
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = mysqli_real_escape_string($conn, trim($_POST['role']));
    
    // Validation checks
    if (empty($username) || empty($password) || empty($confirm_password) || empty($role)) {
        $error_message = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error_message = 'Password must be at least 6 characters long';
    } else {
        // Check if username already exists
        $check_query = "SELECT id FROM users WHERE username = '$username'";
        $result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($result) > 0) {
            $error_message = 'Username already exists';
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $insert_query = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";
            
            if (mysqli_query($conn, $insert_query)) {
                $success_message = 'User created successfully!';
                // Clear form data
                $_POST = array();
            } else {
                $error_message = 'Error creating user: ' . mysqli_error($conn);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow-md mb-8">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-xl font-bold">User Management</h1>
                <div class="flex items-center space-x-4">
                    <a href="pos.php" class="text-blue-500 hover:text-blue-700">Back to POS</a>
                    <span class="text-gray-500">|</span>
                   
                </div>
            </div>
        </div>
    </header>

    <div class="container mx-auto px-4">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Add New User</h2>

            <?php if ($success_message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error_message): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <p class="mt-1 text-sm text-gray-500">Must be at least 6 characters long</p>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:border-blue-500 focus:ring focus:ring-blue-200">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                    <select id="role" 
                            name="role" 
                            required 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm p-2 border focus:border-blue-500 focus:ring focus:ring-blue-200">
                        <option value="">Select a role</option>
                        <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="cashier" <?php echo (isset($_POST['role']) && $_POST['role'] === 'cashier') ? 'selected' : ''; ?>>Cashier</option>
                        <option value="manager" <?php echo (isset($_POST['role']) && $_POST['role'] === 'manager') ? 'selected' : ''; ?>>Manager</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button" 
                            onclick="window.location.href='pos.php'" 
                            class="bg-gray-500 text-white py-2 px-4 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    // Optional: Add client-side password validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('Passwords do not match!');
        }
        
        if (password.length < 6) {
            e.preventDefault();
            alert('Password must be at least 6 characters long!');
        }
    });
    </script>
</body>
</html>