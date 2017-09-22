<?php

/**
 * Classe regroupant les méthodes pour l'affichage de la page de gestion du
 * slider
 */
class EventManagerSlider {

	/**
	 * Constructeur de la classe EventManagerSettings
	 * Ajout du sous-menu à l'administration wordpress.
	 * Ajout des champs acf liés à cette page.
	 * @method __construct
	 */
	function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_submenu' ), 79 );

	}

	/**
	 * Déclaration du sous-menu 'Carroussel'
	 * @method admin_submenu
	 * @return [type]        [description]
	 */
	function admin_submenu() {
		acf_add_options_sub_page( array(
			'page_title'  => 'Carroussel des événements importants',
			'menu_title'  => 'Carroussel',
			'menu_slug'   => 'event-manager-slider',
			'capability'  => event_manager_get_setting( 'capability' ),
			'parent_slug' => 'edit.php?post_type=event',
		) );
	}

}


// initialize
new EventManagerSlider();

?>
