<?php
// Update your fetch_trips.php file to only show pending trips in the main table

// Include the database connection
require_once 'connection.php';

// Function to get all pending trips
function getPendingTrips() {
    global $conn;
    $trips = array();
    
    try {
        // Modified query to properly join related data and only get "Pending" trips
        $sql = "SELECT cs.*, 
                CONCAT(t.model, ', ', t.truck_plate, ', ', t.truck_type) AS truck_details,
                d.fullname AS driver_name,
                h1.fullname AS helper1_name,
                h2.fullname AS helper2_name
                FROM customerservice cs
                LEFT JOIN truck t ON cs.truck_id = t.truck_id
                LEFT JOIN driver d ON cs.driver = d.driver_id
                LEFT JOIN helper1 h1 ON cs.helper1 = h1.helper1_id
                LEFT JOIN helper2 h2 ON cs.helper2 = h2.helper2_id
                WHERE (cs.truck_id IS NULL OR cs.driver IS NULL OR cs.helper1 IS NULL)
                OR cs.situation = 'Pending'
                ORDER BY cs.date DESC";
        
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Check if truck, driver, and helper1 fields are empty
                if (empty($row['truck_id']) || empty($row['driver']) || empty($row['helper1'])) {
                    // Set situation to "Pending"
                    $row['situation'] = "Pending";
                    
                    // Also update the database
                    $updateSql = "UPDATE customerservice SET situation = 'Pending' 
                                 WHERE cs_id = ? AND (truck_id IS NULL OR driver IS NULL OR helper1 IS NULL)";
                    $stmt = $conn->prepare($updateSql);
                    $stmt->bind_param("i", $row['cs_id']);
                    $stmt->execute();
                    $stmt->close();
                }
                
                $trips[] = $row;
            }
        }
    } catch (Exception $e) {
        // Handle exception
        error_log("Error fetching trips: " . $e->getMessage());
    }
    
    return $trips;
}

// Return data as JSON if it's an AJAX request
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $trips = getPendingTrips();
    echo json_encode($trips);
    exit;
}
?>