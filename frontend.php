<?php
// Hooks
add_action( 'wp_head', 'jumplead_tracking_code' );
add_action( 'wp_footer', 'jumplead_automation_trigger_code' );
add_action( 'wp_enqueue_scripts', 'jumplead_automation_trigger_code' );

// Create shortcode [jumplead_form]
add_shortcode( 'jumplead_form', 'jumplead_embed_form' );

/**
 * Echo out the Jumplead Tracking Code
 * Echos the tracking code or just a comment if tracker id is invalid
 *
 * @return void
 */
function jumplead_tracking_code() {
	$tracker_id = get_option( 'jumplead_tracker_id' );

	echo_jumplead_comment( 'Tracking Code' );

	if ( jumplead_is_tracker_id_valid() ) {
	    include(JUMPLEAD_PATH_VIEW . 'tracking-code.php');
	} else {
		echo_jumplead_comment( 'Tracker ID ' . $tracker_id . ' is invalid' );
	}
}

/**
 * Queue JavaScript to trigger an automation
 *
 * @return void
 */
function jumplead_automation_trigger_code() {
	wp_enqueue_script( 'jumplead.js', plugins_url( 'j/jumplead.js', __FILE__ ), array(), JUMPLEAD_VERSION, true );
}

/**
 * Jumplead Form Short Tag
 *
 * @return void
 */
function jumplead_embed_form($atts) {
	// Ensure ID is set
	if ( ! isset($atts['id']) || trim( $atts['id'] ) == '' ) {
	    return 'Jumplead Error: Invalid Form ID';
	}

	$dataStr = 'data-id="' . $atts['id'] . '"';

	// JavaScript callback
	if ( isset($atts['callback']) ) {
		$dataStr .= ' data-callback="' . $atts['callback'] . '"';
	}

	return '<div class="jlcf" ' . $dataStr . '></div>';
}
