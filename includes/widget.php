<?php
/**
 * LV_Widget class
 *
 * @package link-view
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once LV_PATH . 'includes/attribute.php';


/**
 * LinkView Widget class
 */
class LV_Widget extends WP_Widget {

	/**
	 * Widget Items
	 *
	 * @var array<string,LV_Attribute>
	 */
	private $items;


	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'linkview_widget', // Base ID.
			'LinkView', // Name.
			array(
				'description' => sprintf( __( 'With this widget a %1$s shortcode can be added to a sidebar or widget area.', 'link-view' ), 'LinkView' ),
			)
		);
		// Define all available items.
		$this->items = array(
			'title' => new LV_Attribute( __( 'Links', 'link-view' ) ),
			'atts'  => new LV_Attribute( '' ),
		);
	}


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array<string,string> $args Widget arguments.
	 * @param array<string,string> $instance Saved values from database.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		echo wp_kses_post( $args['before_widget'] );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( ! empty( $title ) ) {
			echo wp_kses_post( $args['before_title'] . $title . $args['after_title'] );
		}
		echo do_shortcode( '[linkview ' . $instance['atts'] . ']' );
		echo wp_kses_post( $args['after_widget'] );
	}


	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array<string,string> $new_instance Values just sent to be saved.
	 * @param array<string,string> $old_instance Previously saved values from database (not used).
	 * @return array<string,string> Updated values to be saved.
	 *
	 * @suppress PhanUnusedPublicMethodParameter
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		foreach ( array_keys( $this->items ) as $name ) {
			if ( isset( $new_instance[ $name ] ) ) {
				$instance[ $name ] = wp_strip_all_tags( $new_instance[ $name ] );
			}
		}
		return $instance;
	}


	/**
	 * Admin page widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array<string,string> $instance Previously saved values from database.
	 * @return string Value used to check if the Safe button is displayed.
	 */
	public function form( $instance ) {
		$this->load_helptexts();
		foreach ( $this->items as $name => $item ) {
			if ( ! isset( $instance[ $name ] ) ) {
				$instance[ $name ] = $item->value;
			}
			if ( 'textarea' === $item->type ) {
				echo '
					<p' . ' title="' . esc_attr( $item->tooltip ) . '">
						<label for="' . esc_attr( $this->get_field_id( $name ) ) . '">' . esc_html( (string) $item->caption ) . ' </label>
						<textarea class="widefat" id="' . esc_attr( $this->get_field_id( $name ) )
							. '" name="' . esc_attr( $this->get_field_name( $name ) )
							. '" rows="5">' . esc_attr( $instance[ $name ] ) . '</textarea>
					</p>';
			} else { // 'text'
				echo '
					<p' . ' title="' . esc_attr( $item->tooltip ) . '">
						<label for="' . esc_attr( $this->get_field_id( $name ) ) . '">' . esc_html( (string) $item->caption ) . ' </label>
						<input class="widefat" id="' . esc_attr( $this->get_field_id( $name ) )
							. '" name="' . esc_attr( $this->get_field_name( $name ) )
							. '" type="text" value="' . esc_attr( $instance[ $name ] ) . '" />
					</p>';
			}
		}
		return '';
	}


	/**
	 * Load helptexts of widget items
	 *
	 * @return void
	 */
	private function load_helptexts() {
		global $lv_widget_items_helptexts;
		require_once LV_PATH . 'includes/widget-helptexts.php';
		foreach ( $lv_widget_items_helptexts as $name => $values ) {
			$this->items[ $name ]->modify( $values );
		}
		unset( $lv_widget_items_helptexts );
	}

}
