<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
class elp_cls_dbquery2
{
	// Start - Sent details
	public static function elp_sentmail_select($id = 0, $offset = 0, $limit = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_sentdetails` where 1=1";
		if($id > 0)
		{
			$sSql = $sSql . " and elp_sent_id=".$id;
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else
		{
			$sSql = $sSql . " order by elp_sent_id desc limit $offset, $limit";
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		return $arrRes;
	}
	
	public static function elp_sentmail_count($id = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($id > 0)
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_sentdetails` WHERE `elp_sent_id` = %d", array($id));
		}
		else
		{
			$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_sentdetails`";
		}
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function elp_sentmail_delete($id = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sSql = $wpdb->prepare("DELETE FROM `".$prefix."elp_sentdetails` WHERE `elp_sent_id` = %d LIMIT 1", $id);
		$wpdb->query($sSql);
		return true;
	}
	
	public static function elp_sentmail_ins($guid = "", $qstring = 0, $source = "", $startdt = "", $enddt = "", $count = "", $preview = "", $sent_type = "", $subject = "")
	{
		global $wpdb;
		$returnid = 0;
		
		if($sent_type == "Cron Mail")
		{
			$status = "In Queue";
		}
		else
		{
			$status = "Sent";
		}
		
		if (strpos($preview, 'NO_POST_FOUND_FOR_THIS_MAIL_CONFIGURATION') !== false)
		{
			$status = "No Post";
			$currentdate = date('Y-m-d G:i:s');
			$preview = $preview . " @ " . $currentdate;
			$preview = $preview . " <br /> " . __( 'There is no post available for this configuration please check your mail configuration Post details, Post count and Post categories', 'email-posts-to-subscribers' ) ;
		}
		
		$prefix = $wpdb->prefix;
		$currentdate = date('Y-m-d G:i:s'); 
		$sSql = $wpdb->prepare("INSERT INTO `".$prefix."elp_sentdetails` (`elp_sent_guid`, `elp_sent_qstring`, `elp_sent_source`,
				`elp_sent_starttime`, `elp_sent_endtime`, `elp_sent_count`, `elp_sent_preview`, `elp_sent_status`, `elp_sent_type`, `elp_sent_subject`) 
				VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", array($guid, $qstring, $source, $startdt, $enddt, $count, $preview, $status, $sent_type, $subject));			
		
		//echo "<br><br><br>";
		//echo $sSql;
		
		$wpdb->query($sSql);
		return true;
	}
	
	public static function elp_sentmail_ups($guid = "")
	{
		global $wpdb;
		$returnid = 0;
		$prefix = $wpdb->prefix;
		$currentdate = date('Y-m-d G:i:s'); 
		$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_sentdetails` SET `elp_sent_endtime` = %s WHERE elp_sent_guid = %s LIMIT 1", array($currentdate, $guid));	
		$wpdb->query($sSql);
		return true;
	}
	
	public static function elp_sentmail_select_cron_trigger()
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_sentdetails` where elp_sent_status='In Queue'";
		$sSql = $sSql . " order by elp_sent_id asc limit 0, 1";
		
		//echo $sSql . "<br><br>";
		
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_sentmail_select_cron_update($guid = "")
	{
		global $wpdb;
		$returnid = 0;
		$prefix = $wpdb->prefix;
		$currentdate = date('Y-m-d G:i:s'); 
		
		$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_deliverreport` WHERE `elp_deliver_sentguid` = %s 
			and elp_deliver_senttype=%s and elp_deliver_sentstatus = %s", array($guid, "Cron Mail", "In Queue"));
		$deliverreport = $wpdb->get_var($sSql);
		
		if($deliverreport == 0)
		{
			$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_sentdetails` SET `elp_sent_status` = %s, `elp_sent_endtime` = %s 
							WHERE elp_sent_guid = %s LIMIT 1", array("Sent", $currentdate, $guid));	
			$wpdb->query($sSql);
		}
		
		return true;
	}
	// End - Sent details
	
	// Start - Delivery Report details
	public static function elp_delivery_select($sentguid = "", $offset = 0, $limit = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_deliverreport` where 1=1";
		if($sentguid <> "")
		{
			$sSql = $sSql . " and elp_deliver_sentguid='".$sentguid."'";
			$sSql = $sSql . " order by elp_deliver_id desc limit $offset, $limit";
		}
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_delivery_count($sentguid = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($sentguid <> "")
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_deliverreport` WHERE `elp_deliver_sentguid` = %s", array($sentguid));
		}
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function elp_delivery_ins($guid = "", $dbid = 0, $email = "", $sent_type = "")
	{
		global $wpdb;
		$returnid = 0;
		$prefix = $wpdb->prefix;
		$currentdate = date('Y-m-d G:i:s'); 
		
		if($sent_type == "Cron Mail")
		{
			$sentstatus = "In Queue";
			$senttype = "Cron Mail";
		}
		elseif($sent_type == "Instant Mail")
		{
			$sentstatus = "Sent";
			$senttype = "Instant Mail";
		}
		
		$sSql = $wpdb->prepare("INSERT INTO `".$prefix."elp_deliverreport` (`elp_deliver_sentguid`,`elp_deliver_emailid`, `elp_deliver_emailmail`,
								`elp_deliver_sentdate`,`elp_deliver_status`,`elp_deliver_sentstatus`,`elp_deliver_senttype`) VALUES 
								(%s, %s, %s, %s, %s, %s, %s)", array($guid, $dbid, $email, $currentdate, "Nodata", $sentstatus, $senttype));			
		$wpdb->query($sSql);
		
		//echo $sSql;
		
		$sSql = "SELECT MAX(elp_deliver_id) AS `count` FROM `".$prefix."elp_deliverreport`";
		$returnid = $wpdb->get_var($sSql);
		return $returnid;
	}
	
	public static function elp_delivery_ups($id = 0)
	{
		global $wpdb;
		$returnid = 0;
		$prefix = $wpdb->prefix;
		$currentdate = date('Y-m-d G:i:s'); 
		if(is_numeric($id))
		{
			$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_deliverreport` SET `elp_deliver_status` = %s, 
						`elp_deliver_viewdate` = %s WHERE elp_deliver_id = %d LIMIT 1", array("Viewed", $currentdate, $id));	
			$wpdb->query($sSql);
		}
		return true;
	}
	
	public static function elp_delivery_queue_count($guid = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;

		$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_deliverreport` WHERE `elp_deliver_sentguid` = %s 
				and elp_deliver_sentstatus = %s", array($guid, "In Queue"));
		$deliverreport = $wpdb->get_var($sSql);
			
		return $deliverreport;
	}
	
	public static function elp_delivery_completed_count($guid = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;

		$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_deliverreport` WHERE `elp_deliver_sentguid` = %s 
				and elp_deliver_sentstatus = %s", array($guid, "Sent"));
		$deliverreport = $wpdb->get_var($sSql);
			
		return $deliverreport;
	}
	
	public static function elp_delivery_select_cron_trigger($sentguid = "", $offset = 0, $limit = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_deliverreport` where elp_deliver_sentstatus='In Queue'";
		if($sentguid <> "")
		{
			$sSql = $sSql . " and elp_deliver_sentguid='".$sentguid."'";
			$sSql = $sSql . " order by elp_deliver_id asc limit $offset, $limit";
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		return $arrRes;
	}
	
	public static function elp_delivery_select_cron_update($elp_deliver_id = 0)
	{
		global $wpdb;
		$returnid = 0;
		$prefix = $wpdb->prefix;
		$currentdate = date('Y-m-d G:i:s'); 
		if(is_numeric($elp_deliver_id))
		{
			$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_deliverreport` SET `elp_deliver_sentstatus` = %s, 
						`elp_deliver_sentdate` = %s WHERE elp_deliver_id = %d LIMIT 1", array("Sent", $currentdate, $elp_deliver_id));	
			$wpdb->query($sSql);
		}
		return true;
	}
	
	public static function elp_delivery_select_cron_update_error($elp_deliver_id = 0)
	{
		global $wpdb;
		$returnid = 0;
		$prefix = $wpdb->prefix;
		$currentdate = date('Y-m-d G:i:s'); 
		if(is_numeric($elp_deliver_id))
		{
			$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_deliverreport` SET `elp_deliver_sentstatus` = %s, 
						`elp_deliver_sentdate` = %s WHERE elp_deliver_id = %d LIMIT 1", array("Error", $currentdate, $elp_deliver_id));	
			$wpdb->query($sSql);
		}
		return true;
	}
	// End - Delivery Report details
	
	// Start - Configuration details
	public static function elp_setting_select($id = 1)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_pluginconfig` where 1=1";
		$sSql = $sSql . " and elp_c_id=".$id;
		$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_setting_count($id = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($id > 0)
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_pluginconfig` WHERE `elp_c_id` = %s", array($id));
		}
		else
		{
			$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_pluginconfig`";
		}
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function elp_setting_update($data = array())
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_pluginconfig` SET 
								`elp_c_fromname` = %s, `elp_c_fromemail` = %s, `elp_c_mailtype` = %s,
								`elp_c_adminmailoption` = %s, `elp_c_adminemail` = %s, `elp_c_adminmailsubject` = %s,
								`elp_c_adminmailcontant` = %s, `elp_c_usermailoption` = %s, `elp_c_usermailsubject` = %s,
								`elp_c_usermailcontant` = %s, `elp_c_optinoption` = %s, `elp_c_optinsubject` = %s, `elp_c_optincontent` = %s,
								`elp_c_optinlink` = %s, `elp_c_unsublink` = %s, `elp_c_unsubtext` = %s, `elp_c_unsubhtml` = %s,
								`elp_c_subhtml` = %s, `elp_c_message1` = %s, `elp_c_message2` = %s
								WHERE elp_c_id = %d	LIMIT 1", 
								array($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8],$data[9], $data[10], 
										$data[11], $data[12], $data[13], $data[14], $data[15], $data[16], $data[17], $data[18], $data[19], $data[20], $data[0]));
		//echo $sSql;
		$wpdb->query($sSql);
		return "sus";
	}
	// End - Configuration details
}
?>