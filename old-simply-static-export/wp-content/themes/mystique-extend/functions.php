<?php 

# Functions to change the display name to be full name after new user registration
# Ref: http://wordpress.org/support/topic/display-name-defaultalways-forceonly-allow-firstlast?replies=10#post-2073671
# Ref: http://pastebin.com/tQqC1BrW
class myUsers {
        static function init() {
                // Change the user's display name after insertion
                add_action( 'user_register', array( __CLASS__, 'change_display_name' ) );      
          		// add_action( 'admin_init', array( __CLASS__, 'change_display_name' ) );
        }
       
        static function change_display_name( $user_id ) {
                $info = get_userdata( $user_id );
               
                $args = array(
                        'ID' => $user_id,
                        'display_name' => $info->first_name . ' ' . $info->last_name
                );
               
                wp_update_user( $args ) ;
        }
}
 
myUsers::init();
