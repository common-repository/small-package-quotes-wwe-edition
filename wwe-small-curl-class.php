<?php

/**
 * Class small_package_Request
 *
 * @package     WWE Small Quotes
 * @subpackage  Curl Call
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit; // exit if direct access
}

/**
 * Class to call curl request
 */
class Small_Package_Request {

    /**
     * Get Curl Response 
     * @param  $url curl hitting URL
     * @param  $postData post data to get response
     * @return json
     */
    function small_package_get_curl_response($url, $postData) {
        if (!empty($url) && !empty($postData)) {
            $field_string = http_build_query($postData);

//           Eniture debug mood
            do_action("eniture_debug_mood", "Build Query (s)", $field_string);
            $response = wp_remote_post($url, array(
                'method' => 'POST',
                'timeout' => 60,
                'redirection' => 5,
                'blocking' => true,
                'body' => $field_string,
                    )
            );

            $output = wp_remote_retrieve_body($response);

            return $output;
        }
    }

}
