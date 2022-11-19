<?php
include "../includes/common.php";
include "../includes/thumbnail.php";

$PAGE_TITLE2 = 'Events';

$PAGE_TITLE .= $PAGE_TITLE2;

$disp_url = 'nl_events_disp.php';
$edit_url = 'nl_events_edit.php';

if (isset($_GET['mode'])) $mode = $_GET['mode'];
elseif (isset($_POST['mode'])) $mode = $_POST['mode'];
else $mode = 'A';

if (isset($_GET['id'])) $txtid = $_GET['id'];
elseif (isset($_POST['txtid'])) $txtid = $_POST['txtid'];
else $mode = 'A';


$Q1 = "select iVendorID from vendor_users where iUserID='$sess_user_id'";
$R1 = sql_query($Q1);
list($Vendor_id) = sql_fetch_row($R1);

$CLUB_ARR = GetXArrFromYID('SELECT iClubID,vName FROM nl_club', '3');
$TICKETS_ARR = GetXArrFromYID("select iTicketID, vName from nl_tickets", "3");

$valid_modes = array("A", "I", "E", "U", "D", "DELPIC", "ADD_TICKET", "UPDATE_TICKET", "REMOVE_TICKET");
$mode = EnsureValidMode($mode, $valid_modes, "A");
if ($mode == 'A') {
    $txtid = '0';
    $txtvendor = '';
    $txtname = '';
    $txtwebsite = '';
    $txtemail = '';
    $txtlogo = '';
    $txtaddress = '';
    $txtaddress2 = '';
    $txtlocality = '';
    $txtcity = '';
    $txtcountry = '';
    $txtpostcode = '';
    $txtphone = '';
    $file_pic = '';
    $txtmobile = '';
    $txttagline = '';
    $txtdesc = '';
    $txtcontact = 'A';
    $txtcemail = '0';
    $rdstatus = 'A';
    $countryid = '';
    $website = '';
    $txtaddr1 = '';
    $txtaddr2 = '';
    $post_code = '';
    $tagline = '';
    $descr = '';
    $seo_title = '';
    $seo_keywords = '';
    $seo_descr = '';
    $locality = '';
    $rdslider = 'N';
    $txtMobile = '';
    $txt_city = '';
    $txtcontact = '';
    $txtseotitle = '';
    $txtseokeywords = '';
    $txtseodesc = '';
    $modalTITLE = 'New ' . $PAGE_TITLE2;
    $form_mode = 'I';
} else if ($mode == 'I') {
    $Clubid = db_input($_POST['ClubID']);
    $txtname = db_input($_POST['txtname']);
    $txturlname = GetUrlName($txtname);
    $txtdesc = db_input($_POST['txtdesc']);
    $file_pic = ''; //db_input($_POST['file_pic']);
    $file_listingpic = ''; //db_input($_POST['file_listingpic']);
    $txtvenue = ''; //db_input($_POST['txtvenue']);
    $txt_dstart = db_input($_POST['txt_dstart']);
    $txt_tstart = db_input($_POST['txt_tstart']);
    $txt_dend = ''; //db_input($_POST['txt_dend']);
    $txt_tend = ''; //db_input($_POST['txt_tend']);
    $txtlink = ''; //db_input($_POST['txtlink']);
    $txtlinktext = ''; //db_input($_POST['txtlinktext']);
    $cmbtype = ''; //db_input($_POST['cmbtype']);
    $txtdtcreated = NOW; //db_input($_POST['txtdtcreated']);
    $txtdtupdated = ''; //db_input($_POST['txtdtupdated']);
    $txtuserid = $sess_user_id; // db_input($_POST['txtuserid']);
    $txtrank = GetMaxRank('nl_events');
    $txtseotitle = db_input($_POST['txtseotitle']);
    $txtseokeywords = db_input($_POST['txtseokeywords']);
    $txtseodesc = db_input($_POST['txtseoDescrp']);
    $rdstatus = db_input($_POST['rdstatus']);
    $rdslider = db_input($_POST['rdslider']);

    LockTable('nl_events');
    $txtid = NextID('iEventID', 'nl_events');
    $q = "INSERT INTO nl_events (iEventID,iClubID, vName, vUrlName, vDesc, vVenue, dStart, tStart, dEnd, tEnd, vLink, vLinkText, cType, vSeo_title, vSeo_keywords, vSeo_description, dtCreated, iUserID, iRank, cStatus, cShowSlider) VALUES ('$txtid','$Clubid', '$txtname', '$txturlname', '$txtdesc', '$txtvenue', '$txt_dstart', '$txt_tstart', '$txt_dend', '$txt_tend', '$txtlink', '$txtlinktext', '$cmbtype', '$txtseotitle', '$txtseokeywords', '$txtseodesc', '$txtdtcreated', '$txtuserid', '$txtrank', '$rdstatus', '$rdslider')";
    $r = sql_query($q, "EVENTS.123");
    UnLockTable();

    // $desc_str = 'Newly Created: '.db_input($q);
    // LogMasterEdit2($txtid, 'USR', $mode, $txtname, $desc_str, $sess_user_id);

    $_SESSION[PROJ_SESSION_ID]->success_info = "Event Details Successfully Inserted";
} else if ($mode == 'E') {
    $dataArr = GetDataFromID("nl_events", "iEventID", $txtid);
    if (empty($dataArr)) {
        header("location: $disp_url");
        exit;
    }
    $file_pic = db_output2($dataArr[0]->vImg_Desktop);
    $txtname = db_output2($dataArr[0]->vName);
    $Clubid = db_output2($dataArr[0]->iClubID);
    $txtdesc = db_output2($dataArr[0]->vDesc);
    $txtvenue = ''; //db_input($_POST['txtvenue']);
    $txt_dstart = db_output2($dataArr[0]->dStart);
    $txt_tstart = db_output2($dataArr[0]->tStart);
    $txt_dend = ''; //db_input($_POST['txt_dend']);
    $txt_tend = ''; //db_input($_POST['txt_tend']);
    $txtlink = ''; //db_input($_POST['txtlink']);
    $txtlinktext = ''; //db_input($_POST['txtlinktext']);
    $cmbtype = ''; //db_input($_POST['cmbtype']);
    $txtdtcreated = NOW; //db_input($_POST['txtdtcreated']);
    $txtdtupdated = ''; //db_input($_POST['txtdtupdated']);
    $txtseotitle = db_output2($dataArr[0]->vSeo_title);
    $txtseokeywords = db_output2($dataArr[0]->vSeo_keywords);
    $txtseodesc = db_output2($dataArr[0]->vSeo_description);
    $rdstatus = db_output2($dataArr[0]->cStatus);
    $rdslider = db_output2($dataArr[0]->cShowSlider);

    $modalTITLE = 'Edit ' . $PAGE_TITLE2;
    $form_mode = 'U';
} else if ($mode == 'U') {

    //$iVendorID = $Vendor_id;
    $Clubid = db_input($_POST['ClubID']);
    $txtname = db_input($_POST['txtname']);
    $txturlname = GetUrlName($txtname);
    $txtdesc = db_input($_POST['txtdesc']);
    $file_pic = ''; //db_input($_POST['file_pic']);
    $file_listingpic = ''; //db_input($_POST['file_listingpic']);
    $txtvenue = ''; //db_input($_POST['txtvenue']);
    $txt_dstart = db_input($_POST['txt_dstart']);
    $txt_tstart = db_input($_POST['txt_tstart']);
    $txt_dend = ''; //db_input($_POST['txt_dend']);
    $txt_tend = ''; //db_input($_POST['txt_tend']);
    $txtlink = ''; //db_input($_POST['txtlink']);
    $txtlinktext = ''; //db_input($_POST['txtlinktext']);
    $cmbtype = ''; //db_input($_POST['cmbtype']);
    $txtdtcreated = NOW; //db_input($_POST['txtdtcreated']);
    $txtdtupdated = ''; //db_input($_POST['txtdtupdated']);
    $txtuserid = $sess_user_id; // db_input($_POST['txtuserid']);
    //$txtrank = GetMaxRank('nl_events');
    $txtseotitle = db_input($_POST['txtseotitle']);
    $txtseokeywords = db_input($_POST['txtseokeywords']);
    $txtseodesc = db_input($_POST['txtseoDescrp']);
    $rdstatus = db_input($_POST['rdstatus']);
    $rdslider = db_input($_POST['rdslider']);


    $values = " vName='$txtname',iClubID='$Clubid', vUrlName='$txturlname', vDesc='$txtdesc', vVenue='$txtvenue', dStart='$txt_dstart', tStart='$txt_tstart', dEnd='$txt_dend', tEnd='$txt_tend', vLink='$txtlink', vLinkText='$txtlinktext', cType='$cmbtype', dtUpdated='$txtdtcreated', vSeo_title='$txtseotitle', vSeo_keywords='$txtseokeywords', vSeo_description='$txtseodesc', iUserID='$txtuserid', cStatus='$rdstatus', cShowSlider='$rdslider' ";
    $QUERY = UpdataData('nl_events', $values, "iEventID=$txtid");

    $_SESSION[PROJ_SESSION_ID]->success_info = "Event Details Successfully Updated";
} elseif ($mode == 'DELPIC') {
    $file_name = GetXFromYID("select vImg_Desktop from nl_events where iEventID=$txtid");
    if (!empty($file_name))
        DeleteFile($file_name, CLUB_UPLOAD);

    UpdateField('nl_events', 'vImg_Desktop', '', "iEventID=$txtid");

    $txtname = GetXFromYID('select vImg_Desktop from nl_events where iEventID=' . $txtid);

    $desc_str = 'Deleted: Club Logo';
    // LogMasterEdit($txtid, 'USR', $mode, $txtname, $desc_str);

    $_SESSION[PROJ_SESSION_ID]->success_info = "Logo Deleted Successfully";

    header("location: $edit_url?mode=E&id=$txtid");
    exit;
} elseif ($mode == 'D') {
    $disp_flag = (isset($_GET["disp"]) && $_GET["disp"] == "Y") ? true : false;
    $loc_str = $disp_url;

    // $chk_arr['Requests'] = GetXFromYID('select count(*) from concrequest where iUserID_request=' . $txtid);
    // $chk_arr['Bookings'] = GetXFromYID('select count(*) from concbooking where iUserID_booking=' . $txtid);
    // $chk_arr['Bookings Details'] = GetXFromYID('select count(*) from concbooking_dat where iUserID_booking=' . $txtid);
    // $chk_arr['Property'] = GetXFromYID('select count(*) from users_property_assoc where iUserID=' . $txtid);
    $chk = array_sum($chk_arr);

    if (!$chk) {
        $file_name = GetXFromYID("select vImg_Desktop from nl_events where iEventID=$txtid");
        if (!empty($file_name))
            DeleteFile($file_name, CLUB_UPLOAD);

        $txtname = GetXFromYID('select vName from nl_events where iEventID=' . $txtid);
        //$desc_str = 'Deleted: '.db_input($txtname);
        //LogMasterEdit($txtid, 'USR', $mode, $txtname, $desc_str, $sess_user_id);

        LogMasterEdit($txtid, 'USR', $mode, $txtname);

        DeleteData('nl_events', 'iEventID', $txtid);
        $_SESSION[PROJ_SESSION_ID]->success_info = "Event Deleted Successfully";
    } else
        $_SESSION[PROJ_SESSION_ID]->alert_info = "User Details Could Not Be Deleted Because of Existing " . (CHK_ARR2Str($chk_arr)) . " Dependencies";

    header("location: $edit_url?mode=E&id=$txtid");
    exit;
} else if ($mode == 'ADD_TICKET') {
    $event_ticket_arr = isset($_POST['event_ticket']) ? $_POST['event_ticket'] : array();


    if (!empty($event_ticket_arr) && count($event_ticket_arr)) {
        foreach ($event_ticket_arr as $_TIKCETID) {
            $exists_ticket = GetXFromYID("select count(*) from nl_events_tickets where iTicketID=$_TIKCETID and iEventID=$txtid");

            if ($exists_ticket == 0 || $exists_ticket == -1) {
                $q = "INSERT INTO nl_events_tickets(iEventID, iTicketID, iQty, iQtyBooked, iQtyUsed, fRate, cStatus) VALUES ('$txtid','$_TIKCETID','0','0','0','0','A')";
                $r = sql_query($q, "EVENT.E.192");
            }
        }

        $_SESSION[PROJ_SESSION_ID]->success_info = "Tickets Updated Successfully";
    } else
        $_SESSION[PROJ_SESSION_ID]->error_info = "No Tickets Selected";

    header("location: $edit_url?mode=E&id=$txtid");
    exit;
} else if ($mode == 'UPDATE_TICKET') {
    $txtticketid = isset($_POST['txtticketid']) ? $_POST['txtticketid'] : "";
    $txtqty = isset($_POST['txtqty']) ? $_POST['txtqty'] : "";
    $txtrate = isset($_POST['txtrate']) ? $_POST['txtrate'] : "";

    $q = "UPDATE nl_events_tickets SET iQty='$txtqty',fRate='$txtrate' WHERE iEventID='$txtid' and iTicketID='$txtticketid'";
    $r = sql_query($q, "EVENT.E.222");

    $_SESSION[PROJ_SESSION_ID]->success_info = "Tickets Updated Successfully";
    header("location: $edit_url?mode=E&id=$txtid");
    exit;
} else if ($mode == 'REMOVE_TICKET') {
    $txtticketid = isset($_GET['tid']) ? $_GET['tid'] : "0";

    // $chk_arr['Bookings'] = GetCounts('booking_dat', " and iTicketID=$txtticketid");

    $chk = array_sum($chk_arr);

    if (!$chk) {
        // DeleteData('events', 'iEventID', $txtid);
        $q = "UPDATE nl_events_tickets SET cStatus='X' WHERE iEventID='$txtid' and iTicketID='$txtticketid'";
        $r = sql_query($q, "EVENT.E.222");
        $_SESSION[PROJ_SESSION_ID]->success_info = "Ticket Deleted Successfully";
        $loc_str = $disp_url;
    } else {
        $_SESSION[PROJ_SESSION_ID]->alert_info = "Ticket Could Not Be Deleted Because of Existing " . (CHK_ARR2Str($chk_arr)) . " Dependencies";
    }

    // $_SESSION[PROJ_SESSION_ID]->success_info = "Tickets Deleted Successfully";
    header("location: $edit_url?mode=E&id=$txtid");
    exit;
}

if ($mode == "I" || $mode == "U") {
    if (is_uploaded_file($_FILES["file_pic"]["tmp_name"])) {
        $uploaded_pic = $_FILES["file_pic"]["name"];
        $name = basename($_FILES['file_pic']['name']);
        $file_type = $_FILES['file_pic']['type'];
        $size = $_FILES['file_pic']['size'];
        $extension = substr($name, strrpos($name, '.') + 1);

        if (IsValidFile($file_type, $extension, 'P') && $size <= 3000000) {
            $pic_name = GetXFromYID('select vImg_Desktop from nl_events where iEventID=' . $txtid);

            if (!empty($pic_name))
                DeleteFile($pic_name, EVENTS_UPLOAD);

            if (RANDOMIZE_FILENAME == 0) {
                $newname = NormalizeFilename($uploaded_pic); // normalize the file name
                $pic_name = $txtid . "-event-desktop-" . $newname;
            } else
                $pic_name = $txtid . '-event-desktop-' . NOW3 . '.' . $extension;

            $tmp_name = "TMP_" . $pic_name;

            $dir = opendir(EVENTS_UPLOAD);
            copy($_FILES["file_pic"]["tmp_name"], EVENTS_UPLOAD . $tmp_name);
            ThumbnailImage($tmp_name, $pic_name, EVENTS_UPLOAD, 640, 480);
            DeleteFile($tmp_name, EVENTS_UPLOAD);
            closedir($dir);   // close the directory

            $q = "update nl_events set vImg_Desktop='$pic_name' where iEventID=$txtid";
            $r = sql_query($q, 'User.E.126');
        } else {
            if ($size > 3000000)
                $_SESSION[PROJ_SESSION_ID]->error_info = "Logo Image Could Not Be Uploaded as the File Size is greate then 3MB";
            elseif (!in_array($extension, $IMG_TYPE))
                $_SESSION[PROJ_SESSION_ID]->error_info = "Please only upload files that end in types: " . implode(',', $IMG_TYPE) . ". Please select a new file to upload and submit again.";
        }
    }

    $add_mode = (isset($_POST['add_mode'])) ? $_POST['add_mode'] : 'N';
    $loc_str = $edit_url . '?mode=E&id=' . $txtid;
    if ($add_mode == 'Y') $loc_str = $edit_url;

    header("location: $loc_str");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <?php include 'load.links.php' ?>


</head>
<?php require_once("_include_form.php"); ?>

<body class="bg-theme bg-theme1">
    <!-- wrapper -->
    <div class="wrapper">
        <!--sidebar-wrapper-->
        <?php include 'load.menu.php' ?>
        <!--end sidebar-wrapper-->
        <!--header-->
        <?php //include 'load.header.php' 
        ?>
        <!--end header-->
        <!--page-wrapper-->
        <div class="page-wrapper">
            <!--page-content-wrapper-->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <!--breadcrumb-->
                    <div class="page-breadcrumb d-none d-md-flex align-items-center mb-3">
                        <div class="breadcrumb-title pr-3">Events</div>
                        <div id="LBL_INFO"><?php echo $sess_info_str; ?></div>
                        <div class="pl-3">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0 p-0">
                                    <li class="breadcrumb-item"><a href="javascript:;"><i class='bx bx-home-alt'></i></a>
                                    </li>
                                    <li class="breadcrumb-item active " aria-current="page">Add New</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-7 col-lg-7 col-md-7">
                            <div class="card radius-15 border-lg-top-white">
                                <div class="card-body p-5">

                                    <form class="" id="usersForm" name="usersForm" method="post" action="<?php echo $edit_url; ?>" enctype="multipart/form-data">
                                        <input type="hidden" name="txtid" id="txtid" value="<?php echo $txtid; ?>">

                                        <input type="hidden" name="mode" id="mode" value="<?php echo $form_mode; ?>">

                                        <input type="hidden" name="add_mode" id="add_mode" value="N">
                                        <input type="hidden" name="user_token" id="user_token" value="<?php echo $sess_user_token; ?>">
                                        <!-- TRACK CHANGES -->

                                        <input type="hidden" name="cmblevel_title" value="Level" />
                                        <input type="hidden" name="cmblevel_arr" value="USER_LEVEL_ARR" />
                                        <input type="hidden" name="rdstatus_old" value="<?php echo $rdstatus; ?>" />
                                        <input type="hidden" name="rdstatus_title" value="Status" />
                                        <input type="hidden" name="rdstatus_arr" value="STATUS_ARR" />
                                        <div class="col-md-12">

                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label for="txtname" class="">Name <span class="text-danger">*</span></label>
                                                        <input name="txtname" id="txtname" type="text" value="<?php echo $txtname; ?>" class="form-control form-control-sm radius-30">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label for="txtname" class="">Select Club <span class="text-danger">*</span></label>
                                                        <select class="form-control radius-30" id="ClubID" name="ClubID">
                                                            <?php

                                                            foreach ($CLUB_ARR as $key => $value) {
                                                                $SELECTED = ($Clubid == $key) ? 'selected' : '';
                                                                echo '<option value="' . $key . '"' . $SELECTED . ' >' . $value . '</option>';
                                                            }


                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label for="txtdatefrom" class="">Event Date <span class="text-danger">*</span></label>
                                                        <!-- <input type="text" class="form-control input-mask-trigger" placeholder="dd-mm-yyyy" data-inputmask-alias="datetime" data-inputmask-inputformat="dd-mm-yyyy" data-inputmask-placeholder="dd-mm-yyyy" name="txt_dstart" id="txt_dstart" value="<?php echo $txt_dstart; ?>" />  -->
                                                        <input name="txt_dstart" id="txt_dstart" type="date" value="<?php echo $txt_dstart; ?>" class="form-control" />
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label for="txtdatefrom" class="">Event Time <span class="text-danger">*</span></label>
                                                        <input type="time" class="form-control " name="txt_tstart" id="txt_tstart" value="<?php echo $txt_tstart; ?>" />
                                                        <small class="form-text text-muted"> 24 Hours </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12">
                                                    <div class="position-relative form-group">
                                                        <label for="txtname" class="">Description </label>
                                                        <textarea class="form-control ckeditor radius-30" name="txtdesc" id="txtdesc"><?php echo $txtdesc; ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="avatar-icon-wrapper btn-hover-shine mb-2">
                                                    <label for="file_pic" class="">Logo</label>
                                                    <div class="avatar-icon rounded" style="width: 200px; height: 200px;">
                                                        <?php
                                                        $src = NOIMAGE;
                                                        if (IsExistFile($file_pic, EVENTS_UPLOAD))
                                                            $src = EVENTS_PATH . $file_pic;
                                                        ?>
                                                        <img id="imgDiv" src="<?php echo $src; ?>" alt="Avatar">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php
                                                if (IsExistFile($file_pic, EVENTS_UPLOAD)) {
                                                ?>
                                                    <button type="button" onClick="SubmitIncludeForm('<?php echo $edit_url; ?>','DELPIC','<?php echo $txtid; ?>','User Image');" class="mt-2 btn btn-danger">Remove</button>
                                                <?php
                                                }
                                                ?>
                                                <label for="file_pic" class="custom-file-upload mt-3 btn btn-warning"> <i class="fa fa-cloud-upload"></i> Browse </label>
                                                <input id="file_pic" name="file_pic" type="file" class="file-upload form-control-file" onChange="ValidateFileUpload('file_pic','P'); PreviewImage(this)">
                                            </div>

                                            <div class="form-row">

                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label for="rdstatus" class="">Status</label>
                                                        <?php echo FillRadios($rdstatus, 'rdstatus', $STATUS_ARR); ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label for="rdslider" class="">Feature on Home</label>
                                                        <?php echo FillRadios($rdslider, 'rdslider', $YES_ARR); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label for="txtlocality" class="">SEO Title <span class="text-danger">*</span></label>
                                                        <input name="txtseotitle" id="txtseotitle" type="text" value="<?php echo $txtseotitle; ?>" class="form-control form-control-sm radius-30">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="position-relative form-group">
                                                        <label for="txtlocality" class="">SEO Keywords <span class="text-danger">*</span></label>
                                                        <input name="txtseokeywords" id="txtseokeywords" type="text" value="<?php echo $txtseokeywords; ?>" class="form-control form-control-sm radius-30">
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="form-row">
                                                <div class="col-md-12">

                                                    <div class="form-group">
                                                        <label for="txtname" class="">SEO DESCRP <span class="text-danger">*</span></label>
                                                        <input name="txtseoDescrp" id="txtseoDescrp" type="text" value="<?php echo $txtseodesc; ?>" class="form-control form-control-sm radius-30">
                                                    </div>
                                                </div>
                                            </div>

                                            <button type="button" onClick="location.href='<?php echo $disp_url; ?>?srch_mode=MEMORY';" class="mt-2 btn btn-light m-1 px-5 radius-30">Back</button>
                                            <button type="submit" class="mt-2 btn btn-light m-1 px-5 radius-30">Save</button>
                                            <button type="button" class="mt-2 btn btn-light m-1 px-5 radius-30" onClick="AddAnother(this.form);">Save & Add Another</button>
                                            <?php
                                            if ($mode == 'E' && $txtid) {
                                            ?>
                                                <button type="button" onClick="SubmitIncludeForm('<?php echo $edit_url; ?>','D','<?php echo $txtid; ?>','User');" class="mt-2 btn btn-light m-1 px-5 radius-30">Delete</button>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php if ($mode == "E" && !empty($txtid)) { ?>
                            <div class="col-md-5">
                                <div class="main-card mb-3 card col-md-12">
                                    <div class="card-header-tab card-header">
                                        <div class="card-header-title font-size-lg text-capitalize font-weight-normal"> <i class="header-icon pe-7s-cash mr-3 text-muted opacity-6"> </i>Event Tickets </div>
                                        <div class="btn-actions-pane-right actions-icon-btn">
                                            <button type="button" class="btn-shadow btn btn-info btn-sm" onclick="getTickets('<?php echo $txtid; ?>', 'A')">
                                                <span class="btn-icon-wrapper btn-sm pr-2 opacity-7"> <i class="fa fa-plus fa-w-20"></i> </span> Add Ticket
                                            </button>
                                        </div>
                                    </div>

                                    <div class="card-body">
                                        <table style="width: 100%;" id="tickets" class="table table-hover table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th width="5%">#</th>
                                                    <th>Name</th>
                                                    <th>Qty</th>
                                                    <th>Price</th>

                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php

                                                $q = "SELECT * FROM nl_events_tickets WHERE iEventID=$txtid and cStatus='A'";
                                                $r = sql_query($q, "EVENT.E.254");

                                                if (sql_num_rows($r)) {
                                                    for ($i = 1; $o = sql_fetch_object($r); $i++) {
                                                        $i_str = $i . '.';
                                                        $et_eventid = $o->iEventID;
                                                        $et_ticketid = $o->iTicketID;
                                                        $et_qty = FormatNumber($o->iQty);
                                                        $et_qtybooked = $o->iQtyBooked;
                                                        $et_qtyused = $o->iQtyUsed;
                                                        $et_rate = FormatNumber($o->fRate);
                                                        $et_status = $o->cStatus;
                                                        $et_name = isset($TICKETS_ARR[$et_ticketid]) ? $TICKETS_ARR[$et_ticketid] : NA;

                                                ?>
                                                        <tr>
                                                            <td><?php echo $i_str; ?></td>
                                                            <td><a href="javascript:;" onclick="getTickets('<?php echo $et_eventid; ?>', 'E', '<?php echo $et_ticketid; ?>')"><?php echo $et_name; ?></td>
                                                            <td><?php echo $et_qty; ?></td>
                                                            <td><?php echo $et_rate; ?></td>

                                                        </tr>
                                                <?php
                                                    }
                                                } else {
                                                    echo '<tr><td colspan="4" align="center">No records found</td></tr>';
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                    </div>
                </div>
            </div>
            <!--end page-content-wrapper-->
        </div>
        <!--end page-wrapper-->
        <!--start overlay-->

        <!--end overlay-->
        <!--Start Back To Top Button--> <a href="javaScript:;" class="back-to-top"><i class='bx bxs-up-arrow-alt'></i></a>
        <!--End Back To Top Button-->
        <!--footer -->
        <?php include 'load.footer.php'  ?>
        <!-- end footer -->
    </div>
    <!-- end wrapper -->
    <div class="modal fade bd-example-modal-lg" id="eventTicket" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="mticketTITLE">Modify Event Ticket</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="mticketBODY"></div>
            </div>
        </div>
    </div>


    <!-- JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <?php include 'load.scripts.php' ?>
    <script type="text/javascript" src="../scripts/ajax.js"></script>
    <script type="text/javascript" src="../scripts/common.js"></script>
    <script type="text/javascript" src="../scripts/md5.js"></script>
    <script>
        $(document).ready(function() {
            $("#clubForm").submit(function() {
                err = 0;
                err_arr = new Array();
                ret_val = true;

                var md = "<?php echo $mode ?>";
                var txtname = $(this).find('#txtname');
                var txtphone = $(this).find('#txtphone');

                var txtusername = $(this).find('#txtusername');
                var txtpassword = $(this).find('#txtpassword');
                var code = $(this).find('#code_flag');

                if ($.trim(txtname.val()) == 0 || $.trim(txtname.val()) == '') {
                    ShowError(txtname, "Please enter name");
                    err_arr[err] = txtname;
                    err++;
                } else
                    HideError(txtname);

                if ($.trim(txtphone.val()) == 0 || $.trim(txtphone.val()) == '') {
                    ShowError(txtphone, "Please enter phone no");
                    err_arr[err] = txtphone;
                    err++;
                } else
                    HideError(txtphone);

                if ($.trim(txtusername.val()) == 0 || $.trim(txtusername.val()) == '') {
                    ShowError(txtusername, "Please enter username");
                    err_arr[err] = txtusername;
                    err++;
                } else
                    HideError(txtusername);

                if (code.val() == '0' && $.trim(txtusername.val()) != '') {
                    ShowError(u, "Username already taken, <br>Please select another username")
                    ret = false;
                }

                if (md != 'E') {
                    if ($.trim(txtpassword.val()) == '') {
                        ShowError(txtpassword, "Please enter password");
                        err_arr[err] = txtpassword;
                        err++;
                    } else
                        HideError(txtpassword);
                }

                if (err > 0) {
                    err_arr[0].focus();
                    ret_val = false;
                } else {
                    if ($.trim(txtpassword.val()) != '') {
                        p_str = GenerateNewPass(b64_md5(txtpassword.val()));
                        txtpassword.val(p_str);
                    }
                }

                return true;
            });

        });

        function getTickets(eventid, mode, ticketid = "") {
            $.ajax({
                url: '_event_tickets.php',
                type: 'post',
                async: false,
                data: {
                    response: 'EVENT_TICKET',
                    mode: mode,
                    eventid: eventid,
                    ticketid: ticketid
                },
                success: function(results) {
                    var result = results.split('~~**~~');
                    var title = result[0];
                    var body = result[1];
                    $('#mticketTITLE').html(title);
                    $('#mticketBODY').html(body);
                    $('#eventTicket').modal('show');
                },
                error: function(errorres) {
                    alert(errorres.responseText);
                }
            })
        }

        function removeticket(page) {
            var msg = "You are about to delete this ticket, continue ?";
            if (confirm(msg)) {
                window.location.href = page;
            }
        }
    </script>
</body>

</html>