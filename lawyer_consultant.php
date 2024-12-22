<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lawyer Consultant</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
</head>
<body>
    <div class="content">
        <h1>Lawyer Consultant</h1>
        <p>Welcome to our Lawyer Consultant services page. Here, you can find professional legal assistance for various issues, including criminal defense, civil litigation, family law, and more. Our experienced consultants are here to provide expert advice and support tailored to your needs.</p>
        
        <h2>Contact Us</h2>
        <form method="POST" action="submit_contact.php">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter your name" required>
            
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
            
            <label for="message">Message:</label>
            <textarea id="message" name="message" rows="5" placeholder="Enter your message" required></textarea>
            
            <button type="submit">Send Message</button>
        </form>
    </div>

    <?php
    include 'footer.php'; // Include footer
    ?>
</body>
</html>
