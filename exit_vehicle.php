<?php
include 'db_connection.php';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $vehicle_id = $_GET['id'];

    

    // Retrieve vehicle owner ID
    $sql_owner = "SELECT UserID FROM vehicles WHERE VehicleID = $vehicle_id";
    $result_owner = $conn->query($sql_owner);

    if ($result_owner->num_rows > 0) {
        $row_owner = $result_owner->fetch_assoc();
        $user_id = $row_owner['UserID'];

        // Update spot as available and retrieve spot information
        $sql_spot = "SELECT ps.SpotID, pz.ParkingID
                    FROM ParkingSpots ps
                    JOIN vehicles v ON v.ParkingSpotID = ps.SpotID
                    JOIN ParkingZones pz ON ps.ZoneID = pz.ParkingID
                    WHERE v.VehicleID = $vehicle_id";

        $result_spot = $conn->query($sql_spot);

        if ($result_spot->num_rows > 0) {
            $row_spot = $result_spot->fetch_assoc();
            $spot_id = $row_spot['SpotID'];
            $zone_id = $row_spot['ParkingID'];

            // Update spot as available
            $sql_update_spot = "UPDATE ParkingSpots SET IsOccupied = 0 WHERE SpotID = $spot_id";

            if ($conn->query($sql_update_spot) === TRUE) {
                // Delete vehicle entry
                $sql_delete_vehicle = "DELETE FROM vehicles WHERE VehicleID = $vehicle_id";

                if ($conn->query($sql_delete_vehicle) === TRUE) {
                    // Delete associated user entry
                    $sql_delete_user = "DELETE FROM users WHERE UserID = $user_id";
                    if ($conn->query($sql_delete_user) === TRUE) {
                        // Increase available space for the zone
                        $sql_increase_space = "UPDATE ParkingZones SET AvailableSpace = AvailableSpace + 1 WHERE ParkingID = $zone_id";

                        if ($conn->query($sql_increase_space) === TRUE) {
                            // Include view all list page
                            include 'view_vehicles.php';
                        } else {
                            echo "Error increasing available space: " . $conn->error;
                        }
                    } else {
                        echo "Error deleting user information: " . $conn->error;
                    }
                } else {
                    echo "Error deleting vehicle entry: " . $conn->error;
                }
            } else {
                echo "Error updating spot status: " . $conn->error;
            }
        } else {
            echo "Error retrieving spot information: " . $conn->error;
        }
    } else {
        echo "Vehicle owner not found.";
    }
} else {
    echo "Invalid vehicle ID.";
}

// Close connection
//$conn->close();
?>
