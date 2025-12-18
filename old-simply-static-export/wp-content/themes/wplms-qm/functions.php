<?php
add_action('bp_group_header_actions', 'bp_group_join_button', 5 );	// Enable the "Join Group" button
add_action('wp_head', 'setLoggedInVariable');						// Sets a JavaScript variable that indicates whether a user is logged in or not
add_action('wp_head', 'setDisplayedUser');							// Sets a JavaScript variable to indicate the displayed user's id
add_filter('wp_nav_menu_items', 'user_menu_item', 10, 2);			// Add a "You" menu item that points to the analyze, correlate and connect pages

// Update wp_unique_id table when new user or group is created
add_action('user_register', 'add_id_users', 1);
add_action('groups_create_group', 'add_id_groups', 1);

add_action('deleted_user', 'remove_id_users', 100);
add_action('groups_delete_group', 'remove_id_groups', 100);


/*
** show admin bar only for users who have Administrator role
*/
if (!current_user_can( 'manage_options' )) 
{
	add_filter('show_admin_bar', '__return_false'); 
}

/*
**	Sets a JavaScript variable to indicate the displayed user's id
*/
function setDisplayedUser()
{
	global $bp, $wpdb;
	$user_id = $bp->displayed_user->id;
	if ($user_id != null)
	{
		$table_name = $wpdb->prefix . 'unique_id';
		$unique_id = $wpdb->get_var("SELECT `uniqueId` FROM `$table_name` WHERE `wpId` = $user_id AND `wpType` = 'user'");	
	}
	else
	{
		$unique_id = 'null';
	}

	$isCurrentUser = $bp->loggedin_user->id == $bp->displayed_user->id ? 'true' : 'false';
	echo 	"<script>
				var displayedUser = {$unique_id};
				var isCurrentUser = {$isCurrentUser};
			</script>";
}

/*
**	Sets a JavaScript variable to indicate the user is logged in
*/
function setLoggedInVariable()
{
	if(is_user_logged_in())
	{
		echo "<script> var isLoggedIn = true </script>";
	}
	else
	{
		echo "<script> var isLoggedIn = false </script>";	
	}
}

/*
**	Adds a "You" menu item to the main-menu
*/
function user_menu_item($items, $args) 
{
	if(is_user_logged_in() && $args->theme_location == "main-menu")
	{
		$user_domain = bp_loggedin_user_domain();		

		$items = '
		<li class="menu-item menu-item-type-post_type menu-item-object-page menu-item-has-children hasmenu">
			<a href="' . $user_domain . '"><strong>You</strong></a>
			<ul class="sub-menu">
				<li class="menu-item menu-item-type-post_type menu-item-object-page">
					<a href="' . $user_domain . 'analyze/">Analyze</a>
				</li>
				<li class="menu-item menu-item-type-post_type menu-item-object-page">
					<a href="' . $user_domain . 'correlate/">Correlate</a>
				</li>
				<li class="menu-item menu-item-type-post_type menu-item-object-page">
					<a href="' . $user_domain . 'connect/">Connect</a>
				</li>
			</ul>
		</li>' . $items;
	}
	return $items;
}

/*
**	Adds custom menu items to the user's profile for the various QM specific pages
**	Hides the "Courses" link, we're not using that for the time being
*/
add_action('bp_setup_nav', 'setup_profile_nav', 100);
function setup_profile_nav() 
{
	// Remove course link
	bp_core_remove_nav_item('course');

	bp_core_new_nav_item( array( 
			'name' => __( 'Analyze', 'buddypress' ), 
			'slug' => 'analyze', 
			'position' => 0,
			'item_css_id' => 'importantMenuItem',
			'screen_function' => 'members_analyze_template'
		));
	bp_core_new_nav_item( array(
			'name' => __( 'Correlate', 'buddypress' ),
			'slug' => 'correlate',
			'position' => 0,
			'item_css_id' => 'importantMenuItem',
			'screen_function' => 'members_correlate_template' 
		));
	bp_core_new_nav_item( array(
			'name' => __( 'Connect', 'buddypress' ),
			'slug' => 'connect',
			'position' => 0,
			'item_css_id' => 'importantMenuItem',
			'screen_function' => 'members_connect_template' 
		 ));
}

/*
**	Adds QM specific menu items to a group's page
*/ 
add_action('bp_setup_nav', 'setup_group_nav', 100);
function setup_group_nav () 
{
	bp_core_new_subnav_item( array( 
		    'name' => __( 'Analyze', 'buddypress' ), 
		    'slug' => 'analyze',
			'parent_slug' => bp_get_current_group_slug(),
			'parent_url'  => bp_get_group_permalink( groups_get_current_group() ),
		    'screen_function' => 'groups_analyze_template',
			'item_css_id' => 'importantMenuItem',
		    'position' => 0
		));
	bp_core_new_subnav_item( array( 
		    'name' =>  __( 'Correlate', 'buddypress' ),
		    'slug' => 'correlate',
			'parent_slug' => bp_get_current_group_slug(),
			'parent_url'  => bp_get_group_permalink( groups_get_current_group() ),
		    'screen_function' => 'groups_correlate_template',
			'item_css_id' => 'importantMenuItem',
		    'position' => 0
		)); 
	bp_core_new_subnav_item( array( 
		    'name' =>  __( 'Connect', 'buddypress' ),
		    'slug' => 'connect',
			'parent_slug' => bp_get_current_group_slug(),
			'parent_url'  => bp_get_group_permalink( groups_get_current_group() ),
		    'screen_function' => 'groups_connect_template',
			'item_css_id' => 'importantMenuItem',
		    'position' => 0
		)); 
}

// Profile page templates
function members_analyze_template() {
	bp_core_load_template('page-templates/analyze_members');
}
function members_correlate_template() {
	bp_core_load_template('page-templates/BarGraph');
}
function members_connect_template() {
	bp_core_load_template('page-templates/ManageAccountsV2');
}

// Group page templates
function groups_analyze_template() {
	bp_core_load_template('page-templates/analyze_groups');
}

function groups_correlate_template() {
	bp_core_load_template('page-templates/BarGraph');
}

function groups_connect_template() {
	bp_core_load_template('page-templates/ManageAccountsV2');
}


/*
**	Update wp_unique_id table when new user or group is created / deleted
*/ 
function add_id_groups($id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'unique_id';
	$wpdb->query("INSERT INTO `$table_name` (`wpId`, `wpType`) VALUES ($id, 'group')");
}
function add_id_users($id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'unique_id';
	$wpdb->query("INSERT INTO `$table_name` (`wpId`, `wpType`) VALUES ($id, 'user')");
}

function remove_id_groups($id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'unique_id';
	$wpdb->query("UPDATE `$table_name` SET `active` = 0 WHERE `wpId` = $id AND `wpType` = 'group'");
}
function remove_id_users($id)
{
	global $wpdb;
	$table_name = $wpdb->prefix . 'unique_id';
	$wpdb->query("UPDATE `$table_name` SET `active` = 0 WHERE `wpId` = $id AND `wpType` = 'user'");
}