<?php 
include ('header.php');
?>



<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Class Schedule</h1>
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
            <label for="fullname">Section Name</label>
            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Section Name" required>
          </div>
          <div class="form-group col-md-6">
            <label for="address">Grade Level</label>
            <select class="form-control"></select>
          </div>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-primary btn2">Save</button>
        </div>
      </form>

  </div>
</section>
</div>
<!-- /.content-wrapper -->

<?php include ('footer.php'); ?>
