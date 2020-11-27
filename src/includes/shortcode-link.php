<?php
/**
 * LinkView Link Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!

namespace WordPress\Plugins\mibuthu\LinkView;

if ( ! defined( 'WPINC' ) ) {
	exit();
}

require_once PLUGIN_PATH . 'includes/shortcode-config.php';
require_once PLUGIN_PATH . 'includes/links.php';


/**
 * LinkView Link Class
 *
 * This class handles a link an how to display the link in a shortcode.
 */
class ShortcodeLink {


	/**
	 * Get HTML for showing a single link
	 *
	 * @param \WP_Term             $link Link object.
	 * @param ShortcodeConfig      $shortcode_config The ShortcodeConfig object.
	 * @param ShortcodeSlider|null $shortcode_slider The ShortcodeSlider object.
	 * @return string HTML to render link.
	 */
	public static function show_html( $link, $shortcode_config, $shortcode_slider = null ) {
		$out = '';
		if ( empty( $shortcode_config->link_items ) ) {
			// Simple style (name or image).
			if ( ! empty( $shortcode_config->show_img ) && ! is_null( $link->link_image ) ) {
				// Image.
				$out .= self::html_item( $link, 'image_l', '', $shortcode_config, $shortcode_slider );
			} else {
				// Name.
				$out .= self::html_item( $link, 'name_l', '', $shortcode_config, $shortcode_slider );
			}
		} else {
			// Enhanced style (all items given in link_items attribute).
			$items = json_decode( $shortcode_config->link_items, true );
			if ( is_array( $items ) ) {
				$out .= self::html_section( $link, $items, $shortcode_config, $shortcode_slider );
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
	 * @param ShortcodeConfig      $shortcode_config The ShortcodeConfig object.
	 * @param ShortcodeSlider|null $shortcode_slider The ShortcodeSlider object.
	 * @return string HTML to render link section.
	 */
	private static function html_section( $link, $items, $shortcode_config, $shortcode_slider ) {
		$out = '';
		foreach ( $items as $name => $item ) {
			if ( is_array( $item ) ) {
				$out .= '<div class="lvw-section-' . $name . $shortcode_config->class_suffix . '">';
				$out .= self::html_section( $link, $item, $shortcode_config, $shortcode_slider );
				$out .= '</div>';
			} else {
				$out .= self::html_item( $link, $name, $item, $shortcode_config, $shortcode_slider );
			}
		}
		return $out;
	}


	/**
	 * Get HTML for showing a link item
	 *
	 * @param object               $link Link object.
	 * @param string               $item Item type to display.
	 * @param string               $caption Link item caption.
	 * @param ShortcodeConfig      $shortcode_config The ShortcodeConfig object.
	 * @param ShortcodeSlider|null $shortcode_slider The ShortcodeSlider object.
	 * @return string HTML to render link item.
	 */
	private static function html_item( $link, $item, $caption, $shortcode_config, $shortcode_slider ) {
		// Check if a hyperlink shall be added.
		$is_link = ( '_l' === substr( $item, -2 ) );
		if ( $is_link ) {
			$item = substr( $item, 0, -2 );
		}
		// Handle link_item_img="nothing".
		if ( 'image' === $item && '' === $link->link_image && 'show_nothing' === $shortcode_config->link_item_img ) {
			return '';
		}
		// Prepare output.
		$out = '<div class="lvw-item-' . $item . $shortcode_config->class_suffix . '">';
		if ( ! empty( $caption ) ) {
			$out .= '<span class="lvw-item-caption' . $shortcode_config->class_suffix . '">' . $caption . '</span>';
		}
		// Pepare link if required.
		if ( $is_link ) {
			// Check target.
			if ( 'std' !== $shortcode_config->link_target ) {
				$target = '_' . $shortcode_config->link_target;
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
			$combined_rel = $shortcode_config->link_rel . ' ' . $link->link_rel;
			if ( ! empty( $combined_rel ) ) {
				// Check value according to allowed values for HTML5 (see https://www.w3schools.com/tags/att_a_rel.asp).
				$rels = array_intersect(
					array_unique( explode( ' ', $combined_rel ) ),
					(array) $shortcode_config->get( 'link_rel' )->permitted_values
				);

				$rel = ' rel="' . implode( ' ', $rels ) . '"';
			}
			$out .= '<a class="lvw-anchor' . $shortcode_config->class_suffix . '" href="' . $link->link_url . '" target="' . $target . '" title="' . $link->link_name . $description . '"' . $rel . '>';
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
				$out .= self::html_img_tag( $link, $shortcode_config, $shortcode_slider );
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
	 * @param object               $link Link object.
	 * @param ShortcodeConfig      $shortcode_config The ShortcodeConfig object.
	 * @param ShortcodeSlider|null $shortcode_slider The ShortcodeSlider object.
	 * @return string HTML to render the image.
	 */
	private static function html_img_tag( $link, $shortcode_config, $shortcode_slider ) {
		// Handle links without an image.
		if ( empty( $link->link_image ) ) {
			switch ( $shortcode_config->link_item_img ) {
				case 'show_link_name':
					return $link->link_name;
				case 'show_link_description':
					return $link->link_description;
				// 'show_nothing': is already handled in html_link_item.
				// 'show_img_tag': proceed as normal with the image tag.
			}
		}
		// Handle image size.
		if ( empty( $shortcode_slider ) ) {
			$size_text = '';
		} else {
			$slider_width  = $shortcode_slider->width;
			$slider_height = $shortcode_slider->height;
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

}
