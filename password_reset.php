<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php'; 


error_reporting(E_ALL);
ini_set('display_errors', 1);

$email = $_POST["email"] ?? null;
if (!$email) {
    die("Please enter your email address.");
}

$token = bin2hex(random_bytes(16));
$token_hash = hash("sha256", $token);
$expiry = date("Y-m-d H:i:s", time() + 60 * 30);

$mysqli = require __DIR__ . "/db_connect.php";
if (!($mysqli instanceof mysqli)) {
    die("Database connection failed.");
}

$sql = "UPDATE users
        SET reset_token_hash = ?,
            reset_token_expires_at = ?
        WHERE email = ?";
$stmt = $mysqli->prepare($sql);
if (!$stmt) {
    die("SQL error: " . $mysqli->error);
}
$stmt->bind_param("sss", $token_hash, $expiry, $email);
$stmt->execute();

echo "If this email is registered, a reset link has been sent.";

if ($stmt->affected_rows > 0) {
    $resetLink = "http://localhost/Culture/reset_process.php?token=$token";

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $_ENV['MAIL_USERNAME'];
        $mail->Password = $_ENV['MAIL_PASSWORD'];    
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('your_email@gmail.com', 'Support Team');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Reset your password';
        $mail->Body = "Click the link to reset your password:<br><a href='$resetLink'>$resetLink</a>";

        $mail->send();
        echo "<br>Email sent successfully.";
    } catch (Exception $e) {
        echo "<br>Mailer Error: " . $mail->ErrorInfo;
    }
}
