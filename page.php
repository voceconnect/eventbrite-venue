<?php
/**
 * Default template for pages
 *
 * @package Eventbrite_Event
 */

get_header();
?>
		<div class="row">
			<div class="span8">
				<div class="left-col">
					<?php if ( have_posts() ) : while( have_posts() ) : the_post(); ?>
							<?php get_template_part( 'tmpl/page-loop' ); ?>
							<hr/>
							<?php if ( comments_open() || '0' != get_comments_number() ) : ?>
								<div class="well"><?php comments_template(); ?></div>
							<?php endif; ?>
						<?php
						endwhile;
					endif;
					?>
				</div>
			</div>
			<?php get_sidebar(); ?>
		</div>
<?php
get_footer();
