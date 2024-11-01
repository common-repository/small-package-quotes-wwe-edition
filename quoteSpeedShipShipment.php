<?php

/**
 * WWE Small Test connection
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_nopriv_speedship_action', 'speedship_submit');
add_action('wp_ajax_speedship_action', 'speedship_submit');
add_action('wp_ajax_nopriv_wwe_cr_get_rates', 'wwe_cr_get_rates');
add_action('wp_ajax_wwe_cr_get_rates', 'wwe_cr_get_rates');

/**
 * WWE Small Test connection AJAX Request
 */
function speedship_submit()
{
    $sp_user = (isset($_POST['speed_freight_username'])) ? sanitize_text_field($_POST['speed_freight_username']) : '';
    $sp_pass = (isset($_POST['speed_freight_password'])) ? sanitize_text_field($_POST['speed_freight_password']) : '';
    $sp_au_key = (isset($_POST['authentication_key'])) ? sanitize_text_field($_POST['authentication_key']) : '';
    $sp_acc = (isset($_POST['world_wide_express_account_number'])) ? sanitize_text_field($_POST['world_wide_express_account_number']) : '';
    $sp_licence_key = (isset($_POST['speed_freight_licence_key'])) ? sanitize_text_field($_POST['speed_freight_licence_key']) : '';
    $sp_client_id = (isset($_POST['client_id'])) ? sanitize_text_field($_POST['client_id']) : '';
    $sp_client_secret = (isset($_POST['client_secret'])) ? sanitize_text_field($_POST['client_secret']) : '';
    $domain = $_SERVER['SERVER_NAME'];
    $domain = wwe_small_get_domain();

    $postData = array(
        'plugin_licence_key' => $sp_licence_key,
        'plugin_domain_name' => eniture_parse_url($domain),
        'platform' => 'wordpress',
        'speed_freight_username' => $sp_user,
        'speed_freight_password' => $sp_pass,
    );

    if (isset($_POST['api_end_point']) && $_POST['api_end_point'] == 'wwe_small_new_api') {
        $postData['ApiVersion'] = '2.0';
        $postData['clientId'] = $sp_client_id;
        $postData['clientSecret'] = $sp_client_secret;
    } else {
        $postData['world_wide_express_account_number'] = $sp_acc;
        $postData['authentication_key'] = $sp_au_key;
    }
    
    $url = WWE_DOMAIN_HITTING_URL . '/carriers/wwe-small/speedshipTest.php';
    $field_string = http_build_query($postData);
    $response = wp_remote_post($url, array(
            'method' => 'POST',
            'timeout' => 60,
            'redirection' => 5,
            'blocking' => true,
            'body' => $field_string,
        )
    );

    $output = wp_remote_retrieve_body($response);
    $response = json_decode($output);
    if (isset($response->error_desc) && substr($response->error_desc, 0, 5) == "<?xml") {
        $xmlparser = xml_parser_create();
        xml_parse_into_struct($xmlparser, $response->error_desc, $values);
        xml_parser_free($xmlparser);
        (isset($values[6]['tag']) && $values[6]['tag'] == 'ERRORDESCRIPTION') ? $error = $values[6]['value'] : '';
        $responseBack['error'] = 0;
        $responseBack['error_desc'] = $error;
        print_r(json_encode((object)$responseBack));
        exit;
    } elseif (isset($response->error_desc) && $response->error_desc != "") {
        print_r($output);
    } else {
        print_r($output);
    }

    exit();
}

/**
 * Request for compare rates
 */
function wwe_cr_get_rates(){
    
    $api_request = $_POST['api_request'];
    $api_request['plugin_licence_key'] = get_option('wc_settings_plugin_licence_key_wwe_small_packages_quotes');
    $api_request['speed_ship_domain_name'] = eniture_parse_url(wwe_small_get_domain());
    $api_request['compareRates'] = '1';

    if (get_option('api_endpoint_wwe_small_packages') == 'wwe_small_new_api') {
        $api_request['ApiVersion'] = '2.0';
        $api_request['clientId'] = get_option('wwe_small_client_id');
        $api_request['clientSecret'] = get_option('wwe_small_client_secret');
        $api_request['speed_ship_username'] = get_option('wwe_small_new_api_username');
        $api_request['speed_ship_password'] = get_option('wwe_small_new_api_password');
    } else {
        $api_request['world_wide_express_account_number'] = get_option('wc_settings_account_number_wwe_small_packages_quotes');
        $api_request['speed_ship_username'] = get_option('wc_settings_username_wwe_small_packages_quotes');
        $api_request['speed_ship_password'] = get_option('wc_settings_password_wwe_small_packages');
        $api_request['authentication_key'] = get_option('wc_settings_authentication_key_wwe_small_packages_quotes');
    }

    $spr_obj = new Small_Package_Request();
    $response = $spr_obj->small_package_get_curl_response(WWE_DOMAIN_HITTING_URL . '/carriers/wwe-small/speedshipQuotes.php', $api_request);
    $formated_resp = formate_api_resposnes($response);
    echo json_encode($formated_resp);
    exit;
}

function eniture_parse_url($domain)
{
    $domain = trim($domain);
    $parsed = parse_url($domain);
    if (empty($parsed['scheme'])) {
        $domain = 'http://' . ltrim($domain, '/');
    }
    $parse = parse_url($domain);
    $refinded_domain_name = $parse['host'];
    $domain_array = explode('.', $refinded_domain_name);
    if (in_array('www', $domain_array)) {
        $key = array_search('www', $domain_array);
        unset($domain_array[$key]);
        if(phpversion() < 8) {
            $refinded_domain_name = implode($domain_array, '.');
        }else {
            $refinded_domain_name = implode('.', $domain_array);
        }
    }
    return $refinded_domain_name;
}

function formate_api_resposnes($response)
{
    $response = json_decode($response);
    $formated_resp = [];
    if(isset($response->error)){
        $formated_resp['license_error'] = '1';
        echo json_encode($formated_resp);
        exit;
    }
    
    $formated_resp['wwe'] = get_wwe_formated_quotes($response);
    $formated_resp['shipengine'] = get_shipengine_formated_quotes($response);
    return $formated_resp;
}
/**
 * Formate WWE Resposne
 */
function get_wwe_formated_quotes($response){
    if(isset($response->q) && is_array($response->q)){
        if(isset($response->q[0]->totalOfferPrice)){
            return wwe_formate_new_api_response($response->q);
        }else{
            return wwe_formate_legacy_api_response($response->q);
        }
    }else{
        return '';
    }
}
/**
 * Formate WWE Legacy API Resposne
 */
function wwe_formate_legacy_api_response($response){
    $formated_resp = [];
    foreach ($response as $key => $resp) {
        $formated_resp[] = [
            'code' => $resp->serviceCode,
            'price' => $resp->serviceFeeDetail->serviceFeeGrandTotal,
            'service_name' => $resp->serviceDescription,
            'estimated_delivery' => $resp->estimateDelivery
        ];
    }

    $prices_arr = array_column($formated_resp, 'price'); 
    array_multisort($prices_arr, SORT_ASC, $formated_resp);
    return $formated_resp;
}
/**
 * Formate WWE New API Resposne
 */
function wwe_formate_new_api_response($response){
    $formated_resp = [];
    foreach ($response as $key => $resp) {
        $delivery_date = ($resp->timeInTransit->estimatedDeliveryDate) ? $resp->timeInTransit->estimatedDeliveryDate." " : '';
        $delivery_day = ($resp->timeInTransit->estimatedArrivalDayOfTheWeek) ? $resp->timeInTransit->estimatedArrivalDayOfTheWeek." " : '';
        $delivery_time = ($resp->timeInTransit->deliveryBy) ? $resp->timeInTransit->deliveryBy : '';
        $estimated_delivery = $delivery_day.$delivery_date.$delivery_time;
        $formated_resp[] = [
            'code' => $resp->timeInTransit->upsServiceCode,
            'price' => $resp->totalOfferPrice->value,
            'service_name' => $resp->timeInTransit->serviceDescription,
            'estimated_delivery' => $estimated_delivery
        ];
    }

    $prices_arr = array_column($formated_resp, 'price'); 
    array_multisort($prices_arr, SORT_ASC, $formated_resp);
    return $formated_resp;
}

/**
 * Formate ShipEngine API Resposne
 */

 function get_shipengine_formated_quotes($response){
    if(isset($response->shipEngineQuotes->q) && is_array($response->shipEngineQuotes->q)){
        $formated_resp = [];
        foreach ($response->shipEngineQuotes->q as $key => $resp) {
            $formated_resp[] = [
                'code' => $resp->service_code,
                'price' => $resp->shipping_amount->amount,
                'service_name' => $resp->service_type,
                'estimated_delivery' => $resp->carrier_delivery_days
            ];
        }

        $prices_arr = array_column($formated_resp, 'price'); 
        array_multisort($prices_arr, SORT_ASC, $formated_resp);
        return $formated_resp;
    }else{
        return '';
    }
 }
