<?php
include "../includes/common.php";
$results = false;

if (isset($_GET['response'])) $response = $_GET['response'];
else if (isset($_POST['response'])) $response = $_POST['response'];
else $response = false;

$edit_url = 'nl_events_edit.php';
$TICKETS_ARR = GetXArrFromYID("select iTicketID, vName from nl_tickets", "3");

if ($response == 'EVENT_TICKET') {
    $title = $str = "";
    $mode = isset($_POST['mode']) ? $_POST['mode'] : "";
    $event_id = isset($_POST['eventid']) ? $_POST['eventid'] : 0;
    $ticket_id = isset($_POST['ticketid']) ? $_POST['ticketid'] : 0;

    if ($mode == 'A') {
        if (!empty($event_id) && is_numeric($event_id)) {
            $title = "Add Tickets";
            $q = "select iTicketID, vName from nl_tickets where cStatus='A' order by vName";
            $r = sql_query($q, "EVT,T.A.20");

            $str .= '<form class="" id="addticket" name="addticket" method="post" action="' . $edit_url . '" enctype="multipart/form-data">';
            $str .= '<input type="hidden" name="txtid" id="txtid" value="' . $event_id . '">';
            $str .= '<input type="hidden" name="mode" id="mode" value="ADD_TICKET">';
            $str .= '<input type="hidden" name="add_mode" id="add_mode" value="N">';

            if (sql_num_rows($r)) {
                $str .= '<table style="width: 100%;" class="table table-hover table-striped table-bordered load-more-table">';
                $str .= '<thead>';
                $str .= '<tr>';
                $str .= '<th>#</th>';
                $str .= '<th>Name</th>';
                $str .= '</tr>';
                $str .= '</thead>';
                $str .= '<tbody>';

                $i = 1;
                while (list($t_ticketid, $t_ticketname) = sql_fetch_row($r)) {
                    $str .= '<tr>';
                    $str .= '<td><label><input type="checkbox" name="event_ticket[]" id="event_ticket_' . $t_ticketid . '" value="' . $t_ticketid . '">&nbsp;' . ($i++) . '.</label> </td>';
                    $str .= '<td>' . $t_ticketname . '</td>';
                    $str .= '</tr>';
                }
                $str .= '</tbody>';
                $str .= '<tfoot>';
                $str .= '<tr>';
                $str .= '<td colspan="2" align="right">
				<button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">close</button>
				<button class="btn btn-sm btn-success">save</button>
				</td>';
                $str .= '</tr>';
                $str .= '</tfoot>';
                $str  .= '</table>';
                $str  .= '</form>';
            }
        }
    } else if ($mode == 'E') {
        if (!empty($event_id) && is_numeric($event_id) && !empty($ticket_id) && is_numeric($ticket_id)) {
            $q = "SELECT iEventID, iTicketID, iQty, iQtyBooked, iQtyUsed, fRate, cStatus FROM nl_events_tickets WHERE iTicketID=$ticket_id and iEventID=$event_id and cStatus='A'";
            $r = sql_query($q, "EVT,T.A.70");

            $o = sql_fetch_object($r);
            $t_eventid = $o->iEventID;
            $t_ticketid = $o->iTicketID;
            $t_qty = $o->iQty;
            $t_qtybooked = $o->iQtyBooked;
            $t_qtyused = $o->iQtyUsed;
            $t_rate = $o->fRate;
            $t_status = $o->cStatus;
            $t_ticketname = isset($TICKETS_ARR[$t_ticketid]) ? $TICKETS_ARR[$t_ticketid] : NA;
            $title = "Update Ticket - " . $t_ticketname;
            $del_url = 'nl_events_edit.php?mode=REMOVE_TICKET&id=' . $t_eventid . '&tid=' . $t_ticketid;

            $str .= '<form class="" id="addticket" name="addticket" method="post" action="' . $edit_url . '" enctype="multipart/form-data">';
            $str .= '<input type="hidden" name="txtid" id="txtid" value="' . $event_id . '">';
            $str .= '<input type="hidden" name="txtticketid" id="txtticketid" value="' . $t_ticketid . '">';
            $str .= '<input type="hidden" name="mode" id="mode" value="UPDATE_TICKET">';
            $str .= '<input type="hidden" name="add_mode" id="add_mode" value="N">';

            $str .= '<div class="form-row">';
            $str .= '<div class="col-md-4">';
            $str .= '<div class="position-relative form-group">';
            $str .= '<label for="txtqty" class="">Quantity: <span class="text-danger">*</span></label>';
            $str .= '<input name="txtqty" id="txtqty" type="text" onkeypress="return numbersonly(event);" required="required" value="' . $t_qty . '" class="form-control">';
            $str .= '</div>';
            $str .= '</div>';

            $str .= '<div class="col-md-4">';
            $str .= '<div class="position-relative form-group">';
            $str .= '<label for="txtrate" class="">Rate: <span class="text-danger">*</span></label>';
            $str .= '<input name="txtrate" id="txtrate" type="text" onkeypress="return numbersonly(event);" required="required" value="' . $t_rate . '" class="form-control">';
            $str .= '</div>';
            $str .= '</div>';

            $str .= '</div>';

            $str .= '<div class="form-row">';
            $str .= '<div class="col-md-4">';
            $str .= '<div class="position-relative form-group">';
            $str .=    '<button type="button" class="btn btn-secondary btn-sm mr-2" data-dismiss="modal">close</button>';
            $str .= '<button type="submit"class="btn btn-sm btn-success mr-2">save</button>';
            $str .= '<button type="button" onClick="removeticket(\'' . $del_url . '\')" class="btn btn-sm btn-danger">remove</button>';
            $str .= '</div>';
            $str .= '</div>';
            $str .= '</div>';
            $str .= '</div>';
            $str .= '</form>';
        }
    }

    $results = $title . '~~**~~' . $str;
}
echo $results;
exit;
