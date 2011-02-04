<?php
// Copyright (c) 2010,2011 Arvind Shah
// WP Family Tree is released under the GNU General Public
// License (GPL) http://www.gnu.org/licenses/gpl.txt

require_once('wpft_options.php');

class tree {


	/* Load and return the entire tree from the database. */
	static function get_tree()
	{
	    global $wpdb;
	
		$family_posts = get_posts('category_name='.wpft_options::get_option('family_tree_category_key').'&numberposts=-1&orderby=title&order=asc');
	
		$the_family = array();	
	
		foreach($family_posts as $post_detail) {
			// print_r($post_detail);
			$the_family[$post_detail->ID] = node::get_node($post_detail);
		}
	
		// Set father/mother child relationships...
		foreach ($the_family as $fm) {
			if (isset($fm->father)) {
				$the_family[$fm->post_id]->name_father 	= $the_family[$fm->father]->name;
				$the_family[$fm->post_id]->url_father 	= $the_family[$fm->father]->url;
				$father = $the_family[$fm->father];
				$father->children[] = $fm->post_id;
			}
			if (isset($fm->mother)) {
				$the_family[$fm->post_id]->name_mother 	= $the_family[$fm->mother]->name;
				$the_family[$fm->post_id]->url_mother 	= $the_family[$fm->mother]->url;
				$mother = $the_family[$fm->mother];
				$mother->children[] = $fm->post_id;
			}
		}
	
		// Set sibling relationships...
		foreach ($the_family as $fm) {
			$siblings = array();	// Siblings are your fathers children + your mothers children but not you
			$siblings_f = array();
			$siblings_m = array();
			
			if (isset($fm->father)) {
				$father = $the_family[$fm->father];
				if (is_array($father->children)) {
					$siblings_f = $father->children; 
				}
			}
			if (isset($fm->mother)) {
				$mother = $the_family[$fm->mother];
				if (is_array($mother->children)) {
					$siblings_m = $mother->children; 
				}
			}
			$siblings = array_merge( $siblings_f, array_diff($siblings_m, $siblings_f));
			$temp = array();
			$temp[] = $fm->post_id;
			$fm->siblings = array_diff($siblings, $temp);
		}
	
		return $the_family;
	}	
	
	
}







?>