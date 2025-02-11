<?php

$conn = mysqli_connect('localhost', 'root', '', 'crud_practice_db');

// insert
if (isset($_POST['save_user'])) {
 $name = mysqli_real_escape_string($conn, $_POST['name']);
 $email = mysqli_real_escape_string($conn, $_POST['email']);

 if ($name == NULL || $email == NULL) {
  $res = [
   'status' => 422,
   'message' => 'All fields are mandatory'
  ];

  echo json_encode($res);
  return false;
 }

 $query = "INSERT INTO users_table (name,	email) VALUES ('$name','$email')";
 $query_run = mysqli_query($conn, $query);

 if ($query_run) {
  $res = [
   'status' => 200,
   'message' => 'Users Data Added Succesfully'
  ];

  echo json_encode($res);
  return false;
 } else {
  $res = [
   'status' => 500,
   'message' => 'Users Data Error'
  ];

  echo json_encode($res);
  return false;
 }
}

// edit

if (isset($_GET['user_id'])) {
 $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);
 $query_edit = "SELECT * FROM users_table WHERE id = '$user_id'";
 $query_run = mysqli_query($conn, $query_edit);

 if (mysqli_num_rows($query_run) == 1) {
  $users = mysqli_fetch_assoc($query_run);
  $res = [
   'status' => 200,
   'message' => 'User_id found',
   'data' => $users
  ];
  echo json_encode($res);
  return false;
 } else {
  $res = [
   'status' => 404,
   'message' => 'No user_id found'
  ];

  echo json_encode($res);
  return false;
 }
}

// Update User
if (isset($_POST['update_user'])) {
 $id = mysqli_real_escape_string($conn, $_POST['id']);
 $name = mysqli_real_escape_string($conn, $_POST['name']);
 $email = mysqli_real_escape_string($conn, $_POST['email']);

 if (empty($id) || empty($name) || empty($email)) {
  echo json_encode(["status" => 422, "message" => "All fields are required"]);
  exit;
 }

 $query = "UPDATE users_table SET name='$name', email='$email' WHERE id='$id'";
 $query_run = mysqli_query($conn, $query);

 if ($query_run) {
  echo json_encode(["status" => 200, "message" => "User Updated Successfully"]);
 } else {
  echo json_encode(["status" => 500, "message" => "Database Update Error: " . mysqli_error($conn)]);
 }
}

// delete

if (isset($_POST['delete_user'])) {
 $id = mysqli_real_escape_string($conn, $_POST['id']);

 if (empty($id)) {
  echo json_encode(["status" => 422, "message" => "Invalid User ID"]);
  exit;
 }

 $query = "DELETE FROM users_table WHERE id='$id'";
 $query_run = mysqli_query($conn, $query);

 if ($query_run) {
  echo json_encode(["status" => 200, "message" => "User Deleted Successfully"]);
 } else {
  echo json_encode(["status" => 500, "message" => "Database Delete Error: " . mysqli_error($conn)]);
 }
 exit;
}
