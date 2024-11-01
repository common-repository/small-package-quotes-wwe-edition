<?php
/*
  Plugin Name: WooCommerce WWE Small Package Quotes
  Plugin URI: https://eniture.com/products/
  Description: Obtains a dynamic estimate of Small Package rates via the Worldwide Express Speedship API for your orders.
  Author: Eniture Technology
  Author URI: https://eniture.com/
  Version: 5.2.16
  Text Domain: eniture-technology
  License: GPL version 2 or later - http://www.eniture.com/
  WC requires at least: 6.4
  WC tested up to: 9.3.1
 */
/**
 * WWE Small
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
/*
  Small Package Quotes for WooCommerce - Worldwide Express Edition
  Copyright (C) 2016  Eniture LLC d/b/a Eniture Technology

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License version 2
  as published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

  Inquiries can be emailed to info@eniture.com or sent via the postal service to Eniture Technology, 320 W. Lanier Ave, Suite 200, Fayetteville, GA 30214, USA.
 */
if (!defined('ABSPATH')) {
    exit;
}

define('WWE_SPQ_MAIN_DOMAIN', 'https://ws002.eniture.com');
define('WWE_DOMAIN_HITTING_URL', 'https://ws002.eniture.com');
define('WWE_FDO_HITTING_URL', 'https://freightdesk.online/api/updatedWoocomData');
define('WWE_SMALL_FDO_COUPON_BASE_URL', 'https://freightdesk.online');
define('WWE_SMALL_VA_COUPON_BASE_URL', 'https://validate-addresses.com');

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
});

// Define reference
function en_wwe_small_plugin($plugins)
{
    $plugins['spq'] = (isset($plugins['spq'])) ? array_merge($plugins['spq'], ['speedship' => 'WC_speedship']) : ['speedship' => 'WC_speedship'];
    return $plugins;
}

add_filter('en_plugins', 'en_wwe_small_plugin');

if (!function_exists('en_woo_plans_notification_PD')) {

    function en_woo_plans_notification_PD($product_detail_options)
    {
        $eniture_plugins_id = 'eniture_plugin_';

        for ($e = 1; $e <= 25; $e++) {
            $settings = get_option($eniture_plugins_id . $e);
            if (isset($settings) && (!empty($settings)) && (is_array($settings))) {
                $plugin_detail = current($settings);
                $plugin_name = (isset($plugin_detail['plugin_name'])) ? $plugin_detail['plugin_name'] : "";

                foreach ($plugin_detail as $key => $value) {
                    if ($key != 'plugin_name') {
                        $action = $value === 1 ? 'enable_plugins' : 'disable_plugins';
                        $product_detail_options[$key][$action] = (isset($product_detail_options[$key][$action]) && strlen($product_detail_options[$key][$action]) > 0) ? $product_detail_options[$key][$action] . ", $plugin_name" : "$plugin_name";
                    }
                }
            }
        }

        return $product_detail_options;
    }

    add_filter('en_woo_plans_notification_action', 'en_woo_plans_notification_PD', 10, 1);
}

/**
 * Load scripts for FedEx Freight json tree view
 */
if (!function_exists('en_jtv_script')) {
    function en_jtv_script()
    {
        wp_register_style('json_tree_view_style', plugin_dir_url(__FILE__) . 'logs/en-json-tree-view/en-jtv-style.css');
        wp_register_script('json_tree_view_script', plugin_dir_url(__FILE__) . 'logs/en-json-tree-view/en-jtv-script.js', ['jquery'], '1.0.0');

        wp_enqueue_style('json_tree_view_style');
        wp_enqueue_script('json_tree_view_script', [
            'en_tree_view_url' => plugins_url(),
        ]);
    }

    add_action('admin_init', 'en_jtv_script');
}

if (!function_exists('en_woo_plans_notification_message')) {

    function en_woo_plans_notification_message($enable_plugins, $disable_plugins)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0) ? " $disable_plugins: Upgrade to <b>Standard Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_notification_message_action', 'en_woo_plans_notification_message', 10, 2);
}

// Nesting Material
if (!function_exists('en_woo_plans_nested_notification_message')) {

    function en_woo_plans_nested_notification_message($enable_plugins, $disable_plugins, $feature)
    {
        $enable_plugins = (strlen($enable_plugins) > 0) ? "$enable_plugins: <b> Enabled</b>. " : "";
        $disable_plugins = (strlen($disable_plugins) > 0 && $feature == 'nested_material') ? " $disable_plugins: Upgrade to <b>Advance Plan to enable</b>." : "";
        return $enable_plugins . "<br>" . $disable_plugins;
    }

    add_filter('en_woo_plans_nested_notification_message_action', 'en_woo_plans_nested_notification_message', 10, 3);
}

if (is_admin()) {
    require_once('warehouse-dropship/wwe-small-wild-delivery.php');
    require_once('quoteSpeedShipShipment.php');
    require_once('template/products-nested-options.php');
    require_once('product/en-product-detail.php');
    require_once 'product/en-common-product-detail.php';
}

require_once 'template/csv-export.php';
require_once('standard-package-addon/standard-package-addon.php');
require_once('warehouse-dropship/get-distance-request.php');
require_once 'helper/en_helper_class.php';
require_once('wwe-small-curl-class.php');
require_once 'update-plan.php';
require_once 'fdo/en-fdo.php';
require_once 'fdo/en-sbs.php';
require_once 'carrier_service.php';
require_once 'db/wwesmall_db.php';
require_once 'small_packages_shipping_class.php';
require_once('orders/en-order-export.php');
require_once('orders/en-order-widget.php');
require_once('orders/rates/order-rates.php');
require('shipping-rules/shipping-rules-save.php');

add_action('admin_enqueue_scripts', 'en_speedship_script');

// Origin terminal address
add_action('admin_init', 'wwe_small_update_warehouse');
add_action('admin_init', 'create_wwe_small_shipping_rules_db');

/**
 * Load Front-end scripts for speedship
 */
function en_speedship_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('en_speedship_script', plugin_dir_url(__FILE__) . 'js/en-speedship.js', [], '1.1.2');
    wp_localize_script('en_speedship_script', 'en_speedship_admin_script', array(
        'plugins_url' => plugins_url(),
        'allow_proceed_checkout_eniture' => trim(get_option("allow_proceed_checkout_eniture")),
        'prevent_proceed_checkout_eniture' => trim(get_option("prevent_proceed_checkout_eniture")),
        'wwe_small_order_cutoff_time' => get_option("wwe_small_orderCutoffTime"),
        'wwe_small_packaging_type' => get_option("wwe_small_packaging_type"),
        'backup_rates_fixed_rate_wwe_small' => get_option("backup_rates_fixed_rate_wwe_small"),
        'backup_rates_cart_price_percentage_wwe_small' => get_option("backup_rates_cart_price_percentage_wwe_small"),
        'backup_rates_weight_function_wwe_small' => get_option("backup_rates_weight_function_wwe_small"),
    ));

    // Shipping rules script and styles
    wp_enqueue_script('en_wwe_small_sr_script', plugin_dir_url(__FILE__) . '/shipping-rules/assets/js/shipping_rules.js', array(), '1.0.0');
    wp_localize_script('en_wwe_small_sr_script', 'script', array(
        'pluginsUrl' => plugins_url(),
    ));
    wp_register_style('en_wwe_small_shipping_rules_section', plugin_dir_url(__FILE__) . '/shipping-rules/assets/css/shipping_rules.css', false, '1.0.0');
    wp_enqueue_style('en_wwe_small_shipping_rules_section');
}

require_once 'group_small_shipment.php';
require_once 'wwe_small_wc_update_change.php';
require_once 'wwe-small-packages-quotes-auto-residential-detection.php';
require_once('wwe_small_version_compact.php');

/**
 * Get Host
 * @param type $url
 * @return type
 */
if (!function_exists('getHost')) {

    function getHost($url)
    {
        $parseUrl = parse_url(trim($url));
        if (isset($parseUrl['host'])) {
            $host = $parseUrl['host'];
        } else {
            $path = explode('/', $parseUrl['path']);
            $host = $path[0];
        }
        return trim($host);
    }

}

/**
 * Get Domain Name
 */
if (!function_exists('wwe_small_get_domain')) {

    function wwe_small_get_domain()
    {
        global $wp;
        $wp_request = (isset($wp->request)) ? $wp->request : '';
        $url = home_url($wp_request);
        return getHost($url);
    }
}

/**
 * Admin Scripts
 */
function wwe_smpkg_admin_script()
{
    wp_register_style('small_packges_style', plugin_dir_url(__FILE__) . '/css/small_packges_style.css', false, '2.2.3');
    wp_enqueue_style('small_packges_style');

    wp_register_style('wwe_small_wickedpicker_style', 'https://cdn.jsdelivr.net/npm/wickedpicker@0.4.3/dist/wickedpicker.min.css', false, '2.0.3');
    wp_enqueue_style('wwe_small_wickedpicker_style');
    wp_register_script('wwe_small_wickedpicker_style', plugin_dir_url(__FILE__) . '/js/wickedpicker.js', false, '2.0.3');
    wp_enqueue_script('wwe_small_wickedpicker_style');

    if(is_admin() && (!empty( $_GET['page']) && 'wc-orders' == $_GET['page'] ) && (!empty( $_GET['action']) && 'new' == $_GET['action'] ))
    {
        if (!wp_script_is('eniture_calculate_shipping_admin', 'enqueued')) {
            wp_enqueue_script('eniture_calculate_shipping_admin', plugin_dir_url(__FILE__) . 'js/eniture-calculate-shipping-admin.js', array(), '1.0.0' );
        }
    }
}

add_action('admin_enqueue_scripts', 'wwe_smpkg_admin_script');

if (!function_exists('is_plugin_active')) {

    require_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

add_filter('plugin_action_links', 'wwe_smallpkg_add_action_plugin', 10, 5);

/**
 * Add plugin Actions
 * @staticvar $plugin
 * @param $actions
 * @param $plugin_file
 */
function wwe_smallpkg_add_action_plugin($actions, $plugin_file)
{

    static $plugin;
    if (!isset($plugin))
        $plugin = plugin_basename(__FILE__);
    if ($plugin == $plugin_file) {
        $settings = array('settings' => '<a href="admin.php?page=wc-settings&tab=wwe_small_packages_quotes">' . __('Settings', 'General') . '</a>');
        $site_link = array('support' => '<a href="https://support.eniture.com/" target="_blank">Support</a>');
        $actions = array_merge($settings, $actions);
        $actions = array_merge($site_link, $actions);
    }
    return $actions;
}

add_action('admin_init', 'wwe_check_woo_version');
/**
 * Check Woo Version
 */
function wwe_check_woo_version()
{

    $woo_version = sm_get_woo_version_number();
    $version = '2.6';
    if (!version_compare($woo_version, $version, ">=")) {
        add_action('admin_notices', 'wwe_admin_notice_failure');
    }
}

/**
 * Failure Notices
 */
function wwe_admin_notice_failure()
{
    ?>
    <div class="notice notice-error">
        <p><?php
            _e('WWE Small plugin requires WooCommerce version 2.6 or higher to work. Functionality may not work properly.', 'wwe-woo-version-failure');
            ?></p>
    </div>
    <?php
}

/**
 * Woo Version
 */
function sm_get_woo_version_number()
{
    if (!function_exists('get_plugins'))
        require_once(ABSPATH . 'wp-admin/includes/plugin.php');
    $plugin_folder = get_plugins('/' . 'woocommerce');
    $plugin_file = 'woocommerce.php';

    if (isset($plugin_folder[$plugin_file]['Version'])) {

        return $plugin_folder[$plugin_file]['Version'];
    } else {

        return NULL;
    }
}

if (!is_plugin_active('woocommerce/woocommerce.php')) {
    add_action('admin_notices', 'smallpkg_woocommerce_avaibility_error');
} else {
    add_filter('woocommerce_get_settings_pages', 'smallpkg_shipping_sections');
}

/**
 * Sections
 * @param $settings
 */
function smallpkg_shipping_sections($settings)
{

    include('small_packages_tab_class_woocommrece.php');
    return $settings;
}

/**
 * Woo Availability Error
 */
function smallpkg_woocommerce_avaibility_error()
{

    $class = "error";
    $message = "WooCommerce WWE Small Package is enabled but not effective. It requires WooCommerce in order to work , Please <a target='_blank' href='https://wordpress.org/plugins/woocommerce/installation/'>Install</a> WooCommerce Plugin.";
    echo "<div class=\"$class\"> <p>$message</p></div>";
}

add_action('woocommerce_shipping_init', 'smallpkg_shipping_method_init');
add_filter('woocommerce_shipping_methods', 'smallpkg_add_shipping_method');
add_filter('woocommerce_cart_no_shipping_available_html', 'wwe_small_default_error_message', 999, 1);
add_action('init', 'wwe_small_no_method_available');

add_action('init', 'wwe_small_default_error_message_selection');

/**
 * Update Default custom error message selection
 */
function wwe_small_default_error_message_selection()
{
    $custom_error_selection = get_option('wc_pervent_proceed_checkout_eniture');
    if (empty($custom_error_selection)) {
        update_option('wc_pervent_proceed_checkout_eniture', 'prevent', true);
        update_option('prevent_proceed_checkout_eniture', 'There are no shipping methods available for the address provided. Please check the address.', true);
    }
}

/**
 * @param $message
 * @return string
 */
if (!function_exists("wwe_small_default_error_message")) {

    function wwe_small_default_error_message($message)
    {
        if (get_option('wc_pervent_proceed_checkout_eniture') == 'prevent') {
            remove_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20, 2);
            return __(get_option('prevent_proceed_checkout_eniture'));
        } else if (get_option('wc_pervent_proceed_checkout_eniture') == 'allow') {
            add_action('woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20, 2);
            return __(get_option('allow_proceed_checkout_eniture'));
        }
    }

}
/**
 * Shipping Message On Cart If No Method Available
 */
if (!function_exists("wwe_small_no_method_available")) {

    function wwe_small_no_method_available()
    {
        $allow_checkout = (isset($_POST['allow_proceed_checkout_eniture'])) ? $_POST['allow_proceed_checkout_eniture'] : get_option('allow_proceed_checkout_eniture');
        $prevent_checkout = (isset($_POST['prevent_proceed_checkout_eniture'])) ? $_POST['prevent_proceed_checkout_eniture'] : get_option('prevent_proceed_checkout_eniture');

        if (get_option('allow_proceed_checkout_eniture') !== false) {
            update_option('allow_proceed_checkout_eniture', $allow_checkout);
            update_option('prevent_proceed_checkout_eniture', $prevent_checkout);
        } else {
            $deprecated = null;
            $autoload = 'no';
            add_option('allow_proceed_checkout_eniture', $allow_checkout, $deprecated, $autoload);
            add_option('prevent_proceed_checkout_eniture', $prevent_checkout, $deprecated, $autoload);
        }
    }

}

/**
 * Load shipping method
 * @param array $methods
 * @return string
 */
function smallpkg_add_shipping_method($methods)
{

    $methods['speedship'] = 'WC_speedship';
    return $methods;
}

add_filter('woocommerce_package_rates', 'speedship_hide_shipping');

/**
 * Hide Other plugins
 * @param $available_methods
 */
function speedship_hide_shipping($available_methods)
{
    // flag to check if rates available of current plugin
    $plugin_rates_available = false;
    foreach ($available_methods as $value) {
        if (strpos($value->id, 'backup_rates') !== false) continue;

        if ($value->method_id == 'speedship' || strpos($value->id, 'speedship') !== false) {
            $plugin_rates_available = true;
            break;
        }
    }

    // Remove backup Rates
    $plugin_rates = get_option('backup_rates_display_wwe_small') == 'no_plugin_rates' && $plugin_rates_available;
    $other_rates = (empty(get_option('backup_rates_display_wwe_small')) || get_option('backup_rates_display_wwe_small') == 'no_other_rates') && count($available_methods) > 1;

    if (get_option('enable_backup_rates_wwe_small') == 'yes' && ($plugin_rates || $other_rates)) {
        $backup_rate_id = 'speedship:backup_rates';
        foreach ($available_methods as $key => $value) {
            if (isset($value->id) && $value->id == $backup_rate_id) {
                unset($available_methods[$key]);
            }
        }
    }

    if (get_option('wc_settings_wwe_small_allow_other_plugins') == 'no'
        && count($available_methods) > 0) {
        $plugins_array = array();
        $eniture_plugins = get_option('EN_Plugins');
        if ($eniture_plugins) {
            $plugins_array = json_decode($eniture_plugins, true);
        }

        // add methods which not exist in array
        $plugins_array[] = 'ltl_shipping_method';
        $plugins_array[] = 'daylight';
        $plugins_array[] = 'tql';
        $plugins_array[] = 'unishepper_small';
        $plugins_array[] = 'usps';

        if ($plugin_rates_available) {
            foreach ($available_methods as $index => $method) {
                if (!in_array($method->method_id, $plugins_array)) {
                    unset($available_methods[$index]);
                }
            }
        }
    }
    return $available_methods;
}

/**
 * Return the shipment method.
 */
function en_wwe_small_return_shipment_id($available_methods)
{

    foreach ($available_methods as $method) {
        if ($method->method_id == "speedship") {
            return $method->method_id;
        }
    }
    return false;
}

add_filter('woocommerce_cart_shipping_method_full_label', 'smallpkg_remove_free_label', 10, 2);

/**
 * Remove Label
 * @param $full_label
 * @param $method
 */
function smallpkg_remove_free_label($full_label, $method)
{

    $full_label = str_replace("(Free)", "", $full_label);
    return $full_label;
}

add_action('admin_init', 'wwe_small_update', 10, 2);
register_activation_hook(__FILE__, 'create_sm_wh_db');
register_activation_hook(__FILE__, 'en_wwe_small_activate_hit_to_update_plan');
register_activation_hook(__FILE__, 'old_store_wwe_sm_dropship_status');
register_deactivation_hook(__FILE__, 'en_wwe_small_deactivate_hit_to_update_plan');
register_activation_hook(__FILE__, 'wwe_small_get_all_warehouse_dropship');
register_activation_hook(__FILE__, 'create_wwe_small_shipping_rules_db');

register_activation_hook(__FILE__, 'en_fdo_wwe_small_update_coupon_status_activate');
register_deactivation_hook(__FILE__, 'en_fdo_wwe_small_update_coupon_status_deactivate');
register_activation_hook(__FILE__, 'en_va_wwe_small_update_coupon_status_activate');
register_deactivation_hook(__FILE__, 'en_va_wwe_small_update_coupon_status_deactivate');
register_deactivation_hook(__FILE__, 'en_wwe_small_deactivate_plugin');

/**
 * WWE small plugin update now
 * @param array type $upgrader_object
 * @param array type $options
 */
function en_wwe_small_update_now()
{
    $index = 'small-package-quotes-wwe-edition/woocommerceShip.php';
    $plugin_info = get_plugins();
    $plugin_version = (isset($plugin_info[$index]['Version'])) ? $plugin_info[$index]['Version'] : '';
    $update_now = get_option('en_wwe_small_update_now');

    if ($update_now != $plugin_version) {
        if (!function_exists('en_wwe_small_activate_hit_to_update_plan')) {
            require_once(__DIR__ . '/update-plan.php');
        }

        create_sm_wh_db();
        en_wwe_small_activate_hit_to_update_plan();
        old_store_wwe_sm_dropship_status();
        wwe_small_get_all_warehouse_dropship();

        update_option('en_wwe_small_update_now', $plugin_version);
    }
}

add_action('init', 'en_wwe_small_update_now');

/* Auto-residential hook */
define("en_woo_plugin_wwe_small_packages_quotes", "wwe_small_packages_quotes");


add_action('wp_enqueue_scripts', 'en_wwe_small_frontend_checkout_script');

/**
 * Load Frontend scripts for ODFL
 */
function en_wwe_small_frontend_checkout_script()
{
    wp_enqueue_script('jquery');
    wp_enqueue_script('en_wwe_small_frontend_checkout_script', plugin_dir_url(__FILE__) . 'front/js/en-wwe-small-checkout.js', [], '1.0.1');
    wp_localize_script('en_wwe_small_frontend_checkout_script', 'frontend_script', array(
        'pluginsUrl' => plugins_url(),
    ));
}

add_filter('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'wwe_small_packages_quotes_quotes_plans_suscription_and_features', 1);

function wwe_small_packages_quotes_quotes_plans_suscription_and_features($feature)
{
    $package = get_option('wwe_small_packages_quotes_package');

    $features = array
    (
        'instore_pickup_local_devlivery' => array('3'),
        'transit_days' => array('3'),
        'hazardous_material' => array('2', '3'),
        'insurance_fee' => array('2', '3'),
        'wwe_small_cutOffTime_shipDateOffset' => array('2', '3'),
        'nested_material' => array('3'),
    );

    if (get_option('wwe_small_packages_quotes_store_type') == "1") {
        $features['multi_warehouse'] = array('2', '3');
        $features['multi_dropship'] = array('', '0', '1', '2', '3');
    }
    if (get_option('en_old_user_dropship_status') === "0" && get_option('wwe_small_packages_quotes_store_type') == "0") {
        $features['multi_dropship'] = array('', '0', '1', '2', '3');
    }
    if (get_option('en_old_user_warehouse_status') === "0" && get_option('wwe_small_packages_quotes_store_type') == "0") {
        $features['multi_warehouse'] = array('2', '3');
    }

    return (isset($features[$feature]) && (in_array($package, $features[$feature]))) ? TRUE : ((isset($features[$feature])) ? $features[$feature] : '');
}

add_filter('wwe_small_packages_quotes_plans_notification_link', 'wwe_small_packages_quotes_plans_notification_link', 1);

function wwe_small_packages_quotes_plans_notification_link($plans)
{
    $plan = current($plans);
    $plan_to_upgrade = "";
    switch ($plan) {
        case 2:
            $plan_to_upgrade = "<a href='https://eniture.com/woocommerce-worldwide-express-small-package-plugin/' target='_blank'>Standard Plan required</a>";
            break;
        case 3:
            $plan_to_upgrade = "<a href='https://eniture.com/woocommerce-worldwide-express-small-package-plugin/' target='_blank'>Advanced Plan required</a>";
            break;
    }

    return $plan_to_upgrade;
}

/**
 *
 * old customer check dropship / warehouse status on plugin update
 */
function old_store_wwe_sm_dropship_status()
{
    global $wpdb;

    // Check total no. of dropships on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $count_query = "select count(*) from $table_name where location = 'dropship' ";
    $num = $wpdb->get_var($count_query);

    if (get_option('en_old_user_dropship_status') == "0" && get_option('wwe_small_packages_quotes_store_type') == "0") {

        $dropship_status = ($num > 1) ? 1 : 0;

        update_option('en_old_user_dropship_status', "$dropship_status");
    } elseif (get_option('en_old_user_dropship_status') == "" && get_option('wwe_small_packages_quotes_store_type') == "0") {
        $dropship_status = ($num == 1) ? 0 : 1;

        update_option('en_old_user_dropship_status', "$dropship_status");
    }

    // Check total no. of warehouses on plugin updation
    $table_name = $wpdb->prefix . 'warehouse';
    $warehouse_count_query = "select count(*) from $table_name where location = 'warehouse' ";
    $warehouse_num = $wpdb->get_var($warehouse_count_query);

    if (get_option('en_old_user_warehouse_status') == "0" && get_option('wwe_small_packages_quotes_store_type') == "0") {

        $warehouse_status = ($warehouse_num > 1) ? 1 : 0;

        update_option('en_old_user_warehouse_status', "$warehouse_status");
    } elseif (get_option('en_old_user_warehouse_status') == "" && get_option('wwe_small_packages_quotes_store_type') == "0") {
        $warehouse_status = ($warehouse_num == 1) ? 0 : 1;

        update_option('en_old_user_warehouse_status', "$warehouse_status");
    }
}

/**
 * Filter For CSV Import
 */
if (!function_exists('en_import_dropship_location_csv')) {

    /**
     * Import drop ship location CSV
     * @param $data
     * @param $this
     * @return array
     */
    function en_import_dropship_location_csv($data, $parseData)
    {
        $_product_freight_class = $_product_freight_class_variation = '';
        $_dropship_location = $locations = [];
        foreach ($data['meta_data'] as $key => $metaData) {
            $location = explode(',', trim($metaData['value']));
            switch ($metaData['key']) {
                // Update new columns
                case '_product_freight_class':
                    $_product_freight_class = trim($metaData['value']);
                    unset($data['meta_data'][$key]);
                    break;
                case '_product_freight_class_variation':
                    $_product_freight_class_variation = trim($metaData['value']);
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location_nickname':
                    $locations[0] = $location;
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location_zip_code':
                    $locations[1] = $location;
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location_city':
                    $locations[2] = $location;
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location_state':
                    $locations[3] = $location;
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location_country':
                    $locations[4] = $location;
                    unset($data['meta_data'][$key]);
                    break;
                case '_dropship_location':
                    $_dropship_location = $location;
            }
        }

        // Update new columns
        if (strlen($_product_freight_class) > 0) {
            $data['meta_data'][] = [
                'key' => '_ltl_freight',
                'value' => $_product_freight_class,
            ];
        }

        // Update new columns
        if (strlen($_product_freight_class_variation) > 0) {
            $data['meta_data'][] = [
                'key' => '_ltl_freight_variation',
                'value' => $_product_freight_class_variation,
            ];
        }

        if (!empty($locations) || !empty($_dropship_location)) {
            if (isset($locations[0]) && is_array($locations[0])) {
                foreach ($locations[0] as $key => $location_arr) {
                    $metaValue = [];
                    if (isset($locations[0][$key], $locations[1][$key], $locations[2][$key], $locations[3][$key])) {
                        $metaValue[0] = $locations[0][$key];
                        $metaValue[1] = $locations[1][$key];
                        $metaValue[2] = $locations[2][$key];
                        $metaValue[3] = $locations[3][$key];
                        $metaValue[4] = $locations[4][$key];
                        $dsId[] = en_serialize_dropship($metaValue);
                    }
                }
            } else {
                $dsId[] = en_serialize_dropship($_dropship_location);
            }

            $sereializedLocations = maybe_serialize($dsId);
            $data['meta_data'][] = [
                'key' => '_dropship_location',
                'value' => $sereializedLocations,
            ];
        }
        return $data;
    }

    add_filter('woocommerce_product_importer_parsed_data', 'en_import_dropship_location_csv', '99', '2');
}

/**
 * Serialize drop ship
 * @param $metaValue
 * @return string
 * @global $wpdb
 */

if (!function_exists('en_serialize_dropship')) {
    function en_serialize_dropship($metaValue)
    {
        global $wpdb;
        $dropship = (array)reset($wpdb->get_results(
            "SELECT id
                        FROM " . $wpdb->prefix . "warehouse WHERE nickname='$metaValue[0]' AND zip='$metaValue[1]' AND city='$metaValue[2]' AND state='$metaValue[3]' AND country='$metaValue[4]'"
        ));

        $dropship = array_map('intval', $dropship);

        if (empty($dropship['id'])) {
            $data = en_csv_import_dropship_data($metaValue);
            $wpdb->insert(
                $wpdb->prefix . 'warehouse', $data
            );

            $dsId = $wpdb->insert_id;
        } else {
            $dsId = $dropship['id'];
        }

        return $dsId;
    }
}

/**
 * Filtered Data Array
 * @param $metaValue
 * @return array
 */
if (!function_exists('en_csv_import_dropship_data')) {
    function en_csv_import_dropship_data($metaValue)
    {
        return array(
            'city' => $metaValue[2],
            'state' => $metaValue[3],
            'zip' => $metaValue[1],
            'country' => $metaValue[4],
            'location' => 'dropship',
            'nickname' => (isset($metaValue[0])) ? $metaValue[0] : "",
        );
    }
}

if (!function_exists('en_wwe_check_ground_transit_restrict_status')) {

    function en_wwe_check_ground_transit_restrict_status($ground_transit_statuses)
    {
        $ground_transit_restrict_plan = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'transit_days');
        $ground_restrict_value = (false !== get_option('ground_transit_wwe_small_packages')) ? get_option('ground_transit_wwe_small_packages') : '';
        if ('' !== $ground_restrict_value && strlen(trim($ground_restrict_value)) && !is_array($ground_transit_restrict_plan)) {
            $ground_transit_statuses['wwe'] = '1';
        }

        return $ground_transit_statuses;
    }

    add_filter('en_check_ground_transit_restrict_status', 'en_wwe_check_ground_transit_restrict_status', 10, 1);
}


/**
 * Function that will trigger on activation
 */
function en_fdo_wwe_small_update_coupon_status_activate()
{
    $fdo_coupon_data = get_option('en_fdo_coupon_data');
    if(!empty($fdo_coupon_data)){
        $fdo_coupon_data_decorded = json_decode($fdo_coupon_data);
        if(isset($fdo_coupon_data_decorded->promo)){
            $data = array(
                'marketplace' => 'wp',
                'promocode' => $fdo_coupon_data_decorded->promo->coupon,
                'action' => 'install',
                'carrier' => 'WWE_PL'
            );

            $url = WWE_SMALL_FDO_COUPON_BASE_URL . "/change_promo_code_status";
            $response = wp_remote_get($url,
                array(
                    'method' => 'GET',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => $data,
                )
            );
        }
    }
}
/**
 * Function that will trigger on deactivation
 */
function en_fdo_wwe_small_update_coupon_status_deactivate()
{
    $fdo_coupon_data = get_option('en_fdo_coupon_data');
    if(!empty($fdo_coupon_data)){
        $fdo_coupon_data_decorded = json_decode($fdo_coupon_data);
        if(isset($fdo_coupon_data_decorded->promo)){
            $data = array(
                'marketplace' => 'wp',
                'promocode' => $fdo_coupon_data_decorded->promo->coupon,
                'action' => 'uninstall',
                'carrier' => 'WWE_PL'
            );

            $url = WWE_SMALL_FDO_COUPON_BASE_URL . "/change_promo_code_status";
            $response = wp_remote_get($url,
                array(
                    'method' => 'GET',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => $data,
                )
            );
        }
    }
}

/**
 * Function that will trigger on activation
 */
function en_va_wwe_small_update_coupon_status_activate()
{
    $va_coupon_data = get_option('en_va_coupon_data');
    if(!empty($va_coupon_data)){
        $va_coupon_data_decorded = json_decode($va_coupon_data);
        if(isset($va_coupon_data_decorded->promo)){
            $data = array(
                'marketplace' => 'wp',
                'promocode' => $va_coupon_data_decorded->promo->coupon,
                'action' => 'install',
                'carrier' => 'WWE_PL'
            );

            $url = WWE_SMALL_VA_COUPON_BASE_URL . "/change_promo_code_status?";
            $response = wp_remote_get($url,
                array(
                    'method' => 'GET',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => $data,
                )
            );
        }
    }
}
/**
 * Function that will trigger on deactivation
 */
function en_va_wwe_small_update_coupon_status_deactivate()
{
    $va_coupon_data = get_option('en_va_coupon_data');
    if(!empty($va_coupon_data)){
        $va_coupon_data_decorded = json_decode($va_coupon_data);
        if(isset($va_coupon_data_decorded->promo)){
            $data = array(
                'marketplace' => 'wp',
                'promocode' => $va_coupon_data_decorded->promo->coupon,
                'action' => 'uninstall',
                'carrier' => 'WWE_PL'
            );

            $url = WWE_SMALL_VA_COUPON_BASE_URL . "/change_promo_code_status?";
            $response = wp_remote_get($url,
                array(
                    'method' => 'GET',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => $data,
                )
            );
        }
    }
}

require_once 'fdo/en-coupon-api.php';
new EnWweSmallCouponAPI();

/**
 * Remove plugin option
 */
if(!function_exists('en_wwe_small_deactivate_plugin')) {
    function en_wwe_small_deactivate_plugin()
    {
        $eniture_plugins = get_option('EN_Plugins');
        $plugins_array = json_decode($eniture_plugins, true);
        $plugins_array = !empty($plugins_array) && is_array($plugins_array) ? $plugins_array : array();
        $key = array_search('speedship', $plugins_array);
        if ($key !== false) {
            unset($plugins_array[$key]);
        }
        update_option('EN_Plugins', json_encode($plugins_array));
    }
}
