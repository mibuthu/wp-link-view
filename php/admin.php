<?php

// This class handles all available admin pages
class admin {

	// show the main admin page as a submenu of "Links"
	public static function show_main() {
		if (!current_user_can('edit_posts'))  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}

		$out ='
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
				<p>To facilitate customization of shortcodes, parameters are used. Shortcode parameters are entered in the following format:<p>
				<p><code>[shortcodename parametername=parametervalue]</code></p>
				<p></p>
				<p>The <code>[linkview]</code> shortcode including an example parameter called "cat_name" looks like this:</p>
				<p><code>[linkview cat_name=Sponsors]</code></p>
				<p>Below is a list of all the supported parameters and their functions:</p>
				<style type="text/css">
					<!--
					#lvadmintable {border:1px solid #aaa;border-collapse:collapse}
					#lvadmintable th {border:1px solid #aaa;background:#eeeeee;}
					#lvadmintable td {border:1px solid #aaa;padding:4px 3px;vertical-align: top}
					#lvadmintable td.secondrow {padding:4px 4px;}
					-->
				</style>
				<table id="lvadmintable">
					<tr>
						<th>Parameter name</th>
						<th>Parameter options</th>
						<th>Description</th>
					</tr>
					<tr>
						<td>cat_name</td>
						<td class="secondrow">Name(s)</td>
						<td>Use this parameter to specify what categories should be shown by name. For example <code>[linkview catname=Sponsors]</code>. If the catname has spaces, simply wrap the name in quotes.<br />
							Example: <code>[linkview catname="Social Media"]</code></td> 
					</tr>
				</table>
				<p>In this early version there only this parameter available. Other parameters will be added in a later releases.</p>
			</div>';
		echo $out;
	}
}
?>
