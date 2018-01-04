<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
$elp_name = trim($arr['elp_name']);
$elp_desc = trim($arr['elp_desc']);
$elp_group = trim($arr['elp_group']);
$url = home_url();

global $elp_includes;
if (!isset($elp_includes) || $elp_includes !== true) 
{ 
	$elp_includes = true;
	?>
	<script language="javascript" type="text/javascript" src="<?php echo ELP_URL; ?>widget/widget.js"></script>
	<link rel="stylesheet" media="screen" type="text/css" href="<?php echo ELP_URL; ?>widget/widget.css" />
	<?php
}
?>
<div>
	<?php if( $elp_desc <> "" ) { ?>
	<div class="elp_caption"><?php echo $elp_desc; ?></div>
	<?php } ?>
	<div class="elp_msg"><span id="elp_msg"></span></div>
	<?php if( $elp_name == "YES" ) { ?>
	<div class="elp_lablebox"><?php _e('Name', 'email-posts-to-subscribers'); ?></div>
	<div class="elp_textbox">
		<input class="elp_textbox_class" name="elp_txt_name" id="elp_txt_name" value="" maxlength="225" type="text">
	</div>
	<?php } ?>
	<div class="elp_lablebox"><?php _e('Email *', 'email-posts-to-subscribers'); ?></div>
	<div class="elp_textbox">
		<input class="elp_textbox_class" name="elp_txt_email" id="elp_txt_email" onkeypress="if(event.keyCode==13) elp_submit_page('<?php echo $url; ?>')" value="" maxlength="225" type="text">
	</div>
	<div class="elp_button">
		<input class="elp_textbox_button" name="elp_txt_button" id="elp_txt_button" onClick="return elp_submit_page('<?php echo $url; ?>')" value="<?php _e('Subscribe', 'email-posts-to-subscribers'); ?>" type="button">
	</div>
	<input name="elp_txt_group" id="elp_txt_group" value="<?php echo $elp_group; ?>" type="hidden">
	<?php if( $elp_name != "YES" ) { ?>
		<input name="elp_txt_name" id="elp_txt_name" value="" type="hidden">
	<?php } ?>
</div>