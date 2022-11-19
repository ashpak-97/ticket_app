var ajax_url = '../includes/ajax.inc.php';

try 
{
	http = new XMLHttpRequest(); /* e.g. Firefox */
	http2 = new XMLHttpRequest(); /* e.g. Firefox */
} 
catch(e) 
{
	try 
	{
    	http = new ActiveXObject("Msxml2.XMLHTTP"); 
		http2 = new ActiveXObject("Msxml2.XMLHTTP"); 
  	}
	catch (e) 
	{
    	try 
		{
    		http = new ActiveXObject("Microsoft.XMLHTTP");  /* some versions IE */
    		http2 = new ActiveXObject("Microsoft.XMLHTTP");  /* some versions IE */
    	} 
		catch (E) 
		{
			http = false;
			http2 = false;
		} 
	} 
}

function str_trim(str) // strips of leading and following whitespaces from a string
{	
	//DumpProperties(str);
	
	if(str.length > 0)
		while(str.charAt(0)==' ')
			str = str.substr(1);
		
	if(str.length > 0)
		while(str.charAt((str.length - 1))==' ')
			str = str.substring(0, str.length-1);
	
	return str;
}

function validate_email(email_txt) // validates a string as a email id
{
	var emailReg = "^[\\w-_\.]*[\\w-_\.]\@([\\w].+)\.[\\w]$";
	var regex = new RegExp(emailReg);
	return regex.test(email_txt);
}

function RemoveRow(tr_id)
{
	var tr_obj = document.getElementById(tr_id);
	if(tr_obj) tr_obj.parentNode.removeChild(tr_obj);
}

function ChangeStatus(obj, mode, status, id)
{
	$.get(ajax_url, {response:'UPDATE_STATUS', mode:mode, status:status, id:id}, function(results) {
		var result = results.split('~');
		obj.parentNode.innerHTML=result[0];		
		$('#LBL_INFO').html(NotifyThis(result[1], 'success'));
		// InitNotifyClose();
	});	// */
}

function NotifyThis(text, mode)
{
	var mode_str = 'msgalert';
	var mode_icon = 'flaticon-questions-circular-button';
	
	if(mode == 'success') mode_str = 'alert-success';
	else if(mode == 'error') mode_str = 'alert-danger';
	else if(mode == 'info') mode_str = 'alert-warning';

	if(mode == 'success') mode_icon = 'flaticon2-check-mark';
	else if(mode == 'error') mode_icon = 'flaticon2-cross';
	else if(mode == 'info') mode_icon = 'flaticon-warning';

	text = str_trim(text);
	return (text!='')? ' <div class="alert '+ mode_str +' fade show" role="alert"><div class="alert-icon"><i class="'+ mode_icon +'"></i></div><div class="alert-text">'+text+'</div><div class="alert-close"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true"><i class="la la-close"></i></span></button></div></div>': '';
}

function ToggleVisibility(obj_id)
{
	var obj = document.getElementById(obj_id);
	if(!obj)
		return false;
	
	obj.style.display = (obj.style.display=='none')? '': 'none';
}

function GoToPage(page)
{
	window.document.location.href=page;
}

function GetRadioValue(rd_obj)
{
	for(var xi=0; xi < rd_obj.length; xi++)
	{
		if(rd_obj[xi].checked)
			return rd_obj[xi].value;
	}

	return false;
}

function IsCodeUnique(id, obj, mode, prefix)
{
	var val = obj.value;
	if(!prefix || prefix == "undefined")
		prefix = '';

	$.get(ajax_url, {response:'UNIQUE_CODE', id:id, val:val, mode:mode}, function(results) {
		var ctrl_obj = document.getElementById(prefix+'code_flag');
		if(ctrl_obj)
		{
			ctrl_obj.value = results;
			obj.style.backgroundColor = (ctrl_obj.value=='0')? "#ffcfcf": "#cfffcf";
		}
	});
}

function numbersonly(e)
{
	var unicode = e.charCode ? e.charCode : e.keyCode;
	
	if(unicode != 8 && unicode!=46 && unicode!=43 )//if the key isn't the backspace key (which we should allow)
	{
		if((unicode < 48 || unicode > 57) || (unicode==40 || unicode==41 || unicode==43 || unicode==45 || unicode==16))   //if not a number
				return false;  //disable key press
		else
			return true;   // enable keypress
	}
	else
	{
		return true;   // enable keypress
	}
}

function ConfirmDelete(txt, page)
{ 
	var msg = "You Are About To Delete this " + txt + "! Continue?";
    
    swal.fire({
      title: 'Delete',
      text: msg,
      //type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, delete it!'
    }).then(function(result){
    	if(result.value){
    		window.document.location.href=page;
    	}
    });
}

function ConfirmDelete2(txt, page)
{ 
	var msg = "You Are About To Delete this " + txt + "! Continue?";
    if(confirm(msg))
	{
		window.document.location.href=page;
   	}
}

function ChangeYesNoStatus(obj, mode, status, id)
{
	$.get(ajax_url, {response:'UPDATE_YESNO', mode:mode, status:status, id:id}, function(results) {
		var result = results.split('~');
		obj.parentNode.innerHTML=result[0];		
		$('#LBL_INFO').html(NotifyThis(result[1], 'success'));
		// InitNotifyClose();
	}).error(function(err) {
		$('#LBL_INFO').html(NotifyThis('Error Occured When Making This Request', 'error'));
		// InitNotifyClose();
	});	// */
}

function ShowError( element, mesg)
{	
	var spanID = element+"_span";

	if( $( element ).hasClass( "is-invalid" ) )
	{

	}
	else{
		$(element).addClass("is-invalid");
	    $('<span id="'+spanID+'";" class="invalid-feedback em">'+ mesg +'</span>').insertAfter( element );
	}
}

function HideError(element)
{	
	var elemID = $(element).attr('id');
	var spanID = elemID+"_span";

	$(element).removeClass("is-invalid");
	$('#'+spanID).remove();
}

function ShowErrorFront( element, mesg)
{	
	var elemID = $(element).attr('id');
	var spanID = elemID+"_span";

	if( $( element ).hasClass( "is-invalid" ) )
	{
		/*$(element).removeClass("is-invalid");
		$('#'+spanID).remove();*/
	}
	else{
		$(element).addClass("is-invalid");
	    $('<span id="'+spanID+'" class="validation"><img src="images/alert.png">'+ mesg +'</span>').insertBefore( element );
	}
}

function ClearMessages(frm_id)
{
	$("form#"+frm_id+" :input").each(function(){
 		var input = $(this); // This is the jquery object of the input, do what you will
 		elemID = input.attr('id');

 		if( $( "#"+elemID ).hasClass( "is-invalid" ) )
 		{
 			console.log(elemID);
 			$("#"+elemID).removeClass("is-invalid");
 			$('#'+elemID+'_span').remove();
 		}
	});
}

function InitAdvancedEditor(upload_url="", upload=false, editor_id=".ckeditor")
{
	var editor = null;
	var toolbar_list = ['heading', '|', 'bold', 'italic', 'underline', 'link', 'alignment', 'bulletedList', 'numberedList', '|', 'fontBackgroundColor', 'fontColor', 'fontSize', 'highlight', 'indent', 'horizontalLine', 'outdent', '|', 'blockQuote', 'insertTable', 'undo', 'redo', '|', 'code'];
	if(upload)
		toolbar_list.push("imageUpload");

	ClassicEditor.create(document.querySelector(editor_id), {
		toolbar: {
			items: [
				'heading',
				'|',
				'bold',
				'italic',
				'underline',
				'link',
				'alignment',
				'bulletedList',
				'numberedList',
				'|',
				'fontBackgroundColor',
				'fontColor',
				'fontSize',
				'highlight',
				'indent',
				'horizontalLine',
				'outdent',
				'|',
				'blockQuote',
				'insertTable',
				'undo',
				'redo',
				'|',
				'code'
			]
		},
		language: 'en',
		image: {
			toolbar: [
				'imageTextAlternative',
				'imageStyle:full',
				'imageStyle:side'
			]
		},
		table: {
			contentToolbar: [
				'tableColumn',
				'tableRow',
				'mergeTableCells',
				'tableCellProperties',
				'tableProperties'
			]
		},
		licenseKey: '',

		simpleUpload: {
            // The URL that the images are uploaded to.
            uploadUrl: upload_url,

            // Enable the XMLHttpRequest.withCredentials property.
            // withCredentials: true,

            // Headers sent along with the XMLHttpRequest to the upload server.
            /*headers: {
                'X-CSRF-TOKEN': 'CSFR-Token',
                Authorization: 'Bearer <JSON Web Token>'
            }*/
        }
	})
	.then(editor => {
	  	//debugger;
		window.editor = editor;
	})
	.catch(error => {
		console.error(error);
	})
}

function InitSimpleEditor(editor_id="")
{
	var editor = null;
	ClassicEditor.create(document.querySelector("#"+editor_id), {
		toolbar: [
		  "bold",
		  "italic",
		  "link",
		  "bulletedList",
		  "numberedList",
		  "blockQuote",
		  "undo",
		  "redo"
		]
	})
	.then(editor => {
	  	//debugger;
		window.editor = editor;
	})
	.catch(error => {
		console.error(error);
	})
}

function PreviewImage(fileInput="", displayDIV="imgDiv")
{
    if (fileInput.files && fileInput.files[0]) {
    var reader = new FileReader();
    
    reader.onload = function(e) {
      $('#'+displayDIV).attr('src', e.target.result);
    }
    
    reader.readAsDataURL(fileInput.files[0]);
  }
}

function InitRanking(table_id="", mode="", debug=false)
{
	var id_str='';

	$("#"+table_id+" tbody").sortable({
			opacity: 0.6, cursor: 'move',

			update : function(ev, ui) {
				$('input[name="txtrankid[]"]').each(function(){
					id_str+=jQuery(this).val()+',';
				});

				if(debug) {
					alert(ajax_url+"?response=UPDATE_SORT&mode="+mode+"&id_str="+id_str);
				}

				$.get(ajax_url, {response:"UPDATE_SORT", mode:mode, id_str:id_str}, function(results){
					//alert(records);
					result = results.split('~');
					$("#LBL_INFO").html( NotifyThis(result[1], 'success') );
				});

			},
	});
}

function LiveSearchInside_HTMLTable()
{
	var input, filter, table, tr, td, i, txtValue;
	
	input = document.getElementById("userSearch");
	filter = input.value.toUpperCase();
	table = document.getElementById("usersTable");
	tr = table.getElementsByTagName("tr");

	for (i = 0; i < tr.length; i++)
	{
		td = tr[i].getElementsByTagName("td")[0];
		if(td)
		{
			txtValue = td.textContent || td.innerText;
			if(txtValue.toUpperCase().indexOf(filter) > -1)
				tr[i].style.display = "";
			else
				tr[i].style.display = "none";
		}       
	}
}

function AddAnother(frm)
{
	frm.add_mode.value = "Y";
	$('button[type=submit]').click();
}

function ValidateFileUpload(file_name,upload_type)
{
	if(upload_type=='D')
	{
		var file_type = new Array('.txt','.doc','.docx','.pdf','.xls','.xlsx','.ppt','.pptx','.jpg','.jpeg','.pjpeg','.TXT','.DOC','.DOCX','.PDF','.XLS','.XLSX','.PPT','.PPTX','.JPG','.JPEG','.PJPEG');
		var mime_type2 = new Array('text/plain','text/*','application/msword', 'application/vnd.ms-word', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/pdf', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation', 'image/pjpeg', 'image/jpeg', 'image/jpg', 'application/download');
	}
	else if(upload_type=='D2')
	{
		var file_type = new Array('.json');
		var mime_type2 = new Array('application/json');
	}
	else if(upload_type=='C')
	{
		var file_type = new Array('.csv');
		var mime_type2 = new Array('application/csv', 'text/csv', 'application/vnd.ms-excel');
	}
	else
	{
		var file_type = new Array('.gif','.png','.pjpeg','.jpeg','.jpg','.bmp','.GIF','.PNG','.PJPEG','.JPEG','.JPG','.BMP');
		var mime_type2 = new Array('image/gif','image/png','image/pjpeg','image/jpeg','image/jpg','image/bmp');
	}
	
	var file = document.getElementById(file_name).value;
	var mime_type = document.getElementById(file_name).files[0].type;
    allowSubmit = false;
    allowSubmit2 = false;
    if (!file) return;
    while (file.indexOf("\\") != -1)
    file = file.slice(file.indexOf("\\") + 1);
    ext = file.slice(file.indexOf(".")).toLowerCase();

for (var i = 0; i < file_type.length; i++) {
		if (file_type[i] == ext) { allowSubmit = true; break; }
	}

    for (var i = 0; i < mime_type2.length; i++) {
		if (mime_type2[i] == mime_type) { allowSubmit2 = true; break; }
	}

    if(allowSubmit && allowSubmit2) return true;
    else
	{
		if(!allowSubmit)
		{
			document.getElementById(file_name).value='';
			alert("Please only upload files that end in types:  "
			+ (file_type.join("  ")) + "\nPlease select a new "
			+ "file to upload and submit again.");
		}

		if(!allowSubmit2)
		{
			document.getElementById(file_name).value='';
			alert("Please only upload files that end in types:  "
			+ (mime_type2.join("  ")) + "\nPlease select a new "
			+ "file to upload and submit again.");
		}
	}

    return false;
}

function GenerateNewPass(pass)
{
	var url_str = '../includes/password.inc.php?mode=GetPass&pass='+pass;
	sd_obj = new serverData;
	var myRandom=parseInt(Math.random()*99999999);  // cache buster
	sd_response = sd_obj.send(url_str+'&rand='+myRandom,"");

	return sd_response;
}

function ArrayIndex(arr, str)
{
	var ret_val = -1;
	var arr_len = 0;

	arr_len = arr.length;

	if(arr_len > 0)
	{
		for(var i=0; i < arr_len; i++)
		{			
			if(str_trim(arr[i]," ") == str)
			{
				ret_val = i;
				break;
			}
		}
	}

	return ret_val;
}

function inArray(srch_txt, arr)
{	
	for(var i=arr.length-1; i>=0; i--)
	{
		if(str_trim(arr[i]," ") == srch_txt)
			return true;
	}
	
	return false;
}

function setChecked(obj)
{
//	alert('1');
	var frm = obj.form;	
	var id = obj.value;
//	alert('2');
	
	var chk = obj.checked;
//	alert('3');
	
//	alert(frm + ' || ' + id );
	if(!frm || !id)
		return false;
//	alert('4');
	
	frm.multi_ids.value = str_trim(frm.multi_ids.value);
//	alert('5');

	if(chk) // adding
	{
//	alert('6');
		if(frm.multi_ids.value=='')
		{
			frm.multi_ids.value = id + ",";	
		}
		else
		{
			var id_arr = frm.multi_ids.value.split(",");
			var flag = inArray(id, id_arr);

			if(!flag)
			{	
				frm.multi_ids.value += " " + id + ",";
			}
		}
	}
	else // removing
	{ 
//	alert('7');
		if(frm.multi_ids.value!='')
		{
			var id_arr = frm.multi_ids.value.split(",");
			var id_len = id_arr.length;
			var rmv_index = ArrayIndex(id_arr, id);
			
			if(rmv_index > -1)
			{
				for(var i=rmv_index; i < id_arr.length; i++)
				{
					if(i != (id_arr.length - 1)) // not the last element in the array
					{
						id_arr[i] = id_arr[i+1];
					}
				}

				id_arr.length = (id_len - 1); // pop off the last array item
			}
			
			frm.multi_ids.value = id_arr.join(",");
		}
	}
//	alert('8');
}

function checkAll(frm)
{
	var elem_arr = frm.elements;
	var elem_len = elem_arr.length;

	for(var i=0; i<elem_len; i++)
	{
		if(elem_arr[i].type=='checkbox' && elem_arr[i].name=='c[]')
		{
			elem_arr[i].checked = true;
			setChecked(elem_arr[i]);
		}	
	}
}

function uncheckAll(frm)
{
	var elem_arr = frm.elements;
	var elem_len = elem_arr.length;

	for(var i=0; i<elem_len; i++)
	{
		if(elem_arr[i].type=='checkbox' && elem_arr[i].name=='c[]')
		{
			elem_arr[i].checked = false;
			setChecked(elem_arr[i]);
		}	
	}
}

function ShowLoader()
{
	$.blockUI.defaults = {
		fadeIn: 200,
		fadeOut: 400,
	};
	$.blockUI({message: $('.body-block-example-1')});
}

function HideLoader()
{
	$.unblockUI();
}