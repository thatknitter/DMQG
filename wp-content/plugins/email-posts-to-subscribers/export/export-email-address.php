<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if(isset($_GET['elp']))
{
	if($_GET['elp'] == "export")
	{
		if (!session_id())
		{
			session_start();
		}
		
		if ($_SESSION['elp_exportcsv'] == "YES")
		{
			if($_SERVER['REQUEST_METHOD'] == "POST") 
			{
				if (strpos($_SERVER['HTTP_REFERER'], get_option('siteurl')) !== false) 
				{
					global $wpdb;
					$option = isset($_REQUEST['option']) ? $_REQUEST['option'] : '';
					switch ($option) 
					{
						case "view_subscriber":
							$sSql = "select elp_email_mail as Email, elp_email_name as Name, elp_email_status as Status, ";
							$sSql = $sSql . " elp_email_created as Created, elp_email_group as EmailGroup ";
							$sSql = $sSql . " from ". $wpdb->prefix . "elp_emaillist ORDER BY elp_email_mail";
							$data = $wpdb->get_results($sSql);
							elp_cls_common::download($data, 's', '');
							break;
						case "registered_user":
							$data = $wpdb->get_results("select user_email as 'Email', user_nicename as 'Name' from ". $wpdb->prefix . "users ORDER BY user_nicename");
							elp_cls_common::download($data, 'r', '');
							break;
						case "commentposed_user":
							$sSql = "SELECT DISTINCT(comment_author_email) as Email, comment_author as 'Name'";
							$sSql = $sSql . "from ". $wpdb->prefix . "comments WHERE comment_author_email <> '' ORDER BY comment_author_email";
							$data = $wpdb->get_results($sSql);
							elp_cls_common::download($data, 'c', '');
							break;
						default:
							_e('Unexpected url submit has been detected', 'email-posts-to-subscribers');
							break;
					}
				}
				else
				{
					_e('Unexpected url submit has been detected', 'email-posts-to-subscribers');
				}
			}
			else
			{
				_e('Unexpected url submit has been detected', 'email-posts-to-subscribers');
			}
		}
		else
		{
			_e('Unexpected url submit has been detected', 'email-posts-to-subscribers');
		}
		
		
	}
}
die();
?>