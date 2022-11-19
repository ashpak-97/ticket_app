<?php
function GetConnected()
{
    $CON = mysqli_connect(DB_HOST, DB_USERNAME, DB_PASSWORD) or die(mysqli_error() . "<strong>ERROR CODE : </strong> COM - 66");
    mysqli_select_db($CON, DB_NAME) or die("<strong>ERROR CODE : </strong> NEW_COM - 67");  // . mysqli_error($CON)

    return $CON;
}

function NextID($f, $tbl, $base_num = '0', $cond = '')
{
    $cond_str = (trim($cond) != '') ? ' where ' . $cond : '';

    $query = "select max($f) from $tbl $cond_str";
    $result = sql_query($query, 'COM13');
    list($rid) = sql_fetch_row($result);

    if (!is_numeric($rid))
        $rid = 0;

    $rid++;

    if (!empty($base_num)) {
        $_id = GetXFromYID("select $base_num from sys_config");
        if ($rid < $_id)
            $rid = $_id;
    }

    return $rid;
}
function GetXFromYID($q)
{
    $str = false;
    $result = sql_query($q, 'COM25');

    if (sql_num_rows($result))
        list($str) = sql_fetch_row($result);

    return $str;
}

function GetXArrFromYID($q, $mode = "1")
{
    $arr = array();
    $r = sql_query($q, 'COM39');

    if (sql_num_rows($r)) {
        if ($mode == "2")
            for ($i = 0; list($x) = sql_fetch_row($r); $i++)
                $arr[$i] = $x;
        else if ($mode == "3")
            for ($i = 0; list($x, $y) = sql_fetch_row($r); $i++)
                $arr[$x] = $y;
        else if ($mode == "4")
            while ($a = sql_fetch_assoc($r))
                $arr[$a['I']] = $a;
        else
            while (list($x) = sql_fetch_row($r))
                $arr[$x] = $x;
    }

    return $arr;
}

function GetMaxRank($tbl, $cond = "", $fld = 'iRank')
{
    $cond = (strtoupper(trim($cond)) != "") ? " where " . $cond : "";

    $q = "select max($fld) from $tbl $cond";
    $r = sql_query($q, 'GEN.94');
    list($max) = sql_fetch_row($r);

    if ($max < 1)
        $max = 0;

    return ++$max;
}

function FillTreeData($selected, $ctr, $tp, $comp, $flds, $tbl, $cond, $fn = "", $class = "form-control")
{
    $display = ($tp == "COMBO" || $tp == "COMBO2") ? "" : "size=10";
    $cond = (strtoupper(trim($cond)) != 'N' && trim($cond) != '') ? " where " . $cond : "";
    $class_str = (trim($class) == "") ? "" : $class;

    $stat_fld = ($tp == "COMBO") ? ", 'A' " : ", cStatus";

    $q = "select " . $flds . ", iLevel " . $stat_fld . " from " . $tbl . $cond . " order by iRank, vName";
    $result = sql_query($q, 'GEN.112');
    $str = '<select name="' . $ctr . '" id="' . $ctr . '" class="' . $class_str . '" ' . $display . ' ' . $fn . '>' . "\n"; //

    if ($comp <> 'y' && $comp <> 'Y') {
        $str .= '<option value="0" selected> - select - </option>' . "\n";
    }

    while (list($id, $nm, $level, $stat) = sql_fetch_row($result)) {
        $stat_style = ($stat == "A" && $tp == "COMBO2") ? "" : ' style="background-color: #FFC5C5;"';
        $selected_str = (trim($selected) == trim($id)) ? "selected" : "";
        $space = GenerateSpace($level);
        $str .=  '<option value="' . $id . '" ' . $selected_str . '>' . $space . $nm . '</option>' . "\n";
    }

    $str .= '</select>' . "\n";
    return $str;
}

// function FillTreeCombo($selected, $ctr, $tp, $comp, $cond, $fn="", $class="form-control")
function FillTreeCombo($selected, $ctr, $type, $comp, $values, $fn = "", $class = "form-control", $combo_type = "KEY_VALUE") //fill the values from an array
{
    $display = ($type <> "COMBO" && $type <> "COMBO2") ? "size=10" : "";

    $str = "<select name='$ctr' id='$ctr' class='$class' $display $fn>"; //  

    if (($comp <> "y") && ($comp <> "Y")) {
        if ($comp == '0')
            $str .= "<option value='0' selected> - select - </option>\n";
        elseif ($comp == '1')
            $str .= "<option value='0' selected> - main category - </option>\n";
        elseif ($comp == '2')
            $str .= "<option value='0' selected>MM</option>\n";
        elseif ($comp == '-1')
            $str .= "<option value='-1' selected> - select - </option>\n";
        else
            $str .= "<option value='0' selected> - select - </option>\n";
    }

    if ($combo_type == "KEY_VALUE") {
        foreach ($values as $key => $V) {
            $stat_style = ($V['status'] == 'A') ? "" : ' style="background-color: #FFC5C5;"';
            $select_str = ($selected == $key) ? "selected" : "";
            $space = GenerateSpace($V['level']);
            $str .=  '<option value="' . $key . '" ' . $select_str . $stat_style . '>' . $space . $V['text'] . '</option>' . "\n";
        }
    }

    $str .= "</select>";
    return $str;
}

function FillData($selected, $ctr, $tp, $comp, $flds, $tbl, $cond, $ord, $fn = "", $class = "form-control")
{
    $display = ($tp == "COMBO" || $tp == "COMBO2") ? "" : "size=10";
    $cond = (strtoupper(trim($cond)) != 'N' && trim($cond) != '') ? " where " . $cond : "";
    $class_str = (trim($class) == "") ? "" : $class;

    $stat_fld = ($tp == "COMBO") ? ", 'A' " : ", cStatus";

    $q = "select " . $flds . $stat_fld . " from " . $tbl . $cond . " order by " . $ord;
    $result = sql_query($q, 'GEN.141');
    $str = '<select name="' . $ctr . '" id="' . $ctr . '" class="' . $class_str . '" ' . $display . ' ' . $fn . '>' . "\n"; //

    if ($comp <> 'y' && $comp <> 'Y') {
        if ($comp == '0')
            $str .= '<option value="" selected> - Select Taluka - </option>' . "\n";
        else if ($comp == '-1')
            $str .= '<option value="" selected> - Select Casino Cage - </option>' . "\n";
        else if ($comp == '-2')
            $str .= '<option value="" selected> - Select Counter - </option>' . "\n";
        else if ($comp == '-3')
            $str .= '<option value="" selected> - Select TID - </option>' . "\n";
        else if ($comp == '-4')
            $str .= '<option value="" selected> - Select Bank - </option>' . "\n";
        elseif ($comp == '-5')
            $str .= "<option value='' selected> - Select Card Type  - </option>\n";
        elseif ($comp == '-6')
            $str .= '<option value="" selected> - Select - </option>' . "\n";
        else
            $str .= '<option value="0" selected> - Select - </option>' . "\n";
    }

    while (list($id, $nm, $stat) = sql_fetch_row($result)) {
        $stat_style = ($stat == "A" && $tp == "COMBO2") ? "" : ' style="background-color: #FFC5C5;"';
        $selected_str = (trim($selected) == trim($id)) ? "selected" : "";
        $str .=  '<option value="' . $id . '" ' . $selected_str . '>' . $nm . '</option>' . "\n";
    }

    $str .= '</select>' . "\n";
    return $str;
}

function FillCombo($selected, $ctr, $type, $comp, $values, $fn = "", $class = "form-control", $combo_type = "KEY_VALUE") //fill the values from an array
{
    $display = ($type <> "COMBO" && $type <> "COMBO2") ? "size=10" : "";

    $str = "<select name='$ctr' id='$ctr' class='$class' $display $fn>"; //  

    if (($comp <> "y") && ($comp <> "Y")) {
        if ($comp == '0')
            $str .= "<option value='' selected> - Select  - </option>\n";
        elseif ($comp == '-1')
            $str .= "<option value='' selected> - Select PHC  - </option>\n";
        elseif ($comp == '-2')
            $str .= "<option value='' selected> - Select SubCenter  - </option>\n";
        elseif ($comp == '-3')
            $str .= "<option value='' selected> - Select Volunteer  - </option>\n";
        elseif ($comp == '-4')
            $str .= "<option value='' selected> - Select Doctor  - </option>\n";
        elseif ($comp == '-5')
            $str .= "<option value='' selected> - Test Status  - </option>\n";
        elseif ($comp == '-6')
            $str .= "<option value='' selected> - Select Stage  - </option>\n";
        elseif ($comp == '-7')
            $str .= "<option value='' selected> - Select Critical  - </option>\n";
        elseif ($comp == '-8')
            $str .= "<option value='' selected> - Select Status  - </option>\n";
        elseif ($comp == '-9')
            $str .= "<option value='' selected> - Select Active  - </option>\n";
        elseif ($comp == '-10')
            $str .= "<option value='' selected> - Volunteer Assigned  - </option>\n";
        elseif ($comp == '-11')
            $str .= "<option value='' selected> - Select Contacted  - </option>\n";
        elseif ($comp == '-12')
            $str .= "<option value='' selected> - Select Comorbitity  - </option>\n";
        elseif ($comp == '-13')
            $str .= "<option value='' selected> - Select Vaccine  - </option>\n";
        elseif ($comp == '-14')
            $str .= "<option value='' selected> - Select District  - </option>\n";
        elseif ($comp == '-15')
            $str .= "<option value='' selected> - Select Patient  - </option>\n";
        elseif ($comp != '')
            $str .= "<option value='' selected> - " . $comp . " - </option>\n";
        else
            $str .= "<option value='0' selected> - select - </option>\n";
    }

    if ($combo_type == "KEY_VALUE") {
        if ($type == 'COMBO2') {
            foreach ($values as $key => $V) {
                $stat_style = ($V['status'] == 'A') ? "" : ' style="background-color: #FFC5C5;"';
                $select_str = ($selected == $key) ? "selected" : "";
                $str .=  '<option value="' . $key . '" ' . $select_str . $stat_style . '>' . $V['text'] . '</option>' . "\n";
            }
        } else {
            foreach ($values as $key_val => $var) {
                $ex = explode('~', $var);
                $stat_style = '';
                if (isset($ex[1]) && $ex[1] != 'A')
                    $stat_style = ' style="background-color: #FFC5C5;"';

                $select_str = ($selected == $key_val) ? "selected" : "";
                $str .= '<option value="' . $key_val . '" ' . $select_str . $stat_style . '>' . $ex[0] . '</option>';
            }
        }
    } elseif ($combo_type == "KEY_IS_VALUE") {
        foreach ($values as $var) {
            $select_str = ($selected == $var) ? "selected" : "";
            $str .= "<option value='$var' $select_str> $var</option>";
        }
    } elseif ($combo_type == "SPLIT_FOR_KEY_VALUE") {
        foreach ($values as $var) {
            $v = explode("~", $var);
            $key = $v[0];
            $txt = $v[1];

            $select_str = ($selected == $key) ? "selected" : "";
            $str .= "<option value='$key' $select_str> $txt</option>";
        }
    }

    $str .= "</select>";
    return $str;
}

function FillMultipleData($selected_arr, $ctr, $tp, $comp, $flds, $tbl, $cond, $ord, $fn = "")
{
    $display = (!empty($tp)) ? "size=" . $tp : "";
    $cond = (strtoupper(trim($cond)) != 'N') ? " where " . $cond : "";

    $q = "select " . $flds . " from " . $tbl . $cond . " order by " . $ord;
    $result = sql_query($q, 'COM190');

    $str = "<select name='" . $ctr . "[]' multiple='multiple' id='$ctr' class='multiselect-dropdown form-control' $display $fn>\n"; //  

    if ($comp <> 'y' && $comp <> 'Y') {
        if ($comp == '0')
            $str .= "<option value='0'> -- select -- </option>\n";
        else
            $str .= "<option value='0' selected> - select one - </option>\n";
    }

    while (list($id, $nm) = sql_fetch_row($result)) {
        $selected_str = (in_array($id, $selected_arr)) ? "selected" : "";
        $str .=  "<option value='$id' $selected_str>$nm</option>\n";
    }

    $str .= "</select>\n";
    return $str;
}

function FormatDate($date_val, $flag = "A")
{
    $dt = "";
    $date_val = trim($date_val);

    if ($date_val != "" && $date_val != '0000-00-00' && $date_val != "0000-00-00 00:00:00") {
        $time_val = strtotime($date_val);

        if ($flag == "A")    $date_format = "d M";
        elseif ($flag == "B")    $date_format = "d M Y";
        elseif ($flag == "C")    $date_format = "d-m-Y h:i A";
        elseif ($flag == "D")    $date_format = "d M Y h:i A";
        elseif ($flag == "E")    $date_format = "H:i";
        elseif ($flag == "F")    $date_format = "d/m/y h:i a";
        elseif ($flag == "G")    $date_format = "d/m/Y";
        elseif ($flag == "H")    $date_format = "Y-m-d";
        elseif ($flag == "I")    $date_format = "D, F j";
        elseif ($flag == "J")    $date_format = "D, M j";
        elseif ($flag == "K")    $date_format = "d/m/y";
        elseif ($flag == "L")    $date_format = "M y";
        elseif ($flag == "M")    $date_format = "d M Y";
        elseif ($flag == "N")    $date_format = "d/m";
        elseif ($flag == "O")    $date_format = "d\<\b\\r\>M";
        elseif ($flag == "P")    $date_format = "d/m H:i";
        elseif ($flag == "Q")    $date_format = "d/M/y";
        elseif ($flag == "R")    $date_format = "m/y";
        elseif ($flag == "S")    $date_format = "Y-m";
        elseif ($flag == "T")    $date_format = "d M\<\b\\r\>D";
        elseif ($flag == "U")    $date_format = "dS F Y, H:i A";
        elseif ($flag == "V")    $date_format = "d-M-Y";
        elseif ($flag == "W")    $date_format = "d-M-Y H:i a";
        elseif ($flag == "X")    $date_format = "D, d M Y";
        elseif ($flag == "Y")    $date_format = "D, d M Y H:i a";
        elseif ($flag == "Z")    $date_format = "my";
        elseif ($flag == "1")    $date_format = "d-M-Y";
        elseif ($flag == "2")    $date_format = "d\<\s\u\p\>S\<\/\s\u\p\> F, Y";
        elseif ($flag == "3")    $date_format = "M d";
        elseif ($flag == "4")    $date_format = "h:i a";
        elseif ($flag == "5")    $date_format = "l, d F Y - H:i";
        elseif ($flag == "6")    $date_format = "dS M Y";
        elseif ($flag == "7")    $date_format = "g";
        elseif ($flag == "8")    $date_format = "i";
        elseif ($flag == "9")    $date_format = "Y";
        elseif ($flag == "10")    $date_format = "F d, Y H:i:s";
        elseif ($flag == "11")    $date_format = "d-m-Y";
        elseif ($flag == "12")    $date_format = "s";
        elseif ($flag == "13")    $date_format = "m";
        elseif ($flag == "14")    $date_format = "d.m.Y";
        elseif ($flag == "15")    $date_format = "d/m/y h:ia";
        elseif ($flag == "16") $date_format = "D, M d, H:i A";
        elseif ($flag == "17")    $date_format = "h:i";
        elseif ($flag == "18")    $date_format = "Y-m";
        elseif ($flag == "19")    $date_format = "F, d Y";
        elseif ($flag == "20")    $date_format = "F";
        elseif ($flag == "21")    $date_format = "M";
        elseif ($flag == "22")    $date_format = "m/d/Y";
        elseif ($flag == "23")    $date_format = "Ymd";
        elseif ($flag == "24")    $date_format = "d";
        else $date_format = "d/m/y";

        $dt = date($date_format, $time_val);
    }

    return $dt;
}

function GetStatusImageString($mode, $status, $id, $ajax_flag = true)
{
    $str = "";
    if ($ajax_flag) {
        if ($status == "A") $str = '<button type="button" class="btn btn-sm btn-success btn-icon btn-sm" onClick="ChangeStatus(this, \'' . $mode . '\',\'I\',\'' . $id . '\'); return false;"><i class="fa fa-check"></i></button>';
        else if ($status == 'P') $str = '<button class="btn btn-sm btn-warning btn-icon btn-sm" onClick="ChangeStatus(this, \'' . $mode . '\',\'A\',\'' . $id . '\'); return false;">P</button>';
        else if ($status == 'X') $str = '<button class="btn btn-sm btn-secondary btn-icon btn-sm" onClick="ChangeStatus(this, \'' . $mode . '\',\'A\',\'' . $id . '\'); return false;">E</button>';
        else $str = '<button class="btn btn-sm btn-danger btn-icon btn-sm" onClick="ChangeStatus(this, \'' . $mode . '\',\'A\',\'' . $id . '\'); return false;"><i class="fa fa-times"></i></button>';
    } else {
        if ($status == "A") $str = '<span class="text-success"><i class="fa fa-check"></i></span>';
        else if ($status == 'P') $str = '<span class="text-warning">P</span>';
        else if ($status == 'X') $str = '<span class="text-secondary">E</span>';
        else $str = '<span class="text-danger"><i class="fa fa-times"></i></span>';
    }


    return $str;
}

function GetYesNoImageString($mode, $status, $id, $ajax_flag = true)
{
    $str = "";
    if ($ajax_flag) {
        if ($status == "Y") $str = '<a onClick="ChangeYesNoStatus(this, \'' . $mode . '\',\'N\',\'' . $id . '\'); return false;">' . YES_IMG . '</a>';
        else $str = '<a onClick="ChangeYesNoStatus(this, \'' . $mode . '\',\'Y\',\'' . $id . '\'); return false;">' . NO_IMG . '</a>';
    } else {
        if ($status == "Y") $str = YES_IMG;
        else $str = NO_IMG;
    }

    return $str;
}

function GetFeaturedImageString($mode, $status, $id, $ajax_flag = true)
{
    $str = "";
    if ($ajax_flag) {

        if ($status == "Y") $str = '<a style="cursor:pointer;" onClick="ChangeFeatured(this, \'' . $mode . '\',\'N\',\'' . $id . '\'); return false;">' . FEATURED_IMG . '</a>';
        else $str = '<a style="cursor:pointer;" onClick="ChangeFeatured(this, \'' . $mode . '\',\'Y\',\'' . $id . '\'); return false;">' . UNFEATURED_IMG . '</a>';
    } else {
        if ($status == "Y") $str = FEATURED_IMG;
        else $str = UNFEATURED_IMG;
    }

    return $str;
}

function IsExistFile($file, $path)
{
    $file = trim($file);
    $path = trim($path);

    if (($file != "") && (strtoupper($file) != "NA")) {
        $f = $path . $file;
        //echo($f);
        //exit;
        if (file_exists($f))
            return 1;
    }

    return 0;
}

function DeleteFile($file, $path)
{
    $file = trim($file);
    $path = trim($path);

    if (($file != "") && (strtoupper($file) != "NA")) {
        $f = $path . $file;
        if (file_exists($f))
            unlink($f);
    }
}

function DisplayFormattedArray($arr)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

function NormalizeFilename($filename, $newname = "")
{
    $filename = trim($filename);
    $pos = strrpos($filename, ".");
    $str_nm = (trim($newname != "")) ? $newname : substr($filename, 0, $pos);
    $str_ext = substr($filename, $pos);

    $invalid_chars = array('`', '=', ' ', '\\', '[', ']', ';', '\'', ',', '/', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '|', '{', '}', ':', '\"', '<', '>', '?');

    foreach ($invalid_chars as $I)
        $str_nm = str_replace($I, "-", $str_nm);

    $str_nm .= $str_ext;
    return $str_nm;
}

function ConvertFromYMDtoDMY($ymd_date, $tmode = false)
{
    $year = $mnth = $days = $hour = $mins = $secs = 0;

    if (trim($ymd_date) == "") return $ymd_date;
    elseif ($ymd_date == "0000-00-00 00:00:00" || $ymd_date == "0000-00-00") return "";

    if ($tmode) // time also included
    {
        $t_arr = explode(' ', $ymd_date);
        if (count($t_arr) < 2) return "";

        $ymd_date = $t_arr[0];
        $time_str = $t_arr[1];

        $tm_arr = explode(':', $time_str);
        if (count($tm_arr) < 3) return "";

        $hour = $tm_arr[0];
        $mins = $tm_arr[1];
        $secs = $tm_arr[2];
    }

    $dt_arr = explode('-', $ymd_date);
    if (count($dt_arr) < 3) return "";

    $year = $dt_arr[0];
    $mnth = $dt_arr[1];
    $days = $dt_arr[2];

    if ($tmode)
        $dmy_date = $days . "-" . $mnth . "-" . $year . " " . $hour . ":" . $mins . ":" . $secs;
    else
        $dmy_date = $days . "-" . $mnth . "-" . $year;

    return $dmy_date;
}

function ConvertFromDMYToYMD($dmy_date, $tmode = false)
{
    $year = $mnth = $days = $hour = $mins = $secs = 0;

    if (trim($dmy_date) == "") return $dmy_date;
    elseif ($dmy_date == "0000-00-00 00:00:00" || $dmy_date == "0000-00-00") return "";

    if ($tmode) // time also included
    {
        $t_arr = explode(' ', $dmy_date);
        if (count($t_arr) < 2) return "";

        $dmy_date = $t_arr[0];
        $time_str = $t_arr[1];

        $tm_arr = explode(':', $time_str);
        if (count($tm_arr) < 3) return "";

        $hour = $tm_arr[0];
        $mins = $tm_arr[1];
        $secs = $tm_arr[2];
    }

    $dt_arr = explode('-', $dmy_date);
    if (count($dt_arr) < 3) return "";

    $days = $dt_arr[0];
    $mnth = $dt_arr[1];
    $year = $dt_arr[2];

    if ($tmode)
        $ymd_date = $year . "-" . $mnth . "-" . $days . " " . $hour . ":" . $mins . ":" . $secs;
    else
        $ymd_date = $year . "-" . $mnth . "-" . $days;

    return $ymd_date;
}

function ParseStringForSQL($sqlstr)
{
    $tmp_str = trim($sqlstr);
    $tmp_str = stripslashes($tmp_str);
    $tmp_str = str_replace("'", "''", $tmp_str);
    $tmp_str = str_replace('\\', '\\\\', $tmp_str);
    return $tmp_str;
}

function CheckForXSS($string)
{
    $str = '';
    $x = array('onblur', 'onchange', 'onclick', 'ondblclick', 'onfocus', 'onkeydown', 'onkeypress', 'onkeyup', 'onmousedown', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onreset', 'onselect', 'onsubmit');
    $str = str_replace($x, "", $string);

    return $str;
}
function db_input($string)
{
    $string = CheckForXSS($string);
    //return (get_magic_quotes_gpc())? htmlspecialchars(addslashes($string)): htmlspecialchars(addslashes($string));
    return htmlspecialchars(addslashes($string));
}

function db_input2($string)
{
    $string = CheckForXSS($string);
    //return (get_magic_quotes_gpc())? htmlspecialchars(addslashes($string)): htmlspecialchars(addslashes($string));
    return htmlspecialchars(addslashes($string));
}

function db_output($string)
{
    $string = trim($string);
    return htmlspecialchars($string, ENT_QUOTES);
}

function db_output2($string)
{
    $string = trim($string);
    //if(!get_magic_quotes_gpc()) $string = stripslashes($string);
    $string = stripslashes($string);
    return htmlspecialchars_decode($string, ENT_QUOTES);
}

function Delay($number = 100)
{
    for ($i = 0; $i < $number; $i++);
}

function FormatNumber($number, $pad_len = 0, $mode = 'ind') // int: international; ind: indian
{
    $sign = ($number < 0) ? '-' : '';
    $number = abs($number);

    if ($mode == 'ind')
        $number = exp_to_dec($number);

    $dot =     strrpos($number, ".");
    $int_buffer = array();

    if ($dot === false) {
        $int_part  = $number;
        $deci_part = "";
    } else {
        $int_part = substr($number, 0, $dot);
        $deci_part = substr($number, $dot + 1);
    }

    // echo '['.$mode . ' ' .$int_part.' . '.$deci_part.']<br>';

    if ($pad_len > 0) {
        if ($deci_part != '') {
            $deci_part_str = round('0.' . $deci_part, $pad_len);

            if ($deci_part_str >= 1) // decimals have rounded up to 1 => increment integer part...
                $int_part++;

            $deci_part = substr($deci_part_str, 2); // , $dot+1);
        }
    }

    if ($mode == 'ind') {
        $len = strlen($int_part);
        for ($i = $len - 1; $i >= 0; $i--)
            $int_buffer[$i] = substr($int_part, $i, 1);

        $i = 0;
        $int_part = "";
        foreach ($int_buffer as $digit) {
            $int_part = (($i == 3) || ($i == 5) || ($i == 7) || ($i == 9)) ? $digit . "," . $int_part : $int_part = $digit . $int_part;
            $i++;
        }
    } else if ($mode == 'int') {
        $int_part = number_format($int_part);
    }

    $number = $int_part;

    if ($pad_len > 0)
        $number .=  "." . str_pad($deci_part, $pad_len, "0");

    return $sign . $number;
}

// formats a floating point number string in decimal notation, supports signed floats, also supports non-standard formatting e.g. 0.2e+2 for 20
// e.g. '1.6E+6' to '1600000', '-4.566e-12' to '-0.000000000004566', '+34e+10' to '340000000000'
// Author: Bob
function exp_to_dec($float_str)
{
    // make sure its a standard php float string (i.e. change 0.2e+2 to 20)
    // php will automatically format floats decimally if they are within a certain range
    $float_str = (string)((float)($float_str));

    // if there is an E in the float string
    if (($pos = strpos(strtolower($float_str), 'e')) !== false) {
        // get either side of the E, e.g. 1.6E+6 => exp E+6, num 1.6
        $exp = substr($float_str, $pos + 1);
        $num = substr($float_str, 0, $pos);

        // strip off num sign, if there is one, and leave it off if its + (not required)
        if ((($num_sign = $num[0]) === '+') || ($num_sign === '-')) $num = substr($num, 1);
        else $num_sign = '';
        if ($num_sign === '+') $num_sign = '';

        // strip off exponential sign ('+' or '-' as in 'E+6') if there is one, otherwise throw error, e.g. E+6 => '+'
        if ((($exp_sign = $exp[0]) === '+') || ($exp_sign === '-')) $exp = substr($exp, 1);
        else trigger_error("Could not convert exponential notation to decimal notation: invalid float string '$float_str'", E_USER_ERROR);

        // get the number of decimal places to the right of the decimal point (or 0 if there is no dec point), e.g., 1.6 => 1
        $right_dec_places = (($dec_pos = strpos($num, '.')) === false) ? 0 : strlen(substr($num, $dec_pos + 1));
        // get the number of decimal places to the left of the decimal point (or the length of the entire num if there is no dec point), e.g. 1.6 => 1
        $left_dec_places = ($dec_pos === false) ? strlen($num) : strlen(substr($num, 0, $dec_pos));

        // work out number of zeros from exp, exp sign and dec places, e.g. exp 6, exp sign +, dec places 1 => num zeros 5
        if ($exp_sign === '+') $num_zeros = $exp - $right_dec_places;
        else $num_zeros = $exp - $left_dec_places;

        // build a string with $num_zeros zeros, e.g. '0' 5 times => '00000'
        $zeros = str_pad('', $num_zeros, '0');

        // strip decimal from num, e.g. 1.6 => 16
        if ($dec_pos !== false) $num = str_replace('.', '', $num);

        // if positive exponent, return like 1600000
        if ($exp_sign === '+') return $num_sign . $num . $zeros;
        // if negative exponent, return like 0.0000016
        else return $num_sign . '0.' . $zeros . $num;
    }
    // otherwise, assume already in decimal notation and return
    else return $float_str;
}

function DateDiff($date1, $date2, $mode = '1')
{
    list($yr1, $mnt1, $day1) = explode('-', $date1);
    $xx = gmmktime(0, 0, 0, $mnt1, $day1, $yr1);

    list($yr2, $mnt2, $day2) = explode('-', $date2);
    $xy = gmmktime(0, 0, 0, $mnt2, $day2, $yr2);

    $diff = $xy - $xx;
    $min = $diff / 60;
    $hr = $min / 60;
    $day = $hr / 24;

    if ($mode == '2') {
        $month_arr = array(1 => 31, 2 => 28, 3 => 31, 4 => 30, 5 => 31, 6 => 30, 7 => 31, 8 => 31, 9 => 30, 10 => 31, 11 => 30, 12 => 31);
        $y = $yr2 - $yr1;
        $m = $mnt2 - $mnt1;

        if ($m < 0) {
            $m = 12 + $m;
            $y -= 1;
        }

        $d = $day2 - $day1;

        if ($d < 0) {
            $mnt1 = ltrim($mnt1, '0');

            $_adj = $month_arr[$mnt1];
            if ($mnt1 == 2 && IsLeapYear($y1)) // if start month is Feb and is a Leap Year
                $_adj += 1;

            $d = $_adj + $d;
            $m -= 1;
        }

        $y_txt = ($y) ? ($y > 1) ? $y . " yr " : "1 yr " : "";
        $m_txt = ($m) ? ($m > 1) ? $m . " mnths " : "1 mnth " : "";
        $d_txt = ($d) ? ($d > 1) ? $d . " days " : "1 day " : "";

        if ($y_txt != '')
            $m_txt = "," . $m_txt;

        if ($m_txt != '')
            $d_txt = "," . $d_txt;


        $ret_val = $y_txt . $m_txt . $d_txt;
    } else
        $ret_val = $day;

    return $ret_val;
}

function IsLeapYear($yr)
{
    return ($yr % 4 == 0 && $yr % 100 != 0) ? true : false;
}

function DateTimeAdd($date, $dd = 0, $mm = 0, $yy = 0, $hh = 0, $nn = 0, $ss = 0, $format = "Y-m-d H:i:s")
{
    $d = date("Y-m-d H:i:s", strtotime($date));

    //echo $d . " ($dd, $mm, $yy) <br>";	
    $t_arr = explode(' ', $d);
    $date_str = $t_arr[0];
    $time_str = $t_arr[1];

    $tm_arr = explode(':', $time_str);
    $hour = $tm_arr[0];
    $mins = $tm_arr[1];
    $secs = $tm_arr[2];

    $dt_arr = explode('-', $date_str);
    $year = $dt_arr[0];
    $mnth = $dt_arr[1];
    $days = $dt_arr[2];

    //	echo "mktime($hour, $mins, $secs, ($mnth + $mm), ($days + $dd), ($year + $yy)) <br>";
    $t = mktime(($hour + $hh), ($mins + $nn), ($secs + $ss), ($mnth + $mm), ($days + $dd), ($year + $yy));

    if (empty($format)) $format = "Y-m-d H:i:s";
    $date = date($format, $t);

    return $date;
}

function NewlinesToBR($str, $replace_str = '<br />')
{
    return preg_replace("/(\r\n)+|(\n|\r)+/", $replace_str, $str);
}

function GetRelativeDateDesc($date)
{
    $str = 'Today';
    $d = DateDiff($date, TODAY);

    if ($d == 1) $str = 'yesterday';
    else if ($d > 1) $str = $d . ' days ago';
    else if ($d == -1) $str = 'tomorrow';
    else if ($d < -1) $str = abs($d) . ' days ahead';

    return $str;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
function check_inject($sql_in)
{
    if (strstr($sql_in, '/*') || strstr($sql_in, '--') || stristr($sql_in, '<script>') || stristr($sql_in, '</script>'))
        return false;

    return true;
}

function shuffle_assoc(&$array)
{
    if (count($array) > 1) //$keys needs to be an array, no need to shuffle 1 item anyway
    {
        $keys = array_rand($array, count($array));

        foreach ($keys as $key)
            $new[$key] = $array[$key];

        $array = $new;
    }

    return true; //because it's a wannabe shuffle(), which returns true
}

function input_check_mailinj($value)
{
    # mail adress(ess) for reports...
    //$report_to = "noreply@goenchobalcao.com";

    # array holding strings to check...
    $suspicious_str = array("content-type:", "charset=", "mime-version:", "content-transfer-encoding:", "multipart/mixed", "bcc:");

    // remove added slashes from $value...
    $value = stripslashes($value);

    foreach ($suspicious_str as $suspect) {
        # checks if $value contains $suspect...
        if (eregi($suspect, strtolower($value))) {
            $ip = (empty($_SERVER['REMOTE_ADDR'])) ? 'empty' : $_SERVER['REMOTE_ADDR']; // replace this with your own get_ip function...
            $rf = (empty($_SERVER['HTTP_REFERER'])) ? 'empty' : $_SERVER['HTTP_REFERER'];
            $ua = (empty($_SERVER['HTTP_USER_AGENT'])) ? 'empty' : $_SERVER['HTTP_USER_AGENT'];
            $ru = (empty($_SERVER['REQUEST_URI'])) ? 'empty' : $_SERVER['REQUEST_URI'];
            $rm = (empty($_SERVER['REQUEST_METHOD'])) ? 'empty' : $_SERVER['REQUEST_METHOD'];

            die('Script processing cancelled: Your request contains text portions that are ' .
                'potentially harmful to this server. <em>Your input has not been sent!</em> Please use your ' .
                'browser\'s `back`-button to return to the previous page and try refreshing your input.</p>');
        }
    }
}

function CheckSPAM($string)
{
    /* $len=strlen($string);
	$tmp = "";

	for($i=0;$i<=$len;$i++)
	{
		$c=substr($string,$i,1);
		if( (ord($c)>=0 && ord($c)<=127) || ord($c)==156) $tmp .= $c;
		elseif(ord($c)==146)	$tmp .= chr(39);
		else	return 0;
	}

	return $tmp; */

    $len = strlen($string);

    for ($i = 0; $i <= $len; $i++) {
        $c = substr($string, $i, 1);
        if ((ord($c) >= 0 && ord($c) <= 127) || ord($c) == 156) {
        } else
            return false;
    }
    return true;
}

function GetFolderFileArr($DIR_UPLOAD, $DIR_PATH, $mode = 0)
{
    $image_arr = array();

    $dir_resource = opendir($DIR_UPLOAD);

    for ($i = 0; $file_name = readdir($dir_resource);)
        if (($file_name != ".") && ($file_name != "..") && (strtolower($file_name) != "thumbs.db") && file_exists($DIR_UPLOAD . $file_name))
            $image_arr[$i++] = $file_name;

    closedir($dir_resource);

    return $image_arr;
}

function EnsureValidMode($mode, $valid_modes, $default_mode)
{
    if (empty($mode) || !in_array($mode, $valid_modes))
        $mode = $default_mode;

    return $mode;
}

function Str2Arr($str)
{
    $arr = array();

    for ($i = 0; $i < strlen($str); $i++)
        $arr[$i] = substr($str, $i, 1);

    return $arr;
}

function GetFileName($filedir)
{
    $dir = opendir($filedir);

    while ($file_name = readdir($dir))
        if ($file_name != "." && $file_name != "..")
            return $file_name;
}

function ValidateNumber($num, $default = '0')
{
    if (!is_numeric($num))
        $num = $default;

    return $num;
}

function th($number, $flag = "")
{
    $suffix = "";

    $last_digit = substr($number, -1);

    if ($last_digit == "1")
        $suffix = "st";
    elseif ($last_digit == "2")
        $suffix = "nd";
    elseif ($last_digit == "3")
        $suffix = "rd";
    else
        $suffix = "th";

    if ($flag == "A")
        $suffix = "<sup>" . $suffix . "</sup>";

    return $number . $suffix;
}

function DownloadFile($PATH, $UPLOAD, $file_name)
{
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Disposition: attachment; filename=" . basename($PATH . $file_name) . ";");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: " . filesize($UPLOAD . $file_name));
    readfile($PATH . $file_name);
    exit();
}

function SetSessionInfo($str)
{
    global $sess_info, $lbl_display;
    $sess_info = $str;
    $lbl_display = ($sess_info != "") ? '' : 'none';
}

function WriteFile($file_name, $file_str)
{
    $handle = fopen(PRINT_UPLOAD . $file_name, 'w+');
    fwrite($handle, $file_str);
    fclose($handle);
}

function PrintMultiLine($str, $contd = 1, $limit = 40)
{
    $print_str = '';
    $str = trim($str);

    if ($str != '') {
        $len = strlen($str);
        $x_str = ($len > $limit) ? substr($str, 0, $limit) : $str;
        $print_str = $x_str . NEWLINE;


        if ($contd) {
            $limit -= 3;
            $contd_str = ' ..';
        } else
            $contd_str = '';

        if ($len > $limit)
            for ($a = $limit + 3; $a < $len; $a += $limit)
                $print_str .= $contd_str . substr($str, $a, $limit) . NEWLINE;
    }

    return $print_str;
}

function SearchFromMemory($flag, $disp_url)
{
    global $_SESSION;
    $url_str = $disp_url;

    if (isset($_SESSION[PROJ_SESSION_ID]->srch_ctrl_arr[$flag])) {
        $srch_ctrl_arr = $_SESSION[PROJ_SESSION_ID]->srch_ctrl_arr[$flag];
        $url_str = $disp_url . "?srch_mode=QUERY";

        foreach ($srch_ctrl_arr as $ctrl_nm => $ctrl_val) {
            if ($ctrl_nm == "srch_mode" || $ctrl_nm == "FORM")
                continue;

            $url_str .= "&" . $ctrl_nm . "=" . $ctrl_val;
        }
    }

    header("location: " . $url_str);
    exit;
}

function GenerateSQLInsert($tbl, $q)
{
    $str = '';

    $r = sql_query($q, 'COM.2687');
    for ($i = 1; $assoc = sql_fetch_assoc($r); $i++) {
        $str .= 'insert into ' . $tbl . ' values (';

        $fld_i = 0;
        foreach ($assoc as $val) {
            if ($fld_i++) $str .= ',';
            $str .= '"' . db_input($val) . '"';
        }

        $str .= ');' . NEWLINE;
    }

    return $str;
}

function DFA($arr)
{
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}

function EnsureValueUBound($val, $ubound = 9999)
{
    $val = floatval($val);

    if ($val > $ubound)
        $val = 0; // $ubound;

    return $val;
}

function TimeDiff($dt1, $dt2, $timestamp = false, $mode = 'm')
{
    if (!$timestamp) {
        $t1 = strtotime($dt1);
        $t2 = strtotime($dt2);
    } else {
        $t1 = $dt1;
        $t2 = $dt2;
    }

    $secs = $t1 - $t2;

    if ($mode == 's')
        $x = $secs;
    else if ($mode == 'm') {
        $mins = $secs / 60;
        $x = $mins;
    } else if ($mode == 'h') {
        $mins = $secs / 60;
        $hrs = $mins / 60;
        $x = $hrs;
    } else if ($mode == 'd') {
        $mins = $secs / 60;
        $hrs = $mins / 60;
        $day = $hrs / 24;
        $x = $day;
    }

    return $x;
}

function format_uptime($seconds)
{
    $secs = intval($seconds % 60);
    $mins = intval($seconds / 60 % 60);
    $hours = intval($seconds / 3600 % 24);
    $days = intval($seconds / 86400);
    $uptimeString = '';

    if ($days > 0) {
        $uptimeString .= $days;
        $uptimeString .= (($days == 1) ? " day" : " days");
    }

    if ($hours > 0) {
        $uptimeString .= (($days > 0) ? ", " : "") . $hours;
        $uptimeString .= (($hours == 1) ? " hour" : " hours");
    }

    if ($mins > 0) {
        $uptimeString .= (($days > 0 || $hours > 0) ? ", " : "") . $mins;
        $uptimeString .= (($mins == 1) ? " minute" : " minutes");
    }

    if ($secs > 0) {
        $uptimeString .= (($days > 0 || $hours > 0 || $mins > 0) ? ", " : "") . $secs;
        $uptimeString .= (($secs == 1) ? " second" : " seconds");
    }

    return $uptimeString;
}

function ClearFolder($filedir)
{
    $dir = opendir($filedir);

    while ($file_name = readdir($dir))
        if ($file_name != "." && $file_name != "..")
            unlink($filedir . $file_name);
}

function BackupDB($file_name)
{
    DeletePDF(BACKUP_UPLOAD);
    system('mysqldump -u' . DB_USERNAME . ' -p' . DB_PASSWORD . ' ' . DB_NAME . ' | gzip > ' . BACKUP_UPLOAD . $file_name, $done);
}

function CheckSqlTables(&$msg, $fast = true, $return_text = true)
{
    global $table_count;
    $is_corrupted = false;
    $msg = "";

    $q = "show tables";
    $r = sql_query($q, 'UTL_CD.77');
    $table_count = sql_num_rows($r);
    if ((!$r || $table_count <= 0) && $return_text)
        $msg = '<tr><td colspan="5" class="err">Could not iterate database tables</td></tr>';

    $checktype = "";
    if ($fast)
        $checktype = "FAST";

    for ($i = 1; list($table_name) = sql_fetch_row($r); $i++) {
        $q1 = "check table $table_name $checktype";
        $r1 = sql_query($q1, 'UTL_CD.92');

        if ((!$r1 || sql_num_rows($r1) <= 0) && $return_text) {
            $msg = '<tr><td colspan="5" class="err">Could not status for table ' . $table_name . '</td></tr>';
            continue;
        }

        # Seek to last row
        mysql_data_seek($r1, sql_num_rows($r1) - 1);
        $a = sql_fetch_assoc($r1);

        $chk_str = '&nbsp;';
        $css = '';
        if ($a['Msg_type'] != "status") {
            $css = 'red';
            $chk_str = '<input type="checkbox" name="chk[]" id="chk_' . $i . '" value="' . $table_name . '">';
            $is_corrupted = true;
        }

        if ($return_text) {
            $msg .= '<tr>';
            $msg .= '<td align="right" class="' . $css . '">' . $i . '.</td>';
            $msg .= '<td align="center" class="' . $css . '">' . $chk_str . '</td>';
            $msg .= '<td class="' . $css . '"><label for="chk_' . $i . '">' . $table_name . '</label></td>';
            $msg .= '<td align="center" class="' . $css . '">' . $a['Msg_type'] . '</td>';
            $msg .= '<td class="' . $css . '">' . $a['Msg_text'] . '</td>';
            $msg .= '</tr>';
        }
    }

    return $is_corrupted;
}

function ForceOut($err = false)
{
    $str = ($err === false) ? '' : '?err=' . $err;
    session_destroy(); // destroy all data in session
    header("location:index.php" . $str);
    exit;
}

function ForceOut2($err = false)
{
    $str = ($err === false) ? '' : '?err=' . $err;
    session_destroy(); // destroy all data in session
    header("location:login.php" . $str);
    exit;
}

function ForceOut3($err = false)
{
    $str = ($err === false) ? '' : '?err=' . $err;
    session_destroy(); // destroy all data in session
    header("location:authorise.php" . $str);
    exit;
}

function ForceOutFront($err = false, $return_url = 'register.php')
{
    if (strpos($return_url, "?")) $str = ''; //($err===false)? '': '&err='.$err;
    else $str = ''; //($err===false)? '': '?err='.$err;

    unset($_SESSION[PROJ_FRONT_SESSION_ID]);
    //session_destroy(); // destroy all data in session
    header("location:" . $return_url . $str);
    exit;
}

function Passwordify($password)
{
    $passarr = array();
    $passarr[0] = substr($password, 0, 8);
    $passarr[1] = substr($password, 8, 8);
    $passarr[2] = substr($password, 16, 8);
    $passarr[3] = substr($password, 24, 8);
    $passarr[4] = substr($password, 32, 8);
    $passarr[5] = substr($password, 40, 8);
    $passarr[6] = substr($password, 48, 8);
    $passarr[7] = substr($password, 56, 8);
    $passarr[8] = substr($password, 64, 4);

    $ptrarr = array();
    $ptrarr[0] = substr($passarr[8], 0, 1);
    $ptrarr[1] = substr($passarr[8], 1, 1);
    $ptrarr[2] = substr($passarr[8], 2, 1);
    $ptrarr[3] = substr($passarr[8], 3, 1);

    $k = 1;
    $genpass = array();
    foreach ($ptrarr as $key => $value) {
        $genpass[$value] = $passarr[$key + $k];
        $k++;
    }

    $password = $genpass[1] . $genpass[2] . $genpass[3] . $genpass[4];

    return $password;
}

function LogAttempt($user_name, $log_type, $fail_str)
{
    $now = NOW;
    $hostaddress = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    $log_str = ($log_type == 'S') ? 'Login Successful' : 'Login Failed';

    $q = "insert into log_signin values('$user_name', '$now', '$hostaddress', '" . session_id() . "', '$log_str', '$fail_str', '$log_type')";
    $r = sql_query($q, 'GNC.216');
}

function ChangeStatus($url, $id, $status, $desc_str, $tbl, $pk_fld)
{
    $result = false;

    $q = 'update ' . $tbl . ' set cStatus=\'' . $status . '\' where ' . $pk_fld . '=' . $id;
    $r = sql_query($q, 'GEN.1085');

    if (true) // sql_affected_rows())
    {
        $rstr = ($status == 'A') ? $desc_str . " record has been activated" : $desc_str . " record has been blocked";
        $str = GetStatusImageString($url, $status, $id);
        $result = "1~$str~$rstr";
    }

    return $result;
}

function UpdateLog($file_name, $str)
{
    $handle = fopen(PRINT_UPLOAD . $file_name, 'a');
    fwrite($handle, $str);
    fclose($handle);    // *	
}

function GetIDString($q)
{
    $arr = array();
    $arr[0] = 0;

    $r = sql_query($q, 'GEN.1494');
    while (list($val) = sql_fetch_row($r))
        $arr[$val] = $val;

    return implode(',', $arr);
}

function GetIDString2($q, $separator = ", ")
{
    $arr = array();

    $r = sql_query($q, 'GEN.1494');
    while (list($val) = sql_fetch_row($r))
        $arr[$val] = $val;

    return implode($separator, $arr);
}

function SetupCalendar($date_val, $txt_ctrl, $date_type = 'D', $clr_flag = true, $txt_flag = false)
{
    $btn_ctrl = 'btn' . substr($txt_ctrl, 3);
    $clr_ctrl = 'clr' . substr($txt_ctrl, 3);

    if ($txt_flag) {
        $btn_ctrl = $txt_ctrl;
    }

    if ($date_type == 'DT') // datetime...
    {
        $format = '%d-%m-%Y %H:%M:00';
        $showtime = 'true';
        $css = 'datetime';
    } else {
        $format = '%d-%m-%Y';
        $showtime = 'false';
        $css = 'date';
    }

    $str = '';
    $str .= '<input type="text" name="' . $txt_ctrl . '" id="' . $txt_ctrl . '" value="' . $date_val . '" class="' . $css . ' box" readonly />';

    if (!$txt_flag)
        $str .= '<input name="' . $btn_ctrl . '" type="button" id="' . $btn_ctrl . '" value="..." class="date box">';

    $str .= '<script type="text/javascript">';
    $str .= 'Calendar.setup({inputField:"' . $txt_ctrl . '",ifFormat:"' . $format . '",showsTime:' . $showtime . ',button:"' . $btn_ctrl . '",singleClick:true,step:2});';
    $str .= '</script>';

    if ($clr_flag && !$txt_flag)
        $str .= '<input type="button" name="' . $clr_ctrl . '" id="' . $clr_ctrl . '" value="!" class="date box" onClick="this.form.' . $txt_ctrl . '.value=\'\';">';

    return $str;
}

function ParseID($id)
{
    return (is_numeric($id) && !empty($id)) ? $id : '0';
}

function FillRadioData($selected, $ctrl, $q, $comp = 'Y', $fn_str = "")
{
    $str = '';

    $xtra_arr = array();
    if ($comp <> 'y' && $comp <> 'Y') {
        //		if($comp=='0')
        $xtra_arr['0'] = 'NA';
    }

    foreach ($xtra_arr as $key => $txt) {
        $ctrl_id = $ctrl . '_' . strtolower($key);
        $chk_str = ($key == $selected) ? 'checked' : '';
        $str .= '<input type="radio" name="' . $ctrl . '" id="' . $ctrl_id . '" value="' . $key . '" ' . $chk_str . ' ' . $fn_str . '><label class="label0" for="' . $ctrl_id . '">' . $txt . '</label>';
    }

    $r = sql_query($q, 'COM.1573');

    while (list($key, $txt) = sql_fetch_row($r)) {
        $ctrl_id = $ctrl . '_' . strtolower($key);
        $chk_str = ($key == $selected) ? 'checked' : '';
        $str .= '<input type="radio" name="' . $ctrl . '" id="' . $ctrl_id . '" value="' . $key . '" ' . $chk_str . ' ' . $fn_str . '><label class="label0" for="' . $ctrl_id . '">' . $txt . '</label>';
    }

    return $str;
}

function empty_date($dt)
{
    $dt = trim($dt);
    return (empty($dt) || $dt == '0000-00-00' || $dt == '0000-00-00 00:00:00') ? true : false;
}

function SetCode($x_name, $mode = 'A', $len = 3)
{
    $x_code = '';
    $x_name = trim($x_name);

    if ($mode == 'B') // acronym
    {
        $arr = explode(' ', $x_name); //.split(' ');
        for ($i = 0; ($i < count($arr) && $i < $len); $i++)
            $x_code .= substr($arr[$i], 0, 1);
    } else {
        $x_name_len = strlen($x_name);

        if ($x_name_len > 0)
            $x_code = ($x_name_len > $len) ? $x_name . substr(0, $len) : $x_name;
    }

    return strtoupper($x_code);
}

function CHK_ARR2Str($chk_arr)
{
    $str = '';

    if (count($chk_arr)) {
        foreach ($chk_arr as $x_str => $x_count)
            $str .= ', ' . $x_str;

        $str = substr($str, 2);
    }

    return $str;
}

function FillRadiosYN($is_selected, $ctrl, $yes_str = 'Yes', $no_str = 'No', $width = 90, $fn_str = '')
{
    $chk_str = ($is_selected) ? 'checked' : '';

    $str = '<div class="onoffswitch" style="width:' . $width . 'px;">';
    $str .= '<input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch" ' . $chk_str . '>';
    $str .= '<label class="onoffswitch-label" for="myonoffswitch">';
    $str .= '<div class="onoffswitch-inner"><div class="onoffswitch-active">' . $yes_str . '</div><div class="onoffswitch-inactive">' . $no_str . '</div></div>';
    $str .= '<div class="onoffswitch-switch" style="right:' . ($width - 32) . 'px;"></div>';
    $str .= '</label>';
    $str .= '</div>';

    return $str;
}

function GetPriorityImg($priority)
{
    global $PRIORITY_ARR;
    if (!isset($PRIORITY_ARR[$priority])) $priority = '4';
    return '<img src="./images/icons/priority' . $priority . '.png" align="absmiddle" title="' . $PRIORITY_ARR[$priority] . '" />';
}

function FillTime($selected, $ctr, $comp = 'n', $fn = "", $class = "", $mode = "12") //fill the values from an array
{
    $str = "<select name='$ctr' id='$ctr' class='$class' $fn>"; //  

    if (strtolower($comp) != 'y')
        $str .= "<option value='00:00:00' selected>hh:mm</option>\n";

    if ($mode == "24") {
        for ($i = 0; $i < 24; $i++) {
            $hh = ($i < 10) ? '0' . $i : $i;

            for ($j = 0; $j < 60; $j = $j + 30) {
                $mm = ($j < 10) ? '0' . $j : $j;

                $var = $hh . ':' . $mm;
                $select_str = ($selected == $var) ? "selected" : "";
                $str .= '<option value="' . $var . ':00" ' . $select_str . '>' . $var . '</option>';
            }
        }
    } else {
        for ($a = 5; $a <= 22; $a++) {
            $suffix = ($a >= 12) ? 'pm' : 'am';
            $i = ($a > 12) ? $a - 12 : $a;
            $hh = ($i < 10) ? '0' . $i : $i;
            $hhx = ($a < 10) ? '0' . $a : $a;

            for ($j = 0; $j < 60; $j = $j + 30) {
                $mm = ($j < 10) ? '0' . $j : $j;

                $var = $hh . ':' . $mm;
                $varx = $hhx . ':' . $mm . ':00';
                $select_str = ($selected == $varx) ? "selected" : "";
                // $str .= "<option value='$var' $select_str>$var $suffix</option>";
                $str .= '<option value="' . $varx . '" ' . $select_str . '>' . $var . ' ' . $suffix . '</option>';
            }
        }
    }

    $str .= "</select>";
    return $str;
}

function FillCheckboxList($selected_arr, $ctrl, $value_arr, $mode = '1')
{
    $str = '';

    foreach ($value_arr as $key => $txt) {
        $key = strval($key);
        $ctrl_id = str_replace('[]', '', $ctrl) . '_' . strtolower($key);

        if ($mode == '2')
            $chk_str = (in_array($key, $selected_arr)) ? 'checked' : ''; // $chk_str = ($key == $selected)? 'checked': '';
        else
            $chk_str = (isset($selected_arr[$key])) ? 'checked' : '';

        $str .= '<input type="checkbox" name="' . $ctrl . '" id="' . $ctrl_id . '" value="' . $key . '" ' . $chk_str . '> <label for="' . $ctrl_id . '">' . $txt . '</label>&nbsp;&nbsp;';
    }

    return $str;
}

function NotFoundRow($colspan = 1, $msg = '')
{
    $colspan_str = ($colspan > 1) ? ' colspan="' . $colspan . '"' : '';
    return '<tr><td ' . $colspan_str . '>No Records Found...</td></tr>';
}

function PrintRow($arr, $width, $opt_pad_str = ' ', $newline_flag = true)
{
    $str = '';
    foreach ($width as $i => $w) {
        $space = $data = '';
        $pad_str = $opt_pad_str;
        $pad_type = STR_PAD_RIGHT;

        if (isset($arr[$i])) {
            if (is_array($arr[$i])) {
                $data = $arr[$i][0];

                if (isset($arr[$i][1]) && $arr[$i][1] == 'L') $pad_type = STR_PAD_LEFT;
                else if (isset($arr[$i][1]) && $arr[$i][1] == 'C') $pad_type = STR_PAD_BOTH;
                if (isset($arr[$i][2])) $pad_str = $arr[$i][2];
                if (isset($arr[$i][3])) $space = ' ';
            } else
                $data = $arr[$i];
        }

        $str .= $space . str_pad($data, $w, $pad_str, $pad_type);
    }

    if ($newline_flag)
        $str .= NEWLINE;

    return $str;
}

function GetAnniversaryDt($x_dt)
{
    return (!empty($x_dt) && $x_dt != '0000-00-00') ? date('M d', strtotime($x_dt)) : '';
}

function FillCheckboxGrid($div_id, $selected_arr, $ctrl_name, $data_arr)
{
    $str = '<div id="' . $div_id . '" style="background-color:#ffffff;border:1px solid #000000;position:absolute;height:200px;overflow:scroll;display:none;">';

    foreach ($data_arr as $key => $value) {
        $ctrl_id = $ctrl_name . '_' . $key;
        $_checked = (in_array($key, $selected_arr)) ? 'checked' : '';
        $str .= '<span class="search_ctrl" style="width:200px;">';
        $str .= '<input type="checkbox" name="' . $ctrl_name . '[]" id="' . $ctrl_id . '" value="' . $key . '" ' . $_checked . '/>';
        $str .= '<label for="' . $ctrl_id . '">' . $value . '</label></span> ';
    }
    $str .= '</div>';

    return $str;
}

function ParseStringForXML($val)
{
    $invalid_char_arr = array('%E2%80%99', '%E2%80%93', '%E2%80%A6');

    $val = trim($val);
    $val = strip_tags($val);                                // remove html tags
    $val = urlencode($val);                                    // encode (makes it easier to find & replace chars)
    $val = str_replace($invalid_char_arr, '%27', $val);        // replace invalid chars
    $val = htmlentities(urldecode($val));                    // decode back to txt and then handle special chars

    return $val;
}

function EncodeParam($param)
{
    $rand = rand();
    $crypt_str = md5($rand);
    // $crypt_str = substr($crypt_str,0,12);

    $len = strlen($param);
    $a = substr($crypt_str, 0, 1);
    $start = hexdec($a);

    $a = substr($crypt_str, 0, $start + 1);
    $b = substr($crypt_str, ($start + 1 + $len + 1));
    $x =  $a . $len . $param . $b;

    return $x;
}

function DecodeParam($crypt_param)
{
    $a = substr($crypt_param, 0, 1);
    $start = hexdec($a);
    $len = substr($crypt_param, $start + 1, 1);

    $param = substr($crypt_param, ($start + 1 + 1), $len);
    return $param;
}

function FirstDateOfMonth($date) // as Y-m-d
{
    list($y, $m, $d) = explode('-', $date);
    return $y . '-' . $m . '-01';
}

function MonthDiff($date1, $date2)
{
    list($y1, $m1, $d1) = explode('-', $date1);
    list($y2, $m2, $d2) = explode('-', $date2);

    return (($y2 - $y1) * 12) + ($m2 - $m1) + 1;
}

function EnsureReportStartDate($dfrom, $time_flag = false, $dmy_flag = true)
{
    if ($dmy_flag) $dfrom = ConvertFromDMYToYMD($dfrom, $time_flag);
    $start_date = ($time_flag) ? START_DATE . ' 00:00:00' : START_DATE;
    if ($dfrom < $start_date) $dfrom = $start_date;
    return ($dmy_flag) ? ConvertFromYMDToDMY($dfrom, $time_flag) : $dfrom;
}

function MultiSort($a, $b)
{
    $args = explode('~', USORT_ORDER);

    $i = 0;
    $c = count($args);
    $cmp = 0;
    while ($cmp == 0 && $i < $c) {
        list($key, $is_asc) = explode(':', $args[$i]);

        $cmp = ($is_asc) ? strcmp($a[$key], $b[$key]) : strcmp($b[$key], $a[$key]);
        $i++;
    }

    return $cmp;
}

function GetFirstDayOfWeek($date, $is_ymd = true)
{
    $today_dayno = date("w", strtotime($date)); // wht day is it?
    $format = ($is_ymd) ? 'Y-m-d' : 'd-m-Y';
    return DateTimeAdd($date, (WEEK_START_DAY - $today_dayno), 0, 0, 0, 0, 0, $format); // get the 1st day of the given wk, adjust for offset
}

function GetQtrFromMonth($m = THIS_MONTH) // StartDateX
{
    $mn = $m - QTR_MONTH_OFFSET;
    $mn = AdjustMonthValues($mn);
    return ceil($mn / 3);
}

function AdjustMonthValues($mn)
{
    if ($mn < 0) $mn = 12 + $mn + 1;
    elseif ($mn == 0) $mn = 12;
    elseif ($mn > 12) $mn = $mn % 12;

    return $mn;
}

function SummarizeDataArr($arr)
{
    foreach ($arr as $ref => $A)
        echo $ref . ': ' . array_sum($A) . '<br />';
}

function ListDataArr($arr)
{
    foreach ($arr as $ref => $A)
        foreach ($A as $b_id => $b_val)
            if ($b_val != 0)
                echo $ref . ': ' . $b_id . ' = ' . $b_val . '<br />';
}

function ListCalcDataArrByBatch($arr)

{
    $a = array();

    foreach ($arr as $ref => $A)
        foreach ($A as $b_id => $b_val) {
            if (!isset($a[$b_id])) $a[$b_id] = 0;

            if ($b_val != 0)
                $a[$b_id] += $b_val;
        }

    foreach ($a as $id => $val)
        if ($val != 0)
            echo $id . ': ' . $val . '<br />';
}

function IsDate($date, $is_dmy = false)
{
    //echo $date.'<br/>';

    $x = false;

    $date = trim($date);
    if (!empty($date) && strpos($date, '-')) {
        $d = explode('-', $date);

        if (count($d) == 3)
            if ((!$is_dmy && checkdate($d[1], $d[2], $d[0])) || checkdate($d[1], $d[0], $d[2]))
                $x = true;
    }

    return $x;
}

function JustID(&$val, $default = 0) // $mode: INTEGER/ REAL
{
    JustNumeric($val, 'INTEGER');
}

function JustNumeric(&$val, $mode = 'REAL', $default = 0) // $mode: INTEGER/ REAL
{
    $val = trim($val);
    $val = ($mode == 'INTEGER') ? intval($val) : floatval($val);
    if (!is_numeric($val)) $val = $default;
}

function FormatDateForIMS($date_val)
{
    if (!empty($date_val)) {
        $dt = date("Y-m-d", strtotime($date_val));
        $y1 = date("Y", strtotime($date_val));
        $y2 = date("Y");
        $d = DateDiff($dt, TODAY);

        if ($d) {
            $date_format_str = "M j";

            if ($y1 < $y2)
                $date_format_str .= ", Y";
        } else
            $date_format_str = "H:i";

        return date($date_format_str, strtotime($date_val));
    } else
        return '-NA-';
}

function FormatDateForIMS2($date_val)
{
    $dt = date("Y-m-d", strtotime($date_val));
    $y1 = date("Y", strtotime($date_val));
    $y2 = date("Y");
    $d = DateDiff($dt, TODAY);

    if ($d) {
        $date_format_str = "M j";

        if ($y1 < $y2)
            $date_format_str .= ", Y";

        $date_format_str .= " H:i a";
    } else
        $date_format_str = "M j, H:i a";

    return date($date_format_str, strtotime($date_val));
}

function FormatDateForIMS3($date_val)
{
    if (!empty($date_val)) {
        $dt = date("Y-m-d", strtotime($date_val));
        $y1 = date("Y", strtotime($date_val));
        $y2 = date("Y");
        $d = DateDiff($dt, TODAY);

        if ($d) {
            $date_format_str = "M j";

            if ($y1 < $y2)
                $date_format_str .= ", Y";

            $dSTR = date($date_format_str, strtotime($date_val));
        } else {
            $minutes = round(abs(strtotime(CURRENTTIME) - strtotime(date('H:i:s', strtotime($date_val)))) / 60, 2);
            if ($minutes >= 5 && $minutes < 60) $date_format_str = "i";
            elseif ($minutes >= 60) $date_format_str = "H";
            else $date_format_str = '';

            if ($date_format_str == 'i') $dSTR = date($date_format_str, strtotime($date_val)) . ' mins';
            elseif ($date_format_str == 'H') $dSTR = date($date_format_str, strtotime($date_val)) . ' hours';
            else $dSTR = 'just now';

            //$dSTR .= ' '.date('H:i:s', strtotime($date_val)).' =>'.CURRENTTIME.' =>'.$minutes.' =>'.$date_format_str;
        }

        return $dSTR;
    } else
        return '-NA-';
}

function FormatDateForIMS4($date_val)
{
    $dt = date("Y-m-d", strtotime($date_val));
    $y1 = date("Y", strtotime($date_val));
    $y2 = date("Y");
    $d = DateDiff($dt, TODAY);

    if ($d) {
        $date_format_str = "M j, Y";
    } else
        $date_format_str = "M j, H:i a";

    return date($date_format_str, strtotime($date_val));
}

function MustString($val)
{
    return (trim($val) == '') ? 1 : 0;
}

function MustID($val)
{
    JustID($val);
    return ($val < 1) ? 1 : 0;
}

function MustNumeric($val, $mode = 'REAL', $min_value = 0, $max_value = 0)
{
    JustNumberic($val, $mode);
    return ($val < $min_value || $val > $max_value) ? 1 : 0;
}

function EnsureDateTimeDuration(&$dtstart, &$dtend, $min_diff = 30) // min_diff is expressed in minutes
{
    $start = strtotime($dtstart);
    $end = strtotime($dtend);

    $diff = (strtotime($dtend) - strtotime($dtstart)) / 60;

    if ($diff < $min_diff)
        $dtend = DateTimeAdd($dtstart, 0, 0, 0, 0, $min_diff, 0);
}

function GetPageHeader($page_orientation = 'P', $page_prefix = false)
{
    global $is_pdf;

    if ($is_pdf) {
        if ($page_prefix) $page_orientation .= '.' . $page_prefix;
        return '[PAGE_START][' . $page_orientation . ']';
    }

    return ''; // <div align="center">&nbsp;</div>';
}

function CalcSQMetersToSQFeet($val)
{
    return $val * 10.76391;
}

function IsValidFile($file_type, $extension, $type, $size = false, $max_file_size = false)
{
    global $IMG_TYPE, $DOC_TYPE, $IMG_FILE_TYPE, $DOC_FILE_TYPE;

    $str = false;

    if ($type == 'P') {
        if (in_array($extension, $IMG_TYPE))
            $str = true;
    } elseif ($type == 'D') {
        if (in_array($extension, $DOC_TYPE))
            $str = true;
    }

    return $str;
}

function GenerateDayDropDown($ctrl, $sDate = '', $fn = '')
{
    $str = '<select class="from-day inputbox" name="' . $ctrl . '" id="' . $ctrl . '"' . $fn . '>';
    for ($i = '1'; $i <= 31; $i++) {
        $j = str_pad($i, '2', '0', STR_PAD_LEFT);
        $selected = ($sDate == $j) ? ' selected="true"' : '';
        $str .= '<option value="' . $j . '"' . $selected . '> ' . $i . ' </option>';
    }
    $str .= '</select>';

    return $str;
}

function GenerateMonthDropDown($ctrl, $sDate = '', $fn = '')
{
    $mLeft = 12 - CURRENT_MONTH;
    $mGenerate = 24 +  $mLeft;
    $str = '<select class="from-month inputbox" name="' . $ctrl . '" id="' . $ctrl . '"' . $fn . '>';
    for ($i = '0'; $i <= $mGenerate; $i++) {
        $date = DateTimeAdd(TODAY, 0, $i, 0, 0, 0, 'Y-m-d');
        $value = date("Y", strtotime($date)) . '-' . date("m", strtotime($date));
        $name = date("F", strtotime($date)) . '&nbsp;' . date("Y", strtotime($date));

        $selected = ($sDate == $value) ? ' selected="true"' : '';

        $str .= '<option value="' . $value . '"' . $selected . '> ' . $name . ' </option>';
    }
    $str .= '</select>';

    return $str;
}

function FillMultiCombo($selected, $ctr, $type, $comp, $values, $fn = "", $class = "box", $combo_type = "KEY_VALUE") //fill the values from an array
{
    $display = ($type <> "COMBO") ? "size=10" : "";

    $str = "<select name='" . $ctr . "[]' id='$ctr' multiple='multiple' class='$class' $display $fn>"; //  

    if (($comp <> "y") && ($comp <> "Y")) {
        if ($comp == '0')
            $str .= "<option value='0' selected> - select - </option>\n";
        elseif ($comp == '1')
            $str .= "<option value='0' selected> - main category - </option>\n";
        elseif ($comp == '2')
            $str .= "<option value='0' selected>MM</option>\n";
        elseif ($comp == '-1')
            $str .= "<option value=''>- Select TID -</option>\n";
        else
            $str .= "";
    }

    if ($combo_type == "KEY_VALUE") {
        foreach ($values as $key_val => $var) {
            $select_str = (isset($selected[$key_val]) && $selected[$key_val] == $key_val) ? "selected" : "";
            $str .= "<option value='$key_val' $select_str> $var</option>";
        }
    } elseif ($combo_type == "KEY_IS_VALUE") {
        foreach ($values as $var) {
            $select_str = (isset($selected[$var]) && $selected[$var] == $var) ? "selected" : "";
            $str .= "<option value='$var' $select_str> $var</option>";
        }
    } elseif ($combo_type == "SPLIT_FOR_KEY_VALUE") {
        foreach ($values as $var) {
            $v = explode("~", $var);
            $key = $v[0];
            $txt = $v[1];

            $select_str = (isset($selected[$key]) && $selected[$key] == $key) ? "selected" : "";
            $str .= "<option value='$key' $select_str> $txt</option>";
        }
    }

    $str .= "</select>";
    return $str;
}

function SuggestCode()
{
    $arr = array();

    $len = rand(10, 12);

    // atleast 1 uppercase char
    $a_len = rand(1, $len - 3);
    // echo 'a_len: '.$a_len.'<br>';

    for ($i = 0; $i < $a_len; $i++)
        $arr[$i] = chr(rand(65, 90));

    $ctr = $i;

    // atleast 1 lowercase char
    $b_len = rand(1, $len - 2 - $a_len);
    // echo 'b_len: '.$b_len.'<br>';

    for (; $i < ($ctr + $b_len); $i++)
        $arr[$i] = chr(rand(97, 122));

    $ctr = $i;

    // atleast 1 number
    $c_len = rand(1, $len - 1 - $a_len - $b_len);
    // echo 'c_len: '.$c_len.'<br>';

    for (; $i < ($ctr + $c_len); $i++)
        $arr[$i] = rand(0, 9);

    // DFA($arr);
    shuffle($arr);
    // DFA($arr);

    $str = '';
    foreach ($arr as $a)
        $str .= $a;

    return $str;
}

function chop_words($str, $words = 20, $limit = 0, $suffix = ' ...')
{
    //string  $str --The input string
    //$words  The number of words to return, default 20, 0 to skip
    //$limit  Maximum length of the returned string 
    //string  $suffix  The string to append to the input if shortened.

    if ($limit) $limit -= strlen($suffix);

    for ($i = 0, $ix = 0; $i < $words; $i++)
        if (($is = strpos($str, ' ', $ix)) !== false) {
            if ($limit && $is + 1 > $limit)
                break;

            $ix = $is + 1;
        } else
            return $str;

    return substr($str, 0, $ix) . $suffix;
}

function FillRadios2($selected, $ctrl, $value_arr, $fn_str = '')
{
    $str = '';

    foreach ($value_arr as $key => $txt) {
        $ctrl_id = $ctrl . '_' . strtolower($key);
        $chk_str = ($key === $selected) ? 'checked' : '';

        $str .= '<div class="md-radio">';
        $str .= '<input type="radio" name="' . $ctrl . '" id="' . $ctrl_id . '" value="' . $key . '" class="md-radiobtn" ' . $chk_str . ' ' . $fn_str . ' /><label for="' . $ctrl_id . '"> <span></span> <span class="check"></span> <span class="box"></span>' . $txt . '</label>';
        $str .= '</div>';
    }

    return $str;
}

function GetAccessCountry()
{
    $str = '';

    $ip = $_SERVER['REMOTE_ADDR']; // the IP address to query
    $query = @unserialize(file_get_contents('http://ip-api.com/php/' . $ip));
    if ($query && $query['status'] == 'success') {
        $str = $query['countryCode'];
        if (isset($query['country']))
            $str .= '~' . $query['country'];
        if (isset($query['city']))
            $str .= '~' . $query['city'];
        if (isset($query['regionName']))
            $str .= '~' . $query['regionName'];
    }

    return $str;
}

function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";

    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }

    // Next get the name of the useragent yes separately and for good reason.
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    } else
        $ub = '';

    // Finally get the correct version number.
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }

    // See how many we have.
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

    // Check if we have a number.
    if ($version == null || $version == "") {
        $version = "?";
    }

    return array(
        'userAgent' => $u_agent,
        'name'      => $bname,
        'version'   => $version,
        'platform'  => $platform,
        'pattern'    => $pattern
    );
}

function GetUniqueIDs($arr)
{
    $x = array();

    if (count($arr))
        foreach ($arr as $a)
            if (is_array($a))
                $x = $x + $a;

    $x[0] = 0;

    return array_unique($x);
}

function GetTaxBreakUpForProduct($prod_amount, $tax_perc = '12', $tax_region = 'L')
{
    $str = '';

    $basePrice = (100 / (100 + $tax_perc)) * $prod_amount;
    $taxAMT = ($basePrice * $tax_perc) / 100;

    $sgst = $cgst = $igst = '0';
    if ($tax_region == 'L') {
        $halfTAXPERC = ($tax_perc / 2);
        $halfTAXAMT = ($taxAMT / 2);

        $sgst = $cgst = $halfTAXAMT;
    } else
        $igst = $taxAMT;

    $str = $basePrice . '~' . $sgst . '~' . $cgst . '~' . $igst;

    return $str;
}


function GetTaxForProduct($prod_amount, $tax_perc = '12', $tax_region = 'L')
{
    $str = '';

    $taxAMT = ($prod_amount * $tax_perc) / 100;

    $sgst = $cgst = $igst = '0';
    if ($tax_region == 'L') {
        $halfTAXPERC = ($tax_perc / 2);
        $halfTAXAMT = ($taxAMT / 2);

        $sgst = $cgst = $halfTAXAMT;
    } else
        $igst = $taxAMT;

    $str = $prod_amount . '~' . $sgst . '~' . $cgst . '~' . $igst;

    return $str;
}

function GetCESSTaxCalculation($prod_amount, $tax_perc, $type = 'I')
{
    if ($type == 'I') {
        $basePrice = (100 / (100 + $tax_perc)) * $prod_amount;
        $taxAMT = ($basePrice * $tax_perc) / 100;
    } else {
        $basePrice = $prod_amount;
        $taxAMT = ($prod_amount * $tax_perc) / 100;
    }

    $str = $basePrice . '~' . $taxAMT;

    return $str;
}

function GetLatLong($address)
{
    $address = str_replace(" ", "+", $address);

    //$json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&region=$region");
    $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");
    $json = json_decode($json);

    $lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
    $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
    return $lat . ',' . $long;
}

function GenerateRandomCode($len, $fld, $tbl, $cond = "")
{
    $str = '';

    $arr = array();
    for ($i = 0; $i < $len; $i++)
        $arr[$i] = rand(0, 9);

    shuffle($arr);
    $str = '';
    foreach ($arr as $a)
        $str .= $a;

    if (!empty($fld) && !empty($tbl)) {
        $eXIST = GetXFromYID('select count(*) from ' . $tbl . ' where ' . $fld . '="' . $str . '"' . $cond);
        if (!empty($eXIST) && $eXIST != '-1')
            $str = GenerateRandomCode($len, $fld, $tbl);
    }

    return $str;
}

function encodeId($param)
{
    global $ENC_CHARARR;
    $code = '';

    $param = strval($param);
    for ($i = 0; $i < strlen($param); $i++)
        $code .= $ENC_CHARARR[$param[$i]];

    $str = $code;

    return $str;
}

function GenerateVoucherCode($id = "", $len = "6", $fld = "vVoucherID", $tbl = "booking", $cond = "")
{
    $arr = array();

    $len = 6; //rand(10, 12);	

    // atleast 1 uppercase char
    $a_len = rand(1, 1);
    // echo 'a_len: '.$a_len.'<br>';

    for ($i = 0; $i < $a_len; $i++)
        $arr[$i] = chr(rand(65, 90));

    $ctr = $i;

    /*// atleast 1 lowercase char
	$b_len = rand(2,2);
	// echo 'b_len: '.$b_len.'<br>';

	for(;$i<($ctr+$b_len); $i++)
		$arr[$i] = chr(rand(97, 122));

	$ctr = $i;*/

    // atleast 1 number
    $c_len = rand(1, 2);
    // echo 'c_len: '.$c_len.'<br>';

    for (; $i < ($ctr + $c_len); $i++)
        $arr[$i] = rand(0, 9);

    // DFA($arr);
    shuffle($arr);
    // DFA($arr);

    $str = date('ymd');
    foreach ($arr as $a)
        $str .= $a . encodeId($id);

    if (!empty($fld) && !empty($tbl)) {
        $eXIST = GetXFromYID('select count(*) from ' . $tbl . ' where ' . $fld . '="' . $str . '"' . $cond);
        if (!empty($eXIST) && $eXIST != '-1')
            $str = GenerateVoucherCode($id, $len, $fld, $tbl);
    }

    return $str;
}

function LogAdminUpdates($user_id, $mode, $table, $id)
{
    global $sess_user_level;
    $now = NOW;

    $q = "insert into log_adminsignin values ('$user_id', '$sess_user_level', '$now', '$mode', '$table', '$id')";
    $r = sql_query($q, 'LAU.216');
}

function TrimData($arr)
{
    foreach ($arr as $d_key => $d_val)
        $arr[$d_key] = trim($d_val);

    return $arr;
}

function LockTable($tbl, $mode = 'WRITE')
{
    $q = 'LOCK TABLE ' . $tbl . ' ' . $mode;
    $r = sql_query($q);
}

function UnlockTable()
{
    $q = 'UNLOCK TABLES';
    $r = sql_query($q);
}

function FillRadios($selected, $ctrl, $value_arr, $fn_str = '')
{
    $str = '';

    $str .= '<div class="kt-radio-inline">';
    foreach ($value_arr as $key => $txt) {
        $ctrl_id = $ctrl . '_' . strtolower($key);
        $chk_str = ($key === $selected) ? 'checked' : '';

        $str .= '<label class="kt-radio kt-radio--solid" for="' . $ctrl_id . '"><input type="radio" name="' . $ctrl . '" id="' . $ctrl_id . '" value="' . $key . '" ' . $chk_str . ' ' . $fn_str . ' /> ' . $txt . '<span></span></label> ';
    }

    $str .= '</div>';

    return $str;
}

function FillRadiosWithBr($selected, $ctrl, $value_arr, $fn_str = '')
{
    $str = '';

    $str .= '<div class="kt-radio-inline">';
    foreach ($value_arr as $key => $txt) {
        $ctrl_id = $ctrl . '_' . strtolower($key);
        $chk_str = ($key === $selected) ? 'checked' : '';

        $str .= '<label class="kt-radio kt-radio--solid" for="' . $ctrl_id . '"><input type="radio" name="' . $ctrl . '" id="' . $ctrl_id . '" value="' . $key . '" ' . $chk_str . ' ' . $fn_str . ' /> ' . $txt . '<span></span></label><br />';
    }

    $str .= '</div>';

    return $str;
}

function FormatAmount($number)
{
    $str = '';

    $a = array('1' => '10000000', '2' => '100000', '3' => '1000');
    $b = array('1' => 'Cr', '2' => 'L', '3' => 'K');

    $j = 0;
    foreach ($a as $key => $value) {
        if ($number >= $value) {
            $str = $number / $value;
            $str .= $b[$key];

            $j++;
            break;
        }
    }

    if ($j == 0)
        $str = $number;

    return $str;
}

function GetLast30DaysDetails($p_id)
{
    $str = '';

    $cond = '';
    if (!empty($p_id))
        $cond = ' and iPropertyID=' . $p_id;

    $DATE = TODAY;
    $DATE2 = date("Y-m-d", strtotime("-30 day"));

    $T_ARR  = $T_ARR2 = $V_ARR = array();

    $q = 'select iTID as ID, dtTransaction as DATE, iPropertyID as PROPERTY, fAmount as AMOUNT, iUserID as USER, cReversed as REVERSED, cStatus as STATUS, "T" as TYPE from transactions where DATE_FORMAT(dtTransaction,"%Y-%m-%d")>="' . $DATE2 . '" and DATE_FORMAT(dtTransaction,"%Y-%m-%d")<="' . $DATE . '" ' . $cond . '
		UNION	
		select iVTID as ID, dtVoid as DATE, iTID as PROPERTY, fAmount as AMOUNT, iUserID as USER, "" as REVERSED, "" as STATUS, "V" as TYPE from void_transactions where DATE_FORMAT(dtVoid,"%Y-%m-%d")>="' . $DATE2 . '" and DATE_FORMAT(dtVoid,"%Y-%m-%d")<="' . $DATE . '"';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($ID, $DATE, $PROPERTY, $AMOUNT, $USER, $REVERSED, $STATUS, $TYPE) = sql_fetch_row($r)) {
            if ($TYPE == 'T') {
                $d = FormatDate($DATE, '11');
                if (!isset($T_ARR[$PROPERTY][$d]['AMOUNT'])) $T_ARR[$PROPERTY][$d]['AMOUNT'] = 0;
                $T_ARR[$PROPERTY][$d]['AMOUNT'] = $T_ARR[$PROPERTY][$d]['AMOUNT'] + $AMOUNT;

                if (!isset($T_ARR[$PROPERTY][$d]['COUNT'])) $T_ARR[$PROPERTY][$d]['COUNT'] = 0;
                $T_ARR[$PROPERTY][$d]['COUNT'] = $T_ARR[$PROPERTY][$d]['COUNT'] + 1;

                $T_ARR2[$ID] = $PROPERTY;
            } elseif ($TYPE == 'V') {
                if (isset($T_ARR2[$PROPERTY]))
                    $PROPERTY = $T_ARR2[$PROPERTY];
                else {
                    $PROPERTY2 = GetXFromYID('select iPropertyID from transactions where iTID=' . $PROPERTY);
                    if (!empty($PROPERTY2) && $PROPERTY2 != '-1') {
                        $T_ARR2[$PROPERTY] = $PROPERTY2;
                        $PROPERTY = $PROPERTY2;
                    }
                }

                if (!empty($p_id) && $PROPERTY != $p_id)
                    continue;

                $d = FormatDate($DATE, '11');
                if (!isset($V_ARR[$PROPERTY][$d]['AMOUNT'])) $V_ARR[$PROPERTY][$d]['AMOUNT'] = 0;
                $V_ARR[$PROPERTY][$d]['AMOUNT'] = $V_ARR[$PROPERTY][$d]['AMOUNT'] + $AMOUNT;

                if (!isset($V_ARR[$PROPERTY][$d]['COUNT'])) $V_ARR[$PROPERTY][$d]['COUNT'] = 0;
                $V_ARR[$PROPERTY][$d]['COUNT'] = $V_ARR[$PROPERTY][$d]['COUNT'] + 1;
            }
        }
    }

    return array($T_ARR, $V_ARR);
}

function GetLast6MonthDetails($p_id)
{
    $str = '';

    $cond = '';
    if (!empty($p_id))
        $cond = ' and iPropertyID=' . $p_id;

    $DATE = date("Y-m-t");
    $DATE2 = date("Y-m-01", strtotime("-6 month"));

    $T_ARR  = $T_ARR2 = $V_ARR = $CUSTOMER_ARR = array();

    $q = 'select iTID as ID, dtTransaction as DATE, iPropertyID as PROPERTY, fAmount as AMOUNT, iUserID as USER, cReversed as REVERSED, cStatus as STATUS, "T" as TYPE, iCustID as CUSTOMER from transactions where DATE_FORMAT(dtTransaction,"%Y-%m-%d")>="' . $DATE2 . '" and DATE_FORMAT(dtTransaction,"%Y-%m-%d")<="' . $DATE . '" ' . $cond . '
		UNION	
		select iVTID as ID, dtVoid as DATE, iTID as PROPERTY, fAmount as AMOUNT, iUserID as USER, "" as REVERSED, "" as STATUS, "V" as TYPE, "" as CUSTOMER from void_transactions where DATE_FORMAT(dtVoid,"%Y-%m-%d")>="' . $DATE2 . '" and DATE_FORMAT(dtVoid,"%Y-%m-%d")<="' . $DATE . '"';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($ID, $DATE, $PROPERTY, $AMOUNT, $USER, $REVERSED, $STATUS, $TYPE, $CUSTOMER) = sql_fetch_row($r)) {
            if ($TYPE == 'T') {
                $d = FormatDate($DATE, '18');
                if (!isset($T_ARR[$PROPERTY][$d]['AMOUNT'])) $T_ARR[$PROPERTY][$d]['AMOUNT'] = 0;
                $T_ARR[$PROPERTY][$d]['AMOUNT'] = $T_ARR[$PROPERTY][$d]['AMOUNT'] + $AMOUNT;

                if (!isset($T_ARR[$PROPERTY][$d]['COUNT'])) $T_ARR[$PROPERTY][$d]['COUNT'] = 0;
                $T_ARR[$PROPERTY][$d]['COUNT'] = $T_ARR[$PROPERTY][$d]['COUNT'] + 1;

                $T_ARR2[$ID] = $PROPERTY;

                if (!isset($CUSTOMER_ARR[$CUSTOMER])) {
                    $custDATE = GetXFromYID('select dtCreated from customer where iCustID=' . $CUSTOMER);
                    $custDATE2 = ConvertFromYMDtoDMY($custDATE, true);
                    if ($custDATE2 != '0000-00-00 00:00:00') {
                        $CUSTOMER_ARR[$CUSTOMER] = $custDATE2;

                        $d2 = FormatDate($custDATE2, '18');
                        if (!isset($T_ARR[$PROPERTY][$d2]['REGISTRATIONS'])) $T_ARR[$PROPERTY][$d2]['REGISTRATIONS'] = 0;
                        $T_ARR[$PROPERTY][$d2]['REGISTRATIONS'] = $T_ARR[$PROPERTY][$d2]['REGISTRATIONS'] + 1;
                    }
                }
            } elseif ($TYPE == 'V') {
                if (isset($T_ARR2[$PROPERTY]))
                    $PROPERTY = $T_ARR2[$PROPERTY];
                else {
                    $PROPERTY2 = GetXFromYID('select iPropertyID from transactions where iTID=' . $PROPERTY);
                    if (!empty($PROPERTY2) && $PROPERTY2 != '-1') {
                        $T_ARR2[$PROPERTY] = $PROPERTY2;
                        $PROPERTY = $PROPERTY2;
                    }
                }

                if (!empty($p_id) && $PROPERTY != $p_id)
                    continue;

                $d = FormatDate($DATE, '18');
                if (!isset($V_ARR[$PROPERTY][$d]['AMOUNT'])) $V_ARR[$PROPERTY][$d]['AMOUNT'] = 0;
                $V_ARR[$PROPERTY][$d]['AMOUNT'] = $V_ARR[$PROPERTY][$d]['AMOUNT'] + $AMOUNT;

                if (!isset($V_ARR[$PROPERTY][$d]['COUNT'])) $V_ARR[$PROPERTY][$d]['COUNT'] = 0;
                $V_ARR[$PROPERTY][$d]['COUNT'] = $V_ARR[$PROPERTY][$d]['COUNT'] + 1;
            }
        }
    }

    return array($T_ARR, $V_ARR);
}

function Get6MonthArr()
{
    $result = array();

    $Month_From = date("m", strtotime("-6 month"));
    $Year_From = date("Y", strtotime("-6 month"));
    $Month_To = date("m");
    $Year_To = date("Y");

    $tMonth = $Month_From;
    $tYear = $Year_From;

    while ($tYear <= $Year_To) {
        while (($tMonth <= 12 && $tYear < $Year_To) || ($tMonth <= $Month_To && $tYear == $Year_To)) {
            $result[] = FormatDate($tYear . '-' . str_pad($tMonth, 2, 0, STR_PAD_LEFT) . '-01', '18');
            /*$result[] = array(
				"month" => $tMonth,
				"year" => $tYear,
			);*/

            $tMonth++;
        }

        $tMonth = 1;
        $tYear++;
    }

    return $result;
}
function GetLinkedURLS($id)
{
    $URLS = array();
    $_uq = "select vUrl from menu_dat where cStatus='A' and iMenuID=" . $id;
    $_ur = sql_query($_uq, '');
    if (sql_num_rows($_ur)) {
        while (list($URL) = sql_fetch_row($_ur))
            array_push($URLS, $URL);
    }

    return $URLS;
}

function GetActiveLink($currentFILE, $ARR)
{
    $str = '';

    if (!empty($ARR) && count($ARR)) {
        if ($ARR['HREF'] == $currentFILE || in_array($currentFILE, $ARR['URLS']))
            $str = ' class="mm-active" id="scrollSideMenu"';
        else {
            if ($ARR['IS_SUB'] == 'Y' && !empty($ARR['MENU']) && count($ARR['MENU'])) {
                foreach ($ARR['MENU'] as $sKEY2 => $sVALUE2) {
                    if ($sVALUE2['HREF'] == $currentFILE || in_array($currentFILE, $sVALUE2['URLS']))
                        $str = ' class="mm-active" id="scrollSideMenu"';
                }
            }
        }
    }

    return $str;
}

function GetProgramLevelWeekDayWorkout($p_id, $lvl_id, $week_id, $day_id, $wo_id)
{
    global $DIFFICULTY_SUB_LEVEL, $STATUS_ARR;

    $EXERCISETYPE_ARR = GetXArrFromYID('select iETID, vName from exercise_types where cStatus="A"', '3');
    $EXERCISE_ARR = GetXArrFromYID('select iExerciseID, vName from exercises where cStatus="A"', '3');
    $WARMUP_ARR = GetXArrFromYID('select iWarmupID, vName from warmups where cStatus="A"', '3');
    $COOLDOWN_ARR = GetXArrFromYID('select iCoolDownID, vName from cooldowns where cStatus="A"', '3');
    $WOTYPE_ARR = GetXArrFromYID('select iWOTID, vName from workout_types where cStatus="A"', '3');

    $subLEVEL = $LEVEL = '';
    if (!empty($lvl_id)) {
        $_dq = 'select l.vName, l2.iSubLevelID from difficulty_level as l join program_difficulty_level as l2 on l.iDiffLvlID=l2.iDiffLvlID where l2.iPDiffLvlID=' . $lvl_id;
        $_dr = sql_query($_dq, '');
        list($LEVEL, $subLEVELID) = sql_fetch_row($_dr);

        $LEVEL = htmlspecialchars_decode($LEVEL);
        $subLEVEL = htmlspecialchars_decode($DIFFICULTY_SUB_LEVEL[$subLEVELID]);
    }

    $pWO = array();
    $_wq = 'select iPWOID, iPDiffLvlID, iWeek, iDay, iWOTID, vName, iTimeMin, vDesc, vWarmupDesc, vCooldownDesc, cStatus from program_workout where iProgramID=' . $p_id . ' and iPDiffLvlID=' . $lvl_id . ' and iWeek=' . $week_id . ' and iDay=' . $day_id . ' and iPWOID=' . $wo_id . ' order by iPDiffLvlID, iWeek, iDay'; // MODIF20201222
    $_wr = sql_query($_wq, '');
    if (sql_num_rows($_wr)) {
        while (list($w_ID, $w_DIFFID, $w_WEEK, $w_DAY, $w_WOTID, $w_NAME, $w_TIMEINMIN, $w_DESC, $w_wDESC, $w_cDESC, $w_STATUS) = sql_fetch_row($_wr)) {
            $w_NAME2 = '';
            if (!empty($w_WOTID)) $w_NAME2 = GetXFromYID('select vName from workout_types where iWOTID=' . $w_WOTID);
            if (!empty($w_NAME2)) $w_NAME = $w_NAME2;

            $pWO[$w_ID] = array('DIFF_ID' => $w_DIFFID, 'WEEK' => $w_WEEK, 'DAY' => $w_DAY, 'WOTID' => $w_WOTID, 'NAME' => htmlspecialchars_decode($w_NAME), 'TIME' => $w_TIMEINMIN, 'DESC' => htmlspecialchars_decode($w_DESC), 'WARMUP_DESC' => htmlspecialchars_decode($w_wDESC), 'COOLDOWN_DESC' => htmlspecialchars_decode($w_cDESC), 'STATUS' => $w_STATUS, 'WARMUP' => array(), 'EXERCISE' => array(), 'COOLDOWN' => array()); // MODIF20201222

            $WORKOUT_WARMUP_ARR = $WORKOUT_EXERCISE_ARR = $WORKOUT_COOLDOWN_ARR = array();
            $_wq2 = 'select e.iPWExerciseID, e.cType, e.cRefType, e.iRefID, e.iETID, e.iExerciseID, e.iSets, e.iRepetitions, e.iTimeSecs, e.cTimeType, e.iRestTime, e.cRestTimeType, e.cStatus, e2.iPWEDatID, e2.cRefType, e2.iRefID, e2.iRepetitions, e2.iTimeSecs, e2.cTimeType, e2.cStatus from program_workout_exercise as e left join program_workout_exercise_dat as e2 on e.iPWExerciseID=e2.iPWExerciseID where e.iPWOID=' . $w_ID . ' order by e.iRank, e2.iRank';
            $_wr2 = sql_query($_wq2, '');
            if (sql_num_rows($_wr2)) {
                while (list($we_ID, $we_TYPE, $we_REFTYPE, $we_REFID, $we_EXERCISETYPE, $we_EXERCISEID, $we_SETS, $we_REPS, $we_TIME, $we_TIMETYPE, $we_RESTTIME, $we_RESTTIMETYPE, $we_STATUS, $we_DAT_ID, $we_DAT_REFTYPE, $we_DAT_REFID, $we_DAT_REPS, $we_DAT_TIME, $we_DAT_TIMETYPE, $we_DAT_STATUS) = sql_fetch_row($_wr2)) {
                    //EXERCISE
                    if ($we_TYPE == 'E') {
                        if (!isset($WORKOUT_EXERCISE_ARR[$we_ID])) {
                            if ($we_REFTYPE == 'E') $E = (!empty($we_EXERCISEID) && isset($EXERCISE_ARR[$we_EXERCISEID])) ? $EXERCISE_ARR[$we_EXERCISEID] : '';
                            elseif ($we_REFTYPE == 'W') $E = (!empty($we_REFID) && isset($WARMUP_ARR[$we_REFID])) ? $WARMUP_ARR[$we_REFID] : '';
                            elseif ($we_REFTYPE == 'C') $E = (!empty($we_REFID) && isset($COOLDOWN_ARR[$we_REFID])) ? $COOLDOWN_ARR[$we_REFID] : '';

                            $SETS = $we_SETS;
                            $REPS = $we_REPS;
                            $TIME = $we_TIME;
                            $TIMETYPE = $we_TIMETYPE;
                            $REST = $we_RESTTIME;
                            $RESTTIMETYPE = $we_RESTTIMETYPE;
                            $STATUS = $we_STATUS;
                            $EXERCISETYPE = (!empty($we_EXERCISETYPE) && isset($EXERCISETYPE_ARR[$we_EXERCISETYPE])) ? $EXERCISETYPE_ARR[$we_EXERCISETYPE] : '';


                            $WORKOUT_EXERCISE_ARR[$we_ID] = array('REFTYPE' => $we_REFTYPE, 'REFID' => $we_REFID, 'TYPE' => $EXERCISETYPE, 'TYPE_ID' => $we_EXERCISETYPE, 'EXERCISE' => $E, 'SETS' => $SETS, 'REPS' => $REPS, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'REST' => $REST, 'RESTTIMETYPE' => $RESTTIMETYPE, 'STATUS' => $STATUS, 'DAT' => array());
                        }

                        if (!empty($we_DAT_ID)) {
                            if ($we_DAT_REFTYPE == 'E') $E2 = (!empty($we_DAT_REFID) && isset($EXERCISE_ARR[$we_DAT_REFID])) ? $EXERCISE_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'W') $E2 = (!empty($we_DAT_REFID) && isset($WARMUP_ARR[$we_DAT_REFID])) ? $WARMUP_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'C') $E2 = (!empty($we_DAT_REFID) && isset($COOLDOWN_ARR[$we_DAT_REFID])) ? $COOLDOWN_ARR[$we_DAT_REFID] : '';

                            $REPS2 = $we_DAT_REPS;
                            $TIME = $we_DAT_TIME;
                            $TIMETYPE = $we_DAT_TIMETYPE;
                            $STATUS = $we_DAT_STATUS;

                            array_push($WORKOUT_EXERCISE_ARR[$we_ID]['DAT'], array('ID' => $we_DAT_ID, 'TYPE' => $we_DAT_REFTYPE, 'EXERCISE' => $E2, 'REPS' => $REPS2, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'STATUS' => $STATUS));
                        }
                    }

                    //WARMUP
                    if ($we_TYPE == 'W') {
                        if (!isset($WORKOUT_WARMUP_ARR[$we_ID])) {
                            if ($we_REFTYPE == 'E') $E = (!empty($we_EXERCISEID) && isset($EXERCISE_ARR[$we_EXERCISEID])) ? $EXERCISE_ARR[$we_EXERCISEID] : '';
                            elseif ($we_REFTYPE == 'W') $E = (!empty($we_REFID) && isset($WARMUP_ARR[$we_REFID])) ? $WARMUP_ARR[$we_REFID] : '';
                            elseif ($we_REFTYPE == 'C') $E = (!empty($we_REFID) && isset($COOLDOWN_ARR[$we_REFID])) ? $COOLDOWN_ARR[$we_REFID] : '';

                            $SETS = $we_SETS;
                            $REPS = $we_REPS;
                            $TIME = $we_TIME;
                            $TIMETYPE = $we_TIMETYPE;
                            $REST = $we_RESTTIME;
                            $RESTTIMETYPE = $we_RESTTIMETYPE;
                            $STATUS = $we_STATUS;
                            $EXERCISETYPE = (!empty($we_EXERCISETYPE) && isset($EXERCISETYPE_ARR[$we_EXERCISETYPE])) ? $EXERCISETYPE_ARR[$we_EXERCISETYPE] : '';

                            $WORKOUT_WARMUP_ARR[$we_ID] = array('REFTYPE' => $we_REFTYPE, 'REFID' => $we_REFID, 'TYPE' => $EXERCISETYPE, 'TYPE_ID' => $we_EXERCISETYPE, 'EXERCISE' => $E, 'SETS' => $SETS, 'REPS' => $REPS, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'REST' => $REST, 'RESTTIMETYPE' => $RESTTIMETYPE, 'STATUS' => $STATUS, 'DAT' => array());
                        }

                        if (!empty($we_DAT_ID)) {
                            if ($we_DAT_REFTYPE == 'E') $E2 = (!empty($we_DAT_REFID) && isset($EXERCISE_ARR[$we_DAT_REFID])) ? $EXERCISE_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'W') $E2 = (!empty($we_DAT_REFID) && isset($WARMUP_ARR[$we_DAT_REFID])) ? $WARMUP_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'C') $E2 = (!empty($we_DAT_REFID) && isset($COOLDOWN_ARR[$we_DAT_REFID])) ? $COOLDOWN_ARR[$we_DAT_REFID] : '';

                            $REPS2 = $we_DAT_REPS;
                            $TIME = $we_DAT_TIME;
                            $TIMETYPE = $we_DAT_TIMETYPE;
                            $STATUS = $we_DAT_STATUS;

                            array_push($WORKOUT_WARMUP_ARR[$we_ID]['DAT'], array('ID' => $we_DAT_ID, 'TYPE' => $we_DAT_REFTYPE, 'EXERCISE' => $E2, 'REPS' => $REPS2, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'STATUS' => $STATUS));
                        }
                    }

                    //COOLDOWN
                    if ($we_TYPE == 'C') {
                        if (!isset($WORKOUT_COOLDOWN_ARR[$we_ID])) {
                            if ($we_REFTYPE == 'E') $E = (!empty($we_EXERCISEID) && isset($EXERCISE_ARR[$we_EXERCISEID])) ? $EXERCISE_ARR[$we_EXERCISEID] : '';
                            elseif ($we_REFTYPE == 'W') $E = (!empty($we_REFID) && isset($WARMUP_ARR[$we_REFID])) ? $WARMUP_ARR[$we_REFID] : '';
                            elseif ($we_REFTYPE == 'C') $E = (!empty($we_REFID) && isset($COOLDOWN_ARR[$we_REFID])) ? $COOLDOWN_ARR[$we_REFID] : '';

                            $SETS = $we_SETS;
                            $REPS = $we_REPS;
                            $TIME = $we_TIME;
                            $TIMETYPE = $we_TIMETYPE;
                            $REST = $we_RESTTIME;
                            $RESTTIMETYPE = $we_RESTTIMETYPE;
                            $STATUS = $we_STATUS;
                            $EXERCISETYPE = (!empty($we_EXERCISETYPE) && isset($EXERCISETYPE_ARR[$we_EXERCISETYPE])) ? $EXERCISETYPE_ARR[$we_EXERCISETYPE] : '';

                            $WORKOUT_COOLDOWN_ARR[$we_ID] = array('REFTYPE' => $we_REFTYPE, 'REFID' => $we_REFID, 'TYPE' => $EXERCISETYPE, 'TYPE_ID' => $we_EXERCISETYPE, 'EXERCISE' => $E, 'SETS' => $SETS, 'REPS' => $REPS, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'REST' => $REST, 'RESTTIMETYPE' => $RESTTIMETYPE, 'STATUS' => $STATUS, 'DAT' => array());
                        }

                        if (!empty($we_DAT_ID)) {
                            if ($we_DAT_REFTYPE == 'E') $E2 = (!empty($we_DAT_REFID) && isset($EXERCISE_ARR[$we_DAT_REFID])) ? $EXERCISE_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'W') $E2 = (!empty($we_DAT_REFID) && isset($WARMUP_ARR[$we_DAT_REFID])) ? $WARMUP_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'C') $E2 = (!empty($we_DAT_REFID) && isset($COOLDOWN_ARR[$we_DAT_REFID])) ? $COOLDOWN_ARR[$we_DAT_REFID] : '';

                            $REPS2 = $we_DAT_REPS;
                            $TIME = $we_DAT_TIME;
                            $TIMETYPE = $we_DAT_TIMETYPE;
                            $STATUS = $we_DAT_STATUS;

                            array_push($WORKOUT_COOLDOWN_ARR[$we_ID]['DAT'], array('ID' => $we_DAT_ID, 'TYPE' => $we_DAT_REFTYPE, 'EXERCISE' => $E2, 'REPS' => $REPS2, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'STATUS' => $STATUS));
                        }
                    }
                }
            }

            $pWO[$w_ID]['WARMUP'] = $WORKOUT_WARMUP_ARR;
            $pWO[$w_ID]['EXERCISE'] = $WORKOUT_EXERCISE_ARR;
            $pWO[$w_ID]['COOLDOWN'] = $WORKOUT_COOLDOWN_ARR;
        }
    }

    $formTITLE = 'Add WorkOut';
    $delBUTTON = 'N';
    $addExercise = 'N';
    $cmbworkout_wotid = '0';
    $txtworkout_title = $txtworkout_time = $txtworkout_desc = $txtwarmup_desc = $txtcooldown_desc = ''; // MODIF20201222
    $rdworkout_status = 'A';
    $sort = 'N';
    if (!empty($pWO) && count($pWO)) {
        foreach ($pWO as $KEY => $VALUE) {
            $cmbworkout_wotid = $VALUE['WOTID'];
            $txtworkout_title = $VALUE['NAME'];
            $txtworkout_time = $VALUE['TIME'];
            $txtworkout_desc = $VALUE['DESC']; // MODIF20201222
            $txtwarmup_desc = $VALUE['WARMUP_DESC'];
            $txtcooldown_desc = $VALUE['COOLDOWN_DESC'];
            $rdworkout_status = $VALUE['STATUS'];

            $formTITLE = 'Edit WorkOut';
            $delBUTTON = 'Y';
            $addExercise = 'Y';
            $sort = 'Y';
        }
    }


    $MODAL_TITLE = $LEVEL . ' (Level:' . $subLEVEL . ' | Week:' . $week_id . ' | Day:' . $day_id . ')';
    $MODAL_BODY = '';

    //NESTABLE STARTS
    $MODAL_BODY .= '<div class="col-md-6">';

    if (!empty($pWO) && count($pWO)) {
        foreach ($pWO as $KEY => $VALUE) {
            $MODAL_BODY .= '<div class="main-card mb-3 card">';
            $MODAL_BODY .= '<div class="card-body">';
            $MODAL_BODY .= '<h5 class="card-title">' . $VALUE['NAME'] . '</h5>';
            $MODAL_BODY .= '<div class="page-title-subheading">Time: ' . $VALUE['TIME'] . 'mins</div>';
            $MODAL_BODY .= '</div>';
            $MODAL_BODY .= '</div>';

            //WARMUP STARTS
            $MODAL_BODY .= '<div class="main-card mb-3 card">';
            $MODAL_BODY .= '<div class="card-body">';
            $MODAL_BODY .= '<h5 class="card-title">WarmUp</h5>';
            $MODAL_BODY .= '<div class="dd" id="nestable_list_warmup">';
            if (!empty($VALUE['WARMUP']) && count($VALUE['WARMUP'])) {
                $MODAL_BODY .= '<ol class="dd-list">';
                foreach ($VALUE['WARMUP'] as $KEY2 => $VALUE2) {
                    $SETS = $REPS = $TIME = $TIMETYPE = $REST = $RESTTIMETYPE = '';
                    $ADD = '';

                    if (!empty($VALUE2['TYPE_ID'])) {
                        if ($VALUE2['TYPE_ID'] == '1' || $VALUE2['TYPE_ID'] == '5') {
                            if (!empty($VALUE2['DAT']) && count($VALUE2['DAT'])) {
                                $HEADING = $VALUE2['TYPE'];

                                $style_str = 'font-weight:bold;text-transform:uppercase;';
                            } else {
                                $HEADING = (!empty($VALUE2['EXERCISE'])) ? $VALUE2['EXERCISE'] : $VALUE2['TYPE'];
                                $SETS = $VALUE2['SETS'];
                                $REPS = $VALUE2['REPS'];
                                $TIME = $VALUE2['TIME'];

                                if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                                else $TIMETYPE = 's';

                                $REST = $VALUE2['REST'];
                                if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                                else $RESTTIMETYPE = 's';

                                if (!empty($TIME)) $TIME .= $TIMETYPE;
                                if (!empty($REST)) $REST .= $RESTTIMETYPE;

                                $style_str = (!empty($VALUE2['EXERCISE'])) ? '' : 'font-weight:bold;text-transform:uppercase;';
                            }
                            $ADD = 'N';
                        } else {
                            $HEADING = $VALUE2['TYPE'];
                            $SETS = $VALUE2['SETS'];
                            $REPS = $VALUE2['REPS'];
                            $TIME = $VALUE2['TIME'];

                            if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                            else $TIMETYPE = 's';

                            $REST = $VALUE2['REST'];
                            if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                            else $RESTTIMETYPE = 's';

                            if (!empty($TIME)) $TIME .= $TIMETYPE;
                            if (!empty($REST)) $REST .= $RESTTIMETYPE;

                            $style_str = 'font-weight:bold;text-transform:uppercase;';
                            $ADD = 'Y';
                        }
                    } else {
                        $HEADING = (!empty($VALUE2['EXERCISE'])) ? $VALUE2['EXERCISE'] : $VALUE2['TYPE'];
                        $SETS = $VALUE2['SETS'];
                        $REPS = $VALUE2['REPS'];
                        $TIME = $VALUE2['TIME'];

                        if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                        else $TIMETYPE = 's';

                        $REST = $VALUE2['REST'];
                        if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                        else $RESTTIMETYPE = 's';

                        if (!empty($TIME)) $TIME .= $TIMETYPE;
                        if (!empty($REST)) $REST .= $RESTTIMETYPE;

                        $style_str = (!empty($VALUE2['EXERCISE'])) ? '' : 'font-weight:bold;text-transform:uppercase;';
                    }

                    $DET = '';
                    if (!empty($SETS) && $SETS != '-1') $DET .= 'SETS:' . $SETS . '|';
                    if (!empty($REPS) && $REPS != '-1') $DET .= 'REPS:' . $REPS . '|';
                    if (!empty($TIME)) $DET .= 'TIME:' . $TIME . '|';
                    if (!empty($REST)) $DET .= 'REST:' . $REST . '|';
                    if (!empty($DET)) $DET = ' (' . substr($DET, 0, '-1') . ')';

                    $addDET = '';
                    if ($ADD == 'Y') $addDET = '&nbsp;&nbsp;<a onClick="EditProgramWorkoutExerciseDetails2(\'W\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'' . $KEY2 . '\',\'0\');" title="Add Exercises Under ' . $HEADING . '"><i class="fa fa-plus" style="float:right; padding-left:10px;"></i></a>';

                    $MODAL_BODY .= '<li class="dd-item" data-id="' . $KEY2 . 'E"><div class="dd-handle" style="cursor:pointer;' . $style_str . '">' . $HEADING . $DET . $addDET . '<a onClick="EditProgramWorkoutExerciseDetails(\'W\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'' . $KEY2 . '\');" title="Edit ' . $HEADING . '"><i class="fa fa-edit" style="float:right;"></i></a></div>';
                    if (!empty($VALUE2['DAT']) && count($VALUE2['DAT'])) {
                        $MODAL_BODY .= '<ol class="dd-list">';
                        foreach ($VALUE2['DAT'] as $KEY3 => $VALUE3) {
                            $SETS2 = $REPS2 = $TIME2 = $style_str2 = $addDET2 = '';
                            $HEADING2 = $VALUE3['EXERCISE'];
                            //$SETS2 = $VALUE3['SETS'];
                            $REPS2 = $VALUE3['REPS'];
                            $TIME2 = $VALUE3['TIME'];

                            $DET2 = '';
                            if (!empty($SETS2) && $SETS2 != '-1') $DET2 .= 'SETS:' . $SETS2 . '|';
                            if (!empty($REPS2) && $REPS2 != '-1') $DET2 .= 'REPS:' . $REPS2 . '|';
                            if (!empty($TIME2)) $DET2 .= 'TIME:' . $TIME2 . 's|';
                            if (!empty($DET2)) $DET2 = ' (' . substr($DET2, 0, '-1') . ')';

                            $MODAL_BODY .= '<li class="dd-item" data-id="' . $VALUE3['ID'] . 'D"><div class="dd-handle" style="cursor:pointer;' . $style_str2 . '">' . $HEADING2 . $DET2 . $addDET2 . '<a onClick="EditProgramWorkoutExerciseDetails2(\'W\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'' . $KEY2 . '\',\'' . $VALUE3['ID'] . '\');"><i class="fa fa-edit" style="float:right;"></i></a></div>';
                        }
                        $MODAL_BODY .= '</ol>';
                    }
                    $MODAL_BODY .= '</li>';
                }
                $MODAL_BODY .= '</ol>';
            } else
                $MODAL_BODY .= '<div class="page-title-subheading">No WarmUp Details Added</div>';

            $MODAL_BODY .= '</div>';
            $MODAL_BODY .= '<input type="hidden" id="nestable_list_warmup_output" name="nestable_list_warmup_output" class="form-control col-md-12 margin-bottom-10" value="">';
            $MODAL_BODY .= '</div>';
            $MODAL_BODY .= '</div>';
            //WARM ENDS

            //EXERCISE STARTS
            $MODAL_BODY .= '<div class="main-card mb-3 card">';
            $MODAL_BODY .= '<div class="card-body">';
            $MODAL_BODY .= '<h5 class="card-title">Workout</h5>';
            $MODAL_BODY .= '<div class="dd" id="nestable_list_workout">';
            if (!empty($VALUE['EXERCISE']) && count($VALUE['EXERCISE'])) {
                $MODAL_BODY .= '<ol class="dd-list">';
                foreach ($VALUE['EXERCISE'] as $KEY2 => $VALUE2) {
                    $SETS = $REPS = $TIME = $TIMETYPE = $REST = $RESTTIMETYPE = '';
                    $ADD = '';

                    if (!empty($VALUE2['TYPE_ID'])) {
                        if ($VALUE2['TYPE_ID'] == '1' || $VALUE2['TYPE_ID'] == '5') {
                            if (!empty($VALUE2['DAT']) && count($VALUE2['DAT'])) {
                                $HEADING = $VALUE2['TYPE'];

                                $style_str = 'font-weight:bold;text-transform:uppercase;';
                            } else {
                                $HEADING = (!empty($VALUE2['EXERCISE'])) ? $VALUE2['EXERCISE'] : $VALUE2['TYPE'];
                                $SETS = $VALUE2['SETS'];
                                $REPS = $VALUE2['REPS'];
                                $TIME = $VALUE2['TIME'];

                                if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                                else $TIMETYPE = 's';

                                $REST = $VALUE2['REST'];
                                if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                                else $RESTTIMETYPE = 's';

                                if (!empty($TIME)) $TIME .= $TIMETYPE;
                                if (!empty($REST)) $REST .= $RESTTIMETYPE;

                                $style_str = (!empty($VALUE2['EXERCISE'])) ? '' : 'font-weight:bold;text-transform:uppercase;';
                            }
                            $ADD = 'N';
                        } else {
                            $HEADING = $VALUE2['TYPE'];
                            $SETS = $VALUE2['SETS'];
                            $REPS = $VALUE2['REPS'];
                            $TIME = $VALUE2['TIME'];

                            if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                            else $TIMETYPE = 's';

                            $REST = $VALUE2['REST'];
                            if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                            else $RESTTIMETYPE = 's';

                            if (!empty($TIME)) $TIME .= $TIMETYPE;
                            if (!empty($REST)) $REST .= $RESTTIMETYPE;

                            $style_str = 'font-weight:bold;text-transform:uppercase;';
                            $ADD = 'Y';
                        }
                    } else {
                        $HEADING = (!empty($VALUE2['EXERCISE'])) ? $VALUE2['EXERCISE'] : $VALUE2['TYPE'];
                        $SETS = $VALUE2['SETS'];
                        $REPS = $VALUE2['REPS'];
                        $TIME = $VALUE2['TIME'];

                        if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                        else $TIMETYPE = 's';

                        $REST = $VALUE2['REST'];
                        if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                        else $RESTTIMETYPE = 's';

                        if (!empty($TIME)) $TIME .= $TIMETYPE;
                        if (!empty($REST)) $REST .= $RESTTIMETYPE;

                        $style_str = (!empty($VALUE2['EXERCISE'])) ? '' : 'font-weight:bold;text-transform:uppercase;';
                    }

                    $DET = '';
                    if (!empty($SETS) && $SETS != '-1') $DET .= 'SETS:' . $SETS . '|';
                    if (!empty($REPS) && $REPS != '-1') $DET .= 'REPS:' . $REPS . '|';
                    if (!empty($TIME)) $DET .= 'TIME:' . $TIME . '|';
                    if (!empty($REST)) $DET .= 'REST:' . $REST . '|';
                    if (!empty($DET)) $DET = ' (' . substr($DET, 0, '-1') . ')';

                    $addDET = '';
                    if ($ADD == 'Y') $addDET = '&nbsp;&nbsp;<a onClick="EditProgramWorkoutExerciseDetails2(\'E\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'' . $KEY2 . '\',\'0\');" title="Add Exercises Under ' . $HEADING . '"><i class="fa fa-plus" style="float:right; padding-left:10px;"></i></a>';

                    $MODAL_BODY .= '<li class="dd-item" data-id="' . $KEY2 . 'E"><div class="dd-handle" style="cursor:pointer;' . $style_str . '">' . $HEADING . $DET . $addDET . '<a onClick="EditProgramWorkoutExerciseDetails(\'E\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'' . $KEY2 . '\');" title="Edit ' . $HEADING . '"><i class="fa fa-edit" style="float:right;"></i></a></div>';
                    if (!empty($VALUE2['DAT']) && count($VALUE2['DAT'])) {
                        $MODAL_BODY .= '<ol class="dd-list">';
                        foreach ($VALUE2['DAT'] as $KEY3 => $VALUE3) {
                            $SETS2 = $REPS2 = $TIME2 = $style_str2 = $addDET2 = '';
                            $HEADING2 = $VALUE3['EXERCISE'];
                            //$SETS2 = $VALUE3['SETS'];
                            $REPS2 = $VALUE3['REPS'];
                            $TIME2 = $VALUE3['TIME'];

                            if (!empty($VALUE3['TIMETYPE'])) $TIMETYPE2 = $VALUE3['TIMETYPE'];
                            else $TIMETYPE2 = 's';

                            if (!empty($TIME2)) $TIME2 .= $TIMETYPE2;

                            $DET2 = '';
                            if (!empty($SETS2) && $SETS2 != '-1') $DET2 .= 'SETS:' . $SETS2 . '|';
                            if (!empty($REPS2) && $REPS2 != '-1') $DET2 .= 'REPS:' . $REPS2 . '|';
                            if (!empty($TIME2)) $DET2 .= 'TIME:' . $TIME2 . '|';
                            if (!empty($DET2)) $DET2 = ' (' . substr($DET2, 0, '-1') . ')';

                            $MODAL_BODY .= '<li class="dd-item" data-id="' . $VALUE3['ID'] . 'D"><div class="dd-handle" style="cursor:pointer;' . $style_str2 . '">' . $HEADING2 . $DET2 . $addDET2 . '<a onClick="EditProgramWorkoutExerciseDetails2(\'E\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'' . $KEY2 . '\',\'' . $VALUE3['ID'] . '\');"><i class="fa fa-edit" style="float:right;"></i></a></div>';
                        }
                        $MODAL_BODY .= '</ol>';
                    }
                    $MODAL_BODY .= '</li>';
                }
                $MODAL_BODY .= '</ol>';
            } else
                $MODAL_BODY .= '<div class="page-title-subheading">No Workout Details Added</div>';
            $MODAL_BODY .= '</div>';
            $MODAL_BODY .= '<input type="hidden" id="nestable_list_workout_output" name="nestable_list_workout_output" class="form-control col-md-12 margin-bottom-10" value="">';
            $MODAL_BODY .= '</div>';
            $MODAL_BODY .= '</div>';
            //EXERCISE ENDS

            //COOLDOWN STARTS
            $MODAL_BODY .= '<div class="main-card mb-3 card">';
            $MODAL_BODY .= '<div class="card-body">';
            $MODAL_BODY .= '<h5 class="card-title">Cooldown</h5>';
            $MODAL_BODY .= '<div class="dd" id="nestable_list_cooldown">';
            if (!empty($VALUE['COOLDOWN']) && count($VALUE['COOLDOWN'])) {
                $MODAL_BODY .= '<ol class="dd-list">';
                foreach ($VALUE['COOLDOWN'] as $KEY2 => $VALUE2) {
                    $SETS = $REPS = $TIME = $TIMETYPE = $REST = $RESTTIMETYPE = '';
                    $ADD = '';

                    if (!empty($VALUE2['TYPE_ID'])) {
                        if ($VALUE2['TYPE_ID'] == '1' || $VALUE2['TYPE_ID'] == '5') {
                            if (!empty($VALUE2['DAT']) && count($VALUE2['DAT'])) {
                                $HEADING = $VALUE2['TYPE'];

                                $style_str = 'font-weight:bold;text-transform:uppercase;';
                            } else {
                                $HEADING = (!empty($VALUE2['EXERCISE'])) ? $VALUE2['EXERCISE'] : $VALUE2['TYPE'];
                                $SETS = $VALUE2['SETS'];
                                $REPS = $VALUE2['REPS'];
                                $TIME = $VALUE2['TIME'];

                                if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                                else $TIMETYPE = 's';

                                $REST = $VALUE2['REST'];
                                if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                                else $RESTTIMETYPE = 's';

                                if (!empty($TIME)) $TIME .= $TIMETYPE;
                                if (!empty($REST)) $REST .= $RESTTIMETYPE;

                                $style_str = (!empty($VALUE2['EXERCISE'])) ? '' : 'font-weight:bold;text-transform:uppercase;';
                            }
                            $ADD = 'N';
                        } else {
                            $HEADING = $VALUE2['TYPE'];
                            $SETS = $VALUE2['SETS'];
                            $REPS = $VALUE2['REPS'];
                            $TIME = $VALUE2['TIME'];

                            if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                            else $TIMETYPE = 's';

                            $REST = $VALUE2['REST'];
                            if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                            else $RESTTIMETYPE = 's';

                            if (!empty($TIME)) $TIME .= $TIMETYPE;
                            if (!empty($REST)) $REST .= $RESTTIMETYPE;

                            $style_str = 'font-weight:bold;text-transform:uppercase;';
                            $ADD = 'Y';
                        }
                    } else {
                        $HEADING = (!empty($VALUE2['EXERCISE'])) ? $VALUE2['EXERCISE'] : $VALUE2['TYPE'];
                        $SETS = $VALUE2['SETS'];
                        $REPS = $VALUE2['REPS'];
                        $TIME = $VALUE2['TIME'];

                        if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                        else $TIMETYPE = 's';

                        $REST = $VALUE2['REST'];
                        if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                        else $RESTTIMETYPE = 's';

                        if (!empty($TIME)) $TIME .= $TIMETYPE;
                        if (!empty($REST)) $REST .= $RESTTIMETYPE;

                        $style_str = (!empty($VALUE2['EXERCISE'])) ? '' : 'font-weight:bold;text-transform:uppercase;';
                    }

                    $DET = '';
                    if (!empty($SETS) && $SETS != '-1') $DET .= 'SETS:' . $SETS . '|';
                    if (!empty($REPS) && $REPS != '-1') $DET .= 'REPS:' . $REPS . '|';
                    if (!empty($TIME)) $DET .= 'TIME:' . $TIME . '|';
                    if (!empty($REST)) $DET .= 'REST:' . $REST . '|';
                    if (!empty($DET)) $DET = ' (' . substr($DET, 0, '-1') . ')';

                    $addDET = '';
                    if ($ADD == 'Y') $addDET = '&nbsp;&nbsp;<a onClick="EditProgramWorkoutExerciseDetails2(\'C\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'' . $KEY2 . '\',\'0\');" title="Add Exercises Under ' . $HEADING . '"><i class="fa fa-plus" style="float:right; padding-left:10px;"></i></a>';

                    $MODAL_BODY .= '<li class="dd-item" data-id="' . $KEY2 . 'E"><div class="dd-handle" style="cursor:pointer;' . $style_str . '">' . $HEADING . $DET . $addDET . '<a onClick="EditProgramWorkoutExerciseDetails(\'C\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'' . $KEY2 . '\');" title="Edit ' . $HEADING . '"><i class="fa fa-edit" style="float:right;"></i></a></div>';
                    if (!empty($VALUE2['DAT']) && count($VALUE2['DAT'])) {
                        $MODAL_BODY .= '<ol class="dd-list">';
                        foreach ($VALUE2['DAT'] as $KEY3 => $VALUE3) {
                            $SETS2 = $REPS2 = $TIME2 = $style_str2 = $addDET2 = '';
                            $HEADING2 = $VALUE3['EXERCISE'];
                            //$SETS2 = $VALUE3['SETS'];
                            $REPS2 = $VALUE3['REPS'];
                            $TIME2 = $VALUE3['TIME'];

                            $DET2 = '';
                            if (!empty($SETS2) && $SETS2 != '-1') $DET2 .= 'SETS:' . $SETS2 . '|';
                            if (!empty($REPS2) && $REPS2 != '-1') $DET2 .= 'REPS:' . $REPS2 . '|';
                            if (!empty($TIME2)) $DET2 .= 'TIME:' . $TIME2 . 's|';
                            if (!empty($DET2)) $DET2 = ' (' . substr($DET2, 0, '-1') . ')';

                            $MODAL_BODY .= '<li class="dd-item" data-id="' . $VALUE3['ID'] . 'D"><div class="dd-handle" style="cursor:pointer;' . $style_str2 . '">' . $HEADING2 . $DET2 . $addDET2 . '<a onClick="EditProgramWorkoutExerciseDetails2(\'C\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'' . $KEY2 . '\',\'' . $VALUE3['ID'] . '\');"><i class="fa fa-edit" style="float:right;"></i></a></div>';
                        }
                        $MODAL_BODY .= '</ol>';
                    }
                    $MODAL_BODY .= '</li>';
                }
                $MODAL_BODY .= '</ol>';
            } else
                $MODAL_BODY .= '<div class="page-title-subheading">No Cooldown Details Added</div>';

            $MODAL_BODY .= '</div>';
            $MODAL_BODY .= '<input type="hidden" id="nestable_list_cooldown_output" name="nestable_list_cooldown_output" class="form-control col-md-12 margin-bottom-10" value="">';
            $MODAL_BODY .= '</div>';
            $MODAL_BODY .= '</div>';
            //WARM ENDS
        }
    } else {
        $MODAL_BODY .= '<div class="main-card mb-3 card">';
        $MODAL_BODY .= '<div class="card-body">';
        $MODAL_BODY .= '<h5 class="card-title">No workout details added for the day</h5>';
        $MODAL_BODY .= '</div>';
        $MODAL_BODY .= '</div>';
    }

    if ($sort == 'Y')
        $MODAL_BODY .= '<button type="button" class="mt-2 btn btn-success" onClick="SaveWorkOutOrder(\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\');">Save Order</button>';
    $MODAL_BODY .= '</div>';
    //NESTABLE ENDS

    //ADD+EDIT FORM STARTS
    $MODAL_BODY .= '<div class="col-md-6" id="EDIT_FORM">';
    $MODAL_BODY .= '<div class="main-card mb-3 card col-md-12">';
    $MODAL_BODY .= '<div class="card-header-tab card-header">';
    $MODAL_BODY .= '<div class="card-header-title font-size-lg text-capitalize font-weight-normal"> <i class="header-icon lnr-book mr-3 text-muted opacity-6"> </i>' . $formTITLE . '</div>';

    if ($addExercise == 'Y') {
        $MODAL_BODY .= '<div class="btn-actions-pane-right text-capitalize">';

        //WARM UP
        $MODAL_BODY .= '<button class="mb-2 mr-1 btn btn-outline-primary btn-sm" onClick="GetOtherFormDetails(\'W\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'0\')"><i class="fa fa-plus fa-w-20"></i>Warmup</button>';

        //EXERCISE
        $MODAL_BODY .= '<button class="mb-2 mr-1 btn btn-outline-focus btn-sm" onClick="GetOtherFormDetails(\'E\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'0\')"><i class="fa fa-plus fa-w-20"></i>Exercise</button>';

        //COOL DOWN
        $MODAL_BODY .= '<button class="mb-2 mr-1 btn btn-outline-alternate btn-sm" onClick="GetOtherFormDetails(\'C\',\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\',\'0\')"><i class="fa fa-plus fa-w-20"></i>Cooldown</button>';

        $MODAL_BODY .= '</div>';
    }


    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '<div class="card-body">';
    $MODAL_BODY .= '<div class="col-md-12">';
    $MODAL_BODY .= '<div class="form-row">';
    $MODAL_BODY .= '<div class="col-md-6">';
    $MODAL_BODY .= '<div class="position-relative form-group">';
    $MODAL_BODY .= '<label for="cmbworkout_wotid" class="">Name</label>';
    $MODAL_BODY .= FillCombo($cmbworkout_wotid, 'cmbworkout_wotid', 'COMBO', '0', $WOTYPE_ARR, '', 'multiselect-dropdown form-control');
    //$MODAL_BODY .= '<input name="txtworkout_title" id="txtworkout_title" type="text" value="'.$txtworkout_title.'" class="form-control" required />';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '<div class="col-md-6">';
    $MODAL_BODY .= '<div class="position-relative form-group">';
    $MODAL_BODY .= '<label for="txtworkout_time" class="">Time (In Minutes)</label>';
    $MODAL_BODY .= '<input name="txtworkout_time" id="txtworkout_time" type="text" value="' . $txtworkout_time . '" class="form-control" required />';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';

    // MODIF20201222 : start
    $MODAL_BODY .= '<div class="form-row">';
    $MODAL_BODY .= '<div class="col-md-12">';
    $MODAL_BODY .= '<div class="position-relative form-group">';
    $MODAL_BODY .= '<label for="txtworkout_desc" class="">Description: </label>';
    $MODAL_BODY .= '<textarea name="txtworkout_desc" id="txtworkout_desc" class="form-control" >' . $txtworkout_desc . '</textarea>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';
    // MODIF20201222 : end

    $MODAL_BODY .= '<div class="form-row">';
    $MODAL_BODY .= '<div class="col-md-12">';
    $MODAL_BODY .= '<div class="position-relative form-group">';
    $MODAL_BODY .= '<label for="txtwarmup_desc" class="">Warmup Description</label>';
    $MODAL_BODY .= '<textarea name="txtwarmup_desc" id="txtwarmup_desc" class="form-control" >' . $txtwarmup_desc . '</textarea>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';

    $MODAL_BODY .= '<div class="form-row">';
    $MODAL_BODY .= '<div class="col-md-12">';
    $MODAL_BODY .= '<div class="position-relative form-group">';
    $MODAL_BODY .= '<label for="txtcooldown_desc" class="">Cooldown Description</label>';
    $MODAL_BODY .= '<textarea name="txtcooldown_desc" id="txtcooldown_desc" class="form-control" >' . $txtcooldown_desc . '</textarea>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';

    $MODAL_BODY .= '<div class="form-row">';
    $MODAL_BODY .= '<div class="col-md-4">';
    $MODAL_BODY .= '<div class="position-relative form-group">';
    $MODAL_BODY .= '<label for="rdworkout_status" class="">Status</label>';
    $MODAL_BODY .= FillRadios($rdworkout_status, 'rdworkout_status', $STATUS_ARR) . '</div>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '<div class="form-row"> </div>';
    $MODAL_BODY .= '<button type="button" class="mt-2 btn btn-success" onClick="UpdateWorkOutDetails(\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\');">Save</button>';
    if ($delBUTTON == 'Y')
        $MODAL_BODY .= '<button type="button" onclick="DeleteWorkOutDetails(\'' . $p_id . '\',\'' . $lvl_id . '\',\'' . $week_id . '\',\'' . $day_id . '\',\'' . $wo_id . '\');" class="mt-2 btn btn-danger">Delete</button>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';
    $MODAL_BODY .= '</div>';
    //ADD+EDIT FORM ENDS

    return $MODAL_TITLE . '~*~' . $MODAL_BODY;
}

function MultiDimensionalArraySort($array, $on, $order = SORT_ASC)
{

    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

function LastOnlineSeen($loginDate)
{
    $str = '';

    $date = FormatDate($loginDate, 'H');
    if ($date == TODAY) {
        $diff = abs(strtotime($loginDate) - strtotime(NOW));
        $years   = floor($diff / (365 * 60 * 60 * 24));
        $months  = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days    = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));
        $hours   = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24) / (60 * 60));
        $minuts  = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60) / 60);
        $seconds = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24 - $days * 60 * 60 * 24 - $hours * 60 * 60 - $minuts * 60));

        if (!empty($hours)) {
            if ($hours == '1') $str = $hours . ' hour ago';
            else $str = $hours . ' hours ago';
        } elseif (!empty($minuts)) {
            if ($minuts == '1') $str = $minuts . ' minute ago';
            else $str = $minuts . ' minutes ago';
        } elseif (!empty($seconds)) {
            if ($seconds <= '30') $str = 'Now';
            else $str = $seconds . ' seconds ago';
        }
    } else
        $str = FormatDate($loginDate, 'B');

    return $str;
}

function GetProgramDayWorkoutDetails($pw_id, $KEY, $W, $D)
{
    $EXERCISETYPE_ARR = GetXArrFromYID('select iETID, vName from exercise_types where cStatus="A"', '3');
    $EXERCISE_ARR = GetXArrFromYID('select iExerciseID, vName from exercises where cStatus="A"', '3');
    $WARMUP_ARR = GetXArrFromYID('select iWarmupID, vName from warmups where cStatus="A"', '3');
    $COOLDOWN_ARR = GetXArrFromYID('select iCoolDownID, vName from cooldowns where cStatus="A"', '3');

    $str = '';

    $_wq = 'select iPWOID, iPDiffLvlID, iWeek, iDay, iWOTID, vName, iTimeMin, cStatus from program_workout where iPWOID=' . $pw_id . ' order by iPDiffLvlID, iWeek, iDay';
    $_wr = sql_query($_wq, '');
    if (sql_num_rows($_wr)) {
        while (list($w_ID, $w_DIFFID, $w_WEEK, $w_DAY, $w_WOTID, $w_NAME, $w_TIMEINMIN, $w_STATUS) = sql_fetch_row($_wr)) {
            $w_NAME2 = '';
            if (!empty($w_WOTID)) $w_NAME2 = GetXFromYID('select vName from workout_types where iWOTID=' . $w_WOTID);
            if (!empty($w_NAME2)) $w_NAME = $w_NAME2;

            $pWO[$w_ID] = array('DIFF_ID' => $w_DIFFID, 'WEEK' => $w_WEEK, 'DAY' => $w_DAY, 'NAME' => htmlspecialchars_decode($w_NAME), 'TIME' => $w_TIMEINMIN, 'STATUS' => $w_STATUS, 'WARMUP' => array(), 'EXERCISE' => array(), 'COOLDOWN' => array());

            $WORKOUT_WARMUP_ARR = $WORKOUT_EXERCISE_ARR = $WORKOUT_COOLDOWN_ARR = array();
            $_wq2 = 'select e.iPWExerciseID, e.cType, e.cRefType, e.iRefID, e.iETID, e.iExerciseID, e.iSets, e.iRepetitions, e.iTimeSecs, e.cTimeType, e.iRestTime, e.cRestTimeType, e.cStatus, e2.iPWEDatID, e2.cRefType, e2.iRefID, e2.iRepetitions, e2.iTimeSecs, e2.cTimeType, e2.cStatus from program_workout_exercise as e left join program_workout_exercise_dat as e2 on e.iPWExerciseID=e2.iPWExerciseID where e.iPWOID=' . $w_ID . ' order by FIELD(e.cType, "W","E","C"), e.iRank, e2.iRank';
            $_wr2 = sql_query($_wq2, '');
            if (sql_num_rows($_wr2)) {
                while (list($we_ID, $we_TYPE, $we_REFTYPE, $we_REFID, $we_EXERCISETYPE, $we_EXERCISEID, $we_SETS, $we_REPS, $we_TIME, $we_TIMETYPE, $we_RESTTIME, $we_RESTTIMETYPE, $we_STATUS, $we_DAT_ID, $we_DAT_REFTYPE, $we_DAT_REFID, $we_DAT_REPS, $we_DAT_TIME, $we_DAT_TIMETYPE, $we_DAT_STATUS) = sql_fetch_row($_wr2)) {
                    //EXERCISE
                    if ($we_TYPE == 'E') {
                        if (!isset($WORKOUT_EXERCISE_ARR[$we_ID])) {
                            if ($we_REFTYPE == 'E') $E = (!empty($we_EXERCISEID) && isset($EXERCISE_ARR[$we_EXERCISEID])) ? $EXERCISE_ARR[$we_EXERCISEID] : '';
                            elseif ($we_REFTYPE == 'W') $E = (!empty($we_REFID) && isset($WARMUP_ARR[$we_REFID])) ? $WARMUP_ARR[$we_REFID] : '';
                            elseif ($we_REFTYPE == 'C') $E = (!empty($we_REFID) && isset($COOLDOWN_ARR[$we_REFID])) ? $COOLDOWN_ARR[$we_REFID] : '';

                            $SETS = $we_SETS;
                            $REPS = $we_REPS;
                            $TIME = $we_TIME;
                            $TIMETYPE = $we_TIMETYPE;
                            $REST = $we_RESTTIME;
                            $RESTTIMETYPE = $we_RESTTIMETYPE;
                            $STATUS = $we_STATUS;
                            $EXERCISETYPE = (!empty($we_EXERCISETYPE) && isset($EXERCISETYPE_ARR[$we_EXERCISETYPE])) ? $EXERCISETYPE_ARR[$we_EXERCISETYPE] : '';


                            $WORKOUT_EXERCISE_ARR[$we_ID] = array('REFTYPE' => $we_REFTYPE, 'REFID' => $we_REFID, 'TYPE' => $EXERCISETYPE, 'TYPE_ID' => $we_EXERCISETYPE, 'EXERCISE' => $E, 'SETS' => $SETS, 'REPS' => $REPS, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'REST' => $REST, 'RESTTIMETYPE' => $RESTTIMETYPE, 'STATUS' => $STATUS, 'DAT' => array());
                        }

                        if (!empty($we_DAT_ID)) {
                            if ($we_DAT_REFTYPE == 'E') $E2 = (!empty($we_DAT_REFID) && isset($EXERCISE_ARR[$we_DAT_REFID])) ? $EXERCISE_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'W') $E2 = (!empty($we_DAT_REFID) && isset($WARMUP_ARR[$we_DAT_REFID])) ? $WARMUP_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'C') $E2 = (!empty($we_DAT_REFID) && isset($COOLDOWN_ARR[$we_DAT_REFID])) ? $COOLDOWN_ARR[$we_DAT_REFID] : '';

                            $REPS2 = $we_DAT_REPS;
                            $TIME = $we_DAT_TIME;
                            $TIMETYPE = $we_DAT_TIMETYPE;
                            $STATUS = $we_DAT_STATUS;

                            array_push($WORKOUT_EXERCISE_ARR[$we_ID]['DAT'], array('ID' => $we_DAT_ID, 'TYPE' => $we_DAT_REFTYPE, 'EXERCISE' => $E2, 'REPS' => $REPS2, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'STATUS' => $STATUS));
                        }
                    }

                    //WARMUP
                    if ($we_TYPE == 'W') {
                        if (!isset($WORKOUT_WARMUP_ARR[$we_ID])) {
                            if ($we_REFTYPE == 'E') $E = (!empty($we_EXERCISEID) && isset($EXERCISE_ARR[$we_EXERCISEID])) ? $EXERCISE_ARR[$we_EXERCISEID] : '';
                            elseif ($we_REFTYPE == 'W') $E = (!empty($we_REFID) && isset($WARMUP_ARR[$we_REFID])) ? $WARMUP_ARR[$we_REFID] : '';
                            elseif ($we_REFTYPE == 'C') $E = (!empty($we_REFID) && isset($COOLDOWN_ARR[$we_REFID])) ? $COOLDOWN_ARR[$we_REFID] : '';

                            $SETS = $we_SETS;
                            $REPS = $we_REPS;
                            $TIME = $we_TIME;
                            $TIMETYPE = $we_TIMETYPE;
                            $REST = $we_RESTTIME;
                            $RESTTIMETYPE = $we_RESTTIMETYPE;
                            $STATUS = $we_STATUS;
                            $EXERCISETYPE = (!empty($we_EXERCISETYPE) && isset($EXERCISETYPE_ARR[$we_EXERCISETYPE])) ? $EXERCISETYPE_ARR[$we_EXERCISETYPE] : '';

                            $WORKOUT_WARMUP_ARR[$we_ID] = array('REFTYPE' => $we_REFTYPE, 'REFID' => $we_REFID, 'TYPE' => $EXERCISETYPE, 'TYPE_ID' => $we_EXERCISETYPE, 'EXERCISE' => $E, 'SETS' => $SETS, 'REPS' => $REPS, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'REST' => $REST, 'RESTTIMETYPE' => $RESTTIMETYPE, 'STATUS' => $STATUS, 'DAT' => array());
                        }

                        if (!empty($we_DAT_ID)) {
                            if ($we_DAT_REFTYPE == 'E') $E2 = (!empty($we_DAT_REFID) && isset($EXERCISE_ARR[$we_DAT_REFID])) ? $EXERCISE_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'W') $E2 = (!empty($we_DAT_REFID) && isset($WARMUP_ARR[$we_DAT_REFID])) ? $WARMUP_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'C') $E2 = (!empty($we_DAT_REFID) && isset($COOLDOWN_ARR[$we_DAT_REFID])) ? $COOLDOWN_ARR[$we_DAT_REFID] : '';

                            $REPS2 = $we_DAT_REPS;
                            $TIME = $we_DAT_TIME;
                            $TIMETYPE = $we_DAT_TIMETYPE;
                            $STATUS = $we_DAT_STATUS;

                            array_push($WORKOUT_WARMUP_ARR[$we_ID]['DAT'], array('ID' => $we_DAT_ID, 'TYPE' => $we_DAT_REFTYPE, 'EXERCISE' => $E2, 'REPS' => $REPS2, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'STATUS' => $STATUS));
                        }
                    }

                    //COOLDOWN
                    if ($we_TYPE == 'C') {
                        if (!isset($WORKOUT_COOLDOWN_ARR[$we_ID])) {
                            if ($we_REFTYPE == 'E') $E = (!empty($we_EXERCISEID) && isset($EXERCISE_ARR[$we_EXERCISEID])) ? $EXERCISE_ARR[$we_EXERCISEID] : '';
                            elseif ($we_REFTYPE == 'W') $E = (!empty($we_REFID) && isset($WARMUP_ARR[$we_REFID])) ? $WARMUP_ARR[$we_REFID] : '';
                            elseif ($we_REFTYPE == 'C') $E = (!empty($we_REFID) && isset($COOLDOWN_ARR[$we_REFID])) ? $COOLDOWN_ARR[$we_REFID] : '';

                            $SETS = $we_SETS;
                            $REPS = $we_REPS;
                            $TIME = $we_TIME;
                            $TIMETYPE = $we_TIMETYPE;
                            $REST = $we_RESTTIME;
                            $RESTTIMETYPE = $we_RESTTIMETYPE;
                            $STATUS = $we_STATUS;
                            $EXERCISETYPE = (!empty($we_EXERCISETYPE) && isset($EXERCISETYPE_ARR[$we_EXERCISETYPE])) ? $EXERCISETYPE_ARR[$we_EXERCISETYPE] : '';

                            $WORKOUT_COOLDOWN_ARR[$we_ID] = array('REFTYPE' => $we_REFTYPE, 'REFID' => $we_REFID, 'TYPE' => $EXERCISETYPE, 'TYPE_ID' => $we_EXERCISETYPE, 'EXERCISE' => $E, 'SETS' => $SETS, 'REPS' => $REPS, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'REST' => $REST, 'RESTTIMETYPE' => $RESTTIMETYPE, 'STATUS' => $STATUS, 'DAT' => array());
                        }

                        if (!empty($we_DAT_ID)) {
                            if ($we_DAT_REFTYPE == 'E') $E2 = (!empty($we_DAT_REFID) && isset($EXERCISE_ARR[$we_DAT_REFID])) ? $EXERCISE_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'W') $E2 = (!empty($we_DAT_REFID) && isset($WARMUP_ARR[$we_DAT_REFID])) ? $WARMUP_ARR[$we_DAT_REFID] : '';
                            elseif ($we_DAT_REFTYPE == 'C') $E2 = (!empty($we_DAT_REFID) && isset($COOLDOWN_ARR[$we_DAT_REFID])) ? $COOLDOWN_ARR[$we_DAT_REFID] : '';

                            $REPS2 = $we_DAT_REPS;
                            $TIME = $we_DAT_TIME;
                            $TIMETYPE = $we_DAT_TIMETYPE;
                            $STATUS = $we_DAT_STATUS;

                            array_push($WORKOUT_COOLDOWN_ARR[$we_ID]['DAT'], array('ID' => $we_DAT_ID, 'TYPE' => $we_DAT_REFTYPE, 'EXERCISE' => $E2, 'REPS' => $REPS2, 'TIME' => $TIME, 'TIMETYPE' => $TIMETYPE, 'STATUS' => $STATUS));
                        }
                    }
                }
            }

            $pWO[$w_ID]['WARMUP'] = $WORKOUT_WARMUP_ARR;
            $pWO[$w_ID]['EXERCISE'] = $WORKOUT_EXERCISE_ARR;
            $pWO[$w_ID]['COOLDOWN'] = $WORKOUT_COOLDOWN_ARR;
        }
    }

    $ADD = 'Y';
    $DISPLAY = $EXERCISE = '';
    if (!empty($pWO) && count($pWO)) {
        foreach ($pWO as $wKEY => $wVALUE) {
            if ($wVALUE['DIFF_ID'] == $KEY && $wVALUE['WEEK'] == $W && $wVALUE['DAY'] == $D) {
                $workOUT_ID = $wKEY;
                $DISPLAY .= '<h5 class="menu-header-title">' . $wVALUE['NAME'] . '</h5>';
                if (!empty($wVALUE['TIME']))
                    $DISPLAY .= '<h6 class="menu-header-subtitle"> Time: <b class="text-danger">' . $wVALUE['TIME'] . ' Mins </b></h6>';

                //WARMUP STARTS
                if (!empty($wVALUE['WARMUP']) && count($wVALUE['WARMUP'])) {
                    $EXERCISE .= '<div class="inside-card card-body"><h6 class="text-muted text-uppercase font-size-md opacity-7 mb-3 font-weight-normal">WarmUp</h6></div>';
                    foreach ($wVALUE['WARMUP'] as $KEY2 => $VALUE2) {
                        $EXERCISE .= '<div class="inside-card card-body">';
                        $DET = '';

                        if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                        else $TIMETYPE = 's';

                        $TIME = $VALUE2['TIME'];
                        if (!empty($TIME)) $TIME .= $TIMETYPE;

                        if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                        else $RESTTIMETYPE = 's';

                        $REST = $VALUE2['REST'];
                        if (!empty($REST)) $REST .= $RESTTIMETYPE;

                        if (!empty($VALUE2['SETS']) && $VALUE2['SETS'] != '1') $DET .= '<p>SETS:<span>' . $VALUE2['SETS'] . '</span></p>';
                        if (!empty($VALUE2['REPS'])) $DET .= '<p>REPS:<span>' . $VALUE2['REPS'] . '</span></p>';
                        if (!empty($TIME)) $DET .= '<p>TIME:<span>' . $TIME . '</span></p>';
                        if (!empty($REST)) $DET .= '<p>REST:<span>' . $REST . '</span></p>';

                        if (!empty($VALUE2['TYPE_ID'])) $E = ($VALUE2['TYPE'] == 'Exercise') ? $VALUE2['EXERCISE'] : $VALUE2['TYPE'];
                        else $E = $VALUE2['EXERCISE'];

                        $EXERCISE .= '<h5 class="card-title">' . $E . '</h5>';
                        $EXERCISE .= $DET;
                        $EXERCISE .= '<div class="clear"></div>';

                        if (!empty($VALUE2['DAT']) && count($VALUE2['DAT'])) {
                            $EXERCISE .= '<div class="vertical-time-simple vertical-without-time vertical-timeline vertical-timeline--animate vertical-timeline--one-column sub-cat">';

                            foreach ($VALUE2['DAT'] as $KEY3 => $VALUE3) {
                                $DET2 = '';

                                if (!empty($VALUE3['TIMETYPE'])) $TIMETYPE2 = $VALUE3['TIMETYPE'];
                                else $TIMETYPE2 = 's';

                                $TIME2 = $VALUE3['TIME'];
                                if (!empty($TIME2)) $TIME2 .= $TIMETYPE2;

                                if (!empty($VALUE3['REPS'])) $DET2 .= '<p>REPS:<span>' . $VALUE3['REPS'] . '</span></p>';
                                if (!empty($TIME2)) $DET2 .= '<p>TIME:<span>' . $TIME2 . '</span></p>';

                                $EXERCISE .= '<div class="vertical-timeline-item vertical-timeline-element">';
                                $EXERCISE .= '<div><span class="vertical-timeline-element-icon bounce-in"></span>';
                                $EXERCISE .= '<div class="vertical-timeline-element-content bounce-in">';
                                $EXERCISE .= '<h5 class="timeline-title">' . $VALUE3['EXERCISE'] . '</h5>';
                                $EXERCISE .= $DET2;
                                $EXERCISE .= '</div>';
                                $EXERCISE .= '<div class="clear"></div>';
                                $EXERCISE .= '</div>';
                                $EXERCISE .= '</div>';
                            }

                            $EXERCISE .= '</div>';
                        }


                        $EXERCISE .= '</div>';
                    }
                }
                //WARMUP ENDS

                //EXERCISE STARTS
                if (!empty($wVALUE['EXERCISE']) && count($wVALUE['EXERCISE'])) {
                    $EXERCISE .= '<div class="inside-card card-body"><h6 class="text-muted text-uppercase font-size-md opacity-7 mb-3 font-weight-normal">Workout</h6></div>';
                    foreach ($wVALUE['EXERCISE'] as $KEY2 => $VALUE2) {
                        $EXERCISE .= '<div class="inside-card card-body">';
                        $DET = '';

                        if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                        else $TIMETYPE = 's';

                        $TIME = $VALUE2['TIME'];
                        if (!empty($TIME)) $TIME .= $TIMETYPE;

                        if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                        else $RESTTIMETYPE = 's';

                        $REST = $VALUE2['REST'];
                        if (!empty($REST)) $REST .= $RESTTIMETYPE;

                        if (!empty($VALUE2['SETS']) && $VALUE2['SETS'] != '1') $DET .= '<p>SETS:<span>' . $VALUE2['SETS'] . '</span></p>';
                        if (!empty($VALUE2['REPS'])) $DET .= '<p>REPS:<span>' . $VALUE2['REPS'] . '</span></p>';
                        if (!empty($TIME)) $DET .= '<p>TIME:<span>' . $TIME . '</span></p>';
                        if (!empty($REST)) $DET .= '<p>REST:<span>' . $REST . '</span></p>';

                        if (!empty($VALUE2['TYPE_ID'])) $E = ($VALUE2['TYPE'] == 'Exercise') ? $VALUE2['EXERCISE'] : $VALUE2['TYPE'];
                        else $E = $VALUE2['EXERCISE'];

                        $EXERCISE .= '<h5 class="card-title">' . $E . '</h5>';
                        $EXERCISE .= $DET;
                        $EXERCISE .= '<div class="clear"></div>';

                        if (!empty($VALUE2['DAT']) && count($VALUE2['DAT'])) {
                            $EXERCISE .= '<div class="vertical-time-simple vertical-without-time vertical-timeline vertical-timeline--animate vertical-timeline--one-column sub-cat">';

                            foreach ($VALUE2['DAT'] as $KEY3 => $VALUE3) {
                                $DET2 = '';

                                if (!empty($VALUE3['TIMETYPE'])) $TIMETYPE2 = $VALUE3['TIMETYPE'];
                                else $TIMETYPE2 = 's';

                                $TIME2 = $VALUE3['TIME'];
                                if (!empty($TIME2)) $TIME2 .= $TIMETYPE2;

                                if (!empty($VALUE3['REPS'])) $DET2 .= '<p>REPS:<span>' . $VALUE3['REPS'] . '</span></p>';
                                if (!empty($TIME2)) $DET2 .= '<p>TIME:<span>' . $TIME2 . '</span></p>';

                                $EXERCISE .= '<div class="vertical-timeline-item vertical-timeline-element">';
                                $EXERCISE .= '<div><span class="vertical-timeline-element-icon bounce-in"></span>';
                                $EXERCISE .= '<div class="vertical-timeline-element-content bounce-in">';
                                $EXERCISE .= '<h5 class="timeline-title">' . $VALUE3['EXERCISE'] . '</h5>';
                                $EXERCISE .= $DET2;
                                $EXERCISE .= '</div>';
                                $EXERCISE .= '<div class="clear"></div>';
                                $EXERCISE .= '</div>';
                                $EXERCISE .= '</div>';
                            }

                            $EXERCISE .= '</div>';
                        }


                        $EXERCISE .= '</div>';
                    }
                }
                //EXERCISE ENDS

                //COOLDOWN STARTS
                if (!empty($wVALUE['COOLDOWN']) && count($wVALUE['COOLDOWN'])) {
                    $EXERCISE .= '<div class="inside-card card-body"><h6 class="text-muted text-uppercase font-size-md opacity-7 mb-3 font-weight-normal">Cooldown</h6></div>';
                    foreach ($wVALUE['COOLDOWN'] as $KEY2 => $VALUE2) {
                        $EXERCISE .= '<div class="inside-card card-body">';
                        $DET = '';

                        if (!empty($VALUE2['TIMETYPE'])) $TIMETYPE = $VALUE2['TIMETYPE'];
                        else $TIMETYPE = 's';

                        $TIME = $VALUE2['TIME'];
                        if (!empty($TIME)) $TIME .= $TIMETYPE;

                        if (!empty($VALUE2['RESTTIMETYPE'])) $RESTTIMETYPE = $VALUE2['RESTTIMETYPE'];
                        else $RESTTIMETYPE = 's';

                        $REST = $VALUE2['REST'];
                        if (!empty($REST)) $REST .= $RESTTIMETYPE;

                        if (!empty($VALUE2['SETS']) && $VALUE2['SETS'] != '1') $DET .= '<p>SETS:<span>' . $VALUE2['SETS'] . '</span></p>';
                        if (!empty($VALUE2['REPS'])) $DET .= '<p>REPS:<span>' . $VALUE2['REPS'] . '</span></p>';
                        if (!empty($TIME)) $DET .= '<p>TIME:<span>' . $TIME . '</span></p>';
                        if (!empty($REST)) $DET .= '<p>REST:<span>' . $REST . '</span></p>';

                        if (!empty($VALUE2['TYPE_ID'])) $E = ($VALUE2['TYPE'] == 'Exercise') ? $VALUE2['EXERCISE'] : $VALUE2['TYPE'];
                        else $E = $VALUE2['EXERCISE'];

                        $EXERCISE .= '<h5 class="card-title">' . $E . '</h5>';
                        $EXERCISE .= $DET;
                        $EXERCISE .= '<div class="clear"></div>';

                        if (!empty($VALUE2['DAT']) && count($VALUE2['DAT'])) {
                            $EXERCISE .= '<div class="vertical-time-simple vertical-without-time vertical-timeline vertical-timeline--animate vertical-timeline--one-column sub-cat">';

                            foreach ($VALUE2['DAT'] as $KEY3 => $VALUE3) {
                                $DET2 = '';

                                if (!empty($VALUE3['TIMETYPE'])) $TIMETYPE2 = $VALUE3['TIMETYPE'];
                                else $TIMETYPE2 = 's';

                                $TIME2 = $VALUE3['TIME'];
                                if (!empty($TIME2)) $TIME2 .= $TIMETYPE2;

                                if (!empty($VALUE3['REPS'])) $DET2 .= '<p>REPS:<span>' . $VALUE3['REPS'] . '</span></p>';
                                if (!empty($TIME2)) $DET2 .= '<p>TIME:<span>' . $TIME2 . '</span></p>';

                                $EXERCISE .= '<div class="vertical-timeline-item vertical-timeline-element">';
                                $EXERCISE .= '<div><span class="vertical-timeline-element-icon bounce-in"></span>';
                                $EXERCISE .= '<div class="vertical-timeline-element-content bounce-in">';
                                $EXERCISE .= '<h5 class="timeline-title">' . $VALUE3['EXERCISE'] . '</h5>';
                                $EXERCISE .= $DET2;
                                $EXERCISE .= '</div>';
                                $EXERCISE .= '<div class="clear"></div>';
                                $EXERCISE .= '</div>';
                                $EXERCISE .= '</div>';
                            }

                            $EXERCISE .= '</div>';
                        }


                        $EXERCISE .= '</div>';
                    }
                }
                //COOLDOWN ENDS

                $ADD = 'N';
            }
        }
    }

    $str .= '<div class="dropdown-menu-header mt-0 mb-0">';
    $str .= '<div class="dropdown-menu-header-inner bg-heavy-rain">';
    $str .= '<div class="menu-header-image opacity-1" style="background-image: url(\'dist/assets/images/dropdown-header/city3.jpg\');"></div>';
    $str .= '<div class="menu-header-content text-dark">';

    if ($ADD == 'Y') {
        $str .= '<a style="cursor:pointer" onClick="GetWorkOutDetails(\'' . $KEY . '\',\'' . $W . '\',\'' . $D . '\',\'0\');"><h5 class="menu-header-title">Add Workout</h5></a>';
    } else
        $str .= $DISPLAY;
    $str .= '</div>';
    $str .= '</div>';
    $str .= '</div>';

    if ($ADD != 'Y') {
        if (!empty($EXERCISE)) {
            $str .= '<div class="view_exercise" id="SHOW_EXERCISE_' . $KEY . '_' . $W . '_' . $D . '" onClick="ShowWorkoutExercise(\'' . $KEY . '\',\'' . $W . '\',\'' . $D . '\');" style="cursor:pointer; display:">View Exercises</div>';
            $str .= '<div id="EXERCISE_' . $KEY . '_' . $W . '_' . $D . '" style="display:none;">';
            $str .= $EXERCISE;
            $str .= '<div class="view_exercise" onClick="HideWorkoutExercise(\'' . $KEY . '\',\'' . $W . '\',\'' . $D . '\');" style="cursor:pointer;">Hide Exercises</div>';
            $str .= '</div>';
        }

        $str .= '<ul class="tabs-animated-shadow tabs-animated nav nav-justified tabs-shadow-bordered p-3">';
        $str .= '<li class="nav-item"> <a style="cursor:pointer;" onClick="GetWorkOutDetails(\'' . $KEY . '\',\'' . $W . '\',\'' . $D . '\',\'' . $workOUT_ID . '\');"> <span><i class="fa fa-chevron-right icon-gradient bg-sunny-morning"></i></span> </a> </li>';
        $str .= '<li class="nav-item"> <a style="cursor:pointer;" onClick="CopyWorkOutDetails(\'' . $KEY . '\',\'' . $W . '\',\'' . $D . '\',\'' . $workOUT_ID . '\');"> <span><i class="fa fa-copy icon-gradient bg-tempting-azure"></i></a></span> </a> </li>';
        $str .= '<li class="nav-item"> <a style="cursor:pointer;" onClick="DeleteDayWorkout(\'' . $KEY . '\',\'' . $W . '\',\'' . $D . '\',\'' . $workOUT_ID . '\');"> <span><i class="fa fa-trash icon-gradient bg-love-kiss"></i></a></span> </a> </li>';
        $str .= '</ul>';
    }

    return $str;
}

function GetClientCountDetails()
{
    $arr = array();

    $DATE_30DAYSBEFORE = DateTimeAdd(TODAY, '-30', 0, 0, 0, 0, 0, "Y-m-d");
    $q = 'select count(*), cGender, DATE_FORMAT(dtRegistration,"%Y-%m-%d") as DATE from client where cStatus="A" group by DATE, cGender';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($count, $gender, $date) = sql_fetch_row($r)) {
            if (!isset($arr['TOTAL_CLIENT'])) $arr['TOTAL_CLIENT'] = 0;
            $arr['TOTAL_CLIENT'] = $arr['TOTAL_CLIENT'] + $count;

            if ($gender == 'M') {
                if (!isset($arr['TOTAL_MALE'])) $arr['TOTAL_MALE'] = 0;
                $arr['TOTAL_MALE'] = $arr['TOTAL_MALE'] + $count;
            } elseif ($gender == 'F') {
                if (!isset($arr['TOTAL_FEMALE'])) $arr['TOTAL_FEMALE'] = 0;
                $arr['TOTAL_FEMALE'] = $arr['TOTAL_FEMALE'] + $count;
            }

            if ($date >= $DATE_30DAYSBEFORE) {
                if (!isset($arr['LAST_30DAYCLIENT'])) $arr['LAST_30DAYCLIENT'] = 0;
                $arr['LAST_30DAYCLIENT'] = $arr['LAST_30DAYCLIENT'] + $count;

                if ($gender == 'M') {
                    if (!isset($arr['LAST_30DAYMALE'])) $arr['LAST_30DAYMALE'] = 0;
                    $arr['LAST_30DAYMALE'] = $arr['LAST_30DAYMALE'] + $count;
                } elseif ($gender == 'F') {
                    if (!isset($arr['LAST_30DAYFEMALE'])) $arr['LAST_30DAYFEMALE'] = 0;
                    $arr['LAST_30DAYFEMALE'] = $arr['LAST_30DAYFEMALE'] + $count;
                }
            }
        }
    }

    return $arr;
}

function GetActiveSubscriptionsCountDetails()
{
    $arr = array();

    $q = 'select count(*), cFreeTrial from subscription where cStatus="A" and dEnd>="' . TODAY . '" group by cFreeTrial';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($count, $free_trial) = sql_fetch_row($r)) {
            if (!isset($arr['ACTIVE'])) $arr['ACTIVE'] = 0;
            $arr['ACTIVE'] = $arr['ACTIVE'] + $count;

            if ($free_trial == 'Y') {
                if (!isset($arr['FREE_TRIAL'])) $arr['FREE_TRIAL'] = 0;
                $arr['FREE_TRIAL'] = $arr['FREE_TRIAL'] + $count;
            } elseif ($free_trial == 'N') {
                if (!isset($arr['PAID'])) $arr['PAID'] = 0;
                $arr['PAID'] = $arr['PAID'] + $count;
            } elseif ($free_trial == 'G') {
                if (!isset($arr['GIFTED'])) $arr['GIFTED'] = 0;
                $arr['GIFTED'] = $arr['GIFTED'] + $count;
            }
        }
    }

    return $arr;
}

function GetRevenueSubscriptionsDetails($months)
{
    $arr = array();

    $DATE = DateTimeAdd(TODAY, '', '-' . $months, 0, 0, 0, 0, "Y-m-01");
    $q = 'select DATE_FORMAT(dtSubscription,"%Y-%m-%d") as DATE, sum(fTotal) from subscription where cPaid="Y" and cFreeTrial IN ("N","G") and DATE_FORMAT(dtSubscription,"%Y-%m-%d")>="' . $DATE . '" group by DATE';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($DATE2, $TOTAL) = sql_fetch_row($r)) {
            $date = FormatDate($DATE2, '18') . '-01';
            if (!isset($arr[$date])) $arr[$date] = 0;
            $arr[$date] = $arr[$date] + $TOTAL;
        }
    }

    return $arr;
}

function GetClientWiseTodaysSchedule($date)
{
    $arr = array();

    $PROGRAM_ARR = $LVL_ARR = array();
    $q = 'select s.iSubscriptionID, s.iClientID, c.vName, c.vPic, s.iProgramID, s.iPDiffLvlID, s.dStart from subscription as s join client as c on s.iClientID=c.iClientID where s.dEnd>="' . $date . '" and s.cStatus="A" and c.cStatus="A" order by s.dtSubscription desc';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($sub_id, $client_id, $client_name, $client_pic, $program_id, $diff_id, $sub_startdate) = sql_fetch_row($r)) {
            if ($sub_startdate > $date)
                continue;

            if (isset($PROGRAM_ARR[$program_id])) $PROGRAM_NAME = $PROGRAM_ARR[$program_id];
            else {
                $PROGRAM_NAME = GetXFromYID('select vName from program where iProgramID=' . $program_id);
                $PROGRAM_ARR[$program_id] = $PROGRAM_NAME;
            }

            if (isset($LVL_ARR[$diff_id])) $LVL_NAME = $LVL_ARR[$diff_id];
            else {
                $LVL_NAME = GetXFromYID('select d.vName from difficulty_level as d join program_difficulty_level as pd on d.iDiffLvlID=pd.iDiffLvlID where pd.iProgramID=' . $program_id . ' and pd.iPDiffLvlID=' . $diff_id);
                $LVL_ARR[$diff_id] = $LVL_NAME;
            }

            $SC_PROGRAM = $SC_LEVEL = $SC_WEEK = $SC_DAY = $SC_NAME = $SC_TIME = $SC_STARTED = $SC_SUBMITTED = $SC_TIMETAKEN = $SC_STATUS = $SC_TYPE = '';
            $_sq = 'select iScheduleID, dSchedule, iProgramID, iPDiffLvlID, iWeek, iDay, vName, iTimeMin, dtStarted, dtSubmitted, iTimeTakenMin, cStatus from schedule where iSubscriptionID=' . $sub_id . ' and dSchedule="' . $date . '"';
            $_sr = sql_query($_sq, '');
            if (sql_num_rows($_sr)) {
                while (list($sc_id, $sc_date, $sc_programid, $sc_diffid, $sc_week, $sc_day, $sc_name, $sc_time, $sc_dtstarted, $sc_dtsubmitted, $sc_timetaken, $sc_status) = sql_fetch_row($_sr)) {
                    if (isset($PROGRAM_ARR[$sc_programid])) $PROGRAM_NAME2 = $PROGRAM_ARR[$sc_programid];
                    else {
                        $PROGRAM_NAME2 = GetXFromYID('select vName from program where iProgramID=' . $sc_programid);
                        $PROGRAM_ARR[$sc_programid] = $PROGRAM_NAME2;
                    }

                    if (isset($LVL_ARR[$sc_diffid])) $LVL_NAME2 = $LVL_ARR[$sc_diffid];
                    else {
                        $LVL_NAME2 = GetXFromYID('select d.vName from difficulty_level as d join program_difficulty_level as pd on d.iDiffLvlID=pd.iDiffLvlID where pd.iProgramID=' . $sc_programid . ' and pd.iPDiffLvlID=' . $sc_diffid);
                        $LVL_ARR[$sc_diffid] = $LVL_NAME2;
                    }

                    $SC_PROGRAM = $PROGRAM_NAME2;
                    $SC_LEVEL = $LVL_NAME2;
                    $SC_WEEK = $sc_week;
                    $SC_DAY = $sc_day;
                    $SC_NAME = $sc_name;
                    $SC_TIME = $sc_time;
                    $SC_STARTED = $sc_dtstarted;
                    $SC_SUBMITTED = $sc_dtsubmitted;
                    $SC_TIMETAKEN = $sc_timetaken;
                    $SC_STATUS = $sc_status;
                    $SC_TYPE = 'S';
                }
            } else {
                $SCHEDULE_DATE = array();
                $i = 0;
                do {
                    $DATE = DateTimeAdd($sub_startdate, $i, 0, 0, 0, 0, 0, 'Y-m-d');
                    $SCHEDULE_DATE[$DATE] = $DATE;

                    $i++;
                } while ($DATE < $date);

                $DIFF_ARR = $DIFF_ARR2 = array();
                $_dq = 'select iPDiffLvlID, iDiffLvlID, iWeeks from program_difficulty_level where iProgramID=' . $program_id . ' and cStatus="A" order by iRank';
                $_dr = sql_query($_dq, '');
                if (sql_num_rows($_dr)) {
                    while (list($pID, $pDIFF, $pWEEKS) = sql_fetch_row($_dr))
                        $DIFF_ARR[$pID]['WEEKS'] = $pWEEKS;
                }

                $J = 0;
                foreach ($DIFF_ARR as $dKEY => $dVALUE) {
                    $WEEKS = $dVALUE['WEEKS'];
                    if ($dKEY == $diff_id) {
                        $DIFF_ARR2[$dKEY] = $WEEKS;
                        $J++;
                    }

                    if (empty($J))
                        continue;
                    else
						if (!isset($DIFF_ARR2[$dKEY])) $DIFF_ARR2[$dKEY] = $WEEKS;
                }

                $START_DATE = $sub_startdate;
                $END_DATE = TODAY;

                $START_DATE2 = $START_DATE;
                $DAYWISE_ARR = array();
                foreach ($DIFF_ARR2 as $dKEY => $dVALUE) {
                    $WEEKS = $dVALUE;
                    for ($W = 1; $W <= $WEEKS; $W++) {
                        $DAYS = 7;
                        for ($D = 1; $D <= $DAYS; $D++) {
                            if ($START_DATE2 <= $END_DATE) {
                                $DAYWISE_ARR[$START_DATE2] = array('LVL' => $dKEY, 'WEEK' => $W, 'DAY' => $D);
                                $START_DATE2 = DateTimeAdd($START_DATE2, 1, 0, 0, 0, 0, 0, 'Y-m-d');
                            }
                        }
                    }
                }

                $pWO = array();
                $_wq = 'select iPWOID, iPDiffLvlID, iWeek, iDay, iWOTID, vName, iTimeMin, vDesc, cStatus from program_workout where iProgramID=' . $program_id . ' order by iPDiffLvlID, iWeek, iDay';
                $_wr = sql_query($_wq, '');
                if (sql_num_rows($_wr)) {
                    while (list($w_ID, $w_DIFFID, $w_WEEK, $w_DAY, $w_WOTID, $w_NAME, $w_TIMEINMIN, $w_DESC, $w_STATUS) = sql_fetch_row($_wr)) {
                        if (!empty($w_WOTID)) $w_NAME = (isset($WOTYPE_ARR[$w_WOTID])) ? $WOTYPE_ARR[$w_WOTID] : $w_NAME;
                        $pWO[$w_ID] = array('DIFF_ID' => $w_DIFFID, 'WEEK' => $w_WEEK, 'DAY' => $w_DAY, 'NAME' => htmlspecialchars_decode($w_NAME), 'TIME' => $w_TIMEINMIN, 'DESC' => htmlspecialchars_decode($w_DESC), 'STATUS' => $w_STATUS);
                    }
                }

                foreach ($SCHEDULE_DATE as $DATE) {
                    if (isset($DAYWISE_ARR[$DATE])) {
                        $LEVEL = $DAYWISE_ARR[$DATE]['LVL'];
                        $WEEK = $DAYWISE_ARR[$DATE]['WEEK'];
                        $DAY = $DAYWISE_ARR[$DATE]['DAY'];

                        if (!isset($LVL_ARR[$LEVEL])) {
                            $diffLVL3 = GetXFromYID('select d.vName from difficulty_level as d join program_difficulty_level as d2 on d.iDiffLvlID=d2.iDiffLvlID where iPDiffLvlID=' . $LEVEL . ' and iProgramID=' . $program_id);
                            $LVL_ARR[$LEVEL] = $diffLVL3;
                        } else
                            $diffLVL3 = $LVL_ARR[$LEVEL];

                        $daySET = 'N';
                        foreach ($pWO as $KEY => $VALUE) {
                            if ($VALUE['DIFF_ID'] == $LEVEL && $VALUE['WEEK'] == $WEEK && $VALUE['DAY'] == $DAY) {
                                if ($DATE == TODAY) {
                                    $SC_PROGRAM = $PROGRAM_NAME;
                                    $SC_LEVEL = $diffLVL3;
                                    $SC_WEEK = $WEEK;
                                    $SC_DAY = $DAY;
                                    $SC_NAME = $VALUE['NAME'];
                                    $SC_TIME = $VALUE['TIME'];
                                    $SC_TYPE = 'P';

                                    $daySET = 'Y';
                                }
                            }
                        }

                        if ($daySET == 'N') {
                            if ($DATE == TODAY) {
                                $SC_PROGRAM = $PROGRAM_NAME;
                                $SC_LEVEL = $diffLVL3;
                                $SC_WEEK = $WEEK;
                                $SC_DAY = $DAY;
                                $SC_NAME = 'Rest Day';
                                $SC_TYPE = 'P';
                            }
                        }
                    }
                }
            }

            $arr[$sub_id] = array('CLIENT_ID' => $client_id, 'CLIENT_NAME' => htmlspecialchars_decode($client_name), 'CLIENT_PIC' => $client_pic, 'PROGRAM_ID' => $program_id, 'PROGRAM_NAME' => htmlspecialchars_decode($SC_PROGRAM), 'LEVEL_ID' => $diff_id, 'LEVEL_NAME' => htmlspecialchars_decode($SC_LEVEL), 'WEEK' => $SC_WEEK, 'DAY' => $SC_DAY, 'SCHEDULE' => htmlspecialchars_decode($SC_NAME), 'TIME' => $SC_TIME, 'STARTED' => $SC_STARTED, 'SUBMITTED' => $SC_SUBMITTED, 'TIMETAKEN' => $SC_TIMETAKEN, 'STATUS' => $SC_STATUS, 'TYPE' => $SC_TYPE);
        }
    }

    return $arr;
}

function GetSubscriptionDueSoonDetails()
{
    $arr = array();
    $CLIENT = array();
    $_cq = 'select iClientID, vName, vPic, vEmailID, vNotificationID from client where cStatus="A"';
    $_cr = sql_query($_cq, '');
    if (sql_num_rows($_cr)) {
        while (list($cID, $cNAME, $cPIC, $cEMAIL, $cNOTIFICATIONID) = sql_fetch_row($_cr))
            $CLIENT[$cID] = array('NAME' => $cNAME, 'PIC' => $cPIC, 'EMAIL' => $cEMAIL, 'NOTIFICATION_ID' => $cNOTIFICATIONID);
    }

    $next15Days = DateTimeAdd(TODAY, '15', 0, 0, 0, 0, 0, 'Y-m-d');
    $PROGRAM_ARR = array();
    $q = 'select iSubscriptionID, iClientID, iProgramID, cType, dEnd from subscription where dEnd>="' . TODAY . '" and cStatus="A"';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($sub_id, $client_id, $program_id, $program_type, $end) = sql_fetch_row($r)) {
            if (!isset($arr[$client_id])) {
                if ($end < $next15Days) {
                    $PROGRAM_NAME = '';
                    if (isset($PROGRAM_ARR[$program_id])) $PROGRAM_NAME = $PROGRAM_ARR[$program_id];
                    else {
                        $PROGRAM_NAME = GetXFromYID('select vName from program where iProgramID=' . $program_id);
                        $PROGRAM_ARR[$program_id] = $PROGRAM_NAME;
                    }

                    $dateDIFF = DateDiff(TODAY, $end);

                    $arr[$client_id] = array('ID' => $sub_id, 'CLIENT_ID' => $client_id, 'CLIENT_NAME' => htmlspecialchars_decode($CLIENT[$client_id]['NAME']), 'CLIENT_PIC' => $CLIENT[$client_id]['PIC'], 'CLIENT_EMAIL' => $CLIENT[$client_id]['EMAIL'], 'CLIENT_NOTIFICATION_ID' => $CLIENT[$client_id]['NOTIFICATION_ID'], 'PROGRAM_ID' => $program_id, 'PROGRAM_NAME' => htmlspecialchars_decode($PROGRAM_NAME), 'PROGRAM_TYPE' => $program_type, 'DAYS' => $dateDIFF, 'END' => $end);
                }

                if (isset($CLIENT[$client_id]))
                    unset($CLIENT[$client_id]);
            }
        }
    }

    if (!empty($CLIENT) && count($CLIENT)) {
        $cKEY = implode(',', array_keys($CLIENT));
        if (!empty($cKEY)) {
            $q2 = 'select iSubscriptionID, iClientID, iProgramID, cType, dEnd from subscription where iClientID IN (' . $cKEY . ') and cStatus!="P" order by dtSubscription desc';
            $r2 = sql_query($q2, '');
            if (sql_num_rows($r2)) {
                while (list($sub_id, $client_id, $program_id, $program_type, $end) = sql_fetch_row($r2)) {
                    if (!isset($arr[$client_id])) {
                        $PROGRAM_NAME = '';
                        if (isset($PROGRAM_ARR[$program_id])) $PROGRAM_NAME = $PROGRAM_ARR[$program_id];
                        else {
                            $PROGRAM_NAME = GetXFromYID('select vName from program where iProgramID=' . $program_id);
                            $PROGRAM_ARR[$program_id] = $PROGRAM_NAME;
                        }

                        $dateDIFF = DateDiff(TODAY, $end);

                        $arr[$client_id] = array('ID' => $sub_id, 'CLIENT_ID' => $client_id, 'CLIENT_NAME' => htmlspecialchars_decode($CLIENT[$client_id]['NAME']), 'CLIENT_PIC' => $CLIENT[$client_id]['PIC'], 'CLIENT_EMAIL' => $CLIENT[$client_id]['EMAIL'], 'CLIENT_NOTIFICATION_ID' => $CLIENT[$client_id]['NOTIFICATION_ID'], 'PROGRAM_ID' => $program_id, 'PROGRAM_NAME' => htmlspecialchars_decode($PROGRAM_NAME), 'PROGRAM_TYPE' => $program_type, 'DAYS' => $dateDIFF, 'END' => $end);

                        if (isset($CLIENT[$client_id]))
                            unset($CLIENT[$client_id]);
                    }
                }
            }
        }
    }

    if (!empty($arr) && count($arr))
        $arr = MultiDimensionalArraySort($arr, 'DAYS', $order = SORT_ASC);

    return $arr;
}

function GetClientNeedingAttentionDetails()
{
    $arr = array();
    $CLIENT = array();
    $_cq = 'select iClientID, vName, vPic, vEmailID, vNotificationID from client where cStatus="A"';
    $_cr = sql_query($_cq, '');
    if (sql_num_rows($_cr)) {
        while (list($cID, $cNAME, $cPIC, $cEMAIL, $cNOTIFICATIONID) = sql_fetch_row($_cr))
            $CLIENT[$cID] = array('NAME' => $cNAME, 'PIC' => $cPIC, 'EMAIL' => $cEMAIL, 'NOTIFICATION_ID' => $cNOTIFICATIONID);
    }

    $last7Days = DateTimeAdd(TODAY, '-7', 0, 0, 0, 0, 0, 'Y-m-d');
    $last7DayARR = array();
    $i = 0;
    do {
        $DATE = DateTimeAdd($last7Days, $i, 0, 0, 0, 0, 0, 'Y-m-d');
        $last7DayARR[$DATE] = $DATE;

        $i++;
    } while ($DATE < TODAY);

    if (isset($last7DayARR[TODAY])) unset($last7DayARR[TODAY]);
    $arr2 = $PROGRAM_ARR = array();
    $q = 'select s.iSubscriptionID, s.iClientID, s.iProgramID, s.cType, s.dStart, s2.iScheduleID, s2.dSchedule from subscription as s join schedule as s2 on s.iSubscriptionID=s2.iSubscriptionID where s.cStatus="A" and s.dEnd>="' . $last7Days . '" and dEnd>="' . TODAY . '"';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($sub_id, $client_id, $program_id, $program_type, $start, $sc_id, $schedule) = sql_fetch_row($r)) {
            if (!isset($arr2[$sub_id])) {
                $PROGRAM_NAME = '';
                if (isset($PROGRAM_ARR[$program_id])) $PROGRAM_NAME = $PROGRAM_ARR[$program_id];
                else {
                    $PROGRAM_NAME = GetXFromYID('select vName from program where iProgramID=' . $program_id);
                    $PROGRAM_ARR[$program_id] = $PROGRAM_NAME;
                }

                $arr2[$sub_id] = array('ID' => $sub_id, 'CLIENT_ID' => $client_id, 'CLIENT_NAME' => htmlspecialchars_decode($CLIENT[$client_id]['NAME']), 'CLIENT_PIC' => $CLIENT[$client_id]['PIC'], 'CLIENT_EMAIL' => $CLIENT[$client_id]['EMAIL'], 'CLIENT_NOTIFICATION_ID' => $CLIENT[$client_id]['NOTIFICATION_ID'], 'PROGRAM_ID' => $program_id, 'PROGRAM_NAME' => htmlspecialchars_decode($PROGRAM_NAME), 'PROGRAM_TYPE' => $program_type, 'START' => $start, 'DATE' => array());
            }

            if (!empty($schedule)) array_push($arr2[$sub_id]['DATE'], $schedule);
        }
    }

    if (!empty($last7DayARR) && count($last7DayARR)) {
        foreach ($arr2 as $KEY => $VALUE) {
            $START = $VALUE['START'];
            $last7DayARR2 = $last7DayARR;
            foreach ($last7DayARR2 as $date)
                if ($date < $START) unset($last7DayARR2[$date]);

            $MISSED_DAYS = 0;
            $MISSED_DATE = '';
            if (!empty($last7DayARR2) && count($last7DayARR2)) {
                $DATES = $VALUE['DATE'];
                $DATES2 = array();
                if (!empty($DATES)) {
                    foreach ($DATES as $d)
                        $DATES2[$d] = $d;
                }

                foreach ($last7DayARR2 as $d2) {
                    if (!isset($DATES2[$d2])) {
                        $MISSED_DAYS = $MISSED_DAYS + 1;
                        $MISSED_DAYS = $MISSED_DAYS + 1;
                    }
                }
            }

            if ($MISSED_DAYS > 0)
                $arr[$KEY] = array('ID' => $VALUE['ID'], 'CLIENT_ID' => $VALUE['CLIENT_ID'], 'CLIENT_NAME' => $VALUE['CLIENT_NAME'], 'CLIENT_PIC' => $VALUE['CLIENT_PIC'], 'CLIENT_EMAIL' => $VALUE['CLIENT_EMAIL'], 'CLIENT_NOTIFICATION_ID' => $VALUE['CLIENT_NOTIFICATION_ID'], 'PROGRAM_ID' => $VALUE['PROGRAM_ID'], 'PROGRAM_NAME' => $VALUE['PROGRAM_NAME'], 'PROGRAM_TYPE' => $VALUE['PROGRAM_TYPE'], 'START' => $VALUE['START'], 'MISSED_DAYS' => $MISSED_DAYS, 'MISSED_DATE' => $MISSED_DATE);
        }
    }

    return $arr;
}

function SendMail_OLD($from = '', $from_name = '', $to = '', $cc = '', $bcc = '', $reply_to = '', $subject = '', $content = '', $template = 'N', $link = '')
{
    require_once("class.phpmailer.php");

    $mail_body = '';
    if ($template == 'Y') $mail_body = file_get_contents(SITE_ADDRESS . 'mail_body.php');
    if (!empty($mail_body)) {
        $mail_body = str_replace('<CONTENT>', $content, $mail_body);
        $mail_body = str_replace('<LINK>', $link, $mail_body);
    } else
        $mail_body = $content;

    $Mail = new PHPMailer();
    $Mail->From = $from;
    $Mail->FromName = $from_name;
    $Mail->AddAddress($to);
    if (!empty($cc)) $Mail->AddCC($cc);
    if (!empty($bcc)) $Mail->AddBCC($bcc);
    if (!empty($reply_to)) $Mail->AddReplyTo($reply_to);

    $Mail->WordWrap = 50; // set word wrap
    $Mail->IsHTML(true);
    $Mail->MsgHTML($mail_body);
    $Mail->Subject = $subject;
    $Mail->Send();
}

function SendWebPushrPushNotifications($title, $message, $target_url, $PIC, $sender_id)
{
    $end_point = 'https://api.webpushr.com/v1/notification/send/sid';
    $http_header = array(
        "Content-Type: Application/Json",
        "webpushrKey: c7f99b30703b86d0c9dd79685e2e69ea",
        "webpushrAuthToken: 21423"
    );

    /*$req_data = array(
		'title' 			=> "Notification title", //required
		'message' 		=> "Notification message", //required
		'target_url'	=> 'https://www.webpushr.com', //required
		'sid'		=> '36252' //required
	);*/

    $req_data = array(
        'title'             => $title, //required
        'message'         => $message, //required
        'target_url'    => $target_url, //required
        'sid'        => $sender_id, //required
        'icon'        => 'https://pwa.thatlifestylecoach.com/assets/img/icon/192x192.png', //required
        'image'            => $PIC,
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
    curl_setopt($ch, CURLOPT_URL, $end_point);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    //echo $response;
}

function SendWebPushrPushNotificationsToAll($title, $message, $target_url, $PIC)
{
    $end_point = 'https://api.webpushr.com/v1/notification/send/all';
    $http_header = array(
        "Content-Type: Application/Json",
        "webpushrKey: c7f99b30703b86d0c9dd79685e2e69ea",
        "webpushrAuthToken: 21423"
    );
    $req_data = array(
        'title'         => $title, //required
        'message'         => $message, //required
        'target_url'    => $target_url, //required
        //'name'			=> 'Test campain',
        'icon'            => 'https://pwa.thatlifestylecoach.com/assets/img/icon/192x192.png',
        'image'            => $PIC,
        'auto_hide'        => 1,
        //'expire_push'	=> '5m',
        //'send_at'		=> '2019-10-10 19:31 +5:30',
        /*'action_buttons'=> array(	
			array('title'=> 'Demo', 'url' => 'https://www.webpushr.com/demo'),
			array('title'=> 'Rates', 'url' => 'https://www.webpushr.com/pricing')
		)*/
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, $http_header);
    curl_setopt($ch, CURLOPT_URL, $end_point);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($req_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    //echo $response;
}

function SendMail($from = '', $from_name = '', $to = '', $cc = '', $bcc = '', $reply_to = '', $subject = '', $content = '', $template = 'N', $image = '', $FILES = array())
{
    require_once("class.phpmailer.php");

    $mail_body = '';
    if ($template == 'Y') $mail_body = file_get_contents(SITE_ADDRESS . 'mail_body.php');
    if (!empty($mail_body)) {
        $mail_body = str_replace('<IMAGE>', $image, $mail_body);
        $mail_body = str_replace('<CONTENT>', $content, $mail_body);
    } else
        $mail_body = $content;

    $Mail = new PHPMailer();
    $Mail->From = $from;
    $Mail->FromName = $from_name;
    $Mail->AddAddress($to);
    if (!empty($cc)) {
        $cc_arr = explode(",", $cc);
        if (!empty($cc_arr) && count($cc_arr)) {
            foreach ($cc_arr as $cc_email)
                $Mail->AddCC($cc_email);
        }
    }

    if (!empty($bcc)) {
        $bcc_arr = explode(",", $bcc);
        if (!empty($bcc_arr) && count($bcc_arr)) {
            foreach ($bcc_arr as $bcc_email)
                $Mail->AddBCC($bcc_email);
        }
    }

    // if(!empty($bcc)) $Mail->AddBCC($bcc);

    if (!empty($FILES)) {
        $Mail->AddAttachment($FILES);
    }


    if (!empty($reply_to)) $Mail->AddReplyTo($reply_to);

    $Mail->WordWrap = 50; // set word wrap
    $Mail->IsHTML(true);
    $Mail->MsgHTML($mail_body);
    $Mail->Subject = $subject;
    $Mail->Send();
}

function url_get_contents($Url)
{
    if (!function_exists('curl_init')) {
        die('CURL is not installed!');
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $Url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function SendMailReply($from = '', $from_name = '', $to = '', $cc = '', $bcc = '', $reply_to = '', $subject = '', $content = '', $subject_user = "", $reply_str = '', $template = 'N', $image = '', $file = "")
{
    require_once("class.phpmailer.php");

    $mail_body = '';
    if ($template == 'Y') $mail_body = url_get_contents(SITE_ADDRESS . 'mail_body.php');

    if (!empty($mail_body)) {
        $mail_body = str_replace('<IMAGE>', $image, $mail_body);
        $mail_body = str_replace('<CONTENT>', $content, $mail_body);
    } else
        $mail_body = $content;

    $Mail = new PHPMailer();
    $Mail->From = $from;
    $Mail->FromName = $from_name;
    $Mail->AddAddress($to);
    if (!empty($cc)) $Mail->AddCC($cc);
    if (!empty($bcc)) $Mail->AddBCC($bcc);
    if (!empty($reply_to)) $Mail->AddReplyTo($reply_to);

    $Mail->WordWrap = 50; // set word wrap
    $Mail->IsHTML(true);
    $Mail->MsgHTML($mail_body);
    $Mail->Subject = $subject;
    // $Mail->Send();

    /*if(isset($FILES['myfiles'])) 
	{
		$Mail->AddAttachment($FILES['myfiles']['tmp_name'],$FILES['myfiles']['name']);
	}		*/

    if (!empty($reply_str)) {
        $reply_content = "";
        if ($template == 'Y') {
            $reply_content = url_get_contents(SITE_ADDRESS . 'mail_body.php');
            $reply_content = str_replace('<IMAGE>', $image, $reply_content);
            $reply_content = str_replace('<CONTENT>', $reply_str, $reply_content);
        } else
            $reply_content = $reply_str;

        $AutoMail = new PHPMailer();
        $AutoMail->From = $from;
        $AutoMail->FromName = $from_name;
        $AutoMail->AddAddress($reply_to);
        $AutoMail->AddReplyTo($to);
        $AutoMail->WordWrap = 50;
        $AutoMail->IsHTML(true);
        $AutoMail->Subject = $subject_user;
        $AutoMail->MsgHTML($reply_content);
        $AutoMail->Send();
    }

    if ($Mail->Send()) {
        return 'OK';
    } else {
        return 'Mail Sending Failed';
    }
}

function GetAdviceRelatedToPatient($p_id)
{
    $arr = array();

    $q = 'select dtCreation, cSenderRefType, iSenderRefID, vAdvice, cRefType, iRefID from pat_advice where cStatus!="X" and iPatID=' . $p_id . ' order by dtCreation';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($dt, $sender_type, $sender_id, $advice, $receiver_type, $receiver_id) = sql_fetch_row($r)) {
            $DATE = FormatDate($dt, 'H');
            $arr[$DATE][] = array('DATE' => $dt, 'TYPE' => 'NOTE', 'SENDER_TYPE' => $sender_type, 'SENDER_ID' => $sender_id, 'ADVICE' => htmlspecialchars_decode($advice), 'RECEIVER_TYPE' => $receiver_type, 'RECEIVER_ID' => $receiver_id);
        }
    }

    return $arr;
}

function GetTriggerRelatedToPatient($p_id)
{
    $arr = array();

    $q = 'select dtEntry, dDate, cType, iOxy, iTemp, iPulse, cCough, cHeadAche, cShortnessBreath, cTiredness, cChestPain, cDrowsiness, vNotes, iVolunteerID, vNotes_backoffice, cDismiss, cFlagAlert from log_trigger where iPatID=' . $p_id . ' order by dtEntry';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($dt, $date, $type, $oxy, $temp, $pulse, $cough, $headache, $shortnessbreath, $tiredness, $chestpain, $drowsiness, $notes, $v_id, $notes2, $dismiss, $flag) = sql_fetch_row($r)) {
            $DATE = FormatDate($dt, 'H');
            $arr[$DATE][] = array('DATE' => $dt, 'TYPE' => 'TRIGGER', 'DATE2' => $date, 'TYPE2' => $type, 'SpO2' => $oxy, 'TEMPERATURE' => $temp, 'PULSE' => $pulse, 'COUGH' => $cough, 'HEADACHE' => $headache, 'SHORTNESS_BREATH' => $shortnessbreath, 'TIREDNESS' => $tiredness, 'CHEST_PAIN' => $chestpain, 'DROWSINESS' => $drowsiness, 'NOTES' => $notes, 'VOLUNTEER_ID' => $v_id, 'NOTES2' => $notes2, 'DISMISS' => $dismiss, 'FLAG' => $flag);
        }
    }

    return $arr;
}

function GetStatusLogRelatedToPatient($p_id)
{
    $arr = array();

    $q = 'select dtDate, cRefType, iRefID, cStatus, vText from pat_statuslog where iPatID=' . $p_id . ' order by dtDate';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($dt, $reftype, $refid, $status, $text) = sql_fetch_row($r)) {
            $DATE = FormatDate($dt, 'H');
            $arr[$DATE][] = array('DATE' => $dt, 'TYPE' => 'LOG', 'REFTYPE' => $reftype, 'REFID' => $refid, 'STATUS' => $status, 'TEXT' => $text);
        }
    }

    return $arr;
}

function MoveInviteToActive($invite_id)
{
    $str = '';
    global $TEST_STATUS_ARR, $PATIENT_STAGE_ARR;

    $q = 'select vName, iAge, cGender, vMobile, vAddress, iPHCID, iSCID, dtInvite, cPositive, iVolunteerID, iPatID from patinvite where iPatInviteID=' . $invite_id;
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        list($name, $age, $gender, $mobile, $address, $phcid, $scid, $dtinvite, $positive, $v_id, $patient_id) = sql_fetch_row($r);

        $rdpositive = 'A';
        if ($positive == 'Y') $rdpositive = 'Y';

        if (empty($patient_id)) {
            LockTable('patient');
            $txtid = NextID('iPatID', 'patient');
            sql_query("insert into patient (iPatID, iPHCID, iSCID, vName, iAge, cGender, vMobile, dtInvite, dtOnboarding, cPositive, cAlertFlag, cStage, cContactedPatient, cStatus) values ('$txtid', '$phcid', '$scid', '$name', '$age', '$gender', '$mobile', '$dtinvite', '" . NOW . "', '$rdpositive', 'N', 'I', 'N', 'A')", '');
            UnLockTable();

            sql_query("update patinvite set iPatID='$txtid' where iPatInviteID='$invite_id'", '');
            if (empty($v_id)) {
                //AssignVolunteerToPatient($invite_id,1);
                $v_id = GetActiveVolunteer();
                sql_query("update patinvite set iVolunteerID='$v_id' where iPatInviteID='$invite_id'", '');
                sql_query("update patient set iVolunteerID='$v_id' where iPatID='$txtid'", '');

                UpdateVolunteerList($v_id);
                /*$allocated = GetXFromYID('select count(*) from patinvite where iVolunteerID='.$v_id);
				$active = GetXFromYID('select count(*) from patient where iVolunteerID='.$v_id.' and cStage IN ("I","H")'); //cStage NOT IN ("C","D")

				sql_query("update volunteer set iNumPat_allocated='$allocated', iNumActivePatients='$active' where iVolunteerID='$v_id'","");*/
            } else {
                sql_query("update patient set iVolunteerID='$v_id' where iPatID='$txtid'", '');

                UpdateVolunteerList($v_id);
                /*$allocated = GetXFromYID('select count(*) from patinvite where iVolunteerID='.$v_id);
				$active = GetXFromYID('select count(*) from patient where iVolunteerID='.$v_id.' and cStage IN ("I","H")'); //cStage NOT IN ("C","D")

				sql_query("update volunteer set iNumPat_allocated='$allocated', iNumActivePatients='$active' where iVolunteerID='$v_id'","");*/
            }

            if (!empty($v_id)) {
                $doc_id = GetXFromYID('select iDoctorID from volunteer where iVolunteerID=' . $v_id);
                if (!empty($doc_id) && $doc_id != '-1') {
                    sql_query("update patient set iDocID='$doc_id' where iPatID='$txtid'", '');

                    UpdateDoctorList($doc_id);
                    /*$allocated = GetXFromYID('select count(*) from patient where iDocID='.$doc_id);
					$active = GetXFromYID('select count(*) from patient where iDocID='.$doc_id.' and cStage IN ("I","H")'); //cStage NOT IN ("C","D")
					sql_query("update doctors set iNumPat_allocated=$allocated, iNumActivePatients=$active where iDoctorID='$doc_id'",'');*/
                }
            }

            LockTable('pat_statuslog');
            $sid = NextID('iPSLogID', 'pat_statuslog');
            sql_query("insert into pat_statuslog values ('$sid', '$txtid', '" . NOW . "', 'P', '$txtid', '$rdpositive', '" . $TEST_STATUS_ARR[$rdpositive] . "')", '');
            UnLockTable();

            LockTable('pat_statuslog');
            $sid2 = NextID('iPSLogID', 'pat_statuslog');
            sql_query("insert into pat_statuslog values ('$sid2', '$txtid', '" . NOW . "', 'P', '$txtid', 'I', '" . $PATIENT_STAGE_ARR['I'] . "')", '');
            UnLockTable();
        } else
            $txtid = $patient_id;

        $str = $txtid;
    }

    return $str;
}

function AssignVolunteerToPatient2($patient_id = 0, $limit = 1)
{
    $cond = '';
    if (!empty($patient_id))
        $cond .= ' and iPatInviteID=' . $patient_id;

    $PATIENT_ARR = $PATIENT_CENTER_ARR = $PHC_ARR = $SC_ARR = $VOLUNTEER_ARR = $VOLUNTEER_CENTER_ARR = $VOLUNTEER_CENTER_ARR2 = array();
    $q = 'select iPatInviteID, iPHCID, iSCID, iPatID from patinvite where iVolunteerID=0' . $cond . ' limit ' . $limit;
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($p_id, $p_phcid, $p_scid, $p_id2) = sql_fetch_row($r)) {
            if (!isset($PATIENT_CENTER_ARR[$p_phcid . '~' . $p_scid])) $PATIENT_CENTER_ARR[$p_phcid . '~' . $p_scid] = array();
            array_push($PATIENT_CENTER_ARR[$p_phcid . '~' . $p_scid], $p_id);

            if (!in_array($p_phcid, $PHC_ARR)) array_push($PHC_ARR, $p_phcid);
            if (!in_array($p_scid, $SC_ARR)) array_push($SC_ARR, $p_scid);
            if (!empty($p_id2)) $PATIENT_ARR[$p_id] = $p_id2;
        }

        $cond2 = '';
        if (!empty($PHC_ARR) && count($PHC_ARR))
            $cond2 .= ' and iPHCID IN (' . implode(',', $PHC_ARR) . ')';
        if (!empty($SC_ARR) && count($SC_ARR))
            $cond2 .= ' and iSCID IN (' . implode(',', $SC_ARR) . ')';

        $_vq = 'select iVolunteerID, iNumPat_allocated, iPHCID, iSCID from volunteer where cStatus="A"' . $cond2 . ' order by iNumPat_allocated limit ' . $limit;
        $_vr = sql_query($_vq, '');
        if (sql_num_rows($_vr)) {
            while (list($v_id, $v_patients, $v_phcid, $v_scid) = sql_fetch_row($_vr)) {
                if (!isset($VOLUNTEER_CENTER_ARR[$v_phcid . '~' . $v_scid])) $VOLUNTEER_CENTER_ARR[$v_phcid . '~' . $v_scid] = array();
                array_push($VOLUNTEER_CENTER_ARR[$v_phcid . '~' . $v_scid], array('ID' => $v_id, 'PATIENTS' => $v_patients));

                if (!isset($VOLUNTEER_CENTER_ARR2[$v_phcid][$v_id])) $VOLUNTEER_CENTER_ARR2[$v_phcid][$v_id] = array();
                $VOLUNTEER_CENTER_ARR2[$v_phcid][$v_id] = $v_patients;

                $VOLUNTEER_ARR[$v_id] = $v_patients;
            }
        }
    }

    $TEMP_ARR = array();
    if (!empty($PATIENT_CENTER_ARR) && count($PATIENT_CENTER_ARR)) {
        foreach ($PATIENT_CENTER_ARR as $pKEY => $pVALUE) {
            list($phc_id, $sc_id) = explode('~', $pKEY);

            if (isset($VOLUNTEER_CENTER_ARR[$pKEY])) {
                foreach ($pVALUE as $pKEY2 => $pID) {
                    foreach ($VOLUNTEER_CENTER_ARR[$pKEY] as $vKEY => $vVALUE) {
                        $vID = $vVALUE['ID'];
                        if (!isset($TEMP_ARR[$vID])) $TEMP_ARR[$vID] = array();
                        array_push($TEMP_ARR[$vID], $pID);

                        $VOLUNTEER_CENTER_ARR2[$phc_id][$vID] = $VOLUNTEER_CENTER_ARR2[$phc_id][$vID] + 1;
                        $VOLUNTEER_CENTER_ARR[$pKEY][$vKEY] = array('ID' => $vID, 'PATIENTS' => $vVALUE['PATIENTS'] + 1);
                        $VOLUNTEER_CENTER_ARR[$pKEY] =  MultiDimensionalArraySort($VOLUNTEER_CENTER_ARR[$pKEY], 'PATIENTS', SORT_ASC);
                        unset($PATIENT_CENTER_ARR[$pKEY][$pKEY2]);
                        break;
                    }
                }
            }
        }
    }

    if (!empty($PATIENT_CENTER_ARR) && count($PATIENT_CENTER_ARR)) {
        foreach ($PATIENT_CENTER_ARR as $pKEY4 => $pVALUE4) {
            list($phc_id4, $sc_id4) = explode('~', $pKEY4);

            if (empty($pVALUE4)) {
                unset($PATIENT_CENTER_ARR[$pKEY4]);
                continue;
            }

            if (isset($VOLUNTEER_CENTER_ARR2[$phc_id4])) {
                foreach ($pVALUE4 as $pKEY5 => $pID5) {
                    asort($VOLUNTEER_CENTER_ARR2[$phc_id4]);
                    foreach ($VOLUNTEER_CENTER_ARR2[$phc_id4] as $vKEY4 => $vVALUE4) {
                        if (!isset($TEMP_ARR[$vKEY4])) $TEMP_ARR[$vKEY4] = array();
                        array_push($TEMP_ARR[$vKEY4], $pID5);

                        $VOLUNTEER_CENTER_ARR2[$phc_id4][$vKEY4] = $VOLUNTEER_CENTER_ARR2[$phc_id][$vKEY4] + 1;
                        asort($VOLUNTEER_CENTER_ARR2[$phc_id4]);
                        unset($PATIENT_CENTER_ARR[$pKEY4][$pKEY5]);
                        break;
                    }
                }
            }
        }
    }

    if (!empty($TEMP_ARR) && count($TEMP_ARR)) {
        foreach ($TEMP_ARR as $tKEY => $tVALUE) {
            $volunteer_id = $tKEY;

            $allocation_count = 0;
            $invite_id_str = $patient_id_str = '';
            foreach ($tVALUE as $pI_ID) {
                $invite_id_str .= $pI_ID . ',';
                if (isset($PATIENT_ARR[$pI_ID]))
                    $patient_id_str .= $PATIENT_ARR[$pI_ID] . ',';

                $allocation_count++;
            }

            if (!empty($invite_id_str)) {
                $invite_id_str = substr($invite_id_str, 0, '-1');
                $_uiq = "update patinvite set iVolunteerID='$volunteer_id' where iPatInviteID IN (" . $invite_id_str . ")";
                //echo $_uiq.'<br />';
                $_uir = sql_query($_uiq, '');
            }

            if (!empty($patient_id_str)) {
                $patient_id_str = substr($patient_id_str, 0, '-1');
                $_upq = "update patient set iVolunteerID='$volunteer_id' where iPatID IN (" . $patient_id_str . ")";
                //echo $_upq.'<br />';
                $_upr = sql_query($_upq, '');
            }

            if (!empty($allocation_count)) {
                $newCount = $VOLUNTEER_ARR[$volunteer_id] + $allocation_count;
                $_uvq = "update volunteer set iNumPat_allocated='$newCount' where iVolunteerID='$volunteer_id'";
                //echo $_uvq.'<br />';
                $_uvr = sql_query($_uvq, '');
            }
        }
    }
}

function GetPatientAllocatedDetails($reftype, $refid)
{
    $arr = array();

    $cond = '';
    if ($reftype == 'V') $cond .= ' volunteer where iVolunteerID=' . $refid;
    if ($reftype == 'D') $cond .= ' doctors where iDoctorID=' . $refid;

    $allocated = $active = 0;
    $q = 'select iNumPat_allocated, iNumActivePatients from ' . $cond;
    $r = sql_query($q, '');
    if (sql_num_rows($r))
        list($allocated, $active) = sql_fetch_row($r);

    $arr['ALLOCATED'] = $allocated;
    $arr['ACTIVE'] = $active;

    return $arr;
}

function GetPatientDormantList($reftype, $refid)
{
    $arr = array();

    $LAST_TWO_DAYS = DateTimeAdd(TODAY, '-2', 0, 0, 0, 0, 0, 'Y-m-d');

    $cond = '';
    if ($reftype == 'V') $cond .= ' and iVolunteerID=' . $refid;
    if ($reftype == 'D') $cond .= ' and iDocID=' . $refid;

    $q = 'select iPatID, iPHCID, iSCID, vName, iAge, cGender, vMobile, iVolunteerID, iDocID, cPositive, cAlertFlag, cStage, dtOnboarding, dSympt, dtLastMedLog from patient where cStatus="A" and cStage IN ("I", "H") ' . $cond . ' and DATE_FORMAT(dtOnboarding,"%Y-%m-%d")<="' . $LAST_TWO_DAYS . '" and (DATE_FORMAT(dtLastMedLog,"%Y-%m-%d")<="' . $LAST_TWO_DAYS . '" or dtLastMedLog IS NULL) order by dtLastMedLog limit 10'; //cStage NOT IN ("D", "C")
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($p_id, $phc_id, $sc_id, $name, $age, $gender, $mobile, $v_id, $doc_id, $positive, $alert, $stage, $dt_onboard, $dt_symptom, $dt) = sql_fetch_row($r)) {
            $arr[] = array('P_ID' => $p_id, 'PHC_ID' => $phc_id, 'SC_ID' => $sc_id, 'P_NAME' => $name, 'P_AGE' => $age, 'P_GENDER' => $gender, 'P_MOBILE' => $mobile, 'VOLUNTEER_ID' => $v_id, 'DOC_ID' => $doc_id, 'POSITIVE' => $positive, 'ALERT' => $alert, 'STAGE' => $stage, 'DATE_ON_BOARD' => $dt_onboard, 'DATE' => $dt, 'DATE_SYMPTOM' => $dt_symptom);
        }
    }

    return $arr;
}

function GetPatientCriticalList($reftype, $refid)
{
    $arr = array();

    $LAST_TWO_DAYS = DateTimeAdd(TODAY, '-2', 0, 0, 0, 0, 0, 'Y-m-d');

    $cond = '';
    if ($reftype == 'V') $cond .= ' and p.iVolunteerID=' . $refid;
    if ($reftype == 'D') $cond .= ' and p.iDocID=' . $refid;

    $q = 'select p2.iMedLogid, p2.iPatID, p2.dDate, p2.cType, p2.iOxy, p2.iTemp, p2.iPulse, p2.cCough, p2.cHeadAche, p2.cShortnessBreath, p2.cTiredness, p2.cChestPain, p2.cDrowsiness, p2.vNotes, p.iPHCID, p.iSCID, p.vName, p.iAge, p.cGender, p.vMobile, p.iVolunteerID, p.iDocID, p.cPositive, p.cAlertFlag, p.cStage, p.dtOnboarding, p.dSympt from log_trigger as p2 join patient as p on p2.iPatID=p.iPatID where p.cStatus="A" and p.cStage IN ("I", "H") ' . $cond . ' and p2.cDismiss="N" order by p2.dDate desc, FIELD(p2.cType,"N","A","M") limit 10'; //p.cStage NOT IN ("D", "C")
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($id, $p_id, $date, $type, $oxy, $temp, $pulse, $cough, $headache, $shortnessbreath, $tiredness, $chestpain, $drowsiness, $notes, $phc_id, $sc_id, $name, $age, $gender, $mobile, $v_id, $doc_id, $positive, $alert, $stage, $x_dtboard, $dt_symptom) = sql_fetch_row($r)) {
            $arr[] = array('ID' => $id, 'P_ID' => $p_id, 'DATE' => $date, 'TYPE' => $type, 'SpO2' => $oxy, 'TEMP' => $temp, 'PULSE' => $pulse, 'COUGH' => $cough, 'HEADACHE' => $headache, 'SHORTNESS_BREATH' => $shortnessbreath, 'TIREDNESS' => $tiredness, 'CHEST_PAIN' => $chestpain, 'DROWSINESS' => $drowsiness, 'NOTES' => $notes, 'PHC_ID' => $phc_id, 'SC_ID' => $sc_id, 'P_NAME' => $name, 'P_AGE' => $age, 'P_GENDER' => $gender, 'P_MOBILE' => $mobile, 'VOLUNTEER_ID' => $v_id, 'DOC_ID' => $doc_id, 'POSITIVE' => $positive, 'ALERT' => $alert, 'STAGE' => $stage, 'DATE_ON_BOARD' => $x_dtboard, 'DATE_SYMPTOM' => $dt_symptom);
        }
    }

    return $arr;
}

function GetPatientNotesList($reftype, $refid)
{
    $arr = array();

    $cond = '';
    if ($reftype == 'V') $cond .= ' and p.cRefType="V" and p.iRefID=' . $refid;
    if ($reftype == 'D') $cond .= ' and p.cRefType="D" and p.iRefID=' . $refid;

    $q = 'select p.iPatAdviceID, p.iPatID, p.dtCreation, p.cSenderRefType, p.iSenderRefID, p.vAdvice, p2.iPHCID, p2.iSCID, p2.vName, p2.iAge, p2.cGender, p2.vMobile, p2.iVolunteerID, p2.iDocID, p2.cPositive, p2.cAlertFlag, p2.cStage, p2.dtOnboarding, p2.dtLastMedLog from pat_advice as p join patient as p2 on p.iPatID=p2.iPatID where p.cStatus="N"' . $cond . ' order by p.dtCreation desc limit 10';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($id, $p_id, $dtCreated, $senderType, $senderID, $advice, $phc_id, $sc_id, $name, $age, $gender, $mobile, $v_id, $doc_id, $positive, $alert, $stage, $dt_onboard, $dt) = sql_fetch_row($r)) {
            $arr[] = array('ID' => $id, 'CREATED' => $dtCreated, 'SENDER_TYPE' => $senderType, 'SENDER_ID' => $senderID, 'ADVICE' => htmlspecialchars_decode($advice), 'P_ID' => $p_id, 'PHC_ID' => $phc_id, 'SC_ID' => $sc_id, 'P_NAME' => $name, 'P_AGE' => $age, 'P_GENDER' => $gender, 'P_MOBILE' => $mobile, 'VOLUNTEER_ID' => $v_id, 'DOC_ID' => $doc_id, 'POSITIVE' => $positive, 'ALERT' => $alert, 'STAGE' => $stage, 'DATE_ON_BOARD' => $dt_onboard, 'DATE' => $dt);
        }
    }

    return $arr;
}

function GetPatientVaccinationDetails($pat_id)
{
    $arr = array();
    global $VACCINE_TYPE;

    $q = 'select iPVID, iDose, iVaccine, dDate from pat_vaccine where iPatID=' . $pat_id . ' order by iDose';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($id, $dose, $vaccine, $date) = sql_fetch_row($r))
            $arr[] = array('ID' => $id, 'DOSE' => $dose, 'VACCINE' => (isset($VACCINE_TYPE[$vaccine])) ? $VACCINE_TYPE[$vaccine] : '-NA-', 'DATE' => FormatDate($date, 'B'));
    }

    return $arr;
}
function GetVolunteerListArr($limit = 1, $phcSTR = '', $scSTR = '')
{
    $arr = array();

    $cond2 = '';
    if (!empty($phcSTR))
        $cond2 .= ' and iPHCID IN (' . $phcSTR . ')';
    if (!empty($scSTR))
        $cond2 .= ' and iSCID IN (' . $scSTR . ')';

    $_vq = 'select iVolunteerID, iNumPat_allocated, iPHCID, iSCID from volunteer where cStatus="A"' . $cond2 . ' order by iNumPat_allocated limit ' . $limit;
    $_vr = sql_query($_vq, '');
    if (sql_num_rows($_vr)) {
        while (list($v_id, $v_patients, $v_phcid, $v_scid) = sql_fetch_row($_vr)) {
            if (!isset($arr[$v_phcid . '~' . $v_scid])) $arr[$v_phcid . '~' . $v_scid] = array();
            array_push($arr[$v_phcid . '~' . $v_scid], array('ID' => $v_id, 'PATIENTS' => $v_patients));
        }
    } else {
        if (!empty($scSTR)) $scSTR = '';
        else
			if (!empty($phcSTR)) $phcSTR = '';
        $arr = GetVolunteerListArr($limit, $phcSTR, $scSTR);
    }

    return $arr;
}

function AssignVolunteerToPatient($patient_id = 0, $limit = 1, $type = 'RR')
{
    $cond = '';
    if (!empty($patient_id))
        $cond .= ' and iPatInviteID=' . $patient_id;

    $PATIENT_ARR = $PATIENT_CENTER_ARR = $PHC_ARR = $SC_ARR = $VOLUNTEER_CENTER_ARR = $VOLUNTEER_CENTER_ARR2 = array();
    $q = 'select iPatInviteID, iPHCID, iSCID, iPatID from patinvite where iVolunteerID=0' . $cond . ' limit ' . $limit;
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($p_id, $p_phcid, $p_scid, $p_id2) = sql_fetch_row($r)) {
            if (!isset($PATIENT_CENTER_ARR[$p_phcid . '~' . $p_scid])) $PATIENT_CENTER_ARR[$p_phcid . '~' . $p_scid] = array();
            array_push($PATIENT_CENTER_ARR[$p_phcid . '~' . $p_scid], $p_id);

            if (!in_array($p_phcid, $PHC_ARR)) array_push($PHC_ARR, $p_phcid);
            if (!in_array($p_scid, $SC_ARR)) array_push($SC_ARR, $p_scid);
            if (!empty($p_id2)) $PATIENT_ARR[$p_id] = $p_id2;
        }

        $phcSTR = $scSTR = '';
        if ($type != 'RR') {
            if (!empty($PHC_ARR) && count($PHC_ARR)) $phcSTR = implode(',', $PHC_ARR);
            if (!empty($SC_ARR) && count($SC_ARR)) $scSTR = implode(',', $SC_ARR);
        }

        $VOLUNTEER_CENTER_ARR = GetVolunteerListArr($limit, $phcSTR, $scSTR);
    }

    if (!empty($VOLUNTEER_CENTER_ARR) && count($VOLUNTEER_CENTER_ARR)) {
        foreach ($VOLUNTEER_CENTER_ARR as $vKEY => $vVALUE) {
            list($v_phcid, $v_scid) = explode('~', $vKEY);
            foreach ($vVALUE as $vKEY2 => $vVALUE2) {
                $v_id = $vVALUE2['ID'];
                $v_patients = $vVALUE2['PATIENTS'];

                if (!isset($VOLUNTEER_CENTER_ARR2[$v_phcid][$v_id])) $VOLUNTEER_CENTER_ARR2[$v_phcid][$v_id] = array();
                $VOLUNTEER_CENTER_ARR2[$v_phcid][$v_id] = $v_patients;
            }
        }
    }

    $TEMP_ARR = array();
    if ($type != 'RR') {
        if (!empty($PATIENT_CENTER_ARR) && count($PATIENT_CENTER_ARR)) {
            foreach ($PATIENT_CENTER_ARR as $pKEY => $pVALUE) {
                list($phc_id, $sc_id) = explode('~', $pKEY);

                if (isset($VOLUNTEER_CENTER_ARR[$pKEY])) {
                    foreach ($pVALUE as $pKEY2 => $pID) {
                        foreach ($VOLUNTEER_CENTER_ARR[$pKEY] as $vKEY => $vVALUE) {
                            $vID = $vVALUE['ID'];
                            if (!isset($TEMP_ARR[$vID])) $TEMP_ARR[$vID] = array();
                            array_push($TEMP_ARR[$vID], $pID);

                            $VOLUNTEER_CENTER_ARR2[$phc_id][$vID] = $VOLUNTEER_CENTER_ARR2[$phc_id][$vID] + 1;
                            $VOLUNTEER_CENTER_ARR[$pKEY][$vKEY] = array('ID' => $vID, 'PATIENTS' => $vVALUE['PATIENTS'] + 1);
                            $VOLUNTEER_CENTER_ARR[$pKEY] =  MultiDimensionalArraySort($VOLUNTEER_CENTER_ARR[$pKEY], 'PATIENTS', SORT_ASC);
                            unset($PATIENT_CENTER_ARR[$pKEY][$pKEY2]);
                            break;
                        }
                    }
                }
            }
        }

        if (!empty($PATIENT_CENTER_ARR) && count($PATIENT_CENTER_ARR)) {
            foreach ($PATIENT_CENTER_ARR as $pKEY4 => $pVALUE4) {
                list($phc_id4, $sc_id4) = explode('~', $pKEY4);

                if (empty($pVALUE4)) {
                    unset($PATIENT_CENTER_ARR[$pKEY4]);
                    continue;
                }

                if (isset($VOLUNTEER_CENTER_ARR2[$phc_id4])) {
                    foreach ($pVALUE4 as $pKEY5 => $pID5) {
                        asort($VOLUNTEER_CENTER_ARR2[$phc_id4]);
                        foreach ($VOLUNTEER_CENTER_ARR2[$phc_id4] as $vKEY4 => $vVALUE4) {
                            if (!isset($TEMP_ARR[$vKEY4])) $TEMP_ARR[$vKEY4] = array();
                            array_push($TEMP_ARR[$vKEY4], $pID5);

                            $VOLUNTEER_CENTER_ARR2[$phc_id4][$vKEY4] = $VOLUNTEER_CENTER_ARR2[$phc_id][$vKEY4] + 1;
                            asort($VOLUNTEER_CENTER_ARR2[$phc_id4]);
                            unset($PATIENT_CENTER_ARR[$pKEY4][$pKEY5]);
                            break;
                        }
                    }
                }
            }
        }
    }

    if (!empty($PATIENT_CENTER_ARR) && count($PATIENT_CENTER_ARR)) {
        foreach ($PATIENT_CENTER_ARR as $pKEY4 => $pVALUE4) {
            list($phc_id5, $sc_id5) = explode('~', $pKEY4);

            if (empty($pVALUE4)) {
                unset($PATIENT_CENTER_ARR[$pKEY4]);
                continue;
            }

            if (!empty($VOLUNTEER_CENTER_ARR2) && count($VOLUNTEER_CENTER_ARR2)) {
                foreach ($pVALUE4 as $pKEY5 => $pID5) {
                    asort($VOLUNTEER_CENTER_ARR2);
                    foreach ($VOLUNTEER_CENTER_ARR2 as $vKEY4 => $vVALUE4) {
                        foreach ($vVALUE4 as $vKEY5 => $vVALUE5) {
                            if (!isset($TEMP_ARR[$vKEY5])) $TEMP_ARR[$vKEY5] = array();
                            array_push($TEMP_ARR[$vKEY5], $pID5);

                            $VOLUNTEER_CENTER_ARR2[$phc_id5][$vKEY5] = $VOLUNTEER_CENTER_ARR2[$phc_id5][$vKEY5] + 1;
                            asort($VOLUNTEER_CENTER_ARR2);
                            unset($PATIENT_CENTER_ARR[$pKEY4][$pKEY5]);
                            break;
                        }
                    }
                }
            }
        }
    }

    if (!empty($TEMP_ARR) && count($TEMP_ARR)) {
        foreach ($TEMP_ARR as $tKEY => $tVALUE) {
            $volunteer_id = $tKEY;

            $allocation_count = 0;
            $invite_id_str = $patient_id_str = '';
            foreach ($tVALUE as $pI_ID) {
                $invite_id_str .= $pI_ID . ',';
                if (isset($PATIENT_ARR[$pI_ID]))
                    $patient_id_str .= $PATIENT_ARR[$pI_ID] . ',';

                $allocation_count++;
            }

            if (!empty($invite_id_str)) {
                $invite_id_str = substr($invite_id_str, 0, '-1');
                $_uiq = "update patinvite set iVolunteerID='$volunteer_id' where iPatInviteID IN (" . $invite_id_str . ")";
                //echo $_uiq.'<br />';
                $_uir = sql_query($_uiq, '');
            }

            if (!empty($patient_id_str)) {
                $patient_id_str = substr($patient_id_str, 0, '-1');
                $_upq = "update patient set iVolunteerID='$volunteer_id' where iPatID IN (" . $patient_id_str . ")";
                //echo $_upq.'<br />';
                $_upr = sql_query($_upq, '');
            }

            if (!empty($allocation_count)) {
                $newCount = GetXFromYID('select count(*) from patinvite where iVolunteerID=' . $volunteer_id);
                $activeCount = GetXFromYID('select count(*) from patient where iVolunteerID=' . $volunteer_id . ' and cStage IN ("I","H")'); //cStage NOT IN ("C","D")
                $_uvq = "update volunteer set iNumPat_allocated='$newCount', iNumActivePatients='$activeCount' where iVolunteerID='$volunteer_id'";
                //echo $_uvq.'<br />';
                $_uvr = sql_query($_uvq, '');
            }
        }
    }
}

function GetPulseTrigger($age, $value)
{
    $str = 0;
    global $PULSE_ARR;

    if (is_numeric($value) && !empty($value)) {
        foreach ($PULSE_ARR as $KEY => $VALUE) {
            list($min_age, $max_age) = explode('-', $KEY);
            if ($age > $min_age && $age <= $max_age) {
                list($min_value, $max_value) = explode('-', $VALUE);
                if ($value < $min_value || $value > $max_value)
                    $str = 1;
            }
        }
    }

    return $str;
}

function Generate4DigitRandomCode()
{
    $arr = array();

    $len = 5;

    // atleast 1 uppercase char
    $a_len = rand(1, 1);
    // echo 'a_len: '.$a_len.'<br>';

    for ($i = 0; $i < $a_len; $i++)
        $arr[$i] = chr(rand(65, 90));

    $ctr = $i;

    // atleast 1 lowercase char
    $b_len = rand(2, 2);
    // echo 'b_len: '.$b_len.'<br>';

    for (; $i < ($ctr + $b_len); $i++)
        $arr[$i] = chr(rand(97, 122));

    $ctr = $i;

    // atleast 1 number
    $c_len = rand(1, 1);
    // echo 'c_len: '.$c_len.'<br>';

    for (; $i < ($ctr + $c_len); $i++)
        $arr[$i] = rand(0, 9);

    // DFA($arr);
    shuffle($arr);
    // DFA($arr);

    $str = '';
    foreach ($arr as $a)
        $str .= $a;

    return $str;
}

function enCodeParamSMS($param)
{
    global $ENC_CHARARR;
    $randomCode = Generate4DigitRandomCode();
    $code = '';

    $param = strval($param);
    for ($i = 0; $i < strlen($param); $i++)
        $code .= $ENC_CHARARR[$param[$i]];

    $str = $randomCode . $code;

    return $str;
}

function deCodeParamSMS($param)
{
    global $ENC_CHARARR;
    $p2 = substr($param, 4);

    $code = '';
    $p2 = strval($p2);
    for ($i = 0; $i < strlen($p2); $i++)
        $code .= array_search($p2[$i], $ENC_CHARARR);

    $str = $code;

    return $str;
}

function GetDailyMISReportDetails($DAILY_MIS = array(), $STATUS = "")
{
    $arr = array();

    $arr['TOTAL'] = $arr['MALE'] = $arr['FEMALE'] = 0;
    if (!empty($DAILY_MIS) && count($DAILY_MIS)) {
        foreach ($DAILY_MIS as $KEY => $VALUE) {
            if ($KEY == $STATUS) {
                foreach ($VALUE as $KEY2 => $VALUE2) {
                    $GENDER = $VALUE2['GENDER'];
                    $COUNT = $VALUE2['COUNT'];

                    $arr['TOTAL'] = $arr['TOTAL'] + $COUNT;
                    if ($GENDER == 'M') $arr['MALE'] = $arr['MALE'] + $COUNT;
                    if ($GENDER == 'F') $arr['FEMALE'] = $arr['FEMALE'] + $COUNT;
                }
            }
        }
    }

    return $arr;
}

function Get30DayOnboardingActivityDetails($dFrom, $dTo)
{
    $arr = array();

    $q = 'select dStat, cFlag, cGender, iCount from mis_dailystat where dStat>="' . $dFrom . '" and dStat<="' . $dTo . '" and cFlag IN ("PN", "AC", "PL", "CT")';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($date, $flag, $gender, $count) = sql_fetch_row($r)) {
            $date = ConvertFromYMDtoDMY($date);
            if (!isset($arr[$date][$flag])) $arr[$date][$flag] = 0;
            $arr[$date][$flag] = $arr[$date][$flag] + $count;
        }
    }

    return $arr;
}

function GetAgeGenderDetails()
{
    $arr = array();

    $q = 'select cRefType, iRefID, iAgeGroup, cGender, iCount from mis_agegenderstat';
    $r = sql_query($q, '');
    if (sql_num_rows($r)) {
        while (list($reftype, $refid, $agegroup, $gender, $count) = sql_fetch_row($r)) {
            if (!isset($arr[$agegroup][$gender])) $arr[$agegroup][$gender] = 0;
            $arr[$agegroup][$gender] = $arr[$agegroup][$gender] + $count;
        }
    }

    return $arr;
}

function GetDayOfEntry($dt_symptom, $dt_onboard, $date)
{
    $str = '';

    $start = $dt_onboard;
    if (IsDate($dt_symptom)) $start = $dt_symptom;

    $DAY = 0;
    do {
        $DATE2 = DateTimeAdd($start, $DAY, 0, 0, 0, 0, 0, 'Y-m-d');
        $DAY++;
    } while ($DATE2 < $date);

    $str = $DAY;

    return $str;
}

function GetPatientAccess($id, $refType, $refId)
{
    $str = false;

    if ($refType == 'D') {
        $exist = GetXfromYID('select count(*) from patient where iDocID=' . $refId . ' and iPatID=' . $id);
        if (!empty($exist) && $exist != '-1')
            $str = true;
    }

    if ($refType == 'V') {
        $exist = GetXfromYID('select count(*) from patient where iVolunteerID=' . $refId . ' and iPatID=' . $id);
        if (!empty($exist) && $exist != '-1')
            $str = true;
    }

    return $str;
}

function GetActiveVolunteer_LASTLOGIN($limit = 10)
{
    $str = '';

    $date48hours = DateTimeAdd(NOW, 0, 0, 0, '-48', 0, 0, 'Y-m-d H:i:s');
    $v_id = GetXFromYID('select v.iVolunteerID from volunteer as v join users as u on v.iVolunteerID=u.iRefID where v.cStatus="A" and v.iVolunteerID!=1 and u.cRefType="V" and u.dtLastLogin>="' . $date48hours . '" and v.iNumPat_allocated<' . $limit . ' order by v.iNumPat_allocated limit 1');
    if (empty($v_id) || $v_id == '-1')
        $v_id = GetXFromYID('select iVolunteerID from volunteer where cStatus="A" and iVolunteerID!=1 and iNumPat_allocated<' . $limit . ' order by iNumPat_allocated limit 1');

    if (empty($v_id) || $v_id == '-1') {
        $limit = $limit + 10;
        $v_id = GetActiveVolunteer_LASTLOGIN($limit);
    }

    $str = $v_id;

    return $str;
}

function GetActiveVolunteer($limit = 10)
{
    $str = '';

    $v_id = GetXFromYID('select iVolunteerID from volunteer where cStatus="A" and iVolunteerID!=1 and iNumPat_allocated<' . $limit . ' order by iNumPat_allocated limit 1');
    if (empty($v_id) || $v_id == '-1') {
        $limit = $limit + 10;
        $v_id = GetActiveVolunteer($limit);
    }

    $str = $v_id;

    return $str;
}

function UpdateVolunteerList20210630($idSTR = '')
{
    $cond = '';
    if (!empty($idSTR)) $cond .= ' and iVolunteerID IN (' . $idSTR . ')';

    $ALLOCATED = GetXArrFromYID('select iVolunteerID, count(*) from patinvite where cStatus!="P"' . $cond . ' group by iVolunteerID', '3');
    $ACTIVE = GetXArrFromYID('select iVolunteerID, count(*) from patient where cStage IN ("I","H")' . $cond . ' group by iVolunteerID', '3');

    $q_str = '';
    $VOLUNTEER_ARR = GetXArrFromYID('select iVolunteerID from volunteer where cStatus="A"' . $cond, '1');
    if (!empty($VOLUNTEER_ARR) && count($VOLUNTEER_ARR)) {
        foreach ($VOLUNTEER_ARR as $vKEY) {
            $allocated = (isset($ALLOCATED[$vKEY])) ? $ALLOCATED[$vKEY] : '0';
            $active = (isset($ACTIVE[$vKEY])) ? $ACTIVE[$vKEY] : '0';

            $q_str .= "update volunteer set iNumPat_allocated='$allocated', iNumActivePatients='$active' where iVolunteerID='$vKEY'" . '~*~';
        }
    }

    if (!empty($q_str)) {
        $q = explode('~*~', $q_str);
        foreach ($q as $query) {
            if (!empty($query))
                sql_query($query, '');
        }
    }
}

function UpdateDoctorList20210630($idSTR = '')
{
    $cond = $cond2 = '';
    if (!empty($idSTR)) {
        $cond .= ' and iDocID IN (' . $idSTR . ')';
        $cond2 .= ' and iDoctorID IN (' . $idSTR . ')';
    }

    $ALLOCATED = GetXArrFromYID('select iDocID, count(*) from patient where 1' . $cond . ' group by iDocID', '3');
    $ACTIVE = GetXArrFromYID('select iDocID, count(*) from patient where cStage IN ("I","H")' . $cond . ' group by iDocID', '3');

    $q_str = '';
    $DOCTOR_ARR = GetXArrFromYID('select iDoctorID from doctors where cStatus="A"' . $cond2, '1');
    if (!empty($DOCTOR_ARR) && count($DOCTOR_ARR)) {
        foreach ($DOCTOR_ARR as $dKEY) {
            $allocated = (isset($ALLOCATED[$dKEY])) ? $ALLOCATED[$dKEY] : '0';
            $active = (isset($ACTIVE[$dKEY])) ? $ACTIVE[$dKEY] : '0';

            $q_str .= "update doctors set iNumPat_allocated='$allocated', iNumActivePatients='$active' where iDoctorID='$dKEY'" . '~*~';
        }
    }

    if (!empty($q_str)) {
        $q = explode('~*~', $q_str);
        foreach ($q as $query) {
            if (!empty($query))
                sql_query($query, '');
        }
    }
}

function UpdateVolunteerList($idSTR = '')
{
    $cond = $cond2 = '';
    if (!empty($idSTR)) $cond .= ' and iVolunteerID IN (' . $idSTR . ')';
    if (!empty($idSTR)) $cond2 .= ' and p.iVolunteerID IN (' . $idSTR . ')';

    $ALLOCATED = GetXArrFromYID('select iVolunteerID, count(*) from patinvite where cStatus NOT IN ("P","X")' . $cond . ' group by iVolunteerID', '3');
    $ALLOCATED2 = GetXArrFromYID('select p.iVolunteerID, count(*) from patinvite as p left outer join patient as p2 on p.iPatID=p2.iPatID where p.cStatus NOT IN ("P","X") and p2.cStage NOT IN ("I","H") and p.iPatID!=0' . $cond2 . ' group by p.iVolunteerID', '3');
    $ACTIVE = GetXArrFromYID('select iVolunteerID, count(*) from patient where cStage IN ("I","H")' . $cond . ' group by iVolunteerID', '3');

    $q_str = '';
    $VOLUNTEER_ARR = GetXArrFromYID('select iVolunteerID from volunteer where cStatus="A"' . $cond, '1');
    if (!empty($VOLUNTEER_ARR) && count($VOLUNTEER_ARR)) {
        foreach ($VOLUNTEER_ARR as $vKEY) {
            $allocated = 0;
            if (isset($ALLOCATED[$vKEY]))
                $allocated = $allocated + $ALLOCATED[$vKEY];
            if (isset($ALLOCATED2[$vKEY]))
                $allocated = $allocated - $ALLOCATED2[$vKEY];
            $active = (isset($ACTIVE[$vKEY])) ? $ACTIVE[$vKEY] : '0';

            $q_str .= "update volunteer set iNumPat_allocated='$allocated', iNumActivePatients='$active' where iVolunteerID='$vKEY'" . '~*~';
        }
    }

    if (!empty($q_str)) {
        $q = explode('~*~', $q_str);
        foreach ($q as $query) {
            if (!empty($query))
                sql_query($query, '');
        }
    }
}

function UpdateDoctorList($idSTR = '')
{
    $cond = $cond2 = '';
    if (!empty($idSTR)) {
        $cond .= ' and iDocID IN (' . $idSTR . ')';
        $cond2 .= ' and iDoctorID IN (' . $idSTR . ')';
    }

    $DOC_VOLUNTEER_ARR = array();
    $VOLUNTEER_DOC_ASSOC = GetXArrFromYID('select iVolunteerID, iDoctorID from volunteer where cStatus="A" and iDoctorID!=0' . $cond2, '3');
    if (!empty($VOLUNTEER_DOC_ASSOC) && count($VOLUNTEER_DOC_ASSOC)) {
        foreach ($VOLUNTEER_DOC_ASSOC as $vKEY => $dKEY) {
            if (!isset($DOC_VOLUNTEER_ARR[$dKEY])) $DOC_VOLUNTEER_ARR[$dKEY] = array();
            array_push($DOC_VOLUNTEER_ARR[$dKEY], $vKEY);
        }
    }

    $DOCTOR_PATIENT_ALLOCATED = array();
    if (!empty($DOC_VOLUNTEER_ARR) && count($DOC_VOLUNTEER_ARR)) {
        foreach ($DOC_VOLUNTEER_ARR as $dKEY => $dVALUE) {
            $allocated = '';

            $ALLOCATED = GetXFromYID('select count(*) from patinvite where cStatus NOT IN ("P","X") and iVolunteerID IN (' . implode(',', $dVALUE) . ')');
            $ALLOCATED2 = GetXFromYID('select count(*) from patient where cStage NOT IN ("I","H") and iDocID=' . $dKEY);
            if (!empty($ALLOCATED) && $ALLOCATED != '-1')
                $allocated = $allocated + $ALLOCATED;
            if (!empty($ALLOCATED2) && $ALLOCATED2 != '-1')
                $allocated = $allocated - $ALLOCATED2;

            $DOCTOR_PATIENT_ALLOCATED[$dKEY] = $allocated;
        }
    }

    $ACTIVE = GetXArrFromYID('select iDocID, count(*) from patient where cStage IN ("I","H")' . $cond . ' group by iDocID', '3');

    $q_str = '';
    $DOCTOR_ARR = GetXArrFromYID('select iDoctorID from doctors where cStatus="A"' . $cond2, '1');
    if (!empty($DOCTOR_ARR) && count($DOCTOR_ARR)) {
        foreach ($DOCTOR_ARR as $dKEY) {
            $allocated = (isset($DOCTOR_PATIENT_ALLOCATED[$dKEY])) ? $DOCTOR_PATIENT_ALLOCATED[$dKEY] : '0';
            $active = (isset($ACTIVE[$dKEY])) ? $ACTIVE[$dKEY] : '0';

            $q_str .= "update doctors set iNumPat_allocated='$allocated', iNumActivePatients='$active' where iDoctorID='$dKEY'" . '~*~';
        }
    }

    if (!empty($q_str)) {
        $q = explode('~*~', $q_str);
        foreach ($q as $query) {
            if (!empty($query))
                sql_query($query, '');
        }
    }
}

function UpdateVolunteerDoctorPatients($v_id, $doc_id)
{
    sql_query("update patient set iDocID='$doc_id' where iVolunteerID='$v_id' and cStage IN ('I','H')", '');
    UpdateDoctorList($doc_id);
}

function getDatesRange($first, $last, $step = '+1 day', $output_format = 'd/m/Y')
{

    $dates = array();
    $current = strtotime($first);
    $last = strtotime($last);

    while ($current <= $last) {

        $dates[] = date($output_format, $current);
        $current = strtotime($step, $current);
    }

    return $dates;
}

function getCustomerDetails($custid)
{
    $cust_det = array();
    $cust_arr = GetDataFromID("customer", "iCustID", $custid);

    if (!empty($cust_arr) && count($cust_arr)) {
        $txtname = db_output($cust_arr[0]->vName);
        $txtmobile = db_output($cust_arr[0]->vMobileNum);
        $txtemail = db_output($cust_arr[0]->vEmailID);
        $password = db_output($cust_arr[0]->vPassword);
        $txtlastlogin = $cust_arr[0]->dtLastLogin;
        $txtlastbooking = $cust_arr[0]->dtLastBooking;
        $txttotalvalue = db_output($cust_arr[0]->fTotValue);
        $txtpoints = db_output($cust_arr[0]->fPoints);
        $cmbtype = db_output($cust_arr[0]->cType);
        $rdstatus = db_output2($cust_arr[0]->cStatus);

        $cust_det = array(
            'NAME' => $txtname,
            'MOBILE' => $txtmobile,
            'EMAIL' => $txtemail,
            'LASTLOGIN' => $txtlastlogin,
            'LASTBOOKING' => $txtlastbooking,
            'TOTALVALUE' => $txttotalvalue,
            'POINTS' => $txtpoints,
            'TYPE' => $cmbtype,
            'STATUS' => $rdstatus
        );
    }

    return $cust_det;
}

function checkEmail($email)
{
    $find1 = strpos($email, '@');
    $find2 = strpos($email, '.');
    return ($find1 !== false && $find2 !== false && $find2 > $find1);
}
