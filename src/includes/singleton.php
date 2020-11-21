<?php
/**
 * Abstract singleton class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}


/**
 * Singleton patterns with an abstract base class and late static bindings.
 */
abstract class Singleton {

	/**
	 * The registry of Singleton classes
	 *
	 * @var Singleton[]
	 */
	private static $registry;


	/**
	 * Constructor
	 *
	 * @return void
	 */
	abstract protected function __construct();


	/**
	 * Get the instance
	 *
	 * @return Singleton
	 */
	final public static function get_instance() {
		$class = get_called_class();
		if ( ! isset( self::$registry[ $class ] ) ) {
			self::$registry[ $class ] = new $class();
		}
		return self::$registry[ $class ];
	}


	/**
	 * Prevent the instance from being cloned (which would create a second instance of it)
	 */
	final private function __clone() {
	}


	/**
	 * Prevent from being unserialized (which would create a second instance of it)
	 */
	final private function __wakeup() {
	}

}
