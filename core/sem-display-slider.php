<?php

/**
 * Classe regroupant les méthodes nécessaires à l'affichage du slider
 */
class SuperEventManagerDisplaySlider {

	/**
	 * Constructeur de la classe SuperEventManagerDisplaySlider
	 * Ajout du shortcode pour afficher le slider
	 * @method __construct
	 */
	function __construct() {
		add_shortcode( 'displaySlider', array( $this, 'display_slider' ) );
		add_action( 'wp_loaded', array( $this, 'update_slider_home' ) );
	}

	/**
	 * Fonction qui ne sert strictement à rien à part débugger la valeur du have_rows
	 * @method test_slider
	 * @return
	 */
	function test_slider() {
		#echo '<script>console.log("Etat have_rows : '. have_rows('slider', 'options') .'")</script>';
		if ( have_rows( 'slider', 'options' ) ) {

		}
		#echo '<script>console.log("Etat have_rows : '. have_rows('slider', 'options') .'")</script>';
	}

	/**
	 * Fonction permettant la mise à jour du slider
	 * @method update_slider_home
	 * @return
	 */
	public function update_slider_home() {
		if ( ! is_admin() ) {

			debug_console( "in updater" );

			$this->debug_slider();

			$slider = get_event_settings( 'display' );

			$the_query = new WP_Query( array(
					'post_type'      => 'event',
					'posts_per_page' => $slider['number_event_slider'],
					'order'          => 'ASC',
					'orderby'        => 'meta_value',
					'meta_key'       => 'event_date_start',
				)
			);
			if ( $the_query->have_posts() ) {
				while ( $the_query->have_posts() ) {
					$the_query->the_post();

					debug_console( get_post_status( get_the_ID() ) );
					debug_console( get_field( 'event_priority', get_the_ID() ) );

					if ( get_field( 'event_priority', get_the_ID() ) ) {
						debug_console( "is priority" );
						if ( ( get_post_status( get_the_ID() ) == 'past' ) || get_field( 'event_cancel' ) ) {
							debug_console( "is delete" );
							$this->delete_slide_home( get_the_ID() );
						} else {
							if ( $this->slide_exist( get_the_ID() ) ) {
								debug_console( "is update" );
								$this->update_slide_home( get_post_thumbnail_id(), get_the_title(), get_field( 'event_date_start' ), get_field( 'event_place' ), get_field( 'event_description' ), get_the_ID() );
							} else {
								debug_console( "is add" );
								$this->add_slide_home( get_post_thumbnail_id(), get_the_title(), get_field( 'event_date_start' ), get_field( 'event_place' ), get_field( 'event_description' ), get_the_ID() );
							}
						}
					} else {
						debug_console( "not prior" );
						$this->delete_slide_home( get_the_ID() );
					}
				}
			} else {
				debug_console( 'no event' );
				if ( have_rows( 'slider', 'options' ) ) {

					#debug_console(get_field('slider', 'options'));

					while ( have_rows( 'slider', 'options' ) ) {

						the_row();

						$event_id = get_sub_field( 'event_id' );

						if ( ! empty( intval( $event_id ) ) ) {
							$this->delete_slide_home( $event_id );
						}
					}
				}
			}
			wp_reset_postdata();
		}

	}

	/**
	 * Fonction permettant le nettoyage du slider lorsqu'un événements est supprimé
	 * @method debug_slider
	 * @return
	 */
	function debug_slider() {
		debug_console( 'in debug slider' );

		if ( have_rows( 'slider', 'options' ) ) {

			while ( have_rows( 'slider', 'options' ) ) {

				the_row();

				$id = get_sub_field( 'event_id' );

				if ( ! empty( $id ) ) {

					debug_console( get_post( $id ) );

					if ( get_post( $id ) == null || get_post_status( $id ) == 'trash' ) {

						debug_console( 'post exist pas' );
						$delete_slide = delete_row( 'slider', get_row_index(), 'options' );
					}
				}
			}
		}
	}

	/**
	 * Fonction ajoutant un slide au slider
	 * @method add_slide_home
	 *
	 * @param  int $id_image ID de l'image mis en avant de l'événement
	 * @param  string $head Titre de l'événement
	 * @param  string $date Date de début de l'événement
	 * @param  string $place Lieu où se passe l'événement
	 * @param  string $content Description de l'événement
	 * @param  int $event_id ID de l'événement
	 */
	function add_slide_home( $id_image, $head, $date, $place, $content, $event_id ) {
		debug_console( "add row" );

		if ( ! $this->slide_exist( $event_id ) ) {
			$new_row = array(
				'image'    => intval( $id_image ),
				'head'     => $head,
				'date'     => $date,
				'place'    => $place,
				'content'  => $content,
				'event_id' => $event_id,
			);

			debug_console( "row create" );
			debug_console( $new_row );
			debug_console( 'repeaterfield: ' . get_field( 'slider', 'options' ) );
			debug_console( get_field( 'slider', 'options' ) );
			$new_slide = add_row( 'slider', $new_row, 'options' );
			debug_console( 'etat create:' );
			debug_console( $new_slide );
		}
	}

	/**
	 * Fonction supprimant un slide au slider
	 * @method delete_slide_home
	 *
	 * @param  int $event_id ID de l'événement
	 *
	 * @return
	 */
	function delete_slide_home( $event_id ) {
		debug_console( "delete_row" );

		if ( $this->slide_exist( $event_id ) ) {

			$id_row = $this->the_row_number( $event_id );

			if ( $id_row != null ) {
				debug_console( 'row delete' );
				$delete_slide = delete_row( 'slider', $id_row, 'options' );
			} else {
				debug_console( "Delete Error ! : Une erreur c\'est produite. Cette ligne n\'existe pas", 'error' );
			}
		}
	}

	/**
	 * Fonction mettant à jour un slide du slider
	 * @method update_slide_home
	 *
	 * @param  int $id_image ID de l'image mis en avnt de l'événement
	 * @param  string $head Titre de l'événement
	 * @param  string $date Date de début de l'événement
	 * @param  string $place Lieu où se passe l'événement
	 * @param  string $content Description de l'événement
	 * @param  int $event_id ID de l'événement
	 *
	 * @return
	 */
	function update_slide_home( $id_image, $head, $date, $place, $content, $event_id ) {
		debug_console( 'update row' );
		debug_console( 'id_evn: ' . $event_id );
		debug_console( 'Etat have_rows avant: ' . have_rows( 'slider', 'options' ) );

		if ( $this->slide_exist( $event_id ) ) {
			debug_console( "in row" );

			$update_row = array(
				'image'    => intval( $id_image ),
				'head'     => $head,
				'date'     => $date,
				'place'    => $place,
				'content'  => $content,
				'event_id' => $event_id
			);

			$id_row = $this->the_row_number( $event_id );

			if ( $id_row != null ) {
				debug_console( "row update" );
				$update_slide = update_row( 'slider', $id_row, $update_row, 'options' );
			} else {
				debug_console( "Update Error ! : Une erreur c\'est produite. Cette ligne n\'existe pas", 'error' );
			}

		} else {
			debug_console( "error update" );
		}

	}

	/**
	 * Fonction déterminant l'existance d'un slider
	 * @method slide_exist
	 *
	 * @param  int $event_id ID de l'événement
	 *
	 * @return boolean                Retourne 1 s'il existe, 0 dans le cas contraire
	 */
	function slide_exist( $event_id ) {
		debug_console( "in exist" );
		$this->test_slider();

		if ( have_rows( 'slider', 'options' ) ) {

			$this->test_slider();

			while ( have_rows( 'slider', 'options' ) ) {

				the_row();
				debug_console( 'in row slide exist' );

				if ( $event_id == get_sub_field( 'event_id' ) ) {
					$exist = 1;
					debug_console( 'exist' );

					return $exist;
				}
			}
			$exist = 0;
			debug_console( 'no exist' );

			return $exist;
		}
	}

	/**
	 * Fonction déterminant la ligne d'un slide dans le slider
	 * @method the_row_number
	 *
	 * @param  int $event_id ID de l'événement
	 *
	 * @return int                   Retourne la ligne du slide correspondant à l'événement ou 'null' si aucune ligne ne correspond
	 */
	function the_row_number( $event_id ) {

		$this->test_slider();

		if ( have_rows( 'slider', 'options' ) ) {

			debug_console( "get row number" );

			while ( have_rows( 'slider', 'options' ) ) {

				the_row();

				debug_console( "in while row number" );

				debug_console( 'id_evn_param: ' . $event_id . ' id_evn_row: ' . get_sub_field( 'event_id' ) );

				if ( $event_id == get_sub_field( 'event_id' ) ) {
					return get_row_index();
				}
			}

			return null;
		}
	}

	/**
	 * Fonction affichant le slider
	 * @method display_slider
	 * @return string   $return      Le code html du slider
	 */
	function display_slider() {
		$rows = get_field( 'slider', 'options' );
		debug_console( $rows );

		$have = have_rows( 'slider', 'options' );
		debug_console( $have );

		debug_console( 'create_slider' );

		debug_console( get_field( 'slider', 'options' ) );

		debug_console( have_rows( 'slider', 'options' ) );
		//$this->test_slider();
		debug_console( have_rows( 'slider', 'options' ) );
		//$this->test_slider();
		debug_console( have_rows( 'slider', 'options' ) );


		if ( have_rows( 'slider', 'options' ) ) {

			$data_slide = 0;
			$data_target = "";
			$data_item = "";
			debug_console( "get slider" );

			while ( have_rows( 'slider', 'options' ) ) {

				the_row();

				debug_console( 'get row: ' . $data_slide );
				$image = get_sub_field( 'image' );
				debug_console( $image );

				if ( get_sub_field( 'event_id' ) ) {
					$id = get_sub_field( 'event_id' );
				} else {
					$id = false;
				}

				if ( $data_slide == 0 ) {
					$data_target .= '<li data-target="#carousel-example-generic" data-slide-to="' . $data_slide . '" class="active"></li>';
				} else {
					$data_target .= '<li data-target="#carousel-example-generic" data-slide-to="' . $data_slide . '"></li>';
				}

				if ( $data_slide == 0 ) {
					$data_item .= '<div class="item active">';
				} else {
					$data_item .= '<div class="item">';
				}

				$data_item .= '<img class="sem-slider-img" src="' . $image['url'] . '" alt="' . $image['alt'] . '">';

				if ( $id ) {
					$data_item .= '<a class="sem-a" href="' . get_permalink( $id ) . '">';
				}

				$data_item .= '
				<div class="carousel-caption">
					<h3 class="sem-h3">' . get_sub_field( 'head' ) . '</h3>';

				if ( $id ) {

					$data_item .= '
						<div class="sem-slider-div">
							<span class="sem-slider-span"><i class="fa fa-map-marker" aria-hidden="true"></i> ' . get_sub_field( 'place' ) . '</span>
							<i class="fa fa-clock-o" aria-hidden="true"></i> <span class="sem-slider-span"> ' . get_sub_field( 'date' ) . '</span>
						</div>';
				}

				$data_item .= '
					<div class="sem-slider-div">
						<p>' . get_sub_field( 'content' ) . '</p>
					</div>';

				if ( $id ) {
					$data_item .= '</a>';
				}

				$data_item .= '</div>
				</div>';

				$data_slide ++;

			}

			$return = '
			<div class="sem-slider">
				<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
					<!-- Indicators -->
					<ol class="carousel-indicators">
						' . $data_target . '
					</ol>

					<!-- Wrapper for slides -->
					<div class="carousel-inner" role="listbox">
						' . $data_item . '
					</div>
				</div>
			</div>';
		} else {
			$return = 'Le slider est vide';
			debug_console( "error display slider" );
		}

		return $return;

	}


	function test_display() {
		debug_console( 'intest' );
		debug_console( have_rows( 'slider', 'option' ) );
		if ( have_rows( 'slider', 'options' ) ) {
			$return = '<ul>';
			debug_console( 'in if' );
			while ( have_rows( 'slider', 'options' ) ) {
				the_row();
				debug_console( 'in row' );
				$image  = get_sub_field( 'image' );
				$id     = get_sub_field( 'head' );
				$return .= '<li>' . $image . '</li><li>' . $id . '</li>';
			}
			$return .= '</ul>';

			return $return;
		}
	}
}

/**
 * Fonction faisant référence à 'update_slider_home' afin de mettre à jour le slider
 * @method update_event_slider
 * @return
 */
function update_event_slider() {
	$sem_display_slider = new SuperEventManagerDisplaySlider;

	$sem_display_slider->update_slider_home();
}

new SuperEventManagerDisplaySlider;

?>
