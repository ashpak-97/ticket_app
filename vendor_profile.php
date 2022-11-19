<?php
session_start();
include 'includes/common.php';
if (!isset($_SESSION['vendorid'])) {
    header('location: ' . url1 . '/login.php');
}
?>
<?php
if (isset($_POST['submit'])) {
    // var_dump($_POST);
    // exit;
    $vid = $_SESSION['vendorid'];
    if (isset($_FILES['pdf'], $_FILES['insurancefile'], $_POST['first_name'], $_POST['last_name'], $_POST['phone'], $_POST['company_name'], $_POST['address'], $_POST['date_of_e'], $_POST['DateOfEI'])) {

        $first_name = dbinput($_POST['first_name']);
        $last_name = dbinput($_POST['last_name']);
        $company_name = dbinput($_POST['company_name']);
        $address = dbinput($_POST['address']);
        $date_of_e = $_POST['date_of_e'];
        $DateOfEI = $_POST['DateOfEI'];
        $phone = $_POST['phone'];

        /* Getting file name */
        $filename = $_FILES['pdf']['name'];

        /* Location */
        $location = "./uploads/Licence/" . $filename;
        $FileType = pathinfo($location, PATHINFO_EXTENSION);
        $FileType = strtolower($FileType);

        /* Valid extensions */
        $valid_extensions = array("jpg", "jpeg", "png", "pdf");

        $response = 0;
        /* Check file extension */
        if (in_array(strtolower($FileType), $valid_extensions)) {
            /* Upload file */
            if (move_uploaded_file($_FILES['pdf']['tmp_name'], $location)) {
                $response = $location;
            }
        }
        //image url
        $url1 = 'uploads/License/' . $filename;


        /* Getting file name */
        $filename = $_FILES['insurancefile']['name'];

        /* Location */
        $location = "./uploads/insurancefiles/" . $filename;
        $FileType = pathinfo($location, PATHINFO_EXTENSION);
        $FileType = strtolower($FileType);

        /* Valid extensions */
        $valid_extensions = array("jpg", "jpeg", "png", "pdf");

        $response = 0;
        /* Check file extension */
        if (in_array(strtolower($FileType), $valid_extensions)) {
            /* Upload file */
            if (move_uploaded_file($_FILES['insurancefile']['tmp_name'], $location)) {
                $response = $location;
            }
        }
        //image url
        $url2 = 'uploads/insurancefiles/' . $filename;

        $q = "UPDATE service_providers SET First_name='$first_name',Last_name='$last_name',company_name='$company_name',address='$address',phone='$phone',pdf_file='$url1',dDate_of_expiry='$date_of_e',insurance_file='$url2',dDate_insurance='$DateOfEI' WHERE id='$vid' ";

        if (sql_query($q)) {
            echo '<script>
                alert("Details Updated");
                
            </script>';
        } else {
            echo '<script>
                alert("Details Not Updated");
                
            </script>';
        }
    }
}
?>

<?php include 'header.php' ?>
<!-- Content Wrapper. Contains page content -->
<?php
$vid = $_SESSION['vendorid'];
$q = "SELECT * FROM `service_providers` WHERE id='$vid'";
$r = sql_query($q);
$row = sql_fetch_assoc($r);
?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Profile</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">User Profile</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">

                    <!-- Profile Image -->
                    <div class="card card-primary card-outline">
                        <div class="card-body box-profile">
                            <!-- <div class="text-center">
                                <img class="profile-user-img img-fluid img-circle" src="../../dist/img/user4-128x128.jpg" alt="User profile picture">
                            </div> -->

                            <h3 class="profile-username text-center"><?php echo $row['First_name'] . ' ' . $row['Last_name'] ?></h3>

                            <p class="text-muted text-center">Vendor</p>

                            <ul class="list-group list-group-unbordered mb-3">
                                <li class="list-group-item">
                                    <b>Followers</b> <a class="float-right">1,322</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Following</b> <a class="float-right">543</a>
                                </li>
                                <li class="list-group-item">
                                    <b>Friends</b> <a class="float-right">13,287</a>
                                </li>
                            </ul>

                            <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->

                    <!-- About Me Box -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">About Me</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">


                            <strong><i class="fas fa-map-marker-alt mr-1"></i> Location</strong>

                            <p class="text-muted"><?php echo $row['address'] ?></p>

                            <hr>


                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-header p-2">
                            <ul class="nav nav-pills">

                                <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Profile</a></li>
                                <li class="nav-item"><a class="nav-link" href="#coverage" data-toggle="tab">Coverage</a></li>
                            </ul>
                        </div><!-- /.card-header -->
                        <div class="card-body">
                            <div class="tab-content">

                                <!-- /.tab-pane -->

                                <div class="active tab-pane" id="settings">
                                    <form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">First Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="first_name" value="<?php echo $row['First_name'] ?>" placeholder="First Name">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputName" class="col-sm-2 col-form-label">Last Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="last_name" value="<?php echo $row['Last_name'] ?>" placeholder="Last Name">
                                            </div>
                                        </div>
                                        <!-- <div class="form-group row">
                                            <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" id="inputEmail" placeholder="Email">
                                            </div>
                                        </div> -->
                                        <div class="form-group row">
                                            <label for="inputName2" class="col-sm-2 col-form-label">Company Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="company_name" value="<?php echo $row['company_name'] ?>" placeholder="Company Name">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputExperience" class="col-sm-2 col-form-label">Address</label>
                                            <div class="col-sm-10">
                                                <textarea class="form-control" name="address" placeholder="Address"><?php echo $row['address'] ?></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputSkills" class="col-sm-2 col-form-label">Phone</label>
                                            <div class="col-sm-10">
                                                <input type="number" class="form-control" value="<?php echo $row['phone'] ?>" name="phone" placeholder="Phone">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputSkills" class="col-sm-2 col-form-label">Licence(PDF)</label>
                                            <div class="col-sm-10">
                                                <input type="file" class="form-control" name="pdf">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputSkills" class="col-sm-2 col-form-label">Date Of Expiry</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" value="<?php echo $row['dDate_of_expiry'] ?>" name="date_of_e">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputSkills" class="col-sm-2 col-form-label">Insurance(PDF)</label>
                                            <div class="col-sm-10">
                                                <input type="file" class="form-control" name="insurancefile">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="inputSkills" class="col-sm-2 col-form-label">Date Of Expiry</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" value="<?php echo $row['dDate_insurance'] ?>" name="DateOfEI">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="offset-sm-2 col-sm-10">
                                                <button type="submit" name="submit" value="submit" class="btn btn-danger">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="tab-pane" id="coverage">
                                    <!-- The timeline -->

                                </div>
                                <!-- /.tab-pane -->
                            </div>
                            <!-- /.tab-content -->
                        </div><!-- /.card-body -->
                    </div>
                    <!-- /.nav-tabs-custom -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php include 'footer.php' ?>