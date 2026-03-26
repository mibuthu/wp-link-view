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
			// Prepare cat classes string
			$cat_classes = array_map( fn( string $cat_slug ): string => 'category-' . $cat_slug, $cat_classes );
			$cat_classes = ' ' . implode( ' ', $cat_classes );
		}
		$out = '
			<div class="lvw-link' . $config->class_suffix->get_str() . $cat_classes . '"';
		if ( ViewType::Slider !== $config->view_type->get() && VerticalAlign::Std !== $config->vertical_align->get() ) {
			$out .= ' style="display:inline-block; vertical-align:' . $config->vertical_align->get_str() . ';"';
		}
		$out .= '>';
		if ( '' === $config->link_items->get_str() ) {
			// Simple style (name or image).
			if ( $config->show_img->get_bool() && ! is_null( $this->link_image ) ) {
				// Image.
				$out .= self::html_item( 'image_l', '', $config, $slider );
			} else {
				// Name.
				$out .= self::html_item( 'name_l', '', $config, $slider );
			}
		} else {
			// Enhanced style (all items given in link_items attribute).
			$items = json_decode( $config->link_items->get_str(), true );
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
				$out .= '<div class="lvw-section-' . $name . $config->class_suffix->get_str() . '">';
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
		if ( 'image' === $item && '' === $this->link_image && LinkItemImg::ShowNothing === $config->link_item_img->get() ) {
			return '';
		}
		// Prepare output.
		$out = '<div class="lvw-item-' . $item . $config->class_suffix->get_str() . '">';
		if ( '' !== $caption ) {
			$out .= '<span class="lvw-item-caption' . $config->class_suffix->get_str() . '">' . $caption . '</span>';
		}
		// Prepare link if required.
		if ( $is_link ) {
			// Check target.
			$target = '';
			if ( LinkTarget::Std !== $config->link_target->get() ) {
				$target = ' target="_' . $config->link_target->get_str() . '"';
			}
			// Check description.
			$description = '';
			if ( '' !== $this->link_description ) {
				$description = ' (' . $this->link_description . ')';
			}
			// Check rel attribute.
			// TODO: Check and fix rel implementation, rel attribute is disabled for now
			$rel          = '';
			// $combined_rel = $config->link_rel . ' ' . $this->link_rel;
			// // Check value according to allowed values for HTML5 (see https://www.w3schools.com/tags/att_a_rel.asp).
			// $rels = array_intersect(
			// 	array_unique( explode( ' ', $combined_rel ) ),
			// 	(array) $config->get( 'link_rel' )->permitted_values
			// );
			// $rel  = ' rel="' . implode( ' ', $rels ) . '"';
			$out .= '<a class="lvw-anchor' . $config->class_suffix->get_str() . '" href="' . $this->link_url . $target . ' title="' . $this->link_name . $description . '"' . $rel . '>';
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
				case LinkItemImg::ShowLinkName:
					return $this->link_name;
				case LinkItemImg::ShowLinkDescription:
					return $this->link_description;
				// LinkItemImg::ShowNothing: is already handled in html_link_item.
				// LinkItemImg::ShowImgTag: proceed as normal with the image tag.
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
