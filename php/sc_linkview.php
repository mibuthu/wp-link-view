<?php

// This class handles the shortcode [linkview]
class sc_linkview {

	// main function to show the rendered HTML output
	public static function show_html() {
		//echo wp_list_bookmarks('');
		$links = get_bookmarks('');
		foreach( $links as $link ) {
			echo ( $link->link_name.'<br />' );
			$i++;
		}
	}
}
?>
