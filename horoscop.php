<?php
/*
Plugin Name: Horoscop
Plugin URI: https://wordpress.org/plugins/horoscop/
Description: Afișează pe site prezicerile horoscopului pentru toate cele 12 zodii. După activarea modulului, o nouă piesă (widget) va fi disponibilă și se poate poziționa în zona dorită din site.
Version: 3.9.3
Author: Vlăduț Ilie
Author URI: http://vladilie.ro
Text Domain: horoscop
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

------------------------------------------------------------------------------------------------------
	Copyright (c) 2012-2018 Vlăduț Ilie (vladilie94@gmail.com)
------------------------------------------------------------------------------------------------------
	Preluarea informațiilor de către acest plugin, se face cu permisiunea editorului Dana Mîndru de la
	www.acvaria.com, acord acceptat, în scris prin e-mail, în data de 20 august 2012.
------------------------------------------------------------------------------------------------------
	Informațiile preluate de către acest plugin respectă și sunt protejate de legea 8/1996 privind
	dreptul de autor și drepturile conexe.
------------------------------------------------------------------------------------------------------
*/
require_once( 'class-horoscop.php' );
define( 'ICONS_DIR', plugin_dir_url( __FILE__ ) . 'assets/icons/' );

register_activation_hook( __FILE__, 'horoscop_init' );
register_deactivation_hook( __FILE__, 'horoscop_deactivation' );

add_action( 'widgets_init', 'horoscop_register_widget' );
add_action( 'wp_enqueue_scripts', 'horoscop_enqueue' );
add_action( 'horoscop_cache', 'horoscop_cache' );
add_action( 'plugins_loaded', 'horoscop_load_plugin_textdomain' );
add_filter( 'cron_schedules', 'horoscop_cron_interval' );
?>