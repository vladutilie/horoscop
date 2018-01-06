<?php
/**
 *	Functions file
 *	@package: Horoscop
 *	@since: 2.1.8
 *	@modified: 2.5.6
 */
defined( 'ABSPATH' ) || exit;

/**
 * Initiate Horoscope settings after activate plugin.
 *
 * @since 2.1.8
 * @modified: 3.9.3
 *
 * @see dbDelta function
 * @link https://developer.wordpress.org/reference/functions/dbdelta/
 *
 * @see wp_schedule_event function
 * @link https://developer.wordpress.org/reference/functions/wp_schedule_event/
 *
 * @global object $wpdb SQL methotds.
 */
function horoscop_init() {
	global $wpdb;
	$charset_collate = $wpdb->get_charset_collate();
	$query = "CREATE TABLE " . $wpdb->prefix . "horoscope_cache (
			ID tinyint(2) unsigned NOT NULL auto_increment,
			sign char(10) NOT NULL,
			content text NOT NULL,
			PRIMARY KEY ID (ID)
		) $charset_collate;";
	require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $query );
	wp_schedule_event( time(), '6hours', 'horoscop_cache' );
}

/**
 * 	Remove Horoscope settings after deactivate plugin.
 *
 * @since 2.1.8
 * @modified: 3.9.3
 *
 * @see delete_option function
 * @link https://developer.wordpress.org/reference/functions/delete_option/
 *
 * @see wp_clear_scheduled_hook function
 * @link https://developer.wordpress.org/reference/functions/wp_clear_scheduled_hook/
 *
 * @global object $wpdb SQL methotds.
 */
function horoscop_deactivation() {
	global $wpdb;
	$wpdb->query( 'DROP TABLE ' . $wpdb->prefix . 'horoscope_cache' );
	delete_option( 'widget_horoscop' );
	wp_clear_scheduled_hook( 'horoscop_cache' );
}

/**
 * All horoscope signs.
 *
 * Horoscope star signs with translations and periods.
 *
 * @since 3.9.3
 *
 * @return array Code, name and period of every star sign.
 */
function horoscop_star_signs() {
	$signs = array(
		0 => array(
			'code'	=>	'berbec',
			'name'	=>	__( 'Aries', 'horoscop' ),
			'period'=>	__( 'March 21st - April 20st', 'horoscop' ),
		),
		1 => array(
			'code'	=>	'taur',
			'name'	=>	__( 'Taurus', 'horoscop' ),
			'period'=>	__( 'April 21st - May 21st', 'horoscop' ),
		),
		2 => array(
			'code'	=>	'gemeni',
			'name'	=>	__( 'Gemini', 'horoscop' ),
			'period'=>	__( 'May 22st - June 21st', 'horoscop' ),
		),
		3 => array(
			'code'	=>	'rac',
			'name'	=>	__( 'Cancer', 'horoscop' ),
			'period'=>	__( 'June 22st - July 22st', 'horoscop' ),
		),
		4 => array(
			'code'	=>	'leu',
			'name'	=>	__( 'Leo', 'horoscop' ),
			'period'=>	__( 'July 23st - August 22st', 'horoscop' ),
		),
		5 => array(
			'code'	=>	'fecioara',
			'name'	=>	__( 'Virgo', 'horoscop' ),
			'period'=>	__( 'August 23st - September 22st', 'horoscop' ),
		),
		6 => array(
			'code'	=>	'balanta',
			'name'	=>	__( 'Libra', 'horoscop' ),
			'period'=>	__( 'September 23st - October 22st', 'horoscop' ),
		),
		7 => array(
			'code'	=>	'scorpion',
			'name'	=>	__( 'Scorpio', 'horoscop' ),
			'period'=>	__( 'October 23st - November 21st', 'horoscop' ),
		),
		8 => array(
			'code'	=>	'sagetator',
			'name'	=>	__( 'Sagittarius', 'horoscop' ),
			'period'=>	__( 'November 22st - December 21st', 'horoscop' ),
		),
		9 => array(
			'code'	=>	'capricorn',
			'name'	=>	__( 'Capricorn', 'horoscop' ),
			'period'=>	__( 'December 22st - January 19st', 'horoscop' ),
		),
		10 => array(
			'code'	=>	'varsator',
			'name'	=>	__( 'Aquarius', 'horoscop' ),
			'period'=>	__( 'January 20st - February 18st', 'horoscop' ),
		),
		11 => array(
			'code'	=>	'pesti',
			'name'	=>	__( 'Pisces', 'horoscop' ),
			'period'=>	__( 'February 19st - March 20st', 'horoscop' ),
		),
	);
	return $signs;
}

/**
 * Horoscope WP Cron job.
 *
 * Create a custom interval of 6 hours for horoscope update WP Cron job.
 *
 * @since 2.5.6
 * @modified: 3.9.3
 *
 * @see esc_html__ function
 * @link https://developer.wordpress.org/reference/functions/esc_html__/
 *
 * @param array $schedules Interval options for the customized schedule.
 * @return array Options set.
 */
function horoscop_cron_interval( $schedules ) {
	$schedules['6hours'] = array(
		'interval'	=>	21600, // == 6 hours
		'display'	=>	esc_html__( 'Make an update every six hours', 'horoscop' ),
    );
    return $schedules;
}

/**
 * Get content of start sign from www.acvaria.com.
 *
 * @since 2.1.8
 * @modified: 3.9.3
 *
 * @param string $sign Star sign of horoscope.
 * @return string The content of the star sign retrieved from the source.
 */
function horoscop_get_content( $sign ) {
	global $wp_version;
	$url = 'http://www.acvaria.com/partener-acvaria.php?z=' . $sign;
	$args = array(
		'timeout'     => 30, // seconds
		'user-agent'  => 'WordPress/' . $wp_version . '; ' . home_url(),
	);
	$response = wp_remote_get( $url, $args );
	if ( 200 != $response['response']['code'] ) {
		return 0;
	}
	$content = preg_replace( '/\s\s+/', '', $response['body'] );
	$content = str_replace( ' Horoscop oferit de www.acvaria.com', '', strip_tags( $content ) );
	return utf8_encode( $content );
}

/**
 * Horoscope cache.
 *
 * Make horoscop cache at an interval set in cron-job.
 *
 * @since 2.1.8
 * @modified: 3.9.3
 *
 * @see horoscop_star_signs function above
 *
 * @see get_var, prepare, insert, update methods
 * @link https://developer.wordpress.org/reference/classes/wpdb
 *
 * @global object $wpdb SQL methotds.
 */
function horoscop_cache() {
	global $wpdb;
	$signs = horoscop_star_signs();
	for ( $i = 0; $i < 12; $i++ ) {
		$code = $signs[ $i ]['code'];
		$count = $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'horoscope_cache WHERE sign=%s', $code ) );
		$data = array(
				'sign'		=>	$code,
				'content'	=>	horoscop_get_content( $code ),
			);
		$format = array( '%s', '%s' );
		if ( 0 == $count ) {	
			$wpdb->insert( $wpdb->prefix . 'horoscope_cache', $data, $format );
		} else {
			$where = array( 'sign' => $code );
			$where_format = array( '%s' );
			$wpdb->update( $wpdb->prefix . 'horoscope_cache', $data, $where, $format, $where_format );
		}
	}
}

/**
 * Horoscope register widget
 *
 * @since 3.9.3
 *
 * @see register_widget function
 * @link https://developer.wordpress.org/reference/functions/register_widget/
 */
function horoscop_register_widget() {
	register_widget( 'Horoscop' );
}

/**
 * Enqueue dashicons, jQuery and stylesheet.
 *
 * @since 2.5.6
 * @modified: 3.9.3
 *
 * @see wp_enqueue_script function
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script
 *
 * @see wp_enqueue_style function
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 *
 * @see plugin_dir_url function
 * @link https://developer.wordpress.org/reference/functions/plugin_dir_url
 */
function horoscop_enqueue() {
	wp_enqueue_script( 'horoscop-jquery', plugin_dir_url( __FILE__ ) . 'assets/jquery.min.js', array(), '3.2.1', false );
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_style( 'horoscop-stylesheet', plugin_dir_url( __FILE__ ) . 'assets/style.min.css', '', '1.0.0', 'all' );
}

/**
 * Internationalization plugin.
 *
 * @since 3.9.3
 *
 * @see load_plugin_textdomain funcion
 * @link https://developer.wordpress.org/reference/functions/load_plugin_textdomain
 */
function horoscop_load_plugin_textdomain() {
	load_plugin_textdomain( 'horoscop', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
?>