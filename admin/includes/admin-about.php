<?php
if(!defined('WPINC')) {
	die;
}

require_once(LV_PATH.'includes/options.php');

// This class handles all data for the admin about page
class LV_Admin_About {
	private static $instance;
	private $options;

	private function __construct() {
		$this->options = &LV_Options::get_instance();
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
			<div id="icon-link-manager" class="icon32"><br /></div><h2>'.sprintf(__('About %1$s','link-view'), 'LinkView').'</h2></div>
			<h3>'.__('Help and Instructions','link-view').'</h3>
			<h4>'.__('Show links in posts or pages','link-view').'</h4>
			<div class="help-content">
				<p>'.sprintf(__('To show links in a post or page the shortcode %1$s must be added in the post or page content text.','link-view'), '<code>[linkview]</code>').'</p>
				<p>'.__('The listed links and their styles can be modified with the available attributes for the shortcode.','link-view').'<br />
				'.__('You can combine as much attributes as you want.','link-view').'
				'.sprintf(__('E.g. the shortcode including the attributes %1$s and %2$s would look like this','link-view'), '"cat_name"', '"show_img"').':<br />
				<code>[linkview cat_name=Sponsors show_img=1]</code><br />
				'.__('Below you can find tables with all supported attributes, their descriptions and available options.','link-view').'</p>
			</div>
			<h4>'.__('Show links in sidebars and widget areas','link-view').'</h4>
			<div class="help-content">
				'.sprintf(__('With the %1$s Widget you can add links in sidebars and widget areas.','link-view'), 'LinkView').'<br />
				'.sprintf(__('Goto %1$s and drag the %2$s-Widget into one of the sidebar or widget areas.','link-view'), '<a href="'.admin_url('widgets.php').'">'.__('Appearance').' &rarr; '.__('Widgets').'</a>', '"LinkView"').'<br />
				'.sprintf(__('Enter a title for the widget and add the required shortcode attributes in the appropriate field. All available shortcode attributes for the %1$s-shortcode can be used in the widget too.','link-view'), '"linkview"').'<br />
				'.sprintf(__('Press %1$s to confirm the changes.','link-view'), '"Save"').'
			</div>
			<h4>'.sprintf(__('%1$s Settings','link-view'), 'LinkView').'</h4>
			<div class="help-content">
				'.sprintf(__('In the %1$s settings page, available under %2$s, you can find some options to modify the plugin.','link-view'), 'LinkView', '<a href="'.admin_url('options-general.php?page=lv_admin_options').'">'.__('Settings').' &rarr; LinkView</a>').'
			</div>
			<h3>'.__('About','link-view').'</h3>
			<div class="help-content">
				<p>'.sprintf(__('This plugin is developed by %1$s, you can find more information about the plugin on the %2$s.','link-view'), 'mibuthu', '<a href="http://wordpress.org/plugins/link-view" target="_blank" rel="noopener">'.__('wordpress plugin site','link-view').'</a>').'</p>
				<p>'.sprintf(__('If you like the plugin please rate it on the %1$s.','link-view'), '<a href="http://wordpress.org/support/view/plugin-reviews/link-view" target="_blank" rel="noopener">'.__('wordpress plugin review site','link-view').'</a>').'<br />
				<p>'.__('If you want to support the plugin I would be happy to get a small donation','link-view').':<br />
				<a class="donate" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4ZHXUPHG9SANY" target="_blank" rel="noopener"><img src="'.LV_URL.'admin/images/paypal_btn_donate.gif" alt="PayPal Donation" title="Donate with PayPal" border="0"></a>
				<a class="donate" href="https://flattr.com/submit/auto?user_id=mibuthu&url=https%3A%2F%2Fwordpress.org%2Fplugins%2Flink-view" target="_blank" rel="noopener"><img src="'.LV_URL.'admin/images/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0"></a></p>
			</div>';
			$out .= $this->html_atts();
		echo $out;
	}

	private function html_atts() {
		require_once(LV_PATH.'includes/sc_linkview.php');
		$shortcode = &SC_Linkview::get_instance();
		$shortcode->load_sc_linkview_helptexts();
		$out = '
			<h3>'.__('Shortcode Attributes','link-view').'</h3>
			<div class="help-content">
				'.sprintf(__('In the following tables you can find all available shortcode attributes for %1$s','link-view'), '<code>[linkview]</code>').':
				';
		$out .= '<h4 class="atts-section-title">'.__('General','link-view').':</h4>';
		$out .= $this->html_atts_table($shortcode->get_atts('general'));
		$out .= '<h4 class="atts-section-title">'.__('Link List','link-view').':</h4>';
		$out .= $this->html_atts_table($shortcode->get_atts('list'));
		$out .= '<h4 class="atts-section-title">'.__('Link Slider','link-view').':</h4>';
		$out .= $this->html_atts_table($shortcode->get_atts('slider'));
		$out .= '<br />
				<h4 class="atts-section-title">'.__('Multi-column layout types and options','link-view').':</h4><a id="multicol"></a>
				There are 3 different types of multiple column layouts available for category or link multi-column view. Each type has some advantages and disadvantages compared to the others.
				<p>Additionally the available layouts can be modified with their options:</p>
				<table class="atts-table">
				<tr><th>layout type</th><th>type description</th></tr>
				<tr><td>Number</td><td>Use a single number to specify a static number of columns.<br />
					This is a short form of the static layout type (see below).</td></tr>
				<tr><td>static</td><td>Set a static number of columns. The categories or links will be arranged in rows.
					<h5>available options:</h5>
					<em>num_columns</em>: Provide a single number which specifys the number of columns. If no value is given 3 will be used by default.</td></tr>
				<tr><td>css</td><td>This type uses the <a href="http://www.w3schools.com/css/css3_multiple_columns.asp" target="_blank" rel="noopener">multi-column feature of CSS</a> to arrange the columns.
					<h5>available options:</h5>
					You can use all available properties for CSS3 Multi-column Layout (see <a href="http://www.w3schools.com/css/css3_multiple_columns.asp" target="_blank" rel="noopener">this link</a> for detailed information).<br />
					The given attributes will be added to the wrapper div element. Also the prefixed browser specific attributes will be added.</td></tr>
				<tr><td>masonry</td><td>This type uses the <a href="http://masonry.desandro.com/" target="_blank" rel="noopener">Masonry grid layout javascript library</a> to arrange the columns.
					<h5>available options:</h5>
					You can use all Options which are available for the Masonry library (see <a href="http://masonry.desandro.com/options.html" target="_blank" rel="noopener">masonry options</a> for detailed information).<br />
					The given options will be provided to the Masonry javascript library.</td></tr>
				</table>
				<div class="help-content">
					<h5>Usage:</h5>
					For the most types and options it is recommended to define a fixed width for the categories and/or links. This width must be set manually e.g. via the css entry: <code>.lv-multi-column { width: 32%; }</code><br />
					Depending on the type and options there are probably more css modifications required for a correct multi-column layout.<br />
					There are different ways to add required css code, one method is the link-view setting "CSS-code for linkview" which can be found in <a href="'.admin_url('options-general.php?page=lv_admin_options').'">Settings &rarr; LinkView</a>.<br />
					The optional type options must be added in brackets in the format "option_name=value", multiple options can be added seperated by a pipe ("|").
					<h5>Examples:</h5>
					<p><code>[linkview cat_columns=3]</code> &hellip; show the categories in 3 static columns</p>
					<p><code>[linkview link_columns="static(num_columns=2)"]</code> &hellip; show the link-lists in 2 static columns</p>
					<p><code>[linkview cat_columns="css(column-width=4)"</code> &hellip; show the categories in columns with the css column properties with a fixed width per category</p>
					<p><code>[linkview links_columns="css(column-count=4|column-rule=4px outset #ff00ff|column-gap=40px)"</code> &hellip; show the link-lists in 4 columns with multiple css column properties</p>
					<p><code>[linkview cat_columns="masonry(masonry(isOriginTop=false|isOriginLeft=false)"</code> &hellip; show the categories in columns with the masonry script (with some specific masonry options)</p>
				</div>
			</div>';
		return $out;
	}

	private function html_atts_table($atts) {
		$out = '
			<table class="atts-table">
				<tr>
					<th class="atts-table-name">'.__('Attribute name','link-view').'</th>
					<th class="atts-table-options">'.__('Value options','link-view').'</th>
					<th class="atts-table-default">'.__('Default value','link-view').'</th>
					<th class="atts-table-desc">'.__('Description','link-view').'</th>
				</tr>';
		foreach($atts as $aname => $a) {
			$val = is_array($a['val']) ? implode('<br />', $a['val']) : $a['val'];
			$out .= '
				<tr>
					<td>'.$aname.'</td>
					<td>'.$val.'</td>
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
