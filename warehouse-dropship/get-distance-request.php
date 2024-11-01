<?php

/**
 * WWE Small Get Distance
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Distance Request Class
 */
class Get_sm_distance
{

    function __construct()
    {
        add_filter("en_wd_get_address", array($this, "sm_address"), 10, 2);
    }

    /**
     * Get Address Upon Access Level
     * @param $map_address
     * @param $accessLevel
     */
    function sm_address($map_address, $accessLevel, $destinationZip = [])
    {

        $domain = wwe_small_get_domain();
        $postData = array(
            'acessLevel' => $accessLevel,
            'address' => $map_address,
            'originAddresses' => (isset($map_address)) ? $map_address : "",
            'destinationAddress' => (isset($destinationZip)) ? $destinationZip : "",
            'eniureLicenceKey' => get_option('wc_settings_plugin_licence_key_wwe_small_packages_quotes'),
            'ServerName' => $_SERVER['SERVER_NAME'],
            'ServerName' => $domain,
        );

        $Small_Package_Request = new Small_Package_Request();
        $output = $Small_Package_Request->small_package_get_curl_response(WWE_DOMAIN_HITTING_URL . '/addon/google-location.php', $postData);

        return $output;
    }

}
