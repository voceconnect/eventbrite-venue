<?php
/**
 * Template for single event
 *
 * @package eventbrite-venue
 */

// NOTE: We check for a valid event on template redirect so not checking again
// here.

$event               = eb_get_event_by_id( get_query_var( 'eb_event_id' ) );
$event_date_timespan = ( false !== $event ) ? eventbrite_venue_get_event_date_timespan( $event ) : false;

get_header();
?>
			<div class="row">
				<div class="span8">
					<div class="left-col">
						<div class="well">

						<?php if ( $event ) : ?>
							<div class="event-content">
								<div class="event-intoduction">

									<?php if ( ! empty( $event->logo_url ) ) : ?>
										<img class="event-logo" src="<?php echo esc_url( $event->logo_url ); ?>"/>
									<?php endif; ?>

									<?php if ( ! empty( $event->name->text ) ) : ?>
										<h2 class="event-title"><?php echo esc_html( $event->name->text ); ?></h2>
									<?php endif; ?>

									<?php if ( ! is_wp_error( $event_date_timespan ) ) : ?>
										<span class="event-timespan"><?php echo esc_html( $event_date_timespan ); ?></span>
									<?php endif; ?>
									<a class="event-link" href="<?php echo esc_url( eventbrite_venue_get_eb_event_url( $event, 'wporgevent' ) ); ?>"><?php _e( 'More Information &rarr;', 'eventbrite-venue' ); ?></a>

								</div><!--.event-intoduction-->

							<?php if ( ! empty( $event->id ) ) : ?>
								<div class="ticket-info">
									<?php eb_print_ticket_widget( $event->id ); ?>
								</div>
							<?php endif; ?>

								<div class="event-details">
									<!-- Event description block with description and image from eventbrite -->
									<div class="event-description">
									<?php if ( ! empty( $event->description->html ) ) : ?>
										<?php echo wp_kses( $event->description->html, wp_kses_allowed_html( 'post' ) ); ?>
									<?php endif; ?>
									</div>
								</div>

							</div> <!-- end post -->
						<?php endif; ?>

						</div>
					</div>
				</div>
				<?php get_sidebar(); ?>
			</div>
<?php
get_footer();
