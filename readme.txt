=== WP Family Tree ===
Contributors: the_arv
Donate link: http://www.esscotti.com/wp-family-tree-plugin/
Tags: family tree, genealogy
Requires at least: 2.8.6
Tested up to: 3.0.0
Stable tag: trunk

WP Family Tree is a graphical family tree generator plugin for Wordpress. Each family member have their own blog post.

== Description ==

WP Family Tree is a family tree generator plugin for Wordpress. Each family member have their own post within 
a "Family" category. In the family member posts you can specify birth dates, mother, father, etc. A complete family 
tree can be displayed on any page with the [family-tree] shortcode. 

The root of the tree can be specified with the root parameter: [family-tree root='John Doe']

Specify a 'featured image' for the post and the image thumbnail will be used in the family memebers list and in 
the family tree. Please see the screenshots.

For support please visit the 
[WordPress Family Tree Plugin](http://www.esscotti.com/wp-family-tree-plugin/ "Wordpress Family Tree Plugin") homepage

Released under the terms of the GNU GPL, version 3.

Copyright (c) Arvind Shah

Any suggestions for future enhancements welcome.

NOTE: All releases of this plugin up to V1.0 are considered unfinished or incomplete. 
The plugin renders a family tree and serves to gather feedback but expect that there will be some features missing.

== Installation ==

1. Copy the family tree plugin directory to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Create a page and put a [family-tree] shortcode on it; (this is the family tree page).
4. Create a page and put a {FAMILY-MEMBERS} tag on it; (this is the family list page).
5. Visit the family tree admin panel and set the family tree link option; (link to family tree page).
6. Add posts specifying the custom fields available at the bottom of edit posts page, 
adding the posts to the "Family" category.

Note: You can put the post name/title of the person you wish to be displayed at the root of the family tree as a 
parameter in the shortcode: [family-tree root='&lt;post title&gt;']

== Screenshots ==

1. Family tree as drawn on a page using the short code [family-tree]. Thumbnail shown where mouse is hovering.
2. Here are the custom fields that are added to the posts
3. Family list entry with links to each individual's blog post page

== Changelog ==

= 0.4.2 =

* Bug fix: renamed the function addheadercode() as it could cause conflicts with other plugins.

= 0.4.1 =

* Bug fix: added check for the function get_post_thumbnail_id

= 0.4 =
* Added the capability to show thumbnails in the family tree on hover.

= 0.3.1 =
* Added parameter in admin that needs to be specified and that tells WP Family Tree which page is 
the family tree page

= 0.3 =
* Added tree style parameters in admin optins page
* Added family tree rendering using the Raphael library (Sunil Shah contributed the tree rendering script)
* Fixed problem selecting correct category for family members (Thanks to Cyril Pauya for identifying 
the problem and providing a solution)

= 0.2 =
* Family members list now shown in a table

= 0.1 =
* First version
* Added custom fields on edit posts page: gender, father, mother, born, died
* Added ability to display list of family members and their relations by adding the {FAMILY-MEMBERS} 
tag to pages
* Added Admin page and ability to change the name of the "Family" category


== Roadmap ==

- GEDCOM import/export
- Family tree in printable form(PDF?)
- Please suggest.. :)

