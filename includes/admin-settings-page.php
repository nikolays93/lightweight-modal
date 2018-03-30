<?php

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

class AdminSettingsPage
{
    function __construct()
    {
        $page = new WP_Admin_Page( Utils::get_option_name() );
        $page->set_args( array(
            'parent'      => false,
            'icon_url'    => 'dashicons-external',
            'title'       => __('Modals', DOMAIN),
            'menu'        => __('Modals', DOMAIN),
            'callback'    => array($this, 'page_render'),
            // 'validate'    => array($this, 'validate_options'),
            'permissions' => 'manage_options',
            'tab_sections'=> null,
            'columns'     => 2,
        ) );

        $page->add_metabox( 'lib_settings', __( 'Lib settings', DOMAIN ), array($this, 'lib_metabox'), 'side' );
        $page->set_metaboxes();
    }

    function page_render() {
        $table = new WP_List_Table();
        $table->set_columns( array(
            'shortcode'    => __( 'Shortcode', DOMAIN ),
            '_count'       => __( 'Show Count', DOMAIN ),
            // '_selector' => 'Selector',
            // '_theme'    => 'Design',
            'post_author'  => __( 'Author', DOMAIN ),
            'post_date'    => __( 'Date', DOMAIN ),
        ) );
        $table->set_bulk_actions();

        $posts = get_posts( array('post_type' => Utils::get_shortcode_name()) );
        foreach ($posts as $post) {
            $post_meta = get_post_meta( $post->ID );
            $table->set_value( array(
                'ID'           => $post->ID,
                'post_title'   => esc_html( $post->post_title ),
                'shortcode'    => !empty($post_meta['shortcode']) ? $post_meta['shortcode'] : '',
                '_count'       => !empty($post_meta['_count']) && is_array($post_meta['_count'])
                    ? current($post_meta['_count']) : '',
                // '_selector' => 'Selector',
                // '_theme'    => 'Design',
                'post_author'  => $post->post_author,
                'post_date'    => $post->post_modified,
            ) );
        }

        $table->prepare_items();
        ?>

        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php
        $table->display();
        $create_link = get_admin_url() . 'post-new.php?post_type=' . Utils::get_posttype_name();
        ?>
        <a href="<?php echo $create_link ?>" class="button button-primary" style="margin-top: 5px;">Добавить</a>
        <?php
        // printf( '<input type="hidden" name="page" value="%s" />', $_REQUEST['page'] );
    }

    function lib_metabox()
    {
        $form = new WP_Admin_Forms( Utils::get_settings( 'lib_fancybox3.php' ), false );

        echo $form->render();

        submit_button( 'Сохранить', 'primary right', 'save_changes' );
        echo '<div class="clear"></div>';
    }
    
}
new AdminSettingsPage();
