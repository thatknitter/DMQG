<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
class elp_cls_dbquerynote
{
	public static function elp_notification_select($id = 0)
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_postnotification` where 1=1";
		if($id > 0)
		{
			$sSql = $sSql . " and elp_note_id=".$id;
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else
		{
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		return $arrRes;
	}
	
	public static function elp_notification_select_guid($guid = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_postnotification` where 1=1";
		if($guid <> "")
		{
			$sSql = $sSql . " and elp_note_guid='".$guid."'";
			$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		}
		else
		{
			$arrRes = $wpdb->get_results($sSql, ARRAY_A);
		}
		//echo $sSql;
		return $arrRes;
	}
	
	public static function elp_notification_count($id = 0, $guid = "")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$result = '0';
		if($id > 0)
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_postnotification` WHERE `elp_note_id` = %d", array($id));
		}
		elseif($guid <> "")
		{
			$sSql = $wpdb->prepare("SELECT COUNT(*) AS `count` FROM `".$prefix."elp_postnotification` WHERE `elp_note_guid` = %s", array($guid));
		}
		else
		{
			$sSql = "SELECT COUNT(*) AS `count` FROM `".$prefix."elp_postnotification`";
		}
		//echo $sSql;
		$result = $wpdb->get_var($sSql);
		return $result;
	}
	
	public static function elp_notification_delete($id = 0) 
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$sSql = $wpdb->prepare("DELETE FROM `".$prefix."elp_postnotification` WHERE `elp_note_id` = %d LIMIT 1", $id);
		$wpdb->query($sSql);
		return true;
	}
	
	public static function elp_notification_ins($data = array(), $action = "insert")
	{
		global $wpdb;
		$prefix = $wpdb->prefix;	
		if($action == "insert") 
		{
			$sSql = $wpdb->prepare("INSERT INTO `".$prefix."elp_postnotification` (`elp_note_guid`,`elp_note_postcat`, 
			`elp_note_emailgroup`, `elp_note_mailsubject`, `elp_note_mailcontent`, `elp_note_status`, `elp_note_type`) VALUES (%s, %s, %s, %s, %s, %s, %s)", 
			array($data["elp_note_guid"], $data["elp_note_postcat"], $data["elp_note_emailgroup"], $data["elp_note_mailsubject"], 
			$data["elp_note_mailcontent"], $data["elp_note_status"], $data["elp_note_type"]));
		} 
		elseif($action == "update") 
		{
			$sSql = $wpdb->prepare("UPDATE `".$prefix."elp_postnotification` SET `elp_note_postcat` = %s, `elp_note_emailgroup` = %s, `elp_note_mailsubject` = %s, 
			`elp_note_mailcontent` = %s, `elp_note_status` = %s WHERE elp_note_guid = %s	LIMIT 1", 
			array($data["elp_note_postcat"], $data["elp_note_emailgroup"], $data["elp_note_mailsubject"], 
			$data["elp_note_mailcontent"], $data["elp_note_status"], $data["elp_note_guid"]));
		}
		$wpdb->query($sSql);
		return true;
	}
	
	public static function elp_notification_preview($guid_id)
	{
		$data = array();
		$data = elp_cls_dbquerynote::elp_notification_select_guid($guid_id);
		
		$args = array( 'numberposts' => '1' );
		$recent_posts = wp_get_recent_posts( $args );
		
		//print_r($recent_posts);

		if(count($data) > 0 && count($recent_posts) > 0)
		{
			echo "<div style='padding:15px;background-color:#FFFFFF;'>";
			
			$elp_note_mailsubject = $data['elp_note_mailsubject'];
			$elp_note_mailsubject = str_replace("###POSTTITLE###", $recent_posts[0]['post_title'], $elp_note_mailsubject);
			echo $elp_note_mailsubject;
			
			echo "</div>";
			
			echo "<br>";
			
			echo "<div style='padding:15px;background-color:#FFFFFF;'>";
			
			$content = $data['elp_note_mailcontent'];
			$content = str_replace("\r\n", "<br />", $content);
			
			// Get post excerpt
			$excerpt_length = 50; 
			$the_excerpt = $recent_posts[0]['post_content'];
			$the_excerpt = strip_tags(strip_shortcodes($the_excerpt));
			$words = explode(' ', $the_excerpt, $excerpt_length + 1);
			if(count($words) > $excerpt_length) {
				array_pop($words);
				array_push($words, '...');
				$the_excerpt = implode(' ', $words);
			}
			
			$content = str_replace("###NAME###", "Admin", $content);
			$content = str_replace("###POSTTITLE###", $recent_posts[0]['post_title'], $content);
			$content = str_replace("###POSTDESC###", $the_excerpt, $content);
			
			$post_content_no_html = strip_tags(strip_shortcodes($recent_posts[0]['post_content']));
			$content = str_replace("###POSTFULL###", $post_content_no_html, $content);
			
			$post_link = get_permalink($recent_posts[0]['ID']);
			$post_link_with_title = "<a href='".$post_link."' target='_blank'>".$recent_posts[0]['post_title']."</a>";
			$content = str_replace("###POSTLINK###", $post_link, $content);
			$content = str_replace("###POSTLINK-ONLY###", $post_link, $content);
			$content = str_replace("###POSTLINK-WITHTITLE###", $post_link_with_title, $content);
			
			$post_thumbnail = "";
			if ( (function_exists('has_post_thumbnail')) && (has_post_thumbnail($recent_posts[0]['ID'])) ) 
			{
				$post_thumbnail = get_the_post_thumbnail( $recent_posts[0]['ID'], 'medium' );
			}
			
			$post_thumbnail_link = "";
			if($post_thumbnail <> "") 
			{
				$post_thumbnail_link = "<a href='".$post_link."' target='_blank'>".$post_thumbnail."</a>";
			}
			$content = str_replace("###POSTIMAGE###", $post_thumbnail_link, $content);
			
			if(strlen($recent_posts[0]['post_modified']) > 10)
			{
				$post_modified = substr($recent_posts[0]['post_modified'], 0, 10);
			}
			else
			{
				$post_modified = "";
			}
			$content = str_replace("###DATE###", $post_modified, $content);
			
			$content = nl2br($content);
			echo stripslashes($content);
			
			echo "</div>";
			
		}
	}
	
//	public static function elp_notification_preparemail($post_id, $subject, $content)
//	{
//		$data = array();
//		$data = elp_cls_dbquerynote::elp_notification_select_guid($guid_id);
//		
//		$args = array( 'numberposts' => '1' );
//		$recent_posts = wp_get_recent_posts( $args );
//		
//		//print_r($recent_posts);
//
//		if(count($data) > 0 && count($recent_posts) > 0)
//		{
//			echo "<div style='padding:15px;background-color:#FFFFFF;'>";
//			
//			$elp_note_mailsubject = $data['elp_note_mailsubject'];
//			$elp_note_mailsubject = str_replace("###POSTTITLE###", $recent_posts[0]['post_title'], $elp_note_mailsubject);
//			echo $elp_note_mailsubject;
//			
//			echo "</div>";
//			
//			echo "<br>";
//			
//			echo "<div style='padding:15px;background-color:#FFFFFF;'>";
//			
//			$content = $data['elp_note_mailcontent'];
//			$content = str_replace("\r\n", "<br />", $content);
//			
//			// Get post excerpt
//			$excerpt_length = 50; 
//			$the_excerpt = $recent_posts[0]['post_content'];
//			$the_excerpt = strip_tags(strip_shortcodes($the_excerpt));
//			$words = explode(' ', $the_excerpt, $excerpt_length + 1);
//			if(count($words) > $excerpt_length) {
//				array_pop($words);
//				array_push($words, '...');
//				$the_excerpt = implode(' ', $words);
//			}
//			
//			$content = str_replace("###NAME###", "Admin", $content);
//			$content = str_replace("###POSTTITLE###", $recent_posts[0]['post_title'], $content);
//			$content = str_replace("###POSTDESC###", $the_excerpt, $content);
//			
//			$post_content_no_html = strip_tags(strip_shortcodes($recent_posts[0]['post_content']));
//			$content = str_replace("###POSTFULL###", $post_content_no_html, $content);
//			
//			$post_link = get_permalink($recent_posts[0]['ID']);
//			$post_link_with_title = "<a href='".$post_link."' target='_blank'>".$recent_posts[0]['post_title']."</a>";
//			$content = str_replace("###POSTLINK###", $post_link, $content);
//			$content = str_replace("###POSTLINK-ONLY###", $post_link, $content);
//			$content = str_replace("###POSTLINK-WITHTITLE###", $post_link_with_title, $content);
//			
//			$post_thumbnail = "";
//			if ( (function_exists('has_post_thumbnail')) && (has_post_thumbnail($recent_posts[0]['ID'])) ) 
//			{
//				$post_thumbnail = get_the_post_thumbnail( $recent_posts[0]['ID'], 'medium' );
//			}
//			
//			$post_thumbnail_link = "";
//			if($post_thumbnail <> "") 
//			{
//				$post_thumbnail_link = "<a href='".$post_link."' target='_blank'>".$post_thumbnail."</a>";
//			}
//			$content = str_replace("###POSTIMAGE###", $post_thumbnail_link, $content);
//			
//			if(strlen($recent_posts[0]['post_modified']) > 10)
//			{
//				$post_modified = substr($recent_posts[0]['post_modified'], 0, 10);
//			}
//			else
//			{
//				$post_modified = "";
//			}
//			$content = str_replace("###DATE###", $post_modified, $content);
//			
//			$content = nl2br($content);
//			echo stripslashes($content);
//			
//			echo "</div>";
//			
//		}
//	}
	
	public static function elp_notification_prepare($post_id = 0) 
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$arrNotification = array();

		if($post_id > 0) 
		{
			$post_type = get_post_type( $post_id );
			$sSql = "SELECT * FROM `".$prefix."elp_postnotification` where (elp_note_status = 'Enable' or elp_note_status = 'Cron') ";
			if($post_type == "post") 
			{
				$category = get_the_category( $post_id );
				$totcategory = count($category);
				if ( $totcategory > 0) 
				{
					for($i=0; $i<$totcategory; $i++) 
					{				
						if($i == 0) 
						{
							$sSql = $sSql . " and (";
						} 
						else 
						{
							$sSql = $sSql . " or";
						}
						$sSql = $sSql . " elp_note_postcat LIKE '%##" . htmlspecialchars_decode($category[$i]->cat_name). "##%'";
						if($i == ($totcategory-1)) 
						{
							$sSql = $sSql . ")";
						}
					}
					//echo "1<br>";
					//echo $sSql;
					$arrNotification = $wpdb->get_results($sSql, ARRAY_A);
				}
			} 
			else 
			{
				$sSql = $sSql . " and elp_note_postcat LIKE '%##{T}" . $post_type . "{T}##%'";
				//echo "2<br>";
				//echo $sSql;
				$arrNotification = $wpdb->get_results($sSql, ARRAY_A);
			}
		}
		
		return $arrNotification;
	}
	
	public static function elp_notification_subscribers($arrNotification = array()) 
	{
		global $wpdb;
		$prefix = $wpdb->prefix;
		$subscribers = array();
		$totnotification = count($arrNotification);
		if($totnotification > 0) 
		{
			$sSql = "SELECT * FROM `".$prefix."elp_emaillist` where elp_email_mail <> '' ";
			for($i=0; $i<$totnotification; $i++) 
			{
				if($i == 0) 
				{
					$sSql = $sSql . " and (";
				} 
				else 
				{
					$sSql = $sSql . " or";
				}
				$sSql = $sSql . " elp_email_group = '" . $arrNotification[$i]['elp_note_emailgroup']. "'";
				if($i == ($totnotification-1)) 
				{
					$sSql = $sSql . ")";
				}
			}
			$sSql = $sSql . " and (elp_email_status = 'Confirmed' or elp_email_status = 'Single Opt In')";
			$sSql = $sSql . " order by elp_email_mail asc";
			//echo "<br><br>";
			//echo $sSql;
			//echo "<br><br>";
			$subscribers = $wpdb->get_results($sSql, ARRAY_A);
		}
		return $subscribers;
	}
	
	public static function elp_prepare_notification( $post_status, $original_post_status, $post_id ) 
	{	
		if( ( $post_status == 'publish' || $post_status == 'private' ) && ( $original_post_status != 'publish' && $original_post_status != 'private' ) ) 
		{
			// $post_id is Object type containing the post information 
			// Thus we need to get post_id from $post_id object
			if(is_numeric($post_id)) 
			{
				$post_id = $post_id;
			} 
			else 
			{
				if(is_object($post_id)) 
				{
					$post_id = $post_id->ID;
				} 
				else 
				{
					$post_id = $post_id;
				}
			}

			
			try
			{
				$notification = array();
				$notification = elp_cls_dbquerynote::elp_notification_prepare($post_id);
				$notificationcount = count($notification);
				
				if ( $notificationcount > 0 ) 
				{
					$post = get_post($post_id);
					for($i=0; $i<$notificationcount; $i++) 
					{
						$group = $notification[$i]['elp_note_emailgroup'];
						$subject = $notification[$i]['elp_note_mailsubject'];
						$content = stripslashes($notification[$i]['elp_note_mailcontent']);
						
						$subscribers = array();
						$subscribers = elp_cls_dbquery::elp_view_subscriber_byonegroup($group);
						if(count($subscribers) > 0)
						{
							$sent_type = "";
							if($notification[$i]['elp_note_status'] == "Enable")
							{
								$sent_type = "Instant Mail";
							}
							elseif($notification[$i]['elp_note_status'] == "Cron")
							{
								$sent_type = "Cron Mail";
							}
							
							$subject = str_replace("###POSTTITLE###", $post->post_title, $subject);
							
							$content = str_replace("\r\n", "<br />", $content);
							
							// Get post excerpt
							$excerpt_length = 50; 
							$the_excerpt = $post->post_content;
							$the_excerpt = strip_tags(strip_shortcodes($the_excerpt));
							$words = explode(' ', $the_excerpt, $excerpt_length + 1);
							if(count($words) > $excerpt_length) {
								array_pop($words);
								array_push($words, '...');
								$the_excerpt = implode(' ', $words);
							}
							
							//$content = str_replace("###NAME###", "Admin", $content);
							$content = str_replace("###POSTTITLE###", $post->post_title, $content);
							$content = str_replace("###POSTDESC###", $the_excerpt, $content);
							
							$post_content_no_html = strip_tags(strip_shortcodes($post->post_content));
							$content = str_replace("###POSTFULL###", $post_content_no_html, $content);
							
							$post_link = get_permalink($post_id);
							$post_link_with_title = "<a href='".$post_link."' target='_blank'>".$post->post_title."</a>";
							$content = str_replace("###POSTLINK###", $post_link, $content);
							$content = str_replace("###POSTLINK-ONLY###", $post_link, $content);
							$content = str_replace("###POSTLINK-WITHTITLE###", $post_link_with_title, $content);
							
							$post_thumbnail = "";
							if ( (function_exists('has_post_thumbnail')) && (has_post_thumbnail($post_id)) ) {
								$post_thumbnail = get_the_post_thumbnail( $post_id, 'medium' );
							}
							
							$post_thumbnail_link = "";
							if($post_thumbnail <> "") {
								$post_thumbnail_link = "<a href='".$post_link."' target='_blank'>".$post_thumbnail."</a>";
							}
							$content = str_replace("###POSTIMAGE###", $post_thumbnail_link, $content);
							
							if(strlen($post->post_modified) > 10) {
								$post_modified = substr($post->post_modified, 0, 10);
							}
							else {
								$post_modified = "";
							}
							$content = str_replace("###DATE###", $post_modified, $content);
							
							elp_cls_sendmail::elp_sendmail("notification", $subject, $content, $subscribers, "notification", $sent_type);
						}
					}
				}
			} 
			catch (Exception $e) 
			{
				//echo 'Caught exception: ',  $e->getMessage(), "\n";
			}
		}
	}
	
	public static function elp_notification_default()
	{
		$result = elp_cls_dbquerynote::elp_notification_count(0);
		
		if ($result == 0) 
		{
			$form['elp_note_guid'] 			= elp_cls_common::elp_generate_guid(60);
			
			$listcategory = "";
			$args = array( 'hide_empty' => 0, 'orderby' => 'name', 'order' => 'ASC' );
			$categories = get_categories($args); 
			$total = count($categories);
			$i = 1;
			foreach($categories as $category) 
			{
				$listcategory = $listcategory . " ##" . $category->cat_name . "## ";
				if($i < $total) 
				{
					$listcategory = $listcategory .  "--";
				}
				$i = $i + 1;
			}
			
			$form['elp_note_postcat'] 		= $listcategory;
			$form['elp_note_emailgroup'] 	= "Public";
			$form['elp_note_mailsubject'] 	= 'New post published ###POSTTITLE###';
			$elp_body = "Hello ###NAME###,\r\n\r\n";
			$elp_body = $elp_body . "We have published new blog in our website. ###POSTTITLE###\r\n";
			$elp_body = $elp_body . "###POSTDESC###\r\n";
			$elp_body = $elp_body . "You may view the latest post at ";
			$elp_body = $elp_body . "###POSTLINK###\r\n";
			$elp_body = $elp_body . "You received this e-mail because you asked to be notified when new updates are posted.\r\n\r\n";
			$elp_body = $elp_body . "Thanks & Regards\r\n";
			$elp_body = $elp_body . "Admin";
			$form['elp_note_mailcontent'] 	= $elp_body;
			$form['elp_note_status'] 		= 'Disable';
			$form['elp_note_type'] 			= 'Notification';
			$action = elp_cls_dbquerynote::elp_notification_ins($form, $action = "insert");
		}
	}
}
?>