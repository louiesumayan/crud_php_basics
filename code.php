<?php
require_once 'config.php';
session_start();

//check if the button is click
if (isset($_POST['submit'])) {
 $name = $_POST['name'];
 $email = $_POST['email'];

 //check if the input fields are empty
 if (empty($name) || empty($email)) {
  $_SESSION['error_msg'] = "All input fields are mandatory!";
  header("Location: users.php");
  exit();
 }

 //check for any duplication
 $stmt = $conn->prepare("SELECT email FROM users_table WHERE email =  ? ");

 $stmt->bind_param('s', $email);
 $stmt->execute();
 $result = $stmt->get_result();
 if ($result->num_rows > 0) {
  $_SESSION['error_msg'] = "Email is already used by other user!";
  $stmt->close(); // close after done using
  header("Location: users.php");
  exit();
 }
 $stmt->close(); // close after finished using

 //insert
 $stmt = $conn->prepare("INSERT INTO users_table (name,email) VALUES (?,?)");
 $stmt->bind_param('ss', $name, $email);
 $stmt->execute();
 $stmt->close();
 header("Location: users.php");
 exit();
}