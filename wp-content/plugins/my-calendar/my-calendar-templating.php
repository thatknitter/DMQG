<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

// Display the admin configuration page
function edit_mc_templates() {
	$templates = get_option( 'mc_templates' );

	if ( ! empty( $_POST ) ) {
		$nonce = $_REQUEST['_wpnonce'];
		if ( ! wp_verify_nonce( $nonce, 'my-calendar-nonce' ) ) {
			die( "Security check failed" );
		}
	}
	
	if ( isset( $_POST['mc_template_key'] ) ) {
		$key = $_POST['mc_template_key'];
	} else {
		$key = ( isset( $_GET['mc_template'] ) ) ? $_GET['mc_template'] : 'grid';
	}
		
	if ( isset( $_POST['delete'] ) ) {
		delete_option( 'mc_ctemplate_' . $key );
		echo "<div class=\"updated\"><p>" . __( 'Custom template deleted', 'my-calendar' ) . "</p></div>";
		$key = 'grid';
	} else {
		if ( mc_is_core_template( $key ) && isset( $_POST['add-new'] ) ) {
			echo "<div class=\"updated\"><p>" . __( 'Custom templates cannot have the same key as a core template', 'my-calendar' ) . "</p></div>";
		} else {	
			if ( mc_is_core_template( $key ) && isset( $_POST['mc_template'] ) ) {
				$template  = $_POST['mc_template'];
				$templates[$key] = $template;
				update_option( 'mc_templates', $templates );
				update_option( 'mc_use_' . $key . '_template', ( empty( $_POST['mc_use_template'] ) ? 0 : 1 ) );
				echo "<div class=\"updated\"><p>" . sprintf( __( '%s Template saved', 'my-calendar' ), ucfirst( $key ) ) . "</p></div>";
			} else if ( isset( $_POST['mc_template'] ) ) {
				$template  = $_POST['mc_template'];
				if ( mc_key_exists( $key ) ) {
					$key = mc_update_template( $key, $template );
				} else {
					$key = mc_create_template( $template );
				}
				echo "<div class='updated'><p>" . __( 'Custom Template saved', 'my-calendar' ) . "</p></div>";
			}
		}
	}
	// TODO: create UI for managing additional templates
	// TODO: create admin system for modifying shortcodes
	
	global $grid_template, $list_template, $mini_template, $single_template, $rss_template;
	$mc_grid_template    = ( $templates['grid'] != '' ) ? $templates['grid'] : $grid_template;
	$mc_rss_template     = ( $templates['rss'] != '' ) ? $templates['rss'] : $rss_template;
	$mc_list_template    = ( $templates['list'] != '' ) ? $templates['list'] : $list_template;
	$mc_mini_template    = ( $templates['mini'] != '' ) ? $templates['mini'] : $mini_template;
	$mc_details_template = ( $templates['details'] != '' ) ? $templates['details'] : $single_template;
	
	$template = ( mc_is_core_template( $key ) ) ? ${"mc_" . $key . "_template"} : mc_get_custom_template( $key );
	$template = stripslashes( $template );
	$core = mc_template_description( $key );
	?>
	<div class="wrap my-calendar-admin">
		<?php my_calendar_check_db(); ?>
		<h1 class="wp-heading-inline"><?php _e( 'My Calendar Templates', 'my-calendar' ); ?></h1>
		<a href="<?php echo add_query_arg( 'mc_template', 'add-new', admin_url( "admin.php?page=my-calendar-templates" ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'my-calendar' ); ?></a> 
		<hr class="wp-header-end">
		<div class="postbox-container jcd-wide">
			<div class="metabox-holder">
				<div class="ui-sortable meta-box-sortables">
					<div class="postbox">
						<h2><?php _e( 'Edit Template', 'my-calendar' ); ?></h2>
						<div class="inside">
							<p>
								<a href="<?php echo admin_url( "admin.php?page=my-calendar-help#templates" ); ?>"><?php _e( "Templates Help", 'my-calendar' ); ?></a> &raquo;
							</p>
							<?php if ( $core != '' ) { echo "<p class='template-description'>$core</p>"; } ?>
							<?php 
							if ( $key == 'add-new' ) {
							?>
								<form method="post" action="<?php echo add_query_arg( 'mc_template', $key, admin_url( "admin.php?page=my-calendar-templates" ) ); ?>">
								<div>
									<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>"/>
								</div>
								<p>
									<label for="mc_template_key"><?php _e( 'Template Description (required)', 'my-calendar' ); ?></label><br />
									<input type="text" class="widefat" name="mc_template_key" id="mc_template_key" value="" required />
								</p>
								<p>
									<label for="mc_template"><?php _e( 'Custom Template', 'my-calendar' ); ?></label><br/>
									<textarea id="mc_template" name="mc_template" class="template-editor widefat" rows="32" cols="76"></textarea>
								</p>

								<p>
									<input type="submit" name="save" class="button-primary" value="<?php _e( 'Add Template', 'my-calendar' ); ?>" />
								</p>
							</form>								
							<?php
							} else {
							?>
							<form method="post" action="<?php echo add_query_arg( 'mc_template', $key, admin_url( "admin.php?page=my-calendar-templates" ) ); ?>">
								<div>
									<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>"/>
									<input type="hidden" name="mc_template_key" value="<?php esc_attr_e( $key ); ?>"/>
								</div>
								<?php if ( mc_is_core_template( $key ) ) { ?>
								<p>
									<input type="checkbox" id="mc_use_template" name="mc_use_template" value="1" <?php mc_is_checked( "mc_use_".$key."_template", 1 ); ?> /> <label for="mc_use_template"><?php _e( 'Use this template', 'my-calendar' ); ?></label>
								</p>
								<?php } ?>
								<p>
									<label for="mc_template"><?php _e( 'Custom Template', 'my-calendar' ); ?></label><br/>
									<textarea id="mc_template" name="mc_template" class="template-editor widefat" rows="32" cols="76"><?php echo $template; ?></textarea>
								</p>
								<p>
									<input type="submit" name="save" class="button-primary" value="<?php _e( 'Update Template', 'my-calendar' ); ?>" />
								<?php if ( ! mc_is_core_template( $key ) ) { ?>
									<input type="submit" name="delete" class="button-secondary" value=<?php _e( 'Delete Template', 'my-calendar' ); ?>" />
								<?php } ?>
								</p>
							</form>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="metabox-holder">
				<div class="ui-sortable meta-box-sortables">
					<div class="postbox">
						<h2><?php _e( 'Templates', 'my-calendar' ); ?></h2>

						<div class="inside">
							<?php mc_list_templates(); ?>
							<p>
								<a href="<?php echo add_query_arg( 'mc_template', 'add-new', admin_url( "admin.php?page=my-calendar-templates" ) ); ?>"><?php _e( 'Add New Template', 'my-calendar' ); ?></a>
							</p>							
						</div>
					</div>
				</div>
			</div>
			<div class="metabox-holder">
				<div class="ui-sortable meta-box-sortables">
					<div class="postbox">
						<h2 class='hndle'><?php _e( 'Event Template Tags', 'my-calendar' ); ?></h2>

						<div class='mc_template_tags inside'>
							<p>
								<a href="<?php echo admin_url( 'admin.php?page=my-calendar-help#templates' ); ?>"><?php _e( 'All Template Tags &raquo;', 'my-calendar' ); ?></a>
							</p>						
							<dl>
								<dt><code>{title}</code></dt>
								<dd><?php _e( 'Title of the event.', 'my-calendar' ); ?></dd>

								<dt><code>{link_title}</code></dt>
								<dd><?php _e( 'Title of the event as a link if a URL is present, or the title alone if not.', 'my-calendar' ); ?></dd>

								<dt><code>{time}</code></dt>
								<dd><?php _e( 'Start time for the event.', 'my-calendar' ); ?></dd>

								<dt><code>{date}</code></dt>
								<dd><?php _e( 'Date on which the event begins.', 'my-calendar' ); ?></dd>

								<dt><code>{daterange}</code></dt>
								<dd><?php _e( 'Beginning date to end date; excludes end date if same as beginning.', 'my-calendar' ); ?></dd>

								<dt><code>{multidate}</code></dt>
								<dd><?php _e( 'Multi-day events: an unordered list of dates/times. Otherwise, beginning date/time.', 'my-calendar' ); ?></dd>

								<dt><code>{author}</code></dt>
								<dd><?php _e( 'Author who posted the event.', 'my-calendar' ); ?></dd>

								<dt><code>{host}</code></dt>
								<dd><?php _e( 'Name of the assigned host for the event.', 'my-calendar' ); ?></dd>

								<dt><code>{shortdesc}</code></dt>
								<dd><?php _e( 'Short event description.', 'my-calendar' ); ?></dd>

								<dt><code>{description}</code></dt>
								<dd><?php _e( 'Description of the event.', 'my-calendar' ); ?></dd>

								<dt><code>{image}</code></dt>
								<dd><?php _e( 'Image associated with the event.', 'my-calendar' ); ?></dd>

								<dt><code>{link}</code></dt>
								<dd><?php _e( 'URL provided for the event.', 'my-calendar' ); ?></dd>

								<dt><code>{details}</code></dt>
								<dd><?php _e( 'Link to an auto-generated page containing information about the event.', 'my-calendar' ); ?>

								<dt><code>{event_open}</code></dt>
								<dd><?php _e( 'Whether event is currently open for registration.', 'my-calendar' ); ?></dd>

								<dt><code>{event_status}</code></dt>
								<dd><?php _e( 'Current status of event: either "Published" or "Reserved."', 'my-calendar' ); ?></dd>
							</dl>

							<h3><?php _e( 'Location Template Tags', 'my-calendar' ); ?></h3>
							<dl>
								<dt><code>{location}</code></dt>
								<dd><?php _e( 'Name of the location of the event.', 'my-calendar' ); ?></dd>

								<dt><code>{street}</code></dt>
								<dd><?php _e( 'First line of the site address.', 'my-calendar' ); ?></dd>

								<dt><code>{street2}</code></dt>
								<dd><?php _e( 'Second line of the site address.', 'my-calendar' ); ?></dd>

								<dt><code>{city}</code></dt>
								<dd><?php _e( 'City', 'my-calendar' ); ?></dd>

								<dt><code>{state}</code></dt>
								<dd><?php _e( 'State', 'my-calendar' ); ?></dd>

								<dt><code>{postcode}</code></dt>
								<dd><?php _e( 'Postal Code', 'my-calendar' ); ?></dd>

								<dt><code>{region}</code></dt>
								<dd><?php _e( 'Custom region.', 'my-calendar' ); ?></dd>

								<dt><code>{country}</code></dt>
								<dd><?php _e( 'Country for the event location.', 'my-calendar' ); ?></dd>

								<dt><code>{sitelink}</code></dt>
								<dd><?php _e( 'Output the URL for the location.', 'my-calendar' ); ?></dd>

								<dt><code>{hcard}</code></dt>
								<dd><?php _e( 'Event address in <a href="http://microformats.org/wiki/hcard">hcard</a> format.', 'my-calendar' ); ?></dd>

								<dt><code>{link_map}</code></dt>
								<dd><?php _e( 'Link to Google Map to the event, if address information is available.', 'my-calendar' ); ?></dd>
							</dl>
							<h3><?php _e( 'Category Template Tags', 'my-calendar' ); ?></h3>

							<dl>
								<dt><code>{category}</code></dt>
								<dd><?php _e( 'Name of the category of the event.', 'my-calendar' ); ?></dd>

								<dt><code>{icon}</code></dt>
								<dd><?php _e( 'URL for the event\'s category icon.', 'my-calendar' ); ?></dd>

								<dt><code>{color}</code></dt>
								<dd><?php _e( 'Hex code for the event\'s category color.', 'my-calendar' ); ?></dd>

								<dt><code>{cat_id}</code></dt>
								<dd><?php _e( 'ID of the category of the event.', 'my-calendar' ); ?></dd>
							</dl>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php mc_show_sidebar();
}

function mc_is_core_template( $key ) {
	switch( $key ) {
		case 'grid':
		case 'details':
		case 'list':
		case 'rss':
		case 'mini':
			return true;
			break;
		default:
			return false;
	}
}

function mc_get_custom_template( $key ) {
	$return = get_option( "mc_ctemplate_$key" );
	
	return $return;
}

function mc_key_exists( $key ) {
	if ( get_option( "mc_ctemplate_$key", 'missing' ) != 'missing' ) {
		return true;
	}
	
	return false;	
}

function mc_template_exists( $template ) {
	$key = md5( $template );
	if ( get_option( "mc_ctemplate_$key", 'missing' ) != 'missing' ) {
		return true;
	}
	
	return false;
}

function mc_create_template( $template ) {
	$key = md5( $template );
	$description = $_POST['mc_template_key'];
	update_option( "mc_template_desc_$key", $description );
	update_option( "mc_ctemplate_$key", $template );

	return $key;
}

function mc_update_template( $key, $template ) {
	update_option( "mc_ctemplate_$key", $template );
	
	return $key;
}

function mc_template_description( $key ) {
	$return = sprintf( __( 'Custom template, keyword %s', 'my-calendar' ), "<code>$key</code>" );
	$description = '';
	switch( $key ) {
		case 'grid':
			$return = __( '<strong>Core Template:</strong> used in the details pop-up in the main calendar view.', 'my-calendar' );
			break;
		case 'details':
			$return = __( '<strong>Core Template:</strong> used on the single event view.', 'my-calendar' );
			break;
		case 'list':
			$return = __( '<strong>Core Template:</strong> used when viewing events in the main calendar list view.', 'my-calendar' );
			break;
		case 'rss':
			$return = __( '<strong>Core Template:</strong> used for RSS feeds.', 'my-calendar' );
			break;
		case 'mini':
			$return = __( '<strong>Core Template:</strong> used in pop-ups for the mini calendar.', 'my-calendar' );
			break;
	}
	
	if ( ! mc_is_core_template( $key ) ) {
		$description = strip_tags( stripslashes( get_option( "mc_template_desc_$key" ) ) );
	}
	
	$br = ( $description != '' ) ? '<br />' : '';
	
	return $description . $br . $return;
}

function mc_list_templates() {
	$check = "<span class='dashicons dashicons-yes' aria-hidden='true'></span><span>" . __( 'Enabled', 'my-calendar' ) . "</span>";
	$uncheck = "<span class='dashicons dashicons-no' aria-hidden='true'></span><span>" . __( 'Not Enabled', 'my-calendar' ) . "</span>";
	$grid_enabled = ( get_option( 'mc_use_grid_template' ) == 1 ) ? $check : $uncheck;
	$list_enabled = ( get_option( 'mc_use_list_template' ) == 1 ) ? $check : $uncheck;
	$mini_enabled = ( get_option( 'mc_use_mini_template' ) == 1 ) ? $check : $uncheck;
	$details_enabled = ( get_option( 'mc_use_details_template' ) == 1 ) ? $check : $uncheck;
	$rss_enabled = ( get_option( 'mc_use_rss_template' ) == 1 ) ? $check : $uncheck;
	
	$list = "<table class='widefat'>
				<thead>
					<tr>
						<th scope='col'>Template Key</th>
						<th scope='col'>Description</th>
						<th scope='col'>Status</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><a href='" . add_query_arg( 'mc_template', 'grid', admin_url( "admin.php?page=my-calendar-templates" ) ) . "'>grid</a></td>
						<td>" . mc_template_description( 'grid' ) . "</td>
						<td>$grid_enabled</td>
					</tr>
					<tr>
						<td><a href='" . add_query_arg( 'mc_template', 'list', admin_url( "admin.php?page=my-calendar-templates" ) ) . "'>list</a></td>
						<td>" . mc_template_description( 'list' ) . "</td>
						<td>$list_enabled</td>						
					</tr>
					<tr>
						<td><a href='" . add_query_arg( 'mc_template', 'mini', admin_url( "admin.php?page=my-calendar-templates" ) ) . "'>mini</a></td>
						<td>" . mc_template_description( 'mini' ) . "</td>
						<td>$mini_enabled</td>						
					</tr>
					<tr>
						<td><a href='" . add_query_arg( 'mc_template', 'details', admin_url( "admin.php?page=my-calendar-templates" ) ) . "'>details</a></td>
						<td>" . mc_template_description( 'details' ) . "</td>
						<td>$details_enabled</td>						
					</tr>
					<tr>
						<td><a href='" . add_query_arg( 'mc_template', 'rss', admin_url( "admin.php?page=my-calendar-templates" ) ) . "'>rss</a></td>
						<td>" . mc_template_description( 'rss' ) . "</td>
						<td>$rss_enabled</td>						
					</tr>";
	global $wpdb;
	$select = "SELECT * FROM " . $wpdb->prefix . "options WHERE option_name LIKE '%mc_ctemplate_%'";
	$results = $wpdb->get_results( $select );
	foreach( $results as $result ) {
		$key = str_replace( 'mc_ctemplate_', '', $result->option_name );
		$desc = mc_template_description( $key );
		$list .= "<tr>
					<td><a href='" . add_query_arg( 'mc_template', $key, admin_url( "admin.php?page=my-calendar-templates" ) ) . "'>$key</a></td>
					<td>$desc</td>
					<td> -- </td>
				</tr>";
	}
					
	$list .= "</tbody>
	</table>";
					
	echo $list;
	
}