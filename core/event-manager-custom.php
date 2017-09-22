<?php

/**
 * Classe regroupant les méthodes liées à la sauvegarde du contenu personalisé
 */
class  EventManagerCustom
{

    /**
     * Constructeur de la classe EventManagerCustom
     * Ajout de la sauvegarde des fichiers des éléments personalisable lors de la sauvegarde des paramètres
     * @method __construct
     */
    function __construct()
    {
        add_action('acf/save_post', array($this, 'save_custom_archive_template'), 20);
        add_action('acf/save_post', array($this, 'save_custom_single_template'), 20);
        add_action('acf/save_post', array($this, 'save_custom_css'), 20);
        add_action('acf/save_post', array($this, 'save_custom_js'), 20);
    }


    /**
     * Fonction permettant la sauvegarde du template d'archive
     * @method save_custom_archive_template
     * @return
     */
    function save_custom_archive_template()
    {
        $filename = 'core/template/archive-event-custom.php';

        $custom = get_event_settings('template');

        $custom_template = $custom['archive_template_custom'];

        $this->save_file($filename, $custom_template, 'Votre template a bien été sauvegardé.');
    }

    /**
     * Fonction permettant la sauvegarde du template single
     * @method save_custom_single_template
     * @return
     */
    function save_custom_single_template()
    {
        $filename = 'core/template/single-event-custom.php';

        $custom = get_event_settings('template');

        $custom_template = $custom['single_template_custom'];

        $this->save_file($filename, $custom_template, 'Votre template a bien été sauvegardé.');
    }

    /**
     * Fonction permettant la sauvegarde du css
     * @method save_custom_css
     * @return
     */
    function save_custom_css()
    {
        $filename = 'assets/css/event-manager-custom-design.css';

        $custom = get_event_settings('custom');

        $custom_css = $custom['custom_css'];

        $this->save_file($filename, $custom_css, 'Votre CSS a bien été sauvegardé.');
    }

    /**
     * Fonction permettant la sauvegarde du template d'archive
     * @method save_custom_js
     * @return
     */
    function save_custom_js()
    {
        $filename = 'assets/js/event-manager-custom-main.js';

        $custom = get_event_settings('custom');

        $custom_js = $custom['custom_js'];

        $this->save_file($filename, $custom_js, 'Votre JS a bien été sauvegardé.');
    }

    /**
     * Fonction permettant la sauvegarde d'un fichier
     * @method save_file
     * @param  string $file Chemin du fichier à partir de la racine du plugin
     * @param  string $data Données à sauvegardées
     * @param  string $succes_log Message au succés
     * @return
     */
    function save_file($file, $data, $succes_log)
    {
        $screen = get_current_screen();

        if (strpos($screen->id, "event-manager-settings") == true) {

            $filename = event_manager_get_path($file);

            if (is_writable($filename)) {

                if (!$handle = fopen($filename, 'w')) {
                    debug_console('Erreur ! Vous n\'avez pas les droits de lecture sur ce fichier.', 'error');
                    exit;
                }

                if (fwrite($handle, $data) === FALSE) {
                    debug_console('Erreur ! Vous n\'avez pas les droits d\'écriture sur ce fichier.', 'error');
                    exit;
                }

                debug_console($succes_log);

                fclose($handle);

            } else {
                debug_console('Erreur ! Vous n\'avez pas les droits d\'écriture sur ce fichier.', 'error');
            }
        }
    }

}

new EventManagerCustom();

?>
