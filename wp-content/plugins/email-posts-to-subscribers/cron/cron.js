function _elp_submit()
{
	if(document.elp_form.elp_cron_mailcount.value == "")
	{
		alert("Please select enter number of mails you want to send per hour/trigger.")
		document.elp_form.elp_cron_mailcount.focus();
		return false;
	}
	else if(isNaN(document.elp_form.elp_cron_mailcount.value))
	{
		alert("Please enter the mail count, only number.")
		document.elp_form.elp_cron_mailcount.focus();
		return false;
	}
}

function _elp_redirect()
{
	window.location = "admin.php?page=elp-crondetails";
}

function _elp_help()
{
	window.open("http://www.gopiplus.com/work/2014/05/02/email-subscribers-wordpress-plugin/");
}