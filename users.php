<!doctype html>
<html lang="en">

<head>
 <!-- Required meta tags -->
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">

 <!-- Bootstrap CSS -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
  integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

 <title>User Table</title>
</head>

<body>
 <div class="container mt-5">
  <h1>User Table</h1>

  <!-- Add User Button -->
  <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>

  <!-- User Table -->
  <table id="users_table" class="table table-bordered">
   <thead>
    <tr>
     <th>#</th>
     <th>Name</th>
     <th>Email</th>
     <th>Action</th>
    </tr>
   </thead>
   <tbody id="userTableBody">
    <!-- Users will be added here dynamically -->
    <?php

    require "code.php";

    $query_user = "SELECT * FROM users_table";
    $query_run = mysqli_query($conn, $query_user);
    if (mysqli_num_rows($query_run) > 0) {
     foreach ($query_run as $users_data) {
      ?>

      <tr>
       <td> <?= $users_data['id'] ?> </td>
       <td> <?= $users_data['name'] ?> </td>
       <td> <?= $users_data['email'] ?> </td>
       <td>
        <button type="button" value="<?= $users_data['id'] ?>" class="editUserBTN btn btn-info">EDIT</button>
        <button type="button" value="<?= $users_data['id'] ?>" class="deleteUserBTN btn btn-warning">DELETE</button>
       </td>
      </tr>

      <?php
     }
    }
    ?>
   </tbody>
  </table>
 </div>

 <!-- Add User Modal -->
 <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-header">
     <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
     <div class="alert alert-warning d-none"></div>
     <form id="userFormData" method="POST">
      <div class="mb-3">
       <label for="Forname" class="form-label">Name</label>
       <input type="text" class="form-control" id="userName" name="name" required>
      </div>
      <div class="mb-3">
       <label for="email" class="form-label">Email</label>
       <input type="Foremail" class="form-control" id="userEmail" name="email" required>
      </div>
      <button type="submit" class="btn btn-primary">Add User</button>
     </form>
    </div>
   </div>
  </div>
 </div>

 <!-- Edit User Modal -->
 <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
   <div class="modal-content">
    <div class="modal-header">
     <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
     <form id="editUserForm" method="POST">
      <input type="hidden" id="editUserId" name="id">
      <div class="mb-3">
       <label for="editUserName" class="form-label">Name</label>
       <input type="text" class="form-control" id="editUserName" name="name" required>
      </div>
      <div class="mb-3">
       <label for="editUserEmail" class="form-label">Email</label>
       <input type="email" class="form-control" id="editUserEmail" name="email" required>
      </div>
      <button type="submit" class="btn btn-primary">Update User</button>
     </form>
    </div>
   </div>
  </div>
 </div>


 <!-- Bootstrap Bundle with Popper -->
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
 <!-- jquery cdn -->
 <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

 <script>
  // add
  $(document).on('submit', '#userFormData', function (e) {
   e.preventDefault();

   var formData = new FormData(this);
   formData.append('save_user', true);

   $.ajax({
    type: "POST",
    url: "code.php",
    data: formData,
    processData: false,
    contentType: false,

    success: function (response) {
     var res = $.parseJSON(response);
     if (res.status == 422) {
      $('#ErrorMessage').removeClass('d-none');
      $('#ErrorMessage').text(res.message);
     } else if (res.status == 200) {
      $('#ErrorMessage').addClass('d-none');
      $('#addUserModal').modal('hide');
      $('#userFormData')[0].reset();
      $('#users_table').load(location.href + " #users_table");
     }
    }
   });
  });

  // edit
  $(document).on('click', '.editUserBTN', function () {
   var user_id = $(this).val();

   $.ajax({
    type: "GET",
    url: "code.php?user_id=" + user_id,
    success: function (response) {
     var res = $.parseJSON(response);

     if (res.status == 422) {
      alert(res.message);
     } else if (res.status == 200) {
      $('#editUserId').val(res.data.id)
      $('#editUserName').val(res.data.name)
      $('#editUserEmail').val(res.data.email)
      $('#editUserModal').modal('show');
     }

    }
   });
  });

  //update handle
  $(document).on('submit', '#editUserForm', function (e) {
   e.preventDefault();

   var formData = new FormData(this);
   formData.append('update_user', true);

   $.ajax({
    type: "POST",
    url: "code.php",
    data: formData,
    processData: false,
    contentType: false,

    success: function (response) {
     let res = $.parseJSON(response);
     if (res.status == 200) {
      alert(res.message);
      location.reload();
     } else {
      alert(res.message);
     }
    }
   });
  });

  //delete
  $(document).on('click', '.deleteUserBTN', function () {
   var user_id = $(this).val();

   if (!confirm("Are you sure you want to delete this user?")) {
    return;
   }

   $.ajax({
    type: "POST",
    url: "code.php",
    data: { delete_user: true, id: user_id },
    success: function (response) {
     try {
      let res = JSON.parse(response);

      if (res.status == 200) {
       alert(res.message);
       location.reload();
      } else {
       alert(res.message);
      }
     } catch (e) {
      console.error("JSON Parsing Error:", e);
      console.error("Raw Response:", response);
      alert("Error: Invalid response from the server.");
     }
    },
    error: function (xhr, status, error) {
     console.error("AJAX Error:", status, error);
     alert("AJAX request failed.");
    }
   });
  });


 </script>

</body>

</html>