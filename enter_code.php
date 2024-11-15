<?php
session_start();
require 'database.php'; 

if (!isset($_SESSION['phone_number'])) {
    header("Location: login.php"); 
    exit();
}

$phone_number = $_SESSION['phone_number'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $verification_code = $_POST['verification_code'];


    $query = "SELECT reset_code, code_expiry FROM users WHERE phone_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $phone_number);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $stored_code = $user['reset_code'];
        $expiry = $user['code_expiry'];

 
        if ($verification_code == $stored_code && strtotime($expiry) > time()) {
           
            $_SESSION['verified_phone'] = $phone_number;
            header("Location: reset_password.php"); 
            exit();
        } else {
          
            $_SESSION['error_message'] = "Invalid or expired verification code.";
        }
    } else {

        $_SESSION['error_message'] = "Phone number not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BNHS | Enter Verification Code</title>
  <link rel="icon" href="img/logo.jpg">

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="plugins/fontawesome-free/css/all.min.css">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="plugins/icheck-bootstrap/icheck-bootstrap.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="dist/css/adminlte.min.css">
  <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
</head>
<body class="hold-transition login-page">

<div class="login-box">
  <div class="login-logo">
  </div>
  <!-- Enter Code Form -->
  <div class="card">
    <div class="card-body login-card-body" style="border-radius:200px">
      <h4 style="color:black; font-family:'Bookman Old Style', Georgia, serif;" class="login-box-msg"><b><i>BONIFACIO NHS</i></b></h4>
      <div class="text-center">
        <img src="img/bnhs.jpg" style="width:80%; position:relative; border-radius:10px; margin-bottom:1em">
      </div>

      <form action="enter_code.php" method="POST">
        <div class="form-group">
            <label for="verification_code">Verification Code</label>
            <input type="text" class="form-control" name="verification_code" id="verification_code" placeholder="Enter the code" required>
        </div>
        <button type="submit" class="btn btn-primary">Verify Code</button>
    </form>
    </div>
  </div>
</div>

<script src="plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="dist/js/adminlte.min.js"></script>
<!-- Toastr -->
<script src="plugins/toastr/toastr.min.js"></script>

<?php
if(isset($_SESSION['error_message'])) {
  // Display Toastr error message
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
    toastr.error("<?php echo $_SESSION['error_message']; ?>");
  </script>
  <?php

  unset($_SESSION['error_message']);
}
?>

</body>
</html>
