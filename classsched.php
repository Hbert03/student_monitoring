<?php 
include ('header.php');
?>

<!-- Modal -->
<div class="modal fade" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="viewModalLabel">Student List</h5>
      </div>
      <div class="card">
    <div class="card-body">
    <div class="table-responsive">
                  <table id="viewstudent" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Grade level</th>
                      </tr>
                    </thead>
                <tbody></tbody>
             </table>
        </div>
  </div>
  </div>
    </div>
  </div>
</div>

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
    <div class="table-responsive">
                  <table id="classSched" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Subject</th>
                        <th>Teacher</th>
                        <th>Section</th>
                        <th>School Year</th>
                        <th colspan="3" class="text-center">Action</th>
                      </tr>
                    </thead>
                <tbody></tbody>
             </table>
        </div>
  </div>
  </div>
</section>
<div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Class Section</h1>
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
    <div class="table-responsive">
                  <table id="classSec" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Section Name</th>
                        <th>School Year</th>
                        <th>Student</th>
                      </tr>
                    </thead>
                <tbody></tbody>
             </table>
        </div>
  </div>
  </div>
</section>

</div>


<!-- /.content-wrapper -->





<?php include ('footer.php'); ?>
