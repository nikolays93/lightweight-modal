<?php

namespace CDevelopers\modal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

add_action('init', __NAMESPACE__ . '\register_modal_types');
function register_modal_types(){
    register_post_type( Utils::OPTION, array(
        'label'  => null,
        'labels' => array(
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

add_action('admin_enqueue_scripts', __NAMESPACE__ . '\register_modal_admin_script');
function register_modal_admin_script() {
    $screen = get_current_screen();
    if( !empty($screen->post_type) && Utils::OPTION === $screen->post_type ) {
        wp_enqueue_script( 'admin_modal_post_type', Utils::get_plugin_url('assets/admin-post-type.js'), array('jquery') );
    }

}

add_action( 'save_post', __NAMESPACE__ . '\save_trigger_field' );
function save_trigger_field( $post_id ) {
    if ( ! isset( $_POST['_wp_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['_wp_metabox_nonce'], WP_Post_Boxes::SECURITY ) )
        return $post_id;

    $trigger = '';
    if( !empty( $_POST['_shortcode'] ) ) $trigger = $_POST['_shortcode'];
    if( !empty( $_POST['_onclick'] ) ) $trigger = $_POST['_onclick'];
    if( !empty( $_POST['_onload'] ) ) $trigger = $_POST['_onload'];
    if( !empty( $_POST['_onclose'] ) ) $trigger = $_POST['_onclose'];

    if( $trigger ) {
        update_post_meta( $post_id, '_trigger', sanitize_text_field($trigger) );
    }
    else {
        delete_post_meta( $post_id, '_trigger' );
    }
}

$mb = new WP_Post_Boxes( array( strtolower(Utils::OPTION) ) );
$mb->add_fields( array('_trigger_type', '_disable_ontime') );
$mb->add_box( __( 'Событие', DOMAIN ), __NAMESPACE__ . '\modal_post_metabox2', $side = true );

$mb = new WP_Post_Boxes( array( strtolower(Utils::OPTION) ) );
$mb->add_fields( array('_modal_type',)); //'_selector') );
$mb->add_box( __( 'Дополнительные настройки', DOMAIN ), __NAMESPACE__ . '\modal_post_metabox', $side = true );

function modal_post_metabox() {
    global $post;

    // @todo: Активно до:
    $data = array(
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

    $form = new WP_Admin_Forms( $data, true, $args = array(
        'admin_page'  => false,
        'postmeta' => true,
    ) );
    echo $form->render();
}

function modal_post_metabox2() {
    global $post;

    $shortcode_name = Utils::get_shortcode_name();
    $shortcode = '['.$shortcode_name.' id="'.$post->ID.'"]Открыть[/'.$shortcode_name.']';
    $data = array(
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
            'desc'  => 'Скопируйте код выше на нужную страницу или используйте <br><big>echo do_shortcode("код выше");</big>',
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

    $form = new WP_Admin_Forms( $data, false, $args = array(
            'admin_page' => false,
            'postmeta' => true,
        ) );

    $actve = $form->get_active();
    $actve[ '_' . $actve['_trigger_type'] ] = get_post_meta( $post->ID, '_trigger', true );
    $form->set_active($actve);

    echo $form->render();
}


// add_action( 'edit_form_after_title', __NAMESPACE__ . '\do_something_after_title', 10, 1 );
// function do_something_after_title( $post ) {
//     $scr = get_current_screen();
//     if ( $scr->post_type !== strtolower(Utils::OPTION) ) {
//         return;
//     }
//     echo '<input type="text" name="" class="widefat" value=\''.$sc.'\' onclick="select()">';
// }
