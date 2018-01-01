<?php

/**
 * Classe regroupant les méthodes pour l'affichage de la page d'aide
 */
class SuperEventManagerHelp {
	/**
	 * Constructeur de la classe SuperEventManagerHelp.
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
		add_submenu_page( 'edit.php?post_type=event', 'Aide', 'Aide', sem_get_setting( 'capability' ), 'sem-help', array(
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
		sem_get_view( 'sem-html-help' );
	}
}

new SuperEventManagerHelp();
?>
