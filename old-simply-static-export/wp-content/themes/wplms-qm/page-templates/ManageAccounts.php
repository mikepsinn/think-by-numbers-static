<?php
/**
 *	Template Name: Manage Accounts Page
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

wp_enqueue_style("manageaccounts", get_stylesheet_directory_uri()."/css/manageaccounts.css");
wp_enqueue_style("jquery-ui-flick", get_stylesheet_directory_uri()."/css/jquery-ui-flick.css");
wp_enqueue_style("jquery-dropdown", get_stylesheet_directory_uri()."/css/jquery.dropdown.css");

wp_enqueue_script("jquery", true);
wp_enqueue_script("jquery-ui-core");
wp_enqueue_script("jquery-ui-dialog");
wp_enqueue_script("jquery-ui-datepicker");
wp_enqueue_script("jquery-ui-button");
wp_enqueue_script("jquery-ui-sortable");
wp_enqueue_script("jquery-dropdown",get_stylesheet_directory_uri()."/js/libs/jquery.dropdown.min.js", "jquery");
wp_enqueue_script("jquery-touch",get_stylesheet_directory_uri()."/js/libs/jquery.ui.touch-punch.min.js", "jquery");
wp_enqueue_script("mustache",get_stylesheet_directory_uri()."/js/libs/mustache.js");
wp_enqueue_script("qm-sdk", get_stylesheet_directory_uri()."/js/libs/quantimodo-api.js", "jquery", false, true);

wp_enqueue_script("manageaccounts", get_stylesheet_directory_uri()."/js/manageaccounts.js", "qm-sdk", false, true);

get_header();
?>

<?php if(!is_user_logged_in()): ?>
<div class="dialog" id="login-dialog">
	<?php login_with_ajax(); ?>
</div>
<?php endif; ?>
<div class="dialog-background" id="login-dialog-background"></div>
<div class="connectordialog-background" id="update-dialog-background"></div>
<div id="content">

	<div class="modal-body">
	
		<div id="connectorInfoTable">
			<script type="text/html" id="connectorsTemplate">
					{{#connectors}}
						<div class="connectorBlock" id="connector-{{connectorName}}">	
							<div class="connectors" style="text-align: center; vertical-align: middle;" id="connectorName-{{connectorName}}">
								<img class="connectorLogo {{^apiKeyId}} grayout{{/apiKeyId}}" src="{{image}}" alt="{{name}}" {{^apiKeyId}}style="filter: grayscale(100%);-webkit-filter: grayscale(100%);filter: gray;-webkit-transition: all .6s ease;"{{/apiKeyId}}>
								<span class="connectorName">{{name}}</span>
							</div>
							
						</div>

						<div class="connectordialog" id="showDialog-{{connectorName}}">
							<a href="#" id="closeDialog-{{connectorName}}" class="closeDialog"><img src="http://i.imgur.com/5TBP9VO.png" class="closebutton"></a>
						
						<div style="float: left;width: 25%;"  {{#syncing}}class="nowSynchro"{{/syncing}} id="synStatus-{{connectorName}}">
															

							{{#apiKeyId}}
							<div class="syncStatus" style="background:inherit;height: 40px;">
                                <span id="syncLED-{{connectorName}}" title="{{auditTrail}}" class="syncLED-{{^errors}}yes{{/errors}}{{#errors}}no{{/errors}}" {{#syncing}}style="display:none;"{{/syncing}}></span>
                            </div>
							{{/apiKeyId}}

							<img class="connectorLogo" src="{{image}}" alt="{{name}}">
							<br>
							{{#apiKeyId}}
								<div class="remove">
									<button class="remove-button" id="remove-{{connectorName}}" style="padding: 5px 25px;">Delete</button>	
								</div>  	
													
							{{/apiKeyId}}

						</div>

						<div class="connectorDialog-desc">
							<span class="dialogconnectorName">{{name}}</span>
							{{text}}

							{{#apiKeyId}}
									<table class="connectorInfoTable">
									<tr class="tabletr">
										<td class="bold">Last Sync</td>
										<td id="connectorDialog-lastSync-{{connectorName}}">{{^syncing}}{{lastSync}} {{/syncing}}{{#syncing}}Now synchronizing{{/syncing}}</td>
									</tr>
									<tr class="tabletr">
										<td class="bold">Latest Data</td>
										<td id="connectorDialog-latestData-{{connectorName}}">{{latestData}}</td>
									</tr>
									</table>
									<div style="text-align:left">
									<a id="syncNow-{{connectorName}}" title="sync now" href="#" {{#syncing}}style="display:none;"{{/syncing}}><i class="icon-refresh"></i> Sync Now</a><br>
									<a href="#" id="viewUpdates-{{connectorName}}"><i class="icon-table"></i> View Updates </a>
									</div>	
							{{/apiKeyId}}
 
							{{^apiKeyId}}
								 <div class="auth" id="auth-{{connectorName}}"></div>
							{{/apiKeyId}}

						</div>

					</div>
			{{/connectors}}
					</script>
		</div>
</div>


<div id="viewUpdatesModal" class="updatedialog">
	 <a href="#" id="updatedialog-close"><img src="http://i.imgur.com/5TBP9VO.png" class="closebutton"> </a>
	<div id="viewUpdatesDialog">
	<script type="text/html" id="viewUpdates">
		<div class="viewUpdatesModal-header">
			<h3>Last Updates</h3>
		</div>

		<div class="viewUpdatesModal-body">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Success</th>
						<th style="width: 150px">Time</th>
						<th>Elapsed</th>
						<th>Call Query</th>
					</tr>
				</thead>
				<tbody>
					{{#updates}}
					<tr>
						<td class="syncStatus small"><span
							class="syncLED-{{^success}}no{{/success}}{{#success}}yes{{/success}}"></span>
						</td>
						<td>{{time}}</td>
						<td>{{elapsed}} ms</td>
						<td><a href="{{query}}">{{query}}</a></td>
					</tr>
					{{/updates}}
				</tbody>
			</table>
		</div>
		<div class="viewUpdatesModal-footer">
			<button class="btn" onclick="$('#viewUpdatesModal').modal('hide')">Close</button>
		</div>
	</script>
	</div>
</div>


	<div class="modal-footer">
		<div class="synchAll">
			<a id="sync-all" href="#" class="btn btn-info">sync all your devices
				now <i class="icon-refresh"></i>
			</a>
		</div>
	</div>
</div>

<?php
get_footer();
?>

