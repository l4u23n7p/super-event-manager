<?php

/**
 * Function permettant l'affichage en console d'une chaîne de caractère, d'un tableau ou d'un objet.
 * @method debug_console
 *
 * @param  string|array|object $args Donnée à afficher
 * @param  string $type Type du message à afficher, par défaut 'log' Type accepté : log,info,warn,error.
 *
 * @return [type]              [description]
 */
function debug_console( $args, $type = 'log' ) {

	$enable_log = get_event_settings( 'display' );

	if ( $enable_log['enable_log'] ) {
		if ( is_array( $args ) || is_object( $args ) ) {
			echo '<script>console.' . $type . '(' . json_encode( $args ) . ')</script>';

			return;
		}
		if ( is_bool( $args ) ) {
			switch ( $args ) {
				case true:
					$args = 'true';
					break;

				case false:
					$args = 'false';
					break;
				default:
					# code...
					break;
			}
		}
		echo '<script>console.' . $type . '("' . $args . '")</script>';
	}
}

/**
 * Fonction parsant une date donné en argument dans un tableau
 * @method get_explode_date
 *
 * @param  string $entry_date Date à parser (jj/mm/aaaa hh:mm)
 *
 * @return array   $explode_date  Tableau contenant la date parsé
 */
function get_explode_date( $entry_date ) {
	$raw_data = explode( " ", $entry_date );

	$date_explode = explode( "/", $raw_data[0] );

	$hour_explode = explode( ":", $raw_data[1] );

	$explode_date = array(
		'date'  => array(
			'day'   => $date_explode[0],
			'month' => intval( $date_explode[1] ),
			'year'  => $date_explode[2],
		),
		'hours' => array(
			'hour' => $hour_explode[0],
			'min'  => $hour_explode[1],
		)
	);

	return $explode_date;
}

/**
 * Fonction parsant une date donné en argument dans un tableau avec le mois en alphanumérique
 * @method get_explode_date_alpha
 *
 * @param  string $entry_date Date à parser (jj/mm/aaaa hh:mm)
 *
 * @return array   $explode_date  Tableau contenant la date parsé
 */
function get_explode_date_alpha( $entry_date ) {
	$explode_date = get_explode_date( $entry_date );

	$month = [
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
	];

	$explode_date['date']['month'] = $month[ ( $explode_date['date']['month'] - 1 ) ];

	return $explode_date;
}

/**
 * Fonction retournant le jour de la date donné en argument
 * @method get_event_day
 *
 * @param  string $entry_date Date d'entrée (jj/mm/aaaa hh:mm)
 *
 * @return int    $get_day    Jour de la date
 */
function get_event_day( $entry_date ) {
	$data = get_explode_date( $entry_date );

	$get_day = $data['date']['day'];

	return $get_day;
}

/**
 * Fonction retournant le mois de la date donné en argument
 * @method get_event_month
 *
 * @param  string $entry_date Date d'entrée (jj/mm/aaaa hh:mm)
 *
 * @return string $get_day    Mois de la date
 */
function get_event_month( $entry_date ) {
	$data = get_explode_date_alpha( $entry_date );

	$get_month = $data['date']['month'];

	return $get_month;
}

/**
 * Fonction retournant l'année' de la date donné en argument
 * @method get_event_year
 *
 * @param  string $entry_date Date d'entrée (jj/mm/aaaa hh:mm)
 *
 * @return int    $get_day    Année de la date
 */
function get_event_year( $entry_date ) {
	$data = get_explode_date( $entry_date );

	$get_year = $data['date']['year'];

	return $get_year;
}

/**
 * Fonction retournant le jour de la date donné en argument
 * @method get_event_date
 *
 * @param  string $entry_date Date d'entrée (jj/mm/aaaa hh:mm)
 *
 * @return string $get_day    Date formaté au format JJ Mois AAAA ex:'12 septembre 2017'
 */
function get_event_date( $entry_date ) {
	$get_date = get_event_day( $entry_date ) . ' ' . get_event_month( $entry_date ) . ' ' . get_event_year( $entry_date );

	return $get_date;
}

/**
 * Fonction affichant la date au format JJ Mois AAAA ex:'12 septembre 2017'
 * @method the_event_date
 *
 * @param  string $entry_date Date d'entrée (jj/mm/aaaa hh:mm)
 *
 * @return
 */
function the_event_date( $entry_date ) {
	$the_date = get_event_date( $entry_date );

	echo $the_date;
}

/**
 * Function retournant l'heure de la date donnée en argument
 * @method get_event_hour
 *
 * @param  string $entry_date Date d'entrée (jj/mm/aaaa hh:mm)
 *
 * @return string $get_hour   Heure de la date au format hh:mm
 */
function get_event_hour( $entry_date ) {
	$data = get_explode_date( $entry_date );

	$get_hour = $data['hours']['hour'] . ':' . $data['hours']['min'];

	return $get_hour;
}

/**
 * Fonction affichant l'heure au format hh:mm
 * @method the_event_hour
 *
 * @param  string $entry_date Date d'entrée (jj/mm/aaaa hh:mm)
 *
 * @return
 */
function the_event_hour( $entry_date ) {
	$the_hour = get_event_hour( $entry_date );

	echo $the_hour;
}

/**
 * Fonction retournant un tableau contenant tout les paramètres du plugin affiché sur la page 'Paramètres'
 * @method get_event_settings
 * @return array $meta Tableau contenant les paramètres
 */
function get_event_settings( $name ) {

	$settings = get_field( $name, 'options' );

	return $settings;
}

/**
 * Fonction retournant le slug utilisé par le custom_post_type event
 * @method get_event_slug
 * @return string $slug Le slug du custom_post_type event
 */
function get_event_slug() {

	$slug = get_event_settings( 'custom' );

	$slug = $slug['archive_page']['archive_slug'];

	return $slug;
}

/**
 * Fonction affichant le titre de la page d'archive
 * @method get_archive_event_title
 * @return
 */
function get_archive_event_title() {

	$title = get_event_settings( 'custom' );

	$title = $title['archive_page']['archive_title'];

	return $title;
}


/**
 * Fonction affichant le titre de la page d'archive
 * @method the_archive_event_title
 * @return
 */
function the_archive_event_title() {

	$title = get_archive_event_title();

	echo $title;
}

/**
 * Fonction testant si deux date sont identique
 * @method is_the_same_date
 *
 * @param  string $date1 Première Date (jj/mm/aaaa hh:mm)
 * @param  string $date2 Seconde Date (jj/mm/aaaa hh:mm)
 *
 * @return boolean                 Retourne 'true' si identique, 'false' dans le cas contraire
 */
function is_the_same_date( $date1, $date2 ) {
	$date1 = get_explode_date( $date1 );
	$date2 = get_explode_date( $date2 );

	if ( $date1['date']['day'] . ' ' . $date1['date']['month'] . ' ' . $date1['date']['year'] == $date2['date']['day'] . ' ' . $date2['date']['month'] . ' ' . $date2['date']['year'] ) {
		return true;
	}

	return false;
}

/**
 * Fonction testant si un événement est passé
 * @method is_past_event
 *
 * @param  object $event_date_end Date de fin de l'événement
 * @param  object $current_date Date courante
 *
 * @return boolean                       Retourne 'true' si l'événement est passé, 'false' dans le cas contraire
 */
function is_past_event( $event_date_end, $current_date ) {
	if ( $event_date_end < $current_date ) {
		return true;
	}

	return false;
}

/**
 * Fonction testant si un événement est en cours
 * @method is_ongoing_event
 *
 * @param  object $event_date_start Date de début de l'événement
 * @param  object $event_date_end Date de fin de l'événement
 * @param  object $current_date Date courante
 *
 * @return boolean                            Retourne 'true' si l'événement est en cours, 'false' dans le cas contraire
 */
function is_ongoing_event( $event_date_start, $event_date_end, $current_date ) {
	if ( ( $event_date_start <= $current_date ) && ( $event_date_end >= $current_date ) ) {
		return true;
	}

	return false;
}

/**
 * Fonction testant si un événement est à venir
 * @method is_upcoming_event
 *
 * @param  object $event_date_start Date de début de l'événement
 * @param  object $current_date Date courante
 *
 * @return boolean                         Retourne 'true' si l'événement est à venir, 'false' dans le cas contraire
 */
function is_upcoming_event( $event_date_start, $current_date ) {
	if ( $event_date_start > $current_date ) {
		return true;
	}

	return false;
}

/**
 * Fonction permettant la mise à jour du status de l'événement passé en argument
 * @method update_event_status
 *
 * @param  int $id ID de l'événement
 *
 * @return
 */
function update_event_status( $id ) {

	if ( get_post_type( $id ) == 'event' ) {


		$start_date   = DateTime::createFromFormat( 'd/m/Y H:i', get_field( 'event_date_start', $id ) );
		$end_date     = DateTime::createFromFormat( 'd/m/Y H:i', get_field( 'event_date_end', $id ) );
		$current_date = DateTime::createFromFormat( 'd/m/Y H:i', current_time( 'd/m/Y H:i' ) );

		$type   = get_post_type( $id );
		$status = get_post_status( $id );

		if ( $type = 'event' ) {
			switch ( $status ) {
				case 'trash':
					debug_console( 'trash' );
					$post_status = 'trash';
					break;

				default:
					if ( is_past_event( $end_date, $current_date ) ) {
						debug_console( 'passé' );
						$post_status = 'past';
					}

					if ( is_ongoing_event( $start_date, $end_date, $current_date ) ) {
						debug_console( 'en cours' );
						$post_status = 'ongoing';
					}

					if ( is_upcoming_event( $start_date, $current_date ) ) {
						debug_console( 'a venir' );
						$post_status = 'upcoming';
					}
					break;
			}

			$event_post = array(
				'ID'          => $id,
				'post_status' => $post_status,
			);

			wp_update_post( $event_post );

		}

	}
}

function update_all_event_status() {

	$event = new WP_Query( array(
			'post_type' => 'event',
		)
	);

	if ( $event->have_posts() ) {
		while ( $event->have_posts() ) {
			$event->the_post();
			update_event_status( get_the_ID() );
		}
	}
	wp_reset_postdata();
}

// Mise à jour automatique du status lors de la publication d'un événement
add_action( 'shutdown', 'update_all_event_status' );


?>
