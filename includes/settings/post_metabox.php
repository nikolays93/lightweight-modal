<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

global $post;

$br = ' <br>';

// @todo: Активно до:
$form = array(
    array(
        'id'    => '_modal_type',
        'type'  => 'select',
        'label' => __('Type:', DOMAIN) . $br,
        'input_class' => 'button right',
        'options' => array(
            'inline' => __('Hide on footer', DOMAIN),
            // 'ajax'   => __('load after open', DOMAIN),
            // 'iframe' => __('iFrame link', DOMAIN),
        ),
    ),
    // array(
    //     'id'    => '_modal_type',
    //     'type'  => 'text',
    //     'label' => '<nobr>Активно до:</nobr>',
    //     'input_class' => 'button right',
    // ),
);

return apply_filters('LWModal_post_metabox_fields', $form);