<!-- Updated Navigation Bar -->
<style>
    /* General styling for the navigation bar */
    nav {
        background-color: #0A2E52; /* Using your logo color */
        padding: 25px 30px; /* Increased padding for a larger nav */
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        font-size: 18px; /* Slightly increased font size */
    }

    /* Logo Styling */
    .logo {
        height: 80px; /* Increased height for zoom effect */
        margin-right: 30px; /* Adjusted for better spacing */
    }

    .nav-left {
        display: flex;
        align-items: center;
    }

    .nav-left a.logo {
        display: flex;
        align-items: center;
        padding-right: 30px; /* Increased space between logo and nav items */
    }

    /* Left-side navigation list */
    .nav-left ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
    }

    .nav-left ul li {
        margin-right: 30px; /* Increased spacing between items */
        position: relative;
    }

    .nav-left ul li a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        padding: 15px 20px; /* Increased padding for larger click area */
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    /* Hover effect for menu items */
    .nav-left ul li a:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Submenu dropdown styling */
    .nav-left ul li ul {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        background-color: #1C3C5D;
        padding: 0;
        list-style: none;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
    }

    /* Show submenu on hover */
    .nav-left ul li:hover ul {
        display: block;
    }

    .nav-left ul li ul li {
        width: 220px; /* Increased width for submenu */
    }

    .nav-left ul li ul li a {
        padding: 12px;
        display: block;
        color: white;
        font-weight: normal;
        border-radius: 5px;
    }

    /* Hover effect for submenu */
    .nav-left ul li ul li a:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    /* Right-side profile and login/logout buttons */
    .nav-right ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
    }

    .nav-right ul li {
        margin-right: 30px; /* Increased spacing between profile and buttons */
    }

    .nav-right ul li a {
        color: white;
        text-decoration: none;
        font-weight: bold;
        padding: 15px 20px; /* Increased padding */
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .nav-right ul li a:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    /* Login and Logout button styling */
    .login-button, .logout-button {
        background-color: #28a745;
        color: white;
        padding: 15px 25px; /* Increased padding for larger buttons */
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-size: 18px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .login-button:hover {
        background-color: #218838;
    }

    .logout-button {
        background-color: #dc3545;
    }

    .logout-button:hover {
        background-color: #c82333;
    }
</style>

<nav>
    <div class="nav-left">
        <ul>
            
            <li><a href="index.php">Home</a></li>
            <li>
                <a href="#">Police Complaints</a>
                <ul>
                    <li><a href="assault.php">Assault</a></li>
                    <li><a href="child_abuse.php">Child Abuse</a></li>
                    <li><a href="sexual_abuse.php">Sexual Abuse</a></li>
                    <li><a href="cybercrime.php">Cybercrime</a></li>
                    <li><a href="robbery.php">Robbery</a></li>
                    <li><a href="missing.php">Missing Persons</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Online Services</a>
                <ul>
                    <li><a href="police_clearance.php">Police Clearance</a></li>
                    <li><a href="fine_payment.php">Fine Payment</a></li>
                    <li><a href="vacancies.php">Vacancies</a></li>
                    <li><a href="press.php">Press</a></li>
                </ul>
            </li>
            <a href="index.php">
                <img src="uploads/logo.png" alt="Logo" class="logo">
            </a>
            <li>

                <a href="#">Articles</a>
                <ul>
                    <li><a href="law_education.php">Law Education</a></li>
                    <li><a href="history.php">History</a></li>
                    <li><a href="social_experiments.php">Social Experiments</a></li>
                </ul>
            </li>
            <li><a href="lawyer_consultant.php">Lawyer Consultant</a></li>
            <li>
                <a href="#">More</a>
                <ul>
                    <li><a href="donate.php">Donate</a></li>
                    <li><a href="staff_details.php">Staff Details</a></li>
                </ul>
            </li>
        </ul>
    </div>
    <div class="nav-right">
        <ul>
            <li><a href="profile.php">My Profile</a></li>
        </ul>
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- User is logged in -->
            <a href="logout.php" class="logout-button">Logout</a>
        <?php else: ?>
            <!-- User is not logged in -->
            <a href="login.php" class="login-button">Login</a>
        <?php endif; ?>
    </div>
</nav>
