<?php

/**
 * WWE Small DATABASE
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

/**
 * Create warehouse database table
 */
function create_sm_wh_db($network_wide = null)
{
    if ( is_multisite() && $network_wide ) {

        foreach (get_sites(['fields'=>'ids']) as $blog_id) {
            switch_to_blog($blog_id);
            global $wpdb;
            $warehouse_table = $wpdb->prefix . "warehouse";
            if ($wpdb->query("SHOW TABLES LIKE '" . $warehouse_table . "'") === 0) {
                $origin = 'CREATE TABLE ' . $warehouse_table . '(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            city varchar(200) NOT NULL,
            state varchar(200) NOT NULL,
            address varchar(255) NOT NULL,
            phone_instore varchar(255) NOT NULL,
            zip varchar(200) NOT NULL,
            country varchar(200) NOT NULL,
            location varchar(200) NOT NULL,
            nickname varchar(200) NOT NULL,
            enable_store_pickup VARCHAR(255) NOT NULL,
            miles_store_pickup VARCHAR(255) NOT NULL ,
            match_postal_store_pickup VARCHAR(255) NOT NULL ,
            checkout_desc_store_pickup VARCHAR(255) NOT NULL ,
            fee_store_pickup VARCHAR(10) NOT NULL ,
            enable_local_delivery VARCHAR(255) NOT NULL ,
            miles_local_delivery VARCHAR(255) NOT NULL ,
            match_postal_local_delivery VARCHAR(255) NOT NULL ,
            checkout_desc_local_delivery VARCHAR(255) NOT NULL ,
            fee_local_delivery VARCHAR(255) NOT NULL ,
            suppress_local_delivery VARCHAR(255) NOT NULL,
            wwe_correct_city VARCHAR(100) NOT NULL,
            origin_markup VARCHAR(255), 
            PRIMARY KEY  (id) )';
                dbDelta($origin);
            }

            $enable_store_pickup_col = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'enable_store_pickup'");
            if (!(isset($enable_store_pickup_col->Field) && $enable_store_pickup_col->Field == 'enable_store_pickup')) {
                $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN enable_store_pickup VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN miles_store_pickup VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN match_postal_store_pickup VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN checkout_desc_store_pickup VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN fee_store_pickup VARCHAR(10) NOT NULL , "
                    . "ADD COLUMN enable_local_delivery VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN miles_local_delivery VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN match_postal_local_delivery VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN checkout_desc_local_delivery VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN fee_local_delivery VARCHAR(255) NOT NULL , "
                    . "ADD COLUMN suppress_local_delivery VARCHAR(255) NOT NULL", $warehouse_table));

            }

            $wwe_correct_city = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'wwe_correct_city'");
            if (!(isset($wwe_correct_city->Field) && $wwe_correct_city->Field == 'wwe_correct_city')) {
                $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN wwe_correct_city VARCHAR(100) NOT NULL", $warehouse_table));
            }

            // Origin terminal address
            wwe_small_update_warehouse();
            restore_current_blog();
        }

    } else {
        global $wpdb;
        $warehouse_table = $wpdb->prefix . "warehouse";
        if ($wpdb->query("SHOW TABLES LIKE '" . $warehouse_table . "'") === 0) {
            $origin = 'CREATE TABLE ' . $warehouse_table . '(
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            city varchar(200) NOT NULL,
            state varchar(200) NOT NULL,
            address varchar(255) NOT NULL,
            phone_instore varchar(255) NOT NULL,
            zip varchar(200) NOT NULL,
            country varchar(200) NOT NULL,
            location varchar(200) NOT NULL,
            nickname varchar(200) NOT NULL,
            enable_store_pickup VARCHAR(255) NOT NULL,
            miles_store_pickup VARCHAR(255) NOT NULL ,
            match_postal_store_pickup VARCHAR(255) NOT NULL ,
            checkout_desc_store_pickup VARCHAR(255) NOT NULL ,
            fee_store_pickup VARCHAR(10) NOT NULL ,
            enable_local_delivery VARCHAR(255) NOT NULL ,
            miles_local_delivery VARCHAR(255) NOT NULL ,
            match_postal_local_delivery VARCHAR(255) NOT NULL ,
            checkout_desc_local_delivery VARCHAR(255) NOT NULL ,
            fee_local_delivery VARCHAR(255) NOT NULL ,
            suppress_local_delivery VARCHAR(255) NOT NULL,
            wwe_correct_city VARCHAR(100) NOT NULL,
            origin_markup VARCHAR(255),
            PRIMARY KEY  (id) )';
            dbDelta($origin);
        }

        $enable_store_pickup_col = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'enable_store_pickup'");
        if (!(isset($enable_store_pickup_col->Field) && $enable_store_pickup_col->Field == 'enable_store_pickup')) {
            $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN enable_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN miles_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN match_postal_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN checkout_desc_store_pickup VARCHAR(255) NOT NULL , "
                . "ADD COLUMN fee_store_pickup VARCHAR(10) NOT NULL , "
                . "ADD COLUMN enable_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN miles_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN match_postal_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN checkout_desc_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN fee_local_delivery VARCHAR(255) NOT NULL , "
                . "ADD COLUMN suppress_local_delivery VARCHAR(255) NOT NULL", $warehouse_table));

        }

        $wwe_correct_city = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'wwe_correct_city'");
        if (!(isset($wwe_correct_city->Field) && $wwe_correct_city->Field == 'wwe_correct_city')) {
            $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN wwe_correct_city VARCHAR(100) NOT NULL", $warehouse_table));
        }

        // Origin terminal address
        wwe_small_update_warehouse();
    }
}

/**
 * Update warehouse
 */
function wwe_small_update_warehouse()
{
    // Origin terminal address
    global $wpdb;
    $warehouse_table = $wpdb->prefix . "warehouse";
    $warehouse_address = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'phone_instore'");
    if (!(isset($warehouse_address->Field) && $warehouse_address->Field == 'phone_instore')) {
        $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN address VARCHAR(255) NOT NULL", $warehouse_table));
        $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN phone_instore VARCHAR(255) NOT NULL", $warehouse_table));
    }
    $origin_markup = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'origin_markup'");
    if (!(isset($origin_markup->Field) && $origin_markup->Field == 'origin_markup')) {
        $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN origin_markup VARCHAR(255) NOT NULL", $warehouse_table));
    }

    // instore pickup delivery fee
    $instore_pickup_fee = $wpdb->get_row("SHOW COLUMNS FROM " . $warehouse_table . " LIKE 'fee_store_pickup'");
    if (!(isset($instore_pickup_fee->Field) && $instore_pickup_fee->Field == 'fee_store_pickup')) {
        $wpdb->query(sprintf("ALTER TABLE %s ADD COLUMN fee_store_pickup VARCHAR(10) NOT NULL", $warehouse_table));
    }
}

/**
 *
 * Update warehouse/dropship correct city for WWE
 *
 */
function wwe_small_get_all_warehouse_dropship($address = NULL, $network_wide = null)
{
    if ( is_multisite() && $network_wide ) {

        foreach (get_sites(['fields'=>'ids']) as $blog_id) {
            switch_to_blog($blog_id);
            global $wpdb;
            $addresses = $wpdb->get_results("SELECT id, city as senderCity, state as senderState, zip as senderZip, country as senderCountryCode FROM " . $wpdb->prefix . "warehouse", ARRAY_A);
            $addresses = (isset($address) && !empty($address)) ? $address : $addresses;

            $update_status = isset($address['update_status']) ? $address['update_status'] : '';
            if (isset($addresses) && !empty($addresses)) {

                $domain = wwe_small_get_domain();
                $api_credentials = array(
                    'username' => get_option('wc_settings_username_wwe_small_packages_quotes'),
                    'password' => get_option('wc_settings_password_wwe_small_packages'),
                    'account_number' => get_option('wc_settings_account_number_wwe_small_packages_quotes'),
                    'authentication_key' => get_option('wc_settings_authentication_key_wwe_small_packages_quotes'),
                );

                $postData = array(
                    'acessLevel' => 'wweOriginValidate',
                    'carrier' => 'small',
                    'address' => $addresses,
                    'api' => $api_credentials,
                    'eniureLicenceKey' => get_option('wc_settings_plugin_licence_key_wwe_small_packages_quotes'),
                    'ServerName' => $domain,
                );

                $field_string = http_build_query($postData);
                $response = wp_remote_post(WWE_DOMAIN_HITTING_URL . '/addon/google-location.php', array(
                        'method' => 'POST',
                        'timeout' => 60,
                        'redirection' => 5,
                        'blocking' => true,
                        'body' => $field_string,
                    )
                );

                $output = wp_remote_retrieve_body($response);

                if (isset($output) && !empty($output)) {

                    $response = json_decode($output);
                    $error_status = (isset($response->error) && !empty($response->error)) ? $response->error : '';
                    if (empty($error_status)) {

                        foreach ($response as $id => $address) {
//                  if warehouse / dropship is updated then unset wwe corrected city
                            if (isset($update_status) && !empty($update_status) && $update_status == 1) {
                                $data = array('wwe_correct_city' => '');
                                $wpdb->update($wpdb->prefix . 'warehouse', $data, array('id' => $id));
                            }

                            if ($address->severity == 'ERROR' && isset($address->validCity)) {
                                $data = array('wwe_correct_city' => $address->validCity);
                                $wpdb->update($wpdb->prefix . 'warehouse', $data, array('id' => $id));
                            }
                        }
                    }
                }
            }
            restore_current_blog();
        }

    } else {
        global $wpdb;
        $addresses = $wpdb->get_results("SELECT id, city as senderCity, state as senderState, zip as senderZip, country as senderCountryCode FROM " . $wpdb->prefix . "warehouse", ARRAY_A);
        $addresses = (isset($address) && !empty($address)) ? $address : $addresses;

        $update_status = isset($address['update_status']) ? $address['update_status'] : '';
        if (isset($addresses) && !empty($addresses)) {

            $domain = wwe_small_get_domain();
            $api_credentials = array(
                'username' => get_option('wc_settings_username_wwe_small_packages_quotes'),
                'password' => get_option('wc_settings_password_wwe_small_packages'),
                'account_number' => get_option('wc_settings_account_number_wwe_small_packages_quotes'),
                'authentication_key' => get_option('wc_settings_authentication_key_wwe_small_packages_quotes'),
            );

            $postData = array(
                'acessLevel' => 'wweOriginValidate',
                'carrier' => 'small',
                'address' => $addresses,
                'api' => $api_credentials,
                'eniureLicenceKey' => get_option('wc_settings_plugin_licence_key_wwe_small_packages_quotes'),
                'ServerName' => $domain,
            );

            $field_string = http_build_query($postData);
            $response = wp_remote_post(WWE_DOMAIN_HITTING_URL . '/addon/google-location.php', array(
                    'method' => 'POST',
                    'timeout' => 60,
                    'redirection' => 5,
                    'blocking' => true,
                    'body' => $field_string,
                )
            );

            $output = wp_remote_retrieve_body($response);

            if (isset($output) && !empty($output)) {

                $response = json_decode($output);
                $error_status = (isset($response->error) && !empty($response->error)) ? $response->error : '';
                if (empty($error_status)) {

                    foreach ($response as $id => $address) {
//                  if warehouse / dropship is updated then unset wwe corrected city
                        if (isset($update_status) && !empty($update_status) && $update_status == 1) {
                            $data = array('wwe_correct_city' => '');
                            $wpdb->update($wpdb->prefix . 'warehouse', $data, array('id' => $id));
                        }

                        if ($address->severity == 'ERROR' && isset($address->validCity)) {
                            $data = array('wwe_correct_city' => $address->validCity);
                            $wpdb->update($wpdb->prefix . 'warehouse', $data, array('id' => $id));
                        }
                    }
                }
            }
        }
    }
}

/**
 * Update For First Plugin
 */
function wwe_small_update()
{

    global $wpdb;
    $table = $wpdb->prefix . "warehouse";
    if ($wpdb->query("SHOW TABLES LIKE '" . $table . "'") === 0) {
        add_option("WWE_Small_db_version", "2.0");
        $sql = 'CREATE TABLE ' . $table . '(
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                city varchar(200) NOT NULL,
                state varchar(200) NOT NULL,
                zip varchar(200) NOT NULL,
                country varchar(200) NOT NULL,
                location varchar(200) NOT NULL,
                nickname varchar(200) NOT NULL,
                address varchar(255) NOT NULL,
                PRIMARY KEY  (id) )';
        dbDelta($sql);
        $result = $wpdb->get_results("SELECT * from " . $table);
        if (count($result) == 0) {
            $wpdb->insert(
                'wp_warehouse', array(
                    'city' => get_option('wc_settings_wwe_small_packages_city'),
                    'state' => get_option('wc_settings_wwe_small_packages_state'),
                    'zip' => get_option('wc_settings_wwe_small_packages_zip_code'),
                    'country' => get_option('wc_settings_wwe_small_packages_country'),
                    'location' => 'warehouse'
                )
            );
        }
    }
}

/**
 * Create shipping rules database table
 */
function create_wwe_small_shipping_rules_db($network_wide = null)
{
    if ( is_multisite() && $network_wide ) {

        foreach (get_sites(['fields'=>'ids']) as $blog_id) {
            switch_to_blog($blog_id);
            global $wpdb;
            $shipping_rules_table = $wpdb->prefix . "eniture_wwe_small_shipping_rules";

            if ($wpdb->query("SHOW TABLES LIKE '" . $shipping_rules_table . "'") === 0) {
                $query = 'CREATE TABLE ' . $shipping_rules_table . '(
                    id INT(10) NOT NULL AUTO_INCREMENT,
                    name VARCHAR(50) NOT NULL,
                    type VARCHAR(30) NOT NULL,
                    settings TEXT NULL,
                    is_active TINYINT(1) NOT NULL,
                    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (id)
                )';

                dbDelta($query);
            }

            restore_current_blog();
        }

    } else {
        global $wpdb;
        $shipping_rules_table = $wpdb->prefix . "eniture_wwe_small_shipping_rules";

        if ($wpdb->query("SHOW TABLES LIKE '" . $shipping_rules_table . "'") === 0) {
            $query = 'CREATE TABLE ' . $shipping_rules_table . '(
                id INT(10) NOT NULL AUTO_INCREMENT,
                name VARCHAR(50) NOT NULL,
                type VARCHAR(30) NOT NULL,
                settings TEXT NULL,
                is_active TINYINT(1) NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id) 
            )';

            dbDelta($query);
        }
    }
}