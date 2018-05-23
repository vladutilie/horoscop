<?php
/*
Plugin Name: Horoscope
Plugin URI: https://wordpress.org/plugins/horoscop/
Description: Shows the horoscope on your site using widgets. The content is retrieved from acvaria.com, frequently updated and it is in Romanian language.
Version: 5.4.7
Author: Vlăduț Ilie
Author URI: http://vladilie.ro
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: horoscope
Domain Path: /languages
*/

defined( 'ABSPATH' ) || exit;

/**
 * Initiate Horoscope settings after activate plugin.
 *
 * @since: 2.1.8
 * @modified: 5.4.7
 *
 * @see dbDelta function
 * @link https://developer.wordpress.org/reference/functions/dbdelta/
 *
 * @see wp_next_scheduled function
 * @link https://developer.wordpress.org/reference/functions/wp_next_scheduled/
 * *
 * @see wp_schedule_event function
 * @link https://developer.wordpress.org/reference/functions/wp_schedule_event/
 *
 * @global object $wpdb SQL methotds.
 */
function horoscope_init() {
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}horoscope_cache" ); 
	$query = "CREATE TABLE {$wpdb->prefix}horoscope_cache (
			sign char(10) NOT NULL,
			content text NOT NULL,
			PRIMARY KEY `sign` (sign)
		) " . $wpdb->get_charset_collate() . ";";
	require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $query );
	$Horoscope = new Horoscope();
	$Horoscope->horoscope_cache();
	if ( ! wp_next_scheduled( 'horoscope_cache' ) ) {
		wp_schedule_event( strtotime( 'tomorrow' ), 'daily', 'horoscope_cache' );
	}
}
register_activation_hook( __FILE__, 'horoscope_init' );

/**
 * Remove Horoscope settings after deactivate plugin.
 *
 * @since: 2.1.8
 * @modified: 5.4.7
 *
 * @see delete_option function
 * @link https://developer.wordpress.org/reference/functions/delete_option/
 *
 * @see wp_clear_scheduled_hook function
 * @link https://developer.wordpress.org/reference/functions/wp_clear_scheduled_hook/
 *
 * @global object $wpdb SQL methotds.
 */
function horoscope_deactivation() {
	global $wpdb;
	$wpdb->query( "DROP TABLE {$wpdb->prefix}horoscope_cache" );
	delete_option( 'widget_horoscope' );
	wp_clear_scheduled_hook( 'horoscope_cache' );
}
register_deactivation_hook( __FILE__, 'horoscope_deactivation' );

/**
 * Horoscope register widget
 *
 * @since: 3.9.3
 * @modified: 5.4.7
 *
 * @see register_widget function
 * @link https://developer.wordpress.org/reference/functions/wp_add_privacy_policy_content/
 */
function horoscope_register_widget() {
	register_widget( 'Horoscope' );
}
add_action( 'widgets_init', 'horoscope_register_widget' );

/**
 * Horoscope privacy policy content for GDPR compliance
 *
 * @since: 5.4.7
 *
 * @see GDPR
 * @link http://ec.europa.eu/justice/smedataprotect/index_en.htm
 */
function horoscope_add_privacy_policy_content() {
	if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
		return;
	}
	$content = __( 'The Horoscope plugin does not collect and/or process any personal data of users. The only data which it processes and collects are related to the strictly functionality of the plugin (such data being the name of the horoscope\'s stars and its predictions, data retrieved from acvaria.com through the free agreement of the third party).', 'horoscope' );
	wp_add_privacy_policy_content( __( 'Horoscope plugin', 'horoscope' ), wp_kses_post( $content ) );
}
add_action( 'admin_init', 'horoscope_add_privacy_policy_content' );

require_once( 'class-horoscope.php' );
?>