<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

$post_type_labels = array(
    'name'               => _x( 'Modal windows', DOMAIN, 'post_type' ),
    'singular_name'      => _x( 'Modal window', DOMAIN, 'post_type' ),
    'add_new'            => _x( 'Добавить всплывающее окно', DOMAIN, 'post_type' ),
    'add_new_item'       => _x( 'Добавление всплывающего окна', DOMAIN, 'post_type' ),
    'edit_item'          => _x( 'Редактирование всплывающего окна', DOMAIN, 'post_type' ),
    'new_item'           => _x( 'Новое всплывающее окно', DOMAIN, 'post_type' ),
    'view_item'          => _x( 'Смотреть всплывающее окно', DOMAIN, 'post_type' ),
    'search_items'       => _x( 'Искать всплывающее окно', DOMAIN, 'post_type' ),
    'not_found'          => _x( 'Не найдено', DOMAIN, 'post_type' ),
    'not_found_in_trash' => _x( 'Не найдено в корзине', DOMAIN, 'post_type' ),
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
