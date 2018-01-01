<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'SuperEventManagerAdmin' ) ) :

	/**
	 * Classe regroupant les méthodes pour l'affichage de l'administration
	 * principale du plugin
	 */
	class SuperEventManagerAdmin {

		/**
		 * Constructeur de la classe SuperEventManagerAdmin
		 * Ajout du menu à l'administration wordpress
		 * Ajout des styles de l'administration
		 * @method __construct
		 */
		function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Déclaration du menu d'administration du plugin
		 * @method admin_menu
		 * @return [type]     [description]
		 */
		function admin_menu() {
			if ( ! sem_get_setting( 'show_admin' ) ) {
				return;
			}

			$slug = 'edit.php?post_type=event';
			$cap  = sem_get_setting( 'capability' );

			add_menu_page( 'Événements', 'Événements', $cap, $slug, false, 'dashicons-megaphone', '10' );

			add_submenu_page( $slug, '', 'Tous les événements', $cap, $slug );
			add_submenu_page( $slug, 'Ajouter un événement', 'Ajouter un événement', $cap, 'post-new.php?post_type=event' );
			add_submenu_page( $slug, 'Organiser les événements', 'Organiser les événements', $cap, 'edit-tags.php?taxonomy=event-category&post_type=event' );
		}

		/**
		 * Ajout des styles de l'administration
		 * @method admin_enqueue_scripts
		 */
		function admin_enqueue_scripts() {
			wp_enqueue_script( 'bootstrap-js' );
			wp_enqueue_style( 'bootstrap-css' );
			wp_enqueue_style( 'sem-admin-design' );
		}
	}

	// initialize
	sem()->admin = new SuperEventManagerAdmin();

endif; // class_exists check

?>
