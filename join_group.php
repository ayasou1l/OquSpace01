<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$group_id = $_GET['group_id'];

$check = $conn->query("
    SELECT * FROM group_members 
    WHERE user_id=$user_id AND group_id=$group_id
");

if ($check->num_rows == 0) {
    $conn->query("
        INSERT INTO group_members (user_id, group_id)
        VALUES ($user_id, $group_id)
    ");
}

header("Location: group.php?id=$group_id");
exit;
?>
