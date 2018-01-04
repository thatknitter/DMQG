<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
class elp_cls_dbquery
{
	// START Subscriber details /////////////////////////////////////////////////////////
	public static function elp_view_subscriber_search($search = "", $id = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_emaillist` where elp_email_mail <> '' ";
		if($search <> "")
		{
			$letter = explode(',', $search);
			$length = count($letter);
			for ($i = 0; $i < $length; $i++) 
			{
				if($i == 0)
				{
					$sSql = $sSql . " and";
				}
				else
				{
					$sSql = $sSql . " or";
				}
				$sSql = $sSql . " elp_email_mail LIKE '" . $letter[$i]. "%'";
			}
		}
		if($id > 0)
		{
			$sSql = $sSql . " and elp_email_id=".$id;
			
		}
		$sSql = $sSql . " order by elp_email_mail asc";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_view_subscriber_search2($search = "", $id = 0, $search_sts = "", $offset = 0, $limit = 0, $search_group = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_emaillist` where elp_email_mail <> '' ";
		if($search_sts <> "")
		{
			$sSql = $sSql . " and elp_email_status='".$search_sts."'";
		}
		if($search <> "" && $search <> "ALL")
		{
			$letter = explode(',', $search);
			$length = count($letter);
			for ($i = 0; $i < $length; $i++) 
			{
				if($i == 0)
				{
					$sSql = $sSql . " and (";
				}
				else
				{
					$sSql = $sSql . " or";
				}
				$sSql = $sSql . " elp_email_mail LIKE '" . $letter[$i]. "%'";
				if($i == $length-1)
				{
					$sSql = $sSql . ")";
				}
			}
		}
		if($search_group <> "" && $search_group <> "ALL")
		{
			$sSql = $sSql . " and elp_email_group='".$search_group."'";
		}
		if($id > 0)
		{
			$sSql = $sSql . " and elp_email_id=".$id;
			
		}
		$sSql = $sSql . " order by elp_email_mail asc";
		$sSql = $sSql . " LIMIT $offset, $limit";
		//echo $sSql;
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_view_subscriber_count($id = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($id > 0)
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_emaillist` WHERE `elp_email_id` = %d", array($id));
		}
		else
		{
			$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_emaillist`";
		}
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function elp_view_subscriber_count_bygroup($group = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($group <> "")
		{
			$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_emaillist` WHERE `elp_email_group` = %s";
			$sSql = $sSql . " and (elp_email_status = 'Confirmed' or elp_email_status = 'Single Opt In')";
			$sSql = $wpdb->prepare($sSql, array($group));
		}
		else
		{
			$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_emaillist`";
		}
		echo $sSql;
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function elp_view_subscriber_count_status($status = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($status <> "")
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_emaillist` WHERE `elp_email_status` = %s", array($status));
		}
		else
		{
			$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_emaillist`";
		}
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function elp_view_subscriber_delete($id = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sSql = $wpdb->prepare("DELETE FROM `".$prefix."elp_emaillist` WHERE `elp_email_id` = %d LIMIT 1", $id);
		$wpdb->query($sSql);
		return true;
	}
	
	public static function elp_view_subscriber_ins($data = array())
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = 0;

		if (!filter_var($data[1], FILTER_VALIDATE_EMAIL))
		{
			return "invalid";
		}
		
		$CurrentDate = date('Y-m-d G:i:s'); 
		$sSql = "SELECT COUNT(*) FROM `".$prefix."elp_emaillist` where elp_email_mail='".$data[1]."' and elp_email_group='".trim($data[3])."'";
		
		$result = $wpdb->get_var($sSql);
		if ( $result > 0)
		{
			return "ext";
		}
		else
		{
			$guid = elp_cls_common::elp_generate_guid(60);
			$sql = $wpdb->prepare("INSERT INTO `".$prefix."elp_emaillist` 
					(`elp_email_name`,`elp_email_mail`, `elp_email_status`, `elp_email_created`, `elp_email_viewcount`, `elp_email_group`, `elp_email_guid`)
					VALUES(%s, %s, %s, %s, %d, %s, %s)", array(trim($data[0]), trim($data[1]), trim($data[2]), $CurrentDate, 0, trim($data[3]), $guid));
			$wpdb->query($sql);
			return "sus";
		}
	}
	
	public static function elp_view_subscriber_upd($data = array())
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
			
		$sSql = "SELECT COUNT(*) FROM `".$prefix."elp_emaillist` where elp_email_mail='".$data[2]."'"; 
		$sSql = $sSql . " and elp_email_group='".trim($data[4])."' and elp_email_id <> ".$data[0];
		
		//echo "<br> ==> " . $sSql . "<br>";
		
		$result = $wpdb->get_var($sSql);
		
		//echo "<br> ==> " . $result . "<br>";
		
		if ( $result > 0)
		{
			return "ext";
		}
		else
		{
			$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_emaillist` SET `elp_email_name` = %s, `elp_email_mail` = %s,
					`elp_email_status` = %s, `elp_email_group` = %s WHERE elp_email_id = %d	LIMIT 1", array($data[1], $data[2], $data[3], $data[4], $data[0]));
			$wpdb->query($sSql);
			return "sus";
		}
	}
	
	public static function elp_view_subscriber_bulk($idlist = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_emaillist` where elp_email_mail <> '' ";
		if($idlist <> "")
		{
			$sSql = $sSql . " and elp_email_id in (" . $idlist. ");";
		}
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_view_subscriber_one($mail = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = $wpdb->prepare("SELECT * FROM `".$prefix."elp_emaillist` where elp_email_mail = %s", array($mail));
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_view_subscriber_upd_status($status = "", $idlist = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sSql = "UPDATE `".$prefix."elp_emaillist` SET `elp_email_status` = '".$status."'";
		$sSql = $sSql . " WHERE elp_email_id in (".$idlist.")";
		$wpdb->query($sSql);
		return "sus";
	}
	
	public static function elp_view_subscriber_upd_group($group = "", $idlist = "") 
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sSql = "UPDATE `".$prefix."elp_emaillist` SET `elp_email_group` = '".$group."'";
		$sSql = $sSql . " WHERE elp_email_id in (".$idlist.")";
		$wpdb->query($sSql);
		return "sus";
	}
	
	public static function elp_view_subscriber_job($status = "", $id = 0, $guid = "", $email = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		
		$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_emaillist`";
		$sSql = $sSql . " WHERE elp_email_id = %d";
		$sSql = $sSql . " and elp_email_mail = %s";
		$sSql = $sSql . " and elp_email_guid = %s Limit 1";
		$sSql = $wpdb->prepare($sSql, array($id, $email, $guid));
		$result = $wpdb->get_var($sSql);
		if ( $result > 0)
		{
			$sSql = "UPDATE `".$prefix."elp_emaillist` SET `elp_email_status` = %s";
			$sSql = $sSql . " WHERE elp_email_id = %d";
			$sSql = $sSql . " and elp_email_mail = %s";
			$sSql = $sSql . " and elp_email_guid = %s Limit 1";
			$sSql = $wpdb->prepare($sSql, array($status, $id, $email, $guid));
			$wpdb->query($sSql);
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function elp_view_subscriber_jobstatus($status = "", $id = 0, $guid = "", $email = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		
		$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_emaillist`";
		$sSql = $sSql . " WHERE elp_email_id = %d";
		$sSql = $sSql . " and elp_email_mail = %s";
		$sSql = $sSql . " and elp_email_status = %s";
		$sSql = $sSql . " and elp_email_guid = %s Limit 1";
		$sSql = $wpdb->prepare($sSql, array($id, $email, $status, $guid));
		$result = $wpdb->get_var($sSql);
		if ( $result > 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public static function elp_view_subscriber_widget($data = array())
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$currentdate = date('Y-m-d G:i:s'); 
		
		$sSql = "SELECT * FROM `".$prefix."elp_emaillist` WHERE";
		$sSql = $sSql . " elp_email_mail = %s and elp_email_group = %s";
		$sSql = $sSql . " Limit 1";
		$sSql = $wpdb->prepare($sSql, array($data[1], $data[2]));
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		
		//print_r($arrRes);
		
		
		if(count($arrRes) > 0)
		{
			if( $arrRes[0]['elp_email_status'] == "Confirmed" )
			{
				return "ext";
			}
			else
			{
				$action = "";
				$inputdata = array($arrRes[0]['elp_email_id'], $arrRes[0]['elp_email_name'], $arrRes[0]['elp_email_mail'], $data[2], $data[3]);
				$action = elp_cls_dbquery::elp_view_subscriber_upd($inputdata);
				return $action;
			}
		}
		else
		{
			$action = elp_cls_dbquery::elp_view_subscriber_ins($data);
			return $action;
		}
	}
	
	public static function elp_view_subscriber_cron($offset = 0, $limit = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_emaillist` where (elp_email_status = 'Confirmed' or elp_email_status = 'Single Opt In')";
		$sSql = $sSql . " order by elp_email_id asc limit %d, %d";
		$sSql = $wpdb->prepare($sSql, array($offset, $limit));
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_view_subscriber_bygroups($offset = 0, $limit = 0, $emaillistgroup)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_emaillist` where (elp_email_status = 'Confirmed' or elp_email_status = 'Single Opt In')";
		
		if($emaillistgroup <> "")
		{
			$groups = explode(',', $emaillistgroup);
			$length = count($groups);
			$sSqlsub = ""; 
			for ($i = 0; $i < $length; $i++) 
			{
				if($i == 0)
				{
					$sSql = $sSql . " and (";
					$sSqlsub = $sSqlsub . " where (";
				}
				else
				{
					$sSql = $sSql . " or";
					$sSqlsub = $sSqlsub . " or";
				}
				$sSql = $sSql . " elp_email_group = '" . trim($groups[$i]). "'";
				$sSqlsub = $sSqlsub . " elp_email_group = '" . trim($groups[$i]). "'";
			}
			$sSql = $sSql . ")";
			if($sSqlsub <> "");
			{
				$sSqlsub = $sSqlsub . ")";
			}
		}
		
		if($sSqlsub <> "")
		{
			//$sSql = $sSql . " and elp_email_id in (select max(elp_email_id) from ".$prefix."elp_emaillist " . $sSqlsub . " group by elp_email_mail)";
			$sSql = $sSql . " and elp_email_id in (select max(elp_email_id) from ".$prefix."elp_emaillist group by elp_email_mail)";
		}
		else
		{
			$sSql = $sSql . " and elp_email_id in (select max(elp_email_id) from ".$prefix."elp_emaillist group by elp_email_mail)";
		}
		$sSql = $sSql . " order by elp_email_mail asc";
		
		//echo $sSql;
		
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		
		//print_r($arrRes);
		
		return $arrRes;
	}
	
	public static function elp_view_subscriber_byonegroup($emaillistgroup)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_emaillist` where (elp_email_status = 'Confirmed' or elp_email_status = 'Single Opt In')";
		$sSql = $sSql . " and elp_email_group = '" . trim($emaillistgroup). "'";
		$sSql = $sSql . " and elp_email_id in (select max(elp_email_id) from ".$prefix."elp_emaillist where elp_email_group = '" . trim($emaillistgroup). "' group by elp_email_mail)";
		$sSql = $sSql . " order by elp_email_mail asc";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_view_subscriber_manual($recipients)
	{
		$recipient = implode(', ', $recipients);
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_emaillist` where (elp_email_status = 'Confirmed' or elp_email_status = 'Single Opt In')";
		$sSql = $sSql . " and elp_email_id in (".$recipient.")";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_view_subscriber_sendmail($search = "", $group = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_emaillist` where elp_email_mail <> '' ";
		if($search <> "" && $search <> "ALL")
		{
			$letter = explode(',', $search);
			$length = count($letter);
			for ($i = 0; $i < $length; $i++) 
			{
				if($i == 0)
				{
					$sSql = $sSql . " and (";
				}
				else
				{
					$sSql = $sSql . " or";
				}
				$sSql = $sSql . " elp_email_mail LIKE '" . $letter[$i]. "%'";
			}
			$sSql = $sSql . ")";
		}
		if($group <> "" && $group <> "0")
		{
			$sSql = $sSql . " and elp_email_group='".$group."'";
			
		}
		else
		{
			$sSql = $sSql . " and elp_email_id in (select max(elp_email_id) from ".$prefix."elp_emaillist group by elp_email_mail)";
		}
		$sSql = $sSql . " and (elp_email_status = 'Confirmed' or elp_email_status = 'Single Opt In')";
		$sSql = $sSql . " order by elp_email_mail asc";
		
		//echo "<br>".$sSql."<br>";
		
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_view_subscriber_group()
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT distinct(elp_email_group) FROM `".$prefix."elp_emaillist`";
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	// END Subscriber details /////////////////////////////////////////////////////////
	
	
	// START Template details /////////////////////////////////////////////////////////
	public static function elp_template_select($id = 0, $type = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_templatetable` where 1=1";
		
		if($type <> "")
		{
			$sSql = $sSql . " and elp_email_type='".$type."'";
		}
		
		if($id > 0)
		{
			$sSql = $sSql . " and elp_templ_id=".$id;
			//echo $sSql;
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else
		{
			//echo $sSql;
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		return $arrRes;
	}
	
	public static function elp_template_count($id = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($id > 0)
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_templatetable` WHERE `elp_templ_id` = %d", array($id));
		}
		else
		{
			$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_templatetable`";
		}
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function elp_template_delete($id = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sSql = $wpdb->prepare("DELETE FROM `".$prefix."elp_templatetable` WHERE `elp_templ_id` = %d LIMIT 1", $id);
		$wpdb->query($sSql);
		return true;
	}
	
	public static function elp_template_ins($data = array())
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = 0;
		$sSql = $wpdb->prepare("INSERT INTO `".$prefix."elp_templatetable` (`elp_templ_heading`,`elp_templ_header`, `elp_templ_body`, `elp_templ_footer`, `elp_templ_status`, `elp_email_type`)
				VALUES(%s, %s, %s, %s, %s, %s)", array(trim($data[0]), trim($data[1]), trim($data[2]), trim($data[3]), 'Ready', trim($data[4])));
		$wpdb->query($sSql);
		return true;
	}
	
	public static function elp_template_upd($data = array())
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_templatetable` SET `elp_templ_heading` = %s, `elp_templ_header` = %s, `elp_templ_body` = %s, 
				`elp_templ_footer` = %s	WHERE elp_templ_id = %d	LIMIT 1", array($data[1], $data[2], $data[3], $data[4], $data[0]));
		$wpdb->query($sSql);
		return "sus";
	}
	
	public static function elp_template_getimage($postid=0, $size='thumbnail', $attributes='')
	{
		if ($images = get_children(array(
			'post_parent' => $postid,
			'post_type' => 'attachment',
			'numberposts' => 1,
			'post_mime_type' => 'image',)))
			foreach($images as $image) 
			{
				$attachment = wp_get_attachment_image_src($image->ID, $size);
				return "<img src='". $attachment[0] . "' " . $attributes . " />";
			}
	}
	// END Template details /////////////////////////////////////////////////////////
	
	// START configuration details /////////////////////////////////////////////////////////
	public static function elp_configuration_select($id = 0, $offset = 0, $limit = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_sendsetting` where 1=1";
		if($id > 0)
		{
			$sSql = $sSql . " and elp_set_id=".$id;
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else
		{
			$sSql = $sSql . " order by elp_set_id asc limit $offset, $limit";
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		return $arrRes;
	}
	
	public static function elp_configuration_count($id = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($id > 0)
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_sendsetting` WHERE `elp_set_id` = %d", array($id));
		}
		else
		{
			$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_sendsetting`";
		}
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function elp_configuration_ins($action = "insert", $data = array())
	{
		global $wpdb;
		$prefix = $wpdb->prefix;

		//echo $action. "-<br>--";;;

		switch($action)
		{
			case 'insert':
				$guid = elp_cls_common::elp_generate_guid(60);
				$sSql = $wpdb->prepare("INSERT INTO `".$prefix."elp_sendsetting` 
						(`elp_set_guid`,`elp_set_name`, `elp_set_templid`, `elp_set_totalsent`, `elp_set_unsubscribelink`, `elp_set_viewstatus`, 
							`elp_set_postcount`, `elp_set_postcategory`, `elp_set_postorderby`, `elp_set_postorder`, 
							`elp_set_scheduleday`, `elp_set_scheduletime`, `elp_set_scheduletype`, `elp_set_status`, `elp_set_emaillistgroup`)
						VALUES(%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)", array($guid, trim($data[0]), trim($data[1]), trim($data[2]), trim($data[3]), trim($data[4]),
							trim($data[5]), trim($data[6]), trim($data[7]), trim($data[8]), $data[9], $data[10], $data[11], $data[12], $data[13] ));
				break;
				
			case 'update':
				$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_sendsetting` SET 
				`elp_set_name` = %s, 
				`elp_set_templid` = %s, 
				`elp_set_totalsent` = %s, 
				`elp_set_unsubscribelink` = %s, 
				`elp_set_viewstatus` = %s, 
				`elp_set_postcount` = %s, 
				`elp_set_postcategory` = %s, 
				`elp_set_postorderby` = %s, 
				`elp_set_postorder` = %s, 
				`elp_set_scheduleday` = %s, 
				`elp_set_scheduletime` = %s,
				`elp_set_scheduletype` = %s,
				`elp_set_status` = %s,
				`elp_set_emaillistgroup` = %s 
				WHERE elp_set_id = %d LIMIT 1", 
				array($data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $data[9], $data[10], $data[11], $data[12], $data[13], $data[14], $data[0]));
				break;
		}

		//echo $sSql;

		$wpdb->query($sSql);
		return true;
	}
	
	public static function elp_configuration_delete($id = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sSql = $wpdb->prepare("DELETE FROM `".$prefix."elp_sendsetting` WHERE `elp_set_id` = %d LIMIT 1", $id);
		$wpdb->query($sSql);
		return true;
	}
	
	public static function elp_configuration_cron($guid = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_sendsetting`";
		$sSql = $sSql . " where elp_set_guid = %s";
		$sSql = $wpdb->prepare($sSql, array($guid));
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_configuration_cron_trigger()
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$scheduleday = date("w");
		$sSql = "SELECT * FROM `".$prefix."elp_sendsetting` where elp_set_status='On'";
		$sSql = $sSql . " and elp_set_scheduletype = 'Cron'";
		$sSql = $sSql . " and elp_set_scheduleday like '%#".$scheduleday."#%'";
		$sSql = $sSql . " and DATE(elp_set_lastschedulerun) <> CURDATE() limit 0, 1";
		
		
		//echo $sSql . "<br><br>";
		
		$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		return $arrRes;
	}
	
	public static function elp_configuration_cron_trigger_update($guid = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$CurrentDate = date('Y-m-d G:i:s');
		$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_sendsetting` SET elp_set_lastschedulerun = %s WHERE elp_set_guid = %s LIMIT 1", $CurrentDate, $guid);
		
		//echo $sSql . "< == <br><br>";
		
		
		$wpdb->query($sSql);
		return true;
	}
	
	// END configuration details /////////////////////////////////////////////////////////
}
?>