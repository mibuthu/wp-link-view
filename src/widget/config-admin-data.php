<?php
/**
 * Additional data for the widget arguments required for the widget admin page.
 *
 * @package link-view
 *
 * phpcs:disable Generic.Files.OneObjectStructurePerFile.MultipleFound -- value class is in the same file
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Widget;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;
use WordPress\Plugins\mibuthu\LinkView\Admin\InputType;

require_once PLUGIN_PATH . 'includes/option.php';


/**
 * ShortcodeAdminDataValue class
 *
 * This class represents the widget config admin data for one config value.
 */
final class ConfigAdminDataValue {


	/**
	 * Constructor: Initialize the readonly data
	 */
	public function __construct(
		public readonly InputType $input_type,
		public readonly string $caption,
		public readonly string $tooltip,
	) {}

}


/**
 * LinkView Widget args config admin data class
 *
 * This class provides all additional data for the arguments which is only required in the admin page.
 */
class ConfigAdminData {

	// phpcs:disable Squiz.Commenting.VariableComment.Missing, Squiz.WhiteSpace.MemberVarSpacing.Incorrect -- not required here
	public readonly ConfigAdminDataValue $title;
	public readonly ConfigAdminDataValue $atts;
	// phpcs:enable Squiz.Commenting.VariableComment.Missing, Squiz.WhiteSpace.MemberVarSpacing.Incorrect


	/**
	 * Constructor: Initialize the data
	 */
	public function __construct() {
		$this->title = new ConfigAdminDataValue(
			input_type: InputType::Text,
			caption: __( 'Title', 'link-view' ) . ':',
			tooltip: __( 'This option defines the displayed title for the widget.', 'link-view' ),
		);

		$this->atts = new ConfigAdminDataValue(
			input_type: InputType::TextArea,
			caption: __( 'Shortcode attributes', 'link-view' ) . ':',
			// translators: Placeholder is the shortcode name including brackets but without code tags, as the text is used in a tooltip: '[linkview]'
			tooltip: sprintf( __( 'All attributes which are available for the %1$s shortcode can be used.', 'link-view' ), '[linkview]' ),
		);
	}


	/**
	 * Get the data for a given argument
	 *
	 * @return array<string,string|array>
	 */
	public function __get( string $arg_name ): array {
		return $this->args_data[ $arg_name ] ?? [];
	}

}
