<?php
/**
 * The template for displaying image attachments.
 *
 * @package Eventbrite_Event
 */

get_header();
?>

<section role="main" class="main-container">
	<div class="container">
		<div class="row">
			<div class="span8">
				<div class="left-col">

		<?php while ( have_posts() ) : the_post(); ?>

			<div <?php post_class(); ?> id="post-<?php the_ID(); ?>">
				<?php the_title( '<h1 class="pagetitle entry-title">', '</h1>' ); ?>

					<div class="entry-meta">
						<?php
							$metadata = wp_get_attachment_metadata();
							printf( __( 'Published <span class="entry-date"><time class="entry-date" datetime="%1$s">%2$s</time></span> at <a href="%3$s" title="Link to full-size image">%4$s &times; %5$s</a> in <a href="%6$s" title="Return to %7$s" rel="gallery">%8$s</a>', 'eventbrite-venue' ),
								esc_attr( get_the_date( 'c' ) ),
								esc_html( get_the_date() ),
								esc_url( wp_get_attachment_url() ),
								$metadata['width'],
								$metadata['height'],
								esc_url( get_permalink( $post->post_parent ) ),
								esc_attr( strip_tags( get_the_title( $post->post_parent ) ) ),
								get_the_title( $post->post_parent )
							);
						?>
					</div><!-- .entry-meta -->

				<div class="entry-content">
					<div class="entry-attachment">
						<div class="attachment">
							<?php eventbrite_venue_the_attached_image(); ?>
						</div><!-- .attachment -->

						<?php if ( has_excerpt() ) : ?>
						<div class="entry-caption">
							<?php the_excerpt(); ?>
						</div><!-- .entry-caption -->
						<?php endif; ?>
					</div><!-- .entry-attachment -->

					<?php
						the_content();
						wp_link_pages( array(
							'before' => '<div class="page-links">' . __( 'Pages:', 'eventbrite-venue' ),
							'after'  => '</div>',
						) );
					?>
					<div role="navigation" id="image-navigation" class="pagination pagination-centered image-navigation">
						<ul>
							<li class="nav-previous"><?php previous_image_link( false, __( '<span class="meta-nav">&larr;</span> Previous', 'eventbrite-venue' ) ); ?></li>
							<li class="nav-next"><?php next_image_link( false, __( 'Next <span class="meta-nav">&rarr;</span>', 'eventbrite-venue' ) ); ?></li>
						</ul>
					</div><!-- #image-navigation -->

				</div><!-- .entry-content -->
				<?php get_template_part( 'tmpl/post-meta' ); ?>
			</div><!-- #post-## -->
			<hr/>
			<div class="well"><?php comments_template(); ?></div>

		<?php endwhile; // end of the loop. ?>

					</div>
				</div>
				<?php get_sidebar(); ?>
			</div>
		</div>
	</section>

<?php get_footer();
