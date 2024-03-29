<?php
get_header();
?>
<section id="title">
	<div class="container">
		<div class="row">
            <div class="col-md-9 col-sm8">
                <div class="pagetitle">
                    <h1><?php

                    if(is_month()){
                        single_month_title(' ');
                    }elseif(is_year()){
                        echo get_the_time('Y');
                    }else{
                        post_type_archive_title();
                    }
                     ?></h1>
                    <h5><?php 
                    $td=term_description();
                        if(isset($td) && $td !='')
                            echo term_description(); 
                        else {
                           _e('All Events in ','vibe'); echo get_bloginfo('name');
                        }  
                    ?></h5>
                </div>
            </div>
            <div class="col-md-3 col-sm-4">
                <?php vibe_breadcrumbs(); ?>  
            </div>
        </div>
	</div>
</section>
<section id="content">
	<div class="container">
        <div class="row">
    		<div class="col-md-9 col-sm-8">
    			<div class="content">
    				<?php
                        $events_interface = new WPLMS_Events_Interface;
                        $events_interface->wplms_event_calendar();
                    ?>
    			</div>
    		</div>
    		<div class="col-md-3 col-sm-4">
                <?php posts_nav_link(); ?>
    			<div class="sidebar">
    				<?php 
                    $sidebar = vibe_get_option('events_sidebar');
                        ((isset($sidebar) && $sidebar)?$sidebar:$sidebar='mainsidebar');
                        if ( !function_exists('dynamic_sidebar')|| !dynamic_sidebar($sidebar) ) : ?>
                       <?php endif; ?>
    			</div>
    		</div>
        </div>
	</div>
</section>

<?php
get_footer();
?>