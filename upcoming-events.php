<?php
/**
 * Template for upcoming events page
 *
 * @package eventbrite-venue
 */
?>
<?php get_header(); ?>
			<div class="row">
				<div class="span8">
					<div class="left-col" id="content">
						<?php get_template_part( 'tmpl/home-feature' ); ?>
						<?php get_template_part( 'tmpl/home-loop' ); ?>
					</div>
				</div>
				<?php get_sidebar(); ?>
			</div>
<?php
get_footer();
