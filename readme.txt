=== WP Family Tree ===
Contributors: the_arv, isabel104
Donate link: http://www.wpfamilytree.com/
Tags: family tree, genealogy, pedigree
Requires at least: 3.7
Tested up to: 3.9.1
Stable tag: 1.0.6mod-rc-1

WP Family Tree is a graphical family tree generator plugin for Wordpress. Each family member have their own blog post.

== Description ==

WP Family Tree is a family tree generator plugin for Wordpress. Each family member have their own post within 
a "Family" category. In the family member posts you can specify birth dates, mother, father, etc. A complete family 
tree can be displayed on any page with the [family-tree] shortcode. 

The root of the tree can be specified with the root parameter: [family-tree root='John Doe']

Specify a 'featured image' for the post and the image thumbnail will be used in the family memebers list and in 
the family tree. Please see the screenshots.

For support please visit the 
[WordPress Family Tree Plugin](http://www.wpfamilytree.com/ "Wordpress Family Tree Plugin") homepage

Released under the terms of the GNU GPL, version 3.

Copyright (c) 2013 Arvind Shah

Any suggestions for future enhancements welcome.

== Installation ==

1. Copy the family tree plugin directory to the '/wp-content/plugins/' directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Create a page and put a [family-tree] shortcode on it; (this is the family tree page).
4. Create a page and put a [family-members] shortcode on it; (this is the family list page).
5. Visit the family tree admin panel and set the family tree link option; (Link to page with family tree).
6. Add a post category with the name "Family"
7. For each family member, add a post specifying the custom fields available at the bottom of edit posts page, 
making the posts belong to the "Family" category.

Note: You can put the post name/title of the person you wish to be displayed at the root of the family tree as a 
parameter in the shortcode: [family-tree root='&lt;post title&gt;']

== Screenshots ==

1. Family tree as drawn on a page using the short code [family-tree]. Thumbnail shown where mouse is hovering.
2. Here are the custom fields that are added to the posts
3. Family list entry with links to each individual's blog post page
4. Family tree with other styling options and showing spouses

== Changelog ==

= 1.0.6mod-rc-1 =
* New - Added schema.org microdata for person to the single family member pages, and on the family members directory list page. Person properties include name, birth date, death date, parent, children, sibling, and image.
* Tweak - Added spaces between names and dates in tables for better description snippets in search results.

= 1.0.5 =

* bug fix: change filename of familytree.js.php

= 1.0.4 =

* bug fix: added jquery to list of included scripts

= 1.0.3 =

* made it so the family tree is centered around the ancestor on loading the page

= 1.0.2 =

* minor bug fix (relating to rendering biodata on family member posts)

= 1.0.1 =

* modified thumbnail css
* tested for recent versions of Wordpress

= 1.0 =

* Added icon to show if a person has a featured image attached.
* Fixed a couple of bugs

= 0.14 =

* Fixed bug relating to ordering of siblings.

= 0.13 =

* Fixed issue which caused problems with older versions of PHP.

= 0.12 =

* Siblings are now ordered by age left to right

= 0.11 =

* Fixed bugs in javascript files

= 0.10 =

* Added print view css to enable printing of larger family trees
* Replaced scroll bars for larger trees with click + drag functionality
* Added option to set height of generations

= 0.9.1 =

* Fixed a bug that made some boxes contain '?'

= 0.9 =

* Added ability to specify spouse manually (if there are no children) 
* Ability to specify the primary/current spouse if there are multiple spouses

= 0.8 =

* Added styling options - minimum node width, and node corner radius
* Improved toolbar placement
* Fixed "Headers already sent" issue / conflict with other plugins
* Added option to show/hide biodata info on posts pages

= 0.7 =

* Added support for showing multiple spouses
* Added a few configuration options such as Show/hide gender, Show/hide living dates, Conceal birth date for
those still alive, Diagonal lines, Wrap names

= 0.6.3 =

* Bug fix: There was a bug in the previous version that made family members show up as undefined, this is now fixed..

= 0.6.2 =

* Bug fix: Plugin now shows the correct posts on the {FAMILY-MEMBERS} page

= 0.6.1 =

* Bug fix

= 0.6 =

* Added navigation toolbar for nodes
* Bug fix: Family tree list now only shows posts that belong to the family category

= 0.5 =

* Added so the tree shows partner/spouse.

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
