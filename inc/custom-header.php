<?php

/**
 * Implements a custom header for Eventbrite Venues
 * - Allows customization of the header background and logo images
 *
 * See http://codex.wordpress.org/Custom_Headers
 */

class Eventbrite_Venue_Custom_Header {

	public static function init() {

		add_action( 'after_setup_theme', array( __CLASS__, 'custom_header_setup' ) );

		add_action( 'customize_register', array( __CLASS__, 'logo_customizer_setup' ) );

		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'google_fonts' ) );

	}

	/**
	 * Add an image control for logo to the customizer
	 *
	 * @param WP_Customize_Manager $wp_customize
	 */
	public static function logo_customizer_setup( $wp_customize ) {

		$wp_customize->add_section( 'eventbrite_logo_section' , array(
			'title'       => __( 'Logo', 'eventbrite-venue' ),
			'priority'    => 30,
			'description' => __( 'Upload a logo to replace the default site name in the header', 'eventbrite-venue' ),
		) );

		$wp_customize->add_setting( 'eventbrite_logo', array(
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'eventbrite_logo', array(
			'label'    => __( 'Logo', 'eventbrite-venue' ),
			'section'  => 'eventbrite_logo_section',
			'settings' => 'eventbrite_logo',
		) ) );

	}

	/**
	 * Sets up the WordPress core custom header arguments and settings.
	 */
	public static function custom_header_setup() {

		$args = array(
			// No support for header text
			'default-text-color'     => 'fff',
			'default-image'          => '%s/img/bg-header.jpg',

			// Set height and width, with a maximum value for the width.
			'height'                 => 228,
			'width'                  => 1280,

			// Callbacks for styling the header and the admin preview.
			'wp-head-callback'       => array( __CLASS__, 'header_style'       ),
			'admin-head-callback'    => array( __CLASS__, 'admin_header_style' ),
			'admin-preview-callback' => array( __CLASS__, 'admin_header_area' ),
		);

		add_theme_support( 'custom-header', $args );

		/*
		 * Default custom headers packaged with the theme.
		 * %s is a placeholder for the theme template directory URI.
		 */
		register_default_headers( array(
			'default' => array(
				'url'           => '%s/img/bg-header.jpg',
				'thumbnail_url' => '%s/img/bg-header.jpg',
				'description'   => _x( 'Default', 'header image description', 'eventbrite' )
			),
		) );

	}

	/**
	 * Styles the header displayed on the blog.
	 */
	public static function header_style() {

		$header_image = get_header_image();
		$header_text_color = get_header_textcolor();

		// If no custom options for text or banner are set, let's bail
		// get_header_textcolor() options: HEADER_TEXTCOLOR is default, hide text (returns 'blank') or any hex value
		if ( HEADER_TEXTCOLOR == $header_text_color && empty( $header_image ) )
			return;
		// If we get this far, we have custom styles. Let's do this.
		?>
		<style type="text/css">
		<?php
			// Has the text been hidden?
			if ( ! display_header_text() ) :
		?>
			.logo-text h1,
			.logo-text h5 {
				position: absolute;
				clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
				clip: rect(1px, 1px, 1px, 1px);
			}
		<?php
			// If the user has set a custom color for the text use that
			else :
		?>
			header .logo-text h1,
			header .logo-text h5 {
				color: #<?php echo $header_text_color; ?>;
			}
		<?php endif; ?>
		</style>
		<?php
			if ( ! empty( $header_image ) ) :
		?>
		<style type="text/css" id="eventbrite-header-css">
		header[role=banner] {
			background: url(<?php header_image(); ?>) top center no-repeat;
			background-size: cover;
		}
		</style>
		<?php
			endif;

	}

	/**
	 * Styles the header image displayed on the Appearance > Header admin panel.
	 */
	public static function admin_header_style() {

		$header_image   = get_header_image();
		$logo_image     = get_theme_mod( 'eventbrite_logo' );
		$theme_uri_root = trailingslashit( get_template_directory_uri() );
		?>
		<style type="text/css" id="eventbrite-admin-header-css">

			<?php
				if ( ! empty( $header_image ) ) :
			?>
			.appearance_page_custom-header header[role=banner] {
				background: url(<?php echo esc_url( $header_image ); ?>) top left;
				font-size: 14px;
			}
			<?php
				endif;
				if ( ! empty( $logo_image ) ) :
			?>
			.appearance_page_custom-header header[role=banner] a.logo {
				background: url(<?php echo esc_url( $logo_image ); ?>) no-repeat left center;
				background-size: auto 125px;
				display: block;
				width: 100%;
				height: 100%;
			}
			<?php
				endif;
			?>

			/* logo container area */
			.appearance_page_custom-header header[role=banner] .container {
				height: 228px;
				-webkit-box-sizing: border-box;
				-moz-box-sizing: border-box;
				box-sizing: border-box;
				position: relative;
				padding-top: 1px;
				padding-left: 55px;
			}

			.appearance_page_custom-header header[role=banner] .container a {
				text-decoration: none;
			}

			.appearance_page_custom-header header[role=banner] .container .logo-text h1 {
				font: 3.78571em/1 "Cutive";
				color: #fff;
				margin: 70px 0 0 0;
				text-rendering: optimizelegibility;
			}

			.appearance_page_custom-header header[role=banner] .container .logo-text h5 {
				font: 1em/1 "Raleway";
				margin: 0;
				padding: 0;
				color: #fff;
				text-rendering: optimizelegibility;
			}

			/* thumbnails */
			.default-header img {
				max-width: 230px;
				width: auto;
			}

			<?php if ( ! display_header_text() ) : ?>
			.logo-text h1,
			.logo-text h5 {
				display: none;
			}
			<?php endif; ?>

		</style>

		<?php
	}

	/**
	 * Outputs markup to be displayed on the Appearance > Header admin panel.
	 * This callback overrides the default markup displayed there.
	 */
	public static function admin_header_area() {

		$home_url = home_url( '/' );
		$style    = sprintf( ' style="color:#%s;"', get_header_textcolor() );
		?>
		<header role="banner">
			<div class="container">
				<a href="<?php echo esc_url( $home_url ); ?>" class="logo"></a>
				<a href="<?php echo esc_url( $home_url ); ?>" class="logo-text">
					<h1<?php echo $style; ?> class="displaying-header-text"><?php bloginfo( 'name' ); ?></h1>
					<h5<?php echo $style; ?> class="displaying-header-text"><?php bloginfo( 'description' ); ?></h5>
				</a>
			</div>
		</header>

		<?php
	}

	/**
	 * Enqueue our Google Fonts for use on the Custom Header page.
	 */
	public static function google_fonts( $hook_suffix ) {

		if ( 'appearance_page_custom-header' != $hook_suffix )
			return;

		wp_enqueue_style( 'eventbrite-venue-raleway' );
		wp_enqueue_style( 'eventbrite-venue-cutive' );

	}

}

Eventbrite_Venue_Custom_Header::init();
