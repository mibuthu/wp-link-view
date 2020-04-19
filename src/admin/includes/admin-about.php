<?php
/**
 * LinkViews About Class
 *
 * @package link-view
 */

// declare( strict_types=1 ); Remove for now due to warnings in php <7.0!
if ( ! defined( 'WP_ADMIN' ) ) {
	exit();
}

require_once LV_PATH . 'includes/options.php';


/**
 * LinkViews About Class
 *
 * This class handles the display of the admin about page
 */
class LV_Admin_About {

		/**
		 * Class singleton instance reference
		 *
		 * @var self
		 */
	private static $instance;

	/**
	 * Options class instance reference
	 *
	 * @var LV_Options
	 */
	private $options;


	/**
	 * Singleton provider and setup
	 *
	 * @return self
	 */
	public static function &get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * Class constructor which initializes required variables
	 */
	private function __construct() {
		$this->options = &LV_Options::get_instance();
	}


	/**
	 * Show the admin about page
	 *
	 * @return void
	 */
	public function show_page() {
		// Check required privilegs.
		if ( ! current_user_can( $this->options->get( 'lv_req_cap' ) ) ) {
			// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$tab = ! empty( $_GET['tab'] ) && 'atts' === sanitize_title( (string) wp_unslash( (string) $_GET['tab'] ) ) ? 'atts' : 'general';
		// Create content.
		echo wp_kses_post(
			'
			<div class="wrap">
				<div id="icon-link-manager" class="icon32"><br /></div><h2>' . sprintf( __( 'About %1$s', 'link-view' ), 'LinkView' ) . '</h2>'
		);
		$this->show_tabs( $tab );
		if ( 'atts' === $tab ) {
			$this->show_atts();
		} else {
			$this->show_help();
			$this->show_author();
			$this->show_translation_info();
		}
		echo '
			</div>';
	}


	/**
	 * Show the tab bar
	 *
	 * @param string $current The current tab.
	 * @return void
	 */
	private function show_tabs( $current = 'general' ) {
		$tabs = array(
			'general' => __( 'General', 'link-view' ),
			'atts'    => __( 'Shortcode Attributes', 'link-view' ),
		);
		echo '<h3 class="nav-tab-wrapper">';
		foreach ( $tabs as $tab => $name ) {
			$class = ( $tab === $current ) ? ' nav-tab-active' : '';
			echo '<a class="nav-tab' . esc_html( $class ) . '" href="?page=lv_admin_about&amp;tab=' . esc_attr( $tab ) . '">' . esc_html( $name ) . '</a>';
		}
		echo '</h3>';
	}


	/**
	 * Show help HTML
	 *
	 * @return void
	 */
	private function show_help() {
		echo wp_kses_post(
			'
			<h3>' . __( 'Help and Instructions', 'link-view' ) . '</h3>
			<h4>' . __( 'Show links in posts or pages', 'link-view' ) . '</h4>
			<div class="help-content">
				<p>' . sprintf( __( 'To show links in a post or page the shortcode %1$s must be added in the post or page content text.', 'link-view' ), '<code>[linkview]</code>' ) . '</p>
				<p>' . __( 'The listed links and their styles can be modified with the available attributes for the shortcode.', 'link-view' ) . '<br />
				' . __( 'You can combine as much attributes as you want.', 'link-view' ) . '
				' . sprintf( __( 'E.g. the shortcode including the attributes %1$s and %2$s would look like this', 'link-view' ), '"cat_filter"', '"show_img"' ) . ':<br />
				<code>[linkview cat_filter=Sponsors show_img=1]</code><br />
				' . __( 'Below you can find tables with all supported attributes, their descriptions and available options.', 'link-view' ) . '</p>
			</div>
			<h4>' . __( 'Show links in sidebars and widget areas', 'link-view' ) . '</h4>
			<div class="help-content">
				' . sprintf( __( 'With the %1$s Widget you can add links in sidebars and widget areas.', 'link-view' ), 'LinkView' ) . '<br />
				' .
				sprintf(
					__( 'Goto %1$s and drag the %2$s-Widget into one of the sidebar or widget areas.', 'link-view' ),
					'<a href="' .
					admin_url( 'widgets.php' ) . '">' .
					// Use "default" text domain for translations available in WordPress Core.
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
					__( 'Appearance' ) . ' &rarr; ' .
					// Use "default" text domain for translations available in WordPress Core.
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
					__( 'Widgets' ) . '</a>',
					'"LinkView"'
				) . '<br />
				' . sprintf( __( 'Enter a title for the widget and add the required shortcode attributes in the appropriate field. All available shortcode attributes for the %1$s-shortcode can be used in the widget too.', 'link-view' ), '"linkview"' ) . '<br />
				' .
				sprintf(
					__( 'Press %1$s to confirm the changes.', 'link-view' ),
					'"' .
					// Use "default" text domain for translations available in WordPress Core.
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
					__( 'Save' ) .
					'"'
				) . '
			</div>
			<h4>' . sprintf( __( '%1$s Settings', 'link-view' ), 'LinkView' ) . '</h4>
			<div class="help-content">
				' .
				sprintf(
					__( 'In the %1$s settings page, available under %2$s, you can find some options to modify the plugin.', 'link-view' ),
					'LinkView',
					'<a href="' . admin_url( 'options-general.php?page=lv_admin_options' ) . '">' .
					// Use "default" text domain for translations available in WordPress Core.
					// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
					__( 'Settings' ) . ' &rarr; LinkView</a>'
				) . '
			</div>'
		);
	}


	/**
	 * Show author HTML
	 *
	 * @return void
	 */
	private function show_author() {
		echo wp_kses_post(
			'
			<h3>' . __( 'About the plugin author', 'link-view' ) . '</h3>
			<div class="help-content">
				<p>' . sprintf( __( 'This plugin is developed by %1$s, you can find more information about the plugin on the %2$s.', 'link-view' ), 'mibuthu', '<a href="https://wordpress.org/plugins/link-view" target="_blank" rel="noopener">' . __( 'WordPress plugin site', 'link-view' ) . '</a>' ) . '</p>
				<p>' . sprintf( __( 'If you like the plugin please rate it on the %1$s.', 'link-view' ), '<a href="https://wordpress.org/support/view/plugin-reviews/link-view" target="_blank" rel="noopener">' . __( 'WordPress plugin review site', 'link-view' ) . '</a>' ) . '<br />
				<p>' . __( 'If you want to support the plugin I would be happy to get a small donation', 'link-view' ) . ':<br />
				<a class="donate" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4ZHXUPHG9SANY" target="_blank" rel="noopener"><img src="' . LV_URL . 'admin/images/paypal_btn_donate.gif" alt="PayPal Donation" title="' . sprintf( __( 'Donate with %1$s', 'link-view' ), 'PayPal' ) . '" border="0"></a>
				<a class="donate" href="https://liberapay.com/mibuthu/donate" target="_blank" rel="noopener"><img src="' . LV_URL . 'admin/images/liberapay-donate.svg" alt="Liberapay Donation" title="' . sprintf( __( 'Donate with %1$s', 'link-view' ), 'Liberapay' ) . '" border="0"></a>
				<a class="donate" href="https://flattr.com/submit/auto?user_id=mibuthu&url=https%3A%2F%2Fwordpress.org%2Fplugins%2Flink-view" target="_blank" rel="noopener"><img src="' . LV_URL . 'admin/images/flattr-badge-large.png" alt="Flattr this" title="' . sprintf( __( 'Donate with %1$s', 'link-view' ), 'Flattr' ) . '" border="0"></a></p>
			</div>'
		);
	}


	/**
	 * Show translation info HTML
	 *
	 * @return void
	 */
	private function show_translation_info() {
		echo wp_kses_post(
			'
			<h3>' . __( 'Translations', 'link-view' ) . '</h3>
			<div class="help-content">
				<p>' . __( 'Please help translating this plugin into your language.', 'link-view' ) . '</p>
				<p>' . sprintf( __( 'You can submit your translations at %1$s.', 'link-view' ), '<a href="https://www.transifex.com/projects/p/wp-link-view">Transifex</a>' ) . '<br />
				' . __( 'There the source strings will be kept in sync with the actual development version. And in each plugin release the available translation files will be updated.', 'link-view' ) . '</p>'
		);
	}


	/**
	 * Show attributes HTML table
	 */
	private function show_atts() {
		require_once LV_PATH . 'includes/shortcode.php';
		$shortcode = new LV_Shortcode( 0 );
		$shortcode->load_atts_helptexts();
		echo wp_kses_post(
			'
			<h3>' . __( 'Shortcode Attributes', 'link-view' ) . '</h3>
			<div class="help-content">
				' . sprintf( __( 'In the following tables you can find all available shortcode attributes for %1$s', 'link-view' ), '<code>[linkview]</code>' ) . ':'
		);
			echo wp_kses_post( '<h4 class="atts-section-title">' . __( 'General', 'link-view' ) . ':</h4>' );
			$this->html_atts_table( $shortcode->get_atts( 'general' ) );
			echo wp_kses_post( '<h4 class="atts-section-title">' . __( 'Link List', 'link-view' ) . ':</h4>' );
			$this->html_atts_table( $shortcode->get_atts( 'list' ) );
			echo wp_kses_post( '<h4 class="atts-section-title">' . __( 'Link Slider', 'link-view' ) . ':</h4>' );
			$this->html_atts_table( $shortcode->get_atts( 'slider' ) );
			echo wp_kses_post(
				'<br />
				<h4 class="atts-section-title">' . __( 'Multi-column layout types and options', 'link-view' ) . ':</h4><a id="multicol"></a>
				' . __( 'There are 3 different types of multiple column layouts for category or link-lists available. Each type has some advantages but also some disadvantages compared to the others.', 'link-view' ) . '
				<p>' . __( 'Additionally the available layouts can be modified with their options', 'link-view' ) . ':</p>
				<table class="atts-table">
				<tr><th>' . __( 'layout type', 'link-view' ) . '</th><th>' . __( 'type description', 'link-view' ) . '</th></tr>
				<tr><td>' . __( 'Number', 'link-view' ) . '</td><td>' . __( 'Use a single number to specify a static number of columns.', 'link-view' ) . '<br />
					' . __( 'This is a short form of the static layout type (see below).', 'link-view' ) . '</td></tr>
				<tr><td>static</td><td>' . __( 'Set a static number of columns. The categories or links will be arranged in rows.', 'link-view' ) . '
					<h5>' . __( 'available options', 'link-view' ) . ':</h5>
					<em>num_columns</em>: ' . __( 'Provide a single number which specifys the number of columns. If no value is given 3 columns will be displayed by default.', 'link-view' ) . '</td></tr>
				<tr><td>css</td><td>' . sprintf( __( 'This type uses the %1$s to arrange the columns.', 'link-view' ), '<a href="https://www.w3schools.com/css/css3_multiple_columns.asp" target="_blank" rel="noopener">' . __( 'multi-column feature of CSS', 'link-view' ) . '</a>' ) . '
					<h5>' . __( 'available options', 'link-view' ) . ':</h5>
					' . sprintf( __( 'You can use all available properties for CSS3 Multi-column Layout (see %1$s for detailed information).', 'link-view' ), '<a href="https://www.w3schools.com/css/css3_multiple_columns.asp" target="_blank" rel="noopener">' . __( 'this link', 'link-view' ) . '</a>' ) . '<br />
					' . __( 'The given attributes will be added to the wrapper div element. Also the prefixed browser specific attributes will be added.', 'link-view' ) . '</td></tr>
				<tr><td>masonry</td><td>' . sprintf( __( 'This type uses the %1$s to arrange the columns.', 'link-view' ), '<a href="https://masonry.desandro.com/" target="_blank" rel="noopener">' . sprintf( __( '%1$s grid layout javascript library', 'link-view' ), 'Masonry' ) . '</a>' ) . '
					<h5>' . __( 'available options', 'link-view' ) . ':</h5>
					' . sprintf( __( 'You can use all Options which are available for the Masonry library (see %1$s for detailed information).', 'link-view' ), '<a href="https://masonry.desandro.com/options.html" target="_blank" rel="noopener">' . __( 'this link', 'link-view' ) . '</a>' ) . '<br />
					' . __( 'The given options will be forwarded to the javascript library.', 'link-view' ) . '</td></tr>
				</table>
				<div class="help-content">
					<h5>' . __( 'Usage', 'link-view' ) . ':</h5>
					' . __( 'For the most types and options it is recommended to define a fixed width for the categories and/or links. This width must be set manually e.g. via the css entry:', 'link-view' ) . ' <code>.lv-multi-column { width: 32%; }</code><br />
					' . __( 'Depending on the type and options there are probably more css modifications required for a correct multi-column layout.', 'link-view' ) . '<br />
					' .
					sprintf(
						__( 'There are several ways to add the required css code. One method is the %1$s setting %2$s which can be found in %3$s.', 'link-view' ),
						'LinkView',
						'"' .
						sprintf( __( 'CSS-code for %1$s', 'link-view' ), 'LinkView' ) . '"',
						'<a href="' . admin_url( 'options-general.php?page=lv_admin_options' ) . '">' .
						// Use "default" text domain for translations available in WordPress Core.
						// phpcs:ignore WordPress.WP.I18n.MissingArgDomainDefault
						__( 'Settings' ) . ' &rarr; LinkView</a>'
					) . '<br />
					' . sprintf( __( 'The optional type options must be added in brackets in the format "option_name=value", multiple options can be added seperated by a pipe %1$s.', 'link-view' ), '("<strong>|</strong>")' ) . '
					<h5>' . __( 'Examples', 'link-view' ) . ':</h5>
					<p><code>[linkview cat_columns=3]</code> &hellip; ' . __( 'show the categories in 3 static columns', 'link-view' ) . '</p>
					<p><code>[linkview link_columns="static(num_columns=2)"]</code> &hellip; ' . __( 'show the link-lists in 2 static columns', 'link-view' ) . '</p>
					<p><code>[linkview cat_columns="css(column-width=4)"</code> &hellip; ' . __( 'show the categories in columns with the css column properties with a fixed width per category', 'link-view' ) . '</p>
					<p><code>[linkview links_columns="css(column-count=4|column-rule=4px outset #ff00ff|column-gap=40px)"</code> &hellip; ' . __( 'show the link-lists in 4 columns by using the CSS multi column properties', 'link-view' ) . '</p>
					<p><code>[linkview cat_columns="masonry(isOriginTop=false|isOriginLeft=false)"</code> &hellip; ' . __( 'show the categories in columns by using the masonry script (with some specific masonry options)', 'link-view' ) . '</p>
				</div>
			</div>'
			);
	}


	/**
	 * Show a single attribute table for a given section
	 *
	 * @param array<string,LV_Attribute> $atts Attributes to display.
	 * @return void
	 */
	private function html_atts_table( $atts ) {
		echo wp_kses_post(
			'
			<table class="atts-table">
				<tr>
					<th class="atts-table-name">' . __( 'Attribute name', 'link-view' ) . '</th>
					<th class="atts-table-options">' . __( 'Value options', 'link-view' ) . '</th>
					<th class="atts-table-default">' . __( 'Default value', 'link-view' ) . '</th>
					<th class="atts-table-desc">' . __( 'Description', 'link-view' ) . '</th>
				</tr>'
		);
		foreach ( $atts as $name => $attribute ) {
			$value_options = is_array( $attribute->value_options ) ? implode( '<br />', $attribute->value_options ) : $attribute->value_options;
			echo wp_kses_post(
				'
				<tr>
					<td>' . $name . '</td>
					<td>' . $value_options . '</td>
					<td>' . $attribute->value . '</td>
					<td>' . $attribute->description . '</td>
				</tr>'
			);
		}
		echo '
			</table>
			';
	}

}
