<?php

// This class handles all available admin pages
class lv_admin {
	private $shortcode;

	public function __construct() {
		require_once( 'sc_linkview.php' );
		$this->shortcode = sc_linkview::get_instance();
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
		if( !current_user_can('edit_posts' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		$out ='
			<div class="wrap nosubsub">
			<div id="icon-link-manager" class="icon32"><br /></div><h2>Link View</h2>
			</div>
			<h3>Usage</h3>
			<table>
			<tr>
				<td class="lv-caption"><h4>LinkView Shortcode:</h4></td>
				<td class="lv-content">
					With the shortcode <code>[linkview]</code> you can use LinkView in posts or pages.<br />
					Shortcodes are snippets of pseudo code that are placed in blog posts or pages to easily render HTML output.<br />
					Attributes are used to modify the shortcode. The available attributes for <code>[linkview]</code> are listed below.
				</td>
			</tr>
			<tr>
				<td class="lv-caption"><h4>LinkView Widget:</h4></td>
				<td class="lv-content">
					With the LinkView Widget you can use LinkView in sidebars.<br />
					Goto Appearance -> Widgets and add the "LinkView"-Widget in one of your sidebars.<br />
					You can enter a title for the widget and add all the required attributes in the "Shortcode attributes" field.<br />
					You can use all available attributes from the shortcode for the widget too.<br />
					Press "Save" to enable the changes.
				</td>
			</tr>
			</table>
			<h3>Available Attributes</h3>
			<div>
				 To get the correct result you can combine as much attributes as you want.<br />
				The <code>[linkview]</code> shortcode including the attributes "cat_name" and "show_img" looks like this:</p>
				<p><code>[linkview cat_name=Sponsors show_img=1]</code></p>
				<p>Below is a list of all the supported attributes with their descriptions and available options:</p>';
		$out .= $this->html_atts_table( NULL );
		$out .= '
			</div>';
		echo $out;
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

	public function embed_admin_main_scripts() {
		wp_enqueue_style( 'linkview_admin_main_css', LV_URL.'css/admin_main.css' );
	}
} // end class lv_admin
?>
