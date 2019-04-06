<?php
/**
 * LinkView Shortcodes Class
 *
 * @package link-view
 */

declare( strict_types=1 );
if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once LV_PATH . 'includes/options.php';
require_once LV_PATH . 'includes/shortcode.php';


/**
 * LinkView Shortcodes Class
 *
 * This class handles the shortcode instances and the styles/scripts which are required for all instances.
 */
class LV_Shortcodes {

	/**
	 * Class singleton instance reference
	 *
	 * @var self
	 */
	private static $instance;

	/**
	 * Options class instance reference
	 *
	 * @var LV_Options
	 */
	private $options;

	/**
	 * Shortcode instances
	 *
	 * @var array<int,LV_Shortcode>
	 */
	private $shortcodes = array();


	/**
	 * Singleton provider and setup
	 *
	 * @return self
	 */
	public static function &get_instance() {
		// There seems to be an issue with the self variable in phan.
		// @phan-suppress-next-line PhanPluginUndeclaredVariableIsset.
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor which initializes required variables
	 *
	 * @return void
	 */
	private function __construct() {
		$this->options = LV_Options::get_instance();
		add_action( 'print_late_styles', array( &$this, 'print_styles' ) );
		add_action( 'wp_footer', array( &$this, 'enqueue_scripts' ), 1 );
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
		$this->shortcodes[] = new LV_Shortcode( $sc_id );
		return $this->shortcodes[ $sc_id - 1 ]->show_html( $atts, $content );
	}


	/**
	 * Print general CSS styles and the slider styles (if required)
	 *
	 * @return void
	 */
	public function print_styles() {
		// Default styles for the shortcode and user specific styles from lv_css option.
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
				' . wp_kses_post( $this->options->get( 'lv_css' ) );
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
			wp_enqueue_script( 'lv_easySlider' );
			wp_add_inline_script( 'lv_easySlider', $slider );
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
			wp_enqueue_script( 'lv_masonry' );
			wp_add_inline_script( 'lv_masonry', $masonry );
		}
	}

}
