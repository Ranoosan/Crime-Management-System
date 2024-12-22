<!-- footer.php -->
<footer>
    <div class="footer-content">
        <p>&copy; <?php echo date("Y"); ?> Your Company Name. All rights reserved.</p>
        <ul>
            <li><a href="privacy.php">Privacy Policy</a></li>
            <li><a href="terms.php">Terms of Service</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
    </div>
</footer>
<style>
/* Ensure full-height layout */
html, body {
    height: 100%;
    margin: 0;
}

body {
    display: flex;
    flex-direction: column;
}

.content {
    flex: 1;
}

/* Footer styles */
footer {
    background-color: #0A2E52 ;
    color: #fff;
    padding: 20px;
    text-align: center;
}

.footer-content ul {
    list-style-type: none;
    padding: 0;
}

.footer-content ul li {
    display: inline;
    margin: 0 10px;
}

.footer-content ul li a {
    color: #fff;
    text-decoration: none;
}

.footer-content ul li a:hover {
    text-decoration: underline;
}

</style>
