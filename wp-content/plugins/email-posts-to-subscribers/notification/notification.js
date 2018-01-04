function _elp_submit()
{
	if(document.es_form.es_set_name.value=="")
	{
		alert("Please enter name for configuration.")
		document.es_form.es_set_name.focus();
		return false;
	}
	else if(document.es_form.es_set_templid.value=="")
	{
		alert("Please select template for this configuration.")
		return false;
	}
}

function _elp_delete(id)
{
	if(confirm("Do you want to delete this record?"))
	{
		document.frm_elp_display.action="admin.php?page=elp-postnotification&ac=del&did="+id;
		document.frm_elp_display.submit();
	}
}

function _elp_redirect()
{
	window.location = "admin.php?page=elp-postnotification";
}

function _elp_help()
{
	window.open("http://www.gopiplus.com/work/2014/05/02/email-subscribers-wordpress-plugin/");
}