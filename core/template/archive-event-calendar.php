<?php get_header(); ?>
<header class="em-header">
    <h1 class="entry-title em-h1"><?php the_archive_event_title() ?></h1>
    <div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
		<?php if ( function_exists( 'bcn_display' ) ) {
			bcn_display();
		}
		?>
    </div>
</header>
<?php
$date    = new EventManagerCalendar();
$year    = date( 'Y' );
$month   = date( 'n' );
$current = DateTime::createFromFormat( 'd/m/Y', date( 'd/m/Y' ) );
debug_console( $current );
$dates  = $date->getAll( $year );
$events = $date->getEvent();
?>
<div class="em-archive">
    <div class="em-calendar">
        <div class="em-calendar-head">
            <div class="em-calendar-previous">
                <button id="previous">
                    <span><i class="fa fa-3x fa-angle-double-left" aria-hidden="true"></i></span>
                </button>
            </div>
            <div class="em-calendar-select">
                <select id="current_month" class="form-control">
					<?php foreach ( $date->months as $id => $m ) : ?>
                        <option value="month-<?php echo $id + 1; ?>" <?php $select = ( $month == ( $id + 1 ) ) ? 'selected="selected"' : null;
						echo $select; ?>
                        "><?php echo $m; ?></option>
					<?php endforeach; ?>
                </select>
                <select id="current_year" class="form-control">
                    <option><?php echo $year - 1; ?></option>
                    <option selected="selected"><?php echo $year; ?></option>
                    <option><?php echo $year + 1; ?></option>
                </select>
            </div>
            <div class="em-calendar-next">
                <button id="next">
                    <span><i class="fa fa-3x fa-angle-double-right" aria-hidden="true"></i></span>
                </button>
            </div>
        </div>
		<?php foreach ( $dates as $year => $months ): ?>
            <div class="em-calendar-year">
                <!-- boucle pour chaque moi -->
				<?php foreach ( $months as $month => $days ) : ?>
                    <div class="em-calendar-month" id="<?php echo $year . '-' . $month ?>">
                        <table>
                            <thead>
                            <tr>
                                <th colspan="3"></th>
                                <th class="em-th">
                                    <!-- Mois et Anné -->
                                    <span><?php echo $date->months[ $month - 1 ] . ' ' . $year; ?></span>
                                </th>
                                <th colspan="3"></th>
                            </tr>
                            <tr>
                                <!-- boucle affichage jour header -->
								<?php $end = end( $days );
								foreach ( $date->days as $d ) : ?>
                                    <th class="em-th"><span><?php echo $d; ?></span></th>
								<?php endforeach; ?>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <!-- boucle pour chaque jour -->
								<?php foreach ( $days

								as $day => $week ) : ?>
								<?php $time = DateTime::createFromFormat( 'd/m/Y', $day . '/' . $month . '/' . $year ); ?>
								<?php if ( $day == 1 ) : ?>
									<?php if ( $week != 1 ) : ?>
                                        <td colspan="<?php echo $week - 1; ?>"></td>
									<?php endif; ?>
								<?php endif; ?>
                                <td class="em-td <?php if ( $time == $current ) {
									echo 'em-calendar-current-day';
								} ?>">
                                    <div class="<?php if ( $time == $current ) {
										echo 'em-calendar-boxday-current';
									} else {
										echo 'em-calendar-boxday';
									} ?>">
                                        <div class="em-calendar-day">
                                            <span><?php echo $day; ?></span>
                                        </div>
										<?php if ( isset( $events ) ) : ?>
											<?php foreach ( $events as $event ) : ?>
												<?php if ( $event['start'] <= $time && $event['end'] >= $time ) : ?>
                                                    <a class="em-a" href="<?php echo $event['url']; ?>"
                                                       title="<?php echo $event['full_title']; ?>">
                                                        <div class="em-calendar-event hvr-back-pulse <?php if ( $time == $current ) {
															echo 'em-calendar-current';
														} ?>">
                                                            <span class="em-calendar-title-event"><?php echo $event['title']; ?></span>
															<?php if ( ! $event['all_day'] ) : ?>
                                                                <span class="em-calendar-hour em-badge-list"><?php echo $event['hour']; ?></span>
															<?php endif; ?>
                                                        </div>
                                                    </a>
												<?php endif; ?>
											<?php endforeach; ?>
										<?php endif; ?>
                                    </div>
                                </td>
								<?php if ( $week == 7 ) : ?>
                            </tr>
                            <tr>
								<?php endif; ?>
								<?php endforeach; ?>
								<?php if ( $end != 7 ) : ?>
                                    <td colspan="<?php echo 7 - $end; ?>"></td>
								<?php endif; ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
				<?php endforeach; ?>
            </div>
		<?php endforeach; ?>
    </div>
</div>
<script
        src="https://code.jquery.com/jquery-3.2.1.min.js"
        integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
        crossorigin="anonymous"></script>
<?php get_footer(); ?>
