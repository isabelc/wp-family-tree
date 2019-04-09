Modified version of WP Family Tree
==================================

**Tested up to WordPress 5.1.1**

This is a modified version of [WP Family Tree plugin](https://wordpress.org/plugins/wp-family-tree/) (version 1.0.5) by the_arv.

These are the changes from the original plugin:

* New - The family list is now in alphabetical order by first name.

* New - Show the spouse with the bio data for each member.

* New - In addition to spouse, show "Partners" (if any) with the bio data. Partners are people who the person has common children with.

* New - Added schema.org microdata for person to the single family member pages, and on the family members directory list page. Person properties include name, birth date, death date, spouse, parent, children, sibling, and image.

* Fix: Family tree post meta was not able to be erased.

* Tweak - Added spaces between names and dates in tables for better description snippets in search results. Otherwise, without these spaces, if you use the excerpt for the description meta tag, the [names and dates run together](https://isabelcastillo.com/add-spaces-wp-family-tree).

* Fix - Removed PHP error notice for deprecated use of User Levels instead of capabilites.

* Fix - Removed several other PHP warnings.
