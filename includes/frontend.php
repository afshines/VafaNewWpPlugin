<?php
/**
 * Frontend functionality for Vafa Chat Widget
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get widget version from the server
 */
function vafa_chat_get_widget_version() {
    $settings = get_option('vafa_chat_settings', array());
    $api_base_url = isset($settings['api_base_url']) ? $settings['api_base_url'] : 'https://api.vafaai.com';
    $version_endpoint = $api_base_url . '/public/widget-version';
    $version = VAFA_CHAT_VERSION; // Default fallback version
    
    // Store the fetched version in a transient to avoid making requests on every page load
    // The transient will expire after 1 hour (3600 seconds)
    $transient_key = 'vafa_widget_version';
    $cached_version = get_transient($transient_key);
    
    if ($cached_version !== false) {
        return $cached_version;
    }
    
    // Fetch the version from the server
    $response = wp_remote_get($version_endpoint, array(
        'timeout' => 5,
        'sslverify' => true,
    ));
    
    if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['success']) && $data['success'] && isset($data['version'])) {
            $version = $data['version'];
            // Cache the version for 1 hour
            set_transient($transient_key, $version, HOUR_IN_SECONDS);
        }
    }
    
    return $version;
}

/**
 * Enqueue scripts and styles
 */
function vafa_chat_enqueue_scripts() {
    $settings = get_option('vafa_chat_settings', array());
    
    // Don't load if widget is disabled
    if (isset($settings['enable_widget']) && $settings['enable_widget'] !== 'yes') {
        return;
    }
    
    // Get the widget version from the server
    $widget_version = vafa_chat_get_widget_version();
    
    // Enqueue Vue.js (required dependency)
    wp_enqueue_script(
        'vue-js',
        'https://unpkg.com/vue@3/dist/vue.global.js',
        array(),
        '3.0.0',
        true
    );
    
    // Get widget files from vafaai.com
    $widget_base_url = 'https://vafaai.com/widget';
    
    // Enqueue Vafa Chat Widget script from CDN/remote URL
    wp_enqueue_script(
        'vafa-chat-widget',
        $widget_base_url . '/vafa-chat-widget.umd.js?ver=' . $widget_version,
        array('vue-js'),
        null, // Set version to null since we're adding it directly to the URL
        true
    );
    
    // Instead of enqueueing CSS in the header, we'll add it to the footer
    add_action('wp_footer', function() use ($widget_base_url, $widget_version) {
        echo '<link rel="stylesheet" href="' . esc_url($widget_base_url . '/style.css?ver=' . $widget_version) . '" media="all">';
    });
    
    // Get current user data if logged in and if including user data is enabled
    $user_data = array();
    if (isset($settings['include_user_data']) && $settings['include_user_data'] === 'yes' && is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_data = array(
            'id' => $current_user->ID,
            'name' => $current_user->display_name,
            'email' => $current_user->user_email,
            'roles' => $current_user->roles,
        );
    }
    
    // Prepare suggested questions
    $suggested_questions = array();
    if (!empty($settings['suggested_questions'])) {
        $suggested_questions = array_filter(array_map('trim', explode("\n", $settings['suggested_questions'])));
    }
    
    // Localize script with settings
    wp_localize_script('vafa-chat-widget', 'vafaChatSettings', array(
        // Basic settings
        'token' => isset($settings['token']) ? $settings['token'] : '',
        'welcomeTitle' => isset($settings['welcome_title']) ? $settings['welcome_title'] : 'به سایت ما خوش آمدید',
        'initialMessage' => isset($settings['initial_message']) ? $settings['initial_message'] : 'سلام چطور میتونم کمکتون کنم؟',
        'defaultQuestion' => isset($settings['default_question']) ? $settings['default_question'] : 'کمک میخوام',
        'suggestedQuestions' => $suggested_questions,
        
        // API Configuration
        'apiBaseUrl' => isset($settings['api_base_url']) ? $settings['api_base_url'] : 'https://api.vafaai.com',
        'apiTimeout' => isset($settings['api_timeout']) ? intval($settings['api_timeout']) : 30,
        'apiRetryAttempts' => isset($settings['api_retry_attempts']) ? intval($settings['api_retry_attempts']) : 3,
        
        // User data
        'userData' => $user_data,
    ));
    
    // Enqueue our CORS fix script
    wp_enqueue_script(
        'vafa-chat-cors-fix',
        VAFA_CHAT_PLUGIN_URL . 'assets/js/vafa-chat-cors-fix.js',
        array('vafa-chat-widget'),
        $widget_version,
        true
    );
}
add_action('wp_enqueue_scripts', 'vafa_chat_enqueue_scripts');

/**
 * Register shortcode for the widget
 */
function vafa_chat_shortcode($atts) {
    $settings = get_option('vafa_chat_settings', array());
    
    // Don't load if widget is disabled
    if (isset($settings['enable_widget']) && $settings['enable_widget'] !== 'yes') {
        return '';
    }
    
    // Make sure scripts are enqueued
    vafa_chat_enqueue_scripts();
    
    // Return container div
    return '<div style="z-index: 99999999;" id="vafa-chat-container"></div>';
}
add_shortcode('vafa_chat_widget', 'vafa_chat_shortcode');
