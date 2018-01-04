<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

function mc_switch_sites() {
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		if ( get_site_option( 'mc_multisite' ) == 2 && my_calendar_table() != my_calendar_table( 'global' ) ) {
			if ( get_option( 'mc_current_table' ) == '1' ) {
				// can post to either, but is currently set to post to central table
				return true;
			}
		} else if ( get_site_option( 'mc_multisite' ) == 1 && my_calendar_table() != my_calendar_table( 'global' ) ) {
			// can only post to central table
			return true;
		}
	}

	return false;
}


function mc_tweet_approval( $prev, $new ) {
	if ( function_exists( 'jd_doTwitterAPIPost' ) && isset( $_POST['mc_twitter'] ) && trim( $_POST['mc_twitter'] ) != '' ) {
		if ( ( $prev == 0 || $prev == 2 ) && $new == 1 ) {
			jd_doTwitterAPIPost( stripslashes( $_POST['mc_twitter'] ) );
		}
	}
}


function mc_flatten_event_array( $events ) {
	$flat = array();
	foreach( $events as $event ) {
		foreach( $event as $e ) {
			$flat[] = $e;
		}
	}
	
	return $flat;
}


add_action( 'admin_menu', 'mc_add_outer_box' );

// begin add boxes
function mc_add_outer_box() {
	add_meta_box( 'mcs_add_event', __('My Calendar Event', 'my-calendar'), 'mc_add_inner_box', 'mc-events', 'side','high' );
}

function mc_add_inner_box() {
	global $post;
	$event_id = get_post_meta( $post->ID, '_mc_event_id', true );
	if ( $event_id ) {
		$url = admin_url( 'admin.php?page=my-calendar&mode=edit&event_id='.$event_id );
		$event = mc_get_event_core( $event_id );
		$content = "<p><strong>" . mc_kses_post( $event->event_title ) . '</strong><br />' . $event->event_begin . ' @ ' . $event->event_time . "</p>";
		if ( $event->event_label != '' ) {
			$content .= "<p>" . sprintf( __( '<strong>Location:</strong> %s', 'my-calendar' ), mc_kses_post( $event->event_label ) ) . "</p>";
		}
		$content .= "<p>" . sprintf( __( '<a href="%s">Edit event</a>.', 'my-calendar' ), $url ) . "</p>";
		
		echo $content;
	} 
}