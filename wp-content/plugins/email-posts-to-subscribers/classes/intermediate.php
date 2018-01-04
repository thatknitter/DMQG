<?php
class elp_cls_intermediate
{
	public static function elp_information()
	{
		require_once(ELP_DIR.'help'.DIRECTORY_SEPARATOR.'help.php');
	}
	
	public static function elp_template()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(ELP_DIR.'template'.DIRECTORY_SEPARATOR.'template-add.php');
				break;
			case 'edit':
				require_once(ELP_DIR.'template'.DIRECTORY_SEPARATOR.'template-edit.php');
				break;
			case 'preview':
				require_once(ELP_DIR.'template'.DIRECTORY_SEPARATOR.'template-preview.php');
				break;
			default:
				require_once(ELP_DIR.'template'.DIRECTORY_SEPARATOR.'template-show.php');
				break;
		}
	}
	
	public static function elp_subscribers()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(ELP_DIR.'subscribers'.DIRECTORY_SEPARATOR.'view-subscriber-add.php');
				break;
			case 'edit':
				require_once(ELP_DIR.'subscribers'.DIRECTORY_SEPARATOR.'view-subscriber-edit.php');
				break;
			case 'export':
				require_once(ELP_DIR.'subscribers'.DIRECTORY_SEPARATOR.'view-subscriber-export.php');
				break;
			case 'import':
				require_once(ELP_DIR.'subscribers'.DIRECTORY_SEPARATOR.'view-subscriber-import.php');
				break;
			case 'page':
				require_once(ELP_DIR.'subscribers'.DIRECTORY_SEPARATOR.'view-subscriber-page.php');
				break;
			case 'sync':
				require_once(ELP_DIR.'subscribers'.DIRECTORY_SEPARATOR.'view-subscriber-sync.php');
				break;
			default:
				require_once(ELP_DIR.'subscribers'.DIRECTORY_SEPARATOR.'view-subscriber-show.php');
				break;
		}
	}
	
	public static function elp_configuration()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(ELP_DIR.'configuration'.DIRECTORY_SEPARATOR.'configuration-add.php');
				break;
			case 'edit':
				require_once(ELP_DIR.'configuration'.DIRECTORY_SEPARATOR.'configuration-edit.php');
				break;
			case 'cron':
				require_once(ELP_DIR.'configuration'.DIRECTORY_SEPARATOR.'configuration-cron.php');
				break;
			case 'preview':
				require_once(ELP_DIR.'configuration'.DIRECTORY_SEPARATOR.'configuration-preview.php');
				break;
			default:
				require_once(ELP_DIR.'configuration'.DIRECTORY_SEPARATOR.'configuration-show.php');
				break;
		}
	}
	
	public static function elp_sentmail()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'delivery':
				require_once(ELP_DIR.'sentmail'.DIRECTORY_SEPARATOR.'deliverreport-show.php');
				break;
			case 'preview':
				require_once(ELP_DIR.'sentmail'.DIRECTORY_SEPARATOR.'sentmail-preview.php');
				break;
			default:
				require_once(ELP_DIR.'sentmail'.DIRECTORY_SEPARATOR.'sentmail-show.php');
				break;
		}
	}
	
	public static function elp_sendemail()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'sub':
				require_once(ELP_DIR.'sendmail'.DIRECTORY_SEPARATOR.'sendmail-subscriber.php');
				break;
			default:
				require_once(ELP_DIR.'sendmail'.DIRECTORY_SEPARATOR.'sendmail-subscriber.php');
				break;
		}
	}
	
	public static function elp_crondetails()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(ELP_DIR.'cron'.DIRECTORY_SEPARATOR.'cron-add.php');
				break;
			default:
				require_once(ELP_DIR.'cron'.DIRECTORY_SEPARATOR.'cron-add.php');
				break;
		}
	}
	
	public static function elp_settings()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(ELP_DIR.'settings'.DIRECTORY_SEPARATOR.'settings-add.php');
				break;
			case 'sync':
				require_once(ELP_DIR.'settings'.DIRECTORY_SEPARATOR.'setting-sync.php');
				break;
			default:
				require_once(ELP_DIR.'settings'.DIRECTORY_SEPARATOR.'settings-edit.php');
				break;
				
		}
	}
	
	public static function elp_schedule()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(ELP_DIR.'settings'.DIRECTORY_SEPARATOR.'schedule-add.php');
				break;
			case 'edit':
				require_once(ELP_DIR.'settings'.DIRECTORY_SEPARATOR.'schedule-edit.php');
				break;
			case 'show':
				require_once(ELP_DIR.'settings'.DIRECTORY_SEPARATOR.'schedule-show.php');
				break;
			default:
				require_once(ELP_DIR.'settings'.DIRECTORY_SEPARATOR.'schedule-show.php');
				break;
		}
	}
	
	public static function elp_roles()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(ELP_DIR.'roles'.DIRECTORY_SEPARATOR.'roles-add.php');
				break;
			case 'edit':
				require_once(ELP_DIR.'roles'.DIRECTORY_SEPARATOR.'roles-edit.php');
				break;
			default:
				require_once(ELP_DIR.'roles'.DIRECTORY_SEPARATOR.'roles-add.php');
				break;
		}
	}
	
	public static function elp_postnotification()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(ELP_DIR.'notification'.DIRECTORY_SEPARATOR.'notification-add.php');
				break;
			case 'edit':
				require_once(ELP_DIR.'notification'.DIRECTORY_SEPARATOR.'notification-edit.php');
				break;
			case 'preview':
				require_once(ELP_DIR.'notification'.DIRECTORY_SEPARATOR.'notification-preview.php');
				break;
			default:
				require_once(ELP_DIR.'notification'.DIRECTORY_SEPARATOR.'notification-show.php');
				break;
		}
	}
	
	public static function elp_composenewsletter()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'add':
				require_once(ELP_DIR.'template'.DIRECTORY_SEPARATOR.'newsletter-add.php');
				break;
			case 'edit':
				require_once(ELP_DIR.'template'.DIRECTORY_SEPARATOR.'newsletter-edit.php');
				break;
			case 'preview':
				require_once(ELP_DIR.'template'.DIRECTORY_SEPARATOR.'newsletter-preview.php');
				break;
			default:
				require_once(ELP_DIR.'template'.DIRECTORY_SEPARATOR.'newsletter-show.php');
				break;
		}
	}
	
	public static function elp_sendnewsletter()
	{
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		switch($current_page)
		{
			case 'view':
				require_once(ELP_DIR.'sendmail'.DIRECTORY_SEPARATOR.'sendmail-newsletter.php');
				break;
			default:
				require_once(ELP_DIR.'sendmail'.DIRECTORY_SEPARATOR.'sendmail-newsletter.php');
				break;
		}
	}
}
?>