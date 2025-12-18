<?php
/**
 *	Template Name: Manage Accounts Page V2
 *	Description: Page to manage accounts on, what did you expect?
 */
add_action( 'admin_enqueue_scripts', 'queue_my_admin_scripts');

function queue_my_admin_scripts() {
	wp_enqueue_script (  'my-spiffy-miodal' ,       // handle
	URL_TO_THE_JS_FILE  ,       // source
	array('jquery-ui-dialog')); // dependencies
	// A style available in WP
	wp_enqueue_style (  'wp-jquery-ui-dialog');
}
$stylesheet_dir = get_stylesheet_directory_uri();

wp_enqueue_style("manageaccounts", $stylesheet_dir."/css/manageaccountsv2.css");
wp_enqueue_style("shared-styles", $stylesheet_dir . "/css/_shared_styles.css");
wp_enqueue_style("jquery-ui-flick", $stylesheet_dir."/css/jquery-ui-flick.css");
wp_enqueue_style("jquery-dropdown", $stylesheet_dir."/css/jquery.dropdown.css");
wp_enqueue_style("jquery-fancybox", $stylesheet_dir . "/css/jquery.fancybox.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-dialog");
wp_enqueue_script("jquery-dropdown",$stylesheet_dir."/js/libs/jquery.dropdown.min.js", "jquery");
wp_enqueue_script("jquery-fancybox", $stylesheet_dir . "/js/libs/jquery.fancybox.pack.js", "jquery");
wp_enqueue_script("mustache",$stylesheet_dir."/js/libs/mustache.js");
wp_enqueue_script("qm-sdk", $stylesheet_dir."/js/libs/quantimodo-api.js", "jquery", false, true);

wp_enqueue_script("manageaccounts", $stylesheet_dir."/js/manageaccountsv2.js", "qm-sdk", false, true);

get_header();
?>

<?php if(!is_user_logged_in()): ?>
<div
	class="dialog-background" id="login-dialog-background"></div>
<div class="dialog" id="login-dialog">
	<?php login_with_ajax(); ?>
</div>
<?php endif; ?>

<section id="content">
<div id="buddypress">
<div class="container">
<div class="row">

    <!-- BuddyPress profile menu -->
    <div class="col-md-2 col-sm-3">
        <?php do_action( 'bp_before_member_home_content' ); ?>
        <div class="pagetitle">
            <div id="item-header" role="complementary">
                <?php locate_template( array( 'members/single/member-header.php' ), true ); ?>

            </div><!-- #item-header -->
        </div>
        <div id="item-nav" class="">
            <div class="item-list-tabs no-ajax" id="object-nav" role="navigation">
                <ul>

                    <?php bp_get_displayed_user_nav(); ?>

                    <?php do_action( 'bp_member_options_nav' ); ?>

                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-10 col-sm-8">

		<div id="connectedConnectors">
			<header class="card-header">
	            <h3 class="heading">
	                <span>Connected</span>
	            </h3>
	        </header>
			<div class="connectorContainer">
				<!-- Connectors go here via template -->
			</div>
		</div>

		<div id="availableConnectors">
			<header class="card-header">
                <h3 class="heading">
                    <span>Available</span>
                </h3>
            </header>
			<div class="connectorContainer">
				<!-- Connectors go here via template -->
			</div>
		</div>
	</div>
</div>

<div style="display: none;">
	<table class="updates-table">
		<thead>
			<th>Status</th>
			<th>Time</th>
			<th>Measurements</th>
		</thead>
	</table>
</div>

</div>
</div>
</section>

<?php
    get_footer( 'buddypress' );
?>

<!-- Template for connectors -->
<script type="text/html" id="connectorsTemplate">
	{{#connectors}}
		<div class="connectorBlock" id="connector-{{name}}">	
			<div class="connectors" style="text-align: center; vertical-align: middle;" id="connectorName-{{name}}">
				<img class="connectorLogo {{^connected}}grayout{{/connected}}" src="{{image}}" alt="{{name}}" {{^connected}}style="filter: grayscale(100%);-webkit-filter: grayscale(100%);filter: gray;-webkit-transition: all .6s ease;"{{/connected}}>
				<h6>{{displayName}}</h6>
			</div>
		</div>

		<div class="connectorDialog" id="showDialog-{{name}}">
			<div class="connectNotificationContainer" id="connectNotificationContainer-{{name}}" style="height: 0px;">
			</div>
			<div style="float: left;width: 140px;">
				<div style="height: 150px;">
					<img class="connectorLogo" src="{{image}}" alt="{{name}}" style="width: 140px; height: 140px;">
					{{^connected}}
					  <img class="connectorStatus" src="https://i.imgur.com/tvNH2wA.png" height="30px" width="30px" style="position: relative; top: -40px; left: 110px;">
					{{/connected}}
					{{#connected}}
					  <img class="connectorStatus" src="https://i.imgur.com/Rvv8Ujo.png" height="30px" width="30px" style="position: relative; top: -40px; left: 110px;">
					{{/connected}}
				</div>
			{{#connected}}
				<div class="buttons">
					<button class="disconnect-button" id="update-{{name}}" style="display: block; width: 120px; margin: auto; margin-bottom: 5px;">Sync</button>
					<button class="disconnect-button" id="disconnect-{{name}}" style="display: block; width: 120px; margin: auto;">Disconnect</button>
				</div>  							
			{{/connected}}
		</div>
		<div class="connectorDialog-desc">{{text}}
			{{#connectInstructions}}
			<form id="connectform-{{name}}" style="{{#connected}}display:none{{/connected}}">
			{{#parameters}}
				<label for="{{name}}-{{key}}">{{displayName}}</label>
				<input type="{{type}}" name="{{key}}" id="{{name}}-{{key}}" placeholder="{{placeholder}}">{{defaultValue}}</input>
			{{/parameters}}
			<input type="submit" value="Connect" style="margin-top: 10px; float:right;">
			</form>
			{{/connectInstructions}}
			
                                         
                                        {{^noDataYet}}
			<table class="connectorInfoTable" style="{{^connected}}display:none{{/connected}}">
				<tr class="tabletr">
					<td class="bold">Last Sync</td>
					<td id="connectorDialog-lastUpdate-{{name}}">{{^syncing}}{{lastUpdate}} {{/syncing}}{{#syncing}}Now synchronizing{{/syncing}}</td>
				</tr>
				<tr class="tabletr">
					<td class="bold">Latest Data</td>
					<td id="connectorDialog-latestData-{{name}}">{{latestData}}</td>
				</tr>
			</table>
                                        {{/noDataYet}}                                                        
                                         
                                        {{#noDataYet}}
                                        <b>Retrieving Data Check back soon!</b>
                                        {{/noDataYet}}    
                                        
			{{#connected}} 
                                            {{^noDataYet}}
				<a class="view-updates-button" id="viewUpdates-{{name}}" href="#"><i class="icon-table"></i> View Updates</a>
                                            {{/noDataYet}}    
                                        {{/connected}}
		</div>
		</div>
	{{/connectors}}
</script>