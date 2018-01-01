<?php get_header(); ?>
    <div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
		<?php if ( function_exists( 'bcn_display' ) ) {
			bcn_display();
		}
		?>
    </div>
    <div class="entry-content sem-single">
        <div class="sem-single-info">
            <span class="date">Publié le  <a href=""><?php echo get_the_date(); ?></a></span>
        </div>
		<?php if ( get_field( 'event_cancel' ) ): ?>
        <div class="sem-single-cancel-info">
			<?php if ( get_field( 'event_cancel_reason' ) ): ?>
                <p><strong>Événement annulé</strong> : <?php the_field( 'event_cancel_reason' ) ?></p>
			<?php else: ?>
                <p><strong>Événement annulé</strong></p>
			<?php endif ?>
        </div>
        <div class="sem-cancel-event">
			<?php endif; ?>
            <div class="sem-single-head">
                <div class="sem-single-title col-md-4">
					<?php the_title( '<h3 class="sem-h3">', '</h3>' ); ?>
                </div>
                <div class="sem-single-detail col-md-8">
                    <div class="sem-single-detail-date col-md-6">
						<?php if ( is_the_same_date( get_field( 'event_date_start' ), get_field( 'event_date_end' ) ) ) : ?>
                            <span><i class="fa fa-clock-o"
                                     aria-hidden="true"></i> <?php the_event_date( get_field( 'event_date_start' ) ); ?></span>
                            <span><i class="fa fa-clock-o"
                                     aria-hidden="true"></i> <?php the_event_hour( get_field( 'event_date_start' ) ); ?>
                                - <?php the_event_hour( get_field( 'event_date_end' ) ); ?></span>
						<?php else : ?>
                            <span><i class="fa fa-clock-o"
                                     aria-hidden="true"></i> <?php the_event_date( get_field( 'event_date_start' ) ); ?>
                                - <?php the_event_date( get_field( 'event_date_end' ) ); ?></span>
                            <span><i class="fa fa-clock-o"
                                     aria-hidden="true"></i> <?php the_event_hour( get_field( 'event_date_start' ) ); ?>
                                - <?php the_event_hour( get_field( 'event_date_end' ) ); ?></span>
						<?php endif; ?>
                    </div>
                    <div class="sem-single-detail-place col-md-4">
                        <span><i class="fa fa-map-marker"
                                 aria-hidden="true"></i> <?php the_field( 'event_place' ); ?></span>
                    </div>
                </div>
            </div>
			<?php if ( have_posts() ) : ?>
				<?php while ( have_posts() ) : the_post(); ?>
                    <article class="sem-single-content">
                        <div class="content">
							<?php the_content(); ?>
                        </div>
                    </article>
				<?php endwhile; ?>
			<?php endif; ?>
			<?php if ( get_field( 'event_cancel' ) ): ?>
        </div>
	<?php endif; ?>
    </div>
<?php get_footer(); ?>