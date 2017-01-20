<?php
if(!defined('WPINC')) {
	exit;
}

$options_helptexts = array(
	'lv_req_cap' => array('type'    => 'radio',
	                      'label'   => sprintf(__('Required capabilities to show the %1$s page','link-view'), '"'.__('About','link-view').' LinkView"'),
	                      'caption' => array('manage_links' => 'manage_links ('.__('Default','link-view').')', 'edit_pages' => 'edit_pages', 'edit_posts' => 'edit_posts'),
	                      'desc'    => sprintf(__('With this option you can specify the required capabilities to show the %1$s page.','link-view'), '"'.__('About','link-view').' LinkView"').'<br />
	                                   '.sprintf(__('More information can be found in the %1$s.','link-view'), '<a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank" rel="noopener">WordPress Codex</a>')),

	'lv_ml_role' => array('type'    => 'radio',
	                      'label'   => __('Required role to manage links','link-view'),
	                      'caption' => array('editor' => __('Editor').' (WordPress-'.__('Default','link-view').')', 'author' => __('Author'), 'contributor' => __('Contributor'), 'subscriber' => __('Subscriber')),
	                      'desc'    => __('With this option minimum required role to manage links can be set','link-view').' ('.__('Capability','link-view').': "manage_links").<br />
	                                   '.sprintf(__('More information can be found in the %1$s.','link-view'), '<a href="http://codex.wordpress.org/Roles_and_Capabilities" target="_blank" rel="noopener">WordPress Codex</a>').'<br />
	                                   '.sprintf(__('Please note that this option also affects the access to the %1$s page if the required capabilities are set to %2$s.','link-view'), '"'.__('About','link-view').' LinkView"', '"manage_links"')),

	'lv_css'     => array('type'    => 'textarea',
	                      'label'   => sprintf(__('CSS-code for %1$s','link-view'), 'LinkView'),
	                      'desc'    => sprintf(__('With this option you can specify CSS-code for the links displayed by the %1$s shortcode.','link-view'), '[link-view]').'<br />
	                                   '.sprintf(__('There are a lot of CSS classes available which are automatically added by the %1$s shortcode','link-view'), '[link-view]').' ('.__('e.g.','link-view').' .lv-item-image, .lv-section-name, .lv-cat-name, ...).<br />
	                                   '.__('All available classes can be found in the sourcecode of a post or page where the shortcode is included.','link-view').'<br />
	                                   '.sprintf(__('To differ between different shortcodes you can set the attribute %1$s and add CSS-code for these special classes','link-view'), '"class_suffix"').'
	                                   ('.__('e.g.','link-view').' .lv-link-list-suffix, .lv-item-name-suffix).<br /><br />
	                                   '.__('Examples','link-view').':<br />
	                                   <code>.lv-link {<br />
	                                      &nbsp;&nbsp;&nbsp;margin-bottom: 15px;<br />
	                                   }<br />
	                                   .lv-item-image img {<br />
	                                      &nbsp;&nbsp;&nbsp;-webkit-border-radius: 9px;<br />
	                                      &nbsp;&nbsp;&nbsp;-moz-border-radius: 9px;<br />
	                                      &nbsp;&nbsp;&nbsp;border-radius: 9px;<br />
	                                   }<br />
	                                   .lv-item-image-detail img {<br />
	                                      &nbsp;&nbsp;&nbsp;max-width: 250px;<br />
	                                   }<br />
	                                   .lv-section-left-detail {<br />
	                                      &nbsp;&nbsp;&nbsp;float: left;<br />
	                                   }<br />
	                                   .lv-section-right-detail {<br />
	                                      &nbsp;&nbsp;&nbsp;float: right;<br />
	                                      &nbsp;&nbsp;&nbsp;margin-left: 15px;<br />
	                                   }</code>')
);
?>
