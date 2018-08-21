<?php
defined( 'ABSPATH' ) || exit;
/**
 * Horoscope widget settings.
 *
 * Horoscope class for setting the widget and show it on the site.
 *
 * @since: 2.1.8
 * @modified: 5.5.1
 *
 * @see	WP_Widget class
 * @link https://developer.wordpress.org/reference/classes/wp_widget/
 */
class Horoscope_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress. Class constructor with initialization.
	 *
	 * @since: 2.1.8
	 * @modified: 5.5.1
	 *
	 * @see is_active_widget function
	 * @link https://developer.wordpress.org/reference/functions/is_active_widget/
	 */
	public function __construct() {
		parent::__construct(
			'Horoscope', // Widget Base ID
			__( 'Horoscope', 'horoscope' ), // Widget name
			/* translators: Description of the widget in Dashboard */
			array( 'description' => __( 'Put me in the sidebar to show you what I can.', 'horoscope' ) )
		);
	}

	/**
	 * Front-end display of horoscope widget.
	 *
	 * @since: 2.1.8
	 * @modified: 5.5.1
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
		$signs = array(
			'berbec'		=>	__( 'Aries', 'horoscope' ),
			'taur'			=>	__( 'Taurus', 'horoscope' ),
			'gemeni'		=>	__( 'Gemini', 'horoscope' ),
			'rac'				=>	__( 'Cancer', 'horoscope' ),
			'leu'				=>	__( 'Leo', 'horoscope' ),
			'fecioara'	=>	__( 'Virgo', 'horoscope' ),
			'balanta'		=>	__( 'Libra', 'horoscope' ),
			'scorpion'	=>	__( 'Scorpio', 'horoscope' ),
			'sagetator'	=>	__( 'Sagittarius', 'horoscope' ),
			'capricorn'	=>	__( 'Capricorn', 'horoscope' ),
			'varsator'	=>	__( 'Aquarius', 'horoscope' ),
			'pesti'			=>	__( 'Pisces', 'horoscope' ),
		);
		if ( $instance['display'] ) {
			$str .= '<ul id="horoscope-sign-list">';
			foreach ( $signs as $key => $value ) {
				$str .= '<li><a href="#" title="' . $value . '" data-code="' . $key . '">' . $value . '</a></li>';
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
				$str .= '<a href="#" data-code="' . $key . '"><img src="' . $ICON . $key . '.svg" class="horoscope-sign-widget" title="' . $value . '" alt="' . __( 'Icon', 'horoscope' ) . ' ' . $value . '"></a>' . $br;
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
			'title'			=>	__( 'Horoscope', 'horoscope' ),
			'display'		=>	0,	// 0 for icons matrix (default), other value ordered list
			'animation'	=>	2,	// 0 for show, 1 for fade, 2 for slide (default)
			'speed'			=>	0,	// 0 for slow (default), 1 for fast
			'credits'		=>	0,	// 0 for hide, 1 for showing acvaria.com credits in the widget
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
} // End Horoscope_Widget class
?>
