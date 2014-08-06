<?php
/**
 * Template for sidebar
 *
 * @package Eventbrite_Event
 */
?>

<?php
if ( class_exists( 'Voce_Eventbrite_API' ) && eventbrite_venue_get_page_id( 'event-info' ) == get_queried_object_id() ) {
	$events     = eb_api_get_featured_events();
	$event      = array_shift( $events );
	$venue_info = eventbrite_venue_get_venue_address( $event );
	$map_url    = eventbrite_venue_get_venue_google_map_url( $event );

	$venue_info = eventbrite_venue_get_venue_address( $event );
	if ( isset( $venue_info['mailing-address'] ) )
		$address = $venue_info['mailing-address'];
}
?>

<aside class="span4" role="complementary">
	<div class="sidebar">
		<?php if ( class_exists( 'Voce_Eventbrite_API' ) && ( ! empty( $map_url ) || ! empty( $address ) ) ) : ?>
			<div class="event-location widget">
				<h2 class="widget-title"><?php _e( 'Location', 'eventbrite-multi' ); ?></h2>

				<?php if ( $map_url ) : ?>
					<img class="event-map" src="<?php echo esc_url( $map_url ); ?>" />
				<?php endif; ?>

				<?php if ( ! empty( $address ) ) : ?>
					<p class="venue-address">
					<?php foreach ( $address as $key => $line ) : ?>
						<span class="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $line ); ?></span><br/>
					<?php endforeach; ?>
					</p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	</div>
</aside>
