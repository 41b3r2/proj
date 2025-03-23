<?php
// get_ready_for_budgeting.php
require_once 'connection.php';

$response = [];

try {
    // Modified query to only get "Ready for budgeting" trips
    $sql = "SELECT cs.*, 
            CONCAT(t.model, ', ', t.truck_plate, ', ', t.truck_type) AS truck_details,
            d.fullname AS driver_name,
            h1.fullname AS helper1_name,
            h2.fullname AS helper2_name,
            b.fuel, b.toll, b.parking, b.allowance, b.status
            FROM customerservice cs
            LEFT JOIN truck t ON cs.truck_id = t.truck_id
            LEFT JOIN driver d ON cs.driver = d.driver_id
            LEFT JOIN helper1 h1 ON cs.helper1 = h1.helper1_id
            LEFT JOIN helper2 h2 ON cs.helper2 = h2.helper2_id
            LEFT JOIN budget b ON cs.cs_id = b.cs_id
            WHERE cs.situation = 'Ready for budgeting'
            ORDER BY cs.date DESC";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
    }
    
} catch (Exception $e) {
    error_log("Error fetching ready for budgeting trips: " . $e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>