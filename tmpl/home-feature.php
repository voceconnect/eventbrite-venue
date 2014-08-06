<?php
/**
 * Template part for featured carousel
 *
 * @package eventbrite-venue
 */

$events = eb_api_get_featured_events();

if ( empty( $events ) ) {
	return;
}
?>

<div id="myCarousel" class="eb-carousel slide">
    <div class="eb-arrows">
		<a class="eb-carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
		<ol class="carousel-indicators">
        <?php for ( $x = 0; $x < count( $events ); $x++ ) : ?>
        <li data-target="#myCarousel" data-slide-to="<?php echo esc_attr( $x ); ?>" <?php if ( 0 == $x ) echo 'class="active"'; ?>></li>
        <?php endfor; ?>
		</ol>
		<a class="eb-carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
    </div>

    <!-- Carousel items -->
    <div class="eb-carousel-inner">
        <?php
		$count = 0;
		foreach ( $events as $event ) :
			$wp_event_url = eventbrite_venue_get_wp_event_url( $event );
		?>
        <div class="<?php if ( 0 == $count ) echo 'active'; ?> item">
            <div class="eb-carousel-thumb">
                <?php if ( $wp_event_url ) : ?><a href="<?php echo esc_url( $wp_event_url ); ?>"><?php endif; ?>
				<?php if ( isset( $event->logo_url ) && $event->logo_url ) : ?>
					<img src="<?php echo esc_url( $event->logo_url ); ?>"/>
				<?php else : ?>
					<?php
					$tz = new DateTimeZone( $event->start->timezone );
					$start_date = new DateTime( $event->start->local, $tz );
					?>
					<!-- Just need this once per day -->
					<div class="span2">
						<h2 class="event-date">
							<?php echo $start_date->format( 'M' ); ?><span><?php echo $start_date->format( 'j' ); ?></span><?php echo $start_date->format( 'D' ); ?>
						</h2>
					</div>
				<?php endif; ?>
				<?php if ( $wp_event_url ) : ?></a><?php endif; ?>
			</div>
			<div class="eb-carousel-text">
				<a href="<?php echo esc_url( add_query_arg( 'ref', 'wporghero', $event->url ) ); ?>" class="btn"><?php echo esc_html( eb_get_call_to_action() ); ?></a>
				<h3>
					<?php if ( $wp_event_url ) : ?><a href="<?php echo esc_url( $wp_event_url ); ?>"><?php endif; ?>
					<?php echo esc_html( $event->name->text ); ?>
					<?php if ( $wp_event_url ) : ?></a><?php endif; ?>
				</h3>
                <p><?php echo eventbrite_venue_get_event_excerpt( $event->description->text, 20 ); ?></p>
            </div>
        </div>
        <?php $count++; endforeach; ?>
    </div>
</div>
