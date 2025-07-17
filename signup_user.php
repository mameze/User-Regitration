<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = require __DIR__ . "/db_connect.php";

$firstname = trim($_POST["firstname"] ?? '');
$lastname = trim($_POST["lastname"] ?? '');
$username = trim($_POST["username"] ?? '');
$email = trim($_POST["email"] ?? '');
$password = $_POST["password"] ?? '';
$confirm_password = $_POST["confirm_password"] ?? '';

if (!$firstname || !$username || !$email || !$password || !$confirm_password) {
    header("Location: signup.php?error=missing_fields");
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signup.php?error=invalid_email");
    exit;
}

if ($password !== $confirm_password) {
    header("Location: signup.php?error=password_mismatch");
    exit;
}

if (strlen($password) < 6) {
    header("Location: signup.php?error=weak_password");
    exit;
}

$count_result = $mysqli->query("SELECT COUNT(*) AS totall FROM users");
$count = $count_result->fetch_assoc();

if ($count['totall'] >= 10) {
    header("Location: signup.php?limit_reached=1");
    exit;
}

$check = $mysqli->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$check->bind_param("ss", $username, $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    header("Location: signup.php?error=exists");
    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $mysqli->prepare("INSERT INTO users (firstname, lastname, username, email, password) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $firstname, $lastname, $username, $email, $password_hash);

if ($stmt->execute()) {
    header("Location: signup.php?success=1");
    exit;
} else {
    header("Location: signup.php?error=server");
    exit;
}
