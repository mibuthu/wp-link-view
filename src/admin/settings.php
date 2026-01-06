<?php
/**
 * LinkViews Settings Class
 *
 * @package link-view
 */

// cspell:ignore nosubsub posttype posttypediv

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Admin;

use WordPress\Plugins\mibuthu\LinkView\Config;
use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

if ( ! defined( 'WP_ADMIN' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';


/**
 * LinkViews Settings Class
 *
 * This class handles the display of the admin settings page
 */
class Settings {


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct(
		/**
		 * Config class instance reference
		 */
		private readonly Config $config
	) {
		$this->config->load_admin_data();
	}


	/**
	 * Show the admin settings page
	 */
	public function show_page(): void {
		// Check required privileges.
		if ( ! current_user_can( 'manage_options' ) ) {
			// Use "default" text domain for translations available in WordPress Core.
			// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}
		// Create content.
		echo '
			<div class="wrap nosubsub">
			<div id="icon-link-manager" class="icon32"><br /></div><h2>' . sprintf( esc_html__( '%1$s Settings', 'link-view' ), 'LinkView' ) . '</h2></div>';
		$this->html_settings();
	}


	/**
	 * Show the settings table
	 */
	private function html_settings(): void {
		echo '
			<div id="posttype-page" class="posttypediv">
			<form method="post" action="options.php">
				';
		settings_fields( 'lvw_config' );
		echo '
			<table class="form-table">';
		$this->html_config();
		echo '
			</table>
			';
		submit_button();
		echo '
			</form>
			</div>';
	}


	/**
	 * Show config options
	 */
	private function html_config(): void {
		foreach ( $this->config->get_all() as $oname => $o ) {
			echo '
				<tr>
					<th>';
			if ( '' !== $o->label ) {
				echo '<label for="' . esc_attr( $oname ) . '">' . esc_html( $o->label ) . ':</label>';
			}
			echo '</th>
					<td>';
			switch ( $o->type ) {
				case 'radio':
					$this->show_radio( $oname, $this->config->$oname, (array) $o->caption );
					break;
				case 'text':
					$this->show_text( $oname, $this->config->$oname );
					break;
				case 'textarea':
					$this->show_textarea( $oname, $this->config->$oname );
					break;
			}
			echo '
					</td>
					<td class="description">' . wp_kses_post( $o->description ) . '</td>
				</tr>';
		}
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
