<?php
/**
 * Default template for single posts
 *
 * @package Eventbrite_Event
 */

get_header();
?>
		<div class="row">
			<div class="span8">
				<div class="left-col">
	<?php
	if ( have_posts() ) : while( have_posts() ) : the_post();
			get_template_part( 'tmpl/post-loop' );
			wp_link_pages( array( ) );
			?>
			<div class="pagination pagination-centered">
				<ul>
					<?php previous_post_link( '<li class="nav-previous">%link</li>', ' %title' ); ?>
					<?php next_post_link( '<li class="nav-next">%link</li>', '%title ' ); ?>
				</ul>
			</div>
			<div class="well"><?php comments_template(); ?></div>
			<?php
		endwhile;
	else:
		?>
		<h1><?php _e( 'Sorry, no posts matched your criteria.', 'eventbrite-venue' ); ?></h1>
	<?php endif; ?>
				</div>
			</div>
			<?php get_sidebar(); ?>
		</div>
<?php
get_footer();
