<?php
/**
 * Global theme functions
 *
 * @package Eventbrite_Event
 */

function eventbrite_venue_parent_setup(){

	/**
	 * Define the theme text domain and languages folder for i18n.
	 */
	load_theme_textdomain( 'eventbrite-venue', get_template_directory() . '/languages' );

	/**
	 * Enable support for Custom Backgrounds.
	 */
	add_theme_support( 'custom-background', array(
		'default-color' => '#373737',
		'default-image' => get_template_directory_uri() . '/img/bg-main.png',
	) );

	/**
	 * Enable support for automatic feed links.
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Enable support for Post Formats
	 */
	add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

	/**
	 * Suggest the Eventbrite plugin to users
	 */
	add_theme_support( 'theme-plugin-enhancements', array(
	    array(
	        'slug'    => 'eventbrite-services',
	        'name'    => 'Eventbrite Services',
	        'message' => __( 'The Eventbrite Services plugin is required to connect with Eventbrite.', 'eventbrite-venue' ),
	    ),
	) );

	/**
	 * Register our two theme menus.
	 */
	register_nav_menus( array(
		'primary'   => __( 'Primary Menu', 'eventbrite-venue' ),
		'secondary' => __( 'Secondary Menu', 'eventbrite-venue' ),
	) );

}
add_action( 'after_setup_theme', 'eventbrite_venue_parent_setup' );

/**
 * Global theme script enqueing
 *
 */
if ( ! function_exists( 'eventbrite_venue_enqueue_scripts' ) ) {
	function eventbrite_venue_enqueue_scripts() {

		// Main theme stylesheet
		wp_enqueue_style( 'eventbrite-venue-style', get_stylesheet_uri() );

		// Google Fonts
		wp_enqueue_style( 'eventbrite-venue-cutive' );
		wp_enqueue_style( 'eventbrite-venue-raleway' );

		// Main theme script
		wp_enqueue_script( 'eventbrite-venue-main', get_template_directory_uri() . '/js/script.js', array( 'jquery' ), '20130915', true );

		// Bootstrap scripts
		wp_enqueue_script( 'eventbrite-venue-carousel', get_template_directory_uri() . '/js/bootstrap/bootstrap-carousel.js', array(), '20130915', true );
		wp_enqueue_script( 'eventbrite-venue-collapse', get_template_directory_uri() . '/js/bootstrap/bootstrap-collapse.js', array(), '20130915', true );
		wp_enqueue_script( 'eventbrite-venue-tooltip', get_template_directory_uri() . '/js/bootstrap/bootstrap-tooltip.js', array(), '20130915', true );
		wp_enqueue_script( 'eventbrite-venue-popover', get_template_directory_uri() . '/js/bootstrap/bootstrap-popover.js', array(), '20130915', true );

		// Modernizr
		wp_enqueue_script( 'eventbrite-venue-modernizr', get_template_directory_uri() . '/js/libs/modernizr.min.js',     array(), '20140304', false );

		// Inline reply script for comments
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )
			wp_enqueue_script( 'comment-reply' );

	}
	add_action( 'wp_enqueue_scripts', 'eventbrite_venue_enqueue_scripts' );
}

/**
 * Register Google Fonts
 */
function eventbrite_venue_google_fonts() {
	$protocol = is_ssl() ? 'https' : 'http';

	/*	translators: If there are characters in your language that are not supported
		by Raleway, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Raleway font: on or off', 'eventbrite-venue' ) ) {
		wp_register_style( 'eventbrite-venue-raleway', "{$protocol}://fonts.googleapis.com/css?family=Raleway:400,800" );
	}

	/*	translators: If there are characters in your language that are not supported
		by Cutive, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Cutive font: on or off', 'eventbrite-venue' ) ) {
		wp_register_style( 'eventbrite-venue-cutive', "$protocol://fonts.googleapis.com/css?family=Cutive" );
	}
}
add_action( 'init', 'eventbrite_venue_google_fonts' );

//sidebars
function eventbrite_venue_register_sidebars() {
	register_sidebar( array(
		'name'          => __( 'Primary Sidebar', 'eventbrite-venue' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Appears on posts and pages in the sidebar.', 'eventbrite-venue' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'eventbrite_venue_register_sidebars' );

/**
 * Custom comment callback template
 *
 * @param type $comment
 * @param type $args
 * @param type $depth
 */
function eventbrite_venue_comment_template( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	?>
	<div <?php comment_class(); ?> id="div-comment-<?php comment_ID(); ?>">
		<div id="comment-<?php comment_ID(); ?>" class="comment-id">
			<div class="author-info">
				<?php comment_author_link(); ?> <span><?php
				if ( $comment->comment_author_email == get_the_author_meta( 'email' ) )
					_e( 'responded', 'eventbrite-venue' ); else
					_e( 'said', 'eventbrite-venue' );
				?>:</span><br/>
				<small><?php printf( __( '%1$s at %2$s', 'eventbrite-venue' ), get_comment_date(), get_comment_time() ); ?></small>
			</div>
			<div class="comment-text">
				<?php comment_text(); ?>
				<?php if( $comment->comment_approved == '0' ) : ?>
					<br />
					<em><?php _e( 'Your comment is awaiting moderation.', 'eventbrite-venue' ); ?></em>
				<?php endif; ?>
				<div class="reply">
					<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
				</div>
			</div>
			<div class="clr"></div>
		</div>
	</div>
	<?php
}

/**
 * Print formatted posted on string
 */
function eventbrite_venue_posted_on() {
	$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
	$update_time = '';
	if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) )
		$update_time = '<time class="updated" datetime="%1$s">%2$s</time>';


	$time_string = sprintf( $time_string,
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);

	printf( __( '<span class="posted-date">Posted on <a href="%1$s" title="%2$s" rel="bookmark">%3$s</a></span>', 'eventbrite-venue' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		$time_string
	);

	if ( !empty($update_time) ) {
		$update_time = sprintf( $update_time,
			esc_attr( get_the_modified_date( 'c' ) ),
			esc_html( get_the_modified_date() )
		);

		printf( __( ' <span class="updated-date">Updated on <a href="%1$s" title="%2$s" rel="bookmark">%3$s</a></span>', 'eventbrite-venue' ),
			esc_url( get_permalink() ),
			esc_attr( get_the_time() ),
			$update_time
		);
	}
}

/**
 * Get the next attached image
 */
function eventbrite_venue_the_attached_image() {
	$post                = get_post();
	$attachment_size     = apply_filters( 'eventbrite_venue_attachment_size', array( 1200, 1200 ) );
	$next_attachment_url = wp_get_attachment_url();

	/**
	 * Grab the IDs of all the image attachments in a gallery so we can get the
	 * URL of the next adjacent image in a gallery, or the first image (if
	 * we're looking at the last image in a gallery), or, in a gallery of one,
	 * just the link to that image file.
	 */
	$attachment_ids = get_posts( array(
		'post_parent'    => $post->post_parent,
		'fields'         => 'ids',
		'numberposts'    => -1,
		'post_status'    => 'inherit',
		'post_type'      => 'attachment',
		'post_mime_type' => 'image',
		'order'          => 'ASC',
		'orderby'        => 'menu_order ID'
	) );

	// If there is more than 1 attachment in a gallery...
	if ( 1 < count( $attachment_ids ) ) {
		foreach ( $attachment_ids as $attachment_id ) {
			if ( $attachment_id == $post->ID ) {
				$next_id = current( $attachment_ids );
				break;
			}
		}

		// get the URL of the next image attachment...
		if ( $next_id )
			$next_attachment_url = get_attachment_link( $next_id );

		// or get the URL of the first image attachment.
		else
			$next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
	}

	printf( '<a href="%1$s" title="%2$s" rel="attachment">%3$s</a>',
		esc_url( $next_attachment_url ),
		the_title_attribute( array( 'echo' => false ) ),
		wp_get_attachment_image( $post->ID, $attachment_size )
	);
}

/**
 * Modify caption HTML
 */
function eventbrite_venue_caption_shortcode( $val, $attr, $content = null ) {
	extract( shortcode_atts( array(
		'id'      => '',
		'align'   => 'aligncenter',
		'width'   => '',
		'caption' => ''
	), $attr ) );

	if ( 1 > (int) $width || empty( $caption ) )
		return $val;

	$capid = '';
	if ( $id ) {
		$id = esc_attr( $id );
		$capid = 'id="figcaption_'. $id . '" ';
		$id = 'id="' . $id . '" aria-labelledby="figcaption_' . $id . '" ';
	}

	return '<figure ' . $id . 'class="wp-caption ' . esc_attr( $align ) . '" style="width: '
	. (int) $width . 'px">' . do_shortcode( $content ) . '<figcaption ' . $capid
	. 'class="wp-caption-text">' . $caption . '</figcaption></figure>';
}
add_filter( 'img_caption_shortcode', 'eventbrite_venue_caption_shortcode', 10, 3 );

/**
 * Add eventbrite info to title
 * @param string $title
 * @return string
 */
function eventbrite_venue_wp_title( $title ) {
	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	return $title;
}
add_filter( 'wp_title', 'eventbrite_venue_wp_title', 10, 2 );

/**
 * Displays navigation to next/previous set of posts when applicable.
 */
function eventbrite_venue_paging_nav( $args = array() ) {

	$paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
	$pagenum_link = html_entity_decode( get_pagenum_link() );
	$query_args   = array();
	$url_parts    = explode( '?', $pagenum_link );

	if ( isset( $url_parts[1] ) )
		wp_parse_str( $url_parts[1], $query_args );

	$pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
	$pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

	$format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
	$format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

	$default = array(
		'base'      => $pagenum_link,
		'format'    => $format,
		'total'     => $GLOBALS['wp_query']->max_num_pages,
		'current'   => $paged,
		'mid_size'  => 1,
		'add_args'  => array_map( 'urlencode', $query_args ),
		'prev_text' => __( '&larr; Previous', 'eventbrite-venue' ),
		'next_text' => __( 'Next &rarr;', 'eventbrite-venue' ),
		'type'      => 'list'
	);

	$paginate_links_args = wp_parse_args( (array) $args, $default );
	$paginate_links_args = array_intersect_key( $paginate_links_args, $default );

	// Don't print empty markup if there's only one page.
	if ( $paginate_links_args['total'] < 2 )
		return;

	$links = paginate_links( $paginate_links_args );

	if ( $links ) :
	?>
	<div class="pagination pagination-centered">
		<?php echo $links; ?>
	</div>
	<?php
	endif;
}

/**
 * Output the start date for an event.
 */
function eventbrite_venue_start_date( $event ) {
	if ( ! is_object( $event ) ) {
		return false;
	}

	$start_date = new DateTime( $event->start->local );
	$month = $start_date->format( 'M' );
	$date = $start_date->format( 'j' );
	$day = $start_date->format( 'D' );

	/*
	 * Translators: %1$s = month, %2$s = date, %3$s = day
	 */
	$html = sprintf( __( '%1$s %2$s %3$s', 'eventbrite-venue' ),
		$month,
		'<span>' . $date . '</span>',
		$day
	);

	return $html;
}


/**
 * Register the widgets used by the theme, if available in the activated Eventbrite plugin.
 */
function eventbrite_event_event_register_widgets() {
	if ( class_exists( 'Eventbrite_Introduction_Widget' ) ) {
		register_widget( 'Eventbrite_Introduction_Widget' );
	}
}
add_action( 'widgets_init', 'eventbrite_event_event_register_widgets' );

/**
 * Set the content width global.
 */
 if ( ! isset( $content_width ) ) {
	$content_width = 705;
 }

/**
 * Adds support for a custom header image.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Load Jetpack compatibility file.
 */
require get_template_directory() . '/inc/jetpack.php';

/**
 * Require our Theme Plugin Enhancements class.
 */
require get_template_directory() . '/inc/plugin-enhancements.php';

/**
 * Load our Eventbrite theme options.
 */
require get_template_directory() . '/inc/theme-options.php';

/**
 * Load our Eventbrite-specific functions and hooks.
 */
require get_template_directory() . '/inc/eventbrite.php';

/**
 * Load Eventbrite widgets.
 */
require get_template_directory() . '/inc/widgets.php';
