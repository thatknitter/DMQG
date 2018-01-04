<?php
/*
Plugin Name: New User Approve - Ignore Password Reset
Plugin URI: http://www.caseproof.com/
Description: Prevents a users password from being reset by the New User Approve plugin when they signup.
Version: 1.0.0
Author: Caseproof, LLC
Author URI: http://caseproof.com/
Text Domain: memberpress
Copyright: 2004-2013, Caseproof, LLC
*/

function ignore_new_user_autopass()
{
  return true;
}

add_filter('new_user_approve_bypass_password_reset', 'ignore_new_user_autopass');
