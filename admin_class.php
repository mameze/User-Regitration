<?php
session_start();
ini_set('display_errors', 1);

class Action {
    private $db;

    public function __construct() {
        include 'db_connect.php';
        $this->db = $conn;
    }

    public function __destruct() {
        $this->db->close();
    }

    public function login() {
        extract($_POST);

        $table = $type == 1 ? 'users' : ($type == 2 ? 'staff' : 'customers');
        $login_field = $type == 1 ? 'username' : 'email';

        $stmt = $this->db->prepare("SELECT *, CONCAT(lastname, ', ', firstname) AS name FROM $table WHERE $login_field = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                foreach ($user as $key => $value) {
                    if ($key !== 'password' && !is_numeric($key)) {
                        $_SESSION['login_' . $key] = $value;
                    }
                }
                $_SESSION['login_type'] = $type;
                return 1;
            } else {
                return 3; // Incorrect password
            }
        }

        return 3; // U
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }
}
?>
