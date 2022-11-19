<form action="" id="formINCLUDE" name="formINCLUDE" method="post">
	<input type="hidden" id="mode" name="mode" value="" />
	<input type="hidden" id="txtid" name="txtid" value="" />

</form>
<script>
	function SubmitIncludeForm(action, mode, id, txt) {
		var frm = document.formINCLUDE;

		if (str_trim(action) == '') {
			alert('Invalid Form Action');
			return false;
		}

		if (str_trim(mode) == '') {
			alert('Invalid Form Mode');
			return false;
		}

		if (str_trim(id) == '') {
			alert('Invalid Form ID');
			return false;
		}

		frm.action = action;
		frm.mode.value = mode;
		frm.txtid.value = id;

		var msg = "You Are About To Delete this " + txt + "! Continue?";
		if (confirm(msg))
			frm.submit();
	}
</script>