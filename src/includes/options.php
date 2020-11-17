<?php
/**
 * Options class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/singleton.php';
require_once PLUGIN_PATH . 'includes/attribute.php';

/**
 * Options class
 *
 * This class handles all available options with their information
 */
final class Options extends Singleton {

	/**
	 * Options array
	 *
	 * @var array<string, Attribute>
	 */
	public $options;


	/**
	 * Class constructor which initializes required variables
	 */
	protected function __construct() {
		$this->options = [
			'lv_req_cap' => new Attribute( 'manage_links' ),
			'lv_ml_role' => new Attribute( 'editor' ),
			'lv_css'     => new Attribute( '' ),
		];
	}


	/**
	 * Init action hook
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_init', [ &$this, 'register' ] );
		add_filter( 'pre_update_option_lv_ml_role', [ &$this, 'update_manage_links_role' ] );
	}


	/**
	 * Register all settings in WordPress
	 *
	 * @return void
	 */
	public function register() {
		foreach ( array_keys( $this->options ) as $oname ) {
			register_setting( 'lv_options', $oname );
		}
	}


	/**
	 * Load options helptext from additional file
	 *
	 * @return void
	 */
	public function load_helptexts() {
		global $lv_options_helptexts;
		require_once PLUGIN_PATH . 'includes/options-helptexts.php';
		foreach ( $lv_options_helptexts as $name => $values ) {
			$this->options[ $name ]->modify( $values );
		}
		unset( $lv_options_helptexts );
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
	public function get( $name ) {
		if ( ! isset( $this->options[ $name ] ) ) {
			// Trigger error is allowed in this case.
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error
			trigger_error( 'The requested option "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
			return '';
		}
		return get_option( $name, $this->options[ $name ]->value );
	}

}
