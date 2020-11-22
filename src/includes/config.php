<?php
/**
 * LinkView Config class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/option.php';

/**
 * Config class
 *
 * This class handles all available config options with their information
 *
 * @property-read string $lvw_req_capabilities
 * @property-read string $lvw_req_manage_links_role
 * @property-read string $lvw_custom_css
 */
final class Config {

	/**
	 * Options array
	 *
	 * @var array<string, Option>
	 */
	private $options;


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct() {
		$this->options = [
			'lvw_req_capabilities'      => new Option( 'manage_links' ),
			'lvw_req_manage_links_role' => new Option( 'editor' ),
			'lvw_custom_css'            => new Option( '' ),
		];
		add_action( 'admin_init', [ &$this, 'register' ] );
		add_filter( 'pre_update_option_lvw_req_manages_link_role', [ &$this, 'update_manage_links_role' ] );
	}


	/**
	 * Register all settings in WordPress
	 *
	 * @return void
	 */
	public function register() {
		foreach ( array_keys( $this->options ) as $oname ) {
			register_setting( 'lvw_config', $oname );
		}
	}


	/**
	 * Update the role to manage links
	 *
	 * @param string $new_value New role.
	 * @param null   $old_value Old role (not used).
	 * @return string The $new_value string.
	 *
	 * Variable $old_value is not required.
	 * @phan-suppress PhanUnusedPublicFinalMethodParameter.
	 */
	public function update_manage_links_role( $new_value, $old_value = null ) {
		global $wp_roles;
		switch ( $new_value ) {
			case 'subscriber':
				$wp_roles->add_cap( 'subscriber', 'manage_links' );
				// Case fall-through intended.
			case 'contributor':
				$wp_roles->add_cap( 'contributor', 'manage_links' );
				// Case fall-through intended.
			case 'author':
				$wp_roles->add_cap( 'author', 'manage_links' );
				break;
		}
		switch ( $new_value ) {
			case 'editor':
				$wp_roles->remove_cap( 'author', 'manage_links' );
				// Case fall-through intended.
			case 'author':
				$wp_roles->remove_cap( 'contributor', 'manage_links' );
				// Case fall-through intended.
			case 'contributor':
				$wp_roles->remove_cap( 'subscriber', 'manage_links' );
				break;
		}
		return $new_value;
	}


	/**
	 * Get the value of the specified option
	 *
	 * @param string $name Option name.
	 * @return string Option value.
	 */
	public function __get( $name ) {
		if ( ! isset( $this->options[ $name ] ) ) {
			// Trigger error is allowed in this case.
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'The requested option "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
			return '';
		}
		return get_option( $name, $this->options[ $name ]->value );
	}


	/**
	 * Get all specified options
	 *
	 * @return array<string,Option>
	 */
	public function get_all() {
		return $this->options;
	}


	/**
	 * Load the additional option data
	 *
	 * @return void
	 */
	public function load_admin_data() {
		require_once PLUGIN_PATH . 'includes/config-admin-data.php';
		$config_admin_data = new ConfigAdminData();
		foreach ( array_keys( $this->options ) as $option_name ) {
			$this->options[ $option_name ]->modify( $config_admin_data->$option_name );
		}
	}


	/**
	 * Upgrades renamed or modified options to the actual version
	 *
	 * Version 0.7.3 to 0.8:
	 *  * lv_req_cap -> lvw_req_capabilities
	 *  * lv_ml_role -> lvw_req_manages_links_role
	 *  * lv_css -> lvw_custom_css
	 *
	 * @return void
	 */
	public function version_upgrade() {
		$this->rename_option( 'lv_req_cap', 'lvw_req_capabilities' );
		$this->rename_option( 'lv_ml_role', 'lvw_req_manage_links_role' );
		$this->rename_option( 'lv_css', 'lvw_custom_css' );
	}


	/**
	 * Rename an existing option
	 *
	 * @param string $old_name The old option name.
	 * @param string $new_name The new option name.
	 * @return void
	 */
	private function rename_option( $old_name, $new_name ) {
		$value = get_option( $old_name, null );
		if ( null !== $value ) {
			add_option( $new_name, $value );
			delete_option( $old_name );
		}
	}

}
