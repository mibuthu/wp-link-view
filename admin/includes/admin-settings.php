<?php
/**
 * LinkViews Settings Class
 *
 * @package link-view
 */

declare(strict_types=1);
if ( ! defined( 'WP_ADMIN' ) ) {
	exit;
}

require_once LV_PATH . 'includes/options.php';

/**
 * LinkViews Settings Class
 *
 * This class handles the display of the admin settings page
 */
class LV_Admin_Settings {

	/**
	 * Class singleton instance reference
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Options class instance reference
	 *
	 * @var LV_Options
	 */
	private $options;


	/**
	 * Singleton provider and setup
	 *
	 * @return self
	 */
	public static function &get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor which initializes required variables
	 */
	private function __construct() {
		$this->options = &LV_Options::get_instance();
		$this->options->load_options_helptexts();
	}


	/**
	 * Show the admin settings page
	 *
	 * @return void
	 */
	public function show_page() {
		// Check required privilegs.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'default' ) );
		}
		// Create content.
		echo '
			<div class="wrap nosubsub">
			<div id="icon-link-manager" class="icon32"><br /></div><h2>' . sprintf( esc_html__( '%1$s Settings', 'link-view' ), 'LinkView' ) . '</h2></div>';
		$this->html_settings();
	}


	/**
	 * Show the settings table
	 *
	 * @return void
	 */
	private function html_settings() {
		echo '
			<div id="posttype-page" class="posttypediv">
			<form method="post" action="options.php">
				';
		settings_fields( 'lv_options' );
		echo '
			<table class="form-table">';
		$this->html_options();
		echo '
			</table>
			';
		submit_button();
		echo '
			</form>
			</div>';
	}


	/**
	 * Show options
	 *
	 * @return void
	 */
	private function html_options() {
		foreach ( $this->options->options as $oname => $o ) {
			echo '
				<tr>
					<th>';
			if ( '' !== $o['label'] ) {
				echo '<label for="' . esc_attr( $oname ) . '">' . esc_html( $o['label'] ) . ':</label>';
			}
			echo '</th>
					<td>';
			switch ( $o['type'] ) {
				case 'radio':
					$this->show_radio( $oname, $this->options->get( $oname ), $o['caption'] );
					break;
				case 'textarea':
					$this->show_textarea( $oname, $this->options->get( $oname ) );
					break;
			}
			echo '
					</td>
					<td class="description">' . wp_kses_post( $o['desc'] ) . '</td>
				</tr>';
		}
	}


	/**
	 * Show a set of radio buttons
	 *
	 * @param string   $name HTML name attribute.
	 * @param string   $value HTML value attribute.
	 * @param string[] $caption List of captions.
	 * @param bool     $disabled Disable the radio buttons.
	 * @return void
	 * TODO: implement or remove $disabled
	 */
	private function show_radio( $name, $value, $caption, $disabled = false ) {
		echo '
							<fieldset>';
		foreach ( $caption as $okey => $ocaption ) {
			$checked = ( $value === $okey ) ? 'checked="checked" ' : '';
			echo '
								<label title="' . esc_attr( $ocaption ) . '">
									<input type="radio" ' . $checked . 'value="' . esc_attr( $okey ) . '" name="' . esc_attr( $name ) . '">
									<span>' . esc_html( $ocaption ) . '</span>
								</label>
								<br />';
		}
		echo '
							</fieldset>';
	}


	/**
	 * Show a text area
	 *
	 * @param string $name HTML name attribute.
	 * @param string $value Value.
	 * @return void
	 */
	private function show_textarea( $name, $value ) {
		echo '
						<textarea name="' . esc_attr( $name ) . '" id="' . esc_attr( $name ) . '" rows="25" class="large-text code">' . esc_html( $value ) . '</textarea>';
	}

}
