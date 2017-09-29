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
$mb->add_fields( array('_modal_type',)); //'_selector') );
$mb->add_box( 'Настройки модального окна', 'smodal_settings', $side = true );
function smodal_settings() {
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
                'ajax'   => 'Загружать при открытии',
                'iframe' => 'Cодержимое iframe'
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

$mb = new WP_Post_Boxes( array( strtolower(SMODALS::SETTINGS) ) );
$mb->add_box( 'Шорткод', 'smodal_shortcode', $side = normal );
function smodal_shortcode() {
    global $post;

    if( $post instanceof WP_Post ) {
        $sc = '[smodal title="'.$post->post_title.'" id="'.$post->ID.'"] Открыть [/smodal]';
        ?>
        <input type="text" name="" class="widefat" value='<?php echo $sc; ?>' onclick="select()">
        <?php
        echo '';
    }
    else {
        echo "Сначала сохраните запись";
    }
}