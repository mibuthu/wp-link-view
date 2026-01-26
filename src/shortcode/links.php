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
	 * phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase -- wpTerm is in CamelCase due to rector rule RenameParamToMatchTypeRector
	 */
	public static function get( Config $config, ?\WP_Term $wpTerm = null ): array {
		$args = [
			'orderby' => $config->link_orderby,
			'order'   => $config->link_order,
			'limit'   => $config->num_links,
		];
		if ( $wpTerm instanceof \WP_Term ) {
			$args['category_name'] = $wpTerm->name;
		}
		return get_bookmarks( $args );
		// phpcs:enable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
	}


	/**
	 * Get link categories
	 *
	 * @return \WP_Term[] Link category object array.
	 */
	public static function categories( Config $config ): array {
		$cat_array = [];
		// TODO: The cat_filter value "all" is deprecated and can be removed in 0.9.
		if ( '' !== $config->cat_filter && 'all' !== $config->cat_filter ) {
			str_replace( ',', '|', $config->cat_filter );
			$cat_slugs = array_map( trim( ... ), array_map( strval( ... ), explode( '|', (string) $config->cat_filter ) ) );
			foreach ( $cat_slugs as $cat_slug ) {
				$term = get_term_by( 'slug', $cat_slug, 'link_category' );
				if ( $term instanceof \WP_Term ) {
					$cat_array[] = $term;
				}
			}
		} else {
			$terms = get_terms(
				[
					'taxonomy' => 'link_category',
					'orderby'  => 'name',
				]
			);
			if ( is_array( $terms ) ) {
				$cat_array = $terms;
			}
			if ( '' !== $config->exclude_cat ) {
				$exclude_cat = array_map( trim( ... ), array_map( strval( ... ), explode( ',', (string) $config->exclude_cat ) ) );
				$diff        = [];
				foreach ( $cat_array as $cat ) {
					if ( ! in_array( $cat->name, $exclude_cat, true ) ) {
						$diff[] = $cat;
					}
				}
				$cat_array = $diff;
				unset( $diff );
			}
		}
		return $cat_array;
	}

}
