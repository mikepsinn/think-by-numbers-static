<?php
 /*
  Template Name: blogger
 */
 
 global $wpdb;
 $old_url = $_GET['q'];
 
 if ($old_url != "") {
   $permalink = explode("blogspot.com", $old_url);
 
   $q = "SELECT guid FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ". 
        "ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) WHERE ".
        "$wpdb->postmeta.meta_key='blogger_permalink' AND ".
        "$wpdb->postmeta.meta_value='$permalink[1]'";

  $new_url = $wpdb->get_var($q)? $wpdb->get_var($q) : "/";

  header ("HTTP/1.1 301 Moved Permanently");
  header("Location: $new_url");
 }
?>