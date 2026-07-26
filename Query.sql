-- Fetch available spots for the selected zone
SELECT * FROM parkingspots WHERE ZoneID = $zone AND IsOccupied = 0;

-- Check if registration number already exists
SELECT * FROM vehicles WHERE RegistrationNumber = '$registrationNumber';

-- Insert user data
INSERT INTO users (Name, Email, Phone) VALUES ('$name', '$email', '$phone');

-- Insert vehicle data
INSERT INTO vehicles (UserID, Brand, Type, RegistrationNumber, ParkingSpotID) 
VALUES ('$userID', '$brand', '$type', '$registrationNumber', '$spot');

-- Update spot as occupied
UPDATE parkingspots SET IsOccupied = 1 WHERE SpotID = $spot;

-- Fetch price from PriceChart based on vehicle type
SELECT Price FROM PriceChart WHERE VehicleType = '$type';

-- Update zone capacity and available space
UPDATE ParkingZones SET AvailableSpace = AvailableSpace - 1 WHERE ParkingID = $zone;

-- Retrieve zone name
SELECT Name FROM ParkingZones WHERE ParkingID = $zone;

-- Retrieve spot number
SELECT SpotNumber FROM ParkingSpots WHERE SpotID = $spot;

-- Retrieve parked vehicle info
SELECT v.VehicleID, v.Brand, v.Type, v.RegistrationNumber, u.Name AS OwnerName, u.Phone, pz.Name AS Zone, ps.SpotNumber, pc.Price AS ParkingCharge
FROM vehicles v
JOIN ParkingSpots ps ON v.ParkingSpotID = ps.SpotID
JOIN ParkingZones pz ON ps.ZoneID = pz.ParkingID
JOIN users u ON v.UserID = u.UserID
JOIN PriceChart pc ON v.Type = pc.VehicleType
WHERE ps.IsOccupied = 1;

-- Retrieve parking capacity information
SELECT Name, Capacity, AvailableSpace FROM ParkingZones;

-- Update spot as available and retrieve spot information
SELECT ps.SpotID, pz.ParkingID
FROM ParkingSpots ps
JOIN vehicles v ON v.ParkingSpotID = ps.SpotID
JOIN ParkingZones pz ON ps.ZoneID = pz.ParkingID
WHERE v.VehicleID = $vehicle_id;

-- Retrieve vehicle owner ID
SELECT UserID FROM vehicles WHERE VehicleID = $vehicle_id;

-- Update spot as available
UPDATE ParkingSpots SET IsOccupied = 0 WHERE SpotID = $spot_id;

-- Delete vehicle entry
DELETE FROM vehicles WHERE VehicleID = $vehicle_id;

-- Increase available space for the zone
UPDATE ParkingZones SET AvailableSpace = AvailableSpace + 1 WHERE ParkingID = $zone_id;

-- Delete associated user entry
DELETE FROM users WHERE UserID = $user_id; 

-- Delete vehicle entry
DELETE FROM vehicles WHERE VehicleID = $vehicle_id;
