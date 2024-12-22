<style>
/* General navigation bar container */
nav {
    font-family: Arial, sans-serif;
}

/* Top part of the navigation bar */
.top-nav {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #ffffff;
    padding: 5px 20px; /* Reduce padding to make the section smaller */
    border-bottom: 2px solid #000000;
}

/* Left menu with logo and title */
.left-menu {
    display: flex;
    align-items: center;
}

.left-menu h1 {
    font-size: 20px; /* Adjust the font size to reduce heading size */
    font-weight: bold;
    margin-left: 10px;
    color: #000;
}

/* Menu icon (hamburger menu) */
.menu-icon {
    font-size: 20px; /* Adjust the font size of the menu icon */
    margin-right: 10px; /* Reduce margin */
    cursor: pointer;
}

/* Right side with search and Chief of Police text */
.right-menu {
    display: flex;
    align-items: center;
}

.right-menu span {
    font-size: 14px; /* Adjust font size */
    margin-right: 10px; /* Reduce margin */
}

/* Search bar styling */
.search-bar {
    display: flex;
    align-items: center;
}

.search-bar input {
    border: 1px solid #ddd;
    padding: 3px 8px; /* Reduce padding in the search bar */
    border-radius: 3px;
    font-size: 12px; /* Reduce font size in the search bar */
}

.search-button img {
    width: 18px; /* Slightly smaller search icon */
    height: 18px;
}

/* Logo in the middle */
.logo img {
    height: 70px; /* Adjust the logo size if needed */
}

/* Bottom navigation links */
.bottom-nav {
    background-color: #000000;
    padding: 8px 0; /* Reduce padding */
}

.bottom-nav ul {
    list-style: none;
    display: flex;
    justify-content: center;
    margin: 0;
    padding: 0;
}

.bottom-nav ul li {
    margin: 0 15px; /* Adjust link spacing */
}

.bottom-nav ul li a {
    color: #ffffff;
    text-decoration: none;
    font-weight: bold;
    padding: 5px 10px;
    transition: color 0.3s ease;
}

.bottom-nav ul li a:hover {
    color: #ffcc00; /* Hover color for links */
}

</style><nav>
    <div class="top-nav">
        <div class="left-menu">
            <span class="menu-icon">&#9776; Menu</span>
            <h1>Toronto Police Service</h1>
        </div>
        
        <!-- Logo in the middle -->
        <div class="logo">
            <img src="uploads/toronto-police-service-logo.png" alt="Logo">
        </div>
        
        <div class="right-menu">
            <span>Chief of Police</span>
            <div class="search-bar">
                <input type="text" placeholder="Search">
                <button class="search-button">
                    <img src="search-icon.png" alt="Search Icon">
                </button>
            </div>
        </div>
    </div>
    
    <div class="bottom-nav">
        <ul>
            <li><a href="#">About TPS</a></li>
            <li><a href="#">Data & Maps</a></li>
            <li><a href="#">My Neighbourhood</a></li>
            <li><a href="#">Media Centre</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Careers</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </div>
</nav>
