<?php
if(!defined('WPINC')) {
	exit;
}

$widget_items_helptexts = array(
	'title' => array('type'       => 'text',
	                 'caption'    => __('Title:'),
	                 'tooltip'    => __('The title for the widget'),
	                 'form_style' => null),

	'atts' =>  array('type'       => 'textarea',
	                 'caption'    => __('Shortcode attributes:'),
	                 'tooltip'    => __('You can add all attributes which are available for the linkview shortcode'),
	                 'form_style' => null,
	                 'form_rows'  => 5)
);
?>
