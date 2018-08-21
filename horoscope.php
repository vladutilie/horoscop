<?php
/*
Plugin Name: Horoscope
Plugin URI: https://wordpress.org/plugins/horoscop/
Description: Shows the horoscope on your site using widgets. The content is retrieved from acvaria.com, frequently updated and it is in Romanian language.
Version: 5.5.1
Author: Vlăduț Ilie
Author URI: http://vladilie.ro
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: horoscope
Domain Path: /languages
*/

defined( 'ABSPATH' ) || exit;

/**
 * Include the class for the Widget.
 */
require_once ( 'class-horoscope-widget.php' );

/**
 * The main class of the plugin.
 *
 * Coordinates all the things for doing a great work!
 *
 * @since 5.5.1
 */
class Horoscope {

	/**
	 * The slug of the plugin.
	 *
	 * @since 5.5.1
	 * @var string $plugin_slug Stores the slug-string of the plugin.
	 */
	protected $plugin_slug = 'horoscope';

	/**
	 * The version of the plugin.
	 *
	 * @since 5.5.1
	 * @var string $version Please update below for every	version change!
	 */
	protected $version = '5.4.7';

	/**
	 * Acvaria.com API URL.
	 *
	 * @since 5.5.1
	 * @var string $acvaria_url Stores the API which retrieve the content from.
	 */
	protected $acvaria_url = 'https://acvaria.com/partener-acvaria.php?z=';

	/**
	 * Class constructor with main initializations.
	 *
	 * @since: 5.5.1
	 *
	 * @see register_activation_hook function
	 * @link https://developer.wordpress.org/reference/functions/register_activation_hook/
	 *
	 * @see register_deactivation_hook function
	 * @link https://developer.wordpress.org/reference/functions/register_deactivation_hook/
	 *
	 * @see add_action function
	 * @link https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		add_action( 'admin_init', array( $this, 'privacy_policy_content' ) );
		add_action( 'plugins_loaded', array( $this, 'l10n' ) );
		add_action( 'widgets_init', array( $this, 'register_widget' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'wp_ajax_horoscope_sign_content', array( $this, 'sign_content' ) );
		add_action( 'wp_ajax_nopriv_horoscope_sign_content', array( $this, 'sign_content' ) );
	}

	/**
	 * Load localization.
	 *
	 * Load localization in Romanian language for the plugin.
	 *
	 * @since 5.5.1
	 *
	 * @see load_plugin_textdomain function is relied on
	 * @link https://developer.wordpress.org/reference/functions/load_plugin_textdomain/
	 *
	 * @see plugin_basename function is relied on
	 * @link https://developer.wordpress.org/reference/functions/plugin_basename/
	 */
	public function l10n() {
		load_plugin_textdomain( $this->plugin_slug, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Initiate Horoscope settings after activate plugin.
	 *
	 * @since: 2.1.8
	 * @modified: 5.5.1
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
	public function activate() {
		global $wpdb;
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}horoscope_cache" );
		$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}horoscope" );
		$query = "CREATE TABLE {$wpdb->prefix}horoscope (
				`sign` char(10) NOT NULL,
				`content` text NOT NULL,
				PRIMARY KEY `sign` (sign)
			) " . $wpdb->get_charset_collate() . ";";
		require_once ( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $query );
		$this->syncronize();
		if ( ! wp_next_scheduled( 'horoscope_cache' ) ) {
			wp_schedule_event( strtotime( 'tomorrow' ), 'daily', 'horoscope_cache' );
		}
	}
	/**
	 * Removes Horoscope settings after deactivate plugin.
	 *
	 * @since: 2.1.8
	 * @modified: 5.5.1
	 *
	 * @see delete_option function
	 * @link https://developer.wordpress.org/reference/functions/delete_option/
	 *
	 * @see wp_clear_scheduled_hook function
	 * @link https://developer.wordpress.org/reference/functions/wp_clear_scheduled_hook/
	 *
	 * @global object $wpdb SQL methotds.
	 */
	public function deactivate() {
		global $wpdb;
		$wpdb->query( "DROP TABLE {$wpdb->prefix}horoscope" );
		delete_option( 'widget_horoscope' );
		wp_clear_scheduled_hook( 'horoscope_cache' );
	}

	/**
	* Horoscope register widget
	*
	* @since: 3.9.3
	* @modified: 5.5.1
	*
	* @see register_widget function
	* @link https://developer.wordpress.org/reference/functions/wp_add_privacy_policy_content/
	*/
	public function register_widget() {
		register_widget( 'Horoscope_Widget' );
	}

	/**
	* Horoscope privacy policy content for GDPR compliance.
	*
	* @since: 5.4.7
	* @modified: 5.5.1
	*
	* @see wp_add_privacy_policy_content
	* @link: https://developer.wordpress.org/plugins/privacy/suggesting-text-for-the-site-privacy-policy/
	*
	* @see GDPR
	* @link http://ec.europa.eu/justice/smedataprotect/index_en.htm
	*/
	public function privacy_policy_content() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return;
		}
		$content = __( 'The Horoscope plugin does not collect and/or process any personal data of users. The only data which it processes and collects are related to the strictly functionality of the plugin (such data being the name of the horoscope\'s stars and its predictions, data retrieved from acvaria.com through the free agreement of the third party).', 'horoscope' );
		/* translators: The name of the plugin for WP privacy policy page */
		wp_add_privacy_policy_content( __( 'Horoscope plugin', 'horoscope' ), wp_kses_post( $content ) );
	}

	/**
	 * Enqueue jQuery and stylesheet.
	 *
	 * @since: 2.5.6
	 * @modified: 5.5.1
	 *
	 * @see wp_register_style function
	 * @link https://developer.wordpress.org/reference/functions/wp_register_style/
	 *
	 * @see wp_register_script function
	 * @link https://developer.wordpress.org/reference/functions/wp_register_script/
	 *
	 * @see wp_enqueue_script function
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
	 *
	 * @see wp_localize_script function
	 * @link https://developer.wordpress.org/reference/functions/wp_localize_script/
	 *
	 * @see wp_enqueue_style function
	 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 *
	 * @see plugin_dir_url function
	 * @link https://developer.wordpress.org/reference/functions/plugin_dir_url/
	 */
	public function enqueue() {
		wp_register_style( 'horoscope-stylesheet', plugin_dir_url( __FILE__ ) . 'assets/style.min.css', array(), $this->version );
		wp_register_script( 'horoscope-jquery', plugin_dir_url( __FILE__ ) . 'assets/jquery.min.js', array(), '3.3.1' );
		wp_register_script( 'horoscope-main-js', plugin_dir_url( __FILE__ ) . 'assets/main.min.js', array( 'jquery', 'jquery-form' ), $this->version, true );
		$args = array(
			'nonce'		=>	wp_create_nonce( 'horoscope-nonce' ),
			'ajaxurl'	=>	admin_url( 'admin-ajax.php' )
		);
		wp_localize_script( 'horoscope-main-js', 'horoscope_sign_content', $args );
		wp_enqueue_script( 'horoscope-main-js' );
		wp_enqueue_script( 'horoscope-jquery' );
		wp_enqueue_style( 'horoscope-stylesheet' );
	}

	/**
	 * All horoscope signs.
	 *
	 * Horoscope star signs with translations and periods.
	 *
	 * @since: 3.9.3
	 * @modified: 5.5.1
	 *
	 * @param string $sign Default: null.
	 *
	 * @return {
	 * 		@type array	Name and period of every star sign, if $sign is null.
	 *		@type int		1 or 0. If $sign is not null, the method checks if the given value for $sign exists or not.
	 * }
	 */
	private function star_signs( $sign = null ) {
		$signs = array(
			'berbec' => array(
				'name'	=>	__( 'Aries', 'horoscope' ),
				'period'=>	__( 'March 21st - April 20th', 'horoscope' ),
			),
			'taur' => array(
				'name'	=>	__( 'Taurus', 'horoscope' ),
				'period'=>	__( 'April 21st - May 21st', 'horoscope' ),
			),
			'gemeni' => array(
				'name'	=>	__( 'Gemini', 'horoscope' ),
				'period'=>	__( 'May 22nd - June 21st', 'horoscope' ),
			),
			'rac' => array(
				'name'	=>	__( 'Cancer', 'horoscope' ),
				'period'=>	__( 'June 22nd - July 22nd', 'horoscope' ),
			),
			'leu' => array(
				'name'	=>	__( 'Leo', 'horoscope' ),
				'period'=>	__( 'July 23rd - August 22nd', 'horoscope' ),
			),
			'fecioara' => array(
				'name'	=>	__( 'Virgo', 'horoscope' ),
				'period'=>	__( 'August 23rd - September 22nd', 'horoscope' ),
			),
			'balanta' => array(
				'name'	=>	__( 'Libra', 'horoscope' ),
				'period'=>	__( 'September 23rd - October 22nd', 'horoscope' ),
			),
			'scorpion' => array(
				'name'	=>	__( 'Scorpio', 'horoscope' ),
				'period'=>	__( 'October 23rd - November 21st', 'horoscope' ),
			),
			'sagetator' => array(
				'name'	=>	__( 'Sagittarius', 'horoscope' ),
				'period'=>	__( 'November 22nd - December 21st', 'horoscope' ),
			),
			'capricorn' => array(
				'name'	=>	__( 'Capricorn', 'horoscope' ),
				'period'=>	__( 'December 22nd - January 19th', 'horoscope' ),
			),
			'varsator' => array(
				'name'	=>	__( 'Aquarius', 'horoscope' ),
				'period'=>	__( 'January 20th - February 18th', 'horoscope' ),
			),
			'pesti' => array(
				'name'	=>	__( 'Pisces', 'horoscope' ),
				'period'=>	__( 'February 19th - March 20th', 'horoscope' ),
			),
		);
		if ( null == $sign ) {
			return $signs;
		} else {
			return (int) array_key_exists( $sign, $signs );
		}
	}

	/**
	 * Get content of start sign from www.acvaria.com.
	 *
	 * @since: 2.1.8
	 * @modified: 5.5.1
	 *
	 * @param	string $sign Star sign of horoscope.
	 * @return	string The content of the star sign retrieved from the source.
	 */
	private function get_content( $sign ) {
		$response = wp_remote_get( $this->acvaria_url . $sign );
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
	 * Make horoscope cache at an interval set in cron-job.
	 *
	 * @since: 2.1.8
	 * @modified: 5.5.1
	 *
	 * @see horoscope_star_signs method above
	 *
	 * @see get_var, prepare, insert, update methods
	 * @link https://developer.wordpress.org/reference/classes/wpdb/
	 *
	 * @global object $wpdb SQL methotds.
	 */
	public function syncronize() {
		global $wpdb;
		$signs = $this->star_signs();
		foreach ( $signs as $key => $v ) {
			$count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->prefix}horoscope WHERE sign = '%s'",
					$key
				)
			);
			$data = array(
				'sign'		=>	$key,
				'content'	=>	$this->get_content( $key ),
			);
			$format = array( '%s', '%s' );
			if ( 0 == $count ) {
				$wpdb->insert( "{$wpdb->prefix}horoscope", $data, $format );
			} else {
				$where = array( 'sign' => $key );
				$where_format = array( '%s' );
				$wpdb->update( "{$wpdb->prefix}horoscope", $data, $where, $format, $where_format );
			}
		}
	}

	/**
	 * Horoscope get content for AJAX.
	 *
	 * Method used to get content from {$wpdb-prefix}horoscope_cache and send it to jQuery.
	 *
	 * @since: 5.3.2
	 * @modified: 5.5.1
	 *
	 * @see check_ajax_referer function
	 * @link https://developer.wordpress.org/reference/functions/check_ajax_referer/
	 *
	 * @see horoscope_star_signs method above
	 *
	 * @see get_row, prepare methods
	 * @link https://developer.wordpress.org/reference/classes/wpdb/
	 *
	 * @see get_option function
	 * @link https://developer.wordpress.org/reference/functions/get_option/
	 *
	 * @see ob_get_clean function
	 * @link http://php.net/manual/en/function.ob-get-clean.php/
	 *
	 * @see wp_send_json_success function
	 * @link https://developer.wordpress.org/reference/functions/wp_send_json_success/
	 *
	 * @see wp_die function
	 * @link https://developer.wordpress.org/reference/functions/wp_die/
	 *
	 * @global object $wpdb SQL methotds.
	 */
	public function sign_content() {
		check_ajax_referer( 'horoscope-nonce', 'nonce' );
		$posted_data = isset( $_POST ) ? $_POST : array();
		$response = array();
		if ( ! $this->star_signs( $posted_data['sign'] ) ) {
			$response['error'] = __( 'The chosen sign is not correct. Reload the page and try again.', 'horoscope' );
		} else {
			global $wpdb;
			$content = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT content FROM {$wpdb->prefix}horoscope WHERE sign = '%s'",
					$posted_data['sign'] )
			);
			$widget_options = get_option( 'widget_horoscope' );
			$response['ANIMATION'] = $widget_options[2]['animation'];
			$response['SPEED'] = ( $widget_options[2]['speed'] ? 400 : 800 );

			$signs = $this->star_signs();
			if ( empty( $content ) ) {
				$content = __( 'The content of this star sign has not been updated correctly.', 'horoscope' );
			}
			$response['HTML'] = '<h3 id="horoscope-sign-title"><i class="horoscope-left-arrow"></i>' . $signs[ $posted_data['sign'] ]['name'] . '</h3>
					<p id="horoscope-sign-subtitle">' . $signs[ $posted_data['sign'] ]['period'] . '</p>
					<p id="horoscope-sign-content">' . $content . '</p>';
			if ( $widget_options[2]['credits'] ) {
				$response['HTML'] .= sprintf( __( '%1$sTaken with care from %2$s', 'horoscope' ),
					'<p id="horoscope-credits">', //%1$s
					'&copy; <a href="http://www.acvaria.com" target="_blank">acvaria.com</a></p>' // %2$s
				);
			}
		}
		ob_get_clean();
		wp_send_json_success( $response );
		wp_die();
	}
}
new Horoscope();
?>
