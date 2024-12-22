<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "crime_management"; // Replace with your actual database name

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Delete single profile
if(isset($_GET['delete_id'])){
    $delete_id = intval($_GET['delete_id']);
    $delete_query = "DELETE FROM lawyer_registrations  WHERE lawyer_id = $delete_id";
    if(mysqli_query($conn, $delete_query)) {
        echo "Lawyer profile deleted successfully!";
    } else {
        echo "Error deleting lawyer profile: " . mysqli_error($conn);
    }
}

// Delete multiple profiles
if(isset($_POST['delete_selected'])){
    if(!empty($_POST['selected_lawyers'])){
        $ids = implode(",", $_POST['selected_lawyers']);
        $delete_multiple_query = "DELETE FROM lawyer_registrations  WHERE lawyer_id IN ($ids)";
        if(mysqli_query($conn, $delete_multiple_query)) {
            echo "Selected lawyer profiles deleted successfully!";
        } else {
            echo "Error deleting selected lawyer profiles: " . mysqli_error($conn);
        }
    }
}

// Fetch lawyer profiles from the database
$query = "SELECT * FROM lawyer_registrations  ORDER BY lawyer_id ASC";
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Lawyer Profiles</title>
    <style>
        /* Add your custom styles here */
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            padding: 5px 10px;
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

<h1>Lawyer Profiles</h1>

<form method="post" action="">
    <table>
        <thead>
            <tr>
                <th>Select</th>
                <th>Lawyer ID</th>
                <th>Prefix</th>
                <th>Surname</th>
                <th>Other Names</th>
                <th>Display Name</th>
                <th>Education</th>
                <th>Gender</th>
                <th>Address</th>
                <th>Professional Designation</th>
                <th>Area of Practice</th>
                <th>Experience</th>
                <th>Contact Numbers</th>
                <th>Email</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td><input type='checkbox' name='selected_lawyers[]' value='{$row['lawyer_id']}'></td>";
                    echo "<td>{$row['lawyer_id']}</td>";
                    echo "<td>{$row['prefix']}</td>";
                    echo "<td>{$row['surname']}</td>";
                    echo "<td>{$row['other_names']}</td>";
                    echo "<td>{$row['display_name']}</td>";
                    echo "<td>{$row['education']}</td>";
                    echo "<td>{$row['gender']}</td>";
                    echo "<td>{$row['address']}</td>";
                    echo "<td>{$row['professional_designation']}</td>";
                    echo "<td>{$row['area_of_practice']}</td>";
                    echo "<td>{$row['experience']}</td>";
                    echo "<td>{$row['contact_numbers']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "<td>
                          
                            <a href='delete_profiles_lawyer.php?delete_id={$row['lawyer_id']}'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='14'>No lawyer profiles found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <br>
    <button type="submit" name="delete_selected">Delete Selected</button>
</form>

</body>
</html>

<?php
mysqli_close($conn); // Close the database connection
?>
