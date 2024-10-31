<?php
/*
Plugin Name: Page Showcaser Boxes
Plugin URI: http://www.ctuts.com/
Description: Show your blog pages in columns.
Author: Ari Susanto
Version: 1.1
Author URI: http://ctuts.com/
Copyright 2013  ARI SUSANTO  (email : admin@ctuts.com)
*/

register_activation_hook(__FILE__, 'service_showcaser_boxes_activation_function');
function service_showcaser_boxes_activation_function() {
	service_showcaser_boxes();
    flush_rewrite_rules();
}

register_deactivation_hook('_FILE_', 'service_showcaser_boxes_deactivation_function'); 
function service_showcaser_boxes_deactivation_function() {
}

function service_showcaser_boxes() {

	$labels = array(
		'name'                => _x( 'Page Boxes', 'Post Type General Name', 'services_showcaserbox' ),
		'singular_name'       => _x( 'Page Boxes', 'Post Type Singular Name', 'services_showcaserbox' ),
		'menu_name'           => __( 'Page Boxes', 'services_showcaserbox' ),
		'parent_item_colon'   => __( 'Parent Table', 'services_showcaserbox' ),
		'all_items'           => __( 'All Boxes', 'services_showcaserbox' ),
		'view_item'           => __( 'View Boxes', 'services_showcaserbox' ),
		'add_new_item'        => __( 'Add New Box', 'services_showcaserbox' ),
		'add_new'             => __( 'Create New Box', 'services_showcaserbox' ),
		'edit_item'           => __( 'Edit Box', 'services_showcaserbox' ),
		'update_item'         => __( 'Update Box', 'services_showcaserbox' ),
		'search_items'        => __( 'Search Boxes', 'services_showcaserbox' ),
		'not_found'           => __( 'No Boxes found', 'services_showcaserbox' ),
		'not_found_in_trash'  => __( 'No Boxes found in Trash', 'services_showcaserbox' ),
	);
	$args = array(
		'label'               => __( 'showcaserboxe', 'services_showcaserbox' ),
		'description'         => __( 'Box Info', 'services_showcaserbox' ),
		'labels'              => $labels,
		'supports'            => array( 'title'),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 40,
		'menu_icon'           => plugins_url('/img/books-stack.png',  __FILE__),
		'can_export'          => true,
		'rewrite' 			  => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'query_var'           => 'showcaserboxe',
		'capability_type'     => 'post',
		'with_front'		  => true
	);
		register_post_type( 'showcaserboxe', $args );
}
add_action( 'init', 'service_showcaser_boxes' );

add_action('add_meta_boxes', 'the_services_showcaser_box_metabox');

function the_services_showcaser_box_metabox() {
add_meta_box('service_showcasing_id', 'Create Box', 'the_services_showcaser_box_metabox_form', 'showcaserboxe', 'normal', 'high');
}

add_action('admin_init', 'enqueue_showcaserboxes_ajax');
function enqueue_showcaserboxes_ajax($hook) {
  global $pagenow, $typenow;

  if (empty($typenow) && !empty($_GET['post'])) {
    $post = get_post($_GET['post']);
    $typenow = $post->post_type;
  }
  
  if (is_admin() && $typenow=='showcaserboxe') {
    if ($pagenow=='post-new.php' || $pagenow=='post.php') { 
    
	wp_enqueue_script( 'showcaserboxe_ssbjquery_script', plugins_url('/page-showcaser-boxes/js/ssbshowcaserbox.js'),
		array('jquery'));
		
	wp_localize_script('showcaserboxe_ssbjquery_script', 'showcaserboxe_ajax_script_vars', array (
	'showcaserboxe_ajax_nonce' => 'wp_create_nonce("showcaserboxe-nonce-string")'
	));
	
	}
	}
}

function ssb_front_end_box_style() {
   wp_register_style( 'ssb_css_style', plugins_url('/page-showcaser-boxes/css/ssbshowboxes.css') );
	wp_enqueue_style('ssb_css_style');
}

add_action('wp_enqueue_scripts','ssb_front_end_box_style');

function the_services_showcaser_box_metabox_form($post) {
global $post;
wp_nonce_field('the_services_showcaser_box_metabox_form', 'services_showcaser_box_metabox_nonce' );
$args = array(
	'sort_order' => 'ASC',
	'sort_column' => 'post_title',
	'hierarchical' => 1,
	'exclude' => '',
	'include' => '',
	'meta_key' => '',
	'meta_value' => '',
	'authors' => '',
	'child_of' => 0,
	'parent' => -1,
	'exclude_tree' => '',
	'number' => '',
	'offset' => 0,
	'post_type' => 'page',
	'post_status' => 'publish'
); 
$cont = get_post_meta($post->ID, 'ssb_count_column_key', true);
?>
<button class="ssb_add_column">Add Column</button><button class="ssb_rmv_column">Remove Column</button><br/>
<input type='text' value="[ssb_box id='<?php echo $post->ID; ?>']"/>
<input type="hidden" name="ssbcounterfield" class="ssbcounterfieldinpt" value="<?php if($cont) echo $cont; else echo '1'; ?>" placeholder="columns" />
<input type="hidden" name="ssboxepostid" class="ssb_postid" value="<?php echo $post->ID; ?>" />
<div id="loadingimg" style="display:none;"><b>Processing...</b><br/><img src="<?php echo plugins_url('img/22.gif',  __FILE__); ?> "/></div>
<div id="grand_ssb_form_input">
<?php
$cont = get_post_meta($post->ID, 'ssb_count_column_key', true);
if ($cont != '') {
$count = $cont;
} else {
$count = 1;
}
if ($count) :
for ($n=1; $n<=$count; $n++) {
$pagekey = 'save_page_namedata_key'.$n; 
$pagename = get_post_meta($post->ID, $pagekey, true);
$wordskey = 'save_words_limit_countdata_key'.$n;
$wordslimit = get_post_meta($post->ID, $wordskey, true);
$image_iconkey = 'ssb_image_icon_url_key'.$n;
$imageicon = get_post_meta($post->ID, $image_iconkey, true);
?>
<div id="parent_ssb_form_input">
<div class="ssb_nameidentifier"><br/><hr><b>Column <?php echo $n; ?></b> :</div><hr>
<div class="ssb_selecpagetitle">Select Page :
<select name="ssb_page_namelistval<?php echo $n; ?>"> 
<option value="">Select page</option>
  <option <?php if ($pagename) echo "selected = 'selected'"; ?> style='display:none;'><?php if($pagename) echo $pagename; else echo 'Select Page';?></option>
 <?php 
  $pages = get_pages(); 
  foreach ( $pages as $page ) {
  	$option = '<option>';
	$option .= $page->post_title;
	$option .= '</option>';
	echo $option;
  }
 ?>
 </select>
<input type="text" name="ssb_words_limit<?php echo $n; ?>" value="<?php if($wordslimit) echo $wordslimit; ?>" placeholder="words limit"/>
</div>
<div class="ssb_imageicon">
Image / icon : <input type="text" name="ssb_image_oricon<?php echo $n; ?>" value="<?php if($imageicon) echo $imageicon; ?>" placeholder="image name"/>
</div>
</div>
<?php
}
else :
?>
<div id="parent_ssb_form_input">
<div class="ssb_nameidentifier"><br/><hr><b>Column 1</b> :</div><hr>
<div class="ssb_selecpagetitle">Select Page :
<select name="ssb_page_namelistval1"> 
<option value="" disabled selected>Select page</option>
 <?php 
  $pages = get_pages(); 
  foreach ( $pages as $page ) {
  	$option = '<option>';
	$option .= $page->post_title;
	$option .= '</option>';
	echo $option.$content;
  }
 ?>
 </select>
<input type="text" name="ssb_words_limit1" value="" placeholder="words limit"/>
</div>
<div class="ssb_imageicon">
Image / icon : <input type="text" name="ssb_image_oricon1" value="" placeholder="image url"/>
</div>
</div>

<?php
endif;
?>
</div>
<br/><br/>
<?php
}

add_action( 'save_post', 'the_services_showcaser_box_metabox_save' );
function the_services_showcaser_box_metabox_save( $post_id ) {
	
  if ( ! isset( $_POST['services_showcaser_box_metabox_nonce'] ) )
    return $post_id;
  $ssbshowcasenonce = $_POST['services_showcaser_box_metabox_nonce'];
  if ( ! wp_verify_nonce( $ssbshowcasenonce, 'the_services_showcaser_box_metabox_form' ) )
      return $post_id;
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;
  if ( 'showcaserboxe' == $_POST['post_type'] ) {
    if ( ! current_user_can( 'edit_page', $post_id ) )
        return $post_id;  
  } else {
    if ( ! current_user_can( 'edit_post', $post_id ) )
        return $post_id;
  }

 $countr = get_post_meta($post_id, 'ssb_count_column_key', true);
 if ($countr != ''){
 $cor = $countr;
 } else {
  $cor = 1;
 }
 
for($s=1; $s<=$cor; $s++){
$pagename = sanitize_text_field($_POST['ssb_page_namelistval'.$s]); 
$pagekey = 'save_page_namedata_key'.$s;
if ($pagename != '' && $pagename != 'Select Page')
update_post_meta($post_id, $pagekey, $pagename);
else
delete_post_meta($post_id, $pagekey, $pagename);

$wordslimitcount = sanitize_text_field($_POST['ssb_words_limit'.$s]);
$wordskey = 'save_words_limit_countdata_key'.$s;
if ($wordslimitcount != '')
update_post_meta($post_id, $wordskey, $wordslimitcount);
else
delete_post_meta($post_id, $wordskey, $wordslimitcount);

$image_iconurl = $_POST['ssb_image_oricon'.$s];
$image_iconkey = 'ssb_image_icon_url_key'.$s;
if($image_iconurl && $image_iconkey)
update_post_meta($post_id, $image_iconkey, $image_iconurl);
else
delete_post_meta($post_id, $image_iconkey, $image_iconurl);
}
}

add_shortcode('ssb_box', 'service_showcaser_box_display');

function service_showcaser_box_display($atts ) {
global $post;
	extract( shortcode_atts( array(
	'id' => ''	
	),	$atts ) );
  
 $countr = get_post_meta($id, 'ssb_count_column_key', true);
 if ($countr != ''){
 $cor = $countr;
 } else {
  $cor = 1;
 }
$boxes = "<div id='allservicesboard'>";
for($v=1; $v<=$cor; $v++){
$pagekey = 'save_page_namedata_key'.$v;
$pagetitle = get_post_meta($id, $pagekey, true);

$wordskey = 'save_words_limit_countdata_key'.$v;
$wordslimitcount = get_post_meta($id, $wordskey, true);

$image_iconkey = 'ssb_image_icon_url_key'.$v;
$image_iconurl = get_post_meta($id, $image_iconkey, true);
$image_dir = plugins_url('/page-showcaser-boxes/img/');
$image_url = $image_dir.$image_iconurl;
$pager = get_page_by_title($pagetitle);
$pagerid = $pager->ID;
$numbro = get_post( $pagerid );
$content = $numbro->post_content;
$page_link = get_permalink( $pagerid );
$button = "<a class='ssb_button' href='".$page_link."'>See Detail</a>";
$page_excerpt = wp_trim_words($content, $wordslimitcount, '');
$boxes .= "<div id='innerparentboxe'>";
$boxes .= "<div class='ssb_pagetitle'><h4>".$pagetitle."</h4></div>";
$boxes .= "<div class='ssb_img' style='float:left;padding-right:10px;'><img src='".$image_iconurl."'/></div>";
$boxes .= "<div class='page_content_excerpt'>". $page_excerpt."</div>";
$boxes .= "<div class='ssb_buttondiv'>" . $button . "</div>";
$boxes .= "</div>";
continue;
}
$boxes .= "</div>";
return $boxes;
}

add_action('wp_ajax_ssb_action', 'ssb_action_callback');
function ssb_action_callback($post_id) {
	global $post;
	$post_id = $post['ID'];
	global $wpdb; 
$ajaxnonce = $_POST['showcaserboxe_ajax_nonce'];
if (!isset($_POST['showcaserboxe_ajax_nonce']))
die();
	$potid = $_POST['postid'];
		$colnumbers = $_POST['ssbcolcount'];
		$colnumbers += 1;
	$update = update_post_meta($potid, 'ssb_count_column_key', $colnumbers);
		die();
}

add_action('wp_ajax_decrease_action', 'ssb_action_dec_callback');
function ssb_action_dec_callback($post_id) {
	global $post;
	$post_id = $post['ID'];
	global $wpdb; 
$ajaxnonce = $_POST['showcaserboxe_ajax_nonce'];
if (!isset($_POST['showcaserboxe_ajax_nonce']))
die();
	$podectid = $_POST['postdecid'];
		$coldecnumbers = $_POST['ssbcoldec'];
		$coldecnumbers -= 1;
	$update = update_post_meta($podectid, 'ssb_count_column_key', $coldecnumbers);
		die();
}		
?>