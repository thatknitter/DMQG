<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
elp_cls_common::elp_check_latest_update();

$elp_errors = array();
$elp_success = '';
$elp_error_found = FALSE;
$cron_adminmail = "";

//echo "current_time( 'mysql' ) returns local site time: " . current_time( 'mysql' ) . '<br />';
//echo "current_time( 'mysql', 1 ) returns GMT: " . current_time( 'mysql', 1 ) . '<br />';
//echo "current_time( 'timestamp' ) returns local site time: " . date( 'Y-m-d H:i:s', current_time( 'timestamp', 0 ) );
//echo "current_time( 'timestamp', 1 ) returns GMT: " . date( 'Y-m-d H:i:s', current_time( 'timestamp', 1 ) );

// Form submitted, check the data
if (isset($_POST['elp_form_submit']) && $_POST['elp_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('elp_form_add');
	
	$elp_cron_mailcount = isset($_POST['elp_cron_mailcount']) ? wp_filter_post_kses($_POST['elp_cron_mailcount']) : '';
	if($elp_cron_mailcount == "0" && strlen ($elp_cron_mailcount) > 0)
	{
		$elp_errors[] = __('Please enter valid mail count.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	
	$elp_cron_adminmail = isset($_POST['elp_cron_adminmail']) ? wp_filter_post_kses($_POST['elp_cron_adminmail']) : '';
	$elp_cron_trigger_option = isset($_POST['elp_cron_trigger_option']) ? wp_filter_post_kses($_POST['elp_cron_trigger_option']) : '';

	//	No errors found, we can add this Group to the table
	if ($elp_error_found == FALSE)
	{
		update_option('elp_cron_mailcount', $elp_cron_mailcount );
		update_option('elp_cron_adminmail', $elp_cron_adminmail );
		update_option('elp_cron_trigger_option', $elp_cron_trigger_option );
		$elp_success = __('Cron details successfully updated.', 'email-posts-to-subscribers');
	}
}

$elp_cron_url = get_option('elp_c_cronurl', 'nocronurl');
if($elp_cron_url == "nocronurl")
{
	$guid = elp_cls_common::elp_generate_guid(60);
	$home_url = home_url('/');
	$cronurl = $home_url . "?elp=cron&guid=". $guid;
	add_option('elp_c_cronurl', $cronurl);
	$elp_cron_url = get_option('elp_c_cronurl');
}

$elp_cron_mailcount = get_option('elp_cron_mailcount', '0');
if($elp_cron_mailcount == "0")
{
	add_option('elp_cron_mailcount', "75");
	$elp_cron_mailcount = get_option('elp_cron_mailcount');
}

$elp_cron_adminmail = get_option('elp_cron_adminmail', '');
if($elp_cron_adminmail == "")
{
	add_option('elp_cron_adminmail', "Hi Admin, \r\n\r\nCron URL has been triggered successfully on ###DATE### for the mail ###SUBJECT###. And the mail has been sent to ###COUNT### recipient. \r\n\r\nThank You");
	$elp_cron_adminmail = get_option('elp_cron_adminmail');
}

$elp_cron_trigger_option = get_option('elp_cron_trigger_option', '0');
if($elp_cron_trigger_option == "0")
{
	add_option('elp_cron_trigger_option', "YES");
	$elp_cron_trigger_option = get_option('elp_cron_trigger_option');
}

if ($elp_error_found == TRUE && isset($elp_errors[0]) == TRUE)
{
	?><div class="error fade"><p><strong><?php echo $elp_errors[0]; ?></strong></p></div><?php
}
if ($elp_error_found == FALSE && strlen($elp_success) > 0)
{
	?>
	<div class="updated fade">
		<p><strong><?php echo $elp_success; ?></strong></p>
	</div>
	<?php
}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>cron/cron.js"></script>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<h3><?php _e('Cron Details', 'email-posts-to-subscribers'); ?></h3>
	<form name="elp_form" method="post" action="#" onsubmit="return _elp_submit()"  >
      
	  <label for="tag-link"><?php _e('WordPress Cron', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_cron_trigger_option" id="elp_cron_trigger_option">
        <option value='YES' <?php if($elp_cron_trigger_option == 'YES') { echo 'selected="selected"' ; } ?>>YES (Use WP CRON)</option>
		<option value='NO' <?php if($elp_cron_trigger_option == 'NO') { echo 'selected="selected"' ; } ?>>NO (Do not use WP CRON)</option>
      </select>
      <p>
	  <?php _e('YES : Plugin will use WP CRON option to send emails. No manual configuration is required.', 'email-posts-to-subscribers'); ?><br />
	  <?php _e('NO : Plugin will not use WP CRON option to send emails instead you have to configure CRON JOB in your server using below Cron job URL.', 'email-posts-to-subscribers'); ?>
	  </p>

      <label for="tag-link"><?php _e('Cron job URL', 'email-posts-to-subscribers'); ?></label>
      <input name="elp_cron_url" type="text" id="elp_cron_url" value="<?php echo $elp_cron_url; ?>" maxlength="225" size="75"  />
      <p><?php _e('Please find your cron job URL. This is read only field not able to modify from admin.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-link"><?php _e('Mail Count', 'email-posts-to-subscribers'); ?></label>
      <input name="elp_cron_mailcount" type="text" id="elp_cron_mailcount" value="<?php echo $elp_cron_mailcount; ?>" maxlength="3" />
      <p><?php _e('Enter number of mails you want to send per hour/trigger.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-link"><?php _e('Admin Report', 'email-posts-to-subscribers'); ?></label>
	  <textarea size="100" id="elp_cron_adminmail" rows="6" cols="73" name="elp_cron_adminmail"><?php echo esc_html(stripslashes($elp_cron_adminmail)); ?></textarea>
	  <p><?php _e('Send above mail to admin whenever cron URL triggered in your server.', 'email-posts-to-subscribers'); ?><br />(Keywords: ###DATE###, ###SUBJECT###, ###COUNT###)</p>

      <input type="hidden" name="elp_form_submit" value="yes"/>
      <p class="submit">
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Submit', 'email-posts-to-subscribers'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="_elp_redirect()" value="<?php _e('Cancel', 'email-posts-to-subscribers'); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="_elp_help()" value="<?php _e('Help', 'email-posts-to-subscribers'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('elp_form_add'); ?>
    </form>
</div>
<?php
$timestamp = wp_next_scheduled('elp_cron_hourly_event');
$elp_wp_next_scheduled = "NA";
$elp_wp_last_scheduled = "NA";
if($timestamp <> "" and $elp_cron_trigger_option == "YES")
{
	$elp_wp_next_scheduled = date_i18n('Y-m-d H:i:s', $timestamp, false);
	$elp_wp_last_scheduled = date_i18n('Y-m-d H:i:s', $timestamp-3600, false);
}
?>
<table class="widefat striped">
	<thead>
		<tr>
			<th scope="col"><?php _e('WordPress Cron', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Hook Name', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Actions', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Last Run', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Next Run', 'email-posts-to-subscribers'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo $elp_cron_trigger_option; ?></td>
			<td>elp_cron_hourly_event</td>
			<td><code>elp_cron_trigger_hourly()</code></td>
			<td><?php echo $elp_wp_last_scheduled; ?></td>
			<td><?php echo $elp_wp_next_scheduled; ?></td>
		</tr>
	</tbody>
	</table>

<div class="tool-box">
	<h3><?php _e('How to setup auto emails?', 'email-posts-to-subscribers'); ?></h3>
	<p><?php _e('There are two options available in this plugin to schedule your CRON jobs. First option is let wordpress handle your scheduler (Set YES for wordpress CRON). And second option is configure the scheduler (Set NO for wordpress CRON) in your server.'); ?></p>
	<p><?php _e('1. First option (Let wordpress handle your scheduler) : This is new option introduced in plugin version 3.9. this is very easy option and no server knowledge is required. In this page just set WordPress Cron to YES, wordpress automatically trigger the CRON job once every hour and based on your mail configuration newsletter go to your subscriber automatically. For More info', 'email-posts-to-subscribers'); ?> <a target="_blank" href="http://www.gopiplus.com/work/2014/03/31/schedule-auto-mails-cron-jobs-for-email-posts-to-subscribers-plugin/">click here</a> </p>
	<p><?php _e('2. Second option (Configure CRON in your server) : CRON URL is available in this page. You have to trigger/schedule this URL from your server once every hour (Once every hour is recommended for this plugin). Plugin will send/schedule the newsletter whenever your URL is triggered. For more info ', 'email-posts-to-subscribers'); ?>  <a target="_blank" href="http://www.gopiplus.com/work/2014/03/31/schedule-auto-mails-cron-jobs-for-email-posts-to-subscribers-plugin/">click here</a></p>
</div>
</div>