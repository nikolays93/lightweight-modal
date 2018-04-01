<?php

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

// add_action( 'edit_form_after_title', __NAMESPACE__ . '\do_something_after_title', 10, 1 );
// function do_something_after_title( $post ) {
//     $scr = get_current_screen();
//     if ( $scr->post_type !== strtolower(Utils::get_option_name()) ) {
//         return;
//     }
//     echo '<input type="text" name="" class="widefat" value=\''.$sc.'\' onclick="select()">';
// }