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
	
	if (isset($_POST['frm_elp_bulkaction']) && $_POST['frm_elp_bulkaction'] != 'delete')
	{
		// First check if ID exist with requested ID
		$result = elp_cls_dbquery2::elp_sentmail_count($did);
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
				elp_cls_dbquery2::elp_sentmail_delete($did);
				
				//	Set success message
				$elp_success_msg = TRUE;
				$elp_success = __('Selected record was successfully deleted.', 'email-posts-to-subscribers');
			}
		}
	}
	else
	{
		check_admin_referer('elp_form_show');
		elp_cls_optimize::elp_optimize_setdetails();
		//	Set success message
		$elp_success_msg = TRUE;
		$elp_success = __('Successfully deleted all records except latest 20.', 'email-posts-to-subscribers');
	}	
	if ($elp_success_msg == TRUE)
	{
		?><div class="updated fade"><p><strong><?php echo $elp_success; ?></strong></p></div><?php
	}
}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>sentmail/sentmail.js"></script>
<div class="wrap">
  <div id="icon-plugins" class="icon32"></div>
    <h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<h3><?php _e('Sent Mail', 'email-posts-to-subscribers'); ?></h3>
    <div class="tool-box">
	<?php
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$limit = 30;
	$offset = ($pagenum - 1) * $limit;
	$total = elp_cls_dbquery2::elp_sentmail_count(0);
	$fulltotal = $total;
	$total = ceil( $total / $limit );

	$myData = array();
	$myData = elp_cls_dbquery2::elp_sentmail_select(0, $offset, $limit);
	?>
	<form name="frm_elp_display" method="post" onsubmit="return _elp_bulkaction()">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
			
			<th scope="col"><?php _e('View Reports', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Source', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Status', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Type', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Triggered', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Last Run', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Total', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Pending', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Completed', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Action', 'email-posts-to-subscribers'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
			
			<th scope="col"><?php _e('View Reports', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Source', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Status', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Type', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Triggered', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Last Run', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Total', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Pending', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Completed', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Action', 'email-posts-to-subscribers'); ?></th>
          </tr>
        </tfoot>
		<tbody>
			<?php 
			$i = 0;
			if(count($myData) > 0)
			{
				$i = 1;
				foreach ($myData as $data)
				{
					?>
					<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
						<td>
						<a title="Click For Report" href="<?php echo ELP_ADMINURL; ?>?page=elp-sentmail&amp;ac=delivery&amp;sentguid=<?php echo $data['elp_sent_guid']; ?>">
						<?php echo $data['elp_sent_guid']; ?>
						</a>
						</td>
						<td><?php echo elp_cls_common::elp_disp_status($data['elp_sent_source']); ?></td>
						<td><?php echo elp_cls_common::elp_disp_status($data['elp_sent_status']); ?></td>
						<td><?php echo elp_cls_common::elp_disp_status($data['elp_sent_type']); ?></td>
						<td><?php echo $data['elp_sent_starttime']; ?></td>
						<td><?php echo $data['elp_sent_endtime']; ?></td>
						<td><?php echo $data['elp_sent_count']; ?></td>
						<td><?php echo elp_cls_dbquery2::elp_delivery_queue_count($data['elp_sent_guid']); ?></td>
						<td><?php echo elp_cls_dbquery2::elp_delivery_completed_count($data['elp_sent_guid']); ?></td>
						<td>
						<a title="Mail Preview" href="<?php echo ELP_ADMINURL; ?>?page=elp-sentmail&amp;ac=preview&amp;did=<?php echo $data['elp_sent_id']; ?>&amp;pagenum=<?php echo $pagenum; ?>">
							<img alt="Delete" src="<?php echo ELP_URL; ?>images/preview.gif" />
						</a>&nbsp;&nbsp;
						<a title="Delete" onClick="javascript:_elp_delete('<?php echo $data['elp_sent_id']; ?>')" href="javascript:void(0);">
							<img alt="Delete" src="<?php echo ELP_URL; ?>images/delete.gif" />
						</a>
						</td>
					</tr>
					<?php
					$i = $i+1;
				}
			}
			else
			{
				?><tr><td colspan="9" align="center"><?php _e('No records available.', 'email-posts-to-subscribers'); ?></td></tr><?php 
			}
			?>
		</tbody>
        </table>
		<?php wp_nonce_field('elp_form_show'); ?>
		<input type="hidden" name="frm_elp_display" value="yes"/>
		<div style="padding-top:10px;"></div>
		<?php
		$page_links = paginate_links( array(
			'base' => add_query_arg( 'pagenum', '%#%' ),
			'format' => '',
			'prev_text' => __( ' &lt;&lt; ' ),
			'next_text' => __( ' &gt;&gt; ' ),
			'total' => $total,
			'show_all' => False,
			'current' => $pagenum
		) );
		?>
		<style>
		.page-numbers {
			background: none repeat scroll 0 0 rgba(0, 0, 0, 0.05);
    		border-color: #CCCCCC;
			color: #555555;
    		padding: 5px;
			text-decoration:none;
			margin-left:2px;
			margin-right:2px;
		}
		.current {
			background: none repeat scroll 0 0 #BBBBBB;
		}
		</style>
		<div class="tablenav">
			<div class="alignleft">
				<select name="action" id="action">
					<option value="optimize-table"><?php _e('Optimize Table', 'email-posts-to-subscribers'); ?></option>
				</select>
				<input type="submit" value="<?php _e('Optimize Table', 'email-posts-to-subscribers'); ?>" class="button action" id="doaction" name="">
			</div>
			<div class="alignright">
				<?php echo $page_links; ?>
			</div>
		</div>
		<input type="hidden" name="frm_elp_bulkaction" value=""/>
      </form>
	  <?php if ($fulltotal > 20 ) { ?>
	  <div class="error fade"><p>
	  <?php _e('Note: Please click <strong>Optimize Table</strong> button to delete all records except latest 20.', 'email-posts-to-subscribers'); ?>
	  </p></div>
	  <?php } ?>
	  <p class="description"><?php echo ELP_OFFICIAL; ?></p>
	</div>
</div>