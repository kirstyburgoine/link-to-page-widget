<?php
defined('ABSPATH') or die("No script kiddies please!");

/**
 * Plugin Name: KB - Page Links Widget
 * Description: Widget to be able to add CTO buttons to sidebars that link to either pages, posts
 * Version: 1.0
 * Author: Kirsty Burgoine
 * Author URI: http://kirstyburgoine.co.uk/wordpress-plugins/
 * License: GPLv2 or later
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 20014-2016 Kirsty Burgoine 
*/



// Creating the widget 
class btn_widget extends WP_Widget {

function __construct() {
	parent::__construct(
	// Set the ID of the widget
	'btn_widget', 

	// Widget name will appear in the list of available widgets 
	__('Buttons Widget', 'btn_widget_domain'), 

	// Widget description
	array( 'description' => __( 'Widget to add buttons', 'btn_widget_domain' ), ) 
	);
}

// Create the widget front-end
public function widget( $args, $instance ) {

	extract( $args );

	$title = apply_filters( 'widget_title', $instance['title'] );
	$btn_label    = $instance['btn_label']; 
	$shop_link_target    = $instance['shop_link_target']; // CTA Link Target

	$shop_link_target = explode('-', $shop_link_target);

	// before and after widget arguments are defined by themes
	echo $args['before_widget'];

	if ( ! empty( $shop_link_target && $btn_label ) ) :

		if ( $shop_link_target[1] == 'shop' ) :
			echo '<a href="' . get_term_link( intval($shop_link_target[0]), 'product_cat' ) . '" class="sidebar-btn">'. $btn_label .'</a>'; 
		else :
			echo '<a href="' . get_permalink($shop_link_target[0]) . '" class="sidebar-btn">'. $btn_label .'</a>'; 
		endif;

	else : 

	endif;

	echo $args['after_widget'];

}
		
// Widget Backend 
public function form( $instance ) {

if ( isset( $instance[ 'title' ] ) ) :
		$title = $instance[ 'title' ];
	
	else :
		$title = __( 'New title', 'btn_widget_domain' );
	endif;



// Widget admin setting
?>
	<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'btn_label' ); ?>"><?php _e( 'Label for Button:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'btn_label' ); ?>" name="<?php echo $this->get_field_name( 'btn_label' ); ?>" type="text" value="<?php echo $instance['btn_label']; ?>" />
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_id('shop_link_target'); ?>"><?php _e('Link Button to Page or Shop/Event Category:'); ?></label>
   		
   		<?php
   		$args = array( 
   			'taxonomy' => 'product_cat',
   			'orderby' => 'name', 
    		'order' => 'ASC',
   		);
		
		$terms = get_terms('product_cat', $args);
		//var_dump($terms);

		$p_args = array( 
			'hierarchical' => 1,
			'sort_order' => 'asc',
			'sort_column' => 'post_title',
			'post_type' => 'page',
			'post_status' => 'publish'
		);

		$pages = get_pages($p_args);
		//var_dump($pages);

		$shop_link_target    = $instance['shop_link_target']; // CTA Link Target
		$shop_link_target = explode('-', $shop_link_target);

		if ( count($terms) > 0 || count($pages) > 0 ) :
		    echo '<select name="'. $this->get_field_name('shop_link_target') .'" class="my_term-archive" id="' . $this->get_field_id('shop_link_target') . '">';


			if ( count($pages) > 0) :

			    echo '<option value="null">...</option>';    

			    foreach ($pages as $page) {
			    	
			    	if ( $shop_link_target[0] == $page->ID ) : $selected = 'selected="selected"'; else : $selected = ''; endif;

			        echo '<option value="' . $page->ID . '-page" ' . $selected . '>' . $page->post_title . '</option>';    
			    endforeach
			endif;


		    if ( count($terms) > 0) :
			    echo '<option value="null">...</option>';    

			    foreach ($terms as $term) {
			    	
			    	if ( $shop_link_target[0] == $term->term_id ) : $selected = 'selected="selected"'; else : $selected = ''; endif;

			        echo '<option value="' . $term->term_id . '-shop" ' . $selected . '>' . $term->name . '</option>';    
			    }
			endif;



		    echo '</select>';
		endif; ?>
	</p>
<?php 

endforeach
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {

	$instance = array();
	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
	$instance['btn_label'] = strip_tags($new_instance['btn_label']);
	$instance['link_target'] = strip_tags($new_instance['link_target']);
	$instance['shop_link_target'] = strip_tags($new_instance['shop_link_target']);
	


		return $instance;
	}
} // Class btn_widget ends here

// Register and load the widget
function btn_load_widget() {
	register_widget( 'btn_widget' );
}
add_action( 'widgets_init', 'btn_load_widget' );



?>
