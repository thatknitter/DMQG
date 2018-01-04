<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
class elp_cls_newsletter
{
	public static function elp_template_compose($id = 0, $post = 5, $cat = "", $orderby = "", $order = "DESC", $action = "preview")
	{
		$excerpt_length = 50; // Change this value to increase the content length in newsletter.
		global $wpdb;
		$prefix = $wpdb->prefix;
		$preview = "";
		$arrRes = array();
		$sSql = "SELECT * FROM `".$prefix."elp_templatetable` where 1=1";
		$sSql = $sSql . " and elp_templ_id=".$id;
		$arrRes = $wpdb->get_row($sSql, ARRAY_A);
		
		if(count($arrRes) > 0)
		{
			$elp_templ_heading = stripslashes($arrRes['elp_templ_heading']);
			$elp_templ_header = stripslashes($arrRes['elp_templ_header']);
			$elp_templ_body = stripslashes($arrRes['elp_templ_body']);
			$elp_templ_footer = stripslashes($arrRes['elp_templ_footer']);
			$elp_templ_status = $arrRes['elp_templ_status'];
			
			$replacefrom = array("<ul><br />", "</ul><br />", "<li><br />", "</li><br />", "<ol><br />", "</ol><br />", "</h2><br />", "</h1><br />", "</h3><br />");
			$replaceto = array("<ul>", "</ul>", "<li>" ,"</li>", "<ol>", "</ol>", "</h2>", "</h1>", "</h3>");
			
			$replacefrom1 = array("<ul><br>", "</ul><br>", "<li><br>", "</li><br>", "<ol><br>", "</ol><br>", "</h2><br>", "</h1><br>", "</h3><br>", "<br>");
			$replaceto1 = array("<ul>", "</ul>", "<li>" ,"</li>", "<ol>", "</ol>", "</h2>", "</h1>", "</h3>", "");
		
			//$preview = "<html>";
			//$preview = $preview . "<head><title>" . $elp_templ_heading . "</title></head>";
			//$preview = $preview . "<body>";
			$preview = $preview . '<div style="clear:both;"></div>';
			$preview = $preview . $elp_templ_header;
			$preview = $preview . '<div style="clear:both;"></div>';
			//-----------------------------------------------------------
			$body = $elp_templ_body;
			$post_id  = "";
			$post_title  = "";
			$post_excerpt  = "";
			$post_link  = "";
			$post_thumbnail  = "";
			$post_thumbnail_link  = "";
			$post_date = "";
			$post_author = "";
			$i = 1;
			
			//$qstring = "posts_per_page=".$post."&post_status=publish&category='".$cat."'&orderby='".$orderby."'&order='".$order."'";
			
			$date_from_to = array();
			$date_from_to = elp_cls_newsletter::elp_daterange_calculation($post);
			
			if ($post >= 100 )
			{
				$qstring = array(
					'posts_per_page'   => 100,
					'post_status'      => 'publish',
					'category'         => "'".$cat."'",
					'orderby'          => "'".$orderby."'",
					'order'            => "'".$order."'",
					'date_query' => array(array('after'  => $date_from_to["date_after"], 'before' => $date_from_to["date_before"], 'inclusive' => true ),)
					);
			}
			else
			{
				$qstring = array(
					'posts_per_page'   => $post,
					'post_status'      => 'publish',
					'category'         => "'".$cat."'",
					'orderby'          => "'".$orderby."'",
					'order'            => "'".$order."'"
					);
			}
			
			$postlist  = get_posts( $qstring );
			
			if(count($postlist) == 0 and $action <> "preview") // No post details for this fielter field
			{
				$preview = "NO_POST_FOUND_FOR_THIS_MAIL_CONFIGURATION";
				return $preview;
			}
			
			$posts = array();
			foreach ( $postlist as $post ) 
			{
				setup_postdata($post);
				$post_id = $post->ID;
				$post_title = $post->post_title;
				$post_author = get_the_author();
				$post_date = $post->post_modified;
				$post_category = $post->category;
								
				// Get full post
				$post_full = $post->post_content;
				$post_full = str_replace($replacefrom, $replaceto, $post_full);
				$post_full = str_replace($replacefrom1, $replaceto1, $post_full);
				$post_full = wpautop($post_full);
				
				$post_excerpt = elp_cls_newsletter::elp_excerpt_by_id($post_id, $excerpt_length);
				$post_excerpt = wpautop($post_excerpt);
				$post_link = get_permalink($post_id);	
					
				if (  (function_exists('has_post_thumbnail')) && (has_post_thumbnail($post_id)))
				{
					$post_thumbnail = get_the_post_thumbnail($post_id, 'thumbnail');
				}
				
				if($post_thumbnail <> "")
				{
					$post_thumbnail_link = "<a href='".$post_link."' target='_blank'>".$post_thumbnail."</a>";
				}
				
				if($post_title <> "")
				{
					$post_title = "<a href='".$post_link."' target='_blank' style=''>".$post_title."</a>";
				}
				
				$bodyown = str_replace('###POSTTITLE###', $post_title, $body);
				$bodyown = str_replace('###POSTIMAGE###', $post_thumbnail_link, $bodyown);
				$bodyown = str_replace('###POSTDESC###', $post_excerpt, $bodyown);
				$bodyown = str_replace('###POSTFULL###', $post_full, $bodyown);
				$bodyown = str_replace('###DATE###', $post_date, $bodyown);
				$bodyown = str_replace('###AUTHOR###', $post_author, $bodyown);
				
				//$preview = $preview . '<div style="clear:both;"></div>';
				$preview = $preview . $bodyown;
				$preview = $preview . '<div style="clear:both;"></div>';
				
				$post_id  = "";
				$post_title  = "";
				$post_excerpt  = "";
				$post_link  = "";
				$post_thumbnail  = "";
				$post_thumbnail_link  = "";
				$post_date = "";
				$post_author = "";
				$i = $i + 1;
			}		
			wp_reset_postdata();
			wp_reset_query();
			//-----------------------------------------------------------
			$preview = $preview . '<div style="clear:both;"></div>';
			$preview = $preview . $elp_templ_footer;
			$preview = $preview . '<div style="clear:both;"></div>';
			
			if($action == "preview")
			{
				$preview = str_replace('###EMAIL###', "useremail@email.com", $preview);
				$preview = str_replace('###NAME###', "User Name", $preview);
			}
			//$preview = $preview . "<body>";
			//$preview = $preview . "</html>";
			$preview = str_replace("\r\n", "<br />", $preview);
		}
			
		$preview = str_replace($replacefrom, $replaceto, $preview);
		$preview = str_replace($replacefrom1, $replaceto1, $preview);
		
		return $preview;
	}
	
	public static function elp_excerpt_by_id($post_id, $excerpt_length)
	{
		$the_post = get_post($post_id);
		$the_excerpt = $the_post->post_content;
		$the_excerpt = strip_tags(strip_shortcodes($the_excerpt));
		$words = explode(' ', $the_excerpt, $excerpt_length + 1);
		if(count($words) > $excerpt_length)
		{
			array_pop($words);
			array_push($words, '...');
			$the_excerpt = implode(' ', $words);
		}
		$the_excerpt = nl2br($the_excerpt);
		$the_excerpt = str_replace("<br>", " ", $the_excerpt);
		$the_excerpt = str_replace("<br />", " ", $the_excerpt);
		$the_excerpt = str_replace("\r\n", " ", $the_excerpt);
		return $the_excerpt;
	}
	
	public static function elp_daterange_calculation($post)
	{
		$date_from_to = array();
		$date_before = date("Y-m-d");
		$date_after = date("Y-m-d");
		
		if($post == 100) // Published Today
		{
			$date_before = date("Y-m-d");
			$date_after = date("Y-m-d");
		}
		elseif($post == 110) // Published Last 2 Days
		{
			$date_before = date("Y-m-d");
			$date_after = date("Y-m-d",strtotime("-1 day"));
		}
		elseif($post == 120) // Published Last 3 Days
		{
			$date_before = date("Y-m-d");
			$date_after = date("Y-m-d",strtotime("-2 day"));
		}
		elseif($post == 130) // Published Last 4 Days
		{
			$date_before = date("Y-m-d");
			$date_after = date("Y-m-d",strtotime("-3 day"));
		}
		elseif($post == 140) // Published Last 5 Days
		{
			$date_before = date("Y-m-d");
			$date_after = date("Y-m-d",strtotime("-4 day"));
		}
		elseif($post == 150) //Published Last 6 Days
		{
			$date_before = date("Y-m-d");
			$date_after = date("Y-m-d",strtotime("-5 day"));
		}
		elseif($post == 180) //Published Last 7 Days
		{
			$date_before = date("Y-m-d");
			$date_after = date("Y-m-d",strtotime("-6 day"));
		}
		elseif($post == 190) //Published Last 8 Days
		{
			$date_before = date("Y-m-d");
			$date_after = date("Y-m-d",strtotime("-7 day"));
		}
		elseif($post == 200) //Published Last 9 Days
		{
			$date_before = date("Y-m-d");
			$date_after = date("Y-m-d",strtotime("-8 day"));
		}
		elseif($post == 160) // This Week
		{
			$date_before = date("Y-m-d");
			if(date("w") > 0) // Today is not sunday
			{
				$date_after = date("Y-m-d",strtotime("Last Sunday"));
			}
			else
			{
				$date_after = date("Y-m-d");
			}
		}
		elseif($post == 170) // This Month
		{
			$date_before = date("Y-m-d");
			$date_after = date("Y-m-01");
		}
			
		$date_from_to["date_before"] = $date_before;
		$date_from_to["date_after"] = $date_after;
		return $date_from_to;
	}
}
?>