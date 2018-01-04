<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if(isset($_GET['elp']))
{
	if($_GET['elp'] == "cron")
	{
		require_once(ELP_DIR.'classes'.DIRECTORY_SEPARATOR.'stater.php');
		$noerror = true;

		$elp_c_cronguid = isset($_GET['guid']) ? sanitize_text_field($_GET['guid']) : '';
		$elp_c_cronguid = trim($elp_c_cronguid);
	
		$security1 = strlen($elp_c_cronguid);
		$elp_c_cronguid_noslash = str_replace("-", "", $elp_c_cronguid);
		$security2 = strlen($elp_c_cronguid_noslash);
		if( $security1 == 34 && $security2 == 30 )
		{
			if (!preg_match('/[^a-z]/', $elp_c_cronguid_noslash))
			{
				$elp_c_cronurl = get_option('elp_c_cronurl');	
				$elp_c_croncount = get_option('elp_cron_mailcount');
				parse_str($elp_c_cronurl, $output);
				if($elp_c_cronguid == $output['guid'])
				{
					if(!is_numeric($elp_c_croncount))
					{
						$elp_c_croncount = 50;
					}
					
					$data = array();
					$data = elp_cls_dbquery::elp_configuration_cron_trigger();
					
					if(count($data) > 0)
					{						
						$subject = $data[0]['elp_set_name'];
						$content = elp_cls_newsletter::elp_template_compose($data[0]['elp_set_templid'], $data[0]['elp_set_postcount'], 
								$data[0]['elp_set_postcategory'], $data[0]['elp_set_postorderby'], $data[0]['elp_set_postorder'], "send");
						
						if($content == "NO_POST_FOUND_FOR_THIS_MAIL_CONFIGURATION")
						{
							$sendguid = elp_cls_common::elp_generate_guid(60);
							$currentdate = date('Y-m-d G:i:s');
							elp_cls_dbquery2::elp_sentmail_ins($sendguid, 0, "cron", $currentdate, $currentdate, 0, $content, "Cron Mail", $subject);
						}
						else
						{
							if(!is_numeric($data[0]['elp_set_totalsent']))
							{
								$elp_set_totalsent = 9999;
							}
							else
							{
								$elp_set_totalsent = $data[0]['elp_set_totalsent'];
							}
							
							$elp_set_emaillistgroup = $data[0]['elp_set_emaillistgroup'];
							if($elp_set_emaillistgroup == "")
							{
								$elp_set_emaillistgroup = "Public";
							}
							
							elp_cls_sendmail::elp_prepare_newsletter($subject, $content, 1, $elp_set_totalsent, $elp_set_emaillistgroup);
							elp_cls_dbquery::elp_configuration_cron_trigger_update($data[0]['elp_set_guid']);
						}
					}
					else
					{
						$sentmail = array();
						$sentmail = elp_cls_dbquery2::elp_sentmail_select_cron_trigger();
						
						if(count($sentmail) > 0)
						{		
							$delivery = array();
							$delivery = elp_cls_dbquery2::elp_delivery_select_cron_trigger($sentmail[0]['elp_sent_guid'], 0, $elp_c_croncount);
							elp_cls_sendmail::elp_sendmail_cron_trigger("newsletter", $sentmail[0]['elp_sent_subject'], $sentmail[0]['elp_sent_preview'], $delivery);
							elp_cls_dbquery2::elp_sentmail_select_cron_update($sentmail[0]['elp_sent_guid']);
						}
					}
				}		
			}
		}
	}
}
die();
?>