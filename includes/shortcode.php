<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

class Modal
{
    private static $bootstraps = array();
    private function __construct(){}

    private static function push_active_bootstraps()
    {
        echo "<pre>";
        var_dump( self::$bootstraps );
        echo "</pre>";

        self::$bootstraps += get_posts( array(
            'post_type'  => Utils::get_posttype_name(),
            'meta_query' => array(
                array(
                    'key'     => '_trigger_type',
                    'value'   => array('onclick', 'onload', 'onclose'),
                    'compare' => 'IN',
                )
            )
        ) );

        echo "<pre>";
        var_dump( self::$bootstraps );
        echo "</pre>";
    }

    static function shortcode( $atts = array(), $content = '' )
    {
        $atts = shortcode_atts( array(
            'id'      => 0,
            'href'    => '',
            'class'   => '',
            'attr_id' => '',
            'title'   => '',
        ), $atts, Utils::get_shortcode_name() );

        if( ! $content || 0 >= $modal_id = absint($atts['id']) ) {
            return false;
        }

        self::$bootstraps[] = get_post( $modal_id );

        // sanitize attributes
        $attributes = array_map('esc_attr', apply_filters( 'LWModals_sc_attrs', array(
            'href'  => $atts['href'],
            'id'    => $atts['attr_id'],
            'class' => $atts['class'],
            'title' => $atts['title'],
        ) ) );

        $strAttributes = '';
        foreach ($attributes as $attr_key => $attr_value) {
            $strAttributes .= " $attr_key=$attr_value";
        }

        $html = sprintf('<a data-fancybox data-modal-id="%1$d" data-src="#modal_%1$d" href="%2$s"%3$s>%4$s</a>',
            $modal_id,
            $atts['href'] ? esc_url( $atts['href'] ) : '#',
            $strAttributes,
            $content
        );

        return apply_filters( 'LWModals_sc_html', $html );
    }

    /********************** After collect all bootstraps **********************/
    private static function render_modal_bootstraps()
    {
        foreach (self::$bootstraps as $modal) {
            if( empty($modal->ID) ) continue;

            $type = get_post_meta( $modal->ID, '_modal_type', true );

            echo "<div id='modal_{$modal->ID}' style='display: none;'>";

            do_action( 'LWModal_head', $modal, $type );

            do_action( 'LWModal_body', $modal, $type );

            do_action( 'LWModal_foot', $modal, $type );

            echo "</div>";
        }
    }

    private static function enqueue_modal_scripts()
    {
        $assets = Utils::get_plugin_url('/assets');
        $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '.min' : '';

        $summary = wp_parse_args( Utils::get( 'lib_props' ), array(
            'modal_type' => false,
            'modal_selector' => false,
        ) );

        $has_summary = (!empty( $summary['modal_type'] ) && !empty( $summary['modal_selector'] ));
        $has_modals = sizeof(self::$bootstraps);

        if(!$has_summary && !$has_modals)
            return;

        $summary['modals'] = array();
        foreach (self::$bootstraps as $modal) {
            $summary['modals'][ $modal->ID ] = array(
                'trigger_type'   => get_post_meta( $modal->ID, '_trigger_type', true ),
                'trigger'        => get_post_meta( $modal->ID, '_trigger', true ),
                'disable_ontime' => get_post_meta( $modal->ID, '_disable_ontime', true ),
                'modal_type'     => get_post_meta( $modal->ID, '_modal_type', true ),
            );
        }

        if( 'fancybox3' === $summary['modal_type'] ) {
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

        wp_enqueue_script( 'LWModals_public',
            $assets . '/public.js', array('jquery'), '0.2', true );

        wp_localize_script( 'LWModals_public', 'LWModals', $summary );
        wp_localize_script( 'LWModals_public', 'LWM_Settings', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce( 'Secret' ),
        ) );
    }

    /**
     * @hook wp_footer
     */
    static function setup_footer()
    {
        self::push_active_bootstraps();
        self::render_modal_bootstraps();
        self::enqueue_modal_scripts();
        // Так не пойдет потому что wp_enqueue_scripts уже выполнен
        // add_action( 'wp_enqueue_scripts',
        //     array(__NAMESPACE__ . '\\' . __CLASS__, '_enqueue_modal_scripts') );
    }
}
