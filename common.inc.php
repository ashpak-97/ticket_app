<?php
ini_set('session.gc_maxlifetime', 86400);
function IsUniqueEntry($id_fld, $id_val, $txt_fld, $txt_val, $tbl)
{
    $ret_val = '2';
    $curr_txt = (isset($id_val)) ? GetXFromYID("select $txt_fld from $tbl where $id_fld=$id_val") : "";

    if ($txt_val != '' && $txt_val != $curr_txt) // no change in value, ignore
    {
        $q_str = (isset($id_val)) ? " and $id_fld!=$id_val" : "";
        $chk = GetXFromYID("select count(*) from $tbl where $txt_fld='$txt_val' " . $q_str);

        $ret_val = ($chk) ? '0' : '1';
    }

    return $ret_val;
}

function SetMinRank($tb1, $cond = "")
{
    $cond = (strtoupper(trim($cond)) != "") ? " where " . $cond : "";
    $min = GetXFromYID("select min(iRank) from $tb1 $cond");

    if ($min == 0) {
        mysql_query("update $tb1 set iRank=iRank+2 $cond") or die("<strong>ERROR CODE :</strong> COM-68");
    } else {
        mysql_query("update $tb1 set iRank=iRank+1 $cond") or die("<strong>ERROR CODE :</strong> COM-72");
    }

    return 1;
}

function ContactDetails($phone, $mobile, $email)
{
    $arr = array();

    if ($phone != '' || $mobile != '' || $email != '') {
        if (!empty($email))
            $arr['email'] = $email;

        if (!empty($mobile))
            $arr['mobile'] = $mobile;

        if (!empty($phone))
            $arr['phone'] = $phone;
    }

    return '&nbsp;' . implode(', ', $arr);
}

function FillYearArr()
{
    $arr = array();
    for ($i = START_YEAR; $i <= THIS_YEAR; $i++)
        $arr[$i] = $i . '-' . ($i + 1);
    return $arr;
}

/* 
function FillLocations($selected, $ctr, $tp, $comp, $cond, $fn="", $class="form-control")
{	
	$display = ($tp=="COMBO" || $tp=="COMBO2")? "": "size=10";
	$cond = (strtoupper(trim($cond)) != 'N' && trim($cond)!='')? " where " . $cond: "";	
	$class_str = (trim($class)=="")? "": $class;

	$stat_fld = ($tp == "COMBO")? ", 'A' ": ", cStatus";
	
	$q = "select iLocID, vName, iLevel " . $stat_fld . " from gen_location " . $cond . " order by iRank";
	$result = sql_query($q, 'COM.130');
	$str = '<select name="'.$ctr.'" id="'.$ctr.'" class="'.$class_str.'" '.$display.' '.$fn.'>'."\n"; //

	if($comp<>'y'&&$comp<>'Y') 
	{
		$str .= '<option value="0" selected> - select - </option>'."\n";
	}

	while(list($id,$nm,$level,$stat)=sql_fetch_row($result))
	{
		$stat_style = ($stat=="A" && $tp=="COMBO2")? "": ' style="background-color: #FFC5C5;"';
		$selected_str = (trim($selected) == trim($id))? "selected": "";
		$space = GenerateSpace($level);
		$str .=  '<option value="'.$id.'" '.$selected_str.'>'.$space.$nm.'</option>'."\n";
	}

	$str .= '</select>'."\n";
	return $str;
}

function FillPropclass($selected, $ctr, $tp, $comp, $cond, $fn="", $class="form-control")
{
	$display = ($tp=="COMBO" || $tp=="COMBO2")? "": "size=10";
	$cond = (strtoupper(trim($cond)) != 'N' && trim($cond)!='')? " where " . $cond: "";	
	$class_str = (trim($class)=="")? "": $class;

	$stat_fld = ($tp == "COMBO")? ", 'A' ": ", cStatus";
	
	$q = "select iPropClassID, vName, iLevel " . $stat_fld . " from gen_propclass " . $cond . " order by iRank";
	$result = sql_query($q, 'COM.130');
	$str = '<select name="'.$ctr.'" id="'.$ctr.'" class="'.$class_str.'" '.$display.' '.$fn.'>'."\n"; //

	if($comp<>'y'&&$comp<>'Y') 
	{
		$str .= '<option value="0" selected> - select - </option>'."\n";
	}

	while(list($id,$nm,$level,$stat)=sql_fetch_row($result))
	{
		$stat_style = ($stat=="A" && $tp=="COMBO2")? "": ' style="background-color: #FFC5C5;"';
		$selected_str = (trim($selected) == trim($id))? "selected": "";
		$space = GenerateSpace($level);
		$str .=  '<option value="'.$id.'" '.$selected_str.'>'.$space.$nm.'</option>'."\n";
	}

	$str .= '</select>'."\n";
	return $str;
}	// */

function GetDuration($dfrom, $dto)
{
    $str = '';

    if ($dfrom != '')    $str .= $dfrom;
    if ($dfrom != '' && $dto != '')    $str .= ' to: ';
    if ($dto != '')    $str .= $dto;

    if ($str == '')    $str = '&nbsp;';
    return $str;
}
function GetUrlName($title)
{
    $title = htmlspecialchars_decode($title);
    $URL_CHAR_ARR = array("%", "/", ".", "#", "?", "*", "!", "@", "&", ":", "|", ";", "=", "<", ">", "^", "~", "'", "\"", ",", "-", "(", ")", "'", '"', '\\');
    $rurl = trim($title);
    $rurl = str_replace($URL_CHAR_ARR, '', $title);
    $rurl = str_replace('   ', ' ', $rurl);
    $rurl = str_replace('  ', ' ', $rurl);
    $rurl = str_replace(' ', '-', $rurl);
    $rurl = trim(strtolower($rurl));

    return $rurl;
}

function GetLocationArr($level, $parentid, $arr, $mode = "1")
{
    return GetTreeArr('iLocID', 'gen_location', $level, $parentid, $arr, $mode);
}

function GenerateSpace($level, $symbol = '&nbsp;&nbsp;')
{
    $space = '';

    for ($i = 0; $i < $level; $i++)
        $space .= $symbol;

    return $space;
}

function GetBookPrimaryCategory()
{
    $arr = array();
    $q = "select iBookID, iCategoryID from book_category where cFeatured='Y'";
    $r = sql_query($q) or die("<strong>Error Code: COM1802</strong>");
    while (list($b_id, $c_id) = sql_fetch_row($r))
        $arr[$b_id] = $c_id;
    return $arr;
}

function GetCatUrlName($sess_lan)
{
    $q_str = ($sess_lan == 'G') ? 'vG_UrlName' : 'vE_UrlName';

    $arr = array();
    $q = "select iCatID, $q_str from category";
    $r = sql_query($q) or die("<strong>Error Code: COM1802</strong>");
    while (list($c_id, $c_name) = sql_fetch_row($r))
        $arr[$c_id] = $c_name;
    return $arr;
}

function GetFirstOfMonth($date_ymd)
{
    list($y, $m, $d) = explode('-', $date_ymd);
    return $y . '-' . $m . '-01';
}

function GetLastOfMonth($date_ymd, $month_offset = 0)
{
    $d = GetFirstOfMonth($date_ymd);
    return DateTimeAdd($d, -1, ($month_offset + 1), 0, 0, 0, 0, $format = "Y-m-d");
}

function SetStatusFlags($status)
{
    global $is_inactive, $is_active, $is_complete;

    if ($status == 'C')        $is_complete = true;
    else if ($status == 'A')    $is_active = true;
    else                    $is_inactive = true;
}

function GetWeekStart($dt)
{
    $curr_day = date('w', strtotime($dt));

    $curr_week_start_offset = ($curr_day != WEEK_START_DAY) ? ($curr_day - WEEK_START_DAY) : 0;
    return DateTimeAdd($dt, -$curr_week_start_offset, 0, 0, 0, 0, 0, 'Y-m-d');
}

function GetRelevantItemCatIDArr($icat_id)
{
    $arr = array();
    $arr[$icat_id] = $icat_id;
    $arr = GetSubItemCat(1, $icat_id, $arr, '4');
    return $arr;
}

function GetGenLocationDat($cond = '')
{
    $arr = array();

    $q = "select iLocID, vName from gen_location where 1 $cond"; // iLocID is not NULL
    $r = sql_query($q, 'COM.16');
    while (list($id, $name) = sql_fetch_row($r))
        $arr[$id] = $name;

    return $arr;
}

function GetLevelStr($level, $char = '&nbsp;')
{
    $str = '';

    for ($i = 1; $i < $level; $i++)
        $str .= $char;

    return $str;
}

function GetUniqueCode($id, $val, $pk_fld, $code_fld, $tbl, $char_len = 1, $num_len = 2, $min_num = 0) // GetItemUniqueCode
{
    $ret_val = '';

    $val = strtoupper(trim($val));
    $prefix = substr($val, 0, $char_len);

    $ret_val = $prefix . str_pad('1', 2, '0', STR_PAD_LEFT);
    $code_arr = array();

    $q_str = ($id) ? ' and ' . $pk_fld . '!=' . $id : '';

    $q = "select upper($code_fld) from $tbl where $code_fld like '$prefix%' " . $q_str;
    $r = sql_query($q, 'COM.160');
    while (list($code) = sql_fetch_row($r)) {
        $code_no = str_replace($prefix, '', $code);

        if (!is_numeric($code_no))
            $code_no = '0';

        $code_arr[$code] = $code_no;
    }

    if (count($code_arr)) {
        rsort($code_arr, SORT_NUMERIC);
        reset($code_arr);

        $code_no = max($code_arr[0], $min_num) + 1;
        $ret_val = $prefix . str_pad($code_no, 2, '0', STR_PAD_LEFT);
    }

    return $ret_val;
}

function GetGenTaxDat()
{
    $arr = array();
    $q = "select iTaxID, vName from gen_tax where 1 order by vName";
    $r = sql_query($q, 'COM.742');
    while (list($id, $name) = sql_fetch_row($r))
        $arr[$id] = $name;

    return $arr;
}

function GetGenTaxValueArr($cond = 'N', $ord = 'a.iRank, b.fPerc')
{
    $arr = array('0.0' => 'NA: 0%');
    $q = "select CONCAT(a.iTaxID,'.', fPerc), CONCAT(a.vName,': ',fPerc,'%') from gen_tax as a, gen_tax_values as b 
			where a.iTaxID=b.iTaxID " . $cond . " order by " . $ord;
    $r = sql_query($q, 'COM.921');
    while (list($id, $nm) = sql_fetch_row($r))
        $arr[$id] = $nm;

    return $arr;
}

function FillGenTaxValues($selected, $ctr, $tp, $comp, $cond = 'N', $ord = 'a.iRank, b.fPerc', $fn = '', $class = '')
{
    $display = ($tp == "COMBO" || $tp == "COMBO2") ? "" : "size=10";
    $cond = (strtoupper(trim($cond)) != 'N' && trim($cond) != '') ? " and " . $cond : "";
    $class_str = (trim($class) == "") ? "" : $class;

    $stat_fld = ($tp == "COMBO") ? ", 'A' " : ", a.cStatus";

    $q = "select CONCAT(a.iTaxID,'|',fPerc), CONCAT(fPerc,'% (',a.vName,')') " . $stat_fld . " from gen_tax as a, gen_tax_values as b where a.iTaxID=b.iTaxID " . $cond . " order by " . $ord;
    $r = sql_query($q, 'COM.939');
    $str = "<select name='$ctr' id='$ctr' class='$class_str' $display $fn>\n"; //

    if ($comp <> 'y' && $comp <> 'Y') {
        if ($comp == '0')
            $str .= "<option value='0|0' selected>NA: 0%</option>\n";
    }

    $level_arr = array();

    while (list($id, $nm, $stat) = sql_fetch_row($r)) {
        $stat_style = ($stat == "A" && $tp == "COMBO2") ? "" : " style='background-color: #FFC5C5;'";
        $selected_str = (trim($selected) == trim($id)) ? "selected" : "";

        $str .= "<option value='$id' $selected_str>$nm</option>\n";
    }

    $str .= "</select>\n";
    return $str;
}

function GetGenUserDat($cond = '')
{
    $arr = array();

    $q = "select iUID, vName from gen_user where 1 $cond"; // iLocID is not NULL
    $r = sql_query($q, 'COM.1549');
    while (list($id, $name) = sql_fetch_row($r))
        $arr[$id] = $name;

    return $arr;
}

function GetParentItemCategoryArr(&$parent_item_cat_arr, &$item_cat_arr)
{
    $q = "select iItemCatID, vName, iParentID, iAncestorID from gen_cat order by vName";
    $r = sql_query($q, 'COM.1685');
    while (list($itemcat_id, $itemcat_name, $parent_id, $ancestor_id) = sql_fetch_row($r)) {
        $item_cat_arr[$itemcat_id] = array('NAME' => $itemcat_name, 'ANCESTOR' => $ancestor_id);

        if (empty($parent_id))
            $parent_item_cat_arr[$itemcat_id] = $itemcat_name;
    }
}

function CalcWeightedAdjustValue($item_value, $total_value, $total_adjust)
{
    return (!empty($total_value) && !empty($total_adjust)) ? $item_value / $total_value * $total_adjust : 0;
}

function GetAdjustedMISStartDt($dfrom)
{
}

function LogReset($ref_id, $ref_type) // GR/ GRRET/ STI/ STO
{
    global $sess_user_id;

    $q = "INSERT INTO log_reset (iLocID, iRefID, cRefType, dtLog, iUserID, cFlag, cStatus) 
			VALUES ('" . SYS_LOCID . "', $ref_id, '$ref_type', '" . NOW . "', '$sess_user_id', '0', 'A')";
    $r = sql_query($q, 'COM.5136');
}

function LogQuery($q_str, $error_flag)
{
    global $sess_user_id;

    $q = "INSERT INTO log_query (iLocID, dtLog, vQuery, iUserID, cFlag, cStatus) 
			VALUES ('" . SYS_LOCID . "', '" . NOW . "', '" . addslashes($q_str) . "', '$sess_user_id', '$error_flag', 'A')";
    $r = sql_query($q, 'COM.5144');
}

function IsReset($id, $type)
{
    return (!empty($id) && !empty($type)) ? GetXFromYID("select count(*) from log_reset where iRefID=$id and cRefType='$type'") : 0;
}

function QuickAddClient($name)
{
    $id = NextID('iClientID', 'client');
    $code = GetUniqueCode($id, $name, 'iClientID', 'cCode', 'client');
    $q = "insert into client values ($id, '$code', '$name', '$name', '', '', '', '', '', '', 0, '', '" . TODAY . "', 'B', 'A')";
    $r = sql_query($q, 'COM.528');
    return $id;
}

function QuickAddContact($name, $ref_id, $ref_type)
{
    $id = NextID('iContactID', 'contacts');
    $q = "insert into contacts values ($id, '$ref_type', $ref_id, '$name', '', '', '', '', '', '', '', '', 'Y', 'A')";
    $r = sql_query($q, 'COM.535');
    return $id;
}

function GetLeadStatusIcon($lead_status_id, $status_str = '')
{
    global $LSTAT_IMG_SML_ARR;
    $x_stat_img = (isset($LSTAT_IMG_SML_ARR[$lead_status_id])) ? $LSTAT_IMG_SML_ARR[$lead_status_id] : '';
    return '<a class="lead_status" style="background:url(' . $x_stat_img . ') no-repeat #fff  4px center;">' . $status_str . '</a>';
}

function GetLeadDatDirectionStr($direction) //, $responsibility
{
    $str = '&nbsp;';

    if ($direction == 'O') //  && $responsibility=='S'
        $str = '<img src="images/sales_customer.png" title="Contact Client"/>';
    elseif ($direction == 'I') //  && $responsibility=='S'
        $str = '<img src="images/customer_sales.png" title="Feedback from Client"/>';
    //	elseif($direction=='0' && $responsibility=='S')
    //		$str = '<img src="images/sales_sales.png" title="Discuss within"/>';

    return $str;
}

function DisplayActivityStatus($status) // X: Cancelled, N: New, A: Active/ Inprocess, C: Complete
{
    $css = '';
    $txt = 'na';
    if ($status == 'P') {
        $css = 'pending_3d';
        $txt = 'pending';
    } elseif ($status == 'C') {
        $css = 'complete_3d';
        $txt = 'complete';
    }

    return '<span class="' . $css . '">' . $txt . '</span>';
}

function GenerateCode($mode, $id = false)
{
    if (!$id) {
        if ($mode == 'QUOTE')
            $id = NextID('iQuoteID', 'quote');
    }

    if (!$id)    $id = 1;

    $prefix = '';

    if ($mode == 'QUOTE')        $prefix = 'Q';
    else if ($mode == 'LEAD')    $prefix = 'L';

    return $prefix . str_pad($id, 5, '0', STR_PAD_LEFT);
}

function GetClientName($client_id)
{
    JustID($client_id);
    return GetXFromYID("select vName from client where iClientID=$client_id");
}

function GetServiceName($service_id)
{
    JustID($service_id);
    return GetXFromYID("select vName from service where iServiceID=$service_id");
}

function GetUserName($user_id)
{
    JustID($user_id);
    return GetXFromYID("select vName from gen_user where iUID=$user_id");
}

function GetUserCode($user_id)
{
    JustID($user_id);
    return GetXFromYID("select cCode from gen_user where iUID=$user_id");
}

function GetServiceStandardName($service_standard_id)
{
    JustID($service_standard_id);
    return GetXFromYID("select vName from service_standard where iServiceStandardID=$service_standard_id");
}

/* function GetServiceLevelName($service_level_id)
{
	JustID($service_level_id);
	return GetXFromYID("select vName from service_level where iServiceLevelID=$service_level_id");
}	// */

function GetStatusString($status, $status_arr, $mode = '1')
{
    $str = '';

    if ($status == 'A')        $css = 'success';
    else if ($status == 'I')    $css = 'warning';
    else if ($status == 'P')    $css = 'warning';
    else                    $css = 'info';

    if (isset($status_arr[$status])) {
        if ($mode == '2')
            $str = '<span class="badge badge-' . $css . '">' . $status_arr[$status] . '</span>';
        else if ($mode == '3')
            $str = '<input type="button" name="btn_just" value="' . $status_arr[$status] . '" class="btn-' . $css . ' btn">';
        else
            $str = '<span class="label label-' . $css . '">' . $status_arr[$status] . '</span>';
    }

    return $str;
}

function GetLocationName($loc_id)
{
    JustID($loc_id);
    return GetXFromYID("select vName from gen_location where iLocID=$loc_id");
}

function GetFranchiseeName($franchisee_id)
{
    JustID($franchisee_id);
    return GetXFromYID("select vName from franchisee where iFranchiseeID=$franchisee_id");
}

function GetOrderServiceDetails($order_id)
{
    global $SERVICE_ARR, $STANDARD_ARR;

    $s_arr = $ss_arr = array();
    $s_str = '';
    $q1 = "select iServiceID, iServiceStandardID from orders_dat where iOrderID=$order_id";
    $r1 = sql_query($q1, 'COM.758');
    while (list($s_id, $ss_id) = sql_fetch_row($r1)) {
        if ($s_id)
            $s_arr[$s_id] = (isset($SERVICE_ARR[$s_id])) ? '<li>' . $SERVICE_ARR[$s_id]['name'] . '</li>' : '<li>' . NA . '</li>';
        if ($ss_id)
            $ss_arr[$ss_id] = (isset($STANDARD_ARR[$ss_id])) ? '<li>' . $STANDARD_ARR[$ss_id]['name'] . '</li>' : '<li>' . NA . '</li>';
    }

    return '<div class="col-sm-6" style="font-size:12px;">' . implode('', $s_arr) . '</div><div class="col-sm-6" style="font-size:12px;"><ul style="list-style:circle;">' . implode('', $ss_arr) . '</ul></div>';
}

function GetFinancialYears($date) // date: yyyy-mm-dd
{
    $start_year = THIS_YEAR;
    $end_year = THIS_YEAR + 1;
    list($year, $month, $day) = explode('-', $date);

    if (intval($month) < 4) {
        $start_year--;
        $end_year--;
    }

    return $start_year . '-' . $end_year;
}

function GetSuffix($parent_id, $table_name, $key_field)
{
    $x = '';
    MustID($parent_id);

    if ($parent_id) {
        $x = GetXFromYID("select max(cSuffix) from $table_name where $key_field=$parent_id");
        $x = ($x) ? ++$x : 'a';
    }

    return $x;
}

function GetUniquePMCCode($mode, $id, $dt, $suffix = '')
{
    return 'PMC/' . strtoupper($mode) . '/' . str_pad($id, 2, '0', STR_PAD_LEFT) . $suffix . '/' . GetFinancialYears($dt);
}

function GetCertAgencyName($cert_agency_id)
{
    JustID($cert_agency_id);
    return GetXFromYID("select vName from cert_agency where iCertAgencyID=$cert_agency_id");
}

function GetCalendarEvents($from_dt, $to_dt, $option_str, $order_id = false, $client_id = false, $user_id = false) // option_arr: OFLW, OPS, OFU, SCH
{
    global $CALENDAR_OPTIONS;
    $option_arr = $arr_d = $arr_t = $data_arr = array();
    $cond_lead = '';
    // $cond_orders = $cond_oflow = $cond_ofu = $cond_ops = $cond_sched = '';
    $USER_ARR = GetUserDetails();

    $option_arr = (empty($option_str)) ? array_keys($CALENDAR_OPTIONS) : explode(',', $option_str);

    if ($user_id) {
        $cond_lead = " and iUserID='$user_id'";
    }

    // $user_arr = GetXArrFromYID("select iUID, cCode from gen_user", '3');

    //	if(in_array('LEAD', $option_arr))
    //	{
    $q = "select iLeadDatID, iLeadID, vTask, dtStart, dtEnd, iUID, cStatus from lead_dat where dtStart<='$to_dt' and dtEnd>='$from_dt' $cond_lead";
    $r = sql_query($q, 'COM.788');
    while (list($x_id, $y_id, $x_title, $x_dtstart, $x_dtend, $u_id, $x_stat) = sql_fetch_row($r)) {
        $u_name = 'NA';
        $u_color = '#cdf6b9';

        if (isset($USER_ARR[$u_id])) {
            $u_name = $USER_ARR[$u_id]['text'];
            $u_color = '#' . $USER_ARR[$u_id]['color'];
        }

        $textColor = ($x_stat == 'A') ? 'black' : 'red';
        $arr_t = BuildCalendarEvent($arr_t, 'LD.' . $x_id, $x_title, $x_dtstart, $x_dtend, $u_color, $textColor); // $u_name.': '.
    }
    //	}

    return array($arr_d, $arr_t);
}

function BuildCalendarEvent($arr, $id, $title, $start, $end, $color, $textColor = "black", $allday = false)
{
    $arr[$id] = array('id' => $id, 'title' => $title, 'start' => $start, 'end' => $end, 'color' => $color, 'textColor' => $textColor, 'allDay' => $allday, 'timeFormat' => '(hh:mm)');

    return $arr;
}

function PrepCalendarEventArr4JSon($d_arr, $t_arr)
{
    $arr = array();
    $i = 0;

    /*	foreach($d_arr as $x_dt=>$x)
	{
		$x_title = '';
		
		if(!empty($x['TSK']))
		{
			$x_title .= ', '.$x['TSK'].' tsk';
//			if($x['TSK']>1) $x_title .= 's';
		}
		
		if(!empty($x['MTG']))
		{
			$x_title .= ', '.$x['MTG'].' mtg';
//			if($x['MTG']>1) $x_title .= 's';
		}
		
		if(!empty($x['RFI']))
		{
			$x_title .= ', '.$x['RFI'].' rfi';
//			if($x['RFI']>1) $x_title .= 's';
		}
		
		if(!empty($x['INS']))
		{
			$x_title .= ', '.$x['INS'].' ins';
//			if($x['INS']>1) $x_title .= 's';
		}
		
		$x_title = substr($x_title, 2);
		$arr[$i] = array('id'=>'all.'.$x_dt, 'title'=>$x_title, 'start'=>$x_dt, 'allDay'=>true);		
		$i++;
	}
	
	
	foreach($t_arr as $T)
	{
		$arr[$i] = $T;
		$i++;
	}	// */

    $arr = array_values($d_arr + $t_arr);

    return $arr;
}

function GetCertAgencyRate($cert_agency_id, $service_standard_id, $num_employees, $service_id = 0)
{
    JustID($cert_agency_id);
    JustID($service_standard_id);
    JustID($service_id);
    JustNumeric($num_employees, 'INTEGER');

    $x_amt = $f_amt = 0;
    $q = "select fAmt, fAmt_franchisee from cert_agency_rate where iCertAgencyID=$cert_agency_id and iServiceID=$service_id and iServiceStandardID=$service_standard_id and iNumEmployees_min<=$num_employees and iNumEmployees_max>=$num_employees and cStatus='A'";
    $r = sql_query($q, 'COM.832');
    if (sql_num_rows($r))
        list($x_amt, $f_amt) = sql_fetch_row($r);

    // echo "$x_amt, $f_amt<br />";
    return array($x_amt, $f_amt);
}

function GetCode($ref_id, $ref_type)
{
    $code = '';

    JustID($ref_id);

    if ($ref_id) {
        if ($ref_type == 'ORD')    $code = GetXFromYID("select vCode from orders where iOrderID=$ref_id");
    }

    return $code;
}

function GetReference($ref_id, $ref_type)
{
    $str = '';

    if ($ref_type == 'CLI') $str = GetXFromYID("select vAlias from client where iClientID=$ref_id");

    return $str;
}

function IsSameCode4Name($name, $name_old, $char_len = 1)
{
    $name = substr(db_input(strtoupper($name)), 1, $char_len);
    $name_old = substr(db_input(strtoupper($name_old)), 1, $char_len);

    return ($name == $name_old) ? true : false;
}

function LogThis($section, $ref_id, $ref_type, $desc)
{
    global $sess_user_id, $sess_user_sess;
    $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '';

    $id = NextID('iLogOID', 'log_operations');
    $q = "insert into log_operations values ($id, '$section', $ref_id, '$ref_type', '" . db_input($desc) . "', $sess_user_id, '" . NOW . "', '$sess_user_sess', '$ip', 'A')";
    $r = sql_query($q, 'COM.899');

    $user_arr = GetNotificationUsers($ref_id, $ref_type);
    $user_arr[$sess_user_id] = $sess_user_id;
    $q_str = '';
    foreach ($user_arr as $uid) {
        $x_seen = 'N';
        $x_dseen = '';

        if ($uid == $sess_user_id) {
            $x_seen = 'Y';
            $x_dseen = NOW;
        }

        $q_str .= ", ($id, $uid, '$x_dseen', '$x_seen')";
    }

    $q = "insert into log_notifications values " . substr($q_str, 1);
    $r = sql_query($q, 'COM.908');
}

function GetNotificationUsers($ref_id, $ref_type)
{
    $arr = array();

    if ($ref_type == 'scheduler')
        $arr = GetXArrFromYID("select iUserID from scheduler where iSchedID=$ref_id");
    else
        $arr = GetXArrFromYID("select iUID from gen_user where cStatus='A'");

    return $arr;
}

function SetOrderCompletionPerc($order_id)
{
    JustID($order_id);
    $order_status = GetXFromYID("select cStatus from orders where iOrderID=$order_id");
    if ($order_status == 'I') {
        $oflow_arr = GetXArrFromYID("select cStatus, count(*) from orders_flow where iOrderID=$order_id group by cStatus", '3');
        $oflow_total = array_sum($oflow_arr);
        $oflow_done = (isset($oflow_arr['A'])) ? $oflow_arr['A'] : 0;

        //		$opays_arr = GetXArrFromYID("select cStatus, count(*) from orders_payschedule where iOrderID=$order_id group by cStatus", '3');
        //		$opays_total = array_sum($opays_arr);
        //		$opays_done = (isset($opays_arr['A']))? $opays_arr['A']: 0;
        $opays_done = $opays_total = 0;

        $odat_arr = GetXArrFromYID("select cStatus, count(*) from orders_dat where iOrderID=$order_id group by cStatus", '3');
        $odat_total = array_sum($odat_arr);
        $odat_done = (isset($odat_arr['A'])) ? $odat_arr['A'] : 0;

        $oldat_arr = GetXArrFromYID("select cStatus, count(*) from orders_log_dat where iOrderID=$order_id group by cStatus", '3');
        $oldat_total = array_sum($oldat_arr);
        $oldat_done = (isset($oldat_arr['A'])) ? $oldat_arr['A'] : 0;

        $order_completion = (($oflow_total + $odat_total + $opays_total) > 0) ? round((($oflow_done + $odat_done + $opays_done) / ($oflow_total + $odat_total + $opays_total) * 100)) - 1 : 0;

        $q = "update orders set iCompletePerc=$order_completion where iOrderID=$order_id";
        $r = sql_query($q, 'ORD_E.267');
    }
}

function GetCertAgencyPercArr($ca_id, $order_id)
{
    $ca_amt_paid = $ca_famt_paid = 0;
    $q = "select sum(fAmt), sum(fCertAgency_amt), sum(fCertAgency_famt) from orders_dat where iOrderID=$order_id and iCertAgencyID=$ca_id";
    $r = sql_query($q, 'COM.1041');
    if (sql_num_rows($r)) {
        list($pend_amt, $ca_amt_pend, $ca_famt_pend) = sql_fetch_row($r);

        $q = "select sum(fAmt), sum(fCertAgency_amt), sum(fCertAgency_famt) from acct_receipt where iRefID=$order_id and cRefType='ORD' and iCertAgencyID=$ca_id";
        $r = sql_query($q, 'COM.1047');
        if (sql_num_rows($r))
            list($paid_amt, $ca_amt_paid, $ca_famt_paid) = sql_fetch_row($r);

        $ca_perc = $ca_fperc = 0;

        if (($pend_amt - $paid_amt) > 0) {
            $ca_perc = round(100 / ($pend_amt - $paid_amt) * ($ca_amt_pend - $ca_amt_paid), 2);
            $ca_fperc = round(100 / ($pend_amt - $paid_amt) * ($ca_famt_pend - $ca_famt_paid), 2);
        }
    }

    return array($ca_perc, $ca_fperc);
}

function CalcOrderTotals($order_id)
{
    $total = $paid = 0;

    $total = GetXFromYID("select sum(fAmt) from orders_dat where iOrderID=$order_id");
    if (empty($total)) $total = 0;

    $paid = GetXFromYID("select sum(fAmt) from acct_receipt where iRefID=$order_id and cRefType='ORD'");
    if (empty($paid)) $paid = 0;

    $q = "update orders set fTotal=$total, fGTotal = ($total - fDisc), fPaid=$paid, fPending=($total - ($paid + fDisc)) where iOrderID=$order_id";
    $r = sql_query($q, 'COM.1069');
}

function BuildTopAlerts($ref_id, $ref_type, $time, $text, $flag)
{
    $str = $icon = '';
    $color = 'tiles-grape';
    $ref = $ref_id . '.' . $ref_type;

    if ($ref_type == 'scheduler')
        $icon = '<i class="icon-calendar-empty"></i>';

    if ($flag == 'new')    $color = 'tiles-green';
    else if ($flag == 'old')    $color = 'tiles-grape';

    $str = '<div class="row" id="div_alert_' . $ref . '">
			  <div class="col-xs-6 col-sm-9"> <a href="#" onclick="ShowThisAlert(this);" id="ashow.' . $ref . '" class="shortcut-tiles ' . $color . '">
				<div class="tiles-row">
				  <div class="col-sm-2" align="center">
					<div class="pull-left"><i class="icon-camera"></i> ' . $time . '</div>
				  </div>
				  <div class="col-sm-10">' . $text . ' (' . $flag . ')</div>
				</div>
				</a> </div>
				<div class="col-xs-6 col-sm-2" align="center"> <a href="#" onclick="SnoozeThisAlert(this);" id="asnooze.' . $ref . '" class="shortcut-tiles ' . $color . '">
				<div class="tiles-row">
				  snooze (30 min)
				</div>
				</a> </div>
				<div class="col-xs-6 col-sm-1" align="center"> <a href="#" onclick="CancelThisAlert(this);" id="acancel.' . $ref . '" class="shortcut-tiles ' . $color . '">
				<div class="tiles-row">
				  cancel
				</div>
				</a> </div>
			</div>';

    return $str;
}

/* function GetServiceDetails($cond='')
{
	$arr = array();
	
	$q = "select iServiceID, cCode, vName, vTheme, cStatus from service where 1 $cond";
	$r = sql_query($q, 'COM.1131');
	while(list($id, $code, $name, $theme, $status) = sql_fetch_row($r))
		$arr[$id] = array('id'=>$id, 'code'=>$code, 'name'=>$name, 'theme'=>$theme, 'status'=>$status, 'code_css'=>'<span class="label label-'.$theme.'" title="'.$name.'">'.$code.'</span>', 'name_css'=>'<span class="label label-'.$theme.'">'.$name.'</span>');
	
	return $arr;
}

function GetStandardDetails($cond='')
{
	$arr = array();
	
	$q = "select iServiceStandardID, vName, vTheme, cStatus from service_standard where 1 $cond";
	$r = sql_query($q, 'COM.1143');
	while(list($id, $name, $theme, $status) = sql_fetch_row($r))
		$arr[$id] = array('id'=>$id, 'code'=>'', 'name'=>$name, 'theme'=>$theme, 'status'=>$status, 'name_css'=>'<span class="badge badge-'.$theme.'">'.$name.'</span>');
	
	return $arr;
}

function GetCertAgencyDetails($cond='')
{
	$arr = array();
	
	$q = "select iCertAgencyID, vName, cStatus from cert_agency where 1 $cond";
	$r = sql_query($q, 'COM.1155');
	while(list($id, $name, $status) = sql_fetch_row($r))
		$arr[$id] = array('id'=>$id, 'code'=>'', 'name'=>$name, 'theme'=>'', 'status'=>$status);
	
	return $arr;
}	// */

function GetClientDetails($cond = '')
{
    $arr = array();
    $q = "select iClientID, cCode, vAlias, vName, cStatus from client where 1 $cond";
    $r = sql_query($q, 'COM.1165');
    while (list($id, $code, $alias, $name, $status) = sql_fetch_row($r))
        $arr[$id] = array('id' => $id, 'text' => $name, 'code' => $code, 'alias' => $alias, 'name' => $name, 'status' => $status);

    return $arr;
}

function GetViewDetails($cond = "")
{
    $arr = array();
    $q = "select iViewID, vName, cStatus from gen_view where 1 $cond order by iRank, vName";
    $r = sql_query($q, 'COM.1176');
    while (list($id, $name, $status) = sql_fetch_row($r))
        $arr[$id] = array('id' => $id, 'text' => $name, 'name' => $name, 'status' => $status);

    return $arr;
}

function GetZoneDetails($cond = "")
{
    $arr = array();
    /*$q = "select iZoneID, vName, cStatus from gen_zone where 1 $cond order by iRank, vName";
	$r = sql_query($q, 'COM.1187');
	while(list($id, $name, $status) = sql_fetch_row($r))
		$arr[$id] = array('id'=>$id, 'text'=>$name, 'name'=>$name, 'status'=>$status);*/

    return $arr;
}

function GetSpecialReqDetails($cond = "")
{
    $arr = array();
    $q = "select iSpReqID, vName, cStatus from gen_specialreq where 1 $cond order by iRank, vName";
    $r = sql_query($q, 'COM.1198');
    while (list($id, $name, $status) = sql_fetch_row($r))
        $arr[$id] = array('id' => $id, 'text' => $name, 'name' => $name, 'status' => $status);

    return $arr;
}

function GetLocationDetails($cond = "")
{
    $arr = array();
    $q = "select iLocID, vName, iParentID, iAncestorID, iLevel, iRank, cStatus from gen_location where 1 $cond order by iRank, vName";
    $r = sql_query($q, 'COM.1209');
    while (list($id, $name, $parentid, $ancestorid, $level, $rank, $status) = sql_fetch_row($r))
        $arr[$id] = array('id' => $id, 'text' => GenerateSpace($level) . $name, 'name' => $name, 'parentid' => $parentid, 'ancestorid' => $ancestorid, 'level' => $level, 'rank' => $rank, 'status' => $status);

    return $arr;
}

function GetPropClassDetails($cond = "")
{
    $arr = array();
    $q = "select iPropClassID, vName, iParentID, iAncestorID, iLevel, iRank, cStatus from gen_propclass where 1 $cond order by iRank, vName";
    $r = sql_query($q, 'COM.1220');
    while (list($id, $name, $parentid, $ancestorid, $level, $rank, $status) = sql_fetch_row($r))
        $arr[$id] = array('id' => $id, 'text' => GenerateSpace($level) . $name, 'name' => $name, 'parentid' => $parentid, 'ancestorid' => $ancestorid, 'level' => $level, 'rank' => $rank, 'status' => $status);

    return $arr;
}

function GetUserDetails($cond = "")
{
    $arr = array();
    /*$q = "select iUserID, vName, vPic, cStatus from users where 1 $cond order by iLevel, vName";
	$r = sql_query($q, 'COM.1187');
	while(list($id, $name, $pic, $status) = sql_fetch_row($r))
		$arr[$id] = array('id'=>$id, 'text'=>$name, 'name'=>$name, 'status'=>$status, 'pic'=>$pic);*/

    return $arr;
}

function SetSysIndex($ref_type, $ref_id, $data_arr)
{
    $arr = array();

    sql_query("delete from sys_index where cRefType='$ref_type' and iRefID=$ref_id");

    foreach ($data_arr as $a) {
        $a_val = trim(addslashes($a[0]));
        $param_type = $a[1];
        $param_id = $a[2];
        $a_pts = $a[3];

        if (empty($a_val)) continue;

        $arr[] = "('$ref_type', $ref_id, '$a_val', '$param_type', $param_id, '', $a_pts)";
    }

    if (count($arr))
        sql_query('insert into sys_index values ' . implode(', ', $arr), 'COM.1256');
}

function UpdateLeadStatus($lead_id)
{
    global $LEADSTATUSID_COMPLETE, $LEADSTATUSID_COLD;

    $q = "select iLeadDatID, iLeadStatusID, iScore, dtTask from lead_dat where iLeadID=$lead_id order by dtStart desc, dtEnd desc limit 1";
    $r = sql_query($q, 'COM.1267');
    if (sql_num_rows($r)) {
        list($ld_id, $ls_id, $ld_score, $ld_date) = sql_fetch_row($r);

        $status = ($ls_id == $LEADSTATUSID_COMPLETE || $ls_id == $LEADSTATUSID_COLD) ? 'A' : 'I';

        $q = "update lead set iScore=$ld_score, dtLastActivity='$ld_date', cStatus='$status' where iLeadID=" . $lead_id;
        $r = sql_query($q, 'COM.1275');
    }
}

function GetHUDCart()
{
    global $_SESSION; //, $LOC_ARR, $PROPCLASS_ARR;

    if (!isset($LOC_ARR)) $LOC_ARR = GetLocationDetails();
    if (!isset($PROPCLASS_ARR)) $PROPCLASS_ARR = GetPropClassDetails();

    $list_cart_lcount = (isset($_SESSION[PROJ_SESSION_ID]->list_cart_arr['L'])) ? count($_SESSION[PROJ_SESSION_ID]->list_cart_arr['L']) : 0;
    $list_cart_pcount = (isset($_SESSION[PROJ_SESSION_ID]->list_cart_arr['P'])) ? count($_SESSION[PROJ_SESSION_ID]->list_cart_arr['P']) : 0;
    $list_cart_count = ($list_cart_pcount + $list_cart_lcount);

    $str = '<a data-toggle="dropdown" class="dropdown-toggle" href="index.html#"> <i class="fa fa-shopping-cart"></i> <span class="badge bg-theme">' . $list_cart_count . '</span> </a>
  <ul class="dropdown-menu extended inbox">
    <div class="notify-arrow notify-arrow-green"></div>';

    if ($list_cart_pcount) {
        $str .= '<li>
      <p class="green">' . $list_cart_pcount . ' Properties Shortlisted</p>
    </li>';

        $id_str = implode(', ', $_SESSION[PROJ_SESSION_ID]->list_cart_arr['P']);
        $q = "select p.iPropertyID, cCode, iLocID, iZoneID, iPropClassID, vPic from property as p left outer join property_pics as pp on p.iPropertyID=pp.iPropertyID and pp.iRank=1 where p.iPropertyID in ($id_str)";
        $r = sql_query($q, 'COM.1295');
        while (list($x_id, $x_code, $l_id, $z_id, $pc_id, $pp_pic) = sql_fetch_row($r)) {
            $l_str = (isset($LOC_ARR[$l_id])) ? $LOC_ARR[$l_id]['name'] : '';
            $pc_str = (isset($PROPCLASS_ARR[$pc_id])) ? $PROPCLASS_ARR[$pc_id]['name'] : '';

            $str .= '    <li> <a href="property_edit.php?mode=E&id=' . $x_id . '"> <span class="photo"><img alt="avatar" src="' . PROPERTY_PATH . $x_id . '/pics/' . $pp_pic . '"></span> <span class="subject"> <span class="from"><strong style="display:none;">' . $x_code . '</strong>' . $pc_str . '</span> </span> <span class="message">' . $l_str . ' </span> </a></li>';
        }

        $str .= '    <li><a href="list_shortlist.php?ref=P">save these properties</a> </li>';
    }

    if ($list_cart_lcount) {
        $str .= '<li>
      <p class="green">' . $list_cart_lcount . ' Leads Shortlisted</p>
    </li>';

        $id_str = implode(', ', $_SESSION[PROJ_SESSION_ID]->list_cart_arr['L']);
        // $q = "select p.iPropertyID, cCode, iLocID, iZoneID, iPropClassID, vPic from property as p left outer join property_pics as pp on p.iPropertyID=pp.iPropertyID and pp.iRank=1 where p.iPropertyID in ($id_str)";
        $q = "select l.iLeadID, c.vName, c.vLocation from lead as l, client as c where l.iClientID=c.iClientID and l.iLeadID in ($id_str)";
        $r = sql_query($q, 'COM.1295');
        while (list($x_id, $c_name, $c_addr) = sql_fetch_row($r)) {
            $str .= '    <li> <a href="lead_edit.php?mode=E&id=' . $x_id . '"> <span class="subject"> <span class="from"><strong>#' . $x_id . '</strong> ' . $c_name . '</span> </span> <span class="message">' . $c_addr . ' </span> </a></li>';
        }

        $str .= '    <li><a href="list_shortlist.php?ref=L">save these leads</a> </li>';
    }

    if (!$list_cart_count)
        $str .= '    <li> No Items Shortlisted </li>';

    $str .= '  </ul>';

    return $str;
}

function GetRelevantLocationIdArr($loc_id)
{
    $arr = array();
    $arr[$loc_id] = $loc_id;
    $arr = GetTreeArr('iLocID', 'gen_location', '0', $loc_id, $arr, '3');

    return $arr;
}

function GetRelevantPropClassIdArr($pc_id)
{
    $arr = array();
    $arr[$pc_id] = $pc_id;
    $arr = GetTreeArr('iPropClassID', 'gen_propclass', '0', $pc_id, $arr, '3');

    return $arr;
}

function GetPropertyPicsStr($property_id, $selected_arr = false)
{
    //	global $_SESSION[PROJ_SESSION_ID]->list_pics_arr;
    $str = $cond = '';
    $arr = array();

    $dir_upload = PROPERTY_UPLOAD . $property_id . '/pics/';
    $dir_path = PROPERTY_PATH . $property_id . '/pics/';

    if (!empty($selected_arr) && is_array($selected_arr))
        $cond = " and iPPicID in (" . implode(', ', $selected_arr) . ")";

    $q = "select iPPicID, vName, vPic from property_pics where iPropertyID=$property_id and cStatus='A' $cond order by iRank";
    $r = sql_query($q, 'COM.1360');
    while (list($x_id, $x_name, $x_pic) = sql_fetch_row($r))
        if (IsExistFile($x_pic, $dir_upload)) {
            $arr[$x_id] = array('name' => $x_name, 'pic' => $dir_path . $x_pic);

            $is_checked = isset($_SESSION[PROJ_SESSION_ID]->list_pics_arr['P'][$property_id][$x_id]) ? 'checked' : '';
            $is_checked = isset($selected_arr[$x_id]) ? 'checked' : '';

            $str .= '<label class="col-xs-1" style="border:1px solid #eaeaea;padding:0px;" title="' . $x_name . '"><div><img src="' . $dir_path . $x_pic . '" style="width:100%;" /></div><div><input type="checkbox" name="chk[]" value="P.' . $property_id . '.' . $x_id . '" onclick="ShortListPic(this);" ' . $is_checked . ' /></div></label>';
        }

    return $str;
}

function BuildPropertyRow($i, $o, $mode = false, $container_style = 'all')
{
    global $_SESSION, $PROPCLASS_ARR, $LOC_ARR, $ZONE_ARR;
    $is_shortlist_allowed = true;
    $is_list = false;

    if ($mode == 'list' || $mode == 'list_add' || $mode == 'list_rmv') {
        $is_list = true;
        $is_shortlist_allowed = false;
    }

    if ($mode == 'shortlisted')
        $is_shortlist_allowed = 'hide';

    if ($mode == 'lead') {
    }

    $x_id = db_output($o->iPropertyID);
    $x_code = db_output($o->cCode);
    //	$ = db_output($o->vLatitude);
    //	$ = db_output($o->vLongitude);
    $x_location = db_output($o->vLocation);
    $x_dor = db_output($o->dDor);
    $x_area = db_output($o->iArea);
    $x_bua = db_output($o->iAreaBuiltUp);
    $x_price = db_output($o->iPrice);
    $x_pricelisted = db_output($o->iPriceListed);
    $x_complexname = db_output($o->vComplexName);
    $x_develstatus = db_output($o->cDevelStatus);
    //	$ = db_output($o->dPossessionDate);
    $x_layout = db_output($o->vLayout);
    $x_overview = db_output($o->vOverview);
    //	$ = db_output($o->fBrokeragePerc);
    $x_amenities = db_output($o->vAmenities);
    //	$ = db_output($o->cClearTitle);
    //	$ = db_output($o->cLiabilities);
    //	$x_liabilites = db_output($o->vLiabilitiesDesc);
    $x_dinspect = db_output($o->dInspection);
    $x_sold = db_output($o->cSold);
    //	$ = db_output($o->dSold);
    //	$ = db_output($o->iClientID_Buyer);
    //	$ = db_output($o->fAmtSold);
    //	$ = db_output($o->cFeature);
    //	$x_listed = db_output($o->iListed);
    //	$ = db_output($o->dLastList);
    //	$x_visited = db_output($o->iVisited);
    $x_dlastvisit = db_output($o->dLastVisit);
    $x_rank = db_output($o->iRank);
    $x_stat = db_output($o->cStatus);

    $pp_name = db_output($o->vName);
    $pp_pic = db_output($o->vPic);

    $client_id = db_output($o->iClientID);
    $client_name = ($client_id) ? GetXFromYID("select vName from client where iClientID=$client_id") : NA;

    $x_desc_arr = $y_desc_arr = $notes_arr = array();

    $propclass_id = db_output($o->iPropClassID);
    $propclass_name = (isset($PROPCLASS_ARR[$propclass_id])) ? $PROPCLASS_ARR[$propclass_id]['name'] : false;
    if ($propclass_name) $x_desc_arr[] = $propclass_name;

    if ($x_location != '') $x_desc_arr[] = $x_location;

    $loc_id = db_output($o->iLocID);
    $loc_name = (isset($LOC_ARR[$loc_id])) ? $LOC_ARR[$loc_id]['name'] : false;
    if ($loc_name) $x_desc_arr[] = $loc_name;

    $zone_id = db_output($o->iZoneID);
    $zone_name = (isset($ZONE_ARR[$zone_id])) ? $ZONE_ARR[$zone_id]['name'] : false;

    if ($x_area > 0)    $y_desc_arr[] = 'area: ' . FormatNumber($x_area) . ' m<sup>2</sup>';
    if ($x_bua > 0)    $y_desc_arr[] = 'b.u.a.: ' . FormatNumber($x_bua) . ' m<sup>2</sup>';
    if ($x_pricelisted > 0)    $y_desc_arr[] = 'price: ' . floatval($x_pricelisted) . ' Cr';
    if ($zone_name) $y_desc_arr[] = $zone_name;

    $uid_entry = db_output($o->iUID_entry);
    $uid_inspect = db_output($o->iUID_Inspected);

    $x_desc_str = (count($x_desc_arr)) ? implode(', ', $x_desc_arr) : '';
    $y_desc_str = (count($y_desc_arr)) ? implode(', ', $y_desc_arr) : '';
    $x_pics_listed = '';


    /*	if($shortlist_mode) // being accessed from LIST module
	{
		global $dat_pic_id_arr;
		// $x_pics_list_idstr = (isset($dat_pic_id_arr[$x_id]))? implode(', ', $dat_pic_id_arr[$x_id]): 0;
		$x_pics_list_idarr = (isset($dat_pic_id_arr[$x_id]))? $dat_pic_id_arr[$x_id]: false;
		$x_pics_list = GetPropertyPicsStr($x_id, $x_pics_list_idarr); //  " and iPPicID in ($x_pics_list_idstr) "
	}
	$x_pics = GetPropertyPicsStr($x_id);	// */

    if ($x_layout != '')    $notes_arr[] = array('tabs' => 'Layout', 'text' => $x_layout);
    if ($x_overview != '')    $notes_arr[] = array('tabs' => 'Overview', 'text' => $x_overview);
    if ($x_amenities != '') $notes_arr[] = array('tabs' => 'Amenities', 'text' => $x_amenities);
    // if($x_pics_list!='')$notes_arr[] = array('tabs'=>'Attached', 'text'=>$x_pics_list);
    // if($x_pics!='')		$notes_arr[] = array('tabs'=>'Pictures', 'text'=>$x_pics);

    $x_code_str = '<a href="property_edit.php?mode=E&id=' . $x_id . '">' . $x_code . '</a>';
    $x_stat_str = GetStatusImageString('PROPERTY', $x_stat, $x_id);

    $x_pic = $x_id . '.jpg';
    //	$ppic_upload = PROPERTY_PATH.$x_id.'/pics/';
    //	$ppic_path = PROPERTY_PATH.$x_id.'/pics/';

    $is_this_property_in_cart = (isset($_SESSION[PROJ_SESSION_ID]->list_cart_arr['P'][$x_id])) ? true : false;

    $str = '';

    if ($container_style == 'all')    $str .= '  <tr>' . NEWLINE;
    if ($container_style == 'all')    $str .= '	<td>' . NEWLINE;

    $str .= '	  <span class="pull-right"><button class="btn btn-xs btn-info"><strong>Score: ' . $x_rank . '</strong></button>' . NEWLINE;
    // $str .= '	  <h3><span class="label label-default">Score: '.$x_rank.'</span></h3>'.NEWLINE;

    if ($is_shortlist_allowed)
        $str .= '	  ' . GetShortlistIcon($is_this_property_in_cart, 'P', $x_id, $is_shortlist_allowed) . NEWLINE;

    if ($is_list && ($mode == 'list_add' || $mode == 'list_rmv'))
        $str .= '	  <input type="checkbox" name="chkdat[]" class="opt_chk" id="chkdat_P.' . $x_id . '" value="' . $x_id . '" ' . (($mode == 'list_rmv') ? 'checked' : '') . ' style="display:none;" />
						<span class="btn btn-sm ' . (($mode == 'list_rmv') ? 'btn-success' : 'btn-danger') . '" title="' . (($mode == 'list_rmv') ? 'Remove From List' : 'Add To List') . '" onclick="ToggleForList(this, \'P\', ' . $x_id . ');"><i class="fa fa-check"></i> </span>' . NEWLINE;

    $str .= '	  </span> <img src="' . (IsExistFile($pp_pic, PROPERTY_UPLOAD . $x_id . '/pics/') ? PROPERTY_PATH . $x_id . '/pics/' . $pp_pic : PROPERTY_PATH . '0.jpg') . '" width="10%" class="pull-left" style="border-radius:5px;">' . NEWLINE;
    $str .= '	  <div style="border: 0px solid #f00;margin-left:10%;padding-left:5px;"> ' . $i . '. ' . $x_code_str . ' ' . $x_desc_str . '<br><em>' . $y_desc_str . '</em>' . NEWLINE;

    $notes_len = count($notes_arr);
    if ($notes_len) {
        $str .= '		<div>' . NEWLINE;
        $str .= '		  <div>' . NEWLINE;

        $is_first = true;
        foreach ($notes_arr as $a_key => $a) {
            if (!$is_first) $str .= ' | ';
            $is_first = false;

            $str .= '			<a href="#" id="tab' . $x_id . '_' . $a_key . '" class="show_tab">' . $a['tabs'] . '</a>' . NEWLINE;
        }
        $str .= '	|		<a href="property_pics_gallery.php?property_id=' . $x_id . '&ref_type=P&is_popup=1" name="quick_popup" iframe_width="80%" iframe_height="80%">Pictures</a>' . NEWLINE;

        $str .= '		  </div>' . NEWLINE;
        $str .= '		  <div>' . NEWLINE;

        foreach ($notes_arr as $a_key => $a)
            $str .= '					<div id="tab' . $x_id . '_' . $a_key . '_text" style="display:none;" class="col-sm-12">' . $a['text'] . '</div>' . NEWLINE;

        $str .= '		  </div>' . NEWLINE;
        $str .= '		</div>' . NEWLINE;
    }

    foreach ($notes_arr as $a_key => $a)
        $str .= '					<div id="tab' . $x_id . '_' . $a_key . '_text" style="display:none;" class="col-sm-12">' . $a['text'] . '</div>' . NEWLINE;

    $str .= '		  </div>' . NEWLINE;
    $str .= '		</div>' . NEWLINE;


    $str .= '</div>';

    if ($container_style == 'all')    $str .= '	  </td>' . NEWLINE;
    if ($container_style == 'all')    $str .= '  </tr>' . NEWLINE;

    return $str;
}

function GetShortlistIcon($is_this_property_in_cart, $ref_type, $ref_id, $shortlist_mode)
{
    return '<button class="btn btn-xs ' . (($is_this_property_in_cart) ? 'btn-danger' : 'btn-success') . '" title="' . (($is_this_property_in_cart) ? 'Remove From ShortList' : 'Add To ShortList') . '" onclick="ShortList(this, \'' . $ref_type . '\', ' . $ref_id . ', \'' . $shortlist_mode . '\');"><i class="fa fa-shopping-cart"></i> </button>';
}

function ListResults($type, $cond)
{
    $dat_arr = array();

    if ($type == 'L')
        $q = "select l.*, c.vName, c.vLocation, c.vLandline, c.vMobile, c.vEmail, c.vLocation, c.vAddress from lead as l, client as c where l.iClientID=c.iClientID and ($cond) order by dtLastActivity desc, dtLead desc, iLeadID desc";
    else if ($type == 'P')
        $q = "select p.iPropertyID, p.cCode, p.iClientID, p.iPropClassID, p.iZoneID, p.vLocation, p.iLocID, p.vLatitude, p.vLongitude, p.dDor, p.iArea, p.iAreaBuiltUp, p.iPrice, p.iPriceListed, p.vComplexName, p.cDevelStatus, p.dPossessionDate, p.vLayout, p.vOverview, 
				p.fBrokeragePerc, p.vAmenities, p.cClearTitle, p.cLiabilities, p.vLiabilitiesDesc, p.iUID_entry, p.iUID_Inspected, p.dInspection, p.cSold, p.dSold, p.iClientID_Buyer, p.fAmtSold, p.cFeature, p.iListed, p.dLastList, p.iVisited, p.dLastVisit, 
				p.iRank/DATEDIFF('" . TODAY . "', dDor) as iRank, p.cStatus, pp.vPic, pp.vName 
				from property as p left outer join property_pics as pp on p.iPropertyID=pp.iPropertyID and pp.iRank=1 where ($cond) order by p.iRank/DATEDIFF('" . TODAY . "', dDor), iPriceListed desc";

    if (isset($q)) {
        $r = sql_query($q, 'COM.1545');
        $dat_arr = sql_get_data($r);
    }

    return $dat_arr;
}

function BuildLeadRow($i, $o, $mode = false, $container_style = 'all')
{
    global $_SESSION, $LEAD_STATUS_ARR, $PROPCLASS_ARR, $LOC_ARR;
    $is_shortlist_allowed = true;
    $is_list = false;

    if ($mode == 'list' || $mode == 'list_add' || $mode == 'list_rmv') {
        $is_list = true;
        $is_shortlist_allowed = false;
    }

    if ($mode == 'shortlisted')
        $is_shortlist_allowed = 'hide';

    $x_id = $o->iLeadID;
    $x_dt = FormatDateForIMS($o->dtLead);
    $x_budget = $o->iBudget;
    $x_notes = db_output($o->vNotes);
    $x_details = db_output($o->vDetails);
    $x_dtlast = FormatDateForIMS($o->dtLastActivity);
    $x_score = $o->iScore;
    $x_stat = db_output($o->cStatus);

    $propclass_id = $o->iPropClassID;
    $loc_id = $o->iLocID;
    $zone_id = $o->iZoneID;

    $x_desc_str = '';
    if (isset($PROPCLASS_ARR[$propclass_id]))    $x_desc_str .= ', ' . $PROPCLASS_ARR[$propclass_id]['name'];
    if (isset($LOC_ARR[$loc_id]))                 $x_desc_str .= ', ' . $LOC_ARR[$loc_id]['name'];
    if (isset($ZONE_ARR[$zone_id]))                $x_desc_str .= ', ' . $ZONE_ARR[$zone_id]['name'];
    if ($x_budget > 0)                                $x_desc_str .= ', ' . floatval($x_budget) . ' Cr';
    if ($x_details)                                $x_desc_str .= ', "<em>' . $x_details . '</em>"';
    if ($x_desc_str != '')                            $x_desc_str = '<div>' . substr($x_desc_str, 2) . '</div>';

    $c_id = $o->iClientID;
    $c_name = $o->vName;
    $c_location = $o->vLocation;
    $c_address = $o->vAddress;
    $c_phone = $o->vLandline;
    $c_mobile = $o->vMobile;
    $c_email = $o->vEmail;
    $c_str = BuildClientDetails($c_location, '', $c_phone, $c_mobile, $c_email);
    if ($c_str != '') $c_str = '- <em>' . $c_str . '</em> ';

    $ls_id = $o->iLeadStatusID;
    $ls_name = (isset($LEAD_STATUS_ARR[$ls_id])) ? $LEAD_STATUS_ARR[$ls_id] : 'NA';

    $x_css = ($x_stat != 'A') ? 'warning' : 'success';
    $is_this_lead_in_cart = (isset($_SESSION[PROJ_SESSION_ID]->list_cart_arr['L'][$x_id])) ? true : false;

    $str = '';
    $str .= '<tr>';
    $str .= '  <td class="' . $x_css . '" align="right">' . $i . '.</td>';
    $str .= '  <td class="' . $x_css . '">' . $x_dt . '</td>';
    $str .= '  <td class="' . $x_css . '"><span class="pull-right">';


    if ($is_shortlist_allowed)
        $str .= '	  ' . GetShortlistIcon($is_this_lead_in_cart, 'L', $x_id, $is_shortlist_allowed) . NEWLINE;

    if ($is_list && ($mode == 'list_add' || $mode == 'list_rmv'))
        $str .= '	  <input type="checkbox" name="chkdat[]" class="opt_chk" id="chkdat_L.' . $x_id . '" value="' . $x_id . '" ' . (($mode == 'list_rmv') ? 'checked' : '') . ' style="display:none;" />
						<span class="btn btn-xs ' . (($mode == 'list_rmv') ? 'btn-success' : 'btn-danger') . '" title="' . (($mode == 'list_rmv') ? 'Remove From List' : 'Add To List') . '" onclick="ToggleForList(this, \'L\', ' . $x_id . ');"><i class="fa fa-check"></i> </span>' . NEWLINE;

    // $str .= '		<button class="btn '.($is_this_lead_in_cart)? 'btn-danger': 'btn-success'.'" title="'.($is_this_lead_in_cart)? 'Remove From List': 'Add To List'.'" onclick="ShortList(this, 'L', '.$x_id.');"> <i class="fa fa-shopping-cart"></i> </button>';
    $str .= '		</span><div class="div_score pull-right" data-score="' . $x_score . '"></div><a href="lead_edit.php?mode=E&id=' . $x_id . '">' . $c_name . '</a> ' . $c_str . $x_desc_str . '</td>';
    $str .= '  <td class="' . $x_css . '">' . $x_dtlast . '</td>';
    $str .= '  <td class="' . $x_css . '">' . $ls_name . '</td>';
    $str .= '</tr>';

    return $str;
}

function GetLeadSourceDetails($cond = '')
{
    $arr = array();
    $q = "select iLeadSourceID, vName from gen_leadsource where 1 $cond order by iRank, vName";
    $r = sql_query($q, 'COM.1611');
    while (list($id, $name) = sql_fetch_row($r))
        $arr[$id] = array('id' => $id, 'text' => $name, 'name' => $name, 'status' => 'A');

    return $arr;
}

function GetLeadStatusDetails($cond = '')
{
    $arr = array();
    $q = "select iLeadStatusID, vName from lead_status where 1 $cond order by iRank, vName";
    $r = sql_query($q, 'COM.1622');
    while (list($id, $name) = sql_fetch_row($r))
        $arr[$id] = array('id' => $id, 'text' => $name, 'name' => $name, 'status' => 'A');

    return $arr;
}

function ParseKeywordSearch($keywords)
{
    $cond = '';
    $tmp = $arr = array();

    $tmp = explode(',', $keywords);

    foreach ($tmp as $t) {
        $t = trim($t);
        if (strpos($t, ':') !== false) {
            list($key, $value) = explode(':', $t);
            $arr[$key][$value] = $value;
        }
    }

    $match_arr = $like_arr = $sound_arr = array();
    $match_str = $like_str = $sound_str = '';

    foreach ($arr as $key => $param_arr) {
        $key_cond = '';

        if ($key == 'view')
            $key_cond = " and i.cParamType='V'";
        else if ($key == 'location')
            $key_cond = " and i.cParamType='L'";
        else if ($key == 'type')
            $key_cond = " and i.cParamType='PC'";
        else if ($key == 'zone')
            $key_cond = " and i.cParamType='Z'";
        else if ($key == 'client')
            $key_cond = " and i.cParamType='C'";

        foreach ($param_arr as $str) {
            $match_arr[] = (strpos($str, ' ')) ? '"' . $str . '"' : $str;
            $like_arr[] = "(vContent like '%$str%' $key_cond)";
        }
    }

    $match_str = implode(' ', $match_arr);
    $like_str = implode(' or ', $like_arr);

    return array("and (match(i.vContent) against ('$match_str') or ($like_str))", $match_str);
}

function BuildPropertyPanel($i, $o, $mode = false, $container_style = 'all')
{
    global $_SESSION, $PROPCLASS_ARR, $LOC_ARR, $ZONE_ARR, $VIEW_ARR;
    $is_shortlist_allowed = true;
    $is_list = false;

    if ($mode == 'list' || $mode == 'list_add' || $mode == 'list_rmv') {
        $is_list = true;
        $is_shortlist_allowed = false;
    }

    if ($mode == 'shortlisted')
        $is_shortlist_allowed = 'hide';

    if ($mode == 'lead') {
    }

    $x_id = db_output($o->iPropertyID);
    $x_code = db_output($o->cCode);
    $x_location = (isset($o->vLocation)) ? db_output($o->vLocation) : '';
    //	$ = db_output($o->vLatitude);
    //	$ = db_output($o->vLongitude);
    $x_dor = db_output($o->dDor);
    $x_area = db_output($o->iArea);
    $x_bua = db_output($o->iAreaBuiltUp);
    $x_price = db_output($o->iPrice);
    $x_pricelisted = db_output($o->iPriceListed);
    $x_complexname = db_output($o->vComplexName);
    $x_develstatus = db_output($o->cDevelStatus);
    //	$ = db_output($o->dPossessionDate);
    $x_layout = db_output($o->vLayout);
    $x_overview = db_output($o->vOverview);
    //	$ = db_output($o->fBrokeragePerc);
    $x_amenities = db_output($o->vAmenities);
    //	$ = db_output($o->cClearTitle);
    //	$ = db_output($o->cLiabilities);
    //	$x_liabilites = db_output($o->vLiabilitiesDesc);
    $x_dinspect = db_output($o->dInspection);
    $x_sold = db_output($o->cSold);
    //	$ = db_output($o->dSold);
    //	$ = db_output($o->iClientID_Buyer);
    //	$ = db_output($o->fAmtSold);
    $x_feat = db_output($o->cFeature);
    //	$x_listed = db_output($o->iListed);
    //	$ = db_output($o->dLastList);
    //	$x_visited = db_output($o->iVisited);
    $x_dlastvisit = db_output($o->dLastVisit);
    $x_rank = db_output($o->iRank);
    $x_stat = db_output($o->cStatus);

    $pp_name = db_output($o->vName);
    $pp_pic = db_output($o->vPic);

    $client_id = db_output($o->iClientID);
    $client_name = ($client_id) ? GetXFromYID("select vName from client where iClientID=$client_id") : NA;

    $x_desc_arr = $notes_arr = array();

    $propclass_id = db_output($o->iPropClassID);
    $propclass_name = (isset($PROPCLASS_ARR[$propclass_id])) ? $PROPCLASS_ARR[$propclass_id]['name'] : false;
    //	if($propclass_name) $x_desc_arr[] = $propclass_name;

    $zone_id = db_output($o->iZoneID);
    $zone_name = (isset($ZONE_ARR[$zone_id])) ? $ZONE_ARR[$zone_id]['name'] : false;
    //	if($zone_name) $x_desc_arr[] = $zone_name;

    $loc_id = db_output($o->iLocID);
    $loc_name = (isset($LOC_ARR[$loc_id])) ? $LOC_ARR[$loc_id]['name'] : false;
    //	if($loc_name) $x_desc_arr[] = $loc_name;

    $uid_entry = db_output($o->iUID_entry);
    $uid_inspect = db_output($o->iUID_Inspected);

    $x_desc_str = (count($x_desc_arr)) ? implode(', ', $x_desc_arr) : '';
    $x_pics_listed = '';

    if ($x_layout != '')    $notes_arr[] = array('tabs' => 'Layout', 'text' => $x_layout);
    if ($x_overview != '')    $notes_arr[] = array('tabs' => 'Overview', 'text' => $x_overview);
    if ($x_amenities != '') $notes_arr[] = array('tabs' => 'Amenities', 'text' => $x_amenities);

    $x_code_str = '<a>' . $x_code . '</a>'; // '<a href="property_edit.php?mode=E&id='.$x_id.'">'.$x_code.'</a>';
    $x_sold_str = ($x_sold == 'Y') ? ' style="border:2px solid #f00;"' : '';
    $x_stat_str = GetStatusImageString('PROPERTY', $x_stat, $x_id);

    $x_area_str = '';
    if ($x_bua > 1) $x_area_str .= '/ BUA: ' . $x_bua;
    if ($x_area > 1) $x_area_str .= '/ Area: ' . $x_area;
    if ($x_area_str != '') $x_area_str = substr($x_area_str, 2) . ' m<sup>2</sup>';

    $x_pic = $x_id . '.jpg';

    $is_this_property_in_cart = (isset($_SESSION[PROJ_SESSION_ID]->list_cart_arr['P'][$x_id])) ? true : false;

    $shortlist_str = ($is_shortlist_allowed) ? GetShortlistIcon($is_this_property_in_cart, 'P', $x_id, $is_shortlist_allowed) : '';

    $list_str = ($is_list && ($mode == 'list_add' || $mode == 'list_rmv')) ? '<input type="checkbox" name="chkdat[]" class="opt_chk" id="chkdat_P.' . $x_id . '" value="' . $x_id . '" ' . (($mode == 'list_rmv') ? 'checked' : '') . ' style="display:none;" /><span class="btn btn-xs ' . (($mode == 'list_rmv') ? 'btn-success' : 'btn-danger') . '" title="Remove From List" onclick="ToggleForList(this, \'P\', ' . $x_id . ');"><i class="fa fa-check"></i> </span>' : '';

    $str = '';
    $str .= '<div class="col-lg-4 col-md-4 col-sm-4 mb">';
    $str .= '	<div class="property-panel pn2" ' . $x_sold_str . '><a href="property_edit.php?mode=E&id=' . $x_id . '">';
    if ($x_feat == 'Y')
        $str .= '   <div class="badge badge-hot-property" title="Hot Property"><i class="fa fa-fire"></i></div>';
    $str .= '		<div class="property-bg" style="background: url(' . (IsExistFile($pp_pic, PROPERTY_UPLOAD . $x_id . '/pics/') ? PROPERTY_PATH . $x_id . '/pics/' . $pp_pic : PROPERTY_PATH . '0.jpg') . ') no-repeat center top;">';
    $str .= '			<div class="property-header"><div class="col-sm-6 col-xs-6">' . $propclass_name . '</div><div class="col-sm-6 col-xs-6 goright">' . $x_location . '</div></div>';
    $str .= '			<div class="property-footer"><div class="col-sm-6 col-xs-6">' . $x_code . '</div><div class="col-sm-6 col-xs-6 goright">' . $x_area_str . '</div></div>';
    $str .= '		</div></a>';
    //	$str .= '		<div class="property-title">'.$x_code.'</div>';
    $str .= '		<div class="property-text"><div class="col-sm-6 col-xs-6">' . $loc_name . '</div><div class="col-sm-6 col-xs-6 goright"><span class="btn btn-primary btn-xs">' . floatval($x_pricelisted) . ' Cr</span> ' . $shortlist_str . ' ' . $list_str . '</div>';

    if (count($notes_arr)) {
        $str .= '	  <div>' . NEWLINE;

        $is_first = true;
        foreach ($notes_arr as $a_key => $a) {
            if (!$is_first) $str .= ' | ';
            $is_first = false;

            $str .= '	<a href="#" id="tab' . $x_id . '_' . $a_key . '" onclick="return false;" class="show_tab">' . $a['tabs'] . '</a>' . NEWLINE;
        }
        $str .= '	|	<a href="property_pics_gallery.php?property_id=' . $x_id . '&ref_type=P&is_popup=1" name="quick_popup" iframe_width="60%" iframe_height="90%">Pictures</a>' . NEWLINE;

        $str .= '	  </div>' . NEWLINE;
        $str .= '	  <div>' . NEWLINE;

        foreach ($notes_arr as $a_key => $a)
            $str .= '					<div id="tab' . $x_id . '_' . $a_key . '_text" style="display:none;border:1px solid #eaeaea;background-color:#eaeaea;">' . $a['text'] . '</div>' . NEWLINE;

        $str .= '	  </div>' . NEWLINE;
    }

    $str .= '		</div>';
    $str .= '	</div>';
    $str .= '</div>';

    return $str;
}

function BuildClientDetails($location, $address, $phone, $mobile, $email)
{
    $str = '';
    if ($location != '') $str .= ', ' . $location;
    if ($address != '') $str .= ', ' . $address;
    if ($phone != '') $str .= ', ' . $phone;
    if ($mobile != '') $str .= ', ' . $mobile;
    if ($email != '') $str .= ', ' . $email;

    return ($str != '') ? substr($str, 2) : '';
}

function GetSubContentCat($level, $parentid, $arr, $mode = "1", $cond = "")
{
    $space = "";
    $level++;
    $q = "select iCatID, vName, iParentID, cStatus from category where iParentID=$parentid $cond order by iRank";
    $r = sql_query($q, 'COM.67');

    if (mysql_num_rows($r)) {
        if ($mode == "1") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;&nbsp;";

            while (list($id, $nm, $pid, $stat) = mysql_fetch_row($r)) {
                $arr[$id] = $space . $nm;
                $arr = GetSubContentCat($level, $id, $arr, $mode);
            }
        } elseif ($mode == "2") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;&nbsp;";

            for ($i = 1; list($id, $nm, $pid, $stat) = mysql_fetch_row($r); $i++) {
                $arr[$id] = array("I" => $i, "LEVEL" => $level, "SPACE" => $space, "ID" => $id, "NAME" => $nm, "PARENTID" => $pid, "STATUS" => $stat);
                $arr = GetSubContentCat($level, $id, $arr, $mode);
            }
        } else if ($mode == "3") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;";

            while (list($id, $nm, $pid, $stat) = mysql_fetch_row($r)) {
                $pnm = (isset($arr[$pid])) ? trim($arr[$pid]) . ' &gt;&gt; ' : '';
                $arr[$id] = $space . $pnm . $nm;
                $arr = GetSubContentCat($level, $id, $arr, $mode);
            }
        }
    }
    return $arr;
}

function GetSubContentCat2($level, $parentid, $arr, $mode = "1", $cond = "")
{
    $space = "";
    $level++;
    $q = "select iCatID, vName, iParentID, cStatus, cFeatured from category where iParentID=$parentid $cond order by iRank";
    $r = sql_query($q, 'COM.67');

    if (mysql_num_rows($r)) {
        if ($mode == "1") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;&nbsp;";

            while (list($id, $nm, $pid, $stat, $feat) = mysql_fetch_row($r)) {
                $arr[$id] = $space . $nm;
                $arr = GetSubContentCat2($level, $id, $arr, $mode);
            }
        } elseif ($mode == "2") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;&nbsp;";

            for ($i = 1; list($id, $nm, $pid, $stat, $feat) = mysql_fetch_row($r); $i++) {
                $arr[$id] = array("I" => $i, "LEVEL" => $level, "SPACE" => $space, "ID" => $id, "NAME" => $nm, "PARENTID" => $pid, "STATUS" => $stat, "FEATURED" => $feat);
                $arr = GetSubContentCat2($level, $id, $arr, $mode);
            }
        } else if ($mode == "3") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;";

            while (list($id, $nm, $pid, $stat, $feat) = mysql_fetch_row($r)) {
                $pnm = (isset($arr[$pid])) ? trim($arr[$pid]) . ' &gt;&gt; ' : '';
                $arr[$id] = $space . $pnm . $nm;
                $arr = GetSubContentCat2($level, $id, $arr, $mode);
            }
        }
    }
    return $arr;
}

function GetSubContentCatCategory($level, $parentid, $arr, $mode = "1", $cond = "")
{
    $space = "";
    $level++;
    $q = "select iCatID, vE_Name, vG_Name, iParentID, cStatus, cFeatured from category where iParentID=$parentid $cond order by iRank";
    $r = sql_query($q, 'COM.67');

    if (mysql_num_rows($r)) {
        if ($mode == "1") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;&nbsp;";

            while (list($id, $nm, $g_nm, $pid, $stat, $feat) = mysql_fetch_row($r)) {
                $arr[$id] = $space . $nm;
                $arr = GetSubContentCatCategory($level, $id, $arr, $mode);
            }
        } elseif ($mode == "2") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;&nbsp;";

            for ($i = 1; list($id, $nm, $g_nm, $pid, $stat, $feat) = mysql_fetch_row($r); $i++) {
                $arr[$id] = array("I" => $i, "LEVEL" => $level, "SPACE" => $space, "ID" => $id, "NAME" => $nm, "GNAME" => $g_nm, "PARENTID" => $pid, "STATUS" => $stat, "FEATURED" => $feat);
                $arr = GetSubContentCatCategory($level, $id, $arr, $mode);
            }
        } else if ($mode == "3") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;";

            while (list($id, $nm, $g_nm, $pid, $stat, $feat) = mysql_fetch_row($r)) {
                $pnm = (isset($arr[$pid])) ? trim($arr[$pid]) . ' &gt;&gt; ' : '';
                $arr[$id] = $space . $pnm . $nm;
                $arr = GetSubContentCatCategory($level, $id, $arr, $mode);
            }
        }
    }
    return $arr;
}

function GetTableTreeDetails($parent_id, $pk_id, $tbl, $pk_fld)
{
    $ancestorid = $pk_id;
    $level = 0;

    if (!empty($parent_id)) {
        $q = "select iAncestorID, iLevel from $tbl where $pk_fld=$parent_id";
        $r = sql_query($q, 'GL_E.250');
        if (sql_num_rows($r)) {
            list($ancestorid, $level) = sql_fetch_row($r);

            if (empty($ancestorid)) $ancestorid = $pk_id;
            $level++;
        }
    }

    return array($ancestorid, $level);
}

function GetTreeArr($tbl, $pk_fld, $level, $parentid, $arr, $mode = "1", $cond = '', $order = 'vE_Name, iLevel')
{
    $space = "";
    $level++;
    $q = "select * from $tbl where iParentID=$parentid and $pk_fld!=$parentid $cond order by $order";
    $r = sql_query($q, 'COM.400');

    if (sql_num_rows($r)) {
        if ($mode == "1") {
            for ($i = 0; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;";
        } elseif ($mode == "2") {
        }

        while ($a = sql_fetch_assoc($r)) {
            $id = $a[$pk_fld];
            $arr[$id] = $a;
            $arr[$id]['space'] = $space;

            if ($mode == '3')
                $arr[$id] = $id;
            else {
                $arr[$id] = $a;
                $arr[$id]['space'] = $space;
                $arr[$id]['level'] = $level;
            }

            $arr = GetTreeArr($tbl, $pk_fld, $level, $id, $arr, $mode, $cond, $order);
        }
    }

    return $arr;
}

function SortTreeStruct($tbl, $pk_fld)
{
    $arr = array();
    $arr = GetTreeArr($tbl, $pk_fld, -1, 0, $arr);

    $i = 0;
    foreach ($arr as $id => $a) {
        $q = "update $tbl set iRank=" . (++$i) . ", iLevel=" . $a['level'] . " where $pk_fld=$id;";
        $r = sql_query($q, 'COM.704');
    }
}
function SuggestPassword()
{
    $arr = array();

    // length: 6 - 8 chars
    $len = rand(6, 8);
    // echo $len."<br>";

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

    $ctr = $i;

    // atleast 1 symbol
    $symb_arr = array('!', '#', '$', '%', '&', '*', '+', ',', '-', '.', ':', '=', '?', '@', '_', '~');
    $d_len = $len - $a_len - $b_len - $c_len;
    // echo 'd_len: '.$d_len.'<br>';

    for (; $i < ($ctr + $d_len); $i++)
        $arr[$i] = $symb_arr[rand(0, count($symb_arr) - 1)];

    // DFA($arr);
    shuffle($arr);
    // DFA($arr);

    $str = '';
    foreach ($arr as $a)
        $str .= $a;

    return $str;
}

function GetdepartmentTypeFromUserID($uid)
{
    $q = "select cDeptType from users where iUserID='$uid'";
    $rs = sql_query($q) or die("Error in retrieving Department Info");
    list($depttype) = sql_fetch_array($rs);
    return $depttype;
}

function GenerateAplhabetArray($from, $to)
{
    // 1. find difference between $from & $to
    // 2. find immediate next alphabet after $to -> $to_b
    // 3. implementing the difference (1) to $to_b 
    // 4. repeat (3) until Z

    $from = strtoupper($from);
    $to = strtoupper($to);

    $arr = array();
    $from_ord0 = ord($from);
    $to_ord0 = ord($to);

    $from_ord = min($from_ord0, $to_ord0);
    $to_ord = max($from_ord0, $to_ord0);

    $diff = $to_ord - $from_ord;

    $first_ord = ord('A');
    $last_ord = ord('Z');



    if ($from_ord > $first_ord) {
        $tmp_from_ord = $from_ord;
        $tmp_to_ord = $to_ord;

        while ($tmp_to_ord >= $first_ord) {
            if ($tmp_from_ord < $first_ord)
                $tmp_from_ord = $first_ord;

            $arr[$tmp_from_ord] = ($tmp_from_ord == $tmp_to_ord) ? chr($tmp_from_ord) : chr($tmp_from_ord) . ' - ' . chr($tmp_to_ord);

            $tmp_to_ord = $tmp_from_ord - 1;
            $tmp_from_ord = $tmp_to_ord - $diff;
        }
    }

    while ($from_ord <= $last_ord) {
        if ($to_ord > $last_ord)
            $to_ord = $last_ord;

        $arr[$from_ord] = ($from_ord == $to_ord) ? chr($from_ord) : chr($from_ord) . ' - ' . chr($to_ord);

        $from_ord = $to_ord + 1;
        $to_ord = $from_ord + $diff;
    }

    //DFA($arr);

    ksort($arr);

    return $arr;
}
function  GetDePID($cond = "")
{
    $arr = array();
    $q = "select iADEPID,vName from deps_academic ";
    //echo $q;
    $r = sql_query($q) or die("<strong>Error Code: COM1802</strong>");
    while (list($galid, $catid) = sql_fetch_row($r))
        $arr[$galid] = $catid;
    return $arr;
}
function  GetCGID($cond = "")
{
    $arr = array();
    $q = "select iCourseGID,vName from course_group";
    //echo $q;
    $r = sql_query($q) or die("<strong>Error Code: COM1802</strong>");
    while (list($galid, $catid) = sql_fetch_row($r))
        $arr[$galid] = $catid;
    return $arr;
}
function  GetAffliateID($cond = "")
{
    $arr = array();
    $q = "select iAFID,vName from affiliates";
    //echo $q;
    $r = sql_query($q) or die("<strong>Error Code: COM1802</strong>");
    while (list($galid, $catid) = sql_fetch_row($r))
        $arr[$galid] = $catid;
    return $arr;
}
function  GetCourseType($cond = "")
{
    $arr = array();
    $q = "select iCourseTID,vName from course_types ";
    //echo $q;
    $r = sql_query($q) or die("<strong>Error Code: COM1802</strong>");
    while (list($galid, $catid) = sql_fetch_row($r))
        $arr[$galid] = $catid;
    return $arr;
}


function  GetCourse($cond = "")
{
    $arr = array();
    $q = "select vName,iCourseID from course ";
    //echo $q;
    $r = sql_query($q) or die("<strong>Error Code: COM1802</strong>");
    while (list($galid, $catid) = sql_fetch_row($r))
        $arr[$galid] = $catid;
    return $arr;
}
function ShowCounter()
{
    $q = "select iVisitorsCounter from settings";
    $ctr = GetXFromYID($q);
    echo FormatNumber($ctr);
}
function IncrementCounter()
{
    $q = "update settings set iVisitorsCounter=iVisitorsCounter+1";
    sql_query($q);
}
function GetNTIDFromTypeName($name)
{
    $q = "select INTID from news_type where vName='$name'";
    $rs = sql_query($q, "COMMON.2040");
    if (sql_num_rows($rs)) {
        list($id) = sql_fetch_row($rs);
        return $id;
    } else
        return 0;
}
function GetPhotoCatCount()
{
    $arr = array();
    $q = "select iFCID, count(iFGID) from photo_gal1 group by iFCID";
    $r = sql_query($q) or die("<strong>Error Code: MPCD197</strong>");
    while (list($id, $count) = sql_fetch_row($r))
        $arr[$id] = $count;

    return $arr;
}

function GetSubContentCat3($level, $parentid, $arr, $mode = "1", $cond = "")
{
    $space = "";
    $level++;
    $q = "select iCCatID, vName, iParentID, cStatus, cLinkYN, vLink from content_cat where iParentID=$parentid $cond order by iParentID, iRank";
    $r = sql_query($q, 'COM.67');

    if (sql_num_rows($r)) {
        if ($mode == "1") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;&nbsp;";

            while (list($id, $nm, $pid, $stat, $linkYN, $link) = sql_fetch_row($r)) {
                $arr[$id] = $space . $nm;
                $arr = GetSubContentCat3($level, $id, $arr, $mode);
            }
        } elseif ($mode == "2") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;&nbsp;";

            for ($i = 1; list($id, $nm, $pid, $stat, $linkYN, $link) = sql_fetch_row($r); $i++) {
                $arr[$id] = array("I" => $i, "LEVEL" => $level, "SPACE" => $space, "ID" => $id, "NAME" => $nm, "PARENTID" => $pid, "STATUS" => $stat, "LINKYN" => $linkYN, "LINK" => $link);
                $arr = GetSubContentCat3($level, $id, $arr, $mode);
            }
        } else if ($mode == "3") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;";

            while (list($id, $nm, $pid, $stat, $linkYN, $link) = sql_fetch_row($r)) {
                $pnm = (isset($arr[$pid])) ? trim($arr[$pid]) . ' &gt;&gt; ' : '';
                $arr[$id] = $space . $pnm . $nm;
                $arr = GetSubContentCat3($level, $id, $arr, $mode);
            }
        }
    }
    return $arr;
}

function GetContentCat($level, $parentid, $arr, $mode = "1", $cond = "")
{
    $space = "";
    $level++;
    $q = "select iCCatID, vName, iParentID, cStatus from content_cat where iParentID=$parentid $cond order by iRank";
    $r = sql_query($q, 'COM.67');

    if (sql_num_rows($r)) {
        if ($mode == "1") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;";

            while (list($id, $nm, $pid, $stat) = sql_fetch_row($r)) {
                $arr[$id] = $space . $nm;
                $arr = GetContentCat($level, $id, $arr, $mode);
            }
        } elseif ($mode == "2") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;&nbsp;";

            for ($i = 1; list($id, $nm, $pid, $stat) = sql_fetch_row($r); $i++) {
                $arr[$id] = array("I" => $i, "LEVEL" => $level, "SPACE" => $space, "ID" => $id, "NAME" => $nm, "PARENTID" => $pid, "STATUS" => $stat);
                $arr = GetContentCat($level, $id, $arr, $mode);
            }
        } else if ($mode == "3") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;";

            while (list($id, $nm, $pid, $stat) = sql_fetch_row($r)) {
                $pnm = (isset($arr[$pid])) ? trim($arr[$pid]) . ' &gt;&gt; ' : '';
                $arr[$id] = $space . $pnm . $nm;
                $arr = GetContentCat($level, $id, $arr, $mode);
            }
        }
    }
    return $arr;
}
function CutMe($text, $length)
{
    if (strlen($text) > $length) {
        $text = substr($text, 0, strpos($text, ' ', $length));
    }
    return $text;
}

function GetMenuCat($level = "", $parentid = "", $arr = "", $mode = "1", $LEVEL = 1, $catARR = "", $artARR = "", $facARR = "", $leadARR = "", $ctARR = "", $nwARR = "", $afARR = "", $depARR = "", $facuARR = "")
{
    $space = $cond = "";
    $level++;

    $q = "select iMMID, iRefID, cRefType, cModule, vAltName, iParentID, cStatus, iLevel from menu_manager where iParentID=$parentid $cond order by iRank";
    $r = sql_query($q, 'COM.67');

    if (sql_num_rows($r)) {
        if ($mode == "1") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;";

            while (list($id, $_refid, $_reftype, $_module, $_altname, $pid, $stat, $lvl) = sql_fetch_row($r)) {
                if ($lvl >= $LEVEL)
                    continue;

                $nm = '';
                if ($_module == 'CMS' && $_reftype == 'CAT' && !empty($_refid))
                    $nm = $catARR[$_refid];
                elseif ($_module == 'CMS' && $_reftype == 'ART' && !empty($_refid))
                    $nm = $artARR[$_refid];
                elseif ($_module == 'FAC' && !empty($_refid))
                    $nm = $facARR[$_refid];
                elseif ($_module == 'LEAD' && !empty($_refid))
                    $nm = $leadARR[$_refid];
                elseif ($_module == 'ACAD' && !empty($_refid))
                    $nm = $ctARR[$_refid];
                elseif ($_module == 'NEWS' && !empty($_refid))
                    $nm = $nwARR[$_refid];
                elseif ($_module == 'AFF' && !empty($_refid))
                    $nm = $afARR[$_refid];
                elseif ($_module == 'DEP' && !empty($_refid))
                    $nm = $depARR[$_refid];
                elseif ($_module == 'FACU' && !empty($_refid))
                    $nm = $facuARR[$_refid];
                elseif (!empty($_altname))
                    $nm = $_altname;

                $arr[$id] = $space . $nm;
                $arr = GetMenuCat($level, $id, $arr, $mode, $LEVEL, $catARR, $artARR, $facARR, $leadARR, $ctARR, $nwARR, $afARR, $depARR, $facuARR);
            }
        } elseif ($mode == "2") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;&nbsp;";

            for ($i = 1; list($id, $_refid, $_reftype, $_module, $_altname, $pid, $stat, $lvl) = sql_fetch_row($r); $i++) {
                if ($lvl >= $LEVEL)
                    continue;

                $nm = '';
                if ($_module == 'CMS' && $_reftype == 'CAT' && !empty($_refid))
                    $nm = $catARR[$_refid];
                elseif ($_module == 'CMS' && $_reftype == 'ART' && !empty($_refid))
                    $nm = $artARR[$_refid];
                elseif ($_module == 'FAC' && !empty($_refid))
                    $nm = $facARR[$_refid];
                elseif ($_module == 'LEAD' && !empty($_refid))
                    $nm = $leadARR[$_refid];
                elseif ($_module == 'ACAD' && !empty($_refid))
                    $nm = $ctARR[$_refid];
                elseif ($_module == 'NEWS' && !empty($_refid))
                    $nm = $nwARR[$_refid];
                elseif ($_module == 'AFF' && !empty($_refid))
                    $nm = $afARR[$_refid];
                elseif ($_module == 'DEP' && !empty($_refid))
                    $nm = $depARR[$_refid];
                elseif ($_module == 'FACU' && !empty($_refid))
                    $nm = $facuARR[$_refid];
                elseif (!empty($_altname))
                    $nm = $_altname;

                $arr[$id] = array("I" => $i, "LEVEL" => $level, "SPACE" => $space, "ID" => $id, "NAME" => $nm, "PARENTID" => $pid, "STATUS" => $stat);
                $arr = GetMenuCat($level, $id, $arr, $mode, $LEVEL, $catARR, $artARR, $facARR, $leadARR, $ctARR, $nwARR, $afARR, $depARR, $facuARR);
            }
        } else if ($mode == "3") {
            for ($i = 1; $i < $level; $i++)
                $space .= "&nbsp;&nbsp;";

            while (list($id, $_refid, $_reftype, $_module, $_altname, $pid, $stat, $lvl) = sql_fetch_row($r)) {
                if ($lvl >= $LEVEL)
                    continue;

                $nm = '';
                if ($_module == 'CMS' && $_reftype == 'CAT' && !empty($_refid))
                    $nm = $catARR[$_refid];
                elseif ($_module == 'CMS' && $_reftype == 'ART' && !empty($_refid))
                    $nm = $artARR[$_refid];
                elseif ($_module == 'FAC' && !empty($_refid))
                    $nm = $facARR[$_refid];
                elseif ($_module == 'LEAD' && !empty($_refid))
                    $nm = $leadARR[$_refid];
                elseif ($_module == 'ACAD' && !empty($_refid))
                    $nm = $ctARR[$_refid];
                elseif ($_module == 'NEWS' && !empty($_refid))
                    $nm = $nwARR[$_refid];
                elseif ($_module == 'AFF' && !empty($_refid))
                    $nm = $afARR[$_refid];
                elseif ($_module == 'DEP' && !empty($_refid))
                    $nm = $depARR[$_refid];
                elseif ($_module == 'FACU' && !empty($_refid))
                    $nm = $facuARR[$_refid];
                elseif (!empty($_altname))
                    $nm = $_altname;

                $pnm = (isset($arr[$pid])) ? trim($arr[$pid]) . ' &gt;&gt; ' : '';
                $arr[$id] = $space . $pnm . $nm;
                $arr = GetMenuCat($level, $id, $arr, $mode, $LEVEL, $catARR, $artARR, $facARR, $leadARR, $ctARR, $nwARR, $afARR, $depARR, $facuARR);
            }
        }
    }
    return $arr;
}

function GetCalendarDat($cond = " and s.dSession >= CURRENT_DATE", $order = " order by s.dSession ASC")
{

    $arr = array();

    $q = "select s.iSessionID, s.vTitle, s.vSynapsis, s.vImg, s.dSession, s.tFrom, s.tTo, f.vName as facilitator, se.vName as series from session s join session_facilitator_assoc sfa on s.iSessionID = sfa.iSessionID join facilitator f on sfa.iFacilitatorID = f.iFacilitatorID join series se on s.iSeriesID = se.iSeriesID where 1 $cond and s.cStatus = 'A' $order";
    $r = sql_query($q, "common.inc.2287");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_object($r)) {

            $arr[] = array("session_id" => $row->iSessionID, "session_name" => $row->vTitle, "session_desc" => $row->vSynapsis, "session_img" => $row->vImg, "session_date" => $row->dSession, "session_from" => $row->tFrom, "session_to" => $row->tTo, "facilitator_name" => $row->facilitator, "series_name" => $row->series);
        }
    }

    return $arr;
}

function GenerateB2BPwd($email, $phone)
{

    $email_arr = explode("@", $email);

    $last4 = substr($phone, -4);

    $pwd = $email_arr[0] . $last4;

    return md5($pwd);
}

function GenerateSubscriptionsB2B($member_id, $client_id)
{

    $q = "select cListingType, dListingExpiry from client where iClientID = '$client_id'";
    $r = sql_query($q, "common.inc.2319");
    $type = $expiry = '';
    if (sql_num_rows($r)) {

        list($type, $expiry) = sql_fetch_row($r);
        $subscription = NextID("iSubscriptionID", "subscription");
        $q1 = "insert into subscription values('$subscription', 'B2B_" . $member_id . "', '$member_id', '$expiry', NOW(), 'A')";
        $r1 = sql_query($q1, "common.inc.2325");
    }
}

function SendWelcomeMailTo($member_id, $type, $password = false)
{
    $q = "select vName, vEmailID, vMobileNum, iMemberID from member where iMemberID = '$member_id' and iMCatID = '$type'";
    $r = sql_query($q, "cron_send_welcome_message_b2b.5");

    if (sql_num_rows($r)) {

        $row = sql_fetch_object($r);

        $email_arr = explode("@", $row->vEmailID);
        $last4 = substr($row->vMobileNum, -4);
        $pwd = $email_arr[0] . $last4;

        $split_name = explode(" ", $row->vName);

        $subject = "Hi " . $split_name[0] . ", welcome to Work Better Club! Here are your membership details.";

        $str = "";

        $str .= "<p>Hi " . $split_name[0] . ",</p>";
        $days = '';
        if ($type == 1) {
            $days = '15-day';
        } else if ($type == 2) {
            $days = '1-month';
        } else if ($type == 3) {
            $days = '7-day';
        }

        $str .= "<p>Thank you for signing up for a " . $days . " trial with the Work Better Club! We are excited to have you
        as a part of our network of over 10 lakh professionals who have trained with us over the last 12
        years.</p>";

        $str .= "<p>Work Better Club (WBC) is an exclusive virtual club for professionals that will help you transform
        your career. Launched by Work Better Training – India’s no. 1 executive training company – the Club
        will help you develop critical soft skills, essential attitudes and behaviours, while keeping you
        motivated through this learning journey.</p>";

        $str .= "<p>As part of your trial membership, you will have access to:</p>";

        $str .= "<ul>";
        $str .= "<li><b>Six Live Online Masterclasses:</b> Each masterclass will cover a different soft skills and behaviour related topic that is critical to workplace success.
        </li>";
        $str .= "<li><b>One Online Motivational Session:</b> The aim of these seminars is to help participants learn how to become and stay motivated in the long run.</li>";
        $str .= "<li><b>One Group Coaching Call:</b> The group coaching sessions will involve Q&As, group discussions and short coaching sessions on topics put forward by members present on the call.</li>";
        $str .= "<li><b>Daily Email Insights:</b> Bite-sized tips that participants can apply to their work lives immediately. These will be sent to them over email from Monday to Friday.</li>";
        $str .= "</ul>";

        $str .= "<p>The sessions above will be held online on <b>Saturdays from 6.00 PM to 7.30 PM</b> and <b>Sundays from 10.00 AM to 11.30 AM</b> over Zoom. Attached is the program calendar.</p>";

        $str .= "<p>You will receive an email with the topic and details of the program every Wednesday along with Zoom links to access the scheduled sessions for the week. You will also receive a reminder over Whatsapp every Friday.</p>";

        $str .= "<p>Please note that you will not have access to our world-class <b>content library</b> and <b>members’ dashboard</b> and during the trial period.</p>";

        $str .= "<p>We assure you that your time with WBC is going to be a game-changer for your professional development and career success. After all, your success is our mission!</p>";
        if ($password == true) {
            $str .= "<p>You can login with the following password: " . $pwd . "</p>";
        }

        $str .= "<p>Let the learning begin!</p>";

        $str .= "<p>- Work Better Club</p>";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, 'api:key-d2547dd576f5f5de8411b1432dfa6758');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt(
            $ch,
            CURLOPT_URL,
            'https://api.mailgun.net/v3/mg.workbetterclub.com/messages'
        );
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            array(
                'from' => 'Work Better Club <no-reply@workbetterclub.com>',
                'to' => $row->vName . '<' . $row->vEmailID . '>',
                'subject' => $subject,
                'html' => $str
            )
        );
        $result = curl_exec($ch);
        curl_close($ch);
        if ($result) {

            $qm = "insert into log_mail(iMemberID, cType, dtLog, iUID, cStatus)values('$row->iMemberID', 'WEL', '0', NOW(), '0', 'A')";
            $rm = sql_query($qm, "common.inc.2412");
        }
    }
}

function GetSessions($type = '', $limit = '', $category = '', $order = 's.dSession', $exclude = '', $series = '')
{

    $cond = '';
    $arr = array();
    $today = date('Y-m-d');
    if (!empty($type)) {
        if ($type == 'new') {
            $cond .= " and s.dSession > '$today'";
        } else if ($type == 'old') {
            $cond .= " and s.dSession < '$today'";
        }
    }

    if (!empty($category)) {
        $cond .= " and s.cType = '" . $category . "'";
    }

    if (!empty($exclude)) {
        $cond .= " and s.iSessionID <> '$exclude'";
    }
    //echo $series;
    if (!empty($series)) {

        $parent = GetXFromYID("select iParentID from series where iSeriesID = '$series'");
        if ($parent != 0) {

            $idsarr = GetXArrFromYID("select iSeriesID, iSeriesID from series where iParentID = '$parent'");
            $ids = implode(",", $idsarr);

            $cond .=  " and s.iSeriesID IN ($ids)";
        } else {
            $cond .=  " and s.iSeriesID = '$series'";
        }
    }

    $q = "select s.iSessionID, se.iSeriesID, s.vTitle, se.vName, s.tFrom, s.tTo, s.dSession, s.vImg, s.vThumb, s.iZoomID, s.vSynapsis from session s join series se on s.iSeriesID = se.iSeriesID where s.cStatus = 'A' $cond order by " . $order . " LIMIT " . $limit . "";
    $r = sql_query($q, "common.inc.2436");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_object($r)) {

            $arr[] = array("session_id" => $row->iSessionID, "series_id" => $row->iSeriesID, "session_title" => $row->vTitle, "series_name" => $row->vName, "session_start" => $row->tFrom, "session_end" => $row->tTo, "session_date" => $row->dSession, "session_image" => $row->vImg, "session_zoom" => $row->iZoomID, "session_thumb" => $row->vThumb, "session_desc" => $row->vSynapsis);
        }
    }
    return $arr;
}

function GetSessionInfo($id)
{

    $q = "select vTitle, vSynapsis, vImg, dSession, tFrom, tTo, iZoomID, vJoinURL, iSeriesID from session where cStatus = 'A' and iSessionID = '$id'";
    $r = sql_query($q, "common.inc.2455");
    $arr = array();
    if (sql_num_rows($r)) {

        list($title, $desc, $image, $date, $from, $to, $zoom_id, $url, $series) = sql_fetch_row($r);

        $arr = array("title" => $title, "description" => $desc, "image" => $image, "date" => $date, "from" => $from, "to" => $to, "zoom_id" => $zoom_id, "url" => $url, "series" => $series);
    }

    return $arr;
}

function GetSessionResources($id, $type = '', $exclude = '')
{
    $q = "select iRrefID from sess_res_assoc where iSessionID = '$id' and cRefType = '$type' and cStatus = 'A' and iRrefID <> '$exclude' order by iRank";
    $r = sql_query($q, "common.inc.2471");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_object($r)) {

            $arr[] = $row->iRrefID;
        }
    }

    return implode(",", $arr);
}

function GetResourceInfo($data, $table, $pk)
{

    $q = "select * from " . $table . " where " . $pk . " IN ($data) and cStatus = 'A' order by iRank";
    $r = sql_query($q, "common.inc.2489");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}
function GetVideoProgress($session_id)
{
    $member_id = (!empty($_SESSION[PROJ_SESSION_ID]->user_id)) ? $_SESSION[PROJ_SESSION_ID]->user_id : 0;
    $session_time = GetXFromYID("select iDuration from res_session_dat where iResSessionDatID = '$session_id'");
    $parent_id = GetXFromYID("select iSessionID from res_session_dat where iResSessionDatID = '$session_id'");
    $percentage = 0;
    $session_utilized = GetXFromYID("select iBookmark_seconds from vimeo_utilization where iResSessionDatID = '$session_id' and iMemberID = '$member_id' and iSessionID = '$parent_id'");

    if (!empty($session_utilized) && $session_utilized != 0) {
        $percentage = ($session_utilized / $session_time) * 100;
    }

    return $percentage;
}

function GetAttendedMembers($session)
{

    $count = GetXFromYID("select count(*) from attendance where cAttended = 'Y' and iSessionID = '$session'");

    return $count;
}

function GetSessionsFromSeries($series)
{

    $sessions = GetXArrFromYID("select iSessionID, vTitle from session where iSeriesID = '$series' and cStatus = 'A' and dSession < '2021-06-01' group by vTitle order by iSessionID", 3);

    return $sessions;
}

function GetSeries($parent = 0)
{
    $array = array();
    $q = "select iSeriesID, vName, vDesc from series where cStatus = 'A' and iParentID = $parent order by iRank";
    $r = sql_query($q, "common.inc.2537");
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_object($r)) {

            $array[] = array("series_id" => $row->iSeriesID, "series_name" => $row->vName, "series_desc" => $row->vDesc);
        }
    }

    return $array;
}

function GetCurateDetails($id)
{
    $q = "select vName, cType, vDesc, vImg, vVideoUrl, vReference from res_curates where cStatus = 'A' and iResCuID = '$id'";
    $r = sql_query($q);
    $arr = array();
    if (sql_num_rows($r)) {
        list($name, $type, $desc, $img, $video, $reference) = sql_fetch_row($r);

        $arr = array("name" => $name, "type" => $type, "desc" => $desc, "img" => $img, "video" => $video, 'reference' => $reference);
    }

    return $arr;
}

function GetFAQDetails($id)
{
    $q = "select vName, cType, vDesc, vImg, vVideoUrl from res_faq where cStatus = 'A' and iResFAQID = '$id'";
    $r = sql_query($q);
    $arr = array();
    if (sql_num_rows($r)) {
        list($name, $type, $desc, $img, $video) = sql_fetch_row($r);

        $arr = array("name" => $name, "type" => $type, "desc" => $desc, "img" => $img, "video" => $video);
    }

    return $arr;
}

function SendOTP($mobile)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "http://control.msg91.com/api/v5/otp?authkey=333249AhzernnQK25ef053e3P1&sender=WBCLUB&template_id=5efc8ba8d6fc05643f359803&mobiles=" . $mobile . "",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "Cookie: PHPSESSID=isunk5joq1tfsiq1jc5f66onj3"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}

function VerifyOTP($mobile, $otp)
{
    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.msg91.com/api/v5/otp/verify?otp=" . $otp . "&authkey=333249AhzernnQK25ef053e3P1&mobile=$mobile",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_HTTPHEADER => array(
            "Cookie: PHPSESSID=dfd5f617hpaviog861dj4ld391"
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    return $response;
}
function GetYoutubeData($playlistId, $nxtPageToken = '')
{
    $API_key = 'AIzaSyCW24J83y_oGYqw59R5RXyh-__v5qQHVjk';

    $p_token = '';
    if (!empty($nxtPageToken))
        $p_token = '&pageToken=' . $nxtPageToken;

    $video_list = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/playlistItems?order=date&part=snippet' . $p_token . '&maxResults=50&playlistId=' . $playlistId . '&key=' . $API_key));

    return $video_list;
}

function GetSelfPacedAttendance($session_id)
{
    $response - 0;
    $q = "select COUNT(DISTINCT iMemberID) from vimeo_log where iSessionID = " . $session_id . " group by iMemberID";
    $r = sql_query($q, "common.inc.2641");

    if (sql_num_rows($r)) {

        list($response) = sql_fetch_row($r);
    }

    return $response;
}

function GetFAQS($session = '', $exclude = '')
{
    $arr = array();
    $cond = $join = '';
    if (!empty($session)) {
        $join .= " join sess_res_assoc a on f.iResFAQID = a.iRefID";
        $cond .= " and a.iSessionID = '$session'";
    }
    if (!empty($exclude)) {
        $cond .= " and a.iSessionID <> '$exclude'";
    }
    $q = "select f.* from res_faq f $join where 1 $cond";
    $r = sql_query($q, "common.inc.2552");

    if (sql_num_rows($r)) {
        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function GetCurates($session = '', $exclude = '')
{
    $arr = array();
    $cond = $join = '';
    if (!empty($session)) {
        $join .= " join sess_res_assoc a on c.iResCuID = a.iRefID";
        $cond .= " and a.iSessionID = '$session'";
    }
    if (!empty($exclude)) {
        $cond .= " and a.iSessionID <> '$exclude'";
    }
    $q = "select c.* from res_curates c $join where 1 $cond";
    $r = sql_query($q, "common.inc.2552");

    if (sql_num_rows($r)) {
        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function GetInProcessData($sess_user_id)
{
    $arr = array();
    $q = "select DISTINCT(iSessionID) from vimeo_log where iMemberID = '$sess_user_id' and iSessionID <> '0' and iResSessionDatID <> '0'";
    $r = sql_query($q, "common.2661");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_object($r)) {

            $arr[] = array("session_id" => $row->iSessionID, "session_name" => GetXFromYID("select vTitle from session where iSessionID = '$row->iSessionID'"), "session_series" => GetXFromYID("select iSeriesID from session where iSessionID = '$row->iSessionID'"), "session_thumb" => GetXFromYID("select vThumb from session where iSessionID = '$row->iSessionID'"));
        }
    }

    return $arr;
}

function GetSessionProgress($sess_user_id, $session_id)
{

    $dat = GetSessionResources($session_id, 'RSD');

    $completed = GetXFromYID("select count(distinct(iResSessionDatID)) from vimeo_log where cFlag_Completion = 'Y' and iMemberID = '$sess_user_id' and iSessionID = '$session_id'");

    $explode_dat = explode(",", $dat);
    $total = sizeof($explode_dat);
    $perc = 0;
    $perc = ($completed / $total) * 100;
    if ($perc > 100) {
        $perc = 100;
    }

    return $perc;
}

/*    function GetLatestVideo($sess_user_id, $session_id){
        $response = GetXFromYID("select iRrefID from sess_res_assoc where 1 and iSessionID = '$session_id' and cRefType = 'RSD' order by iRank");
        $count = GetXFromYID("select count(iRrefID) from sess_res_assoc where 1 and iSessionID = '$session_id' and cRefType = 'RSD'");
        //echo "select iResSessionDatID from vimeo_utilization where cFlag_Completion = 'N' and iMemberID = '$sess_user_id' and iSessionID = '$session_id' order by dtLastAccess DESC";
        $last_watched = GetXFromYID("select iResSessionDatID from vimeo_utilization where cFlag_Completion = 'N' and iMemberID = '$sess_user_id' and iSessionID = '$session_id' order by dtLastAccess DESC");
        if(!empty($last_watched)){
          $response = $last_watched;
        } else {
            //echo "select MAX(iResSessionDatID) from vimeo_log where cFlag_Completion = 'Y' and iMemberID = '$sess_user_id' and iSessionID = '$session_id' order by iSubsLogID DESC";
            $last_completed = GetXFromYID("select MAX(iResSessionDatID) from vimeo_log where cFlag_Completion = 'Y' and iMemberID = '$sess_user_id' and iSessionID = '$session_id' order by iSubsLogID DESC");
            if(!empty($last_completed) && $last_completed < $count){
            $response = GetXFromYID("select iRrefID from sess_res_assoc where cStatus = 'A' and iSessionID = '$session_id' and cRefType = 'RSD' and iRrefID > '$last_completed'");            
            }
        }
        
        return $response;
        
        
    }*/

function GetLatestVideo($sess_user_id, $session_id)
{
    $response = GetXFromYID("select iRrefID from sess_res_assoc where 1 and iSessionID = '$session_id' and cRefType = 'RSD' order by iRank");
    $count = GetXFromYID("select count(iRrefID) from sess_res_assoc where 1 and iSessionID = '$session_id' and cRefType = 'RSD'");
    $last_watched = GetXFromYID("select iResSessionDatID from vimeo_log where cFlag_Completion = 'N' and iMemberID = '$sess_user_id' and iSessionID = '$session_id' order by dtStart DESC");

    if (!empty($last_watched)) {
        $response = $last_watched;
    } else {
        //echo "select MAX(iResSessionDatID) from vimeo_log where cFlag_Completion = 'Y' and iMemberID = '$sess_user_id' and iSessionID = '$session_id' order by iSubsLogID DESC";
        $last_completed = GetXFromYID("select MAX(iResSessionDatID) from vimeo_log where cFlag_Completion = 'Y' and iMemberID = '$sess_user_id' and iSessionID = '$session_id' order by iSubsLogID DESC");
        if (!empty($last_completed) && $last_completed < $count) {
            $response = GetXFromYID("select sra.iRrefID from sess_res_assoc sra join res_session_dat srd on sra.iRrefID = srd.iResSessionDatID where srd.cStatus = 'A' and sra.iSessionID = '$session_id' and sra.cRefType = 'RSD' and iRrefID > '$last_completed' order by srd.iRank");
        }
    }

    return $response;
}

function GetArrForBookmark($sess_user_id, $type = 'SES')
{
    $arr = array();
    $q = "select iRefID from member_bookmark_assoc where cRefType = '$type' and iMemberID = '$sess_user_id'";
    $r = sql_query($q, "common.inc.2767");

    if (sql_num_rows($r)) {
        while ($row = sql_fetch_object($r)) {

            $arr[] = $row->iRefID;
        }
    }

    return $arr;
}

function GetSavedSessions($sess_user_id)
{
    $arr = array();
    $q = "select s.iSessionID, s.vTitle, s.iSeriesID, s.vThumb from session s join member_bookmark_assoc mba on s.iSessionID = mba.iRefID where mba.cRefType='SES' and mba.iMemberID = '$sess_user_id'";
    $r = sql_query($q, "common.inc.2784");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_object($r)) {

            $arr[] = array("session_id" => $row->iSessionID, "session_title" => $row->vTitle, "session_series" => $row->iSeriesID, "session_thumb" => $row->vThumb);
        }
    }

    return $arr;
}

function GetAttendedSessions($sess_user_id)
{
    $arr = array();
    $q = "select iSessionID from attendance where cAttended = 'Y' and iMemberID = '$sess_user_id'";
    $r = sql_query($q, "common.inc.2823");

    if (sql_num_rows($r)) {
        while ($row = sql_fetch_object($r)) {
            $arr[] = $row->iSessionID;
        }
    }

    return $arr;
}

function GetSkills($sess_user_id)
{

    $skills = GetXArrFromYID("select iSkillID, vName from skills where cStatus = 'A' and iSkillID NOT IN (select iSkillID from skill_member_assoc where iMemberID = '$sess_user_id') order by iRank", 3);
    return $skills;
}

function GetSkillsForMember($sess_user_id)
{
    $arr = array();
    $q = "select sma.iSkillID, s.vName from skill_member_assoc sma join skills s on sma.iSkillID = s.iSkillID where iMemberID = '$sess_user_id' group by iSkillID";
    $r = sql_query($q, "common.inc.2845");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_object($r)) {

            $arr[] = array("id" => $row->iSkillID, "name" => $row->vName);
        }
    }

    return $arr;
}

function GetMessages($sess_user_id)
{
    $arr = array();
    $q = "select n.vNotes, n.dtNotify, n.iNotificationID, nd.cReadStatus from comm_notify n join comm_notify_dat nd on n.iNotificationID = nd.iNotificationID where nd.iMemberID = '$sess_user_id' and n.cStatus = 'A' order by n.dtNotify DESC";
    $r = sql_query($q, "common.inc.2864");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {
            $arr[] = $row;
        }
    }

    return $arr;
}

function GetProfileProgress($sess_user_id)
{
    $response = 0;
    $pic = GetXFromYID("select vPic from member where iMemberID = '$sess_user_id'");
    $q = "select vCity, vJobTitle, dDOB, vCompany from member_prof_info where iMemberID = '$sess_user_id'";
    $r = sql_query($q, "common.inc.2882");
    $job = $city = '';
    $dob = $company = '';
    if (sql_num_rows($r)) {

        list($city, $job, $dob, $company) = sql_fetch_row($r);
    }

    if (!empty($pic) && !empty($job) && !empty($city) && !empty($dob) && !empty($company)) {

        $response = 100;
    } else if (empty($pic) && empty($job) && empty($city) && empty($dob) && empty($company)) {
        $response = 50;
    } else if (empty($pic) && !empty($job) && !empty($city) && !empty($dob) && !empty($company)) {
        $response = 90;
    } else if (!empty($pic) && empty($job) && !empty($city) && !empty($dob) && !empty($company)) {
        $response = 90;
    } else if (!empty($pic) && !empty($job) && empty($city) && !empty($dob) && !empty($company)) {
        $response = 90;
    } else if (!empty($pic) && !empty($job) && !empty($city) && empty($dob) && !empty($company)) {
        $response = 90;
    } else if (!empty($pic) && !empty($job) && !empty($city) && !empty($dob) && empty($company)) {
        $response = 90;
    } else if (!empty($pic) && empty($job) && empty($city) && empty($dob) && empty($company)) {
        $response = 60;
    } else if (empty($pic) && !empty($job) && empty($city) && empty($dob) && empty($company)) {
        $response = 60;
    } else if (empty($pic) && empty($job) && !empty($city) && empty($dob) && empty($company)) {
        $response = 60;
    } else if (empty($pic) && empty($job) && empty($city) && !empty($dob) && empty($company)) {
        $response = 60;
    } else if (empty($pic) && empty($job) && empty($city) && empty($dob) && !empty($company)) {
        $response = 60;
    } else if (!empty($pic) && !empty($job) && empty($city) && empty($dob) && empty($company)) {
        $response = 70;
    } else if (empty($pic) && !empty($job) && !empty($city) && empty($dob) && empty($company)) {
        $response = 70;
    } else if (empty($pic) && empty($job) && !empty($city) && !empty($dob) && empty($company)) {
        $response = 70;
    } else if (empty($pic) && empty($job) && empty($city) && !empty($dob) && !empty($company)) {
        $response = 70;
    } else if (!empty($pic) && empty($job) && empty($city) && empty($dob) && !empty($company)) {
        $response = 70;
    } else if (!empty($pic) && !empty($job) && !empty($city) && empty($dob) && empty($company)) {
        $response = 80;
    } else if (empty($pic) && !empty($job) && !empty($city) && !empty($dob) && empty($company)) {
        $response = 80;
    } else if (empty($pic) && empty($job) && !empty($city) && !empty($dob) && !empty($company)) {
        $response = 80;
    } else if (!empty($pic) && empty($job) && empty($city) && !empty($dob) && !empty($company)) {
        $response = 80;
    } else if (!empty($pic) && !empty($job) && empty($city) && empty($dob) && !empty($company)) {
        $response = 80;
    }

    return $response;
}

function GetAssessmentQuestions($session)
{
    $arr = array();
    $q = "select iQuesID, vQuestion, iWeightage from assmt_question where cStatus = 'A' and iSessionID = '$session' order by iRank";
    $r = sql_query($q, "common.inc.2917");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function GetAssessmentAnswers($question_id)
{
    $arr = array();
    $q = "select iAnsID, vAnswer from assmt_answer where iQuesID = '$question_id' order by iRank";
    $r = sql_query($q, "common.inc.2936");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function GetVideoThumb($link)
{

    $urlParts = explode("/", parse_url($link, PHP_URL_PATH));
    $videoId = (int)$urlParts[count($urlParts) - 1];

    $data = file_get_contents("http://vimeo.com/api/v2/video/$videoId.json");
    $data = json_decode($data);
    return $data[0]->thumbnail_medium;
}

function GetAverageRating($session)
{
    $response = 0;
    $q = "select count(*) as count, sum(fRating) as sum from reviews where iSessionID = '$session' and cStatus = 'A'";
    $r = sql_query($q, "common.inc.3023");

    if (sql_num_rows($r)) {

        list($count, $sum) = sql_fetch_row($r);

        if ($count != 0) {
            $response = $sum / $count;
        }
    }

    return $response;
}

function GetSessionRating($session)
{
    $response = 0;
    $q = "select fRating from session_rating_assoc where iSessionID = '$session' and cStatus = 'A'";
    $r = sql_query($q, "common.inc.3023");

    if (sql_num_rows($r)) {

        list($rating) = sql_fetch_row($r);

        if ($rating != 0) {
            $response = $rating;
        }
    }

    return $response;
}

function GetSearchedSessions($keyword = "", $type = '', $order = "")
{
    $arr = array();
    $q = "select s.* from session s join series se on s.iSeriesID = se.iSeriesID where (s.vTitle LIKE '%$keyword%' or s.vSynapsis LIKE '%$keyword%' or s.vNotes LIKE '%$keyword%' or se.vName LIKE '%$keyword%') and s.cType = '$type' order by $order";
    $r = sql_query($q, "common.inc.3040");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function GetSearchedFAQ($keyword, $order = '')
{
    $arr = array();
    $q = "select * from res_faq where (vName LIKE '%$keyword%' or vDesc LIKE '%$keyword%') order by $order";
    $r = sql_query($q, "common.inc.3040");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}
function GetSearchedCurates($keyword, $order = '')
{
    $arr = array();
    $q = "select * from res_curates where (vName LIKE '%$keyword%' or vDesc LIKE '%$keyword%') order by $order";
    $r = sql_query($q, "common.inc.3040");

    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function GetTestimonialsForSession($area = '', $session = '')
{
    $cond = '';
    if (!empty($area)) {
        if ($area == 'H') {
            $cond .= " and r.cFeatured_Home = 'Y'";
        } else if ($area == 'S') {
            $cond .= " and r.cFeatured_Session = 'Y'";
        }
    }
    if (!empty($session)) {
        $cond .= " and r.iSessionID = '$session'";
    }
    $q = "select r.iMemberID, r.iRating, r.vComments, m.vName, m.vPic from reviews r join member m on r.iMemberID = m.iMemberID where 1 $cond order by r.iRank";
    $r = sql_query($q);
    $arr = array();
    if (sql_num_rows($r)) {
        while ($row = sql_fetch_assoc($r)) {
            $arr[] = $row;
        }
    }
    return $arr;
}

function GetBannersForSection($type = "", $limit = 1)
{
    $cond = '';

    if (!empty($type)) {
        $cond .= " and cType = '$type'";
    }
    $today = date('Y-m-d H:i:s');
    $q = "select * from adv_banner where cStatus = 'A' and ('$today' between dtStart and dtEnd) $cond order by iBannerID DESC LIMIT $limit";
    $r = sql_query($q, "common.inc.3038");

    $arr = array();
    if (sql_num_rows($r)) {
        while ($row = sql_fetch_assoc($r)) {
            $arr[] = $row;
        }
    }
    return $arr;
}


function GenerateUniqueAssessmentLink($member_id = "", $ref_type = "ASSESSMENT", $session_id = "")
{
    //include 'ti-salt.php';

    $link = "assessment-request.php?uid=";
    $encoded = strval($member_id . 'WBC' . $ref_type . 'WBC' . $session_id);
    //$salt = new SaltIT;
    return $link . base64_encode($encoded);
}

function TotalSessionDatTime($session)
{
    $sum = GetXFromYID("select sum(iDuration) from res_session_dat where iSessionID = '$session'");

    $min = intval($sum / 60);
    return $min; //. ':' . str_pad(($sum % 60), 2, '0', STR_PAD_LEFT);
}

//DATA POINTS

function GetBookmarkCount($member_id, $type = 'SES')
{ //#6

    $q = "select * from session s join member_bookmark_assoc mba on s.iSessionID = mba.iRefID where mba.cRefType = '$type' and iMemberID = '$member_id'";
    $r = sql_query($q, "common.inc.3163");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }
    return $arr;
}

function GetInProgressSelfPaced($member_id)
{ //#5

    $q = "select distinct(iResSessionDatID), iSessionID from vimeo_log where iMemberID = '$member_id' and cFlag_Completion = 'Y' order by dtStart";
    $r = sql_query($q, "common.inc.3181");
    $dat_arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {
            $dat_arr[] = $row['iResSessionDatID'];
        }
    }
    $data = implode(",", $dat_arr);

    if (!empty($data))
        $data_cond = "NOT IN (" . $data . ")";
    else
        $data_cond = "";

    $q1 = "select distinct(iSessionID) from vimeo_log where cFlag_Completion = 'N' and iResSessionDatID $data_cond and iMemberID = '$member_id' and iSessionID <> 0";
    $r1 = sql_query($q1, "common.inc.3193");
    $arr = array();
    if (sql_num_rows($r1)) {

        while ($row1 = sql_fetch_assoc($r1)) {

            $arr[] = $row1['iSessionID'];
        }
    }

    return $arr;
}

function GetCompletedSelfPaced($member_id, $series = false, $topic = false)
{ //#4
    if ($series == true) {
        $qg = "select rsd.iSessionID, count(*) as count, s.iSeriesID from res_session_dat rsd join session s on rsd.iSessionID = s.iSessionID where rsd.cStatus = 'A' group by rsd.iSessionID order by rsd.iSessionID";
        $rg = sql_query($qg, "common.inc.3214");
        $arrg = array();
        if (sql_num_rows($rg)) {
            while ($grow = sql_fetch_object($rg)) {
                $parent = GetXFromYID("select iParentID from series where iSeriesID = '$grow->iSeriesID'");
                $series = ($parent != 0) ? $parent : $grow->iSeriesID;
                $arrg[] = array("SESSION_ID" => $grow->iSessionID, "COUNT" => $grow->count, "SERIES" => $series);
            }
        }

        $qm = "select v.iSessionID, count(distinct(iResSessionDatID)) as count, s.iSeriesID from vimeo_log v join session s on v.iSessionID = s.iSessionID  where v.cFlag_Completion = 'Y' and v.iMemberID = '$member_id' group by iSessionID order by v.iSessionID";
        $rm = sql_query($qm, "common.inc.3214");
        $arrm = array();
        if (sql_num_rows($rm)) {
            while ($mrow = sql_fetch_object($rm)) {
                $parent = GetXFromYID("select iParentID from series where iSeriesID = '$mrow->iSeriesID'");
                $series = ($parent != 0) ? $parent : $mrow->iSeriesID;
                $arrm[] = array("SESSION_ID" => $mrow->iSessionID, "COUNT" => $mrow->count, "SERIES" => $series);
            }
        }
        $completed_sessions = array();
        if (!empty($arrm)) {

            for ($i = 0; $i < sizeof($arrm); $i++) {

                if ($arrm[$i]['COUNT'] == $arrg[$i]['COUNT']) {
                    $completed_sessions[] = $arrm[$i];
                }
            }
        }

        return $completed_sessions;
    } else if ($topic == true) {
        $qg = "select rsd.iSessionID, count(*) as count, s.iSkillID from res_session_dat rsd join skill_session_assoc s on rsd.iSessionID = s.iSessionID where rsd.cStatus = 'A' group by s.iSkillID order by rsd.iSessionID";
        $rg = sql_query($qg, "common.inc.3214");
        $arrg = array();
        if (sql_num_rows($rg)) {
            while ($grow = sql_fetch_object($rg)) {
                $arrg[] = array("SESSION_ID" => $grow->iSessionID, "COUNT" => $grow->count, "SKILLS" => $grow->iSkillID);
            }
        }

        $qm = "select v.iSessionID, count(distinct(iResSessionDatID)) as count, s.iSkillID from vimeo_log v join skill_session_assoc s on v.iSessionID = s.iSessionID  where v.cFlag_Completion = 'Y' and v.iMemberID = '$member_id' group by s.iSkillID order by v.iSessionID";
        $rm = sql_query($qm, "common.inc.3214");
        $arrm = array();
        if (sql_num_rows($rm)) {
            while ($mrow = sql_fetch_object($rm)) {
                $arrm[] = array("SESSION_ID" => $mrow->iSessionID, "COUNT" => $mrow->count, "SKILLS" => $mrow->iSkillID);
            }
        }
        $completed_sessions = array();
        if (!empty($arrm)) {

            for ($i = 0; $i < sizeof($arrm); $i++) {

                if ($arrm[$i]['COUNT'] == $arrg[$i]['COUNT']) {
                    $completed_sessions[] = $arrm[$i];
                }
            }
        }

        return $completed_sessions;
    } else {
        $gen_dat_arr = GetXArrFromYID("select rsd.iSessionID, count(*) as count from res_session_dat rsd where cStatus = 'A' group by iSessionID", 3);

        $mem_dat_arr = GetXArrFromYID("select iSessionID, count(distinct(iResSessionDatID)) as count from vimeo_log where cFlag_Completion = 'Y' and iMemberID = '$member_id' group by iSessionID", 3);
        $completed_sessions = array();
        if (!empty($mem_dat_arr)) {

            foreach ($mem_dat_arr as $session => $dat) {

                if ($mem_dat_arr[$session] == $gen_dat_arr[$session]) {

                    $completed_sessions[] = $session;
                }
            }
        }

        return $completed_sessions;
    }
}

function GetSelfPacedTimeSpent($member_id)
{ //#7

    $time = GetXFromYID("select sum(iUtilization_seconds) from vimeo_utilization where iMemberID = '$member_id'");

    $min = intval($time / 60);

    return $min;
}

function GetIncompleteSessions($member_id)
{ //#9

    $q = "SELECT distinct(iSessionID) FROM vimeo_log WHERE iMemberID = $member_id AND (cFlag_Completion = 'N' and cFlag_Completion <> 'Y') AND dtEnd IS NULL and iSessionID<>0";
    $r = sql_query($q, "common.inc.3220");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function GetSessionFeedback($member_id)
{ //#10

    $q = "SELECT * FROM reviews WHERE iMemberID = $member_id and cStatus = 'A' group by iSessionID";
    $r = sql_query($q, "common.inc.3220");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function GetSessionScores($member_id)
{ //#11

    $q = "SELECT fScorePerc, iSessionID FROM assmt_assessment WHERE iMemberID = $member_id and cStatus = 'A' group by iSessionID order by dDOA DESC";
    $r = sql_query($q, "common.inc.3220");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[$row['iSessionID']] = $row['fScorePerc'];
        }
    }

    return $arr;
}


function GetMemberCertificates($member_id = '')
{ //#12, #32
    $cond = '';
    if (!empty($member_id)) {
        $cond .= " and iMemberID = $member_id";
    }

    $q = "SELECT * FROM assmt_assessment WHERE 1 $cond and cStatus = 'A' and vCertificate <> '' and fScorePerc >= 70  group by iSessionID";
    $r = sql_query($q, "common.inc.3220");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function GetRatingAvg($series = false)
{ //#30, #31

    if ($series == true) {

        $q = "select sum(r.fRating) as sum, count(*) as count, s.iSeriesID from reviews r join session s on r.iSessionID = s.iSessionID where r.cStatus = 'A' group by s.iSeriesID";

        $r = sql_query($q, "common.inc.3306");
        $arr = array();
        if (sql_num_rows($r)) {

            while ($row = sql_fetch_assoc($r)) {

                $arr[] = array("sum" => $row['sum'], "count" => $row['count'], "series" => $row['iSeriesID']);
            }

            if (!empty($arr)) {
                $arr1 = array();
                for ($a = 0; $a < sizeof($arr); $a++) {

                    $parent = GetXFromYID("select iParentID from series where iSeriesID = '" . $arr[$a]['series'] . "'");
                    if (!empty($parent)) {
                        $arr1[$parent][] = array('avg' => ($arr[$a]['sum'] / $arr[$a]['count']));
                    } else {
                        $arr1[$arr[$a]['iSeriesID']][] = array('avg' => ($arr[$a]['sum'] / $arr[$a]['count']));
                    }
                }
            }
        }
        $rating_arr = array();
        if (!empty($arr1)) {

            foreach ($arr1 as $k => $v) {

                foreach ($v as $value) {
                    $rating_arr[$k] += $value['avg'];
                }
            }
        }

        return $rating_arr;
    } else {

        $q = "select sum(fRating) as sum, count(*) as count from reviews where cStatus = 'A' group by iSessionID";

        $r = sql_query($q, "common.inc.3306");
        if (sql_num_rows($r)) {

            while ($row = sql_fetch_assoc($r)) {

                $avg = $row['sum'] / $row['count'];
            }
        }
        return $avg;
    }
}

function GetMemberQuestions($member_id)
{ //#18

    $q = "SELECT * FROM qna_queries WHERE iMemberID = $member_id";
    $r = sql_query($q, "common.inc.3220");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}
function GetMemberNotes($member_id)
{ //#18

    $q = "SELECT * FROM res_session_dat_notes WHERE iMemberID = $member_id";
    $r = sql_query($q, "common.inc.3220");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function AssessmentVSCertificates()
{ //#33

    $q = "select count(*) as answered, (select count(*) from assmt_assessment where vCertificate <> '' and cStatus = 'A') as certificates from assmt_assessment where cStatus = 'A'";
    $r = sql_query($q, "common.inc.3404");
    $answered = $certificates = '0';
    if (sql_num_rows($r)) {

        list($answered, $certificates) = sql_fetch_row($r);
    }
    return $answered . '~' . $certificates;
}

function GetPopularity($type)
{ //#35, #36

    $q = "select s.iSessionID, count(*) as count from attendance a join session s on a.iSessionID = s.iSessionID where a.cAttended = 'Y' and s.cType = '$type' group by s.iSessionID  order by count DESC";
    $r = sql_query($q, "common.inc.3419");
    $arr = array();
    if (sql_num_rows($r)) {
        while ($row = sql_fetch_assoc($r)) {

            $arr[$row['iSessionID']] = $row['count'];
        }
    }

    return $arr;
}

function GetMostPopuplarSessionsBySeries()
{

    $q = "select s.iSeriesID, count(mba.iRefID) as popular from member_bookmark_assoc mba join session s on mba.iRefID = s.iSessionID where mba.cRefType = 'SES' group by s.iSeriesID order by popular desc";
    $r = sql_query($q, "common.inc.3435");
    $arr = array();
    if (sql_num_rows($r)) {
        while ($row = sql_fetch_assoc($r)) {

            $parent = GetXFromYID("select iParentID from series where iSeriesID = '" . $row['iSeriesID'] . "'");
            if (!empty($parent)) {
                $arr[$parent] = $row['popular'];
            } else {
                $arr[$row['iSeriesID']] = $row['popular'];
            }
        }
    }

    return $arr;
}

function GetCertificatesFor($topics, $series)
{ //#34

    if ($series == true) {

        $q = "select s.iSeriesID, count(a.vCertificate) as certificates from assmt_assessment a join session s on a.iSessionID = s.iSessionID where a.vCertificate <> '' and a.cStatus = 'A'";
        $r = sql_query($q, "common.inc.3456");
        $arr = array();
        if (sql_num_rows($r)) {
            while ($row = sql_fetch_assoc($r)) {
                $parent = GetXFromYID("select iParentID from series where iSeriesID = '" . $row['iSeriesID'] . "'");
                if (!empty($parent)) {
                    $arr[$parent] = $row['certificates'];
                } else {
                    $arr[$row['iSeriesID']] = $row['certificates'];
                }
            }
        }

        return $arr;
    } else if ($topics == true) {


        $q = "select iSkillID, count(a.vCertificate) as certificates from assmt_assessment a join skill_session_assoc s on a.iSessionID = s.iSessionID where a.vCertificate <> '' and a.cStatus = 'A'";
        $r = sql_query($q, "common.inc.3456");
        $arr = array();
        if (sql_num_rows($r)) {
            while ($row = sql_fetch_assoc($r)) {
                $arr[$row['iSkillID']] = $row['certificates'];
            }
        }

        return $arr;
    }
}

function MemberCount($status, $expired = true)
{
    $cond = '';
    $today = date('Y-m-d 00:00:00');
    if ($expired == true) {
        $cond .= " and dtExpiry >= '$today'";
    }

    $q = "select * from member where cStatus = '$status' $cond";
    $r = sql_query($q, "common.inc.3498");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {
            $arr[] = $row;
        }
    }

    return sizeof($arr);
}

function MemberReviews()
{

    $q = "select * from reviews where cStatus = 'A' order by dtPosted DESC, fRating DESC";
    $r = sql_query($q, "common.inc.3514");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}


function GetBestAttended()
{

    $q = "select m.*, count(a.cAttended) as attendance from member m join attendance a on m.iMemberID = a.iMemberID where a.cAttended = 'Y' group by a.iMemberID order by attendance DESC";
    $r = sql_query($q, "common.inc.3535");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}
function GetBestScored()
{

    $q = "select m.*, sum(fScorePerc) as score from member m join assmt_assessment a on m.iMemberID = a.iMemberID where a.fScorePerc >= '70' group by a.iMemberID order by score DESC";
    $r = sql_query($q, "common.inc.3535");
    $arr = array();
    if (sql_num_rows($r)) {

        while ($row = sql_fetch_assoc($r)) {

            $arr[] = $row;
        }
    }

    return $arr;
}

function GetCompletedAttendedLive($member_id, $series = false, $topic = false)
{ //#3

    if ($series == true) {
        $q = "select s.iSeriesID, a.* from attendance a join session s on a.iSessionID = s.iSessionID where a.cAttended = 'Y' and a.cStatus = 'A' and iMemberID = '$member_id' group by s.iSeriesID order by a.dtAdded";
        $r = sql_query($q, "common.inc.3570");
        $arr = array();
        if (sql_num_rows($r)) {
            while ($row = sql_fetch_assoc($r)) {
                $parent = GetXFromYID("select iParentID from series where iSeriesID = '" . $row['iSeriesID'] . "'");
                if (!empty($parent)) {
                    $arr[$parent] = $row;
                } else {
                    $arr[$row['iSeriesID']] = $row;
                }
            }
        }

        return $arr;
    } else if ($topic == true) {


        $q = "select s.iSkillID, a.* from attendance a join skill_session_assoc s on a.iSessionID = s.iSessionID where a.cAttended = 'Y' and a.cStatus = 'A' and iMemberID = '$member_id' group by s.iSkillID order by a.dtAdded";
        $r = sql_query($q, "common.inc.3456");
        $arr = array();
        if (sql_num_rows($r)) {
            while ($row = sql_fetch_assoc($r)) {
                $arr[$row['iSkillID']] = $row;
            }
        }

        return $arr;
    } else {

        echo $q = "select * from attendance a where a.cAttended = 'Y' and a.cStatus = 'A' and iMemberID = '$member_id'";
        $r = sql_query($q, "common.inc.3456");
        $arr = array();
        if (sql_num_rows($r)) {
            while ($row = sql_fetch_assoc($r)) {

                $arr[] = $row;
            }
        }

        return $arr;
    }
}

function GetMemberTimeSpent($member_id)
{ //#13
    $time_spent = GetXFromYID("SELECT SUM(TIMESTAMPDIFF(MINUTE,dtSignin,dtSignout)) from log_user where iUID = '$member_id'");
    echo $time_spent;
}

function SendSMS($contact, $sms_content, $content_id, $page_url)
{
    $data = array(
        "username" => "imagoa",
        "password" => "123456",
        "sender" => "IMAGOA",
        "mobile" => $contact,
        "message" => $sms_content,
        "route" => "T",
        "entity_id" => "1201162169168701817",
        "content_id" => $content_id
    );

    list($header, $content) = PostRequest("http://shudhsms.in/sendsms.php", $page_url, $data);

    return $content;
    // print_r($content);
    // echo "<br><br>";
    // print_r($header);

    /*$sms_content = urlencode($sms_content);
	
	if(strlen($contact)) $contact = '91'.$contact;
	
    $user = 'veejayfacility';
    $password = 'facility@321'; 
    $msisdn = $contact;
    $sid = 'DOSTEP';
    $msg = $sms_content;
    $fl = 0;
    $gwid = 2;	
	// https://smpp.keepintouch.co.in/vendorsms/pushsms.aspx?user=abc&password=xyz&msisdn=919898xxxxxx&sid=SenderId&msg=test%20message&fl=0
	//http://5.189.153.48:8080/vendorsms/pushsms.aspx?user=abc&password=xyz&msisdn=919898xxxxxx&sid=SenderId&msg=test message&fl=0&gwid=2
	
	$curl = curl_init();
	curl_setopt_array($curl, array(
	  CURLOPT_URL => "https://smpp.keepintouch.co.in/vendorsms/pushsms.aspx?user=$user&password=$password&msisdn=$msisdn&sid=$sid&msg=$msg&fl=$fl&gwid=$gwid",
	  CURLOPT_RETURNTRANSFER => true,
	  CURLOPT_ENCODING => "",
	  CURLOPT_MAXREDIRS => 10,
	  CURLOPT_TIMEOUT => 30,
	  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	  CURLOPT_CUSTOMREQUEST => "GET",
	  CURLOPT_SSL_VERIFYHOST => 0,
	  CURLOPT_SSL_VERIFYPEER => 0,
	));
	
	$response = curl_exec($curl);

	// echo $response;
	$err = curl_error($curl);
	
	curl_close($curl);
	
	return $response.'=>'.$err;*/

    /*if ($err) 
	{
		echo "cURL Error #:" . $err;
	} 
	else 
	{
	  	echo $response;
	}*/
}

// Clubcubana respective functions
function updateBookingTotals($bookingid)
{
    $result = false;
    if (!empty($bookingid) && is_numeric($bookingid)) {
        $num_guests = $tot_disc = $tot_tax = $tot_payable = 0;
        $q = "SELECT iGuests, fRate, iTicketID,  fTotal, cPromoStatus, fPromoDisc, cDisc_type, fDisc_val, fDisc_amt, iTaxID, fTaxPerc, fTaxValue, fTotAmt from booking_dat where iBookingID=$bookingid";
        $r = sql_query($q, "COMMON.3797");

        if (sql_num_rows($r)) {
            for ($i = 1; $o = sql_fetch_object($r); $i++) {
                $iGuests = $o->iGuests;
                $fRate = $o->fRate;
                $iTicketID = $o->iTicketID;
                $fTotal = $o->fTotal;
                $cPromoStatus = $o->cPromoStatus;
                $fPromoDisc = $o->fPromoDisc;
                $cDisc_type = $o->cDisc_type;
                $fDisc_val = $o->fDisc_val;
                $fDisc_amt = $o->fDisc_amt;
                $iTaxID = $o->iTaxID;
                $fTaxPerc = $o->fTaxPerc;
                $fTaxValue = $o->fTaxValue;
                $fTotAmt = $o->fTotAmt;

                $num_guests += $iGuests;
                $tot_disc += ($fDisc_amt + $fPromoDisc);
                $tot_tax += $fTaxValue;
                $tot_payable += $fTotAmt;
            }

            $q1 = "UPDATE booking set iNumOfGuests='$num_guests', iTotalDiscount='$tot_disc', fTaxValue='$tot_tax', fPayable='$tot_payable', cStatus='A' WHERE iBookingID=$bookingid";
            $r1 = sql_query($q1, "COMMON.3819");

            if (sql_affected_rows($r1))
                $result = sql_affected_rows($r1);
        }
    }

    return $result;
}

function LogFront($type, $desc = "", $qr = "")
{
    $_file_name = basename($_SERVER["SCRIPT_NAME"]);
    $remaddr = $_SERVER['REMOTE_ADDR'];
    if (!empty($type)) {
        $q = "INSERT INTO log_front (cType, vPage, vDesc, dtLog, vIP, vQuery) VALUES ('$type', '$_file_name', '$desc', '" . NOW . "', '$remaddr', '$qr')";
        $r = sql_query2($q, "COMMON.3839");
    }
}

function updateCustvalues($custid)
{
    if (!empty($custid) && is_numeric($custid)) {
    }
}

function updateBookingCount($bookingid)
{
    if (!empty($bookingid) && is_numeric($bookingid)) {
        $_eventid = GetXFromYID("select iEventID from booking where iBookingID=$bookingid limit 1");
        $et_b_arr = GetXArrFromYID("select bd.iTicketID, sum(bd.iGuests) from booking b left join booking_dat bd on bd.iBookingID=b.iBookingID where b.iEventID=$_eventid and b.vPayStatus='Y' group by bd.iTicketID", 3);

        if (!empty($et_b_arr) && count($et_b_arr)) {
            foreach ($et_b_arr as $_ticketid => $num_booked) {
                $q1 = "UPDATE events_tickets SET iQtyBooked='$num_booked' WHERE iEventID=$_eventid and iTicketID=$_ticketid";
                // echo $q1.'<br/>';
                $r1 = sql_query($q1, "");
            }
        }
    }
}

function duplicateEvent($eventid)
{
    global $sess_user_id;
    $ret_flag = 0;
    if (!empty($eventid) && is_numeric($eventid)) {
        $nxt_eid = NextID('iEventID', 'events');
        $q = "INSERT INTO events (iEventID, vName, vUrlName, vDesc, vVenue, dStart, tStart, cType, dtCreated, iUserID, iRank, cStatus) SELECT $nxt_eid, vName, vUrlName, vDesc, vVenue, dStart, tStart, cType, '" . NOW . "', '$sess_user_id', '$nxt_eid', 'A' from events where iEventID='$eventid' ";
        $r = sql_query($q, "");

        // insert dat only if main entry is inserted successfully
        if (sql_affected_rows($r)) {
            $tq = "SELECT iEventID, iTicketID, iQty, fRate, cStatus FROM events_tickets WHERE iEventID='$eventid'";
            $tr = sql_query($tq, "");

            if (sql_num_rows($tr)) {
                while (list($EventID, $TicketID, $Qty, $Rate, $Status) = sql_fetch_row($tr)) {
                    $q1 = "INSERT INTO events_tickets (iEventID, iTicketID, iQty, fRate, cStatus) VALUES ('$nxt_eid', '$TicketID', '$Qty', '$Rate', '$Status')";
                    $r1 = sql_query($q1, "");
                }
            }
            $_SESSION[PROJ_SESSION_ID]->success_info = "Event duplicated successfully";
            $ret_flag = '1~' . $nxt_eid; // insertion complete
        }
    }

    if ($ret_flag == 0)
        $_SESSION[PROJ_SESSION_ID]->error_info = "Event could not be copied!";

    return $ret_flag;
}
