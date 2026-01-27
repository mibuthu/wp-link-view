<?php
/**
 * LinkView Link Class
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Shortcode;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

require_once PLUGIN_PATH . 'shortcode/config.php';
require_once PLUGIN_PATH . 'shortcode/links.php';

/**
 * LinkView Link Class
 *
 * This class handles a link an how to display the link in a shortcode.
 */
class Link {

	/**
	 * The WordPress bookmark object
	 */
	private object $bookmark;


	/**
	 * Create a new instance out of a WordPress bookmark object
	 */
	public function __construct( object $bookmark ) {
		$this->bookmark = $bookmark;
	}


	/**
	 * Get an attribute of the WordPress bookmark
	 */
	public function __get( string $attr ): mixed {
		return $this->bookmark->$attr;
	}


	/**
	 * Get HTML for showing a single link
	 */
	public function show_html( Config $config, ?Slider $slider = null ): string {
		$cat_classes = wp_get_object_terms( $this->link_id, 'link_category', [ 'fields' => 'slugs' ] );
		if ( ! is_array( $cat_classes ) ) {
			$cat_classes = '';
		} else {
			array_walk(
				$cat_classes,
				/**
				 * Prepare the category slug to the form "category-[slug]"
				 *
				 * @param string $cat_slug The category slug.
				 * @return string
				 */
				fn( $cat_slug ): string => 'category-' . $cat_slug
			);
			$cat_classes = ' ' . implode( ' ', $cat_classes );
		}
		$out = '
			<div class="lvw-link' . $config->class_suffix . $cat_classes . '"';
		if ( 'slider' !== $config->view_type && 'std' !== $config->vertical_align ) {
			$out .= ' style="display:inline-block; vertical-align:' . $config->vertical_align . ';"';
		}
		$out .= '>';
		if ( '' === $config->link_items ) {
			// Simple style (name or image).
			if ( $config->show_img && ! is_null( $this->link_image ) ) {
				// Image.
				$out .= self::html_item( 'image_l', '', $config, $slider );
			} else {
				// Name.
				$out .= self::html_item( 'name_l', '', $config, $slider );
			}
		} else {
			// Enhanced style (all items given in link_items attribute).
			$items = json_decode( (string) $config->link_items, true );
			if ( is_array( $items ) ) {
				$out .= self::html_section( $items, $config, $slider );
			} else {
				$out .= 'ERROR while json decoding. There must be an error in your "link_items" json syntax.';
			}
		}
		return $out . '</div>';
	}


	/**
	 * Get HTML for showing a link section
	 *
	 * @param array<string,string> $items Link items array included in the section.
	 */
	private function html_section( array $items, Config $config, ?Slider $slider ): string {
		$out = '';
		foreach ( $items as $name => $item ) {
			if ( is_array( $item ) ) {
				$out .= '<div class="lvw-section-' . $name . $config->class_suffix . '">';
				$out .= self::html_section( $item, $config, $slider );
				$out .= '</div>';
			} else {
				$out .= self::html_item( $name, $item, $config, $slider );
			}
		}
		return $out;
	}


	/**
	 * Get HTML for showing a link item
	 */
	private function html_item( string $item, string $caption, Config $config, ?Slider $slider ): string {
		// Check if a hyperlink shall be added.
		$is_link = ( str_ends_with( $item, '_l' ) );
		if ( $is_link ) {
			$item = substr( $item, 0, -2 );
		}
		// Handle link_item_img="nothing".
		if ( 'image' === $item && '' === $this->link_image && 'show_nothing' === $config->link_item_img ) {
			return '';
		}
		// Prepare output.
		$out = '<div class="lvw-item-' . $item . $config->class_suffix . '">';
		if ( '' !== $caption ) {
			$out .= '<span class="lvw-item-caption' . $config->class_suffix . '">' . $caption . '</span>';
		}
		// Prepare link if required.
		if ( $is_link ) {
			// Check target.
			if ( 'std' !== $config->link_target ) {
				$target = '_' . $config->link_target;
			} else {
				$target = $this->link_target;
				// Set target to _self if an empty string or _none was returned.
				if ( in_array( $target, [ '', '_none' ], true ) ) {
					$target = '_self';
				}
			}
			// Check description.
			$description = '';
			if ( '' !== $this->link_description ) {
				$description = ' (' . $this->link_description . ')';
			}
			// Check rel attribute.
			$rel          = '';
			$combined_rel = $config->link_rel . ' ' . $this->link_rel;
			// Check value according to allowed values for HTML5 (see https://www.w3schools.com/tags/att_a_rel.asp).
			$rels = array_intersect(
				array_unique( explode( ' ', $combined_rel ) ),
				(array) $config->get( 'link_rel' )->permitted_values
			);
			$rel  = ' rel="' . implode( ' ', $rels ) . '"';
			$out .= '<a class="lvw-anchor' . $config->class_suffix . '" href="' . $this->link_url . '" target="' . $target . '" title="' . $this->link_name . $description . '"' . $rel . '>';
		}
		$out .= match ( $item ) {
			'name' => $this->link_name,
			'address' => $this->link_url,
			'description' => $this->link_description,
			'image' =>  self::html_img_tag( $config, $slider ),
			'rss' =>  $this->link_rss,
			'notes' => $this->link_notes,
			'rating' => $this->link_rating,
		};
		if ( $is_link ) {
			$out .= '</a>';
		}
		return $out . '</div>';
	}


	/**
	 * Get HTML for showing the image
	 */
	private function html_img_tag( Config $config, ?Slider $slider ): string {
		// Handle links without an image.
		if ( empty( $this->link_image ) ) {
			switch ( $config->link_item_img ) {
				case 'show_link_name':
					return $this->link_name;
				case 'show_link_description':
					return $this->link_description;
				// 'show_nothing': is already handled in html_link_item.
				// 'show_img_tag': proceed as normal with the image tag.
			}
		}
		// Handle image size.
		$size_text = '';
		if ( $slider instanceof Slider ) {
			$slider_width  = $slider->width;
			$slider_height = $slider->height;
			if ( ! empty( $slider_width ) && ! empty( $slider_height ) ) {
				$slider_ratio             = $slider_width / $slider_height;
				[$img_width, $img_height] = getimagesize( $this->link_image );
				$img_ratio                = $img_width / $img_height;
				$scale                    = $slider_ratio > $img_ratio ? $slider_height / $img_height : $slider_width / $img_width;
				$size_text                = ' width=' . round( $img_width * $scale ) . ' height=' . round( $img_height * $scale );
			}
		}
		return '<img src="' . $this->link_image . '"' . $size_text . ' alt="' . $this->link_name . '" />';
	}

}
