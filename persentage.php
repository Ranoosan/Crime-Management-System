<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "crime_management";

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch available months (distinct)
$months_query = "
    SELECT DISTINCT DATE_FORMAT(incident_date, '%Y-%m') AS month 
    FROM child_abuse_complaints 
    UNION 
    SELECT DISTINCT DATE_FORMAT(occurrence_date, '%Y-%m') AS month 
    FROM cyber_crime_complaints 
    UNION 
    SELECT DISTINCT DATE_FORMAT(last_seen_date, '%Y-%m') AS month 
    FROM missing_person_reports 
    UNION 
    SELECT DISTINCT DATE_FORMAT(occurrence_date, '%Y-%m') AS month 
    FROM robbery_complaints 
    UNION 
    SELECT DISTINCT DATE_FORMAT(occurrence_date, '%Y-%m') AS month 
    FROM sexual_abuse_reports 
    UNION 
    SELECT DISTINCT DATE_FORMAT(occurrence_date, '%Y-%m') AS month 
    FROM complaints
    ORDER BY month DESC
";

$months_result = $conn->query($months_query);
$available_months = [];

if ($months_result->num_rows > 0) {
    while ($row = $months_result->fetch_assoc()) {
        $available_months[] = $row['month'];
    }
}

// Handle the selected month via GET (default: current month)
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
$display_month = date('F Y', strtotime($selected_month));

// Query to fetch crime data for the selected month
$crime_query = "
    WITH crime_counts AS (
        SELECT 'Child Abuse' AS crime_type, COUNT(*) AS crime_count 
        FROM child_abuse_complaints 
        WHERE DATE_FORMAT(incident_date, '%Y-%m') = '$selected_month'
        UNION ALL
        SELECT 'Cyber Crime' AS crime_type, COUNT(*) AS crime_count 
        FROM cyber_crime_complaints 
        WHERE DATE_FORMAT(occurrence_date, '%Y-%m') = '$selected_month'
        UNION ALL
        SELECT 'Missing Persons' AS crime_type, COUNT(*) AS crime_count 
        FROM missing_person_reports 
        WHERE DATE_FORMAT(last_seen_date, '%Y-%m') = '$selected_month'
        UNION ALL
        SELECT 'Robbery' AS crime_type, COUNT(*) AS crime_count 
        FROM robbery_complaints 
        WHERE DATE_FORMAT(occurrence_date, '%Y-%m') = '$selected_month'
        UNION ALL
        SELECT 'Sexual Abuse' AS crime_type, COUNT(*) AS crime_count 
        FROM sexual_abuse_reports 
        WHERE DATE_FORMAT(occurrence_date, '%Y-%m') = '$selected_month'
        UNION ALL
        SELECT 'General Complaints' AS crime_type, COUNT(*) AS crime_count 
        FROM complaints 
        WHERE DATE_FORMAT(occurrence_date, '%Y-%m') = '$selected_month'
    )
    SELECT
        crime_type,
        crime_count,
        ROUND((crime_count * 100.0 / (SELECT SUM(crime_count) FROM crime_counts)), 2) AS percentage
    FROM crime_counts
";

$crime_result = $conn->query($crime_query);

$crime_types = [];
$crime_counts = [];
$crime_percentages = [];

if ($crime_result->num_rows > 0) {
    while ($row = $crime_result->fetch_assoc()) {
        $crime_types[] = $row['crime_type'];
        $crime_counts[] = $row['crime_count'];
        $crime_percentages[] = $row['percentage'];
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Crime Percentages Chart (<?php echo $display_month; ?>)</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* General styles */
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 20px;
    background-color: #f4f4f4;
    color: #333;
}

h2 {
    text-align: center;
    color: #4A4A4A;
    margin-bottom: 20px;
}

/* Form styles */
form {
    text-align: center;
    margin-bottom: 30px;
}

label {
    font-size: 18px;
    color: #555;
}

/* Dropdown styles */
select {
    padding: 10px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #fff;
    transition: border-color 0.3s;
}

select:focus {
    border-color: #007BFF;
    outline: none;
}

/* Chart styles */
canvas {
    display: block;
    margin: 0 auto;
    max-width: 100%;
    border-radius: 10px;
}

/* Responsive styles */
@media (max-width: 600px) {
    h2 {
        font-size: 24px;
    }

    select {
        font-size: 14px;
        width: 80%;
    }
}

    </style>
</head>
<body>

<h2>Crime Percentages for <?php echo $display_month; ?></h2>

<!-- Month Selection Dropdown -->
<form method="GET" action="">
    <label for="month">Select Month:</label>
    <select name="month" id="month" onchange="this.form.submit()">
        <?php foreach ($available_months as $month): ?>
            <option value="<?php echo $month; ?>" <?php echo $month === $selected_month ? 'selected' : ''; ?>>
                <?php echo date('F Y', strtotime($month)); ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<canvas id="crimePieChart" width="400" height="200"></canvas>
<canvas id="crimeLineChart" width="400" height="200"></canvas>

<script>
    var crimeTypes = <?php echo json_encode($crime_types); ?>;
    var crimePercentages = <?php echo json_encode($crime_percentages); ?>;
    
    // Pie Chart
    var ctxPie = document.getElementById('crimePieChart').getContext('2d');
    var crimePieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: crimeTypes,
            datasets: [{
                label: 'Crime Percentages',
                data: crimePercentages,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Crime Percentage Distribution for <?php echo $display_month; ?> (Pie Chart)'
                }
            }
        }
    });

    // Line Chart
    var ctxLine = document.getElementById('crimeLineChart').getContext('2d');
    var crimeLineChart = new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: crimeTypes,
            datasets: [{
                label: 'Crime Percentages',
                data: crimePercentages,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
                fill: false
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Crime Percentage Distribution for <?php echo $display_month; ?> (Line Chart)'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Percentage'
                    }
                }
            }
        }
    });
</script>

</body>
</html>
