<?php
function elp_plugin_query_vars($vars) 
{
	$vars[] = 'elp';
	return $vars;
}
add_filter('query_vars', 'elp_plugin_query_vars');

function elp_plugin_parse_request($qstring)
{
	if (array_key_exists('elp', $qstring->query_vars)) 
	{
		$page = $qstring->query_vars['elp'];
		switch($page)
		{
			case 'subscribe':
				require_once(ELP_DIR.'job'.DIRECTORY_SEPARATOR.'subscribe.php');
				break;
			case 'cron':
				require_once(ELP_DIR.'cronjob'.DIRECTORY_SEPARATOR.'cronjob.php');
				break;
			case 'unsubscribe':
				require_once(ELP_DIR.'job'.DIRECTORY_SEPARATOR.'unsubscribe.php');
				break;
			case 'viewstatus':
				require_once(ELP_DIR.'job'.DIRECTORY_SEPARATOR.'viewstatus.php');
				break;
			case 'export':
				require_once(ELP_DIR.'export'.DIRECTORY_SEPARATOR.'export-email-address.php');
				break;
			case 'optin':
				require_once(ELP_DIR.'job'.DIRECTORY_SEPARATOR.'optin.php');
				break;
		}
	}
}
add_action('parse_request', 'elp_plugin_parse_request');
?>