<!-- sidebar.php -->
<div class="sidebar">
    <h1>Admin Dashboard</h1>
     <!-- Logout Button -->
     <a href="admin_dashboard.php" class="dashboard-button">Main</a>
    <!-- User Management Button -->
    <a href="manage_users.php" class="user-management-button">User Management</a>
    <!-- manage the online cases -->
    <a href="manage_complaints.php" class="complaints-button">Assault</a>
    <a href="manage_cyberCrime.php" class="complaints-button">Cyber crime</a>
    <a href="manage_childAbuse.php" class="complaints-button">Child abuse</a>
    <a href="manage_sexualAbuse.php" class="complaints-button">Sexual abuse</a>
    <a href="manage_robbery.php" class="complaints-button">Robbery</a>
    <a href="manage_missingPerson.php" class="complaints-button">Missing persons</a>
    <a href="manage_clearance.php" class="complaints-button">Clearance</a>
    <a href="admin_view_applications.php" class="complaints-button">Applicants</a>
    <a href="add_vacancy.php" class="complaints-button">Add vacancies</a>
    <a href="entryCases.php" class="complaints-button">Cases</a>
      <!-- Manage Clearance Button -->
      <a href="manage_clearance.php" class="manage-clearance-btn">Manage Clearance Requests</a>
        <a href="entryCases.php" class="manage-clearance-btn">Entry case </a>
        <a href="viewAddedCases.php" class="manage-clearance-btn">View cases </a>
        



    <!-- Logout Button -->
    <a href="ad_logout.php" class="logout-button">Logout</a>
</div>

<style>
    /* Sidebar styling */
    .sidebar {
        width: 250px;
        background-color: #007bff;
        padding: 20px;
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        display: flex;
        flex-direction: column;
    }

    .sidebar h1 {
        color: white;
        margin-bottom: 30px;
        font-size: 24px;
    }

    .sidebar a {
        background-color: #fff;
        color: #007bff;
        padding: 10px 20px;
        border-radius: 5px;
        text-decoration: none;
        text-align: center;
        margin-bottom: 10px;
        transition: background-color 0.3s, color 0.3s;
    }

    .sidebar a:hover {
        background-color: #0056b3;
        color: #fff;
    }
</style>
