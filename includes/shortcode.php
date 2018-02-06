<?php

namespace CDevelopers\modal;

add_shortcode( Utils::get_shortcode_name(), __NAMESPACE__ . '\shortcode_callback' );
add_action( 'wp_footer', __NAMESPACE__ . '\add_modal_scaff' );

add_action( 'wp_ajax_nopriv_increase_click_count', __NAMESPACE__ . '\increase_click_count' );
add_action( 'wp_ajax_increase_click_count', __NAMESPACE__ . '\increase_click_count' );

function shortcode_callback( $atts = array(), $content = '' ) {
    $atts = shortcode_atts( array(
        'id'      => 0,
        'href'    => '',
        'class'   => '',
        'link_id' => '',
        'title'   => '',
        ), $atts, Utils::get_shortcode_name() );

    if( ! $content || 0 >= $modal_id = absint($atts['id']) ) {
        return false;
    }

    Utils::$active_modals[$modal_id] = esc_html( $atts['title'] );

    return sprintf('<a href="%1$s" data-fancybox data-modal-id="%2$d" data-src="#modal_%2$d"%3$s%4$s>%5$s</a>',
        $atts['href'] ? esc_url( $atts['href'] ) : 'javascript:;',
        $modal_id,
        $atts['link_id'] ? sprintf(' id="%s"', esc_attr( $atts['link_id'] )) : '',
        $atts['class'] ? sprintf(' class="%s"', esc_attr( $atts['class'] )) : '',
        $content
        );
}

function add_modal_scaff() {
    $active = Utils::$active_modals;
    if( is_array($active) ) {
        foreach ($active as $post_id => $title) {
            $_post = get_post( $post_id );

            echo "<div id='modal_{$_post->ID}' style='display: none;'>";
            if( $title ) {
                echo "<h2>{$title}</h2>";
            }
            switch ( get_post_meta( $_post->ID, '_modal_type', true ) ) {
                case 'ajax':
                echo '<div style="min-width: 400px;" id="ajax_data_'.$_post->ID.'"> '. __( 'Loading..' ) .' </div>';
                break;

                case 'inline':
                default:
                echo apply_filters( 'the_content', $_post->post_content );
                break;
            }
            echo "</div>";
        }
    }
}

function increase_click_count() {
    if( ! wp_verify_nonce( $_POST['nonce'], 'Secret' ) ) {
        wp_die('Ошибка! нарушены правила безопасности');
    }

    $modal_id = absint( $_POST['modal_id'] );
    if( $modal_id < 1 ) {
        wp_die('Не передан ID модального окна');
    }

    $count = get_post_meta( $modal_id, '_count', true );
    update_post_meta( $modal_id, '_count', + $count + 1 );

    echo $count;
    wp_die();
}