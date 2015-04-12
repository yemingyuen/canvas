=== Plugin Name ===
Contributors: happybooking
Donate link: http://www.happybooking.de/wordpress/plugins/extended-toc/donate
Tags: table of contents, indexes, toc, sitemap, cms, options, list, page listing, category listing
Requires at least: 3.0.1
Tested up to: 3.6.1
Stable tag: 0.9.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin automatically generates and inserts a table of contents (ToC) to your pages and posts, based on tags h1-h6. It can deal with nextpage-tag.

== Description ==

This plugin automatically generates and inserts a table of contents (ToC) to your pages and posts, based on tags h1-h6. Whenever the plugin discovers more than a certain amount of headings (default: 3) the ToC is inserted at the top of the page. This plugin also can handle posts that are divided into pages by the nextpage-wordpress-tag. Any feedback or suggestions are welcome.  

= Available Languages =
* English
* German

== Installation ==

The normal plugin install process applies, that is search for `Extended Table of Contents (with nextpage support)` from your plugin screen or via the manual method:

1. Upload the `extended-toc` folder into your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

That's it!

You can change the default settings and more under Plugins > Extended-ToC

== Screenshots ==

1. Table of Contents after a preamble
2. The options panel found under Plugins > Extended-ToC
3. Markup example within the post content

== Frequently Asked Questions ==

If you have any questions or suggestions please contact us at any time: support@happybooking.de or http://www.happybooking.de/

== Changelog ==
= 0.9.3 = 
* bugfix preg_split error

= 0.9.3 = 
* bugfix for headungs enumeration error

= 0.9.2 = 
* little bugfix within markup

= 0.9.1 = 
* little bugfix within markup

= 0.9.0 =
* Added markups [extoc] [noextoc]
* Inidivual settings within markup per post/page 
* Optimisation for SEO by removing "page=1" 
* No page reload when anchor is on the same page (Performance optimisation)
* Help Tab added with some examples

= 0.8.6 =
* Permalink bug not linking correctly to the first page of the ToC fixed

= 0.8.5 =
* Bug in display of the HTML content in wordpress 3.5.0 fixed

= 0.8.4 = 
* Added tolerance to wrong h1-h6 numeration/hierarchy 

= 0.8.3 = 
* Minor CSS style changes

= 0.8.2 = 
* Bug influence with SEO Plugin fixed / Bug numbering item list fixed

= 0.8.1 = 
* Switch on/off the numbering of the list items (headers)
* Anchor bugs fixed

= 0.8.0 =
* Override the ToC position by using the markup [extoc]
* Insert the ToC only in special pages/subpages/posts by using the markup [extoc] 
* Disable insertion in special pages/subpages/posts by using [noextoc]

= 0.7.1 =
* Small updates to the german translation file

= 0.7.0 =
* Show the header hierarchy within the ToC
* Further options for (min amount of headers, show/don't show header, show headers hierarchy)

= 0.6.4 =
* Released: 1 July 2013
* First release of the ToC supporting nextpage-tag

== Upgrade Notice ==

Update folder with the latest files.  All previous options will be saved.
