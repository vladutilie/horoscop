<?php
/**
 * Horoscop widget setting.
 *
 * Horoscop class for setting the widget and show it on the site.
 *
 * @since 2.1.8
 * @modified: 3.9.3
 *
 * @see WP_Widget class
 * @link https://developer.wordpress.org/reference/classes/wp_widget/
 */
require_once( 'functions.php' );
class Horoscop extends WP_Widget {
	/**
	 * Register widget with Wordpress. Class constructor with initialization.
	 *
	 * @since 2.1.8
	 * @modified: 3.9.3
	 */
	public function __construct() {
		parent::__construct(
			'Horoscop', // Base ID
			__( 'Horoscope', 'horoscop' ), // Widget name
			array( 'description' => __( 'Take me from here and put me in the sidebar to show you what I can.', 'horoscop' ) )
		);
	}

	/**
	 * Front-end display of horoscope widget.
	 *
	 * @since 2.1.8
	 * @modified: 3.9.3
	 *
	 * @see WP_Widget::widget()
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/widget/
	 *
	 * @global object $wpdb	SQL methods.
	 * @global array $signs	Star signs of horoscope.
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $wpdb;
		$title = apply_filters( 'widget_title', $instance['title'] );
		$str = '';
		$str .= $args['before_widget'];
		if ( ! empty( $title ) ) {
			$str .= $args['before_title'] . $title . $args['after_title'];
		}
		$animation = array( 0	=>	array( // show/hide
										'HIDE'	=>	'hide',
										'SHOW'	=>	'show',
									),
							1	=>	array( // fadein/out
										'HIDE'	=>	'fadeOut',
										'SHOW'	=>	'fadeIn',
									),
							2	=>	array( // slideup/down
										'HIDE'	=>	'slideUp',
										'SHOW'	=>	'slideDown',
									)
					);
		$anim = $animation[ $instance['animation'] ];
		$speed = array(
			0	=>	'slow',
			1	=>	'fast',
		);
		$delay = array(
			0	=>	800,
			1	=>	400,
		);
		$hide_effect = $anim['HIDE'] . '("' . $speed[ $instance['speed'] ] . '");';
		$show_effect = $anim['SHOW'] . '("' . $speed[ $instance['speed'] ] . '");';
		$str .= '<script>
		$(document).ready(function() {
			$("div.horoscop-sign").css("display", "none");
			$(".horoscop-sign-widget").click(function() {
				var this_sign = $(this).attr("title");
				var what_show = "show-"+this_sign+"-content";
				$("#horoscop-sign-list").' . $hide_effect . '
				$("div#"+what_show).delay(' . $delay[ $instance['speed'] ] . ').' . $show_effect . '
				$("h3#horoscop-sign-title").click(function() {
					$("div#"+what_show).' . $hide_effect . '
					$("#horoscop-sign-list").delay(' . $delay[ $instance['speed'] ] . ').' . $show_effect . '
				});
			});
		});
		</script>';
		$str .= '<div id="horoscop-widget">';
		$signs = horoscop_star_signs();
		for ( $i = 0; $i < 12; $i++ ) {
			$code = $signs[ $i ]['code'];
			$name = $signs[ $i ]['name'];
			$period = $signs[ $i ]['period'];
			$content = $wpdb->get_results( $wpdb->prepare( "SELECT content FROM {$wpdb->prefix}horoscope_cache WHERE sign=%s", $code ));
			if ( empty( $content[0]->content ) ) {
				$text = __( 'The content of this star sign has not been updated correctly.', 'horoscop' );
			} else {
				$text = $content[0]->content;
			}
			$str .= '<div class="horoscop-sign" id="show-' . $name . '-content">
				<h3 id="horoscop-sign-title"><span style="vertical-align:middle;" class="dashicons dashicons-arrow-left-alt2"></span>' . $name . '</h3><br />
				<p id="horoscop-sign-subtitle">' . $period . '</p>
				<p id="horoscop-sign-content">' . $text . '</p>
				<p id="horoscop-footer">Copyright ' . date( 'Y' ) . ' &copy; <a href="http://www.acvaria.com" target="_blank">acvaria.com</a></p></div>';
		}
		if ( $instance['display'] ) {
			$str .= '<ul id="horoscop-sign-list" style="text-align:left;">';
			foreach ( $signs as $sign ) {
				$str .= '<li class="horoscop-sign-widget" title="' . $sign['name'] . '">' .  $sign['name'] . '</li>';
			}
			$str .= '</ul>';
		} else {
			$str .= '<div id="horoscop-sign-list">';
			$j = 1; $k = 0;
			foreach ( $signs as $sign ) {
				if ( 4 == $j ) {
					$br = '<br />';
					$j = 0;
				} else {
					$br = '';
				}
				$j++;
				$str .= '<img src="' . ICONS_DIR . $sign['code'] . '.svg" class="horoscop-sign-widget" title="' . $sign['name'] . '" alt="' . $sign['name'] . '">' . $br;
			}
			$str .= '</div>';
		}
		$str .= '</div>';
		echo $str . $args['after_widget'];
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @since: 2.1.8
	 * @modified: 3.9.3
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
		$instance['title']		=	( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['display']	=	(int) $new_instance['display'];
		$instance['animation']	=	(int) $new_instance['animation'];
		$instance['speed']		=	(int) $new_instance['speed'];
		return $instance;
	}

	/**
	 * Back-end horoscope widget form.
	 *
	 * @since: 2.1.8
	 * @modified: 3.9.3
	 *
	 * @see WP_Widget::form()
	 * @link https://developer.wordpress.org/reference/classes/wp_widget/form/
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$defaults = array(
			'title'		=>	__( 'Horoscope', 'horoscop' ),
			'display'	=>	0,	// 0 for icons matrix (default), other value ordered list
			'animation'	=>	2,	// 0 for show, 1 for fade, 2 for slide (default)
			'speed'		=>	0,	// 0 for slow (default), 1 for fast
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		?>
		<div class="widget-content">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'horoscop' ); ?>:</label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'display' ); ?>"><?php _e( 'Display', 'horoscop' ); ?>:</label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'display' ); ?>" name="<?php echo $this->get_field_name( 'display' ); ?>">
					<option value="0" <?php echo ( 0 == $instance['display'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Icons matrix', 'horoscop' ); ?></option>
					<option value="1" <?php echo ( 1 == $instance['display'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Ordered list', 'horoscop' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'animation' ); ?>"><?php _e( 'Animation', 'horoscop' ); ?>:</label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'animation' ); ?>" name="<?php echo $this->get_field_name( 'animation' ); ?>">
					<option value="0" <?php echo ( 0 == $instance['animation'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Show', 'horoscop' ); ?></option>
					<option value="1" <?php echo ( 1 == $instance['animation'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Fade', 'horoscop' ); ?></option>
					<option value="2" <?php echo ( 2 == $instance['animation'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Slide', 'horoscop' ); ?></option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'speed' ); ?>"><?php _e( 'Speed', 'horoscop' ); ?>:</label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'speed' ); ?>" name="<?php echo $this->get_field_name( 'speed' ); ?>">
					<option value="0" <?php echo ( 0 == $instance['speed'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Slow', 'horoscop' ); ?></option>
					<option value="1" <?php echo ( 1 == $instance['speed'] ? ' selected="selected"' : '' ); ?>><?php _e( 'Fast', 'horoscop' ); ?></option>
				</select>
			</p>
		</div>
		<?php
	}
} // class Horoscop
?>