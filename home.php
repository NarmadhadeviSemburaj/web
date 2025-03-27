<?php
session_start();
include 'db_config.php';
include 'log_api.php';

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

// Get the emp_id from session
$emp_id = $_SESSION['emp_id'];
$username = $_SESSION['user'];
$is_admin = $_SESSION['is_admin'];

// Log page access
logUserAction(
    $emp_id,
    $username,
    'page_access',
    "Accessed home page",
    $_SERVER['REQUEST_URI'],
    $_SERVER['REQUEST_METHOD'],
    null,
    200,
    null,
    $_SERVER['REMOTE_ADDR'],
    $_SERVER['HTTP_USER_AGENT']
);

// Check if the user is an admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

// Get the current file name to highlight the current page
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Test Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
      html, body {
                height: 100%;
                margin: 0;
                padding: 0;
                background-color: #f0f0f0;
                overflow: hidden;
            }

            .wrapper {
                display: flex;
                height: 100vh;
                padding: 20px;
            }

            .sidebar-container {
                width: 200px;
                height: 100vh;
                background-color: #fff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
                margin-right: 20px;
                overflow: hidden;
                position: fixed;
                left: 20px;
                top: 20px;
                bottom: 20px;
            }
            
            .sidebar a {
                display: flex;
                align-items: center;
                padding: 10px;
                margin: 10px 0;
                text-decoration: none;
                color: #333;
                border-radius: 10px;
                transition: background-color 0.3s;
            }

            .sidebar a:hover, .sidebar a.active {
                background-color: #007bff;
                color: #fff;
            }

            .sidebar a i {
                margin-right: 10px;
            }

            .separator {
                height: 1px;
                background-color: #ddd;
                margin: 20px 0;
            }

            .admin-section h4 {
                font-size: 16px;
                cursor: pointer;
                text-align: center;
                margin: 10px 0;
                padding: 10px;
                border-radius: 10px;
                transition: background-color 0.3s;
            }

            .admin-section h4:hover {
                background-color: #007bff;
                color: #fff;
            }

            .admin-links {
                display: none;
            }

            .admin-links a {
                display: flex;
                align-items: center;
                padding: 10px;
                margin: 5px 0;
                text-decoration: none;
                color: #333;
                border-radius: 10px;
                transition: background-color 0.3s;
            }

            .admin-links a:hover {
                background-color: #007bff;
                color: #fff;
            }

            .admin-links a i {
                margin-right: 10px;
            }
            
            .content-container {
                flex: 1;
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                padding: 20px;
                height: 100vh;
                margin-left: 220px;
                overflow-y: auto;
            }
            
            .user-info {
                text-align: center;
                margin-bottom: 20px;
            }

            .user-info i {
                font-size: 20px;
                margin-right: 5px;
            }

            .user-info h4 {
                font-size: 16px;
                margin: 5px 0 0;
                color: #333;
            }
            
            .welcome-message {
                text-align: center;
                margin-top: 20px;
            }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar in a separate container -->
        <div class="sidebar-container">
            <!-- User Info Section -->
            <div class="user-info">
                <i class="fas fa-user"></i>
                <h4><?php echo htmlspecialchars($_SESSION['user']); ?></h4>
            </div>
            <div class="sidebar">
                <!-- Regular Links with Icons -->
                <a href="summary.php" class="<?php echo ($current_page == 'summary.php') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a href="update_tc3.php" class="<?php echo ($current_page == 'update_tc3.php') ? 'active' : ''; ?>">
                    <i class="fas fa-vial"></i> Testing
                </a>
                <a href="bug_details.php" class="<?php echo ($current_page == 'bug_details.php') ? 'active' : ''; ?>">
                    <i class="fas fa-bug"></i> Bug Reports
                </a>
                <a href="logout.php" class="text-danger <?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>

                <!-- Light Line Separator -->
                <div class="separator"></div>

                <!-- Admin Section -->
                <?php if ($_SESSION['is_admin']): ?>
                    <div class="admin-section">
                        <h4 onclick="toggleAdminLinks()">Admin</h4>
                        <div class="admin-links">
                            <a href="employees.php" class="<?php echo ($current_page == 'employees.php') ? 'active' : ''; ?>">
                                <i class="fas fa-users"></i> Employees
                            </a>
                            <a href="apk_up.php" class="<?php echo ($current_page == 'apk_up.php') ? 'active' : ''; ?>">
                                <i class="fas fa-upload"></i> APK Admin
                            </a>
                            <a href="index1.php" class="<?php echo ($current_page == 'index1.php') ? 'active' : ''; ?>">
                                <i class="fas fa-list-alt"></i> TCM
                            </a>
                            <a href="view_logs.php" class="<?php echo ($current_page == 'view_logs.php') ? 'active' : ''; ?>">
                                <i class="fas fa-clipboard-list"></i> View Logs
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>    
        </div>

        <!-- Main Content -->
        <div class="content-container">
            <div class="welcome-message">
                <h4>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h4>
            </div>
        </div>
    </div>

    <script>
        // Function to toggle the visibility of admin links
        function toggleAdminLinks() {
            const adminLinks = document.querySelector('.admin-links');
            adminLinks.style.display = adminLinks.style.display === 'block' ? 'none' : 'block';
        }
        
        // Log important client-side actions
        function logClientAction(actionType, description) {
            $.ajax({
                url: 'log_api.php',
                type: 'POST',
                data: {
                    action: 'log_client_action',
                    action_type: actionType,
                    description: description
                },
                dataType: 'json'
            });
        }
    </script>
</body>
</html>