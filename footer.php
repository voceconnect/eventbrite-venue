<?php
/**
 * Template for global footer
 *
 * @package eventbrite-event
 */
?>
	</div>
</section>
<footer class="site-footer row" role="contentinfo">
	<div class="container">
		<?php wp_nav_menu( array(
				'theme_location'  => 'secondary',
				'container_class' => 'pull-right',
				'fallback_cb'     => '__return_false'
			) ); ?>
		<p>
			<a class="wordpress-link" href="http://wordpress.org/" rel="generator"><?php _e( 'Proudly powered by WordPress', 'eventbrite-venue' ); ?></a>
			<?php printf( __( 'Theme: %1$s by %2$s.', 'eventbrite-venue' ), 'Eventbrite Venue', '<a href="http://voceplatforms.com/" rel="designer" class="designer-link">Voce Platforms</a>' ); ?>
		</p>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
