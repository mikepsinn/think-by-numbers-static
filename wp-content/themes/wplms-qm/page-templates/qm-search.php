<?php
/**
 *  Template Name: Search
 *  Description: Search for correlations.  Planned for landing page.
 */
add_action('admin_enqueue_scripts', 'queue_my_admin_scripts');

function queue_my_admin_scripts() {
    wp_enqueue_script('my-spiffy-miodal', // handle
            URL_TO_THE_JS_FILE, // source
            array('jquery-ui-dialog')); // dependencies
    // A style available in WP
    wp_enqueue_style('wp-jquery-ui-dialog');
}

$stylesheet_dir = get_stylesheet_directory_uri();

wp_enqueue_style("qm-search", $stylesheet_dir . "/css/qm-search.css");
wp_enqueue_style("shared-styles", $stylesheet_dir . "/css/_shared_styles.css");
wp_enqueue_style("jquery-ui-flick", $stylesheet_dir . "/css/jquery-ui-flick.css");
wp_enqueue_style("jquery-dropdown", $stylesheet_dir . "/css/jquery.dropdown.css");
wp_enqueue_style("jquery-fancybox", $stylesheet_dir . "/css/jquery.fancybox.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-dialog");
wp_enqueue_script("jquery-ui-autocomplete");
wp_enqueue_script("jquery-dropdown", $stylesheet_dir . "/js/libs/jquery.dropdown.min.js", "jquery");
wp_enqueue_script("jquery-fancybox", $stylesheet_dir . "/js/libs/jquery.fancybox.pack.js", "jquery");
wp_enqueue_script("mustache", $stylesheet_dir . "/js/libs/mustache.js");
wp_enqueue_script("qm-sdk", $stylesheet_dir . "/js/libs/quantimodo-api.js", "jquery", false, true);

wp_enqueue_script("qm-search", $stylesheet_dir . "/js/qm-search.js", "qm-sdk", false, true);

get_header();
?>


<br><br>
<center>
    <img src="http://i.imgur.com/KphSCds.png" width="614.14px" height="129.6px">
    <div>
        <form id="varSearchform" action="#" method="get" role="search">
            <input type="hidden" value="course" name="post_type">

            <p>
                <div class="optionsContainer">
                    What &nbsp;&nbsp;
                <input type="radio" name="selectOutputAsType" value="effect" checked="checked">affects  &nbsp;&nbsp;
                <input type="radio" name="selectOutputAsType" value="cause">are the effects of          
                </div>
            </p>    
            <p>
            <div class="fieldsContainer">
            <input id="searchVariable" type="text" placeholder="Enter a medication, food supplement or anything else...">
            <p></p>
            <br>
            <input id="searchsubmit" type="submit" value="Ask QuantiModo">
            </div>
            <ul id="variablePickerList">
				<!-- Variables go here -->
			</ul>
            </p>    
           

        </form>
    </div>
</center>
<?php
get_footer();
?>
