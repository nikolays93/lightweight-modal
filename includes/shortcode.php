<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

class _Modal
{
    static $active = array();

    static function get_active_modals()
    {
        if( empty(self::$active) ) {
             self::$active = get_posts( array(
                'post_type'  => Utils::get_posttype_name(),
                'meta_query' => array(
                    array(
                        'key'     => '_trigger_type',
                        'value'   => array('onclick', 'onload', 'onclose'),
                        'compare' => 'IN',
                    )
                )
            ) ); 
        }

        return self::$active;
    }

    function __construct()
    {
        add_action( 'wp_footer', array($this, 'active_modals_bootstrap') );
        add_shortcode( Utils::get_shortcode_name(), array($this, 'shortcode_callback') );
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_modal_scripts') );
    }

    function enqueue_modal_scripts()
    {
        $assets = Utils::get_plugin_url('/assets');
        $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '.min' : '';
        $props = wp_parse_args( Utils::get( 'lib_props' ), array(
            'modal_type' => false,
            'modal_selector' => false,
        ) );

        $modals = self::get_active_modals();
        $has_modals = sizeof($modals) >= 1;
        if( $has_modals || $props['modal_selector'] ) {
            wp_enqueue_script( 'modal_script', $assets . '/public.js',
                array('jquery'), '1.0', true );
        }

        $props['modals'] = array();
        if( $has_modals ) {
            foreach ($modals as $modal) {
                $props['modals'][ $modal->ID ] = array(
                    'trigger_type'   => get_post_meta( $modal->ID, '_trigger_type', true ),
                    'trigger'        => get_post_meta( $modal->ID, '_trigger', true ),
                    'disable_ontime' => get_post_meta( $modal->ID, '_disable_ontime', true ),
                    'modal_type'     => get_post_meta( $modal->ID, '_modal_type', true ),
                );
            }
        }

        if( 'fancybox3' === $props['modal_type'] ) {
            wp_enqueue_script(
                'fancybox3',
                $assets . "/fancybox3/jquery.fancybox{$affix}.js",
                array('jquery'),
                '3.0',
                true
            );
            wp_enqueue_style(
                'fancybox3',
                $assets . "/fancybox3/jquery.fancybox{$affix}.css",
                null,
                '3.0'
            );
        }

        wp_localize_script( 'modal_script', 'LWModals', $props );
        wp_localize_script( 'modal_script', 'LWM_Settings', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce( 'Secret' ),
        ) );
    }
}
// new Modal();


class Modal
{
    static $active = array();

    function __construct()
    {
    }

    static function enqueue_public() {
        wp_enqueue_script( 'LWModals_public',
            Utils::get_plugin_url('/assets/public.js'), array('jquery'), '1.0', true );
    }

    static function get_active_modals()
    {
        self::$active = get_posts( array(
            'post_type'  => Utils::get_posttype_name(),
            'meta_query' => array(
                array(
                    'key'     => '_trigger_type',
                    'value'   => array('onclick', 'onload', 'onclose'),
                    'compare' => 'IN',
                )
            )
        ) );

        return self::$active;
    }

    function shortcode_callback( $atts = array(), $content = '' )
    {
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

        self::$active = get_post( $modal_id );

        return sprintf('<a href="%1$s" data-fancybox data-modal-id="%2$d" data-src="#modal_%2$d"%3$s%4$s>%5$s</a>',
            $atts['href'] ? esc_url( $atts['href'] ) : '#',
            $modal_id,
            $atts['link_id'] ? sprintf(' id="%s"', esc_attr( $atts['link_id'] )) : '',
            $atts['class'] ? sprintf(' class="%s"', esc_attr( $atts['class'] )) : '',
            $content
        );
    }

    static function footer_scripts() {
        wp_localize_script( 'LWModals_public', 'object_name', array('test' => '1') );
    }

    function active_modals_bootstrap()
    {
        foreach ($modals as $modal) {
            echo "<div id='modal_{$modal->ID}' style='display: none;'>";

            do_action( 'LWModal_header', $modal );

            switch ( get_post_meta( $modal->ID, '_modal_type', true ) ) {
                case 'ajax':
                    echo '<div style="min-width: 400px;" id="ajax_data_'.$modal->ID.'"> '. __( 'Loading..' ) .' </div>';
                break;

                case 'inline':
                default:
                    echo apply_filters( 'the_content', $modal->post_content );
                break;
            }

            do_action( 'LWModal_footer', $modal );

            echo "</div>";
        }
    }
}

$modals = Modal::get_active_modals();
foreach ($modals as $modal) {
    echo do_shortcode( '[modal id="'.$modal->ID.'"][/modal]' );
}

// add_action('wp_enqueue_scripts', array(__NAMESPACE__ . '\Modal', 'enqueue_public'));
// add_action('wp_footer', array(__NAMESPACE__ . '\Modal', 'footer_scripts'));