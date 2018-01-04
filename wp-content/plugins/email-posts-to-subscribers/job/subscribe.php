<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if(isset($_GET['elp']))
{
	if($_GET['elp'] == "subscribe")
	{
		$elp_email = "";
		$elp_name = "";
		$elp_group = "";
		
		// get name and email value
		$elp_email = isset($_POST['elp_email']) ? sanitize_text_field($_POST['elp_email']) : '';
		$elp_name = isset($_POST['elp_name']) ? sanitize_text_field($_POST['elp_name']) : '';
		$elp_group = isset($_POST['elp_group']) ? sanitize_text_field($_POST['elp_group']) : '';
		
		// trim querystring value
		$elp_email = trim($elp_email);
		$elp_name = trim($elp_name);
		$elp_group = trim($elp_group);
		
		//echo $elp_email . "--" . $elp_name . "" . $elp_group;
		//die();
		
		if($elp_email <> "")
		{
			if (!filter_var($elp_email, FILTER_VALIDATE_EMAIL))
			{
				echo "invalid-email";
			}
			else
			{
				//if (strpos($_SERVER['HTTP_REFERER'], get_option('siteurl')) !== false) 
				//{
					$action = "";
					global $wpdb;
					require_once(ELP_DIR.'classes'.DIRECTORY_SEPARATOR.'stater-mini.php');
					
					$data = elp_cls_dbquery2::elp_setting_select(1);
					if($elp_group == "")
					{
						$elp_group = "Public";
					}
					
					if( $data['elp_c_optinoption'] == "Double Opt In" )
					{
						$inputdata = array($elp_name, $elp_email, "Unconfirmed", $elp_group);
					}
					else
					{
						$inputdata = array($elp_name, $elp_email, "Single Opt In", $elp_group);
					}
				
					$action = elp_cls_dbquery::elp_view_subscriber_widget($inputdata);
					if($action == "sus")
					{
						$subscribers = array();
						$subscribers = elp_cls_dbquery::elp_view_subscriber_one($elp_email);
						if( $data['elp_c_optinoption'] == "Double Opt In" )
						{
							elp_cls_sendmail::elp_sendmail("optin", $subject = "", $content = "", $subscribers);
							echo "subscribed-pending-doubleoptin";
						}
						else
						{
							if( $data['elp_c_usermailoption'] == "YES" )
							{
								elp_cls_sendmail::elp_sendmail("welcome", $subject = "", $content = "", $subscribers);
							}
							echo "subscribed-successfully";
						}
					}
					elseif($action == "ext")
					{
						echo "already-exist";
					}
				//}
				//else
				//{
					//echo "unexpected-error";
				//}
			}
		}
	}
}
die();
?>