<?php
/**
 * Additional data for the widget items required for the widget admin page.
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WPINC' ) ) {
	exit();
}

$lv_widget_items_helptexts = [
	'title' => [
		'type'    => 'text',
		'caption' => __( 'Title', 'link-view' ) . ':',
		'tooltip' => __( 'This option defines the displayed title for the widget.', 'link-view' ),
	],

	'atts'  => [
		'type'    => 'textarea',
		'caption' => __( 'Shortcode attributes', 'link-view' ) . ':',
		'tooltip' => sprintf( __( 'All attributes which are available for the %1$s shortcode can be used.', 'link-view' ), '[link-view]' ),
	],
];

