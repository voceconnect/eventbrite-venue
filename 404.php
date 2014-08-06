<?php
/**
 * Template for 404 error
 *
 * @package Eventbrite_Event
 */

get_header();
?>
		<div class="row">
			<div class="span8">
				<div class="left-col">
					<?php get_template_part( 'tmpl/post-empty' ); ?>
				</div>
			</div>
			<?php get_sidebar(); ?>
		</div>
<?php
get_footer();
