<?php
/**
 * Template for events loop page
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

get_header(); ?>
			<div class="row">
				<div class="span8">
					<div class="left-col">
						<h1><?php _e( 'Events', 'eventbrite-venue' ); ?></h1>

						<div class="event-items">
						<?php if ( $events ) :
							$previous_day = false;

							for ( $x = 0; $x < count( $events ); $x++ ) :
								$event = $events[ $x ];
								$tz = new DateTimeZone( $event->start->timezone );
								$start_date = new DateTime( $event->start->local, $tz );
								$current_day = $start_date->format( 'd.m.y' );
								if ( count( $events ) == $x + 1 ) {
									$next_day = false;
								} else {
									$next_event = $events[ $x + 1 ];
									$next_date = new DateTime( $next_event->start->local, $tz );
									$next_day = $next_date->format( 'd.m.y' );
								}

								$wp_event_url = eventbrite_venue_get_wp_event_url( $event );
							?>

							<?php if ( $current_day != $previous_day ) : ?>
							<div class="event-day row-fluid">
								<!-- Just need this once per day -->
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
								<div class="span10">
							<?php endif; ?>

								<!-- Loop through that day's events and put in this div -->
									<!-- loop begins now -->
									<div class="event-item">
										<?php if ( isset( $event->logo_url ) && $event->logo_url ) : ?>
										<div class="event-image">
											<?php if ( $wp_event_url ) : ?><a href="<?php echo esc_url( $wp_event_url ); ?>"><?php endif; ?>
												<img src="<?php echo esc_url( $event->logo_url ); ?>" alt="" />
											<?php if ( $wp_event_url ) : ?></a><?php endif; ?>
										</div>
										<?php endif; ?>
										<div class="event-text">
											<a href="<?php echo esc_url( add_query_arg( 'ref', 'wporglist', $event->url ) ); ?>" class="btn pull-right btn-warning"><?php if ( ! empty( $language ) ) echo esc_html( $language ); ?></a>
											<h3>
												<?php echo wp_kses(
													eventbrite_venue_event_title( $event, $wp_event_url ),
													array(
														'a' => array(
															'href' => array(),
														),
													)
												); ?>
											</h3>
											<p class="date event-meta">
												<?php echo wp_kses(
													eventbrite_venue_event_meta( $event ),
													array(
														'span' => array(
															'class' => array(),
														),
													)
												); ?>
											</p>
											<p><?php echo esc_html( eventbrite_venue_get_event_excerpt( $event->description->text ) ); ?></p>
											<?php if ( $wp_event_url ) : ?>
												<p class="text-right"><a href="<?php echo esc_url( $wp_event_url ); ?>"><?php _e( 'Read More', 'eventbrite-venue' ); ?></a></p>
											<?php endif; ?>
										</div>
									</div>

									<!-- end day loop -->
							<?php $previous_day = $current_day; if ( $current_day != $next_day ) : ?>
								</div>
							</div>
							<?php endif; ?>
							<?php endfor; ?>
							<div class="pagination pagination-centered">
								<?php
								echo paginate_links( array(
									'base'      => get_pagenum_link( 1 ) . '%_%',
									'format'    => 'page/%#%',
									'total'     => ceil( $total_events / $per_page ),
									'current'   => $page,
									'prev_text' => __( '&larr; Previous', 'eventbrite-venue' ),
									'next_text' => __( 'Next &rarr;', 'eventbrite-venue' ),
									'type'      => 'list',
								) );
								?>
							</div>
						<?php else: ?>
							<p><?php _e( 'No events found', 'eventbrite-venue' ); ?></p>
						<?php endif; ?>
						</div>
					</div>
				</div>
				<?php get_sidebar(); ?>
			</div>
<?php
get_footer();
