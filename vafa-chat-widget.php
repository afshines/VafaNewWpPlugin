<?php
/**
 * Plugin Name: Vafa Chat Widget
 * Plugin Name (fa_IR): ویجت گفتگوی وفا
 * Plugin URI: https://vafaai.com
 * Description: Embed the Vafa AI Chat Widget in your WordPress site with easy configuration options.
 * Description (fa_IR): ویجت هوش مصنوعی وفا را در سایت وردپرس خود با تنظیمات آسان جاسازی کنید.
 * Version: 1.0.0
 * Author: Vafa AI
 * Author URI: https://vafaai.com
 * Text Domain: vafa-chat-widget
 * Domain Path: /languages
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VAFA_CHAT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('VAFA_CHAT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VAFA_CHAT_VERSION', '1.0.0');

// Include required files
require_once VAFA_CHAT_PLUGIN_DIR . 'includes/admin-settings.php';
require_once VAFA_CHAT_PLUGIN_DIR . 'includes/frontend.php';

/**
 * Initialize the plugin
 */
function vafa_chat_init() {
    // Create necessary directories on activation
    if (!file_exists(VAFA_CHAT_PLUGIN_DIR . 'includes')) {
        mkdir(VAFA_CHAT_PLUGIN_DIR . 'includes', 0755);
    }
    
    // Load plugin translations
    load_plugin_textdomain('vafa-chat-widget', false, basename(VAFA_CHAT_PLUGIN_DIR) . '/languages');
}
add_action('plugins_loaded', 'vafa_chat_init');

/**
 * Plugin activation hook
 */
function vafa_chat_activate() {
    // Initialize default settings
    $default_settings = array(
        // Basic settings
        'token' => '',
        'welcome_title' => 'به سایت ما خوش آمدید',
        'initial_message' => 'سلام چطور میتونم کمکتون کنم؟',
        'default_question' => 'کمک میخوام',
        'suggested_questions' => "در هاسکووب چیکار میکنید?\nمشاوره حرفه ای میخوام.\nراه اندازی و بهبود کسب و کارها در فضای آنلاین",
        
        // API Configuration
        'api_base_url' => 'https://api.vafaai.com',
        'api_timeout' => 30,
        'api_retry_attempts' => 3,
        
        // Widget settings
        'enable_widget' => 'yes',
        'include_user_data' => 'yes'
    );
    
    add_option('vafa_chat_settings', $default_settings);
}
register_activation_hook(__FILE__, 'vafa_chat_activate');

/**
 * Plugin deactivation hook
 */
function vafa_chat_deactivate() {
    // Cleanup if needed
}
register_deactivation_hook(__FILE__, 'vafa_chat_deactivate');
