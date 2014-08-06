<?php
/**
 * Theme options for Eventbrite pages
 *
 * @since 1.0
 */

/**
 * add Theme Options settings specific to this theme
 */
function eventbrite_venue_page_settings() {
	if ( class_exists( 'Voce_Eventbrite_API' ) && Voce_Eventbrite_API::get_auth_service() ) {
		$settings = Voce_Settings_API::GetInstance()
			->add_page( __( 'Eventbrite', 'eventbrite-parent' ), __( 'Eventbrite', 'eventbrite-parent' ), 'eventbrite', 'edit_theme_options', '' )
			->add_group( '', Eventbrite_Settings::eventbrite_group_key() )
			->add_setting( '<h3 id="eb-pages">' . __( 'Recommended Page Settings for This Theme', 'eventbrite-venue' ) . '</h3>', 'eventbrite-page-settings', array(
				'display_callback' => 'eventbrite_venue_page_settings_description_cb',
			) )->group
			->add_setting( __( 'Events List', 'eventbrite-venue' ), 'events-page-id', array(
				'description' => __( 'This page will be used to show a list of your upcoming Eventbrite events.', 'eventbrite-venue' ),
				'display_callback' => 'eventbrite_venue_page_settings_cb',
				'sanitize_callbacks' => array( 'absint' ),
			) )->group
			->add_setting( __( 'Featured Events List', 'eventbrite-venue' ), 'upcoming-events-page-id', array(
				'description' => __( 'This page will be used to display a carousel of the selected featured Eventbrite events along with a listing of upcoming Eventbrite events.', 'eventbrite-venue' ),
				'display_callback' => 'eventbrite_venue_page_settings_cb',
				'sanitize_callbacks' => array( 'absint' ),
			) )->group
			->add_setting( __( 'Calendar Page', 'eventbrite-venue' ), 'calendar-page-id', array(
				'description' => __( 'This page will show a calendar (monthly) view of your Eventbrite events.', 'eventbrite-venue' ),
				'display_callback' => 'eventbrite_venue_page_settings_cb',
				'sanitize_callbacks' => array( 'absint' ),
			) )->group
			->add_setting( __( 'Additional Suggested Pages', 'eventbrite-venue' ), 'suggested-pages', array(
				'display_callback' => 'eventbrite_venue_page_suggested_cb',
			) );
	}
}
add_action( 'init', 'eventbrite_venue_page_settings', 99 );

/**
 * Callback for Voce_Settings_API for showing the description for pages
 */
function eventbrite_venue_page_settings_description_cb() {
	echo '<p>' . sprintf( __( 'To set up the best site with this theme, we recommend adding at
		least the following pages to your theme - Events List page, Featured Events page, and
		Calendar page. You can use an existing page or <a href="%s">create a new one</a>.', 'eventbrite-venue' ) . '</p>',
		esc_url( admin_url( 'post-new.php?post_type=page' ) ) );
}

/**
 * Callback for Voce_Settings_API for showing a dropdown of pages
 *
 * @param type $value value of setting
 * @param type $setting setting object
 * @param type $setting_args args from setting
 */
function eventbrite_venue_page_settings_cb( $value, $setting, $setting_args ) {
	$dropdown = wp_dropdown_pages( array(
		'echo' => false,
		'name' => esc_attr( $setting->get_field_name() ),
		'show_option_none' => __( '&mdash; Select &mdash;', 'eventbrite-parent' ),
		'option_none_value' => '0',
		'selected' => get_eventbrite_setting( $setting->setting_key, '0' ),
	) );

	if ( ! $dropdown ) {
		echo '<p>' . sprintf( __( "You don't have any published pages. To use this feature <a href='%s'>create a new page</a> then come back here and update this.", 'eventbrite-parent' ) . '</p>',
		esc_url( admin_url( 'post-new.php?post_type=page' ) ) );
		return;
	} else {
		printf( '<div class="page-select">%s</div>', $dropdown );
	}

	if ( ! empty( $setting_args['description'] ) )
		echo sprintf( '<span class="description">%s</span>', wp_kses( $setting_args['description'], wp_kses_allowed_html() ) );

	printf ( '<p><a href="%1$s">%2$s</a></p>',
		esc_url( admin_url( 'post-new.php?post_type=page' ) ),
		__( 'Create new page', 'eventbrite-parent' )
	);
}

/**
 * Callback for Voce_Settings_API for showing the suggested pages
 */
function eventbrite_venue_page_suggested_cb() {
	echo '<p>' . __( 'The following pages are also nice to have for this theme:', 'eventbrite-venue' ) . '<p>';
	echo '<ul>';
	echo '<li>' . __( 'Contact Us - hours, address, phone number, email and other contact details', 'eventbrite-venue' ) . '</li>';
	echo '<li>' . __( 'About - additional information about your venue or events', 'eventbrite-venue' ) . '</li>';
	echo '</ul>';

}

/**
 * Flush the rewrite rules if the settings page is updated. used when the Theme
 * Options page is loaded to flush the rewrite rules when the page used for
 * events may have changed.
 */
function eventbrite_venue_maybe_flush_rewrite_rules() {
	if ( isset( $_REQUEST[ 'settings-updated' ] ) )
		flush_rewrite_rules( false );
}
add_action( 'load-appearance_page_eventbrite-page', 'eventbrite_venue_maybe_flush_rewrite_rules' );