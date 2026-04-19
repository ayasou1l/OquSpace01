<?php
$servername = "fdb1034.awardspace.net"; 
$username = "4752026_oquspace";
$password = "Aa12Ss23@"; 
$dbname = "4752026_oquspace"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Ошибка подключения: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
