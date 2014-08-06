<?php
/**
 * Template part for page loop
 *
 * @package Eventbrite_Event
 */
?>
<div <?php post_class( 'event-text' ); ?> id="post-<?php the_ID(); ?>">
	<?php the_title( '<h1 class="pagetitle">', '</h1>' ); ?>
	<div class="post-entry">
		<?php the_content( __( 'Read the rest of this entry &raquo;', 'eventbrite-venue' ) ); ?>
		<div class="clr"></div>
	</div> <!-- end post-entry -->
	<?php get_template_part( 'tmpl/post-meta' ); ?>
</div> <!-- end post -->
