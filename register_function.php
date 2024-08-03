<?php
session_start();
include('database.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $role = $_POST['role'];

   
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register'] = "Email already exists!";
        $_SESSION['register_code'] = "error";
        header("Location: register.php");
        exit();
    }

    // Insert new user
    $query = "INSERT INTO users (email, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $email, $password, $role);

    if ($stmt->execute()) {
        $_SESSION['register'] = "Registration successful!";
        $_SESSION['register_code'] = "success";
    } else {
        $_SESSION['register'] = "Registration failed!";
        $_SESSION['register_code'] = "error";
    }

    header("Location: register.php");
    exit();
}
?>
