<?php
session_start();

if (!isset($_SESSION['csrf_token'])) {
      $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}


require_once 'config.php';
?>
<!doctype html>
<html lang="en">

<head>
      <!-- Required meta tags -->
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />

      <!-- Bootstrap 5 CSS -->
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" />

      <title>User Table</title>
</head>

<body>
      <div class="container mt-5">
            <h1 class="text-center">User Table</h1>

            <!-- Add User Button -->
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addUserModal">
                  Add User
            </button>
            <p class="text-center bg-warning fs-4 text-danger">
                  <?= isset($_SESSION['message']) ? $_SESSION['message'] : '' ?>
                  <?php unset($_SESSION['message']); ?>
            </p>
            <?php
            // prepare and execute
            $stmt = $conn->prepare("SELECT * FROM users_table");
            // error handling
            if (!$stmt) {
                  die("connection error: " . $conn->error);
            }
            //continue the fetching
            $stmt->execute();
            $result = $stmt->get_result();

            // clean up
            $stmt->close();
            ?>
            <!-- User Table -->
            <table id="users_table" class="table table-bordered text-center">
                  <thead>
                        <tr>
                              <th>#</th>
                              <th>Names</th>
                              <th>Emails</th>
                              <th>Actions</th>
                        </tr>
                  </thead>
                  <tbody id="userTableBody">
                        <!-- Example static row; you will probably generate these dynamically -->
                        <!-- check if the $result is empty -->
                        <?php if ($result->num_rows === 0): ?>
                              <tr>
                                    <td colspan="4" class="text-center">No users found.</td>
                              </tr>
                        <?php else: ?>
                              <!-- if not proceed in looping -->
                              <?php foreach ($result as $data_result): ?>
                                    <tr>
                                          <td><?= htmlspecialchars($data_result['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                          <td><?= htmlspecialchars($data_result['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                          <td><?= htmlspecialchars($data_result['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                          <td>
                                                <button type="button"
                                                      value="<?= htmlspecialchars($data_result['id'], ENT_QUOTES, 'UTF-8'); ?>"
                                                      class="editUserBTN btn btn-info">
                                                      EDIT
                                                </button>
                                                <form method="POST" action="code.php" style="display:inline;">
                                                      <!-- CSRF Token (Generated in PHP) -->
                                                      <input type="hidden" name="csrf_token"
                                                            value="<?= $_SESSION['csrf_token']; ?>">

                                                      <input type="hidden" name="delete"
                                                            value="<?= htmlspecialchars($data_result['id'], ENT_QUOTES, 'UTF-8'); ?>" />

                                                      <button type="submit" name="submit_delete" class="btn btn-warning"
                                                            onClick="return confirm('Are you sure you want to delete this post?');">
                                                            DELETE
                                                      </button>
                                                </form>
                                          </td>
                                    </tr>
                              <?php endforeach; ?>
                        <?php endif; ?>
                  </tbody>
            </table>
      </div>

      <!-- Add User Modal -->
      <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                  <div class="modal-content">
                        <div class="modal-header">
                              <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                              <div id="errorMessage" class="alert alert-warning d-none"></div>
                              <form action="code.php" id="userFormData" method="POST">
                                    <div class="mb-3">
                                          <label for="userName" class="form-label">Name</label>
                                          <input type="text" class="form-control" id="userName" name="name" />
                                    </div>
                                    <div class="mb-3">
                                          <label for="userEmail" class="form-label">Email</label>
                                          <input type="email" class="form-control" id="userEmail" name="email" />
                                    </div>
                                    <button name="submit" type="submit" class="btn btn-primary">Add User</button>
                              </form>
                        </div>
                  </div>
            </div>
      </div>

      <!-- Edit User Modal -->
      <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel">
            <div class="modal-dialog">
                  <div class="modal-content">
                        <div class="modal-header">
                              <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                              <form action="code.php" id="editUserForm" method="POST">
                                    <input type="hidden" id="editUserId" name="id" />
                                    <div class="mb-3">
                                          <label for="editUserName" class="form-label">Name</label>
                                          <input name="name" type="text" class="form-control" id="editUserName"
                                                required />
                                    </div>
                                    <div class="mb-3">
                                          <label for="editUserEmail" class="form-label">Email</label>
                                          <input name="email" type="email" class="form-control" id="editUserEmail"
                                                required />
                                    </div>
                                    <button name="update_user" type="submit" class="btn btn-primary">Update
                                          User</button>
                              </form>
                        </div>
                  </div>
            </div>
      </div>

      <!-- Bootstrap Bundle (includes Popper) -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

      <script>
            document.addEventListener("DOMContentLoaded", function () {
                  // Grab the Edit Modal element and prepare a Bootstrap Modal instance
                  const editModalEl = document.getElementById("editUserModal");
                  const bsEditModal = new bootstrap.Modal(editModalEl);

                  // Whenever someone clicks an .editUserBTN, populate & show the modal
                  document.querySelectorAll(".editUserBTN").forEach(function (btn) {
                        btn.addEventListener("click", function () {
                              // 1) Get the user ID from the button's value
                              const userId = this.value;

                              // 2) Find the <tr> that contains this button
                              const row = this.closest("tr");
                              if (!row) return;

                              // 3) Extract the <td> text for name & email
                              //    (Assumes your columns are always: 0=id, 1=name, 2=email)
                              const name = row.children[1].innerText.trim();
                              const email = row.children[2].innerText.trim();

                              // 4) Fill in the hidden ID + input fields
                              document.getElementById("editUserId").value = userId;
                              document.getElementById("editUserName").value = name;
                              document.getElementById("editUserEmail").value = email;

                              // 5) Finally, show the modal
                              bsEditModal.show();
                        });
                  });
                  document
                        .getElementById("editUserForm")
                        .addEventListener("submit", function (e) {
                              bsEditModal.hide();
                        });
            });
      </script>
</body>

</html>