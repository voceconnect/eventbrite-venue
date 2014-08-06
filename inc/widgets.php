<?php
/**
 * Eventbrite Widgets
 *
 * @package eventbrite
 * @author  Voce Communications
 */

 /**
  * Widget that displays the most recent Eventbrite events for the authenticated user
  */
 if ( ! class_exists( 'Eventbrite_Venue_Just_Announced_Widget' ) && class_exists( 'Voce_Eventbrite_API' ) ) {
 class Eventbrite_Venue_Just_Announced_Widget extends WP_Widget {

 	/**
 	 * Create the widget
 	 */
 	function __construct() {
 		$widget_ops = array( 'classname' => 'widget_just_announced', 'description' => __( 'Your most recent events from Eventbrite', 'eventbrite-parent' ) );
 		parent::__construct( 'just-announced', __( 'Eventbrite: Just Announced', 'eventbrite-parent' ), $widget_ops );
 		$this->alt_option_name = 'widget_just_announced';

 		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
 		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
 		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
 	}

 	/**
 	 * Update function for widget
 	 * @param type $new_instance
 	 * @param type $old_instance
 	 * @return type
 	 */
 	function update( $new_instance, $old_instance ) {
 		$instance = $old_instance;
 		$instance['title'] = strip_tags( $new_instance['title'] );
 		$this->flush_widget_cache();

 		$alloptions = wp_cache_get( 'alloptions', 'options' );
 		if ( isset( $alloptions['widget_just_announced'] ) )
 			delete_option( 'widget_just_announced' );

 		return $instance;
 	}

 	/**
 	 * Delete widget cache
 	 */
 	function flush_widget_cache() {
 		wp_cache_delete( 'widget_just_announced', 'widget' );
 	}

 	/**
 	 * Display function for widget
 	 * @param type $args
 	 * @param type $instance
 	 * @return type
 	 */
 	function widget( $args, $instance ) {
 		if ( ! Voce_Eventbrite_API::get_auth_service() )
 			return;

 		$cache = wp_cache_get( 'widget_just_announced', 'widget' );

 		if ( ! is_array( $cache ) )
 			$cache = array();

 		if ( ! isset( $args['widget_id'] ) )
 			$args['widget_id'] = $this->id;

 		if ( isset( $cache[ $args['widget_id'] ] ) ) {
 			echo wp_kses( $cache[ $args['widget_id'] ], wp_kses_allowed_html( 'post' ) );
 			return;
 		}

 		ob_start();
 		extract( $args );

 		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Just Announced', 'eventbrite-parent' );
 		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

 		$recent_events = Voce_Eventbrite_API::get_user_events( array( 'count' => 5, 'orderby' => 'created', 'order' => 'desc' ) );

 		if ( ! empty( $recent_events ) ) :
 			?>
 			<?php echo $before_widget; ?>
 			<?php if ( $title ) echo $before_title . esc_html( $title ) . $after_title; ?>
 			<ul>
			<?php foreach ( $recent_events as $recent_event ) : $wp_event_url = eventbrite_venue_get_wp_event_url( $recent_event ); ?>
 				<li>
 					<?php if ( $wp_event_url ) : ?>
 						<a href="<?php echo esc_url( $wp_event_url ); ?>" title="<?php echo esc_attr( $recent_event->name->text ); ?>">
 					<?php endif; ?>
					<?php echo esc_html( $recent_event->name->text ); ?>
 					<?php if ( $wp_event_url ) : ?>
 						</a>
 					<?php endif; ?><br/>
					<span><?php echo esc_html( mysql2date( get_option( 'date_format' ), $recent_event->start->utc ) ); ?></span>
 				</li>
 			<?php endforeach; ?>
 			</ul>
 			<?php echo $after_widget; ?>
 			<?php
 		endif;

 		$cache[$args['widget_id']] = ob_get_flush();
 		wp_cache_set( 'widget_just_announced', $cache, 'widget' );
 	}

 	/**
 	 * Form used with the admin
 	 * @param type $instance
 	 */
 	function form( $instance ) {
 		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
 		?>
 		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'eventbrite-parent' ); ?></label>
 		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
 		<?php
 	}
 }
 }