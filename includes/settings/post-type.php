<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

$post_type_labels = array(
    'name'               => __( 'Всплывающие окна', DOMAIN ),
    'singular_name'      => __( 'Всплывающее окно', DOMAIN ),
    'add_new'            => __( 'Добавить всплывающее окно', DOMAIN ),
    'add_new_item'       => __( 'Добавление всплывающего окна', DOMAIN ),
    'edit_item'          => __( 'Редактирование всплывающего окна', DOMAIN ),
    'new_item'           => __( 'Новое всплывающее окно', DOMAIN ),
    'view_item'          => __( 'Смотреть всплывающее окно', DOMAIN ),
    'search_items'       => __( 'Искать всплывающее окно', DOMAIN ),
    'not_found'          => __( 'Не найдено', DOMAIN ),
    'not_found_in_trash' => __( 'Не найдено в корзине', DOMAIN ),
    // 'parent_item_colon'  => '',
    // 'menu_name'          __( => 'Всплывающие окна', DOMAIN ),
);

$post_type = array(
    'label'  => null,
    'description'         => '',
    'public'              => false,
    'publicly_queryable'  => null,
    'exclude_from_search' => true,
    'show_ui'             => true,
    'show_in_menu'        => false,
    'show_in_admin_bar'   => false,
    'show_in_nav_menus'   => false,
    'show_in_rest'        => null,
    'rest_base'           => null,
    'menu_position'       => null,
    'menu_icon'           => null,
    //'capability_type'   => 'post',
    //'capabilities'      => 'post', // массив дополнительных прав для этого типа записи
    //'map_meta_cap'      => null, // Ставим true чтобы включить дефолтный обработчик специальных прав
    'hierarchical'        => false,
    'supports'            => array('title','editor'), // 'title','editor','author','thumbnail','excerpt','trackbacks','comments','revisions','page-attributes','post-formats', 'custom-fields'
    'taxonomies'          => array(),
    'has_archive'         => false,
    'rewrite'             => true,
    'query_var'           => true,
);

$post_type['labels'] = apply_filters('LWModal_type_labels', $post_type_labels);
return apply_filters('LWModal_type', $post_type);
