<?php

$conn = mysqli_connect('localhost', 'root', '', 'crud_practice_db');

// insert
if (isset($_POST['add_user'])) {
 $user_name = mysqli_real_escape_string($conn, $_POST['name']);
 $user_email = mysqli_real_escape_string($conn, $_POST['email']);

 //if empty
 if ($user_email == NULL || $user_name == NULL) {
  $res = [
   'status' => 422,
   'message' => 'All fields are mandatory'
  ];
  echo json_encode($res);
  return false;
 }

 $query_add = "INSERT INTO users_table (name,email) VALUES('$user_name','$user_email')";
 $query_add_run = mysqli_query($conn, $query_add);

 //if success
 if ($query_add_run) {
  $res = [
   'status' => 200,
   'message' => 'Users Sucessfully added'
  ];
  echo json_encode($res);
  return false;
 } else {
  $res = [
   'status' => 500,
   'message' => 'DB_MYSQLI ENCOUNTERED ERROR'
  ];
  echo json_encode($res);
  return false;
 }

}

// edit
if (isset($_GET['user_id'])) {
 $user_id = mysqli_real_escape_string($conn, $_GET['user_id']);

 $query_get_user_data = "SELECT * FROM users_Table where id = '$user_id'";
 $query_get_user_data_run = mysqli_query($conn, $query_get_user_data);

 if (mysqli_num_rows($query_get_user_data_run) == 1) {
  $users_data = mysqli_fetch_assoc($query_get_user_data_run);

  $res = [
   'status' => 200,
   'messaage' => 'Sucessfully get the users_data',
   'data' => $users_data
  ];

  echo json_encode($res);
  return false;
 } else {
  $res = [
   'status' => 404,
   'message' => 'No users_id retrvie'
  ];

  echo json_encode($res);
  return false;
 }
}

//update handle
if (isset($_POST['update_user'])) {
 $id = mysqli_real_escape_string($conn, $_POST['id']);
 $name = mysqli_real_escape_string($conn, $_POST['name']);
 $email = mysqli_real_escape_string($conn, $_POST['email']);

 if ($id == NULL || $name == NULL || $email == NULL) {
  $res = [
   'status' => 422,
   'message' => 'All fields are mandatory'
  ];
  echo json_encode($res);
  return false;
 }

 $query_update_user_data = "UPDATE users_table SET name ='$name' , email = '$email' WHERE id = '$id' ";
 $query_update_user_data_run = mysqli_query($conn, $query_update_user_data);

 if ($query_update_user_data_run) {
  $res = [
   'status' => 200,
   'message' => 'User data updated successfully'
  ];

  echo json_encode($res);
  return false;
 } else {
  $res = [
   'status' => 500,
   'message' => 'DATABASE ERROR.'
  ];

  echo json_encode($res);
  return false;
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
