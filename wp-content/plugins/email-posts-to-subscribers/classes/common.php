<?php
class elp_cls_common
{
	public static function elp_disp_status($value)
	{
		$returnstring = "";
		switch ($value) 
		{
			case "Confirmed":
				$returnstring = '<span style="color:#006600;font-weight:bold;">Confirmed</span>';
				break;
			case "Unconfirmed":
				$returnstring = '<span style="color:#FF0000">Unconfirmed</span>';
				break;
			case "Unsubscribed":
				$returnstring = '<span style="color:#999900">Unsubscribed</span>';
				break;
			case "Single Opt In":
				$returnstring = '<span style="color:#0000FF">Single Opt In</span>';
				break;
			case "Viewed":
				$returnstring = '<span style="color:#00CC00;font-weight:bold">Viewed</span>';
				break;
			case "Nodata":
				$returnstring = '<span style="color:#999900;">Nodata</span>';
				break;
			case "Off":
				$returnstring = '<span style="color:#FF0000">Off</span>';
				break;
			case "On":
				$returnstring = '<span style="color:#00CC00">On</span>';
				break;
			case "Disable":
				$returnstring = '<span style="color:#FF0000">Disable</span>';
				break;
			case "In Queue":
				$returnstring = '<span style="color:#FF0000;font-weight:bold;">In Queue</span>';
				break;
			case "Sent":
				$returnstring = '<span style="color:#00FF00;font-weight:bold;">Sent</span>';
				break;
			case "Cron Mail":
				$returnstring = '<span style="color:#ffd700;font-weight:bold;">Cron Mail</span>';
				break;	
			case "Instant Mail":
				$returnstring = '<span style="color:#993399;">Instant Mail</span>';
				break;
			case "Error":
				$returnstring = '<span style="color:#FF0000;" title="Subscriber is not available (Already deleted)">Error</span>';
				break;
			case "No Post":
				$returnstring = '<span style="color:#CC00FF;font-weight:bold;" title="For more info click preview icon of this record">No Post</span>';
				break;
			default:
       			$returnstring = $value;
		}
		return $returnstring;
	}
	
	public static function elp_readcsv($csvFile)
	{
		$file_handle = fopen($csvFile, 'r');
		while (!feof($file_handle) ) 
		{
			$line_of_text[] = fgetcsv($file_handle, 1024);
		}
		fclose($file_handle);
		return $line_of_text;
	}
	
	public static function elp_txt_clean($excerpt, $substr=0) 
	{
		$string = strip_tags(str_replace('[...]', '...', $excerpt));
		if ($substr>0) 
		{
			$string = substr($string, 0, $substr);
		}
		return $string;
	}
	
	public static function elp_generate_guid($length = 30) 
	{
		$guid = rand();
		$length = 6;
		$rand1 = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
		$rand2 = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
		$rand3 = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
		$rand4 = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
		$rand5 = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
		$rand6 = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, $length);
		$guid = $rand1."-".$rand2."-".$rand3."-".$rand4."-".$rand5;
		return $guid;
	}	
	
	public static function elp_client_os() 
	{
		$http_user_agent = $_SERVER['HTTP_USER_AGENT'];
		return $http_user_agent;
	}
	
	public static function download($arrays, $filename = 'output.csv', $option) 
	{
		$string = '';
		$c=0;
		$filename = 'email-posts-to-subscribers'.$option.'_'.date('Ymd_His').".csv";
		foreach($arrays AS $array) 
		{
			$val_array = array();
			$key_array = array();
			foreach($array AS $key => $val) 
			{
				$key_array[] = $key;
				$val = str_replace('"', '""', $val);
				$val_array[] = "\"$val\"";
			}
			if($c == 0) 
			{
				$string .= implode(",", $key_array)."\n";
			}
			$string .= implode(",", $val_array)."\n";
			$c++;
		}
		ob_clean();
		header('Content-type: application/ms-excel');
		header('Content-Disposition: attachment; filename='.$filename);
		echo $string;
	}
	
	public static function elp_sent_report_subject() 
	{
		$report = "[Email Posts WP Plugin] Newsletter Report";
		return $report;
	}
	
	public static function elp_sent_report_plain() 
	{
		$report = "";
		$report = $report. "Hi Admin,\n\n";
		$report = $report. "Newsletter has been sent/triggered successfully to ###COUNT### email(s). Please find the details below.\n\n";
		$report = $report. "Subject : ###SUBJECT### \n";
		$report = $report. "Unique ID : ###UNIQUE### \n";
		$report = $report. "Start Time: ###STARTTIME### \n";
		$report = $report. "End Time: ###ENDTIME### \n\n";
		$report = $report. "For more information, Login to your Dashboard and go to Sent Mail menu in Email Posts plugin. \n\n";
		$report = $report. "Thank You \n";
		$report = $report. "Email posts to subscribers \n";
		return $report;
	}
	
	public static function elp_sent_report_html() 
	{
		$report = "";
		$report = $report. "Hi Admin, <br/><br/>";
		$report = $report. "Newsletter has been sent/triggered successfully to ###COUNT### email(s). Please find the details below.<br/><br/>";
		$report = $report. "Subject : ###SUBJECT### <br/>";
		$report = $report. "Unique ID : ###UNIQUE### <br/>";
		$report = $report. "Start Time: ###STARTTIME### <br/>";
		$report = $report. "End Time: ###ENDTIME### <br/><br/>";
		$report = $report. "For more information, Login to your Dashboard and go to Sent Mail menu in Email Posts plugin. <br/><br/>";
		$report = $report. "Thank You <br/>";
		$report = $report. "Email posts to subscribers <br/>";
		return $report;
	}
	
	public static function elp_special_letters() 
	{
		$string = "/[\'^$%&*()}{@#~?><>,|=_+\"]/";
		return $string;
	}
	
	public static function elp_check_latest_update() 
	{
		$elp_c_plugin_ver = get_option('email-posts-to-subscribers');
		if ($elp_c_plugin_ver <> "3.9")
		{
			?>
			<div class="error fade">
			<p>
				Note: You have recently upgraded this plugin and your tables are not sync. 
				Please <a title="Sync plugin tables." href="<?php echo ELP_ADMINURL; ?>?page=elp-settings&amp;ac=sync"><?php _e('Click Here', 'email-posts-to-subscribers'); ?></a> to sync the table. 
				This is mandatory and it will not affect your data.
			</p>
			</div>
			<?php
		}
	}
	
	public static function elp_postcount_display($post)
	{
		if($post == 100) // Published Today
		{
			$output = "Published Today";
		}
		elseif($post == 110) // Published Last 2 Days
		{
			$output = "Published Last 2 Days (Published Yesterday)";
		}
		elseif($post == 120) // Published Last 3 Days
		{
			$output = "Published Last 3 Days";
		}
		elseif($post == 130) // Published Last 4 Days
		{
			$output = "Published Last 4 Days";
		}
		elseif($post == 140) // Published Last 5 Days
		{
			$output = "Published Last 5 Days";
		}
		elseif($post == 150) //Published Last 6 Days
		{
			$output = "Published Last 6 Days";
		}
		elseif($post == 160) // This Week
		{
			$output = "Published This Week";
		}
		elseif($post == 170) // This Month
		{
			$output = "Published This Month";
		}
		else
		{
			$output = "Send " . $post . " Post(s) in Newsletter";
		}
		return $output;
	}
}

class elp_cls_security
{
	public static function es_check_number($value) 
	{
		if(!is_numeric($value)) 
		{ 
			die('<p>Security check failed. Are you sure you want to do this?</p>'); 
		}
	}
	
	public static function es_check_guid($value) 
	{
		$value_length1 = strlen($value);
		$value_noslash = str_replace("-", "", $value);
		$value_length2 = strlen($value_noslash);
		
		if( $value_length1 != 34 || $value_length2 != 30)
		{
			die('<p>Security check failed. Are you sure you want to do this?</p>'); 
		}
		
		if (preg_match('/[^a-z]/', $value_noslash))
		{
			die('<p>Security check failed. Are you sure you want to do this?</p>'); 
		}
	}
}
?>