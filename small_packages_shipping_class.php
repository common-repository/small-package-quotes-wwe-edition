<?php

/**
 * WWE Small Shipping Class
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Initialization
 */
function smallpkg_shipping_method_init()
{

    if (!class_exists('WC_speedship')) {

        /**
         * WWE Small Shipping Calculation Class
         */
        class WC_speedship extends WC_Shipping_Method
        {

            /**
             * Woo-commerce Shipping Field Attributes
             * @param $instance_id
             */
            public $smallInluded = false;
            public $order_detail;
            public $is_autoresid;
            public $accessorials;
            public $helper_obj;
            public $instore_pickup_and_local_delivery;
            public $group_small_shipments;
            public $web_service_inst;
            public $package_plugin;
            public $InstorPickupLocalDelivery;
            public $woocommerce_package_rates;
            public $quote_settings;
            public $shipment_type;
            public $eniture_rates;
            public $VersionCompat;
            public $en_ignore_rate_cost;
            public $en_not_returned_the_quotes = FALSE;
            public $minPrices = [];
            public $en_fdo_meta_data = [];

            public function __construct($instance_id = 0)
            {
                $this->id = 'speedship';
                $this->helper_obj = new En_Helper_Class();
                $this->instance_id = absint($instance_id);
                $this->method_title = __('Small Package (parcel)');
                $this->method_description = __('Real-time small package (parcel) shipping rates from Worldwide Express.');
                $this->supports = array(
                    'shipping-zones',
                    'instance-settings',
                    'instance-settings-modal',
                );
                $this->enabled = "yes";
                $this->title = "Small Package Quotes - Worldwide Express Edition ";
                $this->init();
            }

            /**
             * Update WWE Small Woo-commerce Shipping Settings
             */
            function init()
            {

                $this->init_form_fields();
                $this->init_settings();
                add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
            }

            /**
             * Ignore Products
             */
            public function en_ignored_products($package)
            {
                global $woocommerce;
                $products = $woocommerce->cart->get_cart();
                $items = $product_name = [];
                $lobster_list = [
                    '6-lobster-package',
                    '12-lobster-package'
                ];

                $this->en_ignore_rate_cost = 0;
                $en_ignore_rates = isset($package['rates']) ? $package['rates'] : '';
                if(!empty($en_ignore_rates)) {
                    foreach ($en_ignore_rates as $en_ignore_rates_key => $en_ignore_rate) {
                        if (isset($en_ignore_rate->method_id) && $en_ignore_rate->method_id == 'flat_rate') {
                            $this->en_ignore_rate_cost = $en_ignore_rate->cost;
                            continue;
                        }
                    }
                }

                $wc_settings_wwe_ignore_items = get_option("en_ignore_items_through_freight_classification");
                $en_get_current_classes = strlen($wc_settings_wwe_ignore_items) > 0 ? trim(strtolower($wc_settings_wwe_ignore_items)) : '';
                $en_get_current_classes_arr = strlen($en_get_current_classes) > 0 ? array_map('trim', explode(',', $en_get_current_classes)) : [];

                foreach ($products as $key => $product_obj) {
                    $product = $product_obj['data'];

                    //get product shipping class
                    $en_ship_class = strtolower($product_obj['data']->get_shipping_class());
                    if (in_array($en_ship_class, $lobster_list) && in_array($en_ship_class, $en_get_current_classes_arr)) {
                        $attributes = $product->get_attributes();
                        $product_qty = $product_obj['quantity'];
                        $product_title = str_replace(array("'", '"'), '', $product->get_title());
                        $product_name[] = $product_qty . " x " . $product_title;

                        $meta_data = [];
                        if (!empty($attributes)) {
                            foreach ($attributes as $attr_key => $attr_value) {
                                $meta_data[] = [
                                    'key' => $attr_key,
                                    'value' => $attr_value,
                                ];
                            }
                        }

                        $items[] = [
                            'id' => $product_obj['product_id'],
                            'name' => $product_title,
                            'quantity' => $product_qty,
                            'price' => $product->get_price(),
                            'weight' => wc_get_weight($product->get_weight(), 'lbs'),
                            'length' => wc_get_dimension($product->get_length(), 'in'),
                            'width' => wc_get_dimension($product->get_width(), 'in'),
                            'height' => wc_get_dimension($product->get_height(), 'in'),
                            'type' => 'flat_rate',
                            'product' => $product_obj['variation_id'] > 0 ? 'variable' : 'simple',
                            'sku' => $product->get_sku(),
                            'attributes' => $attributes,
                            'variant_id' => $product_obj['variation_id'],
                            'meta_data' => $meta_data,
                        ];
                    }
                }

                $flat_rate = [];

                if (!empty($items)) {
                    $flat_rate = [
                        'id' => 'en_flat_rate',
                        'label' => 'Flat Rate',
                        'cost' => $this->en_ignore_rate_cost,
                        'label_sufex' => ['S'],
                        'plugin_name' => 'WWE SmPkg',
                        'plugin_type' => 'small',
                        'owned_by' => 'eniture'
                    ];

                    $flat_rate_fdo = [
                        'plugin_type' => 'small',
                        'plugin_name' => 'wwe_small_packages_quotes',
                        'accessorials' => '',
                        'items' => $items,
                        'address' => '',
                        'handling_unit_details' => '',
                        'rate' => $flat_rate,
                    ];

                    $meta_data = [
                        'sender_origin' => 'Flat Rate Product',
                        'product_name' => wp_json_encode($product_name),
                        'en_fdo_meta_data' => $flat_rate_fdo,
                    ];

                    $flat_rate['meta_data'] = $meta_data;

                }

                return $flat_rate;
            }

            /**
             * Virtual Products
             */
            public function en_virtual_products()
            {
                global $woocommerce;
                $products = $woocommerce->cart->get_cart();
                $items = $product_name = [];
                foreach ($products as $key => $product_obj) {
                    $product = $product_obj['data'];
                    $is_virtual = $product->get_virtual();

                    if ($is_virtual == 'yes') {
                        $attributes = $product->get_attributes();
                        $product_qty = $product_obj['quantity'];
                        $product_title = str_replace(array("'", '"'), '', $product->get_title());
                        $product_name[] = $product_qty . " x " . $product_title;

                        $meta_data = [];
                        if (!empty($attributes)) {
                            foreach ($attributes as $attr_key => $attr_value) {
                                $meta_data[] = [
                                    'key' => $attr_key,
                                    'value' => $attr_value,
                                ];
                            }
                        }

                        $items[] = [
                            'id' => $product_obj['product_id'],
                            'name' => $product_title,
                            'quantity' => $product_qty,
                            'price' => $product->get_price(),
                            'weight' => 0,
                            'length' => 0,
                            'width' => 0,
                            'height' => 0,
                            'type' => 'virtual',
                            'product' => 'virtual',
                            'sku' => $product->get_sku(),
                            'attributes' => $attributes,
                            'variant_id' => 0,
                            'meta_data' => $meta_data,
                        ];
                    }
                }

                $virtual_rate = [];

                if (!empty($items)) {
                    $virtual_rate = [
                        'id' => 'en_virtual_rate',
                        'label' => 'Virtual Quote',
                        'cost' => 0,
                    ];

                    $virtual_fdo = [
                        'plugin_type' => 'small',
                        'plugin_name' => 'wwe_small_packages_quotes',
                        'accessorials' => '',
                        'items' => $items,
                        'address' => '',
                        'handling_unit_details' => '',
                        'rate' => $virtual_rate,
                    ];

                    $meta_data = [
                        'sender_origin' => 'Virtual Product',
                        'product_name' => wp_json_encode($product_name),
                        'en_fdo_meta_data' => $virtual_fdo,
                    ];

                    $virtual_rate['meta_data'] = $meta_data;

                }

                return $virtual_rate;
            }

            /**
             * Enable Woo-commerce Shipping For WWE Small
             */
            public function init_form_fields()
            {

                $this->instance_form_fields = array(
                    'enabled' => array(
                        'title' => __('Enable / Disable', 'woocommerce'),
                        'type' => 'checkbox',
                        'label' => __('Enable This Shipping Service', 'woocommerce'),
                        'default' => 'yes',
                        'id' => 'speed_ship_enable_disable_shipping'
                    )
                );
            }

            /**
             * Multi shipment query
             * @param array $en_rates
             * @param string $accessorial
             */
            public function en_multi_shipment($en_rates, $accessorial, $origin)
            {
                $accessorial .= '_wwe_small';
                $en_rates = (isset($en_rates) && (is_array($en_rates))) ? array_slice($en_rates, 0, 1) : [];
                $total_cost = array_sum($this->VersionCompat->enArrayColumn($en_rates, 'cost'));

                !$total_cost > 0 ? $this->en_not_returned_the_quotes = TRUE : '';

                $en_rates = !empty($en_rates) ? reset($en_rates) : [];
                $this->minPrices[$origin] = $en_rates;
                $this->en_fdo_meta_data[$origin] = (isset($en_rates['meta_data']['en_fdo_meta_data'])) ? $en_rates['meta_data']['en_fdo_meta_data'] : [];

                if (isset($this->eniture_rates[$accessorial])) {
                    $this->eniture_rates[$accessorial]['cost'] += $total_cost;
                } else {
                    $this->eniture_rates[$accessorial] = [
                        'id' => $accessorial,
                        'label' => 'Shipping',
                        'cost' => $total_cost,
                        'label_sufex' => str_split($accessorial),
                        'plugin_name' => 'WWE SmPkg',
                        'plugin_type' => 'small',
                        'owned_by' => 'eniture'
                    ];
                }
            }

            /**
             * Single shipment query
             * @param array $en_rates
             * @param string $accessorial
             */
            public function en_single_shipment($en_rates, $accessorial, $origin)
            {
                $this->eniture_rates = array_merge($this->eniture_rates, $en_rates);
            }

            /**
             * Calculate Shipping Rates For WWE Small
             * @param $package
             * @return string
             * @global $wpdb
             * @global $current_user
             */
            public function calculate_shipping($package = [], $eniture_admin_order_action = false)
            {
                if (is_admin() && !wp_doing_ajax() && !$eniture_admin_order_action) {
                    return [];
                }

                $this->package_plugin = get_option('wwe_small_packages_quotes_package');
                $selected_quotes_service_options_array = $this->wwe_smpkg_get_active_services();
                $this->get_settings_fields($selected_quotes_service_options_array);
                $this->instore_pickup_and_local_delivery = FALSE;

                $zipcode_for_handling_fee = 0;
                global $wpdb;
                global $current_user;
                $output = "";
                $sandBox = "";
                $rates = [];
                $label_sufex_arr = [];
                $rateArray = [];
                $quotesArray = [];
                $quotes = [];
                $web_service_arr = [];
                $request_for = "";
                (isset($package['itemType']) && $package['itemType'] == 'ltl' ? $request_for = "ltl" : $request_for = "small");
                /*  check coupon exist or not   */
                if (has_filter('check_coupons') && $request_for == 'small') {
                    $couponCode = apply_filters('check_coupons', $package);
                }

                $this->VersionCompat = new VersionCompat();
                $web_service_inst = new smallpkg_shipping_get_quotes();

                $this->web_service_inst = $web_service_inst;

                $group_small_shipments = new group_small_shipment();

                $this->group_small_shipments = $group_small_shipments;

                $coupn = WC()->cart->get_coupons();
                if (isset($coupn) && !empty($coupn)) {
                    $freeShipping = $this->wweSmpkgFreeShipping($coupn);
                    if ($freeShipping == 'y')
                        return FALSE;
                }
                $changObj = new WWE_Small_Woo_Update_Changes();
                (strlen(WC()->customer->get_shipping_postcode()) > 0) ? $freight_zipcode = WC()->customer->get_shipping_postcode() : $freight_zipcode = $changObj->wwe_small_postcode();
                if (empty($freight_zipcode)) {
                    return FALSE;
                }

                // Free shipping
                if ($this->quote_settings['handling_fee'] == '-100%') {
                    $rates = array(
                        'id' => 'speedship:' . 'free',
                        'label' => 'Free Shipping',
                        'cost' => 0,
                        'plugin_name' => 'WWE SmPkg',
                        'plugin_type' => 'small',
                        'owned_by' => 'eniture'
                    );
                    $this->add_rate($rates);
                    
                    return [];
                }

                $this->create_speedship_small_option();
                $sm_package = $group_small_shipments->small_package_shipments($package, $web_service_inst);

                // apply hide methods shipping rules
                $shipping_rules_obj = new EnWweSmallShippingRulesAjaxReq();
                $shipping_rules_applied = $shipping_rules_obj->apply_shipping_rules($sm_package);
                if ($shipping_rules_applied) {
                    return [];
                }
                
                // Suppress small rates when weight threshold is met
                $supress_parcel_rates = apply_filters('en_suppress_parcel_rates_hook', '');
                if (!empty($sm_package) && is_array($sm_package) && $supress_parcel_rates) {
                    foreach ($sm_package as $org_id => $pckg) {
                        $total_shipment_weight = 0;

                        $shipment_items = !empty($pckg['items']) ? $pckg['items'] : []; 
                        foreach ($shipment_items as $item) {
                            $total_shipment_weight += (floatval($item['productWeight']) * $item['productQty']);
                        }

                        $sm_package[$org_id]['shipment_weight'] = $total_shipment_weight;
                        $weight_threshold = get_option('en_weight_threshold_lfq');
                        $weight_threshold = isset($weight_threshold) && $weight_threshold > 0 ? $weight_threshold : 150;
                        
                        if ($total_shipment_weight > $weight_threshold) {
                            $sm_package[$org_id]['is_shipment'] = 'ltl';
                            $sm_package[$org_id]['origin']['ptype'] = 'ltl';
                        }
                    }
                }
                
                // Crowler work
                $request_for != 'ltl' ? $sm_package = apply_filters('en_check_sbs_packaging', $sm_package) : '';
                if (isset($sm_package['warehouse_origin'])) unset($sm_package['warehouse_origin']);
                if (isset($sm_package) && !empty($sm_package)) {
                    $package_valid = true;
                    foreach ($sm_package as $sm_package_key => $sm_package_detail) {
                        $request_for != 'ltl' && isset($sm_package_detail['ltl']) ? $package_valid = false : '';
                    }

                    !$package_valid ? $sm_package = [] : '';
                }

                // pricing_per_product
                $pricing_product_origins = [];
                if(isset($sm_package['pricing_product_origins'])){
                    $pricing_product_origins = $sm_package['pricing_product_origins'];
                    unset($sm_package['pricing_product_origins']);
                }

                $no_param_multi_ship = 0;
                /* apply filter for filter count sample,simple,total,origion */
                if (has_filter('small_package_check_grouping') && $request_for == 'small') {
                    $sm_package = apply_filters('small_package_check_grouping', $sm_package);
                }
                $web_service_arr = $web_service_inst->get_web_service_array($sm_package, $package, $this->package_plugin);

                // Pricing per product
                $en_pricing_per_product = apply_filters('en_pricing_per_product_existence', false);

                if (isset($web_service_arr) && $web_service_arr != '') {

                    $EnWweSmallTransitDays = new EnWweSmallTransitDays();
                    foreach ($web_service_arr as $key => $request) {
                        if ($request != 'ltl') {
                            $sPackage = $request;
                            $package_bins = (isset($sPackage['bins'])) ? $sPackage['bins'] : [];
                            $en_box_fee = (isset($sPackage['en_box_fee'])) ? $sPackage['en_box_fee'] : [];
                            $en_multi_box_qty = (isset($sPackage['speed_ship_quantity_array'])) ? $sPackage['speed_ship_quantity_array'] : [];
                            $fedex_bins = (isset($sPackage['fedex_bins'])) ? $sPackage['fedex_bins'] : [];
                            $hazardous_status = (isset($sPackage['hazardous_status'])) ? $sPackage['hazardous_status'] : '';
                            $en_fdo_meta_data = (isset($sPackage['en_fdo_meta_data'])) ? $sPackage['en_fdo_meta_data'] : '';
                            // Pricing per product
                            $pricing_per_product = (isset($sPackage['pricing_per_product'])) ? $sPackage['pricing_per_product'] : '';
                            $package_bins = !empty($fedex_bins) ? $package_bins + $fedex_bins : $package_bins;
                            if (!isset($sPackage['speed_ship_senderZip'])) {
                                continue;
                            }

                            $speed_ship_senderZip = $sPackage['speed_ship_senderZip'];
                            if ($en_pricing_per_product && strlen($speed_ship_senderZip) > 0) {
                                $speed_ship_senderZip = $key;
                            }

                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['product_name'] = json_encode($sPackage['product_name']);
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['products'] = $sPackage['products'];
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['sender_origin'] = $sPackage['sender_origin'];
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['package_bins'] = $package_bins;
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['en_box_fee'] = $en_box_fee;
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['en_multi_box_qty'] = $en_multi_box_qty;
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['hazardous_status'] = $hazardous_status;
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['origin_markup'] = $sPackage['origin_markup'];
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['product_level_markup'] = $sPackage['product_level_markup'];
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['en_fdo_meta_data'] = $en_fdo_meta_data;
                            // $this->web_service_inst->product_detail[$speed_ship_senderZip]['exempt_ground_transit_restriction'] = (isset($sPackage['exempt_ground_transit_restriction'])) ? $sPackage['exempt_ground_transit_restriction'] : '';
                            // Pricing per product
                            $this->web_service_inst->product_detail[$speed_ship_senderZip]['pricing_per_product'] = $pricing_per_product;

                            if (isset($sPackage['forcefully_residential_delivery']) && $sPackage['forcefully_residential_delivery'] == 'on') {
                                $this->web_service_inst->forcefully_residential_delivery = TRUE;
                            }

                            $output = $web_service_inst->get_web_quotes($request, $this->package_plugin);
                            $zipcode_for_handling_fee = $key;

                            if(!((isset($sPackage['exempt_ground_transit_restriction'])) && isset($sPackage['exempt_ground_transit_restriction']) == 'yes')){
                                $output = $EnWweSmallTransitDays->wwe_small_enable_disable_ups_ground(json_decode($output));
                            }

                            $quotes[$key] = json_decode($output);

                            (isset($request['hazardous_material']) && isset($quotes[$key]) && !empty($quotes[$key])) ? $quotes[$key]->hazardous_material = TRUE : "";

                            $this->InstorPickupLocalDelivery = (isset($quotes[$key]->InstorPickupLocalDelivery)) ? $quotes[$key]->InstorPickupLocalDelivery : [];

                            $Wwe_Small_Auto_Residential_Detection = new Wwe_Small_Auto_Residential_Detection();
                            $label_sfx_rtrn = $Wwe_Small_Auto_Residential_Detection->filter_label_sufex_array($quotes[$key]);
                            $label_sufex_arr = array_merge($label_sufex_arr, $label_sfx_rtrn);
                        }
                    }
                }

                // Virtual products
                $virtual_rate = $this->en_virtual_products();
                if (!empty($virtual_rate)) {
                    $this->minPrices['virtual_rate'] = $virtual_rate;
                    $this->en_fdo_meta_data['virtual_rate'] = (isset($virtual_rate['meta_data']['en_fdo_meta_data'])) ? $virtual_rate['meta_data']['en_fdo_meta_data'] : [];
                }

                // Ignored products added to order widget details
                $en_ignored_rate = $this->en_ignored_products($package);
                if (!empty($en_ignored_rate)) {
                    $this->minPrices['flat_rate'] = $en_ignored_rate;
                    $this->en_fdo_meta_data['flat_rate'] = (isset($en_ignored_rate['meta_data']['en_fdo_meta_data'])) ? $en_ignored_rate['meta_data']['en_fdo_meta_data'] : [];
                }
                foreach ($quotes as $qIndex => $quote) {
                    //  Update origin city with correct city for WWE
                    $originCityData = (isset($quote->originCityData) && !empty($quote->originCityData)) ? $quote->originCityData : '';
                    if (isset($originCityData) && !empty($originCityData)) {
                        $this->wwe_small_update_origin_data($originCityData);
                    }

                    $quotesArray[$qIndex] = $quote;
                }

                $quotes = $quotesArray;

                $en_is_shipment = (count($quotes) > 1 || $no_param_multi_ship == 1) || $no_param_multi_ship == 1 ? 'en_multi_shipment' : 'en_single_shipment';
                $this->quote_settings['shipment'] = $en_is_shipment;
                $this->eniture_rates = [];
                $en_rates = $quotes;

                // apply override rates shipping rules
                $shipping_rule_obj = new EnWweSmallShippingRulesAjaxReq();
                $en_rates = $shipping_rule_obj->apply_shipping_rules($sm_package, true, $en_rates);

                foreach ($en_rates as $origin => $step_for_rates) {

                    $product_detail = (isset($this->web_service_inst->product_detail[$origin])) ? $this->web_service_inst->product_detail[$origin] : [];
                    (isset($domestic_international[$origin])) ? $services = $domestic_international[$origin] : '';
                    $filterd_rates = $web_service_inst->parse_wwe_small_output($step_for_rates, $selected_quotes_service_options_array, $product_detail, $this->quote_settings);
                    $en_sorting_rates = (isset($filterd_rates['en_sorting_rates'])) ? $filterd_rates['en_sorting_rates'] : "";
                    if (isset($filterd_rates['en_sorting_rates']))
                        unset($filterd_rates['en_sorting_rates']);

                    if (is_array($filterd_rates) && !empty($filterd_rates)) {
                        foreach ($filterd_rates as $accessorial => $service) {
                            (!empty($filterd_rates[$accessorial])) ? array_multisort($en_sorting_rates[$accessorial], SORT_ASC, $filterd_rates[$accessorial]) : $en_sorting_rates[$accessorial] = [];
                            $this->$en_is_shipment($filterd_rates[$accessorial], $accessorial, $origin);
                        }
                    } else {
                        $this->en_not_returned_the_quotes = TRUE;
                    }

                    // Add backup rates
                    if (($this->en_not_returned_the_quotes && get_option('backup_rates_carrier_returns_error_wwe_small') == 'yes') || (is_array($filterd_rates) && isset($filterd_rates['error']) && $filterd_rates['error'] == 'backup_rate' && get_option('backup_rates_carrier_fails_to_return_response_wwe_small') == 'yes')) {
                        $this->wwe_small_backup_rates();
                        return [];
                    }
                }
                if ($this->en_not_returned_the_quotes) {
                    return [];
                }

                if ($en_is_shipment == 'en_single_shipment' || (count($pricing_product_origins) == 1)) {

                    // In-store pickup and local delivery
                    $instore_pickup_local_devlivery_action = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'instore_pickup_local_devlivery');
                    if (isset($this->web_service_inst->en_wd_origin_array['suppress_local_delivery']) && $this->web_service_inst->en_wd_origin_array['suppress_local_delivery'] == "1" && (!is_array($instore_pickup_local_devlivery_action))) {
                        $this->eniture_rates = apply_filters('suppress_local_delivery', $this->eniture_rates, $this->web_service_inst->en_wd_origin_array, $this->package_plugin, $this->InstorPickupLocalDelivery);
                    }

                }
                $rad_status = true;
                $all_plugins = apply_filters('active_plugins', get_option('active_plugins'));
                if (stripos(implode($all_plugins), 'residential-address-detection.php') || is_plugin_active_for_network('residential-address-detection/residential-address-detection.php')) {
                    if(get_option('suspend_automatic_detection_of_residential_addresses') != 'yes') {
                        $rad_status = get_option('residential_delivery_options_disclosure_types_to') != 'not_show_r_checkout';
                    }
                }
                $accessorials = $rad_status == true ? ['R' => 'residential delivery'] : [];

                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);

                $en_rates = $this->eniture_rates;

                // Custom work get from old programming.
                if (has_filter('count_sample') && $request_for == 'small') {
                    $sample = apply_filters('count_sample', $sm_package);
                    $sampleQuantity = $sample['sample'];
                }

                // Ignored products added to order widget details
                $en_ignored_flag = false;
                if (empty($en_rates)) {
                    $en_ignored_flag = true;
                    $en_rates = [$en_ignored_rate];
                }

                // Images for FDO
                $image_urls = apply_filters('en_fdo_image_urls_merge', []);

                foreach ($en_rates as $accessorial => $rate) {
                    // Custom work get from old programming.
                    if (has_filter('check_implements_coupons') && $request_for == 'small') {
                        if ($couponCode == 'handful' || $sampleQuantity > 0) {
                            $rate = apply_filters('check_implements_coupons', $rate);
                        }
                    }

                    if (isset($rate['label_sufex']) && !empty($rate['label_sufex'])) {
                        $label_sufex = array_intersect_key($accessorials, array_flip($rate['label_sufex']));
                        $rate['label'] .= (!empty($label_sufex)) ? ' with ' . implode(' and ', $label_sufex) : '';

                        // Order widget detail set
                        // FDO
                        if (isset($this->minPrices) && !empty($this->minPrices)) {
                            if ($en_is_shipment == 'en_single_shipment' && !$en_ignored_flag) {
                                $this->minPrices['speedship_rate'] = $rate;
                                $this->en_fdo_meta_data['speedship_rate'] = (isset($rate['meta_data']['en_fdo_meta_data'])) ? $rate['meta_data']['en_fdo_meta_data'] : [];
                            }

                            $rate['meta_data']['min_prices'] = wp_json_encode($this->minPrices);
                            $rate['minPrices'] = $this->minPrices;
                            $rate['meta_data']['en_fdo_meta_data']['data'] = array_values($this->en_fdo_meta_data);
                            $rate['meta_data']['en_fdo_meta_data']['shipment'] = 'multiple';
                            $rate['meta_data']['en_fdo_meta_data'] = wp_json_encode($rate['meta_data']['en_fdo_meta_data']);
                        } else {
                            $en_set_fdo_meta_data['data'] = [$rate['meta_data']['en_fdo_meta_data']];
                            $en_set_fdo_meta_data['shipment'] = 'sinlge';
                            $rate['meta_data']['en_fdo_meta_data'] = wp_json_encode($en_set_fdo_meta_data);
                        }

                        // Images for FDO
                        $rate['meta_data']['en_fdo_image_urls'] = wp_json_encode($image_urls);
                    }

                    if ($en_is_shipment == 'en_single_shipment') {
                        $wwe_small_delivey_estimate = get_option('wwe_small_delivery_estimates');

                        if (!empty($wwe_small_delivey_estimate) && $wwe_small_delivey_estimate != 'dont_show_estimates') {
                            if ($wwe_small_delivey_estimate == 'delivery_date' && !empty($rate['transit_time'])) {
                                $rate['label'] .= ' (Expected delivery by ' . $rate['transit_time'] . ')';
                            } else if ($wwe_small_delivey_estimate == 'delivery_days' && !empty($rate['delivery_days'])) {
                                $rate['label'] .= ' (Intransit days: ' . $rate['delivery_days'] . ')';
                            }
                        }
                    }

                    if (isset($rate['cost']) && $rate['cost'] > 0) {
                        $rate['id'] = isset($rate['id']) && is_string($rate['id']) ? 'speedship:' . $rate['id'] : '';
                        !$en_ignored_flag && isset($rate['cost']) ? $rate['cost'] += $this->en_ignore_rate_cost : '';
                        $this->add_rate($rate);
                    }

                    $en_rates[$accessorial] = $rate;
                }

                // Custom work get from old programming.
                $this->smallInluded = true;
                add_filter('decide_rm_third_party_quotes', array($this, 'decideRmThirdParty'), 99, 3);

                // Origin terminal address
                if ($en_is_shipment == 'en_single_shipment' || (count($pricing_product_origins) == 1)) {
                    (isset($this->InstorPickupLocalDelivery->localDelivery) && ($this->InstorPickupLocalDelivery->localDelivery->status == 1)) ? $this->local_delivery($this->web_service_inst->en_wd_origin_array['fee_local_delivery'], $this->web_service_inst->en_wd_origin_array['checkout_desc_local_delivery'], $this->web_service_inst->en_wd_origin_array) : "";
                    (isset($this->InstorPickupLocalDelivery->inStorePickup) && ($this->InstorPickupLocalDelivery->inStorePickup->status == 1)) ? $this->pickup_delivery($this->web_service_inst->en_wd_origin_array['checkout_desc_store_pickup'], $this->web_service_inst->en_wd_origin_array, $this->InstorPickupLocalDelivery->totalDistance) : "";
                }

                return $en_rates;
            }

            function get_settings_fields($selected_services)
            {
                $this->quote_settings = [];
                $this->quote_settings['hazardous_materials_shipments'] = get_option('only_quote_ground_service_for_hazardous_materials_shipments');
                $this->quote_settings['ground_hazardous_material_fee'] = get_option('ground_hazardous_material_fee');
                $this->quote_settings['air_hazardous_material_fee'] = get_option('air_hazardous_material_fee');
                $this->quote_settings['dont_sort'] = get_option('shipping_methods_do_not_sort_by_price');
                $this->quote_settings['handling_fee'] = get_option('wc_settings_hand_free_mark_up_wwe_small_packages');
                $this->quote_settings['services'] = [
                    'all' => $selected_services
                ];
            }

            /**
             * Get Calculate service level markup
             * @param $total_charge
             * @param $international_markup
             */
            function calculate_service_level_markup($total_charge, $international_markup)
            {
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
                        $grandTotal += $total_charge + $international_markup;
                    }
                } else {
                    $grandTotal += $total_charge;
                }
                return $grandTotal;
            }

            /**
             * Pickup delivery quote
             * @return array type
             */
            function pickup_delivery($label, $en_wd_origin_array, $total_distance)
            {
                $this->woocommerce_package_rates = 1;
                $this->instore_pickup_and_local_delivery = TRUE;

                $label = (isset($label) && (strlen($label) > 0)) ? $label : 'In-store pick up';
                // Origin terminal address
                $address = (isset($en_wd_origin_array['address'])) ? $en_wd_origin_array['address'] : '';
                $city = (isset($en_wd_origin_array['city'])) ? $en_wd_origin_array['city'] : '';
                $state = (isset($en_wd_origin_array['state'])) ? $en_wd_origin_array['state'] : '';
                $zip = (isset($en_wd_origin_array['zip'])) ? $en_wd_origin_array['zip'] : '';
                $phone_instore = (isset($en_wd_origin_array['phone_instore'])) ? $en_wd_origin_array['phone_instore'] : '';
                strlen($total_distance) > 0 ? $label .= ' | ' . str_replace("mi", "miles", $total_distance) . ' away' : '';
                strlen($address) > 0 ? $label .= ' | ' . $address : '';
                strlen($city) > 0 ? $label .= ', ' . $city : '';
                strlen($state) > 0 ? $label .= ' ' . $state : '';
                strlen($zip) > 0 ? $label .= ' ' . $zip : '';
                strlen($phone_instore) > 0 ? $label .= ' | ' . $phone_instore : '';

                $pickup_delivery = array(
                    'id' => 'speedship:' . 'in-store-pick-up',
                    'cost' => !empty($en_wd_origin_array['fee_store_pickup']) ? $en_wd_origin_array['fee_store_pickup'] : 0,
                    'label' => $label,
                    'plugin_name' => 'WWE SmPkg',
                    'plugin_type' => 'small',
                    'owned_by' => 'eniture'
                );

                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                $this->add_rate($pickup_delivery);
            }

            /**
             * Local delivery quote
             * @param string type $cost
             * @return array type
             */
            function local_delivery($cost, $label, $en_wd_origin_array)
            {
                $this->woocommerce_package_rates = 1;
                $this->instore_pickup_and_local_delivery = TRUE;
                $label = (isset($label) && (strlen($label) > 0)) ? $label : 'Local Delivery';
                $local_delivery = array(
                    'id' => 'speedship:' . 'local-delivery',
                    'cost' => !empty($cost) ? $cost : 0,
                    'label' => $label,
                    'plugin_name' => 'WWE SmPkg',
                    'plugin_type' => 'small',
                    'owned_by' => 'eniture'
                );

                add_filter('woocommerce_package_rates', array($this, 'en_sort_woocommerce_available_shipping_methods'), 10, 2);
                $this->add_rate($local_delivery);
            }

            /**
             * final rates sorting
             * @param array type $rates
             * @param array type $package
             * @return array type
             */
            function en_sort_woocommerce_available_shipping_methods($rates, $package)
            {
                // if there are no rates don't do anything
                if (!$rates) {
                    return [];
                }

                // Check the option to sort shipping methods by price on quote settings
                if (get_option('shipping_methods_do_not_sort_by_price') != 'yes') {
                    // Get an array of prices
                    $prices = [];
                    foreach ($rates as $rate) {
                        $prices[] = $rate->cost;
                    }

                    // Use the prices to sort the rates
                    array_multisort($prices, $rates);
                }
                // Return the rates
                return $rates;
            }

            /**
             * Set residential accessorial access in multi-shipment.
             * @param Array $rates
             * @param object $group_small_shipments
             */
            public function en_set_multiship_residential_del($rates, $group_small_shipments)
            {
                $access_arr = [];
                if (!function_exists('array_column')) {
                    $access_arr = $this->helper_obj->array_column($rates, 'label_sufex');
                } else {
                    $access_arr = array_column($rates, 'label_sufex');
                }
                /* Assign Auto-residential/liftgate to order details */
                foreach ($rates as $key => $value) {
                    $residential_del = get_option('wc_settings_quest_as_residential_delivery_wwe_small_packages');
                    if (
                        isset($value['label_sufex']) &&
                        count($value['label_sufex']) > 0 &&
                        in_array('R', $value['label_sufex']) && $residential_del != 'yes'
                    ) {
                        $group_small_shipments->order_details['accessorials']['R'] = 'R';
                    }
                }
            }

            /**
             * Function to update the filter data and session with order details.
             * @param object $group_small_shipments
             */
            public function en_order_details_hooks_process($group_small_shipments)
            {
                $order_details = [];
                $this->order_detail = $group_small_shipments->order_details;
                /* Filter the data of order details */
                add_filter('en_fitler_order_data', array($this, 'en_update_order_data'));
                /* Passing empty array because data is updated using class property */
                $session_order_details = apply_filters(
                    'en_fitler_order_data', []
                );

                /* Set the session */
                WC()->session->set('en_order_detail', $session_order_details);
            }

            /**
             * Filter function to update order details.
             * @param array $data
             * @return type
             */
            public function en_update_order_data($data)
            {

                $data['en_shipping_details']['en_wwe_small'] = $this->order_detail;
                return $data;
            }

            /**
             * Set the cheapest prices.
             * @param array $rates
             */
            public function en_set_cheapest_prcs($rates, $group_small_shipments)
            {
                foreach ($rates as $key => $value) {
                    foreach ($rates[$key]['minPrices'] as $zip => $val) {
                        $group_small_shipments->order_details['details'][$zip]['cheapest_services'][$val['code']] = $val;
                    }
                }
            }

            /**
             * Check the status of Show no other plugins option.
             * @param string $rmStatus
             * @param array $rmThirdPartyArr
             * @param array $available_methods
             * @return boolean
             */
            function decideRmThirdParty($rmStatus, $rmThirdPartyArr, $available_methods)
            {

                if (!isset($rmThirdPartyArr['wc_settings_wwe_small_allow_other_plugins'])) {
                    return $rmStatus;
                }
                return (($rmThirdPartyArr['wc_settings_wwe_small_allow_other_plugins'] == 'no') && ($this->smallInluded || $rmThirdPartyArr['shipment_id'] == 'speedship')) ? true : false;
            }

            /**
             * Check is free shipping or not
             * @param $coupon
             * @return string
             */
            function wweSmpkgFreeShipping($coupon)
            {
                foreach ($coupon as $key => $value) {
                    if ($value->get_free_shipping() == 1) {
                        $free = array(
                            'id' => 'free',
                            'label' => 'Free Shipping',
                            'cost' => 0,
                            'plugin_name' => 'WWE SmPkg',
                            'plugin_type' => 'small',
                            'owned_by' => 'eniture'
                        );
                        $this->add_rate($free);
                        return 'y';
                    }
                }
            }

            /**
             * Create plugin option
             */
            function create_speedship_small_option()
            {
                $eniture_plugins = get_option('EN_Plugins');
                if (!$eniture_plugins) {
                    add_option('EN_Plugins', json_encode(array('speedship')));
                } else {
                    $plugins_array = json_decode($eniture_plugins, true);
                    if (!in_array('speedship', $plugins_array)) {
                        array_push($plugins_array, 'speedship');
                        update_option('EN_Plugins', json_encode($plugins_array));
                    }
                }
            }

            /**
             * Get Active Service Options
             */
            function wwe_smpkg_get_active_services()
            {
                $selected_quotes_service_options_array = [];
                if (get_option('wc_settings_Service_UPS_Next_Day_Early_AM_small_packages_quotes') == 'yes') {
                    $selected_quotes_service_options_array['1DM'] = ['name' => '1DM', 'markup' => get_option('wwesmall_Service_UPS_Next_Day_Early_AM_small_packages_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_Next_Day_Air_small_packages_quotes') == 'yes') {
                    $selected_quotes_service_options_array['1DA'] = ['name' => '1DA', 'markup' => get_option('wwesmall_Service_UPS_Next_Day_Air_small_packages_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_Next_Day_Air_Saver_small_packages_quotes') == 'yes') {
                    $selected_quotes_service_options_array['1DP'] = ['name' => '1DP', 'markup' => get_option('wwesmall_Service_UPS_Next_Day_Air_Saver_small_packages_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_2nd_Day_AM_quotes') == 'yes') {
                    $selected_quotes_service_options_array['2DM'] = ['name' => '2DM', 'markup' => get_option('wwesmall_Service_UPS_2nd_Day_AM_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_2nd_Day_PM_quotes') == 'yes') {
                    $selected_quotes_service_options_array['2DA'] = ['name' => '2DA', 'markup' => get_option('wwesmall_Service_UPS_2nd_Day_PM_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_2nd_Day_Saturday_quotes') == 'yes') {
                    $selected_quotes_service_options_array['2DAS'] = ['name' => '2DAS', 'markup' => get_option('wwesmall_Service_UPS_2nd_Day_Saturday_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_3rd_Day_quotes') == 'yes') {
                    $selected_quotes_service_options_array['3DS'] = ['name' => '3DS', 'markup' => get_option('wwesmall_Service_UPS_3rd_Day_quotes_markup')];
                }
                if (get_option('wc_settings_Service_UPS_Ground_quotes') == 'yes') {
                    $selected_quotes_service_options_array['GND'] = ['name' => 'GND', 'markup' => get_option('wwesmall_Service_UPS_Ground_quotes_markup')];
                }

                // International Services
                if (get_option('wwe_small_pkg_Worldwide_Express') == 'yes') {
                    $selected_quotes_service_options_array['01'] = ['name' => '01', 'markup' => get_option('wwe_small_worldwide_express_markup')];
                }
                if (get_option('wwe_small_worldwide_saver') == 'yes') {
                    $selected_quotes_service_options_array['28'] = ['name' => '28', 'markup' => get_option('wwe_small_worldwide_saver_markup')];
                }
                if (get_option('wwe_small_worldwide_express_plus') == 'yes') {
                    $selected_quotes_service_options_array['21'] = ['name' => '21', 'markup' => get_option('wwe_small_worldwide_express_plus_markup')];
                }
                if (get_option('wwe_small_worldwide_expedited') == 'yes') {
                    $selected_quotes_service_options_array['05'] = ['name' => '05', 'markup' => get_option('wwe_small_pkg_Worldwide_Expedited_markup')];
                }
                if (get_option('wwe_small_pkg_standard') == 'yes') {
                    $selected_quotes_service_options_array['03'] = ['name' => '03', 'markup' => get_option('wwe_small_standard_markup')];
                }

                return $selected_quotes_service_options_array;
            }

            /**
             *
             * @param type $origin_data
             * Update warehouse/dropship with correct for WWE
             * @global type $wpdb
             */
            function wwe_small_update_origin_data($origin_data)
            {
                global $wpdb;
                $data = array('wwe_correct_city' => $origin_data->validCity);
                $clause_array = array(
                    'zip' => $origin_data->currentZip,
                    'city' => $origin_data->currentCity,
                    'state' => $origin_data->currentState
                );
                $update_qry = $wpdb->update(
                    $wpdb->prefix . 'warehouse', $data, $clause_array
                );
            }

            /**
            * Adds backup rates in the shipping rates
            * @return void
            * */
            function wwe_small_backup_rates()
            {
                if (get_option('enable_backup_rates_wwe_small') != 'yes' || (get_option('backup_rates_carrier_fails_to_return_response_wwe_small') != 'yes' && get_option('backup_rates_carrier_returns_error_wwe_small') != 'yes')) return;

                $backup_rates_type = get_option('backup_rates_category_wwe_small');
                $backup_rates_cost = 0;

                if ($backup_rates_type == 'fixed_rate' && !empty(get_option('backup_rates_fixed_rate_wwe_small'))) {
                    $backup_rates_cost = get_option('backup_rates_fixed_rate_wwe_small');
                } elseif ($backup_rates_type == 'percentage_of_cart_price' && !empty(get_option('backup_rates_cart_price_percentage_wwe_small'))) {
                    $cart_price_percentage = floatval(str_replace('%', '', get_option('backup_rates_cart_price_percentage_wwe_small')));
                    $backup_rates_cost = ($cart_price_percentage * WC()->cart->get_subtotal()) / 100;
                } elseif ($backup_rates_type == 'function_of_weight' && !empty(get_option('backup_rates_weight_function_wwe_small'))) {
                    $cart_weight = wc_get_weight(WC()->cart->get_cart_contents_weight(), 'lbs');
                    $backup_rates_cost = get_option('backup_rates_weight_function_wwe_small') * $cart_weight;
                }

                if ($backup_rates_cost > 0) {
                    $backup_rates = array(
                        'id' => $this->id . ':' . 'backup_rates',
                        'label' => get_option('backup_rates_label_wwe_small'),
                        'cost' => $backup_rates_cost,
                        'plugin_name' => 'WWE SmPkg',
                        'plugin_type' => 'small',
                        'owned_by' => 'eniture'
                    );

                    $this->add_rate($backup_rates);
                }
            }

        }

    }
}
