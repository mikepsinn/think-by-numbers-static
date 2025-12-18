<?php
	$stylesheet_dir = get_stylesheet_directory_uri();
	
	wp_enqueue_style("analyze", $stylesheet_dir . "/css/analyze.css");
	wp_enqueue_style("shared-styles", $stylesheet_dir . "/css/_shared_styles.css");
	wp_enqueue_style("jquery-ui-flick", $stylesheet_dir . "/css/jquery-ui-flick.css");
	wp_enqueue_style("jquery-dropdown", $stylesheet_dir . "/css/jquery.dropdown.css");

	wp_enqueue_script("jquery", true);
	wp_enqueue_script("jquery-ui-core");
	wp_enqueue_script("jquery-ui-dialog");
	wp_enqueue_script("jquery-ui-datepicker");
	wp_enqueue_script("jquery-ui-button");
	wp_enqueue_script("jquery-ui-sortable");
	wp_enqueue_script("jquery-ui-menu");
	wp_enqueue_script("jquery-dropdown",$stylesheet_dir . "/js/libs/jquery.dropdown.min.js", "jquery");
	wp_enqueue_script("jquery-touch",$stylesheet_dir . "/js/libs/jquery.ui.touch-punch.min.js", "jquery");
	wp_enqueue_script("qm-math", $stylesheet_dir . "/js/math.js", "jquery", false, true);
	wp_enqueue_script("timezone", $stylesheet_dir . "/js/jstz.min.js", "jquery", false, true);
	wp_enqueue_script("qm-sdk", $stylesheet_dir . "/js/libs/quantimodo-api.js", "jquery", false, true);
	wp_enqueue_script("highcharts", "https://code.highcharts.com/stock/highstock.js", "jquery", false, true);
	wp_enqueue_script("highcharts-more", "https://code.highcharts.com/highcharts-more.js", "highcharts", false, true);
	wp_enqueue_script("analyze-charts", $stylesheet_dir . "/js/analyze_charts.js", array("highcharts-more", "qm-sdk", "qm-math"), false, true);

	wp_enqueue_script("other-shared", $stylesheet_dir . "/js/_other_shared.js", array("jquery"), false, true);
	wp_enqueue_script("variable-settings", $stylesheet_dir . "/js/_variable_settings.js", array("jquery"), false, true);
	wp_enqueue_script("refresh-shared", $stylesheet_dir . "/js/_data_refresh.js", array("jquery"), false, true);

	wp_enqueue_script("analyze", $stylesheet_dir . "/js/analyze.js", array("analyze-charts", "jquery-ui-datepicker", "jquery-ui-button"), false, true);
	
	get_header();
?>

<?php if(!is_user_logged_in()): ?>
<div class="dialog-background" id="login-dialog-background"></div>
<div class="dialog" id="login-dialog">
	<?php login_with_ajax(); ?>
</div>
<?php endif; ?>

<?php require "modules/dialog_delete_measurements.php"; ?>
<?php require "modules/dialog_share.php"; ?>
<?php require "modules/variable_settings.php"; ?>

<section id="content">
	<div id="buddypress">
		<div class="container">
			<div class="row">

				<!-- BuddyPress profile menu -->
				<div class="col-md-2 col-sm-3">
					<?php 
						if ( bp_has_groups() ) : while ( bp_groups() ) : bp_the_group();
						do_action( 'bp_before_group_home_content' ); 
					?>

					<div id="item-header" role="complementary">
						<?php locate_template( array( 'groups/single/group-header.php' ), true ); ?>
					</div><!-- #item-header -->
		
					<div id="item-nav">
						<div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
							<ul>
								<?php 
									bp_get_options_nav();
									do_action( 'bp_group_options_nav' ); 
								?>
							</ul>
						</div>
					</div><!-- #item-nav -->
				</div>

				<!-- Content -->
				<div class="col-md-10 col-sm-9">
					<?php do_action( 'template_notices' ); ?>
					<div>
						<?php 
							do_action( 'bp_before_group_body' );
							require 'analyze_shared.php';
							do_action( 'bp_after_group_body' );
						?>
					</div>
					<?php 
						do_action( 'bp_after_group_home_content' );
						endwhile; endif; 
					?>
				</div>

			</div>
		</div>
	</div>
</section>

<?php
	get_footer( 'buddypress' );
?>