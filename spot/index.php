<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }
        
        body {
            display: flex;
            height: 100vh;
            overflow: hidden;
            background-color: #f8f9fa;
        }
        
        .sidebar {
            width: 80px;
            background-color: #ffffff;
            color: #333;
            height: 100vh;
            position: fixed;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .logo {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #f0f0f0;
            height: 80px;
        }
        
        .logo img {
            width: 36px;
            height: 36px;
        }
        
        .menu {
            padding: 20px 0;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .menu-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 0;
            cursor: pointer;
            position: relative;
            margin-bottom: 5px;
        }
        
        .menu-item:hover {
            background-color: #f8f8f8;
        }
        
        .menu-item.active {
            color: #1a73e8;
        }
        
        .menu-item i {
            font-size: 20px;
            color: #5f6368;
            margin-bottom: 5px;
        }
        
        .menu-item.active i {
            color: #1a73e8;
        }
        
        .menu-item .tooltip {
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background-color: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s;
            margin-left: 10px;
            z-index: 100;
        }
        
        .menu-item:hover .tooltip {
            visibility: visible;
            opacity: 1;
        }
        
        .menu-item.active .tooltip {
            background-color: #1a73e8;
        }
        
        .logout-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px 0;
            cursor: pointer;
            position: relative;
            border-top: 1px solid #f0f0f0;
        }
        
        .logout-btn:hover {
            background-color: #f8f8f8;
        }
        
        .logout-btn i {
            font-size: 20px;
            color: #5f6368;
            margin-bottom: 5px;
        }
        
        .logout-btn .tooltip {
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            background-color: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s;
            margin-left: 10px;
            z-index: 100;
        }
        
        .logout-btn:hover .tooltip {
            visibility: visible;
            opacity: 1;
        }
        
        .content {
            flex: 1;
            margin-left: 80px;
            overflow-y: auto;
            height: 100vh;
            background-color: white;
        }
        
        iframe {
            width: 100%;
            height: calc(100% - 60px); /* Reduced for more compact header */
            border: none;
        }
        
        /* Card styles for Zone Management */
        .card-container {
            display: none;
            padding: 10px 20px; /* Reduced padding for more compact header */
            background: white;
            margin-bottom: 0;
            border-bottom: 1px solid #e0e0e0;
            height: 80px; /* Reduced height for more compact header */
            box-sizing: border-box;
        }
        
        .card-container.active {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .card-header {
            display: flex;
            align-items: center;
            height: 100%;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: bold;
            color: #202124;
            margin-right: 20px;
            height: 100%;
            display: flex;
            align-items: center;
        }
        
        .card-items {
            display: flex;
            gap: 15px;
            height: 100%;
            align-items: center; /* Center items vertically */
        }
        
        .card-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 5px 15px; /* Reduced padding */
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            height: 100%;
            justify-content: center; /* Center content vertically */
        }
        
        .card-item:hover {
            background-color: #f8f8f8;
        }
        
        .card-item.active {
            color: #1a73e8;
        }
        
        .card-item i {
            font-size: 16px;
            color: #5f6368;
            margin-bottom: 2px; /* Reduced margin */
        }
        
        .card-item.active i {
            color: #1a73e8;
        }
        
        .card-item span {
            font-size: 12px;
            text-align: center;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <div>
            <div class="logo">
                <img src="logo.png" alt="Logo">
            </div>
            <div class="menu">
                <div class="menu-item active" onclick="loadPage('home.php', this, false)">
                    <i class="fas fa-home"></i>
                    <span class="tooltip">Home</span>
                </div>
                <div class="menu-item" onclick="loadZoneManagement('zone.php', this)">
                    <i class="fas fa-map-marked-alt"></i>
                    <span class="tooltip">Zone Management</span>
                </div>
                <div class="menu-item" onclick="loadPage('employee.php', this, false)">
                    <i class="fas fa-users"></i>
                    <span class="tooltip">Employee Management</span>
                </div>
                <div class="menu-item" onclick="loadPage('shift.php', this, false)">
                    <i class="fas fa-user-shield"></i>
                    <span class="tooltip">Staff Allocation</span>
                </div>
                
            </div>
        </div>
        
        <div class="logout-btn" onclick="logout()">
            <i class="fas fa-sign-out-alt"></i>
            <span class="tooltip">Logout</span>
        </div>
    </div>
    
    <div class="content">
        <!-- Card container for Zone Management -->
        <div id="zone-management" class="card-container">
            <div class="card-header">
                <div class="card-title"></div>
                <div class="card-items">
                    <div class="card-item active" onclick="loadCardPage('zone.php', this)">
                        <i class="fas fa-map"></i>
                        <span>Zones</span>
                    </div>
                    <div class="card-item" onclick="loadCardPage('dcm.php', this)">
                        <i class="fas fa-layer-group"></i>
                        <span>DCMs</span>
                    </div>
                    <div class="card-item" onclick="loadCardPage('cluster.php', this)">
                        <i class="fas fa-object-group"></i>
                        <span>Clusters</span>
                    </div>
                    <div class="card-item" onclick="loadCardPage('line.php', this)">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Purchase Lines</span>
                    </div>
                    <div class="card-item" onclick="loadCardPage('clusters.php', this)">
                        <i class="fas fa-truck"></i>
                        <span>Delivery Lines</span>
                    </div>
                </div>
            </div>
        </div>
        
        <iframe id="contentFrame" src="home.php"></iframe>
    </div>

    <script>
        // Initialize the page
        document.addEventListener('DOMContentLoaded', function() {
            // Set home as active by default
            document.querySelector('.menu-item').classList.add('active');
            document.getElementById('contentFrame').src = 'home.php';
        });

        function loadPage(page, element, showCard) {
            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked menu item
            element.classList.add('active');
            
            // Hide all card containers
            document.querySelectorAll('.card-container').forEach(card => {
                card.classList.remove('active');
            });
            
            // Load the page
            document.getElementById('contentFrame').src = page;
        }

        function loadZoneManagement(page, element) {
            // Remove active class from all menu items
            document.querySelectorAll('.menu-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked menu item
            element.classList.add('active');
            
            // Show the zone management card container
            document.getElementById('zone-management').classList.add('active');
            
            // Remove active class from all card items
            document.querySelectorAll('.card-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to zones card item
            document.querySelector('.card-item').classList.add('active');
            
            // Load the zones page
            document.getElementById('contentFrame').src = page;
        }
        
        function loadCardPage(page, element) {
            // Remove active class from all card items
            document.querySelectorAll('.card-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Add active class to clicked card item
            element.classList.add('active');
            
            // Load the page
            document.getElementById('contentFrame').src = page;
        }
        
        function logout() {
            // Add your logout functionality here
            // For example, redirect to logout.php
            window.location.href = 'logout.php';
        }
    </script>
</body>
</html>