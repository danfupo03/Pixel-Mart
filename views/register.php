<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
  $state = 'active';

  $conn->begin_transaction();

  try {
    $stmt = $conn->prepare('INSERT INTO users (username, password, state) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $username, $password, $state);
    $stmt->execute();

    $uid = $conn->insert_id;

    $rid = 2;
    $roleStmt = $conn->prepare('INSERT INTO user_roles (uid, rid) VALUES (?, ?)');
    $roleStmt->bind_param('ii', $uid, $rid);
    $roleStmt->execute();

    $conn->commit();

    header('Location: login');
    exit();
  } catch (Exception $e) {
    $conn->rollback();
    echo "Error creating user: " . $e->getMessage();
  }
}
?>


<!DOCTYPE html>
<html lang="en">

<?php
include 'includes/head.php';
?>

<body class="body">
  <div class="mt-3">
    <a class="button is-dark ml-1" onclick="darkMode()" id="dark-mode"><i class="fa-solid fa-moon"></i></a>
  </div>
  <section>
    <div class="container">
      <div class="form mt-5">
        <h1 class="title is-3">Join Paw-Some &#128054;</h1>
        <p class="content">Create an account to start exploring!</p>
        <form action="" method="POST" id="registerForm">
          <label for="username">Username</label>
          <input type="text" placeholder="JohnDoe03" id="username" name="username" required />
          <span id="usernameError" style="color: red; display: none">The username must contain at least 5 characters and at least one
            capital letter and one lower case letter</span>

          <label for="password">Password</label>
          <input type="text" placeholder="password" id="password" name="password" required />
          <span id="passwordError">The password must be at least 10 characters long</span>

          <label for="confirmPassword">Confirm Password</label>
          <input type="text" required id="confirmPassword" />
          <span id="confirmPasswordError" style="color: red; display: none">The password does not match</span>

          <button class="button is-secondary" type="submit">
            Create account
          </button>
        </form>

        <div class="mb-3">
          <h1 class="title is-5">Already an account?</h1>
          <a class="button is-dark" href="login">Login</a>
        </div>
      </div>
    </div>
  </section>
  <script src="assets/js/register.js"></script>
</body>

</html>