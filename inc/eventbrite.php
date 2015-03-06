<?php
/**
 * Eventbrite-specific functions.
 *
 * @package Eventbrite_Event
 */



/**
 * Output an event's title.
 *
 * @param object $event
 * @param string $url Eventbrite event URL
 * @return string
 */
 function eventbrite_venue_event_title( $event, $url = '' ) {
	if ( ! is_object( $event ) ) {
		return false;
	}

 	if ( empty( $url ) ) {
 		return $event->name->text;
 	} else {
 		return '<a href="' . $url . '">' . $event->name->text . '</a>';
 	}
 }

 /**
  * Output an event's date and price.
 *
 * @param object $event
 * @return string
 */
 function eventbrite_venue_event_meta( $event ) {
	if ( ! is_object( $event ) ) {
		return false;
	}

	/*
	 * Translators: %1$s = date, %2$s = price
	 */
	return sprintf( __( '%1$s | %2$s', 'eventbrite-venue' ),
		'<span class="orange upper">' . eventbrite_venue_get_event_date( $event->start->local, $event->start->timezone ) . '</span>',
		'<span class="orange upper">' . eventbrite_venue_get_event_ticket_price_string( $event->ticket_classes ) . '</span>'
	);
 }

/**
 * Get an excerpt for an event description. Strips tags and trims words.
 *
 * @param string $text
 * @param int $words number of words to return
 * @return string
 */
function eventbrite_venue_get_event_excerpt( $text, $words = 40 ) {
	return wp_trim_words( strip_tags( $text ), $words );
}

/**
 * Format an event date with the given timezone and optional format.
 * Uses the blog's date format if $date_format is not specified.
 *
 * @param string $date Date string
 * @param string $timezone Timezone in 'Americas/New York' format
 * @param string $date_format
 * @return string
 */
function eventbrite_venue_get_event_date( $date, $timezone, $date_format = '' ) {
	if ( ! $date_format )
		$date_format = get_option( 'date_format' );

	$tz = new DateTimeZone( $timezone );
	$dt = new DateTime( $date, $tz );

	return $dt->format( $date_format );
}

/**
 * Get a string describing the price for an event's ticket(s)
 *
 * @param array $tickets
 * @return string ticket price. or events with multiple tickets, the lowest
 * price followed by text noting higher priced tickets.
 */
function eventbrite_venue_get_event_ticket_price_string( $tickets ) {

	foreach ( $tickets as $ticket ) {
		if ( true == $ticket->free ) {
			$prices[0] = 'Free';
		} else {
			$prices[$ticket->cost->value] = $ticket->cost->display;
		}

	}

	// Events created with the API might have no price assigned
	if ( empty( $prices ) ) {
		return _x( 'Price unknown', 'ticket price', 'eventbrite-venue' );
	}

	if ( 1 == count( $prices ) ) {
		$price = reset($prices);
		return _x( $price, 'ticket price', 'eventbrite-venue' );
	}

	ksort($prices);
	$price = reset($prices);
	$price_suffix = ' and up';

	return sprintf( _x( '%s%s', 'ticket price: price - price suffix', 'eventbrite-venue' ), $price, $price_suffix );
}

/**
 * Get a string representing the timespan of an event
 *
 * @param object $event
 * @return string
 */
function eventbrite_venue_get_event_date_timespan( $event, $occurrence = 0 ) {
	if ( ! is_object( $event ) )
		return new WP_Error( 'event_not_set', esc_html__( "The event variable is expected to be an object." ) );

	try {
		$tz = new DateTimeZone( $event->start->timezone );
	} catch( Exception $e ) {
		return new WP_Error( 'bad_datetimezone', $e->getMessage() );
	}

	$event_start_date = $event->start->local;
	$event_end_date   = $event->end->local;

	try {
		$start_date = new DateTime( $event_start_date, $tz );
	} catch( Exception $e ) {
		return new WP_Error( 'bad_datetime', $e->getMessage() );
	}

	try {
		$end_date = new DateTime( $event_end_date, $tz );
	} catch( Exception $e ) {
		return new WP_Error( 'bad_datetime', $e->getMessage() );
	}

	if ( $start_date->format( 'mdY' ) === $end_date->format( 'mdY' ) ) {
		$date_format_start = 'l, F j, Y \f\r\o\m g:i A';
		$date_format_end   = '\t\o g:i A';
	} else {
		$date_format_start = 'l, F j, Y \a\t g:i A';
		$date_format_end   = '- l, F j, Y \a\t g:i A';
	}

	$time_zone_transitions = $tz->getTransitions();
	$time_zone_string      = $time_zone_transitions[0]['abbr'];

	return sprintf( _x( '%s %s (%s)', 'event timespan: statdate, end date, (time zone)', 'eventbrite-venue' ), $start_date->format( $date_format_start ), $end_date->format( $date_format_end ), $time_zone_string );
}

/**
 * Get the events for month and year of the venue set in the admin
 *
 * @param int $month numeric value of the month
 * @param int $year year
 * @return type
 */
function eventbrite_venue_get_monthly_events( $month, $year ) {
	if ( ! class_exists( 'Voce_Eventbrite_API' ) ) {
		return false;
	}

	$venue_id     = get_eventbrite_setting( 'venue-id', 'all' );
	$organizer_id = get_eventbrite_setting( 'organizer-id', 'all' );

	$venue_events = Voce_Eventbrite_API::get_user_events( array(
		'venue'     => $venue_id,
		'organizer' => $organizer_id
	) );

	$calendar_events = eventbrite_venue_filter_events_by_month($month, $year, $venue_events);

	return $calendar_events;
}

/**
 * Builds the calendar control for the specified month and year
 *
 * @param int $month numeric value of the month
 * @param int $year year
 * @return type
 */
function eventbrite_venue_get_calendar_of_events( $month, $year ) {
	$month_events = eventbrite_venue_get_monthly_events( $month, $year );

	$calendar = Calendar::factory( $month, $year );

	$calendar->standard( 'today' )->standard( 'prev-next' );

	foreach ( $month_events as $month_event ) {

		$start_date = new DateTime( $month_event->start->local );
		$end_date   = new DateTime( $month_event->end->local );

		$start_time = $start_date->format( 'g:ia' );
		$end_time   = $end_date->format( 'g:ia' );

		$cta_text     = get_eventbrite_setting( 'call-to-action' );
		// if set to the 'Buy Tickets' option only use 'Buy' to preserve space in flyout
		$cta_text     = ( 'Buy Tickets' === $cta_text ) ? 'Buy' : $cta_text;
		$eb_event_url = eventbrite_venue_get_eb_event_url( $month_event, 'wpcalendar' );
		$wp_event_url = eventbrite_venue_get_wp_event_url( $month_event );
		$event_popover_url = $eb_event_url;

		if ( $wp_event_url )
			$event_popover_url = $wp_event_url;

		$format_string = '%1$s - %2$s<a href="%3$s" data-toggle="popover"
			data-content="<a href=\'%8$s\' class=\'pull-right btn\'>%9$s</a><p><span>%1$s-%2$s</span>%5$s</p><p>%6$s</p>"
			data-original-title="%7$s">%4$s</a>';

		$output = sprintf( $format_string,
			esc_html( $start_time ),
			esc_html( $end_time ),
			esc_url( $event_popover_url ),
			esc_html( $month_event->name->text ),
			esc_html( eventbrite_venue_get_event_ticket_price_string( $month_event->ticket_classes ) ),
			esc_html( eventbrite_venue_get_event_excerpt( $month_event->description->text, 20 ) ),
			esc_html( $month_event->name->text ),
			esc_url( $eb_event_url ),
			__( esc_html( $cta_text ), 'eventbrite-venue' )
		);

		$event = $calendar->event()
			->condition( 'timestamp', $start_date->format( 'U' ) )
			->title( esc_html( $month_event->name->text ) )
			->output ( $output );
		$calendar->attach( $event );

		$diff = date_diff( $start_date, $end_date );
		$days_diff = (int) $diff->format( '%a' );
		if ( $days_diff ) {

			$start_day = (int) $start_date->format( 'Ymd' );

			$event_title = sprintf( _x( '%s - cont.', 'calendar', 'eventbrite-venue' ),
				esc_html( $month_event->name->text )
			);

			$output = sprintf( $format_string,
				esc_html( $start_time ),
				esc_html( $end_time ),
				esc_url( $event_popover_url ),
				esc_html( $event_title ),
				esc_html( eventbrite_venue_get_event_ticket_price_string( $month_event->ticket_classes ) ),
				esc_html( eventbrite_venue_get_event_excerpt( $month_event->description->text, 20 ) ),
				esc_html( $month_event->name->text ),
				esc_url( eventbrite_venue_get_eb_event_url( $month_event, 'wpcalendar' ) ),
				__( esc_html( $cta_text ), 'eventbrite-venue' )
			);

			$counter = 0;
			while ( $counter < $days_diff ) {
				$counter += 1;
				$event = $calendar->event()
					->condition( 'timestamp', strtotime( $start_day + $counter ) )
					->title( esc_html( $month_event->name->text ) )
					->output( $output );

				$calendar->attach( $event );
			}
		}
	}

	return $calendar;
}

/**
 * Retrieve the event's Eventbrite URL, with the referrer value replaced
 *
 * @param object $event
 * @return string
 */
function eventbrite_venue_get_eb_event_url( $event, $refer = 'wporglink' ) {
	$url = $event->url;
	if ( $refer )
		$url = add_query_arg( 'ref', $refer, $url );

	return $url;
}

/**
 * Get the page id set in the eventbrite settings
 *
 * @param string $type the type to get based on the setting name
 * @return mixed false if the page isn't set or doesn't exist or the url
 */
function eventbrite_venue_get_page_id( $type ) {
	if ( class_exists( 'Voce_Eventbrite_API' ) ) {
		return get_eventbrite_setting( "{$type}-page-id", false );
	} else {
		return false;
	}

}

/**
 * Function to get the page link for pages set in the eventbrite settings,
 * if a page is used with page_on_front we can still utilize the page's
 * original url for a base.
 *
 * An important use for this function is if a user sets the "Events" page as the
 * "page on front" to preserve the original link for use with single events.
 *
 * @param string $type the type to get based on the setting name
 * @return string
 */
function eventbrite_venue_get_eventbrite_page_link( $ebpage ) {
	global $wp_rewrite;

	if ( ! $ebpage )
		return false;

	$rewrite = $wp_rewrite->get_page_permastruct();
	if ( ! empty( $rewrite ) && ( get_post_status( $ebpage ) == 'publish' ) ) {
		$event_page_link = str_replace( '%pagename%', get_page_uri( $ebpage ), $rewrite );
		$event_page_link = home_url( $event_page_link );
		$event_page_link = user_trailingslashit( $event_page_link, 'page' );
	} else {
		$event_page_link = home_url( '?page_id=' . $ebpage->ID );
	}

	return $event_page_link;
}

/**
 * Get the url for an Eventbrite selected page
 *
 * @param string $type the type to get based on the setting name
 * @return mixed false if the page isn't set or doesn't exist or the url
 */
function eventbrite_venue_get_page_url( $type ) {
	$eb_page_id = eventbrite_venue_get_page_id( $type );
	if ( ! $eb_page_id )
		return false;

	$eb_page = get_post( $eb_page_id );
	if ( ! $eb_page )
		return false;

	$eb_page_link = eventbrite_venue_get_eventbrite_page_link( $eb_page );

	return $eb_page_link;
}

/**
 * Filter events by month and year
 *
 * @param string $month
 * @param string $year
 * @param object $venue_events
 * @uses strtotime()
 * @uses getdate()
 * @return array
 */
function eventbrite_venue_filter_events_by_month( $month, $year, $venue_events ) {
	$filtered_events = array();
	foreach( $venue_events as $venue_event ) {
		$start_time = strtotime( $venue_event->start->local );
		$date       = getdate( $start_time );
		if ( ( $date['mon'] == $month ) && ( $date['year'] == $year ) )
			$filtered_events[] = $venue_event;
	}
	return $filtered_events;
}

/**
 * Load the specified template if the currently queried object id matches the
 * given page id
 *
 * @param int $page_id the ID of the page to match
 * @param int $queried_object_id the currently queried object's id
 * @param string $template template path relative to theme dir
 */
function eventbrite_venue_maybe_include_template( $page_id, $queried_object_id, $template ) {
	// only redirect if the page id does not match the queried object and if event properties are not specified
	if ( $page_id && $page_id === $queried_object_id & ( empty( get_query_var( 'eb_event' ) ) && empty( get_query_var( 'eb_event_id' ) ) ) ) {
		do_action( 'eventbrite_venue_template_redirect', $page_id, $queried_object_id, $template );
		include( get_template_directory() . '/' . $template );
		die();
	}
}

/**
 * Get the venue address elements.
 *
 * @param object $event
 * @return array
 */
function eventbrite_venue_get_venue_address( $event ) {

	if ( ! $event || empty( $event->venue ) )
		return false;

	$venue      = $event->venue;
	$venue_info = array();

	// formulate full address to easily output
	$venue_full_add = array();
	if ( ! empty( $venue->name ) )
		$venue_full_add['line-1'] = $venue->name;
	if ( ! empty( $venue->address->address_1 ) )
		$venue_full_add['line-2'] = $venue->address->address_1;
	if ( ! empty( $venue->address->address_2 ) )
		$venue_full_add['line-3'] = $venue->address->address_2;

	$venue_city_state = array();
	if ( ! empty( $venue->address->city ) )
		$venue_city_state[] = $venue->address->city;
	if ( ! empty( $venue->address->region ) )
		$venue_city_state[] = $venue->address->region;

	// The Eventbrite API v3 might not have a ZIP data point yet (July 8, 2014)
	// $venue_zip = ( ! empty( $venue->postal_code ) ) ? $venue->postal_code : '';
	// if ( $venue_city_state || $venue_zip )
	// 	$venue_full_add['line-4'] = implode( ', ', $venue_city_state ) . ' ' . $venue_zip;

	$venue_info['mailing-address'] = $venue_full_add;

	return $venue_info;
}

/**
 * Get the Google Map URL for the venue.
 *
 * @param object $event
 * @param array $args
 * @uses wp_parse_args()
 * @uses add_query_arg()
 * @return string A valid Google Map URL for the venue
 */
function eventbrite_venue_get_venue_google_map_url( $event, $args = array() ) {
	$defaults = array(
		'zoom'   => '13',
		'size'   => '320x320',
		'sensor' => 'false'
	);

	if ( ! $event || empty( $event->venue ) )
		return false;

	$venue = $event->venue;

	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	$google_map = false;
	if ( is_object( $venue ) ) {
		$lat  = isset( $venue->latitude ) ? $venue->latitude : false;
		$long = isset( $venue->longitude ) ? $venue->longitude : false;

		$parameters = array();
		if ( $lat && $long ) {
			$parameters[] = $lat;
			$parameters[] = $long;
		} else {
			if ( isset( $venue->address ) ) {
				$address = $venue->address;
				if ( isset( $venue->address_2 ) )
					$address .= ' ' . $venue->address_2;

				$parameters[] = $address;
			}
			if ( isset( $venue->city ) )
				$parameters[] = $venue->city;
			if ( isset( $venue->region ) )
				$parameters[] = $venue->region;
			if ( isset( $venue->postal_code ) )
				$parameters[] = $venue->postal_code;
		}

		if ( $parameters ) {
			$google_map = 'http://maps.googleapis.com/maps/api/staticmap';
			$location   = implode( ',', $parameters );
			$google_map = add_query_arg( 'center', $location, $google_map );
			$google_map = add_query_arg( 'zoom', $zoom, $google_map );
			$google_map = add_query_arg( 'size', $size, $google_map );
			$google_map = add_query_arg( 'markers', $location, $google_map );
			$google_map = add_query_arg( 'sensor', $sensor, $google_map );
		}
	}

	return $google_map;
}

/**
 * Redirect to selected Eventbrite page templates
 *
 * @uses Voce_Eventbrite_API::get_auth_service()
 * @uses eventbrite_venue_get_dynamic_pages()
 * @uses get_queried_object_id()
 * @uses eventbrite_venue_maybe_include_template()
 * @uses get_eventbrite_setting()
 */
function eventbrite_venue_event_template_redirect() {
	if ( class_exists( 'Voce_Eventbrite_API' ) && Voce_Eventbrite_API::get_auth_service() ) {
		$dynamic_pages = eventbrite_venue_get_dynamic_pages();
		if ( $dynamic_pages ) {
			foreach ( $dynamic_pages as $key => $template ) {
				$queried_object_id = get_queried_object_id();
				eventbrite_venue_maybe_include_template( get_eventbrite_setting( "{$key}-page-id", false ), $queried_object_id, $template );
			}
		}
	}
}
add_action( 'template_redirect', 'eventbrite_venue_event_template_redirect' );

/**
 * Register the widgets used by the theme, if available in the activated Eventbrite plugin.
 *
 * @uses register_widget()
 */
function eventbrite_venue_event_register_widgets() {
	if ( class_exists( 'Eventbrite_Introduction_Widget' ) ) {
		register_widget( 'Eventbrite_Introduction_Widget' );
	}
	if ( class_exists( 'Eventbrite_Venue_Just_Announced_Widget' ) ) {
		register_widget( 'Eventbrite_Venue_Just_Announced_Widget' );
	}
}
add_action( 'widgets_init', 'eventbrite_venue_event_register_widgets' );

/**
 * Suggested default pages for the event theme
 *
 * @param array $default_pages
 * @return array
 */
function eventbrite_venue_event_default_pages( $default_pages ) {
	$venue_pages = array(
		'events-list' => array(
			'title' => __( 'Events List', 'eventbrite-venue' )
		),
		'featured-events-list' => array(
			'title' => __( 'Featured Events List', 'eventbrite-venue' )
		),
		'calendar' => array(
			'title' => __( 'Calendar', 'eventbrite-venue' )
		),
		'about' => array(
			'title' => __( 'About', 'eventbrite-venue' )
		)
	);

	$venue_pages = array_merge( $venue_pages, $default_pages );

	return $venue_pages;
}
add_filter( 'eventbrite_default_pages', 'eventbrite_venue_event_default_pages' );

/**
 * Get the theme's dynamic pages.
 *
 * @return array
 */
function eventbrite_venue_get_dynamic_pages() {
	return array(
		'events'          => 'event.php',
		'upcoming-events' => 'upcoming-events.php',
		'calendar'        => 'template-calendar.php',
	);
}

/**
 * Get the WordPress event URL.
 *
 * @param object $event
 * @uses eventbrite_venue_get_page_url()
 * @return string
 */
function eventbrite_venue_get_wp_event_url( $event ) {
	global $wp_rewrite;

	$events_page_url = eventbrite_venue_get_page_url( 'events' );
	if ( ! $events_page_url )
		return '';

	if ( empty( $wp_rewrite->permalink_structure ) ) {
		$event_url = add_query_arg( array( 'eb_event' => 1, 'eb_event_id' => $event->id ), $events_page_url );
	} else {
		$event_url = sprintf( '%s%s/', $events_page_url, $event->id );
	}

	return $event_url;
}

/**
 * Filter to handle the customized (events/posts) search template query,
 * forcing no paging when search "events" to allow
 * "events" paging and halving posts_per_page when initial searching
 *
 * @global WP_Object $wp_query
 * @param string $search
 * @param WP_Object $query
 * @uses is_search()
 * @uses is_admin()
 * @uses is_paged()
 * @uses query_vars()
 * @uses get_option()
 * @return string
 */
function eventbrite_venue_multi_event_search( $search, &$query ) {
   global $wp_query;

   if ( is_search() && ! is_admin() ) {
       if ( isset( $_REQUEST['type'] ) ) {
           // Force no paging so a 404 does not occur when paging through "events"
           if ( 'events' == $_REQUEST['type'] ) {
               $wp_query->is_paged = false;
           }
       } else {
           // Only display half the results on the initial search
           $query->query_vars['posts_per_page'] = ceil( get_option( 'posts_per_page' ) / 2 );
       }
   }

   return $search;
}
add_filter( 'posts_search', 'eventbrite_venue_multi_event_search', 10 , 2 );

/**
 * Given a page url, removes the home url from it and the trailing slash.
 *
 * @param string $page_url
 * @uses get_home_url()
 * @return string base of the page
 */
function eventbrite_venue_get_page_base( $page_url ) {
	return ltrim( str_replace( get_home_url(), '', $page_url ) , '/' );
}

/**
 * Flush rewrite rules.
 *
 * @param string $service
 * @uses flush_rewrite_rules()
 */
function eventbrite_venue_flush_rewrite_rules( $service ) {
	if ( 'eventbrite' === $service )
		flush_rewrite_rules( true );
}
add_action( 'keyring_connection_verified', 'eventbrite_venue_flush_rewrite_rules' );

/**
 * Clean up rewrites when removing Eventbrite connection.
 *
 * @param string $service
 * @uses remove_filter()
 * @uses eventbrite_venue_flush_rewrite_rules()
 */
function eventbrite_venue_keyring_connection_deleted( $service ) {
	// do not want to set the rewrites when we remove the eventbrite keyring connection
	if ( 'eventbrite' === $service ) {
		remove_filter( 'rewrite_rules_array', 'eventbrite_venue_add_rewrite_rules' );
		eventbrite_venue_flush_rewrite_rules( $service );
	}
}
add_action( 'keyring_connection_deleted', 'eventbrite_venue_keyring_connection_deleted' );

/**
 * Add rewrite rules for events.
 *
 * @param array $rules
 * @uses Voce_Eventbrite_API::get_auth_service()
 * @uses eventbrite_venue_get_page_url()
 * @uses get_home_url()
 * @uses eventbrite_venue_get_dynamic_pages()
 * @uses eventbrite_venue_get_page_url()
 * @uses eventbrite_venue_get_page_base()
 * @return array Rewrite rules
 */
function eventbrite_venue_add_rewrite_rules( $rules ) {
	if ( class_exists( 'Voce_Eventbrite_API' ) && Voce_Eventbrite_API::get_auth_service() ) {
		$events_page_url = eventbrite_venue_get_page_url( 'events' );
		// single event rewrites
		if ( $events_page_url ) {
			$events_page_base = ltrim( str_replace( get_home_url(), '', $events_page_url ) , '/' );

			$event_rules = array();
			$event_rules_key = sprintf( '%s?(\d+)?/?$', $events_page_base );
			$event_rules[$event_rules_key] = 'index.php?eb_event=1&eb_event_id=$matches[1]';
			$event_rules_key = sprintf( '%s?(\d+)/(\d+)/?$', $events_page_base );
			$event_rules[$event_rules_key] = 'index.php?eb_event=1&eb_event_id=$matches[1]&eb_occurrence=$matches[2]';
			$rules = array_merge( $event_rules, $rules );
		}

		// add paging to dynamic pages
		$dynamic_pages = eventbrite_venue_get_dynamic_pages();
		if ( $dynamic_pages ) {
			foreach ( $dynamic_pages as $key => $template ) {
				$dynamic_page_url = eventbrite_venue_get_page_url( $key );
				if ( ! $dynamic_page_url ) {
					continue;
				}
				$dynamic_page_base = rtrim ( eventbrite_venue_get_page_base( $dynamic_page_url ), '/' );
				$dynamic_rules = array();

				// base page
				$dynamic_rules_key = sprintf( '%s/?$', $dynamic_page_base );
				$dynamic_rules[$dynamic_rules_key] = sprintf( 'index.php?pagename=%s&eb_dynamic_page=1&paged=1', $dynamic_page_base );
				$new_rules = $dynamic_rules + $rules;

				// paging on base page
				$dynamic_rules_key = sprintf( '%s/page/?([0-9]{1,})/?$', $dynamic_page_base );
				$dynamic_rules[$dynamic_rules_key] = sprintf( 'index.php?pagename=%s&eb_dynamic_page=1&paged=$matches[1]', $dynamic_page_base );
				$rules = array_merge( $dynamic_rules + $rules );
			}
		}
	}

	return $rules;
}

/**
 * Add query vars for events.
 *
 * @param array $query_vars
 * @return array
 */
function eventbrite_venue_add_query_vars( $query_vars ) {
	$query_vars[] = 'eb_event';
	$query_vars[] = 'eb_event_id';
	$query_vars[] = 'eb_dynamic_page';
	$query_vars[] = 'eb_occurrence';

	return $query_vars;
}

/**
 * Redirect to event listing or single event template.
 *
 * @uses Voce_Eventbrite_API::get_auth_service()
 * @uses eventbrite_venue_get_page_id()
 * @uses is_front_page()
 * @uses get_option()
 * @uses set_query_var()
 * @uses get_query_var()
 * @uses get_template_directory()
 * @uses eb_get_event_by_id()
 * @uses eventbrite_venue_maybe_include_template()
 */
function eventbrite_venue_template_redirect() {
	if ( ! class_exists( 'Voce_Eventbrite_API' ) || ! Voce_Eventbrite_API::get_auth_service() )
		return;

	// handle case when reading settings, front page setting is set to upcoming events
	$upcoming_events_page_id = eventbrite_venue_get_page_id( 'upcoming-events' );
	if ( is_front_page() && $upcoming_events_page_id && $upcoming_events_page_id == get_option( 'page_on_front' ) ) {
		// set paged variable
		set_query_var( 'paged' , get_query_var( 'page' ) );
		include( get_template_directory() . '/upcoming-events.php' );
		die();
	}

	// handle case when reading settings, front page setting is set to events
	$events_page_id = eventbrite_venue_get_page_id( 'events' );
	if ( is_front_page() && $events_page_id && $events_page_id == get_option( 'page_on_front' ) ) {
		// set paged variable
		set_query_var( 'paged' , get_query_var( 'page' ) );
		include( get_template_directory() . '/event.php' );
		die();
	}

	if ( get_query_var( 'eb_event' ) && eventbrite_venue_get_page_url( 'events' ) ) {
		$event_id = get_query_var( 'eb_event_id' );
		if ( $event_id ) {

			if ( ! eb_get_event_by_id( $event_id ) ) {
				global $wp_query;
				$wp_query->is_404 = true;
				return;
			}

			include( get_template_directory() . '/event-single.php' );
			die();
		}

		include( get_template_directory() . '/event.php' );
		die();
	}

	if ( get_query_var( 'eb_dynamic_page' ) ) {
		// check currently queried object against Eventbrite pages to see if we
		// need to load a different template
		$dynamic_pages = eventbrite_venue_get_dynamic_pages();
		if ( $dynamic_pages ) {
			$queried_object_id = get_queried_object_id();
			foreach ( $dynamic_pages as $key => $template ) {
				eventbrite_venue_maybe_include_template( eventbrite_venue_get_page_id( $key ), $queried_object_id, $template );
			}
		}
	}
}

/**
 * Filter in body classes.
 *
 * @param array $classes
 * @uses get_query_var()
 * @uses eventbrite_venue_get_page_url()
 * @uses eventbrite_venue_get_page_id()
 * @return array Filtered body classes
 */
function eventbrite_venue_body_class( $classes ) {
	if ( get_query_var( 'eb_event' ) && eventbrite_venue_get_page_url( 'events' ) )
		$classes[] = 'eventbrite-venue-single-event';

	global $post;

	if ( isset( $post->ID ) && $post->ID == eventbrite_venue_get_page_id( 'calendar' ) )
		$classes[] = 'template-calendar';

	return $classes;
}
add_action( 'body_class', 'eventbrite_venue_body_class' );

function eventbrite_venue_setup(){
	add_filter( 'rewrite_rules_array', 'eventbrite_venue_add_rewrite_rules' );
	add_filter( 'query_vars', 'eventbrite_venue_add_query_vars' );
	add_action( 'template_redirect', 'eventbrite_venue_template_redirect' );
}
add_action( 'after_setup_theme', 'eventbrite_venue_setup' );

/**
 * Flush rewrite rules on theme activation.
 */
add_action( 'after_switch_theme', 'flush_rewrite_rules' );
