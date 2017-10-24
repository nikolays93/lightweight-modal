<?php
/*
Plugin Name: Легкие модальные (всплывающие) окна
Plugin URI: https://github.com/nikolays93/lightweight-modal
Description: Модальные окна для создания галерей, всплывающих форм и сообщений
Version: 1.1 beta
Author: NikolayS93
Author URI: https://vk.com/nikolays_93
Author EMAIL: nikolayS93@ya.ru
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

namespace CDevelopers\modal;

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

define('DIR', rtrim( plugin_dir_path( __FILE__ ), '/') );
define('DIR_CLASSES', DIR . '/includes/classes' );
define('URL', rtrim(plugins_url(basename(__DIR__)), '/') );
define('URL_ASSETS', URL . '/assets' );
define('LANG', basename(__FILE__, '.php') );

require_once DIR . '/includes/utils.php';

register_activation_hook( __FILE__, array( __NAMESPACE__ . '\Utils', 'activate' ) );
register_uninstall_hook( __FILE__, array( __NAMESPACE__ . '\Utils', 'uninstall' ) );
// register_deactivation_hook( __FILE__, array( __NAMESPACE__ . '\Utils', 'deactivate' ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Utils', 'get_instance' ), 10 );

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