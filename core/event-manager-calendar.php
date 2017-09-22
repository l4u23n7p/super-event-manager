<?php

/**
 * Classe regroupant les méthodes nécessaires à l'affichage du calendrier
 */
class EventManagerCalendar {
	/**
	 * Constructeur de la classe EventManagerCalendar
	 * Vide pour le moment
	 * @method __construct
	 */
	function __construct() {

	}

	var $days = array( 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche' );
	var $months = array(
		'Janvier',
		'Février',
		'Mars',
		'Avril',
		'Mai',
		'Juin',
		'Juillet',
		'Août',
		'Septembre',
		'Octobre',
		'Novembre',
		'Décembre'
	);

	/**
	 * Fonction retournant les données du calendrier
	 * @method getAll
	 *
	 * @param  int $year Année que doit afficher le calendrier
	 *
	 * @return array $calendar   Tableau contenant les années, mois, jours
	 */
	function getAll( $year ) {
		$calendar = array();

		$date = new DateTime( ( $year - 1 ) . '-01-01' );

		while ( $date->format( 'Y' ) <= $year + 1 ) {

			$y = $date->format( 'Y' );
			$m = $date->format( 'n' );
			$d = $date->format( 'j' );
			$w = $date->format( 'N' );

			$calendar[ $y ][ $m ][ $d ] = $w;

			$date->add( new DateInterval( 'P1D' ) );

		}

		debug_console( $calendar );

		return $calendar;
	}

	/**
	 * Fonction retournant les événements à afficher sur le calendrier
	 * @method getEvent
	 * @return array   $events Tableau contenant les événements
	 */
	function getEvent() {

		$events = array();

		$the_query = new WP_Query( array(
				'post_type' => 'event',
				'order'     => 'ASC',
				'orderby'   => 'meta_value',
				'meta_key'  => 'event_date_start',
			)
		);

		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				$date_start = get_explode_date( get_field( 'event_date_start' ) );

				$date_end = get_explode_date( get_field( 'event_date_end' ) );

				$date1 = $date_start['date']['day'] . '/' . $date_start['date']['month'] . '/' . $date_start['date']['year'];
				$date2 = $date_end['date']['day'] . '/' . $date_end['date']['month'] . '/' . $date_end['date']['year'];
				$hour1 = $date_start['hours']['hour'] . ':' . $date_start['hours']['min'];
				$hour2 = $date_end['hours']['hour'] . ':' . $date_end['hours']['min'];

				$complete = false;

				if ( $hour1 == $hour2 ) {
					$complete = true;
				}

				$start = DateTime::createFromFormat( 'd/m/Y', $date1 );
				$end   = DateTime::createFromFormat( 'd/m/Y', $date2 );

				$title = get_the_title();

				if ( strlen( $title ) > 15 ) {
					$title = utf8_encode( substr_replace( utf8_decode( $title ), ' ...', 15 ) );
				}

				$events[ get_the_ID() ] = array(
					'start'      => $start,
					'end'        => $end,
					'title'      => $title,
					'full_title' => get_the_title(),
					'url'        => get_the_permalink(),
					'hour'       => $hour1 . ' - ' . $hour2,
					'all_day'    => $complete,
				);

			}
		}
		wp_reset_postdata();

		return $events;
	}

}

?>
