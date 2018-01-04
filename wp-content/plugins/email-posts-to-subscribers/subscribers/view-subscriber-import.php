<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$elp_errors = array();
$elp_success = '';
$elp_error_found = FALSE;
$csv = array();

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
	
	$extension = pathinfo($_FILES['elp_csv_name']['name'], PATHINFO_EXTENSION);
	//$extension = strtolower(end(explode('.', $_FILES['elp_csv_name']['name'])));
	$tmpname = $_FILES['elp_csv_name']['tmp_name'];
	
	$elp_email_status = isset($_POST['elp_email_status']) ? sanitize_text_field($_POST['elp_email_status']) : '';
	$elp_email_group = isset($_POST['elp_email_group']) ? sanitize_text_field($_POST['elp_email_group']) : '';
	if ($elp_email_group == '')
	{
		$elp_email_group = isset($_POST['elp_email_group_txt']) ? sanitize_text_field($_POST['elp_email_group_txt']) : '';
	}
	
	if($elp_email_group <> "")
	{
		$special_letters = elp_cls_common::elp_special_letters();
		if (preg_match($special_letters, $elp_email_group))
		{
			$elp_errors[] = __('Error: Special characters ([\'^$%&*()}{@#~?><>,|=_+\"]) are not allowed in the group name.', 'email-posts-to-subscribers');
			$elp_error_found = TRUE;
		}
	}
	
	if ($elp_email_status == '')
	{
		$elp_email_status = "Confirmed";
	}
	
	if ($elp_email_group == '')
	{
		$elp_email_group = "Public";
	}
	
	if($extension === 'csv')
	{
		$csv = elp_cls_common::elp_readcsv($tmpname);
	}
	
	if(count($csv) > 0)
	{
		$inserted = 0;
		$duplicate = 0;
		$invalid = 0;
		if ($elp_email_status != 'Confirmed' && $elp_email_status != 'Unconfirmed' && $elp_email_status != 'Unsubscribed' && $elp_email_status != 'Single Opt In')
		{
			$elp_email_status = "Confirmed";
		}
		for ($i = 0; $i < count($csv); $i++)
		{
			$inputdata = array($csv[$i][1], $csv[$i][0], $elp_email_status, $elp_email_group);
			$action = elp_cls_dbquery::elp_view_subscriber_ins($inputdata);
			if($action == "sus")
			{
				$inserted = $inserted + 1;
			}
			elseif($action == "ext")
			{
				$duplicate = $duplicate + 1;
			}
			elseif($action == "invalid")
			{
				$invalid = $invalid + 1;
			}
		}
		?>
		<div class="updated fade">
			<p><strong><?php echo $inserted; ?> <?php _e('Email(s) was successfully imported.', 'email-posts-to-subscribers'); ?></strong></p>
			<p><strong><?php echo $duplicate; ?> <?php _e('Email(s) are already in our database.', 'email-posts-to-subscribers'); ?></strong></p>
			<p><strong><?php echo $invalid; ?> <?php _e('Email(s) are invalid.', 'email-posts-to-subscribers'); ?></strong></p>
			<p><strong><a href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers">
			<?php _e('Click here', 'email-posts-to-subscribers'); ?></a> <?php _e(' to view the details', 'email-posts-to-subscribers'); ?></strong></p>
		</div>
		<?php
	}
	else
	{
		?>
		<div class="error fade">
			<p><strong><?php _e('File upload failed or no data available in the csv file.', 'email-posts-to-subscribers'); ?></strong></p>
		</div>
		<?php
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
	<form name="form_addemail" id="form_addemail" method="post" action="#" onsubmit="return _elp_importemail()" enctype="multipart/form-data">
      <h3><?php _e('Upload email', 'email-posts-to-subscribers'); ?></h3>
	  <label for="tag-image"><?php _e('Select csv file', 'email-posts-to-subscribers'); ?></label>
	  <input type="file" name="elp_csv_name" id="elp_csv_name" />
      <p><?php _e('Please select the input csv file. Please check official website for csv structure.', 'email-posts-to-subscribers'); ?></p>
	  
	   <label for="tag-email-status"><?php _e('Status', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_email_status" id="elp_email_status">
        <option value='Confirmed' selected="selected">Confirmed</option>
		<option value='Unconfirmed'>Unconfirmed</option>
		<option value='Unsubscribed'>Unsubscribed</option>
		<option value='Single Opt In'>Single Opt In</option>
      </select>
      <p><?php _e('Please select subscriber email status.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-email-group"><?php _e('Select (or) Create Group', 'email-posts-to-subscribers'); ?></label>
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
				?><option value='<?php echo $group["elp_email_group"]; ?>'><?php echo $group["elp_email_group"]; ?></option><?php
			}
		}
		?>
	  </select>
	  (or) 
	  <input name="elp_email_group_txt" type="text" id="elp_email_group_txt" maxlength="20" value="" />
      <p><?php _e('Please select or create group for this subscriber.', 'email-posts-to-subscribers'); ?></p>
	    
      <input type="hidden" name="elp_form_submit" value="yes"/>
	  <div style="padding-top:5px;"></div>
      <p>
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Upload CSV', 'email-posts-to-subscribers'); ?>" type="submit" />
		<input name="publish" lang="publish" class="button add-new-h2" onclick="_elp_redirect()" value="<?php _e('Back', 'email-posts-to-subscribers'); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="_elp_help()" value="<?php _e('Help', 'email-posts-to-subscribers'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('elp_form_add'); ?>
    </form>
</div>
<p class="description"><?php echo ELP_OFFICIAL; ?></p>
</div>