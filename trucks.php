<?php
session_start();

require_once 'connection.php';

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$u_id = $_SESSION["u_id"];
$permissions = [
    1 => ["all_access" => true],
    2 => ["waybill.php" => true, "dispatcher.php" => true, "viewsheet.php" => true],
    3 => ["pod.php" => true],
    4 => ["pod.php" => true, "ar.php" => true, "viewsheet.php" => true],
    5 => ["queries.php" => true, "viewsheet.php" => true],
    6 => ["budget.php" => true, "viewsheet.php" => true],
    7 => ["waybill.php" => true, "dispatcher.php" => true, "viewsheet.php" => true],
    8 => ["dispatcher.php" => true],
    9 => ["pod.php" => true]
];

function hasAccess($u_id, $page, $permissions) {
    return isset($permissions[$u_id]["all_access"]) || 
           (isset($permissions[$u_id][$page]) && $permissions[$u_id][$page]);
}

$query = "SELECT * FROM truck";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCL Dashboard - Truck Information</title>
    <link rel="stylesheet" href="assets/css/landingPage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="assets/js/landingPage.js"></script>
    <style>
        /* Table specific styles */
        .truck-table-container {
            margin: 20px;
            width: 90%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            background-color: #fff;
        }
        
        .table-header {
            background-color:rgba(106, 0, 11, 0.79);
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .truck-table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 3px rgba(0, 0, 0, 0.1);
        }
        
        .truck-table th {
            background-color: maroon;
            color: white;
            font-weight: bold;
            text-align: center;
            padding: 12px 15px;
        }
        
        .truck-table td {
            padding: 10px 15px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .truck-table tr:hover {
            background-color: #f5f5f5;
        }
        
        .truck-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0069d9;
        }
        
        .action-btn {
            margin: 2px;
        }
        
        .table-responsive {
            max-height: 900px; /* Adjust height as needed */
            overflow-y: auto;
            border-radius: 0px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .truck-table thead {
            position: sticky;
            top: 0;
            background-color: maroon;
            color: white;
            z-index: 10;
        }

        .truck-table th {
            padding: 12px;
            border-bottom: 2px solid #ddd;
        }

        /* Make the table responsive */
        @media screen and (max-width: 768px) {
            .truck-table {
                display: block;
                overflow-x: auto;
            }
            
            .truck-table-container {
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="mobile-toggle">☰</div>
    <div class="overlay"></div>
    <div class="loading-screen" id="loading-screen">
        <div class="loader"></div>
        <span>Loading...</span>
    </div>
    
    <div class="sidebar">
        <div class="user-info">
            <div class="name"><?php echo htmlspecialchars($_SESSION["fullname"]); ?></div>
            <div class="role">Role ID: <?php echo htmlspecialchars($_SESSION["u_id"]); ?></div>
        </div>
        <div>
            <div class="metric-section" data-href="landingPage.php">
                <div class="chart-container">
                    <div class="pie-chart">
                        <div class="pie-slice"></div>
                    </div>
                </div>
                <div class="metric-title">UTILIZATION</div>
            </div>
            <div class="metric-section" data-href="available.php">
                <div class="bar-container">
                    <div class="bar bar-1"></div>
                    <div class="bar bar-2"></div>
                    <div class="bar bar-3"></div>
                </div>
                <div class="metric-title">AVAILABLE TDH</div>
            </div>
            <div class="metric-section" data-href="references.php">
                <div class="chart-container">
                    <div class="people-icon">
                        <div class="people-head"></div>
                        <div class="people-body"></div>
                    </div>
                </div>
                <div class="metric-title">REFERENCES</div>
            </div>
        </div>
        
        <a href="logout.php" class="logout-link" id="logout-link">
            <div class="logout-section">
                <div class="logout-icon">←</div>
                <span>Log Out</span>
            </div>
        </a>
    </div>
    
    <div class="main-content">
        
        <div class="truck-table-container">
            <div class="table-header">
                Truck Information
            </div>
            <div class="table-responsive">
                <table class="truck-table">
                    <thead>
                        <tr>
                            <th>Truck ID</th>
                            <th>Model</th>
                            <th>Truck Plate</th>
                            <th>Status</th>
                            <th>Truck Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['truck_id']); ?></td>
                            <td><?php echo htmlspecialchars($row['model']); ?></td>
                            <td><?php echo htmlspecialchars($row['truck_plate']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td><?php echo htmlspecialchars($row['truck_type']); ?></td>
                            <td>
                                <a href="edit_truck.php?id=<?php echo $row['truck_id']; ?>" class="btn btn-primary action-btn">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>