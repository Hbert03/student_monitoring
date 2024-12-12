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
  <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card ">
              <div class="card-header">
                <h4 class="card-title">Student</h4>
              </div>
              <div class="card-body">
                <div>
                  <div class="btn-group w-100 mb-2">                   
                  </div>
                  <div class="mb-2">
                    <a class="btn btn-light btn-lg" href="javascript:void(0)" data-shuffle>Grade Level </a>
                   
                  </div>
                </div>
                <div>
                  <div class="filter-container p-0 row">
                   
                  <div class="card filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=7" 
                          class="img-fluid mb-2 grade-image" 
                          data-grade-level="7" 
                          alt="white sample" />
                    </div>
                    <div class="card filtr-item col-sm-2" data-category="2, 4" data-sort="white sample">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=8" 
                          class="img-fluid mb-2 grade-image" 
                          data-grade-level="8" 
                          alt="black sample" />
                    </div>

                    <div class="card filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=9" 
                          class="img-fluid mb-2 grade-image" 
                          data-grade-level="9" 
                          alt="white sample" />
                    </div>
                    <div class="card filtr-item col-sm-2" data-category="2, 4" data-sort="white sample">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=10" 
                          class="img-fluid mb-2 grade-image" 
                          data-grade-level="10" 
                          alt="black sample" />
                    </div>
                    <div class="card filtr-item col-sm-2" data-category="1" data-sort="white sample">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=11" 
                          class="img-fluid mb-2 grade-image" 
                          data-grade-level="11" 
                          alt="white sample" />
                    </div>
                    <div class=" card filtr-item col-sm-2" data-category="2, 4" data-sort="white sample">
                      <img src="https://via.placeholder.com/300/FFFFFF?text=12" 
                          class="img-fluid mb-2 grade-image" 
                          data-grade-level="12" 
                          alt="black sample" />
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        
        </div>
      </div><!-- /.container-fluid -->
    </section>
    <div class="modal fade" id="student-modal" tabindex="-1" role="dialog" aria-labelledby="studentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="studentModalLabel">Student List</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <button class="btn btn-primary" id="bulk-update-btn">Update Bulk</button>
        <!-- <select id="sort_grade_level" class="form-control w-25 sort_grade_level"></select> -->
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
    </div>
  </div>
</div>

</div>
<!-- /.content-wrapper -->

<?php include ('footer.php'); ?>
