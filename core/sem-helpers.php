<?php
/**
 * Alias de sem()->get_setting
 * @method sem_get_setting
 *
 * @param  string $name Le nom du paramètres
 * @param  int|string $value La valeur par défaut
 *
 * @return int|string        La valeur du paramètre
 */
function sem_get_setting( $name, $value = null ) {
	return sem()->get_setting( $name, $value );
}

/**
 * Alias de sem->update_setting
 * @method sem_update_setting
 *
 * @param  string $name Le nom du paramètres
 * @param  int|string $value La valeur par défaut
 *
 * @return int|string        La nouvelle valeur du paramètre
 */
function sem_update_setting( $name, $value ) {
	return sem()->update_setting( $name, $value );
}

/**
 * Retourne le chemin absolu d'un fichier dans le dossier du plugin
 * @method sem_get_path
 *
 * @param  string $path Le chemin relatif du fichier à la racine du dossier du plugin
 *
 * @return string       Le chemin absolue du fichier
 */
function sem_get_path( $path ) {
	return sem_get_setting( 'path' ) . $path;
}

/**
 * Retourne l'url d'un fichier dans le dossier du plugin
 * @method sem_get_dir
 *
 * @param  string $path Le chemin relatif du fichier à la racine du dossier du plugin
 *
 * @return string       L'url du fichier
 */
function sem_get_dir( $path ) {
	return sem_get_setting( 'dir' ) . $path;
}

/**
 * Inclus un fichier
 * @method sem_include
 *
 * @param  string $file Le chemin relatif du fichier à la racine du dossier du plugin
 *
 * @return
 */
function sem_include( $file ) {
	$path = sem_get_path( $file );

	if ( file_exists( $path ) ) {

		include_once( $path );
	}
}

/**
 * Inclus le fichier html d'un sous-menu
 * @method sem_get_view
 *
 * @param  string $path Le nom du fichier sans l'extension
 *
 * @return
 */
function sem_get_view( $path = '' ) {
	// allow view file name shortcut
	if ( substr( $path, - 4 ) !== '.php' ) {

		$path = sem_get_path( "admin/views/{$path}.php" );
	}
	// include
	if ( file_exists( $path ) ) {

		include( $path );
	}
}


?>
