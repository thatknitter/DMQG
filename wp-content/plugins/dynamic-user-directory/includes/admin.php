<?php

/*** Register Menu Item *************************************************/

function DynamicUserDirectoryAdmin(){
	
add_submenu_page(
 'options-general.php',
 'Dynamic User Directory Settings',
 'Dynamic User Directory',
 'manage_options',
 'user_directory',
 'DynamicUserDirectoryAdminSettings'
 );
}
add_action('admin_menu', 'DynamicUserDirectoryAdmin'); //menu setup

/**** Display Page Content *********************************************/

function DynamicUserDirectoryAdminSettings() {

global $submenu;

// access page settings 
$page_data = array();
foreach($submenu['options-general.php'] as $i => $menu_item) {
 	if($submenu['options-general.php'][$i][2] == 'user_directory')
 		$page_data = $submenu['options-general.php'][$i];
}

/*** load scripts ***/    
wp_enqueue_style( 'wp-color-picker' ); 
wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
wp_enqueue_script( 'dud_custom_js', DYNAMIC_USER_DIRECTORY_URL . '/js/jquery.user-directory.js', array( 'jquery', 'wp-color-picker' ), '', true  );
wp_enqueue_script( 'jquery-ui-sortable');

$dud_options = get_option( 'dud_plugin_settings' );
$dud_option_name = 'dud_plugin_settings'; 

if(! $dud_options ) {
		
	$dud_options = array(
		'user_directory_sort' => 'last_name',
		'ud_format_name' => 'fl',
		'ud_directory_type' => 'alpha-links',
		'ud_letter_divider' => 'nld',
		'ud_letter_divider_font_color' => '#FFFFFF',
		'ud_letter_divider_fill_color' => '#D3D3D3',
		'ud_show_srch' => '',
		'user_directory_show_avatars' => '1',
		'user_directory_avatar_style' => '',		
		'user_directory_border' => 'dividing_border',
		'user_directory_border_thickness' => '',
		'user_directory_border_color' => '#D3D3D3',
		'user_directory_border_length' => '60%',
		'user_directory_border_style' => '',
		'user_directory_letter_fs' => '15',
		'ud_alpha_link_spacer' => '12',
		'user_directory_listing_fs' => '15',
		'user_directory_listing_spacing' => '20',
		'ud_hide_roles' => null,
		'ud_users_exclude_include' => null,
		'ud_exclude_include_radio' => 'exclude',
		'user_directory_email' => '1',
		'user_directory_website' => '1',
		'user_directory_num_meta_flds' => '5',
		'user_directory_meta_field_1' => '',
		'user_directory_meta_label_1' => '',
		'user_directory_meta_field_2' => '',
		'user_directory_meta_label_2' => '',
		'user_directory_meta_field_3' => '',
		'user_directory_meta_label_3' => '',
		'user_directory_meta_field_4' => '',
		'user_directory_meta_label_4' => '',
		'user_directory_meta_field_5' => '',
		'user_directory_meta_label_5' => '',
		'user_directory_meta_field_6' => '',
		'user_directory_meta_label_6' => '',
		'user_directory_meta_field_7' => '',
		'user_directory_meta_label_7' => '',
		'user_directory_meta_field_8' => '',
		'user_directory_meta_label_8' => '',
		'user_directory_meta_field_9' => '',
		'user_directory_meta_label_9' => '',
		'user_directory_meta_label_10' => '',
		'user_directory_meta_field_10' => '',
		'user_directory_meta_link_1' => '',
		'user_directory_meta_link_2' => '',
		'user_directory_meta_link_3' => '',
		'user_directory_meta_link_4' => '',
		'user_directory_meta_link_5' => '',
		'user_directory_meta_link_6' => '',
		'user_directory_meta_link_7' => '',
		'user_directory_meta_link_8' => '',
		'user_directory_meta_link_9' => '',
		'user_directory_meta_link_10' => '',
		'user_directory_address' => '0',
		'user_directory_addr_1' => '',
		'user_directory_addr_2' => '',
		'user_directory_city' => '',
		'user_directory_state' => '',
		'user_directory_zip' => '',
		'user_directory_num_meta_srch_flds' => '5',
		'user_directory_meta_srch_field_1' => '',
		'user_directory_meta_srch_label_1' => '',
		'user_directory_meta_srch_field_2' => '',
		'user_directory_meta_srch_label_2' => '',
		'user_directory_meta_srch_field_3' => '',
		'user_directory_meta_srch_label_3' => '',
		'user_directory_meta_srch_field_4' => '',
		'user_directory_meta_srch_label_4' => '',
		'user_directory_meta_srch_field_5' => '',
		'user_directory_meta_srch_label_5' => '',
		'user_directory_meta_srch_field_6' => '',
		'user_directory_meta_srch_label_6' => '',
		'user_directory_meta_srch_field_7' => '',
		'user_directory_meta_srch_label_7' => '',
		'user_directory_meta_srch_field_8' => '',
		'user_directory_meta_srch_label_8' => '',
		'user_directory_meta_srch_field_9' => '',
		'user_directory_meta_srch_label_9' => '',
		'user_directory_meta_srch_field_10' => '',
		'user_directory_meta_srch_label_10' => '',
		'user_directory_meta_srch_field_11' => '',
		'user_directory_meta_srch_label_11' => '',
		'user_directory_meta_srch_field_12' => '',
		'user_directory_meta_srch_label_12' => '',
		'user_directory_meta_srch_field_13' => '',
		'user_directory_meta_srch_label_13' => '',
		'user_directory_meta_srch_field_14' => '',
		'user_directory_meta_srch_label_14' => '',
		'user_directory_meta_srch_field_15' => '',
		'user_directory_meta_srch_label_15' => '',
		'ud_show_last_name_srch_fld' => '',
		'user_directory_sort_order' => '',
		'ud_debug_mode' => 'off',	
		'ud_author_page' => '',
		'ud_show_author_link' => '',
		'ud_auth_or_bp' => '',
		'ud_clear_search' => '',
		'ud_show_srch_results' => 'alpha-links',
		'ud_srch_icon_color' => 'dimgray',
		'dud_instance_name' => 'original'
	);

	// if old options exist, update to new system
	foreach( $dud_options as $key => $value ) {
		if( $existing = get_option( $key ) ) {
			$dud_options[$key] = $existing;
			delete_option( $key );
		}
	}
	
	add_option('dud_plugin_settings', $dud_options );
}

$dud_plugin_list = get_option('active_plugins');

if ( in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $dud_plugin_list )) 
{
	$instance_name = "Original";
	$dud_option_name = "dud_plugin_settings";
	$dud_multi_instances_err = "";

	if(isset($_POST['add']) && $_POST['add'] === 'Add')
	{
		$dud_total_instances = 0;
		$new_instance_name = $_POST['dud_new_instance_name'];
		
		if(!$new_instance_name)
			$dud_multi_instances_err = 'You must give the new directory instance a name!'; 
		else if(strlen($new_instance_name) > 20)
		{
			$dud_multi_instances_err = 'The directory instance name cannot be over 20 characters!'; 
		}
		else
		{
			$new_instance_name = sanitize_text_field($new_instance_name);
					
			if(strtoupper($new_instance_name) === "ORIGINAL")
			{
				$dud_multi_instances_err = 'The directory instance name "' . $new_instance_name . '" is reserved for your original settings. Please choose a different one.';
			}
			else
			{
				for($inc=0; $inc <= 4; $inc++) 
				{		  
					if( $dud_tmp_options = get_option( 'dud_plugin_settings_' . ($inc+1) ) )
					{
						if($dud_tmp_options['dud_instance_name'] && (strtoupper ($dud_tmp_options['dud_instance_name']) === strtoupper ($new_instance_name)) )  
						{
								$dud_multi_instances_err = 'The directory instance name "' . $new_instance_name . '" already exists. Please choose a different one.'; 
								break;
						} 
						
						if($dud_tmp_options['dud_instance_name'])
							$dud_total_instances++;
						else
						{
							unset($dud_options);
						    $dud_options = get_option( 'dud_plugin_settings' );
							$dud_options['dud_instance_name'] = $new_instance_name;
							update_option('dud_plugin_settings_' . ($inc+1), $dud_options );
							$dud_option_name = 'dud_plugin_settings_' . ($inc+1);
							$instance_name = $new_instance_name;
							
							break;
						}	
					}
					else
					{
						unset($dud_options);
						$dud_options = get_option( 'dud_plugin_settings' );
						$dud_options['dud_instance_name'] = $new_instance_name;
						add_option('dud_plugin_settings_' . ($inc+1), $dud_options );
						$dud_option_name = 'dud_plugin_settings_' . ($inc+1);
						$instance_name = $new_instance_name;
				
						break;
					}	
				}
				
				if($dud_total_instances >= 5)
					$dud_multi_instances_err = 'All available instances are taken! You must delete one before adding any more.'; 
			}
		}
	}
	else if(isset($_POST['load']) && $_POST['load'] === 'Load') 
	{
		
		$load_instance_name = $_POST['dud_load_dir_instance'];
		
		if(strtoupper($load_instance_name) === "ORIGINAL")
		{
			$instance_name = "Original";
			$dud_option_name = 'dud_plugin_settings';
		}
		else
		{
			$found_instance = false;
			
			for($inc=0; $inc <= 4; $inc++) 
			{		  
				if( $dud_tmp_options = get_option( 'dud_plugin_settings_' . ($inc+1) ) )
				{
					if($load_instance_name === $dud_tmp_options['dud_instance_name'])
					{
						unset($dud_options);
						$dud_options = $dud_tmp_options;
						$dud_option_name = 'dud_plugin_settings_' . ($inc+1);
						$instance_name = $load_instance_name;
						$found_instance = true;
						break;
					}	
				}	
			}
			
			if(!$found_instance)
				$dud_multi_instances_err = "Could not load instance " . $load_instance_name . " because it could not be found!"; 
		}
	}
	else if(isset($_POST['delete']) && $_POST['delete'] === 'Delete')
	{
		$load_instance_name = $_POST['dud_load_dir_instance'];
		$deleted_instance = false;
		
		if(strtoupper($load_instance_name) === "ORIGINAL")
		{
			$dud_multi_instances_err = "The original settings cannot be deleted!"; 
		}
		else
		{
			for($inc=0; $inc <= 4; $inc++) 
			{		  
				if( $dud_tmp_options = get_option( 'dud_plugin_settings_' . ($inc+1) ) )
				{
					if($load_instance_name === $dud_tmp_options['dud_instance_name'])
					{
						delete_option('dud_plugin_settings_' . ($inc+1));
						$deleted_instance = true;
						$dud_multi_instances_err = 'Directory instance "' . $load_instance_name . '" has been deleted.'; 
						break;
					}	
				}
			}
			
			if(!$deleted_instance)
				$dud_multi_instances_err = 'Could not delete instance ' . $load_instance_name . ' because it could not be found!'; 
		}
	}
	else if($updated_settings = get_option('dud_updated_settings'))
	{
		//echo "DUD UPDATED SETTINGS: " . $updated_settings . "<BR>";
		unset($dud_options);
		$dud_option_name = $updated_settings;
		$dud_options = get_option( $updated_settings );
		$instance_name = $dud_options['dud_instance_name'];
		if(!$instance_name) $instance_name = "Original"; 
	}
	
	delete_option('dud_updated_settings');	
}

//var_dump($_POST);	

/*** display settings screen ***/ 
?>
<div class="wrap">
<?php screen_icon();?>
<h2><?php echo $page_data[3];?></h2>

<?php 

if ( in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $dud_plugin_list )) 
{ do_action(dud_multiple_directories_settings($dud_multi_instances_err, $instance_name)); } 
else { ?> <BR> <?php } ?>

<form id="user_directory_options" action="options.php" method="post" onSubmit="return selectAll()">

<?php 

settings_fields('user_directory_options');
do_settings_sections('user_directory_options'); 
      
if (!wp_script_is( 'user-directory-style', 'enqueued' )) {
	wp_register_style('user-directory-style',  DYNAMIC_USER_DIRECTORY_URL . '/css/user-directory-admin-min.css', false, 0.1);	
	//wp_register_style('user-directory-style',  DYNAMIC_USER_DIRECTORY_URL . '/css/user-directory-admin.css', false, 0.1);	
	wp_enqueue_style( 'user-directory-style' );
}
		
?>  
<div class="dud-settings-section-header">&nbsp; Main Directory Settings</div>
<div class="dud-settings-section">
<table class="form-table">

  <?php if ( !in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $dud_plugin_list )) 
        { ?>
			 <tr>
				<td><b>Shortcode</b></td>
				<td><input class="dd-menu-no-chk-box-width" type="text" id="plugin_shortcode" name="plugin_shortcode" value="[DynamicUserDirectory]" size="32" readonly/></td>
				<td>Copy and paste this shortcode onto the page where the directory should be displayed. Our <a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Multiple Directories Add-on</a> will let you create and display up to five directories, each with its own settings.</td>
				<td></td>
			 </tr> 
  <?php }
        else 
        {?>	
	          <tr>
				<td><b>Loaded Instance</b></td>
				<td style="font-weight:bold;color:#008888;letter-spacing:1px;font-size:15px;"><?php echo $instance_name;?></td>
				<td></td>
				<td><input type="hidden" id="dud_instance_name" name="<?php echo $dud_option_name;?>[dud_instance_name]" value="<?php echo $instance_name;?>"></td>
			 </tr> 
  <?php } ?>     
      <tr>
        <td><b>Sort Field</b>
        </td>
        <td><select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[user_directory_sort]" id="user_directory_sort">
            <OPTION value="display_name">Display Name</OPTION>             
            <OPTION value="last_name" <?php echo ($dud_options['user_directory_sort'] == "last_name") ? "SELECTED" : ""; ?>>Last Name</OPTION> 
            </select> 
        </td>
        <td>This field will always be shown first on each listing. You may sort by Last Name or Display Name. If Last Name is selected, it will sort by last name but still display the full name.</td>
        <td><input type="hidden" id="dynamic_ud_cimy_plugin" name="dynamic_ud_cimy_plugin" value="<?php echo $dynamic_ud_cimy_installed; ?>"></td>
     </tr>  
     <tr>
        <td><b>Directory Type</b></td>
        <td>
        	<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_directory_type]" id="ud_directory_type">
            		<OPTION value="alpha-links">Alphabet Letter Links</OPTION> 
            		<OPTION value="all-users" <?php echo ($dud_options['ud_directory_type'] == "all-users") 
            			? "SELECTED" : ""; ?>>Single Page Directory</OPTION>             
            		
            	</select> 
        </td>
        <td>"Alphabet Letter Links" shows only the users for the selected letter. "Single Page Directory" shows all users on one page. Choose "Alphabet Letter Links" if page load time is an issue.</td>
        <td></td>
     </tr>	 
     <tr>
        <td><b>Directory Search</b></td>
        <td><input name="<?php echo $dud_option_name;?>[ud_show_srch]" id="ud_show_srch" type="checkbox" 
           value="1" <?php if(!empty($dud_options['ud_show_srch'])) { checked( '1', $dud_options['ud_show_srch'] ); } ?> />&nbsp;&nbsp;
           <select class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[ud_srch_style]" id="ud_srch_style">
            		<OPTION value="default">Default Background</OPTION>             
            		<OPTION value="transparent" <?php echo ($dud_options['ud_srch_style'] == "transparent") 
            			? "SELECTED" : ""; ?>>Transparent Bkg</OPTION>           		
           </select>
        </td>
        <td>Show a search box at the top of your directory. You may search by last name or display name depending on the Sort Field. Our <a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Meta Fields Search Add-on</a> allows the viewer to search by ANY user meta field(s) you specify.</td>
        <td></td>
     </tr>
	 <tr>
	    <td id="bot" colspan="2"><b>Hide Users With These Roles</b><div id='lb_hide_roles'><?php echo !empty($dud_options['ud_hide_roles']) ? dynamic_ud_roles_listbox($dud_options['ud_hide_roles'], $dud_option_name) : dynamic_ud_roles_listbox("", $dud_option_name); ?></div></td>
        <td style="font-size:13.5px;font-style:italic; line-height: 21px;padding-left:4%">Select any user roles that should NOT appear in the directory. Hold down the ctrl key while clicking on each role to select or deselect. If nothing is selected all users will be shown.</td>
        <td></td>
     </tr>
	  <tr>  
        <td colspan="2"><div id='lb_inc_exc'><b>Exclude or Include These Users </b>
        	<br>
        	<?php echo !empty($dud_options['ud_users_exclude_include']) ? dynamic_ud_users_listbox($dud_options['ud_users_exclude_include'], $dud_option_name) : dynamic_ud_users_listbox("", $dud_option_name); ?></div><br><br>
        	<input type="radio" name="<?php echo $dud_option_name;?>[ud_exclude_include_radio]" 
        		value="exclude" <?php if(!empty($dud_options['ud_exclude_include_radio'])) { checked( 'exclude', $dud_options['ud_exclude_include_radio'] ); } ?> /><b>Exclude</b>&nbsp;         	
        			<input type="radio" name="<?php echo $dud_option_name;?>[ud_exclude_include_radio]" value="include" 
        				<?php if(!empty($dud_options['ud_exclude_include_radio'])) { checked( 'include', $dud_options['ud_exclude_include_radio'] ); } ?> /><b>Include</b></td>
        <td style="font-size:13.5px;font-style:italic; line-height: 21px;padding-left:4%">"Include" creates a directory in which ONLY the selected users are shown. "Exclude" hides the selected users. If no users are selected this setting will not be applied. Note: Selected users will be included or excluded even if their user role was selected for hiding.</td>
        <td></td>
     </tr>
	  <tr>
        <td><b>Debug Mode</b></td>
        <td>
        	<input type="radio" name="<?php echo $dud_option_name;?>[ud_debug_mode]" 
        		value="off" <?php checked( 'off', $dud_options['ud_debug_mode'] ); ?> /><b>Off</b>&nbsp;         	
        	<input type="radio" name="<?php echo $dud_option_name;?>[ud_debug_mode]" 
        		value="on" <?php checked( 'on', $dud_options['ud_debug_mode'] ); ?> /><b>On</b></td>
        <td>When debug mode is "on," a set of debug statements will be shown for admins *ONLY* at the top of the User Directory. Leave debug mode "off" unless instructed to turn on.</td>
        <td></td>
     </tr> 	
</table>
<br/>
</div><br/><br/>

<div class="dud-settings-section-header">&nbsp; Listing Display Settings</div>
<div class="dud-settings-section">
	<table class="form-table">
	      <tr>
			<td><b>Show avatars</b></td>
			<td><input name="<?php echo $dud_option_name;?>[user_directory_show_avatars]" id="user_directory_show_avatars" type="checkbox" 
			   value="1" <?php if(!empty($dud_options['user_directory_show_avatars'])) { checked( '1', $dud_options['user_directory_show_avatars'] ); } ?> />&nbsp;&nbsp;
			   <select class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[user_directory_avatar_style]" id="user_directory_avatar_style">
						<OPTION value="standard">Standard Style</OPTION>             
						<OPTION value="rounded-edges" <?php echo ($dud_options['user_directory_avatar_style'] == "rounded-edges") 
							? "SELECTED" : ""; ?>>Rounded edges</OPTION> 
						<OPTION value="circle" <?php echo ($dud_options['user_directory_avatar_style'] == "circle") ? "SELECTED" : ""; ?>>Circle</OPTION> 
			   </select>
			</td>
			<td>Show avatars in your directory. Note: Some themes enforce a certain avatar shape. In those cases, DUD will *not* alter the site-wide avatar shape settings.</td>
			<td></td>
		 <?php if(function_exists('bp_is_active')) { ?>
			 <tr>
				<td><b>Link to Author Page<br>or BP Profile</b></td>
				<td><input name="<?php echo $dud_option_name;?>[ud_author_page]" id="ud_author_page" type="checkbox" 
				   value="1" <?php if(!empty($dud_options['ud_author_page'])) { checked( '1', $dud_options['ud_author_page'] ); } ?> />&nbsp;&nbsp;
				   <select class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[ud_auth_or_bp]" id="ud_auth_or_bp">
							<OPTION value="auth">WP Author Page</OPTION>             
							<OPTION value="bp" <?php echo (!empty($dud_options['ud_auth_or_bp']) && $dud_options['ud_auth_or_bp'] == "bp") 
								? "SELECTED" : ""; ?>>BuddyPress Profile</OPTION> 
				   </select>
				</td>
				<td>Hyperlink the user name & avatar to the user&lsquo;s WP author page or BuddyPress profile page.</td>
				<td></td>
			 </tr>
			 <tr id="open_linked_page">
				<td><b>Open Linked Page</b></td>
				<td><select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_target_window]" id="ud_target_window">
							<OPTION value="separate">In separate window</OPTION>             
							<OPTION value="main" <?php echo ($dud_options['ud_target_window'] == "main") 
								? "SELECTED" : ""; ?>>In main window</OPTION> 
							
				   </select>
				</td>
				<td></td>
				<td></td>
			 </tr>
    <?php } else { ?> 
				 <tr>
					<td><b>Link to Author Page</b></td>
					<td><input name="<?php echo $dud_option_name;?>[ud_author_page]" id="ud_author_page" type="checkbox" 
					   value="1" <?php if(!empty($dud_options['ud_author_page'])) { checked( '1', $dud_options['ud_author_page'] ); } ?> />&nbsp;&nbsp;
					   <select class="dd-menu-chk-box-width" name="<?php echo $dud_option_name;?>[ud_target_window]" id="ud_target_window">
								<OPTION value="separate">Open in new window</OPTION>             
								<OPTION value="main" <?php echo ($dud_options['ud_target_window'] == "main") 
									? "SELECTED" : ""; ?>>Open in main window</OPTION> 
								
					   </select>
					</td>
					<td>Hyperlink the user name and avatar to the user&lsquo;s WordPress author page.</td>
					<td></td>
				 </tr>
    <?php } ?>
	
		  <tr id="show-auth-pg-lnk">
			<td><b>Show Page Link</b></td>
			<td>
			   <select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_show_author_link]" id="ud_show_author_link">          
						<OPTION value="posts-exist">If Posts Exist</OPTION> 
						<OPTION value="always" <?php echo ($dud_options['ud_show_author_link'] == "always") 
							? "SELECTED" : ""; ?>>Always</OPTION> 
			   </select>
			</td>
			<td>Select "Always" ONLY if you have a custom author.php page that is shown whether or not the author has posts. Otherwise you'll get a Page Not Found error for authors with no posts.</td>
			<td></td>
		 </tr>
		 
		 <tr>
			<td><b>User Name Display Format</b></td>
			<td><select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_format_name]" id="ud_format_name">
						<OPTION value="fl">First Last</OPTION> 
						<OPTION value="lf" <?php echo ($dud_options['ud_format_name'] == "lf") 
							? "SELECTED" : ""; ?>>Last, First</OPTION>            		                 		
					</select> 
			</td>
			<td> <i>First Last</i> shows the user name like "Sally Smith." <i>Last, First</i> shows it like "Smith, Sally."</td>
			<td></td>
		 </tr>
		 
		 <tr id="one-page-dir-type-a">
			<td><b>Letter Divider</b></td>
			<td>
				
				<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_letter_divider]" id="ud_letter_divider">
						<OPTION value="nld">No letter divider</OPTION> 
						<OPTION value="ld-ds" <?php echo ($dud_options['ud_letter_divider'] == "ld-ds") 
							? "SELECTED" : ""; ?>>Letter Divider (dropshadow)</OPTION> 
						<OPTION value="ld-fl" <?php echo ($dud_options['ud_letter_divider'] == "ld-fl") 
							? "SELECTED" : ""; ?>>Letter Divider (no dropshadow)</OPTION>                      		
					</select> 
			</td>
			<td>You can show a dividng bar for each alphabet letter in a Single Page Directory. Our <a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Alpha Links Scroll Add-on</a> displays clickable letter links at the top that will smoothly scroll to the matching letter divider.</td>
			<td></td>
		 </tr>
     
		  <tr id="one-page-dir-type-b">
			<td>         
			   <div id="divider-colors"><b>Divider Font Color</b></div>
			   <input type="text" name="<?php echo $dud_option_name;?>[ud_letter_divider_font_color]" 
					value="<?php echo esc_attr( $dud_options['ud_letter_divider_font_color'] ); ?>" class="cpa-color-picker">
			</td>
			<td>
				<div id="divider-colors"><b>Divider Fill Color</b></div>
			    <input type="text" name="<?php echo $dud_option_name;?>[ud_letter_divider_fill_color]" 
				value="<?php echo esc_attr( $dud_options['ud_letter_divider_fill_color'] ); ?>" class="cpa-color-picker">			          
			</td>
				<td></td>
				<td></td>
		 </tr>

		 <tr>
			<td><b>Listing Border</b></th>
			<td>
				<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[user_directory_border]" id="user_directory_border">
						<OPTION value="dividing_border">Dividing border</OPTION> 
						<OPTION value="surrounding_border" <?php echo ($dud_options['user_directory_border'] == "surrounding_border") 
							? "SELECTED" : ""; ?>>Surrounding border</OPTION>             
						<OPTION value="no_border" <?php echo ($dud_options['user_directory_border'] == "no_border") 
							? "SELECTED" : ""; ?>>No border</OPTION> 
					</select> 
			</td>
			<td>Show a border around or between each listing.</td>
			<td></td>
		 </tr>
	     </table>
		
		 <table class="border-settings" id="border-settings">   
			<tr>
				<td><div id="top"><b>Thickness</b></div><BR>               
				<select name="<?php echo $dud_option_name;?>[user_directory_border_thickness]" id="user_directory_border_thickness">
					<OPTION value="1px" <?php echo ($dud_options['user_directory_border_thickness'] == "1px") ? "SELECTED" : ""; ?>>1px</OPTION> 
					<OPTION value="2px" <?php echo ($dud_options['user_directory_border_thickness'] == "2px") ? "SELECTED" : "";
										   ?>>2px</OPTION>             
					<OPTION value="3px" <?php echo ($dud_options['user_directory_border_thickness'] == "3px") ? "SELECTED" : ""; ?>>3px</OPTION>  
					<OPTION value="4px" <?php echo ($dud_options['user_directory_border_thickness'] == "4px") ? "SELECTED" : ""; 
										   ?>>4px</OPTION>              
					</select> 
				</td>
				<td><div id="top"><b>Color</b></div><BR>
					<input type="text" name="<?php echo $dud_option_name;?>[user_directory_border_color]" 
						value="<?php echo esc_attr( $dud_options['user_directory_border_color'] ); ?>" class="cpa-color-picker">
				</td>
				<td><div id="top"><b>Length</b></div><BR>
				<select name="<?php echo $dud_option_name;?>[user_directory_border_length]" id="user_directory_border_length">
						<OPTION value="100%" <?php echo ($dud_options['user_directory_border_length'] == "100%") ? "SELECTED" : ""; ?>>100%</OPTION> 
						<OPTION value="90%" <?php echo ($dud_options['user_directory_border_length'] == "90%") ? "SELECTED" : ""; ?>>90%</OPTION> 
						<OPTION value="80%" <?php echo ($dud_options['user_directory_border_length'] == "80%") ? "SELECTED" : ""; ?>>80%</OPTION> 
						<OPTION value="70%" <?php echo ($dud_options['user_directory_border_length'] == "70%") ? "SELECTED" : ""; ?>>70%</OPTION> 
						<OPTION value="60%" <?php echo ($dud_options['user_directory_border_length'] == "60%") ? "SELECTED" : ""; ?>>60%</OPTION> 
						<OPTION value="50%" <?php echo ($dud_options['user_directory_border_length'] == "50%") ? "SELECTED" : ""; ?>>50%</OPTION> 
				 </select>
				 </td>
				 <td><div id="top"><b>Style</b></div><BR>
				 <select name="<?php echo $dud_option_name;?>[user_directory_border_style]" id="user_directory_border_style">
						<OPTION value="solid" <?php echo ($dud_options['user_directory_border_style'] == "solid") ? "SELECTED" : ""; ?>>solid</OPTION> 
						<OPTION value="dotted" <?php echo ($dud_options['user_directory_border_style'] == "dotted") ? "SELECTED" : ""; ?>>dotted</OPTION> 
						<OPTION value="dashed" <?php echo ($dud_options['user_directory_border_style'] == "dashed") ? "SELECTED" : ""; ?>>dashed</OPTION> 
						<OPTION value="double" <?php echo ($dud_options['user_directory_border_style'] == "double") ? "SELECTED" : ""; ?>>double</OPTION> 
						<OPTION value="groove" <?php echo ($dud_options['user_directory_border_style'] == "groove") ? "SELECTED" : ""; ?>>groove</OPTION> 
						<OPTION value="ridge" <?php echo ($dud_options['user_directory_border_style'] == "ridge") ? "SELECTED" : ""; ?>>ridge</OPTION> 
				 </select> </td>
				 <td></td>
			</tr>    	
		 </table> 
              
		 <table class="form-table">
		 <tr>
			<td><b>Show Email Addr</b>&nbsp;&nbsp;<input name="<?php echo $dud_option_name;?>[user_directory_email]" id="user_directory_email" type="checkbox" value="1" 
				<?php if(!empty($dud_options['user_directory_email'])) { checked( '1', $dud_options['user_directory_email'] ); } ?> /></td>
			<td><b>Show Website</b>&nbsp;&nbsp;<input name="<?php echo $dud_option_name;?>[user_directory_website]" id="user_directory_website" type="checkbox" value="1" 
				<?php if(!empty($dud_options['user_directory_website'])) { checked( '1', $dud_options['user_directory_website'] ); } ?> /></td>
			<td>Check the boxes to show these WordPress user profile fields in your directory. Do not enter these in the meta key fields below.</td>
			<td></td>
		 </tr>	 
		 <tr id="letter-link-dir-type">
			<td><div id="top"><b>Letter Links Font Size</b></div><br>
				<input type="text" size="2" maxlength="2" id="user_directory_letter_fs" name="<?php echo $dud_option_name;?>[user_directory_letter_fs]" 
					value="<?php echo esc_attr( $dud_options['user_directory_letter_fs'] ); ?>" /> px
			</td>
			<td><div id="top"><b>Letter Links Spacing</b></div><BR>
				<input type="text" size="2" maxlength="2" id="ud_alpha_link_spacer" name="<?php echo $dud_option_name;?>[ud_alpha_link_spacer]" 
					value="<?php echo esc_attr( $dud_options['ud_alpha_link_spacer'] ); ?>" /> px
			</td>
			<td>Letter Links Spacing: how much space (in pixels) to insert between each of the alphabetic links.</td>
			<td></td>
		 </tr>
			  
		 <tr>
			<td><div id="top"><b>Listing Font Size</b></div><BR>
				<input type="text" size="2" maxlength="2" id="user_directory_listing_fs" name="<?php echo $dud_option_name;?>[user_directory_listing_fs]" 
					value="<?php echo esc_attr( $dud_options['user_directory_listing_fs'] ); ?>" /> px
			</td>
			<td><div id="top"><b>Space Between Listings</b></div><BR>
				<input type="text" size="2" maxlength="2" id="user_directory_listing_spacing" name="<?php echo $dud_option_name;?>[user_directory_listing_spacing]" 
					value="<?php echo esc_attr( $dud_options['user_directory_listing_spacing'] ); ?>" /> px</td>
			<td>Space Between Listings: how much space (in pixels) to insert between each directory listing.</td>
			<td></td>
		 </tr>
	</table>	
<br/><br/>
</div>
<br/><br/>

<div class="dud-settings-section-header">&nbsp; Meta Fields Settings</div>
<div class="dud-settings-section">
	<table class="form-table">
	<tr>
        <td colspan="2"><b>WordPress Meta Key Names</b><br><?php echo dynamic_ud_load_meta_keys("wp"); ?></td> 
        <td id="list-box-instructions">A listing of the meta key fields <u>for reference only</u>. You must type or copy & paste the key name into the appropriate meta field below for the key field value to be displayed in the directory. Enter the key name using the SAME capitalization shown in the key names list.</td>
        <td></td>
     </tr>
	 <?php 
	 $dud_plugin_list = get_option('active_plugins');
	 
	 if ( in_array( 'cimy-user-extra-fields/cimy_user_extra_fields.php' , $dud_plugin_list ) || function_exists('bp_is_active') || in_array( 's2member/s2member.php' , $dud_plugin_list ) ) { ?>
		 <tr>
			<td colspan="2"><?php if ( in_array( 'cimy-user-extra-fields/cimy_user_extra_fields.php' , $dud_plugin_list ) ) { ?>
										<b>Cimy Field Names</b><br><?php echo dynamic_ud_load_meta_keys("cimy"); } 
								  else if(function_exists('bp_is_active')) { ?>
										<b>BuddyPress Extended Profile Field Names</b><BR><?php echo dynamic_ud_load_meta_keys("bp"); } 
								  else if(in_array( 's2member/s2member.php' , $dud_plugin_list ) ) { ?>
										<b>s2Member Custom Field Names</b><BR><?php echo dynamic_ud_load_meta_keys("s2m"); } ?>
			</td>
			<td id="list-box-instructions">You may also include any of these custom fields in your directory. <?php if(function_exists('bp_is_active')) { ?> Note: BuddyPress may clear the WordPress "last name" profile field in certain circumstances. Please ensure this field is not blank if sorting by last name, or the user will NOT appear in the directory.<?php } ?></td>
			<td></td>
		 </tr>
	 <?php } ?>
     <tr>
        <td><b><span style='color:#08788c;'>USER META FIELDS</span></b></td>
        <td>
        	<select name="<?php echo $dud_option_name;?>[user_directory_num_meta_flds]" id="user_directory_num_meta_flds">
	            	<OPTION value="1" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "1") ? "SELECTED" : ""; ?>>1</OPTION> 
	            	<OPTION value="2" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "2") ? "SELECTED" : ""; ?>>2</OPTION> 
					<OPTION value="3" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "3") ? "SELECTED" : ""; ?>>3</OPTION> 
	            	<OPTION value="4" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "4") ? "SELECTED" : ""; ?>>4</OPTION> 
	            	<OPTION value="5" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "5") ? "SELECTED" : ""; ?>>5</OPTION> 
	            	<OPTION value="6" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "6") ? "SELECTED" : ""; ?>>6</OPTION>
	            	<OPTION value="7" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "7") ? "SELECTED" : ""; ?>>7</OPTION>
	            	<OPTION value="8" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "8") ? "SELECTED" : ""; ?>>8</OPTION>
	            	<OPTION value="9" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "9") ? "SELECTED" : ""; ?>>9</OPTION>
	            	<OPTION value="10" <?php echo (!empty($dud_options['user_directory_num_meta_flds']) && $dud_options['user_directory_num_meta_flds'] == "10") ? "SELECTED" : ""; ?>>10</OPTION> 
		</select> 
	</td>
        <td>Use the dropdown to show extra meta fields or hide unneeded ones. If you hide a meta key name/label field, that field will automatically be cleared.</td>
        <td></td>
     </tr>	
	 
	 <?php 
			for($inc = 1; $inc < 11; $inc++)
			{ 
				if( !empty($dud_options['user_directory_meta_link_' . $inc]) && $dud_options['user_directory_meta_link_' . $inc] === '#' ) 
					$dud_options['user_directory_meta_label_' . $inc] = '#' . $dud_options['user_directory_meta_label_' . $inc]
		
		?> 
				 <tr id="meta_fld_<?php echo $inc; ?>">
					<td><b>Meta Key Name <?php echo $inc; ?></b><br><input type="text" id="user_directory_meta_field_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[user_directory_meta_field_<?php echo $inc; ?>]" 
						value="<?php echo esc_attr( $dud_options['user_directory_meta_field_' . $inc]); ?>" maxlength="50" /></td>
					<td><b>Meta Field Label <?php echo $inc; ?></b><br><input type="text" id="user_directory_meta_label_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[user_directory_meta_label_<?php echo $inc; ?>]" 
						value="<?php echo esc_attr( $dud_options['user_directory_meta_label_' . $inc] ); ?>" maxlength="50"/>  
						</td>
						
					<?php if($inc === 1) { ?>
							<td>Enter the meta key name for each meta field to be displayed, using the Key Names list for reference. You may optionally enter a label for each field, which will be displayed in bold. </td>
							<td width="23%"></td>
					<?php } else if ($inc === 2){ ?>
							<td>To hyperlink a meta field, place the # sign at the beginning of the Meta Label. Example: if the meta label is <b>Twitter</b>, you would type <b>#Twitter</b>. The # sign will not appear on your label in the directory and the field will be shown as a hyperlink.</td>
							<td></td>
					<?php } else { ?>
							<td></td>
							<td></td>
					<?php } ?>
				 </tr>
			
	  <?php } ?>
       <tr>
        <td><b><span style='color:#08788c;'>ADDRESS META FIELDS &nbsp;&nbsp;<div id="address-down-arrow" name="address-down-arrow"><i class="fa fa-angle-down" aria-hidden="true"></i></div><div id="address-up-arrow" name="address-up-arrow"><i class="fa fa-angle-up" aria-hidden="true"></i></div>
</span></b></td>
        <td><input name="<?php echo $dud_option_name;?>[user_directory_address]" id="user_directory_address" type="hidden" value="<?php echo $dud_options['user_directory_address'];?>"/>        	
        </td>
        <td>Expand this section if a formatted mailing address is needed. Note: the address fields will be cleared automatically if this section is minimized.</td>
        <td></td>
     </tr> 
        
     <tr id="street1">
        <td><b>Street 1 Meta Key Name</b></td>
        <td><input type="text" id="user_directory_addr_1" name="<?php echo $dud_option_name;?>[user_directory_addr_1]" 
            value="<?php echo esc_attr( $dud_options['user_directory_addr_1'] ); ?>" maxlength="50"/></td>
        <td>Enter your address meta keys here to display a formatted mailing address. Use the Key Names list above for reference.</td>
        <td></td>
     </tr>
     
     <tr id="street2">
        <td><b>Street 2 Meta Key Name</b></td>
        <td><input type="text" id="user_directory_addr_2" name="<?php echo $dud_option_name;?>[user_directory_addr_2]" 
            value="<?php echo esc_attr( $dud_options['user_directory_addr_2'] ); ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr>
     
     <tr id="city">
        <td><b>City Meta Key Name</b></td>
        <td><input type="text" id="user_directory_city" name="<?php echo $dud_option_name;?>[user_directory_city]" 
            value="<?php echo esc_attr( $dud_options['user_directory_city'] ); ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr>
          
     <tr id="state">
        <td><b>State Meta Key Name</b></td>
        <td><input type="text" id="user_directory_state" name="<?php echo $dud_option_name;?>[user_directory_state]" 
        	value="<?php echo esc_attr( $dud_options['user_directory_state'] ); ?>" /></td>
        <td></td>
     </tr> 
         
     <tr id="zip">
        <td><b>Zip Meta Key Name</b></td>
        <td><input type="text" id="user_directory_zip" name="<?php echo $dud_option_name;?>[user_directory_zip]" 
            value="<?php echo esc_attr( $dud_options['user_directory_zip'] ); ?>" maxlength="50"/></td>
        <td></td>
        <td></td>
     </tr> 	  
	</table>	
<br/><br/>
</div>
<br/><br/>

<div class="dud-settings-section-header">&nbsp; Layout Settings</div>
<div class="dud-settings-section">
	<table class="form-table">
		<tr>
        <td><b>Display Order</b></td>
        <td>
    		<ul id="sortable"> 
    		<?php 
			$sort_order_items = dynamic_ud_sort_order_admin( $dud_options['user_directory_sort_order'] );
    		foreach ($sort_order_items as $item)
			{ ?> 
  				<li class="sort-order-list-item" id="<?php echo esc_attr($item);?>">
  				    <div class="sort-order-text"><?php echo esc_attr($item);?></div></li>
  	 <?php  } ?>
   		 </ul> 
   		 <input type="hidden" id="user_directory_sort_order" name="<?php echo $dud_option_name;?>[user_directory_sort_order]" 
   		     value="<?php echo esc_attr( $dud_options['user_directory_sort_order'] ); ?>" />
   	</td>	
   	<td>Drag the list items up or down using your mouse to rearrange the display order. Note that the Sort Field (Last Name or Display Name) 
   	    will always be the first field shown.</td>
   	<td></td>
   </tr>
	</table>	
<br/><br/>
</div>

<?php if ( in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $dud_plugin_list ) ) 
 { ?>
<br/><br/>
<div class="dud-settings-section-header">&nbsp; Meta Fields Search Settings</div>
<div class="dud-settings-section">
	<table class="form-table">
            <tr>
				<td colspan="3" style="line-height:22px;"><b>Instructions</b><br><hr>Enter up to fifteen user meta search fields in addition to the last name/display name. If there is only one total search field, the label for that field will appear as placeholder text in the search input box at the top of your directory. 
				If there are two or more total search fields, the labels for these fields will be shown in a dropdown box next to the search input box at the top of your directory. Note: search fields will only be displayed if the "Show Search Box" box is checked in your DUD settings above.<hr></td>
			</tr>
						
			<tr>
				<td><b>Last Name / Display Name</b></td>
				<td>
					<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_show_last_name_srch_fld]" id="ud_show_last_name_srch_fld">
						<OPTION value="first" <?php echo (!empty($dud_options['ud_show_last_name_srch_fld']) && $dud_options['ud_show_last_name_srch_fld'] == "first") ? "SELECTED" : ""; ?>>Show</OPTION> 
						<OPTION value="never" <?php echo (!empty($dud_options['ud_show_last_name_srch_fld']) && $dud_options['ud_show_last_name_srch_fld'] == "never") ? "SELECTED" : ""; ?>>Hide</OPTION> 
					</select> 
				</td>
				<td>Show or hide the Last Name / Display Name (based on your Sort Field setting) as a search field.</td>
				<td></td>
			</tr>
			
			<tr>
				<td><b><span style='color:#08788c;'>SEARCH META FIELDS</span></b></td>
				<td>
					<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[user_directory_num_meta_srch_flds]" id="user_directory_num_meta_srch_flds">
						<OPTION value="1" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "1") ? "SELECTED" : ""; ?>>1</OPTION> 
						<OPTION value="2" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "2") ? "SELECTED" : ""; ?>>2</OPTION> 
						<OPTION value="3" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "3") ? "SELECTED" : ""; ?>>3</OPTION> 
						<OPTION value="4" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "4") ? "SELECTED" : ""; ?>>4</OPTION> 
						<OPTION value="5" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "5") ? "SELECTED" : ""; ?>>5</OPTION> 
						<OPTION value="6" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "6") ? "SELECTED" : ""; ?>>6</OPTION>
						<OPTION value="7" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "7") ? "SELECTED" : ""; ?>>7</OPTION>
						<OPTION value="8" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "8") ? "SELECTED" : ""; ?>>8</OPTION>
						<OPTION value="9" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "9") ? "SELECTED" : ""; ?>>9</OPTION>
						<OPTION value="10" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "10") ? "SELECTED" : ""; ?>>10</OPTION> 
						<OPTION value="11" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "11") ? "SELECTED" : ""; ?>>11</OPTION> 
						<OPTION value="12" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "12") ? "SELECTED" : ""; ?>>12</OPTION> 
						<OPTION value="13" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "13") ? "SELECTED" : ""; ?>>13</OPTION> 
						<OPTION value="14" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "14") ? "SELECTED" : ""; ?>>14</OPTION> 
						<OPTION value="15" <?php echo (!empty($dud_options['user_directory_num_meta_srch_flds']) && $dud_options['user_directory_num_meta_srch_flds'] == "15") ? "SELECTED" : ""; ?>>15</OPTION> 
					</select> 
				</td>
				<td>Select the number of meta fields you want to permit users to search on.</td>
				<td></td>
			</tr>	
		 
			<?php 
				for($inc = 1; $inc < 16; $inc++)
				{
					$dud_srch_fld_name =   !empty($dud_options['user_directory_meta_srch_field_' . $inc]) ? $dud_options['user_directory_meta_srch_field_' . $inc] : null;
					$dud_srch_fld_label =  !empty($dud_options['user_directory_meta_srch_label_' . $inc]) ? $dud_options['user_directory_meta_srch_label_' . $inc] : null;
					$dud_cimy_flag =       !empty($dud_options['ud_meta_srch_cimy_flag_' . $inc]) ? $dud_options['ud_meta_srch_cimy_flag_' . $inc] : null;
					$dud_bp_flag =         !empty($dud_options['ud_meta_srch_bp_flag_' . $inc]) ? $dud_options['ud_meta_srch_bp_flag_' . $inc] : null;
				?> 
					<tr id="meta_srch_fld_<?php echo $inc; ?>">
						<td><b>Meta Key Name <?php echo $inc; ?></b><br><input class="meta-flds-srch-key-input" type="text" id="user_directory_meta_srch_field_<?php echo $inc; ?>" 
							name="<?php echo $dud_option_name;?>[user_directory_meta_srch_field_<?php echo $inc; ?>]" value="<?php echo esc_attr( $dud_srch_fld_name ); ?>" maxlength="50"/></td>
						<td><b>Meta Field Label <?php echo $inc; ?></b><br><input class="meta-flds-srch-label-input" type="text" id="user_directory_meta_srch_label_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[user_directory_meta_srch_label_<?php echo $inc; ?>]" 
							value="<?php echo esc_attr( $dud_srch_fld_label ); ?>" maxlength="50"/>
							<input type="hidden" id="ud_meta_srch_cimy_flag_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[ud_meta_srch_cimy_flag_<?php echo $inc; ?>]" 
								value="<?php echo esc_attr( $dud_cimy_flag ); ?>" maxlength="2"/>
							<input type="hidden" id="ud_meta_srch_bp_flag_<?php echo $inc; ?>" name="<?php echo $dud_option_name;?>[ud_meta_srch_bp_flag_<?php echo $inc; ?>]" 
								value="<?php echo esc_attr( $dud_bp_flag ); ?>" maxlength="2"/></td>
						
						<?php if($inc === 1) { ?>
							<td>Each Search Meta Key Name should match one in your existing directory. The Search Meta Field Labels will be displayed as options in a dropdown box in the order you entered them here.</td>
						<?php } else { ?>
							<td></td>
						<?php } ?>
						
						<td></td>
						
					</tr>
		    <?php } ?>
				
				 <tr id="show_srch_results">
					<td><b>Show Search Results</b></td>
					<td>
						<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_show_srch_results]" id="ud_show_srch_results">
							<OPTION value="alpha-links" <?php echo (!empty($dud_options['ud_show_srch_results']) && $dud_options['ud_show_srch_results'] == "alpha-links") ? "SELECTED" : ""; ?>>Letter Links Format</OPTION>
							<OPTION value="single-page" <?php echo (!empty($dud_options['ud_show_srch_results']) && $dud_options['ud_show_srch_results'] == "single-page") ? "SELECTED" : ""; ?>>Single Page Format</OPTION> 
						</select> 
					</td>
					<td>The search results may be displayed either on a single page or by alphabet letter links. If page load time is an issue, select 'Letter Links Format' for improved performance.</td>
					<td></td>
				 </tr>
			 <tr>
				<td><b>Search Icon Color</b></td>
				<td>
					<select class="dd-menu-no-chk-box-width" name="<?php echo $dud_option_name;?>[ud_srch_icon_color]" id="ud_srch_icon_color">
						<OPTION value="dimgray" <?php echo (!empty($dud_options['ud_srch_icon_color']) && $dud_options['ud_srch_icon_color'] == "DimGray") ? "SELECTED" : ""; ?>>DimGray</OPTION> 
						<OPTION value="white" <?php echo (!empty($dud_options['ud_srch_icon_color']) && $dud_options['ud_srch_icon_color'] == "white") ? "SELECTED" : ""; ?>>White</OPTION> 
					</select> 
				</td>
				<td>Choose the color of the magnifying glass icon on the Search button.</td>
				<td></td>
			 </tr>
			 <tr>
				<td><b>Show 'Clear' link</b></td>
				<td><input name="<?php echo $dud_option_name;?>[ud_clear_search]" id="ud_clear_search" type="checkbox" 
				   value="1" <?php if(!empty($dud_options['ud_clear_search'])) { checked( '1', $dud_options['ud_clear_search'] ); } ?> />
				</td>
				<td>Check this box to show a 'Clear' link next to the search box. This provides an easy way to clear the search box and refresh the directory.</td>
				<td></td>
			 </tr>	
	</table>	
<br/><br/>
</div>

<?php } ?>
    
<?php submit_button('Save options', 'primary', 'user_directory_options_submit'); ?>

 </form>
</div>
<?php
}

/*** Settings Link on Plugin Management Screen ************************************/

function user_directory_settings_link($actions, $file) {

if(false !== strpos($file, 'user-directory'))
 $actions['settings'] = '<a href="options-general.php?page=user_directory">Settings</a>';
return $actions; 
}
add_filter('plugin_action_links', 'user_directory_settings_link', 2, 2);

/*** Register Settings on Page Init ***********************************************/

function user_directory_settings_init(){
	
	$dud_plugin_list = get_option('active_plugins');

	if ( in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $dud_plugin_list )) 
		do_action('dud_register_loaded_directory_setting');
	else
		register_setting( 'user_directory_options', 'dud_plugin_settings', 'dynamic_ud_validate');
	
}
add_action('admin_init', 'user_directory_settings_init');

/*** Validation Functions ***********************************************************/ 

function dynamic_ud_validate( $input ) 
{
    //var_dump($_POST);
    $dud_option_name = 'dud_plugin_settings';
	$dud_plugin_list = get_option('active_plugins');
	$found_error = false;
		
	if ( in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $dud_plugin_list )) 
	{
		
		if(!empty($_POST['dud_plugin_settings_1'])) $dud_option_name = 'dud_plugin_settings_1';
		else if(!empty($_POST['dud_plugin_settings_2'])) $dud_option_name = 'dud_plugin_settings_2';
		else if(!empty($_POST['dud_plugin_settings_3'])) $dud_option_name = 'dud_plugin_settings_3';
		else if(!empty($_POST['dud_plugin_settings_4'])) $dud_option_name = 'dud_plugin_settings_4';
		else if(!empty($_POST['dud_plugin_settings_5'])) $dud_option_name = 'dud_plugin_settings_5';
				
		add_option('dud_updated_settings', $dud_option_name  );
	}
	
    $input['user_directory_border_color'] = dynamic_ud_validate_hex( $input['user_directory_border_color'], $dud_option_name );
    if($input['user_directory_border_color'] === null) return get_option( $dud_option_name );

    $input['ud_letter_divider_font_color'] = dynamic_ud_validate_hex( $input['ud_letter_divider_font_color'], $dud_option_name );
    if($input['ud_letter_divider_font_color'] === null) return get_option( $dud_option_name );
    
    $input['ud_letter_divider_fill_color'] = dynamic_ud_validate_hex( $input['ud_letter_divider_fill_color'], $dud_option_name );
    if($input['ud_letter_divider_fill_color'] === null) return get_option( $dud_option_name );
    
    
    $input['user_directory_letter_fs'] = dynamic_ud_check_numeric( $input['user_directory_letter_fs'], $dud_option_name );
    if($input['user_directory_letter_fs'] === null) return get_option( $dud_option_name );
    
    $input['ud_alpha_link_spacer'] = dynamic_ud_check_numeric( $input['ud_alpha_link_spacer'], $dud_option_name );
    if($input['ud_alpha_link_spacer'] === null) return get_option( $dud_option_name );
    
    $input['user_directory_listing_fs'] = dynamic_ud_check_numeric( $input['user_directory_listing_fs'], $dud_option_name );
    if($input['user_directory_listing_fs'] === null) return get_option( $dud_option_name );
    
    $input['user_directory_listing_spacing'] = dynamic_ud_check_numeric( $input['user_directory_listing_spacing'], $dud_option_name );
    if($input['user_directory_listing_spacing'] === null) return get_option( $dud_option_name );
    
    $input['user_directory_addr_1'] = sanitize_text_field($input['user_directory_addr_1']);
    $input['user_directory_addr_2'] = sanitize_text_field($input['user_directory_addr_2']);
    $input['user_directory_city'] = sanitize_text_field($input['user_directory_city']);
    $input['user_directory_state'] = sanitize_text_field($input['user_directory_state']);
    $input['user_directory_zip'] = sanitize_text_field($input['user_directory_zip']);
    
	for($inc = 1; $inc < 11; $inc++)
	{ 
		$input['user_directory_meta_field_' . $inc] = sanitize_text_field($input['user_directory_meta_field_' . $inc]);
		$input['user_directory_meta_label_' . $inc] = sanitize_text_field($input['user_directory_meta_label_' . $inc]);
		
		if($input['user_directory_meta_label_' . $inc])
		{
			if ($input['user_directory_meta_label_' . $inc][0] === '#')
			{
				if(strlen($input['user_directory_meta_label_' . $inc]) > 1)
					$input['user_directory_meta_label_' . $inc] = substr($input['user_directory_meta_label_' . $inc], 1);
				else
					$input['user_directory_meta_label_' . $inc] = "";
				
				$input['user_directory_meta_link_' . $inc] = '#';
			}
		}
	}	
   
	if ( in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $dud_plugin_list ) ) 
	{
		$found_srch_fld = false;
		
		//Clear out the flag fields...
		for($inc = 1; $inc < 16; $inc++)
		{ 
			$input['ud_meta_srch_cimy_flag_'. $inc] = null;
			$input['ud_meta_srch_bp_flag_'. $inc] = null;
		}
		
		//Now determine the new flag fields
		for($inc = 1; $inc < 16; $inc++)
		{ 
			$input['user_directory_meta_srch_field_' . $inc] = sanitize_text_field($input['user_directory_meta_srch_field_' . $inc]);
			$input['user_directory_meta_srch_label_'. $inc] = sanitize_text_field($input['user_directory_meta_srch_label_' . $inc]);
			$input['ud_meta_srch_cimy_flag_'. $inc] = dud_check_cimy_field($input['user_directory_meta_srch_field_' . $inc]);
			$input['ud_meta_srch_bp_flag_'. $inc] = dud_check_bp_field($input['user_directory_meta_srch_field_' . $inc]);		
			
			if($input['user_directory_meta_srch_field_' . $inc] && !$input['user_directory_meta_srch_label_'. $inc])
			{
				add_settings_error( $dud_option_name, 'user_directory_bc_error', 'Please add a label for Meta Search Field ' . $inc, 'error' ); 
				$found_error = true;
			}
			
			if($input['user_directory_meta_srch_field_' . $inc])
				$found_srch_fld = true;
		}	
		
		if(!$found_srch_fld && $input['ud_show_last_name_srch_fld'] === "never")
		{
			add_settings_error( $dud_option_name, 'user_directory_bc_error', 'Please enter at least one Meta Search Field or uncheck the Show Search Box option.', 'error' ); 
			$found_error = true;
		}
	}

    return $input;
}


function dynamic_ud_validate_txt_fld( $input ) {

    if(isset($input))
    {
    	//our text fields will never be larger than 50 characters.
		if(strlen($input) > 50)
			$input = substr( $input, 0, 50 );
		
    	return sanitize_text_field($input);
    }
    
    return $input;
}

function dynamic_ud_validate_hex( $input, $dud_option_name ) {

   if(isset($input))
   {
		if( !dynamic_ud_check_color( sanitize_text_field($input) ) ) 
		{
        	// $setting, $code, $message, $type
       		add_settings_error( $dud_option_name, 'user_directory_bc_error', 'Border color must be a valid hexadecimal value!', 'error' ); 
         
       		return null;
		} 
		else
			return sanitize_text_field($input);
   }  
}

function dynamic_ud_check_color( $value ) { 
     
    if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #     
        return true;
    }
     
    return false;
}

function dynamic_ud_check_numeric($input, $dud_option_name) {

	if (!is_numeric($input)) {

       		add_settings_error( $dud_option_name, 'user_directory_fs_error', 'All pixel sizes must be a numeric value!', 'error' ); 
         
        	// Return the previous valid value
       		return null;
	}
	
	//our numeric fields will never be larger than two digits.
	if(strlen($input) > 2)
		$input = substr( $input, 0, 2 );
		
	return sanitize_text_field($input);
}

function dynamic_ud_sort_order_admin( $input ) {
       
     $output = "";
     
     if($input) 
     {
     	 //append the newly added Meta Flds to list
     	 if(strpos($input, 'MetaKey5') === FALSE) $input .= ',MetaKey5'; 
     	 if(strpos($input, 'MetaKey6') === FALSE) $input .= ',MetaKey6'; 
     	 if(strpos($input, 'MetaKey7') === FALSE) $input .= ',MetaKey7'; 
     	 if(strpos($input, 'MetaKey8') === FALSE) $input .= ',MetaKey8'; 
     	 if(strpos($input, 'MetaKey9') === FALSE) $input .= ',MetaKey9'; 
     	 if(strpos($input, 'MetaKey10') === FALSE) $input .= ',MetaKey10'; 
     	 
         $output = explode(',', $input);  
     }
     else
     {
     	$output = "Address,Email,Website,MetaKey1,MetaKey2,MetaKey3,MetaKey4,MetaKey5,MetaKey6,MetaKey7,MetaKey8,MetaKey9,MetaKey10";
     	$output = explode(',', $output);
     }
     
     return $output;
}

function dynamic_ud_load_meta_keys($meta_type) {

	global $wpdb;
	$list_box = "";
	
	if($meta_type === "cimy" && defined("DUD_CIMY_FIELDS_TABLE")) 
	{
		$results = $wpdb->get_results("SELECT distinct NAME FROM " . DUD_CIMY_FIELDS_TABLE );
		
		if($results)
		{			
			$meta_key_list = "<textarea id='styled' spellcheck='false' rows='4' cols='55'>";
			
			$list_length = count($results);
			$cnt = 1;	
			
			foreach ($results as $result)
			{ 
				$meta_key_list .= $result->NAME; 
				if($cnt !== $list_length) $meta_key_list .= "\n";
   				$cnt++;
    			}
    				
    			$meta_key_list .= "</textarea>";
    			return $meta_key_list;
    		}
	}
	else if($meta_type === "bp" && defined("DUD_BP_PLUGIN_FIELDS_TABLE")) 
	{
		$results = $wpdb->get_results("SELECT distinct name FROM " . DUD_BP_PLUGIN_FIELDS_TABLE . " where type <> 'option'");
		
		if($results)
		{			
			$meta_key_list = "<textarea id='styled' spellcheck='false' rows='4' cols='55'>";
			
			$list_length = count($results);
			$cnt = 1;	
			
			foreach ($results as $result)
			{ 
				$meta_key_list .= $result->name; 
				if($cnt !== $list_length) $meta_key_list .= "\n";
   				$cnt++;
    			}
    				
    			$meta_key_list .= "</textarea>";
    			return $meta_key_list;
    		}
	}
	else if($meta_type === "s2m") 
	{		
		$meta_key_list = "<textarea id='styled' spellcheck='false' rows='4' cols='55'>";
		
		$flds_arr = get_s2member_custom_fields();
		
		if(!empty($flds_arr))
		{
			$list_length = count($flds_arr);
			$cnt = 1;
			
			foreach($flds_arr as $key => $value) {
				$meta_key_list .= $key;
				if($cnt !== $list_length) $meta_key_list .= "\n";
   				$cnt++;
			}
			
			$meta_key_list .= "</textarea>";
			return $meta_key_list;
		}
	}
	else
	{
		$user_meta_key_val_list = array();
		$user_meta_key_list = array();
		
		$results = $wpdb->get_results("SELECT user_id FROM " . $wpdb->prefix . "usermeta ORDER BY RAND() LIMIT 300");
		
		if($results)
		{
		        // Skip known WordPress meta fields that do not apply 
			$skip_me = "last_name*rich_editing*comment_shortcuts*admin_color*use_ssl*show_admin_bar_front
                        		*dismissed_wp_pointers*session_tokens*wp_user-settings*wp_user-settings-time
                        			*default_password_nag*wp_capabilities*wp_user_level*wporg_favorites
                        				*closedpostboxes_dashboard*metaboxhidden_dashboard*meta-box-order_dashboard";
                        		
			foreach ($results as $result)
			{ 		
				$all_meta_for_user = array_map( function( $a ){ return $a[0]; }, get_user_meta( $result->user_id ) );
							
				foreach ($all_meta_for_user as $key => $value) 
				{
					$key_exists = false;
					foreach ($user_meta_key_val_list as $key1 => $value1) 
					{
						if($key === $key1) $key_exists = true;
					}
					
					if(!$key_exists)
					{					 
						$pos = strpos($skip_me, $key);
   					
   						if($pos === false) 
   						{
   							if($value) $user_meta_key_val_list[$key] = $value;						
   							if($value) array_push($user_meta_key_list, $key);
    					}
    				}
				}
			}	
			
			$meta_key_list = "<textarea id='styled' spellcheck='false' rows='4' cols='55'>";
			
			$list_length = count($user_meta_key_list);
			$cnt = 1;
			
			asort($user_meta_key_list, SORT_STRING | SORT_FLAG_CASE | SORT_NATURAL);

			if($user_meta_key_list) 
			{		
				foreach ($user_meta_key_list as $key2) 
				{			
   					$meta_key_list .= $key2;
   					if($cnt !== $list_length) $meta_key_list .= "\n";
   					$cnt++;
				}
				
				$meta_key_list .= "</textarea>";
				return $meta_key_list;
			}		
		}
	}
	
	return "";
}

function dud_check_cimy_field($fld) {

	global $wpdb;
	
	$dud_plugin_list = get_option('active_plugins');
		
	if ( in_array( 'cimy-user-extra-fields/cimy_user_extra_fields.php' , $dud_plugin_list ) ) 
	{
		if(defined("DUD_CIMY_FIELDS_TABLE")) {
			
			$results = $wpdb->get_results("SELECT distinct NAME FROM " . DUD_CIMY_FIELDS_TABLE . " where NAME = '" . $fld . "'");
			
			if($results)
				return "1";
		}
	}
	
	return "";
}

function dud_check_bp_field($fld) {

	global $wpdb;
	
	$dud_plugin_list = get_option('active_plugins');
    	
	if( function_exists('bp_is_active'))
	{
		if(defined("DUD_BP_PLUGIN_FIELDS_TABLE")) {
			
			$results = $wpdb->get_results("SELECT distinct name FROM " . DUD_BP_PLUGIN_FIELDS_TABLE . " where name = '" . $fld . "'");
			
			if($results)
			{
				return "1";
			}
		}
	}
	
	return "";
}

function dud_check_s2m_field($fld, $fld_type) {

	global $wpdb;
	$dud_plugin_list = get_option('active_plugins');
    	
	if(in_array( 's2member/s2member.php' , $dud_plugin_list ) )
	{
		$s2member_custom_fields = get_s2member_custom_fields();

		foreach ($s2member_custom_fields as $key => $value) 
		{
			if($fld === $key && !$fld_type) return "1";
			else if($fld === $key && $fld_type)
			{
				if(is_array($value)) return "a";
				else return "s";
			}
		}	
	}
	
	return "";
}

function dynamic_ud_roles_listbox($selected_roles_arr, $dud_option_name) 
{
	global $wp_roles;

	$wproles = $wp_roles->get_names();

	$ud_listbox = "<SELECT style='height:100%;width:98%;font-size:14px;letter-spacing:1px' name='" . $dud_option_name . "[ud_hide_roles][]' size='5' multiple='multiple'>";
		
	foreach($wproles as $role_name)
	{
		$ud_listbox .= "<option value='{$role_name}'";
		
		if($selected_roles_arr){
			if(in_array($role_name, $selected_roles_arr))
				$ud_listbox .= " SELECTED";
		}
				
		$ud_listbox .= ">{$role_name}</option>";
	}	
	
	$ud_listbox .= "</SELECT>";

	return $ud_listbox;
}

function dynamic_ud_users_listbox($selected_users_arr, $dud_option_name) 
{
	global $wpdb;
	$ud_listbox = "";
	
	$results = $wpdb->get_results("SELECT DISTINCT user_id from " . $wpdb->prefix . "usermeta WHERE meta_key = 'last_name' order by meta_value");
		
	if($results)
	{           
		$ud_listbox = "<SELECT style='height:100%;width:98%;font-size:14px;letter-spacing:1px' name='" . $dud_option_name . "[ud_users_exclude_include][]' size='5' multiple='multiple'>";
		
		foreach($results as $result)
		{
			$ud_listbox .= "<option value='{$result->user_id}'";
		
			if($selected_users_arr){
				if(in_array($result->user_id, $selected_users_arr))
					$ud_listbox .= " SELECTED";
			}
			
			$user_first_name = get_user_meta($result->user_id, 'first_name', true);
	        	$user_last_name = get_user_meta($result->user_id, 'last_name', true);
				
			$ud_listbox .= ">{$user_last_name}, {$user_first_name}</option>";
		}
	}	
	else 
		return "";
	
	$ud_listbox .= "</SELECT>";

	return $ud_listbox;
}

/*function my_plugin_notice() {
    $user_id = get_current_user_id();
    if ( !get_user_meta( $user_id, 'dud_multiple_dirs_notice_dismissed' ) && current_user_can('manage_options') )
	{		
		$current_url = esc_url( home_url( '/' ) ) . 'wp-admin/options-general.php?page=user_directory&';
		echo '<div class="notice notice-warning"><p>Dynamic User Directory has a new <a href="http://sgcustomwebsolutions.com/wordpress-plugin-development/" target="_blank">Multiple Directories Add-on</a> available now!&nbsp;&nbsp;<a href="' . $current_url . 'my-plugin-dismissed">Dismiss</a></p></div>';
	}
}
add_action( 'admin_notices', 'my_plugin_notice' );

function my_plugin_notice_dismissed() {
    $user_id = get_current_user_id();
    if ( isset( $_GET['my-plugin-dismissed'] ) )
        add_user_meta( $user_id, 'dud_multiple_dirs_notice_dismissed', 'true', true );
}
add_action( 'admin_init', 'my_plugin_notice_dismissed' );*/