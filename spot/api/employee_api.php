<?php
header("Content-Type: application/json");
// At the top of employee_api.php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
include_once __DIR__ . '/../db_config.php';
include 'log_api.php';

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0755, true);
}

// Function to handle file uploads
function handleFileUpload($fileInputName) {
    $targetDir = "uploads/";
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!isset($_FILES[$fileInputName])) {
        return ['error' => 'No file uploaded'];
    }
    
    $file = $_FILES[$fileInputName];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error: ' . $file['error']];
    }
    
    // Check file type
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExt, $allowedTypes)) {
        return ['error' => 'Invalid file type. Only JPG, JPEG, PNG, GIF allowed'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        return ['error' => 'File too large. Max 2MB allowed'];
    }
    
    // Verify image
    if (!getimagesize($file['tmp_name'])) {
        return ['error' => 'File is not a valid image'];
    }
    
    // Generate unique filename
    $fileName = uniqid() . '.' . $fileExt;
    $targetPath = $targetDir . $fileName;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['success' => true, 'path' => $targetPath];
    } else {
        return ['error' => 'Failed to move uploaded file'];
    }
}

// Function to send JSON response with detailed logging
function sendResponse($status, $message, $data = null, $http_status_code = 200) {
    global $conn;

    $response = ["status" => $status, "message" => $message];
    if ($data !== null) {
        $response["data"] = $data;
    }

    // Get the current endpoint
    $endpoint = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Get the request payload
    $request_payload = file_get_contents("php://input");
    $decoded_payload = json_decode($request_payload, true) ?: [];

    // Log the API request with all details
    logUserAction(
        isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
        isset($_SESSION['username']) ? $_SESSION['username'] : 'API_USER',
        "EMPLOYEE_API_" . strtoupper($method),
        $message,
        $endpoint,
        $method,
        $decoded_payload,
        $status,
        $response,
        $_SERVER['REMOTE_ADDR'],
        $_SERVER['HTTP_USER_AGENT']
    );

    // Set HTTP status code
    http_response_code($http_status_code);
    
    echo json_encode($response);
    exit;
}

// Function to hash password
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Function to validate email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to get ID from name for related tables
function getRelatedId($conn, $table, $nameField, $idField, $name) {
    $sql = "SELECT $idField FROM $table WHERE $nameField = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row[$idField];
    }
    return null;
}

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'POST': // Create Employee
            // Handle file upload first
            $photoPath = null;
            if (!empty($_FILES['photo']['name'])) {
                $uploadResult = handleFileUpload('photo');
                if (isset($uploadResult['error'])) {
                    sendResponse("error", $uploadResult['error'], null, 400);
                }
                $photoPath = $uploadResult['path'];
            }
            
            // Get other form data
            $data = $_POST;
            $data['photo'] = $photoPath;
            
            // Validate required fields
            $requiredFields = ['emp_name', 'email', 'mobile_number', 'address', 'emp_pincode', 
                              'designation', 'password', 'zone_name', 'dcm_name', 'cluster_name'];
            
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    // Clean up uploaded photo if validation fails
                    if ($photoPath && file_exists($photoPath)) {
                        unlink($photoPath);
                    }
                    sendResponse("error", "Field '$field' is required", null, 400);
                }
            }
        
            // Start transaction
            $conn->begin_transaction();
        
            try {
                // Insert new employee
                $sql = "INSERT INTO employee (
                    emp_name, email, mobile_number, aadhar_number, pan_number, photo, 
                    address, emp_pincode, designation, password, is_admin, 
                    zone_id, zone_name, dcm_id, dcm_name, 
                    cluster_id, cluster_name, shift_id, shift_name
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Database error: " . $conn->error);
                }
                
                // Bind parameters and execute
                $stmt->bind_param(
                    "ssssssssssissssssss",
                    $data['emp_name'],
                    $data['email'],
                    $data['mobile_number'],
                    $data['aadhar_number'],
                    $data['pan_number'],
                    $photoPath,
                    $data['address'],
                    $data['emp_pincode'],
                    $data['designation'],
                    $hashedPassword,
                    $is_admin,
                    $zone_id,
                    $data['zone_name'],
                    $dcm_id,
                    $data['dcm_name'],
                    $cluster_id,
                    $data['cluster_name'],
                    $shift_id,
                    $shift_name
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Error creating employee: " . $stmt->error);
                }
        
                // Get the inserted ID - use LAST_INSERT_ID() directly
                $employee_id = $conn->insert_id;
                if ($employee_id == 0) {
                    $result = $conn->query("SELECT LAST_INSERT_ID() as id");
                    if ($result && $row = $result->fetch_assoc()) {
                        $employee_id = $row['id'];
                    } else {
                        throw new Exception("Could not retrieve employee ID after creation");
                    }
                }
        
                // Commit transaction
                $conn->commit();
        
                sendResponse("success", "Employee created successfully", ["employee_id" => $employee_id], 201);
                
            } catch (Exception $e) {
                // Rollback on error and delete uploaded photo
                $conn->rollback();
                if ($photoPath && file_exists($photoPath)) {
                    unlink($photoPath);
                }
                sendResponse("error", $e->getMessage(), null, 500);
            } finally {
                if (isset($stmt)) $stmt->close();
            }
            break;

        case 'GET': // Retrieve Employees (All or Specific)
            if (isset($_GET['employee_id'])) {
                // Get single employee
                $employee_id = $conn->real_escape_string($_GET['employee_id']);
                $sql = "SELECT * FROM employee WHERE employee_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $employee_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $employee = $result->fetch_assoc();
                    // Remove password from response
                    unset($employee['password']);
                    sendResponse("success", "Employee found", $employee);
                } else {
                    sendResponse("error", "Employee not found", null, 404);
                }
                $stmt->close();
            } else {
                // Get all employees with optional pagination and filters
                $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
                $limit = isset($_GET['limit']) ? max(1, intval($_GET['limit'])) : 100;
                $offset = ($page - 1) * $limit;

                // Build WHERE clause for filters
                $where = [];
                $params = [];
                $types = "";

                if (isset($_GET['zone_name'])) {
                    $where[] = "zone_name = ?";
                    $params[] = $_GET['zone_name'];
                    $types .= "s";
                }

                if (isset($_GET['cluster_name'])) {
                    $where[] = "cluster_name = ?";
                    $params[] = $_GET['cluster_name'];
                    $types .= "s";
                }

                if (isset($_GET['designation'])) {
                    $where[] = "designation = ?";
                    $params[] = $_GET['designation'];
                    $types .= "s";
                }

                $where_clause = empty($where) ? "" : "WHERE " . implode(" AND ", $where);

                $sql = "SELECT SQL_CALC_FOUND_ROWS employee_id, emp_name, email, mobile_number, 
                        aadhar_number, pan_number, photo, address, emp_pincode, designation, 
                        is_admin, zone_id, zone_name, dcm_id, dcm_name, cluster_id, cluster_name, 
                        shift_id, shift_name, created_at, updated_at 
                        FROM employee $where_clause LIMIT ? OFFSET ?";
                
                $stmt = $conn->prepare($sql);
                
                // Bind parameters if there are filters
                if (!empty($params)) {
                    $params[] = $limit;
                    $params[] = $offset;
                    $types .= "ii";
                    $stmt->bind_param($types, ...$params);
                } else {
                    $stmt->bind_param("ii", $limit, $offset);
                }
                
                $stmt->execute();
                $result = $stmt->get_result();
                
                if (!$result) {
                    sendResponse("error", "Database error: " . $conn->error, null, 500);
                }
                
                $employees = [];
                while ($row = $result->fetch_assoc()) {
                    $employees[] = $row;
                }

                // Get total count
                $total_result = $conn->query("SELECT FOUND_ROWS() AS total");
                $total = $total_result->fetch_assoc()['total'];
                $total_pages = ceil($total / $limit);

                $meta = [
                    'page' => $page,
                    'limit' => $limit,
                    'total_items' => $total,
                    'total_pages' => $total_pages
                ];

                sendResponse("success", "Employees retrieved", ['employees' => $employees, 'meta' => $meta]);
                $stmt->close();
            }
            break;

        case 'PUT': // Update Employee
            // Handle file upload if present
            $photoPath = null;
            if (!empty($_FILES['photo']['name'])) {
                $uploadResult = handleFileUpload('photo');
                if (isset($uploadResult['error'])) {
                    sendResponse("error", $uploadResult['error'], null, 400);
                }
                $photoPath = $uploadResult['path'];
                
                // Delete old photo if it exists
                if (!empty($_POST['current_photo']) && file_exists($_POST['current_photo'])) {
                    unlink($_POST['current_photo']);
                }
            } elseif (!empty($_POST['current_photo'])) {
                $photoPath = $_POST['current_photo'];
            }
            
            // Get other form data
            $data = $_POST;
            $data['photo'] = $photoPath;
            
            // Validate employee_id
            if (empty($data['employee_id'])) {
                if ($photoPath && !empty($_FILES['photo']['name'])) {
                    unlink($photoPath);
                }
                sendResponse("error", "Employee ID is required", null, 400);
            }

            $employee_id = $conn->real_escape_string($data['employee_id']);
            $updates = [];
            $params = [];
            $types = "";

            // Build dynamic update query
            if (isset($data['emp_name'])) {
                $updates[] = "emp_name = ?";
                $params[] = $conn->real_escape_string($data['emp_name']);
                $types .= "s";
            }
            if (isset($data['email'])) {
                if (!validateEmail($data['email'])) {
                    if ($photoPath && !empty($_FILES['photo']['name'])) {
                        unlink($photoPath);
                    }
                    sendResponse("error", "Invalid email format", null, 400);
                }
                $updates[] = "email = ?";
                $params[] = $conn->real_escape_string($data['email']);
                $types .= "s";
            }
            if (isset($data['mobile_number'])) {
                $updates[] = "mobile_number = ?";
                $params[] = $conn->real_escape_string($data['mobile_number']);
                $types .= "s";
            }
            if (isset($data['aadhar_number'])) {
                $updates[] = "aadhar_number = ?";
                $params[] = $conn->real_escape_string($data['aadhar_number']);
                $types .= "s";
            }
            if (isset($data['pan_number'])) {
                $updates[] = "pan_number = ?";
                $params[] = $conn->real_escape_string($data['pan_number']);
                $types .= "s";
            }
            if (isset($data['photo'])) {
                $updates[] = "photo = ?";
                $params[] = $data['photo'];
                $types .= "s";
            }
            if (isset($data['address'])) {
                $updates[] = "address = ?";
                $params[] = $conn->real_escape_string($data['address']);
                $types .= "s";
            }
            if (isset($data['emp_pincode'])) {
                $updates[] = "emp_pincode = ?";
                $params[] = $conn->real_escape_string($data['emp_pincode']);
                $types .= "s";
            }
            if (isset($data['designation'])) {
                $updates[] = "designation = ?";
                $params[] = $conn->real_escape_string($data['designation']);
                $types .= "s";
            }
            if (isset($data['password']) && !empty($data['password'])) {
                $hashedPassword = hashPassword($data['password']);
                $updates[] = "password = ?";
                $params[] = $hashedPassword;
                $types .= "s";
            }
            if (isset($data['is_admin'])) {
                $updates[] = "is_admin = ?";
                $params[] = $conn->real_escape_string($data['is_admin']);
                $types .= "i";
            }
            if (isset($data['zone_name'])) {
                $zone_id = getRelatedId($conn, 'zone', 'zone_name', 'zone_id', $data['zone_name']);
                if (!$zone_id) {
                    if ($photoPath && !empty($_FILES['photo']['name'])) {
                        unlink($photoPath);
                    }
                    sendResponse("error", "Invalid zone name", null, 400);
                }
                $updates[] = "zone_id = ?";
                $updates[] = "zone_name = ?";
                $params[] = $zone_id;
                $params[] = $conn->real_escape_string($data['zone_name']);
                $types .= "is";
            }
            if (isset($data['dcm_name'])) {
                $dcm_id = getRelatedId($conn, 'dcm', 'dcm_name', 'dcm_id', $data['dcm_name']);
                if (!$dcm_id) {
                    if ($photoPath && !empty($_FILES['photo']['name'])) {
                        unlink($photoPath);
                    }
                    sendResponse("error", "Invalid DCM name", null, 400);
                }
                $updates[] = "dcm_id = ?";
                $updates[] = "dcm_name = ?";
                $params[] = $dcm_id;
                $params[] = $conn->real_escape_string($data['dcm_name']);
                $types .= "is";
            }
            if (isset($data['cluster_name'])) {
                $cluster_id = getRelatedId($conn, 'cluster', 'cluster_name', 'cluster_id', $data['cluster_name']);
                if (!$cluster_id) {
                    if ($photoPath && !empty($_FILES['photo']['name'])) {
                        unlink($photoPath);
                    }
                    sendResponse("error", "Invalid cluster name", null, 400);
                }
                $updates[] = "cluster_id = ?";
                $updates[] = "cluster_name = ?";
                $params[] = $cluster_id;
                $params[] = $conn->real_escape_string($data['cluster_name']);
                $types .= "is";
            }
            if (isset($data['shift_name'])) {
                $shift_id = getRelatedId($conn, 'shift', 'shift_name', 'shift_id', $data['shift_name']);
                if (!$shift_id) {
                    if ($photoPath && !empty($_FILES['photo']['name'])) {
                        unlink($photoPath);
                    }
                    sendResponse("error", "Invalid shift name", null, 400);
                }
                $updates[] = "shift_id = ?";
                $updates[] = "shift_name = ?";
                $params[] = $shift_id;
                $params[] = $conn->real_escape_string($data['shift_name']);
                $types .= "is";
            }

            if (empty($updates)) {
                if ($photoPath && !empty($_FILES['photo']['name'])) {
                    unlink($photoPath);
                }
                sendResponse("error", "No fields to update", null, 400);
            }

            // Add employee_id to params
            $params[] = $employee_id;
            $types .= "i";

            $sql = "UPDATE employee SET " . implode(", ", $updates) . " WHERE employee_id = ?";
            $stmt = $conn->prepare($sql);
            
            if (!$stmt) {
                if ($photoPath && !empty($_FILES['photo']['name'])) {
                    unlink($photoPath);
                }
                sendResponse("error", "Database error: " . $conn->error, null, 500);
            }
            
            $stmt->bind_param($types, ...$params);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    sendResponse("success", "Employee updated successfully", ["employee_id" => $employee_id]);
                } else {
                    sendResponse("error", "No changes made or employee not found", null, 404);
                }
            } else {
                sendResponse("error", "Error updating employee: " . $stmt->error, null, 500);
            }
            $stmt->close();
            break;

        case 'DELETE': // Delete Employee
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (empty($data['employee_id'])) {
                sendResponse("error", "Employee ID is required", null, 400);
            }

            $employee_id = $conn->real_escape_string($data['employee_id']);

            // First get employee data to delete photo
            $sql = "SELECT photo FROM employee WHERE employee_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $employee_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                sendResponse("error", "Employee not found", null, 404);
            }
            
            $employee = $result->fetch_assoc();
            
            // Delete the employee
            $delete_sql = "DELETE FROM employee WHERE employee_id = ?";
            $delete_stmt = $conn->prepare($delete_sql);
            $delete_stmt->bind_param("i", $employee_id);
            
            if ($delete_stmt->execute()) {
                // Delete photo file if it exists
                if (!empty($employee['photo']) && file_exists($employee['photo'])) {
                    unlink($employee['photo']);
                }
                
                if ($delete_stmt->affected_rows > 0) {
                    sendResponse("success", "Employee deleted successfully");
                } else {
                    sendResponse("error", "No employee was deleted", null, 404);
                }
            } else {
                sendResponse("error", "Error deleting employee: " . $delete_stmt->error, null, 500);
            }
            
            $stmt->close();
            $delete_stmt->close();
            break;

        default:
            sendResponse("error", "Invalid request method", null, 405);
    }
} catch (Exception $e) {
    error_log("Employee API Error: " . $e->getMessage());
    sendResponse("error", "An unexpected error occurred: " . $e->getMessage(), null, 500);
} finally {
    if (isset($conn) && $conn) {
        $conn->close();
    }
}
?>