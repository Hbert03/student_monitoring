<?php


include('header.php');
include('class.php');

$elems = new Elementary();
$totalElems = $elems->getValue("totalElems");

$junior = new Junior_High();
$totalJunior = $junior->getValue("totalJunior");

$senior = new Senior_High();
$totalSenior = $senior->getValue("totalSenior");
?>






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
            <h1 class="m-0">Dashboard</h1>
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
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-primary">
              <div class="inner">
                <h3><?php echo $totalElems; ?></h3>

                <p>ELEMENTARY</p>
              </div>
              <div class="icon">
              <i class="nav-icon fas fa-user-graduate"></i>
              </div>
    
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3><?php echo $totalJunior ?></h3>

                <p>JUNIOR HIGH SCHOOL</p>
              </div>
              <div class="icon">
              <i class="nav-icon fas fa-user-graduate"></i>
              </div>
        
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-4 col-6">
            <!-- small box -->
            <div class="small-box bg-primary">
              <div class="inner">
                <h3><?php echo $totalSenior ?></h3>

                <p>SENIOR HIGH SCHOOL</p>
              </div>
              <div class="icon">
              <i class="nav-icon fas fa-user-graduate"></i>
              </div>
         
            </div>
          </div>
          <!-- ./col -->
         
          <!-- ./col -->
        </div>
        <!-- /.row -->
           <!-- Main row -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Absent Student Data</h5>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>

                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
              
                  <div class="col-md-12">
                     <p class="text-center">
                      <strong></strong>
                         </p>

                       <div class="chart" style="width:100%;">
                       <!-- Sales Chart Canvas -->
                         <canvas id="absentChart" height="70"></canvas>
                        </div>
                      </div>
     
                    </div>
 
                 </div>
          
                <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h5 class="card-title">Number of Enrolled in Current Year</h5>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>

                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
              
                  <div class="col-md-12">
                     <p class="text-center">
                      <strong></strong>
                         </p>

                       <div class="chart" style="width:100%;">
                       <!-- Sales Chart Canvas -->
                         <canvas id="enrolledChart" height="70"></canvas>
                        </div>
                      </div>
     
                    </div>
 
                 </div>
                </div>
                <!-- /.row -->
              </div>
              <!-- ./card-body -->
           
              <!-- /.card-footer -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->

          
</div>
</section>
</div>
  </div>
  <!-- /.content-wrapper -->
  <?php include ('footer.php');  ?>