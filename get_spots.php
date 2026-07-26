<?php
include 'db_connection.php';

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the zone ID from the request
$zone = $_GET['zone'];

// Fetch available spots for the selected zone
$sql = "SELECT * FROM parkingspots WHERE ZoneID = $zone AND IsOccupied = 0";
$result = $conn->query($sql);

$spots = array();
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $spots[] = $row;
    }
}

echo json_encode($spots);

$conn->close();
?>
