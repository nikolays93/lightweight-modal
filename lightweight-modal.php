<?php

/*
Plugin Name: Легкие модальные (всплывающие) окна
Plugin URI: https://github.com/nikolays93/lightweight-modal
Description: Модальные окна для создания галерей, всплывающих форм и сообщений
Version: 0.2.1 (beta, development)
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CDevelopers\modal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

const DOMAIN = 'lightweight-modal';

class Utils
{
    const OPTION = 'lw-modal';

    private static $settings, $initialized, $posts;
    public static $active_modals = array();

    private function __construct() {}
    private function __clone() {}

    static function activate() {
        add_option( self::OPTION, array() );
    }

    static function uninstall() {
        delete_option(self::OPTION);
    }

    static function get_shortcode_name() {
        return apply_filters( 'lightweight-modal-shortcode-name', self::OPTION );
    }

    private static function include_required_classes()
    {
        $dir_include = self::get_plugin_dir('includes');
        $dir_class = self::get_plugin_dir('classes');

        $classes = array(
            __NAMESPACE__ . '\Example_List_Table' => $dir_include . '/wp-list-table.php',
            __NAMESPACE__ . '\WP_Admin_Page'      => $dir_class . '/wp-admin-page.php',
            __NAMESPACE__ . '\WP_Admin_Forms'     => $dir_class . '/wp-admin-forms.php',
            __NAMESPACE__ . '\WP_Post_Boxes'      => $dir_class . '/wp-post-boxes.php',
        );

        foreach ($classes as $classname => $dir) {
            if( ! class_exists($classname) ) {
                self::load_file_if_exists( $dir );
            }
        }

        // includes
        self::load_file_if_exists( $dir_include . '/register-post-type.php' );
        self::load_file_if_exists( $dir_include . '/shortcode.php' );
        self::load_file_if_exists( $dir_include . '/admin-page.php' );
    }

    public static function initialize()
    {
        if( self::$initialized ) {
            return false;
        }

        load_plugin_textdomain( DOMAIN, false, DOMAIN . '/languages/' );
        self::include_required_classes();

        self::$posts = get_posts( array( 'post_type' => self::OPTION ) );
        add_action( 'wp_enqueue_scripts',
            array(__CLASS__, 'enqueue_modal_scripts') );

        self::$initialized = true;
    }

    /**
     * Записываем ошибку
     */
    public static function write_debug( $msg, $dir )
    {
        if( ! defined('WP_DEBUG_LOG') || ! WP_DEBUG_LOG )
            return;

        $dir = str_replace(__DIR__, '', $dir);
        $msg = str_replace(__DIR__, '', $msg);

        $date = new \DateTime();
        $date_str = $date->format(\DateTime::W3C);

        if( $handle = @fopen(__DIR__ . "/debug.log", "a+") ) {
            fwrite($handle, "[{$date_str}] {$msg} ({$dir})\r\n");
            fclose($handle);
        }
        elseif (defined('WP_DEBUG_DISPLAY') && WP_DEBUG_DISPLAY) {
            echo sprintf( __('Can not have access the file %s (%s)', DOMAIN),
                __DIR__ . "/debug.log",
                $dir );
        }
    }

    /**
     * Загружаем файл если существует
     */
    public static function load_file_if_exists( $file_array, $args = array(), $once = false, $reqire = false )
    {
        $cant_be_loaded = __('The file %s can not be included', DOMAIN);
        if( is_array( $file_array ) ) {
            $result = array();
            foreach ( $file_array as $id => $path ) {
                if ( ! is_readable( $path ) ) {
                    self::write_debug(sprintf($cant_be_loaded, $path), __FILE__);
                    continue;
                }

                if( $reqire )
                    $result[] = ( $once ) ? require_once( $path ) : require( $path );
                else
                    $result[] = ( $once ) ? include_once( $path ) : include( $path );
            }
        }
        else {
            if ( ! is_readable( $file_array ) ) {
                self::write_debug(sprintf($cant_be_loaded, $file_array), __FILE__);
                return false;
            }

            if( $reqire )
                $result = ( $once ) ? require_once( $file_array ) : require( $file_array );
            else
                $result = ( $once ) ? include_once( $file_array ) : include( $file_array );
        }

        return $result;
    }

    public static function get_plugin_dir( $path = false )
    {
        $result = __DIR__;

        switch ( $path ) {
            case 'classes': $result .= '/includes/classes'; break;
            case 'settings': $result .= '/includes/settings'; break;
            default: $result .= '/' . $path;
        }

        return $result;
    }

    public static function get_plugin_url( $path = false )
    {
        $result = plugins_url(basename(__DIR__) );

        switch ( $path ) {
            default: $result .= '/' . $path;
        }

        return $result;
    }

    /**
     * Получает настройку из self::$settings или из кэша или из базы данных
     */
    public static function get( $prop_name, $default = false )
    {
        if( ! self::$settings )
            self::$settings = get_option( self::OPTION, array() );

        if( 'all' === $prop_name ) {
            if( is_array(self::$settings) && count(self::$settings) )
                return self::$settings;

            return $default;
        }

        return isset( self::$settings[ $prop_name ] ) ? self::$settings[ $prop_name ] : $default;
    }

    public static function get_settings( $filename, $args = array() )
    {

        return self::load_file_if_exists( self::get_plugin_dir('settings') . '/' . $filename, $args );
    }

    static function enqueue_modal_scripts()
    {
        $assets = self::get_plugin_url('assets');
        $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '.min' : '';
        $props = wp_parse_args( self::get( 'lib_props' ), array(
            'modal_type' => false,
            'modal_selector' => false,
            ) );

        $has_modals = sizeof(self::$posts) >= 1;
        if( $has_modals || $props['modal_selector'] ) {
            wp_enqueue_script( 'modal_script', $assets . '/public.js',
                array('jquery'), '1.0', true );
        }

        $props['modals'] = array();
        if( $has_modals ) {
            foreach (self::$posts as $post) {
                $props['modals'][ $post->ID ] = array(
                    'trigger_type'   => get_post_meta( $post->ID, '_trigger_type', true ),
                    'trigger'        => get_post_meta( $post->ID, '_trigger', true ),
                    'disable_ontime' => get_post_meta( $post->ID, '_disable_ontime', true ),
                    'modal_type'     => get_post_meta( $post->ID, '_modal_type', true ),
                );
            }
        }

        if( 'fancybox3' === $props['modal_type'] ){
            wp_enqueue_script(
                'fancybox3',
                $assets . "/fancybox3/jquery.fancybox{$affix}.js",
                array('jquery'),
                '3.0',
                true
                );
            wp_enqueue_style(
                'fancybox3',
                $assets . "/fancybox3/jquery.fancybox{$affix}.css",
                null,
                '3.0'
                );
        }

        wp_localize_script( 'modal_script', 'LWModals', $props );
        wp_localize_script( 'modal_script', 'LWM_Settings', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce( 'Secret' ),
            ) );
    }
}

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Utils', 'activate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Utils', 'uninstall' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Utils', 'deactivate' ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Utils', 'initialize' ), 10 );
