<?php

namespace NikolayS93\LWModal;

use NikolayS93\WPAdminForm\Form as Form;

/**
 * Register Post type
 */
add_action('init', __NAMESPACE__ . '\register_modal_types');
function register_modal_types() {
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
        'label'               => null,
        'labels'              => apply_filters('LWModal_type_labels', $post_type_labels),
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
        'hierarchical'        => false,
        'supports'            => array('title','editor'),
        'taxonomies'          => array(),
        'has_archive'         => false,
        'rewrite'             => true,
        'query_var'           => true,
    );

    register_post_type( Utils::get_post_type_name(), $post_type );
}

add_action('admin_enqueue_scripts', __NAMESPACE__ . '\register_modal_admin_script');
function register_modal_admin_script() {
    $screen = get_current_screen();

    /**
     * @todo add filter or action (for localize script?)
     */
    if( !empty($screen->post_type) && Utils::get_post_type_name() === $screen->post_type ) {
        wp_enqueue_script( 'admin_modal_post_type', Utils::get_plugin_url('/admin/assets/edit-post.js'), array('jquery') );
    }
}

/**
 * Add metabox
 */
$mb = new WP_Post_Boxes( array( Utils::get_post_type_name() ) );
$mb->add_fields( array('_trigger_type', '_disable_ontime') );
$mb->add_box( __( 'Event', DOMAIN ), __NAMESPACE__ . '\modal_post_metabox2', $side = true );
function modal_post_metabox2() {
    global $post;

    $shortcode = sprintf('[%1$s id="%2$d"]%3$s[/%1$s]',
        Utils::get_shortcode_name(),
        $post->ID,
        __('Open', DOMAIN));

    $shortcode_tpl = "<pre id='shortcode_tpl'>&lt;?php echo do_shortcode('<br><span class='no-break'>$shortcode</span><br>'); ?></pre>";

    $help = __('Copy the code above to the desired page, or paste the following code into the template',
        DOMAIN) . '<br>';

    $data = array(
        array(
            'id'    => '_trigger_type',
            'type'  => 'select',
            // 'label' => 'Trigger',
            'input_class' => 'widefat button',
            'options' => array(
                'shortcode' => __('On click on [shortcode]\'s content', DOMAIN),
                'onclick'   => __('On click on selector', DOMAIN),
                'onload'    => __('Page on load, after (sec)', DOMAIN),
                'onclose'   => __('When user try to close tab', DOMAIN),
            ),
        ),
        array(
            'id'    => '_shortcode',
            'type'  => 'text',
            'desc'  => $help . $shortcode_tpl,
            'input_class' => 'widefat',
            'custom_attributes' => array(
                'onclick' => 'select(this)',
            ),
            'value' => $shortcode,
        ),
        array(
            'id'    => '_onclick',
            'type'  => 'text',
            'desc'  => __('Insert CSS/jQuery selector for "Click" event', DOMAIN),
            'input_class' => 'widefat',
            'placeholder' => '#selector',
            'custom_attributes' => array(
                'onclick' => 'select(this)',
            ),
        ),
        array(
            'id'    => '_onload',
            'type'  => 'text',
            'desc'  => __('Insert time (seconds count), open window after page on load', DOMAIN),
            'input_class' => 'widefat',
            'custom_attributes' => array(
                'onclick' => 'select(this)',
            ),
        ),
        array(
            'id'    => '_disable_ontime',
            'type'  => 'number',
            'label' => sprintf('<%1$s>%2$s</%1$s>',
                'strong',
                __('Disallow show window', DOMAIN)
            ),
            'desc'  => sprintf('%s. <span style="color: #f00;">%s</span>',
                __('Insert the number of hours the window will be disabled', DOMAIN),
                __('Need the cookies', DOMAIN)
            ),
            'custom_attributes' => array(
                'onclick' => 'select(this)',
                'style' => 'width: 50px; float: right;',
                'min' => 0,
            ),
            'placeholder' => '0',
        ),
    );

    $form = new Form( $data, $args = array(
        'is_table' => false,
        'admin_page' => false,
        'postmeta' => true,
    ) );

    // $actve = $form->get_active();
    // $actve[ '_' . $actve['_trigger_type'] ] = get_post_meta( $post->ID, '_trigger', true );
    // $form->set_active($actve);

    $form->display();
}

/**
 * Save metabox fields
 */
add_action( 'save_post', __NAMESPACE__ . '\save_trigger_field' );
function save_trigger_field( $post_id ) {
    if ( ! isset( $_POST['_wp_metabox_nonce'] ) || ! wp_verify_nonce( $_POST['_wp_metabox_nonce'], WP_Post_Boxes::SECURITY ) )
        return $post_id;

    $trigger = '';
    if( !empty( $_POST['_shortcode'] ) ) $trigger = $_POST['_shortcode'];
    if( !empty( $_POST['_onclick'] ) )   $trigger = $_POST['_onclick'];
    if( !empty( $_POST['_onload'] ) )    $trigger = $_POST['_onload'];
    if( !empty( $_POST['_onclose'] ) )   $trigger = $_POST['_onclose'];

    if( $trigger ) {
        update_post_meta( $post_id, '_trigger', sanitize_text_field($trigger) );
    }
    else {
        delete_post_meta( $post_id, '_trigger' );
    }
}
