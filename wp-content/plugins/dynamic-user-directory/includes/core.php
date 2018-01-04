<?php

/*** LIST OF DUD FILTERS ********************************************************************************

* dud_after_load_letters       = modify the alphabet letters corresponding to users in the directory
* dud_after_load_uids          = modify user ids of everyone shown in the directory (if "single page directory") 
*                                or page (if "alphabet letter links" directory)
* dud_after_load_sort_order    = modify sort order of directory meta fields 
* dud_after_load_meta_flds     = modify array containing the meta field names, labels, keys, and links
* dud_after_load_meta_vals     = modify array containing the meta field names, vals, labels, keys, and links
* dud_set_user_full_name       = modify the display format of the user's full name
* dud_set_avatar_url           = modify the url that points to the avatar image location
* dud_set_avatar_link          = modify the link that points to the user profile/author page 
* dud_set_user_profile_link    = modify the link that points to the user profile/author page
* dud_search_err               = modify the search error that is displayed when no results are found
* dud_no_users_msg             = modify the message that is displayed when the directory is empty
*
* META FIELDS SEARCH ADD-ON FILTERS
*
* dud_meta_fld_srch_load_alpha_links 
* dud_meta_fld_srch_print_alpha_links
* dud_meta_fld_srch_build_sql
* dud_build_srch_form
* dud_S2M_search
*
* ALPHA LINKS SCROLL ADD-ON FILTERS
*
* dud_print_scroll_letter_links
*
*********************************************************************************************************/

function DynamicUserDirectory( $atts )
{	
global $wpdb;
global $userid;

$plugins = get_option('active_plugins');
$loaded_options = "";
$letters = "";
$user_sql = "";
$srch_err = "";

$dud_options = get_option( 'dud_plugin_settings' );

/*** If the Multiple Directories add-on is installed, load the appropriate directory instance ***/
if ( in_array( 'dynamic-user-directory-multiple-dirs/dynamic-user-directory-multiple-dirs.php' , $plugins ))
{
	$loaded_options = 'dud_plugin_settings'; //default unless changed below
	
	if(!empty($atts) && $atts['name'] != "original")
	{	
		for($inc=0; $inc <= 4; $inc++) 
		{	
			if( $dud_tmp_options = get_option( 'dud_plugin_settings_' . ($inc+1) ) )
			{
				if($atts['name'] === $dud_tmp_options['dud_instance_name'])
				{
					$dud_options = $dud_tmp_options;
					$loaded_options = 'dud_plugin_settings_' . ($inc+1);					
					break;
				}	
			}	
		}
	}
} 

/*** Load the scripts ***/
if (!wp_script_is( 'user-directory-style', 'enqueued' )) {
  
	wp_register_style('user-directory-style',  DYNAMIC_USER_DIRECTORY_URL . '/css/user-directory-min.css', false, 0.1);	
	wp_enqueue_style( 'user-directory-style' );
	//wp_register_style('user-directory-style',  DYNAMIC_USER_DIRECTORY_URL . '/css/user-directory.css', false, 0.1);	
	//wp_enqueue_style( 'user-directory-style' );
	wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
}

/*** Turn debug on if debug mode is set to "on" ***/
global $dynamic_ud_debug; 
$dynamic_ud_debug = false;

if(current_user_can('administrator'))
	if($dud_options['ud_debug_mode'] === "on")
		$dynamic_ud_debug = true;
		
if($dynamic_ud_debug)
	dynamic_ud_dump_settings($loaded_options);
		
/*** Get sort, hide roles, search, and include/exclude fields ***/
$user_directory_sort  = !empty($dud_options['user_directory_sort']) ? $dud_options['user_directory_sort'] : null;
$ud_hide_roles        = !empty($dud_options['ud_hide_roles']) ? $dud_options['ud_hide_roles'] : null;
$exc_inc_radio        = !empty($dud_options['ud_exclude_include_radio']) ? $dud_options['ud_exclude_include_radio'] : null;
$inc_exc_user_ids     = !empty($dud_options['ud_users_exclude_include']) ? $dud_options['ud_users_exclude_include'] : null;
$ud_directory_type    = !empty($dud_options['ud_directory_type']) ? $dud_options['ud_directory_type'] : null;
$ud_show_srch         = !empty($dud_options['ud_show_srch']) ? $dud_options['ud_show_srch'] : null;

/*** Get the search input field or search letter ***/
$dud_user_srch_key = !empty($_REQUEST ["dud_user_srch_key"]) ? $_REQUEST ["dud_user_srch_key"] : null; //For the meta flds srch add-on

if(is_null($dud_user_srch_key) || $dud_user_srch_key === "") 
	$dud_user_srch_key = '';

$dud_user_srch_name = !empty($_REQUEST ["dud_user_srch_val"]) ? $_REQUEST ["dud_user_srch_val"] : null;

if(is_null($dud_user_srch_name) || $dud_user_srch_name === "") 
	$dud_user_srch_name = '';

/*** Meta Fields Search Add-On: if a search value was entered & the results should be shown on single page ***/
if(in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $plugins ) &&
	!empty($dud_options['ud_show_srch_results']) && $dud_options['ud_show_srch_results'] === 'single-page' && $dud_user_srch_name)
{	
	$ud_directory_type = "all-users";
}

/*** Input validation and sanitization ***/
if(strlen($dud_user_srch_name) > 0) 
{
	if(strlen($dud_user_srch_name) > 50)
		return "<H3>The search field is limited to 50 characters!</H3>";
			
	$dud_user_srch_name = sanitize_text_field(htmlspecialchars($dud_user_srch_name));
}

/*** Load an array with alphabet letters corresponding to existing user last names ***/
if ( ! in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $plugins ) || !$dud_user_srch_name || !$ud_show_srch)
	$letters = dynamic_ud_load_alpha_links($user_directory_sort, $ud_hide_roles, $exc_inc_radio, $inc_exc_user_ids, $dud_user_srch_name, $ud_directory_type);

else	
{	
	$letters = apply_filters('dud_meta_fld_srch_load_alpha_links', $letters, $user_directory_sort, $ud_hide_roles, $exc_inc_radio, 
		$inc_exc_user_ids, $dud_user_srch_key, $dud_user_srch_name, $loaded_options);
	
	// If meta field search came up empty
	if(count($letters) == 0 && $dud_user_srch_name ) 
	{	
		$letters = dynamic_ud_load_alpha_links($user_directory_sort, $ud_hide_roles, $exc_inc_radio, $inc_exc_user_ids, "", $ud_directory_type);
		$srch_err = apply_filters('dud_search_err', "<H3>No users were found matching your search criteria.</H3>");
	}
}

/* For developers who want to modify the plugin */
$letters = apply_filters( 'dud_after_load_letters', $letters);

if ( count($letters) == 0 && !$dud_user_srch_name )	return apply_filters('dud_no_users_msg', "<H3>There are no users in the directory at this time.</H3>");	

/*** Get last name letter ***/

// If NOT using the Meta Fields Search add-on 
if ( ! in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $plugins ) || !$ud_show_srch) 
{
	if(strlen($dud_user_srch_name) > 0) { // if a basic last name search --> get the first letter
		
		$last_name_letter = substr($dud_user_srch_name, 0, 1); 
	}
	else // If not a last name search --> get letter from request or default to first available letter
	{
		$last_name_letter = !empty($_REQUEST ["letter"]) ? $_REQUEST ["letter"] : null;

		if(is_null($last_name_letter) || $last_name_letter === "") 
			$last_name_letter = $letters[0];
	}	
}
else
{		
	$last_name_letter = !empty($_REQUEST ["letter"]) ? $_REQUEST ["letter"] : null;
	
	$search_button_clicked = !empty($_REQUEST ["search_button_clicked"]) ? $_REQUEST ["search_button_clicked"] : null; 
	if(empty($search_button_clicked)) $search_button_clicked = "";
	
	if(is_null($last_name_letter) || $last_name_letter === "" || ($search_button_clicked && !($ud_directory_type === "all-users"))) 
		$last_name_letter = $letters[0];
	
	if(!is_null($last_name_letter) && !in_array($last_name_letter, $letters) && !($ud_directory_type === "all-users"))
		$srch_err = apply_filters('dud_search_err', "<H3>No users for the selected letter were found matching your search criteria.</H3>");
}

/*** Validate request data ***/
if(!ctype_alpha($last_name_letter) || strlen($last_name_letter) > 1) 
	return apply_filters('dud_no_users_msg', "<H3>There are no users in the directory at this time.</H3>");	

/*** BUILD SQL QUERY ****************************************************************/

$roles_sql = "";
$include_exclude_sql = "";
$S2M_keymatch = false;
$uids = array();

// Flag if this is an S2Member srch fld
if(in_array( 's2member/s2member.php' , $plugins)) 
{
	$flds_arr = get_s2member_custom_fields();

	foreach($flds_arr as $key => $value)
		if(strtoupper($dud_user_srch_key) === strtoupper($key)) $S2M_keymatch = true;	
}

// If not running a meta field search other than last name 		
if ( ! in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $plugins ) || !$dud_user_srch_name || !$ud_show_srch) 
{	
	if($ud_hide_roles && !($exc_inc_radio === 'include' && $inc_exc_user_ids))
		$roles_sql = dynamic_ud_build_roles_query($user_directory_sort, $ud_hide_roles);
		
	if($inc_exc_user_ids)
		$include_exclude_sql = dynamic_ud_build_inc_exc_query($user_directory_sort, $ud_hide_roles, $exc_inc_radio, $inc_exc_user_ids);	

	if($user_directory_sort === "last_name") 
	{    
		if(!($ud_directory_type === "all-users"))
			$user_sql = "SELECT DISTINCT user_id from " . $wpdb->prefix 
				. "usermeta WHERE meta_key = 'last_name' and meta_value like '" . $last_name_letter . "%' ";
		else
			$user_sql = "SELECT DISTINCT user_id from " . $wpdb->prefix . "usermeta WHERE meta_key = 'last_name'";
		
		if($ud_hide_roles && !($exc_inc_radio === 'include' && $inc_exc_user_ids)) 
			$user_sql .= " AND " . $roles_sql;
			
		if($inc_exc_user_ids) 
			$user_sql .= " AND " . $include_exclude_sql;
			
		$user_sql .= " ORDER BY meta_value"; 
	}		
	else
	{ 
		if(!($ud_directory_type === "all-users"))
			$user_sql = "SELECT DISTINCT ID, display_name from " . $wpdb->prefix . "users WHERE display_name like '" . $last_name_letter . "%'" ;
		else
			$user_sql = "SELECT DISTINCT ID, display_name from " . $wpdb->prefix . "users" ;
		
		if($ud_hide_roles && !($exc_inc_radio === 'include' && $inc_exc_user_ids)) 
		{     
			if($ud_directory_type === "all-users")
				$user_sql .= " WHERE " . $roles_sql;
			else
				$user_sql .= " AND " . $roles_sql;
		}
		if($inc_exc_user_ids) 
		{
			if($ud_directory_type === "all-users" && !($ud_hide_roles && !($exc_inc_radio === 'include' && $inc_exc_user_ids))) 
				$user_sql .= " WHERE " . $include_exclude_sql;
			else
				$user_sql .= " AND " . $include_exclude_sql;
		}

		$user_sql .= " ORDER BY display_name"; 	
	}
	
	$uids = $wpdb->get_results($user_sql);
}
// If running a meta field search on an S2Member custom field 
else if(in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $plugins ) && $S2M_keymatch)
{
	// Only used if a Meta Fld search was run on an S2M field 
	$uids = apply_filters('dud_S2M_search', $uids, $loaded_options, $last_name_letter);

}
// If running any other kind of meta field search 		
else 
{
	$user_sql = apply_filters( 'dud_meta_fld_srch_build_sql', $user_sql, $last_name_letter, $dud_user_srch_key, $dud_user_srch_name, $loaded_options);
	$uids = $wpdb->get_results($user_sql);
}

/* For developers who want to modify the plugin */
$uids = apply_filters( 'dud_after_load_uids', $uids);

if($dynamic_ud_debug) { echo "<PRE>Load Users SQL:<BR><BR>" . $user_sql . "<BR><BR></PRE>"; }

/*** If users were found ***/
if($uids)
{   
	$inc = 1;
	$listing_cnt = 0;
	$user_fullname = "";
	$user_website = ""; 
	$user_first_name = "";
	$user_last_name = ""; 
	$user_approval = ""; 
	$user_avatar_url = "";
	$printed_letter = "";
	$user_contact_info = "";
	
	/*** OPTION SETTINGS ******************************************************************/
	
	/*** Display Preferences ***/
    $user_directory_show_avatar            = !empty($dud_options['user_directory_show_avatars']) ? $dud_options['user_directory_show_avatars'] : null;	     
    $user_directory_avatar_style           = !empty($dud_options['user_directory_avatar_style']) ? $dud_options['user_directory_avatar_style'] : null;	       
	$user_directory_border                 = !empty($dud_options['user_directory_border']) ? $dud_options['user_directory_border'] : null;
	$user_directory_border_length          = !empty($dud_options['user_directory_border_length']) ? $dud_options['user_directory_border_length'] : null;
	$user_directory_border_style           = !empty($dud_options['user_directory_border_style']) ? $dud_options['user_directory_border_style'] : null;
	$user_directory_border_color           = !empty($dud_options['user_directory_border_color']) ? $dud_options['user_directory_border_color'] : null;
	$user_directory_border_thickness       = !empty($dud_options['user_directory_border_thickness']) ? $dud_options['user_directory_border_thickness'] : null;
	$user_directory_listing_fs             = !empty($dud_options['user_directory_listing_fs']) ? $dud_options['user_directory_listing_fs'] : null;
	$user_directory_listing_sp             = !empty($dud_options['user_directory_listing_spacing']) ? $dud_options['user_directory_listing_spacing'] : null;
	$ud_author_page                        = !empty($dud_options['ud_author_page']) ? $dud_options['ud_author_page'] : null;
	$ud_auth_or_bp                         = !empty($dud_options['ud_auth_or_bp']) ? $dud_options['ud_auth_or_bp'] : null;
	$ud_show_author_link                   = !empty($dud_options['ud_show_author_link']) ? $dud_options['ud_show_author_link'] : null;
	$ud_target_window                      = !empty($dud_options['ud_target_window']) ? $dud_options['ud_target_window'] : null;
	$ud_letter_divider                     = !empty($dud_options['ud_letter_divider']) ? $dud_options['ud_letter_divider'] : null;
	$ud_letter_divider_font_color          = !empty($dud_options['ud_letter_divider_font_color']) ? $dud_options['ud_letter_divider_font_color'] : null;
	$ud_letter_divider_fill_color          = !empty($dud_options['ud_letter_divider_fill_color']) ? $dud_options['ud_letter_divider_fill_color'] : null;
	$ud_srch_style                         = !empty($dud_options['ud_srch_style']) ? $dud_options['ud_srch_style'] : null;
	$ud_format_name                        = !empty($dud_options['ud_format_name']) ? $dud_options['ud_format_name'] : null;
	$letter_div_shadow = ""; 
	
	if($ud_letter_divider === "ld-ds") $letter_div_shadow = " letter-div-shadow";
	
	if( ($ud_directory_type === "all-users" || (!empty($dud_options['ud_show_srch_results']) && $dud_options['ud_show_srch_results'] === 'single-page')) && $ud_letter_divider !== "nld") 
	{			
		if(!($user_directory_border === "surrounding_border" || $user_directory_border === "dividing_border"))
			$user_directory_border_length = "65%"; //set letter divider length	
	}
	
	$sort_order_items = dynamic_ud_sort_order( $dud_options['user_directory_sort_order'] );
		
	// For developers who want to modify the plugin 
	$sort_order_items = apply_filters( 'dud_after_load_sort_order', $sort_order_items);
	
	/*** Meta field names, keys, and labels ***/
	$user_directory_addr_1_op = $dud_options['user_directory_addr_1'];
	$user_directory_addr_2_op = $dud_options['user_directory_addr_2'];
	$user_directory_city_op = $dud_options['user_directory_city'];
	$user_directory_state_op = $dud_options['user_directory_state'];
	$user_directory_zip_op = $dud_options['user_directory_zip'];
	$user_directory_meta_flds = array();

	$fldIdx = 0;
	for ($inc=0; $inc<10; $inc++)
	{
		$tmp_fld = $dud_options['user_directory_meta_field_' . ($inc+1)];
		
		if($tmp_fld) 
		{
			$user_directory_meta_flds[$fldIdx] = array();
			$user_directory_meta_flds[$fldIdx]['field'] = $tmp_fld;
			$user_directory_meta_flds[$fldIdx]['label'] = !empty($dud_options['user_directory_meta_label_' . ($inc+1)]) ? $dud_options['user_directory_meta_label_' . ($inc+1)] : null;
			$user_directory_meta_flds[$fldIdx]['key'] = "MetaKey" . ($inc+1);
			$user_directory_meta_flds[$fldIdx]['link'] = !empty($dud_options['user_directory_meta_link_' . ($inc+1)]) ? $dud_options['user_directory_meta_link_' . ($inc+1)] : null;
			$fldIdx++;
		}	
		else
		{
			$idx = array_search( ("MetaKey" . ($inc+1) ), $sort_order_items);
			
			if($idx===false) continue;
			else unset($sort_order_items[$idx]); //if meta key has empty value, remove from sort list
		}
	}
	
	if($dynamic_ud_debug) {
		echo "<PRE>";
		echo "Meta Fld Types<BR><BR>";
	
		for($inc=0; $inc < sizeof($user_directory_meta_flds); $inc++ ) 
		{
			echo "Fld: " . $user_directory_meta_flds[$inc]['field'] . "<BR>";		
	
			$fld_type = "WordPress";
			
			if(dud_chk_bp_field($user_directory_meta_flds[$inc]['field'])) $fld_type = "BuddyPress";
			else if(dud_chk_s2m_field($user_directory_meta_flds[$inc]['field'], false))  $fld_type = "BuddyPress";
			else if(dud_chk_cimy_field($user_directory_meta_flds[$inc]['field']))  $fld_type = "Cimy";
			
			echo "Field Type: " . $fld_type . "<BR><BR>";
						
		}
		echo "<BR></PRE>";
	}
		
	// For developers who want to modify the plugin 
	$user_directory_meta_flds = apply_filters('dud_after_load_meta_flds', $user_directory_meta_flds);
	
	/*** Meta fields from wp_users table ***/
	$user_directory_email = !empty($dud_options['user_directory_email']) ? $dud_options['user_directory_email'] : null;        //wp_users field
	$user_directory_website = !empty($dud_options['user_directory_website']) ? $dud_options['user_directory_website'] : null;  //wp_users field
	
	/*** Set defaults for empty options ***/
	if(!$user_directory_border_length) $user_directory_border_length = "100%";
	if(!$user_directory_border_style) $user_directory_border_style = "solid";
	if(!$user_directory_border_color) $user_directory_border_color = "#dddddd";
	if(!$user_directory_border_thickness) $user_directory_border_thickness = "1px";
	if(!$user_directory_listing_fs) $user_directory_listing_fs = "12px";
	if(!$user_directory_listing_sp) $user_directory_listing_sp = "20";
	if(!$ud_format_name) $ud_format_name = "fl";
																	
	/*** DISPLAY DIRECTORY ********************************************************************************/
			
	/*** Display the alphabet links at the top ***/
	if(!($ud_directory_type === "all-users"))
	{
		/*** For the meta fld search add-on ***/
		if ( in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $plugins ) && $ud_show_srch) 
			$alpha_links = apply_filters('dud_meta_fld_srch_print_alpha_links', $letters, $dud_options['ud_alpha_link_spacer'], $dud_options['user_directory_letter_fs'], $dud_user_srch_key, $dud_user_srch_name) . "<BR>";
		else 
			$alpha_links = dynamic_ud_print_alpha_links($letters, $dud_options['ud_alpha_link_spacer'], $dud_options['user_directory_letter_fs']) . "<BR>";
		
		$user_contact_info .= $alpha_links;
	}
	else
	{
	    /* For Alpha Links Scroll add-on */
		$user_contact_info .= apply_filters( 'dud_print_scroll_letter_links', $user_contact_info, $letters, $loaded_options );
	}	
		
	/*** Search box stuff ***/	
	if($ud_show_srch)
	{	
		$user_srch_placeholder_txt = "Last Name";
	
		if($user_directory_sort !== "last_name")
			$user_srch_placeholder_txt = "Display Name";
	
		if($ud_srch_style === "transparent") $ud_srch_style = "background:none;";
		else $ud_srch_style = "";
	        	
        $user_directory_srch_fld = "<form id='dud_user_srch' method='post'>\n"; 
		$user_directory_srch_fld .= "    \t<DIV id='user-srch' style='width:45%;height:40px;'>\n";
       	$user_directory_srch_fld .= "          \t\t<input type='text' id='dud_user_srch_val' name='dud_user_srch_val' style='"  . $ud_srch_style . "'"; 
        $user_directory_srch_fld .= " value='' maxlength='50' placeholder='" . $user_srch_placeholder_txt . "'/>\n";
        $user_directory_srch_fld .= "        \t\t<button type='submit' id='dud_user_srch_submit' name='dud_user_srch_submit' value=''>\n";
        $user_directory_srch_fld .= "             \t\t\t<i class='fa fa-search fa-lg' aria-hidden='true'></i>\n";
        $user_directory_srch_fld .= "        \t\t</button>\n";
		$user_directory_srch_fld .= "     \t</DIV>\n";
        $user_directory_srch_fld .="</form><BR>\n"; 
		
		$user_directory_srch_fld = apply_filters('dud_build_srch_form', $user_directory_srch_fld, $loaded_options);
		
		if(!empty($srch_err))
		{
			$user_contact_info .= $user_directory_srch_fld . $srch_err;
			return $user_contact_info;
		}
		else
			$user_contact_info .= $user_directory_srch_fld;
    }
		
	/*** Determine if Cimy User Extra Fields plugin is installed and active ***/
	if ( in_array( 'cimy-user-extra-fields/cimy_user_extra_fields.php' , $plugins ) ) 
		$user_directory_cimy = TRUE;  //installed & active
	else
		$user_directory_cimy = FALSE; //not installed or inactive
		
	/*** Loop through all users with last name or display name matching the selected letter ***/
	
	foreach ($uids as $uid)
	{      
	    $user_id = 0;
		$user_directory_csz = "";
		$user_fullname = "";
		$user_website = ""; 
		$user_email = "";
		$user_first_name = "";
		$user_last_name = ""; 
		$user_directory_addr_1 = "";
		$user_directory_addr_2 = "";
		$user_directory_city = "";
		$user_directory_state = "";
		$user_directory_zip = "";
		$cimy_avatar_loc = "";
		$address_flds = array();
		$got_cimy_data = false;
		$letter_div_printed = false;
			
		//Remove old meta fld values from the previous iteration
		unset($user_directory_meta_flds_tmp);
		$user_directory_meta_flds_tmp = $user_directory_meta_flds;
		
		/*** GATHER THE DIRECTORY DATA ***************************************************************/	
		
		if($user_directory_sort === "last_name")
		{
			$user_id = $uid->user_id;
			$user_last_name = get_user_meta($user_id, 'last_name', true);
		}
		else
		{
			$user_id = $uid->ID; 
			$user_last_name = $uid->display_name;
		}
				
		/*** If running a last name srch and NOT using the Meta Search Add-on  ***/
		if ( ! in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $plugins ) ) 
		{
			if($dud_user_srch_name)
			{ 
				 if (!(strpos(strtoupper ($user_last_name), strtoupper ($dud_user_srch_name)) === 0))
				 {	         
					  continue;
				 }	 
			}
		}
		
		$ud_author_posts = count_user_posts( $user_id ); //used to see if we should link to the WP author page
 		
		/*** LOAD WP USER META DATA ***/
		
		if($user_directory_addr_1_op) $address_flds[0] = get_user_meta($user_id, $user_directory_addr_1_op, true);	 
		if($user_directory_addr_2_op) $address_flds[1] = get_user_meta($user_id, $user_directory_addr_2_op, true); 
		if($user_directory_city_op) $address_flds[2] = get_user_meta($user_id, $user_directory_city_op, true); 
		if($user_directory_state_op) $address_flds[3] = get_user_meta($user_id, $user_directory_state_op, true); 
		if($user_directory_zip_op) $address_flds[4] = get_user_meta($user_id, $user_directory_zip_op, true);
								
		for($inc=0; $inc < sizeof($user_directory_meta_flds_tmp); $inc++) 
		{		  
		   if($user_directory_meta_flds_tmp[$inc]['field']) 
		   {
			   //calling get_user_meta() this way so that we can parse meta fields than contain arrays
			   $user_meta_fld = get_user_meta($user_id, $user_directory_meta_flds_tmp[$inc]['field']);	
			   
			   $user_meta_fld = !empty($user_meta_fld[0]) ? $user_meta_fld[0] : null; //it will always be an array even for single values
				
			   $user_directory_meta_flds_tmp[$inc]['value'] =  dynamic_ud_parse_meta_val($user_meta_fld);
			}
		} 			
		
		/*** LOAD USER META DATA STORED IN SEPARATE TABLES BY OTHER PLUGINS ***/
		
		if ( $user_directory_cimy ) //Cimy fields
		{
			$user_directory_meta_flds_tmp = dud_load_cimy_vals($user_id, $dud_options, $user_directory_meta_flds_tmp);
			
			if(!empty($user_directory_meta_flds_tmp) && $user_directory_meta_flds_tmp[(sizeof($user_directory_meta_flds_tmp)-1)]['field'] === 'CIMY_ADDRESS')
			{
				$address_flds = $user_directory_meta_flds_tmp[(sizeof($user_directory_meta_flds_tmp)-1)]['value'];
				unset($user_directory_meta_flds_tmp[(sizeof($user_directory_meta_flds_tmp)-1)]);
			}
		}
		else if( function_exists('bp_is_active') )  //BuddyPress fields
		{
			$user_directory_meta_flds_tmp = dud_load_bp_vals($user_id, $dud_options, $user_directory_meta_flds_tmp);
			
			if(!empty($user_directory_meta_flds_tmp) && $user_directory_meta_flds_tmp[(sizeof($user_directory_meta_flds_tmp)-1)]['field'] === 'BP_ADDRESS')
			{
				$address_flds = $user_directory_meta_flds_tmp[(sizeof($user_directory_meta_flds_tmp)-1)]['value'];
				unset($user_directory_meta_flds_tmp[(sizeof($user_directory_meta_flds_tmp)-1)]);
			}
		}
		else if(in_array( 's2member/s2member.php' , $plugins ) )  //s2Member fields
		{
			$user_directory_meta_flds_tmp = dud_load_s2m_vals($user_id, $dud_options, $user_directory_meta_flds_tmp);
			
			if(!empty($user_directory_meta_flds_tmp) && $user_directory_meta_flds_tmp[(sizeof($user_directory_meta_flds_tmp)-1)]['field'] === 'S2M_ADDRESS')
			{
				$address_flds = $user_directory_meta_flds_tmp[(sizeof($user_directory_meta_flds_tmp)-1)]['value'];
				unset($user_directory_meta_flds_tmp[(sizeof($user_directory_meta_flds_tmp)-1)]);
			}
		}
		
		// For developers who want to modify the plugin 
		$user_directory_meta_flds_tmp = apply_filters('dud_after_load_meta_vals', $user_directory_meta_flds_tmp, $user_id);
	
		if($dynamic_ud_debug) {
			echo "<PRE>";
			echo "Meta Flds for User: " . $user_last_name . "<BR><BR>";
		
			for($inc=0; $inc < sizeof($user_directory_meta_flds_tmp); $inc++ ) 
			{
				echo "<b>Fld:</b> " . $user_directory_meta_flds_tmp[$inc]['field'] . "&nbsp;&nbsp;";	
				echo "<b>Lbl:</b> " . $user_directory_meta_flds_tmp[$inc]['label'] . "&nbsp;&nbsp;";	
				echo "<b>Key:</b> " . $user_directory_meta_flds_tmp[$inc]['key'] . "&nbsp;&nbsp;";	
				echo "<b>Val:</b> " . $user_directory_meta_flds_tmp[$inc]['value'] . "&nbsp;&nbsp;";	
				echo "<b>Link:</b> " . $user_directory_meta_flds_tmp[$inc]['link'] . "<BR>";				
			}
			echo "<BR></PRE>";
		}
		
		$userdata = get_userdata($user_id);	//wp_users fields
		
		if(!empty($user_directory_website) && !empty($userdata))
			$user_website =  $userdata->user_url;
		if(!empty($user_directory_email) && !empty($userdata))
			$user_email =  $userdata->user_email;	
		
		if(!empty($userdata))
			$username = $userdata->user_login; // For cimy plugin - may not be needed		
					
		/*** PREPARE THE DIRECTORY DATA ****************************************************************/
		
		if(!empty($address_flds[0])) $user_directory_addr_1 = $address_flds[0];	 
		if(!empty($address_flds[1])) $user_directory_addr_2 = $address_flds[1];
		if(!empty($address_flds[2])) $user_directory_city   = $address_flds[2];
		if(!empty($address_flds[3])) $user_directory_state  = $address_flds[3];
		if(!empty($address_flds[4])) $user_directory_zip    = $address_flds[4];
		
		if($user_directory_city && $user_directory_state && $user_directory_zip)
		    $user_directory_csz = $user_directory_city . ", " . $user_directory_state . " " . $user_directory_zip;	
		else if($user_directory_city && $user_directory_state)
		    $user_directory_csz = $user_directory_city . ", " . $user_directory_state;	
		else
		{        
		        if($user_directory_city)
		             $user_directory_csz .= $user_directory_city . " ";	
		        if($user_directory_state)
		             $user_directory_csz .= $user_directory_state . " ";	
		        if($user_directory_zip)
		             $user_directory_csz .= $user_directory_zip;	
		}
		    
		if($user_directory_sort === "last_name")	
		{
			$user_first_name = get_user_meta($user_id, 'first_name', true);	
      		$user_fullname = "<b>" . $user_first_name . " " . $user_last_name . "</b>";
      			
      		if($ud_format_name === "lf")
      		{
      			$user_fullname = "<b>" . $user_last_name . ", " . $user_first_name . "</b>";
      		}
      			
      		// For developers who want to change the full name
			$user_fullname = apply_filters('dud_set_user_full_name', $user_fullname, $user_first_name, $user_last_name);
      	}
      	else
      	{
      		$user_fullname = "<b>". $uid->display_name . "</b>";
      	} 
      	
		/*** if BuddyPress is installed and linking to BP Profile or WP Auth page ***/
		if( function_exists('bp_is_active') && $ud_author_page)
		{
			//if linking to BP profile page
			if($ud_auth_or_bp === "bp")
			{
				$user_fullname_tmp = "<a href=\"" . bp_core_get_user_domain( $user_id ) . "\"";
			}
			//if linking to WP Author Page
			else if( ($ud_author_posts > 0 || $ud_show_author_link === "always") )
			{
				$user_fullname_tmp = "<a href=\"" . get_author_posts_url( get_the_author_meta( 'ID', $user_id ), 
					get_the_author_meta( 'user_nicename', $user_id ) ) . "\"";	
			}
			
			if($ud_auth_or_bp === "bp" || ($ud_auth_or_bp === "auth" && ($ud_author_posts > 0 || $ud_show_author_link === "always")) )
			{
				if($ud_target_window === "separate") $user_fullname_tmp .= " target='_blank'";	
				$user_fullname_tmp .= ">" . $user_fullname . "</a>";	
				$user_fullname = $user_fullname_tmp;
			}
		}
		/*** If no BuddyPress and linking to WP author page ***/
		else if( $ud_author_page && ($ud_author_posts > 0 || $ud_show_author_link === "always") )
      	{ 
			$user_fullname_tmp = "<a href=\"" . get_author_posts_url( get_the_author_meta( 'ID', $user_id ), 
				get_the_author_meta( 'user_nicename', $user_id ) ) . "\"";
      		
      		if($ud_target_window === "separate") $user_fullname_tmp .= " target='_blank'";	
      		$user_fullname_tmp .= ">" . $user_fullname . "</a>";
      		$user_fullname = $user_fullname_tmp;
      	} 

		// For developers who need to configure the user profile page link
		$user_fullname = apply_filters('dud_set_user_profile_link', $user_fullname, $user_first_name, $user_last_name, $user_id);
					
		/*** PRINT THE DIRECTORY DATA *************************************************************************/	
			
		/* if showing a letter divider on a single page directory */	
		if( ($ud_directory_type === "all-users" || ( !empty($dud_options['ud_show_srch_results']) && $dud_options['ud_show_srch_results'] === 'single-page' && $dud_user_srch_name) ) && $ud_letter_divider !== "nld"  )	
		{
			/* if we're on a new alphabet letter */	
			if(strtoupper($printed_letter) !== strtoupper(substr($user_last_name, 0, 1)))
			{
				/* space between each listing */	
				if($user_directory_border === "surrounding_border")
					$user_contact_info .= "<DIV style=\"height:" . $user_directory_listing_sp . "px;\"></DIV>";	
		
				$printed_letter = substr($user_last_name, 0, 1);
				
				$user_contact_info .= "\n<DIV style=\"width:" . $user_directory_border_length 
					. "; background-color: " . $ud_letter_divider_fill_color 
						. ";\" class=\"printed-letter-div" . $letter_div_shadow;
				
				$user_contact_info .= "\"><DIV id=\"letter-divider-" . strtoupper($printed_letter) . "\" style=\"color:" . $ud_letter_divider_font_color 
					. ";\" class=\"printed-letter\">" . strtoupper($printed_letter) . "</DIV></DIV>";
					
				$letter_div_printed = true;
			}
			/* if showing a dividing border */
			else if($user_directory_border === "dividing_border" && $listing_cnt !== 0)
			{
				$user_contact_info .= "<DIV style=\"width:" . $user_directory_border_length 
					. ";border-style:" . $user_directory_border_style . ";border-width:" . $user_directory_border_thickness . ";border-color:" . 	
						 $user_directory_border_color . ";\" class=\"dir-listing-border-2\" ></DIV>\n";
			}
		}
		/* if showing a dividing border */
		else if($user_directory_border === "dividing_border" && $listing_cnt !== 0)
		{
			$user_contact_info .= "<DIV style=\"width:" . $user_directory_border_length 
				. ";border-style:" . $user_directory_border_style . ";border-width:" . $user_directory_border_thickness . ";border-color:" . 	
					 $user_directory_border_color . ";\" class=\"dir-listing-border-2\" ></DIV>\n";
		}
		
		/* space between each listing */			
		if(!$letter_div_printed && $listing_cnt !== 0)
			$user_contact_info .= "<DIV style=\"height:" . $user_directory_listing_sp . "px;\"></DIV>";	
		
		/* if showing a surrounding border */
		if($user_directory_border === "surrounding_border")
		{
			$user_contact_info .= "\n<DIV style=\"width:" . $user_directory_border_length . "; border-style:" 
				. $user_directory_border_style . ";border-width:" . $user_directory_border_thickness . ";border-color:" 
					. $user_directory_border_color . ";\" class=\"dir-listing-border\" >";
			
		}

		$user_contact_info .= "\n<DIV class=\"dir-listing\">\n";	
					
		/*** Print Avatar ***/		
	    if($user_directory_show_avatar)
	    {          	
	       	if($user_directory_avatar_style === "rounded-edges")
	       	{
	       	 	$atts = array('class' => 'avatar-rounded-edges');
	       	 	$img_style = "avatar-rounded-edges";
	       	}
        	else if($user_directory_avatar_style === "circle")
        	{
               	$atts = array('class' => 'avatar-circle');
              	$img_style  = "avatar-circle";
            }
            else
            {
              	$atts = array('class' => '');
               	$img_style  = "";
            }
                         	
            if($user_directory_cimy)
               	$user_avatar_url = dynamic_ud_get_cimy_avatar($user_id, $username, $atts, $img_style, $cimy_avatar_loc );
            else
           		$user_avatar_url = get_avatar( $user_id, '', '', '', $atts );
			
			/* Use this filter if your theme places the avatar somewhere other than the default path */
			$user_avatar_url = apply_filters('dud_set_avatar_url', $user_avatar_url, $user_id, $atts, $img_style);
            
			$user_avatar_url_path = $user_avatar_url;
			
			//If BuddyPress is installed and the avatar should be linked to the WP Author Page or BP Profile page
			if( function_exists('bp_is_active') && $ud_author_page)
			{
				//if linking to BP profile page
				if($ud_auth_or_bp === "bp")
				{	
					$user_avatar_url_tmp = "<a href=\"" . bp_core_get_user_domain( $user_id ) . "\"";
				}
				else if( $ud_author_posts > 0 || $ud_show_author_link === "always")
				{
					$user_avatar_url_tmp = "<a href=\"" . get_author_posts_url( get_the_author_meta( 'ID', $user_id ), 
      				get_the_author_meta( 'user_nicename', $user_id ) ) . "\"";
				}
				
				if($ud_auth_or_bp === "bp" || ($ud_author_posts > 0 || $ud_show_author_link === "always") )
				{					
					if($ud_target_window === "separate") $user_avatar_url_tmp .= " target='_blank'";	
					$user_avatar_url_tmp .= ">" . $user_avatar_url . "</a>";
					$user_avatar_url = $user_avatar_url_tmp;
				}
			}
			//If no BuddyPress
           	else if($ud_author_page && ($ud_author_posts > 0 || $ud_show_author_link === "always"))
      		{     				
      			$user_avatar_url_tmp = "<a href=\"" . get_author_posts_url( get_the_author_meta( 'ID', $user_id ), 
      				get_the_author_meta( 'user_nicename', $user_id ) ) . "\"";
      		
      			if($ud_target_window === "separate") $user_avatar_url_tmp .= " target='_blank'";
      			$user_avatar_url_tmp .= ">" . $user_avatar_url . "</a>";
      			$user_avatar_url = $user_avatar_url_tmp;
      		} 
			
			/* Use this filter if you need to manually build the avatar url link to a different profile/author page */
			$user_avatar_url = apply_filters('dud_set_avatar_link', $user_avatar_url, $user_avatar_url_path, $user_id);
								   	
			if($user_directory_avatar_style === "circle")
				$user_contact_info .= "\t<DIV class='user-avatar-circle'>". $user_avatar_url . "</DIV>\n\t";
			else
				$user_contact_info .= "\t<DIV class='user-avatar'>". $user_avatar_url . "</DIV>\n\t";
			
			if($user_directory_border === "surrounding_border")
				$user_contact_info .= "<DIV style='font-size:" . $user_directory_listing_fs . "px;' class='dir-listing-text-surr-border'>\n\t\t";
			else
				$user_contact_info .= "<DIV style='font-size:" . $user_directory_listing_fs . "px;' class='dir-listing-text'>\n\t\t";
		}
		else	
			$user_contact_info .= "\n\t<DIV style='font-size:" 
				. $user_directory_listing_fs . "px;' class='dir-listing-text-no-avatar'>\n\t\t";
			
		/*** Sort Field field is always displayed first ***/
		
		if($user_fullname !== '')
			$user_contact_info .= "\t" . $user_fullname . "<br>\n";
			 
		/*** Print remaining fields in the chosen display order ***/	 
		$line_cnt = 0;
	
		foreach ($sort_order_items as $item)
		{
			if($item === "Email")
			{
				if($user_directory_email && $user_email !== '') {
					$user_contact_info .= "\t\t\t<a href=\"mailto:" . $user_email . "\" target=\"_top\">" . $user_email . "</a><br>\n";	
					$line_cnt++;
				}
			}
			else if($item === "Website")
			{
				if($user_directory_website && $user_website !== '') {
					$user_contact_info .= "\t\t\t<a href=\"" . $user_website . "\">" . $user_website . "</a><br>\n";	
					$line_cnt++;
				}
			}
			else if($item === "Address")
			{
				if($user_directory_addr_1) { $user_contact_info .= "\t\t\t" . $user_directory_addr_1 . "<br>\n"; $line_cnt++; }
				if($user_directory_addr_2) { $user_contact_info .= "\t\t\t" . $user_directory_addr_2 . "<br>\n"; $line_cnt++; }
				if($user_directory_csz) { $user_contact_info .= "\t\t\t" .$user_directory_csz . "<br>\n"; $line_cnt++; }
			}
			else
			{
				foreach ( $user_directory_meta_flds_tmp as $ud_mflds )
				{
					if(!empty($ud_mflds['key']) && $item === $ud_mflds['key'])			
					{	
						if(!empty($ud_mflds['value']) && !empty($ud_mflds['label'])) 
							$user_contact_info .= "\t\t\t<b>" . $ud_mflds['label'] . " </b>\n";
							
						if(!empty($ud_mflds['value'])) {
							if($ud_mflds['link'] === '#')
								$user_contact_info .= "\t\t\t<a href=\"" . $ud_mflds['value'] . "\">" . $ud_mflds['value'] . "</a><br>\n";	
							else
								$user_contact_info .= "\t\t\t" . $ud_mflds['value'] . "<br>\n"; 
							$line_cnt++; 
							break;
						}	
					}
				}
			}
		}
				 	
		/*** Close the proper divs and print the dividing border if that is being used ***/					
		$user_contact_info .= "\t</DIV>\n</DIV>\n";	
										
		if($user_directory_border === "surrounding_border")
			$user_contact_info .= "</DIV>\n"; 	

		$listing_cnt++;
	  			
	} //END foreach ($uids as $uid)
	
	if($dud_user_srch_name && $listing_cnt < 1)
		$user_contact_info .= apply_filters('dud_search_err', "<H3>No users were found matching your search criteria.</H3>");		
	
	if($listing_cnt > 8 && !($ud_directory_type === "all-users"))
	{
		/*** Display the alphabet links at the top ***/
		if ( !in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $plugins ) 
				&& !in_array( 'dynamic-user-directory-alpha-links-scroll/dynamic-user-directory-alpha-links-scroll.php' , $plugins )) 
					$user_contact_info .=  "<BR>" . dynamic_ud_print_alpha_links($letters, $dud_options['ud_alpha_link_spacer'], $dud_options['user_directory_letter_fs']) . "<BR>";
		
	}
	
	return $user_contact_info;
}
else // No uids were found
{
	$user_contact_info              = "";
	$ud_srch_style                  = !empty($dud_options['ud_srch_style']) ? $dud_options['ud_srch_style'] : null;
	$user_directory_border_length   = !empty($dud_options['user_directory_border_length']) ? $dud_options['user_directory_border_length'] : null;
	
	if($dud_user_srch_name || $uids == 0) //checking for 0 allows the dud_after_load_uids filter to clear out the directory data but still  
	{                                     //show the meta fld search box, for those who only want to show the directory when a search is run
		if($uids != 0)
		{
			if(!($ud_directory_type === "all-users"))
			{
				/*** For the meta fld search add-on ***/
				if ( in_array( 'dynamic-user-directory-meta-flds-srch/dud_meta_flds_srch.php' , $plugins ) ) 
					$alpha_links = apply_filters('dud_meta_fld_srch_print_alpha_links', $letters, $dud_options['ud_alpha_link_spacer'], $dud_options['user_directory_letter_fs'], $dud_user_srch_key, $dud_user_srch_name) . "<BR>";
				else
					$alpha_links = dynamic_ud_print_alpha_links($letters, $dud_options['ud_alpha_link_spacer'], $dud_options['user_directory_letter_fs']) . "<BR>";
				
				$user_contact_info .= $alpha_links;
			}
			else
			{
				/* For Alpha Links Scroll add-on */
				$user_contact_info .= apply_filters( 'dud_print_scroll_letter_links', $user_contact_info, $letters, $loaded_options );
			}	
		}
						
		$user_srch_placeholder_txt = "Last Name";
	
		if($user_directory_sort !== "last_name")
			$user_srch_placeholder_txt = "Display Name";
	
		if($ud_srch_style === "transparent") $ud_srch_style = "background:none;";
		else $ud_srch_style = "";
	        	
        $user_directory_srch_fld = "<form id='dud_user_srch' method='post'>\n"; 
		$user_directory_srch_fld .= "    \t<DIV id='user-srch' style='width:" . $user_directory_border_length . ";'>\n";
       	$user_directory_srch_fld .= "          \t\t<input type='text' id='dud_user_srch_val' name='dud_user_srch_val' style='"  . $ud_srch_style . "'"; 
        $user_directory_srch_fld .= " value='' maxlength='50' placeholder='" . $user_srch_placeholder_txt . "'/>\n";
        $user_directory_srch_fld .= "        \t\t<button type='submit' id='dud_user_srch_submit' name='dud_user_srch_submit' value=''>\n";
        $user_directory_srch_fld .= "             \t\t\t<i class='fa fa-search fa-lg' aria-hidden='true'></i>\n";
        $user_directory_srch_fld .= "        \t\t</button>\n";
		$user_directory_srch_fld .= "     \t</DIV>\n";
        $user_directory_srch_fld .="</form><BR>\n"; 
		
		$user_directory_srch_fld = apply_filters('dud_build_srch_form', $user_directory_srch_fld, $loaded_options);
		
		if($srch_err)
			$user_contact_info .= $user_directory_srch_fld . $srch_err;
		else
			$user_contact_info .= $user_directory_srch_fld;
				
		return $user_contact_info;
		
	}
	else
		return apply_filters('dud_no_users_msg', "<H3>There are no users in the directory at this time.</H3>");	
}
	
}
add_shortcode( 'DynamicUserDirectory', 'DynamicUserDirectory' );

//** DUD UTILITY FUNCTIONS **************************************************************************************/

/*** Format meta fields that contain an array of items. If nested arrays are encountered, dump the contents  ***/
function dynamic_ud_parse_meta_val($user_meta_fld)
{
	$parsed_val = "";
	$numeric_idx = false;
	
	if(is_array($user_meta_fld))
	{
		foreach ($user_meta_fld as $key => $value) 
		{
			if (is_string($key))
			{	
				if(is_array($value)) //there are nested arrays
					$parsed_val .= "<BR>" . var_export($value, true);	
				else                 //add key-value pair to the meta fld var
					$parsed_val .= "<BR> " . $key . ": " . $value;
			} 
			else
			{
				$numeric_idx = true;
				break;
			}
		}
		
		if($numeric_idx)
		{
			for($met=0; $met < sizeof($user_meta_fld); $met++) 
			{
				if($user_meta_fld[$met])
				{
					if(is_array($user_meta_fld[$met])) //there are nested arrays
						$parsed_val .= var_export($user_meta_fld[$met], true);
					else                               //add the item to the meta fld var
						$parsed_val .= "<BR> " . $user_meta_fld[$met];
				}
			}
		}
		
		return $parsed_val;
	}
	
	return $user_meta_fld;	
}

function dynamic_ud_sort_order( $input ) {
       
     $output = "";
        
     if($input) 
         $output = explode(',', $input);
         
     else
     {
     	$output = "Address,Email,Website,MetaKey1,MetaKey2,MetaKey3,MetaKey4,MetaKey5,MetaKey6,MetaKey7,MetaKey8,MetaKey9,MetaKey10";
     	$output = explode(',', $output);
     }
     
     return $output;
}

/*** Loads an array with the alphabet letters for the existing users based on the filters selected on the settings page  ***/
function dynamic_ud_load_alpha_links($sort_fld, $ud_hide_roles, $exc_inc_radio, $inc_exc_user_ids, $dud_user_srch_name, $ud_directory_type)
{
	global $dynamic_ud_debug;
	global $wpdb;
	$roles_sql = "";
	$include_exclude_sql = "";
	
	if($ud_hide_roles && !($inc_exc_user_ids && $exc_inc_radio === 'include' )) //if including users, no need to build hide roles query
		$roles_sql = dynamic_ud_build_roles_query($sort_fld, $ud_hide_roles);
		
	if($inc_exc_user_ids)
		$include_exclude_sql = dynamic_ud_build_inc_exc_query($sort_fld, $ud_hide_roles, $exc_inc_radio, $inc_exc_user_ids);	
	
	
	if($sort_fld === "last_name")
	{
		$ud_sql = "Select COUNT(*) as cnt,SUBSTRING(meta_value,1,1) as letter FROM " . $wpdb->prefix . "usermeta where meta_key = 'last_name' ";
		
		if($ud_hide_roles && !($inc_exc_user_ids && $exc_inc_radio === 'include' )) $ud_sql .= " AND " . $roles_sql; 
		
		if($inc_exc_user_ids) $ud_sql .= " AND " . $include_exclude_sql;
			
		$ud_sql .= " GROUP BY SUBSTRING(meta_value,1,1)";
	}
	else
	{
		$ud_sql = "Select COUNT(*) as cnt, SUBSTRING(display_name,1,1) as letter FROM " . $wpdb->prefix . "users ";
		
		if( ($ud_hide_roles && !($inc_exc_user_ids && $exc_inc_radio === 'include' )) || $inc_exc_user_ids) $ud_sql .= " where ";
	
		if($ud_hide_roles && !($inc_exc_user_ids && $exc_inc_radio === 'include' )) $ud_sql .= $roles_sql;
		
		if($inc_exc_user_ids)
		{
			if($ud_hide_roles && !($inc_exc_user_ids && $exc_inc_radio === 'include' )) $ud_sql .= " AND ";
			
		 	$ud_sql .= $include_exclude_sql; 
		}
	
		$ud_sql .= " GROUP BY SUBSTRING(display_name,1,1)";
		
		if($dynamic_ud_debug) { echo "<PRE>Load Alpha Links SQL:<BR><BR>" . $ud_sql . "<BR><BR></PRE>"; }
	}
		
	$results = $wpdb->get_results($ud_sql);
	
	if($results)
	{	
		if($dynamic_ud_debug) { echo "<PRE>Existing Users Sorted By Letter<BR>"; }
		
		$letter_exists = array();
	
		foreach($results as $result) {
			if($dynamic_ud_debug) { echo strtoupper($result->letter) . "&nbsp;&nbsp;(" . $result->cnt . ")"; }
			
			/*** The code below only affects users who are using meta flds srch and alpha links scroll add-ons ***/
			
			//user search got results, hide unnecessary letters on single page dir
			if($ud_directory_type === "all-users" && !empty($dud_user_srch_name)) 
			{
				if( !(strtoupper($dud_user_srch_name[0]) === strtoupper($result->letter)) )
					continue;
			}
			/************************************************************************************************/	
			
			if(ctype_alpha($result->letter)) 
			{
				if($dynamic_ud_debug) echo " *";
				
				array_push($letter_exists, strtoupper($result->letter));
			}
			if($dynamic_ud_debug) echo "<BR>";
		}
		if($dynamic_ud_debug) echo "</PRE>";
			
		return $letter_exists;
	}

	return null; 
}

/***  Prints the letters of alphabet as links that will be used by the MembersListing function ***/
function dynamic_ud_print_alpha_links($ud_existing_letters, $ud_alpha_link_spacer, $user_directory_letter_fs)
{
	global $wp;
	global $dynamic_ud_debug;
			
	if(!$user_directory_letter_fs) $user_directory_letter_fs = "14px";
		
	if(!$ud_alpha_link_spacer) $ud_alpha_link_spacer = "8px";
	else $ud_alpha_link_spacer .= "px";
		
	//If there is no custom permalink structure
	if ( !get_option('permalink_structure') )
	{	
		//This accommodates certain intranet configurations
		$current_url = esc_url( home_url( '/' ) ) . basename(get_permalink());
		$url_param = "/?";
	}
	else
	{
		$current_url = esc_url( get_permalink()); 
		$url_param = "?";
	}
	
	if ((strpos($current_url, "?") !== false)) $url_param = "&";
	        
	$ud_letters_links = "\n<DIV class=\"alpha-links\" style=\"font-size:" . $user_directory_letter_fs . "px;\">\n";

	/*** alphabet array ***/
		
	$ud_alpha_string = "A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
	$ud_alpha_array = explode(',', $ud_alpha_string);
		
	if($dynamic_ud_debug) echo "<PRE>";	
	for( $inc = 0; $inc<26; $inc++ )
	{	
	    	$ud_letter = $ud_alpha_array[$inc];
	    	
	    	if($dynamic_ud_debug) echo "Checking letter " . $ud_letter;	
	    	
		if(in_array ( $ud_letter, $ud_existing_letters ))
		{	
			if($dynamic_ud_debug) echo " *";			
			if($ud_letter !== 'Z')
				$ud_letters_links .= "\t\t<a href=\"" . $current_url . $url_param . "letter=" . $ud_letter . "\"><b>" . $ud_letter . "</b></a><span style=\"padding-right:" 
					. $ud_alpha_link_spacer . ";\"></span>\n";
			else
				$ud_letters_links .= "\t\t<a style=\"font-weight: 400;\" href=\"" 
					. $current_url . $url_param . "letter=" . $ud_letter . "\"><b>" . $ud_letter . "</b></a>\n";
		}
		else
		{
			if($ud_letter !== 'Z')
				$ud_letters_links .= "\t\t<span style=\"color:gray;padding-right:" 
					. $ud_alpha_link_spacer . ";\">". $ud_letter . "</span>\n";
			else
				$ud_letters_links .= "\t\t<span style=\"color:gray\">". $ud_letter . "</span>\n";
		}
		if($dynamic_ud_debug) echo "<BR>";	
	}

	if($dynamic_ud_debug) echo "</PRE>";	
	$ud_letters_links .= "</DIV>\n\n";
	
	return $ud_letters_links;
}

/*** SQL Utilities ***/

function dynamic_ud_build_roles_query($sort_fld, $ud_hide_roles) {

	$roles_sql = "";
	$role_cnt = 1;
	$user_id_col_name = "ID";
	global $wpdb;
	global $wp_roles;

	if($sort_fld === "last_name") $user_id_col_name = "user_id"; 
		
	$roles_sql .= $user_id_col_name . " NOT IN (SELECT user_id FROM " . $wpdb->prefix . "usermeta WHERE ((
		meta_key = '" . $wpdb->prefix . "capabilities' AND (";
			
	$roles_arr_len = count($ud_hide_roles);
	
	$wproles = $wp_roles->get_names();
	
	foreach($ud_hide_roles as $role)
	{
		foreach($wproles as $key => $val)
			if(strtoupper($val) === strtoupper($role))
				$role = $key;
			
		$roles_sql .= " meta_value like '%" . $role . "%'";
		
		if($role_cnt < $roles_arr_len)
			$roles_sql .= " OR ";
		
		$role_cnt++;
	}	
	
	$roles_sql .= ")))) ";
	
	return $roles_sql;
}

function dynamic_ud_build_inc_exc_query($sort_fld, $ud_hide_roles, $exc_inc_radio, $inc_exc_user_ids) {

	$users_sql = "";
	$user_cnt = 1;
	$user_id_col_name = "ID";
	global $wpdb;
		
	if($sort_fld === "last_name") $user_id_col_name = "user_id";
	
	$users_arr_len = count($inc_exc_user_ids);
	
	if($exc_inc_radio === "include") {
		
		$users_sql .= "( ";	
		
		foreach($inc_exc_user_ids as $user_id)
		{
			$users_sql .= $user_id_col_name . " = " . $user_id;
		
			if($user_cnt < $users_arr_len)
				$users_sql .= " OR ";
		
			$user_cnt++;
		}	
		
		$users_sql .= " ) ";
	}
	else if($exc_inc_radio === "exclude") {
		
		$users_sql .= "(" . $user_id_col_name . " NOT IN (SELECT user_id FROM " . $wpdb->prefix . "usermeta WHERE ( ";	
		
		foreach($inc_exc_user_ids as $user_id)
		{
			$users_sql .= $user_id_col_name . " = " . $user_id;
		
			if($user_cnt < $users_arr_len)
				$users_sql .= " OR ";
		
			$user_cnt++;
		}	
		
		$users_sql .= " ))) ";
	}
	
	return $users_sql;
}

/*** Check Field Types ***/

function dud_chk_cimy_field($fld) {

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

function dud_chk_bp_field($fld) {

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

function dud_chk_s2m_field($fld, $fld_type) {

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
/*** String Utilities ***/

function dynamic_ud_before ($instr, $inthat)
{
        return substr($inthat, 0, strpos($inthat, $instr));
};

function dynamic_ud_after ($instr, $inthat)
{
        if (!is_bool(strpos($inthat, $instr)))
        return substr($inthat, strpos($inthat,$instr)+strlen($instr));
};
        
function dynamic_ud_after_last ($instr, $inthat)
{
        if (!is_bool(dynamic_ud_strrevpos($inthat, $instr)))
        	return substr($inthat, dynamic_ud_strrevpos($inthat, $instr)+strlen($instr));
}
    
function dynamic_ud_before_last ($instr, $inthat)
{
        return substr($inthat, 0, dynamic_ud_strrevpos($inthat, $instr));
}

function dynamic_ud_between_last ($instr, $that, $inthat)
{
        return dynamic_ud_after_last($instr, dynamic_ud_before_last($that, $inthat));
}    

function dynamic_ud_strrevpos($instr, $needle)
{
    $rev_pos = strpos (strrev($instr), strrev($needle));
    if ($rev_pos===false) return false;
    else return strlen($instr) - $rev_pos - strlen($needle);
};

function endswith($string, $test) {
    $strlen = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen) return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}

/*** Debug Utility ***/
function dynamic_ud_dump_settings($loaded_options)
{
	global $wpdb;
	
	if($loaded_options)
		$dud_options = get_option( $loaded_options );
	else
		$dud_options = get_option( 'dud_plugin_settings' );
	
	echo "<PRE>";
	
	if($loaded_options)
	{
		echo "Loaded option: " . $loaded_options . "<BR><BR>";
		
		echo "Instance name: " . $dud_options['dud_instance_name'] . "<BR><BR>";
	}
		    	
    echo "Users Table Name: " . $wpdb->prefix . "users " . "<BR><BR>";
    	
    echo "User Meta Table Name: " . $wpdb->prefix . "usermeta " . "<BR><BR>";
     
    echo "Directory Type: " . $dud_options['ud_directory_type'] . "<BR><BR>";
     	    	
	echo "Sort Field: " . $dud_options['user_directory_sort'] . "<BR>"; 
	
	$sort_order_items = dynamic_ud_sort_order( $dud_options['user_directory_sort_order'] );
	
	echo "<BR>Sort Order:<BR><BR>";
		foreach($sort_order_items as $sort_item) {
			echo "&nbsp;&nbsp;&nbsp;&nbsp;" . $sort_item. "<BR>";
		}
		
	echo "<BR>Include/Exclude: " . $dud_options['ud_exclude_include_radio'] . "<BR>"; 
	
	$ud_hide_roles_array = !empty($dud_options['ud_hide_roles']) ? $dud_options['ud_hide_roles'] : null;
	$ud_uids_array = !empty($dud_options['ud_users_exclude_include']) ? $dud_options['ud_users_exclude_include'] : null;
	
	if($ud_uids_array)
		echo "<BR>Size of Include/Exclude UIDs Array: " . sizeof($ud_uids_array) . "<BR>";
	else
		echo "<BR>UIDs Selected for Include/Exclude: none<BR>";
		
	
	if($ud_hide_roles_array)
	{
		echo "<BR>Roles selected for hiding:<BR><BR>";
		foreach($ud_hide_roles_array as $ud_role)
			echo "&nbsp;&nbsp;&nbsp;&nbsp;" . $ud_role . "<BR>";
	}
	else
		echo "<BR>Roles selected for hiding: none<BR>";
		
	echo "<BR>Show avatars: " . $dud_options['user_directory_show_avatars'] . "<BR>";     
    echo "<BR>Avatar Style: " . $dud_options['user_directory_avatar_style'] . "<BR>";     
	echo "<BR>Border: " . $dud_options['user_directory_border'] . "<BR>";
	echo "<BR>Border Len: " . $dud_options['user_directory_border_length'] . "<BR>";
	echo "<BR>Border Style: " . $dud_options['user_directory_border_style'] . "<BR>";
	echo "<BR>Border Color: " . $dud_options['user_directory_border_color'] . "<BR>";
	echo "<BR>Border Thickness: " . $dud_options['user_directory_border_thickness'] . "<BR>";
	echo "<BR>Directory Font Size: " . $dud_options['user_directory_listing_fs'] . "<BR>";
	echo "<BR>Directory Listing Spacing: " . $dud_options['user_directory_listing_spacing'] . "<BR>";
	echo "<BR>Link to Author Page: " . $dud_options['ud_author_page'] . "<BR>";
	echo "<BR>Author Page Target Window: " . $dud_options['ud_target_window'] . "<BR>";
	
	echo "</PRE>";
}

