<?php
$elp_plugin_name='email-posts-to-subscribers';
$elp_plugin_folder_name = dirname(dirname(plugin_basename(__FILE__)));
$elp_current_folder=dirname(dirname(__FILE__));

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
if(!defined('ELP_TDOMAIN')) define('ELP_TDOMAIN', $elp_plugin_name);
if(!defined('ELP_PLUGIN_NAME')) define('ELP_PLUGIN_NAME', $elp_plugin_name);
if(!defined('ELP_PLUGIN_DISPLAY')) define('ELP_PLUGIN_DISPLAY', "Email posts to subscribers");
if(!defined('ELP_PLG_DIR')) define('ELP_PLG_DIR', dirname($elp_current_folder).DS);
if(!defined('ELP_DIR')) define('ELP_DIR', $elp_current_folder.DS);
if(!defined('ELP_URL')) define('ELP_URL',plugins_url().'/'.strtolower('email-posts-to-subscribers').'/');
define('ELP_FILE',ELP_DIR.'email-posts-to-subscribers.php');
if(!defined('ELP_FAV')) define('ELP_FAV', 'http://www.gopiplus.com/work/2014/03/28/wordpress-plugin-email-posts-to-subscribers/');
if(!defined('ELP_ADMINURL')) define('ELP_ADMINURL', get_option('siteurl') . '/wp-admin/admin.php');
define('ELP_OFFICIAL', 'Check official website for more information <a target="_blank" href="'.ELP_FAV.'">click here</a>');
global $elp_altmsg;
global $elp_includes;
?>