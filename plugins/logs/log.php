<?php

/**
 * Plugin Name:       Log 
 * Plugin URI:        http://localhost/log-testing/
 * Description:       A simple plugin for creating custom post types and get post types content.
 * Version:           1.0.0
 * Author:            David Kohav     
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       logs
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Require
 */
require_once __DIR__ . '/post-types/register.php';


class Log {
    /**
     * Define Plugin Version
     */
    const VERSION = '1.0.0';


    /**
     * Construct Function
     */
    public function __construct() {
        $this->plugin_constants();
        add_action('init', 'log_register');
        add_action('wp_enqueue_scripts', [$this, 'load_js']);
        add_action( 'save_post', [$this, 'get_post_type_content']); 
    }


    /**
     * Plugin Constants
     * @since 1.0.0
     */
    public function plugin_constants() {
        define( 'LOG_VERSION', self::VERSION );
        define( 'LOG_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
        define( 'LOG_PLUGIN_URL', plugins_url( '', __FILE__ ) );
    }

    /**
     * Singletone Instance
     * @since 1.0.0
     */
    public static function init() {
        static $instance = false;

        if( !$instance ) {
            $instance = new self();
        }

        return $instance;
    }

    private function create_log($post_type_content) {

        $content = __('A new page of type '. $post_type_content->post_type . 
                ' is created and is name ' . html_entity_decode($post_type_content->post_title) .
                ' by user ' . $post_type_content->post_author);
         
    
         // Create post object
        $my_post = array(
            'post_title'    => $post_type_content->post_modified,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_author'   => $post_type_content->post_author,
            'post_type'     => 'log'
        );
        
        // Insert the post into the database
        
         $post_id = wp_insert_post( $my_post );
         if(!is_wp_error($post_id)){
            //the post is valid
          }else{
            //there was an error in the post insertion, 
            echo $post_id->get_error_message();
          }
    
    
    }

    public function get_post_type_content($post_id) {
        $post_type_content = get_post($post_id);
        $post_type = $post_type_content->post_type;
        if (($post_type == 'post') || ($post_type == 'page')) {
            $this->create_log($post_type_content);
        }
    
    }

    public function load_js() {
        wp_register_script('log', plugins_url('log.js', __FILE__), array('jquery', 'jquery-ui-datepicker'));
        wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css' );
        wp_enqueue_style( 'jquery-ui' );
        wp_enqueue_script('log');
        wp_localize_script( 'log', 'postdata',
            array(
                'theme_uri' => get_stylesheet_directory_uri(),
                'rest_url' => rest_url('wp/v2/'),
            )
        );
    }
}



/**
 * Initialize Main Plugin
 * @since 1.0.0
 */
function logs_kickstart() {
    return Log::init();
}

// Run the Plugin
logs_kickstart();