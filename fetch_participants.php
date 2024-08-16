<?php
// Database configuration
$host = 'localhost';
$dbname = 'zoom_participants';
$user = 'root';
$pass = '';

// Create a database connection
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Fetch participants from the database
$stmt = $pdo->query("SELECT participant_name FROM participants WHERE status = 'joined'");
$participants = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Return the participants as JSON
header('Content-Type: application/json');
echo json_encode($participants);
?>
