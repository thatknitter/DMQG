<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php //if ( ! empty( $_POST ) && ! wp_verify_nonce( $_REQUEST['wp_create_nonce'], 'sendmail-nonce' ) )  { die('<p>Security check failed.</p>'); } ?>
<?php
elp_cls_common::elp_check_latest_update();

$elp_errors = array();
$elp_success = '';
$elp_error_found = FALSE;

$elp_set_guid 		= isset($_POST['elp_set_guid']) ? sanitize_text_field($_POST['elp_set_guid']) : '';
$elp_sent_type 		= isset($_POST['elp_sent_type']) ? sanitize_text_field($_POST['elp_sent_type']) : '';
$elp_sent_group 	= isset($_POST['elp_sent_group']) ? sanitize_text_field($_POST['elp_sent_group']) : '';
$elp_search_query 	= isset($_POST['elp_search_query']) ? sanitize_text_field($_POST['elp_search_query']) : 'A,B,C';
$sendmailsubmit 	= isset($_POST['sendmailsubmit']) ? sanitize_text_field($_POST['sendmailsubmit']) : 'no';

if ($sendmailsubmit == 'yes')
{
	check_admin_referer('elp_form_submit');
	
	$form['elp_set_guid'] = isset($_POST['elp_set_guid']) ? sanitize_text_field($_POST['elp_set_guid']) : '';
	if ($form['elp_set_guid'] == '')
	{
		$elp_errors[] = __('Please select mail configuration.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	//$recipients = $_POST['eemail_checked'];
	$recipients = isset($_POST['eemail_checked']) ? $_POST['eemail_checked'] : '';
	$elp_sent_type = isset($_POST['elp_sent_type']) ? sanitize_text_field($_POST['elp_sent_type']) : 'Instant Mail';
	if ($recipients == '')
	{
		$elp_errors[] = __('No email address selected.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	
	if ($elp_error_found == FALSE)
	{
		$data = array();
		$data = elp_cls_dbquery::elp_configuration_cron($form['elp_set_guid']);
		
		if(count($data) > 0)
		{
			$subject = $data[0]['elp_set_name'];
			$content = elp_cls_newsletter::elp_template_compose($data[0]['elp_set_templid'], $data[0]['elp_set_postcount'], 
								$data[0]['elp_set_postcategory'], $data[0]['elp_set_postorderby'], $data[0]['elp_set_postorder'], "send");
			
			if($content == "NO_POST_FOUND_FOR_THIS_MAIL_CONFIGURATION")
			{
				$sendguid = elp_cls_common::elp_generate_guid(60);
				$currentdate = date('Y-m-d G:i:s');
				elp_cls_dbquery2::elp_sentmail_ins($sendguid, 0, "manual", $currentdate, $currentdate, 0, $content, $elp_sent_type, $subject);
				$elp_success_msg = TRUE;
				$elp_success = __('No post found for this mail configuration', 'email-posts-to-subscribers');
			}
			else
			{			
				elp_cls_sendmail::elp_prepare_newsletter_manual($subject, $content, $recipients, $elp_sent_type);
				$elp_success_msg = TRUE;
				$elp_success = __('Mail sent successfully', 'email-posts-to-subscribers');
			}
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

if ($elp_set_guid <> "")
{
	$data = elp_cls_dbquery::elp_configuration_cron($elp_set_guid);
	if(count($data) > 0)
	{
		$perpage = $data[0]['elp_set_totalsent'];
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
	<h3><?php _e('Send Mail Manually', 'email-posts-to-subscribers'); ?></h3>
	<form name="elp_form" method="post" action="#" onsubmit="return _elp_submit()"  >
	<table class="form-table">
	<tbody>
		<tr>
		<th scope="row">
			<label for="elp">
				<?php _e('Select your mail configuration', 'email-posts-to-subscribers'); ?>
				<p class="description"><?php _e('Select a mail configuration from available list. To create please check mail configuration menu.', 'email-posts-to-subscribers'); ?></p>
			</label>
		</th>
		<td>
			<select name="elp_set_guid" id="elp_set_guid" onChange="_elp_mailsubject(this.options[this.selectedIndex].value)">
			<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
			<?php
			$configs = array();
			$configs = elp_cls_dbquery::elp_configuration_select(0, 0, 100);
			$thisselected = "";
			if(count($configs) > 0)
			{
				$i = 1;
				foreach ($configs as $config)
				{
					if($config["elp_set_guid"] == $elp_set_guid) 
					{ 
						$thisselected = "selected='selected'" ; 
					}
					?><option value='<?php echo $config["elp_set_guid"]; ?>' <?php echo $thisselected; ?>><?php echo $config["elp_set_name"]; ?></option><?php
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
				<select name="elp_sent_type" id="elp_sent_type">
					<option value=''>Select</option>
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
			<select name="elp_sent_group" id="elp_sent_group" onChange="_elp_mailgroup(this.options[this.selectedIndex].value)">
			<option value=''><?php _e('All Group', 'email-posts-to-subscribers'); ?></option>
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
					?><option value="<?php echo esc_html($group["elp_email_group"]); ?>" <?php echo $thisselected; ?>><?php echo stripslashes($group["elp_email_group"]); ?></option><?php
					$thisselected = "";
				}
			}
			?>
			
			</select>
			<input type="button" name="CheckAll" value="<?php _e('Check All', 'email-posts-to-subscribers'); ?>" onClick="_elp_checkall('elp_form', 'eemail_checked[]', true);">
			<input type="button" name="UnCheckAll" value="<?php _e('Uncheck All', 'email-posts-to-subscribers'); ?>" onClick="_elp_checkall('elp_form', 'eemail_checked[]', false);">
		</td>
		</tr>
		<tr>
			<td colspan="2">
				<div class="tablenav">
					<input type="button" class="button action <?php if($elp_search_query == "A,B,C"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_sendemailsearch('A,B,C')" id="" value="A,B,C" />
					<input type="button" class="button action <?php if($elp_search_query == "D,E,F"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_sendemailsearch('D,E,F')" id="" value="D,E,F" />
					<input type="button" class="button action <?php if($elp_search_query == "G,H,I"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_sendemailsearch('G,H,I')" id="" value="G,H,I" />
					<input type="button" class="button action <?php if($elp_search_query == "J,K,L"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_sendemailsearch('J,K,L')" id="" value="J,K,L" />
					<input type="button" class="button action <?php if($elp_search_query == "M,N,O"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_sendemailsearch('M,N,O')" id="" value="M,N,O" />
					<input type="button" class="button action <?php if($elp_search_query == "P,Q,R"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_sendemailsearch('P,Q,R')" id="" value="P,Q,R" />
					<input type="button" class="button action <?php if($elp_search_query == "S,T,U"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_sendemailsearch('S,T,U')" id="" value="S,T,U" />
					<input type="button" class="button action <?php if($elp_search_query == "V,W,X,Y,Z"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_sendemailsearch('V,W,X,Y,Z')" id="" value="V,W,X,Y,Z" />
					<input type="button" class="button action <?php if($elp_search_query == "0,1,2,3,4,5,6,7,8,9"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_sendemailsearch('0,1,2,3,4,5,6,7,8,9')" id="" value="0 to 9" />
					<input type="button" class="button action <?php if($elp_search_query == "ALL"){ echo 'button-primary'; } ?>" onclick="javascript:_elp_sendemailsearch('ALL')" id="" value="ALL" />
				</div>
			</td>
		</tr>
		<tr>
		<td colspan="2">
			<?php
			$subscribers = array();
			$subscribers = elp_cls_dbquery::elp_view_subscriber_sendmail($elp_search_query, $elp_sent_group);	
			$i = 1;
			$count = 0;
			if(count($subscribers) > 0)
			{
				echo "<table border='0' cellspacing='0'><tr>";
				$col=3;
				foreach ($subscribers as $subscriber)
				{
					$to = $subscriber['elp_email_mail'];
					$id = $subscriber['elp_email_id'];
					//$ToAddress = trim($to) . '<||>' . trim($id);
					if($to <> "")
					{
						echo "<td style='padding-top:4px;padding-bottom:4px;padding-right:10px;'>";
						?>
						<input class="radio" type="checkbox" checked="checked" value='<?php echo $id; ?>' id="eemail_checked[]" name="eemail_checked[]">
						<?php echo $to; ?> (<?php echo $id; ?>)
						<?php
						if($col > 1) 
						{
							$col=$col-1;
							echo "</td><td>"; 
						}
						elseif($col = 1)
						{
							$col=$col-1;
							echo "</td></tr><tr>";;
							$col=3;
						}
						$count = $count + 1;
					}
				}
				echo "</tr></table>";
			}
			else
			{
				$message = __('Oops.. There is no email starts with ', 'email-posts-to-subscribers');
				echo $message . " " . $elp_search_query . "<br>";
			}
			?>
		</td>
		</tr>
	</tbody>
	</table>
	<div>
	<br />
	<?php wp_nonce_field('elp_form_submit'); ?>
	<input type="hidden" name="sendmailsubmit" id="sendmailsubmit" value=""/>
	<input type="hidden" name="elp_search_query" id="elp_search_query" value="<?php echo $elp_search_query; ?>"/>
	<input type="hidden" name="wp_create_nonce" id="wp_create_nonce" value="<?php echo wp_create_nonce( 'sendmail-nonce' ); ?>"/>
	
	<input type="submit" name="Submit" id="Submit" class="button add-new-h2" value="<?php _e('Send Email', 'email-posts-to-subscribers'); ?>" style="width:160px;" />&nbsp;&nbsp;
	<input name="publish" lang="publish" class="button add-new-h2" onclick="_elp_redirect()" value="<?php _e('Cancel', 'email-posts-to-subscribers'); ?>" type="button" />&nbsp;&nbsp;
    <input name="Help" lang="publish" class="button add-new-h2" onclick="_elp_help()" value="<?php _e('Help', 'email-posts-to-subscribers'); ?>" type="button" />
	</div>
	</form>
</div><br />
<p class="description"><?php echo ELP_OFFICIAL; ?></p>
</div>