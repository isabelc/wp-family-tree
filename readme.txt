=== WP Family Tree ===
Contributors: Arvind Shah, Sunil Shah
Donate link: http://www.esscotti.com/wp-family-tree-plugin/
Tags: family tree, genealogy
Requires at least: 2.8.6
Tested up to: 3.0.0
Stable tag: trunk

WP Family Tree is a plugin for Wordpress that renders family trees on any Page or Post.

== Description ==

WP Family Tree is a family tree generator plugin for Wordpress. Each family member have their own post within 
a "Family" category. In the family member posts you can specify birth dates, mother, father, etc. A complete family 
tree can be displayed on any page with the [family-tree] shortcode. 

The root of the tree can be specified with the root parameter: [family-tree root='John Doe']

For support please visit the [WordPress Family Tree Plugin](http://www.esscotti.com/wp-family-tree-plugin/ "Wordpress Family Tree Plugin") homepage

Released under the terms of the GNU GPL, version 3.

Copyright (c) Arvind Shah

Any suggestions for future enhancements welcome.

== Installation ==

1. Copy the family tree plugin directory to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create a page and put a [family-tree] on it
4. Add posts specifying the custom fields available at the bottom of edit posts page, adding the posts to the "Family"
category.

== Screenshots ==

1. Family tree as drawn on a page using the short code [family-tree]
2. Here are the custom fields that are added to the posts
3. Family list entry with links to each individual's blog post page

== Changelog ==

= 0.3 =
* Added tree style parameters in admin optins page
* Added family tree rendering using the Raphael library (Sunil Shah contributed the tree rendering script)
* Fixed problem selecting correct category for family members (Thanks to Cyril Pauya for identifying the problem and 
providing a solution)

= 0.2 =
* Family members list now shown in a table

= 0.1 =
* First version
* Added custom fields on edit posts page: gender, father, mother, born, died
* Added ability to display list of family members and their relations by adding the {FAMILY-MEMBERS} tag to pages
* Added Admin page and ability to change the name of the "Family" category


== Roadmap ==

- GEDCOM support
- Family tree in printable form(PDF?)
- Please suggest.. :)

