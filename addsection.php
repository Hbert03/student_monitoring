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
          <h1 class="m-0">Add Section</h1>
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
    <div class="card-header">
      <button style="margin-left:1em" class="btn btn-primary float-right add_school_year"><span><i class="nav-icon fas fa-plus"></i></span>ADD SCHOOL YEAR</button>
      <!-- <button class="btn btn-primary float-right add_subject"><span><i class="nav-icon fas fa-plus"></i></span>ADD SUBJECT</button> -->
    </div>
    <div class="card-body">
      <form id="addSectionForm">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="fullname">Section Name</label>
            <input type="text" class="form-control"  name="section" placeholder="Section Name" required>
          </div>
          <div class="form-group col-md-6">
            <label for="gradelevel">Grade Level</label>
            <select class="form-control grade_level1" name="gradelevel" required></select>
          </div>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-primary btn3">Save</button>
        </div>
      </form>
  </div>
</section>
<!-- 
<div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Add Class Schedule</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
          </ol>
        </div>
      </div>
    </div>
  </div>

<section class="content">
  <div class="card">
    <div class="card-body">
      <form id="addclassScheduleForm">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="fullname">Subject</label>
            <select class="form-control subject" name="subject" required></select>
          </div>
          <div class="form-group col-md-6">
            <label for="address">Teacher</label>
            <select class="form-control teacher" name="teacher" required></select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="fullname">Section</label>
            <select class="form-control section" name="section" required></select>
          </div>
          <div class="form-group col-md-6">
            <label for="address">School Year</label>
            <select class="form-control school_year" name="school_year" required></select>
          </div>
        </div>
        <div class="text-center">
          <button type="submit" class="btn btn-primary btn4">Save</button>
        </div>
      </form>
  </div>
</section> -->
</div>





<!-- /.content-wrapper -->

<?php include ('footer.php'); ?>
