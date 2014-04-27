<?php
if(!defined('WPINC')) {
	die;
}

require_once(LV_PATH.'includes/sc_linkview.php');
require_once(LV_PATH.'includes/options.php');

// This class handles all data for the admin about page
class LV_Admin_About {
	private static $instance;
	private $options;
	private $shortcode;

	private function __construct() {
		$this->options = &LV_Options::get_instance();
		$this->shortcode = &SC_Linkview::get_instance();
	}

	public static function &get_instance() {
		// singleton setup
		if(!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	// show the admin about page
	public function show_page() {
		// check required privilegs
		if(!current_user_can($this->options->get('lv_req_cap'))) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		// create content
		$out ='
			<div class="wrap nosubsub">
			<div id="icon-link-manager" class="icon32"><br /></div><h2>About LinkView</h2></div>
			<h3>Help and Instructions</h3>
			<h4>Create a page or post with links</h4>
			<div class="help-content"
				<p>"LinkView" works by using a "shortcode" in a page or post.</p>
				<p>Shortcodes are snippets of pseudo code that are placed in blog posts or pages to easily render HTML output.<br />
				To create a link page or post add the shortcode <code>[linkview]</code> in the text field of any page or post.</p>
				<p>There are many shortcode attributes available which let you change the listed links and their styling.<br />
				To get the correct result you can combine as much attributes as you want.<br />
				E.g. the shortcode including the attributes "cat_name" and "show_img" would look like this:<br />
				<code>[linkview cat_name=Sponsors show_img=1]</code><br />
				Below you can find a list with all supported attributes, their descriptions and available options.</p>
			</div>
			<h4>LinkView Widget</h4>
			<div class="help-content">
				With the LinkView Widget you can add links in sidebars and widget areas.<br />
				Goto <a href="'.admin_url('widgets.php').'">Appearance &rarr; Widgets</a> and add the "LinkView"-Widget in one of your sidebars.<br />
				You can enter a title for the widget and add all the required shortcode attributes in the appropriate field.<br />
				You can use all available shortcode attributes of the linkview-shortcode in the widget too.<br />
				Press "Save" to activate the changes.
			</div>
			<h4>Settings</h4>
			<div class="help-content">
				In the linkview settings page, available under <a href="'.admin_url('options-general.php?page=lv_admin_options').'">Settings &rarr; LinkView</a>, you can find some options to modify the plugin.
			</div>
			<h3>About</h3>
			<div class="help-content">
				<p>This plugin is developed by mibuthu, you can find more information about the plugin on the <a href="http://wordpress.org/plugins/link-view">wordpress plugin site</a>.</p>
				<p>If you like the plugin please give me a good rating on the <a href="http://wordpress.org/support/view/plugin-reviews/link-view">wordpress plugin review site</a>.<br />
				<p>I would also be happy to get a small <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W54LNZMWF9KW2" target="_blank">donation</a>.</p>
			</div>';
			$out .= $this->html_atts();
		echo $out;
	}

	private function html_atts() {
		$out = '
			<h3>Shortcode Attributes</h3>
			<div class="help-content">
				In the following tables you can find all available shortcode attributes for <code>[linkview]</code>:
				';
		$out .= '<h4 class="atts-section-title">General:</h4>';
		$out .= $this->html_atts_table('general');
		$out .= '<h4 class="atts-section-title">Link List:</h4>';
		$out .= $this->html_atts_table('list');
		$out .= '<h4 class="atts-section-title">Link Slider:</h4>';
		$out .= $this->html_atts_table('slider');
		$out .= '</div>';
		return $out;
	}

	private function html_atts_table($section) {
		$out = '
			<table class="atts-table">
				<tr>
					<th class="atts-table-name">Attribute name</th>
					<th class="atts-table-options">Value options</th>
					<th class="atts-table-default">Default value</th>
					<th class="atts-table-desc">Description</th>
				</tr>';
		$atts = $this->shortcode->get_atts($section);
		foreach($atts as $aname => $a) {
			$out .= '
				<tr>
					<td>'.$aname.'</td>
					<td>'.$a['val'].'</td>
					<td>'.$a['std_val'].'</td>
					<td>'.$a['desc'].'</td>
				</tr>';
		}
		$out .= '
			</table>
			';
		return $out;
	}
} // end class LV_Admin_About
?>
