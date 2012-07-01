=== Link View ===
Contributors: mibuthu
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=W54LNZMWF9KW2
Tags: link, links, blogroll, view, linkview, list, slider, slideshow, images, pictures, banner, integrated, page, category, categories, admin, attribute, widget, sidebar
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 0.3.0
Licence: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display a link-list or link-slider in a post or page by using a shortcode.


== Description ==

The purpose of this plugin is to to show the wordpress integrated links in a list or a slider by using a shortcode.
It is also possible to include the images. Use the shortcode [linkview] to add the Links/Blogroll in your site.
A detailed description of all available shortcode-attributes to modify the output can be found on the admin page under Links -> Link View.
There is also a widget available where all attributes of the linkview shortcode can be set.

If you want to follow the development status have a look at the [git-repository on github](https://github.com/mibuthu/wp-linkview "wp-linkview git-repository").


== Installation ==

The easiest version of installing is to go to the admin page. There you can install new plugins in the menu Plugins -> Add new. Search for "Link View" and press "Install now".

If you want to install the plugin manually download the zip-file and extract the files in your wp-content/plugins folder.


== Frequently Asked Questions ==

= Is it possible to use the shortcode in a widget? =

Yes, a widget especially for the use of the [linkview] shortcode is included in this plugin. Insert the widget LinkView in your sidebar and set all attributes you want to change in the appropriate field.

= Is it possible to add multiple slider on one site? =

Yes, since version 0.3.0 you can use as much sliders as you want on one site.


== Screenshots ==

1. Admin page with a description of the usage and the available attributes (plugin version 0.2.3)
2. Simple example page with a link list


== Changelog ==

= 0.3.0 (2012-07-01) =

* Added widget to show shortcode with all options in a sidebar
* It is possible to have multiple slider on one site now
* Wordpress internal jquery script is used, the plugins jquery script is removed

= 0.2.5 (2012-06-17) =

* Renamed admin class to avoid conflicts with other plugins
* Added possibility to set multiple categories in attribute "cat_name"
* Changed sorting of categories to alphabetic order, if no cat_name is given

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
