<?php
// Start the session
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

// Variables to store form input
$enrollment_certificate = $prefix = $surname = $other_names = $display_name = "";
$education = $gender = $address = $professional_designation = $area_of_practice = "";
$experience = $practice_courts = $contact_numbers = $email = $fax = $dob = $nic = "";
$authenticator_name = $authenticator_designation = $enrollment_certificate_copy = $nic_front_copy = "";
$nic_back_copy = $photograph_copy = $status = "pending"; // Default status

// Assuming the user is logged in and email is stored in the session
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form values
    $surname = $_POST['surname'];
    $other_names = $_POST['other_names'];
    $display_name = $_POST['display_name'];
    $education = $_POST['education'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $professional_designation = $_POST['professional_designation'];
    $area_of_practice = $_POST['area_of_practice'];
    $experience = $_POST['experience'];
    $practice_courts = $_POST['practice_courts'];
    $contact_numbers = $_POST['contact_numbers'];
    $email = $_POST['email'];
    $fax = $_POST['fax'];
    $dob = $_POST['dob'];
    $nic = $_POST['nic'];
    $authenticator_name = $_POST['authenticator_name'];
    $authenticator_designation = $_POST['authenticator_designation'];

    // Check if the email already exists in the database
    $emailCheckQuery = "SELECT * FROM lawyer_registrations WHERE email = '$email'";
    $result = mysqli_query($conn, $emailCheckQuery);
    
    if (mysqli_num_rows($result) > 0) {
        // If email already exists, show error and exit
        echo "This account is already registered.";
    } else {

        // Handle file uploads
        $uploadsDir = 'uploads/';
        $allowedFileTypes = ['pdf', 'jpg', 'jpeg', 'png'];

        // Function to generate a unique file name
        function generateUniqueFileName($file)
        {
            $fileExt = pathinfo($file['name'], PATHINFO_EXTENSION);
            return uniqid('file_', true) . '.' . $fileExt;
        }

        $enrollment_certificate_copy = generateUniqueFileName($_FILES['enrollment_certificate_copy']);
        $nic_front_copy = generateUniqueFileName($_FILES['nic_front_copy']);
        $nic_back_copy = generateUniqueFileName($_FILES['nic_back_copy']);
        $photograph_copy = generateUniqueFileName($_FILES['photograph_copy']);

        // Upload files
        if (
            move_uploaded_file($_FILES['enrollment_certificate_copy']['tmp_name'], $uploadsDir . $enrollment_certificate_copy) &&
            move_uploaded_file($_FILES['nic_front_copy']['tmp_name'], $uploadsDir . $nic_front_copy) &&
            move_uploaded_file($_FILES['nic_back_copy']['tmp_name'], $uploadsDir . $nic_back_copy) &&
            move_uploaded_file($_FILES['photograph_copy']['tmp_name'], $uploadsDir . $photograph_copy)
        ) {

            // Insert into database
            $query = "INSERT INTO lawyer_registrations 
                      (surname, other_names, display_name, education, gender, address, professional_designation, area_of_practice, 
                      experience, practice_courts, contact_numbers, email, fax, dob, nic, authenticator_name, authenticator_designation,
                      enrollment_certificate_copy, nic_front_copy, nic_back_copy, photograph_copy)
                      VALUES ('$surname', '$other_names', '$display_name', '$education', '$gender', '$address', '$professional_designation',
                              '$area_of_practice', '$experience', '$practice_courts', '$contact_numbers', '$email', '$fax', '$dob',
                              '$nic', '$authenticator_name', '$authenticator_designation', '$enrollment_certificate_copy', 
                              '$nic_front_copy', '$nic_back_copy', '$photograph_copy')";

            if (mysqli_query($conn, $query)) {
                echo "New lawyer registered successfully!";
            } else {
                echo "Error: " . mysqli_error($conn);
            }
        } else {
            echo "Sorry, there was an error uploading your files.";
        }
    }
}

// Function to handle file uploads
function uploadFile($inputName)
{
    if (isset($_FILES[$inputName]) && $_FILES[$inputName]['error'] == 0) {
        $targetDir = "uploads/"; // Ensure this directory exists
        $fileName = basename($_FILES[$inputName]["name"]);
        $targetFile = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($targetFile)) {
            echo "Sorry, file already exists.";
            return null; // If the file already exists, return null
        }

        // Check file size (limit to 5MB)
        if ($_FILES[$inputName]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            return null; // File is too large, return null
        }

        // Allow certain file formats
        if (!in_array($fileType, ["jpg", "png", "jpeg", "pdf"])) {
            echo "Sorry, only JPG, JPEG, PNG & PDF files are allowed.";
            return null; // Only specific file types allowed, return null
        }

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES[$inputName]["tmp_name"], $targetFile)) {
            return $targetFile; // Return the file path if upload is successful
        } else {
            echo "Sorry, there was an error uploading your file.";
            return null; // File upload failed
        }
    }
    return null; // No file uploaded
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Registration</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        form {
            max-width: 600px;
            margin: 20px auto;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }
    </style>
</head>

<body>
    <h1>Lawyer Registration Form</h1>
    <form action="registration_lawyer.php" method="POST" enctype="multipart/form-data">
        
        <!-- <label for="enrollment_certificate">Enrollment Certificate</label>
        <input type="text" id="enrollment_certificate" name="enrollment_certificate" required> -->

        <label for="prefix">Prefix</label>
        <select id="prefix" name="prefix" required>
            <option value="Mr">Mr</option>
            <option value="Mrs">Mrs</option>
            <option value="Ms">Ms</option>
        </select>

        <label for="surname">Surname</label>
        <input type="text" id="surname" name="surname" required>

        <label for="other_names">Other Names</label>
        <input type="text" id="other_names" name="other_names" required>

        <label for="display_name">Display Name</label>
        <input type="text" id="display_name" name="display_name" required>

        <label for="education">Education</label>
        <input type="text" id="education" name="education" required>

        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>

        <label for="address">Address</label>
        <input type="text" id="address" name="address" required>

        <label for="professional_designation">Professional Designation</label>
        <input type="text" id="professional_designation" name="professional_designation" required>

        <label for="area_of_practice">Area of Practice</label>
        <select id="area_of_practice" name="area_of_practice" required>
            <option value="civil">Civil</option>
            <option value="criminal">Criminal</option>
            <option value="labour">Labour</option>
            <option value="commercial">Commercial</option>
            <option value="notarial">Notarial</option>
        </select>


        <label for="experience">Experience</label>
        <input type="text" id="experience" name="experience" required>

        <label for="practice_courts">Practice Courts</label>
        <input type="text" id="practice_courts" name="practice_courts" required>

        <label for="contact_numbers">Contact Numbers</label>
        <input type="text" id="contact_numbers" name="contact_numbers" required>

        <label for="email">Email</label>
<input type="email" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" readonly required>

        <label for="fax">Fax</label>
        <input type="text" id="fax" name="fax">

        <label for="dob">Date of Birth</label>
        <input type="date" id="dob" name="dob" required>

        <label for="nic">NIC</label>
        <input type="text" id="nic" name="nic" required>

        <label for="authenticator_name">Authenticator Name</label>
        <input type="text" id="authenticator_name" name="authenticator_name" required>

        <label for="authenticator_designation">Authenticator Designation</label>
        <input type="text" id="authenticator_designation" name="authenticator_designation" required>

        <label for="enrollment_certificate_copy">Enrollment Certificate Copy (PDF)</label>
        <input type="file" id="enrollment_certificate_copy" name="enrollment_certificate_copy" required>

        <label for="nic_front_copy">NIC Front Copy</label>
        <input type="file" id="nic_front_copy" name="nic_front_copy" required>

        <label for="nic_back_copy">NIC Back Copy</label>
        <input type="file" id="nic_back_copy" name="nic_back_copy" required>

        <label for="photograph_copy">Photograph Copy</label>
        <input type="file" id="photograph_copy" name="photograph_copy" required>

        <button type="submit">Register Lawyer</button>
    </form>
</body>

</html>