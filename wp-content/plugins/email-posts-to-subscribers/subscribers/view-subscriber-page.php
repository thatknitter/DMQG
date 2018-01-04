<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
// Form submitted, check the data
$pg = isset($_GET['pg']) ? sanitize_text_field($_GET['pg']) : '1';
$tot = isset($_GET['tot']) ? sanitize_text_field($_GET['tot']) : '100';
$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '1';
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>subscribers/view-subscriber.js"></script>
<div class="wrap">
  <div id="icon-plugins" class="icon32"></div>
  <h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
  <div class="tool-box">
  <h3><?php _e('View subscriber', 'email-posts-to-subscribers'); ?></h3>
	<table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <th class="check-column" scope="col"><input type="checkbox" name="chk_delete[]" id="chk_delete[]" /></th>
            <th scope="col"><?php _e('Sno', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Email', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Name', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Status', 'email-posts-to-subscribers'); ?></th>
            <th scope="col"><?php _e('Database ID', 'email-posts-to-subscribers'); ?></th>
          </tr>
        </thead>
        <tfoot>
          <tr>
            <th class="check-column" scope="col"><input type="checkbox" name="chk_delete[]" id="chk_delete[]" /></th>
            <th scope="col"><?php _e('Sno', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Email address', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Name', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Status', 'email-posts-to-subscribers'); ?></th>
            <th scope="col"><?php _e('Database ID', 'email-posts-to-subscribers'); ?></th>
          </tr>
        </tfoot>
		<?php
		$subscribers = array();
		$offset = ($pg - 1) * $tot;
		$subscribers = elp_cls_dbquery::elp_view_subscriber_cron($offset, $tot);	
		$i = 1;
		if(count($subscribers) > 0)
		{
			foreach ($subscribers as $data)
			{
				?>
				<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
					<td align="left"><input name="chk_delete[]" id="chk_delete[]" type="checkbox" value="<?php echo $data['elp_email_id'] ?>" /></td>
					<td><?php echo $i; ?></td>
					<td><?php echo $data['elp_email_mail']; ?></td>
					<td><?php echo $data['elp_email_name']; ?></td>     
					<td><?php echo elp_cls_common::elp_disp_status($data['elp_email_status']); ?></td>
					<td><?php echo $data['elp_email_id']; ?></td>
				</tr>
				<?php
				$i = $i+1;
			}
		}
		else
		{
			?>
			<tr>
				<td colspan="6" align="center"><?php _e('No records available.', 'email-posts-to-subscribers'); ?></td>
			</tr>
			<?php 
		}
		?>
		</tbody>
      </table>
	<div style="padding-top:10px;"></div>
    <div class="tablenav">
		<div class="alignleft">
			<a class="button add-new-h2" href="<?php echo ELP_ADMINURL; ?>?page=elp-configuration&ac=cron&did=<?php echo $did; ?>"><?php _e('Back', 'email-posts-to-subscribers'); ?></a> &nbsp;
			<a class="button add-new-h2" target="_blank" href="<?php echo ELP_FAV; ?>"><?php _e('Help', 'email-posts-to-subscribers'); ?></a> 
		</div>
    </div>
 	<p class="description"><?php echo ELP_OFFICIAL; ?></p>
  </div>
</div>