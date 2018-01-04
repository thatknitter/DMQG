<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$elp_errors 		= array();
$elp_success 		= '';
$elp_error_found 	= FALSE;

$elp_role_subscriber 	= "";
$elp_role_templates 	= "";
$elp_role_mailconfig 	= "";
$elp_role_crondetails 	= "";
$elp_role_setting 		= "";
$elp_role_sendemail 	= "";
$elp_role_sentmail 		= "";
$elp_role_roles 		= "";
$elp_role_help 			= "";

// Preset the form fields
$form = array(
	'elp_role_subscriber' 	=> '',
	'elp_role_templates' 	=> '',
	'elp_role_mailconfig' 	=> '',
	'elp_role_crondetails' 	=> '',
	'elp_role_setting'		=> '',
	'elp_role_sendemail'	=> '',
	'elp_role_sentmail' 	=> '',
	'elp_role_roles' 		=> '',
	'elp_role_help' 		=> ''
);

// Form submitted, check the data
if (isset($_POST['elp_form_submit']) && $_POST['elp_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('elp_rolelp_add');
	
	$form['elp_role_subscriber'] 	= isset($_POST['elp_role_subscriber']) ? $_POST['elp_role_subscriber'] : '';
	$form['elp_role_templates'] 	= isset($_POST['elp_role_templates']) ? $_POST['elp_role_templates'] : '';
	$form['elp_role_mailconfig'] 	= isset($_POST['elp_role_mailconfig']) ? $_POST['elp_role_mailconfig'] : '';
	$form['elp_role_crondetails'] 	= isset($_POST['elp_role_crondetails']) ? $_POST['elp_role_crondetails'] : '';
	$form['elp_role_setting'] 		= isset($_POST['elp_role_setting']) ? $_POST['elp_role_setting'] : '';
	$form['elp_role_sendemail'] 	= isset($_POST['elp_role_sendemail']) ? $_POST['elp_role_sendemail'] : '';
	$form['elp_role_sentmail'] 		= isset($_POST['elp_role_sentmail']) ? $_POST['elp_role_sentmail'] : '';
	$form['elp_role_roles'] 		= isset($_POST['elp_role_roles']) ? $_POST['elp_role_roles'] : '';
	$form['elp_role_help'] 			= isset($_POST['elp_role_help']) ? $_POST['elp_role_help'] : '';
		
	//	No errors found, we can add this Group to the table
	if ($elp_error_found == FALSE)
	{
		update_option('elp_c_rolesandcapabilities', $form );
					
		// Reset the form fields
		$form = array(
			'elp_role_subscriber' 	=> '',
			'elp_role_templates' 	=> '',
			'elp_role_mailconfig' 	=> '',
			'elp_role_crondetails' 	=> '',
			'elp_role_setting' 		=> '',
			'elp_role_sendemail' 	=> '',
			'elp_role_sentmail' 	=> '',
			'elp_role_roles' 		=> '',
			'elp_role_help' 		=> ''
		);
	}
}

$elp_c_rolesandcapabilities = get_option('elp_c_rolesandcapabilities', 'norecord');
if($elp_c_rolesandcapabilities <> 'norecord' && $elp_c_rolesandcapabilities <> "")
{
	$elp_role_subscriber 	= $elp_c_rolesandcapabilities['elp_role_subscriber'];
	$elp_role_templates 	= $elp_c_rolesandcapabilities['elp_role_templates'];
	$elp_role_mailconfig 	= $elp_c_rolesandcapabilities['elp_role_mailconfig'];
	$elp_role_crondetails 	= $elp_c_rolesandcapabilities['elp_role_crondetails'];
	$elp_role_setting 		= $elp_c_rolesandcapabilities['elp_role_setting'];
	$elp_role_sendemail 	= $elp_c_rolesandcapabilities['elp_role_sendemail'];
	$elp_role_sentmail 		= $elp_c_rolesandcapabilities['elp_role_sentmail'];
	$elp_role_roles 		= $elp_c_rolesandcapabilities['elp_role_roles'];
	$elp_role_help 			= $elp_c_rolesandcapabilities['elp_role_help'];
}

if ($elp_error_found == TRUE && isset($elp_errors[0]) == TRUE)
{
	?><div class="error fade"><p><strong><?php echo $elp_errors[0]; ?></strong></p></div><?php
}
if ($elp_error_found == FALSE && isset($elp_success[0]) == TRUE)
{
	?>
	<div class="updated fade">
	<p><strong>
	<?php echo $elp_success; ?>
	<a href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers"><?php _e('Click here', 'email-posts-to-subscribers'); ?></a> <?php _e(' to view the details', 'email-posts-to-subscribers'); ?>
	</strong></p>
	</div>
	<?php
}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>roles/roles.js"></script>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<form name="form_roles" method="post" action="#">
      <h3 class="title"><?php _e('Roles and Capabilities', 'email-posts-to-subscribers'); ?></h3>
      
	  <label for="tag-image"><?php _e('Subscribers Menu', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_role_subscriber" id="elp_role_subscriber">
		<option value='manage_options' <?php if($elp_role_subscriber == 'manage_options') { echo "selected='selected'" ; } ?>>Administrator Only</option>
		<option value='edit_others_pages' <?php if($elp_role_subscriber == 'edit_others_pages') { echo "selected='selected'" ; } ?>>Administrator/Editor</option>
		<option value='edit_posts' <?php if($elp_role_subscriber == 'edit_posts') { echo "selected='selected'" ; } ?>>Administrator/Editor/Author/Contributor</option>
	  </select>
      <p><?php _e('Select user role to access plugin Subscribers Menu. Only Admin user can change this value.', 'email-posts-to-subscribers'); ?></p>
	  
	  
	  <label for="tag-image"><?php _e('Templates Menu', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_role_templates" id="elp_role_templates">
		<option value='manage_options' <?php if($elp_role_templates == 'manage_options') { echo "selected='selected'" ; } ?>>Administrator Only</option>
		<option value='edit_others_pages' <?php if($elp_role_templates == 'edit_others_pages') { echo "selected='selected'" ; } ?>>Administrator/Editor</option>
		<option value='edit_posts' <?php if($elp_role_templates == 'edit_posts') { echo "selected='selected'" ; } ?>>Administrator/Editor/Author/Contributor</option>
	  </select>
      <p><?php _e('Select user role to access plugin Compose Menu. Only Admin user can change this value.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-display-status"><?php _e('Mail Configuration Menu', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_role_mailconfig" id="elp_role_mailconfig">
		<option value='manage_options' <?php if($elp_role_mailconfig == 'manage_options') { echo "selected='selected'" ; } ?>>Administrator Only</option>
		<option value='edit_others_pages' <?php if($elp_role_mailconfig == 'edit_others_pages') { echo "selected='selected'" ; } ?>>Administrator/Editor</option>
		<option value='edit_posts' <?php if($elp_role_mailconfig == 'edit_posts') { echo "selected='selected'" ; } ?>>Administrator/Editor/Author/Contributor</option>
	  </select>
      <p><?php _e('Select user role to access plugin Notification Menu. Only Admin user can change this value.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-display-status"><?php _e('Cron Details Menu', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_role_crondetails" id="elp_role_crondetails">
		<option value='manage_options' <?php if($elp_role_crondetails == 'manage_options') { echo "selected='selected'" ; } ?>>Administrator Only</option>
		<option value='edit_others_pages' <?php if($elp_role_crondetails == 'edit_others_pages') { echo "selected='selected'" ; } ?>>Administrator/Editor</option>
		<option value='edit_posts' <?php if($elp_role_crondetails == 'edit_posts') { echo "selected='selected'" ; } ?>>Administrator/Editor/Author/Contributor</option>
	  </select>
      <p><?php _e('Select user role to access plugin Send Email Menu. Only Admin user can change this value.', 'email-posts-to-subscribers'); ?></p>
	    
	  <label for="tag-display-status"><?php _e('Send Email Menu', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_role_sendemail" id="elp_role_sendemail">
		<option value='manage_options' <?php if($elp_role_sendemail == 'manage_options') { echo "selected='selected'" ; } ?>>Administrator Only</option>
		<option value='edit_others_pages' <?php if($elp_role_sendemail == 'edit_others_pages') { echo "selected='selected'" ; } ?>>Administrator/Editor</option>
		<option value='edit_posts' <?php if($elp_role_sendemail == 'edit_posts') { echo "selected='selected'" ; } ?>>Administrator/Editor/Author/Contributor</option>
	  </select>
      <p><?php _e('Select user role to access plugin Sent Mails Menu. Only Admin user can change this value.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-display-status"><?php _e('Sent Mail Menu', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_role_sentmail" id="elp_role_sentmail">
		<option value='manage_options' <?php if($elp_role_sentmail == 'manage_options') { echo "selected='selected'" ; } ?>>Administrator Only</option>
		<option value='edit_others_pages' <?php if($elp_role_sentmail == 'edit_others_pages') { echo "selected='selected'" ; } ?>>Administrator/Editor</option>
		<option value='edit_posts' <?php if($elp_role_sentmail == 'edit_posts') { echo "selected='selected'" ; } ?>>Administrator/Editor/Author/Contributor</option>
	  </select>
      <p><?php _e('Select user role to access plugin Help & Info Menu. Only Admin user can change this value.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-display-status"><?php _e('Settings Menu', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_role_setting" id="elp_role_setting">
		<option value='manage_options' <?php if($elp_role_setting == 'manage_options') { echo "selected='selected'" ; } ?>>Administrator Only</option>
		<option value='edit_others_pages' <?php if($elp_role_setting == 'edit_others_pages') { echo "selected='selected'" ; } ?>>Administrator/Editor</option>
		<option value='edit_posts' <?php if($elp_role_setting == 'edit_posts') { echo "selected='selected'" ; } ?>>Administrator/Editor/Author/Contributor</option>
	  </select>
      <p><?php _e('Select user role to access plugin Settings Menu. Only Admin user can change this value.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-display-status"><?php _e('Roles Menu', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_role_roles" id="elp_role_roles">
		<option value='manage_options' <?php if($elp_role_roles == 'manage_options') { echo "selected='selected'" ; } ?>>Administrator Only</option>
		<!--<option value='edit_others_pages' <?php //if($elp_role_roles == 'edit_others_pages') { echo "selected='selected'" ; } ?>>Administrator/Editor</option>
		<option value='edit_posts' <?php //if($elp_role_roles == 'edit_posts') { echo "selected='selected'" ; } ?>>Administrator/Editor/Author/Contributor</option>-->
	  </select>
      <p><?php _e('Select user role to access plugin Settings Menu. Only Admin user can change this value.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-display-status"><?php _e('Help & Info Menu', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_role_help" id="elp_role_help">
		<option value='manage_options' <?php if($elp_role_help == 'manage_options') { echo "selected='selected'" ; } ?>>Administrator Only</option>
		<option value='edit_others_pages' <?php if($elp_role_help == 'edit_others_pages') { echo "selected='selected'" ; } ?>>Administrator/Editor</option>
		<option value='edit_posts' <?php if($elp_role_help == 'edit_posts') { echo "selected='selected'" ; } ?>>Administrator/Editor/Author/Contributor</option>
	  </select>
      <p><?php _e('Select user role to access plugin Settings Menu. Only Admin user can change this value.', 'email-posts-to-subscribers'); ?></p>
	  
      <input type="hidden" name="elp_form_submit" value="yes"/>
	  <div style="padding-top:5px;"></div>
      <p>
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Submit', 'email-posts-to-subscribers'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="_elp_redirect()" value="<?php _e('Cancel', 'email-posts-to-subscribers'); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="_elp_help()" value="<?php _e('Help', 'email-posts-to-subscribers'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('elp_rolelp_add'); ?>
    </form>
</div>
<p class="description"><?php echo ELP_OFFICIAL; ?></p>
</div>