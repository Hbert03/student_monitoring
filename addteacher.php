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
          <h1 class="m-0">Add Teacher</h1>
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
      <form id="addteacherForm">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="firstname">Firstname <span style="color:red">*</span></label>
            <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Firstname" required>
          </div>
          <div class="form-group col-md-6">
            <label for="middlename">Middlename <span style="color:red">*</span></label>
            <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Middlename" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="lastname">Lastname <span style="color:red">*</span></label>
            <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Lastname" required>
          </div>
          <div class="form-group col-md-6">
            <label for="address">Address <span style="color:red">*</span></label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="mobile_num">Mobile Number <span style="color:red">*</span></label>
            <input type="text" class="form-control" id="mobile_num" name="mobile_num" placeholder="Mobile Number" required>
          </div>
          <div class="form-group col-md-6">
          <label for="status">Status <span style="color:red">*</span></label>
             <select class="form-control" id="status" name="status" required>
                  <option value="" disabled selected>Select status</option>
                  <option value="single">Single</option>
                  <option value="married">Married</option>
                  <option value="divorced">Divorced</option>
                  <option value="widowed">Widowed</option>
                  <option value="separated">Separated</option>
             </select>
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
