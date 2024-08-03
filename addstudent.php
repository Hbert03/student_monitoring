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
        <div class="card mb-3">
          <div class="card-header">
           <h4> Parent's/Guardian Information</h4>
          </div>
          <div class="card-body">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="parentname">Parent/Guardian Name</label>
                <input type="text" class="form-control" id="parentname" name="parentname" placeholder="Fullname" required>
              </div>
              <div class="form-group col-md-6">
                <label for="mobilenumber">Mobile Number</label>
                <input type="text" class="form-control" id="mobilenumber" name="mobilenumber" placeholder="Mobile Number" required>
              </div>
            
            </div>
            <div class="form-row">
            <div class="form-group col-md-6">
                <label for="email">Email (Optional)</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email">
              </div>
              <div class="form-group col-md-6">
                <label for="address">Address</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
              </div>
            </div>
          </div>
        </div>
        
        <div class="card mb-3">
          <div class="card-header">
            <h4>Student's Information</h4>
          </div>
          <div class="card-body">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="firstname">Student Firstname</label>
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Firstname" required>
              </div>
              <div class="form-group col-md-6">
                <label for="middlename">Student Middlename</label>
                <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Middlename" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="lastname">Student Lastname</label>
                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Lastname" required>
              </div>
              <div class="form-group col-md-6">
                <label for="studentmobile">Student Mobile (Optional)</label>
                <input type="text" class="form-control" id="studentmobile" name="studentmobile" placeholder="Mobile Number">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="studentaddress">Student Address</label>
                <input type="text" class="form-control" id="studentaddress" name="studentaddress" placeholder="Address" required>
              </div>
              <div class="form-group col-md-6">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status" required>
                  <option value="" disabled selected>Select your status</option>
                  <option value="single">Single</option>
                  <option value="married">Married</option>
                  <option value="divorced">Divorced</option>
                  <option value="widowed">Widowed</option>
                  <option value="separated">Separated</option>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="grade">Grade Level</label>
                <select class="form-control gradelevel" id="grade" name="grade" required>
                </select>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Save Button -->
        <div class="text-center">
          <button type="submit" class="btn btn-primary btn1">Save</button>
        </div>
      </form>
      
      <!-- QR Code Section -->
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
