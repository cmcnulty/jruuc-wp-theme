<?php


add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
function my_theme_enqueue_styles() {

    $parent_style = 'uuais-entry'; // Any UUA specific theme

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
	wp_enqueue_script( 'jruuc-script', get_stylesheet_directory_uri() . '/js/script.js', array ( 'jquery' ), 0.1, true);
}

// Add font awesome & bootstrap - Still needed for share logos on side-bars 2020-04-08 - CEM
function sociallogos_css() {
	wp_enqueue_style("sociallogos", 'https://jruuc.org/wp-content/plugins/jetpack/_inc/social-logos/social-logos.min.css?ver=1');
}

add_action( 'wp_enqueue_scripts', 'sociallogos_css' );


/**
 * Added by CEM to add visual indicator for where there's a recording to the archive page
 */
function add_audio_icon( $title, $id = null ) {
	$audio_file_meta = get_post_meta($id, 'audio_file');
	if (is_page('past-worship-services') && !empty($audio_file_meta[0])) {
		$title = '<span role="img" aria-label="Recorded">' . mb_convert_encoding('&#x1F3A7;', 'UTF-8', 'HTML-ENTITIES') . "</span> " . $title;
	}
    return $title;
}

add_filter( 'the_title', 'add_audio_icon', 10, 2 );

add_filter('widget_posts_args','modify_widget');
function modify_widget() {
	$r = array( 'category__not_in' =>  array( 183, 218 ));
	return $r;
}


function parent_override() {

    unregister_sidebar('sidebar-footer-one');
	unregister_sidebar('sidebar-footer-two');
	unregister_sidebar('sidebar-footer-three');

	register_sidebar( array(
		'name'          => __( 'Footer 1', 'uuatheme' ),
		'id'            => 'sidebar-footer-one',
		'description'   => __( 'Add widgets here to appear in your footer.', 'uuatheme' ),
		'before_widget' => '<section id="%1$s" class="widget col-md-4 %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 2', 'uuatheme' ),
		'id'            => 'sidebar-footer-two',
		'description'   => __( 'Add widgets here to appear in your footer.', 'uuatheme' ),
		'before_widget' => '<section id="%1$s" class="widget col-md-4 %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );

	register_sidebar( array(
		'name'          => __( 'Footer 3', 'uuatheme' ),
		'id'            => 'sidebar-footer-three',
		'description'   => __( 'Add widgets here to appear in your footer.', 'uuatheme' ),
		'before_widget' => '<section id="%1$s" class="widget col-md-4 %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h4 class="widget-title">',
		'after_title'   => '</h4>',
	) );
}
add_action( 'widgets_init', 'parent_override', 11 );



// function that runs when shortcode is called
function jruuc_themes_shortcode() {
	// setlocale(LC_ALL, 'cs_CZ');


	$taxonomy = 'uu_service_topics';
	$monthly_term = get_term_by( 'slug', 'monthly-themes',$taxonomy);
	$list_start = '<ul class="uu_topics">';
	$wsubargs = array(
		'hierarchical' => 1,
		'show_option_none' => '',
		'hide_empty' => 1,
		'parent' => $monthly_term->term_id,
		'taxonomy' => $taxonomy
	 );
	 $wsubcats = get_terms($wsubargs);
	 $messages = array();

	 foreach ($wsubcats as $wsc) {

		$services = get_posts(
			array(
				'post_type' => 'uu_services',
				'numberposts' => 1,
				'tax_query' => array(
					array(
						'taxonomy' => $taxonomy,
						'field' => 'term_id',
						'terms' => $wsc->term_id, /// Where term_id of Term 1 is "1".
						'include_children' => false
					)
				)
			)
		);
		$service_timestamp = get_post_meta($services[0]->ID, '_services_unixtime', true);


		$topic_month = date('F Y', $service_timestamp);

		$link = get_term_link($wsc->slug, $wsc->taxonomy);
		$count = wp_get_topic_postcount($wsc->term_id);
		$services = sprintf(ngettext("%d service", "%d services", $count), $count);
		$description = nl2br($wsc->description);
		$message = <<<EOT
			<li class="uu_topic uu_topic_{$wsc->slug}">
			<span class="topic_header"><a href="{$link}">$wsc->name</a> $topic_month - $services</span>
			<p>$description</p>
			</li>
EOT;
		$messages[$service_timestamp] = $message;
	};

	krsort($messages);

	// Output needs to be return
	$message = $list_start . implode( '', $messages ) . '</ul>';
	return $message;
}

function wp_get_topic_postcount($id) {
    $args = array(
      'post_type'     => 'uu_services',
      'post_status'   => 'publish', // just tried to find all published post
      'posts_per_page' => -1,  //show all
      'tax_query' => array(
        'relation' => 'AND',
        array(
          'taxonomy' => 'uu_service_topics',
          'field' => 'id',
          'terms' => array( $id )
        )
      )
    );
    $query = new WP_Query($args);
    return (int)$query->post_count;
}

// register shortcode
add_shortcode('service_themes', 'jruuc_themes_shortcode');



function jruuc_html_excerpt( $text ) {
	global $post;
	if ( '' == $text ) {
		$permalink = get_permalink();
		$text = get_the_content('');
		$text = apply_filters('the_content', $text);
		$text = str_replace('\]\]\>', ']]&gt;', $text);
		/*just add all the tags you want to appear in the excerpt --
		be sure there are no white spaces in the string of allowed tags */
		$text = strip_tags($text,'<p><br><b><a><em><strong>');
		/* you can also change the length of the excerpt here, if you want */
		$excerpt_length = 45;
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words)> $excerpt_length) {
			array_pop($words);
			array_push($words, '<a href="' . $permalink . '">'.'<strong>... read more</strong>.</a>');
			$text = implode(' ', $words);
		}
	}
	return $text;
}
/* remove the default filter */
remove_filter( 'get_the_excerpt', 'uua_html_excerpt' );
/* now, add our filter */
add_filter( 'get_the_excerpt', 'jruuc_html_excerpt' );


?>