<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
$did = isset($_GET['did']) ? sanitize_text_field($_GET['did']) : '0';
if(!is_numeric($did)) { die('<p>Are you sure you want to do this?</p>'); }

// First check if ID exist with requested ID
$result = elp_cls_dbquery::elp_template_count($did);
if ($result != '1')
{
	?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist.', 'email-posts-to-subscribers'); ?></strong></p></div><?php
}
?>
<script language="javaScript" src="<?php echo ELP_URL; ?>template/template.js"></script>
<div class="wrap">
  <div id="icon-plugins" class="icon32"></div>
    <h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<h3><?php _e('Preview Template', 'email-posts-to-subscribers'); ?></h3>
    <div class="tool-box">
	<div style="padding:15px;background-color:#FFFFFF;">
	<?php
		$preview = elp_cls_newsletter::elp_template_compose($did, 6, "", "ID", "DESC", "preview");
		echo $preview;
	?>
	</div>
	<div class="tablenav bottom">
		<a href="<?php echo ELP_ADMINURL; ?>?page=elp-email-template"><input class="button action" type="button" value="<?php _e('Back', 'email-posts-to-subscribers'); ?>" /></a>
		<a href="<?php echo ELP_ADMINURL; ?>?page=elp-email-template&ac=edit&did=<?php echo $did; ?>"><input class="button action" type="button" value="<?php _e('Edit', 'email-posts-to-subscribers'); ?>" /></a>
		<a target="_blank" href="<?php echo ELP_FAV; ?>"><input class="button action" type="button" value="<?php _e('Help', 'email-posts-to-subscribers'); ?>" /></a>
	</div>
	<p class="description"><?php echo ELP_OFFICIAL; ?></p>
	</div>
</div>