<?php
/**
 * @package WP Family Tree
 * @author Arvind Shah
 * @version 0.5
 */
/*
Plugin Name: WP Family Tree
Plugin URI: http://www.esscotti.com/wp-family-tree-plugin/
Description: Family Tree plugin
Author: Arvind Shah
Version: 0.5
Author URI: http://www.esscotti.com/

Copyright (c) 2010,2011 Arvind Shah
WP Family Tree is released under the GNU General Public
License (GPL) http://www.gnu.org/licenses/gpl.txt

*/


require_once('wpft_options.php');
require_once('class.node.php');
require_once('class.tree.php');



/* Render a list of nodes. */
function family_list() {
	
	$the_family = tree::get_tree();
	
	$html = "";
	// Print information about each family member...
	foreach ($the_family as $fm) {
		$html .= $fm->get_html($the_family);
		$html .= '<hr>';
	}
	return $html;
}

/* Render the tree. */
function family_tree($root='') {
	$the_family = tree::get_tree();

	$out = '';
//	$out .= '<div id="buttons">';
//	$out .= '		<button id="wrap" 	type="button" onclick="setOneNamePerLine(!getOneNamePerLine());">Wrap Names</button>';
//	$out .= '		<button id="one" 	type="button" onclick="setOnlyFirstName(!getOnlyFirstName());">First Name</button>';
//	$out .= '		<button id="dates"	type="button" onclick="setBirthAndDeathDates(!getBirthAndDeathDates());">Dates</button>';
//	$out .= '		<button id="conceal" type="button" onclick="setConcealLivingDates(!getConcealLivingDates());">Conceal living dates</button>';
//	$out .= '		<button id="diag" 	type="button" onclick="setDiagonalConnections(!getDiagonalConnections());">Diagonal Lines</button>';
//	$out .= '		<button id="gender" type="button" onclick="setGender(!getGender());">Show Gender</button>';
//	$out .= '		<button id="maiden" type="button" onclick="setMaidenName(!getMaidenName());">Prev. Names</button>';
//	$out .= '		<button id="spouse" type="button" onclick="setSpouse(!getSpouse());">Show Spouse</button>';
//	$out .= '</div>';
	$out .= '<div>';

	$ancestor = $_GET['ancestor'];
	if (empty($ancestor)) {
		if (!empty($root)) {
			$focuspers = $root;
		} else {
			$node = reset($the_family);
			$focuspers = ($node!==false)?$node->name:'kjk';
		}
	} else {
		$focuspers = $ancestor;
	}

	$out .= '<input type="hidden" onkeyup="onFocusPersonChanged();" size="30" name="focusperson" id="focusperson" value="'.$focuspers.'">';
	$out .= '<button id="go" 	type="button" onclick="redrawTree();">Redraw</button>';
	$out .= '</div>';
	$out .= '<div style="width:630px;border:0px solid black;overflow:auto">';
	$out .= '	<div id="familytree"> </div>';
	$out .= '	<img name="hoverimage" id="hoverimage" style="visibility:hidden;">';
	$out .= '</div>';

	return $out;
}
function bio_data() {
	global $post;
	
//	echo '<p>This is the family member with id: '.$post->ID.'</p>';
	echo '<p><a href="/family-tree/?ancestor='.$post->post_title.'">click here to view family tree</a>';
	
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

	$family 	= get_posts('category_name='.wpft_options::get_option('family_tree_category_key').'&numberposts=-1&orderby=title&order=asc');
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
function family_list_insert($content) {
	if (preg_match('{FAMILY-MEMBERS}',$content)) {
		$ft_output = family_list();
		$content = str_replace('{FAMILY-MEMBERS}', $ft_output, $content);
	}
	return $content;
}
// Function to deal with showing the family tree on pages
function family_tree_insert($content) {
	if (preg_match('{FAMILY-TREE}',$content)) {
		$ft_output = family_tree();
		$content = str_replace('{FAMILY-TREE}', $ft_output, $content);
	}
	return $content;
}
// Function to deal with showing biodata on posts
function bio_data_insert($content) {
	global $post;
	$category = wpft_options::get_option('family_tree_category_key');
	$cats = get_the_category();
	foreach ($cats as $cat) {
		if ($cat->name == $category) {
			// This post is a family member post so do the work...
			$the_family = tree::get_tree();
			$html = $the_family[$post->ID]->get_html($the_family);
			$content = $html.$content;
			break;	
		}	
	}
	return $content;
}

function wpft_family_tree_shortcode($atts, $content=NULL) {
	$root = $atts['root'];
	$ft_output = family_tree($root);

//	$content = do_shortcode($content);
	
	return $ft_output;
}

add_shortcode('family-tree', 'wpft_family_tree_shortcode');


add_action('admin_menu', 'family_tree_options_page');

function addHeaderCode() {
	$plugloc = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	wp_enqueue_script('raphael', $plugloc.'raphael.js');
	wp_enqueue_script('familytree', $plugloc.'familytree.js.php');
	wp_enqueue_script('onload', $plugloc.'onload.js');
	wp_enqueue_style('ft-style', $plugloc.'styles.css');
}
function addFooterCode() {

}

// Enable the ability for the family tree to be loaded from pages
add_filter('the_content','family_list_insert');
add_filter('the_content','family_tree_insert');
add_filter('the_content','bio_data_insert');

add_action('init', 'addHeaderCode');
add_action('edit_post', 'family_tree_update_post');
add_action('save_post', 'family_tree_update_post');
add_action('publish_post', 'family_tree_update_post');

add_action('edit_page_form', 'family_tree_edit_page_form');
add_action('edit_form_advanced', 'family_tree_edit_page_form');
add_action('simple_edit_form', 'family_tree_edit_page_form');

?>