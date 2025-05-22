<?php
/**
 * Frontend functionality for Vafa Chat Widget
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
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
        $widget_base_url . '/vafa-chat-widget.umd.js',
        array('vue-js'),
        VAFA_CHAT_VERSION,
        true
    );
    
    // Enqueue Vafa Chat Widget styles from CDN/remote URL
    wp_enqueue_style(
        'vafa-chat-widget-style',
        $widget_base_url . '/style.css',
        array(),
        VAFA_CHAT_VERSION
    );
    
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
        'token' => isset($settings['token']) ? $settings['token'] : '',
        'welcomeTitle' => isset($settings['welcome_title']) ? $settings['welcome_title'] : 'به سایت ما خوش آمدید',
        'initialMessage' => isset($settings['initial_message']) ? $settings['initial_message'] : 'سلام چطور میتونم کمکتون کنم؟',
        'defaultQuestion' => isset($settings['default_question']) ? $settings['default_question'] : 'کمک میخوام',
        'suggestedQuestions' => $suggested_questions,
        'apiBaseUrl' => isset($settings['api_base_url']) ? $settings['api_base_url'] : 'https://api.vafaai.com',
        'userData' => $user_data,
    ));
    
    // Initialize widget
    wp_add_inline_script('vafa-chat-widget', '
        document.addEventListener("DOMContentLoaded", function() {
            if (typeof VafaChatWidget === "undefined") {
                console.error("VafaChatWidget not loaded properly");
                return;
            }
            
            // Check if the container already exists
            let chatContainer = document.getElementById("vafa-chat-container");
            
            // Create container if it doesn\'t exist
            if (!chatContainer) {
                chatContainer = document.createElement("div");
                chatContainer.id = "vafa-chat-container";
                document.body.appendChild(chatContainer);
            }
            
            // Initialize widget with settings
            VafaChatWidget.init("#vafa-chat-container", {
                token: vafaChatSettings.token,
                welcomeTitle: vafaChatSettings.welcomeTitle,
                initialMessage: vafaChatSettings.initialMessage,
                defaultQuestion: vafaChatSettings.defaultQuestion,
                suggestedQuestions: vafaChatSettings.suggestedQuestions,
                apiBaseUrl: vafaChatSettings.apiBaseUrl,
                userData: vafaChatSettings.userData,
            });
        });
    ');
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
