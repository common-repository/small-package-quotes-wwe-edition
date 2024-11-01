<?php

/**
 * WWE Small Carrier Service
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get Quotes For WWE Small
 */
class smallpkg_shipping_get_quotes extends EnSpeedshipFdo
{

    public $en_wd_origin_array;
    public $product_detail = [];
    public $forcefully_residential_delivery = FALSE;

    /**
     * Array For Getting Quotes
     * @param $packages
     * @param $content
     * @return array
     */
    function get_web_service_array($packages, $content, $package_plugin = "")
    {

        $Pweight = 0;
        $count = 0;
        // FDO
        $en_fdo_meta_data = $post_data = [];

        // Pricing per product
        $pricing_per_product = [];
        $en_pricing_per_product = apply_filters('en_pricing_per_product_existence', false);

        $shipping_package_obj = new group_small_shipment();
        $exceedWeight = get_option('wc_settings_wwe_return_LTL_quotes');
        $destinationAddressWweSmall = $this->destinationAddressWweSmall();
        $en_shipments = (isset($content['en_shipments'])) ? $content['en_shipments'] : [];

        // check plan for nested material
        $nested_plan = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'nested_material');

        $weight_threshold = get_option('en_weight_threshold_lfq');
        $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;

        foreach ($packages as $en_key => $package) {
            $ship_item_alone = $product_tag = $storeDateTime = $modifyShipmentDateTime = $shipmentOffsetDays = $orderCutoffTime = $productName = $productQty = $productPrice = $productWeight = $productLength = $productWidth = $productHeight = $product_name = $products = $nestingPercentage = $nestedDimension = $nestedItems = $stakingProperty = [];
            if(isset($package['origin'])) {
                $package_zip = (isset($package['origin']['zip'])) ? $package['origin']['zip'] : '';
                $package['origin']['city'] = (isset($package['origin']['corrected_city']) && !empty($package['origin']['corrected_city'])) ? $package['origin']['corrected_city'] : $package['origin']['city'];
                $this->en_wd_origin_array = (isset($package['origin'])) ? $package['origin'] : [];
                $doNesting = "";

                $zip_code = (isset($package['origin']['zip'])) ? $package['origin']['zip'] : 0;
            }else {
                $package_zip = '';
                $this->en_wd_origin_array = [];
                $zip_code = 0;
            }
            if ($en_pricing_per_product && strlen($zip_code) > 0) {
                $zip_code = $en_key;
            }

            $product_markup_shipment = 0;
            $product_insurance_apply = false;
            if (!($exceedWeight == 'yes' && $Pweight > $weight_threshold) &&
                (empty($en_shipments) || (!empty($en_shipments) && isset($en_shipments[$package_zip]))) &&
                (!isset($package['is_shipment']) || (isset($package['is_shipment']) && $package['is_shipment'] != 'ltl'))) {
                if (isset($package['items'])) {
                    $productIdCount = 0;
                    foreach ($package['items'] as $item) {
                        $Pweight = $item['productWeight'];
                        $productName[$productIdCount] = $item['productName'];
                        $productWeight[$productIdCount] = $item['productWeight'];
                        $productLength[$productIdCount] = $item['productLength'];
                        $productWidth[$productIdCount] = $item['productWidth'];
                        $productHeight[$productIdCount] = $item['productHeight'];
                        $productQty[$productIdCount] = $item['productQty'];
                        $productPrice[$productIdCount] = $item['productPrice'];
                        $product_tag[$productIdCount] = (isset($item['product_tag'])) ? $item['product_tag'] : '';
                        $ship_item_alone[$productIdCount] = (isset($item['ship_item_alone'])) ? $item['ship_item_alone'] : '';

                        $product_name[] = isset($item['product_name']) ? $item['product_name'] : '';
                        $products[] = isset($item['products']) ? $item['products'] : '';

                        $product_insurance = $item['product_insurance'];
                        isset($product_insurance) && $product_insurance > 0 ? $product_insurance_apply = true : '';
                        $pricing_per_product[] = [
                            'product_insurance' => $product_insurance,
                            'product_markup' => isset($item['product_markup']) ? $item['product_markup'] : 0,
                            'product_rental' => isset($item['product_rental']) ? $item['product_rental'] : 'no',
                            'product_quantity' => isset($item['product_quantity']) ? $item['product_quantity'] : 1,
                            'product_price' => isset($item['product_price']) ? $item['product_price'] : 0
                        ];

                        // Nested Material
                        $nestingPercentage[$productIdCount] = $item['nestedPercentage'];
                        $nestedDimension[$productIdCount] = $item['nestedDimension'];
                        $nestedItems[$productIdCount] = $item['nestedItems'];
                        $stakingProperty[$productIdCount] = $item['stakingProperty'];
                        isset($item['nestedMaterial']) && !empty($item['nestedMaterial']) &&
                        $item['nestedMaterial'] == 'yes' && !is_array($nested_plan) ? $doNesting = 1 : "";
                        if(!empty($item['markup']) && is_numeric($item['markup'])){
                            $product_markup_shipment += $item['markup'];
                        }
                        $productIdCount++;
                    }
                }
                $getVersion = $this->wwesmpkgWcVersionNumber();

                // Shipment days of a week
                $shipmentWeekDays = $this->wwex_small_shipment_week_days();

                // **Start:      Cut Off Time & Ship Date Offset
                $wwe_small_delivery_estimates = get_option('wwe_small_delivery_estimates');
                // Shipment days of a week
                if ($wwe_small_delivery_estimates == 'delivery_days' || $wwe_small_delivery_estimates == 'delivery_date') {
                    $orderCutoffTime = get_option('wwe_small_orderCutoffTime');
                    $shipmentOffsetDays = get_option('wwe_small_shipmentOffsetDays');
                    $modifyShipmentDateTime = ($orderCutoffTime != '' || $shipmentOffsetDays != '' || (is_array($shipmentWeekDays) && count($shipmentWeekDays) > 0)) ? 1 : 0;
                    $storeDateTime = date('Y-m-d H:i:s', current_time('timestamp'));
                }

                $package_type = get_option('wwe_small_packaging_method');
                $per_package_weight = '';
                if('ship_one_package_70' == $package_type){
                    $package_type = 'ship_as_one';
                    $per_package_weight = '70';
                }elseif('ship_one_package_150' == $package_type){
                    $package_type = 'ship_as_one';
                    $per_package_weight = '150';
                }

                $domain = wwe_small_get_domain();

                // FDO
                $en_fdo_meta_data = $this->en_cart_package($package);

                $s_post_data = array(
                    'platform' => 'wordpress',
                    'carrierName' => 'WWE SmPkg',
                    'plugin_version' => $getVersion["wwesmpkg_plugin_version"],
                    'wordpress_version' => get_bloginfo('version'),
                    'woocommerce_version' => $getVersion["woocommerce_plugin_version"],
                    'plugin_licence_key' => get_option('wc_settings_plugin_licence_key_wwe_small_packages_quotes'),
                    'speed_ship_domain_name' => function_exists('eniture_parse_url') ? eniture_parse_url($domain) : $domain,
                    'speed_ship_reciver_city' => $destinationAddressWweSmall['city'],
                    'speed_ship_receiver_state' => $destinationAddressWweSmall['state'],
                    'speed_ship_receiver_zip_code' => $destinationAddressWweSmall['zip'],
                    'receiverCountryCode' => $destinationAddressWweSmall['country'],
                    'speed_ship_senderCity' => isset($package['origin']['city']) ? $package['origin']['city'] : '',
                    'speed_ship_senderState' => isset($package['origin']['state']) ? $package['origin']['state'] : '',
                    'speed_ship_senderZip' => isset($package['origin']['zip']) ? $package['origin']['zip'] : '',
                    'speed_ship_senderCountryCode' => isset($package['origin']['country']) ? $package['origin']['country'] : '',
                    'residentials_delivery' => get_option('wc_settings_quest_as_residential_delivery_wwe_small_packages'),
                    // Product Information
                    'product_width_array' => $productWidth,
                    'product_height_array' => $productHeight,
                    'product_length_array' => $productLength,
                    'speed_ship_product_price_array' => $productPrice,
                    'speed_ship_product_weight' => $productWeight,
                    'speed_ship_title_array' => $productName,
                    'speed_ship_quantity_array' => $productQty,
                    'sender_origin' => isset($package['origin']) ? $package['origin']['location'] . ": " . $package['origin']['city'] . ", " . $package['origin']['state'] . " " . $package['origin']['zip'] : '',
                    'product_name' => $product_name,
                    'products' => $products,
                    // FDO
                    'en_fdo_meta_data' => $en_fdo_meta_data,
                    'modifyShipmentDateTime' => $modifyShipmentDateTime,
                    'OrderCutoffTime' => $orderCutoffTime,
                    'shipmentOffsetDays' => $shipmentOffsetDays,
                    'storeDateTime' => $storeDateTime,
                    'shipmentWeekDays' => $shipmentWeekDays,
                    // Nested indexes
                    'doNesting' => isset($doNesting) ? $doNesting : '',
                    'nesting_percentage' => $nestingPercentage,
                    'nesting_dimension' => $nestedDimension,
                    'nested_max_limit' => $nestedItems,
                    'nested_stack_property' => $stakingProperty,
                    'product_tags_array' => $product_tag,
                    // Shippable item
                    'ship_item_alone' => $ship_item_alone,
                    'origin_markup' => (isset($package['origin']['origin_markup'])) ? $package['origin']['origin_markup'] : 0,
                    'product_level_markup' => $product_markup_shipment,
                    // Pricing per product
                    'pricing_per_product' => $pricing_per_product,
                    'packagesType' => $package_type,
                    'perPackageWeight' => $per_package_weight,
                    // Sbs optimization mode
                    'sbsMode' => get_option('box_sizing_optimization_mode'),
                );

                // get large cart settings shipping rules
                $large_cart_settings = (new EnWweSmallShippingRulesAjaxReq())->get_large_cart_settings();
                $s_post_data = array_merge($s_post_data, $large_cart_settings);

                if (get_option('api_endpoint_wwe_small_packages') == 'wwe_small_new_api') {
                    $s_post_data['ApiVersion'] = '2.0';
                    $s_post_data['clientId'] = get_option('wwe_small_client_id');
                    $s_post_data['clientSecret'] = get_option('wwe_small_client_secret');
                    $s_post_data['speed_ship_username'] = get_option('wwe_small_new_api_username');
                    $s_post_data['speed_ship_password'] = get_option('wwe_small_new_api_password');
                } else {
                    $s_post_data['world_wide_express_account_number'] = get_option('wc_settings_account_number_wwe_small_packages_quotes');
                    $s_post_data['speed_ship_username'] = get_option('wc_settings_username_wwe_small_packages_quotes');
                    $s_post_data['speed_ship_password'] = get_option('wc_settings_password_wwe_small_packages');
                    $s_post_data['authentication_key'] = get_option('wc_settings_authentication_key_wwe_small_packages_quotes');
                }

                // Insurance Fee
                $action_insurance = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'insurance_fee');
                if (!is_array($action_insurance)) {
                    $s_post_data['includeInsuranceValue'] = 1;
                }

                if ($product_insurance_apply) {
                    $s_post_data['includeInsuranceValue'] = 1;
                }

                // Hazardous Material
                $hazardous_material = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'hazardous_material');
                if (!is_array($hazardous_material)) {
                    (isset($package['hazardous_material'])) ? $s_post_data['hazardous_material'] = TRUE : "";
                    (isset($package['hazardous_material'])) ? $s_post_data['hazardous_material'] = 'yes' : "";
                    // FDO
                    $s_post_data['en_fdo_meta_data'] = array_merge($s_post_data['en_fdo_meta_data'], $this->en_package_hazardous($package, $en_fdo_meta_data));
                }

                // Except Ground Transit Restriction
                $exempt_ground_restriction_plan = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'transit_days');
                if (!is_array($exempt_ground_restriction_plan)) {
                    (isset($package['exempt_ground_transit_restriction'])) ? $s_post_data['exempt_ground_transit_restriction'] = 'yes' : '';
                }

                // In-store pickup and local delivery
                $instore_pickup_local_devlivery_action = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');
                if (!is_array($instore_pickup_local_devlivery_action)) {
                    $s_post_data = apply_filters('en_wwe_small_wd_standard_plans', $s_post_data, $s_post_data['speed_ship_receiver_zip_code'], $this->en_wd_origin_array, $package_plugin);
                }

                $post_data[$zip_code] = apply_filters("en_woo_addons_carrier_service_quotes_request", $s_post_data, en_woo_plugin_wwe_small_packages_quotes);
            }

            /* Box sizes filter */
            if (strlen($zip_code) > 0) {
                $post_data = apply_filters('enit_box_sizes_post_array_filter', $post_data, $package, $zip_code);
                // Crowler work
                $post_data = apply_filters('en_update_sbs_packaging', $post_data, $package, $zip_code);
                // Compatability with OLD SBS Addon
                if (isset($post_data[$zip_code]['vertical_rotation'], $post_data[$zip_code]['length']) &&
                    count($post_data[$zip_code]['length']) == count($post_data[$zip_code]['vertical_rotation']) &&
                    !empty($post_data[$zip_code]['vertical_rotation'])) {
                    $post_data[$zip_code]['vertical_rotation'] = array_combine(array_keys($post_data[$zip_code]['length']), $post_data[$zip_code]['vertical_rotation']);
                }
                if (isset($post_data[$zip_code]['shipBinAlone'], $post_data[$zip_code]['length']) &&
                    count($post_data[$zip_code]['length']) == count($post_data[$zip_code]['shipBinAlone']) &&
                    !empty($post_data[$zip_code]['shipBinAlone'])) {
                    $post_data[$zip_code]['shipBinAlone'] = array_combine(array_keys($post_data[$zip_code]['length']), $post_data[$zip_code]['shipBinAlone']);
                }
            }
            $count++;
        }

        do_action("eniture_debug_mood", "Quotes Request (wwe)", $post_data);
        do_action("eniture_debug_mood", "Plugin Features (wwe)", get_option('eniture_plugin_1'));

        $post_data = $this->applyErrorManagement($post_data);

        return $post_data;
    }

    /**
     * @return shipment days of a week
     */
    public function wwex_small_shipment_week_days()
    {
        $shipment_days_of_week = array();

        if (get_option('all_shipment_days_wwex_small') == 'yes') {
            return [1, 2, 3, 4, 5];
        }
        if (get_option('monday_shipment_day_wwex_small') == 'yes') {
            $shipment_days_of_week[] = 1;
        }
        if (get_option('tuesday_shipment_day_wwex_small') == 'yes') {
            $shipment_days_of_week[] = 2;
        }
        if (get_option('wednesday_shipment_day_wwex_small') == 'yes') {
            $shipment_days_of_week[] = 3;
        }
        if (get_option('thursday_shipment_day_wwex_small') == 'yes') {
            $shipment_days_of_week[] = 4;
        }
        if (get_option('friday_shipment_day_wwex_small') == 'yes') {
            $shipment_days_of_week[] = 5;
        }

        return $shipment_days_of_week;
    }

    /**
     * destinationAddressFedexSmall
     * @return array type
     */
    function destinationAddressWweSmall()
    {
        $en_order_accessories = apply_filters('en_order_accessories', []);
        if (isset($en_order_accessories) && !empty($en_order_accessories)) {
            return $en_order_accessories;
        }

        $wwe_small_woo_obj = new WWE_Small_Woo_Update_Changes();
        $freight_zipcode = (strlen(WC()->customer->get_shipping_postcode()) > 0) ? WC()->customer->get_shipping_postcode() : $wwe_small_woo_obj->wwe_small_postcode();
        $freight_state = (strlen(WC()->customer->get_shipping_state()) > 0) ? WC()->customer->get_shipping_state() : $wwe_small_woo_obj->wwe_small_getState();
        $freight_country = (strlen(WC()->customer->get_shipping_country()) > 0) ? WC()->customer->get_shipping_country() : $wwe_small_woo_obj->wwe_small_getCountry();
        $freight_city = (strlen(WC()->customer->get_shipping_city()) > 0) ? WC()->customer->get_shipping_city() : $wwe_small_woo_obj->wwe_small_getCity();
        $address = (strlen(WC()->customer->get_shipping_address_1()) > 0) ? WC()->customer->get_shipping_address_1() : $wwe_small_woo_obj->wwe_small_getAddress1();

        return array(
            'city' => $freight_city,
            'state' => $freight_state,
            'zip' => $freight_zipcode,
            'country' => $freight_country,
            'address' => $address,
        );
    }

    /**
     * Get WWE Small Web Quotes
     * @param $postData
     * @return json
     */
    function get_web_quotes($request_data, $package_plugin = "")
    {

//      check response from session 
        $srequest_data = $request_data;
//      get response from session
        $currentData = md5(json_encode($srequest_data));
        $requestFromSession = WC()->session->get('previousRequestData');
        $requestFromSession = ((is_array($requestFromSession)) && (!empty($requestFromSession))) ? $requestFromSession : [];

        if (isset($requestFromSession[$currentData]) && (!empty($requestFromSession[$currentData]))) {
//          Eniture debug mood
            do_action("eniture_debug_mood", "Build Query (wwe)", http_build_query($request_data));
            do_action("eniture_debug_mood", "Quotes Response (wwe)", json_decode($requestFromSession[$currentData]));

            // Crolwer work
            $requestFromSession[$currentData] = apply_filters('en_show_only_ground_service', $requestFromSession[$currentData], $request_data);

            /* Action hook */
            do_action('en_box_sizing_response', json_decode($requestFromSession[$currentData]));
            return $requestFromSession[$currentData];
        }

        if (is_array($request_data) && count($request_data) > 0) {
            $Small_Package_Request = new Small_Package_Request();

            // requestKeySBS
            if (isset($request_data['requestKeySBS']) && strlen($request_data['requestKeySBS']) > 0) {
                $request_data['requestKey'] = $request_data['requestKeySBS'];
            } else {
                $request_data['requestKey'] = (isset($request_data['requestKey'])) ? $request_data['requestKey'] : md5(microtime() . rand());
            }

            $output = $Small_Package_Request->small_package_get_curl_response(WWE_DOMAIN_HITTING_URL . '/carriers/wwe-small/speedshipQuotes.php', $request_data);

//          set response in session
            $response = json_decode($output);
            if (isset($response->q) && (!empty($response->q))) {
                if (isset($response->autoResidentialSubscriptionExpired) &&
                    ($response->autoResidentialSubscriptionExpired == 1)) {
                    $flag_api_response = "no";
                    $srequest_data['residential_detecion_flag'] = $flag_api_response;
                    $currentData = md5(json_encode($srequest_data));
                }

                $requestFromSession[$currentData] = $output;
                WC()->session->set('previousRequestData', $requestFromSession);
            }

            // Crolwer work
            $output = apply_filters('en_show_only_ground_service', $output, $request_data);

//          Eniture debug mood
            do_action("eniture_debug_mood", "Quotes Response (wwe)", json_decode($output));

            /* Action hook */
            do_action('en_box_sizing_response', json_decode($output));

            return $output;
        }
    }

    /**
     * Get Nearest Address If Multiple Warehouses
     * @param $warehous_list
     * @param $receiverZipCode
     * @return array
     */
    function wwe_smpkg_multi_warehouse($warehous_list, $receiverZipCode)
    {

        if (count($warehous_list) == 1) {
            $warehous_list = reset($warehous_list);
            return $this->smpkg_origin_array($warehous_list);
        }
        require_once 'warehouse-dropship/get-distance-request.php';


        $smpkg_distance_request = new Get_sm_distance();
        $accessLevel = "MultiDistance";
        $response_json = $smpkg_distance_request->sm_address($warehous_list, $accessLevel, $this->destinationAddressWweSmall());

        $response_json = json_decode($response_json);

        return $this->smpkg_origin_array($response_json->origin_with_min_dist);
    }

    /**
     * Return the array
     * @param object $result
     * @return object
     */
    public function en_bin_packaging_detail($result)
    {
        return isset($result->binPackaging->response) ? $result->binPackaging->response : [];
    }

    /**
     * Get Shipping Array For Single Shipment
     * @param $result
     * @param $serviceType
     * @return array
     */
    function parse_wwe_small_output($result, $active_services, $product_detail, $quote_settings)
    {
        $all_services_array = [];
        $transit_time = 0;
        $hazardous_fee = 0;
        $en_count_rates = 0;
        $en_box_fee = 0;
        $meta_data = [];
        $accessorials = [];
        $sandBox = '';

        $en_rates = [];
        $en_sorting_rates = [];

        $bin_packaging = [];
        $handling_fee = get_option('wc_settings_hand_free_mark_up_wwe_small_packages');

        $en_always_accessorial = [];
        $multiple_accessorials[] = ['S'];

        $this->forcefully_residential_delivery ? $multiple_accessorials[] = ['R'] : '';
        $hazardous_material = (isset($result->hazardous_material)) ? TRUE : FALSE;

        (get_option('wc_settings_quest_as_residential_delivery_wwe_small_packages') == 'yes') ? $en_always_accessorial[] = 'R' : '';
        ($hazardous_material) ? $en_always_accessorial[] = 'H' : '';
        $meta_data['accessorials'] = json_encode($en_always_accessorial);
        $meta_data['sender_origin'] = (isset($product_detail['sender_origin'])) ? $product_detail['sender_origin'] : '';
        $meta_data['product_name'] = (isset($product_detail['product_name'])) ? $product_detail['product_name'] : '';
        $meta_data['plugin_name'] = "wwe_small_packages_quotes";

        // Pricing per product
        $pricing_per_product = (isset($product_detail['pricing_per_product'])) ? $product_detail['pricing_per_product'] : [];

        // FDO
        $en_fdo_meta_data = (isset($product_detail['en_fdo_meta_data'])) ? $product_detail['en_fdo_meta_data'] : [];
        if (!empty($en_fdo_meta_data) && !is_array($en_fdo_meta_data)) {
            $en_fdo_meta_data = json_decode($en_fdo_meta_data, true);
        }

        $package_bins = (isset($product_detail['package_bins'])) ? $product_detail['package_bins'] : [];
        $en_box_fee_arr = (isset($product_detail['en_box_fee']) && !empty($product_detail['en_box_fee'])) ? $product_detail['en_box_fee'] : [];
        $en_multi_box_qty = (isset($product_detail['en_multi_box_qty']) && !empty($product_detail['en_multi_box_qty'])) ? $product_detail['en_multi_box_qty'] : [];
        $products = (isset($product_detail['products'])) ? $product_detail['products'] : [];

        if (isset($en_box_fee_arr) && is_array($en_box_fee_arr) && !empty($en_box_fee_arr)) {
            foreach ($en_box_fee_arr as $en_box_fee_key => $en_box_fee_value) {
                $en_multi_box_quantity = (isset($en_multi_box_qty[$en_box_fee_key])) ? $en_multi_box_qty[$en_box_fee_key] : 0;
                $en_box_fee += $en_box_fee_value * $en_multi_box_quantity;
            }
        }

        $bin_packaging_filtered = $this->en_bin_packaging_detail($result);
        $bin_packaging_filtered = !empty($bin_packaging_filtered) ? json_decode(json_encode($bin_packaging_filtered), TRUE) : [];

        // Bin Packaging Box Fee|Product Title Start
        $en_box_total_price = 0;
        if (isset($bin_packaging_filtered['bins_packed']) && !empty($bin_packaging_filtered['bins_packed'])) {
            foreach ($bin_packaging_filtered['bins_packed'] as $bins_packed_key => $bins_packed_value) {
                $bin_data = (isset($bins_packed_value['bin_data'])) ? $bins_packed_value['bin_data'] : [];
                $bin_items = (isset($bins_packed_value['items'])) ? $bins_packed_value['items'] : [];
                $bin_id = (isset($bin_data['id'])) ? $bin_data['id'] : '';
                $bin_type = (isset($bin_data['type'])) ? $bin_data['type'] : '';
                $bins_detail = (isset($package_bins[$bin_id])) ? $package_bins[$bin_id] : [];
                $en_box_price = (isset($bins_detail['box_price'])) ? $bins_detail['box_price'] : 0;
                $en_box_total_price += $en_box_price;

                foreach ($bin_items as $bin_items_key => $bin_items_value) {
                    $bin_item_id = (isset($bin_items_value['id'])) ? $bin_items_value['id'] : '';
                    $get_product_name = (isset($products[$bin_item_id])) ? $products[$bin_item_id] : '';
                    if ($bin_type == 'item') {
                        $bin_packaging_filtered['bins_packed'][$bins_packed_key]['bin_data']['product_name'] = $get_product_name;
                    }

                    if (isset($bin_packaging_filtered['bins_packed'][$bins_packed_key]['items'][$bin_items_key])) {
                        $bin_packaging_filtered['bins_packed'][$bins_packed_key]['items'][$bin_items_key]['product_name'] = $get_product_name;
                    }
                }
            }
        }

        $en_box_total_price += $en_box_fee;

        // FDO
        $meta_data['bin_packaging'] = wp_json_encode($bin_packaging_filtered);
        $en_fdo_meta_data['bin_packaging'] = $bin_packaging_filtered;
        $en_fdo_meta_data['bins'] = $package_bins;
        // Bin Packaging Box Fee|Product Title End

        $allServices = [];
        $en_auto_residential_status = !in_array('R', $en_always_accessorial) && isset($result->residentialStatus) && $result->residentialStatus == 'r' ? 'r' : '';
        // FDO
        $en_auto_residential_status == 'r' ? $en_fdo_meta_data['accessorials']['residential'] = true : '';
        $handling_fee = get_option('wc_settings_hand_free_mark_up_wwe_small_packages');
        $this->updateAPISelection($result);

        $no_quotes = false;
        if (empty($result->q)) {
            $result = (object)['q' => (object)[1]];
            $active_services = [1];
            $no_quotes = true;
        }

        if (!empty($result->q) && !empty($active_services)) {

            if (isset($result->t)) {
                $sandBox = ' (Sandbox) ';
            }

            $_count = 0;
            foreach ($result->q as $key => $val) {
                $val = $this->formatQuoteDetails($val);

                if (!isset($val->serviceCode)) {
                    continue;
                }

                //**Change: only if condition by Zeeshan Tanveer
                if (isset($active_services[$val->serviceCode]) || $no_quotes) {

                    $serviceFeeGrandTotal = (isset($val->serviceFeeDetail->serviceFeeGrandTotal)) ? $val->serviceFeeDetail->serviceFeeGrandTotal : 0;
                    $serviceFeeGrandTotal = (isset($val->serviceFeeGrandTotal)) ? $val->serviceFeeGrandTotal : $serviceFeeGrandTotal;
                    $serviceFeeGrandTotal = $this->calculate_markup($serviceFeeGrandTotal, $pricing_per_product);
                    $service_name = (isset($val->serviceCode)) ? $val->serviceCode : '';
                    $hazardous_material_fee = $hazardous_material ? $this->add_hazardous_material($service_name, $quote_settings) : 0;

                    // hazardous material fee
                    if (!empty($hazardous_material_fee)) {
                        $serviceFeeGrandTotal = $this->calculate_handeling_fee($hazardous_material_fee, $serviceFeeGrandTotal);
                    }

                    // product level markup
                    if(!empty($product_detail['product_level_markup'])){
                        $serviceFeeGrandTotal = $this->calculate_service_level_markup($serviceFeeGrandTotal, $product_detail['product_level_markup']);
                    }

                    // origin level markup
                    if(!empty($product_detail['origin_markup'])){
                        $serviceFeeGrandTotal = $this->calculate_service_level_markup($serviceFeeGrandTotal, $product_detail['origin_markup']);
                    }

                    //**Start: Adding Service Level Markup by Zeeshan Tanveer
                    $service_level_markup = (isset($active_services[$val->serviceCode]['markup'])) ? $active_services[$val->serviceCode]['markup'] : 0;
                    $serviceFeeGrandTotal = $this->calculate_service_level_markup($serviceFeeGrandTotal, $service_level_markup);
                    //**End: Adding Service Level Markup by Zeeshan Tanveer

                    // quote settings handling fee
                    $grand_total = strlen($handling_fee) > 0 ? $this->calculate_handeling_fee($handling_fee, $serviceFeeGrandTotal) : $serviceFeeGrandTotal;

                    $surcharges = [];

                    // adding delivery label changes for estimated delivery date
                    $estimate_delivery = isset($val->estimateDelivery) ? $val->estimateDelivery : '';
                    if (!empty($estimate_delivery)) {
                        $str_len = 40;
                        if (strlen($estimate_delivery) > $str_len) {
                            $estimate_delivery = substr($estimate_delivery, 0, $str_len);
                        }
                    }

                    $transit_time = $estimate_delivery;
                    $delivery_days = (isset($val->totalTransitTimeInDays)) ? $val->totalTransitTimeInDays : '';

                    $service_title = (isset($val->serviceDescription)) ? $val->serviceDescription : '';
                    $service_type = 'wwe_small_packages_quotes';

                    if ($hazardous_material && $service_name != "GND") {
                        if (isset($quote_settings['hazardous_materials_shipments']) && ($quote_settings['hazardous_materials_shipments'] == "yes")) {
                            continue;
                        }
                    }

                    $en_service_cost = $grand_total > 0 ? $grand_total + (float)$en_box_total_price : 0;

                    $en_service = array(
                        'service_type' => $service_type . "_" . $service_name,
                        'id' => $service_type . "_" . $service_name,
                        'cost' => $en_service_cost,
                        'rate' => $en_service_cost,
                        'transit_time' => $transit_time,
                        'delivery_days' => $delivery_days,
                        'title' => $service_title . $sandBox,
                        'label' => $service_title . $sandBox,
                        'label_as' => $service_title . $sandBox,
                        'service_name' => $service_name,
                        'sandBox' => $sandBox,
                        'meta_data' => $meta_data,
                        'origin_markup' => $product_detail['origin_markup'],
                        'product_level_markup' => $product_detail['product_level_markup'],
                        'surcharges' => $this->en_get_accessorials_prices($surcharges, $en_always_accessorial, $en_auto_residential_status, $grand_total),
                        'plugin_name' => 'WWE SmPkg',
                        'plugin_type' => 'small',
                        'owned_by' => 'eniture'
                    );

                    foreach ($multiple_accessorials as $multiple_accessorials_key => $accessorial) {
                        $en_fliped_accessorial = array_flip($accessorial);

                        // When auto-rad detected
                        (!$this->forcefully_residential_delivery && $en_auto_residential_status == 'r') ? $accessorial[] = 'R' : '';

                        $en_extra_charges = array_diff_key((isset($en_service['surcharges']) ? $en_service['surcharges'] : []), $en_fliped_accessorial);

                        $en_accessorial_type = implode('', $accessorial);
                        $en_rates[$en_accessorial_type][$en_count_rates] = $en_service;

                        // Service name changed GROUND HOME DELIVERY to FEDEX GROUND
                        if ((isset($en_service['service_type'], $en_service['title'], $en_service['label']) &&
                                $service_type == 'GROUND_HOME_DELIVERY') &&
                            $this->forcefully_residential_delivery &&
                            !in_array('R', $accessorial)) {
                            $en_rates[$en_accessorial_type][$en_count_rates]['service_type'] = 'FEDEX_GROUND_home_ground_pricing';
                            $en_rates[$en_accessorial_type][$en_count_rates]['title'] = 'FedEx Ground';
                            $en_rates[$en_accessorial_type][$en_count_rates]['label'] = 'FedEx Ground';
                        }

                        // Cost of the rates
                        $en_sorting_rates
                        [$en_accessorial_type]
                        [$en_count_rates]['cost'] = // Used for sorting of rates
                        $en_rates
                        [$en_accessorial_type]
                        [$en_count_rates]['cost'] = (isset($en_service['cost']) ? $en_service['cost'] : 0) - array_sum($en_extra_charges);

                        $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['label_sufex'] = wp_json_encode($accessorial);
                        $en_rates[$en_accessorial_type][$en_count_rates]['label_sufex'] = $accessorial;
                        if (isset($en_rates[$en_accessorial_type][$en_count_rates]['service_name']) && strlen($en_accessorial_type) > 0) {
                            $en_rates[$en_accessorial_type][$en_count_rates]['id'] = $en_rates[$en_accessorial_type][$en_count_rates]['service_name'] . '_' . $en_accessorial_type;
                        } else {
                            $alphabets = 'abcdefghijklmnopqrstuvwxyz';
                            $rand_string = substr(str_shuffle(str_repeat($alphabets, mt_rand(1, 10))), 1, 10);
                            $en_rates[$en_accessorial_type][$en_count_rates]['id'] .= $rand_string;
                        }

                        // FDO
                        $en_fdo_meta_data['rate'] = $en_rates[$en_accessorial_type][$en_count_rates];
                        if (isset($en_fdo_meta_data['rate']['meta_data'])) {
                            unset($en_fdo_meta_data['rate']['meta_data']);
                        }

                        $en_fdo_meta_data['quote_settings'] = $quote_settings;
                        $en_rates[$en_accessorial_type][$en_count_rates]['meta_data']['en_fdo_meta_data'] = $en_fdo_meta_data;
                        $en_count_rates++;
                    }
                }
            }
        }

        $en_rates['en_sorting_rates'] = $en_sorting_rates;

        return $en_rates;
    }

    /**
     * Add hazardous fee.
     * @param type $service_code
     * @param type $quote_settings
     * @return type
     */
    function add_hazardous_material($service_code, $quote_settings)
    {
        return ($service_code == "GND") ? $quote_settings['ground_hazardous_material_fee'] : $quote_settings['air_hazardous_material_fee'];
    }

    /**
     * Get accessorials prices from api response
     * @param array $accessorials
     * @return array
     */
    public function en_get_accessorials_prices($accessorials, $en_always_accessorial, $en_auto_residential_status, $total_price)
    {
        $surcharges = [];
        $fuel_surcharges = 0;
        $mapp_surcharges = [
            'RESIDENTIAL_DELIVERY' => 'R',
        ];

        if (isset($accessorials->SurchargeType) && $accessorials->SurchargeType == 'FUEL') {
            $fuel_surcharges = $accessorials->Amount->Amount;
        }

        foreach ($accessorials as $key => $accessorial) {
            $key = (isset($accessorial->SurchargeType)) ? $accessorial->SurchargeType : '';
            ($key == 'FUEL') ? $fuel_surcharges = $accessorial->Amount->Amount : '';

            if (isset($mapp_surcharges[$key])) {
                $accessorial = (isset($accessorial->Amount->Amount)) ? $accessorial->Amount->Amount : 0;
                in_array($mapp_surcharges[$key], $en_always_accessorial) && !$this->forcefully_residential_delivery ?
                    $accessorial = 0 : '';
                $en_auto_residential_status == 'r' && $mapp_surcharges[$key] == 'R' && !$this->forcefully_residential_delivery ?
                    $accessorial = 0 : '';
                $surcharges[$mapp_surcharges[$key]] = $accessorial;
            }
        }

        if (isset($surcharges['R']) && $surcharges['R'] > 0) {
            $residential_surcharges = $surcharges['R'];
            $fuel_percentage = ($fuel_surcharges * 100) / ($total_price - $fuel_surcharges);
            $surcharges['R'] = $residential_surcharges + ($residential_surcharges * $fuel_percentage / 100);
        }

        return $surcharges;
    }

    /**
     * Get Calculate service level markup
     * @param $total_charge
     * @param $international_markup
     */
    function calculate_service_level_markup($total_charge, $international_markup)
    {
        $international_markup = !$total_charge > 0 ? 0 : $international_markup;
        $grandTotal = 0;
        if (floatval($international_markup)) {
            $pos = strpos($international_markup, '%');
            if ($pos > 0) {
                $rest = substr($international_markup, $pos);
                $exp = explode($rest, $international_markup);
                $get = $exp[0];
                $percnt = $get / 100 * $total_charge;
                $grandTotal += $total_charge + $percnt;
            } else {
                $grandTotal += floatval($total_charge) + floatval($international_markup);
            }
        } else {
            $grandTotal += floatval($total_charge);
        }
        return $grandTotal;
    }

    /**
     * Calculate Handeling Fee For Each Shipment
     * @param $handeling_fee
     * @param $total
     * @return int
     */
    function calculate_markup($total, $pricing_per_product)
    {
        // Pricing per product
        $en_pricing_per_product = apply_filters('en_pricing_per_product_existence', false);
        if (!$en_pricing_per_product) {
            return $total;
        }

        $handeling_fee = 0;
        $product_quantity = 1;
        $product_rental = 'no';
        if (!empty($pricing_per_product)) {
            foreach ($pricing_per_product as $key => $per_product) {
                $handeling_fee = (isset($per_product['product_markup'])) ? $per_product['product_markup'] : 0;
                $product_quantity = (isset($per_product['product_quantity'])) ? $per_product['product_quantity'] : 0;
                $product_rental = (isset($per_product['product_rental'])) ? $per_product['product_rental'] : 'no';
            }
        }

        $handeling_fee = isset($handeling_fee) && $handeling_fee > 0 ? $handeling_fee : 0;
        $handeling_fee = !$total > 0 ? 0 : $handeling_fee;
        $grandTotal = 0;
        if (floatval($handeling_fee)) {
            $pos = strpos($handeling_fee, '%');
            if ($pos > 0) {
                $rest = substr($handeling_fee, $pos);
                $exp = explode($rest, $handeling_fee);
                $get = $exp[0];
                $percnt = $get / 100 * $total;
                $handeling_fee = $percnt;
            }
        }

        if ($product_rental == 'yes') {
            $total_fee = ((float)$total + (float)$handeling_fee) * 2;
        } else {
            $total_fee = (float)$total + (float)$handeling_fee;
        }

        return $total_fee;
    }

    /**
     * Calculate Handeling Fee For Each Shipment
     * @param $handeling_fee
     * @param $total
     * @return int
     */
    function calculate_handeling_fee($handeling_fee, $total)
    {
        $handeling_fee = !$total > 0 ? 0 : $handeling_fee;
        $grandTotal = 0;
        if (floatval($handeling_fee)) {
            $pos = strpos($handeling_fee, '%');
            if ($pos > 0) {
                $rest = substr($handeling_fee, $pos);
                $exp = explode($rest, $handeling_fee);
                $get = $exp[0];
                $percnt = $get / 100 * $total;
                $grandTotal += $total + $percnt;
            } else {
                $grandTotal += $total + $handeling_fee;
            }
        } else {
            $grandTotal += $total;
        }
        return $grandTotal;
    }

    /**
     * Create Origin Array
     * @param $origin
     * @return array
     */
    function smpkg_origin_array($origin)
    {

//      In-store pickup and local delivery
        if (has_filter("en_wwe_small_wd_origin_array_set")) {
            return apply_filters("en_wwe_small_wd_origin_array_set", $origin);
        }

        $zip = (isset($origin->zip)) ? $origin->zip : "";
        $city = (isset($origin->city)) ? $origin->city : "";
        $state = (isset($origin->state)) ? $origin->state : "";
        $country = (isset($origin->country)) ? $origin->country : "";
        $location = (isset($origin->location)) ? $origin->location : "";
        $locationId = (isset($origin->id)) ? $origin->id : "";
        $correctedCity = (isset($origin->wwe_correct_city)) ? $origin->wwe_correct_city : "";
        return array('locationId' => $locationId, 'zip' => $zip, 'city' => $city, 'state' => $state, 'location' => $location, 'country' => $country, 'corrected_city' => $correctedCity);
    }

    /** Return woocomerce and WWE Small version */
    function wwesmpkgWcVersionNumber()
    {

        if (!function_exists('get_plugins'))
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $pluginFolder = get_plugins('/' . 'woocommerce');
        $pluginFile = 'woocommerce.php';
        $wwesmpkPluginFolder = get_plugins('/' . 'small-package-quotes-wwe-edition');
        $wwesmpkgPluginFile = 'woocommerceShip.php';
        $wcPlugin = (isset($pluginFolder[$pluginFile]['Version'])) ? $pluginFolder[$pluginFile]['Version'] : "";
        $wwesmpkgPlugin = (isset($wwesmpkPluginFolder[$wwesmpkgPluginFile]['Version'])) ? $wwesmpkPluginFolder[$wwesmpkgPluginFile]['Version'] : "";
        $pluginVersions = array(
            "woocommerce_plugin_version" => $wcPlugin,
            "wwesmpkg_plugin_version" => $wwesmpkgPlugin
        );
        return $pluginVersions;
    }

    // Formats and returns indexes of quote in case of new api response
    function formatQuoteDetails($quote)
    {
        if (!empty($quote) && isset($quote->totalOfferPrice) && isset($quote->timeInTransit)) {
            $quote->serviceCode = !empty($quote->timeInTransit->upsServiceCode) ? $quote->timeInTransit->upsServiceCode : '';
            $quote->serviceFeeGrandTotal = !empty($quote->totalOfferPrice->value) ? $quote->totalOfferPrice->value : '';
            $quote->DeliveryDate = !empty($quote->timeInTransit->estimatedDeliveryDate) ? $quote->timeInTransit->estimatedDeliveryDate : '';
            $quote->totalTransitTimeInDays = !empty($quote->timeInTransit->totalTransitTimeInDays) ? $quote->timeInTransit->totalTransitTimeInDays : '';
            $quote->serviceDescription = !empty($quote->timeInTransit->serviceDescription) ? $quote->timeInTransit->serviceDescription : '';

            // Format estimated delivery date
            $delivery_time = isset($quote->timeInTransit->deliveryBy) ? $quote->timeInTransit->deliveryBy : '';
            $delivery_date = isset($quote->timeInTransit->estimatedDeliveryDate) ? ($quote->timeInTransit->estimatedDeliveryDate) : '';
            $estimated_delivery = $delivery_date . " " . $delivery_time;
            $estimated_delivery = (new DateTime($estimated_delivery))->format('h:i A l m/d/Y');

            $quote->estimateDelivery = $estimated_delivery;
        }

        return $quote;
    }

    function updateAPISelection($result)
    {
        // New API to Old API migration
        $newAPICredentials = isset($result->newAPICredentials) ? $result->newAPICredentials : [];
        
        if (!empty($newAPICredentials) && isset($newAPICredentials->client_id) && isset($newAPICredentials->client_secret)) {
            $username = get_option('wc_settings_username_wwe_small_packages_quotes');
            $password = get_option('wc_settings_password_wwe_small_packages');

            // Update customer's API selection and creds info
            update_option('api_endpoint_wwe_small_packages', 'wwe_small_new_api');
            update_option('wwe_small_client_id', $newAPICredentials->client_id);
            update_option('wwe_small_client_secret', $newAPICredentials->client_secret);
            update_option('wwe_small_new_api_username', $username);
            update_option('wwe_small_new_api_password', $password);
        }

        // Old API to New API migration
        $oldAPICredentials = isset($result->oldAPICredentials) ? $result->oldAPICredentials : [];
        
        if (!empty($oldAPICredentials) && isset($oldAPICredentials->account_number)) {
            update_option('api_endpoint_wwe_small_packages', 'wwe_small_old_api');
        }
    }

    function applyErrorManagement($quotes_request)
    {
        if (empty($quotes_request)) return $quotes_request;
        
        // error management will be applied only for more than 1 product
        $products_count = 0;
        foreach ($quotes_request as $qr_value) {
            if (!empty($qr_value['products']) && is_array($qr_value['products'])) {
                $products_count = count($qr_value['products']);
                if ($products_count > 1) break;
            }
        }

        $dimsArr = ['product_width_array', 'product_height_array', 'product_length_array', 'speed_ship_product_weight'];
        $otherArr = array_merge($dimsArr, ['speed_ship_product_price_array', 'speed_ship_title_array', 'speed_ship_quantity_array', 'product_name', 'products', 'nesting_percentage','nesting_dimension', 'nested_max_limit', 'nested_stack_property', 'ship_item_alone', 'product_tags_array']);
        $error_option = get_option('error_management_settings_wwe_small_packages');
        $dont_quote_shipping = false;
        $items_ids = [];
        
        foreach ($quotes_request as $org_key => $value) {
            foreach ($value['product_width_array'] as $k => $v) {
                if (empty($value['speed_ship_product_weight'][$k])) {
                    if ($error_option == 'dont_quote_shipping') {
                        $dont_quote_shipping = true;
                        break;
                    } else {
                        foreach ($otherArr as $other_value) {
                            unset($quotes_request[$org_key][$other_value][$k]);
                            $quotes_request[$org_key]['error_management'] = $error_option;
                            $items_ids[] = $k;
                        }
                    }
                }
            }

            if ($dont_quote_shipping) break;
        }

        // error management will be applied for all products in case of dont quote shipping option
        if ($dont_quote_shipping) {
            foreach ($quotes_request as $key => $value) {
                foreach ($otherArr as $k => $v) {
                    $quotes_request[$key][$v] = [];
                }

                $quotes_request[$key]['error_management'] = $error_option;
            }
        }

        // set error property for items in fdo meta-data array to hide them on order widget details
        if (!empty($items_ids) && !$dont_quote_shipping && isset($quotes_request['en_fdo_meta_data']['items'])) {
            foreach ($quotes_request['en_fdo_meta_data']['items'] as $key => $item) {
                if (!isset($item['id'])) continue;

                if (in_array($item['id'], $items_ids)) {
                    $quotes_request['en_fdo_meta_data']['items'][$key]['error_management'] = true;
                }
            }
        }

        return $quotes_request;
    }
}
