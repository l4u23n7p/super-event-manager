<?php

/**
 * Classe regroupant les méthodes pour l'affichage de la page d'aide
 */
class EventManagerHelp {
	/**
	 * Constructeur de la classe EventManagerHelp.
	 * Ajout du sous-menu à l'administration wordpress
	 * @method __construct
	 */
	function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_submenu' ), 99 );
	}

	/**
	 * Cette fonction ajoute le sous-menu 'Aide'.
	 * @method admin_submenu
	 */
	function admin_submenu() {
		add_submenu_page( 'edit.php?post_type=event', 'Aide', 'Aide', event_manager_get_setting( 'capability' ), 'event-manager-help', array(
			$this,
			'html'
		) );
	}

	/**
	 * Cette fonction inclus le template à afficher
	 * @method html
	 * @return [type] [description]
	 */
	function html() {
		event_manager_get_view( 'event-manager-html-help' );
	}
}

new EventManagerHelp();
?>
