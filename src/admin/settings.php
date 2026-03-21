<?php
/**
 * LinkViews Settings Class
 *
 * @package link-view
 */

// cspell:ignore nosubsub posttype posttypediv

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Admin;

use WordPress\Plugins\mibuthu\LinkView\InputType;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

require_once PLUGIN_PATH . 'includes/config.php';
require_once PLUGIN_PATH . 'includes/config-admin-data.php';
require_once PLUGIN_PATH . 'admin/input-type.php';

use WordPress\Plugins\mibuthu\LinkView\Config;
use WordPress\Plugins\mibuthu\LinkView\ConfigAdminData;

/**
 * LinkViews Settings Class
 *
 * This class handles the display of the admin settings page
 */
class Settings {

	/**
	 * Config class instance reference
	 */
	private readonly Config $config;

	/**
	 * Config Admin Data class instance
	 */
	private readonly ConfigAdminData $config_admin_data;


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct( Config $config ) {
		$this->config            = $config;
		$this->config_admin_data = new ConfigAdminData();
	}


	/**
	 * Show the admin settings page
	 */
	public function show_page(): void {
		// Check required privileges.
		if ( ! current_user_can( 'manage_options' ) ) {
			// Use "default" text domain for translations available in WordPress Core.
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'default' ) );
		}
		// Create content.
		echo '
			<div class="wrap nosubsub">
			<div id="icon-link-manager" class="icon32"><br /></div><h2>' .
			// translators: Placeholder is the plugin name: 'LinkView'
			sprintf( esc_html__( '%1$s Settings', 'link-view' ), 'LinkView' ) .
			'</h2></div>';
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
		foreach ( $this->config->get_all() as $name => $value ) {
			$admin_data = $this->config_admin_data->$name;
			echo '
				<tr>
					<th>';
			if ( '' !== $admin_data->label ) {
				echo '<label for="' . esc_attr( $name ) . '">' . esc_html( $admin_data->label ) . ':</label>';
			}
			echo '</th>
					<td>';
			$admin_data->input_type->show_input_tag( $name, $value->get_str(), $admin_data->captions );
			echo '
					</td>
					<td class="description">' . wp_kses_post( $admin_data->description ) . '</td>
				</tr>';
		}
	}

}
