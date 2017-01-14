<?php
if(!defined('WPINC')) {
	die;
}

require_once(LV_PATH.'includes/options.php');

// This class handles all available admin pages
class LV_Admin_Settings {
	private static $instance;
	private $options;

	private function __construct() {
		$this->options = &LV_Options::get_instance();
		$this->options->load_options_helptexts();
	}

	public static function &get_instance() {
		// singleton setup
		if(!isset(self::$instance)) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	// show the admin settings page
	public function show_page() {
		// check required privilegs
		if(!current_user_can('manage_options')) {
			wp_die(__('You do not have sufficient permissions to access this page.'));
		}
		// create content
		$out ='
			<div class="wrap nosubsub">
			<div id="icon-link-manager" class="icon32"><br /></div><h2>'.sprintf(__('%1$s Settings','link-view'), 'LinkView').'</h2></div>';
		$out .= $this->html_settings();
		echo $out;
	}

	private function html_settings() {
		$out = '
			<div id="posttype-page" class="posttypediv">
			<form method="post" action="options.php">
				';
		ob_start();
		settings_fields('lv_options');
		$out .= ob_get_contents();
		ob_end_clean();
		$out .= '
			<table class="form-table">';
		$out .= $this->html_options();
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

	private function html_options() {
		$out = '';
		foreach($this->options->options as $oname => $o) {
			$out .= '
				<tr>
					<th>';
			if($o['label'] != '') {
				$out .= '<label for="'.$oname.'">'.$o['label'].':</label>';
			}
			$out .= '</th>
					<td>';
			switch($o['type']) {
				case 'radio':
					$out .= $this->show_radio($oname, $this->options->get($oname), $o['caption']);
					break;
				case 'textarea':
					$out .= $this->show_textarea($oname, $this->options->get($oname));
					break;
			}
			$out .= '
					</td>
					<td class="description">'.$o['desc'].'</td>
				</tr>';
		}
		return $out;
	}

	private function show_radio($name, $value, $caption, $disabled=false) {
		$out = '
							<fieldset>';
		foreach($caption as $okey => $ocaption) {
			$checked = ($value === $okey) ? 'checked="checked" ' : '';
			$out .= '
								<label title="'.$ocaption.'">
									<input type="radio" '.$checked.'value="'.$okey.'" name="'.$name.'">
									<span>'.$ocaption.'</span>
								</label>
								<br />';
		}
		$out .= '
							</fieldset>';
		return $out;
	}

	private function show_textarea($name, $value) {
		$out = '
						<textarea name="'.$name.'" id="'.$name.'" rows="25" class="large-text code">'.$value.'</textarea>';
		return $out;
	}
} // end class LV_Admin_Settings
?>
