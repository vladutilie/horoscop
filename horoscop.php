<?php
/*
Plugin Name: Horoscop
Plugin URI: https://wordpress.org/plugins/horoscop/
Description: Shows the horoscope on your site using widgets. The content is retrieved from acvaria.com, frequently updated and it is in Romanian language.
Version: 5.3.2
Author: Vlăduț Ilie
Author URI: http://vladilie.ro
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: horoscop
Domain Path: /languages

------------------------------------------------------------------------------------------------------
	Copyright (c) 2012-2018 Vlăduț Ilie (vladilie94@gmail.com)
------------------------------------------------------------------------------------------------------
	Preluarea informațiilor prin intermediul acestui modul se face cu permisiunea managerului
	www.acvaria.com, Dana MĂŽndru, acord acceptat ĂŽn scris prin e-mail ĂŽn data de 20 august 2012.
------------------------------------------------------------------------------------------------------
	Informațiile preluate de către acest modul sunt protejate de legea 8/1996 privind dreptul de autor
	și drepturile conexe.
------------------------------------------------------------------------------------------------------
*/
defined( 'ABSPATH' ) || exit;

/**
 * Initiate Horoscope settings after activate plugin.
 *
 * @since 2.1.8
 * @modified: 5.3.2
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
function horoscop_init() {
	global $wpdb;
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}horoscope_cache" ); 
	$query = "CREATE TABLE {$wpdb->prefix}horoscope_cache (
			sign char(10) NOT NULL,
			content text NOT NULL,
			PRIMARY KEY `sign` (sign)
		) " . $wpdb->get_charset_collate() . ";";
	require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $query );
	$Horoscop = new Horoscop();
	$Horoscop->horoscop_cache();
	if ( ! wp_next_scheduled( 'horoscop_cache' ) ) {
		wp_schedule_event( strtotime( 'tomorrow' ), 'daily', 'horoscop_cache' );
	}
}
register_activation_hook( __FILE__, 'horoscop_init' );

/**
 * Remove Horoscope settings after deactivate plugin.
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
	$wpdb->query( "DROP TABLE {$wpdb->prefix}horoscope_cache" );
	delete_option( 'widget_horoscop' );
	wp_clear_scheduled_hook( 'horoscop_cache' );
}
register_deactivation_hook( __FILE__, 'horoscop_deactivation' );

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
add_action( 'widgets_init', 'horoscop_register_widget' );

require_once( 'class-horoscop.php' );
?>
