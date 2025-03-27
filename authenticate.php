<?php
session_start();
include 'db_config.php';
include 'log_api.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_or_mobile = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email_or_mobile) || empty($password)) {
        $_SESSION['error'] = "Email/Mobile and Password are required.";
        header("Location: index.php");
        exit();
    }

    // Determine if input is email or mobile number
    $is_email = filter_var($email_or_mobile, FILTER_VALIDATE_EMAIL);
    $is_mobile = preg_match('/^[6-9]\d{9}$/', $email_or_mobile);

    if (!$is_email && !$is_mobile) {
        $_SESSION['error'] = "Invalid email or mobile number format.";
        header("Location: index.php");
        exit();
    }

    // Fetch user details securely
    $sql = "SELECT emp_id, emp_name, is_admin, password, email FROM employees WHERE email = ? OR mobile_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email_or_mobile, $email_or_mobile);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];

        // Migrate MD5 Passwords to Bcrypt
        if (strlen($stored_password) === 32 && ctype_xdigit($stored_password)) {
            if (md5($password) === $stored_password) {
                // Convert MD5 to bcrypt
                $new_password_hash = password_hash($password, PASSWORD_BCRYPT);
                $update_sql = "UPDATE employees SET password = ? WHERE email = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $new_password_hash, $row['email']);
                $update_stmt->execute();
            } else {
                $_SESSION['error'] = "Invalid email/mobile or password.";
                header("Location: index.php");
                exit();
            }
        } 
        // Verify Bcrypt Password
        elseif (!password_verify($password, $stored_password)) {
            $_SESSION['error'] = "Invalid email/mobile or password.";
            header("Location: index.php");
            exit();
        }

        // Secure Session Handling
        session_regenerate_id(true); // Prevent session fixation attack
        
        // Store all necessary user information in session
        $_SESSION['emp_id'] = $row['emp_id']; // Store emp_id
        $_SESSION['user'] = $row['emp_name'];
        $_SESSION['is_admin'] = (int)$row['is_admin'] === 1;
        $_SESSION['last_activity'] = time();

        // Log the login action
        logUserAction(
            $row['emp_id'], // user_id
            $row['emp_name'], // username
            'login', // action_type
            'User logged in successfully', // action_description
            $_SERVER['REQUEST_URI'], // endpoint
            $_SERVER['REQUEST_METHOD'], // http_method
            ['email' => $email_or_mobile], // request_payload (don't log password)
            200, // response_status
            ['message' => 'Login successful'], // response_data
            $_SERVER['REMOTE_ADDR'], // ip_address
            $_SERVER['HTTP_USER_AGENT'] // user_agent
        );

        header("Location: home.php");
        exit();
    } else {
        // Log failed login attempt
        logUserAction(
            null, // No user_id for failed attempts
            $email_or_mobile, // Use the attempted email/mobile as username
            'login_failed', // action_type
            'Failed login attempt', // action_description
            $_SERVER['REQUEST_URI'], // endpoint
            $_SERVER['REQUEST_METHOD'], // http_method
            ['email' => $email_or_mobile], // request_payload
            401, // response_status
            ['message' => 'Invalid credentials'], // response_data
            $_SERVER['REMOTE_ADDR'], // ip_address
            $_SERVER['HTTP_USER_AGENT'] // user_agent
        );

        $_SESSION['error'] = "Invalid email/mobile or password.";
        header("Location: index.php");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: index.php");
    exit();
}
?>