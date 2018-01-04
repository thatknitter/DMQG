<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
  <div id="icon-plugins" class="icon32"></div>
    <h2><?php _e(ELP_PLUGIN_DISPLAY, 'email-posts-to-subscribers'); ?></h2>
    <div class="tool-box">
	<div>
		<p>The aim of this plugin is One Time Configuration and Life Time Newsletter to subscribers. This plugin generate a newsletter with the latest available posts in the blog and send to your subscriber. We can easily schedule the newsletter daily, weekly or monthly. 10 default templates available with this plugin, also admin can create the templates using visual editor.</p>
		
		<h3>What are all the steps to do after plugin activation?</h3>
		
		<strong>Step 1: Checking Templates</strong>
		<p>After installing Email posts to subscriber plugin on your blogs, verifying a template would be your first step, because design is very important for newsletters. Go to Templates menu and preview the default templates. If you think the default templates are not interested, click Add New button to create your own theme. For more details, please follow the link. <a target="_blank" href="http://www.gopiplus.com/work/2014/03/28/wordpress-plugin-email-posts-to-subscribers/">click here</a></p> 
		
		<strong>Step 2: Mail Configuration</strong>
		<p>This is an important section in this plugin. Here you have option to select the design and post categories for the newsletters, so that newsletter will be generated automatically based on this input. Also in this section you have option to set number of emails per page (Number of emails per shot). For more details, follow the link.  <a target="_blank" href="http://www.gopiplus.com/work/2014/03/28/wordpress-plugin-email-posts-to-subscribers/">click here</a></p>
		
		<strong>Step 3: Preview Your Newsletter</strong>
		<p>After you have completed the mail configuration setting, you have option preview the newsletter by click preview link in the Mail Configuration page. I strongly suggest you to check the preview before you send/configure newsletters. For more details, follow the link.  <a target="_blank" href="http://www.gopiplus.com/work/2014/03/28/wordpress-plugin-email-posts-to-subscribers/">click here</a></p>
		
		<strong>Step 4: Send a Test Mail</strong>
		<p>Before you schedule/send a mass email, you can send a test email to yourself or your friends in order to verify that your email contents look how you want them to look. For this action, go to Send Mail page and select your email address and send test mail. For more details, follow the link.  <a target="_blank" href="http://www.gopiplus.com/work/2014/03/28/wordpress-plugin-email-posts-to-subscribers/">click here</a></p>
		
		<strong>Step 5: Schedule Mail</strong>
		<p>There are two options available in this plugin to schedule your CRON jobs. First option is let wordpress handle your scheduler (Set YES for wordpress CRON). and second option is configure the scheduler (Set NO for wordpress CRON) in your server. <a target="_blank" href="http://www.gopiplus.com/work/2014/03/28/wordpress-plugin-email-posts-to-subscribers/">click here</a></p>

		<p>1. First option (Let wordpress handle your scheduler)</p>
		<p>This is new option introduced in plugin version 3.9. this is very easy option and no server knowledge is required. Go to Cron Details page in your admin and set WordPress Cron to YES (Refer below screen). In this option wordpress automatically trigger the CRON job once every hour and based on your mail configuration newsletter go to your subscriber automatically. Plugin is smart to check your mail configuration once per day (first CRON run of the day) and generate the newsletter as per your configuration and set mail status to IN QUEUE. and in the subsequent CRON run plugin will send the newsletter from the queue.</p>
		<p>2. Second option (Configure CRON in your server)</p>
		<p>CRON URL is available in Cron Details admin page. You have to trigger/schedule this URL from your server once every hour (Once every hour is recommended for this plugin) . Plugin will send/schedule the newsletter whenever your URL is triggered. <a target="_blank" href="http://www.gopiplus.com/work/2014/03/31/schedule-auto-mails-cron-jobs-for-email-posts-to-subscribers-plugin/">click here</a> </p>
		
		<h3>How to send newsletter daily with list of posts published yesterday?</h3>
		<p>If you want this plugin to send newsletter daily with the latest posts published yesterday you can easily configure in Mail Configuration page.</p>
		<p>1. Enter your newsletter subject and select template which you want to use and select GROUP which you want to send.</p>
		<p>2. Select Published Last 2 Days in Post Count drop box.</p>
		<p>3. Enter your categories ID in Post categories text box.</p>
		<p>4. Select all days (Sunday to Saturday) in the schedule details.</p>

		<h3>Frequently Asked Questions</h3>
		<p>Q1. What are all the steps to do after plugin activation?</p>
		<p>Q2. How to setup subscription box widget?</p>
		<p>Q3. How to import and export email address to subscriber list?</p>
		<p>Q4. How to create/modify the template?</p>
		<p>Q5. How to add subscription box in posts?</p>
		<p>Q6. How to modify the existing mails (Opt-in mail, Welcome mail, Admin mails) content?</p>
		<p>Q7. How to schedule auto mails (Cron mails)?</p>
		<p>Q8. Hosting doesn't support cron jobs?</p>
		<p>Q9. How to filter posts category in the newsletter?</p>
		<p>Q10. How to configure number of emails send per day?</p>
		<p>Q11. How to send newsletter manually?</p>
		<p>Q12. Where to check sent mails?</p>
		<p>Q13. Is email not working on Email posts to subscribers wordpress plugin?</p>
		<p>Q14. How to install and activate on multisite installation blogs?</p>
		<p>Q16. How to schedule auto emails in cPanel?</p>
		<p>Q17. How to schedule auto emails in Parallels Plesk?</p>
		
		<p>Check official website for FAQ answer <a target="_blank" href="http://www.gopiplus.com/work/2014/03/28/wordpress-plugin-email-posts-to-subscribers/">click here</a></p>
	</div>
	</div>
</div>