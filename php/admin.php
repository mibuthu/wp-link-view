<?php
require_once( LV_PATH.'php/sc_linkview.php' );
require_once( LV_PATH.'php/options.php' );

// This class handles all available admin pages
class lv_admin {
	private $shortcode;
	private $options;
	private $tabs;

	public function __construct() {
		$this->shortcode = &sc_linkview::get_instance();
		$this->options = &lv_options::get_instance();
		$this->tabs = array( 'attributes' => 'Attributes',
		                     'css'        => 'CSS-Styles' );
	}

	/**
	 * Add and register all admin pages in the admin menu
	 */
	public function register_pages() {
		$page = add_submenu_page( 'link-manager.php', 'Link View', 'Link View', 'edit_posts', 'lv_admin_main', array( &$this, 'show_main' ) );
		add_action( 'admin_print_scripts-'.$page, array( &$this, 'embed_admin_main_scripts' ) );
	}

	// show the main admin page as a submenu of "Links"
	public function show_main() {
		if( !current_user_can( 'edit_posts' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$out ='
			<div class="wrap nosubsub">
			<div id="icon-link-manager" class="icon32"><br /></div><h2>Link View</h2>
			</div>
			<h3 class="lv-headline">Usage</h3>
			<table>
			<tr>
				<td class="lv-usage-caption"><h4>LinkView Shortcode:</h4></td>
				<td class="lv-usage-content">
					With the shortcode <code>[linkview]</code> you can use LinkView in posts or pages.<br />
					Shortcodes are snippets of pseudo code that are placed in blog posts or pages to easily render HTML output.<br />
					Attributes are used to modify the shortcode. The available attributes for <code>[linkview]</code> are listed below.
				</td>
			</tr>
			<tr>
				<td class="lv-usage-caption"><h4>LinkView Widget:</h4></td>
				<td class="lv-usage-content">
					With the LinkView Widget you can use LinkView in sidebars.<br />
					Goto Appearance -> Widgets and add the "LinkView"-Widget in one of your sidebars.<br />
					You can enter a title for the widget and add all the required attributes in the "Shortcode attributes" field.<br />
					You can use all available attributes from the shortcode for the widget too.<br />
					Press "Save" to enable the changes.
				</td>
			</tr>
			</table>';
		$current_tab = $this->get_current_tab();
		$out .= $this->html_tabs( $current_tab );
		switch( $current_tab ) {
			case 'css' :
				$out .= $this->html_css( 'css', 'newline' );
				break;
			default : // attributes
				$out .= $this->html_atts();
		}
		echo $out;
	}

	private function html_tabs( $current ) {
		$out = '
			<div style="clear: both;"><h3 class="nav-tab-wrapper">';
		foreach( $this->tabs as $tab => $name ){
			$class = ( $tab == $current ) ? ' nav-tab-active' : '';
			$out .= '
				<a class="nav-tab'.$class.'" href="?page=lv_admin_main&amp;tab='.$tab.'">'.$name.'</a>';
		}
		$out .= '
			</h3></div>';
		return $out;
	}

	private function html_atts() {
		$out = '
			<h3 class="lv-headline">Available Attributes</h3>
			<div>
				To get the correct result you can combine as much attributes as you want.<br />
				The <code>[linkview]</code> shortcode including the attributes "cat_name" and "show_img" looks like this:
				<p><code>[linkview cat_name=Sponsors show_img=1]</code></p>
				<p>Below is a list of all the supported attributes with their descriptions and available options:</p>';
		$out .= '<h4 class="lv-section-caption">General:</h4>';
		$out .= $this->html_atts_table( 'general' );
		$out .= '<h4 class="lv-section-caption">Link List:</h4>';
		$out .= $this->html_atts_table( 'list' );
		$out .= '<h4 class="lv-section-caption">Link Slider:</h4>';
		$out .= $this->html_atts_table( 'slider' );
		$out .= '
			</div>';
		return $out;
	}

	private function html_atts_table( $section ) {
		$out = '
			<table class="lv-atts-table">
				<tr>
					<th class="lv-atts-table-name">Attribute name</th>
					<th class="lv-atts-table-options">Value options</th>
					<th class="lv-atts-table-default">Default value</th>
					<th class="lv-atts-table-desc">Description</th>
				</tr>';
		$atts = $this->shortcode->get_atts( $section );
		foreach( $atts as $aname => $a ) {
			$out .= '
				<tr>
					<td>'.$aname.'</td>
					<td>'.$a['val'].'</td>
					<td>'.$a['std_val'].'</td>
					<td>'.$a['desc'].'</td>
				</tr>';
		}
		$out .= '
			</table>';
		return $out;
	}

	private function html_css() {
		$out = '
			<div id="posttype-page" class="posttypediv">
			<form method="post" action="options.php">
				';
		ob_start();
		settings_fields( 'lv_'.$_GET['tab'] );
		$out .= ob_get_contents();
		ob_end_clean();
		$out .= '
			<table class="form-table">';
		$out .= $this->html_options( 'css', 'newline' );
		$out .= '
			</table>
			';
		ob_start();
		submit_button();
		$out .= ob_get_contents();
		ob_end_clean();
		$out .='
			</form>
			</div>';
		return $out;
	}

	private function html_options( $section, $desc_pos='right' ) {
		$out = '';
		foreach( $this->options->options as $oname => $o ) {
			if( $o['section'] == $section ) {
				$out .= '
					<tr>
						<th>';
				if( $o['label'] != '' ) {
					$out .= '<label for="'.$oname.'">'.$o['label'].':</label>';
				}
				$out .= '</th>
						<td>';
				switch( $o['type'] ) {
					case 'textarea':
						$out .= $this->show_textarea( $oname, $this->options->get( $oname ) );
						break;
				}
				$out .= '
						</td>';
				if( $desc_pos == 'newline' ) {
					$out .= '
					</tr>
					<tr>
						<td></td>';
				}
				$out .= '
						<td class="description">'.$o['desc'].'</td>
					</tr>';
			}
		}
		return $out;
	}

	private function get_current_tab() {
		if( isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $this->tabs ) ) {
			return $_GET['tab'];
		}
		else {
			return 'attributes';
		}
	}

	private function show_textarea( $name, $value ) {
		$out = '
							<textarea name="'.$name.'" id="'.$name.'" rows="20" class="large-text code">'.$value.'</textarea>';
		return $out;
	}

	public function embed_admin_main_scripts() {
		wp_enqueue_style( 'linkview_admin_main_css', LV_URL.'css/admin_main.css' );
	}
} // end class lv_admin
?>
