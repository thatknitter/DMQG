<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$elp_errors = array();
$elp_success = '';
$elp_error_found = FALSE;

// Preset the form fields
$form = array(
	'elp_note_guid' => '',
	'elp_note_postcat' => '',
	'elp_note_emailgroup' => '',
	'elp_note_mailsubject' => '',
	'elp_note_mailcontent' => '',
	'elp_note_status' => '',
	'elp_note_type' => ''
);

// Form submitted, check the data
if (isset($_POST['elp_form_submit']) && $_POST['elp_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('elp_form_add');
	
	$form['elp_note_guid'] 			= elp_cls_common::elp_generate_guid(30);
	$form['elp_note_emailgroup'] 	= isset($_POST['elp_note_emailgroup']) ? sanitize_text_field($_POST['elp_note_emailgroup']) : '';
	if ($form['elp_note_emailgroup'] == '')
	{
		$elp_errors[] = __('Please select email subscribers group.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	
	$form['elp_note_status'] 		= isset($_POST['elp_note_status']) ? sanitize_text_field($_POST['elp_note_status']) : '';
	if ($form['elp_note_status'] == '')
	{
		$elp_errors[] = __('Please select notification Status.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	
	$form['elp_note_mailsubject'] 	= isset($_POST['elp_note_mailsubject']) ? sanitize_text_field($_POST['elp_note_mailsubject']) : '';
	if ($form['elp_note_mailsubject'] == '')
	{
		$elp_errors[] = __('Please enter notification mail subject.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	
	$form['elp_note_mailcontent'] 	= isset($_POST['elp_note_mailcontent']) ? $_POST['elp_note_mailcontent'] : '';
	
	$elp_note_cat = isset($_POST['elp_note_cat']) ? $_POST['elp_note_cat'] : '';
	if ($elp_note_cat == '')
	{
		$elp_errors[] = __('Please select post categories.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	
	//	No errors found, we can add this Group to the table
	if ($elp_error_found == FALSE)
	{
		$action = "";
		
		$listcategory = "";
		$total = count($elp_note_cat);
		if( $total > 0 )
		{
			for($i=0; $i<$total; $i++)
			{
				$listcategory = $listcategory . " ##" . $elp_note_cat[$i] . "## ";
				if($i <> ($total - 1))
				{
					$listcategory = $listcategory .  "--";
				}
			}
		}
		$form['elp_note_postcat'] = $listcategory;
		
		$action = elp_cls_dbquerynote::elp_notification_ins($form, $action = "insert");
		if($action)
		{
			$elp_success = __('Notification was successfully inserted.', 'email-posts-to-subscribers');
		}
		else
		{
			$elp_errors[] = __('Error in notification creation.', 'email-posts-to-subscribers');
			$elp_error_found = TRUE;
		}
			
		// Reset the form fields
		$form = array(
			'elp_note_guid' => '',
			'elp_note_postcat' => '',
			'elp_note_emailgroup' => '',
			'elp_note_mailsubject' => '',
			'elp_note_mailcontent' => '',
			'elp_note_status' => '',
			'elp_note_type' => ''
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
		<a href="<?php echo ELP_ADMINURL; ?>?page=elp-postnotification">
		<?php _e('Click here', 'email-posts-to-subscribers'); ?></a> <?php _e(' to view the details', 'email-posts-to-subscribers'); ?>
		</strong>
		</p>
	  </div>
	  <?php
	}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>notification/notification.js"></script>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<form name="form_addnotification" method="post" action="#" onsubmit="return _elp_addnotification()"  >
      <h3 class="title"><?php _e('Add Notification', 'email-posts-to-subscribers'); ?></h3>
	  
	  <label for="tag-image"><?php _e('Subscribers Group', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_note_emailgroup" id="elp_note_emailgroup">
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
      <p><?php _e('Select email subscribers group for this notification.', 'email-posts-to-subscribers'); ?></p><br />
	  
	  
	  <label for="tag-link"><?php _e('Post Categories', 'email-posts-to-subscribers'); ?></label>
      <?php
		$args = array( 'hide_empty' => 0, 'orderby' => 'name', 'order' => 'ASC' );
		$categories = get_categories($args); 
		$count = 0;
		$col=5;
		echo "<table border='0' cellspacing='0'><tr>"; 
		foreach($categories as $category) 
		{     
			echo "<td style='padding-top:4px;padding-bottom:4px;padding-right:10px;'>";
			?>
			<input type="checkbox" value='<?php echo $category->cat_name; ?>' id="elp_note_cat[]" name="elp_note_cat[]">
			<?php echo $category->cat_name; ?>
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
				$col=5;
			}
			$count = $count + 1;
		}
		echo "</tr></table>";
	  ?>
      <p><?php _e('Please select post categories.', 'email-posts-to-subscribers'); ?></p><br />
	  
	  <label for="tag-link"><?php _e('Custom post type', 'email-posts-to-subscribers'); ?></label>
	  <?php
		$args = array('public'=> true, 'exclude_from_search'=> false, '_builtin' => false); 
		$output = 'names';
		$operator = 'and';
		$post_types = get_post_types($args, $output, $operator);
		if(count($post_types) > 0)
		{
			$col = 5;
			echo "<table border='0' cellspacing='0'><tr>"; 
			foreach($post_types as $post_type) 
			{     
				echo "<td style='padding-top:4px;padding-bottom:4px;padding-right:10px;'>";
				?>
				<input type="checkbox" value='{T}<?php echo $post_type; ?>{T}' id="elp_note_cat[]" name="elp_note_cat[]">
				<?php echo $post_type; ?>
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
					$col=5;
				}
				$count = $count + 1;
			}
			echo "</tr></table>";
		}
		else
		{
			?>
			<input type="checkbox" value='' id="elp_no_custome[]" name="elp_no_custome[]"> No custom post type available
			<?php
		}
	  ?>
	  <p><?php _e('Please select your custom post type (Optional).', 'email-posts-to-subscribers'); ?></p><br />
	    
	  <label for="tag-display-status"><?php _e('Notification Status', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_note_status" id="elp_note_status">
        <option value='Enable' selected="selected">Send mail immediately when new post is published.</option>
		<option value='Cron'>Add to cron when new post is published and send via cron job.</option>
		<option value='Disable'>Disable notification.</option>
      </select>
      <p><?php _e('Select status for this notification.', 'email-posts-to-subscribers'); ?></p><br />
	  
	  <label for="tag-link"><?php _e('Enter mail subject.', 'email-posts-to-subscribers'); ?></label>
      <input name="elp_note_mailsubject" type="text" id="elp_note_mailsubject" value="" size="80" maxlength="225" />
      <p><?php _e('Enter your mail subject.', 'email-posts-to-subscribers'); ?> Keyword: ###POSTTITLE###</p><br />
	  
	  <label for="tag-link"><?php _e('Mail content', 'email-posts-to-subscribers'); ?></label>
	  <?php $settings_body = array( 'textarea_rows' => 15 ); ?>
      <?php wp_editor("", "elp_note_mailcontent", $settings_body);?>
      <p><?php _e('Please enter content for your mail.', 'email-posts-to-subscribers'); ?>
	  <br />Keywords: ###POSTTITLE###, ###POSTLINK###, ###POSTIMAGE###, ###POSTDESC###, ###POSTFULL###, ###DATE###, ###POSTLINK-ONLY###, ###POSTLINK-WITHTITLE###</p>
	  
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