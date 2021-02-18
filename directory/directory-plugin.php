<?php

/*
Plugin Name: Elms Directory Plugin
Description: Controls the customizations used by the Elms College directory (elms.edu/directory/) page
Version: 1.0
Author: Ryan Millner <millnerr@elms.edu>
*/


/**
 * Define the plugin version
 */
define("Directory Plugin Functions", "1.0");

/* register the personnel content type */
function cptui_register_my_cpts_personnel() {

	/**
	 * Post Type: Personnel.
	 */

	$labels = array(
		"name" => __( "Personnel", "gs_elms" ),
		"singular_name" => __( "Personnel", "gs_elms" ),
	);

	$args = array(
		"label" => __( "Personnel", "gs_elms" ),
		"labels" => $labels,
		"description" => "Used for the individual faculty pages, the department faculty pages and the campus-wide directory page.",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"delete_with_user" => false,
		"show_in_rest" => false,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "personnel", "with_front" => true ),
		"query_var" => true,
		"supports" => array( "title", "editor", "excerpt", "revisions", "page-attributes", "thumbnail" ),
	);

	register_post_type( "personnel", $args );
}

add_action( 'init', 'cptui_register_my_cpts_personnel' );

/* register the department taxonomy */
function cptui_register_my_taxes_department() {

	/**
	 * Taxonomy: Department.
	 */

	$labels = array(
		"name" => __( "Department", "gs_elms" ),
		"singular_name" => __( "Department", "gs_elms" ),
		"parent_item_colon" => __( "Department", "gs_elms" ),
	);

	$args = array(
		"label" => __( "Department", "gs_elms" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'department', 'with_front' => true, ),
		"show_admin_column" => false,
		"show_in_rest" => true,
		"rest_base" => "department",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => false,
		);
	register_taxonomy( "department", array( "personnel" ), $args );
}
add_action( 'init', 'cptui_register_my_taxes_department' );

/* register the division-school taxonomy */
function cptui_register_my_taxes_division() {

	/**
	 * Taxonomy: Division.
	 */

	$labels = array(
		"name" => __( "Division", "gs_elms" ),
		"singular_name" => __( "Division", "gs_elms" ),
	);

	$args = array(
		"label" => __( "Division", "gs_elms" ),
		"labels" => $labels,
		"public" => true,
		"publicly_queryable" => true,
		"hierarchical" => false,
		"show_ui" => true,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"query_var" => true,
		"rewrite" => array( 'slug' => 'division', 'with_front' => true, ),
		"show_admin_column" => false,
		"show_in_rest" => true,
		"rest_base" => "division",
		"rest_controller_class" => "WP_REST_Terms_Controller",
		"show_in_quick_edit" => true,
		);
	register_taxonomy( "division", array( "personnel" ), $args );
}
add_action( 'init', 'cptui_register_my_taxes_division' );


/* Below are all the custom functions that make the directory work */

function personnel_automatic_fields(){
?>
	<script>
		jQuery(document).ready( function () {
			jQuery(".post-type-personnel #excerpt").prop("readonly","true");
			jQuery("#title-prompt-text").text("");
			jQuery(".post-type-personnel #title").css("background-color","#eee").prop({
				readonly:true,
				placeholder:"Title will be automatically generated"
			})
			jQuery(".titleSource input").focusout(function(e){
				var titleValue = "";
				jQuery(".titleSource input").each(function(index) {
					//accreditation field
					if( (jQuery(this).attr("id") == "acf-field_584ffc1c3cf6a") && jQuery(this).val() ){
						titleValue = titleValue + ", " + jQuery(this).val();
					//prefix field
					}else if ( (jQuery(this).attr("id") == "acf-field_5c48c0c896201") && jQuery(this).val() ){
						titleValue = titleValue + jQuery(this).val() + " " ;
					//first name field
					}else if ( jQuery(this).attr("id") == "acf-field_584ffbd83cf68" ) {
						titleValue = titleValue + jQuery(this).val() + " " ;
					}else{
						titleValue = titleValue + jQuery(this).val();
					}
				});
				jQuery(".post-type-personnel #title").val(titleValue);
			});

			jQuery(".excerptSource input").focusout(function(e){
				var jobTitleValue = jQuery("#jobTitle input").val();
				var emailValue = jQuery("#email input").val();
				var emailSuffixValue = jQuery("#emailSuffix input:checked").val();
				var emailValueLink = "<a href='mailto:" + emailValue + emailSuffixValue +"'>" + emailValue + emailSuffixValue + "</a>";
				var phoneValue = jQuery("#phone input").val();
				var phoneValueLink = "<a href='tel:" + phoneValue + "'>" + phoneValue + "</a>";
				var departmentsPrintedVariable = "";
				var departmentsValueLinks = [];
				jQuery("#department select option:selected").each(function(){
					departmentsValueLinks.push(" <a href='/department/" + jQuery(this).text().replace(/\s+/g, '-').replace("'", "").toLowerCase() + "/'>" + jQuery(this).text() + "</a>");
				});
				if(departmentsValueLinks.length === 0){
					var departmentsPrintedVariable = "";
				}else{
					var departmentsPrintedVariable = " <br />Department(s):" + departmentsValueLinks;
				}
				if(phoneValue == ""){
					jQuery(".post-type-personnel #excerpt").val(jobTitleValue + " <br />" + emailValueLink + departmentsPrintedVariable);
				}else{
					jQuery(".post-type-personnel #excerpt").val(jobTitleValue + " <br />" + emailValueLink + " | " + phoneValueLink + departmentsPrintedVariable);
				}
			});

		});
	</script>
<?php
}
add_action('admin_head', 'personnel_automatic_fields');

function override_the_a_z_index_letter( $indices, $item ) {
    if ( 'personnel' !== $item->post_type ) {
        return $indices;
    }
    $meta = get_post_meta( $item->ID, 'last_name', true );
    $idx = mb_substr( $meta, 0, 1, 'UTF-8' );
    // replace the current indices - if you just want to add it as an additional index
    // add the value to a new item in $indices rather than overwriting $indices entirely
    $indices = array(
        $idx => array(
            array(
                'title' => $item->post_name,
                // or use the value in $meta - this is shown as the post title in the listing
                'item' => $item,
            ),
        ),
    );
    // I really need to improve this so you don't need to add all that boiler plate
    // but instead just return the indices. For the future.
    return $indices;
}
add_filter( 'a_z_listing_item_indices', 'override_the_a_z_index_letter', 10, 2 );


function slug_save_post_callback( $post_ID, $post, $update ) {
    // allow 'publish', 'draft', 'future'
    if ($post->post_type != 'personnel' || $post->post_status == 'auto-draft')
        return;

    // only change slug when the post is created (both dates are equal)
    if ($post->post_date_gmt != $post->post_modified_gmt)
        return;

    // use title, since $post->post_name might have unique numbers added
    $new_slug = sanitize_title( $_POST['acf']['field_584ffcb13cf6f'], $post_ID );

    if ($new_slug == $post->post_name)
        return; // already set

    // unhook this function to prevent infinite looping
    remove_action( 'save_post', 'slug_save_post_callback', 10, 3 );
    // update the post slug (WP handles unique post slug)
    wp_update_post( array(
        'ID' => $post_ID,
        'post_name' => $new_slug
    ));
    // re-hook this function
    add_action( 'save_post', 'slug_save_post_callback', 10, 3 );
}
add_action( 'save_post', 'slug_save_post_callback', 10, 3 );

// filter for tags (as a taxonomy) with comma
//  replace '--' with ', ' in the output - allow tags with comma this way
if( !is_admin() ) { // make sure the filters are only called in the frontend
    $custom_taxonomy_type = 'division'; // here goes your taxonomy type
    
    function comma_taxonomy_filter( $tag_arr ){
        global $custom_taxonomy_type;
        $tag_arr_new = $tag_arr;
        if( $tag_arr->taxonomy == $custom_taxonomy_type && strpos( $tag_arr->name, '--' ) ){
            $tag_arr_new->name = str_replace( '--' , ', ', $tag_arr->name);
        }
        return $tag_arr_new;    
    }
    add_filter( 'get_' . $custom_taxonomy_type, 'comma_taxonomy_filter' );
    
    function comma_taxonomies_filter( $tags_arr ) {
        $tags_arr_new = array();
        foreach( $tags_arr as $tag_arr ) {
            $tags_arr_new[] = comma_taxonomy_filter( $tag_arr );
        }
        return $tags_arr_new;
    }
    add_filter( 'get_the_taxonomies', 'comma_taxonomies_filter' );
    add_filter( 'get_terms', 'comma_taxonomies_filter' );
    add_filter( 'get_the_terms', 'comma_taxonomies_filter' );
}


?>