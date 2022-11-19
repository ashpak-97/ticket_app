<?php
session_start();
include 'includes/common.php';
if (!isset($_SESSION['admin'])) {
    header('location: ' . url1 . '/adminlogin.php');
}
$vid = $_GET['catid'];
$q = "SELECT * FROM service_categories WHERE id='$vid'";
$r = sql_query($q);
$row = sql_fetch_assoc($r);
?>
<?php include 'header.php' ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Edit Categories</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Edit Categories </li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-10">


                                </div>
                                <div class="col-md-2">


                                </div>



                            </div>

                        </div>
                        <div class="card-body">

                            <div id="alert_message"></div>
                            <form>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Category Name</label>
                                        <input type="text" value="<?php echo $row['name'] ?>" class="form-control" name="category_name" placeholder="Enter Category Name">
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Image</label>
                                        <input type="file" class="form-control" name="img">
                                    </div>

                                    <div class="form-check">
                                        <?php
                                        if ($row['status'] == 1) {
                                            echo '<input type="checkbox" class="form-check-input" name="status" checked>';
                                        } else {
                                            echo '<input type="checkbox" class="form-check-input" name="status">';
                                        }

                                        ?>

                                        <label class="form-check-label" for="exampleCheck1">Status</label>
                                    </div>
                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
<?php include 'footer.php' ?>