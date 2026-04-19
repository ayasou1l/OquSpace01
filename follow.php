<?php
session_start();
include 'db.php';

$follower = $_SESSION['user_id'];
$following = $_GET['id'];

$conn->query("
    INSERT INTO follows (follower_id, following_id)
    VALUES ($follower, $following)
");

header("Location: profile.php?id=$following");
exit;
?>
