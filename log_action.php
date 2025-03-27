<?php
session_start();
require 'db_config.php'; // Include your database connection file

// Get data from the AJAX request
$user_id = $_POST['user_id'];
$username = $_POST['username'];
$action_type = $_POST['action_type'];
$action_description = $_POST['action_description'];
$endpoint = $_POST['endpoint'];
$http_method = $_POST['http_method'];
$request_payload = $_POST['request_payload'];
$response_status = $_POST['response_status'];
$response_data = $_POST['response_data'];
$ip_address = $_POST['ip_address'];
$user_agent = $_POST['user_agent'];

// Log the action
logAction($conn, $user_id, $username, $action_type, $action_description, $endpoint, $http_method, $request_payload, $response_status, $response_data, $ip_address, $user_agent);

echo json_encode(['status' => 'success']);