<?php
/**
 * Template for search results
 *
 * @package Eventbrite_Event
 */
?>

<?php get_header(); ?>
<div class="row">
	<div class="span8">
		<div class="left-col">
			<h1 class="pagetitle"><?php printf( __( 'Search Results for: %s', 'eventbrite-venue' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
			<div class="event-items">
				<div class="event-day">
				<?php if ( have_posts() ) : ?>
					<?php while ( have_posts() ) : the_post(); ?>
					<!-- loop begins now -->
					<div class="event-item">
						<div class="event-text">
							<?php the_title( '<h3><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
							<p class="date"><?php eventbrite_venue_posted_on(); ?></p>
							<?php the_excerpt(); ?>
						</div>
					</div>
					<?php endwhile; ?>
					<?php eventbrite_venue_paging_nav(); ?>
				<?php else : ?>
					<p><?php _e( 'No results found', 'eventbrite-venue' ); ?></p>
				<?php endif; ?>
				</div> <!-- end event-day -->
			</div> <!-- end event-items -->
		</div> <!-- end left-col -->
	</div> <!-- end span8 -->
	<?php get_sidebar(); ?>
</div>
<?php
get_footer();
