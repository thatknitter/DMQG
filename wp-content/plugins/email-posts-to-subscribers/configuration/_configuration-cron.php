<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;
if(!is_numeric($pagenum)) { die('<p>Are you sure you want to do this?</p>'); }

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
		'elp_set_postorder' => $data['elp_set_postorder']
	);
}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>configuration/configuration.js"></script>
<div class="wrap">
  <div id="icon-plugins" class="icon32"></div>
    <h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
    <div class="tool-box">
	<!--<h3 class="title"><?php //_e('Configuration Details', 'email-posts-to-subscribers'); ?></h3>-->
	<form name="frm_elp_display" method="post">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
		  <tr>
			<td><h3 class="title"><?php _e('Total Emails', 'email-posts-to-subscribers'); ?></h3></td>
		  </tr>
		  <tr>
			<td>		  
			<?php
			$url = home_url('/');
			$confirmed = elp_cls_dbquery::elp_view_subscriber_count_status("Confirmed");
			$singleoptin = elp_cls_dbquery::elp_view_subscriber_count_status("Single Opt In");
			$total = $singleoptin + $confirmed;
			?>
			<?php _e('Total valid email address in our database :', 'email-posts-to-subscribers'); ?> <strong><?php echo $total; ?></strong><br /><br />
			</td>
		  </tr>
		  <tr>
			<td><h3 class="title"><?php _e('Cron Links', 'email-posts-to-subscribers'); ?></h3></td>
		  </tr>
		  <tr>
			<td>
			<?php _e('Note: Email will be retrieved based on Database ID (i.e. Oldest email come first)', 'email-posts-to-subscribers'); ?><br /> <br /> 
			
			<strong><?php _e('Cron link for first', 'email-posts-to-subscribers'); ?> <?php echo $data['elp_set_totalsent']; ?> <?php _e('email', 'email-posts-to-subscribers'); ?></strong> 
			<a href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers&amp;ac=page&pg=1&tot=<?php echo $data['elp_set_totalsent']; ?>&did=<?php echo $did; ?>">
				<?php _e('click here to view emails', 'email-posts-to-subscribers'); ?>
			</a>:
			<br /> <br /><?php echo $url; ?>?elp=cronjob&guid=<?php echo $data['elp_set_guid']; ?>&page=1 <br /> <br /> 
			
			<strong><?php _e('Cron link for second', 'email-posts-to-subscribers'); ?> <?php echo $data['elp_set_totalsent']; ?> <?php _e('email', 'email-posts-to-subscribers'); ?></strong> 
			<a href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers&amp;ac=page&pg=2&tot=<?php echo $data['elp_set_totalsent']; ?>&did=<?php echo $did; ?>">
				<?php _e('click here to view emails', 'email-posts-to-subscribers'); ?>
			</a>: 
			<br /> <br /><?php echo $url; ?>?elp=cronjob&guid=<?php echo $data['elp_set_guid']; ?>&page=2 <br /> <br /> 
						
			<strong><?php _e('Cron link for third', 'email-posts-to-subscribers'); ?> <?php echo $data['elp_set_totalsent']; ?> <?php _e('email', 'email-posts-to-subscribers'); ?></strong> 
			<a href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers&amp;ac=page&pg=3&tot=<?php echo $data['elp_set_totalsent']; ?>&did=<?php echo $did; ?>">
				<?php _e('click here to view emails', 'email-posts-to-subscribers'); ?>
			</a>:
			<br /> <br /><?php echo $url; ?>?elp=cronjob&guid=<?php echo $data['elp_set_guid']; ?>&page=3 <br /> <br /> 
			
			<strong><?php _e('Cron link for fourth', 'email-posts-to-subscribers'); ?> <?php echo $data['elp_set_totalsent']; ?> <?php _e('email', 'email-posts-to-subscribers'); ?></strong> 
			<a href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers&amp;ac=page&pg=4&tot=<?php echo $data['elp_set_totalsent']; ?>&did=<?php echo $did; ?>">
				<?php _e('click here to view emails', 'email-posts-to-subscribers'); ?>
			</a>:
			<br /> <br /><?php echo $url; ?>?elp=cronjob&guid=<?php echo $data['elp_set_guid']; ?>&page=4 <br /> <br /> 
			
			<strong><?php _e('Cron link for fifth', 'email-posts-to-subscribers'); ?> <?php echo $data['elp_set_totalsent']; ?> <?php _e('email', 'email-posts-to-subscribers'); ?></strong> 
			<a href="<?php echo ELP_ADMINURL; ?>?page=elp-view-subscribers&amp;ac=page&pg=5&tot=<?php echo $data['elp_set_totalsent']; ?>&did=<?php echo $did; ?>">
				<?php _e('click here to view emails', 'email-posts-to-subscribers'); ?>
			</a>:  
			<br /> <br /><?php echo $url; ?>?elp=cronjob&guid=<?php echo $data['elp_set_guid']; ?>&page=5 <br /><br />
			</td>
		  </tr>
		  <tr>
			<td><h3 class="title"><?php _e('Template Details', 'email-posts-to-subscribers'); ?></h3></td>
		  </tr>
		  <tr>
			<td>
			<p>
			<?php
			$Template = array();
			$Template = elp_cls_dbquery::elp_template_select($data['elp_set_templid']);
			echo $Template['elp_templ_heading'];
			?>
			</p>
			</td>
		  </tr>
		  <tr>
			<td><h3 class="title"><?php _e('Post Details', 'email-posts-to-subscribers'); ?></h3></td>
		  </tr>
		  <tr>
			<td>
			<p>This configuration retrieve <strong><?php echo $data['elp_set_postcount']; ?></strong> posts 
			<?php if ($data['elp_set_postcategory'] <> "") { ?>
			from the category <strong><?php echo $data['elp_set_postcategory']; ?></strong> and
			<?php } ?>
			order by <strong><?php echo $data['elp_set_postorderby']; ?> <?php echo $data['elp_set_postorder']; ?></strong></p></td>
		  </tr>
	</table>
	</form>	
	<div style="padding-top:20px;"></div>
	<div class="tablenav">
	  <div class="alignleft">
		<a class="button add-new-h2" href="<?php echo ELP_ADMINURL; ?>?page=elp-configuration&amp;pagenum=<?php echo $pagenum; ?>"><?php _e('Back', 'email-posts-to-subscribers'); ?></a>
		<a class="button add-new-h2" target="_blank" href="<?php echo ELP_FAV; ?>"><?php _e('Help', 'email-posts-to-subscribers'); ?></a>
	  </div>
	</div>
	<div style="height:10px;"></div>
	<p class="description"><?php echo ELP_OFFICIAL; ?></p>
	</div>
</div>