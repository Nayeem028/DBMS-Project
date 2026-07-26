
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Entry Form</title>
    <style>
        /* CSS styles for the UI */
        body {

 font-family: 'Arial', sans-serif;
  background-color: #f4f4f4;
  margin: 0px;
  padding-top: 10px;
  padding-left: 150px;
padding-right: 150px;
padding-bottom: 50px;
border: 1px solid #ddd;
box-shadow: 1px #45a049;

}

form {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

input, select {
  width: 100%;
  padding: 10px;
  margin-bottom: 10px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size:16px;
}

button {
  background-color: #5cb85c;
  font-size:18px;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #4cae4c;
}

        .container {
            max-width: 600px;
            margin: 0 auto;
 padding-top: 10px;
padding-bottom: 50px;
        }
        h2 {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-top: 10px;
        }
        input, select {
            width: 98%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>



<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $brand = $_POST['brand'];
    $type = $_POST['type'];
    $registrationNumber = $_POST['registrationNumber'];
    $zone = $_POST['zone'];
    $spot = $_POST['spot'];

    // Check if registration number already exists
    $sql_check = "SELECT * FROM vehicles WHERE RegistrationNumber = '$registrationNumber'";
    $result_check = $conn->query($sql_check);
    if ($result_check->num_rows > 0) {
        echo "Error: Registration number already exists. Please use a different registration number.";
    } else {
        // Insert user data
        $sql_user = "INSERT INTO users (Name, Email, Phone) VALUES ('$name', '$email', '$phone')";
        if ($conn->query($sql_user) === TRUE) {
            $userID = $conn->insert_id;

            // Insert vehicle data
            $sql_vehicle = "INSERT INTO vehicles (UserID, Brand, Type, RegistrationNumber, ParkingSpotID) 
                            VALUES ('$userID', '$brand', '$type', '$registrationNumber', '$spot')";
            if ($conn->query($sql_vehicle) === TRUE) {
                // Update spot as occupied
                $sql_update_spot = "UPDATE parkingspots SET IsOccupied = 1 WHERE SpotID = $spot";
                $conn->query($sql_update_spot);

                

                // Fetch price from PriceChart based on vehicle type
                $sql_price = "SELECT Price FROM PriceChart WHERE VehicleType = '$type'";
                $result_price = $conn->query($sql_price);
                if ($result_price->num_rows > 0) {
                    $row = $result_price->fetch_assoc();
                    $price = $row['Price'];

                    // Update zone capacity and available space
                    $sql_update_capacity = "UPDATE ParkingZones SET AvailableSpace = AvailableSpace - 1 WHERE ParkingID = $zone";
                    $conn->query($sql_update_capacity);

                    // Retrieve spot number
                    $sql_spot_number = "SELECT SpotNumber FROM ParkingSpots WHERE SpotID = $spot";
                    $result_spot_number = $conn->query($sql_spot_number);
                    if ($result_spot_number->num_rows > 0) {
                        $row_spot_number = $result_spot_number->fetch_assoc();
                        $spot_number = $row_spot_number['SpotNumber'];

                        // Retrieve zone name
                        $sql_zone_name = "SELECT Name FROM ParkingZones WHERE ParkingID = $zone";
                        $result_zone_name = $conn->query($sql_zone_name);
                        if ($result_zone_name->num_rows > 0) {
                            $row_zone_name = $result_zone_name->fetch_assoc();
                            $zone_name = $row_zone_name['Name'];

                            // Return confirmation message and details
                            echo "<div style='color: red; font-weight: bold; font-size: 20px;'>Booking confirmed!<br></div>";
                            echo "Name: $name<br>";
                            echo "Vehicle Type: $type<br>";
                            echo "Registration Number: $registrationNumber<br>";
                            echo "Parking Spot: Spot $spot_number<br>";
                            echo "Parking Zone: $zone_name<br>";
                            echo "<h2>Total Bill: $$price</h2>";
                            echo "<form action='index.php'><button type='submit'>Make Another Booking</button></form>";
                            echo "<form action='view_vehicles.php'><button type='submit'>View parked vehicle list</button></form>"; // Add link to view all vehicles page
                        } else {
                            echo "Error: Unable to retrieve zone name.";
                        }
                    } else {
                        echo "Error: Unable to retrieve spot number.";
                    }
                } else {
                    echo "Error: Unable to fetch price for vehicle type.";
                }
            } else {
                echo "Error: " . $sql_vehicle . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql_user . "<br>" . $conn->error;
        }
    }

    $conn->close();
} else {
    header("Location: index.php"); // Redirect back to the form page
}
?>
</body>
</html>
