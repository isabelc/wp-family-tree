=== WP Family Tree ===
Contributors: the_arv
Donate link: http://www.esscotti.com/wp-family-tree-plugin/
Tags: family tree, genealogy
Requires at least: 2.8.6
Tested up to: 2.8.6
Stable tag: trunk

WP Family Tree is a simple family tree generator plugin for Wordpress. Each family member has its own post within 
a "Family" category. In the family member posts you can specify birth dates, mother, father, etc. A complete family 
tree is then displayed on any page with the {FAMILY-MEMBERS} tag. 

In this version 0.1 the family tree is simply a list of family memebrs with lnks to parents, childrens and siblings. 
In future versions the family tree will be fancier.


== Description ==

This is a simple family tree generator plugin for Wordpress. Each family member is defined by a post within a certain 
category. In the family member posts you can specify birth dates, mother, father, etc. A complete family tree
is then displayed on any page with the {FAMILY-MEMBERS} tag. 

In this version 0.1 the family tree is simply a list of family memebrs with lnks to parents, childrens and siblings. 
In future versions the family tree will be fancier.

For support and an example installation please visit the 
[WordPress Family Tree Plugin](http://www.esscotti.com/wp-family-tree-plugin/ "Wordpress Family Tree Plugin") homepage

Any suggestions for future enhancements welcome.

== Installation ==

1. Copy the family tree plugin directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create a page and put a {FAMILY-MEMBERS} on it
4. Add posts specifying the custom fields available at the bottom of edit posts page

== Screenshots ==

1. Here are the custom fields that are added to the posts
2. Family list entry with links to each individual's blog post page
3. My Family page that displays the family tree

== Changelog ==

= 0.1 =
* First version
* Added custom fields on edit posts page: gender, father, mother, born, died
* Added ability to display list of family members and their relations by adding the {FAMILY-MEMBERS} tag to pages
* Added an admin page where one can specify the name of the category that holds family memebers


== Roadmap ==

- GEDCOM support
- Family tree as graphical output (PNG, PDF, or both)

