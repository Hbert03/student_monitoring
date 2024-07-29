<?php 
include ('header.php');
?>

<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <!-- Brand Logo -->
  <a href="#" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">Admin</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="img/admin.png" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">Admin</a>
        </div>
      </div>

    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
             with font-awesome or any other icon font library -->
        <li class="nav-item menu-open">
          <a href="index.php" class="nav-link ">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>
              Dashboard
            </p>
          </a>
        </li>
        <li class="nav-item menu-open">
          <a href="#" class="nav-link active">
            <i class="fas fa-user-plus"></i>
            <p>
              Add Student
            </p>
          </a>
        </li>
      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Add Student</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
  <section class="content">
  <div class="card">
    <div class="card-body">
      <form id="addStudentForm">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="parentname">Parent Name</label>
            <input type="text" class="form-control" id="parentname" name="parentname" placeholder="Fullname" required>
          </div>
          <div class="form-group col-md-6">
            <label for="mobilenumber">Mobile Number</label>
            <input type="text" class="form-control" id="mobilenumber" name="mobilenumber" placeholder="Mobile Number" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="address">Address</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
          </div>
          <div class="form-group col-md-6">
            <label for="firstname">Student Firstname</label>
            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Firstnamae" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="middlename">Student Middlename</label>
            <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Middlename" required>
          </div>
          <div class="form-group col-md-6">
            <label for="lastname"> Student Lastname</label>
            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Lastname" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="studentmobile">Student Mobile</label>
            <input type="text" class="form-control" id="studentmobile" name="studentmobile" placeholder="Mobile Number" required>
          </div>
          <div class="form-group col-md-6">
            <label for="studentaddress">Student Address</label>
            <input type="text" class="form-control" id="studentaddress" name="studentaddress" placeholder="Address" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="status">Status</label>
            <input type="text" class="form-control" id="status" name="status" placeholder="" required>
          </div>
          <div class="form-group col-md-6">
            <label for="grade">Grade Level</label>
            <select class="form-control gradelevel" id="grade" name="grade" required>
            </select>
          </div>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-primary btn1">Save</button>
        </div>
      </form>
      <div id="qrCodeContainer" class="text-center mt-3"></div>
      <div class="text-center mt-3">
        <a id="downloadLink" href="#" download="qrcode.png" style="display:none;" class="btn btn-success">Download QR Code</a>
      </div>
    </div>
  </div>
</section>
</div>
<!-- /.content-wrapper -->

<?php include ('footer.php'); ?>
