<?php
/**
 * Template for global header
 *
 * @package Eventbrite_Event
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]> <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]> <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?php wp_title( '&laquo;', true, 'right' ); ?></title>
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php do_action( 'before' ); ?>
	<header role="banner">
		<div class="container">
			<div class="logo-row">
				<?php $logo = get_theme_mod( 'eventbrite_logo' ); ?>
				<?php if ( !empty($logo) ) : ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo">
					<img src="<?php echo esc_url( $logo ); ?>" />
				</a>
				<?php endif; ?>
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-text">
					<h1><?php bloginfo( 'name' ); ?></h1>
					<h5><?php bloginfo( 'description' ); ?></h5>
				</a>
			</div>
			<a href="#" class="menu-toggle">-</a>
		</div>
	</header>

	<section role="main" class="main-container">
		<div id="site-container" class="container">
			<?php wp_nav_menu( array(
						'theme_location'  => 'primary',
						'container'       => 'nav',
						'container_class' => 'menu',
						'depth'           => 3,
					) ); ?>
