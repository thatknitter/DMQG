<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

function mc_private_categories( $return = 'query' ) {
	$cats = '';
	if ( ! is_user_logged_in() ) {
		$categories = mc_get_private_categories();
		$cats = implode( ',', $categories );
		if ( $cats != '' ) {
			$cats = " AND category_id NOT IN ($cats)";
		}
	}

	return $cats;
}

/**
 * Fetch array of private categories.
 * 
 * @uses filter mc_private_categories
 *
 * @return array private categories
 */
function mc_get_private_categories() {
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$query   = "SELECT category_id FROM " . my_calendar_categories_table() . " WHERE category_private = 1";
	$results = $mcdb->get_results( $query );
	$categories = array();
	foreach ( $results as $result ) {
		$categories[] = $result->category_id;
	}
	
	return apply_filters( 'mc_private_categories', $categories );
}

// used to generate upcoming events lists
function mc_get_all_events( $category, $before, $after, $today, $author, $host, $ltype = '', $lvalue = '', $site = false ) {
	global $wpdb;
	$mcdb    = $wpdb;
	$events1 = $events2 = $events3 = array();
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$exclude_categories = mc_private_categories();
	$select_category    = ( $category != 'default' ) ? mc_select_category( $category ) : '';
	$limit_string       = mc_limit_string( '', $ltype, $lvalue );
	$select_author      = ( $author != 'default' ) ? mc_select_author( $author ) : '';
	$select_host        = ( $host != 'default' ) ? mc_select_host( $host ) : '';
	$date               = date( 'Y', current_time( 'timestamp' ) ) . '-' . date( 'm', current_time( 'timestamp' ) ) . '-' . date( 'd', current_time( 'timestamp' ) );
	// if a value is non-zero, I'll grab a handful of extra events so I can throw out holidays and others like that.
	if ( $before > 0 ) {
		$before  = $before + 15;
		$events1 = $mcdb->get_results( "SELECT *, UNIX_TIMESTAMP(occur_begin) AS ts_occur_begin, UNIX_TIMESTAMP(occur_end) AS ts_occur_end
		FROM " . my_calendar_event_table( $site ) . " 
		JOIN " . my_calendar_table( $site ) . " 
		ON (event_id=occur_event_id) 
		JOIN " . my_calendar_categories_table( $site ) . " 
		ON (event_category=category_id) 		
		WHERE $select_category $select_author $select_host $limit_string event_approved = 1 
		AND event_flagged <> 1 
		AND DATE(occur_begin) < '$date' 
		$exclude_categories 
		ORDER BY occur_begin DESC LIMIT 0,$before" );
	}
	if ( $today == 'yes' ) {
		$events3 = $mcdb->get_results( "SELECT *, UNIX_TIMESTAMP(occur_begin) AS ts_occur_begin, UNIX_TIMESTAMP(occur_end) AS ts_occur_end
		FROM " . my_calendar_event_table( $site ) . " 
		JOIN " . my_calendar_table( $site ) . " 
		ON (event_id=occur_event_id) 
		JOIN " . my_calendar_categories_table( $site ) . " 
		ON (event_category=category_id) 	
		WHERE $select_category $select_author $select_host $limit_string event_approved = 1 
		AND event_flagged <> 1 
		$exclude_categories 
		AND ( ( DATE(occur_begin) < '$date' AND DATE(occur_end) > '$date' ) OR DATE(occur_begin) = '$date' )" );    // event crosses or equals
	}
	if ( $after > 0 ) {
		$after   = $after + 15;
		$events2 = $mcdb->get_results( "SELECT *, UNIX_TIMESTAMP(occur_begin) AS ts_occur_begin, UNIX_TIMESTAMP(occur_end) AS ts_occur_end
		FROM " . my_calendar_event_table( $site ) . " 
		JOIN " . my_calendar_table( $site ) . " 
		ON (event_id=occur_event_id) 
		JOIN " . my_calendar_categories_table( $site ) . " 
		ON (event_category=category_id) 		
		WHERE $select_category $select_author $select_host $limit_string event_approved = 1 
		AND event_flagged <> 1 
		$exclude_categories 		
		AND DATE(occur_begin) > '$date' ORDER BY occur_begin ASC LIMIT 0,$after" );
	}
	$arr_events = array();
	if ( ! empty( $events1 ) || ! empty( $events2 ) || ! empty( $events3 ) ) {
		$arr_events = array_merge( $events1, $events3, $events2 );
	}
	
	return $arr_events;
}

function mc_get_all_holidays( $before, $after, $today ) {
	if ( ! get_option( 'mc_skip_holidays_category' ) ) {
		return array();
	}
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$holiday = get_option( 'mc_skip_holidays_category' );
	$date    = date( 'Y', current_time( 'timestamp' ) ) . '-' . date( 'm', current_time( 'timestamp' ) ) . '-' . date( 'd', current_time( 'timestamp' ) );
	// if a value is non-zero, I'll grab a handful of extra events so I can throw out holidays and others like that.
	if ( $before > 0 ) {
		$before  = $before + 10;
		$events1 = $mcdb->get_results( "SELECT *
		FROM " . my_calendar_event_table() . " 
		JOIN " . my_calendar_table() . " 
		ON (event_id=occur_event_id) 
		JOIN " . my_calendar_categories_table() . " 
		ON (event_category=category_id) WHERE event_category = $holiday AND event_approved = 1 AND event_flagged <> 1 
		AND DATE(occur_begin) < '$date' ORDER BY occur_begin DESC LIMIT 0,$before" );
	} else {
		$events1 = array();
	}
	if ( $today == 'yes' ) {
		$events3 = $mcdb->get_results( "SELECT *
		FROM " . my_calendar_event_table() . " 
		JOIN " . my_calendar_table() . " 
		ON (event_id=occur_event_id) 
		JOIN " . my_calendar_categories_table() . " 
		ON (event_category=category_id) WHERE event_category = $holiday AND event_approved = 1 AND event_flagged <> 1 
		AND DATE(occur_begin) = '$date'" );
	} else {
		$events3 = array();
	}
	if ( $after > 0 ) {
		$after   = $after + 10;
		$events2 = $mcdb->get_results( "SELECT *
		FROM " . my_calendar_event_table() . " 
		JOIN " . my_calendar_table() . " 
		ON (event_id=occur_event_id) 
		JOIN " . my_calendar_categories_table() . " 
		ON (event_category=category_id) WHERE event_category = $holiday AND  event_approved = 1 AND event_flagged <> 1 
		AND DATE(occur_begin) > '$date' ORDER BY occur_begin ASC LIMIT 0,$after" );
	} else {
		$events2 = array();
	}
	$arr_events = array();
	if ( ! empty( $events1 ) || ! empty( $events2 ) || ! empty( $events3 ) ) {
		$arr_events = array_merge( $events1, $events3, $events2 );
	}

	return $arr_events;
}

function mc_get_rss_events( $cat_id = false ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	if ( $cat_id ) {
		$cat = "WHERE event_category = $cat_id AND event_approved = 1";
	} else {
		$cat = 'WHERE event_approved = 1';
	}
	$exclude_categories = mc_private_categories();
	$limit = apply_filters( 'mc_rss_feed_size', 30 );
	
	$events = $mcdb->get_results( 
		"SELECT *, UNIX_TIMESTAMP(occur_begin) AS ts_occur_begin, UNIX_TIMESTAMP(occur_end) AS ts_occur_end 
		FROM " . my_calendar_event_table() . " 
		JOIN " . my_calendar_table() . " ON (event_id=occur_event_id) 
		JOIN " . my_calendar_categories_table() . " ON (event_category=category_id) $cat 
		$exclude_categories
		ORDER BY event_added DESC LIMIT 0,$limit" );
	$groups = $output = array();
	foreach ( array_keys( $events ) as $key ) {
		$event =& $events[ $key ];
		if ( ! in_array( $event->occur_group_id, $groups ) ) {
			$output[ $event->event_begin ][] = $event;
		}
		if ( $event->event_span == 1 ) {
			$groups[] = $event->occur_group_id;
		}
	}
	
	return $output;
}

/**
 * get event basic info
 * 
 * @param integer $id Event ID in my_calendar db
 */
function mc_get_event_core( $id, $rebuild = false ) {
	if ( !is_numeric( $id ) ) {		
		return;
	}
	
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	// get event data
	$event = $mcdb->get_row( "SELECT * FROM " . my_calendar_table() . " JOIN " . my_calendar_categories_table() . " ON (event_category=category_id) WHERE event_id=$id" );
	// include first occurrence
	if ( $rebuild ) {
		return $event;
	}
	
	$occur = $mcdb->get_row( "SELECT *, UNIX_TIMESTAMP(occur_begin) AS ts_occur_begin, UNIX_TIMESTAMP(occur_end) AS ts_occur_end FROM " . my_calendar_event_table() . " WHERE occur_event_id = $id ORDER BY occur_id ASC LIMIT 1" );
	$event = (object) array_merge( (array) $event, (array) $occur );
	
	return $event;
}

// get first event instance for core
function mc_get_first_event( $id ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$event = $mcdb->get_row( "SELECT *, UNIX_TIMESTAMP(occur_begin) AS ts_occur_begin, UNIX_TIMESTAMP(occur_end) AS ts_occur_end FROM " . my_calendar_event_table() . " JOIN " . my_calendar_table() . " ON (event_id=occur_event_id) JOIN " . my_calendar_categories_table() . " ON (event_category=category_id) WHERE occur_event_id=$id" );

	return $event;
}

function mc_valid_id( $mc_id ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	
	$result = $mcdb->get_row( $wpdb->prepare( "SELECT * FROM " . my_calendar_event_table() . " WHERE occur_id = %d", $mc_id ) );
	
	if ( is_object( $result ) ) {
		return true;
	}
	
	return false;
}

// get nearest event instance to current date for core
function mc_get_nearest_event( $id ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$event = $mcdb->get_row( "SELECT *, UNIX_TIMESTAMP(occur_begin) AS ts_occur_begin, UNIX_TIMESTAMP(occur_end) AS ts_occur_end FROM " . my_calendar_event_table() . " JOIN " . my_calendar_table() . " ON (event_id=occur_event_id) JOIN " . my_calendar_categories_table() . " ON (event_category=category_id) WHERE occur_event_id=$id ORDER BY ABS( DATEDIFF( occur_begin, NOW() ) )" );

	return $event;
}

// get event instance (object or html)
function mc_get_event( $id, $type = 'object' ) {
	if ( !is_numeric( $id ) ) {
		return;
	}
// indicates whether you want a specific instance, or a general event
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$event = $mcdb->get_row( "SELECT *, UNIX_TIMESTAMP(occur_begin) AS ts_occur_begin, UNIX_TIMESTAMP(occur_end) AS ts_occur_end FROM " . my_calendar_event_table() . " JOIN " . my_calendar_table() . " ON (event_id=occur_event_id) JOIN " . my_calendar_categories_table() . " ON (event_category=category_id) WHERE occur_id=$id" );
	if ( $type == 'object' ) {
		return $event;
	} else {
		$date  = date( 'Y-m-d', strtotime( $event->occur_begin ) );
		$time  = date( 'H:i:s', strtotime( $event->occur_begin ) );
		$value = "<div id='mc_event'>" . my_calendar_draw_event( $event, 'single', $date, $time, 'single' ) . "</div>\n";

		return $value;
	}
}

// get a specific field with an event ID
function mc_get_data( $field, $id ) {
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$field  = esc_sql( $field );
	$sql    = $wpdb->prepare( "SELECT $field FROM " . my_calendar_table() . " WHERE event_id = %d", $id );
	$result = $mcdb->get_var( $sql );

	return $result;
}

// get all occurrences associated with an event.
function mc_get_occurrences( $id ) {
	global $wpdb;
	$id = (int) $id;
	if ( $id === 0 ) {
		return array();
	}

	$sql     = "SELECT * FROM " . my_calendar_event_table() . " WHERE occur_event_id=$id";
	$results = $wpdb->get_results( $sql );

	return $results;
}

// get all events related to an event ID (group IDs)
function mc_related_events( $id, $template = false, $return = false ) {
	global $wpdb;
	$id = (int) $id;
	if ( $id === 0 && $return === false ) {
		echo "<li>" . __( 'No related events', 'my-calendar' ) . "</li>";

		return;
	}
	if ( $id === 0 && $return ) {
		return array();
	}
	$output  = '';
	$sql     = "SELECT * FROM " . my_calendar_event_table() . " WHERE occur_group_id=$id";
	$results = $wpdb->get_results( $sql );

	if ( $return ) {
		return $results;
	}
	
	if ( is_array( $results ) && ! empty( $results ) ) {
		foreach ( $results as $result ) {
			$event    = $result->occur_event_id;
			$current  = "<a href='" . admin_url( 'admin.php?page=my-calendar' ) . "&amp;mode=edit&amp;event_id=$event'>";
			$end      = "</a>";
			$begin    = date_i18n( get_option( 'mc_date_format' ), strtotime( $result->occur_begin ) ) . ', ' . date( get_option( 'mc_time_format' ), strtotime( $result->occur_begin ) );
			$template = $current . $begin . $end;			
			$output .= "<li>$template</li>";
		}
	} else {
		$output = "<li>" . __( 'No related events', 'my-calendar' ) . "</li>";
	}
	if ( $return == 'template' ) {
		return $output;
	} else { 
		echo $output;
	}
}


function mc_holiday_limit( $events, $holidays ) {
	foreach ( array_keys( $events ) as $key ) {
		if ( ! empty( $holidays[ $key ] ) ) {
			foreach ( $events[ $key ] as $k => $event ) {
				if ( $event->event_category != get_option( 'mc_skip_holidays_category' ) && $event->event_holiday == 1 ) {
					unset( $events[ $key ][ $k ] );
				}
			}
		}
	}

	return $events;
}

// Used to draw multiple events
function mc_set_date_array( $events ) {
	$event_array = array();
	if ( is_array( $events ) ) {
		foreach ( $events as $event ) {
			$date = date( 'Y-m-d', strtotime( $event->occur_begin ) );
			$end  = date( 'Y-m-d', strtotime( $event->occur_end ) );
			if ( $date != $end ) {
				$start = strtotime( $date );
				$end   = strtotime( $end );
				do {
					$date                   = date( 'Y-m-d', $start );
					$event_array[ $date ][] = $event;
					$start                  = strtotime( "+1 day", $start );
				} while ( $start <= $end );
			} else {
				$event_array[ $date ][] = $event;
			}
		}
	}

	return $event_array;
}

// get all events related to an event ID (group IDs)
function mc_list_related( $id, $this_id, $template = '{date}, {time}' ) {	
	global $wpdb;
	$id = (int) $id;
	if ( $id === 0 ) {
		return '';
	}
		
	$output  = '';
	$classes = '';
	$sql     = "SELECT event_id FROM " . my_calendar_table() . " WHERE event_group_id=$id";
	$results = $wpdb->get_results( $sql );
	
	$count = count( $results );
	// If a large number of events, skip this; 
	if ( $count > apply_filters( 'mc_related_event_limit', 50 ) ) {
		// filter to return an subset of related events.
		return apply_filters( 'mc_related_events', '', $results );
	}
	
	if ( is_array( $results ) && ! empty( $results ) ) {
		foreach ( $results as $result ) {
			$event_id = $result->event_id;
			$event    = mc_get_event_core( $event_id );
			$array    = mc_create_tags( $event, 'related' );
			if ( mc_key_exists( $template ) ) {
				$template = mc_get_custom_template( $template );
			}
			$html     = jd_draw_template( $array, $template );
			$classes  = mc_event_classes( $event, '', 'related' );
			$classes .= ( $event_id == $this_id ) ? ' current-event' : '';
			$output .= "<li class='$classes'>$html</li>";
		}
	} else {
		$output = "<li>" . __( 'No related events', 'my-calendar' ) . "</li>";
	}

	return $output;
}

/* 
* Main My Calendar event fetch
* @since 2.3.0
* 
* Fetch all events according to date parameters and supported limits.
*
* @param string $from Date formatted string 2014-2-10 
* @param string $to Date formatted string 2014-2-17
* @param string/int $category Category ID or category name.
* @param string/int $ltype Location filter type. 
* @param string $lvalue Location data to filter to.
* @param string $source Source of data request.
* @param int $author Author ID to filter to.
* @return array Array of events with dates as keys.
*/
function my_calendar_events( $from, $to, $category, $ltype, $lvalue, $source, $author, $host, $search = '', $site = 'global' ) {
	$events = my_calendar_grab_events( $from, $to, $category, $ltype, $lvalue, $source, $author, $host, null, $search, $site );
		
	if ( ! get_option( 'mc_skip_holidays_category' ) || get_option( 'mc_skip_holidays_category' ) == '' ) {
		$holidays = array();
	} else {
		$holidays      = my_calendar_grab_events( $from, $to, get_option( 'mc_skip_holidays_category' ), $ltype, $lvalue, $source, $author, $host, 'holidays', '', $site );
		$holiday_array = mc_set_date_array( $holidays );
	}
	// get events into an easily parseable set, keyed by date.
	if ( is_array( $events ) && ! empty( $events ) ) {
		$event_array = mc_set_date_array( $events );
		if ( is_array( $holidays ) && count( $holidays ) > 0 ) {
			$event_array = mc_holiday_limit( $event_array, $holiday_array ); // if there are holidays, rejigger.
		}
	} else {
		$event_array = array();
	}

	return $event_array;
}

function my_calendar_events_now( $category = 'default', $template = '<strong>{link_title}</strong> {timerange}', $site='' ) {
	
	if ( $site ) {
		$site = ( $site == 'global' ) ? BLOG_ID_CURRENT_SITE : $site;
		switch_to_blog( $site );
	}	
		
	global $wpdb;
	$mcdb = $wpdb;
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	$arr_events = array();
	$limit_string = "event_flagged <> 1 AND event_approved = 1";
	$select_category = ( $category != 'default' ) ? mc_select_category( $category ) : '';
	$exclude_categories = mc_private_categories();
	
	// may add support for location/author/host later.
	$select_location = $select_author = $select_host = '';
	$now = date( 'Y-m-d H:i:s', current_time( 'timestamp' ) );
	$event_query = "SELECT *, UNIX_TIMESTAMP(occur_begin) AS ts_occur_begin, UNIX_TIMESTAMP(occur_end) AS ts_occur_end
					FROM " . my_calendar_event_table( $site ) . " AS e 
					JOIN " . my_calendar_table( $site ) . " AS t 
					ON (event_id=occur_event_id) 					
					JOIN " . my_calendar_categories_table( $site ) . " AS c 
					ON (event_category=category_id) 
					WHERE $select_category $select_location $select_author $select_host $limit_string  
					$exclude_categories
					AND ( CAST('$now' AS DATETIME) BETWEEN occur_begin AND occur_end ) 
						ORDER BY " . apply_filters( 'mc_primary_sort', 'occur_begin' ) . ", " . apply_filters( 'mc_secondary_sort', 'event_title ASC' );
	$events      = $mcdb->get_results( $event_query );
	if ( ! empty( $events ) ) {
		foreach ( array_keys( $events ) as $key ) {
			$event        =& $events[ $key ];
			$arr_events[] = $event;
		}
	}
	if ( !empty( $arr_events ) ) {
		$event = mc_create_tags( $arr_events[0] );

		if ( mc_key_exists( $template ) ) {
			$template = mc_get_custom_template( $template );
		}
		
		$output = jd_draw_template( $event, apply_filters( 'mc_happening_now_template', $template, $event ) );
		$return = ( get_option( 'mc_process_shortcodes' ) == 'true' ) ? do_shortcode( $output ) : $output;
	} else {
		$return = '';
	}
		
	if ( $site ) {
		restore_current_blog();
	}	
	
	return $return;
}

/**
 * Get post associated with a given My Calendar event
 *
 * @param int $event_id
 *
 * @return mixed int/boolean post ID if found; else false
 */
function mc_get_event_post( $event_id ) {
	$event = mc_get_event_core( $event_id );
	if ( is_object( $event ) ) {
		if ( property_exists( $event, 'event_post' ) && get_post_status( $event->event_post ) ) {
			return $event->event_post;
		}
	}
	
	return false;
}

// Grab all events for the requested date from calendar
function my_calendar_grab_events( $from, $to, $category = null, $ltype = '', $lvalue = '', $source = 'calendar', $author = null, $host = null, $holidays = null, $search = '', $site = false ) {
	global $wpdb;
	$mcdb = $wpdb;
	
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}
	if ( $holidays === null ) {
		if ( isset( $_GET['mcat'] ) ) {
			$ccategory = $_GET['mcat'];
		} else {
			$ccategory = $category;
		}
	} else {
		$ccategory = $category;
	}
	if ( isset( $_GET['ltype'] ) ) {
		$cltype = $_GET['ltype'];
	} else {
		$cltype = $ltype;
	}
	if ( isset( $_GET['loc'] ) ) {
		$clvalue = $_GET['loc'];
	} else {
		$clvalue = $lvalue;
	}
	if ( isset( $_GET['mc_auth'] ) ) {
		$clauth = $_GET['mc_auth'];
	} else {
		$clauth = $author;
	}
	if ( isset( $_GET['mc_host'] ) ) {
		$clhost = $_GET['mc_host'];
	} else {
		$clhost = $host;
	}

	if ( $ccategory == '' ) {
		$ccategory = 'all';
	}
	if ( $clvalue == '' ) {
		$clvalue = 'all';
	}
	if ( $cltype == '' ) {
		$cltype = 'all';
	}
	if ( $clvalue == 'all' ) {
		$cltype = 'all';
	}
	if ( $clauth == '' ) {
		$clauth = 'all';
	}
	if ( $clhost == '' ) {
		$clhost = 'all';
	}

	if ( ! mc_checkdate( $from ) || ! mc_checkdate( $to ) ) {
		return array();
	} // not valid dates
		
	$caching = apply_filters( 'mc_caching_enabled', false, $ccategory, $ltype, $lvalue, $author, $host );
	$hash    = md5( $from . $to . $ccategory . $cltype . $clvalue . $clauth . $clhost );
	if ( $source != 'upcoming' ) { // no caching on upcoming events by days widgets or lists
		if ( $caching ) {
			$output = mc_check_cache( $ccategory, $cltype, $clvalue, $clauth, $clhost, $hash );
			if ( $output && $output != 'empty' ) {
				return $output;
			}
			if ( $output == 'empty' ) {
				return array();
			}
		}
	}

	$select_category    = ( $ccategory != 'all' ) ? mc_select_category( $ccategory ) : '';
	$select_author      = ( $clauth != 'all' ) ? mc_select_author( $clauth ) : '';
	$select_host        = ( $clhost != 'all' ) ? mc_select_host( $clhost ) : '';
	$select_location    = mc_limit_string( 'grab', $cltype, $clvalue );
	$exclude_categories = mc_private_categories();
	
	
	if ( $caching && $source != 'upcoming' ) {
		$select_category = '';
		$select_location = '';
		$select_author   = '';
		$select_host     = '';
	}
	// if caching, then need all categories/locations in cache. UNLESS this is an upcoming events list

	$arr_events   = array();
	$limit_string = "event_flagged <> 1 AND event_approved = 1";
	$search = mc_prepare_search_query( $search );

	$event_query = "SELECT *, UNIX_TIMESTAMP(occur_begin) AS ts_occur_begin, UNIX_TIMESTAMP(occur_end) AS ts_occur_end
					FROM " . my_calendar_event_table( $site ) . " 
					JOIN " . my_calendar_table( $site ) . "
					ON (event_id=occur_event_id) 					
					JOIN " . my_calendar_categories_table( $site ) . " 
					ON (event_category=category_id) 
					WHERE $select_category $select_location $select_author $select_host $limit_string $search 
					AND ( DATE(occur_begin) BETWEEN '$from 00:00:00' AND '$to 23:59:59' 
						OR DATE(occur_end) BETWEEN '$from 00:00:00' AND '$to 23:59:59' 
						OR ( DATE('$from') BETWEEN DATE(occur_begin) AND DATE(occur_end) ) 
						OR ( DATE('$to') BETWEEN DATE(occur_begin) AND DATE(occur_end) ) ) 
					$exclude_categories
					ORDER BY " . apply_filters( 'mc_primary_sort', 'occur_begin' ) . ", " . apply_filters( 'mc_secondary_sort', 'event_title ASC' );
	$events      = $mcdb->get_results( $event_query );
			
	if ( ! empty( $events ) ) {
		foreach ( array_keys( $events ) as $key ) {
			$event          =& $events[ $key ];
			$event->site_id = $site;
			$arr_events[]   = $event;
		}
	}
	
	if ( $source != 'upcoming' && $caching ) {
		$new_cache = mc_create_cache( $arr_events, $hash, $category, $ltype, $lvalue, $author, $host );
		if ( $new_cache ) {
			$output = mc_check_cache( $ccategory, $cltype, $clvalue, $clauth, $clhost, $hash );

			return $output;
		} else {
			// need to clean cache if the cache is maxed.
			return mc_clean_cache( $arr_events, $ccategory, $cltype, $clvalue, $clauth, $clhost );
		}
	} else {
		return $arr_events;
	}
}

// My Calendar does not currently have a draft status
function mc_event_published( $event ) {
	if ( $event->event_approved == 1 ) {
		return true;
	}
	
	return false;
}

function mc_get_db_type() {
	global $wpdb;
	$mcdb = $wpdb;
	$db_type = 'MyISAM';
	if ( get_option( 'mc_remote' ) == 'true' && function_exists( 'mc_remote_db' ) ) {
		$mcdb = mc_remote_db();
	}	
	$my_calendar = my_calendar_table();
	$dbs = $mcdb->get_results( "SHOW TABLE STATUS WHERE name='$my_calendar'" );
	foreach( $dbs as $db ) {
		if ( $db->Name == my_calendar_table() ) {
			$db_type = $db->Engine;
		}
	}

	return $db_type;
}

function mc_check_cache( $category, $ltype, $lvalue, $author, $host, $hash ) {
	$caching = apply_filters( 'mc_caching_enabled', false, $category, $ltype, $lvalue, $author, $host );
	if ( $caching == true ) {
		$cache = mc_get_cache( "mc_cache" );
		if ( isset( $cache[ $hash ] ) ) {
			$value = $cache[ $hash ];
		} else {
			return false;
		}
		if ( $value ) {
			return mc_clean_cache( $value, $category, $ltype, $lvalue, $author, $host );
		} else {
			return false;
		}
	} else {
		return false;
	}
}

function mc_clean_cache( $cache, $category, $ltype, $lvalue, $auth, $host ) {
	// process cache to strip events which do not meet current restrictions
	if ( $cache == 'empty' ) {
		return false;
	}
	$type   = ( $ltype != 'all' ) ? "event_$ltype" : "event_state";
	$return = false;
	if ( is_array( $cache ) ) {
		$cats = array( $category );
		if ( strpos( $category, ',' ) !== false ) {
			$cats = explode( ',', $category );
		} else if ( strpos( $category, '|' ) !== false ) {
			$cats = explode( '|', $category );
		}
		$authors = array( $auth );
		if ( strpos( $auth, ',' ) !== false ) {
			$authors = explode( ',', $auth );
		} else if ( strpos( $auth, '|' ) !== false ) {
			$authors = explode( '|', $auth );
		}
		$hosts = array( $host );
		if ( strpos( $host, ',' ) !== false ) {
			$authors = explode( ',', $host );
		} else if ( strpos( $host, '|' ) !== false ) {
			$authors = explode( '|', $host );
		}
		foreach ( $authors as $k => $v ) {
			if ( ! is_numeric( $v ) && $v != 'all' ) {
				$u             = get_user_by( 'login', $v );
				$id            = $u->ID;
				$authors[ $k ] = $id;
			}
		}
		foreach ( $hosts as $k => $v ) {
			if ( ! is_numeric( $v ) && $v != 'all' ) {
				$u           = get_user_by( 'login', $v );
				$id          = $u->ID;
				$hosts[ $k ] = $id;
			}
		}
		foreach ( $cache as $k => $v ) {
			foreach ( $cats as $cat ) {
				if ( is_numeric( $cat ) ) {
					$cat = (int) $cat;
				}
				if ( ( $v->event_category == $cat || $category == 'all' || $v->category_name == $cat )
				     && ( $v->event_author == $auth || $auth == 'all' || in_array( $v->event_author, $authors ) )
				     && ( $v->event_host == $host || $host == 'all' || in_array( $v->event_host, $hosts ) )
				     && ( $v->{$type} == urldecode( $lvalue ) || ( $ltype == 'all' && $lvalue == 'all' ) )
				) {
					$return[ $k ] = $v;
				}
			}
		}
	}

	return $return;
}

function mc_create_cache( $arr_events, $hash, $category, $ltype, $lvalue, $author, $host ) {
	$caching = apply_filters( 'mc_caching_enabled', false, $category, $ltype, $lvalue, $author, $host );
	if ( $arr_events == false ) {
		$arr_events = 'empty';
	}
	if ( $caching == true ) {
		$before = memory_get_usage();
		mc_get_cache( "mc_cache" );
		$after     = memory_get_usage();
		$mem_limit = mc_allocated_memory( $before, $after );
		if ( $mem_limit ) {
			return false;
		} // if cache is maxed, don't add additional references. Cache expires every 12 hours.
		$cache          = mc_get_cache( "mc_cache" );
		$cache[ $hash ] = $arr_events;
		mc_set_cache( "mc_cache", $cache, 60 * 60 * 12 );

		return true;
	}

	return false;
}

function mc_allocated_memory( $before, $after ) {
	$size             = ( $after - $before );
	$total_allocation = str_replace( 'M', '', ini_get( 'memory_limit' ) ) * 1048576; // CONVERT TO BYTES
	$limit            = $total_allocation / 64;
	// limits each cache to occupying 1/64 of allowed PHP memory (usually will be between 125K and 1MB).
	if ( $size > $limit ) {
		return true;
	} else {
		return false;
	}
}

function mc_delete_cache() {
	mc_remove_cache( 'mc_cache' );
	delete_transient( 'mc_todays_cache' );
	delete_transient( 'mc_cache_upcoming' );
}

function mc_get_cache( $cache ) {
	return get_transient( $cache );
}

function mc_set_cache( $cache, $time ) {
	set_transient( 'mc_cache', $cache, $time );
}

function mc_remove_cache( $cache ) {
	delete_transient( $cache );
}