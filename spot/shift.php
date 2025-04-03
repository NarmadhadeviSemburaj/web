<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .main-container {
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(33, 40, 50, 0.15);
        }
        .card-header {
            font-weight: 600;
            background-color: #f8f9fa;
        }
        .time-input {
            max-width: 150px;
        }
        .action-btns .btn {
            margin-right: 5px;
        }
        .loading-spinner {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="main-container container-fluid">
        <!-- Response Message -->
        <div id="responseMessage" class="alert mb-4" style="display: none;"></div>

        <div class="row">
            <!-- Create Shift Card -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-plus-circle me-2"></i>Create New Shift
                    </div>
                    <div class="card-body">
                        <form id="createShiftForm">
                            <div class="mb-3">
                                <label for="shiftName" class="form-label">Shift Name</label>
                                <input type="text" class="form-control" id="shiftName" required>
                                <div class="invalid-feedback">
                                    Shift name must contain alphabets and be unique
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="shiftStartTime" class="form-label">Start Time</label>
                                <input type="time" class="form-control time-input" id="shiftStartTime" step="1" required>
                                <div class="invalid-feedback">
                                    Please enter a valid start time (HH:MM:SS)
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="shiftEndTime" class="form-label">End Time</label>
                                <input type="time" class="form-control time-input" id="shiftEndTime" step="1" required>
                                <div class="invalid-feedback">
                                    Please enter a valid end time (HH:MM:SS)
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <span class="loading-spinner spinner-border spinner-border-sm me-2 d-none"></span>
                                Create Shift
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Shifts List Card -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-list me-2"></i>Shift List
                        </div>
                        <div class="d-flex">
                            <input type="text" id="searchInput" class="form-control form-control-sm me-2" placeholder="Search shifts..." style="width: 200px;">
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
                                        <th>Shift Name</th>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="shiftsTableBody">
                                    <!-- Shifts will be loaded here -->
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

        <!-- Edit Shift Modal -->
        <div class="modal fade" id="editShiftModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Shift</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editShiftForm">
                            <input type="hidden" id="editShiftId">
                            <div class="mb-3">
                                <label for="editShiftName" class="form-label">Shift Name</label>
                                <input type="text" class="form-control" id="editShiftName" required>
                                <div class="invalid-feedback">
                                    Shift name must contain alphabets and be unique
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="editShiftStartTime" class="form-label">Start Time</label>
                                <input type="time" class="form-control time-input" id="editShiftStartTime" step="1" required>
                                <div class="invalid-feedback">
                                    Please enter a valid start time (HH:MM:SS)
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="editShiftEndTime" class="form-label">End Time</label>
                                <input type="time" class="form-control time-input" id="editShiftEndTime" step="1" required>
                                <div class="invalid-feedback">
                                    Please enter a valid end time (HH:MM:SS)
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="saveShiftChanges">
                            <span class="loading-spinner spinner-border spinner-border-sm me-2 d-none"></span>
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteShiftModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">Confirm Deletion</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this shift? This action cannot be undone.</p>
                        <p><strong>Shift ID:</strong> <span id="deleteShiftIdText"></span></p>
                        <p><strong>Shift Name:</strong> <span id="deleteShiftNameText"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteShift">
                            <span class="loading-spinner spinner-border spinner-border-sm me-2 d-none"></span>
                            Delete Shift
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
            let allShifts = []; // Store all shifts for duplicate checking
            
            // DOM elements
            const shiftsTableBody = document.getElementById('shiftsTableBody');
            const pagination = document.getElementById('pagination');
            const perPageSelect = document.getElementById('perPageSelect');
            const searchInput = document.getElementById('searchInput');
            const responseMessage = document.getElementById('responseMessage');
            
            // Initialize the page
            loadShifts();
            
            // Event listeners
            document.getElementById('createShiftForm').addEventListener('submit', createShift);
            perPageSelect.addEventListener('change', function() {
                perPage = parseInt(this.value);
                currentPage = 1;
                loadShifts();
            });
            
            searchInput.addEventListener('input', function() {
                searchQuery = this.value.trim();
                currentPage = 1;
                loadShifts();
            });
            
            document.getElementById('saveShiftChanges').addEventListener('click', updateShift);
            document.getElementById('confirmDeleteShift').addEventListener('click', confirmDeleteShift);
            
            // Add input validation for shift name fields
            document.getElementById('shiftName').addEventListener('input', validateShiftName);
            document.getElementById('editShiftName').addEventListener('input', validateEditShiftName);
            
            // Function to validate shift name
            function validateShiftName() {
                const input = document.getElementById('shiftName');
                const value = input.value.trim();
                
                if (!value) {
                    input.classList.remove('is-invalid');
                    return false;
                }
                
                // Regex: must contain at least one alphabet, can contain numbers but not only numbers
                const isValid = /^(?=.*[a-zA-Z])[a-zA-Z0-9\s]+$/.test(value);
                
                if (!isValid) {
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
                
                return isValid;
            }
            
            // Function to validate edit shift name
            function validateEditShiftName() {
                const input = document.getElementById('editShiftName');
                const value = input.value.trim();
                
                if (!value) {
                    input.classList.remove('is-invalid');
                    return false;
                }
                
                // Regex: must contain at least one alphabet, can contain numbers but not only numbers
                const isValid = /^(?=.*[a-zA-Z])[a-zA-Z0-9\s]+$/.test(value);
                
                if (!isValid) {
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
                
                return isValid;
            }
            
            // Function to check for duplicate shift names
            function isDuplicateShiftName(name, currentId = null) {
                return allShifts.some(shift => 
                    shift.shift_name.toLowerCase() === name.toLowerCase() && 
                    shift.shift_id !== currentId
                );
            }
            
            // Function to load shifts
            function loadShifts() {
                showLoading(true, '#shiftsTableBody');
                
                fetch('api/shift_api.php')
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status && data.status === 'error') {
                            throw new Error(data.message);
                        }
                        
                        allShifts = Array.isArray(data) ? data : (data.data || []);
                        let filteredShifts = [...allShifts];
                        
                        if (searchQuery) {
                            filteredShifts = filteredShifts.filter(shift => 
                                shift.shift_name.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                shift.shift_start_time.toLowerCase().includes(searchQuery.toLowerCase()) ||
                                shift.shift_end_time.toLowerCase().includes(searchQuery.toLowerCase())
                            );
                        }
                        
                        renderShifts(filteredShifts);
                        renderPagination({
                            total_pages: Math.ceil(filteredShifts.length / perPage),
                            total_items: filteredShifts.length
                        });
                    })
                    .catch(error => {
                        console.error('Error loading shifts:', error);
                        if (error.message !== 'Failed to fetch') {
                            showResponseMessage('error', error.message || 'Failed to load shifts');
                        }
                    })
                    .finally(() => {
                        showLoading(false, '#shiftsTableBody');
                    });
            }
            
            // Function to render shifts in the table
            function renderShifts(shifts) {
                shiftsTableBody.innerHTML = '';
                
                if (shifts.length === 0) {
                    shiftsTableBody.innerHTML = `
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-info-circle me-2"></i>No shifts found
                            </td>
                        </tr>
                    `;
                    return;
                }
                
                // Apply pagination
                const startIndex = (currentPage - 1) * perPage;
                const paginatedShifts = shifts.slice(startIndex, startIndex + perPage);
                
                paginatedShifts.forEach(shift => {
                    const row = document.createElement('tr');
                    
                    row.innerHTML = `
                        <td>${shift.shift_name || 'N/A'}</td>
                        <td>${shift.shift_start_time}</td>
                        <td>${shift.shift_end_time}</td>
                        <td class="action-btns">
                            <button class="btn btn-sm btn-outline-primary edit-shift" data-id="${shift.shift_id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger delete-shift" 
                                    data-id="${shift.shift_id}" 
                                    data-name="${shift.shift_name || 'N/A'}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    `;
                    shiftsTableBody.appendChild(row);
                });
                
                // Add event listeners to action buttons
                document.querySelectorAll('.edit-shift').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const shiftId = this.getAttribute('data-id');
                        showEditShiftModal(shiftId);
                    });
                });
                
                document.querySelectorAll('.delete-shift').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const shiftId = this.getAttribute('data-id');
                        const shiftName = this.getAttribute('data-name');
                        showDeleteConfirmationModal(shiftId, shiftName);
                    });
                });
            }
            
            // Function to create a new shift
            function createShift(e) {
                e.preventDefault();
                
                const shiftName = document.getElementById('shiftName').value.trim();
                const shiftStartTime = document.getElementById('shiftStartTime').value;
                const shiftEndTime = document.getElementById('shiftEndTime').value;
                
                // Clear previous messages
                responseMessage.style.display = 'none';
                
                if (!shiftName || !shiftStartTime || !shiftEndTime) {
                    showResponseMessage('error', 'All fields are required');
                    return;
                }
                
                if (!validateShiftName()) {
                    showResponseMessage('error', 'Shift name must contain alphabets and can include numbers (but not only numbers)');
                    return;
                }
                
                // Check for duplicate shift name
                if (isDuplicateShiftName(shiftName)) {
                    showResponseMessage('error', 'Shift name already exists. Please choose a different name.');
                    document.getElementById('shiftName').classList.add('is-invalid');
                    return;
                }
                
                const spinner = e.target.querySelector('.loading-spinner');
                spinner.classList.remove('d-none');
                
                fetch('api/shift_api.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        shift_name: shiftName,
                        shift_start_time: shiftStartTime,
                        shift_end_time: shiftEndTime
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Failed to create shift');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        showResponseMessage('success', data.message);
                        document.getElementById('createShiftForm').reset();
                        loadShifts();
                    } else {
                        throw new Error(data.message || 'Failed to create shift');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showResponseMessage('error', error.message || 'Failed to create shift. Please try again.');
                })
                .finally(() => {
                    spinner.classList.add('d-none');
                });
            }

            // Function to show edit shift modal
            function showEditShiftModal(shiftId) {
                fetch(`api/shift_api.php?shift_id=${shiftId}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success' || data.shift_id) {
                            const shift = data.data || data;
                            document.getElementById('editShiftId').value = shift.shift_id;
                            document.getElementById('editShiftName').value = shift.shift_name;
                            
                            // Format time for time input fields
                            const startTime = shift.shift_start_time.includes(':') ? 
                                shift.shift_start_time.substring(0, shift.shift_start_time.lastIndexOf(':')) : 
                                shift.shift_start_time;
                            const endTime = shift.shift_end_time.includes(':') ? 
                                shift.shift_end_time.substring(0, shift.shift_end_time.lastIndexOf(':')) : 
                                shift.shift_end_time;
                                
                            document.getElementById('editShiftStartTime').value = startTime;
                            document.getElementById('editShiftEndTime').value = endTime;
                            
                            const modal = new bootstrap.Modal(document.getElementById('editShiftModal'));
                            modal.show();
                        } else {
                            throw new Error(data.message || 'Failed to load shift details');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showResponseMessage('error', error.message || 'Failed to load shift details. Please try again.');
                    });
            }
            
            // Function to update shift
            function updateShift() {
                const shiftId = document.getElementById('editShiftId').value;
                const shiftName = document.getElementById('editShiftName').value.trim();
                const shiftStartTime = document.getElementById('editShiftStartTime').value;
                const shiftEndTime = document.getElementById('editShiftEndTime').value;
                
                if (!shiftName || !shiftStartTime || !shiftEndTime) {
                    showResponseMessage('error', 'All fields are required');
                    return;
                }
                
                if (!validateEditShiftName()) {
                    showResponseMessage('error', 'Shift name must contain alphabets and can include numbers (but not only numbers)');
                    return;
                }
                
                // Check for duplicate shift name (excluding current shift)
                if (isDuplicateShiftName(shiftName, shiftId)) {
                    showResponseMessage('error', 'Shift name already exists. Please choose a different name.');
                    document.getElementById('editShiftName').classList.add('is-invalid');
                    return;
                }
                
                const spinner = document.querySelector('#saveShiftChanges .loading-spinner');
                spinner.classList.remove('d-none');
                
                fetch('api/shift_api.php', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        shift_id: shiftId,
                        shift_name: shiftName,
                        shift_start_time: shiftStartTime,
                        shift_end_time: shiftEndTime
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Failed to update shift');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        showResponseMessage('success', 'Shift updated successfully');
                        loadShifts();
                        bootstrap.Modal.getInstance(document.getElementById('editShiftModal')).hide();
                    } else {
                        throw new Error(data.message || 'Failed to update shift');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showResponseMessage('error', error.message || 'Failed to update shift. Please try again.');
                })
                .finally(() => {
                    spinner.classList.add('d-none');
                });
            }
            
            // Function to show delete confirmation modal
            function showDeleteConfirmationModal(shiftId, shiftName) {
                document.getElementById('deleteShiftIdText').textContent = shiftId;
                document.getElementById('deleteShiftNameText').textContent = shiftName;
                
                const modal = new bootstrap.Modal(document.getElementById('deleteShiftModal'));
                modal.show();
            }
            
            // Function to confirm and delete shift
            function confirmDeleteShift() {
                const shiftId = document.getElementById('deleteShiftIdText').textContent;
                
                const spinner = document.querySelector('#confirmDeleteShift .loading-spinner');
                spinner.classList.remove('d-none');
                
                fetch('api/shift_api.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        shift_id: shiftId
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.message || 'Failed to delete shift');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        showResponseMessage('success', data.message);
                        loadShifts();
                        bootstrap.Modal.getInstance(document.getElementById('deleteShiftModal')).hide();
                    } else {
                        throw new Error(data.message || 'Failed to delete shift');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showResponseMessage('error', error.message || 'Failed to delete shift. Please try again.');
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
                
                // Auto-hide after 5 seconds
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
                                <p class="mt-2">Loading shifts...</p>
                            </td>
                        </tr>
                    `;
                }
            }
            
            // Function to render pagination
            function renderPagination(meta) {
                pagination.innerHTML = '';
                totalPages = meta.total_pages || Math.ceil(meta.total_items / perPage);
                
                if (meta.total_pages <= 1 && meta.total_items <= perPage) {
                    return;
                }
                
                // Previous button
                const prevLi = document.createElement('li');
                prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
                prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
                prevLi.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (currentPage > 1) {
                        currentPage--;
                        loadShifts();
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
                        loadShifts();
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
                        loadShifts();
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
                        loadShifts();
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
                        loadShifts();
                    }
                });
                pagination.appendChild(nextLi);
            }
        });
    </script>
</body>
</html>