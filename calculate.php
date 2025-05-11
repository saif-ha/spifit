<?php
// calculate.php

// Database connection
$host = 'localhost';
$dbname = 'spifit';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form data
    $age = $_POST['age'];
    $weight = $_POST['weight'];
    $size = $_POST['size'];
    $gender = $_POST['gender'];
    $activity = $_POST['activity'];

    // Perform calculation (example: BMI)
    $heightInMeters = $size / 100;
    $bmi = $weight / ($heightInMeters * $heightInMeters);

    // Determine result based on activity factor
    $result = '';
    if ($activity === 'lose') {
        $result = 'You need to lose weight.';
    } else {
        $result = 'You need to gain weight.';
    }

    // Store data in the database
    $stmt = $pdo->prepare("INSERT INTO calculations (age, weight, size, gender, activity, bmi, result) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$age, $weight, $size, $gender, $activity, $bmi, $result]);

    // Display the result
    echo "<h2>Calculation Result</h2>";
    echo "<p>Age: $age</p>";
    echo "<p>Weight: $weight kg</p>";
    echo "<p>Size: $size cm</p>";
    echo "<p>Gender: $gender</p>";
    echo "<p>Activity Factor: $activity</p>";
    echo "<p>BMI: $bmi</p>";
    echo "<p>Result: $result</p>";
} else {
    // Redirect if accessed directly
    header('Location: page_accueil.html');
    exit();
}
?> 