<?php
/**
 * Additional data for the widget items required for the widget admin page.
 *
 * @package link-view
 */

if ( ! defined( 'WPINC' ) ) {
	exit;
}

$lv_widget_items_helptexts = array(
	'title' => array(
		'type'    => 'text',
		'caption' => __( 'Title', 'link-view' ) . ':',
		'tooltip' => __( 'This option defines the displayed title for the widget.', 'link-view' ),
	),

	'atts'  => array(
		'type'    => 'textarea',
		'caption' => __( 'Shortcode attributes', 'link-view' ) . ':',
		'tooltip' => sprintf( __( 'All attributes which are available for the %1$s shortcode can be used.', 'link-view' ), '[link-view]' ),
	),
);

