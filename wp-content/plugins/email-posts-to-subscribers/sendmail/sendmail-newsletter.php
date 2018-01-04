<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php if ( ! empty( $_POST ) && ! wp_verify_nonce( $_REQUEST['wp_create_nonce'], 'sendmail-nonce' ) )  { die('<p>Security check failed.</p>'); } ?>
<?php

$elp_errors = array();
$elp_success = '';
$elp_error_found = FALSE;

$elp_set_templid 	= isset($_POST['elp_set_templid']) ? sanitize_text_field($_POST['elp_set_templid']) : '';
$elp_sent_type 		= isset($_POST['elp_sent_type']) ? sanitize_text_field($_POST['elp_sent_type']) : '';
$elp_sent_group 	= isset($_POST['elp_sent_group']) ? sanitize_text_field($_POST['elp_sent_group']) : '';
$sendmailsubmit 	= isset($_POST['sendmailsubmit']) ? sanitize_text_field($_POST['sendmailsubmit']) : 'no';

if ($sendmailsubmit == 'yes')
{
	check_admin_referer('elp_form_submit');
	
	$form['elp_set_templid'] = isset($_POST['elp_set_templid']) ? sanitize_text_field($_POST['elp_set_templid']) : '';
	if ($form['elp_set_templid'] == '')
	{
		$elp_errors[] = __('Please select your newsletter.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}

	$elp_sent_type = isset($_POST['elp_sent_type']) ? sanitize_text_field($_POST['elp_sent_type']) : '';
	if ($elp_sent_type == '')
	{
		$elp_errors[] = __('Please select your mail type.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	
	$elp_sent_group = isset($_POST['elp_sent_group']) ? sanitize_text_field($_POST['elp_sent_group']) : '';
	if ($elp_sent_group == '')
	{
		$elp_errors[] = __('Please select subscriber group.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	
	if ($elp_error_found == FALSE)
	{
		$data = array();
		$data = elp_cls_dbquery::elp_template_select($form['elp_set_templid'], "Newsletter");
		
		if(count($data) > 0)
		{
			$subject = $data['elp_templ_heading'];
			$content = $data['elp_templ_body'];
			$subscribers = array();
			$subscribers = elp_cls_dbquery::elp_view_subscriber_sendmail("", $elp_sent_group);
			
			if(count($subscribers) > 0)
			{
				elp_cls_sendmail::elp_sendmail("newsletter", $subject, $content, $subscribers, "manual", $elp_sent_type);
			}
			
			$elp_success_msg = TRUE;
			$elp_success = __('Mail sent successfully', 'email-posts-to-subscribers');
		}
		
		if ($elp_success_msg == TRUE)
		{
			?>
			<div class="updated fade">
			  <p>
				<strong><?php echo $elp_success; ?> <a href="<?php echo ELP_ADMINURL; ?>?page=elp-sentmail"><?php _e('Click here for details', 'email-posts-to-subscribers'); ?></a></strong>
			  </p>
			</div>
			<?php
		}
	}
}

if ($elp_error_found == TRUE && isset($elp_errors[0]) == TRUE)
{
	?><div class="error fade"><p><strong><?php echo $elp_errors[0]; ?></strong></p></div><?php
}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>sendmail/sendmail.js"></script>
<style>
.form-table th {
    width: 250px;
}
</style>
<div class="wrap">
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<h3><?php _e('Send Newsletter', 'email-posts-to-subscribers'); ?></h3>
	<form name="elp_form" method="post" action="#" onsubmit="return _elp_newsletter_submit()"  >
	<table class="form-table">
	<tbody>
		<tr>
		<th scope="row">
			<label for="elp">
				<?php _e('Select your newsletter', 'email-posts-to-subscribers'); ?>
				<p class="description"><?php _e('Select a newsletter from available list. To create please go to Compose Newsletter menu.', 'email-posts-to-subscribers'); ?></p>
			</label>
		</th>
		<td>
			<select name="elp_set_templid" id="elp_set_templid" style="width:400px;">
			<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
			<?php
			$Templates = array();
			$Templates = elp_cls_dbquery::elp_template_select(0, "Newsletter");
			$thisselected = "";
			if(count($Templates) > 0)
			{
				foreach ($Templates as $Template)
				{
					if($Template["elp_templ_id"] == $elp_set_templid) 
					{ 
						$thisselected = "selected='selected'" ; 
					}
					?>
					<option value='<?php echo $Template['elp_templ_id']; ?>' <?php echo $thisselected; ?>>
						<?php echo esc_html(stripslashes($Template['elp_templ_heading'])); ?>
					</option>
					<?php
					$thisselected = "";
				}
			}
			?>
		  </select>
		</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="elp">
					<?php _e('Mail Type', 'email-posts-to-subscribers'); ?>
					<p class="description"><?php _e('Select your mail type.', 'email-posts-to-subscribers'); ?></p>
				</label>
			</th>
			<td>
				<select name="elp_sent_type" id="elp_sent_type" style="width:250px;">
					<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
					<option value='Instant Mail' <?php if($elp_sent_type == 'Instant Mail') { echo "selected='selected'" ; } ?>>Send mail immediately.</option>
					<option value='Cron Mail' <?php if($elp_sent_type == 'Cron Mail') { echo "selected='selected'" ; } ?>>Send mail via cron job.</option>
				</select>
			</td>
		</tr>
		<tr>
		<th scope="row">
			<label for="elp">
				<?php _e('Select subscriber group', 'email-posts-to-subscribers'); ?>
				<p class="description"><?php _e('Select your subscriber group to send email.', 'email-posts-to-subscribers'); ?></p>
			</label>
		</th>
		<td>
			<select name="elp_sent_group" id="elp_sent_group" style="width:250px;">
			<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
			<?php
			$groups = array();
			$thisselected = "";
			$groups = elp_cls_dbquery::elp_view_subscriber_group();
			if(count($groups) > 0)
			{
				$i = 1;
				foreach ($groups as $group)
				{
					if(stripslashes($group["elp_email_group"]) == stripslashes($elp_sent_group)) 
					{ 
						$thisselected = "selected='selected'" ; 
					}
					$result = elp_cls_dbquery::elp_view_subscriber_count_bygroup($group["elp_email_group"]);
					?>
					<option value="<?php echo esc_html($group["elp_email_group"]); ?>" <?php echo $thisselected; ?>>
						<?php echo stripslashes($group["elp_email_group"]); ?> (Total Email: <?php echo $result; ?>)
					</option>
					<?php
					$thisselected = "";
				}
			}
			?>
			</select>
		</td>
		</tr>
	</tbody>
	</table>
	<div>
	<?php wp_nonce_field('elp_form_submit'); ?>
	<input type="hidden" name="sendmailsubmit" id="sendmailsubmit" value="yes"/>
	<input type="hidden" name="wp_create_nonce" id="wp_create_nonce" value="<?php echo wp_create_nonce( 'sendmail-nonce' ); ?>"/>
	
	<input type="submit" name="Submit" id="Submit" class="button add-new-h2" value="<?php _e('Send Email', 'email-posts-to-subscribers'); ?>" />&nbsp;&nbsp;
    <input name="Help" lang="publish" class="button add-new-h2" onclick="_elp_help()" value="<?php _e('Help', 'email-posts-to-subscribers'); ?>" type="button" />
	</div>
	</form>
</div>
<br />
<p class="description"><?php echo ELP_OFFICIAL; ?></p>
</div>