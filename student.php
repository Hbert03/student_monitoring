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
          <h1 class="m-0">Student's</h1>
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
      <select id="sort_grade_level" class="form-control w-25 sort_grade_level"></select>
    <div class="table-responsive">
                  <table id="student" class="table table-bordered table-striped">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Mobile Number</th>
                        <th>Address</th>
                        <th>Grade Level</th>
                        <th>QR CODE</th>
                        <th colspan="2">Action</th>
                      </tr>
                    </thead>
                <tbody></tbody>
             </table>
        </div>
  </div>
</section>
</div>
<!-- /.content-wrapper -->

<?php include ('footer.php'); ?>
