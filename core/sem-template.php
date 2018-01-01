<?php

/**
 * Classe regroupant les méthodes nécessaires au changement de template
 */
class SuperEventManagerTemplate {

	/**
	 * Constructeur de la classe SuperEventManagerTemplate
	 * Ajout du filtre pour réécrire le template choisis par wordpress
	 * @method __construct
	 */
	function __construct() {
		add_filter( 'template_include', array( $this, 'sem_template' ), 99 );
	}


	/**
	 * Fonction renvoyant le template à utiliser
	 * @method sem_template
	 *
	 * @param  string $template Le template par défaut
	 *
	 * @return string           Le nouveau template
	 */
	function sem_template( $template ) {
		$event_template = get_event_settings( 'template' );

		if ( is_post_type_archive( 'event' ) ) {
			$template = sem_get_path( 'core/template/archive-event-' . $event_template['archive_template']['value'] . '.php' );
		}

		if ( is_singular( 'event' ) ) {
			$template = sem_get_path( 'core/template/single-event-' . $event_template['single_template']['value'] . '.php' );
		}

		return $template;
	}
}

new SuperEventManagerTemplate();
?>
