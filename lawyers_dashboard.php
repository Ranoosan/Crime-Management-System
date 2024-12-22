<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "crime_management"; // replace with your actual database name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize filters
$search_name = '';
$area_of_practice = '';
$gender = '';

// Check if filter form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_name = mysqli_real_escape_string($conn, $_POST['search_name']);
    $area_of_practice = mysqli_real_escape_string($conn, $_POST['area_of_practice']);
    $gender = mysqli_real_escape_string($conn, $_POST['gender']);
}

// Fetch lawyer details based on filter
$query = "SELECT * FROM lawyer_registrations WHERE 1=1";

// Apply filters
if (!empty($search_name)) {
    $query .= " AND display_name LIKE '%$search_name%'";
}
if (!empty($area_of_practice)) {
    $query .= " AND area_of_practice = '$area_of_practice'";
}
if (!empty($gender)) {
    $query .= " AND gender = '$gender'";
}

$result = mysqli_query($conn, $query);
if (!$result) {
    die("Error: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include 'navbar.php'; ?>
    <br><br><br><br><br><br><br>
    <title style="color: #007bff;">Lawyer Consultants</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }

        .lawyer-card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            overflow: hidden;
            display: flex;
            padding: 15px;
            align-items: center;
        }

        .lawyer-card img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 20px;
        }

        .lawyer-details {
            flex: 1;
        }

        .lawyer-details h2 {
            margin-top: 0;
            font-size: 22px;
            color: #333;
        }

        .lawyer-details p {
            margin: 5px 0;
            font-size: 16px;
            color: #666;
        }

        .lawyer-details p span {
            font-weight: bold;
        }

        .action-buttons {
            margin-top: 15px;
        }

        .action-buttons a {
            text-decoration: none;
            margin-right: 10px;
            padding: 10px 20px;
            border-radius: 5px;
            color: #fff;
            background-color: #28a745;
            transition: background-color 0.3s;
        }

        .action-buttons a.call-now {
            background-color: #007bff;
        }

        .action-buttons a.mail-now {
            background-color: #ffc107;
        }

        .action-buttons a:hover {
            opacity: 0.8;
        }

        form {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        form label {
            margin-right: 10px;
            font-size: 16px;
        }

        form input,
        form select {
            padding: 10px;
            font-size: 16px;
            margin: 5px;
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>Lawyer Consultants</h1>
        <p>Here are the details of all the registered lawyers.</p>

        <!-- Filter Form -->
        <form method="POST" action="">
            <div>
                <label for="search_name">Search by Name</label>
                <input type="text" id="search_name" name="search_name" value="<?php echo $search_name; ?>" placeholder="Search Lawyer Name">
            </div>
            <div>
                <label for="area_of_practice">Area of Practice</label>
                <select id="area_of_practice" name="area_of_practice">
                    <option value="">-- Select --</option>
                    <option value="civil" <?php if ($area_of_practice == 'civil') echo 'selected'; ?>>Civil</option>
                    <option value="criminal" <?php if ($area_of_practice == 'criminal') echo 'selected'; ?>>Criminal</option>
                    <option value="labour" <?php if ($area_of_practice == 'labour') echo 'selected'; ?>>Labour</option>
                    <option value="commercial" <?php if ($area_of_practice == 'commercial') echo 'selected'; ?>>Commercial</option>
                    <option value="notarial" <?php if ($area_of_practice == 'notarial') echo 'selected'; ?>>Notarial</option>
                </select>
            </div>
            <div>
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="">-- Select --</option>
                    <option value="male" <?php if ($gender == 'male') echo 'selected'; ?>>Male</option>
                    <option value="female" <?php if ($gender == 'female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <button type="submit">Filter</button>
        </form>

        <?php
        // Check if there are any lawyers in the database
        if (mysqli_num_rows($result) > 0) {
            // Output data of each lawyer
            while ($row = mysqli_fetch_assoc($result)) {
                // Lawyer photo and details
                $photo_url = 'uploads/' . $row['photograph_copy'];
                $name = $row['display_name'];
                $profession = $row['professional_designation'];
                $contact = $row['contact_numbers'];
                $email = $row['email'];
                $area_of_practice = $row['area_of_practice'];
                $experience = $row['experience'];
                $address = $row['address'];
        ?>
                <div class="lawyer-card">
                    <img src="<?php echo $photo_url; ?>" alt="Lawyer Photo">
                    <div class="lawyer-details">
                        <h2><?php echo $name; ?></h2>
                        <p><span>Profession:</span> <?php echo $profession; ?></p>
                        <p><span>Contact:</span> <?php echo $contact; ?></p>
                        <p><span>Email:</span> <?php echo $email; ?></p>
                        <p><span>Area of Practice:</span> <?php echo $area_of_practice; ?></p>
                        <p><span>Experience:</span> <?php echo $experience; ?></p>
                        <p><span>Address:</span> <?php echo $address; ?></p>

                        <!-- Call Now and Mail Now buttons -->
                        <div class="action-buttons">
                            <a href="tel:<?php echo $contact; ?>" class="call-now">Call Now</a>
                            <a href="mailto:<?php echo $email; ?>" class="mail-now">Mail Now</a>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<p>No lawyers found.</p>";
        }

        // Close the database connection
        mysqli_close($conn);
        ?>
    </div>

</body>

</html>
