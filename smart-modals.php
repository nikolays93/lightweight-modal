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
}
