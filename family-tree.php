<?php
/**
 * @package WP Family Tree
 * @author Arvind Shah
 * @version 0.2
 */
/*
Plugin Name: WP Family Tree
Plugin URI: http://www.esscotti.com/wp-family-tree-plugin/
Description: Family Tree plugin
Author: Arvind Shah
Version: 0.2
Author URI: http://www.esscotti.com/
*/


class family_member {
	var $post_id;
	var $gender;
	var $father;
	var $mother;
	var $born;
	var $died;

	var $children;
	var $siblings;
	
	var $name;
	var $name_father;
	var $name_mother;
	var $url;
	var $url_father;
	var $url_mother;

	function __construct() {
		$children = array();
	}		
	function get_html($the_family) {

		$html = '<table border="0" width="100%">';
		$html .= '<tr><td width="150"><b><a href="'.$this->url.'">'.$this->name.'</a></b></td>';
		$html .= '<td width="80">';
		$html .= ($this->gender == 'm') ? 'Male' : 'Female';
		$html .= '</td>';
		$html .= '<td>Born: '.$this->born.'</td>';
		if (!empty($this->died) && strlen($this->died) > 1) {
			$html .= '<td>Died: '.	$this->died.'</td>';	
		} else {
			$html .= '<td></td>';	
		}
		$html .= '</tr>';
		$html .= '<tr><td colspan="2">Father: ';
		if (isset($this->name_father)) {
			$html .= '<a href="'.$this->url_father.'">'.$this->name_father.'</a>';
		} else {
			$html .= 'Unspecified';
		}
		$html .= '</td>';	
		$html .= '<td colspan="2">Mother: ';
		if (isset($this->name_mother)) {
			$html .= '<a href="'.$this->url_mother.'">'.$this->name_mother.'</a>';
		} else {
			$html .= 'Unspecified';
		}
		$html .= '</td></tr>';
		$html .= '<tr><td colspan="4">Children: ';
		if (count($this->children) > 0) {
			$first = true; 
			foreach ($this->children as $child) {
				if (!$first) {
					$html .= ', ';
				} else {
					$first = false;
				}
				$html .= '<a href="'.$the_family[$child]->url.'">'.$the_family[$child]->name.'</a>';
			}
		} else {
			$html .= 'none';
		}
		$html .= '</td></tr>';
		$html .= '<tr><td colspan="4">Siblings: ';
		if (count($this->siblings) > 0) {
			$first = true; 
			foreach ($this->siblings as $sibling) {
				if (!$first) {
					$html .= ', ';
				} else {
					$first = false;
				}
				$html .= '<a href="'.$the_family[$sibling]->url.'">'.$the_family[$sibling]->name.'</a>';
			}
		} else {
			$html .= 'none';
		}
		$html .= '</td></tr>';
		$html .= '</table>';
		return $html;
	}
	function get_html_old($the_family) {
		$html = '';
		$html .= '<p><a href="'.$this->url.'">'.$this->name.'</a></p>';
		$html .= '<p>Born: '.$this->born.'</p>';	
		$html .= '<p>Gender: ';
		$html .= ($this->gender == 'm') ? 'Male' : 'Female';
		$html .= '</p>';
		$html .= '<p>Father: ';
		if (isset($this->name_father)) {
			$html .= '<a href="'.$this->url_father.'">'.$this->name_father.'</a>';
		} else {
			$html .= 'Unspecified';
		}
		$html .= '</p>';	
		$html .= '<p>Mother: ';
		if (isset($this->name_mother)) {
			$html .= '<a href="'.$this->url_mother.'">'.$this->name_mother.'</a>';
		} else {
			$html .= 'Unspecified';
		}
		$html .= '</p>';
		if (!empty($this->died) && strlen($this->died) > 1) {
			$html .= '<p>Died: '.	$this->died.'</p>';	
		}
//		$html .= '<p>ID: '.		$this->post_id.'</p>';	
		$html .= '<p>Children: ';
		if (count($this->children) > 0) {
			foreach ($this->children as $child) {
				$html .= '<a href="'.$the_family[$child]->url.'">'.$the_family[$child]->name.'</a> ';
			}
		} else {
			$html .= 'none';
		}
		$html .= '<p>Siblings: ';
		if (count($this->siblings) > 0) {
			foreach ($this->siblings as $sibling) {
				$html .= '<a href="'.$the_family[$sibling]->url.'">'.$the_family[$sibling]->name.'</a> ';
			}
		} else {
			$html .= 'none';
		}
		$html .= '</p>';
		return $html;
	}
	function get_box_html($the_family) {
		$html = '';
		$html .= '<a href="'.$this->url.'">'.$this->name.'</a>';
		$html .= '<br>Born: '.$this->born;
		if (!empty($this->died) && strlen($this->died) > 1) {
			$html .= '<br>Died: '.	$this->died;	
		}
		
		return $html;
		
	}
}

function get_the_family()
{
    global $wpdb;

	$family_posts = get_posts('category_name='.get_option("family_tree_category_key").'&numberposts=-1&orderby=title&order=asc');

	$the_family = array();	

	foreach($family_posts as $post_detail) {

		// print_r($post_detail);
		
		$fm = new family_member();

		$fm->post_id 	= $post_detail->ID;
		$fm->name 		= $post_detail->post_title;
		$fm->url		= get_permalink($post_detail->ID);
		
		$fm->gender	= get_post_meta($post_detail->ID, 'gender', true);
		$fm->father	= get_post_meta($post_detail->ID, 'father', true);
		$fm->mother	= get_post_meta($post_detail->ID, 'mother', true);
		$fm->born	= get_post_meta($post_detail->ID, 'born', true);
		$fm->died	= get_post_meta($post_detail->ID, 'died', true);
			
		$the_family[$post_detail->ID] = $fm;
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

function family_list() {
	
	$the_family = get_the_family();
	
	$html = "";
	// Print information about each family member...
	foreach ($the_family as $fm) {
		$html .= $fm->get_html($the_family);
		$html .= '<hr>';
	}
	return $html;
}

function family_tree() {

	$the_family = get_the_family();
	$member = 3;
?>

<div style="position:relative;width:450px;height:500px;border:0px solid blue;background:#eee;">
<?php
	$fm = $the_family[3];
	
	echo '<div style="position:absolute;top:0px;left:0px;border:1px dashed black;padding:5px">';
	echo $fm->get_box_html($the_family);
	echo '</div>';	

	$father = $fm->father;
	echo '<div style="position:absolute;top:0px;left:200px;border:1px solid black;padding:5px">';
	if (!empty($father)) {
		echo $the_family[$father]->get_box_html($the_family);
	} else {
		echo '???';
	}
	echo '</div>';	

	$mother = $fm->mother;
	echo '<div style="position:absolute;top:60px;left:200px;border:1px solid black;padding:5px">';
	if (!empty($mother)) {
		echo $the_family[$mother]->get_box_html($the_family);
	} else {
		echo '???';
	}
	echo '</div>';	



?>	
</div>
	


	
<?php
}

function family_tree_edit_page_form()
{
    global $post;

    ?>
    <div id="ftdiv" class="postbox">
    <h3>Family tree info (optional)</h3>
    <div class="inside">

	<table>
<?php

	$family 	= get_posts('category_name='.get_option("family_tree_category_key").'&numberposts=-1&orderby=title&order=asc');
	$males 		= array();
	$females 	= array();
	foreach ($family as $f) {
		if ($f->ID != $post->ID) {
			$postgender = get_post_meta($f->ID, 'gender', true);
			if ($postgender == "m") {
				$males[] = $f;
			} else if ($postgender = "f") {
				$females[] = $f;
			} else {
				$males[] = $f;
				$females[] = $f;
			}
		}
	}

	$gender = get_post_meta($post->ID, 'gender', true);
	$mother = get_post_meta($post->ID, 'mother', true);
	$father = get_post_meta($post->ID, 'father', true);
?>
	<tr><td>Gender:</td><td> 
    <select name="gender" id="gender">
    <option value="" <?php if (empty($gender)) echo "selected=\"selected\""; ?>></option>
    <option value="m" <?php if ($gender == "m") echo "selected=\"selected\""; ?>>Male</option>
    <option value="f" <?php if ($gender == "f") echo "selected=\"selected\""; ?>>Female</option>
	</select></td></tr>
    <tr><td>Born (YYYY-MM-DD):</td><td><input type="text" name="born" value="<?php echo wp_specialchars(get_post_meta($post->ID, 'born', true), true) ?>" id="born" size="80" /></td></tr>
    <tr><td>Died (YYYY-MM-DD):</td><td><input type="text" name="died" value="<?php echo wp_specialchars(get_post_meta($post->ID, 'died', true), true) ?>" id="died" size="80" /></td></tr>
    <tr><td>Mother:</td><td>
    <select style="width:200px" name="mother" id="mother">
    <option value="" <?php if (empty($mother)) echo "selected=\"selected\""; ?>> </option>
<?php
	foreach ($females as $f) {
		echo '<option value="'.$f->ID.'" ';
		if ($f->ID == $mother) echo "selected=\"selected\"";
		echo '>'.$f->post_title.'</option>';
	}
?>
	</select>
	</td></tr>

    <tr><td>Father:</td><td>
    <select style="width:200px" name="father" id="father">
    <option value="" <?php if (empty($father)) echo "selected=\"selected\""; ?>> </option>
<?php
	foreach ($males as $f) {
		echo '<option value="'.$f->ID.'" ';
		if ($f->ID == $father) echo "selected=\"selected\"";
		echo '>'.$f->post_title.'</option>';
	}
?>
	</select>
	</td></tr>

    </table>
    </div>
    </div>
    <?php
}

function family_tree_update_post($id)
{
    $born   = stripslashes(strip_tags($_POST['born']));
    $died   = stripslashes(strip_tags($_POST['died']));
    $mother = stripslashes(strip_tags($_POST['mother']));
    $father = stripslashes(strip_tags($_POST['father']));
    $gender = stripslashes(strip_tags($_POST['gender']));

    if (!empty($born)) { delete_post_meta($id, 'born'); 	add_post_meta($id, 'born', $born); 		} //else { add_post_meta($id, 'born', $born); 		}
    if (!empty($died)) { delete_post_meta($id, 'died'); 	add_post_meta($id, 'died', $died); 		} //else { add_post_meta($id, 'died', $died); 		}
    if (!empty($mother)) { delete_post_meta($id, 'mother'); add_post_meta($id, 'mother', $mother); 	} //else ( add_post_meta($id, 'mother', $mother); 	}
    if (!empty($father)) { delete_post_meta($id, 'father'); add_post_meta($id, 'father', $father); 	} //else { add_post_meta($id, 'father', $father); 	}
    if (!empty($gender)) { delete_post_meta($id, 'gender'); add_post_meta($id, 'gender', $gender); 	} //else { add_post_meta($id, 'gender', $gender); 	}
}


// Function to deal with showing the family tree on pages
function family_list_insert($content)
{
  if (preg_match('{FAMILY-MEMBERS}',$content))
    {
      $ft_output = family_list();
      $content = str_replace('{FAMILY-MEMBERS}', $ft_output, $content);
    }
  return $content;
}
// Function to deal with showing the family tree on pages
function family_tree_insert($content)
{
  if (preg_match('{FAMILY-TREE}',$content))
    {
      $ft_output = family_tree	();
      $content = str_replace('{FAMILY-TREE}', $ft_output, $content);
    }
  return $content;
}


function family_tree_options_page()
{
    if (function_exists('add_options_page')) {
        add_options_page('WP Family Tree', 'WP Family Tree', 10, 'wp-family-tree', 'family_tree_options_subpanel');
    }
}

function family_tree_options_subpanel()
{
    global $wp_version;

    if (isset($_POST['info_update'])) {
        if ( function_exists('check_admin_referer') ) {
            check_admin_referer('family-tree-action_options');
        }

        if ($_POST['family_tree_category_key'] != "")  {
            update_option('family_tree_category_key', stripslashes(strip_tags($_POST['family_tree_category_key'])));
        }

        echo '<div class="updated"><p>Options saved.</p></div>';
    }

    if (get_option("family_tree_category_key")) {
        $family_tree_category_key          = get_option("family_tree_category_key");
    } else {
        $family_tree_category_key          = "Family";
    };
 ?>

    <div class="wrap">
    <h2>WP Family Tree Options</h2>
    <a href="http://www.esscotti.com/wp-family-tree-plugin/"><img width="150" height="50" alt="Visit WP Family Tree home" align="right" src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/wp-family-tree/logo.jpg"/></a>
    <form name="ft_main" method="post">
    <?php
    if (function_exists('wp_nonce_field')) {
        wp_nonce_field('family-tree-action_options');
    }
    ?>
    <table class="form-table">
    <tr valign="top">
    <th scope="row"><label for="family_tree_category_key">Name of category for family members (default: "Family")</label></th>
    <td><input name="family_tree_category_key" type="text" id="family_tree_category_key" value="<?php echo $family_tree_category_key; ?>" size="40" /></td>
    </tr>
    </table>


    <p class="submit">
    <input type="hidden" name="action" value="update" />
<!--
    <input type="hidden" name="page_options" value="family_tree_category_key"/>
-->
    <input type="submit" name="info_update" class="button" value="<?php _e('Save Changes', 'Localization name') ?> &raquo;" />
    </p>

    </form>
    </div>
<?php
}




// Enable the ability for the family tree to be loaded from pages
add_filter('the_content','family_list_insert');
add_filter('the_content','family_tree_insert');

add_action('edit_post', 'family_tree_update_post');
add_action('save_post', 'family_tree_update_post');
add_action('publish_post', 'family_tree_update_post');

add_action('edit_page_form', 'family_tree_edit_page_form');
add_action('edit_form_advanced', 'family_tree_edit_page_form');
add_action('simple_edit_form', 'family_tree_edit_page_form');

add_action('admin_menu', 'family_tree_options_page');

?>