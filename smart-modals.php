<?php
/*
Plugin Name: Новый плагин
Plugin URI:
Description:
Version: 0.0
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
class SMODALS
{
    const SETTINGS = __CLASS__;

    private static $posts = array();

    public static $settings = array();

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

        add_action( 'wp_enqueue_scripts', array(__CLASS__, 'enqueue_modal_scripts') );
        add_action( 'wp_footer', array(__CLASS__, 'set_modals') );
    }

    private static function include_required_classes(){
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

        // wp_enqueue_script( 'smodals', SMODALS_ASSETS . '/public.js', array('jquery'), '1.0', true );
    }

    static function set_modals()
    {
        echo "<a href='#' data-fancybox data-src='#modal_254' href='javascript:;'>Open</a>";
        foreach (self::$posts as $post) {
            echo "<div id='modal_{$post->ID}' style='display: none;'>{$post->ID}</div>";
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

add_action('wp_ajax_nopriv_view', 'my_action_callback');
add_action('wp_ajax_view', 'my_action_callback');
function my_action_callback() {
    var_dump($_REQUEST['action']);
    // if( ! wp_verify_nonce( $_POST['nonce'], 'any_secret_string' ) ){
    //     echo 'Ошибка! нарушены правила безопасности';
    //     wp_die();
    // }

    $post = get_post(254);
    echo apply_filters( 'the_content', $post->post_content );

    // do something.. for ex:
    echo intval( $_POST['whatever'] );
    wp_die();
}