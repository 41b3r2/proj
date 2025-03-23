<?php
session_start();

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCL Dashboard</title>
    <link rel="stylesheet" href="assets/css/landingPage.css">
    <script src="assets/js/landingPage.js"></script>
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
        <div class="logo-container">
            <img src="assets/img/logo.png" alt="PCL Logo" style="margin-right: 10px; width: 320px; height: auto;">
        </div>
        
        <div class="menu-grid">
            <!-- View Sheets - only accessible to users with viewsheet.php permission -->
            <?php if (hasAccess($u_id, "viewsheet.php", $permissions)): ?>
            <a href="viewsheet.php" class="menu-item">
                <div class="menu-icon sheets"></div>
                <div class="menu-label">View Sheets</div>
            </a>
            <?php else: ?>
            <div class="menu-item disabled">
                <div class="menu-icon sheets"></div>
                <div class="menu-label">View Sheets</div>
            </div>
            <?php endif; ?>
            
            <!-- New Sale (waybill.php) -->
            <?php if (hasAccess($u_id, "waybill.php", $permissions)): ?>
            <a href="waybill.php" class="menu-item">
                <div class="menu-icon new-sheet"></div>
                <div class="menu-label">New Sale</div>
            </a>
            <?php else: ?>
            <div class="menu-item disabled">
                <div class="menu-icon new-sheet"></div>
                <div class="menu-label">New Sale</div>
            </div>
            <?php endif; ?>

            <!-- Dispatch -->
            <?php if (hasAccess($u_id, "dispatcher.php", $permissions)): ?>
            <a href="dispatcher.php" class="menu-item">
                <div class="menu-icon dispatch"></div>
                <div class="menu-label">Dispatch</div>
            </a>
            <?php else: ?>
            <div class="menu-item disabled">
                <div class="menu-icon dispatch"></div>
                <div class="menu-label">Dispatch</div>
            </div>
            <?php endif; ?>

            <!-- Budget -->
            <?php if (hasAccess($u_id, "budget.php", $permissions)): ?>
            <a href="budget.php" class="menu-item">
                <div class="menu-icon budget"></div>
                <div class="menu-label">Budget</div>
            </a>
            <?php else: ?>
            <div class="menu-item disabled">
                <div class="menu-icon budget"></div>
                <div class="menu-label">Budget</div>
            </div>
            <?php endif; ?>

            <!-- Proof of Delivery -->
            <?php if (hasAccess($u_id, "pod.php", $permissions)): ?>
            <a href="pod.php" class="menu-item">
                <div class="menu-icon pod"></div>
                <div class="menu-label">Proof of Delivery</div>
            </a>
            <?php else: ?>
            <div class="menu-item disabled">
                <div class="menu-icon pod"></div>
                <div class="menu-label">Proof of Delivery</div>
            </div>
            <?php endif; ?>

            <!-- AR -->
            <?php if (hasAccess($u_id, "ar.php", $permissions)): ?>
            <a href="ar.php" class="menu-item">
                <div class="menu-icon ar"></div>
                <div class="menu-label">AR</div>
            </a>
            <?php else: ?>
            <div class="menu-item disabled">
                <div class="menu-icon ar"></div>
                <div class="menu-label">AR</div>
            </div>
            <?php endif; ?>
            
            <!-- Queries -->
            <?php if (hasAccess($u_id, "queries.php", $permissions)): ?>
            <a href="queries.php" class="menu-item">
                <div class="menu-icon queries">
                    <div class="funnel"></div>
                </div>
                <div class="menu-label">Queries</div>
            </a>
            <?php else: ?>
            <div class="menu-item disabled">
                <div class="menu-icon queries">
                    <div class="funnel"></div>
                </div>
                <div class="menu-label">Queries</div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>