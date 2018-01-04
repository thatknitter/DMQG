<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if(isset($_GET['elp']))
{
	if($_GET['elp'] == "viewstatus")
	{
		$form = array();
		$form['delvid'] = isset($_GET['delvid']) ? sanitize_text_field($_GET['delvid']) : 0;
		if(is_numeric($form['delvid']))
		{
			elp_cls_dbquery2::elp_delivery_ups($form['delvid']);
		}
	}
}
die();
?>