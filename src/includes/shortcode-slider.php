<?php
/**
 * LinkView ShortcodeSlider Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/shortcode-config.php';
require_once PLUGIN_PATH . 'includes/shortcode-link.php';
require_once PLUGIN_PATH . 'includes/links.php';


/**
 * LinkView ShortcodeSlider Class
 *
 * This class handles the a slider used in a shortcode.
 */
class ShortcodeSlider {

	/**
	 * Shortcode attributes
	 *
	 * @var ShortcodeConfig
	 */
	private $shortcode_config;

	/**
	 * The links of the slider
	 *
	 * @var \WP_Term[]
	 */
	private $links;

	/**
	 * Id string
	 *
	 * @var string
	 */
	private $id_string;

	/**
	 * Slider width
	 *
	 * @var int
	 */
	public $width;

	/**
	 * Slider height
	 *
	 * @var int
	 */
	public $height;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @param \WP_Term[]      $links The links of the slider.
	 * @param ShortcodeConfig $shortcode_config The ShortcodeConfig object.
	 * @param string          $id_string The id string of the slider.
	 */
	public function __construct( $links, $shortcode_config, $id_string ) {
		$this->links            = $links;
		$this->shortcode_config = $shortcode_config;
		$this->id_string        = $id_string;
		$this->slider_size();
	}


	/**
	 * Get calculated slider size
	 */
	private function slider_size() {
		// Use manual size given in the attributes.
		if ( ! empty( $this->shortcode_config->slider_width ) && ! empty( $this->shortcode_config->slider_height ) ) {
			$this->width  = intval( $this->shortcode_config->slider_width );
			$this->height = intval( $this->shortcode_config->slider_height );
		}

		// Get the maximum image size.
		$width  = 0;
		$height = 0;
		foreach ( $this->links as $link ) {
			if ( ! empty( $this->shortcode_config->show_img ) && ! empty( $link->link_image ) ) {
				list($w, $h) = getimagesize( $link->link_image );
				$width       = max( $width, $w );
				$height      = max( $height, $h );
			}
		}
		// Get the maximum image size depending on the given size in the attributes.
		$ratio = 1;
		if ( ! empty( $this->shortcode_config->slider_width ) ) {
			$ratio = intval( $this->shortcode_config->slider_width ) / $width;
		} elseif ( ! empty( $this->shortcode_config->slider_height ) ) {
			$ratio = intval( $this->shortcode_config->slider_height ) / $height;
		}
		$width  = intval( round( $width * $ratio ) );
		$height = intval( round( $height * $ratio ) );
		// If no image was in all links, set a manual size.
		if ( empty( $width ) ) {
			$width = 300;
		}
		if ( empty( $height ) ) {
			$height = 30;
		}
		$this->width  = $width;
		$this->height = $height;
	}


	/**
	 * Get HTML for showing slider styles
	 *
	 * @return string HTML to render slider styles.
	 */
	public function slider_style() {
		$ret = '
				#lvw-id-' . $this->id_string . ' li { ' .
					'width:' . $this->width . 'px; ' .
					'height:' . $this->height . 'px; }';
		if ( 'std' !== $this->shortcode_config->vertical_align ) {
			$ret .= '
					#lvw-id-' . $this->id_string . ' .lvw-link' . $this->shortcode_config->class_suffix . ' { ' .
					'display:table-cell; ' .
					'vertical-align:' . $this->shortcode_config->vertical_align . '; ' .
					'width:' . $this->width . 'px; ' .
					'height:' . $this->height . 'px; }';
		}
		return $ret;
	}


	/**
	 * Get the slider scripts text (if required)
	 *
	 * @return string The slider script text (if required) or an empty string.
	 */
	public function slider_script() {
		$ret  = '
			jQuery("#lvw-id-' . $this->id_string . '").easySlider({';
		$ret .= 'auto: true, continuous: true, controlsShow: false';
		$ret .= ', pause: ' . $this->shortcode_config->slider_pause;
		$ret .= ', speed: ' . $this->shortcode_config->slider_speed;
		$ret .= '});';
		return $ret;
	}

}

