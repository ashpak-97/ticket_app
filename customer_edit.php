<?php
session_start();
include 'includes/common.php';
if (!isset($_SESSION['admin'])) {
    header('location: ' . url1 . '/adminlogin.php');
}


$disp_url = 'customers.php';
$edit_url = 'customer_edit.php';

if (isset($_GET['mode'])) $mode = $_GET['mode'];
elseif (isset($_POST['mode'])) $mode = $_POST['mode'];
else $mode = 'A';

if (isset($_GET['id'])) $txtid = $_GET['id'];
elseif (isset($_POST['txtid'])) $txtid = $_POST['txtid'];
else $mode = 'A';




$valid_modes = array("A", "I", "E", "U", "D", "DELPIC", "ADD_TICKET", "UPDATE_TICKET", "REMOVE_TICKET");
$mode = EnsureValidMode($mode, $valid_modes, "A");
if ($mode == 'A') {
    $txtid = '0';
    $txtvendor = '';
    $name = '';
    $type = '';
    $contact = '';
    $location = '';
    $address = '';
    $form_mode = 'I';
} else if ($mode == 'I') {
    $name = db_input($_POST['name']);
    $type = db_input($_POST['type']);
    $contact = db_input($_POST['contact']);
    $location = db_input($_POST['location']);
    $address = db_input($_POST['address']);


    LockTable('tb_customer');
    $txtid = NextID('Customer_code', 'tb_customer');
    $q = "INSERT INTO `tb_customer`(`Customer_code`, `Name`, `Type`, `Location`, `Address`, `Contact`) VALUES ('$txtid','$name','$type','$location','$address','$contact')";
    $r = sql_query($q, "EVENTS.123");
    UnLockTable();
    header("location: $disp_url");
    exit;
    // $desc_str = 'Newly Created: '.db_input($q);
    // LogMasterEdit2($txtid, 'USR', $mode, $txtname, $desc_str, $sess_user_id);

    // $_SESSION[PROJ_SESSION_ID]->success_info = "Event Details Successfully Inserted";
} else if ($mode == 'E') {
    $dataArr = GetDataFromID("tb_customer", "Customer_code", $txtid);
    if (empty($dataArr)) {
        header("location: $disp_url");
        exit;
    }
    $name = db_output2($dataArr[0]->Name);
    $type = db_output2($dataArr[0]->Type);
    $contact = db_output2($dataArr[0]->Contact);
    $location = db_output2($dataArr[0]->Location);
    $address = db_output2($dataArr[0]->Address);


    //$modalTITLE = 'Edit ' . $PAGE_TITLE2;
    $form_mode = 'U';
} else if ($mode == 'U') {
    $name = db_input($_POST['name']);
    $type = db_input($_POST['type']);
    $contact = db_input($_POST['contact']);
    $location = db_input($_POST['location']);
    $address = db_input($_POST['address']);
    $values = " Name='$name',Type='$type', Location='$location', Address='$address', Contact='$contact' ";
    $QUERY = UpdataData('tb_customer', $values, "Customer_code=$txtid");
    header("location: $disp_url");
    exit;
    //$_SESSION[PROJ_SESSION_ID]->success_info = "Event Details Successfully Updated";
}


?>
<?php include 'header.php' ?>
<?php include '_include_form.php' ?>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Customers</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Customers </li>
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
                            <h2>Add New Customer</h2>
                        </div>
                        <div class="card-body py-5">
                            <form class="" id="usersForm" name="usersForm" method="post" action="<?php echo $edit_url; ?>" enctype="multipart/form-data">
                                <input type="hidden" name="txtid" id="txtid" value="<?php echo $txtid; ?>">

                                <input type="hidden" name="mode" id="mode" value="<?php echo $form_mode; ?>">

                                <input type="hidden" name="add_mode" id="add_mode" value="N">
                                <div class="col-md-12">


                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <div class="position-relative form-group">
                                                <label for="txtlocality" class="">Name <span class="text-danger">*</span></label>
                                                <input name="name" id="name" type="text" value="<?php echo $name; ?>" class="form-control form-control-sm radius-30">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="position-relative form-group">
                                                <label for="txtlocality" class="">Type <span class="text-danger">*</span></label>
                                                <input name="type" id="type" type="text" value="<?php echo $type; ?>" class="form-control form-control-sm radius-30">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-row">
                                        <div class="col-md-12">

                                            <div class="form-group">
                                                <label for="txtname" class="">Contact <span class="text-danger">*</span></label>
                                                <input name="contact" id="contact" type="text" value="<?php echo $contact; ?>" class="form-control form-control-sm radius-30">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="position-relative form-group">
                                                <label for="txtname" class="">Location </label>
                                                <textarea class="form-control  radius-30" name="location" id="location"><?php echo $location; ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <div class="position-relative form-group">
                                                <label for="txtname" class="">Address </label>
                                                <textarea class="form-control  radius-30" name="address" id="address"><?php echo $address; ?></textarea>
                                            </div>
                                        </div>
                                    </div>





                                    <a href="<?php echo $disp_url; ?>?srch_mode=MEMORY" class="mt-2 btn btn-primary">Back</a>
                                    <button type="submit" class="mt-2 btn btn-success">Save</button>
                                    <!-- <button type="button" class="mt-2 btn btn-light m-1 px-5 radius-30" onClick="AddAnother(this.form);">Save & Add Another</button> -->
                                    <?php
                                    if ($mode == 'E' && $txtid) {
                                    ?>
                                        <button type="button" onClick="SubmitIncludeForm('<?php echo $edit_url; ?>','D','<?php echo $txtid; ?>','User');" class="mt-2 btn btn-danger">Delete</button>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>

        </div>
    </section>
</div>
<script>
    $(document).ready(function() {

    });
</script>
<?php include 'footer.php' ?>