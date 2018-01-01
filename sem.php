<?php
/*
Plugin Name: Super Event Manager
Plugin URI:
Description: Gérer vos événements avec ce plugin.
Author: Laurent Panek
Author URI: https://laurentpanek.me/
Version: 1.0.1
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'SuperEventManager' ) ) :

	/**
	 * Classe principale du plugin
	 */
	class SuperEventManager {
		var $version = '1.0.1';

		var $settings = array();

		/**
		 * Constructeur de la classe EventManager
		 * Ajout des fonctions lors de l'activation/désactivation du plugin
		 * @method __construct
		 */
		function __construct() {
			add_action( 'admin_init', array( $this, 'load_plugin' ) );
			add_action( 'activated_plugin', array( $this, 'plugin_activation' ) );
			add_action( 'deactivate_plugin', array( $this, 'plugin_deactivation' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		}

		/**
		 * Fonction permettant le chargement du plugin
		 * @method load_plugin
		 * @return
		 */
		function load_plugin() {
			if ( get_option( 'sem_load' ) == 'can_load' ) {
				$this->initialize();
			} else if ( is_admin() ) {
				update_option( 'sem_admin_notices', $this->notice_warning_acf() );
			}
		}

		/**
		 * Fonction déclenché lors de l'activation du plugin
		 * @method plugin_activation
		 *
		 * @param  int $network_wide Argument de wordpress
		 *
		 * @return
		 */
		function plugin_activation( $network_wide ) {
			if ( ! function_exists( 'get_plugins' ) ) {
				require_once ABSPATH . 'wp-admin/includes/plugin.php';
			}
			$all_plugins = get_plugins();
			$re          = '/advanced-custom-fields-pro(.*)/';
			foreach ( $all_plugins as $name_plugin => $all_plugin ) {
				if ( preg_match( $re, $name_plugin ) ) {
					$acf = $name_plugin;
					break;
				}
			}
			if ( is_plugin_inactive( $acf ) && get_option( 'acf_version' ) < '5.6.0' ) {
				update_option( 'sem_admin_notices', $this->notice_warning_acf() );
			} else {
				update_option( 'sem_load', 'can_load' );
				update_option( 'sem_admin_notices', $this->notice_install() );
				if ( ! get_option( 'sem_flag' ) ) {
					update_option( 'sem_flag', true );
				}
				if ( ! get_option( 'sem_default_settings' ) ) {
					update_option( 'sem_default_settings', true );
				}
				$this->load_plugin();

			}
		}

		/**
		 * Fonction déclenché à la désactivation du plugin_deactivation
		 * @method plugin_deactivation
		 *
		 * @param  int $network_wide Argument de wordpress
		 *
		 * @return
		 */
		function plugin_deactivation( $network_wide ) {
			delete_option( 'sem_load' );
			unregister_post_type( 'event' );
			$this->delete_acf_fields();
			$this->deregister_style();
			flush_rewrite_rules();
		}

		/**
		 * Fonction supprimant les champs acf
		 * @method delete_acf_fields
		 * @return
		 */
		function delete_acf_fields() {
			if ( function_exists( 'acf_remove_local_field_group' ) ) {
				acf_remove_local_field_group( 'group_sem' );
				acf_remove_local_field_group( 'group_sem_slider' );
				acf_remove_local_field_group( 'group_sem_settings' );
			}
		}

		/**
		 * Fonction supprimant les fichier de style utilisé par le plugin
		 * @method deregister_style
		 * @return
		 */
		function deregister_style() {
			wp_deregister_style( 'sem-default-design' );
			wp_deregister_style( 'sem-custom-design' );
			wp_deregister_style( 'font-awesome' );
			wp_deregister_style( 'bootstrap-css' );
			wp_deregister_script( 'bootstrap-js' );
			wp_deregister_script( 'sem-custom-main' );
			wp_deregister_script( 'sem-calendar' );
		}

		/**
		 * Fonction affichant une notification dans l'administration de wordpress
		 * @method admin_notices
		 * @return
		 */
		function admin_notices() {
			$notices = get_option( 'sem_admin_notices' );
			if ( $notices ) {
				echo $notices;
			}
			delete_option( 'sem_admin_notices', '' );
		}

		/**
		 * Fonction retournant le message d'erreur lié à la version d'acf
		 * @method notice_warning_acf
		 * @return string             Le message d'erreur
		 */
		function notice_warning_acf() {

			$message = '
	<div class="notice notice-error is-dismissible">
		<p>Erreur Super Event Manager ! Advanced Custom Fields PRO 5.6.0 (ou supérieur) est requise à l\'éxécution de ce plugin.</p>
	</div>';

			return $message;
		}

		/**
		 * Fonction retournant le message de confirmation
		 * @method notice_install
		 * @return string         Le message de confirmation
		 */
		function notice_install() {
			$message = '
	<div class="notice notice-success is-dismissible">
		<p>Vous pouvez utilisé Super Event Manager.</p>
	</div>';

			return $message;
		}

		function default_settings() {
			if ( get_option( 'sem_default_settings' ) ) {

				$default_value = array(
					'template' => array(
						'archive_template'        => array(
							'value' => 'default',
							'label' => 'Défaut',
						),
						'archive_template_custom' => '',
						'single_template'         => array(
							'value' => 'default',
							'label' => 'Défaut',
						),
						'single_template_custom'  => '',
					),
					'display'  => array(
						'number_event_slider' => 3,
						'number_event_widget' => 3,
						'enable_log'          => 0,
					),
					'custom'   => array(
						'archive_page' => array(
							'archive_title' => 'Evenements',
							'archive_slug'  => 'evenements',
						),
						'custom_css'   => '',
						'custom_js'    => '',
					),
				);


				update_row('template', 1, $default_value['template'], 'options');
				update_row('display', 1, $default_value['display'], 'options');
				update_row('custom', 1, $default_value['custom'], 'options');


				delete_option( 'sem_default_settings' );

			}
		}

		function flush_rules_maybe() {
			if ( get_option( 'sem_flag' ) ) {
				flush_rewrite_rules();
				delete_option( 'sem_flag' );
			}
		}

		/**
		 * Fonction d'initialisation du plugin
		 * Cette fonction est la méthode principale de la classe
		 * @method initialize
		 * @return
		 */
		function initialize() {
			// vars plugin
			$this->settings = array(
				// basic
				'name'     => __( 'Super Event Manager', 'SuperEventManager' ),
				'version'  => $this->version,

				// urls
				'file'     => __FILE__,
				'basename' => plugin_basename( __FILE__ ),
				'path'     => plugin_dir_path( __FILE__ ),
				'dir'      => plugin_dir_url( __FILE__ ),

				'capability' => 'manage_options',
				'show_ui'    => true,
				'show_admin' => true,
			);

			$this->define( 'SEM_PATH', $this->settings['path'] );

			include_once( SEM_PATH . 'core/sem-helpers.php' );

			sem_include( 'core/sem-api.php' );
			sem_include( 'core/sem-display-widget.php' );
			sem_include( 'core/sem-display-slider.php' );
			sem_include( 'core/sem-template.php' );
			sem_include( 'core/sem-calendar.php' );

			// admin
			if ( is_admin() ) {

				// include admin
				sem_include( 'core/sem-custom.php' );
				sem_include( 'admin/sem-admin.php' );
				sem_include( 'admin/sem-admin-slider.php' );
				sem_include( 'admin/sem-admin-settings.php' );
				sem_include( 'admin/sem-admin-help.php' );
			}

			// actions
			add_action( 'init', array( $this, 'register_post_types' ), 10 );
			add_action( 'init', array( $this, 'register_post_taxonomy' ), 10 );
			add_action( 'init', array( $this, 'register_post_status' ), 10 );
			add_action( 'init', array( $this, 'register_assets' ), 10 );
			add_action( 'init', array( $this, 'flush_rules_maybe' ), 20 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );

			add_filter( 'manage_event_posts_columns', array( $this, 'set_custom_edit_event_columns' ) );
			add_action( 'manage_event_posts_custom_column', array( $this, 'custom_event_column' ), 10, 2 );
			add_filter( "manage_edit-event_sortable_columns", array( $this, 'custom_sortable_column' ) );

			add_action( 'acf/init', array( $this, 'create_acf_fields_event' ), 10 );
			add_action( 'acf/init', array( $this, 'create_acf_fields' ), 10 );
			add_action( 'acf/init', array( $this, 'default_settings' ), 20 );

		}


		function create_acf_fields() {

			if ( function_exists( 'acf_add_local_field_group' ) ) {

				acf_add_local_field_group( array(
					'key'                   => 'group_sem_slider',
					'title'                 => 'Event Slider',
					'fields'                => array(
						array(
							'key'               => 'field_slider',
							'label'             => 'Carrousel',
							'name'              => 'slider',
							'type'              => 'repeater',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'collapsed'         => 'field_slider_image',
							'min'               => 0,
							'max'               => 0,
							'layout'            => 'block',
							'button_label'      => '',
							'sub_fields'        => array(
								array(
									'key'               => 'field_slider_image',
									'label'             => 'Image',
									'name'              => 'image',
									'type'              => 'image',
									'instructions'      => '',
									'required'          => 1,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'return_format'     => 'array',
									'preview_size'      => 'thumbnail',
									'library'           => 'all',
									'min_width'         => '',
									'min_height'        => '',
									'min_size'          => '',
									'max_width'         => '',
									'max_height'        => '',
									'max_size'          => '',
									'mime_types'        => '',
								),
								array(
									'key'               => 'field_slider_head',
									'label'             => 'Titre',
									'name'              => 'head',
									'type'              => 'text',
									'instructions'      => 'Ajouter un titre (optionnel)',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'default_value'     => '',
									'placeholder'       => '',
									'prepend'           => '',
									'append'            => '',
									'maxlength'         => 60,
								),
								array(
									'key'               => 'field_slider_content',
									'label'             => 'Description',
									'name'              => 'content',
									'type'              => 'textarea',
									'instructions'      => 'Ajouter une description (optionnel)',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'default_value'     => '',
									'placeholder'       => '',
									'maxlength'         => '',
									'rows'              => 4,
									'new_lines'         => '',
								),
								array(
									'key'               => 'field_slider_advanced_settings',
									'label'             => 'Réglages Avancés',
									'name'              => 'advanced_settings',
									'type'              => 'true_false',
									'instructions'      => '',
									'required'          => 0,
									'conditional_logic' => 0,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'message'           => '',
									'default_value'     => 0,
									'ui'                => 1,
									'ui_on_text'        => '',
									'ui_off_text'       => '',
								),
								array(
									'key'               => 'field_slider_date',
									'label'             => 'Date',
									'name'              => 'date',
									'type'              => 'text',
									'instructions'      => 'Ce champ se remplit automatiquement s\'il le faut. Ne rien toucher !',
									'required'          => 0,
									'conditional_logic' => array(
										array(
											array(
												'field'    => 'field_slider_advanced_settings',
												'operator' => '==',
												'value'    => '1',
											),
										),
									),
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'default_value'     => '',
									'placeholder'       => '',
									'prepend'           => '',
									'append'            => '',
									'maxlength'         => '',
								),
								array(
									'key'               => 'field_slider_place',
									'label'             => 'Lieu',
									'name'              => 'place',
									'type'              => 'text',
									'instructions'      => 'Ce champ se remplit automatiquement s\'il le faut. Ne rien toucher !',
									'required'          => 0,
									'conditional_logic' => array(
										array(
											array(
												'field'    => 'field_slider_advanced_settings',
												'operator' => '==',
												'value'    => '1',
											),
										),
									),
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'default_value'     => '',
									'placeholder'       => '',
									'prepend'           => '',
									'append'            => '',
									'maxlength'         => '',
								),
								array(
									'key'               => 'field_slider_event_id',
									'label'             => 'Identifiant',
									'name'              => 'event_id',
									'type'              => 'number',
									'instructions'      => 'Ce champ se remplit automatiquement s\'il le faut. Ne rien toucher !',
									'required'          => 0,
									'conditional_logic' => array(
										array(
											array(
												'field'    => 'field_slider_advanced_settings',
												'operator' => '==',
												'value'    => '1',
											),
										),
									),
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'default_value'     => '',
									'placeholder'       => '',
									'prepend'           => '',
									'append'            => '',
									'min'               => '',
									'max'               => '',
									'step'              => '',
								),
							),
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'options_page',
								'operator' => '==',
								'value'    => 'sem-slider',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'acf_after_title',
					'style'                 => 'seamless',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
					'active'                => 1,
					'description'           => '',
				) );

				acf_add_local_field_group( array(
					'key'                   => 'group_sem_settings',
					'title'                 => 'Paramètres',
					'fields'                => array(
						array(
							'key' => 'field_tab_general',
							'label' => 'General',
							'name' => '',
							'type' => 'tab',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'placement' => 'top',
							'endpoint' => 0,
						),
						array(
							'key'               => 'field_settings_template',
							'label'             => 'Template',
							'name'              => 'template',
							'type'              => 'group',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'layout'            => 'row',
							'sub_fields'        => array(
								array(
									'ID'                => 25,
									'key'               => 'field_settings_template_archive',
									'label'             => 'Template Archive',
									'name'              => 'archive_template',
									'prefix'            => '',
									'type'              => 'radio',
									'value'             => null,
									'menu_order'        => 0,
									'instructions'      => 'Sélectionner un template',
									'required'          => 0,
									'id'                => '',
									'class'             => '',
									'conditional_logic' => 0,
									'parent'            => 24,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'_name'             => 'archive_template',
									'_prepare'          => 0,
									'_valid'            => 1,
									'choices'           => array(
										'default'  => 'Défaut',
										'calendar' => 'Calendrier',
										'custom'   => 'Personaliser',
									),
									'allow_null'        => 0,
									'other_choice'      => 0,
									'save_other_choice' => 0,
									'default_value'     => 'default',
									'layout'            => 'horizontal',
									'return_format'     => 'array',
								),
								array(
									'ID'                => 26,
									'key'               => 'field_settings_template_archive_custom',
									'label'             => 'Votre Template',
									'name'              => 'archive_template_custom',
									'prefix'            => '',
									'type'              => 'textarea',
									'value'             => null,
									'menu_order'        => 1,
									'instructions'      => 'Ajouter votre template dans ce champ ( PHP et/ou HTML)',
									'required'          => 0,
									'id'                => '',
									'class'             => '',
									'conditional_logic' => array(
										array(
											array(
												'field'    => 'field_settings_template_archive',
												'operator' => '==',
												'value'    => 'custom',
											),
										),
									),
									'parent'            => 24,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'_name'             => 'archive_template_custom',
									'_prepare'          => 0,
									'_valid'            => 1,
									'default_value'     => '',
									'placeholder'       => '',
									'maxlength'         => '',
									'rows'              => '',
									'new_lines'         => '',
								),
								array(
									'ID'                => 78,
									'key'               => 'field_settings_template_single',
									'label'             => 'Template Single',
									'name'              => 'single_template',
									'prefix'            => '',
									'type'              => 'radio',
									'value'             => null,
									'menu_order'        => 2,
									'instructions'      => 'Sélectionner un template',
									'required'          => 0,
									'id'                => '',
									'class'             => '',
									'conditional_logic' => 0,
									'parent'            => 24,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'_name'             => 'single_template',
									'_prepare'          => 0,
									'_valid'            => 1,
									'choices'           => array(
										'default' => 'Défaut',
										'custom'  => 'Personaliser',
									),
									'allow_null'        => 0,
									'other_choice'      => 0,
									'save_other_choice' => 0,
									'default_value'     => 'default',
									'layout'            => 'horizontal',
									'return_format'     => 'array',
								),
								array(
									'ID'                => 79,
									'key'               => 'field_settings_template_single_custom',
									'label'             => 'Votre Template',
									'name'              => 'single_template_custom',
									'prefix'            => '',
									'type'              => 'textarea',
									'value'             => null,
									'menu_order'        => 3,
									'instructions'      => 'Ajouter votre template dans ce champ ( PHP et/ou HTML)',
									'required'          => 0,
									'id'                => '',
									'class'             => '',
									'conditional_logic' => array(
										array(
											array(
												'field'    => 'field_settings_template_single',
												'operator' => '==',
												'value'    => 'custom',
											),
										),
									),
									'parent'            => 24,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'_name'             => 'single_template_custom',
									'_prepare'          => 0,
									'_valid'            => 1,
									'default_value'     => '',
									'placeholder'       => '',
									'maxlength'         => '',
									'rows'              => '',
									'new_lines'         => '',
								),
							),
						),
						array(
							'key'               => 'field_settings_display',
							'label'             => 'Affichage',
							'name'              => 'display',
							'type'              => 'group',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'layout'            => 'row',
							'sub_fields'        => array(
								array(
									'ID'                => 33,
									'key'               => 'field_settings_display_slider',
									'label'             => 'Nombre d\'événement dans le carroussel',
									'name'              => 'number_event_slider',
									'prefix'            => '',
									'type'              => 'number',
									'value'             => null,
									'menu_order'        => 0,
									'instructions'      => 'Entrer le nombre maximum d\'événement',
									'required'          => 0,
									'id'                => '',
									'class'             => '',
									'conditional_logic' => 0,
									'parent'            => 32,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'_name'             => 'number_event_slider',
									'_prepare'          => 0,
									'_valid'            => 1,
									'default_value'     => 3,
									'placeholder'       => '',
									'prepend'           => '',
									'append'            => '',
									'min'               => 1,
									'max'               => '',
									'step'              => '',
								),
								array(
									'ID'                => 34,
									'key'               => 'field_settings_display_widget',
									'label'             => 'Nombre d\'événement dans le widget',
									'name'              => 'number_event_widget',
									'prefix'            => '',
									'type'              => 'number',
									'value'             => null,
									'menu_order'        => 1,
									'instructions'      => 'Entrer le nombre maximum d\'événement',
									'required'          => 0,
									'id'                => '',
									'class'             => '',
									'conditional_logic' => 0,
									'parent'            => 32,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'_name'             => 'number_event_widget',
									'_prepare'          => 0,
									'_valid'            => 1,
									'default_value'     => 3,
									'placeholder'       => '',
									'prepend'           => '',
									'append'            => '',
									'min'               => 1,
									'max'               => '',
									'step'              => '',
								),
								array(
									'ID'                => 51,
									'key'               => 'field_settings_display_log',
									'label'             => 'Mode Développeur',
									'name'              => 'enable_log',
									'prefix'            => '',
									'type'              => 'true_false',
									'value'             => null,
									'menu_order'        => 2,
									'instructions'      => 'Activer l\'affichage des log en console',
									'required'          => 0,
									'id'                => '',
									'class'             => '',
									'conditional_logic' => 0,
									'parent'            => 32,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'_name'             => 'enable_log',
									'_prepare'          => 0,
									'_valid'            => 1,
									'message'           => '',
									'default_value'     => 0,
									'ui'                => 1,
									'ui_on_text'        => '',
									'ui_off_text'       => '',
								),
							),
						),
						array(
							'key' => 'field_tab_custom',
							'label' => 'Custom',
							'name' => '',
							'type' => 'tab',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'placement' => 'top',
							'endpoint' => 0,
						),
						array(
							'key'               => 'field_settings_custom',
							'label'             => 'Customisation',
							'name'              => 'custom',
							'type'              => 'group',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'layout'            => 'row',
							'sub_fields'        => array(
								array(
									'ID'                => 81,
									'key'               => 'field_settings_custom_archive',
									'label'             => 'Page d\'archive',
									'name'              => 'archive_page',
									'prefix'            => '',
									'type'              => 'group',
									'value'             => null,
									'menu_order'        => 0,
									'instructions'      => '',
									'required'          => 0,
									'id'                => '',
									'class'             => '',
									'conditional_logic' => 0,
									'parent'            => 28,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'_name'             => 'archive_page',
									'_prepare'          => 0,
									'_valid'            => 1,
									'layout'            => 'block',
									'sub_fields'        => array(
										array(
											'ID'                => 82,
											'key'               => 'field_settings_custom_archive_title',
											'label'             => 'Titre',
											'name'              => 'archive_title',
											'prefix'            => '',
											'type'              => 'text',
											'value'             => null,
											'menu_order'        => 0,
											'instructions'      => 'Personaliser le titre de la page d\'archive',
											'required'          => 0,
											'id'                => '',
											'class'             => '',
											'conditional_logic' => 0,
											'parent'            => 81,
											'wrapper'           => array(
												'width' => '',
												'class' => '',
												'id'    => '',
											),
											'_name'             => 'archive_title',
											'_prepare'          => 0,
											'_valid'            => 1,
											'default_value'     => 'Evenements',
											'placeholder'       => '',
											'prepend'           => '',
											'append'            => '',
											'maxlength'         => '',
										),
										array(
											'ID'                => 83,
											'key'               => 'field_settings_custom_archive_slug',
											'label'             => 'Permaliens',
											'name'              => 'archive_slug',
											'prefix'            => '',
											'type'              => 'text',
											'value'             => null,
											'menu_order'        => 1,
											'instructions'      => 'Changer le chemin d\'accès de la page d\'archive. Attention ! Pour que le changement soit effectif, vous devrez re-renregistré la structure des permaliens dans General > Permaliens',
											'required'          => 0,
											'id'                => '',
											'class'             => '',
											'conditional_logic' => 0,
											'parent'            => 81,
											'wrapper'           => array(
												'width' => '',
												'class' => '',
												'id'    => '',
											),
											'_name'             => 'archive_slug',
											'_prepare'          => 0,
											'_valid'            => 1,
											'default_value'     => 'evenements',
											'placeholder'       => '',
											'prepend'           => '',
											'append'            => '',
											'maxlength'         => '',
										),
									),
								),
								array(
									'ID'                => 29,
									'key'               => 'field_settings_custom_css',
									'label'             => 'CSS',
									'name'              => 'custom_css',
									'prefix'            => '',
									'type'              => 'textarea',
									'value'             => null,
									'menu_order'        => 0,
									'instructions'      => 'Ajouter votre CSS dans ce champ',
									'required'          => 0,
									'id'                => '',
									'class'             => '',
									'conditional_logic' => 0,
									'parent'            => 28,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'_name'             => 'custom_css',
									'_prepare'          => 0,
									'_valid'            => 1,
									'default_value'     => '',
									'placeholder'       => '',
									'maxlength'         => '',
									'rows'              => 6,
									'new_lines'         => '',
								),
								array(
									'ID'                => 30,
									'key'               => 'field_settings_custom_js',
									'label'             => 'JS',
									'name'              => 'custom_js',
									'prefix'            => '',
									'type'              => 'textarea',
									'value'             => null,
									'menu_order'        => 1,
									'instructions'      => 'Ajouter votre JS dans ce champ',
									'required'          => 0,
									'id'                => '',
									'class'             => '',
									'conditional_logic' => 0,
									'parent'            => 28,
									'wrapper'           => array(
										'width' => '',
										'class' => '',
										'id'    => '',
									),
									'_name'             => 'custom_js',
									'_prepare'          => 0,
									'_valid'            => 1,
									'default_value'     => '',
									'placeholder'       => '',
									'maxlength'         => '',
									'rows'              => 6,
									'new_lines'         => '',
								),
							),
						),
						array(
							'key' => 'field_tab_colors',
							'label' => 'Colors',
							'name' => '',
							'type' => 'tab',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'placement' => 'top',
							'endpoint' => 0,
						),
						array(
							'key' => 'field_tab_others',
							'label' => 'Others ?',
							'name' => '',
							'type' => 'tab',
							'instructions' => '',
							'required' => 0,
							'conditional_logic' => 0,
							'wrapper' => array(
								'width' => '',
								'class' => '',
								'id' => '',
							),
							'placement' => 'top',
							'endpoint' => 0,
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'options_page',
								'operator' => '==',
								'value'    => 'sem-settings',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'normal',
					'style'                 => 'seamless',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
					'active'                => 1,
					'description'           => '',
				) );

			}
		}

		/**
		 * Création des champs ACF lié au événements
		 * @method create_acf_fields_event
		 * @return
		 */
		function create_acf_fields_event() {
			if ( function_exists( 'acf_add_local_field_group' ) ) {
				acf_add_local_field_group( array(
					'key'                   => 'group_sem',
					'title'                 => 'Informations événement',
					'fields'                => array(
						array(
							'key'               => 'field_sem_1',
							'label'             => 'Date de début',
							'name'              => 'event_date_start',
							'type'              => 'date_time_picker',
							'instructions'      => 'Indiquer la date de début de l\'événement',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'display_format'    => 'd/m/Y H:i',
							'return_format'     => 'd/m/Y H:i',
							'first_day'         => 1,
						),
						array(
							'key'               => 'field_sem_2',
							'label'             => 'Date de Fin',
							'name'              => 'event_date_end',
							'type'              => 'date_time_picker',
							'instructions'      => 'Indiquer la date de fin de l\'événement',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'display_format'    => 'd/m/Y H:i',
							'return_format'     => 'd/m/Y H:i',
							'first_day'         => 1,
						),
						array(
							'key'               => 'field_sem_3',
							'label'             => 'Lieu',
							'name'              => 'event_place',
							'type'              => 'text',
							'instructions'      => 'Indiquer le lieu de l\'événement',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'formatting'        => 'html',
							'maxlength'         => 40,
						),
						array(
							'key'               => 'field_sem_4',
							'label'             => 'Description de l\'événement',
							'name'              => 'event_description',
							'type'              => 'textarea',
							'instructions'      => 'Ajouter une description',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'maxlength'         => 100,
							'rows'              => 5,
							'new_lines'         => 'br',
						),
						array(
							'key'               => 'field_sem_5',
							'label'             => 'Événement important ?',
							'name'              => 'event_priority',
							'type'              => 'true_false',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'message'           => '',
							'default_value'     => 0,
							'ui'                => 1,
							'ui_on_text'        => '',
							'ui_off_text'       => '',
						),
						array(
							'key'               => 'field_sem_6',
							'label'             => 'Annuler l\'événement ?',
							'name'              => 'event_cancel',
							'type'              => 'true_false',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => 0,
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'message'           => '',
							'default_value'     => 0,
							'ui'                => 1,
							'ui_on_text'        => '',
							'ui_off_text'       => '',
						),
						array(
							'key'               => 'field_sem_7',
							'label'             => 'Raison de l\'annulation',
							'name'              => 'event_cancel_reason',
							'type'              => 'text',
							'instructions'      => '',
							'required'          => 0,
							'conditional_logic' => array(
								array(
									array(
										'field'    => 'field_sem_6',
										'operator' => '==',
										'value'    => '1',
									),
								),
							),
							'wrapper'           => array(
								'width' => '',
								'class' => '',
								'id'    => '',
							),
							'default_value'     => '',
							'placeholder'       => '',
							'prepend'           => '',
							'append'            => '',
							'maxlength'         => '',
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'post_type',
								'operator' => '==',
								'value'    => 'event',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'acf_after_title',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
					'active'                => 1,
					'description'           => '',
				) );
			}
		}

		/**
		 * Création du custom post type event
		 * @method register_post_types
		 * @return
		 */
		function register_post_types() {
			register_post_type( 'event', array(
					'label'               => 'Événement',
					'labels'              => array(
						'name'               => 'Événements',
						'singular_name'      => 'Événement',
						'all_items'          => 'Tous les événements',
						'add_new_item'       => 'Ajouter un événement',
						'edit_item'          => 'Éditer l\'événement',
						'new_item'           => 'Nouveau événement',
						'view_item'          => 'Voir l\'événement',
						'search_items'       => 'Rechercher parmi les événements',
						'not_found'          => 'Pas d\'événement trouvé',
						'not_found_in_trash' => 'Pas d\'événement dans la corbeille',
						'all_items'          => 'Tous les événements'
					),
					'public'              => true,
					'exclude_from_search' => false,
					'show_ui'             => true,
					'capability_type'     => 'post',
					'hierarchical'        => false,
					'supports'            => array(
						'title',
						'editor',
						'thumbnail',
					),
					'taxonomies'          => array( 'event-category' ),
					'has_archive'         => true,
					'rewrite'             => array( 'slug' => get_event_slug(), 'with_front' => true ),
					'show_in_menu'        => false,
				)
			);
		}

		/**
		 * Création de la taxonomie des événements
		 * @method register_post_taxonomy
		 * @return
		 */
		function register_post_taxonomy() {
			register_taxonomy(
				'event-category',
				'event',
				array(
					'label'        => 'Catégorie d\'événement',
					'labels'       => array(
						'name'          => 'Catégories d\'événement',
						'singular_name' => 'Catégorie d\'événement',
						'all_items'     => 'Toutes les catégories d\'événement',
						'edit_item'     => 'Éditer la catégorie d\'événement',
						'view_item'     => 'Voir la catégorie d\'événement',
						'update_item'   => 'Mettre à jour la catégorie d\'événement',
						'add_new_item'  => 'Ajouter une catégorie d\'événement',
						'new_item_name' => 'Nouvelle catégorie d\'événement',
						'search_items'  => 'Rechercher parmi les catégories d\'événement',
						'popular_items' => 'Catégories d\'événement les plus utilisées'
					),
					'hierarchical' => true,
				)
			);
		}

		/**
		 * Création des status des événements
		 * @method register_post_status
		 * @return
		 */
		function register_post_status() {
			register_post_status( 'past', array(
				'label'                     => 'Past',
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'Passé <span class="count">(%s)</span>', 'Passé <span class="count">(%s)</span>' ),
			) );
			register_post_status( 'ongoing', array(
				'label'                     => 'Ongoing',
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'En cours <span class="count">(%s)</span>', 'En cours <span class="count">(%s)</span>' ),
			) );
			register_post_status( 'upcoming', array(
				'label'                     => 'Upcoming',
				'public'                    => true,
				'exclude_from_search'       => false,
				'show_in_admin_all_list'    => true,
				'show_in_admin_status_list' => true,
				'label_count'               => _n_noop( 'À venir <span class="count">(%s)</span>', 'À venir <span class="count">(%s)</span>' ),
			) );
		}

		/**
		 * Création des colonnes personalisées lors de l'affichage dans l'administration
		 * @method set_custom_edit_event_columns
		 *
		 * @param  array $columns Les colonnes de base de wordpress
		 *
		 * @return array                        $columns Les nouvelles colonnes
		 */
		function set_custom_edit_event_columns( $columns ) {
			unset( $columns['date'] );

			$customColumns = array(
				'event_date'   => 'Date de l\'événement',
				'desc'         => 'Description',
				'category'     => 'Catégorie',
				'date_publish' => 'Date de publication',
				'status'       => 'Status',
				'priority'     => 'Priorité',
			);

			$columns = array_merge( $columns, $customColumns );

			return $columns;
		}

		/**
		 * Ajout des informations à ajouté dans les colonnes personalisées
		 * @method custom_event_column
		 *
		 * @param  array $column Les colonnes qui sont affichées
		 * @param  int $post_id L'id du post dans la bouce wordpress
		 *
		 * @return
		 */
		function custom_event_column( $column, $post_id ) {
			switch ( $column ) {

				case 'event_date' :
					if ( get_field( 'event_date_start', $post_id ) && get_field( 'event_date_end', $post_id ) ) {

						echo 'Du ' . get_event_date( get_field( 'event_date_start', $post_id ) ) . ' au ' . get_event_date( get_field( 'event_date_end', $post_id ) );
					} else {
						echo 'Aucune date de trouvé';
					}
					break;

				case 'desc' :
					if ( get_field( 'event_description', $post_id ) ) {
						echo get_field( 'event_description', $post_id );
					} else {
						echo 'Aucune description';
					}
					break;

				case 'category':
					if ( get_the_term_list( $post_id, 'event-category' ) ) {
						echo get_the_term_list( $post_id, 'event-category' );
					} else {
						echo 'Aucune catégorie';
					}
					break;

				case 'date_publish':
					echo get_the_date( 'd/m/Y', $post_id );
					break;

				case 'status':
					switch ( get_post_status( $post_id ) ) {
						case 'past':
							echo 'Passé';
							break;

						case 'ongoing':
							echo 'En cours';
							break;

						case 'upcoming':
							echo 'À venir';
							break;

						default:
							echo 'Status non valide';
							break;
					}
					break;

				case 'priority':
					if ( get_field( 'event_priority' ) ) {
						echo 'Importante';
					} else {
						echo 'Normale';
					}
					break;

			}
		}

		/**
		 * Ajout des colonnes personnalisées qui sont triables
		 * @method custom_sortable_column
		 *
		 * @param  [type]                 $columns [description]
		 *
		 * @return [type]                          [description]
		 */
		function custom_sortable_column( $columns ) {
			$customColumns = array(
				'event_date' => 'event_date',
				'desc'       => 'desc',
				'category'   => 'category',
			);

			$columns = array_merge( $columns, $customColumns );

			return $columns;
		}

		/**
		 * Déclaration des fichiers de style
		 * @method register_assets
		 * @return
		 */
		function register_assets() {
			wp_register_script( 'bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', array( 'jquery' ), null );
			wp_register_script( 'sem-custom-main', sem_get_dir( 'assets/js/sem-custom-main.js' ), array( 'jquery' ), $this->version );
			wp_register_script( 'sem-calendar', sem_get_dir( 'assets/js/sem-calendar.js' ), array( 'jquery' ), $this->version );

			wp_register_style( 'sem-default-design', sem_get_dir( 'assets/css/sem-default-design.css' ), array(), $this->version );
			wp_register_style( 'sem-admin-design', sem_get_dir( 'assets/css/sem-admin-design.css' ), array(), $this->version );
			wp_register_style( 'font-awesome', sem_get_dir( 'assets/css/font-awesome-4.7.0/css/font-awesome.min.css' ), array(), $this->version );
			wp_register_style( 'bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css', array(), null );
			wp_register_style( 'sem-custom-design', sem_get_dir( 'assets/css/sem-custom-design.css' ), array(), $this->version );

		}

		/**
		 * Chargement des fichiers de styles principaux
		 * @method enqueue_assets
		 * @return
		 */
		function enqueue_assets() {
			wp_enqueue_script( 'bootstrap-js' );
			wp_enqueue_style( 'bootstrap-css' );

			wp_enqueue_style( 'sem-default-design' );
			wp_enqueue_style( 'font-awesome' );

			$custom   = get_event_settings( 'custom' );
			$template = get_event_settings( 'template' );

			if ( ! empty( $custom['custom_css'] ) ) {
				wp_enqueue_style( 'sem-custom-design' );
			}
			if ( ! empty( $custom['custom_js'] ) ) {
				wp_enqueue_script( 'sem-custom-main' );
			}
			if ( $template['archive_template']['value'] == 'calendar' && is_archive( 'event' ) ) {
				wp_enqueue_script( 'sem-calendar' );
			}
		}

		/**
		 * Définition sécurisé d'une constante
		 * @method define
		 *
		 * @param  string $name Nom de la constante
		 * @param  mixed $value Valeur de la constante
		 *
		 * @return
		 */
		function define( $name, $value = true ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Retourne un paramètre du plugin
		 * @method get_setting
		 *
		 * @param  string $name Nom du paramètre
		 * @param  mixed $value Valeur par défaut
		 *
		 * @return mixed              Valeur du paramètre
		 */
		function get_setting( $name, $value = null ) {
			if ( isset( $this->settings[ $name ] ) ) {

				$value = $this->settings[ $name ];
			}

			return $value;
		}

		/**
		 * Met à jour un paramètre du plugin
		 * @method update_setting
		 *
		 * @param  string $name Nom du paramètre
		 * @param  mixed $value Valeur du paramètre
		 *
		 * @return
		 */
		function update_setting( $name, $value ) {

			$this->settings[ $name ] = $value;

			return true;

		}

	}

	/**
	 * Fonction principale du plugin, garantissant une seule instance de la classe principale
	 * @method sem
	 * @return
	 */
	function sem() {
		global $sem;

		if ( ! isset( $sem ) ) {

			$sem = new SuperEventManager();

			$sem->load_plugin();

		}

		return $sem;
	}

// initialize
	sem();


endif; // class_exists check
