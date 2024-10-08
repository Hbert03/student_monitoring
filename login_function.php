<?php
session_start();
include('database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['user'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['teacher_id'] = $user['teacher_id'];

            if ($user['role'] == 1) {
                header("Location: t_index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $_SESSION['login'] = "Invalid password!";
            $_SESSION['login_code'] = "error";
        }
    } else {
        $_SESSION['login'] = "No user found with that email!";
        $_SESSION['login_code'] = "error";
    }

    header("Location: login.php");
    exit();
}
?>
