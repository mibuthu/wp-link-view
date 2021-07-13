﻿=== Link View ===
Contributors: mibuthu
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=4ZHXUPHG9SANY
Tags: link, links, blogroll, view, linkview, list, slider, slideshow, images, pictures, banner, integrated, page, category, categories, admin, setting, option, attribute, widget, sidebar, css, multi-column
Requires at least: 4.9
Tested up to: 5.6
Requires PHP: 5.6
Stable tag: 0.8.0
Plugin URI: https://wordpress.org/plugins/link-view
Licence: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display a link-list or link-slider in a post or page by using a shortcode.

== Description ==

The purpose of this plugin is to to show the WordPress integrated links in a list or a slider by using a shortcode or a widget.

= Current Features: =
* the shortcode [linkview] can be used to add the links in a post or page
* the widget "LinkView" can be used to add links in a sidebar
* the links can be displayed in a list or in a slider
* there are many options available to adjust the output of the links (see shortcode options in the "About LinkView" page)
* the image of the link can also be displayed
* categories and/or links can be displayed in multicolumn layout
* option to set additional CSS styles for the link-lists and link-sliders
* the required user roles to edit links can be adjusted in the settings page

= Development: =
If you want to follow the development status have a look at the [git-repository on github](https://github.com/mibuthu/wp-link-view "wp-link-view git-repository").
Feel free to add your merge requests there, if you want to help to improve the plugin.

= Translations: =
Please help translating this plugin into multiple languages.
You can submit your translations at [transifex.com](https://www.transifex.com/projects/p/wp-link-view "wp-link-view at transifex").
There the source strings will be kept in sync with the actual development version. And in each plugin release the available translation files will be updated.

== Installation ==

The easiest version of installing is to go to the admin page. There you can install new plugins in the menu **Plugins -> Add new**. Search for **Link View** and press **Install now**.

If you want to install the plugin manually download the zip-file and extract the files into your `wp-content/plugins` folder.


== Frequently Asked Questions ==

= Is it possible to use the shortcode in a widget? =
Yes, a widget especially for the use of the [linkview] shortcode is included in this plugin. Insert the widget LinkView in your sidebar and set all attributes you want to change in the appropriate field.

= Is it possible to add multiple slider on one site? =
Yes, since version 0.3.0 you can use as many sliders as you want on one site.

= Can I call the shortcode directly via php e.g. for my own template, theme or plugin? =
Yes, you can create an instance of the `SC_Linkview` class which is located in `php/sc_linkview.php` in the plugin folder and call the function `show_html($atts)`. With `$atts` you can specify all the shortcode attributes you require. Another possibility would be to call the WordPress function `do_shortcode()`.

== Screenshots ==

1. Admin about page
2. Available shortcode attributes - Part 1
3. Available shortcode attributes - Part 2
4. Admin settings page
5. Linkview Widget
6. Simple example page with a small link list

== Changelog ==

= 0.8.1 (2021-06-26) =
* raise minimum required PHP version to 7.0, to catch some fatal errors which used to be simple deprecation notices
* tested up to 5.7.2
* minor spelling changes (ex. writing *WordPress* according to Automattic's trademark)
* added Portuguese (European) translation

= 0.8.0 (2020-11-29) =
* raise minimum required PHP version to 5.6
* some internal code refactoring (namespaces, file structure, ...)
* added setting to add custom CSS classes
* added a shortcode attribute to add custom CSS classes
* added category CSS classes for each link
* change shortcode boolean attributes from `1` and `0` to `true` and `false` (numbers are still working)
* changed CSS class prefix from `lv` to `lvw`. Attention: If you use these class names in your CSS or the custom CSS option you have to update the CSS there!

= 0.7.3 (2020-04-19) =
* fixed warnings for PHP-versions < 7.0
* small improvement in `link_rel` handling
* prepare missing texts for translation and improve helptexts
* updated German translation

= 0.7.2 (2018-11-25) =
* complete code rewrite:
* switched to WordPress coding standard
* added comments for all files, classes and functions
* code check with `phpcs` and `phan`
* use shortcode class instances instead of singleton

= 0.7.1 (2017-08-13) =
* added shortcode attribute `show_num_links`
* changed default value for `cat_filter` from `all` to an empty string (`all` is deprecated now)
* splitted admin about page in 2 tabs (general and shortcode attributes)
* added information about translations
* updated masonry script to version 4.2
* prepare more strings for translation and added German translations for them
* Rise mimimum required WordPress version to 3.8
* moved screenshots to assets folder

= 0.7.0 (2017-01-20) =
* added multi-language-support (not all strings translatable yet)
* added German translation
* moved helptexts into separate file and only load them if required
* updated masonry script from version 3.2.2 to 4.1.1
* removed deprecated shortcode attributes `cat_name` and `target`
* changed link to renamed github-repository

Attention: Due to a change of a filename the plugin probably gets deactivated after upgrade! Please check the plugin settings and activate `link-view` again if required!

= 0.6.4 (2016-10-31) =
* added minified version of slider-script
* consolidate and improve multi-column support for categories and link-lists
* updated help texts for multi-column support
* some CSS changes for multi-column support
* security improvement for external links

Attention: This version includes some modifications in multi-column layout, which can break existing shortcodes! So if you already use the multi-column feature for categories and/or links please check the output of your link page after the update. If the layout is broken you can find help in the admin page: Links -> About LinkView.

= 0.6.3 (2016-04-20) =
* added shortcode attribute `link_rel`
* added shortcode attribute `link_item_img`
* renamed shortcode attribute `target` to `link_target`
* added shortcode attribute `target` and marked it as deprecated

Attention: The shortcode attribute `target` is deprecated since this version and will be removed in a future version. So please change your existing shortcodes to the new attribute name `link_target` !

= 0.6.2 (2015-11-09) =
* added advanced multi-column options for categories

= 0.6.1 (2015-02-07) =
* added shortcode attribute to set multiple columns for links
* added shortcode attribute `cat_filter` which replaces `cat_name`
* marked shortcode attribute `cat_name` as deprecated

= 0.6.0 (2014-10-17) =
* added shortcode attribute to set multiple columns for categories
* added wrapper `div` around full shortcode content

= 0.5.2 (2014-06-19) =
* added option to set required user role to manage links

= 0.5.1 (2014-04-27) =
* added option to set required capabilities to view LinkView-About page
* some small changes in option handling

= 0.5.0 (2014-03-31) =
* changed plugin dir structure
* some internal code changes
* splitted admin page in about and settings page
* some CSS improvements

= 0.4.4 (2013-11-11) =
* fixed required privilegs to show admin page and to edit CSS styles

= 0.4.3 (2013-09-01) =
* added shortcode attribute `num_links` to limit the number of displayed links
* changed shortcode attribute `link_order` to lowercase (using uppercase letters is still working)

= 0.4.2 (2013-06-29) =
* added info message after changing the CSS settings
* don't use name as link item default when a not available item was choosen
* added tooltips for widget options on admin page

= 0.4.1 (2013-02-16) =
* Added new attributes `list_orderby` and `list_order`
* Fixed an error in the slider javascript which causes problem in IE 6 and 7
* Reorganized some CSS styles
* Use ascending list ids instead of random number

= 0.4.0 (2012-12-26) =
* Internal code changes
* Changed admin page layout and help texts
* Splitted attributes table on admin page into different sections
* Added attributes `css_suffix` and `link_items`
* Added option `css for linkview`
* Fixed target in links
* Fixed html-code for defining image size

= 0.3.3 (2012-12-16) =
* Enable link manager (required for WordPress 3.5)
* Include WordPress 3.5 in version information

= 0.3.2 (2012-10-14) =
* Fixed queue of jQuery which is required for the slider (in the old version the bad inclusion can cause issues with themes or other plugins that uses jQuery)

= 0.3.1 (2012-07-28) =
* Fixed all php-warnings
* Added attribute `exclude_cat`
* Added help text for LinkView widget

= 0.3.0 (2012-07-01) =
* Added widget to show shortcode with all options in a sidebar
* It is possible to have multiple slider on one site now
* WordPress internal jQuery script is used, the plugins jQuery script is removed

= 0.2.5 (2012-06-17) =
* Renamed admin class to avoid conflicts with other plugins
* Added possibility to set multiple categories in attribute `cat_name`
* Changed sorting of categories to alphabetic order, if no `cat_name` is given

= 0.2.4 (2012-04-22) =
* Show link name and link description in tooltip text, when the mouse is over the link

= 0.2.3 (2012-04-14) =
* Added attributes `vertical_align` and `list_symbol`

= 0.2.2 (2012-03-25) =
* Added attributes `slider_pause` and `slider_speed`

= 0.2.1 (2012-03-18) =
* Fixed bug to show correct image size in image list

= 0.2.0 (2012-03-17) =
* Initial support to show the links in a slider
* Added attributes `view_type`, `slider_width`, `slider_height`

= 0.1.1 (2012-03-03) =
* Modified html-output of link list (use own function to render output for more flexibility)
* Added attributes `show_cat_name` and `target`

= 0.1.0 (2012-02-27) =
* Initial release
