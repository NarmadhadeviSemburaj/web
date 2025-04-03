<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zone Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-blue: #1e88e5;
            --light-blue: #e3f2fd;
            --dark-blue: #1565c0;
            --ribbon-height: 15px;
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
            padding: 10px;
            margin-top: 0;
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
        
        .status-success {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-error {
            background-color: #ffebee;
            color: #c62828;
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
        
        /* New styles for improved layout */
        .cards-container {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .cards-container > div {
            flex: 1;
        }
        
        @media (max-width: 992px) {
            .cards-container {
                flex-direction: column;
            }
        }
        
        /* Input validation styles */
        .is-invalid {
            border-color: #dc3545 !important;
        }
        
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875em;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
   

    <div class="main-container container-fluid">
        <!-- Response Message -->
        <div id="responseMessage" class="alert mb-4"></div>

        <div class="cards-container">
            <!-- Create Zone Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-plus-circle me-2"></i>Create New Zone
                </div>
                <div class="card-body">
                    <form id="createZoneForm">
                        <div class="mb-3">
                            <label for="zoneName" class="form-label">Zone Name</label>
                            <input type="text" class="form-control" id="zoneName" required>
                            <div class="invalid-feedback">Please enter only alphabets (A-Z, a-z)</div>
                        </div>
                        <div class="mb-3">
                            <label for="zoneLocation" class="form-label">Location</label>
                            <input type="text" class="form-control" id="zoneLocation" required>
                            <div class="invalid-feedback">Please enter only alphabets (A-Z, a-z)</div>
                        </div>
                        <div class="mb-3">
                            <label for="zonePincode" class="form-label">Pincode</label>
                            <input type="text" class="form-control" id="zonePincode" maxlength="6" required>
                            <div class="invalid-feedback">Please enter exactly 6 digits</div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <span class="loading-spinner spinner-border spinner-border-sm me-2 d-none"></span>
                            Create Zone
                        </button>
                    </form>
                </div>
            </div>

            <!-- Zones List Card -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-list me-2"></i>Zone List
                    </div>
                    <div class="d-flex">
                        <input type="text" id="searchInput" class="form-control form-control-sm me-2" placeholder="Search zones..." style="width: 200px;">
                        <select id="perPageSelect" class="form-select form-select-sm" style="width: 80px;">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Zone Name</th>
                                    <th>Location</th>
                                    <th>Pincode</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="zonesTableBody">
                                <!-- Zones will be loaded here -->
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

        <!-- Edit Zone Modal -->
        <div class="modal fade" id="editZoneModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Zone</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editZoneForm">
                            <input type="hidden" id="editZoneId">
                            <div class="mb-3">
                                <label for="editZoneName" class="form-label">Zone Name</label>
                                <input type="text" class="form-control" id="editZoneName" required>
                                <div class="invalid-feedback">Please enter only alphabets (A-Z, a-z)</div>
                            </div>
                            <div class="mb-3">
                                <label for="editZoneLocation" class="form-label">Location</label>
                                <input type="text" class="form-control" id="editZoneLocation" required>
                                <div class="invalid-feedback">Please enter only alphabets (A-Z, a-z)</div>
                            </div>
                            <div class="mb-3">
                                <label for="editZonePincode" class="form-label">Pincode</label>
                                <input type="text" class="form-control" id="editZonePincode" maxlength="6" required>
                                <div class="invalid-feedback">Please enter exactly 6 digits</div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveZoneChanges">
                            <span class="loading-spinner spinner-border spinner-border-sm me-2 d-none"></span>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteZoneModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this zone? This action cannot be undone.</p>
                        <p><strong>Zone ID:</strong> <span id="deleteZoneIdText"></span></p>
                        <p><strong>Zone Name:</strong> <span id="deleteZoneNameText"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteZone">
                            <span class="loading-spinner spinner-border spinner-border-sm me-2 d-none"></span>
                            Delete Zone
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
            let allZones = []; // Store all zones for client-side search
            
            // DOM elements
            const zonesTableBody = document.getElementById('zonesTableBody');
            const pagination = document.getElementById('pagination');
            const perPageSelect = document.getElementById('perPageSelect');
            const searchInput = document.getElementById('searchInput');
            const responseMessage = document.getElementById('responseMessage');
            
            // Initialize the page
            loadZones();
            
            // Event listeners
            document.getElementById('createZoneForm').addEventListener('submit', createZone);
            perPageSelect.addEventListener('change', function() {
                perPage = parseInt(this.value);
                currentPage = 1;
                loadZones();
            });
            
            // Input validation for zone name and location (alphabets only)
            document.getElementById('zoneName').addEventListener('input', function() {
                validateAlphabets(this);
            });
            
            document.getElementById('zoneLocation').addEventListener('input', function() {
                validateAlphabets(this);
            });
            
            // Input validation for pincode (6 digits only)
            document.getElementById('zonePincode').addEventListener('input', function() {
                validatePincode(this);
            });
            
            // Improved search functionality (client-side)
            searchInput.addEventListener('input', function() {
                searchQuery = this.value.trim().toLowerCase();
                currentPage = 1;
                
                if (searchQuery === '') {
                    renderZones(allZones);
                    renderPagination({
                        total_pages: Math.ceil(allZones.length / perPage),
                        total_items: allZones.length
                    });
                } else {
                    const filteredZones = allZones.filter(zone => 
                        zone.zone_name.toLowerCase().includes(searchQuery) || 
                        zone.zone_location.toLowerCase().includes(searchQuery) || 
                        zone.zone_pincode.toLowerCase().includes(searchQuery)
                    );
                    
                    renderZones(filteredZones);
                    renderPagination({
                        total_pages: Math.ceil(filteredZones.length / perPage),
                        total_items: filteredZones.length
                    });
                }
            });
            
            document.getElementById('saveZoneChanges').addEventListener('click', updateZone);
            document.getElementById('confirmDeleteZone').addEventListener('click', deleteZone);
            
            // Function to validate alphabets only
            function validateAlphabets(inputElement) {
                const value = inputElement.value.trim();
                const isValid = /^[A-Za-z\s]+$/.test(value);
                
                if (value && !isValid) {
                    inputElement.classList.add('is-invalid');
                    return false;
                } else {
                    inputElement.classList.remove('is-invalid');
                    return true;
                }
            }
            
            // Function to validate pincode (6 digits)
            function validatePincode(inputElement) {
                const value = inputElement.value.trim();
                const isValid = /^\d{6}$/.test(value);
                
                if (value && !isValid) {
                    inputElement.classList.add('is-invalid');
                    return false;
                } else {
                    inputElement.classList.remove('is-invalid');
                    return true;
                }
            }
            
            // Function to validate all form fields
            function validateForm(formType = 'create') {
                let isValid = true;
                
                if (formType === 'create') {
                    const zoneNameValid = validateAlphabets(document.getElementById('zoneName'));
                    const zoneLocationValid = validateAlphabets(document.getElementById('zoneLocation'));
                    const zonePincodeValid = validatePincode(document.getElementById('zonePincode'));
                    
                    isValid = zoneNameValid && zoneLocationValid && zonePincodeValid;
                } else {
                    const editZoneNameValid = validateAlphabets(document.getElementById('editZoneName'));
                    const editZoneLocationValid = validateAlphabets(document.getElementById('editZoneLocation'));
                    const editZonePincodeValid = validatePincode(document.getElementById('editZonePincode'));
                    
                    isValid = editZoneNameValid && editZoneLocationValid && editZonePincodeValid;
                }
                
                return isValid;
            }
            
            // Function to load zones
            function loadZones() {
                showLoading(true, '#zonesTableBody');
                
                fetch(`api/zone_api.php?page=${currentPage}&limit=${perPage}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            allZones = data.data.all_zones || data.data.zones; // Store all zones
                            renderZones(data.data.zones);
                            renderPagination(data.data.meta);
                        } else {
                            showResponseMessage('error', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showResponseMessage('error', 'Failed to load zones. Please try again.');
                    })
                    .finally(() => {
                        showLoading(false, '#zonesTableBody');
                    });
            }
            
            // Function to render zones in the table
            function renderZones(zones) {
                zonesTableBody.innerHTML = '';
                
                if (zones.length === 0) {
                    zonesTableBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-info-circle me-2"></i>No zones found
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                zones.forEach(zone => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${zone.zone_name}</td>
                        <td>${zone.zone_location}</td>
                        <td>${zone.zone_pincode}</td>
                        <td class="action-btns">
                            <button class="btn btn-sm btn-outline-primary edit-zone" data-id="${zone.zone_id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-zone" data-id="${zone.zone_id}" data-name="${zone.zone_name}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    `;
                    zonesTableBody.appendChild(row);
                });
                
                // Add event listeners to edit and delete buttons
                document.querySelectorAll('.edit-zone').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const zoneId = this.getAttribute('data-id');
                        showEditZoneModal(zoneId);
                    });
                });
                
                document.querySelectorAll('.delete-zone').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const zoneId = this.getAttribute('data-id');
                        const zoneName = this.getAttribute('data-name');
                        showDeleteConfirmationModal(zoneId, zoneName);
                    });
                });
            }
            
            // Function to render pagination
            function renderPagination(meta) {
                pagination.innerHTML = '';
                totalPages = meta.total_pages;
                
                if (totalPages <= 1) return;
                
                // Previous button
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
                prevLi.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        loadZones();
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
                        loadZones();
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
                        loadZones();
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
                        loadZones();
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
                        loadZones();
                    }
                });
                pagination.appendChild(nextLi);
            }
            
            // Function to create a new zone
            function createZone(e) {
                e.preventDefault();
                
                // Validate form fields
                if (!validateForm('create')) {
                    showResponseMessage('error', 'Please correct the form errors before submitting');
                    return;
                }
                
                const zoneName = document.getElementById('zoneName').value.trim();
                const zoneLocation = document.getElementById('zoneLocation').value.trim();
                const zonePincode = document.getElementById('zonePincode').value.trim();
                
                if (!zoneName || !zoneLocation || !zonePincode) {
                    showResponseMessage('error', 'All fields are required');
                    return;
                }
                
                const spinner = e.target.querySelector('.loading-spinner');
                spinner.classList.remove('d-none');
                
                fetch('api/zone_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        zone_name: zoneName,
                        zone_location: zoneLocation,
                        zone_pincode: zonePincode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showResponseMessage('success', data.message);
                        document.getElementById('createZoneForm').reset();
                        loadZones();
                    } else {
                        showResponseMessage('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showResponseMessage('error', 'Failed to create zone. Please try again.');
                })
                .finally(() => {
                    spinner.classList.add('d-none');
                });
            }
            
            // Function to show edit zone modal
            function showEditZoneModal(zoneId) {
                fetch(`api/zone_api.php?zone_id=${zoneId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            const zone = data.data;
                            document.getElementById('editZoneId').value = zone.zone_id;
                            document.getElementById('editZoneName').value = zone.zone_name;
                            document.getElementById('editZoneLocation').value = zone.zone_location;
                            document.getElementById('editZonePincode').value = zone.zone_pincode;
                            
                            // Add validation event listeners for edit modal
                            document.getElementById('editZoneName').addEventListener('input', function() {
                                validateAlphabets(this);
                            });
                            
                            document.getElementById('editZoneLocation').addEventListener('input', function() {
                                validateAlphabets(this);
                            });
                            
                            document.getElementById('editZonePincode').addEventListener('input', function() {
                                validatePincode(this);
                            });
                            
                            const modal = new bootstrap.Modal(document.getElementById('editZoneModal'));
                            modal.show();
                        } else {
                            showResponseMessage('error', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showResponseMessage('error', 'Failed to load zone details. Please try again.');
                    });
            }
            
            // Function to update zone
            function updateZone() {
                // Validate form fields
                if (!validateForm('edit')) {
                    showResponseMessage('error', 'Please correct the form errors before submitting');
                    return;
                }
                
                const zoneId = document.getElementById('editZoneId').value;
                const zoneName = document.getElementById('editZoneName').value.trim();
                const zoneLocation = document.getElementById('editZoneLocation').value.trim();
                const zonePincode = document.getElementById('editZonePincode').value.trim();
                
                if (!zoneName || !zoneLocation || !zonePincode) {
                    showResponseMessage('error', 'All fields are required');
                    return;
                }

                const payload = {
                    zone_id: zoneId,
                    zone_name: zoneName,
                    zone_location: zoneLocation,
                    zone_pincode: zonePincode
                };

                const spinner = document.querySelector('#saveZoneChanges .loading-spinner');
                spinner.classList.remove('d-none');
                
                fetch('api/zone_api.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(payload)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showResponseMessage('success', 'Zone updated successfully');
                        loadZones();
                        bootstrap.Modal.getInstance(document.getElementById('editZoneModal')).hide();
                    } else {
                        showResponseMessage('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showResponseMessage('error', 'Failed to update zone. Please try again.');
                })
                .finally(() => {
                    spinner.classList.add('d-none');
                });
            }
            
            // Function to show delete confirmation modal
            function showDeleteConfirmationModal(zoneId, zoneName) {
                document.getElementById('deleteZoneIdText').textContent = zoneId;
                document.getElementById('deleteZoneNameText').textContent = zoneName;
                
                const modal = new bootstrap.Modal(document.getElementById('deleteZoneModal'));
                modal.show();
            }
            
            // Function to delete zone
            function deleteZone() {
                const zoneId = document.getElementById('deleteZoneIdText').textContent;
                
                const spinner = document.querySelector('#confirmDeleteZone .loading-spinner');
                spinner.classList.remove('d-none');
                
                fetch('api/zone_api.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        zone_id: zoneId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showResponseMessage('success', data.message);
                        loadZones();
                        const modal = bootstrap.Modal.getInstance(document.getElementById('deleteZoneModal'));
                        modal.hide();
                    } else {
                        showResponseMessage('error', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showResponseMessage('error', 'Failed to delete zone. Please try again.');
                })
                .finally(() => {
                    spinner.classList.add('d-none');
                });
            }
            
            // Function to show response message
            function showResponseMessage(type, message) {
                responseMessage.style.display = 'block';
                responseMessage.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
                responseMessage.innerHTML = `
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                
                setTimeout(() => {
                    responseMessage.style.display = 'none';
                }, 5000);
            }
            
            // Function to show/hide loading state
            function showLoading(show, elementSelector) {
                const element = document.querySelector(elementSelector);
                if (show) {
                    element.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading zones...</p>
                            </td>
                        </tr>
                    `;
                }
            }
        });
    </script>
</body>
</html>