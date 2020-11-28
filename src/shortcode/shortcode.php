<?php
/**
 * LinkView Shortcode Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView\Shortcode;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'shortcode/config.php';
require_once PLUGIN_PATH . 'shortcode/link.php';
require_once PLUGIN_PATH . 'shortcode/slider.php';
require_once PLUGIN_PATH . 'includes/links.php';

use WordPress\Plugins\mibuthu\LinkView\Links;

/**
 * LinkView Shortcode Class
 *
 * This class handles the shortcode [linkview].
 */
class Shortcode {

	/**
	 * Shortcode attributes
	 *
	 * @var Config
	 */
	private $config;

	/**
	 * Shortcode id
	 *
	 * @var int
	 */
	private $sc_id;

	/**
	 * Number of link lists (required for link list id assignment)
	 *
	 * @var int
	 */
	private $num_lists = 0;

	/**
	 * Category multicolumn settings
	 *
	 * @var array
	 */
	private $cat_multicol_settings = [];

	/**
	 * Link multicolumn settings
	 *
	 * @var array
	 */
	private $link_multicol_settings = [];

	/**
	 * Sliders used in the shortcode
	 *
	 * @var array<int,Slider>
	 */
	private $sliders = [];


	/**
	 * Class constructor which initializes required variables
	 *
	 * @param int $sc_id The id of the shortcode.
	 */
	public function __construct( $sc_id ) {
		$this->config = new Config();
		$this->sc_id  = $sc_id;
	}


	/**
	 * Main function to show the rendered HTML output
	 *
	 * @param array<string,string> $atts Shortcode attributes.
	 * @param string               $content Shortcode content.
	 * @return string HTML to render.
	 */
	public function show_html( $atts, $content = '' ) {
		$this->prepare_atts( $atts, $content );
		$categories = Links::categories( $this->config );
		$cat_column = 0;

		// Wrapper div.
		$out = '
				<div class="linkview" id="lvw-sc-id-' . $this->sc_id . '"' . $this->cat_multicol_settings['wrapper_styles'] . '>';
		// Go through each category.
		foreach ( $categories as $category ) {
			$out .= $this->html_category( $category, $cat_column );
		}
		// Close last column div if required.
		if ( ! empty( $cat_column ) ) {
			$out .= '
					</div>';
		}
		// Close wrapper div.
		$out .= '
				</div>';
		return $out;
	}


	/**
	 * Prepare the given attribute for the shortcode handling
	 *
	 * @param array<string,string> $atts Shortcode attributes.
	 * @param string               $content Shortcode content.
	 * @return void
	 */
	private function prepare_atts( $atts, $content ) {
		// Add leading "-" for css-suffix.
		if ( isset( $atts['class_suffix'] ) ) {
			$atts['class_suffix'] = '-' . $atts['class_suffix'];
		}
		// Set attribute link_items to $content if an enclosing shortcode was used.
		if ( ! empty( $content ) ) {
			// Replace quotes html code with real quotes.
			$content = str_replace( [ '&#8220;', '&#8221;', '&#8222;' ], '"', $content );
			// Set attribute.
			$atts['link_items'] = $content;
		}
		// Set given attributes.
		$this->config->set_values( $atts );
		// Preparations for multi-column category and link-list.
		$this->cat_multicol_settings  = $this->multicol_settings( $this->config->cat_columns );
		$this->link_multicol_settings = $this->multicol_settings( $this->config->link_columns, $this->config->list_symbol );
	}


	/**
	 * Get HTML for showing a single category
	 *
	 * @param \WP_Term $category Category object to show.
	 * @param int      $cat_column The actual category column.
	 * @return string HTML to render.
	 */
	private function html_category( $category, &$cat_column ) {
		$links = Links::get( $category, $this->config );
		$out   = $this->html_multicol_before( $this->cat_multicol_settings, $cat_column );
		if ( ! empty( $links ) ) {
			$out .= '
					<div' . $this->multicol_classes( $this->cat_multicol_settings, 'lvw-category' . $this->config->class_suffix ) . '>';
			if ( ! empty( $this->config->show_cat_name ) ) {
				$num_links_text = ! empty( $this->config->show_num_links ) ? ' <small>(' . count( $links ) . ')</small>' : '';
				$out           .= '
						<h2 class="lvw-cat-name' . $this->config->class_suffix . '">' . $category->name . $num_links_text . '</h2>';
			}
			// Show links.
			$list_id = ++ $this->num_lists;
			if ( 'slider' === $this->config->view_type ) {
				$this->sliders[ $list_id ] = new Slider(
					$links,
					$this->config,
					$this->sc_id . '-' . $list_id
				);
			}
			$out .= $this->html_link_list( $links, $list_id );
			$out .= '
					</div>';
		}
		$out .= $this->html_multicol_after( $this->cat_multicol_settings, $cat_column );
		return $out;
	}


	/**
	 * Get HTML for showing a link list
	 *
	 * @param object[] $links Links object array to show.
	 * @param int      $list_id Shortcode id.
	 * @return string HTML to render link list.
	 */
	private function html_link_list( $links, $list_id ) {
		$link_col = 0;
		// Wrapper div and list tag.
		$out = '
					<div id="lvw-id-' . $this->sc_id . '-' . $list_id . '"';
		if ( 'slider' === $this->config->view_type ) {
			$out .= ' class="lvw-slider"';
		}
		$out .= '>
					<ul class="lvw-link-list' . $this->config->class_suffix . '"' . $this->link_multicol_settings['wrapper_styles'] . '>';
		// Iterate over the links.
		foreach ( $links as $link ) {
			// Link multi-column handling.
			$out .= $this->html_multicol_before( $this->link_multicol_settings, $link_col );
			// Actual link.
			$out .= '
						<li' . $this->multicol_classes( $this->link_multicol_settings, 'lvw-list-item' . $this->config->class_suffix ) . '>';
			$out .= Link::show_html(
				$link,
				$this->config,
				// @phan-suppress-next-line PhanPluginDuplicateConditionalNullCoalescing Cannot use NullCoalescing due to PHP 5.6 support.
				isset( $this->sliders[ $list_id ] ) ? $this->sliders[ $list_id ] : null
			);
			$out .= '</li>';
			// Link multi-column-handling.
			$out .= $this->html_multicol_after( $this->link_multicol_settings, $link_col );
		}
		// Close last column div if required.
		if ( ! empty( $link_col ) ) {
			$out .= '
					</div>';
		}
		// Close list and wrapper div.
		$out .= '
					</ul>
					</div>';
		return $out;
	}


	/**
	 * Helper function for multicolumn handling (opening)
	 *
	 * @param array<string, string|array> $multicol_settings Multicolumn settings.
	 * @param int                         $column Acual column.
	 * @return string Required HTML which is required before the element for multicolumns.
	 */
	private function html_multicol_before( $multicol_settings, &$column ) {
		$column = intval( $column );
		if ( 'static' === $multicol_settings['type'] ) {
			$column++;
			if ( 1 === $column ) {   // First column.
				return '
				<div class="lvw-row">';
			}
		}
		return '';
	}


	/**
	 * Helper function for multicolumn handling (closing)
	 *
	 * @param array<string, string|array> $multicol_settings Multicolumn settings.
	 * @param int                         $column Acual column.
	 * @return string Required HTML which is required after the element for multicolumns.
	 */
	private function html_multicol_after( $multicol_settings, &$column ) {
		if ( 'static' === $multicol_settings['type'] && intval( $column ) === intval( $multicol_settings['opt']['num_columns'] ) ) {   // Last column.
			$column = 0;
			return '
				</div>';
		}
		return '';
	}


	/**
	 * Get all Settings for multicolumn handling
	 *
	 * @param string      $column_option The value of the category or link column option.
	 * @param null|string $list_symbol The list symbol type (if required).
	 * @return array<string,string|array> Multicolumn settings.
	 */
	private function multicol_settings( $column_option, $list_symbol = null ) {
		$ret = [];
		// Check if multicolumn is enabled.
		if ( 1 === intval( $column_option ) ) {  // No multicolumn.
			$ret['type']               = false;
			$ret['opt']['num_columns'] = 1;
		} elseif ( ctype_digit( strval( $column_option ) ) ) {  // Special case for number only (short form of static type).
			$ret['type']               = 'static';
			$ret['opt']['num_columns'] = $column_option;
		} else {  // All other cases.
			// Extract type and options.
			$ret['opt']  = [];
			$options     = explode( '(', $column_option );
			$ret['type'] = $options[0];
			if ( 'static' !== $ret['type'] && 'css' !== $ret['type'] && 'masonry' !== $ret['type'] ) {
				$ret['type'] = 'static';
			}
			if ( ! empty( $options[1] ) ) {
				$option_array = explode( '|', (string) substr( $options[1], 0, -1 ) );
				foreach ( $option_array as $option_text ) {
					$o                   = explode( '=', $option_text );
					$ret['opt'][ $o[0] ] = $o[1];
				}
			}
			// Validate required options and set them if not available.
			switch ( $ret['type'] ) {
				case 'static':
					if ( ! isset( $ret['opt']['num_columns'] ) || ! ctype_digit( strval( $ret['opt']['num_columns'] ) ) || 0 >= intval( $ret['opt']['num_columns'] ) ) {
						$ret['opt']['num_columns'] = 3;
						// Disable multi-column if num_columns = 1.
						if ( 1 === intval( $ret['opt']['num_columns'] ) ) {
							$ret['type'] = false;
						}
					}
					break;
				case 'css':
					// No requirements.
					break;
				case 'masonry':
					// No requirements.
					break;
			}
			if ( ! isset( $ret['opt']['num_columns'] ) ) {
				$ret['opt']['num_columns'] = 0;
			}
		}
		// Set wrapper styles.
		$ret['wrapper_styles'] = $this->multicol_wrapper_styles( $ret, $list_symbol );
		return $ret;
	}


	/**
	 * Get required HTML classes for Multicolumn handling
	 *
	 * @param array<string|array> $multicol_settings Multicolumn settings.
	 * @param string              $additional_classes Additional classes to include.
	 * @return string HTML class string.
	 */
	private function multicol_classes( $multicol_settings, $additional_classes = '' ) {
		$classes = $additional_classes;
		if ( ! empty( $multicol_settings['type'] ) ) {
			$classes .= ' lvw-multi-column lvw-' . $multicol_settings['type'] . '-column';
		}
		if ( empty( $classes ) ) {
			return '';
		} else {
			return ' class="' . $classes . '"';
		}
	}


	/**
	 * Get required wrapper styles for Multicolumn handling
	 *
	 * @param array<string, string|array> $multicol_settings Multicolumn settings.
	 * @param null|string                 $list_symbol The list symbol type (if required).
	 * @return string HTML style text.
	 */
	private function multicol_wrapper_styles( $multicol_settings, $list_symbol = null ) {
		if ( ! empty( $list_symbol ) && 'std' !== $list_symbol ) {
			$styles = 'list-style-type:' . $list_symbol . ';';
		} else {
			$styles = '';
		}
		// Prepare multi-column css options.
		if ( 'css' === $multicol_settings['type'] ) {
			foreach ( $multicol_settings['opt'] as $name => $value ) {
				// Do not add internal options.
				if ( 'num_columns' === $name ) {
					continue; }
				// Add attribute.
				$styles .= $name . ':' . $value . ';';
				// Add prefixed browser specific attributes.
				if ( 'column' === substr( $name, 0, 6 ) ) {
					$styles .= '-moz-' . $name . ':' . $value . ';-webkit-' . $name . ':' . $value . ';';
				}
			}
		}
		if ( empty( $styles ) ) {
			return '';
		} else {
			return ' style="' . $styles . '"';
		}
	}


	/**
	 * Get HTML for showing slider styles
	 *
	 * @return string HTML to render slider styles.
	 */
	public function slider_styles() {
		$ret = '';
		foreach ( $this->sliders as $slider ) {
			$ret .= $slider->slider_style();
		}
		return $ret;
	}


	/**
	 * Get the slider scripts text (if required)
	 *
	 * @return string The slider script text (if required) or an empty string.
	 */
	public function slider_scripts() {
		$ret = '';
		foreach ( $this->sliders as $slider ) {
			$ret .= $slider->slider_script();
		}
		return $ret;
	}


	/**
	 * Get the Masonry scripts text (if required)
	 *
	 * @return string The Masonry script text (if required) or an empty string.
	 */
	public function mansonry_scripts() {
		$ret = '';
		// Scripts for categories.
		if ( 'masonry' === $this->cat_multicol_settings['type'] ) {
			$options = 'itemSelector:".lvw-category-column"';
			foreach ( $this->cat_multicol_settings['opt'] as $name => $value ) {
				$options .= ',' . $name . ':' . $value;
			}
			$ret .= '
					jQuery(".linkview#lvw-sc-id-' . $this->sc_id . '").masonry({' . $options . '});';
		}
		// Scripts for links.
		if ( 'masonry' === $this->link_multicol_settings['type'] ) {
			$options = 'itemSelector:".lvw-list-item-column"';
			foreach ( $this->link_multicol_settings['opt'] as $name => $value ) {
				$options .= ',' . $name . ':' . $value;
			}
			for ( $id = 1; $id <= $this->num_lists; $id++ ) {
				$ret .= '
						jQuery(".linkview#lvw-id-' . $this->sc_id . '-' . $id . '").masonry({' . $options . '});';
			}
		}
		return $ret;
	}

}
