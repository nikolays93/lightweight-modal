<?php

/*
Plugin Name: Легкие модальные (всплывающие) окна
Plugin URI: https://github.com/nikolays93/lightweight-modal
Description: Модальные окна для создания галерей, всплывающих форм и сообщений
Version: 0.2.2 (beta)
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

/**
 * @todo : Добавить сюда хуки плагина
 *         Добавить переводы
 *
 * Хуки плагина:
 * $pageslug . _after_title (default empty hook)
 * $pageslug . _before_form_inputs (default empty hook)
 * $pageslug . _inside_page_content
 * $pageslug . _inside_side_container
 * $pageslug . _inside_advanced_container
 * $pageslug . _after_form_inputs (default empty hook)
 * $pageslug . _after_page_wrap (default empty hook)
 *
 * Фильтры плагина:
 * "get_{DOMAIN}_option_name" - имя опции плагина
 * "get_{DOMAIN}_option" - значение опции плагина
 * "load_{DOMAIN}_file_if_exists" - информация полученная с файла
 * "get_{DOMAIN}_plugin_dir" - Дирректория плагина (доступ к файлам сервера)
 * "get_{DOMAIN}_plugin_url" - УРЛ плагина (доступ к внешним файлам)
 *
 * $pageslug . _form_action - Аттрибут action формы на странице настроек плагина
 * $pageslug . _form_method - Аттрибут method формы на странице настроек плагина
 */

namespace NikolayS93\LWModal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

const PLUGIN_DIR = __DIR__;
const DOMAIN = 'lightweight-modal';

// Нужно подключить заранее для подключения файлов @see include_required_files()
// активации и деактивации плагина @see activate(), uninstall();
require __DIR__ . '/utils.php';

class Plugin
{
    private static $initialized;
    private function __construct() {}

    static function activate()
    { 
        add_option( Utils::get_option_name(), array(
            'lib_props_openCloseEffect' => 'zoom',
            'lib_props_nextPrevEffect' => 'slide',
        ) );
    }

    static function uninstall() {

        delete_option( Utils::get_option_name() );
    }

    public static function initialize()
    {
        if( self::$initialized )
            return false;

        load_plugin_textdomain( DOMAIN, false, basename(PLUGIN_DIR) . '/languages/' );
        self::include_required_files();
        self::_actions();
        self::_filters();

        self::$initialized = true;
    }

    /**
     * Подключение файлов нужных для работы плагина
     */
    private static function include_required_files()
    {
        $dir_include = Utils::get_plugin_dir('includes');
        $libs        = Utils::get_plugin_dir('libs');

        $classes = array(
            __NAMESPACE__ . '\Example_List_Table' => $dir_include . '/wp-list-table.php',
            __NAMESPACE__ . '\WP_Admin_Page'      => $libs . '/wp-admin-page.php',
            __NAMESPACE__ . '\WP_Admin_Forms'     => $libs . '/wp-admin-forms.php',
            __NAMESPACE__ . '\WP_Post_Boxes'      => $libs . '/wp-post-boxes.php',
        );

        foreach ($classes as $classname => $path) {
            if( ! class_exists($classname) ) {
                Utils::load_file_if_exists( $path );
            }
            else {
                Utils::write_debug(sprintf( __('Duplicate class %s', DOMAIN), $classname ), __FILE__);
            }
        }

        // includes
        Utils::load_file_if_exists( $dir_include . '/register-post-type.php' );
        Utils::load_file_if_exists( $dir_include . '/shortcode.php' );
        Utils::load_file_if_exists( $dir_include . '/click-count.php' );
        Utils::load_file_if_exists( $dir_include . '/admin-settings-page.php' );
    }

    private static function _actions()
    {
        $class = __NAMESPACE__ . '\Modal';
        add_action('wp_footer', array($class, 'setup_footer'));
        add_shortcode( Utils::get_shortcode_name(), array($class, 'shortcode') );

        add_action( 'LWModal_body', array(__CLASS__, 'modal_window_head'), 10, 2 );
        add_action( 'LWModal_body', array(__CLASS__, 'modal_window_body'), 10, 2 );
    }

    static function modal_window_body( $modal, $type )
    {
        switch ( $type ) {
            case 'ajax':
            echo '<div style="min-width: 400px;" id="ajax_data_'.$modal->ID.'"> '. __( 'Loading..' ) .' </div>';
            break;

            case 'inline':
            default:
            echo apply_filters( 'the_content', $modal->post_content );
            break;
        }
    }

    static function modal_window_head( $modal, $type )
    {
        if( $modal )
            echo "<h2>{$modal->post_title}</h2>";
    }

    private static function _filters(){}
}



register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'activate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'uninstall' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Plugin', 'deactivate' ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Plugin', 'initialize' ), 10 );
