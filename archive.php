<?php
/**
 * Template for archive pages
 *
 * @package Eventbrite_Event
 */
?>

<?php get_header(); ?>

<div class="row">
	<div class="span8">
		<div class="left-col">
			<?php if( have_posts() ) : ?>
				<h1 class="page-title"><?php
					if ( is_category() ) :
						single_cat_title();

					elseif ( is_tag() ) :
						single_tag_title();

					elseif ( is_author() ) :
						/* Queue the first post, that way we know
						 * what author we're dealing with (if that is the case).
						*/
						the_post();
						printf( __( 'Author: %s', 'eventbrite-venue' ), '<span class="vcard">' . get_the_author() . '</span>' );
						/* Since we called the_post() above, we need to
						 * rewind the loop back to the beginning that way
						 * we can run the loop properly, in full.
						 */
						rewind_posts();

					elseif ( is_day() ) :
						printf( __( 'Day: %s', 'eventbrite-venue' ), '<span>' . get_the_date() . '</span>' );

					elseif ( is_month() ) :
						printf( __( 'Month: %s', 'eventbrite-venue' ), '<span>' . get_the_date( 'F Y' ) . '</span>' );

					elseif ( is_year() ) :
						printf( __( 'Year: %s', 'eventbrite-venue' ), '<span>' . get_the_date( 'Y' ) . '</span>' );

					elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
						_e( 'Asides', 'eventbrite-venue' );

					elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
						_e( 'Images', 'eventbrite-venue' );

					elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
						_e( 'Videos', 'eventbrite-venue' );

					elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
						_e( 'Quotes', 'eventbrite-venue' );

					elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
						_e( 'Links', 'eventbrite-venue' );

					else :
						_e( 'Archives', 'eventbrite-venue' );

					endif;
				?>
				</h1>
				<div class="event-items">
					<div class="event-day row-fluid">
						<?php while( have_posts() ) : the_post(); ?>
							<div class="span12">
								<?php get_template_part( 'tmpl/post-loop' ); ?>
							</div>
						<?php endwhile; ?>
					</div>
				</div>
				<?php eventbrite_venue_paging_nav(); ?>
			<?php
			else :
				get_template_part( 'tmpl/post-empty' );
			endif;
			?>
			</div>
		</div>
		<?php get_sidebar(); ?>
	</div>

<?php
get_footer();
