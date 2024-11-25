<?php

// To handle session variables on this page
session_start();

// Including database connection from db.php
require_once("db.php");

// If the login button was clicked
if (isset($_POST)) {

    // Escape special characters in the input
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Encrypt the password
    $password = base64_encode(strrev(md5($password)));

    // SQL query to check user login
    $sql = "SELECT id_user, firstname, lastname, email FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    // If matching user found
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Set session variables
        $_SESSION['name'] = $row['firstname'] . " " . $row['lastname'];
        $_SESSION['id_user'] = $row['id_user'];

        // Redirect to the original location or the user dashboard
        if (isset($_SESSION['callFrom'])) {
            $location = $_SESSION['callFrom'];
            unset($_SESSION['callFrom']);
            header("Location: " . $location);
        } else {
            header("Location: user/index.php");
        }
        exit();
    } else {
        // If no match found, set login error and redirect back to login
        $_SESSION['loginError'] = "Invalid Email/Password!";
        header("Location: login-candidates.php");
        exit();
    }

    // Close database connection
    $conn->close();

} else {
    // Redirect to login page if login button was not clicked
    header("Location: login-candidates.php");
    exit();
}
