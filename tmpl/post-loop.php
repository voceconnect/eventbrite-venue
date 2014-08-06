<?php
/**
 * Template part for post loop
 *
 * @package Eventbrite_Event
 */

$format = get_post_format();
$formats = get_theme_support( 'post-formats' );
?>
<div <?php post_class( 'event-item' ); ?> id="post-<?php the_ID(); ?>">

	<div class="event-text">
		<?php the_title( '<h3><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h3>' ); ?>
		<p class="date">
			<span class="orange upper">
				<?php if ( $format && in_array( $format, $formats[0] ) ): ?>
					<a class="entry-format" href="<?php echo esc_url( get_post_format_link( $format ) ); ?>" title="<?php echo esc_attr( sprintf( __( 'All %s posts', 'eventbrite-venue' ), get_post_format_string( $format ) ) ); ?>"><?php echo get_post_format_string( $format ); ?></a>
				<?php endif; ?>
				<?php eventbrite_venue_posted_on(); ?>
			</span>
		</p>
		<?php the_content( __( 'Read the rest of this entry &raquo;', 'eventbrite-venue' ) ); ?>
	</div>

	<?php get_template_part( 'tmpl/post-meta' ); ?>

</div> <!-- end post -->
