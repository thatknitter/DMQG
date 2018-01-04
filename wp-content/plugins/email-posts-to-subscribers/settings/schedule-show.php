<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<script language="javaScript" src="<?php echo ELP_URL; ?>settings/settings.js"></script>
<div class="tool-box">
	<div id="icon-plugins" class="icon32"></div>
	<h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
	<h3><?php _e('Schedule Newsletter', 'email-posts-to-subscribers'); ?></h3>
	<p><?php _e('We have plan to add wp_cron feature in the next release. At present I strongly recommend you to use your server CRON job (cPanel or Plesk) to send your newsletters. The following link explains how to create a CRON job through the cPanel or Plesk.', 'email-posts-to-subscribers'); ?></p>
	<p><?php _e('How to setup auto emails (cron job) in Plesk', 'email-posts-to-subscribers'); ?> <a target="_blank" href="http://www.gopiplus.com/work/2014/03/30/how-to-setup-auto-emails-for-email-posts-to-subscribers-plugin/"><?php _e('Click here', 'email-posts-to-subscribers'); ?></a>.</p>
	<p><?php _e('How to setup auto emails (cron job) in cPanal', 'email-posts-to-subscribers'); ?> <a target="_blank" href="http://www.gopiplus.com/work/2014/03/30/how-to-setup-auto-emails-for-email-posts-to-subscribers-plugin-cpanal/"><?php _e('Click here', 'email-posts-to-subscribers'); ?></a>.</p>
	<p><?php _e('Hosting doesnt support cron jobs?', 'email-posts-to-subscribers'); ?> <a target="_blank" href="http://www.gopiplus.com/work/2014/03/31/schedule-auto-mails-cron-jobs-for-email-posts-to-subscribers-plugin/"><?php _e('Click here', 'email-posts-to-subscribers'); ?></a> for solution.</p>
	<div style="height:4px;"></div>
</div>
<p class="description"><?php echo ELP_OFFICIAL; ?></p>
</div>