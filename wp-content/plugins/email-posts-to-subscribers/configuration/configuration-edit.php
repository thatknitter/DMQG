<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }

// First check if ID exist with requested ID
$result = elp_cls_dbquery::elp_configuration_count($did);
if ($result != '1')
{
	?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist.', 'email-posts-to-subscribers'); ?></strong></p></div><?php
}
else
{
	$elp_errors = array();
	$elp_success = '';
	$elp_error_found = FALSE;
	
	$data = array();
	$data = elp_cls_dbquery::elp_configuration_select($did, 0, 0);
	
	// Preset the form fields
	$form = array(
		'elp_set_name' => $data['elp_set_name'],
		'elp_set_templid' => $data['elp_set_templid'],
		'elp_set_totalsent' => $data['elp_set_totalsent'],
		'elp_set_unsubscribelink' => $data['elp_set_unsubscribelink'],
		'elp_set_viewstatus' => $data['elp_set_viewstatus'],
		'elp_set_postcount' => $data['elp_set_postcount'],
		'elp_set_postcategory' => $data['elp_set_postcategory'],
		'elp_set_postorderby' => $data['elp_set_postorderby'],
		'elp_set_postorder' => $data['elp_set_postorder'],
		'elp_set_scheduleday' => $data['elp_set_scheduleday'],
		'elp_set_scheduletime' => $data['elp_set_scheduletime'],
		'elp_set_scheduletype' => $data['elp_set_scheduletype'],
		'elp_set_status' => $data['elp_set_status'],
		'elp_set_emaillistgroup' => $data['elp_set_emaillistgroup']
	);
}
// Form submitted, check the data
if (isset($_POST['elp_form_submit']) && $_POST['elp_form_submit'] == 'yes')
{
	//	Just security thingy that wordpress offers us
	check_admin_referer('elp_form_edit');
	
	$form['elp_set_name'] = isset($_POST['elp_set_name']) ? sanitize_text_field($_POST['elp_set_name']) : '';
	if ($form['elp_set_name'] == '')
	{
		$elp_errors[] = __('Please enter mail subject.', 'email-posts-to-subscribers');
		$elp_error_found = TRUE;
	}
	$form['elp_set_templid'] 		= isset($_POST['elp_set_templid']) ? sanitize_text_field($_POST['elp_set_templid']) : '';
	$form['elp_set_totalsent'] 		= isset($_POST['elp_set_totalsent']) ? sanitize_text_field($_POST['elp_set_totalsent']) : '';
	$form['elp_set_unsubscribelink'] = isset($_POST['elp_set_unsubscribelink']) ? sanitize_text_field($_POST['elp_set_unsubscribelink']) : '';
	$form['elp_set_viewstatus'] 	= isset($_POST['elp_set_viewstatus']) ? sanitize_text_field($_POST['elp_set_viewstatus']) : '';
	
	$form['elp_set_postcount'] 		= isset($_POST['elp_set_postcount']) ? sanitize_text_field($_POST['elp_set_postcount']) : '';
	$form['elp_set_postcategory'] 	= isset($_POST['elp_set_postcategory']) ? sanitize_text_field($_POST['elp_set_postcategory']) : '';
	$form['elp_set_postorderby'] 	= isset($_POST['elp_set_viewstatus']) ? sanitize_text_field($_POST['elp_set_postorderby']) : '';
	$form['elp_set_postorder'] 		= isset($_POST['elp_set_postorder']) ? sanitize_text_field($_POST['elp_set_postorder']) : '';
	
	$elp_set_scheduleday 			= isset($_POST['elp_set_scheduleday']) ? $_POST['elp_set_scheduleday'] : '';
	$form['elp_set_scheduletime'] 	= isset($_POST['elp_set_scheduletime']) ? sanitize_text_field($_POST['elp_set_scheduletime']) : '';
	$form['elp_set_scheduletype'] 	= isset($_POST['elp_set_scheduletype']) ? sanitize_text_field($_POST['elp_set_scheduletype']) : '';
	$form['elp_set_status'] 		= isset($_POST['elp_set_status']) ? sanitize_text_field($_POST['elp_set_status']) : '';
	$elp_set_emaillistgroup 		= isset($_POST['elp_set_emaillistgroup']) ? $_POST['elp_set_emaillistgroup'] : '';
	
	if(count($elp_set_emaillistgroup) > 0 && $elp_set_emaillistgroup <> "")
	{
		$form['elp_set_emaillistgroup'] = implode(", ", $elp_set_emaillistgroup);
	}
	else
	{
		$form['elp_set_emaillistgroup'] = "Public";
	}

	//	No errors found, we can add this Group to the table
	if ($elp_error_found == FALSE)
	{	
	
		$total = count($elp_set_scheduleday);
		$listdays = "";
		if( $total > 0 )
		{
			for($i=0; $i<$total; $i++)
			{
				$listdays= $listdays . " #" . $elp_set_scheduleday[$i] . "# ";
				if($i <> ($total - 1))
				{
					$listdays = $listdays .  "--";
				}
			}
		}
		$form['elp_set_scheduleday'] = $listdays;
		
		$inputdata = array($did, $form['elp_set_name'], $form['elp_set_templid'], $form['elp_set_totalsent'], $form['elp_set_unsubscribelink'], 
						 	$form['elp_set_viewstatus'],$form['elp_set_postcount'], $form['elp_set_postcategory'],$form['elp_set_postorderby'], $form['elp_set_postorder']
							, $form['elp_set_scheduleday'], $form['elp_set_scheduletime'], $form['elp_set_scheduletype'], $form['elp_set_status'], $form['elp_set_emaillistgroup']);
		$action = elp_cls_dbquery::elp_configuration_ins("update", $inputdata);
		if($action)
		{
			$elp_success = __('Mail configuration was successfully updated.', 'email-posts-to-subscribers');
		}
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
if ($elp_error_found == FALSE && strlen($elp_success) > 0)
{
	?>
	<div class="updated fade">
		<p><strong><?php echo $elp_success; ?> 
		<a href="<?php echo ELP_ADMINURL; ?>?page=elp-configuration">
		<?php _e('Click here', 'email-posts-to-subscribers'); ?></a> <?php _e(' to view the details', 'email-posts-to-subscribers'); ?></strong></p>
	</div>
	<?php
}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>configuration/configuration.js"></script>
<div class="form-wrap">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<form name="elp_form" method="post" action="#" onsubmit="return _elp_submit()"  >
      <h3><?php _e('Edit configuration', 'email-posts-to-subscribers'); ?></h3>
      
	  <label for="tag-image"><?php _e('Mail subject', 'email-posts-to-subscribers'); ?></label>
      <input name="elp_set_name" type="text" id="elp_set_name" value="<?php echo $form['elp_set_name']; ?>" maxlength="225" size="30"  />
      <p><?php _e('Please enter mail subject.', 'email-posts-to-subscribers'); ?></p>
	   
	  <label for="tag-display-status"><?php _e('Template', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_set_templid" id="elp_set_templid">
		<option value='' selected="selected">Select</option>
		<?php
		$Templates = array();
		$Templates = elp_cls_dbquery::elp_template_select(0, "System");
		$selected = "";
		if(count($Templates) > 0)
		{
			foreach ($Templates as $Template)
			{
				if($form['elp_set_templid'] == $Template['elp_templ_id'] ) 
				{
					$selected = "selected"; 
				}
				?><option value='<?php echo $Template['elp_templ_id']; ?>' <?php echo $selected; ?>><?php echo $Template['elp_templ_heading']; ?></option><?php
				$selected = "";
			}
		}
		?>
	  </select>
      <p><?php _e('Please select template for this configuration.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-image"><?php _e('Status', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_set_status" id="elp_set_status">
	 	<option value='On' <?php if($form['elp_set_status']=='On') { echo 'selected="selected"' ; } ?>>On</option>
		<option value='Off' <?php if($form['elp_set_status']=='Off') { echo 'selected="selected"' ; } ?>>Off</option>
	  </select>
      <p><?php _e('Enable or Disbale this mail configuration.', 'email-posts-to-subscribers'); ?></p>
	   
	  <label for="tag-image"><?php _e('Subscribers group', 'email-posts-to-subscribers'); ?></label>
	  <?php
		$groups = array();
		$thisselected = "";
		$groups = elp_cls_dbquery::elp_view_subscriber_group();
		if(count($groups) > 0)
		{
			echo "<table border='0' cellspacing='0'><tr>";
			$col=4;
			$i = 1;
			foreach ($groups as $group)
			{
				$selected = strpos($form['elp_set_emaillistgroup'], $group["elp_email_group"]);
				echo "<td style='padding-top:4px;padding-bottom:4px;padding-right:10px;'>";
				?>
				<input <?php if ($selected !== false) { echo 'checked="checked"'; } ?> type="checkbox" value='<?php echo $group["elp_email_group"]; ?>' id="elp_set_emaillistgroup[]" name="elp_set_emaillistgroup[]"> 
				<?php echo stripslashes($group["elp_email_group"]); ?>
				<?php
				if($col > 1) 
				{
					$col = $col-1;
					echo "</td><td>"; 
				}
				elseif($col = 1)
				{
					$col = $col-1;
					echo "</td></tr><tr>";;
					$col=3;
				}
			}
			echo "</tr></table>";
		}
		else
		{
			?>
				<input type="checkbox" value='Public' id="elp_set_emaillistgroup[]" name="elp_set_emaillistgroup[]">Public
			<?php
		}
	  ?>
	  <p><?php _e('Select subscribers group for this configuration.', 'email-posts-to-subscribers'); ?></p>
	  
	  <h3><?php _e('Post Details', 'email-posts-to-subscribers'); ?></h3>
	  <label for="tag-image"><?php _e('Post count.', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_set_postcount" id="elp_set_postcount">
		<option value='1' <?php if($form['elp_set_postcount']=='1') { echo 'selected="selected"' ; } ?>>1</option>
		<option value='2' <?php if($form['elp_set_postcount']=='2') { echo 'selected="selected"' ; } ?>>2</option>
		<option value='3' <?php if($form['elp_set_postcount']=='3') { echo 'selected="selected"' ; } ?>>3</option>
		<option value='4' <?php if($form['elp_set_postcount']=='4') { echo 'selected="selected"' ; } ?>>4</option>
		<option value='5' <?php if($form['elp_set_postcount']=='5') { echo 'selected="selected"' ; } ?>>5</option>
		<option value='6' <?php if($form['elp_set_postcount']=='6') { echo 'selected="selected"' ; } ?>>6</option>
		<option value='7' <?php if($form['elp_set_postcount']=='7') { echo 'selected="selected"' ; } ?>>7</option>
		<option value='8' <?php if($form['elp_set_postcount']=='8') { echo 'selected="selected"' ; } ?>>8</option>
		<option value='9' <?php if($form['elp_set_postcount']=='9') { echo 'selected="selected"' ; } ?>>9</option>
		<option value='10' <?php if($form['elp_set_postcount']=='10') { echo 'selected="selected"' ; } ?>>10</option>
		<option value='12' <?php if($form['elp_set_postcount']=='12') { echo 'selected="selected"' ; } ?>>12</option>
		<option value='15' <?php if($form['elp_set_postcount']=='15') { echo 'selected="selected"' ; } ?>>15</option>
		<option value='20' <?php if($form['elp_set_postcount']=='20') { echo 'selected="selected"' ; } ?>>20</option>		
		<option value='100' <?php if($form['elp_set_postcount']=='100') { echo 'selected="selected"' ; } ?>>Published Today</option>
		<option value='110' <?php if($form['elp_set_postcount']=='110') { echo 'selected="selected"' ; } ?>>Published Last 2 Days (Published Yesterday)</option>
		<option value='120' <?php if($form['elp_set_postcount']=='120') { echo 'selected="selected"' ; } ?>>Published Last 3 Days</option>
		<option value='130' <?php if($form['elp_set_postcount']=='130') { echo 'selected="selected"' ; } ?>>Published Last 4 Days</option>
		<option value='140' <?php if($form['elp_set_postcount']=='140') { echo 'selected="selected"' ; } ?>>Published Last 5 Days</option>
		<option value='150' <?php if($form['elp_set_postcount']=='150') { echo 'selected="selected"' ; } ?>>Published Last 6 Days</option>
		<option value='160' <?php if($form['elp_set_postcount']=='160') { echo 'selected="selected"' ; } ?>>Published This Week</option>
		<option value='170' <?php if($form['elp_set_postcount']=='170') { echo 'selected="selected"' ; } ?>>Published This Month</option>
		<option value='180' <?php if($form['elp_set_postcount']=='180') { echo 'selected="selected"' ; } ?>>Published Last 7 Days</option>
		<option value='190' <?php if($form['elp_set_postcount']=='190') { echo 'selected="selected"' ; } ?>>Published Last 8 Days</option>
		<option value='200' <?php if($form['elp_set_postcount']=='200') { echo 'selected="selected"' ; } ?>>Published Last 9 Days</option>
	  </select>
      <p><?php _e('Number of post to add in the email.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-image"><?php _e('Post categories', 'email-posts-to-subscribers'); ?></label>
      <input name="elp_set_postcategory" type="text" id="elp_set_postcategory" value="<?php echo $form['elp_set_postcategory']; ?>" maxlength="225" size="30"  />
      <p><?php _e('Please enter category IDs, separated by commas.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-image"><?php _e('Post orderbys', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_set_postorderby" id="elp_set_postorderby">
		<option value='ID' <?php if($form['elp_set_postorderby']=='ID') { echo 'selected="selected"' ; } ?>>ID</option>
		<option value='author' <?php if($form['elp_set_postorderby']=='author') { echo 'selected="selected"' ; } ?>>author</option>
		<option value='title' <?php if($form['elp_set_postorderby']=='title') { echo 'selected="selected"' ; } ?>>title</option>
		<option value='rand' <?php if($form['elp_set_postorderby']=='rand') { echo 'selected="selected"' ; } ?>>rand</option>
		<option value='date' <?php if($form['elp_set_postorderby']=='date') { echo 'selected="selected"' ; } ?>>date</option>
		<option value='modified' <?php if($form['elp_set_postorderby']=='modified') { echo 'selected="selected"' ; } ?>>modified</option>
	  </select>
      <p><?php _e('Select your post orderbys', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-image"><?php _e('Post order', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_set_postorder" id="elp_set_postorder">
		<option value='DESC' <?php if($form['elp_set_postorder']=='DESC') { echo 'selected="selected"' ; } ?>>DESC</option>
		<option value='ASC' <?php if($form['elp_set_postorder']=='ASC') { echo 'selected="selected"' ; } ?>>ASC</option>
	  </select>
      <p><?php _e('Select your post order', 'email-posts-to-subscribers'); ?></p>
	  
	  
	  <h3><?php _e('Mail Setting', 'email-posts-to-subscribers'); ?></h3>
	  
	  <label for="tag-image"><?php _e('Max mail count for one trigger.', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_set_totalsent" id="elp_set_totalsent">
		<option value='100' <?php if($form['elp_set_totalsent']=='100') { echo 'selected="selected"' ; } ?>>100</option>
		<option value='200' <?php if($form['elp_set_totalsent']=='200') { echo 'selected="selected"' ; } ?>>200</option>
		<option value='500' <?php if($form['elp_set_totalsent']=='500') { echo 'selected="selected"' ; } ?>>500</option>
		<option value='700' <?php if($form['elp_set_totalsent']=='700') { echo 'selected="selected"' ; } ?>>700</option>
		<option value='1000' <?php if($form['elp_set_totalsent']=='1000') { echo 'selected="selected"' ; } ?>>1000</option>
		<option value='1500' <?php if($form['elp_set_totalsent']=='1500') { echo 'selected="selected"' ; } ?>>1500</option>
		<option value='2000' <?php if($form['elp_set_totalsent']=='2000') { echo 'selected="selected"' ; } ?>>2000</option>
		<option value='2500' <?php if($form['elp_set_totalsent']=='2500') { echo 'selected="selected"' ; } ?>>2500</option>
		<option value='3000' <?php if($form['elp_set_totalsent']=='3000') { echo 'selected="selected"' ; } ?>>3000</option>
		<option value='5000' <?php if($form['elp_set_totalsent']=='5000') { echo 'selected="selected"' ; } ?>>5000</option>
		<option value='9999' <?php if($form['elp_set_totalsent']=='9999') { echo 'selected="selected"' ; } ?>>9999</option>
	  </select>
      <p><?php _e('How many emails you want to send at one cron trigger', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-image"><?php _e('Add unsubscribe link', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_set_unsubscribelink" id="elp_set_unsubscribelink">
		<option value='YES' <?php if($form['elp_set_unsubscribelink']=='YES') { echo 'selected="selected"' ; } ?>>YES</option>
	  </select>
      <p><?php _e('Do you want to add unsubscribe link with this mail configuration.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-image"><?php _e('View status (BETA)', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_set_viewstatus" id="elp_set_viewstatus">
		<option value='YES' <?php if($form['elp_set_viewstatus']=='YES') { echo 'selected="selected"' ; } ?>>YES</option>
	  </select>
      <p><?php _e('Like to track whether that email is viewed or not?', 'email-posts-to-subscribers'); ?></p>
	  
	  <h3><?php _e('Schedule Details', 'email-posts-to-subscribers'); ?></h3>
	  
	  <?php 
	  if($form['elp_set_scheduleday'] == "")
	  {
	  	$elp_set_scheduleday = "##";
	  }
	  else
	  {
	  	$elp_set_scheduleday = $form['elp_set_scheduleday'];
	  }
	  
	  $a = strpos($elp_set_scheduleday, "#0#");
	  $b = strpos($elp_set_scheduleday, "#1#");
	  $c = strpos($elp_set_scheduleday, "#2#");
	  $d = strpos($elp_set_scheduleday, "#3#");
	  $e = strpos($elp_set_scheduleday, "#4#");
	  $f = strpos($elp_set_scheduleday, "#5#");
	  $g = strpos($elp_set_scheduleday, "#6#");
	  ?>
	  <label for="tag-image"><?php _e('Select Day', 'email-posts-to-subscribers'); ?></label>
		<table width="300" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100" height="25"><input <?php if ($a !== false) { echo 'checked="checked"'; } ?> type="checkbox" value='0' id="elp_set_scheduleday[]" name="elp_set_scheduleday[]"> Sunday</td>
			<td width="100"><input <?php if ($b !== false) { echo 'checked="checked"'; } ?> type="checkbox" value='1' id="elp_set_scheduleday[]" name="elp_set_scheduleday[]"> Monday</td>
			<td width="100"><input <?php if ($c !== false) { echo 'checked="checked"'; } ?> type="checkbox" value='2' id="elp_set_scheduleday[]" name="elp_set_scheduleday[]"> Tuesday</td>
		</tr>
		<tr>
			<td width="100" height="25"><input <?php if ($d !== false) { echo 'checked="checked"'; } ?> type="checkbox" value='3' id="elp_set_scheduleday[]" name="elp_set_scheduleday[]"> Wednesday</td>
			<td><input <?php if ($e !== false) { echo 'checked="checked"'; } ?> type="checkbox" value='4' id="elp_set_scheduleday[]" name="elp_set_scheduleday[]"> Thursday</td>
			<td><input <?php if ($f !== false) { echo 'checked="checked"'; } ?> type="checkbox" value='5' id="elp_set_scheduleday[]" name="elp_set_scheduleday[]"> Friday</td>
		</tr>
		<tr>
			<td width="100" height="25"><input <?php if ($g !== false) { echo 'checked="checked"'; } ?> type="checkbox" value='6' id="elp_set_scheduleday[]" name="elp_set_scheduleday[]"> Saturday</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		</table>
	  <p><?php _e('Select the day you wants to trigger email list.', 'email-posts-to-subscribers'); ?></p>
	  
	  <label for="tag-image"><?php _e('Select Time', 'email-posts-to-subscribers'); ?></label>
	  <select name="elp_set_scheduletime" id="elp_set_scheduletime">
		<option value='00:00:00'>00:00:00</option>
	  </select>
	  <p><?php _e('Select the time you wants to trigger email list.', 'email-posts-to-subscribers'); ?></p>
	   
	  <label for="tag-image"><?php _e('Schedule Type', 'email-posts-to-subscribers'); ?></label>
      <select name="elp_set_scheduletype" id="elp_set_scheduletype">
		<option value='Cron'>Cron</option>
		<!--<option value='Auto'>Auto</option>-->
	  </select>
      <p><?php //_e('', 'email-posts-to-subscribers'); ?></p>
	  
      <input type="hidden" name="elp_form_submit" value="yes"/>
	  <div style="padding-top:5px;"></div>
      <p>
        <input name="publish" lang="publish" class="button add-new-h2" value="<?php _e('Update Details', 'email-posts-to-subscribers'); ?>" type="submit" />
        <input name="publish" lang="publish" class="button add-new-h2" onclick="_elp_redirect()" value="<?php _e('Cancel', 'email-posts-to-subscribers'); ?>" type="button" />
        <input name="Help" lang="publish" class="button add-new-h2" onclick="_elp_help()" value="<?php _e('Help', 'email-posts-to-subscribers'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('elp_form_edit'); ?>
    </form>
</div>
<p class="description"><?php echo ELP_OFFICIAL; ?></p>
</div>