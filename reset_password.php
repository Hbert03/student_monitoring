<?php
session_start();
require 'database.php';

if (!isset($_SESSION['verified_phone'])) {
    header("Location: reset_password.php");
    exit();
}

$phone_number = $_SESSION['verified_phone'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = ? WHERE phone_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $new_password, $phone_number);
    if ($stmt->execute()) {
        $_SESSION['reset_message'] = "Password Updated Successfully.";
        // Keep the user on the same page and display the message
        header("Location: reset_password.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BNHS | Reset Password</title>
  <link rel="icon" href="img/logo.jpg">

  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
</head>
<body class="hold-transition login-page">

<div class="login-box">
  <div class="login-logo">
  </div>
  <div class="card">
    <div class="card-body login-card-body" style="border-radius:200px">
      <h4 style="color:black; font-family:'Bookman Old Style', Georgia, serif;" class="login-box-msg"><b><i>BONIFACIO NHS</i></b></h4>
      <div class="text-center">
        <img src="img/bnhs.jpg" style="width:80%; position:relatives; border-radius:10px; margin-bottom:1em">
      </div>

      <form action="reset_password.php" method="POST">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" class="form-control" name="new_password" id="new_password" placeholder="Enter new password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
        </form>
        <br>
        <a href="login.php" class="btn btn-link">Go Back to Login</a>
    </div>
  </div>
</div>

<!-- jQuery -->
<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- Toastr -->
<script src="plugins/toastr/toastr.min.js"></script>

<?php
if(isset($_SESSION['reset_message'])) {
  ?>
  <script>
    toastr.options = {
      "closeButton": true,
      "progressBar": true,
      "positionClass": "toast-top-right",
      "showDuration": "300",
      "hideDuration": "1000",
      "timeOut": "5000",
      "extendedTimeOut": "1000",
    };
    toastr.success("<?php echo $_SESSION['reset_message']; ?>");
  </script>
  <?php
  unset($_SESSION['reset_message']);
}
?>
</body>
</html>
