<?php
include 'db_connection.php';


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql_parked = "SELECT v.VehicleID, v.Brand, v.Type, v.RegistrationNumber, u.Name AS OwnerName, u.Phone, pz.Name AS Zone, ps.SpotNumber, pc.Price AS ParkingCharge
                FROM vehicles v
                JOIN ParkingSpots ps ON v.ParkingSpotID = ps.SpotID
                JOIN ParkingZones pz ON ps.ZoneID = pz.ParkingID
                JOIN users u ON v.UserID = u.UserID
                JOIN PriceChart pc ON v.Type = pc.VehicleType
                WHERE ps.IsOccupied = 1";
$result_parked = $conn->query($sql_parked);

// Retrieve parking capacity information
$sql_capacity = "SELECT Name, Capacity, AvailableSpace FROM ParkingZones";
$result_capacity = $conn->query($sql_capacity);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Entry Form</title>
    <style>
        /* CSS styles for the UI */
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding-left: 60px;
            padding-right: 60px;
        }
/* CSS for Parking Capacity Table */
.table-parking {
  width: 100%;
  border-collapse: collapse;
  margin: 25px 0;
  font-size: 0.9em;
  font-family: sans-serif;
  min-width: 400px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}

.table-parking thead tr {
  background-color: #ffffff;
  color: #ffffff;
  text-align: left;

}

.table-parking th,
.table-parking td {
  padding: 12px 15px;
font-size:16px;
}

.table-parking tbody tr {
  border-bottom: 1px solid #ffffff;
}

.table-parking tbody tr:nth-of-type(even) {
  background-color: #ffffff;
}

.table-parking tbody tr:last-of-type {
  border-bottom: 2px solid #ffffff;
}

.table-parking tbody tr.active-row {
  font-weight: bold;
  color: #009879;

}


button {
  background-color: #5cb85c;
  font-size:18px;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
align:center;
}
h1{
            margin-top: 20px;
            text-align: center;
            padding-top: 40px;
            padding-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .nav {
            margin-bottom: 20px;
    display: flex;
    justify-content: center;

}

.nav button {
margin-top:20px;
margin-left:20px;

}

        
        .nav a {
            padding: 10px;
            text-decoration: none;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<div class="nav">
    <button onclick="window.location.href='index.php'">Entry A Vehicle</button>
    
</div>


    <div class="container">
        <h1>Parked Vehicles</h1>
        <?php if ($result_parked->num_rows > 0): ?>
            <table class="table-parking">
                <tr>
                    <th>Vehicle ID</th>
                    <th>Owner Name</th>
                    <th>Phone</th>
                    <th>Brand</th>
                    <th>Type</th>
                    <th>Registration Number</th>
                    <th>Parking Zone</th>
                    <th>Parking Spot</th>
                    <th>Parking Charge</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result_parked->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row["VehicleID"]; ?></td>
                        <td><?php echo $row["OwnerName"]; ?></td>
                        <td><?php echo $row["Phone"]; ?></td>
                        <td><?php echo $row["Brand"]; ?></td>
                        <td><?php echo $row["Type"]; ?></td>
                        <td><?php echo $row["RegistrationNumber"]; ?></td>
                        <td><?php echo $row["Zone"]; ?></td>
                        <td><?php echo $row["SpotNumber"]; ?></td>
                        <td><?php echo "$",$row["ParkingCharge"]; ?></td>
                        <td><a href="exit_vehicle.php?id=<?php echo $row["VehicleID"]; ?>">Remove</a></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No vehicles parked.</p>
        <?php endif; ?>
    </div>

    <div class="container">
        <h1>Parking Capacity</h1>
        <?php if ($result_capacity->num_rows > 0): ?>
            <table class="table-parking">
                <tr>
                    <th>Zone Name</th>
                    <th>Capacity</th>
                    <th>Available Space</th>
                </tr>
                <?php while ($row = $result_capacity->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row["Name"]; ?></td>
                        <td><?php echo $row["Capacity"]; ?></td>
                        <td><?php echo $row["AvailableSpace"]; ?></td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No parking capacity information available.</p>
        <?php endif; ?>
    </div>
    <div class="nav">
    <button onclick="window.location.href='index.php'">Entry A Vehicle</button>
    
</div>
</body>
</html>

<?php
$conn->close();
?>
