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
  margin: 0;
  padding-top: 10px;
  padding-left: 150px;
padding-right: 150px;
padding-bottom: 50px;
}

form {
    padding-top: 60px;
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}
nav{
    padding: 60 px;

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
  margin-top : 20px ;
  margin-bottom: 30px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background-color: #4cae4c;
}

        .container {
            max-width: 900px;
            margin: 0 auto;
 padding-top: 10px;
padding-bottom: 50px;
        }
        h2 {
            margin-top: 20px;
        }
        h1{
            margin-top: 20px;
            text-align: center;
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

<div class="container">
    <h1>Parking Entry Form</h1>
    <div class="nav">
        <!-- Button to view all vehicles -->
        <button onclick="window.location.href='view_vehicles.php'">View All Vehicles</button>
    </div>


   

        <form id="entryForm" action="submit_entry.php" method="post">
            <h2>Driver Details</h2>
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" >
            <label for="phone">Phone:</label>
            <input type="number" id="phone" name="phone" required >

            <h2>Vehicle Details</h2>
            <label for="brand">Brand:</label>
            <input type="text" id="brand" name="brand" >
            <label for="type">Type:</label>
            <select id="type" name="type" required>
                <option value="Car">Car</option>
                <option value="Motorbike">Motorbike</option>
                <option value="Truck">Truck</option>
            </select>
            <label for="registrationNumber">Registration Number:</label>
            <input type="text" id="registrationNumber" name="registrationNumber" required>

            <h2>Choose Zone and Spot</h2>
            <label for="zone">Zone:</label>
            <select id="zone" name="zone" required>
    <option value="" disabled selected>-Select-</option>
    <?php
    include 'db_connection.php';


    $sql = "SELECT * FROM ParkingZones";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['ParkingID'] . "'>" . $row['Name'] . "</option>";
        }
    } else {
        echo "<option disabled>No zones available th</option>";
    }
    ?>
</select>

            <label for="spot">Spot:</label>
            <select id="spot" name="spot" required>
                <option value="">Select Spot</option>
            </select>

            <button type="submit">Confirm Booking</button>
            <p id="noSpotMessage" style="color: red; display: none;">No spots available in this zone. Please select another zone.</p>

        </form>
    </div>

    <script>
        // JavaScript code for dynamic spot selection based on zone
        document.getElementById('zone').addEventListener('change', function() {
            var zone = this.value;
            var spotSelect = document.getElementById('spot');

            // Clear existing options except the first one
            while (spotSelect.options.length > 1) {
                spotSelect.remove(1);
            }

            // Fetch spots from database
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var spots = JSON.parse(this.responseText);
                    if (spots.length === 0) {
                        document.getElementById('noSpotMessage').style.display = 'block';
                    } else {
                        document.getElementById('noSpotMessage').style.display = 'none';
                        spots.forEach(function(spot) {
                            var option = document.createElement('option');
                            option.text = 'Spot ' + spot.SpotNumber;
                            option.value = spot.SpotID;
                            spotSelect.add(option);
                        });
                    }
                }
            };
            xhr.open('GET', 'get_spots.php?zone=' + zone, true);
            xhr.send();
        });
          
    </script>

</body>
</html>
