<?php
require_once '../config/config.php';

// Check if user is logged in and is a super admin
requireSuperAdmin();

// Get database connection
$conn = getDbConnection();

// Handle actions
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$message = '';
$error = '';

// Handle delete action
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $message = 'User deleted successfully';
    } else {
        $error = 'Error deleting user: ' . $conn->error;
    }
    
    $stmt->close();
    $action = 'list'; // Return to list view
}

// Handle add/edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        // Add new user
        $name = sanitizeInput($_POST['name']);
        $email = sanitizeInput($_POST['email']);
        $mobile = sanitizeInput($_POST['mobile']);
        $city = sanitizeInput($_POST['city']);
        $location = sanitizeInput($_POST['location']);
        $password = password_hash(sanitizeInput($_POST['password']), PASSWORD_DEFAULT);
        $roles = isset($_POST['roles']) ? $_POST['roles'] : [];
        
        // Generate unique user ID
        $user_id = 'USR' . sprintf('%04d', rand(1000, 9999));
        
        // Check if user ID already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE user_id = ?");
        $stmt->bind_param("s", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($result->num_rows > 0) {
            $user_id = 'USR' . sprintf('%04d', rand(1000, 9999));
            $stmt->bind_param("s", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
        }
        
        $stmt->close();
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (user_id, name, email, mobile, password, city, location) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $user_id, $name, $email, $mobile, $password, $city, $location);
        
        if ($stmt->execute()) {
            $user_id_db = $conn->insert_id;
            
            // Add roles
            if (!empty($roles)) {
                $role_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                
                foreach ($roles as $role_id) {
                    $role_stmt->bind_param("ii", $user_id_db, $role_id);
                    $role_stmt->execute();
                }
                
                $role_stmt->close();
            }
            
            $message = 'User added successfully';
            $action = 'list'; // Return to list view
        } else {
            $error = 'Error adding user: ' . $conn->error;
        }
        
        $stmt->close();
    } elseif (isset($_POST['edit_user'])) {
        // Edit existing user
        $id = (int)$_POST['id'];
        $name = sanitizeInput($_POST['name']);
        $email = sanitizeInput($_POST['email']);
        $mobile = sanitizeInput($_POST['mobile']);
        $city = sanitizeInput($_POST['city']);
        $location = sanitizeInput($_POST['location']);
        $roles = isset($_POST['roles']) ? $_POST['roles'] : [];
        
        // Update user
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, mobile = ?, city = ?, location = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $name, $email, $mobile, $city, $location, $id);
        
        if ($stmt->execute()) {
            // Update roles
            $conn->query("DELETE FROM user_roles WHERE user_id = $id");
            
            if (!empty($roles)) {
                $role_stmt = $conn->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
                
                foreach ($roles as $role_id) {
                    $role_stmt->bind_param("ii", $id, $role_id);
                    $role_stmt->execute();
                }
                
                $role_stmt->close();
            }
            
            $message = 'User updated successfully';
            $action = 'list'; // Return to list view
        } else {
            $error = 'Error updating user: ' . $conn->error;
        }
        
        $stmt->close();
    }
}

// Get roles for forms
$roles = [];
$result = $conn->query("SELECT id, name, description FROM roles ORDER BY name");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $roles[] = $row;
    }
}

// Get user data for edit form
$user = null;
if ($action === 'edit' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Get user roles
        $user['roles'] = [];
        $role_result = $conn->query("SELECT role_id FROM user_roles WHERE user_id = $id");
        if ($role_result) {
            while ($role_row = $role_result->fetch_assoc()) {
                $user['roles'][] = $role_row['role_id'];
            }
        }
    }
    
    $stmt->close();
}

// Get users for list view
$users = [];
if ($action === 'list') {
    $result = $conn->query("SELECT u.*, GROUP_CONCAT(r.name) as role_names 
                           FROM users u 
                           LEFT JOIN user_roles ur ON u.id = ur.user_id 
                           LEFT JOIN roles r ON ur.role_id = r.id 
                           GROUP BY u.id 
                           ORDER BY u.created_at DESC");
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - SportsVani</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            width: 250px;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 0.5rem 1rem;
            margin: 0.2rem 0;
            border-radius: 0.25rem;
        }
        .sidebar .nav-link:hover {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: #4a6cf7;
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
        }
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3">
            <h3>SportsVani</h3>
            <p class="text-muted">Super Admin Panel</p>
        </div>
        <hr class="my-2">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="dashboard.php">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="users.php">
                    <i class="bi bi-people"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="teams.php">
                    <i class="bi bi-shield"></i> Teams
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="tournaments.php">
                    <i class="bi bi-trophy"></i> Tournaments
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="matches.php">
                    <i class="bi bi-calendar-event"></i> Matches
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="system.php">
                    <i class="bi bi-gear"></i> System
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="settings.php">
                    <i class="bi bi-sliders"></i> Settings
                </a>
            </li>
        </ul>
        <hr class="my-2">
        <div class="p-3">
            <a href="../logout.php" class="btn btn-outline-light w-100">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>User Management</h2>
            <?php if ($action === 'list'): ?>
                <a href="?action=add" class="btn btn-primary">
                    <i class="bi bi-person-plus"></i> Add New User
                </a>
            <?php else: ?>
                <a href="?action=list" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            <?php endif; ?>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if ($action === 'list'): ?>
            <!-- Users List -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    <th>City</th>
                                    <th>Roles</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center">No users found</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo $user['user_id']; ?></td>
                                            <td><?php echo $user['name']; ?></td>
                                            <td><?php echo $user['email'] ?? 'N/A'; ?></td>
                                            <td><?php echo $user['mobile']; ?></td>
                                            <td><?php echo $user['city']; ?></td>
                                            <td>
                                                <?php 
                                                    $role_names = explode(',', $user['role_names']);
                                                    foreach ($role_names as $role) {
                                                        echo '<span class="badge bg-primary me-1">' . $role . '</span>';
                                                    }
                                                ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                            <td>
                                                <a href="?action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php elseif ($action === 'add'): ?>
            <!-- Add User Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Add New User</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="col-  required>
                            </div>
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Roles</label>
                            <div class="row">
                                <?php foreach ($roles as $role): ?>
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="<?php echo $role['id']; ?>" id="role_<?php echo $role['id']; ?>">
                                            <label class="form-check-label" for="role_<?php echo $role['id']; ?>">
                                                <?php echo $role['name']; ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="?action=list" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" name="add_user" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php elseif ($action === 'edit' && $user): ?>
            <!-- Edit User Form -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Edit User</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mobile" class="form-label">Mobile</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" value="<?php echo $user['mobile']; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" value="<?php echo $user['city']; ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" value="<?php echo $user['location'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Roles</label>
                            <div class="row">
                                <?php foreach ($roles as $role): ?>
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="roles[]" value="<?php echo $role['id']; ?>" id="role_<?php echo $role['id']; ?>" <?php echo in_array($role['id'], $user['roles']) ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="role_<?php echo $role['id']; ?>">
                                                <?php echo $role['name']; ?>
                                            </label>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="?action=list" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" name="edit_user" class="btn btn-primary">Update User</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

