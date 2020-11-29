<?php
/**
 * Additional data for the cofig required for the settings help page.
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
 * ConfigAdminData class
 *
 * This class provides all additional data for the Config class which is only required in the admin page.
 *
 * @property-read string $lvw_req_capabilities Required capabilities option.
 * @property-read string $lvw_req_manage_links_role Required manage links role option.
 * @property-read string $lvw_custom_class Custom css option.
 * @property-read string $lvw_custom_css Custom css option.
 */
final class ConfigAdminData {

	/**
	 * Additional data for the config options
	 *
	 * @var array<string,array<string,string|array>>
	 */
	private $config_data;


	/**
	 * Constructor: Initialize the data
	 */
	public function __construct() {
		$this->config_data = [
			'lvw_req_capabilities'      => [
				'type'        => 'radio',
				'label'       => sprintf( __( 'Required capabilities to show the %1$s page', 'link-view' ), '"' . __( 'About', 'link-view' ) . ' LinkView"' ),
				'caption'     => [
					'manage_links' => 'manage_links (' . __( 'Default', 'link-view' ) . ')',
					'edit_pages'   => 'edit_pages',
					'edit_posts'   => 'edit_posts',
				],
				'description' =>
					sprintf( __( 'With this option you can specify the required capabilities to show the %1$s page.', 'link-view' ), '"' . __( 'About', 'link-view' ) . ' LinkView"' ) . '<br />
					' . sprintf( __( 'More information can be found in the %1$s.', 'link-view' ), '<a href="https://codex.wordpress.org/Roles_and_Capabilities" target="_blank" rel="noopener">WordPress Codex</a>' ),
			],

			'lvw_req_manage_links_role' => [
				'type'        => 'radio',
				'label'       => __( 'Required role to manage links', 'link-view' ),
				'caption'     => [
					// Use "default" text domain for translations available in WordPress Core.
					'editor'      => __( 'Editor', 'default' ) . ' (WordPress-' . __( 'Default', 'link-view' ) . ')',
					'author'      => __( 'Author', 'default' ),
					'contributor' => __( 'Contributor', 'default' ),
					'subscriber'  => __( 'Subscriber', 'default' ),
				],
				'description' =>
					__( 'With this option minimum required role to manage links can be set', 'link-view' ) . ' (' . __( 'Capability', 'link-view' ) . ': "manage_links").<br />
					' . sprintf( __( 'More information can be found in the %1$s.', 'link-view' ), '<a href="https://codex.wordpress.org/Roles_and_Capabilities" target="_blank" rel="noopener">WordPress Codex</a>' ) . '<br />
					' . sprintf( __( 'Please note that this option also affects the access to the %1$s page if the required capabilities are set to %2$s.', 'link-view' ), '"' . __( 'About', 'link-view' ) . ' LinkView"', '"manage_links"' ),
			],

			'lvw_custom_class'          => [
				'type'        => 'text',
				'label'       => sprintf( __( 'Custom CSS classes for %1$s', 'link-view' ), 'LinkView' ),
				'description' =>
					sprintf( __( 'With this option you can specify custom CSS classes which will be added to the wrapper div of the %1$s shortcode.', 'link-view' ), '<code>[link-view]</code>' ) . '<br />
					' . sprintf( __( 'Use the %1$s to seperate multiple classes', 'link-view' ), '<code>,</code>' ),
			],

			'lvw_custom_css'            => [
				'type'        => 'textarea',
				'label'       => sprintf( __( 'Custom CSS for %1$s', 'link-view' ), 'LinkView' ),
				'description' =>
					sprintf( __( 'With this option you can specify custom CSS for the links displayed by the %1$s shortcode.', 'link-view' ), '[link-view]' ) . '<br />
					' . sprintf( __( 'There are a lot of CSS classes available which are automatically added by the %1$s shortcode', 'link-view' ), '[link-view]' ) . ' (' . __( 'e.g.', 'link-view' ) . ' .lvw-item-image, .lvw-section-name, .lvw-cat-name, ...).<br />
					' . __( 'All available classes can be found in the sourcecode of a post or page where the shortcode is included.', 'link-view' ) . '<br />
					' . sprintf( __( 'To differ between different shortcodes you can set the attribute %1$s and add CSS-code for these special classes', 'link-view' ), '"class_suffix"' ) . '
					(' . __( 'e.g.', 'link-view' ) . ' .lvw-link-list-suffix, .lvw-item-name-suffix).<br /><br />
					' . __( 'Examples', 'link-view' ) . ':<br />
					<code>.lvw-link {<br />
						&nbsp;&nbsp;&nbsp;margin-bottom: 15px;<br />
					}<br />
					.lvw-item-image img {<br />
						&nbsp;&nbsp;&nbsp;-webkit-border-radius: 9px;<br />
						&nbsp;&nbsp;&nbsp;-moz-border-radius: 9px;<br />
						&nbsp;&nbsp;&nbsp;border-radius: 9px;<br />
					}<br />
					.lvw-item-image-detail img {<br />
						&nbsp;&nbsp;&nbsp;max-width: 250px;<br />
					}<br />
					.lvw-section-left-detail {<br />
						&nbsp;&nbsp;&nbsp;float: left;<br />
					}<br />
					.lvw-section-right-detail {<br />
						&nbsp;&nbsp;&nbsp;float: right;<br />
						&nbsp;&nbsp;&nbsp;margin-left: 15px;<br />
					}</code>',
			],
		];
	}


	/**
	 * Get the data for a given option.
	 *
	 * @param string $option_name The name of the option.
	 * @return array<string,string|array>
	 */
	public function __get( $option_name ) {
		if ( isset( $this->config_data[ $option_name ] ) ) {
			return $this->config_data[ $option_name ];
		}
	}

}
