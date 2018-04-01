<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

$modal_types = array(
    ''          => __('Do not use', DOMAIN),
    'fancybox3' => __('Fancybox 3', DOMAIN),
    // 'magnific'   => __('Magnific Popup', DOMAIN),
    // 'photoswipe' => __('PhotoSwipe', DOMAIN),
    // 'lightgallery' => https://sachinchoolur.github.io/lightgallery.js/
);

$props = array(
    array(
        'type'    => 'select',
        'id'      => 'lib_props][modal_type',
        'label'   => __('Libary', DOMAIN),
        'options' => $modal_types,
        'value'   => 'fancybox3',
        'input_class' => 'button right',
    ),
    array(
        'type'      => 'text',
        'id'        => 'lib_props][modal_selector',
        'label'     => sprintf('<hr><%2$s>%1$s</%2$s><br>',
            __('jQuery Selector', DOMAIN),
            'strong'),
        'placeholder'   => '.fancybox, .zoom',
        'custom_attributes' => array(
            'onclick' => 'if(!this.value)this.value=jQuery(this).attr(\'placeholder\');focus()',
        ),
    ),
    array(
        'type'    => 'select',
        'id'      => 'lib_props][openCloseEffect',
        'label'   => sprintf('<%2$s>%1$s</%2$s><br>',
            __('Show effect', DOMAIN),
            'strong'),
        'options' => array(
            'false'       => __('Without effect', DOMAIN),
            'zoom'        => __('Zoom', DOMAIN),
            'fade'        => __('Fade', DOMAIN),
            'zoom-in-out' => __('Zoom in out', DOMAIN),
        ),
        'default' => 'zoom',
    ),
    array(
        'type'    => 'select',
        'id'      => 'lib_props][nextPrevEffect',
        'label'   => sprintf('<%2$s>%1$s</%2$s><br>',
            __('Prev/Next effect', DOMAIN),
            'strong'),
        'options' => array(
            'false'       => __('Without effect', DOMAIN),
            'fade'        => __('Fade', DOMAIN),
            'slide'       => __('Slide', DOMAIN),
            'circular'    => __('Circular', DOMAIN),
            'tube'        => __('Tube', DOMAIN),
            'zoom-in-out' => __('Zoom in out', DOMAIN),
            'rotate'      => __('Rotate', DOMAIN),
        ),
        'default' => 'fade',
    ),
    array(
        'type'    => 'html',
        'id'      => 'for_group',
        'value'   => __('To group objects, use the same <em>rel</em>', DOMAIN),
    ),
);

return apply_filters('LWModal_lib_props', $props, 'fancybox3');