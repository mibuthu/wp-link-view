<?php
/**
 * An enum with the available input types including a function to show the input type in the admin interface
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Admin;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly


/**
 * An enum with all available HTML input types
 */
enum InputType {
	case Radio;
	case Text;
	case TextArea;


	/**
	 * Show the input tag
	 *
	 * @param string               $name HTML name attribute.
	 * @param string               $value HTML value attribute.
	 * @param array<string,string> $captions List of captions.
	 */
	public function show_input_tag( string $name, string $value, array $captions = [] ): void {
		match ( $this ) {
			self::Radio => $this->show_radio( $name, $value, $captions ),
			self::Text => $this->show_text( $name, $value ),
			self::TextArea => $this->show_textarea( $name, $value ),
		};
	}


	/**
	 * Show a set of radio buttons
	 *
	 * @param string               $name HTML name attribute.
	 * @param string               $value HTML value attribute.
	 * @param array<string,string> $captions List of captions.
	 */
	private function show_radio( string $name, string $value, array $captions ): void {
		echo '
							<fieldset>';
		foreach ( $captions as $key => $caption ) {
			$checked = ( $value === $key ) ? 'checked="checked" ' : '';
			echo '
								<label title="' . esc_attr( $caption ) . '">
									<input type="radio" ' . wp_kses_post( $checked ) . 'value="' . esc_attr( $key ) . '" name="' . esc_attr( $name ) . '">
									<span>' . esc_html( $caption ) . '</span>
								</label>
								<br />';
		}
		echo '
							</fieldset>';
	}


	/**
	 * Show a text
	 */
	private function show_text( string $name, string $value ): void {
		echo '
						<input type="text" name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" value="' . esc_html( $value ) . '" />';
	}


	/**
	 * Show a text area
	 */
	private function show_textarea( string $name, string $value ): void {
		echo '
						<textarea name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" rows="25" class="large-text code">' . esc_html( $value ) . '</textarea>';
	}

}
