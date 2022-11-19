<?php

error_reporting(E_ALL);
include 'includes/common.php';

if (isset(
    $_POST['first_name'],
    $_POST['last_name'],
    $_POST['c_name'],
    $_POST['phone'],
    $_POST['address'],
    $_POST['email'],
    $_POST['password1'],
    $_POST['password2'],
    $_POST['state'],
    $_POST['county_name'],
    $_POST['city'],
    $_POST['zips']
)) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $c_name = $_POST['c_name'];
    $phone = $_POST['phone'];
    $address = dbinput($_POST['address']);
    $email = $_POST['email'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $state = $_POST['state'];
    $county_name = $_POST['county_name'];
    $city = $_POST['city'];
    $zips = $_POST['zips'];
    $str1 = '';

    $state = "state IN ('" . implode("','", $state) . "')";

    $str1 .= "  AND  County_name IN ('" . implode("','", $county_name) . "')";

    $str2 = '';
    $str2 .= "  AND  city IN ('" . implode("','", $city) . "')";

    $str3 = '';
    $str3 .= "  AND  zip NOT IN ('" . implode("','", $zips) . "')";

    $Getzipq = "SELECT zip,id FROM areas WHERE " . $state . $str1 . $str2 . $str3;
    $GetzipqR = sql_query($Getzipq);
    $data = array();
    $q = "SELECT * FROM `service_providers` WHERE email_address='$email' ";
    $r = sql_query($q);
    if (sql_num_rows($r) > 0) {
        echo '<script>
                alert("Email address is already registered with quote master .Please choose another email");
                window.location.href = "new_vendor.php";
            </script>';

        //header('location:new_vendor.php');
        exit;
    }
    if ($password1 != $password2) {

        echo '<script>
                alert("Both the Password should match");
                window.location.href = "new_vendor.php";
            </script>';

        //header('location:new_vendor.php');
        exit;
    }
    $vkey = md5(time() . $first_name);
    $p = md5($password1);
    LockTable("service_providers");
    $q = "INSERT INTO `service_providers`( `dDate`,`First_name`, `Last_name`, `company_name`, `address`, `phone`, `email_address`,`password`,`email_verify_key`, `email_verify`, `approve`) VALUES (NOW(),'$first_name','$last_name','$c_name','$address','$phone','$email','$p','$vkey','0','0')";
    $result = sql_query($q);
    //echo $result . '<br>' . Lastid();
    if ($result) {
        $lastid = Lastid();
        UnLockTable();
        while ($R = sql_fetch_assoc($GetzipqR)) {
            //array_push($data, array('zip' => $R['zip'], 'id' => $R['id']));
            sql_query("INSERT INTO `service_providers_areas`( `service_providers_id`, `zip`) VALUES ('$lastid','" . $R['zip'] . "')");
        }
        echo '<script>
                alert("Regsitration Successful");
               window.location.href = "new_vendor.php";
            </script>';
    }
} elseif (isset(
    $_POST['first_name'],
    $_POST['last_name'],
    $_POST['c_name'],
    $_POST['phone'],
    $_POST['address'],
    $_POST['email'],
    $_POST['password1'],
    $_POST['password2'],
    $_POST['state'],
    $_POST['county_name'],
    $_POST['city']
)) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $c_name = $_POST['c_name'];
    $phone = $_POST['phone'];
    $address = dbinput($_POST['address']);
    $email = $_POST['email'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $state = $_POST['state'];
    $county_name = $_POST['county_name'];
    $city = $_POST['city'];

    $state = "state IN ('" . implode("','", $state) . "')";

    $str1 = '';

    $str1 .= "  AND  County_name IN ('" . implode("','", $county_name) . "')";

    $str2 = '';
    $str2 .= "  AND  city IN ('" . implode("','", $city) . "')";

    $GetZipQ = "SELECT zip,id FROM areas WHERE " . $state . $str1 . $str2;
    $GetZipQR = sql_query($GetZipQ);
    $data = array();
    $q = "SELECT * FROM `service_providers` WHERE email_address='$email' ";
    $r = sql_query($q);
    if (sql_num_rows($r) > 0) {
        echo '<script>
                alert("Email address is already registered with quote master .Please choose another email");
              window.location.href = "new_vendor.php";
            </script>';

        //header('location:new_vendor.php');
        exit;
    }
    if ($password1 != $password2) {

        echo '<script>
                alert("Both the Password should match");
                window.location.href = "new_vendor.php";
            </script>';

        //header('location:new_vendor.php');
        exit;
    }
    $vkey = md5(time() . $first_name);
    $p = md5($password1);
    LockTable("service_providers");
    $q = "INSERT INTO `service_providers`( `dDate`,`First_name`, `Last_name`, `company_name`, `address`, `phone`, `email_address`,`password`,`email_verify_key`, `email_verify`, `approve`) VALUES (NOW(),'$first_name','$last_name','$c_name','$address','$phone','$email','$p','$vkey','0','0')";
    $result = sql_query($q);
    //echo $result . '<br>' . Lastid();
    if ($result) {
        $lastid = Lastid();
        UnLockTable();
        while ($R = sql_fetch_assoc($GetZipQR)) {
            //array_push($data, array('zip' => $R['zip'], 'id' => $R['id']));
            sql_query("INSERT INTO `service_providers_areas`( `service_providers_id`, `zip`) VALUES ('$lastid','" . $R['zip'] . "')");
        }
        echo '<script>
                alert("Regsitration Successful");
               window.location.href = "new_vendor.php";
            </script>';
    }
    // while ($R = sql_fetch_assoc($r)) {
    //     array_push($data, array('zip' => $R['zip'], 'id' => $R['id']));
    // }
} elseif (isset(
    $_POST['first_name'],
    $_POST['last_name'],
    $_POST['c_name'],
    $_POST['phone'],
    $_POST['address'],
    $_POST['email'],
    $_POST['password1'],
    $_POST['password2'],
    $_POST['state'],
    $_POST['county_name']
)) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $c_name = $_POST['c_name'];
    $phone = $_POST['phone'];
    $address = dbinput($_POST['address']);
    $email = $_POST['email'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $state = $_POST['state'];
    $county_name = $_POST['county_name'];

    $state = "state IN ('" . implode("','", $state) . "')";


    $str1 = '';

    $str1 .= "  AND  County_name IN ('" . implode("','", $county_name) . "')";
    $GetZipQ = "SELECT zip,id FROM areas WHERE " . $state . $str1;
    $GetZipQR = sql_query($GetZipQ);

    $q = "SELECT * FROM `service_providers` WHERE email_address='$email' ";
    $r = sql_query($q);
    if (sql_num_rows($r) > 0) {
        echo '<script>
                alert("Email address is already registered with quote master .Please choose another email");
               
            </script>';

        //header('location:new_vendor.php');
        exit;
    }
    if ($password1 != $password2) {

        echo '<script>
                alert("Both the Password should match");
              window.location.href = "new_vendor.php";
            </script>';

        //header('location:new_vendor.php');
        exit;
    }
    $vkey = md5(time() . $first_name);
    $p = md5($password1);
    LockTable("service_providers");
    $q = "INSERT INTO `service_providers`( `dDate`,`First_name`, `Last_name`, `company_name`, `address`, `phone`, `email_address`,`password`,`email_verify_key`, `email_verify`, `approve`) VALUES (NOW(),'$first_name','$last_name','$c_name','$address','$phone','$email','$p','$vkey','0','0')";
    $result = sql_query($q);
    //echo $result . '<br>' . Lastid();
    if ($result) {
        $lastid = Lastid();
        UnLockTable();
        while ($R = sql_fetch_assoc($GetZipQR)) {
            //array_push($data, array('zip' => $R['zip'], 'id' => $R['id']));
            sql_query("INSERT INTO `service_providers_areas`( `service_providers_id`, `zip`) VALUES ('$lastid','" . $R['zip'] . "')");
        }
        echo '<script>
                alert("Regsitration Successful");
               window.location.href = "new_vendor.php";
            </script>';
    }
} elseif (isset(
    $_POST['first_name'],
    $_POST['last_name'],
    $_POST['c_name'],
    $_POST['phone'],
    $_POST['address'],
    $_POST['email'],
    $_POST['password1'],
    $_POST['password2'],
    $_POST['state']
)) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $c_name = $_POST['c_name'];
    $phone = $_POST['phone'];
    $address = dbinput($_POST['address']);
    $email = $_POST['email'];
    $password1 = $_POST['password1'];
    $password2 = $_POST['password2'];
    $state = $_POST['state'];

    $state = "state IN ('" . implode("','", $state) . "')";
    $GetZipQ = "SELECT zip,id FROM areas WHERE " . $state;
    $GetZipQR = sql_query($GetZipQ);


    $q = "SELECT * FROM `service_providers` WHERE email_address='$email' ";
    $r = sql_query($q);
    if (sql_num_rows($r) > 0) {
        echo '<script>
                alert("Email address is already registered with quote master .Please choose another email");
               
            </script>';

        //header('location:new_vendor.php');
        exit;
    }
    if ($password1 != $password2) {

        echo '<script>
                alert("Both the Password should match");
             window.location.href = "new_vendor.php";
            </script>';

        //header('location:new_vendor.php');
        exit;
    }
    $vkey = md5(time() . $first_name);
    $p = md5($password1);
    LockTable("service_providers");
    $q = "INSERT INTO `service_providers`( `dDate`,`First_name`, `Last_name`, `company_name`, `address`, `phone`, `email_address`,`password`,`email_verify_key`, `email_verify`, `approve`) VALUES (NOW(),'$first_name','$last_name','$c_name','$address','$phone','$email','$p','$vkey','0','0')";
    $result = sql_query($q);
    //echo $result . '<br>' . Lastid();
    if ($result) {
        $lastid = Lastid();
        UnLockTable();
        while ($R = sql_fetch_assoc($GetZipQR)) {
            //array_push($data, array('zip' => $R['zip'], 'id' => $R['id']));
            sql_query("INSERT INTO `service_providers_areas`( `service_providers_id`, `zip`) VALUES ('$lastid','" . $R['zip'] . "')");
        }
        echo '<script>
                alert("Regsitration Successful");
              window.location.href = "new_vendor.php";
            </script>';
    }
}
