<?php
/*
Plugin Name: WP Family Tree
Plugin URI: http://www.wpfamilytree.com/
Description: Family Tree plugin
Author: Arvind Shah
Version: 1.0.6-mod-4
Author URI: http://www.esscotti.com/

Copyright (c) 2010 - 2013 Arvind Shah
WP Family Tree is released under the GNU General Public
License (GPL) http://www.gnu.org/licenses/gpl.txt

* @contributor Isabel Castillo
* Modified by Isabel Castillo
*/
require_once('wpft_options.php');
require_once('class.node.php');
require_once('class.tree.php');

/* Render a list of nodes. */
function family_list() {
	
	wpft_options::check_options();

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
	$ancestor = '';
	
	if (!empty($_GET['ancestor'])) {
		$ancestor = $_GET['ancestor'];
	} else { 
		if (!empty($root)) {
			$ancestor = $root;
		} else {
			$node = reset($the_family);
			$ancestor = ($node!==false)?$node->post_id:'-1';
		}
	}

	if (!is_numeric($ancestor)) {
		// find post by post title and assigns the post id to ancestor
		$ancestor = tree::get_id_by_name($ancestor, $the_family);
	}

	$render_from_parent = true;
	if ($render_from_parent) {
		$node = tree::get_node_by_id($ancestor, $the_family);	
		if (!empty($node->father)) {
			$ancestor = $node->father;
		} else if (!empty($node->mother)) {
			$ancestor = $node->mother;
		}
	}
	
	$out .= "<script type='text/javascript'>";	

	// Generate javascript tree text...
	$tree_data_js = "var tree_txt = new Array(\n";	
	$the_family = tree::get_tree();
	$first = true;
	foreach ($the_family as $node) {
		if (!$first) {
			$tree_data_js .= ','."\n";
		} else {
			$first = false;
		}
		$str  = '"EsscottiFTID='.$node->post_id.'",'."\n";
		$str .= '"Name='.addslashes($node->name).'",'."\n";
		if ($node->gender=='m') {
			$str .= '"Male",'."\n";
		} else if ($node->gender=='f') {
			$str .= '"Female",'."\n";
		}
		$str .= '"Birthday='.$node->born.'",'."\n";
		if (!empty($node->died) && $node->died != '-') {
			$str .= '"Deathday='.$node->died.'",'."\n";
		}

		if (isset($node->partners) && is_array($node->partners)) {
			foreach ($node->partners as $partner) {
				if (is_numeric($partner)) {
					$str .= '"Spouse='.$the_family[$partner]->post_id.'",'."\n";
				}
			}
		}

		$str .= '"Toolbar=toolbar'.$node->post_id.'",'."\n";
		$str .= '"Thumbnaildiv=thumbnail'.$node->post_id.'",'."\n";

		$str .= '"Parent=';
		if ( isset($the_family[$node->mother]) ) {
			$str .= $the_family[$node->mother]->post_id;
		}
		$str .= '",'."\n";

		$str .= '"Parent=';
		if ( isset($the_family[$node->father]) ) {
			$str .= $the_family[$node->father]->post_id;

		}
		$str .= '"';

		$tree_data_js .= $str;	
	}
	$tree_data_js .= ');'."\n";
	$out .= $tree_data_js;
	// End generate javascript tree text.

//	$out .= 'AddOnload(add_drag);'."\n";
	$out .= 'BOX_LINE_Y_SIZE = "'. 	wpft_options::get_option('generationheight').'";'."\n";
	$out .= 'canvasbgcol = "'. 	wpft_options::get_option('canvasbgcol').'";'."\n";
	$out .= 'nodeoutlinecol = "'.wpft_options::get_option('nodeoutlinecol').'";'."\n";
	$out .= 'nodefillcol	= "'. wpft_options::get_option('nodefillcol').'";'."\n";
	$out .= 'nodefillopacity = '.wpft_options::get_option('nodefillopacity').';'."\n";
	$out .= 'nodetextcolour = "'.wpft_options::get_option('nodetextcolour').'";'."\n";
	$out .= 'setOneNamePerLine('.wpft_options::get_option('bOneNamePerLine').');'."\n";
	$out .= 'setOnlyFirstName('.wpft_options::get_option('bOnlyFirstName').');'."\n";
	$out .= 'setBirthAndDeathDates('.wpft_options::get_option('bBirthAndDeathDates').');'."\n";
	$out .= 'setConcealLivingDates('.wpft_options::get_option('bConcealLivingDates').');'."\n";
	$out .= 'setShowSpouse('.wpft_options::get_option('bShowSpouse').');'."\n";
	$out .= 'setShowOneSpouse('.wpft_options::get_option('bShowOneSpouse').');'."\n";	
	$out .= 'setVerticalSpouses('.wpft_options::get_option('bVerticalSpouses').');'."\n";
	$out .= 'setMaidenName('.wpft_options::get_option('bMaidenName').');'."\n";
	$out .= 'setShowGender('.wpft_options::get_option('bShowGender').');'."\n";
	$out .= 'setDiagonalConnections('.wpft_options::get_option('bDiagonalConnections').');'."\n";
	$out .= 'setRefocusOnClick('.wpft_options::get_option('bRefocusOnClick').');'."\n";
	$out .= 'setShowToolbar('.wpft_options::get_option('bShowToolbar').');'."\n";
	$out .= 'setNodeRounding('.wpft_options::get_option('nodecornerradius').');'."\n";

	if (wpft_options::get_option('bShowToolbar') == 'true') {
		$out .= 'setToolbarYPad(20);'."\n";
	} else {
		$out .= 'setToolbarYPad(0);'."\n";
	}
	$out .= 'setToolbarPos(true, 3, 3);'."\n";
	$out .= 'setMinBoxWidth('.wpft_options::get_option('nodeminwidth').');'."\n";

	$out .= 'jQuery(document).ready(function($){'."\n";
	$out .= '	add_drag();'."\n";
	$out .= '	familytreemain();'."\n";
	$out .= "	var midpos = $('#familytree svg').width()/2 - $('#borderBox').width()/2;"."\n";
	$out .= "	$('#dragableElement').css('left', -midpos);"."\n";	
	$out .= '});'."\n";
	
	$out .= '</script>';	

/*
	setDeath = function(bState) 				
*/
	$out .= '<input type="hidden" size="30" name="focusperson" id="focusperson" value="'.$ancestor.'">'."\n";

	$out .= '<div id="borderBox">'."\n";
	$out .= '<div id="dragableElement">';
	$out .= '<div id="tree-container">'."\n";
	$out .= '<div id="toolbar-container">'."\n";
	foreach ($the_family as $node) {
		$out .= $node->get_toolbar_div();
	}
	$out .= '</div>'."\n";
	$out .= '<div id="thumbnail-container">'."\n";
	foreach ($the_family as $node) {
		$out .= $node->get_thumbnail_div();
	}
	$out .= '</div>'."\n";
	$out .= '<div id="familytree"></div>'."\n";
	$out .= '<img name="hoverimage" id="hoverimage" style="visibility:hidden;" >'."\n";
	$out .= '</div>'."\n"; // tree-container
	$out .= '</div>'."\n"; // borderBox
	$out .= '</div>'."\n"; // dragableElement
	if (wpft_options::get_option('showcreditlink') == 'true') {
		$out .= '<p style="text-align:left"><small>powered by <a target="_blank" href="http://www.esscotti.com/wp-family-tree-plugin">WP Family Tree</a></small></p>'."\n";
	}
/*
	$out .='<script type="text/javascript">';
	$out .='	var el = document.getElementById(\'tree-container\');';
	$out .='	var leftEdge = el.parentNode.clientWidth - el.clientWidth;';
	$out .='	var topEdge = el.parentNode.clientHeight - el.clientHeight;';
	$out .='	var dragObj = new dragObject(el, null, new Position(leftEdge, topEdge), new Position(0, 0));';
	$out .='</script>';
*/	
	return $out;
}
function bio_data() {
	global $post;
	$ftlink = wpft_options::get_option('family_tree_link');
	if (strpos($ftlink, '?') === false) {
		echo '<p><a href="'.$ftlink.'?ancestor='.$post->post_title.'">click here to view family tree</a>';
	} else {
		echo '<p><a href="'.$ftlink.'&ancestor='.$post->post_title.'">click here to view family tree</a>';
	}
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
	$spouse = get_post_meta($post->ID, 'spouse', true);
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

    <tr><td>Spouse:</td><td>
    <select style="width:200px" name="spouse" id="spouse">
    <option value="-" <?php if (empty($spouse) || $spouse=="-") echo "selected=\"selected\""; ?>> </option>
<?php
/*
	if ($gender == "f") {
		foreach ($males as $f) {
			echo '<option value="'.$f->ID.'" ';
			if ($f->ID == $spouse) echo "selected=\"selected\"";
			echo '>'.$f->post_title.'</option>';
		}
	} else if ($gender == "m") {
		foreach ($females as $f) {
			echo '<option value="'.$f->ID.'" ';
			if ($f->ID == $spouse) echo "selected=\"selected\"";
			echo '>'.$f->post_title.'</option>';
		}
	} else {
*/
		foreach ($family as $f) {
			echo '<option value="'.$f->ID.'" ';
			if ($f->ID == $spouse) echo "selected=\"selected\"";
			echo '>'.$f->post_title.'</option>';
		}
//	}
?>
	</select>
	</td></tr>

    </table>
    </div>
    </div>
    <?php
}


// Javascript data picker
// Facebook page, skype id, IM etc
// Occupation
// Locations: birthplace, died at, current location
// Spouse


function family_tree_update_post($id)
{
    $born   = stripslashes(strip_tags($_POST['born']));
    $died   = stripslashes(strip_tags($_POST['died']));
    $mother = stripslashes(strip_tags($_POST['mother']));
    $father = stripslashes(strip_tags($_POST['father']));
    $spouse = stripslashes(strip_tags($_POST['spouse']));
    $gender = stripslashes(strip_tags($_POST['gender']));

    if (!empty($born)) { delete_post_meta($id, 'born'); 	add_post_meta($id, 'born', $born); 		} //else { add_post_meta($id, 'born', $born); 		}
    if (!empty($died)) { delete_post_meta($id, 'died'); 	add_post_meta($id, 'died', $died); 		} //else { add_post_meta($id, 'died', $died); 		}
    if (!empty($mother)) { delete_post_meta($id, 'mother'); add_post_meta($id, 'mother', $mother); 	} //else ( add_post_meta($id, 'mother', $mother); 	}
    if (!empty($father)) { delete_post_meta($id, 'father'); add_post_meta($id, 'father', $father); 	} //else { add_post_meta($id, 'father', $father); 	}
    if (!empty($spouse)) { delete_post_meta($id, 'spouse'); add_post_meta($id, 'spouse', $spouse); 	} //else { add_post_meta($id, 'father', $father); 	}
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
	$cats = get_the_category();	// get array of category objects that apply to this post
	foreach ($cats as $cat) {
		if ($cat->slug == $category || $cat->name == $category) {
			// This post is a family member post so do the work...
			$the_family = tree::get_tree();
			if (isset($the_family[$post->ID])) {
				$html = $the_family[$post->ID]->get_html($the_family);
				$content = $html.$content;
			}
			break;	
		}	
	}
	return $content;
}

function wpft_family_tree_shortcode($atts, $content=NULL) {
	$root = $atts['root'];
	$ft_output = family_tree($root);

	wpft_options::check_options();

	return $ft_output;
}

function wpft_family_members_shortcode($atts, $content=NULL) {
	$root = $atts['root'];
	$ft_output = family_tree($root);
	$ft_output = family_list();
		
	wpft_options::check_options();

	return $ft_output;
}

add_shortcode('family-tree', 'wpft_family_tree_shortcode');
add_shortcode('family-members', 'wpft_family_members_shortcode');


add_action('admin_menu', 'family_tree_options_page');

function wpft_addHeaderCode() {
	$plugloc = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
	wp_enqueue_script('jquery');
	wp_enqueue_script('raphael', $plugloc.'raphael.js');
	wp_enqueue_script('familytree', $plugloc.'familytree.js');
	wp_enqueue_script('dragobject', $plugloc.'dragobject.js');
	wp_enqueue_script('onload', $plugloc.'onload.js');
	wp_enqueue_style('ft-style', $plugloc.'styles.css');
}
function addFooterCode() {

}

// Enable the ability for the family tree to be loaded from pages
add_filter('the_content','family_list_insert');
add_filter('the_content','family_tree_insert');

if (wpft_options::get_option('show_biodata_on_posts_page') == 'true') {
	add_filter('the_content','bio_data_insert');
}

add_action('init', 'wpft_addHeaderCode');
add_action('edit_post', 'family_tree_update_post');
add_action('save_post', 'family_tree_update_post');
add_action('publish_post', 'family_tree_update_post');

add_action('edit_page_form', 'family_tree_edit_page_form');
add_action('edit_form_advanced', 'family_tree_edit_page_form');
add_action('simple_edit_form', 'family_tree_edit_page_form');
