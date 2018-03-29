<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

global $post;

// @todo: Активно до:
$form = array(
    array(
        'id'    => '_modal_type',
        'type'  => 'select',
        'label' => 'Тип: <br>',
        'input_class' => 'button right',
        'options' => array(
            'inline' => 'Прятать на странице',
            // 'ajax'   => 'Загружать при открытии',
            // 'iframe' => 'Cодержимое iframe'
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