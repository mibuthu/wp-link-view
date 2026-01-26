<?php
/**
 * LinkView ShortcodeSlider Class
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Shortcode;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

require_once PLUGIN_PATH . 'shortcode/config.php';
require_once PLUGIN_PATH . 'shortcode/links.php';
require_once PLUGIN_PATH . 'shortcode/link.php';


/**
 * LinkView ShortcodeSlider Class
 *
 * This class handles the a slider used in a shortcode.
 */
class Slider {

	/**
	 * Shortcode attributes
	 */
	private readonly Config $shortcode_config;

	/**
	 * The links of the slider
	 *
	 * @var \WP_Term[]
	 */
	private readonly array $links;

	/**
	 * Id string
	 */
	private readonly string $id_string;

	/**
	 * Slider width
	 */
	public int $width;

	/**
	 * Slider height
	 */
	public int $height;


	/**
	 * Class constructor which initializes required variables
	 *
	 * @param \WP_Term[] $links The links of the slider.
	 */
	public function __construct( array $links, Config $config, string $id_string ) {
		$this->links            = $links;
		$this->shortcode_config = $config;
		$this->id_string        = $id_string;
		$this->slider_size();
	}


	/**
	 * Get calculated slider size
	 */
	private function slider_size(): void {
		$config_width  = intval( $this->shortcode_config->slider_width );
		$config_height = intval( $this->shortcode_config->slider_height );
		// Use manual size given in the attributes.
		if ( 0 < $config_width && 0 < $config_height ) {
			$this->width  = $config_width;
			$this->height = $config_height;
		}

		// Get the maximum image size.
		$width  = 0;
		$height = 0;
		foreach ( $this->links as $link ) {
			if ( $this->shortcode_config->show_img && ! empty( $link->link_image ) ) {
				[$w, $h] = getimagesize( $link->link_image );
				$width   = max( $width, $w );
				$height  = max( $height, $h );
			}
		}
		// Get the maximum image size depending on the given size in the attributes.
		$ratio = 1;
		if ( 0 < $config_width ) {
			$ratio = $config_width / $width;
		} elseif ( 0 < $config_height ) {
			$ratio = $config_height / $height;
		}
		$width  = intval( round( $width * $ratio ) );
		$height = intval( round( $height * $ratio ) );
		// If no image was in all links, set a manual size.
		if ( 0 >= $width ) {
			$width = 300;
		}
		if ( 0 >= $height ) {
			$height = 30;
		}
		$this->width  = $width;
		$this->height = $height;
	}


	/**
	 * Get HTML for showing slider styles
	 */
	public function slider_style(): string {
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
	 */
	public function slider_script(): string {
		$ret  = '
			jQuery("#lvw-id-' . $this->id_string . '").easySlider({';
		$ret .= 'auto: true, continuous: true, controlsShow: false';
		$ret .= ', pause: ' . $this->shortcode_config->slider_pause;
		$ret .= ', speed: ' . $this->shortcode_config->slider_speed;
		return $ret . '});';
	}

}
