<?php
/**
 * 	Template Name: Bargraph page
 * 	Description: Page based on original PHP website
 */
$stylesheet_dir = get_stylesheet_directory_uri();

wp_enqueue_style("correlate", $stylesheet_dir . "/css/bargraph.css");
wp_enqueue_style("shared-styles", $stylesheet_dir . "/css/_shared_styles.css");
wp_enqueue_style("jquery-ui-flick", $stylesheet_dir . "/css/jquery-ui-flick.css");
wp_enqueue_style("jquery-fancybox", $stylesheet_dir . "/css/jquery.fancybox.css");
wp_enqueue_style("jquery-dropdown", $stylesheet_dir . "/css/jquery.dropdown.css");
wp_enqueue_style("jquery-tip", $stylesheet_dir . "/css/simpletip.css");
wp_enqueue_style("jquery-datetimepicker", $stylesheet_dir . "/css/jquery.datetimepicker.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-dialog");
wp_enqueue_script("jquery-ui-datepicker");
wp_enqueue_script("jquery-ui-button");
wp_enqueue_script("jquery-ui-sortable");
wp_enqueue_script("jquery-ui-autocomplete");
wp_enqueue_script("jquery-dropdown", $stylesheet_dir . "/js/libs/jquery.dropdown.min.js", "jquery");
wp_enqueue_script("jquery-datetimepicker", $stylesheet_dir . "/js/libs/jquery.datetimepicker.js", "jquery");
wp_enqueue_script("jquery-touch", $stylesheet_dir . "/js/libs/jquery.ui.touch-punch.min.js", "jquery");
wp_enqueue_script("jquery-fancybox", $stylesheet_dir . "/js/libs/jquery.fancybox.pack.js", "jquery");
wp_enqueue_script("qm-math", $stylesheet_dir . "/js/math.js", "jquery", false, true);
wp_enqueue_script("timezone", $stylesheet_dir . "/js/jstz.min.js", "jquery", false, true);
wp_enqueue_script("qm-sdk", $stylesheet_dir . "/js/libs/quantimodo-api.js", "jquery", false, true);
wp_enqueue_script("jquery-simpletip", $stylesheet_dir . "/js/libs/jquery.simpletip-1.3.1.js", "jquery", false, true);
wp_enqueue_script("highcharts", $stylesheet_dir . "/js/libs/highstock.js", "jquery", false, true);
wp_enqueue_script("highcharts-more", $stylesheet_dir . "/js/libs/highcharts-more.js", "highcharts", false, true);
wp_enqueue_script("correlate-charts", $stylesheet_dir . "/js/bargraph_charts.js", array("highcharts-more", "qm-sdk", "qm-math"), false, true);

wp_enqueue_script("other-shared", $stylesheet_dir . "/js/_other_shared.js", array("jquery"), false, true);
wp_enqueue_script("variable-settings", $stylesheet_dir . "/js/_variable_settings.js", array("jquery"), false, true);
wp_enqueue_script("refresh-shared", $stylesheet_dir . "/js/_data_refresh.js", array("jquery"), false, true);
wp_enqueue_script("variable-picker", $stylesheet_dir . "/js/_variable_picker.js", array("jquery"), false, true);

wp_enqueue_script("correlate", $stylesheet_dir . "/js/bargraph.js", array("correlate-charts", "jquery-ui-datepicker", "jquery-ui-button"), false, true);

get_header();
?>

<?php if (!is_user_logged_in()): ?>
    <div class="dialog-background" id="login-dialog-background"></div>
    <div class="dialog" id="login-dialog">
        <?php login_with_ajax(); ?>
    </div>
<?php endif; ?>


<?php require "modules/dialog_add_measurement.php"; ?>
<?php require "modules/dialog_delete_measurements.php"; ?>
<?php require "modules/dialog_share.php"; ?>
<?php require "modules/variable_settings.php"; ?>

<section id="content">
    <div id="buddypress">
        <div class="container">
            <div class="row">

                <!-- BuddyPress profile menu -->
                <div class="col-md-2 col-sm-3">
                    <?php do_action('bp_before_member_home_content'); ?>
                    <div class="pagetitle">
                        <div id="item-header" role="complementary">
                            <?php locate_template(array('members/single/member-header.php'), true); ?>

                        </div><!-- #item-header -->
                    </div>
                    <div id="item-nav" class="">
                        <div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
                            <ul>
                                <?php bp_get_displayed_user_nav(); ?>

                                <?php do_action('bp_member_options_nav'); ?>

                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Content -->
                <div class="col-md-10 col-sm-9">
                    <section id="section-configure">
                       <?php include 'modules/variable_picker.php'; ?>
                    </section>

                    <section id="section-bargraph">
                        <div id="bar-graph">
                            <header class="card-header">
                                <h3 class="heading">
                                    <span>Correlations</span>
                                    <div id="gauge-timeline-settingsicon" data-dropdown="#dropdown-barchart-settings" class="gear-icon"></div>
                                    <div class="icon-question-sign icon-large questionMark"></div>
                                </h3>
                            </header>
                            <div class="graph-content">
                                <div style="width:100%;text-align: center;">
                                    <img src=" <?php echo $stylesheet_dir; ?>/css/images/brainloading.gif" class="barloading" style="margin-top: 20px; display:none" />
                                </div>
                                <span class="no-data" style="display:none"> <br />  <center> No data found </center> <br /><br /></span>
                                <div id="graph-bar"></div>
                                <input type="hidden"  id="selectBargraphInputVariable" value="" />
                                <input type="hidden"  id="selectBargraphInputCategory" value="" />
                            </div>
                        </div>                        
                    </section>


                </div>
            </div>

            <div id="section-analyze" style="display: none;">
                <div class="open" id="correlation-gauge">
                    <div class="inner">
                        <header id="card-header-detail" class="card-header">
                            <h3 class="heading">
                                <span>Details</span>
                            </h3>
                        </header>
                        <div id="gauge-graph-content" class="graph-content" style="width: 100%; overflow: hidden;">
                            <div style="float: left;  width: 360px; margin-top: -10px; margin-left: 10px; height:400px;" id="gauge-correlation"></div>
                            <div style="overflow: hidden; height: 70%; padding-left: 5px;">
                                <table style="height: 100%;">
                                    <tr>
                                        <td>
                                            <strong>Statistical Relationship</strong>
                                        </td>
                                        <td id="statisticalRelationshipValue">
                                            Significant
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Effect Size</strong>
                                        </td>
                                        <td id="effectSizeValue">
                                            Large
                                        </td>
                                    </tr>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="open" id="scatterplot-graph" >
                    <div class="inner">                               
                        <header class="card-header">
                            <h3 class="heading">
                                <span>Scatterplot</span>
                                <div id="graph-scatterplot-settingsicon" data-dropdown="#dropdown-scatterplot-settings" class="gear-icon"></div>
                                <div class="icon-plus-sign icon-2x plusMark" title="Click to add a measurement."></div>
                                <div class="icon-question-sign icon-large questionMark questionMarkPlus"></div>
                            </h3>
                        </header>                                   


                        <div class="graph-content" id="graph-scatterplot"></div>             

                    </div>
                </div>                        
                <div id="timeline-graph">                            
                    <header class="card-header">
                        <h3 class="heading">
                            <span>Timeline</span>
                            <div id="gauge-timeline-settingsicon" data-dropdown="#dropdown-timeline-settings" class="gear-icon"></div>              
                            <div class="icon-plus-sign icon-2x plusMark" title="Click to add a measurement." ></div>
                            <div class="icon-question-sign icon-large questionMark questionMarkPlus"></div>                                    
                        </h3>
                    </header>                            
                    <div class="graph-content" id="graph-timeline"></div>
                </div>

            </div>


            <!-- Menu for correlation gauge settings -->
            <div id="dropdown-gauge-settings" class="dropdown dropdown-tip">
                <ul class="dropdown-menu">
                    <li><a id="shareCorrelationGauge">Share graph</a></li>
                </ul>
            </div>
            <!-- Menu for timeline settings -->
            <div id="dropdown-timeline-settings" class="dropdown dropdown-tip dropdown-anchor-right">
                <ul class="dropdown-menu">
                    <li><label><input name="tl-enable-markers" type="checkbox" /> Show markers</label></li>
                    <li><label><input name="tl-smooth-graph" type="checkbox" /> Smoothen graph</label></li>
                    <li><label><input name="tl-enable-horizontal-guides" type="checkbox" /> Show horizontal guides</label></li>
                    <li class="dropdown-divider"></li>
                    <li><a id="shareTimeline">Share graph</a></li>
                </ul>
            </div>

            <!-- Menu for timeline settings -->
            <div id="dropdown-scatterplot-settings" class="dropdown dropdown-tip dropdown-anchor-right">
                <ul class="dropdown-menu">
                    <li><label><input name="sp-show-linear-regression" type="checkbox" /> Show linear regression</label></li>
                    <li class="dropdown-divider"></li>
                    <li><a id="shareScatterplot" >Share graph</a></li>
                </ul>
            </div>

            <!-- Menu for barchart settings -->
            <div id="dropdown-barchart-settings" class="dropdown dropdown-tip dropdown-anchor-right">
                <ul class="dropdown-menu">
                    <li><a id="" onclick="sortByCorrelation()">Sort By Correlation</a></li>		
                    <li><a id="shareScatterplot"  onclick="sortByCausality()">Sort By Causality Factor</a></li>
                    <li  style="padding:3px 15px;"><input type="text" id="minimumNumberOfSamples" placeholder="Min. Number of Samples"></li>
                </ul>
            </div>

        </div>
    </div>
</section>

<?php
get_footer('buddypress');
?>