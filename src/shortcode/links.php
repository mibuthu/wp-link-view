<?php
/**
 * LinkView Links Class
 *
 * @package link-view
 */

declare( strict_types=1 );

namespace WordPress\Plugins\mibuthu\LinkView\Shortcode;

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

use const WordPress\Plugins\mibuthu\LinkView\PLUGIN_PATH;

require_once PLUGIN_PATH . 'shortcode/config.php';


/**
 * LinkView Links Class
 *
 * This class handles the shortcode view for link lists.
 */
class Links {


	/**
	 * Get Links
	 *
	 * @return Link[]
	 *
	 * phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- wpTerm is in CamelCase due to rector rule RenameParamToMatchTypeRector
	 */
	public static function get( Config $config, ?\WP_Term $wpTerm = null ): array {
		$args = [
			'orderby' => $config->link_orderby->get_str(),
			'order'   => $config->link_order->get_str(),
			'limit'   => $config->num_links->get_int(),
		];
		if ( $wpTerm instanceof \WP_Term ) {
			$args['category_name'] = $wpTerm->name;
		}
		return array_map(
			fn ( $bookmark ): Link => new Link( $bookmark ),
			get_bookmarks( $args )
		);
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	}


	/**
	 * Get link categories
	 *
	 * @return \WP_Term[] Link category object array.
	 */
	public static function categories( Config $config ): array {
		if ( 0 < count( $config->cat_filter->get_array() ) ) {
			// use the categories from cat_filter attribute
			$cat_array = [];
			foreach ( $config->cat_filter->get_array() as $cat ) {
				$term = get_term_by( 'slug', $cat, 'link_category' );
				if ( $term instanceof \WP_Term ) {
					$cat_array[] = $term;
				}
			}
			return $cat_array;
		}
		// get all categories (terms)
		$terms = get_terms(
			[
				'taxonomy' => 'link_category',
				'orderby'  => 'name',
			]
		);
		if ( ! is_array( $terms ) ) {
			// no categories available
			return [];
		}
		if ( 0 < count( $config->exclude_cat->get_array() ) ) {
			// use all categories, except the categories defined in exclude_cat
			$cat_array = [];
			foreach ( $terms as $cat ) {
				if ( ! in_array( $cat->name, $config->exclude_cat->get_array(), true ) ) {
					$cat_array[] = $cat;
				}
			}
			return $cat_array;
		}
		// use all categories
		return $terms;
	}

}
