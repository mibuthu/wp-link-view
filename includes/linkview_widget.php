<?php
/**
 * Adds Foo_Widget widget.
 */
class linkview_widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'linkview_widget', // Base ID
			'LinkView', // Name
			array( 'description' => __( 'This widget allows you to insert the linkview shortcode in the sidebar. You can set every attribute which is available for the shortcode.', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;
		if ( ! empty( $title ) )
		{
			echo $before_title . $title . $after_title;
		}
		$out = do_shortcode( '[linkview '.$instance['atts'].']' );
		echo $out;
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['atts'] = strip_tags( $new_instance['atts'] );
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		isset( $instance['title'] ) ? $title = $instance['title'] : $title = __( 'New title', 'text_domain' );
		isset( $instance['atts'] ) ? $atts = $instance['atts'] : $atts = '';
		$out = '
		<p title="The title for the widget">
			<label for="'.$this->get_field_id( 'title' ).'">'.__( 'Title:' ).'</label>
			<input class="widefat" id="'.$this->get_field_id( 'title' ).'" name="'.$this->get_field_name( 'title' ).'" type="text" value="'.esc_attr( $title ).'" />
		</p>
		<p title="You can add all attributes which are available for the linkview shortcode">
			<label for="'.$this->get_field_id( 'atts' ).'">'.__( 'Shortcode attributes:' ).'</label>
			<textarea class="widefat" id="'.$this->get_field_id( 'atts' ).'" name="'.$this->get_field_name( 'atts' ).'" rows=6>'.esc_attr( $atts ).'</textarea>
		</p>';
		echo $out;
	}

} // end class linkview_widget
?>
