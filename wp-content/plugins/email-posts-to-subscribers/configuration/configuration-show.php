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
	$result = elp_cls_dbquery::elp_configuration_count($did);
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
			elp_cls_dbquery::elp_configuration_delete($did);
			
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
<script language="javaScript" src="<?php echo ELP_URL; ?>configuration/configuration.js"></script>
<div class="wrap">
  <div id="icon-plugins" class="icon32"></div>
    <h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
    <div class="tool-box">
	<h3><?php _e('Mail Configuration', 'email-posts-to-subscribers'); ?>  
	<a class="add-new-h2" href="<?php echo ELP_ADMINURL; ?>?page=elp-configuration&amp;ac=add"><?php _e('Add New', 'email-posts-to-subscribers'); ?></a></h3>
	<?php
	$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
	$limit = 25;
	$offset = ($pagenum - 1) * $limit;
	$total = elp_cls_dbquery::elp_configuration_count(0);
	$fulltotal = $total;
	$total = ceil( $total / $limit );
	
	$myData = array();
	$myData = elp_cls_dbquery::elp_configuration_select(0, $offset, $limit);
	?>
	<form name="frm_elp_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
            <!--<th scope="col"><?php //_e('Sno', 'email-posts-to-subscribers'); ?></th>-->
            <th scope="col"><?php _e('Mail Subject', 'email-posts-to-subscribers'); ?></th>
			<!--<th scope="col"><?php //_e('Template', 'email-posts-to-subscribers'); ?></th>-->
			<th scope="col"><?php _e('Posts Details', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Schedule Day', 'email-posts-to-subscribers'); ?></th>
			<!--<th scope="col"><?php //_e('Type', 'email-posts-to-subscribers'); ?></th>-->
			<th scope="col"><?php _e('Subscribers', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Status', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Last Run', 'email-posts-to-subscribers'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
            <!--<th scope="col"><?php //_e('Sno', 'email-posts-to-subscribers'); ?></th>-->
            <th scope="col"><?php _e('Mail Subject', 'email-posts-to-subscribers'); ?></th>
			<!--<th scope="col"><?php //_e('Template', 'email-posts-to-subscribers'); ?></th>-->
			<th scope="col"><?php _e('Posts Details', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Schedule Day', 'email-posts-to-subscribers'); ?></th>
			<!--<th scope="col"><?php //_e('Type', 'email-posts-to-subscribers'); ?></th>-->
			<th scope="col"><?php _e('Subscribers', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Status', 'email-posts-to-subscribers'); ?></th>
			<th scope="col"><?php _e('Last Run', 'email-posts-to-subscribers'); ?></th>
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
						<!--<td align="left"><?php //echo $i; ?></td>-->
					  	<td><?php echo esc_html(stripslashes($data['elp_set_name'])); ?>
						<div class="row-actions">
						<span class="edit">
						<a title="Edit" href="<?php echo ELP_ADMINURL; ?>?page=elp-configuration&amp;ac=edit&amp;did=<?php echo $data['elp_set_id']; ?>"><?php _e('Edit', 'email-posts-to-subscribers'); ?></a> |
						<a title="Send Mail" href="<?php echo ELP_ADMINURL; ?>?page=elp-sendemail"><?php _e('Send Email', 'email-posts-to-subscribers'); ?></a> |
						<a title="Preview" href="<?php echo ELP_ADMINURL; ?>?page=elp-configuration&amp;ac=preview&amp;did=<?php echo $data['elp_set_id']; ?>"><?php _e('Preview', 'email-posts-to-subscribers'); ?></a>
						</span>
						<?php if($data['elp_set_id'] > 4) { ?>
						<span class="trash"> | <a onClick="javascript:_elp_delete('<?php echo $data['elp_set_id']; ?>')" href="javascript:void(0);"><?php _e('Delete', 'email-posts-to-subscribers'); ?></a></span> 
						</div>
						<?php } ?>
					  	</td>
						<!--<td>-->
						<?php
						//$Template = array();
						//$Template = elp_cls_dbquery::elp_template_select($data['elp_set_templid'], "System");
						//echo $Template['elp_templ_heading'];
						?>
						<!--</td>-->
						<td><?php echo elp_cls_common::elp_postcount_display($data['elp_set_postcount']); ?></td>
						<td>
						<?php 		
						$elp_set_scheduleday = "";	
						if( $data['elp_set_scheduleday'] <> "" )
						{
							$elp_set_scheduleday = str_replace(' -- ','', $data['elp_set_scheduleday']);
							$elp_set_scheduleday = str_replace('#0#','0, ', $elp_set_scheduleday);
							$elp_set_scheduleday = str_replace('#1#','1, ', $elp_set_scheduleday);
							$elp_set_scheduleday = str_replace('#2#','2, ', $elp_set_scheduleday);
							$elp_set_scheduleday = str_replace('#3#','3, ', $elp_set_scheduleday);
							$elp_set_scheduleday = str_replace('#4#','4, ', $elp_set_scheduleday);
							$elp_set_scheduleday = str_replace('#5#','5, ', $elp_set_scheduleday);
							$elp_set_scheduleday = str_replace('#6#','6, ', $elp_set_scheduleday);
						}
						echo $elp_set_scheduleday;
						?></td>
						<!--<td><?php //echo $data['elp_set_scheduletype']; ?></td>-->
						<td><?php echo $data['elp_set_emaillistgroup']; ?></td>
						<td><?php echo elp_cls_common::elp_disp_status($data['elp_set_status']); ?></td>
						<td><?php echo $data['elp_set_lastschedulerun']; ?></td>
					</tr>
					<?php
					$i = $i+1;
				}
			}
			else
			{
				?><tr><td colspan="6"><?php _e('No records available.', 'email-posts-to-subscribers'); ?></td></tr><?php 
			}
			?>
		</tbody>
        </table>
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
			background: none repeat scroll 0 0 #E0E0E0;
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
		<?php wp_nonce_field('elp_form_show'); ?>
		<input type="hidden" name="frm_elp_display" value="yes"/>
      </form>	
	  <div style="padding-top:10px;"></div>
	  <div class="tablenav">
		  <div class="alignleft">
			<a href="<?php echo ELP_ADMINURL; ?>?page=elp-configuration&amp;ac=add"><input class="button action" type="button" value="<?php _e('Add New', 'email-posts-to-subscribers'); ?>" /></a>
			<a target="_blank" href="<?php echo ELP_FAV; ?>"><input class="button action" type="button" value="<?php _e('Help', 'email-posts-to-subscribers'); ?>" /></a>
		  </div>
		  <div class="alignright"><?php echo $page_links; ?></div>
	  </div>
	  <div style="height:10px;"></div>
	  <p class="description">
	  This page is to configure automatic newsletter to your subscribers. Based on this configuration your CRON url will trigger and send the newsletter email to your subscribers.<br />
	  <?php echo ELP_OFFICIAL; ?></p>
	</div>
</div>