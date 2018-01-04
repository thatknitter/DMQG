<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$elp_errors = array();
$elp_success = '';
$elp_error_found = FALSE;

// Preset the form fields
$form = array(
	'elp_email_name' => '',
	'elp_email_mail' => ''
);

// Form submitted, check the data
if (isset($_POST['elp_form_submit']) && $_POST['elp_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('elp_form_add');
	
	$form['elp_email_status'] 	= isset($_POST['elp_email_status']) ? sanitize_text_field($_POST['elp_email_status']) : '';
	$form['elp_email_name'] 	= isset($_POST['elp_email_name']) ? sanitize_text_field($_POST['elp_email_name']) : '';
	$form['elp_email_mail'] 	= isset($_POST['elp_email_mail']) ? sanitize_text_field($_POST['elp_email_mail']) : '';
	if ($form['elp_email_mail'] == '')
	{
		$elp_errors[] = __('Please enter valid email.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	
	$elp_email_group = isset($_POST['elp_email_group']) ? sanitize_text_field($_POST['elp_email_group']) : '';
	if ($elp_email_group == '')
	{
		$elp_email_group = isset($_POST['elp_email_group_txt']) ? sanitize_text_field($_POST['elp_email_group_txt']) : '';
		$form['elp_email_group'] = $elp_email_group;
	}
	else
	{
		$form['elp_email_group'] = $elp_email_group;
	}
	
	//	No errors found, we can add this Group to the table
	if ($elp_error_found == FALSE)
	{
		$inputdata = array($form['elp_email_name'], $form['elp_email_mail'], $form['elp_email_status'], trim($form['elp_email_group']));
		$action = "";
		$action = elp_cls_dbquery::elp_view_subscriber_ins($inputdata);
		if($action == "sus")
		{
			$elp_success = __('Email was successfully inserted.', 'email-posts-to-subscribers');
		}
		elseif($action == "ext")
		{
			$elp_errors[] = __('Email already exist in our list.', 'email-posts-to-subscribers');
			$elp_error_found = TRUE;
		}
		elseif($action == "invalid")
		{
			$elp_errors[] = __('Email is invalid.', 'email-posts-to-subscribers');
			$elp_error_found = TRUE;
		}
			
		// Reset the form fields
		$form = array(
			'elp_email_name' => '',
			'elp_email_mail' => ''
		);
	}
}

if ($elp_error_found == TRUE && isset($elp_errors[0]) == TRUE)
{
	?>
	<div class="error fade">
		<p><strong><?php echo $elp_errors[0]; ?></strong></p>
	</div>
	<?php
}
if ($elp_error_found == FALSE && isset($elp_success[0]) == TRUE)
{
	?>
	  <div class="updated fade">
		<p>
		<strong>
		<?php echo $elp_success; ?>
		<a href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers">
		<?php _e('Click here', 'email-posts-to-subscribers'); ?></a> <?php _e(' to view the details', 'email-posts-to-subscribers'); ?>
		</strong>
		</p>
	  </div>
	  <?php
	}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>subscribers/view-subscriber.js"></script>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<form name="form_addemail" method="post" action="#" onsubmit="return _elp_addemail()"  >
      <h3 class="title"><?php _e('Add email', 'email-posts-to-subscribers'); ?></h3>
      
	  <label for="tag-image"><?php _e('Enter full name', 'email-posts-to-subscribers'); ?></label>
      <input name="elp_email_name" type="text" id="elp_email_name" value="" maxlength="225" size="30"  />
      <p><?php _e('Enter the name for email.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-image"><?php _e('Enter email address.', 'email-posts-to-subscribers'); ?></label>
      <input name="elp_email_mail" type="text" id="elp_email_mail" value="" maxlength="225" size="50" />
      <p><?php _e('Enter the email address to add in the subscribers list.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-display-status"><?php _e('Select (or) Create Group', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_email_group" id="elp_email_group">
		<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
		<?php
		$groups = array();
		$groups = elp_cls_dbquery::elp_view_subscriber_group();
		if(count($groups) > 0)
		{
			$i = 1;
			foreach ($groups as $group)
			{
				?><option value="<?php echo stripslashes($group["elp_email_group"]); ?>"><?php echo stripslashes($group["elp_email_group"]); ?></option><?php
			}
		}
		?>
	  </select>
	  (or) 
	  <input name="elp_email_group_txt" type="text" id="elp_email_group_txt" value="" maxlength="20" onkeyup="return _elp_numericandtext(document.form_addemail.elp_email_group_txt)" />
      <p><?php _e('Please select or create group for this subscriber.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-display-status"><?php _e('Status', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_email_status" id="elp_email_status">
        <option value='Confirmed' selected="selected">Confirmed</option>
		<option value='Unconfirmed'>Unconfirmed</option>
		<option value='Unsubscribed'>Unsubscribed</option>
		<option value='Single Opt In'>Single Opt In</option>
      </select>
      <p><?php _e('Unsubscribed, Unconfirmed emails not display in send mail page.', 'email-posts-to-subscribers'); ?></p>
	  
      <input type="hidden" name="elp_form_submit" value="yes"/>
	  <div style="padding-top:5px;"></div>
      <p>
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Insert Details', 'email-posts-to-subscribers'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="_elp_redirect()" value="<?php _e('Cancel', 'email-posts-to-subscribers'); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="_elp_help()" value="<?php _e('Help', 'email-posts-to-subscribers'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('elp_form_add'); ?>
    </form>
</div>
<p class="description"><?php echo ELP_OFFICIAL; ?></p>
</div>