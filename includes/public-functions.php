<?php

namespace CDevelopers\modal;

class PublicFunctions
{
    function __construct()
    {
    }

    static function enqueue_modal_scripts()
    {
        $assets = self::get_plugin_url('assets');
        $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '.min' : '';
        $props = wp_parse_args( self::get( 'lib_props' ), array(
            'modal_type' => false,
            'modal_selector' => false,
        ) );

        $has_modals = sizeof(self::$posts) >= 1;
        if( $has_modals || $props['modal_selector'] ) {
            wp_enqueue_script( 'modal_script', $assets . '/public.js',
                array('jquery'), '1.0', true );
        }

        $props['modals'] = array();
        if( $has_modals ) {
            foreach (self::$posts as $post) {
                $props['modals'][ $post->ID ] = array(
                    'trigger_type'   => get_post_meta( $post->ID, '_trigger_type', true ),
                    'trigger'        => get_post_meta( $post->ID, '_trigger', true ),
                    'disable_ontime' => get_post_meta( $post->ID, '_disable_ontime', true ),
                    'modal_type'     => get_post_meta( $post->ID, '_modal_type', true ),
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

        echo "<pre>";
        var_dump( $props['modals'] );
        echo "</pre>";
    }
}
