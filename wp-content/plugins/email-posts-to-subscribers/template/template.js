function _elp_submit()
{
	if(document.elp_form.elp_set_name.value=="")
	{
		alert("Please enter name for configuration.")
		document.elp_form.elp_set_name.focus();
		return false;
	}
	else if(document.elp_form.elp_set_templid.value=="")
	{
		alert("Please select template for this configuration.")
		return false;
	}
}

function _elp_delete(id)
{
	if(confirm("Do you want to delete this record?"))
	{
		document.frm_elp_display.action="admin.php?page=elp-email-template&ac=del&did="+id;
		document.frm_elp_display.submit();
	}
}

function _elp_newsletter_delete(id)
{
	if(confirm("Do you want to delete this record?"))
	{
		document.frm_elp_display.action="admin.php?page=elp-composenewsletter&ac=del&did="+id;
		document.frm_elp_display.submit();
	}
}

function _elp_redirect()
{
	window.location = "admin.php?page=elp-email-template";
}

function _elp_newsletter_redirect()
{
	window.location = "admin.php?page=elp-composenewsletter";
}

function _elp_help()
{
	window.open("http://www.gopiplus.com/work/2014/03/28/wordpress-plugin-email-posts-to-subscribers/");
}