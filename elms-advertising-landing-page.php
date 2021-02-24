<?php

/*
Plugin Name: Elms Advertising Landing Pages
Description: Controls the advertising landing page content type. This is a full-width (no sidebar), Gutenberg-editor enabled content type whose pages use the /lp/ URL prefix.
Version: 1.0
Author: Ryan Millner <millnerr@elms.edu>
*/

/**
 * Define the plugin version
 */
define("Elms Advertising Landing Pages", "1.0");

  //we only want the advertising landing page  subsite
  if ( $GLOBALS[ "blog_id" ] == 1 ) {
    function cptui_register_my_cpts_lp() {
      /**
       * Post Type: Advertising Landing Page.
       */
      $labels = [
        "name" => __( "Advertising Landing Page", "gs_elms" ),
        "singular_name" => __( "Advertising Landing Page", "gs_elms" ),
      ];
      $args = [
        "label" => __( "Advertising Landing Page", "gs_elms" ),
        "labels" => $labels,
        "description" => "",
        "public" => true,
        "publicly_queryable" => true,
        "show_ui" => true,
        "delete_with_user" => false,
        "show_in_rest" => true,
        "rest_base" => "",
        "rest_controller_class" => "WP_REST_Posts_Controller",
        "has_archive" => false,
        "show_in_menu" => true,
        "show_in_nav_menus" => true,
        "delete_with_user" => false,
        "exclude_from_search" => true,
        "capability_type" => "page",
        "map_meta_cap" => true,
        "hierarchical" => false,
        "rewrite" => [ "slug" => "lp", "with_front" => true ],
        "query_var" => true,
        "menu_icon" => "dashicons-admin-page",
        "supports" => [ "title", "editor" ],
      ];
      register_post_type( "lp", $args );
    }
    add_action( 'init', 'cptui_register_my_cpts_lp' );
  }

?>