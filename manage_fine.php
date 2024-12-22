<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "crime_management";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    // Delete the fine with the specified ID
    $sql = "DELETE FROM traffic_fines WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    
    if ($stmt->execute()) {
        echo "Fine deleted successfully!";
    } else {
        echo "Error deleting fine: " . $conn->error;
    }
}

// Initialize search variables
$search_value = "";
$search_query = "";

// Handle search request
if (isset($_POST['search'])) {
    $search_value = $_POST['search_value'];
    
    // Search by NIC number, ID, or Full Name
    $search_query = " WHERE nic_number LIKE '%$search_value%' OR id LIKE '%$search_value%' OR full_name LIKE '%$search_value%'";
}

// Fetch all fine details or search results from the database
$sql = "SELECT * FROM traffic_fines" . $search_query;
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="complaints.css"> 
    <title>Manage Traffic Fines</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-right: 10px;
        }

        input[type="text"] {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 300px;
        }

        button {
            padding: 8px 15px;
            margin-left: 10px;
            background-color: #0a2e52;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="button"] {
            background-color: #f44336;
        }

        button:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        a {
            text-decoration: none;
            color: #f44336;
        }

        a:hover {
            text-decoration: underline;
        }

        .btn-add {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
        }

        .btn-add a {
            text-decoration: none;
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-add a:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <h1>Manage Traffic Fines</h1>

    <!-- Search form -->
    <form method="post" action="">
        <label for="search_value">Search by NIC, ID, or Name:</label>
        <input type="text" name="search_value" id="search_value" value="<?php echo $search_value; ?>">
        <button type="submit" name="search">Search</button>
        <a href="manage_fine.php"><button type="button">Reset</button></a>
    </form>
    <br>

    <!-- Button to add a new fine -->
    <a href="add_fine_traffic.php">
        <button>Add New Fine</button>
    </a>
    <br><br>

    <!-- Table displaying all fines -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>NIC Number</th>
                <th>Full Name</th>
                <th>Place</th>
                <th>Case Details</th>
                <th>Fine Amount</th>
                <th>Paid Amount</th>
                <th>Remaining Amount</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                // Output data of each row
                while ($row = $result->fetch_assoc()) {
                    $remaining_amount = $row['fine_amount'] - $row['paid_amount'];
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['nic_number'] . "</td>";
                    echo "<td>" . $row['full_name'] . "</td>";
                    echo "<td>" . $row['place'] . "</td>";
                    echo "<td>" . $row['case_details'] . "</td>";
                    echo "<td>" . $row['fine_amount'] . "</td>";
                    echo "<td>" . $row['paid_amount'] . "</td>";
                    echo "<td>" . $remaining_amount . "</td>";
                    echo "<td>";
                    echo "<a href='manage_fine.php?delete_id=" . $row['id'] . "' onclick='return confirm(\"Are you sure you want to delete this fine?\");'>Delete</a>";
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='9'>No fines found</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>

<?php
$conn->close();
?>
