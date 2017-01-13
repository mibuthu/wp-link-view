<?php
if(!defined('WPINC')) {
	exit;
}

$options_helptexts = array(
	'lv_req_cap'  => array('type'    => 'radio',
	                       'label'   => __('Required capabilities to show LinkView About page','link-view'),
	                       'caption' => array('manage_links' => 'manage_links (Standard)', 'edit_pages' => 'edit_pages', 'edit_posts' => 'edit_posts'),
	                       'desc'    => __('With this option you can specify the required capabilities to show the LinkView About page.<br />
	                                       (see <a href="http://codex.wordpress.org/Roles_and_Capabilities">WordPress Codex</a> for more infos).','link-view')),

	'lv_ml_role'  => array('type'    => 'radio',
	                       'label'   => __('Required role to manage links','link-view'),
	                       'caption' => array('editor' => 'Editor (Wordpress-Standard)', 'author' => 'Author', 'contributor' => 'Contributor', 'subscriber' => 'Subscriber'),
	                       'desc'    => __('With this option you can overwrite the wordpress default minimum required role to manage links (Capability: "manage_links").<br />
	                                       (see <a href="http://codex.wordpress.org/Roles_and_Capabilities">WordPress Codex</a> for more infos).<br />
	                                       Please not that this option also affects the viewing the LinkView About page if the required capabilities are set to "manage_links".','link-view')),

	'lv_css'      => array('type'    => 'textarea',
	                       'label'   => __('CSS-code for linkview','link-view'),
	                       'desc'    => 'With this option you can specify CSS-code for the links displayed by the linkview shortcode or widget.<br />
	                                     You can use the classes which are automatically created by the linkview shortcode or widget e.g. .lv-item-image, .lv-section-name, .lv-cat-name, ...<br />
	                                     You can find all available classes if you have a look at the sourcecode of your page where the shortcode or widget is included.<br />
	                                     If you use the shortcode several times you can specify different css styles if you set the attribute "class_suffix" and create CSS-code for these special classes
	                                     e.g. .lv-link-list-suffix, .lv-item-name-suffix.<br /><br />
	                                     Below you can find some working examples:<br />
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
