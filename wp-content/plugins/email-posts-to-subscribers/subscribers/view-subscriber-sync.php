<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$elp_errors = array();
$elp_success = '';
$elp_error_found = FALSE;
$elp_registered = "";
$elp_registered_group = "";
	
// Preset the form fields
$form = array(
	'elp_registered' => '',
	'elp_registered_group' => ''
);

// Form submitted, check the data
if (isset($_POST['elp_form_submit']) && $_POST['elp_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('elp_form_add');
	
	$form['elp_registered'] = isset($_POST['elp_registered']) ? $_POST['elp_registered'] : '';
	$form['elp_registered_group'] = isset($_POST['elp_registered_group']) ? $_POST['elp_registered_group'] : '';
	
	if ($form['elp_registered_group'] == '' && $form['elp_registered'] == "YES")
	{
		$elp_errors[] = __('Please select default group to newly registered user.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}

	//	No errors found, we can add this Group to the table
	if ($elp_error_found == FALSE)
	{
		update_option('elp_c_syncemail', $form );
		
		// Reset the form fields
		$form = array(
			'elp_registered' => '',
			'elp_registered_group' => ''
		);
		
		$elp_success = __('Sync email successfully updated.', 'email-posts-to-subscribers');
	}
}

$elp_c_syncemail= get_option('elp_c_syncemail', 'norecord');
if($elp_c_syncemail<> 'norecord' && $elp_c_syncemail<> "")
{
	$elp_registered = $elp_c_syncemail['elp_registered'];
	$elp_registered_group = $elp_c_syncemail['elp_registered_group'];
}

if ($elp_error_found == TRUE && isset($elp_errors[0]) == TRUE)
{
	?><div class="error fade"><p><strong><?php echo $elp_errors[0]; ?></strong></p></div><?php
}

if ($elp_error_found == FALSE && isset($elp_success[0]) == TRUE)
{
	?>
	<div class="updated fade">
		<p><strong><?php echo $elp_success; ?></strong></p>
	</div>
	<?php
}

?>
<script language="javaScript" src="<?php echo ELP_URL; ?>subscribers/view-subscriber.js"></script>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<form name="form_syncemail" method="post" action="#">
      <h3 class="title"><?php _e('Sync email', 'email-posts-to-subscribers'); ?></h3>
      
	  <label for="tag-image"><?php _e('Sync newly registered user', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_registered" id="elp_email_status">
        <option value='NO' <?php if($elp_registered == 'NO') { echo "selected='selected'" ; } ?>>NO</option>
		<option value='YES' <?php if($elp_registered == 'YES') { echo "selected='selected'" ; } ?>>YES</option>
      </select>
      <p><?php _e('Automatically add a newly registered user email address to subscribers list.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-display-status"><?php _e('Select default group', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_registered_group" id="elp_email_group">
		<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
		<?php
		$thisselected = "";
		$groups = array();
		$groups = elp_cls_dbquery::elp_view_subscriber_group();
		if(count($groups) > 0)
		{
			$i = 1;
			foreach ($groups as $group)
			{
				if($group["elp_email_group"] == $elp_registered_group) 
				{ 
					$thisselected = "selected='selected'" ; 
				}
				?><option value='<?php echo $group["elp_email_group"]; ?>' <?php echo $thisselected; ?>><?php echo $group["elp_email_group"]; ?></option><?php
				$thisselected = "";
			}
		}
		?>
	  </select>
      <p><?php _e('Please select default group to newly registered user.', 'email-posts-to-subscribers'); ?></p>
	    
      <input type="hidden" name="elp_form_submit" value="yes"/>
	  <div style="padding-top:5px;"></div>
      <p>
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Submit', 'email-posts-to-subscribers'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="_elp_redirect()" value="<?php _e('Cancel', 'email-posts-to-subscribers'); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="_elp_help()" value="<?php _e('Help', 'email-posts-to-subscribers'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('elp_form_add'); ?>
    </form>
</div>
<p class="description"><?php echo ELP_OFFICIAL; ?></p>
</div>