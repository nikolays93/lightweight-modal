<?php
/*
Plugin Name: Модальные (всплывающие) окна
Plugin URI:
Description:
Version: 1.0
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

if( defined('SMODALS_DIR') ) {
    return;
}

define('SMODALS_DIR', rtrim( plugin_dir_path( __FILE__ ), '/') );
define('SMODALS_ASSETS', rtrim( plugins_url( basename(__DIR__) ), '/' ) . '/includes/assets' );

register_activation_hook( __FILE__, array( 'SMODALS', 'activate' ) );
// register_deactivation_hook( __FILE__, array( 'SMODALS', 'deactivate' ) );
register_uninstall_hook( __FILE__, array( 'SMODALS', 'uninstall' ) );

add_action( 'plugins_loaded', array('SMODALS', 'init'), 10 );
class SModals
{
    const SETTINGS = __CLASS__;

    private static $posts = array();

    public static $settings = array();

    protected static $modal_ids = array();

    static function activate()
    {
        add_option( self::SETTINGS, array() );
    }

    static function uninstall()
    {
        delete_option(self::SETTINGS);
    }

    public static function init()
    {
        self::$settings = get_option( self::SETTINGS, array() );
        self::include_required_classes();

        self::$posts = get_posts( array(
            'post_type' => SMODALS::SETTINGS,
        ) );

        add_shortcode( 'smodal', array(__CLASS__, 'smodal_shortcode') );
        add_action( 'wp_enqueue_scripts', array(__CLASS__, 'enqueue_modal_scripts') );
        add_action( 'wp_footer', array(__CLASS__, 'add_modals') );
    }

    private static function include_required_classes()
    {
        // Classes
        require_once SMODALS_DIR . '/includes/classes/wp-list-table.php';
        require_once SMODALS_DIR . '/includes/classes/wp-admin-page.php';
        require_once SMODALS_DIR . '/includes/classes/wp-admin-forms.php';
        require_once SMODALS_DIR . '/includes/classes/wp-post-boxes.php';

        // includes
        require_once SMODALS_DIR . '/includes/register-post_type.php';
        require_once SMODALS_DIR . '/includes/admin-list-page.php';
    }

    static function enqueue_modal_scripts()
    {
        $affix = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '.min' : '';
        $props = wp_parse_args( isset(self::$settings['lib_props']) ? self::$settings['lib_props'] : array(), array(
            'modal_type' => false,
            'modal_selector' => false
        ) );

        if( sizeof(self::$posts) >= 1 || $props['modal_selector'] ) {
            wp_enqueue_script( 'smodals', SMODALS_ASSETS . '/public.js', array('jquery'), '1.0', true );
        }

        // if( $props['modal_selector'] ) {
            if( 'fancybox3' === $props['modal_type'] ){
                wp_enqueue_script(
                    'fancybox3',
                    SMODALS_ASSETS . "/fancybox3/jquery.fancybox{$affix}.js",
                    array('jquery'),
                    '3.0',
                    true
                    );

                wp_enqueue_style(
                    'fancybox3',
                    SMODALS_ASSETS . "/fancybox3/jquery.fancybox{$affix}.css",
                    null,
                    '3.0'
                    );
            }

            wp_localize_script( 'smodals', 'SModals', $props );
        // }
    }

    static function smodal_shortcode( $atts = array(), $content = '' )
    {
        $atts = shortcode_atts( array(
            'id'      => 0,
            'href'    => '#',
            'class'   => '',
            'link_id' => '',
        ), $atts, 'smodal' );

        if( ! $content || 0 >= $modal_id = absint($atts['id']) ) {
            return false;
        }

        self::$modal_ids[] = $modal_id;

        return sprintf('<a href="%s" data-fancybox data-src="#modal_%d" id="%s" class="%s" href="javascript:;">%s</a>',
            esc_url( $atts['href'] ),
            $modal_id,
            esc_attr( $atts['link_id'] ),
            esc_attr( $atts['class'] ),
            $content
        );
    }

    static function add_modals() {
        foreach (self::$modal_ids as $post_id) {
            $_post = get_post( $post_id );

            echo "<div id='modal_{$_post->ID}' style='display: none;'>";
            switch ( get_post_meta( $_post->ID, '_modal_type', true ) ) {
                case 'ajax':
                    echo '<div style="min-width: 400px;" id="ajax_data_'.$_post->ID.'"> '. __( 'Loading..' ) .' </div>';
                    break;

                case 'inline':
                default:
                    echo apply_filters( 'the_content', $_post->post_content );
                    break;
            }
            echo "</div>";
        }
    }
}

// add_action('', '');
// function enqueue_modal_scripts() {

//     $selectors = array();
//     foreach ($posts as $post) {
//         echo "<div class='container'> {$post->post_content} </div>";

//         $selectors[ '#modal_' . $post->ID ] = get_post_meta( $post->ID, '_selector', true );
//     }

//     wp_localize_script( 'smodals', 'smodals_opt', array(
//         'selectors' => json_encode($selectors),
//     ) );
// }

// add_action('wp_ajax_nopriv_view', 'my_action_callback');
// add_action('wp_ajax_view', 'my_action_callback');
// function my_action_callback() {
//     var_dump($_REQUEST['action']);
//     // if( ! wp_verify_nonce( $_POST['nonce'], 'any_secret_string' ) ){
//     //     echo 'Ошибка! нарушены правила безопасности';
//     //     wp_die();
//     // }

//     $post = get_post(254);
//     echo apply_filters( 'the_content', $post->post_content );

//     // do something.. for ex:
//     echo intval( $_POST['whatever'] );
//     wp_die();
// }