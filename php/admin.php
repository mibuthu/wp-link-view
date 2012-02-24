<?php

// This class handles all available admin pages
class admin {

	// show the main admin page as a submenu of "Links"
	public static function show_main() {
		if (!current_user_can('edit_posts'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		$output ='
			<div class="wrap nosubsub" style="padding-bottom:15px">
			<div id="icon-link-manager" class="icon32"><br /></div><h2>Link View</h2>
			</div>
			<h4>Usage of the <code>[linkview]</code> shortcode</h4>
			<div style="padding:0 20px">
				<h4>A short introduction</h4>
				<p>Link View works using a "shortcode". Shortcodes are snippets of pseudo code that are placed in blog posts, pages and on some forums to easily render HTML output.</p>
				<p>The following is the basic form of a shortcode:</p>
				<p><code>[shortcodename]</code></p>
				<p></p>

				<p>In this early version there is no parameter available. Parameters will be added in a later release.</p>
			</div>';
		echo $output;
	}
}
?>
