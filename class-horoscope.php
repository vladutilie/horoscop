<?php
defined( 'ABSPATH' ) || exit;
/**
 * Horoscope widget settings.
 *
 * Horoscope class for setting the widget and show it on the site.
 *
 * @since: 2.1.8
 * @modified: 5.4.7
 *
 * @see	WP_Widget class
 * @link https://developer.wordpress.org/reference/classes/wp_widget/
 */
class Horoscope extends WP_Widget {
	/**
	 * Register widget with Wordpress. Class constructor with initialization.
	 *
	 * @since: 2.1.8
	 * @modified: 5.4.7
	 *
	 * @see is_active_widget function
	 * @link https://developer.wordpress.org/reference/functions/is_active_widget/
	 */
	public function __construct() {
		load_plugin_textdomain( 'horoscope', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
		parent::__construct(
			'horoscope', // Widget Base ID
			__( 'Horoscope', 'horoscope' ), // Widget name
			array( 'description' => __( 'Put me in the sidebar to show you what I can.', 'horoscope' ) )
		);
		add_action( 'horoscope_cache', array( &$this, 'horoscope_cache' ) );
		if ( is_active_widget( false, false, $this->id_base ) ) {
			add_action( 'wp_enqueue_scripts', array( &$this, 'horoscope_enqueue' ) );
			add_action( 'wp_ajax_horoscope_sign_content', array( &$this, 'horoscope_sign_content' ) );
			add_action( 'wp_ajax_nopriv_horoscope_sign_content', array( &$this, 'horoscope_sign_content' ) ); // for not logged in users
		}
	}

	/**
	 * All horoscope signs.
	 *
	 * Horoscope star signs with translations and periods.
	 *
	 * @since: 3.9.3
	 * @modified: 5.4.7
	 *
	 * @param string $sign Default: null.
	 *
	 * @return
	 * 		array	Name and period of every star sign, if $sign is null.
	 *		int		1 or 0. If $sign is not null, the method checks if the given value for $sign exists or not.
	 */
	private function horoscope_star_signs( $sign = null ) {
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
			$found = 0;
			foreach ( $signs as $key => $value ) {
				if ( $key === $sign ) {
					$found = 1;
				}
			}
			return $found;
		}
	}

	/**
	 * Front-end display of horoscope widget.
	 *
	 * @since: 2.1.8
	 * @modified: 5.4.7
	 *
	 * @see WP_Widget::widget()
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/widget/
	 *
	 * @param array $args		Widget arguments.
	 * @param array $instance	Saved values from database.
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$str = '';
		$str .= $args['before_widget'];
		if ( ! empty( $title ) ) {
			$str .= $args['before_title'] . $title . $args['after_title'];
		}
		$str .= '<div id="horoscope-widget">';
		$signs = $this->horoscope_star_signs();
		if ( $instance['display'] ) {
			$str .= '<ul id="horoscope-sign-list">';
			foreach ( $signs as $key => $value ) {
				$str .= '<li><a href="#" title="' . $value['name'] . '" data-code="' . $key . '">' . $value['name'] . '</a></li>';
			}
			$str .= '</ul><!-- /#horoscope-sign-list -->';
		} else {
			$str .= '<div id="horoscope-sign-list">';
			$ICON = plugin_dir_url( __FILE__ ) . 'assets/icons/';
			$j = 1; $k = 0;
			foreach ( $signs as $key => $value ) {
				if ( 4 == $j ) {
					$br = '<br />';
					$j = 0;
				} else {
					$br = '';
				}
				$j++;
				$str .= '<a href="#" data-code="' . $key . '"><img src="' . $ICON . $key . '.svg" class="horoscope-sign-widget" title="' . $value['name'] . '" alt="' . __( 'Icon', 'horoscope' ) . ' ' . $value['name'] . '"></a>' . $br;
			}
			$str .= '</div><!-- /#horoscope-sign-list -->';
		}
		$str .= '</div><!-- /horoscope-widget -->';
		echo $str . $args['after_widget'];
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @since: 2.1.8
	 * @modified: 5.3.2
	 *
	 * @see WP_Widget::update()
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/update/
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title']		=	! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['display']	=	(int) $new_instance['display'];
		$instance['animation']	=	(int) $new_instance['animation'];
		$instance['speed']		=	(int) $new_instance['speed'];
		$instance['credits']	=	isset( $new_instance['credits'] ) ? 1 : 0;
		return $instance;
	}

	/**
	 * Back-end horoscope widget form.
	 *
	 * @since: 2.1.8
	 * @modified: 5.4.7
	 *
	 * @see WP_Widget::form()
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/form/
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'		=>	__( 'Horoscope', 'horoscope' ),
			'display'	=>	0,	// 0 for icons matrix (default), other value ordered list
			'animation'	=>	2,	// 0 for show, 1 for fade, 2 for slide (default)
			'speed'		=>	0,	// 0 for slow (default), 1 for fast
			'credits'	=>	0,	// 0 for hide, 1 for showing acvaria.com credits in the widget
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<div class="widget-content">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'horoscope' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( 'Display', 'horoscope' ); ?>:</label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>">
					<option value="0" <?php echo ( 0 == $instance['display'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Icons matrix', 'horoscope' ); ?></option>
					<option value="1" <?php echo ( 1 == $instance['display'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Ordered list', 'horoscope' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'animation' ); ?>"><?php _e( 'Animation', 'horoscope' ); ?>:</label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'animation' ); ?>" name="<?php echo $this->get_field_name( 'animation' ); ?>">
					<option value="0" <?php echo ( 0 == $instance['animation'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Show', 'horoscope' ); ?></option>
					<option value="1" <?php echo ( 1 == $instance['animation'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Fade', 'horoscope' ); ?></option>
					<option value="2" <?php echo ( 2 == $instance['animation'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Slide', 'horoscope' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'speed' ); ?>"><?php _e( 'Speed', 'horoscope' ); ?>:</label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'speed' ); ?>" name="<?php echo $this->get_field_name( 'speed' ); ?>">
					<option value="0" <?php echo ( 0 == $instance['speed'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Slow', 'horoscope' ); ?></option>
					<option value="1" <?php echo ( 1 == $instance['speed'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Fast', 'horoscope' ); ?></option>
				</select>
			</p>
			<p>
				<input class="checkbox" id="<?php echo $this->get_field_id( 'credits' ); ?>" name="<?php echo $this->get_field_name( 'credits' ); ?>" type="checkbox"<?php checked( 1 == $instance['credits'] ); ?> />
				<label for="<?php echo $this->get_field_id( 'credits' ); ?>" title="<?php _e( 'Check it for showing the acvaria.com credits in the widget.', 'horoscope' ); ?>"><?php _e( 'Show a link to Acvaria&reg; site.', 'horoscope' ); ?></label>
			</p>
		</div>
		<?php
	}

	/**
	 * Enqueue jQuery and stylesheet.
	 *
	 * @since: 2.5.6
	 * @modified: 5.4.7
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
	public function horoscope_enqueue() {
		wp_register_style( 'horoscope-stylesheet', plugin_dir_url( __FILE__ ) . 'assets/style.min.css', array(), '1.0.0' );
		wp_register_script( 'horoscope-jquery', plugin_dir_url( __FILE__ ) . 'assets/jquery.min.js', array(), '3.3.1' );
		wp_register_script( 'horoscope-main-js', plugin_dir_url( __FILE__ ) . 'assets/main.min.js', array( 'jquery', 'jquery-form' ), '1.0.0', true );
		$args = array(
			'nonce'		=>	wp_create_nonce( 'horoscope-nonce' ),
			'ajaxurl'	=>	admin_url( 'admin-ajax.php' )
		);
		wp_enqueue_script( 'horoscope-main-js' );
		wp_localize_script( 'horoscope-main-js', 'horoscope_sign_content', $args );
		wp_enqueue_script( 'horoscope-jquery' );
		wp_enqueue_style( 'horoscope-stylesheet' );
	}

	/**
	 * Get content of start sign from www.acvaria.com.
	 *
	 * @since: 2.1.8
	 * @modified: 5.4.7
	 *
	 * @param	string $sign Star sign of horoscope.
	 * @return	string The content of the star sign retrieved from the source.
	 */
	private function horoscope_get_content( $sign ) {
		global $wp_version;
		$url = 'https://www.acvaria.com/partener-acvaria.php?z=' . $sign;
		$response = wp_remote_get( $url );
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
	 * @modified: 5.4.7
	 *
	 * @see horoscope_star_signs method above
	 *
	 * @see get_var, prepare, insert, update methods
	 * @link https://developer.wordpress.org/reference/classes/wpdb/
	 *
	 * @global object $wpdb SQL methotds.
	 */
	public function horoscope_cache() {
		global $wpdb;
		$signs = $this->horoscope_star_signs();
		foreach ( $signs as $key => $v ) {
			$count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$wpdb->prefix}horoscope_cache WHERE sign = '%s'",
					$key
				)
			);
			$data = array(
				'sign'		=>	$key,
				'content'	=>	$this->horoscope_get_content( $key ),
			);
			$format = array( '%s', '%s' );
			if ( 0 == $count ) {
				$wpdb->insert( "{$wpdb->prefix}horoscope_cache", $data, $format );
			} else {
				$where = array( 'sign' => $key );
				$where_format = array( '%s' );
				$wpdb->update( "{$wpdb->prefix}horoscope_cache", $data, $where, $format, $where_format );
			}
		}
	}

	/**
	 * Horoscope get content for AJAX.
	 *
	 * Method used to get content from {$wpdb-prefix}horoscope_cache and send it to jQuery.
	 *
	 * @since: 5.3.2
	 * @modified: 5.4.7
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
	public function horoscope_sign_content() {
		check_ajax_referer( 'horoscope-nonce', 'nonce' );
		$posted_data = isset( $_POST ) ? $_POST : array();
		$response = array();
		if ( ! $this->horoscope_star_signs( $posted_data['sign'] ) ) {
			$response['error'] = __( 'The chosen sign is not correct. Reload the page and try again.', 'horoscope' );
		} else {
			global $wpdb;
			$content = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT content FROM {$wpdb->prefix}horoscope_cache WHERE sign = '%s'",
					$posted_data['sign'] )
			);
			$widget_options = get_option( 'widget_horoscope' );
			$response['ANIMATION'] = $widget_options[2]['animation'];
			$response['SPEED'] = ( $widget_options[2]['speed'] ? 400 : 800 );

			$signs = $this->horoscope_star_signs();
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
} // End Horoscope class
?>