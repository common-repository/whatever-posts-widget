<?php
/**
 * Plugin Name: Whatever Posts Widget
 * Description: Display posts from any post types with thumbnail
 * Author: Nashita
 * Author URI: https://www.devnash.com
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: www
 * Domain Path: domain/path
 */

function whatever_posts_widget() {
	register_widget( 'Whatever_Posts_Widget' );
}
add_action( 'widgets_init', 'whatever_posts_widget' );

class Whatever_Posts_Widget extends WP_Widget {

	function __construct() {
		parent::__construct( 'whatever-posts-widget', __('Whatever Posts', 'www'), array( 'description' => __( 'Display posts from any post types with thumbnail', 'www' ), ) );
	}

	public function widget( $args, $instance ) {
		wp_enqueue_style( 'whatever-posts-style', plugins_url('style.css', __FILE__) );
		$title = apply_filters( 'widget_title', $instance['title'] );
		$numberposts = ($instance['numberposts']) ? $instance['numberposts'] : 3;
		$orderby = ($instance['orderby']) ? $instance['orderby'] : 'date';
		$order = ($instance['order']) ? $instance['order'] : 'DESC';
		$post_type = ($instance['post_type']) ? $instance['post_type'] : 'post';
		$thumbnailwidth = ($instance['thumbnail_width']) ? $instance['thumbnail_width'] : 50;
		$thumbnailheight = ($instance['thumbnail_height']) ? $instance['thumbnail_height'] : 50;
		$style = ($instance['style']) ? $instance['style'] : 'onerow';
		echo $args['before_widget'];

		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];

		$arg = array(
			'post_type' => $post_type,
			'orderby' => $orderby,
			'order' => $order,
			'showposts' => $numberposts,
		);
		$query = new WP_Query($arg);
		if($query->have_posts()) :
			echo '<div class="whatever-posts post-wrapper">';
			while($query->have_posts()) : $query->the_post();
				echo '<div class="whatever-post post '.$style.'">';
				echo '<div class="image"><a href="'.get_permalink().'">'.get_the_post_thumbnail( get_the_ID(), array( $thumbnailwidth, $thumbnailheight ) ).'</a></div>';
				echo '<div class="content">';
				echo '<div class="title"><a href="'.get_permalink().'">'.get_the_title().'</a></div>';
				echo '<div class="excerpt">'.get_the_excerpt().'</div>';
				echo '</div>';
				echo '</div>';
			endwhile;
			echo '</div>';
		endif;
		wp_reset_query();

		echo $args['after_widget'];
	}

	public function form( $instance ) {
		$title = ( isset( $instance[ 'title' ] ) ) ? $instance[ 'title' ] : __( 'New title', 'www' );
		$numberposts = ( isset( $instance[ 'numberposts' ] ) ) ? $instance[ 'numberposts' ] : 3;
		$orderby = ( isset( $instance[ 'orderby' ] ) ) ? $instance[ 'orderby' ] : __( 'date', 'www' );
		$order = ( isset( $instance[ 'order' ] ) ) ? $instance[ 'order' ] : __( 'DESC', 'www' );
		$post_type = ( isset( $instance[ 'post_type' ] ) ) ? $instance[ 'post_type' ] : __( 'post', 'www' );
		$thumbnailwidth = ( isset( $instance[ 'thumbnail_width' ] ) ) ? $instance[ 'thumbnail_width' ] : 50;
		$thumbnailheight = ( isset( $instance[ 'thumbnail_height' ] ) ) ? $instance[ 'thumbnail_height' ] : 50;
		$style = ( isset( $instance[ 'style' ] ) ) ? $instance[ 'style' ] : __( 'onerow', 'www' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'post_type' ); ?>"><?php _e( 'Post type:' ); ?></label> 
			<select name="<?php echo $this->get_field_name( 'post_type' ); ?>" id="<?php echo $this->get_field_id( 'post_type' ); ?>" type="text">
				<option <?php selected( $post_type, 'post'); ?> value="post"><?php echo _e('Post','www'); ?></option>			
				<?php  
				$args = array(
					'public'   => true,
					'_builtin' => false
				);

				$output = 'names';
				$operator = 'and';

				$posttypes = get_post_types( $args, $output, $operator ); 
				if(!empty($posttypes)) {
					foreach ($posttypes as $key => $value) {	
						?>
						<option <?php selected( $post_type, $value); ?> value="<?php echo $value; ?>"><?php echo ucfirst($value); ?></option>
						<?php
					}
				}
				?>
			</select>
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id( 'numberposts' ); ?>"><?php _e( 'Number of posts:' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'numberposts' ); ?>" name="<?php echo $this->get_field_name( 'numberposts' ); ?>" type="text" value="<?php echo esc_attr( $numberposts ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'orderby' ); ?>"><?php _e( 'Order by:' ); ?></label> 
			<select name="<?php echo $this->get_field_name( 'orderby' ); ?>" id="<?php echo $this->get_field_id( 'orderby' ); ?>" type="text">
				<option <?php selected( $orderby, 'date'); ?> value="date"><?php echo _e('Date','www'); ?></option>
				<option <?php selected( $orderby, 'name'); ?> value="name"><?php echo _e('Name','www'); ?></option>			
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'order' ); ?>"><?php _e( 'Order:' ); ?></label> 
			<select name="<?php echo $this->get_field_name( 'order' ); ?>" id="<?php echo $this->get_field_id( 'order' ); ?>" type="text">
				<option <?php selected( $order, 'DESC'); ?> value="DESC"><?php echo _e('Descending','www'); ?></option>
				<option <?php selected( $order, 'ASC'); ?> value="ASC"><?php echo _e('Ascending','www'); ?></option>			
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'thumbnail_width' ); ?>"><?php _e( 'Thumbnail width (px):' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'thumbnail_width' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_width' ); ?>" type="text" value="<?php echo esc_attr( $thumbnailwidth ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'thumbnail_height' ); ?>"><?php _e( 'Thumbnail height (px):' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'thumbnail_height' ); ?>" name="<?php echo $this->get_field_name( 'thumbnail_height' ); ?>" type="text" value="<?php echo esc_attr( $thumbnailheight ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Style:' ); ?></label> 
			<select name="<?php echo $this->get_field_name( 'style' ); ?>" id="<?php echo $this->get_field_id( 'style' ); ?>" type="text">
				<option <?php selected( $style, 'onerow'); ?> value="onerow"><?php echo _e('Row','www'); ?></option>			
				<option <?php selected( $style, 'onecolumn'); ?> value="onecolumn"><?php echo _e('Column','www'); ?></option>
			</select>
		</p>
		<?php 
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : __( 'New title', 'www' );
		$instance['numberposts'] = ( ! empty( $new_instance['numberposts'] ) ) ? strip_tags( $new_instance['numberposts'] ) : 3;
		$instance['orderby'] = ( ! empty( $new_instance['orderby'] ) ) ? strip_tags( $new_instance['orderby'] ) : __( 'date', 'www' );
		$instance['order'] = ( ! empty( $new_instance['order'] ) ) ? strip_tags( $new_instance['order'] ) : __( 'DESC', 'www' );
		$instance['post_type'] = ( ! empty( $new_instance['post_type'] ) ) ? strip_tags( $new_instance['post_type'] ) : __( 'post', 'www' );
		$instance['thumbnail_width'] = ( ! empty( $new_instance['thumbnail_width'] ) ) ? strip_tags( $new_instance['thumbnail_width'] ) : 50;
		$instance['thumbnail_height'] = ( ! empty( $new_instance['thumbnail_height'] ) ) ? strip_tags( $new_instance['thumbnail_height'] ) : 50;
		$instance['style'] = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : '';
		return $instance;
	}
}