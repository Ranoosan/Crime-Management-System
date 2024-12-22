<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Police Clearance Application</title>
</head>
<body>
    <h1>Police Clearance Application Form</h1>
      <!-- Button to Check Police Clearance Status -->
      <form action="user_clearance_status.php" method="get" style="margin-top: 20px;">
        <button type="submit">Check My Clearance Status</button>
    </form>
    <form action="submit_clearance.php" method="post" enctype="multipart/form-data">
        <!-- Personal Information Section -->
        <h2>Personal Information</h2>
        <label for="full_name">Full Name:</label>
        <input type="text" id="full_name" name="full_name" required><br><br>
        
        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required><br><br>
        
        <label for="gender">Gender:</label>
        <select id="gender" name="gender" required>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
            <option value="Other">Other</option>
        </select><br><br>
        
        <label for="nationality">Nationality:</label>
        <input type="text" id="nationality" name="nationality" required><br><br>
        
        <label for="id_number">National ID/Passport Number:</label>
        <input type="text" id="id_number" name="id_number" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="phone">Phone Number:</label>
        <input type="text" id="phone" name="phone" required><br><br>

        <!-- Address Details Section -->
        <h2>Address Details</h2>
        <label for="current_address">Current Address:</label><br>
        <textarea id="current_address" name="current_address" rows="4" cols="50" required></textarea><br><br>

        <label for="previous_address">Previous Address (if any):</label><br>
        <textarea id="previous_address" name="previous_address" rows="4" cols="50"></textarea><br><br>

        <!-- Employment Details Section -->
        <h2>Employment Details</h2>
        <label for="occupation">Occupation:</label>
        <input type="text" id="occupation" name="occupation"><br><br>

        <label for="employer_name">Employer Name:</label>
        <input type="text" id="employer_name" name="employer_name"><br><br>

        <label for="employer_address">Employer Address:</label><br>
        <textarea id="employer_address" name="employer_address" rows="4" cols="50"></textarea><br><br>

        <label for="employer_contact_number">Employer Contact Number:</label>
        <input type="text" id="employer_contact_number" name="employer_contact_number"><br><br>

        <!-- Purpose of Police Clearance Section -->
        <h2>Purpose of Police Clearance</h2>
        <label for="reason">Reason for Police Clearance:</label><br>
        <textarea id="reason" name="reason" rows="4" cols="50" required></textarea><br><br>

        <label for="country_applying_for">Country Applying For (if any):</label>
        <input type="text" id="country_applying_for" name="country_applying_for"><br><br>

        <!-- Criminal History Section -->
        <h2>Criminal History</h2>
        <label for="criminal_history">Criminal History (if any):</label><br>
        <textarea id="criminal_history" name="criminal_history" rows="4" cols="50"></textarea><br><br>

        <!-- Supporting Documents Section -->
        <h2>Supporting Documents</h2>
        <label for="id_upload">Upload National ID/Passport Copy:</label>
        <input type="file" id="id_upload" name="id_upload" required><br><br>

        <label for="photo_upload">Upload Passport-Sized Photograph:</label>
        <input type="file" id="photo_upload" name="photo_upload" required><br><br>

        <!-- Declaration Section -->
        <h2>Declaration</h2>
        <label>
            <input type="checkbox" name="declaration" required> I hereby declare that the information provided is true and correct.
        </label><br><br>

        <!-- Form Buttons -->
        <button type="submit">Submit Application</button>
        <button type="reset">Reset Form</button>
    </form>
</body>
</html>
