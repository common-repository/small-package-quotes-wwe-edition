<script type="text/javascript">
    jQuery(function() {
        jQuery('a').on('click', function(e) {
            const class_name = this.className;
            const show_class_name = class_name.includes('show') ? class_name.replace('show', 'hide') : class_name.replace('hide', 'show');

            if (class_name.includes('show') || class_name.includes('hide')) {
                e.preventDefault();
            }

            jQuery('.' + class_name).hide();
            jQuery('.' + show_class_name).show();
        })
    });
</script>

<style>
    .hide {
        display: none;
    }
    .dims_space {
        padding-top: 0 !important;
    }
    .packaging_actions {
        display: flex;
        justify-content: space-evenly;
        align-items: center;
    }
    .empty_packaging_actions {
        text-align: center !important;
    }
</style>

<?php

if (!class_exists('EnFedexFreightLogs')) {
    class EnFedexFreightLogs
    {
        public function __construct()
        {
            $this->en_logs();
        }

        // Logs request
        public function en_logs()
        {
            $obj_classs = new \Small_Package_Request();
            $carrier_name = get_option('api_endpoint_wwe_small_packages') == 'wwe_small_new_api' ? 'WWE Small New API' : 'WWE SmPkg';
            $data = array(
                'serverName' => wwe_small_get_domain(),
                'licenseKey' => get_option('wc_settings_plugin_licence_key_wwe_small_packages_quotes'),
                'lastLogs' => '25',
                'carrierName' => $carrier_name
            );

            require_once('en-json-tree-view/en-jtv.php');
            require_once 'en-packaging-details/en-packaging-details.php';

            $url = WWE_SPQ_MAIN_DOMAIN . '/request-log/index.php';
            $logs = $obj_classs->small_package_get_curl_response($url, $data);
            $logs = (isset($logs) && strlen($logs) > 0) ? json_decode($logs, true) : [];

            echo '<table class="en_logs">';
            
            if (isset($logs['severity'], $logs['data']) && $logs['severity'] == 'SUCCESS') {
                echo '<tr>';
                echo '<th>Log ID</th>';
                echo '<th>Request Time</th>';
                echo '<th>Response Time</th>';
                echo '<th>Latency</th>';
                echo '<th>Items</th>';
                echo '<th>DIMs (L x W x H)</th>';
                echo '<th>Qty</th>';
                echo '<th>Sender Address</th>';
                echo '<th>Receiver Address</th>';
                echo '<th>Response</th>';
                echo '</tr>';

                foreach ($logs['data'] as $key => $shipment) {
                    if (empty($shipment) || !is_array($shipment)) continue;

                    echo '<tr>';
                    $request = $response = $carrier = $status = '';
                    extract($shipment);
                    $request = is_string($request) && strlen($request) > 0 ? json_decode($request, true) : [];
                    $receiverLineAddress = $receiverCity = $receiverState = $receiverZip = $receiverCountry = $receiverCountryCode = '';
                    $speed_ship_reciver_city = $speed_ship_receiver_state = $speed_ship_receiver_zip_code = '';
                    if (empty($request) || !is_array($request)) continue;

                    extract($request);
                    $en_fdo_meta_data = (isset($request['en_fdo_meta_data'])) ? $request['en_fdo_meta_data'] : [];
                    if (empty($en_fdo_meta_data)) {
                        continue;
                    }

                    $items = $address = [];
                    extract($en_fdo_meta_data);
                    $en_address = $address;
                    $en_qty = $en_items = $en_dim = '';
                    $class_name = 'wwe-small-log-' . $key . rand(1, 100);

                    foreach ($items as $key => $item) {
                        $name = $quantity = $length = $width = $height = '';
                        extract($item);
                        $en_qty .= strlen($en_qty) > 0 ? "<br> $quantity" : $quantity;
                        $en_items .= strlen($en_items) > 0 ? "<br> $name" : $name;
                        $en_dim .= strlen($en_dim) > 0 && count($items) > 1 ? "<br> $length X $width X $height" : "$length X $width X $height";
                    }

                    $en_updated_qty = $en_updated_items = $en_updated_dim = '';
                    $updated_items = count($items) > 5 ? array_slice($items, 0, 5) : $items;
                    if (!empty($updated_items)) {
                        foreach ($updated_items as $key => $item) {
                            $name = $quantity = $length = $width = $height = '';
                            extract($item);
                            $en_updated_qty .= strlen($en_updated_qty) > 0 ? "<br> $quantity" : $quantity;
                            $en_updated_items .= strlen($en_updated_items) > 0 ? "<br> $name" : $name;
                            $en_updated_dim .= strlen($en_updated_dim) > 0 && count($items) > 1 ? "<br> $length X $width X $height" : "$length X $width X $height";
                        }    
                    }

                    // Sender address
                    $address = $city = $state = $zip = $country = '';
                    extract($en_address);
                    $en_sender = strlen(trim($address) > 0) ? "$address, " : '';
                    $en_sender .= "$city, $state $zip $country";

                    // Receiver address
                    $en_receiver = strlen(trim($receiverLineAddress) > 0) ? "$receiverLineAddress, " : '';
                    $en_receiver .= "$speed_ship_reciver_city, $speed_ship_receiver_state $speed_ship_receiver_zip_code $receiverCountryCode";
                    $carrier = ucfirst($carrier);
                    $status = ucfirst($status);
                    $log_id = isset($shipment['id']) ? $shipment['id'] : '';
                    $request_time = $this->setTimeZone($request_time);
                    $response_time = $this->setTimeZone($response_time);
                    $latency = strtotime($response_time) - strtotime($request_time);

                    // Handle packaging details
                    $response = str_replace(array("\r", "\n", "\t"), '', $response);
                    $temp_resp = json_decode($response);
                    $bin_response = [];
                    if (isset($temp_resp->binPackaging) && !empty($temp_resp->binPackaging)) {
                        $bin_response[] = isset($temp_resp->binPackaging, $temp_resp->binPackaging->response) ? $temp_resp->binPackaging->response : [];
                        $bin_packaging = new \EnWweSpqLogsPackaging();
                        $bin_packaging_markup = $bin_packaging->en_get_bins_packed($bin_response, $products);
                    }

                    echo "<td>$log_id</td>";
                    echo "<td>$request_time</td>";
                    echo "<td>$response_time</td>";
                    echo "<td>$latency</td>";

                    $name = 'show-' . $class_name;
                    if (count($items) > 5) {
                        echo "<td class='items_space $name'>$en_updated_items <br /> <a href='#' class='$name'>Show more items</a> </td>";
                    } else {
                        echo "<td class='items_space $name'>$en_updated_items</td>";
                    }
                    
                    echo "<td class='dims_space $name'>$en_updated_dim</td>";
                    echo "<td class='$name'>$en_updated_qty</td>";
                    
                    $name = 'hide-' . $class_name;
                    echo "<td class='items_space hide $name'>$en_items <br /> <a href='#' class='$name'>Hide more items</a> </td>";
                    echo "<td class='dims_space hide $name'>$en_dim</td>";
                    echo "<td class='hide $name'>$en_qty</td>";
                    
                    echo "<td>$en_sender</td>";
                    echo "<td>$en_receiver</td>";

                    $action_links = '';

                    if (!empty($bin_response) && strtolower($status) != 'error') {
                        $action_links = '<td class="packaging_actions"><a href = "#en_jtv_showing_res" class="response" onclick=\'en_jtv_res_detail(' . $response . ')\'>' . $status . '</a>';

                        $box_icon = plugins_url('en-packaging-details/box-icon.png', __FILE__);
                        $packaging_link = ' <span> | </span> <a title="View packaging details" href="#en_jtv_showing_res" class="response" onclick=\'en_packging_details(' . json_encode($bin_packaging_markup) . ')\' ><img src=' . $box_icon . ' /></a>';

                        $action_links .= $packaging_link;
                        $action_links .= '</td>';
                    } else {
                        $action_links = '<td class="empty_packaging_actions"><a href = "#en_jtv_showing_res" class="response" onclick=\'en_jtv_res_detail(' . $response . ')\'>' . $status . '</a></td>';
                    }

                    echo $action_links;
                    echo '</tr>';
                }
            } else {
                echo '<div class="user_guide">';
                echo '<p>Logs are not available.</p>';
                echo '</div>';
            }

            echo '<table>';
        }

        // Rates content
        public function en_rate_content($rate)
        {
            $rate_content = '';
            if (isset($rate['label'], $rate['cost'])) {
                $rate_content = '<b>' . $rate['label'] . '</b>' . ': ' . $rate['cost'];
            }
            return $rate_content;
        }

        public function setTimeZone($date_time)
        {
            $time_zone = wp_timezone_string();
            if (empty($time_zone)) {
                return $date_time;
            }

            $converted_date_time = new DateTime($date_time, new DateTimeZone($time_zone));

            return $converted_date_time->format('m/d/Y h:i:s');
        }
    }

    new EnFedexFreightLogs();
}