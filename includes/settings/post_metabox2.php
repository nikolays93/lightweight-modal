<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

global $post;

$shortcode_name = Utils::get_shortcode_name();
$shortcode = '['.$shortcode_name.' id="'.$post->ID.'"]Открыть[/'.$shortcode_name.']';
$template_sc = "<big onclick='select(this)'>echo do_shortcode('<span class='no-break'>$shortcode</span>');</big>";

$form = array(
    array(
        'id'    => '_trigger_type',
        'type'  => 'select',
        // 'label' => 'Trigger',
        'input_class' => 'widefat button',
        'options' => array(
            'shortcode' => 'При нажатии на контент shortcode\'а',
            'onclick' => 'При нажатии по селектору',
            'onload' => 'При загрузке страницы, через (сек)',
            'onclose' => 'При попытке закрыть вкладку',
        ),
        'desc'  => '',
    ),
    array(
        'id'    => '_shortcode',
        'type'  => 'text',
        // 'label' => 'Селектор',
        'desc'  => 'Скопируйте код выше на нужную страницу или вставьте в шаблон <br>
        ',
        'input_class' => 'widefat',
        'custom_attributes' => array(
            'onclick' => 'select(this)',
        ),
        'value' => $shortcode,
    ),
    array(
        'id'    => '_onclick',
        'type'  => 'text',
        'desc'  => 'Введите CSS/jQuery селектор для события "Click"',
        'input_class' => 'widefat',
        'placeholder' => '#selector',
        'custom_attributes' => array(
            'onclick' => 'select(this)',
        ),
    ),
    array(
        'id'    => '_onload',
        'type'  => 'text',
        'desc'  => 'Введите время (колличество секунд), через которое открыть окно после загрузки страницы',
        'input_class' => 'widefat',
        'custom_attributes' => array(
            'onclick' => 'select(this)',
        ),
    ),
    array(
        'id'    => '_disable_ontime',
        'type'  => 'number',
        'label' => '<strong>После, запретить показ на</strong>',
        'desc'  => 'Введите колличество часов, открытие окна на протяжении которых будет отключено',
        'custom_attributes' => array(
            'onclick' => 'select(this)',
            'style' => 'width: 50px; float: right;',
            'min' => 0,
        ),
        'placeholder' => '0',
    ),
);

return apply_filters('LWModal_post_metabox_fields', $form);
