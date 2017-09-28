<?php

add_action('init', 'register_smodals_types');
function register_smodals_types(){
    register_post_type(SMODALS::SETTINGS, array(
        'label'  => null,
        'labels' => array(
            'name'               => 'Всплывающие окна',
            'singular_name'      => 'Всплывающее окно',
            'add_new'            => 'Добавить всплывающее окно',
            'add_new_item'       => 'Добавление всплывающего окна',
            'edit_item'          => 'Редактирование всплывающего окна',
            'new_item'           => 'Новое всплывающее окно',
            'view_item'          => 'Смотреть всплывающее окно',
            'search_items'       => 'Искать всплывающее окно',
            'not_found'          => 'Не найдено',
            'not_found_in_trash' => 'Не найдено в корзине',
            'parent_item_colon'  => '',
            // 'menu_name'          => 'Всплывающие окна',
        ),
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
    ) );
}

$mb = new WP_Post_Boxes( array( strtolower(SMODALS::SETTINGS) ) );
$mb->add_fields( '_selector' );
$mb->add_box( 'Modal Settings', 'test_callback', $side = true );
function test_callback() {
    global $post;

    $data = array(
            // id or name - required
        array(
            'id'    => '_selector',
            'type'  => 'text',
            'label' => 'Selector',
            'desc'  => 'Enter CSS Selector for onclick event',
        ),
        array(
            'id'    => '_type',
            'type'  => 'select',
            'label' => 'Modal Type: <br>',
                // 'desc'  => 'Enter CSS Selector for onclick event',
            'options' => array(
                'inline' => 'inline', 
                'ajax'   => 'ajax',
                'iframe' => 'iframe'
            ),
        ),
    );

    $count = array( '_selector' => get_post_meta( $post->ID, '_selector', true ) );

    $form = new WP_Admin_Forms( $data, $count, $is_table = false, $args = array(
            'admin_page'  => false,
            // 'item_wrap'   => array('<p>', '</p>'),
            // 'form_wrap'   => array('', ''),
            // 'label_tag'   => 'th',
            // 'hide_desc'   => false,
        ) );
    echo $form->render();
}