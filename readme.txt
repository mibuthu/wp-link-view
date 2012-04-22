=== Link View ===
Contributors: mibuthu
Tags: link, links, blogroll, view, linkview, list, slider, slideshow, images, pictures, banner, integrated, page, category, categories, admin, attribute
Requires at least: 3.0
Tested up to: 3.3.2
Stable tag: 0.2.4

Display a link-list or link-slider in a post or page by using a shortcode.


== Description ==

The purpose of this plugin is to to show the wordpress integrated links in a list or a slider by using a shortcode.
It is also possible to include the images. Use the shortcode [linkview] to add the Links/Blogroll in your site.
A detailed description of all available shortcode-attributes to modify the output can be found on the admin page under Links -> Link View.


== Installation ==

The easiest version of installing is to go to the admin page. There you can install new plugins in the menu Plugins -> Add new. Search for "Link View" and press "Install now".

If you want to install the plugin manually download the zip-file and extract the files in your wp-content/plugins folder.


== Frequently Asked Questions ==

= Is it possible to use the shortcode in a widget? =

Yes, but normally you have to enable a filter, that generally allows to use shortcodes in widgets. Depending on which theme you use, this filter is probably already enabled. But for example in the Twenty Eleven theme shortcodes doesn't work without enabling the filter. The required filter is already included in linkview.php, but is deactivated. If you want the enable the filter uncomment the line:

add_filter( 'widget_text', 'do_shortcode' );

In a later release an option will be added to allow enableing the filter via admin page.

= Is it possible to add multiple slider on one site? =

No, in the actual release it is only possible to have one slider working on screen. This will be fixed in a later release.


== Screenshots ==

1. Admin page with a description of the usage and the available attributes (plugin version 0.2.3)
2. Simple example page with a link list


== Changelog ==

= 0.2.4 (2012-04-22) =

* Show link name and link description in tooltip text, when the mouse is over the link

= 0.2.3 (2012-04-14) =

* Added attributes "vertical_align" and "list_symbol"

= 0.2.2 (2012-03-25) =

* Added attributes "slider_pause" and "slider_speed"

= 0.2.1 (2012-03-18) =

* Fixed bug to show correct image size in image list

= 0.2.0 (2012-03-17) =

* Initial support to show the links in a slider
* Added attributes "view_type", "slider_width", "slider_height"

= 0.1.1 (2012-03-03) =

* Modified html-output of link list (use own function to render output for more flexibility) 
* Added attributes "show_cat_name" and "target"

= 0.1.0 (2012-02-27) =

* Initial release

== Upgrade Notice ==

not available yet, will be added in a later release
