<?php
// Shared database connection. mysqli throws exceptions instead of failing
// silently, and utf8mb4 makes prepared statements escape any input correctly.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host="localhost";
$user="root";
$pass="";
$db="login";

try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Could not connect to the database. Check connect.php and make sure MySQL is running.");
}
