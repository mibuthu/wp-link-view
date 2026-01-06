<?php
/**
 * Widget class
 *
 * @package link-view
 */

// cspell:ignore widefat

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Widget;

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/option.php';
require_once PLUGIN_PATH . 'widget/config.php';


/**
 * LinkView Widget class
 */
class Widget extends \WP_Widget {

	/**
	 * Widget Arguments
	 */
	private readonly Config $config;


	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'linkview_widget', // Base ID.
			'LinkView', // Name.
			[
				'description' => sprintf( __( 'With this widget a %1$s shortcode can be added to a sidebar or widget area.', 'link-view' ), 'LinkView' ),
			]
		);
		$this->config = new Config();
	}


	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array<string,string> $args Widget arguments.
	 * @param array<string,string> $instance Saved values from database.
	 */
	public function widget( array $args, array $instance ): void {
		echo wp_kses_post( $args['before_widget'] );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( '' !== $title ) {
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
	public function update( array $new_instance, array $old_instance ): array {
		$instance = [];
		foreach ( array_keys( $this->config->get_all() ) as $name ) {
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
	 */
	public function form( array $instance ): string {
		$this->config->load_args_admin_data();
		foreach ( $this->config->get_all() as $name => $item ) {
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

}
