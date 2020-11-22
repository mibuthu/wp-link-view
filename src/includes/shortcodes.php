<?php
/**
 * LinkView Shortcodes Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/config.php';
require_once PLUGIN_PATH . 'includes/shortcode.php';


/**
 * LinkView Shortcodes Class
 *
 * This class handles the shortcode instances and the styles/scripts which are required for all instances.
 */
class Shortcodes {

	/**
	 * Config class instance reference
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Shortcode instances
	 *
	 * @var array<int,Shortcode>
	 */
	private $shortcodes = [];


	/**
	 * Class constructor which initializes required variables
	 *
	 * @param Config $config_instance The Config instance as a reference.
	 * @return void
	 */
	public function __construct( &$config_instance ) {
		$this->config = $config_instance;
		add_action( 'print_late_styles', [ &$this, 'print_styles' ] );
		add_action( 'wp_footer', [ &$this, 'enqueue_scripts' ], 1 );
	}


	/**
	 * Add a new shortcode instance
	 *
	 * @param array<string,string> $atts Shortcode attributes.
	 * @param string               $content Shortcode content.
	 * @return string HTML to render.
	 */
	public function add( $atts, $content = '' ) {
		$sc_id              = count( $this->shortcodes ) + 1;
		$this->shortcodes[] = new Shortcode( $sc_id );
		return $this->shortcodes[ $sc_id - 1 ]->show_html( $atts, $content );
	}


	/**
	 * Print general CSS styles and the slider styles (if required)
	 *
	 * @return void
	 */
	public function print_styles() {
		// Default styles for the shortcode and user specific styles from lvw_custom_css option.
		echo '
			<style type="text/css">
				.linkview { overflow:auto; }
				.linkview > div { overflow:hidden; }
				.lv-slider ul, .lv-slider li { margin:0; padding:0; list-style-type:none; list-style-image:none; }
				.lv-slider li { overflow:hidden; text-align:center; }
				.lv-slider img { max-width:100%; }
				.lv-multi-column { float:left; }
				.lv-multi-column li { page-break-inside: avoid; }
				.lv-row { overflow:auto; }
				.lv-css-column { break-inside:avoid-column; column-break-inside:avoid; -webkit-column-break-inside:avoid; overflow:hidden; }
				' . wp_kses_post( $this->config->lvw_custom_css );
		// Slider styles.
		foreach ( $this->shortcodes as $shortcode ) {
			echo wp_kses_post( $shortcode->slider_styles() );
		}
		echo '
			</style>';
	}


	/**
	 * Enqueue all slider and Masonry scripts
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		// Slider scripts.
		$slider = '';
		foreach ( $this->shortcodes as $shortcode ) {
			$slider .= $shortcode->slider_scripts();
		}
		if ( ! empty( $slider ) ) {
			$slider = '
				jQuery(document).ready( function() {' . $slider . '
				});';
			wp_enqueue_script( 'lvw_easySlider' );
			wp_add_inline_script( 'lvw_easySlider', $slider );
		}
		// Masonry scripts.
		$masonry = '';
		foreach ( $this->shortcodes as $shortcode ) {
			$masonry .= $shortcode->mansonry_scripts();
		}
		if ( ! empty( $masonry ) ) {
			$masonry = '
				jQuery(document).ready( function() {' . $masonry . '
				});';
			wp_enqueue_script( 'lvw_masonry' );
			wp_add_inline_script( 'lvw_masonry', $masonry );
		}
	}

}
