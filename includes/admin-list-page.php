<?php

class SMODALS_List_Page
{
    function __construct()
    {
        $page = new WP_Admin_Page();
        $page->set_args( SMODALS::SETTINGS . '-List', array(
            'parent'      => false,
            'title'       => '',
            'menu'        => __( 'Modals' ),
            'callback'    => array($this, 'page_render'),
            // 'validate'    => array($this, 'validate_options'),
            'permissions' => 'manage_options',
            'tab_sections'=> null,
            'columns'     => 2,
            ) );

        // $page->set_assets( array($this, 'set_assets') );

        $page->add_metabox( 'metabox1', 'metabox1', array($this, 'metabox1_callback'), $position = 'side');
        $page->add_metabox( 'metabox2', 'metabox2', array($this, 'metabox2_callback'), $position = 'side');
        $page->set_metaboxes();
    }

    // function set_assets()
    // {
    //     wp_enqueue_script( 'PLUGINNAME_Script', SMODALS_ASSETS . '/page.js', array('jquery'), '1.0', true );
    //     wp_localize_script('PLUGINNAME_Script', 'PLUGINNAME_opt', array(
    //         'nonce' => wp_create_nonce( 'PLUGINNAME' ),
    //         ) );
    // }

    /**
     * Основное содержимое страницы
     *
     * @access
     *     must be public for the WordPress
     */
    function page_render()
    {
        $table = new Example_List_Table();
        $table->set_fields( array('post_type' => SMODALS::SETTINGS) );
        $table->prepare_items();
        ?>

        <div style="background:#ececec;border:1px solid #ccc;padding:0 10px;margin-top:5px;border-radius:5px;">
            <p>This page demonstrates the use of the <code>WP_List_Table</code> class in plugins.</p>
            <p>Additional class details are available on the <a href="http://codex.wordpress.org/Class_Reference/WP_List_Table" target="_blank">WordPress Codex</a> or <a href="https://developer.wordpress.org/reference/classes/WP_List_Table/" target="_blank">Developer Code Reference</a>.</p>
        </div>

        <!-- <form id="movies-filter" method="get"> -->
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <!-- Now we can render the completed list table -->
        <?php
            $table->display();
            $create_link = get_admin_url() . 'post-new.php?post_type=' . strtolower('smodals');
        ?>
        <!-- </form> -->
        <a href="<?php echo $create_link ?>" class="button button-primary" style="margin-top: 5px;">Добавить</a>
        <?php
    }

    /**
     * Тело метабокса вызваное функций $this->add_metabox
     *
     * @access
     *     must be public for the WordPress
     */
    function metabox1_callback() {
        echo "test1";
    }

    function metabox2_callback() {
        $data = array(
            // id or name - required
            array(
                'id'    => 'example_0',
                'type'  => 'text',
                'label' => 'TextField',
                'desc'  => 'This is example text field',
                ),
             array(
                'id'    => 'example_1',
                'type'  => 'select',
                'label' => 'Select',
                'options' => array(
                    // simples first (not else)
                    'key_option5' => 'option5',
                    'option1' => array(
                        'key_option2' => 'option2',
                        'key_option3' => 'option3',
                        'key_option4' => 'option4'),
                    ),
                ),
            array(
                'id'    => 'example_2',
                'type'  => 'checkbox',
                'label' => 'Checkbox',
                ),
            );

        $form = new WP_Admin_Forms( $data, $active = null, $is_table = true, $args = array(
            // Defaults:
            // 'admin_page'  => true,
            // 'item_wrap'   => array('<p>', '</p>'),
            // 'form_wrap'   => array('', ''),
            // 'label_tag'   => 'th',
            // 'hide_desc'   => false,
            ) );
        echo $form->render();

        submit_button( 'Сохранить', 'primary right', 'save_changes' );
        echo '<div class="clear"></div>';
    }
}
new SMODALS_List_Page();
