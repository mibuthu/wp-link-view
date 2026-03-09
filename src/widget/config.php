<?php
/**
 * Widget class
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Widget;

use WordPress\Plugins\mibuthu\LinkView\OptionValue;
use WordPress\Plugins\mibuthu\LinkView\OptionValueType;
use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require_once PLUGIN_PATH . 'includes/option-value.php';
require_once PLUGIN_PATH . 'includes/option-value-type.php';


/**
 * LinkView Widget arguments config class
 *
 * @property string $title
 * @property string $atts
 */
class Config {

	/**
	 * Widget Items
	 *
	 * @var array<string,OptionValue>
	 */
	private array $args;


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct() {
		$this->args = [
			'title' => new OptionValue( OptionValueType::String, __( 'Links', 'link-view' ) ),
			'atts'  => new OptionValue( OptionValueType::StringArray ),
		];
	}


	/**
	 * Get the value of the given arguments
	 */
	public function __get( string $name ): OptionValue {
		assert( isset( $this->args[ $name ] ), 'Widget argument "' . esc_attr( $name ) . '" does not exist!' );
		return $this->args[ $name ]->get();
	}


	/**
	 * Get all specified arguments
	 *
	 * @return array<string,OptionValue>
	 */
	public function get_all(): array {
		return $this->args;
	}

}
