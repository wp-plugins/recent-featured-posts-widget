<?php
/*
Plugin Name: Recent & Featured Posts Widget
Description: Display recent posts or manually selected posts with image thumbnails. Show the excerpt directly on the page or as a dropdown.
Version: 1.0.0
Author: graysea
Author URL: http://www.nnekaudoh.com/
License: GPLv2 or later
Text Domain: rfpw_widget
Domain Path: /languages
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit; 

// Initialize RFPW_Widget
add_action( 'widgets_init', 
	create_function( '', 'return register_widget("RFPW_Widget");' ) 
);

// Add style sheet after widget has initialized
add_action( 'init', 'RFPW_Widget::rfpw_load_style' );

/**
 * Add RFPW_Widget class
 */
class RFPW_Widget extends WP_Widget {
	
	/**
	* Register widget with WordPress
	*/
	public function __construct() {
		
		/**
		 * Widget Processes.
		 */
		parent::__construct(
			'rfpw_widget', //Base ID
			__( 'Recent & Featured Posts Widget', 'rfpw_widget' ), //Name
			array( 'description' => __( 'Your site\'s recent posts or your selected posts with thumbnail images and excerpts', 'rfpw_widget' ) )
		);
		
	}
	
	/**
	 * Back-end widget form.
	 *
	 * @param array $instance	Previously saved values from database.
	 */
	public function form( $instance ) {
		//output the options form in the admin
		$title = isset( $instance['title'] ) ? $instance['title'] : __( 'Recent Posts', 'rfpw_widget' );
		$number = isset( $instance['number'] ) ? $instance['number'] : 5;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : 'on';	
		$show_time = isset( $instance['show_time'] ) ? $instance['show_time'] : '';	
		$show_excerpt = isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : '';
		$show_sticky = isset( $instance['show_sticky'] ) ? $instance['show_sticky'] : '';
		$excerpt_len = isset( $instance['excerpt_len'] ) ? $instance['excerpt_len'] : 35;
		$img_width = isset( $instance['img_width'] ) ? $instance['img_width'] : 30;
		$show_featured = isset( $instance['show_featured'] ) ? $instance['show_featured'] : '';	
		$select_posts = isset( $instance['select_posts'] ) ? $instance['select_posts'] : '';		
		$show_images = isset( $instance['show_images'] ) ? $instance['show_images'] : '';
		$select_images = isset( $instance['select_images'] ) ? $instance['select_images'] : '';
		$excerpt_border = isset( $instance['excerpt_border'] ) ? $instance['excerpt_border'] : __( '1px solid black' );
		$show_rectangular_image = isset( $instance['show_rectangular_image'] ) ? $instance['show_rectangular_image'] : '';
		$dropdown_text_width = isset( $instance['dropdown_text_width'] ) ? $instance['dropdown_text_width'] : 60;
		$text_position = isset( $instance['text_position'] ) ? $instance['text_position'] : 0;
		$date_time_separator = isset( $instance['date_time_separator'] ) ? $instance['date_time_separator'] : ', ';
	
?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>">
		<?php _e( 'Title:', 'rfpw_widget' ); ?></label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'number' ); ?>">
		<?php _e( 'Number of posts to show:', 'rfpw_widget' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" type="text" size="3" value="<?php echo esc_attr( $number ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>"></input>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'excerpt_len' ); ?>">
		<?php _e( 'Number of words in excerpt: (maximum = 150)', 'rfpw_widget' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'excerpt_len' ); ?>" type="text" size="3" value="<?php echo esc_attr( $excerpt_len ); ?>" name="<?php echo $this->get_field_name( 'excerpt_len' ); ?>"></input>
		</p>

		<p>
		<label for="<?php echo $this->get_field_id( 'img_width' ); ?>">
		<?php _e( 'Image width in percent:', 'rfpw_widget' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'img_width' ); ?>" type="text" size="3" value="<?php echo esc_attr( $img_width ); ?>" name="<?php echo $this->get_field_name( 'img_width' ); ?>"></input>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'dropdown_text_width' ); ?>">
		<?php _e( 'Width of post title/date/time in percent: (This only works if excerpt is a dropdown)', 'rfpw_widget' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'dropdown_text_width' ); ?>" type="text" size="3" value="<?php echo esc_attr( $dropdown_text_width ); ?>" name="<?php echo $this->get_field_name( 'dropdown_text_width' ); ?>"></input>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'text_position' ); ?>">
		<?php _e( 'Text\'s top position in pixels: (This can be negative)', 'rfpw_widget' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'text_position' ); ?>" type="text" size="3" value="<?php echo esc_attr( $text_position ); ?>" name="<?php echo $this->get_field_name( 'text_position' ); ?>"></input>
		</p>	
		
		<p>
		<label for="<?php echo $this->get_field_id( 'excerpt_border' ); ?>">
		<?php _e( 'Border of the dropdown excerpt: (e.g., 2px solid #666)', 'rfpw_widget' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'excerpt_border' ); ?>" type="text" size="3" value="<?php echo esc_attr( $excerpt_border ); ?>" name="<?php echo $this->get_field_name( 'excerpt_border' ); ?>"></input>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'date_time_separator' ); ?>">
		<?php _e( 'Separator between date and time: (This can be a space)', 'rfpw_widget' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'date_time_separator' ); ?>" type="text" size="3" value="<?php echo esc_attr( $date_time_separator ); ?>" name="<?php echo $this->get_field_name( 'date_time_separator' ); ?>"></input>
		</p>
		
		<p>
		<input id="<?php echo $this->get_field_id( 'show_date' ); ?>" class="checkbox" type="checkbox" <?php checked( $show_date, 'on' ); ?> name="<?php echo $this->get_field_name( 'show_date' ); ?>"></input>
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>">
		<?php _e( 'Display post date?', 'rfpw_widget' ); ?></label>
		</p>
		
		<p>
		<input id="<?php echo $this->get_field_id( 'show_time' ); ?>" class="checkbox" type="checkbox" <?php checked( $show_time, 'on' ); ?> name="<?php echo $this->get_field_name( 'show_time' ); ?>"></input>
		<label for="<?php echo $this->get_field_id( 'show_time' ); ?>">
		<?php _e( 'Display post time?', 'rfpw_widget' ); ?></label>
		</p
		
		<p>
		<input id="<?php echo $this->get_field_id( 'show_excerpt' ); ?>" class="checkbox" type="checkbox" <?php checked( $show_excerpt, 'on' ); ?> name="<?php echo $this->get_field_name( 'show_excerpt' ); ?>"></input>
		<label for="<?php echo $this->get_field_id( 'show_excerpt' ); ?>">
		<?php _e( 'Show excerpt below image and post title? (excerpt will no longer display as a dropdown)', 'rfpw_widget' ); ?></label>
		</p>
		
		<p>
		<input id="<?php echo $this->get_field_id( 'show_sticky' ); ?>" class="checkbox" type="checkbox" <?php checked( $show_sticky, 'on' ); ?> name="<?php echo $this->get_field_name( 'show_sticky' ); ?>"></input>
		<label for="<?php echo $this->get_field_id( 'show_sticky' ); ?>">
		<?php _e( 'Include sticky posts?', 'rfpw_widget' ); ?></label>
		</p>
		
		<p>
		<input id="<?php echo $this->get_field_id( 'show_rectangular_image' ); ?>" class="checkbox" type="checkbox" <?php checked( $show_rectangular_image, 'on' ); ?> name="<?php echo $this->get_field_name( 'show_rectangular_image' ); ?>"></input>
		<label for="<?php echo $this->get_field_id( 'show_rectangular_image' ); ?>">
		<?php _e( 'Display rectangular image instead of circular image?', 'rfpw_widget' ); ?></label>
		</p>
		
		<hr>
		<p>
		<input id="<?php echo $this->get_field_id( 'show_featured' ); ?>" class="checkbox" type="checkbox" <?php checked( $show_featured, 'on' ); ?> name="<?php echo $this->get_field_name( 'show_featured' ); ?>"></input>
		<label for="<?php echo $this->get_field_id( 'show_featured' ); ?>">
		<?php _e( 'Show the posts listed below?', 'rfpw_widget' ); ?></label>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'select_posts' ); ?>">
		<?php _e( 'Enter comma-separated post ID numbers in the order they\'ll appear in the widget. E.g., 23, 30, 40', 'rfpw_widget' ); ?></label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'select_posts' ); ?>" name="<?php echo $this->get_field_name( 'select_posts' ); ?>" value="<?php echo esc_attr( $select_posts ); ?>">
		</p>
		
		<input id="<?php echo $this->get_field_id( 'show_images' ); ?>" class="checkbox" type="checkbox" <?php checked( $show_images, 'on' ); ?> name="<?php echo $this->get_field_name( 'show_images' ); ?>"></input>
		<label for="<?php echo $this->get_field_id( 'show_images' ); ?>">
		<?php _e( 'Show the images listed below?', 'rfpw_widget' ); ?></label>
		</p>
		
		<p>
		<label for="<?php echo $this->get_field_id( 'select_images' ); ?>">
		<?php _e( 'Enter comma-separated image ID numbers in the order they\'ll appear in the widget. E.g., 23,30,40', 'rfpw_widget' ); ?></label>
		<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'select_images' ); ?>" name="<?php echo $this->get_field_name( 'select_images' ); ?>" value="<?php echo esc_attr( $select_images ); ?>">
		</p>
			
<?php 
	} // function form
	
	/**
	 * Sanitize and save widget form values.
	 *
	 * @param array $new_instance	Values just sent to be saved.
	 * @param array $old_instance	Previously saved values from database.
	 *
	 * @return array Updated values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );	
		$instance['number'] = strip_tags( $new_instance['number'] );				 
		$instance['show_date'] = strip_tags( $new_instance['show_date'] );
		$instance['show_time'] = strip_tags( $new_instance['show_time'] );
		$instance['show_excerpt'] = strip_tags( $new_instance['show_excerpt'] );
		$instance['show_sticky'] = strip_tags( $new_instance['show_sticky'] );
		$instance['excerpt_len'] = strip_tags( $new_instance['excerpt_len'] );
		$instance['img_width'] = strip_tags( $new_instance['img_width'] );
		$instance['show_featured'] = strip_tags( $new_instance['show_featured'] );
		$instance['select_posts'] = strip_tags( $new_instance['select_posts'] );		
		$instance['show_images'] = strip_tags( $new_instance['show_images'] );
		$instance['select_images'] = strip_tags( $new_instance['select_images'] );
		$instance['excerpt_border'] = strip_tags( $new_instance['excerpt_border'] );
		$instance['show_rectangular_image'] = strip_tags( $new_instance['show_rectangular_image'] );
		$instance['dropdown_text_width'] = strip_tags( $new_instance['dropdown_text_width'] );
		$instance['text_position'] = strip_tags( $new_instance['text_position'] );
		$instance['date_time_separator'] = strip_tags( $new_instance['date_time_separator'] );
		
		return $instance;
	}
	
	/**
	 * Display recent or featured posts.
	 *
	 * @param array $args		Widget arguments.
	 * @param array $instance	Saved values from database.
	 */
	public function widget( $args, $instance ) {	
		$img = array(); // Array of images attached to a post
		$is_image_id_num = ''; // Is each user-inputted image ID a number?
		$is_post_id_num = ''; // Is each user-inputted post ID a number?
		$image_ids = ''; // IDs of images to display
		$img_index = 0; // Index for the inputted image IDs
		$show_content = ''; // Content for the 'show excerpt' setting
		$dropdown_content = ''; // Content for the 'dropdown excerpt' setting
		$ignore_sticky = true; // Ignore sticky posts (for WP_Query);	
		
		// Get all widget instances
		if ( isset( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
		}
		else {
			$title = __( 'Recent Posts', 'rfpw_wigdet' );
		}		
		// If the number of posts to show is zero, do nothing
		if ( isset( $instance['number'] ) && $instance['number'] == 0 ) { 
			return;
		}
		
		$num = ! empty( $instance['number'] ) ? (int)$instance['number'] : 5;	
		$excerpt_len = isset( $instance['excerpt_len'] ) ? (int)$instance['excerpt_len'] : 35;
		$show_date = isset( $instance['show_time'] ) ? $instance['show_date'] : 'on';
		$show_time = isset( $instance['show_time'] ) ? $instance['show_time'] : '';
		$show_sticky = isset( $instance['show_sticky'] ) ? $instance['show_sticky'] : '';
		$show_images = isset( $instance['show_images'] ) ? $instance['show_images'] : '';
		$select_images = isset( $instance['select_images'] ) ? $instance['select_images'] : '';
		$show_featured = isset( $instance['show_featured'] ) ? $instance['show_featured'] : '';
		$select_posts = isset( $instance['select_posts'] ) ? $instance['select_posts'] : '';
		$show_excerpt = isset( $instance['show_excerpt'] ) ? $instance['show_excerpt'] : '';
		$excerpt_border = isset( $instance['excerpt_border'] ) ? $instance['excerpt_border'] : __( '1px solid black' );
		$show_rectangular_image = isset( $instance['show_rectangular_image'] ) ? $instance['show_rectangular_image'] : '';
		$dropdown_text_width = ! empty( $instance['dropdown_text_width'] ) ? (int)$instance['dropdown_text_width'] : 60;
		$text_position = ! empty( $instance['text_position'] ) ? (int)$instance['text_position'] : 0;
		$date_time_separator = isset( $instance['date_time_separator'] ) ? $instance['date_time_separator'] : ', ';
		
		if ( isset( $instance['img_width'] ) && $instance['img_width'] == 0 ) {
			$img_width = 0;
		}
		elseif ( ! empty( $instance['img_width'] ) ) {
			$img_width = (int)$instance['img_width'];
		}
		else {
			$img_width = 30;
		}
			
		if ( $num < -1 ) {
			$num = 5;
		}		
		
		if ( $show_sticky == 'on' ) {
			$ignore_sticky = false;
		}	
		
		// Arguments array for WP_Query
		$post_args = array(
			'posts_per_page' 	  => $num,
			'post_status'		  => 'publish',
			'post_type'			  => 'post',
			'ignore_sticky_posts' => $ignore_sticky,
		);
		
		// Convert string of image IDs to an array
		if ( $show_images == 'on' && ! empty( $select_images ) ) { 
			$image_ids = $select_images;
			$images_arr = $this->rfpw_convert_to_array( $image_ids );
			$image_ids = $images_arr[0];
			$is_image_id_num = $images_arr[1];
		}
			
		// If user enters post IDs, turn string into array, and add $post_args array for WP_Query
		if ( $show_featured == 'on' && ! empty( $select_posts ) ) { 
			$post_ids = $select_posts; 		
			$posts_arr = $this->rfpw_convert_to_array( $post_ids );
			$post_ids = $posts_arr[0];
			$is_post_id_num = $posts_arr[1];
			
			// If the post IDs are numerical, add/change properties and values to $post_args array
			if ( $is_post_id_num == true ) {
				$number = count( $post_ids );
				$post_args['post__in'] = $post_ids;
				$post_args['orderby'] = 'post__in';				
				$post_args['posts_per_page'] = $number;
			}
			
		} 
		
		$query = new WP_Query( $post_args );
		
		// Run the loop to get the posts
		while ( $query->have_posts() ) {
			$query->the_post();
					
			$img = get_attached_media( 'image' ); // Get all images attached to the post
			
			// Get the first image attached to the post, then break the loop
			foreach($img as $key => $val) {						
				$img_arr = wp_get_attachment_image_src( $val->ID, 'thumbnail' );	
				break;
			} 
			
			// Set the image's border radius
			if ( $show_rectangular_image == 'on' ) {
				$border_radius = 'border-radius: 0;';
			}
			else {
				$border_radius = '';
			}
					
			// Create style property for the inputted image width
			$img_style = 'style="width: ' . $img_width . '&#37;; ' . $border_radius . '"';
			
			
			// If there's an image ID given or if the post has an image, create HTML for the image
			if ( $show_images == 'on' && ! empty( $image_ids[ $img_index ] ) && $is_image_id_num == true  ) {
				$the_img = wp_get_attachment_image_src( $image_ids[ $img_index ], 'thumbnail' );
				$image = '<div class="rfpw-image-link"><a href="' . get_permalink() . '"><img ' . $img_style . ' src="' . $the_img[0] . '"></a></div>';

				$img_index++;
				$img_arr = array(); // Reset the array for the next loop
			}
			elseif ( ! empty( $img_arr[0] ) ) {
				$image = '<div class="rfpw-image-link"><a href="' . get_permalink() . '"><img ' . $img_style . ' src="' . $img_arr[0] . '"></a></div>';
			
				$img_arr = array(); // Reset the array for the next loop
			}
			else {
				$image = '';	
			}		
			
			// Create HTML for the post date
			if ( $show_date == 'on' ) {
				$the_date =  '<span class="rfpw-date">' . get_the_date() . '</span>';
			}
			else {
				$the_date = '';
			}
			
			// Create HTML for post time
			if ( $show_time == 'on' ) {
				$the_time = '<span class="rfpw-time">' . get_the_time() . '</span>';
			}
			else {
				$the_time = '';
			}
			
			// Create separator for date and time
			if ( $show_date == 'on' && $show_time == 'on' ) {
				$separator = $date_time_separator;
			}
			else {
				$separator = '';
			}
			$date_time = '<div class="rfpw-date-time">' . $the_date . $separator . $the_time . '</div>';
			
			// Set the maximum excerpt length/word count
			add_filter( 'excerpt_length', function() {
				return 150;
			}, 999 );
			
			// Filter excerpt more
			add_filter( 'excerpt_more', function() {
				return '...';
			} );
			
			// Get the excerpt, convert to an array, then back to a string
			$post_excerpt = get_the_excerpt(); 
			$post_excerpt = str_ireplace( '&nbsp;', ' ', $post_excerpt );
			$new_excerpt = preg_split( '/\s+/', $post_excerpt ); // Remove extra spaces from excerpt
			$count = count( $new_excerpt );
			array_splice( $new_excerpt, $excerpt_len );			
			$excerpt = implode( ' ', $new_excerpt ); // Convert the excerpt array to string

			if ( $excerpt_len < $count ) {
				$excerpt .= '...';
			}
			
			// If excerpt length is not empty, create HTML for the excerpt
			if ( empty( $excerpt_len ) ) {
				$rfpw_dropdown_excerpt = '';
				$rfpw_show_excerpt = '';
			}
			elseif ( $show_excerpt == 'on' ) { 
				$rfpw_show_excerpt = '<div class="rfpw-show-excerpt">' . $excerpt . '</div>';
			}
			else {
				$border_style = 'style="border:' . $excerpt_border . ';"';
				$rfpw_dropdown_excerpt = '<div ' . $border_style . ' class="rfpw-dropdown-excerpt">' . $excerpt . '</div>';
			}
			
			// Create the widget's HTML content
			if ( $show_excerpt == 'on' ) {
				$text_pos_style = 'style="top: ' . $text_position . 'px;"';
				$show_content .= 
					'<li class="rfpw-show-content">'
					 . $image . 
					 '<div ' . $text_pos_style . 'class="rfpw-show-text">
					 <a href="' . get_permalink() . '">' . get_the_title() . 
					 '</a>' . $date_time . $rfpw_show_excerpt . 
					 '</div>
					 </li>';
			}
			else {
				$text_pos_style = ' top: ' . $text_position . 'px;';
				$text_width_style = 'style="width:' . $dropdown_text_width . '&#37;;' . $text_pos_style . '"';
				$dropdown_content .= 
						'<li class="rfpw-dropdown-content">'
						 . $image . 
						 '<div '. $text_width_style . ' class="rfpw-dropdown-text">
						 <a href="' . get_permalink() . '">' . get_the_title() . 
						 '</a>' . $date_time .
						 '</div>' .
						 $rfpw_dropdown_excerpt .
						 '</li>';
			}
			 			 
		} // while have_posts
		
		wp_reset_postdata();

		// Echo widget content
		echo $args['before_widget'];
		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		
		if ( $show_excerpt == 'on' ) {
			$show_content = '<ul class="rfpw-show-post">' . $show_content . '</ul>';			
			echo $show_content;
		}
		else { 
			$dropdown_content = '<ul class="rfpw-dropdown-post">' . $dropdown_content . '</ul>';	
			echo $dropdown_content;
		}		
		echo $args['after_widget'];
		
	} // function widget
	
	/**
	 * Convert user-inputted post IDs and image IDs to array.
	 *
	 * @param string $ids		Post or image ID numbers.
	 * @param bool $is_id_num	True if IDs are numerical.
	 *
	 * @return array 			IDs in an array and true if IDs are numerical.
	 */
	function rfpw_convert_to_array( $ids, $is_id_num = true ) {
		// Trim the IDs and convert to an array
		$ids = rtrim( $ids );
		$ids = rtrim( $ids, ',' );
		$ids = explode( ',', $ids );
		$ids = array_map( 'trim', $ids );
		
		// Check if each value in the array is a number
		foreach( $ids as $key => $val ) {
			if ( ! is_numeric( $val ) ) {
				$is_id_num = false;
				break;
			}
		}
		
		return array( $ids, $is_id_num );
	}
	
	/**
	* Enqueue style sheet if the widget is active.
	*/
	static function rfpw_load_style() {
		if ( is_active_widget( false, false, 'rfpw_widget', true ) ) {
			wp_enqueue_style( 'rfpw-style', plugins_url( 'css/style.css', __FILE__ ) );	
		}
	
	}

}// class RFPW_Widget