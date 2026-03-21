<?php
/**
 * Additional data for the config required for the settings help page.
 *
 * @package link-view
 *
 * phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound -- value class is in the same file
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use WordPress\Plugins\mibuthu\LinkView\Admin\InputType;

require_once PLUGIN_PATH . 'admin/input-type.php';


/**
 * ConfigAdminDataValue class
 *
 * This class represents the admin data for one config value.
 */
final class ConfigAdminDataValue {


	/**
	 * Constructor: Initialize the readonly data
	 */
	public function __construct(
		public readonly InputType $input_type,
		public readonly string $label,
		public readonly string $description,
		public readonly array $captions = [],
	) {}

}


/**
 * ConfigAdminData class
 *
 * This class provides all additional data for the Config class which is only required in the admin page.
 */
final class ConfigAdminData {

	// phpcs:disable Squiz.Commenting.VariableComment.Missing, Squiz.WhiteSpace.MemberVarSpacing.Incorrect -- not required here
	public readonly ConfigAdminDataValue $lvw_req_capabilities;
	public readonly ConfigAdminDataValue $lvw_req_manage_links_role;
	public readonly ConfigAdminDataValue $lvw_custom_class;
	public readonly ConfigAdminDataValue $lvw_custom_css;
	// phpcs:enable Squiz.Commenting.VariableComment.Missing, Squiz.WhiteSpace.MemberVarSpacing.Incorrect


	/**
	 * Constructor: Initialize the data
	 */
	public function __construct() {
		$this->lvw_req_capabilities = new ConfigAdminDataValue(
			input_type: InputType::Radio,
			// translators: Placeholder is the plugin name: 'LinkView'
			label: sprintf( __( 'Required capabilities to show the %1$s page', 'link-view' ), '"' . __( 'About', 'link-view' ) . ' LinkView"' ),
			description:
				// translators: Placeholder is the plugin name: 'LinkView'
				sprintf( __( 'With this option you can specify the required capabilities to show the %1$s page.', 'link-view' ), '"' . __( 'About', 'link-view' ) . ' LinkView"' ) . '<br />
				' .
				// translators: Placeholder is the plugin name: 'LinkView'
				sprintf( __( 'More information can be found in the %1$s.', 'link-view' ), '<a href="https://codex.wordpress.org/Roles_and_Capabilities" target="_blank" rel="noopener">WordPress Codex</a>' ),
			captions: [
				'manage_links' => 'manage_links (' . __( 'Default', 'link-view' ) . ')',
				'edit_pages'   => 'edit_pages',
				'edit_posts'   => 'edit_posts',
			],
		);

		$this->lvw_req_manage_links_role = new ConfigAdminDataValue(
			input_type: InputType::Radio,
			label: __( 'Required role to manage links', 'link-view' ),
			description:
				__( 'With this option minimum required role to manage links can be set', 'link-view' ) . ' (' . __( 'Capability', 'link-view' ) . ': "manage_links").<br />
				' .
				// translators: Placeholder is a link to the WordPress codex
				sprintf( __( 'More information can be found in the %1$s.', 'link-view' ), '<a href="https://codex.wordpress.org/Roles_and_Capabilities" target="_blank" rel="noopener">WordPress Codex</a>' ) . '<br />
				' .
				// translators: Placeholder is the plugin name: 'LinkView'
				sprintf( __( 'Please note that this option also affects the access to the %1$s page if the required capabilities are set to %2$s.', 'link-view' ), '"' . __( 'About', 'link-view' ) . ' LinkView"', '"manage_links"' ),
			captions: [
				'editor'      => __( 'Editor', 'default' ),
				'author'      => __( 'Author', 'default' ) . ' (WordPress-' . __( 'Default', 'link-view' ) . ')',
				'contributor' => __( 'Contributor', 'default' ),
				'subscriber'  => __( 'Subscriber', 'default' ),
			],
		);

		$this->lvw_custom_class = new ConfigAdminDataValue(
			input_type: InputType::Text,
			// translators: Placeholder is the plugin name: 'LinkView'
			label: sprintf( __( 'Custom CSS classes for %1$s', 'link-view' ), 'LinkView' ),
			description:
				// translators: Placeholder is a code tag including the shortcode name: '<code>[linkview]</code>'
				sprintf( __( 'With this option you can specify custom CSS classes which will be added to the wrapper div of the %1$s shortcode.', 'link-view' ), '<code>[linkview]</code>' ) . '<br />
				' .
				// translators: Placeholder is a code tag including the comma separator: '<code>,</code>'
				sprintf( __( 'Use the %1$s to separate multiple classes.', 'link-view' ), '<code>,</code>' ),
		);

		$this->lvw_custom_css = new ConfigAdminDataValue(
			input_type: InputType::TextArea,
			// translators: Placeholder is the plugin name: 'LinkView'
			label: sprintf( __( 'Custom CSS for %1$s', 'link-view' ), 'LinkView' ),
			description:
				// translators: Placeholder is a code tag including the shortcode: '<code>[linkview]</code>'
				sprintf( __( 'With this option you can specify custom CSS for the links displayed by the %1$s shortcode.', 'link-view' ), '<code>[linkview]</code>' ) . '<br />
				' . __( 'There are a lot of CSS classes available which are automatically added by the shortcode', 'link-view' ) . ' (' . __( 'e.g.', 'link-view' ) . ' .lvw-item-image, .lvw-section-name, .lvw-cat-name, ...).<br />
				' . __( 'All available classes can be found in the sourcecode of a post or page where the shortcode is included.', 'link-view' ) . '<br />
				' .
				// translators: Placeholder is a code tag including the value 'class_suffix'
				sprintf( __( 'To differ between different shortcodes you can set the shortcode attribute %1$s and add CSS-code for these special classes', 'link-view' ), '<code>class_suffix</code>' ) . '
				(' . __( 'e.g.', 'link-view' ) . ' <code>.lvw-link-list-suffix, .lvw-item-name-suffix</code>).<br /><br />
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
		);
	}

}
