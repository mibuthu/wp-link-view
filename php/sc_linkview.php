<?php

// This class handles the shortcode [linkview]
class sc_linkview {

	// main function to show the rendered HTML output
	public static function show_html( $atts ) {
		
		$a = shortcode_atts( array(
			'cat_name' => '',
			'test' => 'test'
		), $atts );

		$out = "";
		$out .= 'cat_name: {'.$a['cat_name'].'}<br />';
		$out .= 'test: {'.$a['test'].'}<br />';
		ob_start();
		wp_list_bookmarks( 'category_name='.$a['cat_name'] );
		$out .= ob_get_contents();
		ob_end_clean();
/*		$links = get_bookmarks('');
		foreach( $links as $link ) {
			$out .= $link->link_name.'<br />';
			$i++;
		}
*/
		return "$out";
	}
}
?>
