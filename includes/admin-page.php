<?php

namespace CDevelopers\modal;

if ( ! defined( 'ABSPATH' ) )
    exit; // disable direct access

class Admin_Page
{
    function __construct()
    {
        $page = new WP_Admin_Page( Utils::OPTION );
        $page->set_args( array(
            'parent'      => false,
            'icon_url'    => 'dashicons-external',
            'title'       => 'Модальные окна',
            'menu'        => 'Модальные окна',
            'callback'    => array($this, 'page_render'),
            // 'validate'    => array($this, 'validate_options'),
            'permissions' => 'manage_options',
            'tab_sections'=> null,
            'columns'     => 2,
            ) );

        $page->add_metabox( 'lib_settings', __( 'Lib settings', DOMAIN ), array($this, 'lib_metabox'), 'side' );
        $page->set_metaboxes();
    }

    function _assets()
    {
        // wp_enqueue_style();
        // wp_enqueue_script();
    }

    /**
     * Тело метабокса вызваное функций $this->add_metabox
     *
     * @access
     *     must be public for the WordPress
     */
    function lib_metabox()
    {
        $modal_types = array(
            ''          => 'Не использовать',
            // 'fancybox2' => 'Fancybox 2',
            'fancybox3' => 'Fancybox 3',
            // 'magnific'   => 'Magnific Popup',
            // 'photoswipe' => 'PhotoSwipe',
            // 'lightgallery' => https://sachinchoolur.github.io/lightgallery.js/
            );

        // revisions-next
        $modal['fancybox3'] = array(
            array(
              'type'    => 'select',
              'id'      => 'lib_props][modal_type',
              'label'   => 'Библиотека',
              'options' => $modal_types,
              'value'   => 'fancybox3',
              'input_class' => 'button right',
              ),
            array(
              'type'      => 'text',
              'id'        => 'lib_props][modal_selector',
              'label'     => '<hr> <strong>jQuery Селектор</strong> <br>',
              // 'desc'      => 'Модальное окно (Галерея, всплывающее окно)',
              'placeholder'   => '.fancybox, .zoom',
              'custom_attributes' => array(
                'onclick' => 'if(!this.value)this.value=jQuery(this).attr(\'placeholder\');focus()',
              ),
              // 'input_class' => 'button right',
              ),
            array(
              'type'    => 'select',
              'id'      => 'lib_props][openCloseEffect',
              'label'   => 'Эффект открытия',
              'options' => array(
                'false'     => 'Без эффекта',
                'zoom'        => 'Увеличение от объекта',
                'fade'        => 'Угасание',
                'zoom-in-out' => 'Увеличение из вне',
                ),
              'default' => 'zoom',
              ),
            array(
              'type'    => 'select',
              'id'      => 'lib_props][nextPrevEffect',
              'label'   => 'Эффект перелистывания',
              'options' => array(
                'false'       => 'Без эффекта',
                'fade'        => 'Угасание',
                'slide'       => 'Скольжение',
                'circular'    => 'Циркуляция',
                'tube'        => 'Труба',
                'zoom-in-out' => 'Увеличение из вне',
                'rotate'      => 'Переворот',
                ),
              'default' => 'fade',
              ),
            array(
              'type'    => 'html',
              'id'      => 'for_group',
              'value'   => 'Для группировки объектов используйте одинаковый <em>rel</em>'
              ),
            );

        $modal['magnific'] = array(
            array(
              'type' => 'select',
              'id'   => 'modal_type',
              'label'=> 'Библиотека',
              'options' => $modal_types,
              'value' => 'magnific',
              ),
            array(
              'type'      => 'text',
              'id'        => 'magnific',
              'label'     => 'Селектор',
              // 'desc'      => 'Модальное окно (Галерея, всплывающее окно)',
              'placeholder'   => '.magnific, .zoom',
              ),
            );

        $modal['photoswipe'] = array(
            array(
              'type' => 'select',
              'id'   => 'modal_type',
              'label'=> 'Библиотека',
              'options' => $modal_types,
              'value' => 'photoswipe',
              ),
            array(
              'type'      => 'text',
              'id'        => 'photoswipe',
              'label'     => 'Селектор',
              // 'desc'      => 'Модальное окно (Галерея, всплывающее окно)',
              'placeholder'   => '.photoswipe, .zoom',
              ),
            );

        $form = new WP_Admin_Forms( $modal['fancybox3'], $is_table = false, $args = array(
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

    /**
     * Основное содержимое страницы
     *
     * @access
     *     must be public for the WordPress
     */
    function page_render() {
        $table = new List_Table();
        $table->set_fields( array('post_type' => Utils::OPTION) );
        $table->prepare_items();
        ?>

        <!-- <form id="movies-filter" method="get"> -->
        <!-- For plugins, we also need to ensure that the form posts back to our current page -->
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <!-- Now we can render the completed list table -->
        <?php
        $table->display();
        $create_link = get_admin_url() . 'post-new.php?post_type=' . Utils::OPTION;
        ?>
        <!-- </form> -->
        <a href="<?php echo $create_link ?>" class="button button-primary" style="margin-top: 5px;">Добавить</a>
        <?php
    }
}
new Admin_Page();
