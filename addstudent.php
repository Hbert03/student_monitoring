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
            <div class="row">
              <div class="col">
            <h4>Student's Information</h4>
            </div>
            <div class="col">
            <button class="btn btn-primary" data-toggle="modal" data-target="#bulkenrollment">Bulk Enrollment</button>
            </div>
            </div>
          </div>
          <div class="card-body">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="firstname">Student Firstname <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="firstname" name="firstname" placeholder="Firstname" required>
              </div>
              <div class="form-group col-md-6">
                <label for="middlename">Student Middlename <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="middlename" name="middlename" placeholder="Middlename" required>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="lastname">Student Lastname <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="lastname" name="lastname" placeholder="Lastname" required>
              </div>
              <div class="form-group col-md-6">
                <label for="studentmobile">Student Mobile (Optional)</label>
                <input type="text" class="form-control" id="studentmobile" name="studentmobile" placeholder="Mobile Number">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="studentaddress">Student Address <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="studentaddress" name="studentaddress" placeholder="Address" required>
              </div>
              <div class="form-group col-md-6">
                <label for="status">Status <span style="color:red">*</span></label>
                <select class="form-control " id="status" name="status" required>
                  <option value="" disabled selected>Select status</option>
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
                <label for="grade">Gender</label>
                <select class="form-control gender" id="gender" name="gender" required>
                </select>
                
              </div>
              <div class="form-group col-md-6">
                <label for="grade">Grade Level</label>
                <select class="form-control gradelevel" id="grade" name="grade" required>
                </select>
                
              </div>
            </div>
            
          </div>
          
        </div>
        <div class="card mb-3">
          <div class="card-header">
           <h4> Parent's/Guardian Information</h4>
          </div>
          <div class="card-body">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="parentname">Parent/Guardian Name <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="parentname" name="parentname" placeholder="Fullname" required>
              </div>
              <div class="form-group col-md-6">
                <label for="mobilenumber">Parent Type<span style="color:red">*</span></label>
                <select  class="form-control" id="parent_type" name="parent_type" required>
                <option value="" disabled selected>Select</option>
                <option value="Parent">Parent</option>
                <option value="Guardian">Guardian</option>
                </select>
              </div>
            
            </div>
           <div class="form-row">
              <!-- <div class="form-group col-md-6">
                <label for="mobilenumber">Mobile Number <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="mobilenumber" name="mobilenumber" placeholder="Mobile Number" required>
              </div>  -->
              <div class="form-group col-md-6">
                <label for="address">Address <span style="color:red">*</span></label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Address" required>
              </div>
              <div class="form-group col-md-6">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
              </div>
            </div>
            <!-- <div class="form-row">
            
            </div> -->
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
<div class="modal fade" id="bulkenrollment" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentModalLabel">Bulk Enrollment</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      <form id="bulkUploadForm" enctype="multipart/form-data">
      <div class="form-group">
        <label for="csvFile">Upload CSV File</label>
        <input type="file" class="form-control" id="bulkFileInput" name="csvFile" accept=".csv" required>
      </div>
      <button type="submit" id="bulkUploadButton" class="btn btn-primary">Upload and Enroll</button>
    </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include ('footer.php'); ?>
