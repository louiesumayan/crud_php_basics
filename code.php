<?php
require_once 'config.php';
session_start();



//check if the button is click for add
if (isset($_POST['submit'])) {
 $name = $_POST['name'];
 $email = $_POST['email'];

 //check if the input fields are empty
 if (empty($name) || empty($email)) {
  $_SESSION['message'] = "All input fields are mandatory!";
  header("Location: users.php");
  exit();
 }

 //check for any duplication
 $stmt = $conn->prepare("SELECT email FROM users_table WHERE email =  ? ");

 $stmt->bind_param('s', $email);
 $stmt->execute();
 $result = $stmt->get_result();
 if ($result->num_rows > 0) {
  $_SESSION['message'] = "Email is already used by other user!";
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

//check if the button for update is clicked
if (isset($_POST['update_user'])) {
 $id = $_POST['id'];
 $name = $_POST['name'];
 $email = $_POST['email'];

 //check if the input fields are empty
 if (empty($name) || empty($email)) {
  $_SESSION['message'] = "All input fields are mandatory!";
  header("Location: users.php");
  exit();
 }

 //check for any duplication, excluded the current users email
 $stmt = $conn->prepare("SELECT email FROM users_table WHERE email =  ? AND id != ?");
 $stmt->bind_param('si', $email, $id);
 $stmt->execute();
 $result = $stmt->get_result();
 if ($result->num_rows > 0) {
  $_SESSION['message'] = "Email is already used by other user!";
  $stmt->close(); // close after done using
  header("Location: users.php");
  exit();
 }
 $stmt->close(); // close after finished using

 $stmt = $conn->prepare("UPDATE users_table SET name = ?, email = ? WHERE id = ? ");
 $stmt->bind_param('ssi', $name, $email, $id);
 $stmt->execute();
 $stmt->close();

 $_SESSION['message'] = "User updated sucessfully!";
 header("Location: users.php");
 exit();
}

// Check if the delete request is sent
if (isset($_POST['submit_delete'])) {
 // Validate CSRF token
 if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
  die('CSRF token validation failed!');
 }

 // Get the menu ID to delete
 $id = $_POST['delete'];

 // Proceed with the deletion process
 if (isset($id) && !empty($id)) {
  // Prepare your query to delete the menu item
  $stmt = $conn->prepare("DELETE FROM users_table WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();

  // Check if the deletion was successful
  if ($stmt->affected_rows > 0) {
   $_SESSION['message'] = "Users deleted successfully!";
  } else {
   $_SESSION['message'] = "Failed to delete Users.";
  }

  $stmt->close();
 } else {
  $_SESSION['message'] = "Invalid Users ID.";
 }

 // Redirect back to the products page
 header("Location: users.php");
 exit();
}