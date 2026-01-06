<?php
/**
 * Additional data for the widget arguments required for the widget admin page.
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Widget;

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require_once PLUGIN_PATH . 'includes/option.php';


/**
 * LinkView Widget args config admin data class
 *
 * This class provides all additional data for the arguments which is only required in the admin page.
 *
 * @property string $title
 * @property string $atts
 */
class ConfigAdminData {

	/**
	 * Additional data for the arguments
	 *
	 * @var array<string,array<string,string|array>>
	 */
	private array $args_data;


	/**
	 * Constructor: Initialize the data
	 */
	public function __construct() {
		$this->args_data = [
			'title' => [
				'type'    => 'text',
				'caption' => __( 'Title', 'link-view' ) . ':',
				'tooltip' => __( 'This option defines the displayed title for the widget.', 'link-view' ),
			],

			'atts'  => [
				'type'    => 'textarea',
				'caption' => __( 'Shortcode attributes', 'link-view' ) . ':',
				'tooltip' => sprintf( __( 'All attributes which are available for the %1$s shortcode can be used.', 'link-view' ), '[link-view]' ),
			],
		];
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
