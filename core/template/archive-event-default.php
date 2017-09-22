<?php get_header(); ?>
<header class="em-header">
    <h1 class="entry-title em-h1"><?php the_archive_event_title() ?></h1>
    <div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
		<?php if ( function_exists( 'bcn_display' ) ) {
			bcn_display();
		}
		?>
    </div>
</header>
<div class="em-archive">
    <h2 class="em-h2 em-archive-title"><?php the_archive_event_title(); ?> en Cours</h2>
    <div class="row em-flex">
		<?php
		$event_ongoing = new WP_Query( array(
				'post_type'   => 'event',
				'post_status' => 'ongoing',
				'order'       => 'ASC',
				'orderby'     => 'meta_value',
				'meta_key'    => 'event_date_start'
			)
		);
		?>
		<?php if ( $event_ongoing->have_posts() ) : ?>
			<?php while ( $event_ongoing->have_posts() ) : $event_ongoing->the_post(); ?>
				<?php
				$title = get_the_title();
				if ( strlen( $title ) > 20 ) {
					$title = utf8_encode( substr_replace( utf8_decode( $title ), ' ...', 20 ) );
				}
				?>
                <div class="col-sm-6 col-md-4 hvr-grow em-auto">
                    <a class="em-a" title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
						<?php if ( get_field( 'event_cancel' ) ): ?>
                        <div class="thumbnail em-cancel-event">
							<?php else: ?>
                            <div class="thumbnail">
								<?php endif; ?>
                                <div class="thumbnail-image">
									<?php the_post_thumbnail( 'full' ); ?>
                                </div>
                                <div class="caption">
                                    <h3 class="em-h3"><?php echo $title; ?></h3>
									<?php if ( get_field( 'event_cancel' ) ): ?>
                                        <span class="em-archive-cancel em-badge-list em-cancel"><strong>Annulé</strong></span>
									<?php endif; ?>
									<?php if ( is_the_same_date( get_field( 'event_date_start' ), get_field( 'event_date_end' ) ) ) : ?>
                                        <p><i class="fa fa-calendar-o"
                                              aria-hidden="true"></i><span>Le <?php the_event_date( get_field( 'event_date_start' ) ); ?></span>
                                        </p>
                                        <p><i class="fa fa-clock-o"
                                              aria-hidden="true"></i><span>De <?php the_event_hour( get_field( 'event_date_start' ) ); ?>
                                                à <?php the_event_hour( get_field( 'event_date_end' ) ); ?></span></p>
									<?php else : ?>
                                        <p><i class="fa fa-calendar-o"
                                              aria-hidden="true"></i><span>Du <?php the_event_date( get_field( 'event_date_start' ) ); ?>
                                                au <?php the_event_date( get_field( 'event_date_end' ) ); ?></span></p>
                                        <p><i class="fa fa-clock-o"
                                              aria-hidden="true"></i><span>À partir de <?php the_event_hour( get_field( 'event_date_start' ) ); ?>
                                                jusqu'à <?php the_event_hour( get_field( 'event_date_end' ) ); ?></span>
                                        </p>
									<?php endif; ?>
                                    <p><i class="fa fa-map-marker"
                                          aria-hidden="true"></i><span><?php the_field( 'event_place' ); ?></span></p>
                                    <p class="em-archive-description"><i class="fa fa-align-left"
                                                                         aria-hidden="true"></i><span><?php the_field( 'event_description' ); ?></span>
                                    </p>
                                </div>
                            </div>
                    </a>
                </div>
			<?php endwhile; ?>
		<?php else: ?>
            <div class="em-empty">
                <span>Aucun événement en cour</span>
            </div>
		<?php endif; ?>
    </div>
    <h2 class="em-h2 em-archive-title"><?php the_archive_event_title(); ?> à Venir</h2>
    <div class="row em-flex">
		<?php
		$event_upcoming = new WP_Query( array(
				'post_type'   => 'event',
				'post_status' => 'upcoming',
				'order'       => 'ASC',
				'orderby'     => 'meta_value',
				'meta_key'    => 'event_date_start'
			)
		);
		?>
		<?php if ( $event_upcoming->have_posts() ) : ?>
			<?php while ( $event_upcoming->have_posts() ) : $event_upcoming->the_post(); ?>
				<?php
				$title = get_the_title();
				if ( strlen( $title ) > 20 ) {
					$title = utf8_encode( substr_replace( utf8_decode( $title ), ' ...', 20 ) );
				}
				?>
                <div class="col-sm-6 col-md-4 hvr-grow em-auto">
                    <a class="em-a" title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
						<?php if ( get_field( 'event_cancel' ) ): ?>
                        <div class="thumbnail em-cancel-event">
							<?php else: ?>
                            <div class="thumbnail">
								<?php endif; ?>
                                <div class="thumbnail-image">
									<?php the_post_thumbnail( 'full' ); ?>
                                </div>
                                <div class="caption">
                                    <h3 class="em-h3"><?php echo $title; ?></h3>
									<?php if ( get_field( 'event_cancel' ) ): ?>
                                        <span class="em-archive-cancel em-badge-list em-cancel"><strong>Annulé</strong></span>
									<?php endif; ?>
									<?php if ( is_the_same_date( get_field( 'event_date_start' ), get_field( 'event_date_end' ) ) ) : ?>
                                        <p><i class="fa fa-calendar-o"
                                              aria-hidden="true"></i><span>Le <?php the_event_date( get_field( 'event_date_start' ) ); ?></span>
                                        </p>
                                        <p><i class="fa fa-clock-o"
                                              aria-hidden="true"></i><span>De <?php the_event_hour( get_field( 'event_date_start' ) ); ?>
                                                à <?php the_event_hour( get_field( 'event_date_end' ) ); ?></span></p>
									<?php else : ?>
                                        <p><i class="fa fa-calendar-o"
                                              aria-hidden="true"></i><span>Du <?php the_event_date( get_field( 'event_date_start' ) ); ?>
                                                au <?php the_event_date( get_field( 'event_date_end' ) ); ?></span></p>
                                        <p><i class="fa fa-clock-o"
                                              aria-hidden="true"></i><span>À partir de <?php the_event_hour( get_field( 'event_date_start' ) ); ?>
                                                jusqu'à <?php the_event_hour( get_field( 'event_date_end' ) ); ?></span>
                                        </p>
									<?php endif; ?>
                                    <p><i class="fa fa-map-marker"
                                          aria-hidden="true"></i><span><?php the_field( 'event_place' ); ?></span></p>
                                    <p class="em-archive-description"><i class="fa fa-align-left"
                                                                         aria-hidden="true"></i><span><?php the_field( 'event_description' ); ?></span>
                                    </p>
                                </div>
                            </div>
                    </a>
                </div>
			<?php endwhile; ?>
		<?php else: ?>
            <div class="em-empty">
                <span>Aucun événement à venir</span>
            </div>
		<?php endif; ?>
    </div>
    <h2 class="em-h2 em-archive-title"><?php the_archive_event_title(); ?> Passés</h2>
    <div class="row em-flex">
		<?php
		$event_past = new WP_Query( array(
				'post_type'   => 'event',
				'post_status' => 'past',
				'order'       => 'ASC',
				'orderby'     => 'meta_value',
				'meta_key'    => 'event_date_start'
			)
		);
		?>
		<?php if ( $event_past->have_posts() ) : ?>
			<?php while ( $event_past->have_posts() ) : $event_past->the_post(); ?>
				<?php
				$title = get_the_title();
				if ( strlen( $title ) > 20 ) {
					$title = utf8_encode( substr_replace( utf8_decode( $title ), ' ...', 20 ) );
				}
				?>
                <div class="col-sm-6 col-md-4 hvr-grow em-auto">
                    <a class="em-a" title="<?php the_title(); ?>" href="<?php the_permalink(); ?>">
						<?php if ( get_field( 'event_cancel' ) ): ?>
                        <div class="thumbnail em-cancel-event">
							<?php else: ?>
                            <div class="thumbnail">
								<?php endif; ?>
                                <div class="thumbnail-image">
									<?php the_post_thumbnail( 'full' ); ?>
                                </div>
                                <div class="caption">
                                    <h3 class="em-h3"><?php echo $title; ?></h3>
									<?php if ( get_field( 'event_cancel' ) ): ?>
                                        <span class="em-archive-cancel em-badge-list em-cancel"><strong>Annulé</strong></span>
									<?php endif; ?>
									<?php if ( is_the_same_date( get_field( 'event_date_start' ), get_field( 'event_date_end' ) ) ) : ?>
                                        <p><i class="fa fa-calendar-o"
                                              aria-hidden="true"></i><span>Le <?php the_event_date( get_field( 'event_date_start' ) ); ?></span>
                                        </p>
                                        <p><i class="fa fa-clock-o"
                                              aria-hidden="true"></i><span>De <?php the_event_hour( get_field( 'event_date_start' ) ); ?>
                                                à <?php the_event_hour( get_field( 'event_date_end' ) ); ?></span></p>
									<?php else : ?>
                                        <p><i class="fa fa-calendar-o"
                                              aria-hidden="true"></i><span>Du <?php the_event_date( get_field( 'event_date_start' ) ); ?>
                                                au <?php the_event_date( get_field( 'event_date_end' ) ); ?></span></p>
                                        <p><i class="fa fa-clock-o"
                                              aria-hidden="true"></i><span>À partir de <?php the_event_hour( get_field( 'event_date_start' ) ); ?>
                                                jusqu'à <?php the_event_hour( get_field( 'event_date_end' ) ); ?></span>
                                        </p>
									<?php endif; ?>
                                    <p><i class="fa fa-map-marker"
                                          aria-hidden="true"></i><span><?php the_field( 'event_place' ); ?></span></p>
                                    <p class="em-archive-description"><i class="fa fa-align-left"
                                                                         aria-hidden="true"></i><span><?php the_field( 'event_description' ); ?></span>
                                    </p>
                                </div>
                            </div>
                    </a>
                </div>
			<?php endwhile; ?>
		<?php else: ?>
            <div class="em-empty">
                <span>Aucun événement passé</span>
            </div>
		<?php endif; ?>
    </div>

</div>
<?php wp_reset_postdata(); ?>
<?php get_footer(); ?>

