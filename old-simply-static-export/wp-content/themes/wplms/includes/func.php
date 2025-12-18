<?php

/**
 * FILE: func.php 
 * Author: Mr.Vibe 
 * Credits: www.VibeThemes.com
 * Project: WPLMS
 */
//Remove this fix
/*add_filter( 'bbp_verify_nonce_request_url', 'my_bbp_verify_nonce_request_url', 999, 1 );
function my_bbp_verify_nonce_request_url( $requested_url )
{
    return 'http://localhost:8888' . $_SERVER['REQUEST_URI'];
}*/

function bbp_enable_visual_editor( $args = array() ) {
    $args['tinymce'] = true;
    return $args;
}
add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );


// Adding Course Categories in LMS Menu
function wpa83704_adjust_the_wp_menu() {
    add_submenu_page(
        'lms',
        'Course Categories',
        'Course Category',
        'edit_posts',
        'edit-tags.php?taxonomy=course-cat'
    );
}

add_action( 'admin_menu', 'wpa83704_adjust_the_wp_menu', 999 );

//Correcting the Active bug in WordPress admin menus (refer: https://core.trac.wordpress.org/ticket/12718) 
function course_cat_menu_correction($parent_file) {
    global $current_screen;
    $taxonomy = $current_screen->taxonomy;
    if ($taxonomy == 'course-cat')
        $parent_file = 'lms';
    return $parent_file;
}
add_action('parent_file', 'course_cat_menu_correction');


add_filter('wpseo_pre_analysis_post_content','vibe_page_builder_content',10,2);
function vibe_page_builder_content($post_content,$post){

  if(get_post_type($post->ID) != 'page')
    return $post_content;

  $builder_enable = get_post_meta( $post->ID, '_enable_builder', true );
  $builder_layout = get_post_meta( $post->ID, '_builder_settings', true );
  $add_content = get_post_meta( $post->ID, '_add_content', true );
  
        
  if ( isset($builder_layout) &&  isset($builder_layout['layout_shortcode']) && '' != $builder_layout['layout_shortcode'] && $add_content == 'no') { 
             
            $content = $builder_layout['layout_shortcode'];
          
        }
        
        if ( $builder_layout && '' != $builder_layout['layout_shortcode'] && $add_content == 'yes_top') {
           
            $content = $content.$builder_layout['layout_shortcode'];
        }
        
        if ( $builder_layout && '' != $builder_layout['layout_shortcode'] && $add_content == 'yes_below') {
            
            $content = $builder_layout['layout_shortcode'].$content;
        }
        if(isset($content)){
            $post_content = str_replace('[','',$content);
            $post_content = str_replace(']','',$content);
        }

    return $post_content;
}

//FIX : COOKIE LOGIN FIX  @ jpmcafee : http://vibethemes.com/forums/forum/wordpress-html-css/wordpress-themes/wplms/7857-problem-with-user-login

function set_wp_test_cookie() {   
  setcookie(TEST_COOKIE, 'WP Cookie check', 0, COOKIEPATH, COOKIE_DOMAIN);  
  if ( SITECOOKIEPATH != COOKIEPATH )     
    setcookie(TEST_COOKIE, 'WP Cookie check', 0, SITECOOKIEPATH, COOKIE_DOMAIN); 
} 
add_action( 'after_setup_theme', 'set_wp_test_cookie', 101 );


/* FIX : BP DEFAULT AVATAR FIX : Refer : https://buddypress.trac.wordpress.org/ticket/4571 
function bp_wplms_define_constants(){
  $img_url=vibe_get_option('default_avatar');

  ///DEFINING BUDDYPRESS DEFAULT AVATAR
  if ( !defined( 'BP_AVATAR_DEFAULT' ) )
   define ( 'BP_AVATAR_DEFAULT', $img_url );  
}
*/

//add_filter('bp_core_mysteryman_src','vibe_custom_avatar',2,7);
function vibe_custom_avatar($avatar){
  global $bp;
   $avatar=vibe_get_option('default_avatar');
   return $avatar;
} 

add_filter('wplms_activity_loop','wplms_student_activity');
function wplms_student_activity($appended){
  $student_activity = vibe_get_option('student_activity');
  if(!current_user_can('edit_posts') && isset($student_activity) && $student_activity){
    $appended .='&user_id='.get_current_user_id();
  }
    
    return $appended;
}
// Fix for Member/Settings/Profile => 1.5 hrs of reading BuddyPress core code just to find the hook :D
add_filter('bp_settings_screen_xprofile','bp_settings_custom_profile',1,1);
function bp_settings_custom_profile($profile){
  return '/members/single/settings/profile';
}

add_filter('course_friendly_time','convert_unlimited_time',1,2);
function convert_unlimited_time($time_html,$time){
    if(intval($time/86400) > 999){
      return __('UNLIMITED ACCESS','vibe');
    }
    return $time_html;
}

/// BEGIN AJAX HANDELERS
add_action( 'wp_ajax_reset_googlewebfonts', 'reset_googlewebfonts' );
	function reset_googlewebfonts(){ 
            echo "reselecting..";
            $r = get_option('google_webfonts');
            if(isset($r)){
                delete_option('google_webfonts');
            }
                die();
}

if(!function_exists('import_data')){           
add_action( 'wp_ajax_import_data', 'import_data' );
  function import_data(){
    $name = stripslashes($_POST['name']);
                $code = base64_decode(trim($_POST['code'])); 
                if(is_string($code))
                    $code = unserialize ($code);
                
                $value = get_option($name);
                if(isset($value)){
                                update_option($name,$code);
                }else{
                    echo "Error, Option does not exist !";
                }
                die();
            }
} 


add_action( 'pre_get_posts', 'course_search_results' );


add_action('wplms_be_instructor_button','wplms_be_instructor_button');
function wplms_be_instructor_button(){
  $teacher_form = vibe_get_option('teacher_form');
  if(isset($teacher_form) && $teacher_form){
    echo '<a href="'.(isset($teacher_form)?get_permalink($teacher_form):'#').'" class="button create-group-button full">'. __( 'Become an Instructor', 'vibe' ).'</a>';  
  }else{
    vibe_breadcrumbs();
  }
}



function course_search_results($query){

  if(!$query->is_search())
    return $query;

  $course_cat = $_GET['course-cat'];
  if(isset($course_cat) && $course_cat !='*' && $course_cat !=''){
    $query->set('course-cat', $course_cat);
  }

  $instructor = $_GET['instructor'];
  if(isset($instructor) && $instructor !='*' && $instructor !=''){
    $query->set('author', $instructor);
  }

  return $query;
}

//add_action( 'pre_get_comments', 'restrict_questions_answers' );
function restrict_questions_answers( $comments ) {
    global $post;
    /*
    if($post->post_type != 'quiz' && $post->post_type != 'question'){
      //$comments->query_vars['post_type'] = 'post';
      //$comments->meta_query->parse_query_vars( $comments->query_vars );  
    }*/
}



add_action( 'template_redirect', 'vibe_check_course_archive' );
function vibe_check_course_archive(){
    if(is_post_type_archive('course') && !is_search()){
        $pages=get_site_option('bp-pages');
        if(is_array($pages) && isset($pages['course'])){
          $all_courses = get_permalink($pages['course']);
          wp_redirect($all_courses);
          exit();
        }
    }
}

add_action( 'template_redirect', 'vibe_product_woocommerce_direct_checkout' );
function vibe_product_woocommerce_direct_checkout()
{   
  if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
        $check=vibe_get_option('direct_checkout');

    if(isset($check) && $check == '2' || $check == 2){
      if( is_single() && get_post_type() == 'product' ){
          global $woocommerce;
          $woocommerce->cart->add_to_cart( get_the_ID() );
          $checkout_url = $woocommerce->cart->get_checkout_url();
          wp_redirect( $checkout_url);
          exit();
      }
    }
  }
}

add_action('woocommerce_order_item_name','vibe_view_woocommerce_order_course_details',2,100);
function vibe_view_woocommerce_order_course_details($html, $item ){
 
  $product_id=$item['item_meta']['_product_id'][0];
  if(isset($product_id) && is_numeric($product_id)){
      $courses = vibe_sanitize(get_post_meta($product_id,'vibe_courses',false));
      if(isset($courses) && is_Array($courses)){
        $html .= ' [ <i>'.__('COURSE : ');
        foreach($courses as $course){ 
          if(is_numeric($course))
           $html .= '<a href="'.get_permalink($course).'"><strong><i>'.get_the_title($course).'</i></strong></a>, ';
        }
        $html .=' </i> ]';
      }
  }
  return $html;
}


// added in version 1.5.3 , Assignment -> Unit Locking : Mark Assignments complete before seeing the mark this unit complete switch 
add_filter('wplms_unit_mark_complete','wplms_assignments_force_unit_complete',1,3);  
function wplms_assignments_force_unit_complete($mark_unit_html,$unit_id,$course_id){
    $flag=0;
    $assignment_locking = vibe_get_option('assignment_locking');
    if(isset($assignment_locking) && $assignment_locking){
        $unit_assignment = get_post_meta($unit_id,'vibe_assignment',true);
        if(isset($unit_assignment) && $unit_assignment){
            $user_id = get_current_user_id();
            $assignment_complete = get_post_meta($unit_assignment,$user_id,true);
            if(isset($assignment_complete) && $assignment_complete){
              $flag=0;
            }else{
              $flag=1;
            }
        }
      }
  if($flag)
      return '';
  return $mark_unit_html;
}  


add_filter('wplms_unit_mark_complete','wplms_media_force_unit_complete',10,3);  
function wplms_media_force_unit_complete($mark_unit_html,$unit_id,$course_id){
    
    $unit_media_lock = vibe_get_option('unit_media_lock');
    if(isset($unit_media_lock) && $unit_media_lock){
       $flag=0;
        $unit_content=get_post($unit_id);

        if(strpos($unit_content->post_content,'[video ') !== false){
          $flag=1;
        }else{
          if(strpos($unit_content->post_content,'[audio ') !== false)
            $flag=1;
          else
            $flag=0;
        }

        if($flag)
          return '<a href="#" id="mark-complete" data-unit="'.$unit_id.'" class="unit_button tip" title="'.__('Finish Video/Audio to mark this unit complete','vibe').'">'.__('Mark this Unit Complete','vibe').'</a>';    
    }
  return $mark_unit_html;
} 


add_action( 'wp_ajax_tour_number', 'inc_tour_number' );
  function inc_tour_number(){ 
            $r = get_option('tour_number');
            if(isset($r)){ $r++;
                update_option('tour_number', $r);
            }else
                add_option('tour_number', 0);
                die();
}

add_filter('get_avatar','change_avatar_css');

function change_avatar_css($class) {
$class = str_replace("class='avatar", "class='retina_avatar zoom animate", $class) ;
return $class;
}


function the_sub_title($id=NULL){
  global $post;
  if(isset($id)){
    $return=getPostMeta($id,'vibe_subtitle');
  }else{
    $return=getPostMeta($post->ID,'vibe_subtitle');  
  }
  
  if(!isset($return) || !$return){
    $return=0;
  }else{
    echo $return;  
  }
  
}

if(!function_exists('vibe_socialicons')){
    function vibe_socialicons(){
        global $vibe_options; $html='';
        $social_icons = vibe_get_option('social_icons');
        $social_icons_type = vibe_get_option('social_icons_type');
        $html = '<ul class="socialicons '.$social_icons_type.'">';

        $show_social_tooltip = vibe_get_option('show_social_tooltip');
        
        if(is_array($social_icons) && is_array($social_icons['social'])){
         foreach($social_icons['social'] as $key=>$icon){ 
            $url=$social_icons['url'][$key];
            $html .= '<li><a href="'.$url.'" title="'.$icon.'" target="_blank"  class="'.$icon.'"><i class="icon-'.$icon.'"></i></a></li>';
            }
         }
        $html .= '</ul>';
        return $html;  
     } 
}

if(!function_exists('vibe_inpagemenu')){
    function vibe_inpagemenu(){
        global $post;
        $show_menu=getPostMeta($post->ID,'vibe_show_inpagemenu');
        if($show_menu && $show_menu == 'S'){
            $inpage_menu=getPostMeta($post->ID,'vibe_inpage_menu');
            $inpage=array();
            foreach($inpage_menu as $item){
                $nitem = preg_replace('/[^a-zA-Z0-9\']/', '_', $item);
                $nitem = str_replace("'", '', $nitem);
                $inpage[$nitem]=$item;
            }
            return $inpage;
        }else
            return 0;
    }
    
}


if(!function_exists('get_all_taxonomy_terms')){
    function get_all_taxonomy_terms(){
        $taxonomies=get_taxonomies('','objects'); 
        $termchildren = array();
        foreach ($taxonomies as $taxonomy ) {
            $toplevelterms = get_terms($taxonomy->name, 'hide_empty=0&hierarchical=0&parent=0');
          foreach ($toplevelterms as $toplevelterm) {
                    $termchildren[ $toplevelterm->slug] = $taxonomy->name.' : '.$toplevelterm->name;
            }
            }
            
    return $termchildren;  
    }
}

if(!function_exists('wplms_profile_group_tabs')){
  function wplms_profile_group_tabs($tabs=NULL, $groups=NULL, $group_name=NULL){
    $instructor_field_group = vibe_get_option('instructor_field_group');
    if(isset($instructor_field_group) && $instructor_field_group !=''){
      if(isset($groups) && is_array($groups))
        foreach($groups as $key=>$group){ 
         if($group->name == $instructor_field_group && !current_user_can('edit_posts')){ 
           unset($tabs[$key]);
           break;
         }
       }
    }
     return $tabs;
  }

  add_filter('xprofile_filter_profile_group_tabs','wplms_profile_group_tabs',1,3);
}

add_action('bp_init','wplms_profile_group_tabs');


if(!function_exists('wp_get_attachment_info')){
function wp_get_attachment_info( $attachment_id ) {
       
	$attachment = get_post( $attachment_id );
        if(isset($attachment)){
	return array(
		'alt' => get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ),
		'caption' => $attachment->post_excerpt,
		'description' => $attachment->post_content,
		'href' => get_permalink( $attachment->ID ),
		'src' => $attachment->guid,
		'title' => $attachment->post_title
	);
       }
}
}


function set_wpmenu(){
    echo '<p style="padding:20px 0 10px;text-align:center;">'.__('Setup Menus','vibe').'</p>';
}

add_action('wplms_the_course_button','wplms_pmp_pro_connect',10,2);
function wplms_pmp_pro_connect($course_id,$user_id){
  if ( in_array( 'paid-memberships-pro/paid-memberships-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) && is_user_logged_in()) {

     $membership_ids=vibe_sanitize(get_post_meta($course_id,'vibe_pmpro_membership',false));

     if(pmpro_hasMembershipLevel($membership_ids)){

        $coursetaken=get_user_meta($user_id,$course_id,true);
        if(!isset($coursetaken) || $coursetaken ==''){

            $duration=get_post_meta($course_id,'vibe_duration',true);
            $new_duration = time()+86400*$duration;
            $new_duration = apply_filters('wplms_pmpro_course_check',$new_duration);
            if(update_user_meta($user_id,$course_id,$new_duration)){
              update_post_meta($course_id,$user_id,0); // Consistency
              $group_id=get_post_meta($course_id,'vibe_group',true);
              if(isset($group_id) && $group_id !=''){
                groups_join_group($group_id, $user_id );
              }
            }

        }
     }
  }
}



add_action('pmpro_after_change_membership_level','wplms_pmprostop_previous_courses',10,2);
function wplms_pmprostop_previous_courses($level_id, $user_id){
  global $pmpro_pages, $wpdb;



  if($level_id !=0)
    return;

  $my_started_courses = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %d AND meta_value IN (0,1)",$user_id), ARRAY_A);
  
  

  if(isset($my_started_courses) && is_array($my_started_courses) && count($my_started_courses)){
    foreach($my_started_courses as $my_course){
      if(get_post_type($my_course['post_id']) == 'course'){
       
          $course_time=get_user_meta($user_id,$my_course['post_id'],true);
         
          if(isset($course_time) && $course_time > time())
             if(delete_user_meta($user_id,$my_course['post_id'])){
                delete_post_meta($my_course['post_id'],$user_id);
             }else
              wp_die(__('SOME ISSUE OCCURED WHILE REMOVING STUDENT FROM COURSES','vibe'));
      }    
    }
  }
  //disable this hook so we don't loop
  remove_action("pmpro_after_change_membership_level", "wplms_pmprostop_previous_courses", 10, 2);
      
  return;
}


function get_image_id($image_url) {
    global $wpdb;
    
    $attachment = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE guid='%s'",$image_url));
  if($attachment)
        return $attachment;
    else
        return false;
}

add_filter('wplms_course_front_details','wplms_custom_social_sharing_links');

function wplms_custom_social_sharing_links($return){
   $sharing = '<div class="course_sharing">'.social_sharing().'</div>';
   return $return.$sharing;
}


add_filter('wplms_course_credits','wplms_show_course_student_status',10,2);
function wplms_show_course_student_status($credits,$course_id){
  if(is_user_logged_in()){
    $user_id=get_current_user_id();
    $check=get_user_meta($user_id,$course_id,true);
    if(isset($check) && $check){
      if($check < time()){
        return '<strong>'.__('EXPIRED','vibe').'<span class="subs">'.__('COURSE','vibe').'</span></strong>';
      }
      $check_course=get_post_meta($course_id,$user_id,true);
      switch($check_course){
        case 0:
        $credits ='<strong>'.__('START','vibe').'<span class="subs">'.__('COURSE','vibe').'</span></strong>';
        break;
        case 1:
        $credits ='<strong>'.__('CONTINUE','vibe').'<span class="subs">'.__('COURSE','vibe').'</span></strong>';
        break;
        case 2:
        $credits ='<strong>'.__('EVALUATION','vibe').'<span class="subs">'.__('COURSE UNDER','vibe').'</span></strong>';
        break;
        default:
        $credits ='<strong>'.__('FINISHED','vibe').'<span class="subs">'.__('COURSE','vibe').'</span></strong>';
        break;
      }
    }
  }
  return $credits;
}
/*==== End Show Values ====*/


add_filter('widget_text', 'do_shortcode');


function custom_excerpt($chars=0, $id = NULL) {
	global $post;
        if(!isset($id)) $id=$post->ID;
	$text = get_post($id);
        
	if(strlen($text->post_excerpt) > 10)
            $text = $text->post_excerpt . " ";
        else
            $text = $text->post_content . " ";
        
	$text = strip_tags($text);
        $ellipsis = false;
        $text = strip_shortcodes($text);
	if( strlen($text) > $chars )
		$ellipsis = true;
  

	$text = substr($text,0,intval($chars));

	$text = substr($text,0,strrpos($text,' '));

	if( $ellipsis == true && $chars > 1)
		$text = $text . "...";
        
	return $text;
}



//Registeration and Activation refirect URL  
add_filter( 'registration_redirect' , 'vibe_registration_redirect' );
function vibe_registration_redirect() {
    $pageid=vibe_get_option('activation_redirect');
    return get_permalink($pageid);
}



if(!function_exists('pagination')){
function pagination($pages = '', $range = 4)
{  
     $showitems = ($range * 2)+1;  
 
     global $paged;
     if(empty($paged)) $paged = 1;
 
     if($pages == '')
     {
         global $wp_query;
         $pages = $wp_query->max_num_pages;
         if(!$pages)
         {
             $pages = 1;
         }
     }   
 
     if(1 != $pages)
     {
         echo "<div class=\"pagination\"><span>Page ".$paged." of ".$pages."</span>";
         if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo; First</a>";
         if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo; Previous</a>";
 
         for ($i=1; $i <= $pages; $i++)
         {
             if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems ))
             {
                 echo ($paged == $i)? "<span class=\"current\">".$i."</span>":"<a href='".get_pagenum_link($i)."' class=\"inactive\">".$i."</a>";
             }
         }
 
         if ($paged < $pages && $showitems < $pages) echo "<a href=\"".get_pagenum_link($paged + 1)."\">Next &rsaquo;</a>";  
         if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>Last &raquo;</a>";
         echo "</div>\n";
     }
}
}

if(!function_exists('get_current_post_type')){
function get_current_post_type() {
  global $post, $typenow, $current_screen;
  
  //lastly check the post_type querystring
  if( isset( $_REQUEST['post_type'] ) )
    return sanitize_key( $_REQUEST['post_type'] );
  
  elseif ( isset( $_REQUEST['post'] ) )
    return get_post_type($_REQUEST['post']);
  
  elseif ( $post && $post->post_type )
    return $post->post_type;
  
  elseif( $typenow )
    return $typenow;

  //check the global $current_screen object - set in sceen.php
  elseif( $current_screen && $current_screen->post_type )
    return $current_screen->post_type;

  //we do not know the post type!
  return 'post';
}
}

if(!function_exists('vibe_sanitize')){
  function vibe_sanitize($array){
    if(isset($array[0]) && is_array($array[0]))
      return $array[0];
  }
}

if(!function_exists('vibe_validate')){
  function vibe_validate($value){
    if(isset($value) && $value && $value !='H')
      return true;
    else
      return false;
  }
}



function vibe_breadcrumbs() {  
    global $post;
   
    /* === OPTIONS === */  
    $text['home']     = __('Home','vibe'); // text for the 'Home' link  
    $text['category'] = '%s'; // text for a category page  
    $text['search']   = '%s'; // text for a search results page  
    $text['tag']      = '%s'; // text for a tag page  
    $text['author']   = '%s'; // text for an author page  
    $text['404']      = 'Error 404'; // text for the 404 page  
  
    $showCurrent = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show  
    $showOnHome  = 0; // 1 - show breadcrumbs on the homepage, 0 - don't show  
    $delimiter   = ''; // delimiter between crumbs  
    $before      = '<li class="current">'; // tag before the current crumb  
    $after       = '</li>'; // tag after the current crumb  
    /* === END OF OPTIONS === */  
  
    global $post;  
    $homeLink = home_url();  
    $linkBefore = '<li>';  
    $linkAfter = '</li>';  
    $linkAttr = ' rel="v:url" property="v:title"';  
    $link = $linkBefore . '<a' . $linkAttr . ' href="%1$s">%2$s</a>' . $linkAfter;  
  
    if (is_home() || is_front_page()) {  
  
        if ($showOnHome == 1) echo '<div id="crumbs"><a href="' . $homeLink . '">' . $text['home'] . '</a></div>';  
  
    } else {  
  
        echo '<ul class="breadcrumbs">' . sprintf($link, $homeLink, $text['home']) . $delimiter;  
  
        if ( is_category() ) {  
            $thisCat = get_category(get_query_var('cat'), false);  
            if ($thisCat->parent != 0) {  
                $cats = get_category_parents($thisCat->parent, TRUE, $delimiter);  
                $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);  
                $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);  
                echo $cats;  
            }  
            echo $before . sprintf($text['category'], single_cat_title('', false)) . $after;  
  
        } elseif ( is_search() ) {  
            echo $before . sprintf($text['search'], get_search_query()) . $after;  
  
        } elseif ( is_day() ) {  
            echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;  
            echo sprintf($link, get_month_link(get_the_time('Y'),get_the_time('m')), get_the_time('F')) . $delimiter;  
            echo $before . get_the_time('d') . $after;  
  
        } elseif ( is_month() ) {  
            echo sprintf($link, get_year_link(get_the_time('Y')), get_the_time('Y')) . $delimiter;  
            echo $before . get_the_time('F') . $after;  
  
        } elseif ( is_year() ) {  
            echo $before . get_the_time('Y') . $after;  
  
        } elseif ( is_single() && !is_attachment() ) {  

            $post_type_var = get_post_type();

            switch($post_type_var){
              case 'post':
                  $cat = get_the_category(); $cat = $cat[0];  
                  $cats = get_category_parents($cat, TRUE, $delimiter);  
                  if ($showCurrent == 0) $cats = preg_replace("#^(.+)$delimiter$#", "$1", $cats);  
                  $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);  
                  $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);  
                  echo $cats;  
                  if ($showCurrent == 1) echo $before . get_the_title() . $after; 
              break;
              case 'product':
                  $shop_page_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
                  $post_type = get_post_type_object(get_post_type());  
                  printf($link, $homeLink . '/' . basename($shop_page_url) . '/', $post_type->labels->singular_name);  
                  if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after; 
              break;
              case 'course':
                  $post_type =  get_post_type_object(get_post_type()); 

                  $course_category = get_the_term_list($post->ID, 'course-cat', '', '', '' );  

                  $slug = $post_type->rewrite;  
                  $course_category;

                  $course_category = str_replace('<a', $linkBefore . '<a' . $linkAttr, $course_category);  
                  $course_category = str_replace('</a>', '</a>' . $linkAfter, $course_category);  
                  printf($link, $homeLink . '/' . $slug['slug'] . '/', __('Course','vibe'));  //$post_type->labels->singular_name
                  
                  echo apply_filters('wplms_breadcrumbs_course_category',$course_category);

                  if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after; 
              break;
              default:
                  $post_type = get_post_type_object(get_post_type());  
                  $slug = $post_type->rewrite;  
                  printf($link, $homeLink . '/' . $slug['slug'] . '/', $post_type->labels->singular_name);  
                  if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after; 
              break;
            }
  
        } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {  
            $post_type = get_post_type_object(get_post_type());  

            echo $before . $post_type->labels->singular_name . $after;  
  
        } elseif ( is_attachment() ) {  
            $parent = get_post($post->post_parent);  
            $cat = get_the_category($parent->ID); 
            if(isset($cat[0])){
            $cat = $cat[0];  
            $cats = get_category_parents($cat, TRUE, $delimiter);  
            $cats = str_replace('<a', $linkBefore . '<a' . $linkAttr, $cats);  
            $cats = str_replace('</a>', '</a>' . $linkAfter, $cats);  
            echo $cats;  
            }
            printf($link, get_permalink($parent), __('Attachment','vibe'));  
            
            if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;  
  
        } elseif ( is_page() && !$post->post_parent ) {  
            if ($showCurrent == 1) echo $before . get_the_title() . $after;  
  
        } elseif ( is_page() && $post->post_parent ) {  
            $parent_id  = $post->post_parent;  
            $breadcrumbs = array();  
            while ($parent_id) {  
                $page = get_page($parent_id);  
                $breadcrumbs[] = sprintf($link, get_permalink($page->ID), get_the_title($page->ID));  
                $parent_id  = $page->post_parent;  
            }  
            $breadcrumbs = array_reverse($breadcrumbs);  
            for ($i = 0; $i < count($breadcrumbs); $i++) {  
                echo $breadcrumbs[$i];  
                if ($i != count($breadcrumbs)-1) echo $delimiter;  
            }  
            if ($showCurrent == 1) echo $delimiter . $before . get_the_title() . $after;  
  
        } elseif ( is_tag() ) {  
            echo $before . sprintf($text['tag'], single_tag_title('', false)) . $after;  
  
        } elseif ( is_author() ) {  
            global $author;  
            $userdata = get_userdata($author);  
            echo $before . sprintf($text['author'], $userdata->display_name) . $after;  
  
        } elseif ( is_404() ) {  
            echo $before . $text['404'] . $after;  
        }  
  
        if ( get_query_var('paged') ) {  
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';  
            echo __('Page','vibe') . ' ' . get_query_var('paged');  
            if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';  
        }  
  
        echo '</ul>';  
  
    }  
} // end vibe_breadcrumbs()  


function get_all_testimonials(){
  $args=array(
    'post_type' => 'testimonials',
    'orderby'   => 'modified',
    'numberposts' => 999
    );

  $testimonials=get_posts($args);

  $testimonial_array=array();
  foreach($testimonials as $testimonial){
    $testimonial_array[$testimonial->ID]=$testimonial->post_title;
  }
  return $testimonial_array;
}


function vbp_current_user_notification_count() {
    $notifications = bp_notifications_get_notifications_for_user(bp_loggedin_user_id(), 'object');
    $count = !empty($notifications) ? count($notifications) : 0;
   return $count;
}

// WOO COMMERCE  HANDLES
remove_filter( 'lostpassword_url',  'wc_lostpassword_url', 10, 0 );
remove_action( 'woocommerce_before_main_content','woocommerce_breadcrumb', 20, 0);
add_action('woocommerce_before_main_content','vibe_breadcrumbs',20,0);

remove_action('woocommerce_pagination', 'woocommerce_pagination', 10);
add_action( 'woocommerce_pagination', 'pagination', 10);


/**
* Set custom add to cart redirect
*/


add_action('woocommerce_init', 'vibe_woocommerce_direct_checkout');

if(!function_exists('vibe_woocommerce_direct_checkout')){
  function vibe_woocommerce_direct_checkout(){
    if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))){
        $check=vibe_get_option('direct_checkout');
        if(isset($check) && ($check == 1  || $check == '1')){
          update_option('woocommerce_cart_redirect_after_add', 'no');
          update_option('woocommerce_enable_ajax_add_to_cart', 'no');
          add_filter('add_to_cart_redirect', 'vcustom_add_to_cart_redirect');
        }
    }else
      return;
  }
}

function vcustom_add_to_cart_redirect() {
    return get_permalink(get_option('woocommerce_checkout_page_id'));
}


add_action('wplms_course_unit_meta','vibe_custom_print_button');
if(!function_exists('vibe_custom_print_button')){
    function vibe_custom_print_button(){
      $print_html='<a href="#" class="print_unit"><i class="icon-printer-1"></i></a>';
        echo apply_filters('wplms_unit_print_button',$print_html);  
      }
}


add_action('wplms_course_start_after_time','wplms_course_progressbar',1,2);
function wplms_course_progressbar($course_id,$unit_id){
    $user_id=get_current_user_id();
    $course_progressbar = vibe_get_option('course_progressbar');
    if(!isset($course_progressbar) || !$course_progressbar)
       return;

    $units = bp_course_get_curriculum_units($course_id);

    $total_units = count($units);

    $key = array_search($unit_id,$units);
    $meta=get_user_meta($user_id,$unit_id,true);
    
    if(isset($meta) && $meta)
      $key++;

    $percentage = round(($key/$total_units)*100); // Indexes are less than the count value
    if($percentage > 100)
      $percentage= 100;

    $unit_increase = round((1/$total_units)*100);

    echo '<div class="progress course_progressbar" data-increase-unit="'.$unit_increase.'" data-value="'.$percentage.'">
             <div class="bar animate cssanim stretchRight load" style="width: '.$percentage.'%;"><span>'.$percentage.'%</span></div>
           </div>';

}


add_action('wplms_course_before_front_main','wplms_error_message_handle');
function wplms_error_message_handle(){
  global $post;
  if(isset($_REQUEST['error'])){ 
  switch($_REQUEST['error']){
    case 'precourse':
      $pre=get_post_meta($post->ID,'vibe_pre_course',true);
      echo '<div id="message" class="notice"><p>'.__('Requires completion of course : ','vibe').'<a href="'.get_permalink($pre).'">'.get_the_title($pre).'</a></p></div>';
    break;
    case 'login':
      echo '<div id="message" class="notice"><p>'.__('You must be logged in to take this course ','vibe').apply_filters('wplms_registeration_page','<a href="'.wp_registration_url().'" class="link"> : '.__(' REGISTER NOW','vibe').'</a>').'</p></div>';
    break;
    }
  }
}

add_action('wplms_before_start_course','wplms_before_start_course_check');
function wplms_before_start_course_check(){

  $user_id = get_current_user_id();  
  $expire=time()+24*3600; // One Day Cookie logged in Limit for the course
  if ( isset($_POST['start_course']) && wp_verify_nonce($_POST['start_course'],'start_course') ){
    $course_id=$_POST['course_id'];
    $coursetaken=1;
      $cflag=0;
      $precourse=get_post_meta($course_id,'vibe_pre_course',true);
      if(isset($precourse) && $precourse !=''){
          $preid=get_post_meta($precourse,$user_id,true);
          if(isset($preid) && $preid !='' && $preid > 2){ // COURSE STATUS : 0 : NOT STARTED 1: STARTED 2 : SUBMITTED
              $cflag=1;
          }
      }else{
          $cflag=1;
      }

      if($cflag){
          setcookie('course',$course_id,$expire,'/');
          $students=get_post_meta($course_id,'vibe_students',true);
          $students++;
          update_post_meta($course_id,'vibe_students',$students);
          update_post_meta($course_id,$user_id,1); // COURSE STARTED

          $activity_id=bp_course_record_activity(array(
            'action' => __('Student started course ','vibe').get_the_title($course_id),
            'content' => __('Student ','vibe').bp_core_get_userlink( $user_id ).__(' started the course ','vibe').get_the_title($course_id),
            'type' => 'start_course',
            'item_id' => $course_id,
            'primary_link'=>get_permalink($course_id),
            'secondary_item_id'=>$user_id
          ));

          bp_course_record_activity_meta(array(
              'id' => $activity_id,
              'meta_key' => 'instructor',
              'meta_value' => get_post_field( 'post_author', $course_id )
              ));
      }else{
          
          header('Location: ' . get_permalink($course_id) . '?error=precourse');
          
      }

    

  }else if ( isset($_POST['continue_course']) && wp_verify_nonce($_POST['continue_course'],'continue_course') ){
    $course_id=$_POST['course_id'];
    $coursetaken=get_user_meta($user_id,$course_id,true);
      setcookie('course',$course_id,$expire,'/');
  }else{
    if(isset($_COOKIE['course'])){
      $course_id=$_COOKIE['course'];
      $coursetaken=1;
    }else
      wp_die( __('This Course can not be taken. Contact Administrator.','vibe'), 'Contact Admin', array(500,true) );
  }

}


add_action('wplms_before_quiz','wplms_check_quiz_retake');
function wplms_check_quiz_retake(){
    if(isset($_POST['initiate_retake']) && isset($_POST['security'])){
        if(function_exists('student_quiz_retake')){
            student_quiz_retake();
        }
    }
}

add_action('wplms_before_quiz','wplms_check_quiz_submission');
function wplms_check_quiz_submission(){

}


// Below function is used in multiple locations so keeping as it is
function wplms_get_course_unfinished_unit($course_id){
  $user_id = get_current_user_id();  

  if(isset($_COOKIE['course'])){
      $coursetaken=1;
  }else{
      $coursetaken=get_user_meta($user_id,$course_id,true);
  }    
  
  $course_curriculum=vibe_sanitize(get_post_meta($course_id,'vibe_course_curriculum',false));

  $key=0;
  if(isset($coursetaken) && $coursetaken){
      if(isset($course_curriculum) && is_array($course_curriculum) && count($course_curriculum)){
        foreach($course_curriculum as $uid){
          if(is_numeric($uid)){
            $units[$key]=$uid;
            $unittaken=get_user_meta($user_id,$uid,true);
            if(!isset($unittaken) || !$unittaken){
              break;
            }
            $key++;
          }
        }
      }else{
          echo '<div class="error"><p>'.__('Course Curriculum Not Set','vibe').'</p></div>';
          return;
      }    
  }

$flag=1;
$drip_enable=get_post_meta($course_id,'vibe_course_drip',true);

if(vibe_validate($drip_enable)){
    $drip_duration = get_post_meta($course_id,'vibe_course_drip_duration',true);
    
    if($key > 0){
       $pre_unit_time=get_post_meta($units[($key-1)],$user_id,true);

       if(isset($pre_unit_time) && $pre_unit_time){
         if(($pre_unit_time + ($key)*86400) > time()){
                $flag=0;
            }
        }else{
            $flag=0;
        }  

  }
}  


  if(isset($uid) && $flag && $key) // Should Always be set 
      $unit_id=$uid; // Last un finished unit
  else{
     if(isset($key) && $key > 0)
       $unit_id=$units[($key-1)] ;
     else
      $unit_id = '' ;
  } 
  
  return $unit_id;
}

add_filter('woocommerce_payment_complete_order_status', 'wplms_force_change_status_function',10,2);
function wplms_force_change_status_function($order_status,$order_id){
  $force_complete = vibe_get_option('force_complete');
  $flag=0;

  if(isset($force_complete) && $force_complete ){
    $order = new WC_Order($order_id);
    $items = $order->get_items();
    $user_id=$order->user_id;
    foreach($items as $item){
      $product_id = $item['product_id'];
      $is_virtual = get_post_meta( $product_id, '￼_virtual', true );
      if( $is_virtual == 'yes' ){
        $flag=1;
        break;
      }
    } 
    
    if($flag) 
     return 'completed';
  }


  return $order_status;
}

add_action('wplms_force_woocommerce_order_complete','wplms_force_paypal_orders_complete',1,1);
function wplms_force_paypal_orders_complete($order){
  $force_complete = vibe_get_option('force_complete');  
  if(isset($force_complete) && $force_complete == 2){
     $order->update_status('completed');
  }
}

add_filter( 'woocommerce_checkout_fields' , 'woo_remove_billing_zipcode_field' );
function woo_remove_billing_zipcode_field($fields){
    global $woocommerce;
    
    unset($fields['billing']['billing_postcode']);

    return $fields;
}

add_filter( 'woocommerce_checkout_fields' , 'woo_remove_billing_checkout_fields' );

function woo_remove_billing_checkout_fields( $fields ) {
  global $woocommerce;
    $remove_fields = vibe_get_option('remove_woo_fields');

    if(!isset($remove_fields) || !$remove_fields){
      return $fields;
    }
    
    $products = $woocommerce->cart->get_cart();

    if( woo_cart_has_virtual_product()) {
      
      unset($fields['billing']['billing_company']);
      unset($fields['billing']['billing_address_1']);
      unset($fields['billing']['billing_address_2']);
      unset($fields['billing']['billing_city']);
      unset($fields['billing']['billing_postcode']);
      unset($fields['billing']['billing_country']);
      unset($fields['billing']['billing_state']);
      unset($fields['billing']['billing_phone']);
      unset($fields['billing']['billing_address_2']);
      unset($fields['billing']['billing_postcode']);
      unset($fields['billing']['billing_company']);
      unset($fields['billing']['billing_city']);
    }
    
    return $fields;
}


function woo_cart_has_virtual_product() {
  
  global $woocommerce;
  $has_virtual_products = false;
  $virtual_products = 0;

  $products = $woocommerce->cart->get_cart();

  foreach( $products as $product ) {
    
    $product_id = $product['product_id'];
    $is_virtual = get_post_meta( $product_id, '_virtual', true );

    if( $is_virtual == 'yes' )
      $virtual_products += 1;
  }
  
  if( count($products) == $virtual_products )
    $has_virtual_products = true;
  
  return $has_virtual_products;
 
}

add_filter('wplms_course_finished','wplms_course_finished_course_review_form');
function wplms_course_finished_course_review_form($return){
  global $withcomments;
  $withcomments = true;
  ob_start();
  comments_template('/course-review.php',true);
  $return .= ob_get_contents();
  ob_end_clean();

  return $return;
}

/* ===== INSTRUCTOR PRIVACY ====== */
add_filter('wplms_frontend_cpt_query','wplms_instructor_privacy_filter');
add_filter('wplms_backend_cpt_query','wplms_instructor_privacy_filter');
function wplms_instructor_privacy_filter($args=array()){
    $instructor_privacy = vibe_get_option('instructor_content_privacy');
    if(isset($instructor_privacy) && $instructor_privacy && !current_user_can('manage_options')){
        global $current_user;
        get_currentuserinfo();
        $args['author'] = $current_user->ID;
    }
    return $args;
}

/* ===== NINJA FORMS FOR INSTRUCTOR SIGNUP ====== */

/**
 * Add an export link in the submission table
 */
function wplms_ninja_forms_sub_table_row_actions_convert_to_instructor( $row_actions, $data, $sub_id, $form_id ) {

  $ninja_instructor_form_id = vibe_get_option('ninja_instructor_form_id');
  if(isset($ninja_instructor_form_id) && $ninja_instructor_form_id == $form_id)
    $row_actions['instructor'] = '<span class="mar_instructor"><a href="#" id="wplms_ninja_forms_sub_'.$sub_id.'" class="wplms-ninja-forms-convert-to-instructor-sub">'. __( 'Make Instructor', 'ninja-forms' ).'</a></span>';

  return $row_actions;

}
add_filter( 'ninja_forms_sub_table_row_actions', 'wplms_ninja_forms_sub_table_row_actions_convert_to_instructor', 40, 4 );


add_action('wp_ajax_wplms_ninja_forms_change_to_instructor_sub','wplms_ninja_forms_change_to_instructor_sub');

function wplms_ninja_forms_change_to_instructor_sub($sub_id = ''){
  global $wpdb;
  if($sub_id == ''){
    $ajax = true;
    $sub_id = absint( $_REQUEST['sub_id'] );
  }else{
    $ajax = false;
  }

  $submission = $wpdb->get_results($wpdb->prepare("SELECT data FROM ".NINJA_FORMS_SUBS_TABLE_NAME." WHERE id = %d", $sub_id));

  if(isset($submission) && isset($submission[0])){
    $submission_value = $submission[0]->data;
    $values = unserialize($submission_value);
    foreach($values as $value){
      $email=$value['user_value'];
       if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            $args = array(
                  'search'         => $email,
                  'search_columns' => array('user_email' ),
                );
            $user_query = new WP_User_Query( $args );
            // User Loop
            if ( ! empty( $user_query->results ) && count($user_query->results) == 1) {
              $user = $user_query->results[0];
              $user_id = $user->ID;
              if(!user_can($user->ID,'edit_posts')){
                $user_id = wp_update_user( array( 'ID' => $user_id, 'role' => 'instructor' ) );
                  if ( is_wp_error( $user_id ) ) {
                    _e('Unable to change user role');
                  } else {
                    _e('User role Successfully Changed to Instructor');
                  }
              }
            }
       }
    }
  }
  
  if( $ajax ){
    die();
  }
}
?>
