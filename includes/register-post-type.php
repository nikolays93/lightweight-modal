<?php

namespace CDevelopers\modal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

add_action('init', __NAMESPACE__ . '\register_modal_types');
function register_modal_types(){
    register_post_type( Utils::OPTION, array(
        'label'  => null,
        'labels' => array(
            'name'               => __( 'Всплывающие окна', LANG ),
            'singular_name'      => __( 'Всплывающее окно', LANG ),
            'add_new'            => __( 'Добавить всплывающее окно', LANG ),
            'add_new_item'       => __( 'Добавление всплывающего окна', LANG ),
            'edit_item'          => __( 'Редактирование всплывающего окна', LANG ),
            'new_item'           => __( 'Новое всплывающее окно', LANG ),
            'view_item'          => __( 'Смотреть всплывающее окно', LANG ),
            'search_items'       => __( 'Искать всплывающее окно', LANG ),
            'not_found'          => __( 'Не найдено', LANG ),
            'not_found_in_trash' => __( 'Не найдено в корзине', LANG ),
            // 'parent_item_colon'  => '',
            // 'menu_name'          __( => 'Всплывающие окна', LANG ),
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

$mb = new WP_Post_Boxes( array( strtolower(Utils::OPTION) ) );
$mb->add_fields( array('_modal_type',)); //'_selector') );
$mb->add_box( __( 'Настройки модального окна', LANG ), __NAMESPACE__ . '\modal_post_metabox', $side = true );
function modal_post_metabox() {
    global $post;

    $data = array(
            // id or name - required
        array(
            'id'    => '_modal_type',
            'type'  => 'select',
            'label' => 'Тип: <br>',
            'input_class' => 'button right',
                // 'desc'  => 'Enter CSS Selector for onclick event',
            'options' => array(
                'inline' => 'Прятать на странице',
                // 'ajax'   => 'Загружать при открытии',
                // 'iframe' => 'Cодержимое iframe'
            ),
        ),
        // array(
        //     'id'    => '_selector',
        //     'type'  => 'text',
        //     'label' => 'Селектор',
        //     'desc'  => 'Введите CSS/jQuery селектор для события "Click"',
        // ),
    );

    $form = new WP_Admin_Forms( $data, true, $args = array(
            'admin_page'  => false,
            // 'item_wrap'   => array('<p>', '</p>'),
            // 'form_wrap'   => array('', ''),
            // 'label_tag'   => 'th',
            // 'hide_desc'   => false,
            'postmeta' => true,
        ) );
    // var_dump( $form->get_active() );
    echo $form->render();
}

add_action( 'edit_form_after_title', __NAMESPACE__ . '\do_something_after_title', 10, 1 );
function do_something_after_title( $post ) {
    $scr = get_current_screen();
    if ( $scr->post_type !== strtolower(Utils::OPTION) ) {
        return;
    }

    $sc = '[smodal title="'.$post->post_title.'" id="'.$post->ID.'"] Открыть [/smodal]';
    echo '<input type="text" name="" class="widefat" value=\''.$sc.'\' onclick="select()">';
}
