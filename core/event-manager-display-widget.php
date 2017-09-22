<?php

/**
 * Classe regroupant les méthodes nécessaires à l'affichage du widget
 */
class EventManagerDisplayWidget {

	/**
	 * Constructeur de la classe EventManagerDisplayWidget
	 * Ajout du shortcode pour afficher le widget
	 * @method __construct
	 */
	function __construct() {
		add_shortcode( 'listevents', array( $this, 'list_event' ) );
		add_filter( 'widget_text', 'do_shortcode' );
	}

	/**
	 * Fonction affichant le widget
	 * @method list_event
	 * @return string   $string   Le code html du widget
	 */
	function list_event() {
		$display = get_event_settings( 'display' );

		$the_query = new WP_Query( array(
				'post_type'      => 'event',
				'posts_per_page' => $display['number_event_widget'],
				'order'          => 'ASC',
				'orderby'        => 'meta_value',
				'meta_key'       => 'event_date_start',
				'meta_query'     => array(
					array(
						'key'     => 'event_date_end',
						'value'   => current_time( 'Ymd' ),
						'type'    => 'DATETIME',
						'compare' => '>'
					)
				),
			)
		);
		$string    = '<div class="widget-event-list"><h2 class="em-h2">Dernier ' . get_archive_event_title() . '</h2><ul class="event-list">';
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();

				if ( get_field( 'event_cancel' ) ) {
					$string .= '<li class="hvr-grow em-cancel-event"><a href="' . get_the_permalink() . '">';
				} else {
					$string .= '<li class="hvr-grow"><a href="' . get_the_permalink() . '">';
				}
				$string .= '<div class="event"><div class="col-md-3 event-date-list">';
				$string .= '<div class="event-day-list"><span> ' . get_event_day( get_field( 'event_date_start' ) ) . '</span></div>';
				$string .= '<div class="event-month-list"><span> ' . get_event_month( get_field( 'event_date_start' ) ) . '</span></div></div>';
				$string .= '<div class="col-md-9 flex-list event-content-list">';
				$string .= '<div class="align-vertical-flex event-title-list"><div class="em-no-padding col-md-8"><h4>' . get_the_title() . '</h4></div>';
				if ( get_post_status( get_the_ID() ) == 'ongoing' ) {
					$string .= '<div class="col-md-4 em-badge-list em-status"><span>Événement en cours</span></div>';
				}
				if ( get_field( 'event_cancel' ) ) {
					$string .= '<div class="col-md-4 em-badge-list em-cancel"><span>Événement Annulé</span></div>';
				}
				$string .= '</div><div class="align-vertical-flex event-hour-list"><i class="fa fa-clock-o" aria-hidden="true"></i>';
				if ( is_the_same_date( get_field( 'event_date_start' ), get_field( 'event_date_end' ) ) ) {
					$string .= ' <span>' . get_event_hour( get_field( 'event_date_start' ) ) . ' - ' . get_event_hour( get_field( 'event_date_end' ) ) . '</span></div>';
				} else {
					$string .= ' <span>' . get_event_date( get_field( 'event_date_start' ) ) . ' ' . get_event_hour( get_field( 'event_date_start' ) ) . ' - ' . get_event_date( get_field( 'event_date_end' ) ) . ' ' . get_event_hour( get_field( 'event_date_end' ) ) . '</span></div>';
				}
				$string .= '<div class="align-vertical-flex event-place-list"><i class="fa fa-map-marker" aria-hidden="true"></i>';
				$string .= '<span> ' . get_field( 'event_place' ) . '</span></div>';
				$string .= '<div class="align-vertical-flex event-text-list"><p>' . get_field( 'event_description' ) . '</p>';
				$string .= '</div></div>';
				$string .= '</div></a></li>';
				/* Restauration des données */
				wp_reset_postdata();
			}
		} else {
			// Aucun article disponible
			$string .= '<li class="items">Aucun événement</li>';
		}
		$string .= '</ul><a class="em-link-all" href="' . site_url() . '/' . get_event_slug() . '/">Voir tout</a></div>';

		return $string;
	}
}

new EventManagerDisplayWidget();


?>
