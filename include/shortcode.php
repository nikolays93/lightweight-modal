<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) ) exit; // disable direct access

class Shortcode
{
    private static $bootstraps = array();
    private function __construct(){}

    private static function push_active_bootstraps()
    {
        self::$bootstraps += get_posts( array(
            'post_status' => 'publish',
            'post_type'  => Utils::get_post_type_name(),
            'meta_query' => array(
                array(
                    'key'     => '_trigger_type',
                    'value'   => array('onclick', 'onload', 'onclose'),
                    'compare' => 'IN',
                )
            )
        ) );
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

        $gSettings = wp_parse_args(Utils::get(), array(
            'selector' => '',
            'lib_name' => '',
            'lib_args' => array(),
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce( 'Secret' ),
            'cookie'   => 'lw_disabled',
            'expires'  => 24 * 7, // one week
        ));

        if( !empty($gSettings['lib_name']) ) {
            if( 'fancybox3' === $gSettings['lib_name'] ) {
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
        }

        $modals = array();
        foreach (self::$bootstraps as $modal) {
            $modals[ $modal->ID ] = array(
                'trigger_type'   => get_post_meta( $modal->ID, '_trigger_type', true ),
                'trigger'        => get_post_meta( $modal->ID, '_trigger', true ),
                'disable_ontime' => get_post_meta( $modal->ID, '_disable_ontime', true ),
                'modal_type'     => get_post_meta( $modal->ID, '_modal_type', true ),
            );
        }

        wp_enqueue_script( 'LWModals_public', $assets . '/public.js', array('jquery'), Plugin::$data['Version'], true );

        wp_localize_script( 'LWModals_public', 'LWModals', $modals );
        wp_localize_script( 'LWModals_public', 'LWM_Settings', $gSettings );
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

    static function modal_window_body( $modal, $type )
    {
        switch ( $type ) {
            case 'ajax':
            echo '<div style="min-width: 400px;" id="ajax_data_'.$modal->ID.'"> '. __( 'Loading..' ) .' </div>';
            break;

            case 'inline':
            default:
            echo apply_filters( 'the_content', $modal->post_content );
            break;
        }
    }

    static function modal_window_head( $modal, $type )
    {
        if( $modal )
            echo "<h2>{$modal->post_title}</h2>";
    }
}
