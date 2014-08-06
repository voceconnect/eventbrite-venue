<?php
/**
 * Template for calendar page
 *
 * @package eventbrite-venue
 */
?>
<?php get_header(); ?>

<?php

$month = isset( $_GET['mon'] ) ? absint( $_GET['mon'] ) : false;
$year  = isset( $_GET['yr'] ) ? absint( $_GET['yr'] ) : false;

// validate date inputs
if ( ( !$month || !$year ) || !checkdate( $month, 1, $year ) ) {
	$current_date = getdate( time() );
	$month = $current_date['mon'];
	$year  = $current_date['year'];
}

// create calendar control
$calendar = eventbrite_venue_get_calendar_of_events( $month, $year );

?>
<div class="row">
	<div class="span12">
<table class="page-calendar">
	<thead>
		<tr class="navigation">
			<th class="prev-month"><a href="<?php echo esc_url( htmlspecialchars( $calendar->prev_month_url() ) ); ?>"><?php echo esc_html( $calendar->prev_month() ); ?></a></th>
			<th colspan="5" class="current-month"><?php echo esc_html( $calendar->month() ); ?> <?php echo esc_html( $calendar->year ); ?></th>
			<th class="next-month"><a href="<?php echo esc_url( htmlspecialchars( $calendar->next_month_url() ) ); ?>"><?php echo esc_html( $calendar->next_month() ); ?></a></th>
		</tr>
		<tr class="weekdays">
			<?php foreach ( $calendar->days( 3 ) as $day ): ?>
				<th><?php echo esc_html( $day ); ?></th>
			<?php endforeach; ?>
		</tr>
	</thead>
	<tbody>
		<?php foreach ( $calendar->weeks() as $week ): ?>
			<tr>
				<?php foreach ( $week as $day ): ?>
					<?php
					list( $number, $current, $data ) = $day;

					$classes = array();
					$output  = '';

					if ( is_array( $data ) ) {
						$classes = $data['classes'];
						$title   = $data['title'];
						$output  = empty( $data['output'] ) ? '' : sprintf( '<ul class="output"><li>%s</li></ul>', implode('</li><li>', $data['output'] ) );
					}
					?>
					<td class="day <?php echo implode(' ', $classes); ?>">
						<span class="date" title="<?php echo esc_attr( implode(' / ', $title) ); ?>"><?php echo esc_html( $number ); ?></span>
						<div class="day-content">
							<?php echo $output; ?>
						</div>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>
</div>
</div>
<?php
get_footer();
