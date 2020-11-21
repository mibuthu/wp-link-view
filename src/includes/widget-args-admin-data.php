<?php
/**
 * Additional data for the widget arguments required for the widget admin page.
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
 * LinkView Shortcode Attribute Class
 *
 * This class provides all additional data for the arguments which is only required in the admin page.
 *
 * @property string $title
 * @property string $atts
 */
class WidgetArgsAdminData extends Singleton {

	/**
	 * Additional data for the arguments
	 *
	 * @var array<string,array<string,string|array>>
	 */
	private $args_data;


	/**
	 * Constructor: Initialize the data
	 */
	protected function __construct() {
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
	 * @param string $arg_name The name of the attribute.
	 * @return array<string,string|array>
	 */
	public function __get( $arg_name ) {
		if ( isset( $this->args_data[ $arg_name ] ) ) {
			return $this->args_data[ $arg_name ];
		}
	}

}

