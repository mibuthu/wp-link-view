<?php
/**
 * Widget class
 *
 * @package link-view
 */

// cspell:ignore widefat

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Widget;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

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
				// translators: Placeholder is the plugin name: 'LinkView'
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
	 *
	 * TODO: Currently no type declarations are allowed for the function arguments, because the parent class WP_Widget does not define them
	 */
	public function widget( $args, $instance ): void {
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
	 * TODO: Currently no type declarations are allowed for the function arguments, because the parent class WP_Widget does not define them
	 *
	 * @suppress PhanUnusedPublicMethodParameter
	 */
	public function update( $new_instance, $old_instance ): array {
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
	 *
	 * TODO: Currently no type declarations are allowed for the function arguments, because the parent class WP_Widget does not define them
	 */
	public function form( $instance ): string {
		require_once PLUGIN_PATH . 'widget/config-admin-data.php';
		require_once PLUGIN_PATH . 'admin/input-type.php';
		$config_admin_data = new ConfigAdminData();
		foreach ( $this->config->get_all() as $option_name => $option ) {
			$option_admin_data = $config_admin_data->$option_name;
			if ( ! isset( $instance[ $option_name ] ) ) {
				$instance[ $option_name ] = $option->value;
			}
			if ( \WordPress\Plugins\mibuthu\LinkView\Admin\InputType::TextArea === $option_admin_data->input_type ) {
				echo '
					<p' . ' title="' . esc_attr( $option_admin_data->tooltip ) . '">
						<label for="' . esc_attr( $this->get_field_id( $option_name ) ) . '">' . esc_html( (string) $option_admin_data->caption ) . ' </label>
						<textarea class="widefat" id="' . esc_attr( $this->get_field_id( $option_name ) )
							. '" name="' . esc_attr( $this->get_field_name( $option_name ) )
							. '" rows="5">' . esc_attr( $instance[ $option_name ] ) . '</textarea>
					</p>';
			} else { // InputType::Text
				echo '
					<p' . ' title="' . esc_attr( $option_admin_data->tooltip ) . '">
						<label for="' . esc_attr( $this->get_field_id( $option_name ) ) . '">' . esc_html( (string) $option_admin_data->caption ) . ' </label>
						<input class="widefat" id="' . esc_attr( $this->get_field_id( $option_name ) )
							. '" name="' . esc_attr( $this->get_field_name( $option_name ) )
							. '" type="text" value="' . esc_attr( $instance[ $option_name ] ) . '" />
					</p>';
			}
		}
		return '';
	}

}
