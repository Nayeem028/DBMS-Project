# 🅿️ Parking DBMS — Parking Management System

A web-based parking management system built with **PHP** and **MySQL** that allows staff to register incoming vehicles, assign parking spots, track occupancy, and process vehicle exits with automated billing.

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Database Schema](#database-schema)
- [Project Structure](#project-structure)
- [File Descriptions](#file-descriptions)
- [Setup & Installation](#setup--installation)
- [Usage Guide](#usage-guide)
- [SQL Queries Reference](#sql-queries-reference)
- [Known Issues & Limitations](#known-issues--limitations)

---

## Overview

The **Parking DBMS** is a simple, functional parking lot management system. It supports three parking zones (A, B, C), each with a fixed number of spots. When a vehicle arrives, a staff member fills out an entry form with driver and vehicle details and selects an available spot. Upon exit, the vehicle is removed from the system, the spot is freed, and the zone's available space is updated automatically.

---

## Features

- 📝 **Vehicle Entry Form** — Collects driver info (name, email, phone) and vehicle info (brand, type, registration number)
- 🗺️ **Zone & Spot Selection** — Dynamically loads available spots based on the selected parking zone via AJAX
- 💵 **Auto Billing** — Calculates parking charge based on vehicle type (Car, Motorbike, Truck)
- 📋 **Parked Vehicles List** — View all currently parked vehicles with owner details, zone, spot, and charge
- 🚪 **Vehicle Exit** — One-click removal that frees the spot and updates zone availability
- 📊 **Capacity Dashboard** — Shows total capacity and available space per zone

---

## Tech Stack

| Layer      | Technology          |
|------------|---------------------|
| Frontend   | HTML, CSS, JavaScript (Vanilla) |
| Backend    | PHP (procedural)    |
| Database   | MySQL / MariaDB     |
| DB Client  | phpMyAdmin          |
| Server     | Apache (XAMPP/WAMP) |

---

## Database Schema

The database is named `parkdbms` and contains five tables:

### `users`
Stores driver/owner information collected at entry.

| Column   | Type         | Notes              |
|----------|--------------|--------------------|
| UserID   | INT (PK, AI) | Auto-increment     |
| Name     | VARCHAR(255) |                    |
| Email    | VARCHAR(255) |                    |
| Phone    | VARCHAR(20)  |                    |

### `vehicles`
Stores vehicle information linked to a user and a parking spot.

| Column             | Type         | Notes                          |
|--------------------|--------------|--------------------------------|
| VehicleID          | INT (PK, AI) | Auto-increment                 |
| UserID             | INT (FK)     | References `users.UserID`      |
| Brand              | VARCHAR(255) |                                |
| Type               | VARCHAR(255) | Car / Motorbike / Truck        |
| RegistrationNumber | VARCHAR(20)  | Unique                         |
| ParkingSpotID      | INT (FK)     | References `parkingspots.SpotID` |

### `parkingspots`
Represents individual parking spots within a zone.

| Column     | Type          | Notes                              |
|------------|---------------|------------------------------------|
| SpotID     | INT (PK, AI)  | Auto-increment                     |
| ZoneID     | INT (FK)      | References `parkingzones.ParkingID`|
| SpotNumber | INT           | Spot number within the zone        |
| IsOccupied | TINYINT(1)    | 0 = Available, 1 = Occupied        |

### `parkingzones`
Represents parking zones with capacity tracking.

| Column         | Type         | Notes                     |
|----------------|--------------|---------------------------|
| ParkingID      | INT (PK, AI) | Auto-increment             |
| Name           | VARCHAR(255) | e.g., Zone A, Zone B      |
| Capacity       | INT          | Total spots in the zone   |
| AvailableSpace | INT          | Currently available spots |

**Default zones:**

| Zone   | Capacity | Initial Available |
|--------|----------|-------------------|
| Zone A | 20       | 17                |
| Zone B | 15       | 13                |
| Zone C | 10       | 0 (Full)          |

### `pricechart`
Defines the flat parking charge per vehicle type.

| Column      | Type           | Notes                |
|-------------|----------------|----------------------|
| PriceID     | INT (PK, AI)   |                      |
| VehicleType | VARCHAR(255)   | Unique               |
| Price       | DECIMAL(10,2)  |                      |

**Default pricing:**

| Vehicle Type | Price   |
|--------------|---------|
| Car          | $10.00  |
| Motorbike    | $5.00   |
| Truck        | $15.00  |

---

## Project Structure

```
parkdbms/
│
├── db_connection.php       # Database connection setup
├── index.php               # Vehicle entry form (main page)
├── get_spots.php           # AJAX endpoint — fetches available spots by zone
├── submit_entry.php        # Processes the entry form submission
├── view_vehicles.php       # Lists all parked vehicles + capacity dashboard
├── exit_vehicle.php        # Handles vehicle exit and cleanup
│
├── parkdbms.sql            # Full database dump (schema + seed data)
└── Query.sql               # Reference file of all SQL queries used
```

---

## File Descriptions

### `db_connection.php`
Establishes the MySQLi connection to the `parkdbms` database. Included at the top of every PHP file that needs DB access. Connection parameters: host `localhost`, user `root`, no password.

### `index.php`
The main landing page and vehicle entry form. Contains:
- A form collecting driver details (name, email, phone) and vehicle details (brand, type, registration number)
- A zone dropdown populated dynamically from the `ParkingZones` table
- A spot dropdown that updates via AJAX when a zone is selected (calls `get_spots.php`)
- A "View All Vehicles" button that navigates to `view_vehicles.php`

### `get_spots.php`
A lightweight AJAX endpoint called by the JavaScript in `index.php`. Accepts a `zone` parameter via GET and returns a JSON array of available (unoccupied) spots for that zone. Used to populate the spot dropdown dynamically without a page reload.

### `submit_entry.php`
Handles the POST submission from the entry form. Performs the following steps in order:
1. Checks if the registration number already exists (prevents duplicates)
2. Inserts a new row into `users`
3. Inserts a new row into `vehicles`
4. Marks the selected spot as occupied (`IsOccupied = 1`) in `parkingspots`
5. Decrements `AvailableSpace` in `parkingzones`
6. Fetches the price from `pricechart` based on vehicle type
7. Displays a booking confirmation with all details and total bill

### `view_vehicles.php`
Displays two tables:
- **Parked Vehicles** — A full list of currently parked vehicles with owner name, phone, brand, type, registration, zone, spot number, parking charge, and a "Remove" action link
- **Parking Capacity** — A summary table showing each zone's total capacity and current available spaces

### `exit_vehicle.php`
Processes a vehicle exit when the "Remove" link is clicked from `view_vehicles.php`. Accepts a `VehicleID` via GET and performs these steps:
1. Retrieves the `UserID` associated with the vehicle
2. Retrieves the `SpotID` and `ZoneID` from the spot assignment
3. Marks the spot as available (`IsOccupied = 0`)
4. Deletes the vehicle record from `vehicles`
5. Deletes the associated user record from `users`
6. Increments `AvailableSpace` in `parkingzones`
7. Re-renders `view_vehicles.php` to show the updated list

### `parkdbms.sql`
A complete phpMyAdmin SQL dump. Import this file to recreate the full database with schema, indexes, foreign keys, auto-increment values, and sample data.

### `Query.sql`
A reference file listing all SQL queries used throughout the application. Useful for understanding the data flow and for debugging.

---

## Setup & Installation

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) or [WAMP](https://www.wampserver.com/) installed
- PHP 7.x or 8.x
- MySQL / MariaDB

### Steps

1. **Clone or copy** the project files into your web server's root directory:
   ```
   C:/xampp/htdocs/parkdbms/
   ```

2. **Import the database:**
   - Open phpMyAdmin at `http://localhost/phpmyadmin`
   - Click **Import**
   - Select `parkdbms.sql` and click **Go**

3. **Verify the DB connection** in `db_connection.php`:
   ```php
   $db_host = 'localhost';
   $db_user = 'root';
   $db_pass = '';        // Update if you have a MySQL password
   $db_name = 'parkdbms';
   ```

4. **Start Apache and MySQL** in XAMPP Control Panel.

5. **Open the application** in your browser:
   ```
   http://localhost/parkdbms/index.php
   ```

---

## Usage Guide

### Parking a Vehicle
1. Go to `index.php` (the entry form)
2. Fill in driver details: Name, Email, Phone
3. Fill in vehicle details: Brand, Type, Registration Number
4. Select a **Zone** from the dropdown — available spots for that zone will load automatically
5. Select a **Spot**
6. Click **Confirm Booking** — a confirmation page shows the booking summary and total charge

### Viewing Parked Vehicles
- Click **View All Vehicles** from the entry form, or navigate to `view_vehicles.php`
- The page lists all currently parked vehicles and shows zone capacity at the bottom

### Removing a Vehicle (Exit)
- On the `view_vehicles.php` page, click **Remove** next to a vehicle
- The system frees the spot, deletes the vehicle and user records, and updates zone availability

---

## SQL Queries Reference

| Purpose | Query File Location |
|---|---|
| Fetch available spots by zone | `get_spots.php` / `Query.sql` |
| Check duplicate registration | `submit_entry.php` / `Query.sql` |
| Insert user & vehicle on entry | `submit_entry.php` / `Query.sql` |
| Mark spot occupied/available | `submit_entry.php`, `exit_vehicle.php` |
| Update zone available space | `submit_entry.php`, `exit_vehicle.php` |
| Retrieve full parked vehicle list | `view_vehicles.php` / `Query.sql` |
| Delete vehicle & user on exit | `exit_vehicle.php` / `Query.sql` |

---

## Known Issues & Limitations

> These are areas that should be addressed before deploying to production.

- **SQL Injection Vulnerability** — User inputs are inserted directly into SQL strings without prepared statements or parameterized queries. All queries should be rewritten using `mysqli_prepare()` or PDO.
- **No Authentication** — There is no login system; anyone with access to the URL can manage vehicles.
- **User Records Deleted on Exit** — Each entry creates a new user record, and it is deleted on exit. There is no persistent customer history.
- **Duplicate Users Allowed** — The same person can be inserted into the `users` table multiple times (only the registration number is checked for uniqueness).
- **No Timestamp Tracking** — Entry and exit times are not recorded, so duration-based billing is not possible with the current schema.
- **Static Pricing** — Pricing is flat per vehicle type with no hourly/daily rate calculation.
- **No Input Validation on Server Side** — Validation is minimal; robust server-side sanitization should be added.

---

## License

This project was created for academic/educational purposes.
