<?php
/**
 * Template Name: All Instructors
 */
get_header();

$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
if($paged==1){
		$offset=0;  
}else {
   $offset= ($paged-1)*$no;
}

//$no = vibe_get_option('loop_number');
//if(!isset($no) || $no == '')
	$no=999;


$args = array(
                'role' => 'instructor', // instructor
    			'number' => $no, 
    			'offset' => $offset,
                'orderby' => 'post_count', 
                'order' => 'DESC' 
    		);
$user_query = new WP_User_Query( $args );


$field = vibe_get_option('instructor_field');
if(!isset($ifield) || $ifield =='')$ifield='Expertise';

$title=get_post_meta(get_the_ID(),'vibe_title',true);
if(vibe_validate($title)){
?>
<section id="title">
    <div class="container">
        <div class="row">
            <div class="col-md-9 col-sm-8">
                <div class="pagetitle">
                    <h1><?php the_title(); ?></h1>
                    <h5><?php the_sub_title(); ?></h5>
                </div>
            </div>
             <div class="col-md-3 col-sm-4">
            	<?php
                    $breadcrumbs=get_post_meta(get_the_ID(),'vibe_breadcrumbs',true);
                    if(vibe_validate($breadcrumbs)){
                     vibe_breadcrumbs();
                    }
                ?>
            </div>
        </div>
    </div>
</section>
<?php
}

?>
<section id="content">
    <div class="container">
        <div class="row">
            <div class="content padding_adjusted">
                <?php
                   
					
				    if ( !empty( $user_query->results ) ) {
				        foreach ( $user_query->results as $user ) {
				             ?>
				             	<div class="col-md-4 col-sm-4 clear3">
				             		<div class="instructor">
										<?php echo bp_core_fetch_avatar( array( 'item_id' => $user->ID,'type'=>'full', 'html' => true ) ); ?>
										<span><?php echo bp_get_profile_field_data( 'field='.$ifield.'&user_id=' .$user->ID ); ?></span>
										<h3><?php echo bp_core_get_userlink( $user->ID ); ?></h3>
										<strong><a href="<?php echo get_author_posts_url( $user->ID ); ?>"><?php _e('Courses by Instructor ','vibe'); echo  '<span>'.count_user_posts_by_type($user->ID,'course').'</span></a>'; ?></strong>
									</div>
				             		<?php
				             			
				             		?>
				             	</div>

				             <?php
				        }
				    }else {
					 echo '<div id="message"><p>'.__('No Instructors found.','vibe').'</p></div>';
					}
                 ?>
            </div>
        </div>
    </div>
</section>
<?php

?>
</div>
<?php
get_footer();

?>