<?php
if(!defined('WPINC')) {
	exit;
}

$widget_items_helptexts = array(
	'title' => array('type'       => 'text',
	                 'caption'    => __('Title','link-view').':',
	                 'tooltip'    => __('This option defines the displayed title for the widget.','link-view'),
	                 'form_style' => null),

	'atts' =>  array('type'       => 'textarea',
	                 'caption'    => __('Shortcode attributes','link-view').':',
	                 'tooltip'    => sprintf(__('All attributes which are available for the %1$s shortcode can be used.','link-view'), '[link-view]'),
	                 'form_style' => null,
	                 'form_rows'  => 5)
);
?>
