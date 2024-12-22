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

// Handle status update request
if (isset($_POST['update_status'])) {
    $action_level = $_POST['action_level'];
    $update_sql = "UPDATE complaints SET action_level = ? WHERE id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("si", $action_level, $id);
    
    if ($stmt->execute()) {
        $message = "Complaint status updated successfully.";
    } else {
        $message = "Error updating status: " . $conn->error;
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Complaint</title>
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
        .detail {
            margin-bottom: 15px;
        }
        .label {
            font-weight: bold;
        }
        .message {
            text-align: center;
            margin-bottom: 15px;
            color: green;
        }
        select {
            padding: 5px;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            padding: 5px 10px;
            color: white;
            border: none;
            border-radius: 4px;
            background-color: #007bff;
            cursor: pointer;
        }
        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
      <!-- Include the sidebar -->
      <?php include 'sidebar.php'; ?>
    <div class="container">
        <h1>Complaint Details</h1>

        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <div class="detail">
            <span class="label">Title:</span> <?php echo $complaint['title']; ?>
        </div>
        <div class="detail">
            <span class="label">Victim Name:</span> <?php echo $complaint['victim_name']; ?>
        </div>
        <div class="detail">
            <span class="label">Address:</span> <?php echo $complaint['address']; ?>
        </div>
        <div class="detail">
            <span class="label">NIC Number:</span> <?php echo $complaint['nic_number']; ?>
        </div>
        <div class="detail">
            <span class="label">District:</span> <?php echo $complaint['district']; ?>
        </div>
        <div class="detail">
            <span class="label">Mobile Number:</span> <?php echo $complaint['mobile_number']; ?>
        </div>
        <div class="detail">
            <span class="label">Age:</span> <?php echo $complaint['age']; ?>
        </div>
        <div class="detail">
            <span class="label">Gender:</span> <?php echo $complaint['gender']; ?>
        </div>
        <div class="detail">
            <span class="label">Date of Occurrence:</span> <?php echo $complaint['occurrence_date']; ?>
        </div>
        <div class="detail">
            <span class="label">Place of Occurrence:</span> <?php echo $complaint['place']; ?>
        </div>
        <div class="detail">
            <span class="label">Complaint:</span> <?php echo nl2br($complaint['complaint']); ?>
        </div>
        <div class="detail">
            <span class="label">NIC Front Image:</span> <a href="<?php echo $complaint['nic_front']; ?>" target="_blank">View Image</a>
        </div>
        <div class="detail">
            <span class="label">NIC Back Image:</span> <a href="<?php echo $complaint['nic_back']; ?>" target="_blank">View Image</a>
        </div>

        <h2>Update Complaint Status</h2>
        <form action="" method="POST">
            <div class="detail">
                <span class="label">Current Status:</span> <?php echo $complaint['action_level']; ?>
            </div>
            <div class="detail">
                <span class="label">Change Status:</span>
                <select name="action_level">
                    <option value="Pending" <?php if ($complaint['action_level'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Taking Action" <?php if ($complaint['action_level'] == 'Taking Action') echo 'selected'; ?>>Taking Action</option>
                    <option value="Completed" <?php if ($complaint['action_level'] == 'Completed') echo 'selected'; ?>>Completed</option>
                </select>
            </div>
            <button type="submit" name="update_status">Update Status</button>
        </form>

        <a href="manage_complaints.php">Back to Complaints List</a>
    </div>
</body>
</html>

<?php
// Close the connection
$conn->close();
?>
