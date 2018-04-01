<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

add_action('init', __NAMESPACE__ . '\register_modal_types');
function register_modal_types() {
    register_post_type( Utils::get_posttype_name(), Utils::get_settings( 'post-type.php' ) );
}

add_action('admin_enqueue_scripts', __NAMESPACE__ . '\register_modal_admin_script');
function register_modal_admin_script() {
    $screen = get_current_screen();
    if( !empty($screen->post_type) && Utils::get_posttype_name() === $screen->post_type ) {
        wp_enqueue_script( 'admin_modal_post_type',
            Utils::get_plugin_url('/assets/admin-post-type.js'), array('jquery') );
    }
}

// $mb = new WP_Post_Boxes( array( Utils::get_posttype_name() ) );
// $mb->add_fields( array('_modal_type',)); //'_selector') );
// $mb->add_box( __( 'Advanced settings', DOMAIN ), __NAMESPACE__ . '\modal_post_metabox', $side = true );

// function modal_post_metabox() {
//     $form = new WP_Admin_Forms( Utils::get_settings('post_metabox.php'), true, $args = array(
//         'admin_page'  => false,
//         'postmeta' => true,
//     ) );

//     echo $form->render();
// }

$mb = new WP_Post_Boxes( array( Utils::get_posttype_name() ) );
$mb->add_fields( array('_trigger_type', '_disable_ontime') );
$mb->add_box( __( 'Event', DOMAIN ), __NAMESPACE__ . '\modal_post_metabox2', $side = true );

function modal_post_metabox2() {
    global $post;

    $form = new WP_Admin_Forms( Utils::get_settings('post_metabox2.php'), false, $args = array(
        'admin_page' => false,
        'postmeta' => true,
    ) );

    $actve = $form->get_active();
    $actve[ '_' . $actve['_trigger_type'] ] = get_post_meta( $post->ID, '_trigger', true );
    $form->set_active($actve);

    echo $form->render();
}

add_action( 'save_post', __NAMESPACE__ . '\save_trigger_field' );
function save_trigger_field( $post_id ) {
    if ( ! isset( $_POST['_wp_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['_wp_metabox_nonce'], WP_Post_Boxes::SECURITY ) )
        return $post_id;

    $trigger = '';
    if( !empty( $_POST['_shortcode'] ) ) $trigger = $_POST['_shortcode'];
    if( !empty( $_POST['_onclick'] ) )   $trigger = $_POST['_onclick'];
    if( !empty( $_POST['_onload'] ) )    $trigger = $_POST['_onload'];
    if( !empty( $_POST['_onclose'] ) )   $trigger = $_POST['_onclose'];

    if( $trigger ) {
        update_post_meta( $post_id, '_trigger', sanitize_text_field($trigger) );
    }
    else {
        delete_post_meta( $post_id, '_trigger' );
    }
}

// add_action( 'edit_form_after_title', __NAMESPACE__ . '\do_something_after_title', 10, 1 );
// function do_something_after_title( $post ) {
//     $scr = get_current_screen();
//     if ( $scr->post_type !== strtolower(Utils::get_option_name()) ) {
//         return;
//     }
//     echo '<input type="text" name="" class="widefat" value=\''.$sc.'\' onclick="select()">';
// }
