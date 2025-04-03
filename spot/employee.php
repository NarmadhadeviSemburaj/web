<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1e88e5;
            --light-blue: #e3f2fd;
            --dark-blue: #1565c0;
            --ribbon-height: 60px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            padding-top: var(--ribbon-height);
        }
        
        .ribbon {
            background-color: var(--primary-blue);
            color: white;
            height: var(--ribbon-height);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            padding: 0 20px;
        }
        
        .ribbon-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-right: 30px;
        }
        
        .nav-tabs .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border: none;
            font-weight: 500;
            padding: 10px 15px;
            margin-right: 5px;
        }
        
        .nav-tabs .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
            border-bottom: 3px solid white;
        }
        
        .nav-tabs .nav-link:hover {
            color: white;
        }
        
        .main-container {
            padding: 20px;
            margin-top: 20px;
        }
        
        .card {
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border: none;
        }
        
        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            font-weight: 600;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0 !important;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .btn-primary {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .btn-primary:hover {
            background-color: var(--dark-blue);
            border-color: var(--dark-blue);
        }
        
        .btn-outline-primary {
            color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .btn-outline-primary:hover {
            background-color: var(--primary-blue);
            color: white;
        }
        
        .table th {
            background-color: var(--light-blue);
            color: var(--dark-blue);
        }
        
        .action-btns .btn {
            padding: 5px 10px;
            margin-right: 5px;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-blue);
            border-color: var(--primary-blue);
        }
        
        .pagination .page-link {
            color: var(--primary-blue);
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-inactive {
            background-color: #fff3e0;
            color: #e65100;
        }
        
        .form-section {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        #responseMessage {
            display: none;
        }
        
        .loading-spinner {
            display: none;
            color: var(--primary-blue);
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        .employee-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .photo-preview {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
            margin-top: 10px;
            display: none;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                display: block;
                width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
        }
    </style>
</head>
<body>
    <!-- Blue Ribbon Header -->
    <div class="ribbon">
        <div class="ribbon-title">Employee Management</div>
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link active" href="#" id="employeesTab"><i class="fas fa-users me-2"></i>Employees</a>
            </li>
        </ul>
    </div>

    <div class="main-container container-fluid">
        <!-- Response Message -->
        <div id="responseMessage" class="alert mb-4"></div>

        <div class="row">
            <!-- Create Employee Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user-plus me-2"></i>Add New Employee
                    </div>
                    <div class="card-body">
                        <form id="createEmployeeForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="empName" class="form-label">Full Name*</label>
                                <input type="text" class="form-control" id="empName" name="emp_name" required minlength="2">
                                <div class="invalid-feedback">Please enter a valid name (minimum 2 characters)</div>
                            </div>
                            <div class="mb-3">
                                <label for="empEmail" class="form-label">Email*</label>
                                <input type="email" class="form-control" id="empEmail" name="email" required>
                                <div class="invalid-feedback">Please enter a valid email address</div>
                            </div>
                            <div class="mb-3">
                                <label for="empMobile" class="form-label">Mobile Number*</label>
                                <input type="tel" class="form-control" id="empMobile" name="mobile_number" required pattern="[0-9]{10}">
                                <div class="invalid-feedback">Please enter a valid 10-digit mobile number</div>
                            </div>
                            <div class="mb-3">
                                <label for="empAadhar" class="form-label">Aadhar Number</label>
                                <input type="text" class="form-control" id="empAadhar" name="aadhar_number">
                            </div>
                            <div class="mb-3">
                                <label for="empPan" class="form-label">PAN Number</label>
                                <input type="text" class="form-control" id="empPan" name="pan_number">
                            </div>
                            <div class="mb-3">
                                <label for="empAddress" class="form-label">Address*</label>
                                <textarea class="form-control" id="empAddress" name="address" rows="2" required></textarea>
                                <div class="invalid-feedback">Please enter an address</div>
                            </div>
                            <div class="mb-3">
                                <label for="empPincode" class="form-label">Pincode*</label>
                                <input type="text" class="form-control" id="empPincode" name="emp_pincode" required pattern="[0-9]{6}">
                                <div class="invalid-feedback">Please enter a valid 6-digit pincode</div>
                            </div>
                            <div class="mb-3">
                                <label for="empDesignation" class="form-label">Designation*</label>
                                <input type="text" class="form-control" id="empDesignation" name="designation" required>
                                <div class="invalid-feedback">Please enter a designation</div>
                            </div>
                            <div class="mb-3">
                                <label for="empPassword" class="form-label">Password*</label>
                                <input type="password" class="form-control" id="empPassword" name="password" required minlength="6">
                                <div class="invalid-feedback">Password must be at least 6 characters</div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="empIsAdmin" name="is_admin">
                                <label class="form-check-label" for="empIsAdmin">Is Admin</label>
                            </div>
                            <div class="mb-3">
                                <label for="empZone" class="form-label">Zone Name*</label>
                                <select class="form-select" id="empZone" name="zone_name" required>
                                    <option value="">Select Zone</option>
                                </select>
                                <div class="invalid-feedback">Please select a zone</div>
                            </div>
                            <div class="mb-3">
                                <label for="empDcm" class="form-label">DCM Name*</label>
                                <select class="form-select" id="empDcm" name="dcm_name" required>
                                    <option value="">Select DCM</option>
                                </select>
                                <div class="invalid-feedback">Please select a DCM</div>
                            </div>
                            <div class="mb-3">
                                <label for="empCluster" class="form-label">Cluster Name*</label>
                                <select class="form-select" id="empCluster" name="cluster_name" required>
                                    <option value="">Select Cluster</option>
                                </select>
                                <div class="invalid-feedback">Please select a cluster</div>
                            </div>
                            <div class="mb-3">
                                <label for="empShift" class="form-label">Shift Name</label>
                                <select class="form-select" id="empShift" name="shift_name">
                                    <option value="">Select Shift</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="empPhoto" class="form-label">Photo</label>
                                <input type="file" class="form-control" id="empPhoto" name="photo" accept="image/*">
                                <img id="photoPreview" class="photo-preview" alt="Photo Preview">
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <span class="loading-spinner spinner-border spinner-border-sm me-2 d-none"></span>
                                Add Employee
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Employees List Card -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-users me-2"></i>Employee List
                        </div>
                        <div class="d-flex">
                            <input type="text" id="searchInput" class="form-control form-control-sm me-2" placeholder="Search employees..." style="width: 200px;">
                            <select id="zoneFilter" class="form-select form-select-sm me-2" style="width: 150px;">
                                <option value="">All Zones</option>
                            </select>
                            <select id="perPageSelect" class="form-select form-select-sm" style="width: 80px;">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Designation</th>
                                        <th>Zone</th>
                                        <th>DCM</th>
                                        <th>Cluster</th>
                                        <th>Admin</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="employeesTableBody">
                                    <!-- Employees will be loaded here -->
                                </tbody>
                            </table>
                        </div>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center" id="pagination">
                                <!-- Pagination will be loaded here -->
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Employee Modal -->
        <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Employee</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editEmployeeForm" enctype="multipart/form-data">
                            <input type="hidden" id="editEmployeeId" name="employee_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editEmpName" class="form-label">Full Name*</label>
                                        <input type="text" class="form-control" id="editEmpName" name="emp_name" required minlength="2">
                                        <div class="invalid-feedback">Please enter a valid name (minimum 2 characters)</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpEmail" class="form-label">Email*</label>
                                        <input type="email" class="form-control" id="editEmpEmail" name="email" required>
                                        <div class="invalid-feedback">Please enter a valid email address</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpMobile" class="form-label">Mobile Number*</label>
                                        <input type="tel" class="form-control" id="editEmpMobile" name="mobile_number" required pattern="[0-9]{10}">
                                        <div class="invalid-feedback">Please enter a valid 10-digit mobile number</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpAadhar" class="form-label">Aadhar Number</label>
                                        <input type="text" class="form-control" id="editEmpAadhar" name="aadhar_number">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpPan" class="form-label">PAN Number</label>
                                        <input type="text" class="form-control" id="editEmpPan" name="pan_number">
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpAddress" class="form-label">Address*</label>
                                        <textarea class="form-control" id="editEmpAddress" name="address" rows="2" required></textarea>
                                        <div class="invalid-feedback">Please enter an address</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="editEmpPincode" class="form-label">Pincode*</label>
                                        <input type="text" class="form-control" id="editEmpPincode" name="emp_pincode" required pattern="[0-9]{6}">
                                        <div class="invalid-feedback">Please enter a valid 6-digit pincode</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpDesignation" class="form-label">Designation*</label>
                                        <input type="text" class="form-control" id="editEmpDesignation" name="designation" required>
                                        <div class="invalid-feedback">Please enter a designation</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpPassword" class="form-label">Password (leave blank to keep current)</label>
                                        <input type="password" class="form-control" id="editEmpPassword" name="password" minlength="6">
                                        <div class="invalid-feedback">Password must be at least 6 characters</div>
                                    </div>
                                    <div class="mb-3 form-check">
                                        <input type="checkbox" class="form-check-input" id="editEmpIsAdmin" name="is_admin">
                                        <label class="form-check-label" for="editEmpIsAdmin">Is Admin</label>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpZone" class="form-label">Zone Name*</label>
                                        <select class="form-select" id="editEmpZone" name="zone_name" required>
                                            <option value="">Select Zone</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a zone</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpDcm" class="form-label">DCM Name*</label>
                                        <select class="form-select" id="editEmpDcm" name="dcm_name" required>
                                            <option value="">Select DCM</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a DCM</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpCluster" class="form-label">Cluster Name*</label>
                                        <select class="form-select" id="editEmpCluster" name="cluster_name" required>
                                            <option value="">Select Cluster</option>
                                        </select>
                                        <div class="invalid-feedback">Please select a cluster</div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpShift" class="form-label">Shift Name</label>
                                        <select class="form-select" id="editEmpShift" name="shift_name">
                                            <option value="">Select Shift</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="editEmpPhoto" class="form-label">Photo</label>
                                        <input type="file" class="form-control" id="editEmpPhoto" name="photo" accept="image/*">
                                        <img id="editPhotoPreview" class="photo-preview" alt="Photo Preview">
                                        <input type="hidden" id="currentPhoto" name="current_photo">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveEmployeeChanges">
                            <span class="loading-spinner spinner-border spinner-border-sm me-2 d-none"></span>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this employee? This action cannot be undone.</p>
                        <p><strong>Employee ID:</strong> <span id="deleteEmployeeIdText"></span></p>
                        <p><strong>Employee Name:</strong> <span id="deleteEmployeeNameText"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteEmployee">
                            <span class="loading-spinner spinner-border spinner-border-sm me-2 d-none"></span>
                            Delete Employee
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Global variables
            let currentPage = 1;
            let perPage = 10;
            let totalPages = 1;
            let searchQuery = '';
            let zoneFilter = '';
            let zones = [];
            let dcms = [];
            let clusters = [];
            let shifts = [];
            
            // DOM elements
            const employeesTableBody = document.getElementById('employeesTableBody');
            const pagination = document.getElementById('pagination');
            const perPageSelect = document.getElementById('perPageSelect');
            const searchInput = document.getElementById('searchInput');
            const zoneFilterSelect = document.getElementById('zoneFilter');
            const responseMessage = document.getElementById('responseMessage');
            
            // Dropdown elements
            const empZoneSelect = document.getElementById('empZone');
            const empDcmSelect = document.getElementById('empDcm');
            const empClusterSelect = document.getElementById('empCluster');
            const empShiftSelect = document.getElementById('empShift');
            
            // Edit form dropdowns
            const editEmpZoneSelect = document.getElementById('editEmpZone');
            const editEmpDcmSelect = document.getElementById('editEmpDcm');
            const editEmpClusterSelect = document.getElementById('editEmpCluster');
            const editEmpShiftSelect = document.getElementById('editEmpShift');
            
            // Photo preview elements
            const photoPreview = document.getElementById('photoPreview');
            const editPhotoPreview = document.getElementById('editPhotoPreview');
            
            // Initialize the page
            loadReferenceData();
            loadEmployees();
            
            // Event listeners
            document.getElementById('createEmployeeForm').addEventListener('submit', createEmployee);
            perPageSelect.addEventListener('change', updatePerPage);
            searchInput.addEventListener('input', updateSearchQuery);
            zoneFilterSelect.addEventListener('change', updateZoneFilter);
            document.getElementById('empPhoto').addEventListener('change', handlePhotoPreview);
            document.getElementById('editEmpPhoto').addEventListener('change', handleEditPhotoPreview);
            document.getElementById('saveEmployeeChanges').addEventListener('click', updateEmployee);
            document.getElementById('confirmDeleteEmployee').addEventListener('click', deleteEmployee);
            
            function updatePerPage() {
                perPage = parseInt(this.value);
                currentPage = 1;
                loadEmployees();
            }
            
            function updateSearchQuery() {
                searchQuery = this.value.trim();
                currentPage = 1;
                loadEmployees();
            }
            
            function updateZoneFilter() {
                zoneFilter = this.value;
                currentPage = 1;
                loadEmployees();
            }
            
            function handlePhotoPreview(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        photoPreview.src = event.target.result;
                        photoPreview.style.display = 'block';
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            }
            
            function handleEditPhotoPreview(e) {
                if (e.target.files && e.target.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        editPhotoPreview.src = event.target.result;
                        editPhotoPreview.style.display = 'block';
                    }
                    reader.readAsDataURL(e.target.files[0]);
                }
            }
            
            function loadReferenceData() {
                // Load zones
                fetch('api/zone_api.php?all=true')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            zones = data.data.zones || data.data;
                            populateZoneDropdowns();
                        }
                    });
                
                // Load DCMs
                fetch('api/dcm_api.php?all=true')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            dcms = data.data.dcms || data.data;
                            populateDcmDropdowns();
                        }
                    });
                
                // Load clusters
                fetch('api/cluster_api.php?all=true')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            clusters = data.data.clusters || data.data;
                            populateClusterDropdowns();
                        }
                    });
                
                // Load shifts
                fetch('api/shift_api.php?all=true')
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            shifts = data.data.shifts || data.data;
                            populateShiftDropdowns();
                        }
                    });
            }
            
            function populateZoneDropdowns() {
                empZoneSelect.innerHTML = '<option value="">Select Zone</option>';
                editEmpZoneSelect.innerHTML = '<option value="">Select Zone</option>';
                zoneFilterSelect.innerHTML = '<option value="">All Zones</option>';
                
                zones.forEach(zone => {
                    const option = document.createElement('option');
                    option.value = zone.zone_name;
                    option.textContent = zone.zone_name;
                    
                    empZoneSelect.appendChild(option.cloneNode(true));
                    editEmpZoneSelect.appendChild(option.cloneNode(true));
                    zoneFilterSelect.appendChild(option.cloneNode(true));
                });
            }
            
            function populateDcmDropdowns() {
                empDcmSelect.innerHTML = '<option value="">Select DCM</option>';
                editEmpDcmSelect.innerHTML = '<option value="">Select DCM</option>';
                
                dcms.forEach(dcm => {
                    const option = document.createElement('option');
                    option.value = dcm.dcm_name;
                    option.textContent = dcm.dcm_name;
                    
                    empDcmSelect.appendChild(option.cloneNode(true));
                    editEmpDcmSelect.appendChild(option.cloneNode(true));
                });
            }
            
            function populateClusterDropdowns() {
                empClusterSelect.innerHTML = '<option value="">Select Cluster</option>';
                editEmpClusterSelect.innerHTML = '<option value="">Select Cluster</option>';
                
                clusters.forEach(cluster => {
                    const option = document.createElement('option');
                    option.value = cluster.cluster_name;
                    option.textContent = cluster.cluster_name;
                    
                    empClusterSelect.appendChild(option.cloneNode(true));
                    editEmpClusterSelect.appendChild(option.cloneNode(true));
                });
            }
            
            function populateShiftDropdowns() {
                empShiftSelect.innerHTML = '<option value="">Select Shift</option>';
                editEmpShiftSelect.innerHTML = '<option value="">Select Shift</option>';
                
                shifts.forEach(shift => {
                    const option = document.createElement('option');
                    option.value = shift.shift_name;
                    option.textContent = shift.shift_name;
                    
                    empShiftSelect.appendChild(option.cloneNode(true));
                    editEmpShiftSelect.appendChild(option.cloneNode(true));
                });
            }
            
            function loadEmployees() {
                showLoading(true, '#employeesTableBody');
                
                let url = `api/employee_api.php?page=${currentPage}&limit=${perPage}`;
                if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;
                if (zoneFilter) url += `&zone_name=${encodeURIComponent(zoneFilter)}`;
                
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            renderEmployees(data.data.employees || data.data);
                            renderPagination(data.data.meta || {
                                total_items: data.data.length,
                                total_pages: 1,
                                current_page: 1,
                                per_page: perPage
                            });
                        }
                    })
                    .catch(error => {
                        showResponseMessage('error', 'Failed to load employees');
                        employeesTableBody.innerHTML = `
                            <tr>
                                <td colspan="11" class="text-center py-4 text-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Failed to load employees
                                </td>
                            </tr>
                        `;
                        pagination.innerHTML = '';
                    })
                    .finally(() => {
                        showLoading(false, '#employeesTableBody');
                    });
            }
            
            function renderEmployees(employees) {
                employeesTableBody.innerHTML = '';
                
                if (!employees || employees.length === 0) {
                    employeesTableBody.innerHTML = `
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="fas fa-info-circle me-2"></i>No employees found
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                employees.forEach(employee => {
                    const row = document.createElement('tr');
                    
                    const photoCell = document.createElement('td');
                    if (employee.photo) {
                        photoCell.innerHTML = `<img src="api/get_image.php?path=${encodeURIComponent(employee.photo)}" alt="Employee Photo" class="employee-photo">`;
                    } else {
                        photoCell.innerHTML = '<i class="fas fa-user-circle fa-2x text-muted"></i>';
                    }
                    
                    const adminBadge = employee.is_admin ? 
                        '<span class="badge bg-success">Admin</span>' : 
                        '<span class="badge bg-secondary">User</span>';
                    
                    row.innerHTML = `<td>${employee.employee_id}</td>`;
                    row.appendChild(photoCell);
                    row.innerHTML += `
                        <td>${employee.emp_name}</td>
                        <td><a href="mailto:${employee.email}">${employee.email}</a></td>
                        <td>${employee.mobile_number}</td>
                        <td>${employee.designation}</td>
                        <td>${employee.zone_name || 'N/A'}</td>
                        <td>${employee.dcm_name || 'N/A'}</td>
                        <td>${employee.cluster_name || 'N/A'}</td>
                        <td>${adminBadge}</td>
                        <td class="action-btns">
                            <button class="btn btn-sm btn-outline-primary edit-employee" data-id="${employee.employee_id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-employee" data-id="${employee.employee_id}" data-name="${employee.emp_name}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    `;
                    employeesTableBody.appendChild(row);
                });
                
                // Add event listeners to edit and delete buttons
                document.querySelectorAll('.edit-employee').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const employeeId = this.getAttribute('data-id');
                        showEditEmployeeModal(employeeId);
                    });
                });
                
                document.querySelectorAll('.delete-employee').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const employeeId = this.getAttribute('data-id');
                        const employeeName = this.getAttribute('data-name');
                        showDeleteConfirmationModal(employeeId, employeeName);
                    });
                });
            }
            
            function renderPagination(meta) {
                pagination.innerHTML = '';
                totalPages = meta.total_pages || 1;
                
                if (totalPages <= 1) return;
                
                // Previous button
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
                prevLi.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        loadEmployees();
                    }
                });
                pagination.appendChild(prevLi);
                
                // Page numbers
                const startPage = Math.max(1, currentPage - 2);
                const endPage = Math.min(totalPages, currentPage + 2);
                
                if (startPage > 1) {
                    const firstLi = document.createElement('li');
                    firstLi.className = 'page-item';
                    firstLi.innerHTML = `<a class="page-link" href="#">1</a>`;
                    firstLi.addEventListener('click', function(e) {
                        e.preventDefault();
                        currentPage = 1;
                        loadEmployees();
                    });
                    pagination.appendChild(firstLi);
                    
                    if (startPage > 2) {
                        const ellipsisLi = document.createElement('li');
                        ellipsisLi.className = 'page-item disabled';
                        ellipsisLi.innerHTML = `<span class="page-link">...</span>`;
                        pagination.appendChild(ellipsisLi);
                    }
                }
                
                for (let i = startPage; i <= endPage; i++) {
                    const pageLi = document.createElement('li');
                    pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
                    pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
                    pageLi.addEventListener('click', function(e) {
                        e.preventDefault();
                        currentPage = i;
                        loadEmployees();
                    });
                    pagination.appendChild(pageLi);
                }
                
                if (endPage < totalPages) {
                    if (endPage < totalPages - 1) {
                        const ellipsisLi = document.createElement('li');
                        ellipsisLi.className = 'page-item disabled';
                        ellipsisLi.innerHTML = `<span class="page-link">...</span>`;
                        pagination.appendChild(ellipsisLi);
                    }
                    
                    const lastLi = document.createElement('li');
                    lastLi.className = 'page-item';
                    lastLi.innerHTML = `<a class="page-link" href="#">${totalPages}</a>`;
                    lastLi.addEventListener('click', function(e) {
                        e.preventDefault();
                        currentPage = totalPages;
                        loadEmployees();
                    });
                    pagination.appendChild(lastLi);
                }
                
                // Next button
                const nextLi = document.createElement('li');
                nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
                nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
                nextLi.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (currentPage < totalPages) {
                        currentPage++;
                        loadEmployees();
                    }
                });
                pagination.appendChild(nextLi);
            }
            function createEmployee(e) {
    e.preventDefault();
    
    const form = document.getElementById('createEmployeeForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const empName = formData.get('emp_name').trim();
    if (!empName || empName.length < 2) {
        showResponseMessage('error', 'Please enter a valid full name (minimum 2 characters)');
        return;
    }

    const spinner = document.querySelector('#createEmployeeForm .loading-spinner');
    spinner.classList.remove('d-none');
    
    fetch('api/employee_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            // Try to get error message from response
            return response.json().then(errData => {
                throw new Error(errData.message || `HTTP error! status: ${response.status}`);
            }).catch(() => {
                throw new Error(`HTTP error! status: ${response.status}`);
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            showResponseMessage('success', data.message);
            form.reset();
            photoPreview.style.display = 'none';
            loadEmployees(); // Refresh the employee list
        } else {
            throw new Error(data.message || 'Failed to create employee');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showResponseMessage('error', error.message || 'Failed to create employee. Please try again.');
    })
    .finally(() => {
        spinner.classList.add('d-none');
    });
}
           
            
            function showEditEmployeeModal(employeeId) {
                fetch(`api/employee_api.php?employee_id=${employeeId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const employee = data.data;
                            
                            document.getElementById('editEmployeeId').value = employee.employee_id;
                            document.getElementById('editEmpName').value = employee.emp_name;
                            document.getElementById('editEmpEmail').value = employee.email;
                            document.getElementById('editEmpMobile').value = employee.mobile_number;
                            document.getElementById('editEmpAadhar').value = employee.aadhar_number || '';
                            document.getElementById('editEmpPan').value = employee.pan_number || '';
                            document.getElementById('editEmpAddress').value = employee.address || '';
                            document.getElementById('editEmpPincode').value = employee.emp_pincode;
                            document.getElementById('editEmpDesignation').value = employee.designation;
                            document.getElementById('editEmpIsAdmin').checked = employee.is_admin == 1;
                            
                            document.getElementById('editEmpZone').value = employee.zone_name || '';
                            document.getElementById('editEmpDcm').value = employee.dcm_name || '';
                            document.getElementById('editEmpCluster').value = employee.cluster_name || '';
                            document.getElementById('editEmpShift').value = employee.shift_name || '';
                            
                            if (employee.photo) {
                                document.getElementById('currentPhoto').value = employee.photo;
                                editPhotoPreview.src = `api/get_image.php?path=${encodeURIComponent(employee.photo)}`;
                                editPhotoPreview.style.display = 'block';
                            } else {
                                editPhotoPreview.style.display = 'none';
                            }
                            
                            const modal = new bootstrap.Modal(document.getElementById('editEmployeeModal'));
                            modal.show();
                        }
                    });
            }
            
            function updateEmployee() {
                const form = document.getElementById('editEmployeeForm');
                const formData = new FormData(form);
                const empName = formData.get('emp_name').trim();
                
                if (!empName || empName.length < 2) {
                    showResponseMessage('error', 'Please enter a valid full name (minimum 2 characters)');
                    return;
                }
                
                const spinner = document.querySelector('#saveEmployeeChanges .loading-spinner');
                spinner.classList.remove('d-none');
                
                fetch('api/employee_api.php', {
                    method: 'PUT',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showResponseMessage('success', 'Employee updated successfully');
                        loadEmployees();
                        bootstrap.Modal.getInstance(document.getElementById('editEmployeeModal')).hide();
                    } else {
                        throw new Error(data.message || 'Failed to update employee');
                    }
                })
                .catch(error => {
                    showResponseMessage('error', error.message);
                })
                .finally(() => {
                    spinner.classList.add('d-none');
                });
            }
            
            function showDeleteConfirmationModal(employeeId, employeeName) {
                document.getElementById('deleteEmployeeIdText').textContent = employeeId;
                document.getElementById('deleteEmployeeNameText').textContent = employeeName;
                
                const modal = new bootstrap.Modal(document.getElementById('deleteEmployeeModal'));
                modal.show();
            }
            
            function deleteEmployee() {
                const employeeId = document.getElementById('deleteEmployeeIdText').textContent;
                
                const spinner = document.querySelector('#confirmDeleteEmployee .loading-spinner');
                spinner.classList.remove('d-none');
                
                fetch('api/employee_api.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        employee_id: employeeId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showResponseMessage('success', data.message);
                        loadEmployees();
                        bootstrap.Modal.getInstance(document.getElementById('deleteEmployeeModal')).hide();
                    } else {
                        throw new Error(data.message || 'Failed to delete employee');
                    }
                })
                .catch(error => {
                    showResponseMessage('error', error.message);
                })
                .finally(() => {
                    spinner.classList.add('d-none');
                });
            }
            
            function showResponseMessage(type, message) {
                responseMessage.style.display = 'block';
                responseMessage.className = `alert alert-${type}`;
                responseMessage.innerHTML = `
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                setTimeout(() => {
                    responseMessage.style.display = 'none';
                }, 5000);
            }
            
            function showLoading(show, elementSelector) {
                const element = document.querySelector(elementSelector);
                if (show) {
                    element.innerHTML = `
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading employees...</p>
                            </td>
                        </tr>
                    `;
                }
            }
        });
    </script>
</body>
</html>