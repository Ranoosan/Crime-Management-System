<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'crime_management');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch complaint details
$id = intval($_GET['id']);
$sql = "SELECT * FROM complaints WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$complaint = $result->fetch_assoc();

if (!$complaint) {
    die("Complaint not found.");
}

// Handle form submission for updating
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $conn->real_escape_string(trim($_POST['title']));
    $victim_name = $conn->real_escape_string(trim($_POST['victim_name']));
    $address = $conn->real_escape_string(trim($_POST['address']));
    $nic_number = $conn->real_escape_string(trim($_POST['nic_number']));
    $district = $conn->real_escape_string(trim($_POST['district']));
    $mobile_number = $conn->real_escape_string(trim($_POST['mobile_number']));
    $age = $conn->real_escape_string(trim($_POST['age']));
    $gender = $conn->real_escape_string(trim($_POST['gender']));
    $occurrence_date = $conn->real_escape_string(trim($_POST['occurrence_date']));
    $place = $conn->real_escape_string(trim($_POST['place']));
    $complaint_text = $conn->real_escape_string(trim($_POST['complaint']));

    // Update the complaint in the database
    $update_sql = "UPDATE complaints SET title = ?, victim_name = ?, address = ?, nic_number = ?, district = ?, mobile_number = ?, age = ?, gender = ?, occurrence_date = ?, place = ?, complaint = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssssssssssii", $title, $victim_name, $address, $nic_number, $district, $mobile_number, $age, $gender, $occurrence_date, $place, $complaint_text, $id);

    if ($stmt->execute()) {
        $message = "Complaint updated successfully.";
    } else {
        $message = "Error updating complaint: " . $conn->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Complaint</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .form-group button {
            padding: 10px 20px;
            color: white;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            cursor: pointer;
        }

        .form-group button:hover {
            opacity: 0.9;
        }

        .message {
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>

<body>
      <!-- Include the sidebar -->
      <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Edit Complaint</h1>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="title">Title (Mr/Mrs/Ms):</label>
                <input type="radio" id="mr" name="title" value="Mr" <?php echo $complaint['title'] == 'Mr' ? 'checked' : ''; ?> required> Mr
                <input type="radio" id="mrs" name="title" value="Mrs" <?php echo $complaint['title'] == 'Mrs' ? 'checked' : ''; ?> required> Mrs
                <input type="radio" id="ms" name="title" value="Ms" <?php echo $complaint['title'] == 'Ms' ? 'checked' : ''; ?> required> Ms
            </div>

            <div class="form-group">
                <label for="victim_name">Full Name:</label>
                <input type="text" id="victim_name" name="victim_name" value="<?php echo $complaint['victim_name']; ?>" required>
            </div>

            <div class="form-group">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" value="<?php echo $complaint['address']; ?>" required>
            </div>

            <div class="form-group">
                <label for="nic_number">NIC Number:</label>
                <input type="text" id="nic_number" name="nic_number" value="<?php echo $complaint['nic_number']; ?>" required>
            </div>

            <div class="form-group">
                <label for="district">District:</label>
                <input type="text" id="district" name="district" value="<?php echo $complaint['district']; ?>" required>
            </div>

            <div class="form-group">
                <label for="mobile_number">Mobile Number:</label>
                <input type="tel" id="mobile_number" name="mobile_number" value="<?php echo $complaint['mobile_number']; ?>" required>
            </div>

            <div class="form-group">
                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo $complaint['age']; ?>" required>
            </div>

            <div class="form-group">
                <label for="gender">Gender:</label>
                <input type="radio" id="male" name="gender" value="Male" <?php echo $complaint['gender'] == 'Male' ? 'checked' : ''; ?> required> Male
                <input type="radio" id="female" name="gender" value="Female" <?php echo $complaint['gender'] == 'Female' ? 'checked' : ''; ?> required> Female
            </div>

            <div class="form-group">
                <label for="occurrence_date">Date of the Occurrence:</label>
                <input type="date" id="occurrence_date" name="occurrence_date" value="<?php echo $complaint['occurrence_date']; ?>" required>
            </div>

            <div class="form-group">
                <label for="place">Place of Occurrence:</label>
                <input type="text" id="place" name="place" value="<?php echo $complaint['place']; ?>" required>
            </div>

            <div class="form-group">
                <label for="complaint">Describe Your Complaint:</label>
                <textarea id="complaint" name="complaint" rows="5" required><?php echo $complaint['complaint']; ?></textarea>
            </div>

            <div class="form-group">
                <button type="submit">Update Complaint</button>
            </div>
        </form>

        <a href="manage_complaints.php">Back to Complaints List</a>
    </div>
</body>

</html>

<?php
// Close the connection
$conn->close();
?>