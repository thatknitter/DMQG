<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
elp_cls_common::elp_check_latest_update();

// Form submitted, check the data
if (isset($_POST['frm_elp_display']) && $_POST['frm_elp_display'] == 'yes')
{
	$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
	if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }
	
	$elp_success = '';
	$elp_success_msg = FALSE;
	
	// First check if ID exist with requested ID
	$result = elp_cls_dbquerynote::elp_notification_count($did, "");
	if ($result != '1')
	{
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist.', 'email-posts-to-subscribers'); ?></strong></p></div><?php
	}
	else
	{
		// Form submitted, check the action
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '')
		{
			//	Just security thingy that wordpress offers us
			check_admin_referer('elp_form_show');
			
			//	Delete selected record from the table
			elp_cls_dbquerynote::elp_notification_delete($did);
			
			//	Set success message
			$elp_success_msg = TRUE;
			$elp_success = __('Selected record was successfully deleted.', 'email-posts-to-subscribers');
		}
	}
	
	if ($elp_success_msg == TRUE)
	{
		?><div class="updated fade"><p><strong><?php echo $elp_success; ?></strong></p></div><?php
	}
}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>notification/notification.js"></script>
<div class="wrap">
  <div id="icon-plugins" class="icon32"></div>
    <h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<h3><?php _e('Post Notification by Email ', 'email-posts-to-subscribers'); ?>  
	<a class="add-new-h2" href="<?php echo ELP_ADMINURL; ?>?page=elp-postnotification&amp;ac=add"><?php _e('Add New', 'email-posts-to-subscribers'); ?></a></h3>
    <div class="tool-box">
	<?php
	$myData = array();
	$myData = elp_cls_dbquerynote::elp_notification_select(0);
	?>
	<form name="frm_elp_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
			<th scope="col"><?php _e('Sno', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Notification mail subject', 'email-posts-to-subscribers'); ?></th>
            <th scope="col"><?php _e('Subscriber Group', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Categories / Custom Post', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Notification Status', 'email-posts-to-subscribers'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
			<th scope="col"><?php _e('Sno', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Notification mail subject', 'email-posts-to-subscribers'); ?></th>
            <th scope="col"><?php _e('Subscriber Group', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Categories / Custom Post', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Notification Status', 'email-posts-to-subscribers'); ?></th>
          </tr>
        </tfoot>
		<tbody>
			<?php 
			$i = 0;
			$displayisthere = FALSE;
			if(count($myData) > 0)
			{
				$i = 1;
				foreach ($myData as $data)
				{
					?>
					<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
					  	<td align="left"><?php echo $i; ?></td>
						<td><?php echo esc_html(stripslashes($data['elp_note_mailsubject'])); ?>
						<div class="row-actions">
						<span class="edit">
						<a title="Edit" href="<?php echo ELP_ADMINURL; ?>?page=elp-postnotification&amp;ac=edit&amp;did=<?php echo $data['elp_note_id']; ?>">
						<?php _e('Edit', 'email-posts-to-subscribers'); ?></a> 
						| <a title="Preview" href="<?php echo ELP_ADMINURL; ?>?page=elp-postnotification&amp;ac=preview&amp;guid=<?php echo $data['elp_note_guid']; ?>">
						<?php _e('Preview', 'email-posts-to-subscribers'); ?></a> 
						<?php if($data['elp_note_id'] > 1) { ?>
						| <a onClick="javascript:_elp_delete('<?php echo $data['elp_note_id']; ?>')" href="javascript:void(0);">
						<?php _e('Delete', 'email-posts-to-subscribers'); ?></a>
						<?php } ?>
						</span>
						</div>
						</td>
						<td><?php echo $data['elp_note_emailgroup']; ?></td>
						<td>
						<?php 
						$elp_note_postcat = str_replace("## -- ##", ", ", $data['elp_note_postcat']);
						$elp_note_postcat = str_replace("##", "", $elp_note_postcat);
						$elp_note_postcat = str_replace("{T}", "", $elp_note_postcat);
						$j=0;
						$caegorydisplay = explode(",", $elp_note_postcat);
						if(count($caegorydisplay) > 0)
						{
							for($j=0; $j < count($caegorydisplay); $j++)
							{
								echo $caegorydisplay[$j] . ", ";
								if (($j > 0) && ($j % 3 == 0)) 
								{
									echo "<br>";
								}
							}
						}
						?>
						</td>
						<td>
						<?php 
						if ($data['elp_note_status'] == "Enable")
						{
							echo "Send mail immediately<br> when new post is published.";
						}
						elseif ($data['elp_note_status'] == "Cron")
						{
							echo "Add to cron and send mail via cron job.";
						}
						else
						{
							echo elp_cls_common::elp_disp_status($data['elp_note_status']);
						}
						?>
						</td>
					</tr>
					<?php
					$i = $i+1;
				}
			}
			else
			{
				?><tr><td colspan="5" align="center"><?php _e('No records available.', 'email-posts-to-subscribers'); ?></td></tr><?php 
			}
			?>
		</tbody>
        </table>
		<?php wp_nonce_field('elp_form_show'); ?>
		<input type="hidden" name="frm_elp_display" value="yes"/>
      </form>
	  <div style="height:10px;"></div>
	  <p class="description">
	  This page is to configure automatic notification email to your subscribers as soon as a post is published on your blog.<br />
	  <?php echo ELP_OFFICIAL; ?></p>
	</div>
</div>