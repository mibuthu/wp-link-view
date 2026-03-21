<?php
/**
 * LinkView Config class
 *
 * @package link-view
 *
 * phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound -- enums for the options are in the same file
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require_once PLUGIN_PATH . 'includes/option-value.php';


enum ReqCapabilities: string {
	case ManageLinks = 'manage_links';
	case EditPages   = 'edit_pages';
	case EditPosts   = 'edit_posts';
}


enum ReqManageLinksRole: string {
	case Editor      = 'editor';
	case Author      = 'author';
	case Contributor = 'contributor';
	case Subscriber  = 'subscriber';
}


/**
 * A wrapper for option value to handle WordPress options
 *
 * This class reads the value from the database when required and save it in the option value.
 * There is an additional variable loaded to handle the loading status.
 * The loading status must be handled in the config, because the option name is not available in this class.
 */
final class WpOptionValue {

	/**
	 * The option value instance
	 */
	public OptionValue $option_value;

	/**
	 * The loading status of the value
	 */
	public bool $loaded;


	/**
	 * Class constructor which sets the required variables
	 *
	 * For an option with OptionType::Enum a value is required.
	 * For all other option types the value is optional.
	 */
	public function __construct( OptionValueType $value_type, mixed $value = null ) {
		$this->option_value = new OptionValue( $value_type, $value );
		$this->loaded       = false;
	}

}


/**
 * Config class
 *
 * This class handles all available config options with their information
 *
 * @property-read OptionValue $req_capabilities
 * @property-read OptionValue $req_manage_links_role
 * @property-read OptionValue $custom_class
 * @property-read OptionValue $custom_css
 */
final class Config {

	/**
	 * Options array
	 *
	 * @var array<string, WpOptionValue>
	 */
	private array $options;


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct() {
		$this->options = [
			'lvw_req_capabilities'      => new WpOptionValue( OptionValueType::Enum, ReqCapabilities::ManageLinks ),
			'lvw_req_manage_links_role' => new WpOptionValue( OptionValueType::Enum, ReqManageLinksRole::Author ), // TODO: Option is not working, there seems to be not place where the value is considered.
			'lvw_custom_class'          => new WpOptionValue( OptionValueType::String ),
			'lvw_custom_css'            => new WpOptionValue( OptionValueType::String ),
		];
		add_action( 'admin_init', $this->register( ... ) );
		add_filter( 'pre_update_option_lvw_req_manages_link_role', $this->update_manage_links_role( ... ) );
	}


	/**
	 * Register all settings in WordPress
	 */
	public function register(): void {
		foreach ( array_keys( $this->options ) as $option_name ) {
			register_setting( 'lvw_config', $option_name );
		}
	}


	/**
	 * Update the role to manage links
	 */
	public function update_manage_links_role( string $new_value ): string {
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
	 * The "lvw_" prefix in the option name is optional.
	 */
	public function __get( string $name ): OptionValue {
		if ( ! str_starts_with( $name, 'lvw_' ) ) {
			$name = 'lvw_' . $name;
		}
		if ( ! isset( $this->options[ $name ] ) ) {
			// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_trigger_error -- Trigger a warning is correct here
			trigger_error( 'The requested option "' . esc_attr( $name ) . '" does not exist!', E_USER_WARNING );
		}
		if ( ! $this->options[ $name ]->loaded ) {
			$this->load_wp_option( $name );
		}
		return $this->options[ $name ]->option_value;
	}


	/**
	 * Load the WordPress option from the database
	 */
	private function load_wp_option( string $name ): void {
		$value = get_option( $name, $this->options[ $name ]->option_value->get() );
		$this->options[ $name ]->option_value->set_from_str( $value );
		$this->options[ $name ]->loaded = true;
	}


	/**
	 * Get all specified options
	 *
	 * @return array<string,OptionValue>
	 */
	public function get_all(): array {
		$ret = [];
		foreach ( $this->options as $name => $value ) {
			$ret[ $name ] = $this->$name;
		}
		return $ret;
	}


	/**
	 * Upgrades renamed or modified options to the actual version
	 *
	 * Version 0.7.3 to 0.8:
	 *  * lv_req_cap -> lvw_req_capabilities
	 *  * lv_ml_role -> lvw_req_manages_links_role
	 *  * lv_css -> lvw_custom_css
	 */
	public function version_upgrade(): void {
		$this->rename_option( 'lv_req_cap', 'lvw_req_capabilities' );
		$this->rename_option( 'lv_ml_role', 'lvw_req_manage_links_role' );
		$this->rename_option( 'lv_css', 'lvw_custom_css' );
	}


	/**
	 * Rename an existing option
	 */
	private function rename_option( string $old_name, string $new_name ): void {
		$value = get_option( $old_name, null );
		if ( null !== $value ) {
			add_option( $new_name, $value );
			delete_option( $old_name );
		}
	}

}
