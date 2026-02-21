<?php
/**
 * LinkView Shortcodes Class
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Shortcode;

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

require_once PLUGIN_PATH . 'includes/config.php';
require_once PLUGIN_PATH . 'shortcode/shortcode.php';

use WordPress\Plugins\mibuthu\LinkView\Config;

/**
 * LinkView Shortcodes Class
 *
 * This class handles the shortcode instances and the styles/scripts which are required for all instances.
 */
class Factory {

	/**
	 * Config class instance reference
	 */
	private readonly Config $config;

	/**
	 * Shortcode instances
	 *
	 * @var array<int,Shortcode>
	 */
	private array $shortcodes = [];


	/**
	 * Class constructor which initializes required variables
	 */
	public function __construct( Config &$config ) {
		$this->config = $config;
		add_action( 'print_late_styles', $this->print_styles( ... ) );
		add_action( 'wp_footer', $this->enqueue_scripts( ... ), 1 );
	}


	/**
	 * Add a new shortcode instance
	 *
	 * @param array<string,string> $atts Shortcode attributes.
	 */
	public function add( array $atts, string $content = '' ): string {
		$sc_id              = count( $this->shortcodes ) + 1;
		$this->shortcodes[] = new Shortcode( $this->config, $sc_id );
		return $this->shortcodes[ $sc_id - 1 ]->show_html( $atts, $content );
	}


	/**
	 * Print general CSS styles and the slider styles (if required)
	 */
	public function print_styles(): void {
		// Default styles for the shortcode and user specific styles from lvw_custom_css option.
		echo '
			<style type="text/css">
				.linkview { overflow:auto; }
				.linkview > div { overflow:hidden; }
				.lvw-slider ul, .lvw-slider li { margin:0; padding:0; list-style-type:none; list-style-image:none; }
				.lvw-slider li { overflow:hidden; text-align:center; }
				.lvw-slider img { max-width:100%; }
				.lvw-multi-column { float:left; }
				.lvw-multi-column li { page-break-inside: avoid; }
				.lvw-row { overflow:auto; }
				.lvw-css-column { break-inside:avoid-column; column-break-inside:avoid; -webkit-column-break-inside:avoid; overflow:hidden; }
				' . wp_kses_post( $this->config->custom_css );
		// Slider styles.
		foreach ( $this->shortcodes as $shortcode ) {
			echo wp_kses_post( $shortcode->slider_styles() );
		}
		echo '
			</style>';
	}


	/**
	 * Enqueue all slider and Masonry scripts
	 */
	public function enqueue_scripts(): void {
		// Slider scripts.
		$slider = '';
		foreach ( $this->shortcodes as $shortcode ) {
			$slider .= $shortcode->slider_scripts();
		}
		if ( '' !== $slider ) {
			$slider = '
				jQuery(document).ready( function() {' . $slider . '
				});';
			wp_enqueue_script( 'lvw_easySlider' );
			wp_add_inline_script( 'lvw_easySlider', $slider );
		}
		// Masonry scripts.
		$masonry = '';
		foreach ( $this->shortcodes as $shortcode ) {
			$masonry .= $shortcode->masonry_scripts();
		}
		if ( '' !== $masonry ) {
			$masonry = '
				jQuery(document).ready( function() {' . $masonry . '
				});';
			wp_enqueue_script( 'masonry' );
			wp_add_inline_script( 'masonry', $masonry );
		}
	}

}
