<?php
/**
 * Admin Settings for Vafa Chat Widget
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add admin menu item
 */
function vafa_chat_add_admin_menu() {
    add_options_page(
        __('Vafa Chat Widget Settings', 'vafa-chat-widget'),
        __('Vafa Chat Widget', 'vafa-chat-widget'),
        'manage_options',
        'vafa-chat-widget',
        'vafa_chat_settings_page'
    );
}
add_action('admin_menu', 'vafa_chat_add_admin_menu');

/**
 * Register settings
 */
function vafa_chat_register_settings() {
    register_setting(
        'vafa_chat_settings_group',
        'vafa_chat_settings',
        'vafa_chat_sanitize_settings'
    );
}
add_action('admin_init', 'vafa_chat_register_settings');

/**
 * Sanitize settings
 */
function vafa_chat_sanitize_settings($input) {
    $sanitized = array();
    
    $sanitized['token'] = sanitize_text_field($input['token']);
    $sanitized['welcome_title'] = sanitize_text_field($input['welcome_title']);
    $sanitized['initial_message'] = sanitize_text_field($input['initial_message']);
    $sanitized['default_question'] = sanitize_text_field($input['default_question']);
    $sanitized['suggested_questions'] = sanitize_textarea_field($input['suggested_questions']);
    $sanitized['api_base_url'] = sanitize_url($input['api_base_url']);
    $sanitized['enable_widget'] = isset($input['enable_widget']) ? 'yes' : 'no';
    $sanitized['include_user_data'] = isset($input['include_user_data']) ? 'yes' : 'no';
    
    return $sanitized;
}

/**
 * Settings page content
 */
function vafa_chat_settings_page() {
    $settings = get_option('vafa_chat_settings', array());
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Vafa Chat Widget Settings', 'vafa-chat-widget'); ?></h1>
        
        <form method="post" action="options.php">
            <?php settings_fields('vafa_chat_settings_group'); ?>
            <?php do_settings_sections('vafa_chat_settings_group'); ?>
            
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php echo esc_html__('Enable Widget', 'vafa-chat-widget'); ?></th>
                    <td>
                        <input type="checkbox" name="vafa_chat_settings[enable_widget]" value="yes" <?php checked('yes', isset($settings['enable_widget']) ? $settings['enable_widget'] : 'yes'); ?> />
                        <p class="description"><?php echo esc_html__('Enable or disable the chat widget on your site', 'vafa-chat-widget'); ?></p>
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php echo esc_html__('Assistant Token', 'vafa-chat-widget'); ?></th>
                    <td>
                        <input type="text" name="vafa_chat_settings[token]" value="<?php echo esc_attr(isset($settings['token']) ? $settings['token'] : ''); ?>" class="regular-text" required />
                        <p class="description"><?php echo esc_html__('Enter your Vafa Assistant token (required)', 'vafa-chat-widget'); ?></p>
                    </td>
                </tr>
                

                
                <tr valign="top">
                    <th scope="row"><?php echo esc_html__('Welcome Title', 'vafa-chat-widget'); ?></th>
                    <td>
                        <input type="text" name="vafa_chat_settings[welcome_title]" value="<?php echo esc_attr(isset($settings['welcome_title']) ? $settings['welcome_title'] : 'به سایت ما خوش آمدید'); ?>" class="regular-text" />
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php echo esc_html__('Initial Message', 'vafa-chat-widget'); ?></th>
                    <td>
                        <input type="text" name="vafa_chat_settings[initial_message]" value="<?php echo esc_attr(isset($settings['initial_message']) ? $settings['initial_message'] : 'سلام چطور میتونم کمکتون کنم؟'); ?>" class="regular-text" />
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php echo esc_html__('Default Question', 'vafa-chat-widget'); ?></th>
                    <td>
                        <input type="text" name="vafa_chat_settings[default_question]" value="<?php echo esc_attr(isset($settings['default_question']) ? $settings['default_question'] : 'کمک میخوام'); ?>" class="regular-text" />
                    </td>
                </tr>
                
                <tr valign="top">
                    <th scope="row"><?php echo esc_html__('Suggested Questions', 'vafa-chat-widget'); ?></th>
                    <td>
                        <textarea name="vafa_chat_settings[suggested_questions]" rows="5" class="large-text"><?php echo esc_textarea(isset($settings['suggested_questions']) ? $settings['suggested_questions'] : "در هاسکووب چیکار میکنید?\nمشاوره حرفه ای میخوام.\nراه اندازی و بهبود کسب و کارها در فضای آنلاین"); ?></textarea>
                        <p class="description"><?php echo esc_html__('Enter suggested questions, one per line', 'vafa-chat-widget'); ?></p>
                    </td>
                </tr>
                

                
                <tr valign="top">
                    <th scope="row"><?php echo esc_html__('Include User Data', 'vafa-chat-widget'); ?></th>
                    <td>
                        <input type="checkbox" name="vafa_chat_settings[include_user_data]" value="yes" <?php checked('yes', isset($settings['include_user_data']) ? $settings['include_user_data'] : 'yes'); ?> />
                        <p class="description"><?php echo esc_html__('Include WordPress user data in chat widget when user is logged in', 'vafa-chat-widget'); ?></p>
                    </td>
                </tr>
            </table>
            
            <h2><?php echo esc_html__('How to Use', 'vafa-chat-widget'); ?></h2>
            <p><?php echo esc_html__('Use shortcode [vafa_chat_widget] to embed the chat widget in your posts or pages.', 'vafa-chat-widget'); ?></p>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Add settings link on plugin page
 */
function vafa_chat_settings_link($links) {
    $settings_link = '<a href="options-general.php?page=vafa-chat-widget">' . __('Settings', 'vafa-chat-widget') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_vafa-chat-widget/vafa-chat-widget.php", 'vafa_chat_settings_link');
