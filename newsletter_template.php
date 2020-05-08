<?php 
/** 
 * Template Name: JRUUC Newsletter
 *
 *
 *
 */ 

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="main col-md-12" role="main">


		<?php 
		$query_args = array(
			'post_type'      => 'uu_services',
			'meta_key'       => '_services_unixtime',
			'orderby'        => 'meta_value',
			'order'          => ( $atts['order'] ) ? esc_html( $atts['order'] ) : 'ASC',
			'posts_per_page' => 1,
			'paged'          => 0,
		);
		// get upcoming services
		$query_args['meta_query'] = array(
			array(
				'key'     => '_services_unixtime',
				'compare' => '>=',
				'value'   => current_time('timestamp'),
			),
		);
		$my_posts = new WP_Query($query_args);
		while($my_posts->have_posts()) : $my_posts->the_post(); ?>
			<h3><a href="<?php the_permalink() ?>" rel="bookmark">Upcoming Service: <?php the_title(); ?></a></h3>
			<p><?php echo uua_service_datetime();?></p>
			<?php
			// Service Speaker(s)
			echo get_the_term_list(
				get_the_ID(),
				'uu_service_speaker',
				'<span class="speaker">',
				', ',
				'</span>'
			);
			?>			
			<p>
			<?php the_content(); ?>
			</p>
		<?php endwhile; ?> 

		<?php wp_reset_postdata(); ?>

		<?php $catquery = new WP_Query( 'category_name=musings&posts_per_page=1&orderBy=date' ); ?>
		<?php while($catquery->have_posts()) : $catquery->the_post(); ?>
			<h3><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
			<p><img src="https://jruuc.org/wp-content/uploads/2018/05/Karen-photo-420x370.jpg" class="alignright wp-post-image" alt="" srcset="https://jruuc.org/wp-content/uploads/2018/05/Karen-photo-420x370.jpg 420w, https://jruuc.org/wp-content/uploads/2018/05/Karen-photo-768x677.jpg 768w, https://jruuc.org/wp-content/uploads/2018/05/Karen-photo-1024x903.jpg 1024w" sizes="(max-width: 300px) 100vw, 300px" width="300" height="264">
			<?php the_content(); ?>
			</p>
		<?php endwhile; ?> 

		<?php wp_reset_postdata(); ?>
		
		<?php $catquery = new WP_Query( 'category_name=theme&posts_per_page=1&orderBy=date' ); ?>
		<?php while($catquery->have_posts()) : $catquery->the_post(); ?>
			<h3><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
			<p>
				<?php the_content(); ?>
			</p>
		<?php endwhile; ?> 

		<?php wp_reset_postdata(); ?>		

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_footer(); ?>
