<?php
/**
 * Template part home loop
 *
 * @package eventbrite-venue
 */

$venue_id     = get_eventbrite_setting( 'venue-id', 'all' );
$organizer_id = get_eventbrite_setting( 'organizer-id', 'all' );
$language     = get_eventbrite_setting( 'call-to-action' );
$page         = max( 1, get_query_var( 'paged' ) );
$per_page     = get_option( 'posts_per_page' );

$events = Voce_Eventbrite_API::get_user_events( array(
	'per_page'  => -1,
	'page'      => -1,
	'venue'     => $venue_id,
	'organizer' => $organizer_id
) );

$total_events = count( $events );
if ( $page > 0 ) {
	$events = array_slice( $events, ( $page - 1 ) * $per_page, $per_page );
} else {
	// return the specified number
	if ( $count > 0 )
		$events = array_slice( $events, 0, $count );
}
?>
<h1><?php _e( 'Upcoming Events', 'eventbrite-venue' ); ?></h1>

<div class="event-items">
<?php if ( $events ) : ?>
	<div class="event-day row-fluid">
		<div class="span12">
			<?php
			foreach ( $events as $event ) :
				$wp_event_url = eventbrite_venue_get_wp_event_url( $event );
			?>
			<div class="event-item">
				<div class="event-image">
					<?php if ( $wp_event_url ) : ?><a href="<?php echo esc_url( $wp_event_url ); ?>"><?php endif; ?>
					<?php if ( isset( $event->logo_url ) && $event->logo_url ) : ?>
						<img src="<?php echo esc_url( $event->logo_url ); ?>" alt="" />
					<?php else : ?>
						<div class="span2">
							<h2 class="event-date">
								<?php echo wp_kses(
									eventbrite_venue_start_date( $event ),
									array(
										'span' => array(),
									)
								); ?>
							</h2>
						</div>
					<?php endif; ?>
					<?php if ( $wp_event_url ) : ?></a><?php endif; ?>
				</div>
				<div class="event-text">
					<a href="<?php echo esc_url( add_query_arg( 'ref', 'wporglist', $event->url ) ); ?>" class="btn pull-right btn-warning"><?php if ( ! empty( $language ) ) echo esc_html( $language ); ?></a>
					<h3>
						<?php if ( $wp_event_url ) : ?><a href="<?php echo esc_url( $wp_event_url ); ?>"><?php endif; ?>
						<?php echo esc_html( $event->name->text ); ?>
						<?php if ( $wp_event_url ) : ?></a><?php endif; ?>
					</h3>
					<p class="date">
						<span class="orange upper"><?php echo esc_html( eventbrite_venue_get_event_date( $event->start->local, $event->start->timezone, 'j F Y' ) ); ?></span>
						| <span class="orange upper"><?php echo esc_html( eventbrite_venue_get_event_ticket_price_string( $event->ticket_classes ) ); ?></span>
					</p>
					<p><?php echo esc_html( eventbrite_venue_get_event_excerpt( $event->description->text ) ); ?></p>

					<?php if ( $wp_event_url ) : ?>
						<p class="text-right"><a href="<?php echo esc_url( $wp_event_url ); ?>"><?php _e( 'Read More', 'eventbrite-venue' ); ?></a></p>
					<?php endif; ?>
				</div>
			</div>
			<?php endforeach; ?>
			<div class="pagination pagination-centered">
				<?php echo paginate_links( array(
					'base'      => get_pagenum_link( 1 ) . '%_%',
					'format'    => 'page/%#%',
					'total'     => ceil( $total_events / $per_page ),
					'current'   => $page,
					'prev_text' => __( '&larr; Previous', 'eventbrite-venue' ),
					'next_text' => __( 'Next &rarr;', 'eventbrite-venue' ),
					'type'      => 'list',
				) ); ?>
			</div>
		</div>
	</div>
<?php else: ?>
	<p><?php _e( 'No events found', 'eventbrite-venue' ); ?></p>
<?php endif; ?>
</div>