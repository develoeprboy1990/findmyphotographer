<?php

/**
 * Plugin Name: ZTS PHOTOGRA
 * Plugin URI:  https://findmyphotographer.ca/
 * Description: This Plugin contains the custom features of site.
 * Version: 2.1
 * Author:  Junaid Elahi
 * Author URI: https://findmyphotographer.ca/
 */
defined('ABSPATH') || die('Cant Access To Plugin');
require_once ABSPATH . 'wp-admin/includes/plugin.php';
/**
 * Create Custom table.
 *
 * @since 1.1.0
 */
register_activation_hook(__FILE__, 'zts_custom_table');
function zts_custom_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'zts_user_data';
    // SQL query to create/update the custom table
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id VARCHAR(255) NOT NULL,
        customer_plan VARCHAR(255) NOT NULL,
        order_id VARCHAR(255) NOT NULL,
        company_name VARCHAR(255),
        company_url VARCHAR(255),
        profile_image VARCHAR(255),
        gallery_images TEXT,
        phone_number VARCHAR(20),
        categories TEXT,
        locations TEXT,
        priority INT(11) DEFAULT 0,
        status VARCHAR(20) DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        expiry INT(11) DEFAULT 0, -- New column
        PRIMARY KEY (id)
    )";
    // Execute the SQL query
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
/**
 * Enqueue Scripts for front end.
 *
 * @since 1.1.0
 */
add_action('wp_enqueue_scripts', 'max_add_style_sheets');
function max_add_style_sheets()
{
    // select 2 css.
    wp_register_style('zts_business_css', plugin_dir_url(__FILE__) . 'assets/css/business.css', array(), '4.0.13');
    wp_register_style('select2_css', plugin_dir_url(__FILE__) . 'assets/css/select2.min.css', array(), '4.0.13');

    // gallery css files
    wp_register_style('zts_gallery_bootstrap_css', plugin_dir_url(__FILE__) . 'assets/css/gallery/bootstrap.min.css', array(), '4.0.13');
    wp_register_style('zts_gallery_font_awsome', plugin_dir_url(__FILE__) . 'assets/css/gallery/font-awesome_all.css', array(), '4.0.13');
    wp_register_style('zts_gallery_fileinput', plugin_dir_url(__FILE__) . 'assets/css/gallery/fileinput.min.css', array(), '4.0.13');
    wp_register_style('zts_gallery_css', plugin_dir_url(__FILE__) . 'assets/css/gallery/gallery.css', array(), '4.0.13');
    wp_register_style('zts_pagination_css', plugin_dir_url(__FILE__) . 'assets/pagination/simplePagination.css', array(), '4.0.13');
    // Enqueue Select2 JavaScript file
    // wp_register_script( 'select2_js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array( 'jquery' ), '4.0.14', true );
    wp_register_script('zts_pagination_js',  plugin_dir_url(__FILE__) . 'assets/pagination/jquery.simplePagination.js', array('jquery'), '4.0.13', true);
    wp_register_script('select2_js',  plugin_dir_url(__FILE__) . 'assets/js/select2.min.js', array('jquery'), '4.0.13', true);
    wp_enqueue_script('select2_js');
    // Gallery js files.
    wp_register_script('zts_bs_file_input',  plugin_dir_url(__FILE__) . 'assets/js/gallery/bootstrap-fileinput.min.js', array('jquery'), '4.0.13', true);
    wp_register_script('zts_file_input_theme',  plugin_dir_url(__FILE__) . 'assets/js/gallery/bootstrap-fileinput_themes_theme.min.js', array('jquery'), '4.0.13', true);
    wp_register_script('zts_bs_min',  plugin_dir_url(__FILE__) . 'assets/js/gallery/bootstrap.min.js', array('jquery'), '4.0.13', true);
    wp_register_script('zts_bs_pooper',  plugin_dir_url(__FILE__) . 'assets/js/gallery/popper.min.js', array('jquery'), '4.0.13', true);
    wp_register_script('zts_gallery_main_js',  plugin_dir_url(__FILE__) . 'assets/js/gallery/gallery.js', array('jquery'), '4.0.13', true);
    // custom js and ajax calls.
    wp_register_script('zts_ajax_script', plugin_dir_url(__FILE__) . 'assets/js/script.js', array(), '1.1.0', true);
    wp_enqueue_script('zts_ajax_script');
    $get_setting =  get_option('mma_settings');
    $localized_vars = array(
        'ajax_url'       => admin_url('admin-ajax.php'),
        'zts_site_url'   => get_site_url(),
        'login'          => is_user_logged_in(),
        'loader_gif_url' => plugin_dir_url(__FILE__) . 'assets/images/g-loader.gif',
        'gallery_limit'  => !empty($get_setting['zts_gallery_size']) ? $get_setting['zts_gallery_size'] : '10',
        'category_limit' => !empty($get_setting['zts_location_size']) ? $get_setting['zts_location_size'] : '3'
    );
    wp_localize_script('zts_ajax_script', 'zts_ajax_url', $localized_vars);
}
// add_action('wp_login', 'zts_redirect_after_login', 10, 2);
// function zts_redirect_after_login($user_login, $user) {
//     if (isset($_COOKIE['zts_form_data']) && !empty($_COOKIE['zts_form_data'])) {
//         wp_safe_redirect(wc_get_checkout_url());
//         exit;
//     } 
// }

// add_action('user_register', 'zts_redirect_after_register', 10, 1);
// function zts_redirect_after_register($user_id) {
//     if (isset($_COOKIE['zts_form_data']) && !empty($_COOKIE['zts_form_data'])) {
//         wp_safe_redirect(wc_get_checkout_url());
//         exit;
//     }
// }

///////////// checkout data
function add_custom_checkout_fields()
{
    if (!is_user_logged_in()) {
        echo '<div id="custom_checkout_fields">
            <h3>' . __('Account Details', 'your-text-domain') . '</h3>
            <p class="form-row form-row-wide">
                <label for="account_email">' . __('Email Address', 'your-text-domain') . ' <span class="required">*</span></label>
                <input type="email" class="input-text" name="account_email" id="account_email" required />
            </p>
            <p class="form-row form-row-wide">
                <label for="account_password">' . __('Password', 'your-text-domain') . ' <span class="required">*</span></label>
                <input type="password" class="input-text" name="account_password" id="account_password" required />
            </p>
        </div>';
    }
}
add_action('woocommerce_checkout_before_customer_details', 'add_custom_checkout_fields');

function store_custom_checkout_fields()
{
    if (!is_user_logged_in()) {
        if (!empty($_POST['account_email'])) {
            WC()->session->set('account_email', sanitize_email($_POST['account_email']));
        }
        if (!empty($_POST['account_password'])) {
            WC()->session->set('account_password', sanitize_text_field($_POST['account_password']));
        }
    }
}
add_action('woocommerce_checkout_process', 'store_custom_checkout_fields');

function register_user_after_order($order_id)
{
    if (!is_user_logged_in()) {
        $order = wc_get_order($order_id);
        $account_email = WC()->session->get('account_email');
        $account_password = WC()->session->get('account_password');
        if (empty($account_email) || empty($account_password)) {
            return;
        }
        // Check if the email is not already registered
        $user = get_user_by('email', $account_email);
        if (!$user) {
            // Register the new user
            $user_id = wp_create_user($account_email, $account_password, $account_email);
            if (!is_wp_error($user_id)) {
                // Set the user role (optional)
                $user = new WP_User($user_id);
                $user->set_role('customer');
                // Send a registration email (optional)
                wp_new_user_notification($user_id, null, 'user');
                // Update the order user ID
                $order->set_customer_id($user_id);
                $order->save();
            }
        }
        // Clear the session data
        WC()->session->__unset('account_email');
        WC()->session->__unset('account_password');
    } else {
        $user_id = get_current_user_id();
    }
    global $wpdb;
    $table_name = $wpdb->prefix . 'zts_user_data';
    // Check if the user record exists
    $user_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE user_id = %s AND order_id !=''", $user_id));
    if ($user_exists) {
        // User record exists, update the fields
        $wpdb->update(
            $table_name,
            array(
                'expiry' => 0,
                'status' => 'active',
                'created_at' => current_time('mysql', 1)
            ),
            array('user_id' => $user_id),
            array(
                '%d', // expiry data type
                '%s', // status data type
                '%s'  // created_at data type
            ),
            array('%s') // user_id data type
        );

        return;
    }
    if (isset($_COOKIE['zts_form_data']) && !empty($_COOKIE['zts_form_data'])) {
        $cookies_data   = $_COOKIE['zts_form_data'];
        $remove_slashes = stripslashes($cookies_data);
        $get_form_array = json_decode($remove_slashes);
        global $wpdb;
        $table_name = $wpdb->prefix . 'zts_user_data';

        if ($get_form_array->product_scope == 'free') {
            $data = array(
                'customer_plan'    => $get_form_array->product_id,
                'company_name'     => $get_form_array->company_name,
                'order_id'         => $order_id,
                'company_url'      => $get_form_array->company_url,
                'categories'       => serialize($get_form_array->categories),
                'locations'        => serialize($get_form_array->location_id),
                'priority'         => '3',
                'listing_url'      => get_site_url() . '/profile-page/?user=' . $get_form_array->company_name
            );
        } else {
            $data = array(
                'customer_plan'    => $get_form_array->product_id,
                'company_name'     => $get_form_array->company_name,
                'order_id'         => $order_id,
                'phone_number'     => $get_form_array->company_phone,
                'company_url'      => $get_form_array->company_url,
                'profile_image'    => $get_form_array->profile_img,
                'gallery_images'   => serialize($get_form_array->gallery),
                'categories'       => serialize($get_form_array->categories),
                'locations'        => serialize($get_form_array->location_id),
                'priority'         => '2',
                'listing_url'      => get_site_url() . '/profile-page/?user=' . $get_form_array->company_name
            );
        }
        // Check if the user's data already exists
        $user_data = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE user_id = %d AND order_id !=''", $user_id));
        if ($user_data) {
            // Update the existing record
            $wpdb->update($table_name, $data, array('user_id' => $user_id));
        } else {
            // Insert a new record
            $data['user_id'] = $user_id;
            if (get_current_user_id()) { //mutahir
                $wpdb->update($table_name, array('status' => 'upgraded'), array('user_id' => $user_id));
            }
            $wpdb->insert($table_name, $data);
        }
    }
}
add_action('woocommerce_thankyou', 'register_user_after_order', 10, 1);
add_filter('woocommerce_thankyou_order_received_text', 'zts_change_data_on_thankou_page', 10, 2);
function zts_change_data_on_thankou_page($thankyou_message, $order)
{
    $thankyou_message = 'Your Plan Has Been Successfully Purchased';
    return $thankyou_message;
}
function zts_change_thankyou_text($translated_text, $text, $domain)
{
    // Check if the text domain is 'woocommerce' and the text matches 'Order received'
    if ($domain === 'woocommerce' && $text === 'Order received') {
        // Modify the translated text as desired
        $translated_text = 'Thank You For Your Order';
    }
    return $translated_text;
}
add_filter('gettext', 'zts_change_thankyou_text', 20, 3);
add_filter('woocommerce_order_item_quantity_html', 'zts_remove_qunatity_from_thankyou_page', 10, 2);
function zts_remove_qunatity_from_thankyou_page($quantity_html, $item)
{
    return '';
}
add_action('woocommerce_thankyou', 'zts_remove_shipping_from_thankyou', 10, 1);
function zts_remove_shipping_from_thankyou($order_id)
{
    $order = wc_get_order($order_id);
    if (!$order->needs_shipping_address()) {
        remove_action('woocommerce_thankyou', 'woocommerce_order_shipping_to_display', 10);
    }
}
// Add custom button to WooCommerce thank you page
add_action('woocommerce_thankyou', 'add_custom_button_to_thankyou_page');
function add_custom_button_to_thankyou_page($order_id)
{
    if (!is_user_logged_in()) {
        echo 'Your account has been successfully registered. Please login to see your directory.';
        echo "<br>";
        echo '<a style="margin-top:20px;" href="' . get_site_url() . '/my-account" class="button">Login</a>';
    }
}
function zts_login_shortcodes()
{
    if (is_user_logged_in()) {
        $current_user = wp_get_current_user();
        $user_name = $current_user->display_name;
        $logout_url = wp_logout_url(home_url());
        $output =  '<a href="' . $logout_url . '">Logout</a>';
    } else {
        $my_account_url = get_permalink(get_option('woocommerce_myaccount_page_id'));
        $output = '<a href="' . $my_account_url . '">LOG IN</a>';
    }
    return $output;
}
add_shortcode('ZTS_LOGIN_USER', 'zts_login_shortcodes');
function zts_add_business_btn_shortcodes()
{
    if (is_user_logged_in()) {
        $url = get_site_url() . '/my-account/zts-tab';
        $output = '<a class="add-business-btn" href="' . $url . '"><span class="add-business-button-content-wrapper"><i aria-hidden="true" class="fa fa-handshake"></i>     
                        My LISTING </span></a>';
    } else {
        $output = '<a class="add-business-btn" href="/add-business"><span class="add-business-button-content-wrapper"><i aria-hidden="true" class="fa fa-handshake"></i>  
                        ADD BUSINESS </span></a>';
    }
    return $output;
}
add_shortcode('ZTS_ADD_BUSINESS_BTN', 'zts_add_business_btn_shortcodes');
function zts_custom_login_redirect($redirect, $user)
{

    // Modify the redirect URL to the desired page
    $redirect = get_site_url() . '/my-account/zts-tab';
    return $redirect;
}
add_filter('woocommerce_login_redirect', 'zts_custom_login_redirect', 10, 2);
function zts_custom_refund_hook($order_id)
{
    // Update the custom tableget_site_url()
    global $wpdb;
    $table_name = $wpdb->prefix . 'zts_user_data';

    // Update query
    $wpdb->update(
        $table_name,
        array(
            'expiry' => 1,
            'status' => 'pending'
        ),
        array('order_id' => $order_id),
        array('%d', '%s'),
        array('%s')
    );
    // Example: Log a message when an order is refunded
    error_log('Order ' . $order_id . ' has been refunded by the admin.');
}
add_action('woocommerce_order_status_changed', 'zts_check_refund_status', 10, 3);
function zts_check_refund_status($order_id, $old_status, $new_status)
{
    if ($new_status === 'refunded') {
        zts_custom_refund_hook($order_id);
    }
}
/**
 * Remove order notes from the checkout page and hide the empty div.
 */
function zts_remove_order_notes_checkout()
{
    // Check if WooCommerce is active.
    if (class_exists('WooCommerce')) {

        // Remove order notes field from the checkout.
        add_filter('woocommerce_enable_order_notes_field', '__return_false');

        // Hide the empty div associated with the order notes field.
        add_action('wp_footer', 'zts_hide_order_notes_div');
    }
}
add_action('init', 'zts_remove_order_notes_checkout');
/**
 * Hide the empty div associated with the order notes field.
 */
function zts_hide_order_notes_div()
{
?>
    <style>
        .woocommerce-additional-fields {
            display: none !important;
        }
    </style>
<?php
}
require_once plugin_dir_path(__FILE__) . 'admin/admin-menu.php';
require_once plugin_dir_path(__FILE__) . 'inc/custom-texonomy.php';
require_once plugin_dir_path(__FILE__) . 'inc/home-page.php';
require_once plugin_dir_path(__FILE__) . 'inc/add-business.php';
require_once plugin_dir_path(__FILE__) . 'inc/listing.php';
require_once plugin_dir_path(__FILE__) . 'inc/profile.php';
require_once plugin_dir_path(__FILE__) . 'inc/my-account.php';
require_once plugin_dir_path(__FILE__) . 'inc/crone.php';
require_once plugin_dir_path(__FILE__) . 'inc/helper.php';
