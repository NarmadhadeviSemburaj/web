<?php
session_start();
include 'log_api.php'; // Make sure this file exists for logging

// Set session timeout to 5 minutes (300 seconds)
$timeout = 300;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
    // Log session timeout
    logUserAction(
        $_SESSION['emp_id'] ?? null,
        $_SESSION['user'] ?? 'unknown',
        'session_timeout',
        "Session timed out due to inactivity",
        $_SERVER['REQUEST_URI'],
        $_SERVER['REQUEST_METHOD'],
        null,
        401,
        null,
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT']
    );
    
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
$_SESSION['last_activity'] = time();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // Log unauthorized access attempt
    logUserAction(
        $_SESSION['emp_id'] ?? null,
        $_SESSION['user'] ?? 'unknown',
        'unauthorized_access',
        "Attempted to access edit employee page without admin privileges",
        $_SERVER['REQUEST_URI'],
        $_SERVER['REQUEST_METHOD'],
        null,
        403,
        null,
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT']
    );
    
    header("Location: home.php");
    exit();
}

// Log page access
logUserAction(
    $_SESSION['emp_id'],
    $_SESSION['user'],
    'page_access',
    "Accessed edit employee page",
    $_SERVER['REQUEST_URI'],
    $_SERVER['REQUEST_METHOD'],
    null,
    200,
    null,
    $_SERVER['REMOTE_ADDR'],
    $_SERVER['HTTP_USER_AGENT']
);

// Define the current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Employee - Test Management</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            overflow-x: hidden;
        }

        /* Wrapper to hold both sidebar and content */
        .wrapper {
            display: flex;
            min-height: 100vh;
            padding: 20px;
        }

        /* Sidebar styles */
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
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar-container.collapsed {
            transform: translateX(-240px);
        }

        .sidebar-container.show {
            transform: translateX(0);
        }

        /* Content container */
        .content-container {
            flex: 1;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            min-height: 100vh;
            margin-left: 220px;
            transition: margin-left 0.3s ease;
        }

        .content-container.expanded {
            margin-left: 20px;
        }

        /* Sidebar toggle button */
        .sidebar-toggle {
            display: none;
            position: fixed;
            left: 3px;
            top: 20px;
            z-index: 1050;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            width: 35px;
            height: 35px;
            font-size: 16px;
            cursor: pointer;
            padding: 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            align-items: center;
            justify-content: center;
        }

        /* Sidebar Links */
        .sidebar a {
            display: block;
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

        /* Admin section */
        .admin-section h4 {
            font-size: 16px;
            cursor: pointer;
            margin: 10px 0;
            padding: 10px;
            border-radius: 10px;
            transition: background-color 0.3s;
        }

        .admin-section h4:hover {
            background-color: #007bff;
            color: #fff;
        }

        .admin-section {
            margin-top: 0;
            padding-top: 0;
            border-top: none;
        }

        /* User Info */
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

        .admin-links {
            display: none;
        }

        /* Form styles */
        .form-container {
            max-width: 800px;
            padding: 20px;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        #status-message {
            display: none;
            margin-top: 15px;
        }

        /* Responsive styles */
        @media (max-width: 767.98px) {
            .sidebar-container {
                transform: translateX(-240px);
            }
            .sidebar-container.show {
                transform: translateX(0);
            }
            .content-container {
                margin-left: 20px;
            }
            .sidebar-toggle {
                display: flex;
            }
            .form-container {
                padding: 15px;
            }
        }
        
        @media (min-width: 768px) and (max-width: 1199.98px) {
            .sidebar-container {
                transform: translateX(-240px);
            }
            .sidebar-container.show {
                transform: translateX(0);
            }
            .content-container {
                margin-left: 20px;
            }
            .sidebar-toggle {
                display: flex;
            }
            .form-container {
                padding: 20px;
            }
        }
        @media (min-width: 1200px) {
            .form-container {
                padding: 20px 0;
                margin-left: 0;
                max-width: 100%;
            }
            .form-container .form-label {
                text-align: left;
                margin-left: 0;
            }
            .form-container .form-control,
            .form-container .form-check {
                text-align: left;
                margin-left: 0;
                width: 100%;
            }
            .form-container .btn-primary {
                margin-left: 0;
            }
            /* Specific fix for the checkbox */
            .form-check {
                display: flex;
                align-items: center;
            }
            .form-check-input {
                position: relative;
                margin-top: 0;
                margin-right: 0.5em;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar-container" id="sidebarContainer">
            <!-- User Info Section -->
            <div class="user-info">
                <i class="fas fa-user"></i>
                <h4><?php echo htmlspecialchars($_SESSION['user']); ?></h4>
            </div>

            <!-- Sidebar Menu -->
            <div class="sidebar">
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

                <?php if ($_SESSION['is_admin']): ?>
                    <div class="admin-section">
                        <h4 onclick="toggleAdminLinks()"><i class="fas fa-cogs"></i> Admin <i class="fas fa-chevron-down"></i></h4>
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
                            <a href="view_logs.php">
                                <i class="fas fa-clipboard-list"></i> View Logs
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Main Content -->
        <div class="content-container" id="contentContainer">
            <h3 class="text-dark mb-4">Edit Employee</h3>
            <div id="status-message" class="alert" role="alert"></div>
            <div class="form-container">
                <form id="editEmployeeForm">
                    <input type="hidden" name="emp_id" id="emp_id">
                    
                    <div class="mb-3">
                        <label class="form-label">Employee Name:</label>
                        <input type="text" name="emp_name" id="emp_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email:</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mobile Number:</label>
                        <input type="text" name="mobile_number" id="mobile_number" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Designation:</label>
                        <input type="text" name="designation" id="designation" class="form-control" required>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" name="is_admin" id="is_admin" value="1">
                        <label class="form-check-label">Admin Access</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Employee</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Session Timeout Popup -->
    <div id="sessionPopup" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Session Expiring Soon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Your session will expire in 2 minutes. Please save your work.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Session timeout in milliseconds (5 minutes)
        const sessionTimeout = 5 * 60 * 1000;
        const popupTime = 2 * 60 * 1000;

        // Show the session timeout popup
        setTimeout(() => {
            const sessionPopup = new bootstrap.Modal(document.getElementById('sessionPopup'));
            sessionPopup.show();
            
            // Log session timeout warning
            $.ajax({
                url: 'log_api.php',
                type: 'POST',
                data: {
                    action: 'log_client_action',
                    action_type: 'session_timeout_warning',
                    description: 'Session timeout warning shown on edit employee page'
                },
                dataType: 'json'
            });
        }, sessionTimeout - popupTime);

        // Logout the user after session timeout
        setTimeout(() => {
            window.location.href = 'logout.php';
        }, sessionTimeout);

        function toggleAdminLinks() {
            const adminLinks = document.querySelector('.admin-links');
            adminLinks.style.display = adminLinks.style.display === 'block' ? 'none' : 'block';
            
            // Log admin links toggle
            $.ajax({
                url: 'log_api.php',
                type: 'POST',
                data: {
                    action: 'log_client_action',
                    action_type: 'admin_links_toggle',
                    description: 'Toggled admin links visibility'
                },
                dataType: 'json'
            });
        }

        $(document).ready(function() {
            // Sidebar toggle
            $('#sidebarToggle').click(function() {
                $('#sidebarContainer').toggleClass('show');
                $('#contentContainer').toggleClass('expanded');
            });

            // Close sidebar when clicking outside on mobile/tablet
            $(document).click(function(e) {
                if ($(window).width() < 1200) {
                    if (!$(e.target).closest('#sidebarContainer').length && 
                        !$(e.target).is('#sidebarToggle') && 
                        $('#sidebarContainer').hasClass('show')) {
                        $('#sidebarContainer').removeClass('show');
                        $('#contentContainer').addClass('expanded');
                    }
                }
            });

            // Handle window resize
            $(window).resize(function() {
                if ($(window).width() >= 1200) {
                    $('#sidebarContainer').removeClass('show');
                    $('#contentContainer').removeClass('expanded');
                }
            });

            // Enhanced logging function
            function logAction(action, details = {}, showMessage = false, messageType = '') {
                // Send to server logs
                $.ajax({
                    url: 'log_api.php',
                    method: 'POST',
                    data: {
                        action: 'client_log',
                        log_type: action,
                        user_id: '<?php echo $_SESSION['emp_id']; ?>',
                        username: '<?php echo $_SESSION['user']; ?>',
                        details: JSON.stringify(details),
                        message: details.message || '',
                        page: 'edit_employee'
                    },
                    error: function(xhr) {
                        console.error('Logging failed:', xhr.responseText);
                    }
                });

                // Show message to user if requested
                if (showMessage && details.message) {
                    const $msg = $('#status-message');
                    $msg.removeClass('alert-success alert-danger')
                        .addClass(messageType === 'success' ? 'alert-success' : 'alert-danger')
                        .text(details.message)
                        .show();
                }
            }

            // Log page load
            logAction('page_load', {url: window.location.href});
            
            const empId = new URLSearchParams(window.location.search).get('id');
            if (!empId) {
                logAction('error', {message: 'Employee ID missing'}, true, 'error');
                window.location.href = 'employees.php';
                return;
            }

            // Fetch employee data
            $.ajax({
                url: `api.php?action=getEmployee&id=${empId}`,
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        // Populate form fields
                        const emp = response.data;
                        $('#emp_id').val(emp.emp_id);
                        $('#emp_name').val(emp.emp_name);
                        $('#email').val(emp.email);
                        $('#mobile_number').val(emp.mobile_number);
                        $('#designation').val(emp.designation);
                        $('#is_admin').prop('checked', emp.is_admin == 1);
                    } else {
                        logAction('employee_fetch_failed', {
                            message: response.message || 'Failed to load employee data',
                            employee_id: empId
                        }, true, 'error');
                    }
                },
                error: function(xhr) {
                    logAction('employee_fetch_error', {
                        message: 'Network error loading employee data',
                        status: xhr.status
                    }, true, 'error');
                }
            });

            // Handle form submission
            $('#editEmployeeForm').submit(function(e) {
                e.preventDefault();
                
                const formData = {
                    emp_id: $('#emp_id').val(),
                    emp_name: $('#emp_name').val(),
                    email: $('#email').val(),
                    mobile_number: $('#mobile_number').val(),
                    designation: $('#designation').val(),
                    is_admin: $('#is_admin').is(':checked') ? 1 : 0
                };

                logAction('employee_update_attempt', formData);

                $.ajax({
                    url: 'api.php?action=updateEmployee',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(formData),
                    success: function(response) {
                        if (response.status === 'success') {
                            logAction('employee_update_success', {
                                message: response.message,
                                employee_id: formData.emp_id,
                                changes: formData
                            }, true, 'success');
                            
                            setTimeout(() => {
                                window.location.href = 'employees.php';
                            }, 2000);
                        } else {
                            logAction('employee_update_failed', {
                                message: response.message || 'Update failed',
                                employee_id: formData.emp_id
                            }, true, 'error');
                        }
                    },
                    error: function(xhr) {
                        logAction('employee_update_error', {
                            message: 'Server error during update',
                            status: xhr.status,
                            response: xhr.responseText
                        }, true, 'error');
                    }
                });
            });
        });
    </script>
</body>
</html>