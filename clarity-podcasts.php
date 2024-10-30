<?php
/*
Plugin Name: Clarity Podcasts
Plugin URI: http://blogthememachine.com/clarity-podcasts
Description: This is a plugin that adds a Podcast post type.
Version: 1.0
Author: Mike Smith
Author URI: http://www.blogthememachine.com
*/

/*  Copyright 2016  Mike Smith (email : hi@madebyguerrilla.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Podcast Custom Post Type

if ( ! function_exists('podcasts') ) {

// Register Custom Post Type
function podcasts() {

	$labels = array(
		'name'                  => _x( 'Podcasts', 'Post Type General Name', 'clarity-podcasts' ),
		'singular_name'         => _x( 'Podcast', 'Post Type Singular Name', 'clarity-podcasts' ),
		'menu_name'             => __( 'Podcasts', 'clarity-podcasts' ),
		'name_admin_bar'        => __( 'Podcasts', 'clarity-podcasts' ),
		'archives'              => __( 'Podcast Archives', 'clarity-podcasts' ),
		'parent_item_colon'     => __( 'Parent Podcast:', 'clarity-podcasts' ),
		'all_items'             => __( 'All Podcasts', 'clarity-podcasts' ),
		'add_new_item'          => __( 'Add New Podcast', 'clarity-podcasts' ),
		'add_new'               => __( 'Add New', 'clarity-podcasts' ),
		'new_item'              => __( 'New Podcast', 'clarity-podcasts' ),
		'edit_item'             => __( 'Edit Podcast', 'clarity-podcasts' ),
		'update_item'           => __( 'Update Podcast', 'clarity-podcasts' ),
		'view_item'             => __( 'View Podcast', 'clarity-podcasts' ),
		'search_items'          => __( 'Search Podcasts', 'clarity-podcasts' ),
		'not_found'             => __( 'Not found', 'clarity-podcasts' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'clarity-podcasts' ),
		'featured_image'        => __( 'Featured Image', 'clarity-podcasts' ),
		'set_featured_image'    => __( 'Set featured image', 'clarity-podcasts' ),
		'remove_featured_image' => __( 'Remove featured image', 'clarity-podcasts' ),
		'use_featured_image'    => __( 'Use as featured image', 'clarity-podcasts' ),
		'insert_into_item'      => __( 'Insert into podcast', 'clarity-podcasts' ),
		'uploaded_to_this_item' => __( 'Uploaded to this podcast', 'clarity-podcasts' ),
		'items_list'            => __( 'Podcasts list', 'clarity-podcasts' ),
		'items_list_navigation' => __( 'Podcasts list navigation', 'clarity-podcasts' ),
		'filter_items_list'     => __( 'Filter podcast list', 'clarity-podcasts' ),
	);
	$args = array(
		'label'                 => __( 'Podcast', 'clarity-podcasts' ),
		'description'           => __( 'Nutrition Certification Podcasts', 'clarity-podcasts' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'custom-fields', ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'menu_icon'             => 'dashicons-microphone',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'register_meta_box_cb' => 'add_podcasts_infoboxes'
	);
	register_post_type( 'podcasts', $args );
	// Add The Podcast Info Meta Boxes
	function add_podcasts_infoboxes() {
		add_meta_box('ppt_podcasts_info', 'Podcast Info', 'ppt_podcasts_info', 'podcasts', 'side', 'default');
	}
	
	// The Podcasts Info Data input boxes
	function ppt_podcasts_info() {
		global $post;
		// Noncename needed to verify where the data originated
		echo '<input type="hidden" name="podcastsinfo_noncename" id="podcastsinfo_noncename" value="' .
		wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
		// Get the social data if its already been entered
		$length = get_post_meta($post->ID, '_length', true);
		$year = get_post_meta($post->ID, '_year', true);
		$itunes = get_post_meta($post->ID, '_itunes', true);
		// Echo out the field
		echo '<p>Length:</p>';
		echo '<input type="text" name="_length" value="' . $length  . '" class="widefat" />';
		echo '<p>Year:</p>';
		echo '<input type="text" name="_year" value="' . $year  . '" class="widefat" />';
		echo '<p>iTunes:</p>';
		echo '<input type="text" name="_itunes" value="' . $itunes  . '" class="widefat" />';
	}
	
	// Save the Info Data
	function ppt_save_podcasts_info($post_id, $post) {
		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( !wp_verify_nonce( $_POST['podcastsinfo_noncename'], plugin_basename(__FILE__) )) {
		return $post->ID;
		}
		// Is the user allowed to edit the post or page?
		if ( !current_user_can( 'edit_post', $post->ID ))
			return $post->ID;
		// OK, we're authenticated: we need to find and save the data
		// We'll put it into an array to make it easier to loop though.
		$podcasts_info['_length'] = $_POST['_length'];
		$podcasts_info['_year'] = $_POST['_year'];
		$podcasts_info['_itunes'] = $_POST['_itunes'];
		// Add values of $events_meta as custom fields
		foreach ($podcasts_info as $key => $value) { // Cycle through the $events_meta array!
			if( $post->post_type == 'revision' ) return; // Don't store custom data twice
			$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
			if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
				update_post_meta($post->ID, $key, $value);
			} else { // If the custom field doesn't have a value
				add_post_meta($post->ID, $key, $value);
			}
			if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
		}
	}
	add_action('save_post', 'ppt_save_podcasts_info', 1, 2); // save the custom fields

		
	// Add the Type taxonomy for the Podcasts
    function podcastsinfo_taxonomy() {
       register_taxonomy(
        'podcasts_type',
        'podcasts',
        array(
            'hierarchical' => true,
            'label' => 'Podcast Type',
            'query_var' => true,
            'rewrite' => array('slug' => 'type')
        )
		);
    }
    add_action( 'init', 'podcastsinfo_taxonomy' );

}
add_action( 'init', 'podcasts', 0 );

}


// Recent Podcasts Shortcode

function clarity_podcasts_shortcode($atts){
	
	extract(shortcode_atts(array(
		'posts' => '3',
		'class' => 'col4',
		'order' => 'date',
	), $atts));

	$q = new WP_Query(
		array( 'post_type' => 'podcasts', 'orderby' => $order, 'posts_per_page' => $posts)
	);
	$list = '';

	while($q->have_posts()) : $q->the_post();
	
			// Grab the URL of the featured image
			$featuredimage = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'podcasts', true);
			$thumb_id = get_post_thumbnail_id();
			$thumb_url_array = wp_get_attachment_image_src($thumb_id, 'podcasts', true);
			$thumb_url = $thumb_url_array[0];
			
			// Grab the release date
			$releasedate = get_post_meta( get_the_ID(), '_year', true );
			
		$list .= '<div class="'. $class .' homepodcasts"><a href="' . get_permalink() . '"><img src="' . $thumb_url .'" alt="' . get_the_title() . '" /></a><span>Released: '. $releasedate .'</span></div>';

	endwhile;

wp_reset_query();
return $list . '';
}

add_shortcode('recent-podcasts', 'clarity_podcasts_shortcode');

/* This code is what creates the widget */
class PodcastsWidget extends WP_Widget
{
    function PodcastsWidget(){
		$widget_ops = array('description' => 'Displays recent podcasts');
		$control_ops = array('width' => 300, 'height' => 300);
		parent::WP_Widget(false,$name='Recent Podcasts',$widget_ops,$control_ops);
    }
	
	/* Displays the Widget in the front-end */
    function widget($args, $instance){
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? 'Recent Podcasts' : $instance['title']);
		$PodcastCount = empty($instance['PodcastCount']) ? '' : $instance['PodcastCount'];

		echo $before_widget;

		if ( $title )
		echo $before_title . $title . $after_title;
?>

		<?php $my_query = new WP_Query(array( 'post_type' => 'podcasts', 'showposts' => $PodcastCount )); while ($my_query->have_posts()) : $my_query->the_post(); $do_not_duplicate = $post->ID; ?>
		<div class="claritypodcasts">
			<p><a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('claritypodcasts-widget-image'); ?></a></p>
		</div><!-- END .claritypodcasts -->
		<?php endwhile; ?>

<?php
		echo $after_widget;
	}

	/* Saves the settings. */
    function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = stripslashes($new_instance['title']);
		$instance['PodcastCount'] = stripslashes($new_instance['PodcastCount']);

		return $instance;
	}

	/*Creates the form for the widget in the back-end. */
    function form($instance){
		//Defaults
		$instance = wp_parse_args( (array) $instance, array('title'=>'Recent Podcasts', 'PodcastCount'=>'', 'PostID'=>'') );

		$title = htmlspecialchars($instance['title']);
		$PodcastCount = htmlspecialchars($instance['PodcastCount']);
		$PostID = htmlspecialchars($instance['PostID']);

		# Title
		echo '<p><label for="' . $this->get_field_id('title') . '">' . 'Title:' . '</label><input class="widefat" id="' . $this->get_field_id('title') . '" name="' . $this->get_field_name('title') . '" type="text" value="' . $title . '" /></p>';
		# Podcast Count
		echo '<p><label for="' . $this->get_field_id('PodcastCount') . '">' . 'Update Count (ex: 3):' . '</label><input class="widefat" id="' . $this->get_field_id('PodcastCount') . '" name="' . $this->get_field_name('PodcastCount') . '" type="text" value="' . $PodcastCount . '" /></p>';	
	}

}// end PodcastWidget class

function PodcastWidgetInit() {
  register_widget('PodcastsWidget');
}

add_action('widgets_init', 'PodcastWidgetInit');


// This code adds the default Work stylesheet to your website
function clarity_podcasts_style() {
	// Register the style like this for a plugin:
	wp_register_style( 'clarity-podcasts-custom-post-type', plugins_url( '/style.css', __FILE__ ), array(), '20160203', 'all' );
	// For either a plugin or a theme, you can then enqueue the style:
	wp_enqueue_style( 'clarity-podcasts-custom-post-type' );
}

add_action( 'wp_enqueue_scripts', 'clarity_podcasts_style' );
