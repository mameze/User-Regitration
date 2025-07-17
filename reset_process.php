<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mysqli = require __DIR__ . "/db_connect.php";

$token = $_GET["token"] ?? null;

if (!$token) {
    die("No reset token provided.");
}

$token_hash = hash("sha256", $token);

$sql = "SELECT id, reset_token_expires_at FROM users WHERE reset_token_hash = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $token_hash);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    die("Invalid or expired reset token.");
}

if (strtotime($user["reset_token_expires_at"]) < time()) {
    die("Token has expired.");
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"];
    $confirm = $_POST["confirm_password"];

    if (empty($password) || empty($confirm)) {
        $errors[] = "All fields are required.";
    } elseif ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $update = $mysqli->prepare("UPDATE users SET password = ?, reset_token_hash = NULL, reset_token_expires_at = NULL WHERE id = ?");
        $update->bind_param("si", $hashed_password, $user["id"]);
        $update->execute();

        $_SESSION['success_message'] = "✅ Password has been reset successfully.";
        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <style>
        body {
            font-family: sans-serif;
            background: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px #ccc;
            width: 300px;
        }
        h2 {
            text-align: center;
        }
        input[type=password], button {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border: 1px solid #ccc;
            border-radius: 8px;
        }
        button {
            background-color: #007BFF;
            color: white;
            border: none;
            cursor: pointer;
        }
        .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
        .password-toggle {
            position: relative;
        }
        .password-toggle input {
            padding-right: 35px;
        }
        .password-toggle span {
            position: absolute;
            top: 50%;
            right: 10px;
            cursor: pointer;
            transform: translateY(-50%);
            color: #888;
        }
    </style>
</head>
<body>

<form method="post">
    <h2>Set New Password</h2>

    <?php if (!empty($errors)): ?>
        <div class="error"><?php echo implode("<br>", $errors); ?></div>
    <?php endif; ?>

    <div class="password-toggle">
        <input type="password" name="password" id="password" placeholder="New password" required>
        <span onclick="togglePassword('password')">👁</span>
    </div>

    <div class="password-toggle">
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
        <span onclick="togglePassword('confirm_password')">👁</span>
    </div>

    <button type="submit">Reset Password</button>
</form>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}
</script>

</body>
</html>
