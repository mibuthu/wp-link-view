<?php
/**
 * LinkView Shortcode Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/shortcode-config.php';


/**
 * LinkView Shortcode Class
 *
 * This class handles the shortcode [linkview].
 */
class Shortcode {

	/**
	 * Shortcode attributes
	 *
	 * @var ShortcodeConfig
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
	 * Slider parameter (Parameters for the easyslider javascript function)
	 *
	 * @var array
	 */
	private $slider_parameter = [];


	/**
	 * Class constructor which initializes required variables
	 *
	 * @param int $sc_id The id of the shortcode.
	 */
	public function __construct( $sc_id ) {
		$this->config = new ShortcodeConfig();
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
		$categories = $this->get_categories();
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
	 * Get link categories
	 *
	 * @return \WP_Term[] Link category object array.
	 */
	private function get_categories() {
		$catarray = [];
		// TODO: The cat_filter value "all" is depricated and can be removed in 0.9.
		if ( ! empty( $this->config->cat_filter ) && 'all' !== $this->config->cat_filter ) {
			str_replace( ',', '|', $this->config->cat_filter );
			$catslugs = array_map( 'trim', array_map( 'strval', (array) explode( '|', $this->config->cat_filter ) ) );
			foreach ( $catslugs as $catslug ) {
				$term = get_term_by( 'slug', $catslug, 'link_category' );
				if ( $term instanceof \WP_Term ) {
					$catarray[] = $term;
				}
			}
		} else {
			// There seems to be a problem to recognize the get_terms function correctly.
			// @phan-suppress-next-line PhanAccessMethodInternal.
			$terms = get_terms(
				[
					'taxonomy' => 'link_category',
					'orderby'  => 'name',
				]
			);
			if ( is_array( $terms ) ) {
				$catarray = $terms;
			}
			if ( ! empty( $this->config->exclude_cat ) ) {
				$excludecat = array_map( 'trim', array_map( 'strval', (array) explode( ',', $this->config->exclude_cat ) ) );
				$diff       = [];
				foreach ( $catarray as $cat ) {
					if ( false === array_search( $cat->name, $excludecat, true ) ) {
						array_push( $diff, $cat );
					}
				}
				$catarray = $diff;
				unset( $diff );
			}
		}
		return $catarray;
	}


	/**
	 * Get Links
	 *
	 * @param \WP_Term $category Category object.
	 * @return object[] Links object array.
	 */
	private function get_links( $category ) {
		$args = [
			'orderby'       => $this->config->link_orderby,
			'order'         => $this->config->link_order,
			'limit'         => $this->config->num_links,
			'category_name' => $category->name,
		];
		return get_bookmarks( $args );
	}


	/**
	 * Add a new slider and prepare its settings
	 *
	 * @param int      $list_id The list id which is also used for the slider id.
	 * @param object[] $links The links which are displayed in the slider.
	 * @return void
	 */
	private function new_slider( $list_id, $links ) {
		$this->slider_parameter[ $list_id ] = [
			'size' => $this->slider_size( $links ),
		];
	}


	/**
	 * Get calculated slider size
	 *
	 * @param object[] $links Links object array.
	 * @return array<string,int> Array with slider width and height.
	 */
	private function slider_size( $links ) {
		// Use manual size given in the attributes.
		if ( ! empty( $this->config->slider_width ) && ! empty( $this->config->slider_height ) ) {
			return [
				'w' => intval( $this->config->slider_width ),
				'h' => intval( $this->config->slider_height ),
			];
		}

		// Get the maximum image size.
		$width  = 0;
		$height = 0;
		foreach ( $links as $link ) {
			if ( ! empty( $this->config->show_img ) && ! empty( $link->link_image ) ) {
				list($w, $h) = getimagesize( $link->link_image );
				$width       = max( $width, $w );
				$height      = max( $height, $h );
			}
		}
		// Get the maximum image size depending on the given size in the attributes.
		$ratio = 1;
		if ( ! empty( $this->config->slider_width ) ) {
			// @phan-suppress-next-line PhanTypeInvalidLeftOperandOfNumericOp $ratio is not a string!
			$ratio = $this->config->slider_width / $width;
		} elseif ( ! empty( $this->config->slider_height ) ) {
			// @phan-suppress-next-line PhanTypeInvalidLeftOperandOfNumericOp $ratio is not a string!
			$ratio = $this->config->slider_height / $height;
		}
		$width  = round( $width * $ratio );
		$height = round( $height * $ratio );
		// If no image was in all links, set a manual size.
		if ( empty( $width ) ) {
			$width = 300;
		}
		if ( empty( $height ) ) {
			$height = 30;
		}
		return [
			'w' => $width,
			'h' => $height,
		];
	}


	/**
	 * Get HTML for showing a single category
	 *
	 * @param \WP_Term $category Category object to show.
	 * @param int      $cat_column The actual category column.
	 * @return string HTML to render.
	 */
	private function html_category( $category, &$cat_column ) {
		$links = $this->get_links( $category );
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
				$this->new_slider( $list_id, $links );
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
						<li' . $this->multicol_classes( $this->link_multicol_settings, 'lvw-list-item' . $this->config->class_suffix ) . '>
						<div class="lvw-link' . $this->config->class_suffix . '"';
			if ( 'slider' !== $this->config->view_type && 'std' !== $this->config->vertical_align ) {
				$out .= ' style="display:inline-block; vertical-align:' . $this->config->vertical_align . ';"';
			}
			$out .= '>';
			$out .= $this->html_link( $link, $list_id );
			$out .= '</div></li>';
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
	 * Get HTML for showing a single link
	 *
	 * @param object $link Link object.
	 * @param int    $list_id The id of the actual link list/slider.
	 * @return string HTML to render link.
	 */
	private function html_link( $link, $list_id ) {
		$out = '';
		if ( empty( $this->config->link_items ) ) {
			// Simple style (name or image).
			if ( ! empty( $this->config->show_img ) && ! is_null( $link->link_image ) ) {
				// Image.
				$out .= $this->html_link_item( $link, 'image_l', $list_id );
			} else {
				// Name.
				$out .= $this->html_link_item( $link, 'name_l', $list_id );
			}
		} else {
			// Enhanced style (all items given in link_items attribute).
			$items = json_decode( $this->config->link_items, true );
			if ( is_array( $items ) ) {
				$out .= $this->html_link_section( $link, $items, $list_id );
			} else {
				$out .= 'ERROR while json decoding. There must be an error in your "link_items" json syntax.';
			}
		}
		return $out;
	}


	/**
	 * Get HTML for showing a link section
	 *
	 * @param object               $link Link object.
	 * @param array<string,string> $items Link items array included in the section.
	 * @param int                  $list_id The id of the actual link list/slider.
	 * @return string HTML to render link section.
	 */
	private function html_link_section( $link, $items, $list_id ) {
		$out = '';
		foreach ( $items as $name => $item ) {
			if ( is_array( $item ) ) {
				$out .= '<div class="lvw-section-' . $name . $this->config->class_suffix . '">';
				$out .= $this->html_link_section( $link, $item, $list_id );
				$out .= '</div>';
			} else {
				$out .= $this->html_link_item( $link, $name, $list_id, $item );
			}
		}
		return $out;
	}


	/**
	 * Get HTML for showing a link item
	 *
	 * @param object $link Link object.
	 * @param string $item Item type to display.
	 * @param int    $list_id The id of the actual link list/slider.
	 * @param string $caption Link item caption.
	 * @return string HTML to render link item.
	 */
	private function html_link_item( $link, $item, $list_id, $caption = '' ) {
		// Check if a hyperlink shall be added.
		$is_link = ( '_l' === substr( $item, -2 ) );
		if ( $is_link ) {
			$item = substr( $item, 0, -2 );
		}
		// Handle link_item_img="nothing".
		if ( 'image' === $item && '' === $link->link_image && 'show_nothing' === $this->config->link_item_img ) {
			return '';
		}
		// Prepare output.
		$out = '<div class="lvw-item-' . $item . $this->config->class_suffix . '">';
		if ( ! empty( $caption ) ) {
			$out .= '<span class="lvw-item-caption' . $this->config->class_suffix . '">' . $caption . '</span>';
		}
		// Pepare link if required.
		if ( $is_link ) {
			// Check target.
			if ( 'std' !== $this->config->link_target ) {
				$target = '_' . $this->config->link_target;
			} else {
				$target = $link->link_target;
				// Set target to _self if an empty string or _none was returned.
				if ( in_array( $target, [ '', '_none' ], true ) ) {
					$target = '_self';
				}
			}
			// Check description.
			$description = '';
			if ( ! empty( $link->link_description ) ) {
				$description = ' (' . $link->link_description . ')';
			}
			// Check rel attribute.
			$rel          = '';
			$combined_rel = $this->config->link_rel . ' ' . $link->link_rel;
			if ( ! empty( $combined_rel ) ) {
				// Check value according to allowed values for HTML5 (see https://www.w3schools.com/tags/att_a_rel.asp).
				$rels = array_intersect(
					array_unique( explode( ' ', $combined_rel ) ),
					(array) $this->config->get( 'link_rel' )->permitted_values
				);

				$rel = ' rel="' . implode( ' ', $rels ) . '"';
			}
			$out .= '<a class="lvw-anchor' . $this->config->class_suffix . '" href="' . $link->link_url . '" target="' . $target . '" title="' . $link->link_name . $description . '"' . $rel . '>';
		}
		switch ( $item ) {
			case 'name':
				$out .= $link->link_name;
				break;
			case 'address':
				$out .= $link->link_url;
				break;
			case 'description':
				$out .= $link->link_description;
				break;
			case 'image':
				$out .= $this->html_img_tag( $link, $list_id );
				break;
			case 'rss':
				$out .= $link->link_rss;
				break;
			case 'notes':
				$out .= $link->link_notes;
				break;
			case 'rating':
				$out .= $link->link_rating;
				break;
		}
		if ( $is_link ) {
			$out .= '</a>';
		}
		$out .= '</div>';
		return $out;
	}


	/**
	 * Get HTML for showing the image
	 *
	 * @param object $link Link object.
	 * @param int    $list_id The id of the actual link list/slider.
	 * @return string HTML to render the image.
	 */
	private function html_img_tag( $link, $list_id ) {
		// Handle links without an image.
		if ( empty( $link->link_image ) ) {
			switch ( $this->config->link_item_img ) {
				case 'show_link_name':
					return $link->link_name;
				case 'show_link_description':
					return $link->link_description;
				// 'show_nothing': is already handled in html_link_item.
				// 'show_img_tag': proceed as normal with the image tag.
			}
		}
		// Handle image size.
		if ( ! isset( $this->slider_parameter[ $list_id ] ) ) {
			$size_text = '';
		} else {
			$slider_width  = $this->slider_parameter[ $list_id ]['size']['w'];
			$slider_height = $this->slider_parameter[ $list_id ]['size']['h'];
			if ( empty( $slider_width ) || empty( $slider_height ) ) {
				$size_text = '';
			} else {
				$slider_ratio                 = $slider_width / $slider_height;
				list($img_width, $img_height) = getimagesize( $link->link_image );
				$img_ratio                    = $img_width / $img_height;
				if ( $slider_ratio > $img_ratio ) {
					$scale = $slider_height / $img_height;
				} else {
					$scale = $slider_width / $img_width;
				}
				$size_text = ' width=' . round( $img_width * $scale ) . ' height=' . round( $img_height * $scale );
			}
		}
		return '<img src="' . $link->link_image . '"' . $size_text . ' alt="' . $link->link_name . '" />';
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
		foreach ( $this->slider_parameter as $list_id => $parameter ) {
			$ret .= '
					#lvw-id-' . $this->sc_id . '-' . $list_id . ' li { ' .
						'width:' . intval( $parameter['size']['w'] ) . 'px; ' .
						'height:' . intval( $parameter['size']['h'] ) . 'px; }';
			if ( 'std' !== $this->config->vertical_align ) {
				$ret .= '
					#lvw-id-' . $this->sc_id . '-' . $list_id . ' .lvw-link' . $this->config->class_suffix . ' { ' .
						'display:table-cell; ' .
						'vertical-align:' . $this->config->vertical_align . '; ' .
						'width:' . $parameter['size']['w'] . 'px; ' .
						'height:' . $parameter['size']['h'] . 'px; }';
			}
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
		foreach ( array_keys( $this->slider_parameter ) as $list_id ) {
			$ret .= '
					jQuery("#lvw-id-' . $this->sc_id . '-' . $list_id . '").easySlider({';
			$ret .= 'auto: true, continuous: true, controlsShow: false';
			$ret .= ', pause: ' . $this->config->slider_pause;
			$ret .= ', speed: ' . $this->config->slider_speed;
			$ret .= '});';
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
