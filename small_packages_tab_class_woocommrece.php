<?php

/**
 * WWE Small Tab Class
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Woo-commerce Setting Tab Class
 */
class WC_Settings_Small_Packages extends WC_Settings_Page
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = 'wwe_small_packages_quotes';
        add_filter('woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50);
        add_action('woocommerce_sections_' . $this->id, array($this, 'output_sections'));
        add_action('woocommerce_settings_' . $this->id, array($this, 'output'));
        add_action('woocommerce_settings_save_' . $this->id, array($this, 'save'));
    }

    /**
     * Setting Tab
     * @param array $settings_tabs
     */
    public function add_settings_tab($settings_tabs)
    {
        $settings_tabs[$this->id] = __('Speedship', 'woocommerce-settings-wwe_small_packages_quotes');
        return $settings_tabs;
    }

    /**
     * Sections
     */
    public function get_sections()
    {
        $sections = array(
            '' => __('Connection Settings', 'woocommerce-settings-wwe_small_packages_quotes'),
            'section-1' => __('Quote Settings', 'woocommerce-settings-wwe_small_packages_quotes'),
            'section-2' => __('Warehouses', 'woocommerce-settings-wwe_small_packages_quotes'),
            'shipping-rules' => __('Shipping Rules', 'woocommerce-settings-wwe_small_packages_quotes'),
            'section-4' => __('FreightDesk Online', 'woocommerce-settings-wwe_small_packages_quotes'),
            'section-5' => __('Validate Addresses', 'woocommerce-settings-wwe_small_packages_quotes'),
            'section-6' => __('Compare Rates', 'woocommerce-settings-wwe_small_packages_quotes'),
            'section-3' => __('User Guide', 'woocommerce-settings-wwe_small_packages_quotes'),
        );

        // Logs data
        $enable_logs = get_option('en_wwe_spq_enable_logs');
        if ($enable_logs == 'yes') {
            $sections['en-logs'] = 'Logs';
        }
        $sections = apply_filters('en_woo_addons_sections', $sections, en_woo_plugin_wwe_small_packages_quotes);
        return apply_filters('woocommerce_get_sections_' . $this->id, $sections);
    }

    /**
     * Warehouse Portion
     */
    public function sm_warehouse()
    {
        require_once 'warehouse-dropship/wild/warehouse/wwe_small_warehouse_template.php';
        require_once 'warehouse-dropship/wild/dropship/wwe_small_dropship_template.php';
    }

    /**
     * User Guide
     */
    public function sm_user_guide()
    {
        include_once('template/guide.php');
    }

    /**
     * Conn Settings
     * @return array
     */
    public function speeship_con_setting()
    {
        echo '<div class="connection_section_class" id="wwesmpkg-conn-section">';
        $default_api_endpoint = !empty(get_option('wc_settings_username_wwe_small_packages_quotes')) ? 'wwe_small_old_api' : 'wwe_small_new_api';

        $settings = array(
            'section_title_wwe_small_packages' => array(
                'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'title',
                'desc' => '<br> ',
                'id' => 'wc_settings_wwe_small_packages_title_section_connection',
            ),
            'api_endpoint_wwe_small_packages' => array(
                'name' => __('Which API will you connect to? ', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'select',
                'default' => $default_api_endpoint,
                'id' => 'api_endpoint_wwe_small_packages',
                'options' => array(
                    'wwe_small_old_api' => __('Legacy API', 'Legacy API'),
                    'wwe_small_new_api' => __('New API', 'New API'),
                )
            ),
            // New API
            'wwe_small_client_id' => array(
                'name' => __('Client ID ', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wwe_small_client_id',
                'class' => 'wwe_small_new_api_field'
            ),

            'wwe_small_client_secret' => array(
                'name' => __('Client Secret ', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wwe_small_client_secret',
                'class' => 'wwe_small_new_api_field'
            ),
            'wwe_small_new_api_username' => array(
                'name' => __('Username ', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wwe_small_new_api_username',
                'class' => 'wwe_small_new_api_field'
            ),
            'wwe_small_new_api_password' => array(
                'name' => __('Password ', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wwe_small_new_api_password',
                'class' => 'wwe_small_new_api_field'
            ),
            // Old API
            'account_number_wwe_small_packages_quotes' => array(
                'name' => __('Worldwide Express Account Number ', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wc_settings_account_number_wwe_small_packages_quotes',
                'class' => 'wwe_small_old_api_field'
            ),
            'username_wwe_small_packages_quotes' => array(
                'name' => __('Speedship Username ', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wc_settings_username_wwe_small_packages_quotes',
                'class' => 'wwe_small_old_api_field'
            ),
            'password_wwe_small_packages' => array(
                'name' => __('Speedship Password ', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wc_settings_password_wwe_small_packages',
                'class' => 'wwe_small_old_api_field'
            ),
            'authentication_key_wwe_small_packages_quotes' => array(
                'name' => __('Authentication Key ', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wc_settings_authentication_key_wwe_small_packages_quotes',
                'class' => 'wwe_small_old_api_field'
            ),
            'plugin_licence_key_wwe_small_packages_quotes' => array(
                'name' => __('Eniture API Key ', 'woocommerce-settings-wwe_small_packages_quotes'),
                'type' => 'text',
                'desc' => __('Obtain a Eniture API Key from <a href="https://eniture.com/woocommerce-worldwide-express-small-package-plugin/" target="_blank" >eniture.com </a>', 'woocommerce-settings-wwe_small_packages_quotes'),
                'id' => 'wc_settings_plugin_licence_key_wwe_small_packages_quotes'
            ),
            'section_end_wwe_small_packages' => array(
                'type' => 'sectionend',
                'id' => 'wc_settings_plugin_licence_key_wwe_small_packages_quotes'
            ),
        );
        return $settings;
    }

    /**
     * Settings
     * @param $section
     */
    public function get_settings($section = null)
    {
        ob_start();
        switch ($section) {

            case 'en-logs' :
                require_once 'logs/en-logs.php';
                $settings = [];
                break;

            case 'section-0' :
                $settings = $this->speeship_con_setting();
                break;

            case 'section-1':

                $disable_transit = "";
                $transit_package_required = "";

                $disable_hazardous = "";
                $hazardous_package_required = "";

                // Error management
                if (empty(get_option('error_management_settings_wwe_small_packages'))) {
                    update_option('error_management_settings_wwe_small_packages', 'quote_shipping');
                }

                // Backup rates
                if (empty(get_option('backup_rates_category_wwe_small'))) {
                    update_option('backup_rates_category_wwe_small', 'fixed_rate');
                }

                if (empty(get_option('backup_rates_display_wwe_small'))) {
                    update_option('backup_rates_display_wwe_small', 'no_other_rates');
                }

                $action_transit = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'transit_days');
                if (is_array($action_transit)) {
                    $disable_transit = "disabled_me";
                    $transit_package_required = apply_filters('wwe_small_packages_quotes_plans_notification_link', $action_transit);
                }

                $action_hazardous = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'hazardous_material');
                if (is_array($action_hazardous)) {
                    $disable_hazardous = "disabled_me";
                    $hazardous_package_required = apply_filters('wwe_small_packages_quotes_plans_notification_link', $action_hazardous);
                }

                //**Plan_Validation: Cut Off Time & Ship Date Offset
                $disable_cot_sdo = "";
                $wwe_small_cutOffTime_shipDateOffset_package_required = "";
                $action_wwe_small_cutOffTime_shipDateOffset = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'wwe_small_cutOffTime_shipDateOffset');
                if (is_array($action_wwe_small_cutOffTime_shipDateOffset)) {
                    $disable_cot_sdo = "disabled_me";
                    $wwe_small_cutOffTime_shipDateOffset_package_required = apply_filters('wwe_small_packages_quotes_plans_notification_link', $action_wwe_small_cutOffTime_shipDateOffset);
                }

                $package_type_options = [
                    'ship_alone' => __('Quote each item as shipping as its own package', 'woocommerce-settings-wwe_small_packages_quotes'),
                    'ship_combine_and_alone' => __('Combine the weight of all items without dimensions and quote them as one package while quoting each item with dimensions as shipping as its own package', 'woocommerce-settings-wwe_small_packages_quotes'),
                    'ship_one_package_70' => __('Quote shipping as if all items ship as one package up to 70 LB each', 'woocommerce-settings-wwe_small_packages_quotes'),
                    'ship_one_package_150' => __('Quote shipping as if all items ship as one package up to 150 LB each', 'woocommerce-settings-wwe_small_packages_quotes'),
                ];
                $package_type_default = 'ship_alone';
                $wwe_small_packaging_type = get_option("wwe_small_packaging_type");
                if(!empty($wwe_small_packaging_type) && $wwe_small_packaging_type == 'old'){
                    $package_type_default = 'eniture_packaging';
                    $package_type_options['eniture_packaging'] = __('Use the default Eniture packaging algorithm', 'woocommerce-settings-wwe_small_packages_quotes');
                }

                //**End: Cut Off Time & Ship Date Offset

                echo '<div class="custom_box_message" style="display: none">Markup Field: (Will accept Dollars and Percentages) You can markup the rates for the individual services. Markup can be either a flat Dollar Amount i.e. If you want the rate quoted to the customer to be $5 higher than the rate you will pay carrier, then enter into the field 5.00. On the other hand, if you would like charges to be enhanced by 5% of what a carrier is charging you. Then enter 5.00% into the field.</div>';
                echo '<div class="quote_section_class_smpkg">';
                $settings = array(
                    'Services_quoted_wwe_small_packages' => array(
                        'title' => __('', 'woocommerce'),
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'desc' => '',
                        'id' => 'woocommerce_Services_quoted_wwe_small_packages',
                        'css' => '',
                        'default' => '',
                        'type' => 'title',
                    ),
                    'Sevice_wwe_small_packages' => array(
                        'name' => __('Quote Service Options ', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'title',
                        'desc' => '',
                        'id' => 'wc_settings_Sevice_wwe_small_packages'
                    ),
                    'wwe_small_domastic_srvcs' => array(
                        'name' => __('US Domestic Services', 'woocommerce-settings-ups-small-quotes'),
                        'type' => 'checkbox',
                        'id' => 'wwe_small_dom_srvc_hdng',
                        'class' => 'wwe_small_services_hdng quotes_services'
                    ),
                    'wwe_small_int_srvcs' => array(
                        'name' => __('International Services', 'woocommerce-settings-ups-small-quotes'),
                        'type' => 'checkbox',
                        'id' => 'wwe_small_int_srvc_hdng',
                        'class' => 'wwe_small_services_hdng wwe_small_int_quotes_services'
                    ),
                    'select_smpkg_services' => array(
                        'name' => __('Select All', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'id' => 'wc_settings_select_all_ampkg_services',
                        'class' => 'sm_all_services quotes_services',
                    ),
                    'wwe_small_select_all_int_services' => array(
                        'name' => __('Select All', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'id' => 'wwe_small_select_all_int_services',
                        'class' => 'wwe_small_int_quotes_services',
                    ),
                    'Service_UPS_Ground_quotes' => array(
                        'name' => __('UPS Ground', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_Ground_quotes',
                        'class' => 'quotes_services',
                    ),
                    'wwe_small_pkg_Worldwide_Express' => array(
                        'name' => __('UPS Worldwide Express', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wwe_small_pkg_Worldwide_Express',
                        'class' => 'wwe_small_int_quotes_services wwe_international_service',
                    ),
                    'Service_UPS_Ground_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_Ground_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_Ground_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_Ground_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'wwe_small_worldwide_express_markup' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wwe_small_worldwide_express_markup',
                        'class' => 'wwe_small_quotes_markup_right_markup',
                    ),
                    'Service_UPS_3rd_Day_quotes' => array(
                        'name' => __('UPS 3 Day Select', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_3rd_Day_quotes',
                        'class' => 'quotes_services',
                    ),
                    'wwe_small_worldwide_saver' => array(
                        'name' => __('UPS Worldwide Saver', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wwe_small_worldwide_saver',
                        'class' => 'wwe_small_int_quotes_services wwe_international_service',
                    ),
                    'Service_UPS_3rd_Day_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_3rd_Day_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_3rd_Day_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_3rd_Day_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'wwe_small_worldwide_saver_markup' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wwe_small_worldwide_saver_markup',
                        'class' => 'wwe_small_quotes_markup_right_markup',
                    ),
                    'Service_UPS_2nd_Day_Saturday_quotes' => array(
                        'name' => __('UPS 2nd Day Air (Saturday Delivery)', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_2nd_Day_Saturday_quotes',
                        'class' => 'quotes_services',
                    ),
                    'wwe_small_worldwide_expedited' => array(
                        'name' => __('UPS Worldwide Expedited', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wwe_small_worldwide_expedited',
                        'class' => 'wwe_small_int_quotes_services wwe_international_service',
                    ),
                    'Service_UPS_2nd_Day_Saturday_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_2nd_Day_Saturday_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_2nd_Day_Saturday_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_2nd_Day_Saturday_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'wwe_small_pkg_Worldwide_Expedited_markup' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wwe_small_pkg_Worldwide_Expedited_markup',
                        'class' => 'wwe_small_quotes_markup_right_markup',
                    ),
                    'Service_UPS_2nd_Day_PM_quotes' => array(
                        'name' => __('UPS 2nd Day Air', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_2nd_Day_PM_quotes',
                        'class' => 'quotes_services',
                    ),
                    'wwe_small_pkg_standard' => array(
                        'name' => __('UPS Standard', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wwe_small_pkg_standard',
                        'class' => 'wwe_small_int_quotes_services wwe_international_service',
                    ),
                    'Service_UPS_2nd_Day_PM_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_2nd_Day_PM_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_2nd_Day_PM_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_2nd_Day_PM_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'wwe_small_standard_markup' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wwe_small_standard_markup',
                        'class' => 'wwe_small_quotes_markup_right_markup',
                    ),
                    'Service_UPS_2nd_Day_AM_quotes' => array(
                        'name' => __('UPS 2nd Day Air A.M.', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_2nd_Day_AM_quotes',
                        'class' => 'quotes_services',
                    ),
                    'wwe_small_worldwide_express_plus' => array(
                        'name' => __('UPS Worldwide Express Plus', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wwe_small_worldwide_express_plus',
                        'class' => 'wwe_small_int_quotes_services wwe_international_service',
                    ),
                    'Service_UPS_2nd_Day_AM_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_2nd_Day_AM_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_2nd_Day_AM_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_2nd_Day_AM_quotes_markup',
                        'class' => 'wwe_small_markup',
                    ),
                    'wwe_small_worldwide_express_plus_markup' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%)', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wwe_small_worldwide_express_plus_markup',
                        'class' => 'wwe_small_quotes_markup_right_markup',
                    ),
                    'Service_UPS_Next_Day_Air_Saver_small_packages_quotes' => array(
                        'name' => __('UPS Next Day Air Saver', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_Next_Day_Air_Saver_small_packages_quotes',
                        'class' => 'quotes_services remove_flex_display',
                    ),
                    'Service_UPS_Next_Day_Air_Saver_small_packages_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_Next_Day_Air_Saver_small_packages_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_Next_Day_Air_Saver_small_packages_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_Next_Day_Air_Saver_small_packages_quotes_markup',
                        'class' => 'wwe_small_markup remove_flex_display',
                    ),
                    'Service_UPS_Next_Day_Air_small_packages_quotes' => array(
                        'name' => __('UPS Next Day Air', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_Next_Day_Air_small_packages_quotes',
                        'class' => 'quotes_services remove_flex_display',
                    ),
                    'Service_UPS_Next_Day_Air_small_packages_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_Next_Day_Air_small_packages_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_Next_Day_Air_small_packages_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_Next_Day_Air_small_packages_quotes_markup',
                        'class' => 'wwe_small_markup remove_flex_display',
                    ),
                    'Service_UPS_Next_Day_Early_AM_small_packages_quotes_tab_class' => array(
                        'name' => __('UPS Next Day Air Early', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_Service_UPS_Next_Day_Early_AM_small_packages_quotes',
                        'class' => 'quotes_services remove_flex_display',
                    ),
                    'Service_UPS_Next_Day_Early_AM_small_packages_quotes_markup' => array(
                        'name' => __('', 'wwesmall_Service_UPS_Next_Day_Early_AM_small_packages_quotes_markup'),
                        'type' => 'text',
                        'placeholder' => 'Markup',
                        'desc' => __('Markup (e.g. Currency: 1.00 or Percentage: 5.0%).', 'wwesmall_Service_UPS_Next_Day_Early_AM_small_packages_quotes_markup'),
                        'id' => 'wwesmall_Service_UPS_Next_Day_Early_AM_small_packages_quotes_markup',
                        'class' => 'wwe_small_markup remove_flex_display',
                    ),
                    'wwe_small_sort_wwe_small' => array(
                        'name' => __("Don't sort shipping methods by price  ", 'woocommerce-settings-wwe_small_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'By default, the plugin will sort all shipping methods by price in ascending order.',
                        'id' => 'shipping_methods_do_not_sort_by_price'
                    ),

                    // Package rating method when Standard Box Sizes isn't in use
                    'wwe_small_packaging_method_label' => array(
                        'name' => __('Package rating method when Standard Box Sizes isn\'t in use', 'woocommerce-settings-wwe_small_quotes'),
                        'type' => 'text',
                        'id' => 'wwe_small_packaging_method_label'
                    ),
                    'wwe_small_packaging_method' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_quotes'),
                        'type' => 'radio',
                        'default' => $package_type_default,
                        'options' => $package_type_options,
                        'id' => 'wwe_small_packaging_method',
                    ),

                    // show delivery estimates options
                    'service_wwe_small_estimates_title' => array(
                        'name' => __('Delivery Estimate Options ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => '',
                        'id' => 'service_wwe_small_estimates_title'
                    ),
                    'dont_show_estimates_wwe_small' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_quotes'),
                        'type' => 'radio',
                        'class' => "",
                        'default' => "dont_show_estimates",
                        'options' => array(
                            'dont_show_estimates' => __("Don't display delivery estimates.", 'woocommerce'),
                            'delivery_days' => __('Display estimated number of days until delivery.', 'woocommerce'),
                            'delivery_date' => __('Display estimated delivery date.', 'woocommerce'),
                        ),
                        'id' => 'wwe_small_delivery_estimates',
                    ),
                    //**Start: Cut Off Time & Ship Date Offset
                    'wwe_small_cutOffTime_shipDateOffset_wwe_small' => array(
                        'name' => __('Cut Off Time & Ship Date Offset ', 'woocommerce-settings-en_woo_addons_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => $wwe_small_cutOffTime_shipDateOffset_package_required,
                        'id' => 'wwe_small_cutOffTime_shipDateOffset'
                    ),
                    'orderCutoffTime_wwe_small' => array(
                        'name' => __('Order Cut Off Time ', 'woocommerce-settings-wwe_small_freight_orderCutoffTime'),
                        'type' => 'text',
                        'placeholder' => '--:-- --',
                        'desc' => 'Enter the cut off time (e.g. 2.00) for the orders. Orders placed after this time will be quoted as shipping the next business day.',
                        'id' => 'wwe_small_orderCutoffTime',
                        'class' => $disable_cot_sdo,
                    ),
                    'shipmentOffsetDays_wwe_small' => array(
                        'name' => __('Fulfilment Offset Days ', 'woocommerce-settings-wwe_small_shipmentOffsetDays'),
                        'type' => 'text',
                        'desc' => 'The number of days the ship date needs to be moved to allow the processing of the order.',
                        'placeholder' => 'Fulfilment Offset Days, e.g. 2',
                        'id' => 'wwe_small_shipmentOffsetDays',
                        'class' => $disable_cot_sdo,
                    ),
                    'all_shipment_days_wwex_small' => array(
                        'name' => __("What days do you ship orders?", 'woocommerce-settings-ups_small_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Select All',
                        'class' => "all_shipment_days_wwex_small $disable_cot_sdo",
                        'id' => 'all_shipment_days_wwex_small'
                    ),
                    'monday_shipment_day_wwex_small' => array(
                        'name' => __("", 'woocommerce-settings-ups_small_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Monday',
                        'class' => "wwex_small_shipment_day $disable_cot_sdo",
                        'id' => 'monday_shipment_day_wwex_small'
                    ),
                    'tuesday_shipment_day_wwex_small' => array(
                        'name' => __("", 'woocommerce-settings-ups_small_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Tuesday',
                        'class' => "wwex_small_shipment_day $disable_cot_sdo",
                        'id' => 'tuesday_shipment_day_wwex_small'
                    ),
                    'wednesday_shipment_day_wwex_small' => array(
                        'name' => __("", 'woocommerce-settings-ups_small_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Wednesday',
                        'class' => "wwex_small_shipment_day $disable_cot_sdo",
                        'id' => 'wednesday_shipment_day_wwex_small'
                    ),
                    'thursday_shipment_day_wwex_small' => array(
                        'name' => __("", 'woocommerce-settings-ups_small_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Thursday',
                        'class' => "wwex_small_shipment_day $disable_cot_sdo",
                        'id' => 'thursday_shipment_day_wwex_small'
                    ),
                    'friday_shipment_day_wwex_small' => array(
                        'name' => __("", 'woocommerce-settings-ups_small_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Friday',
                        'class' => "wwex_small_shipment_day $disable_cot_sdo",
                        'id' => 'friday_shipment_day_wwex_small'
                    ),
                    // Start Transit days
                    'ground_transit_label' => array(
                        'name' => __('Ground transit time restriction', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => $transit_package_required,
                        'id' => 'ground_transit_label'
                    ),
                    'ground_transit_resident_wwe_small_packages' => array(
                        'name' => __('Enter the number of transit days to restrict ground service to. Leave blank to disable this feature.', 'ground-transit-settings-ground_transit'),
                        'type' => 'text',
                        'class' => $disable_transit,
                        'id' => 'ground_transit_wwe_small_packages'
                    ),
                    'restrict_calendar_transit_wwe_small_packages' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'radio',
                        'class' => "$disable_transit restrict_by_calendar_days_in_transit_1st_option",
                        'options' => array(
                            'TransitTimeInDays' => __('Restrict by the carrier\'s in transit days metric.', 'woocommerce'),
                            'CalenderDaysInTransit' => __('Restrict by the calendar days in transit.', 'woocommerce'),
                        ),
                        'id' => 'restrict_calendar_transit_wwe_small_packages',
                    ),
//                  End Transit days 
                    'Service_UPS_Next_Day_Early_AM_small_packages_quotes' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'title',
                        'class' => 'hidden',
                    ),
                    'residential_delivery_options_label' => array(
                        'name' => __('Residential Delivery', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'id' => 'residential_delivery_options_label'
                    ),
                    'quest_as_residential_delivery_wwe_small_packages' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => __('Always quote as residential delivery.', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_quest_as_residential_delivery_wwe_small_packages'
                    ),
//                  Auto-detect residential addresses notification
                    'avaibility_auto_residential' => array(
                        'name' => __('', 'woocommerce-settings-wwex_small'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/'>here</a> to add the Auto-detect residential addresses module. (<a target='_blank' href='https://eniture.com/woocommerce-residential-address-detection/#documentation'>Learn more</a>)",
                        'id' => 'avaibility_auto_residential'
                    ),
//                  Use my standard box sizes notification
                    'avaibility_box_sizing' => array(
                        'name' => __('Use my standard box sizes', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => "Click <a target='_blank' href='https://eniture.com/woocommerce-standard-box-sizes/'>here</a> to add the Standard Box Sizes module. (<a target='_blank' href='https://eniture.com/woocommerce-standard-box-sizes/#documentation'>Learn more</a>)",
                        'id' => 'avaibility_box_sizing'
                    ),
//                  Start Hazardous Material
                    'hazardous_material_settings' => array(
                        'name' => __('Hazardous material settings', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'desc' => $hazardous_package_required,
                        'id' => 'hazardous_material_settings'
                    ),
                    'only_quote_ground_service_for_hazardous_materials_shipments' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'Only quote ground service for hazardous materials shipments',
                        'class' => $disable_hazardous,
                        'id' => 'only_quote_ground_service_for_hazardous_materials_shipments',
                    ),
                    'ground_hazardous_material_fee' => array(
                        'name' => __('Ground Hazardous Material Fee', 'ground-transit-settings-ground_transit'),
                        'type' => 'text',
                        'desc' => 'Enter an amount, e.g 20. or Leave blank to disable.',
                        'class' => $disable_hazardous,
                        'id' => 'ground_hazardous_material_fee'
                    ),
                    'air_hazardous_material_fee' => array(
                        'name' => __('Air Hazardous Material Fee', 'ground-transit-settings-ground_transit'),
                        'type' => 'text',
                        'desc' => 'Enter an amount, e.g 20. or Leave blank to disable.',
                        'class' => $disable_hazardous,
                        'id' => 'air_hazardous_material_fee'
                    ),
                    // End Hazardous Material
                    'hand_free_mark_up_wwe_small_packages' => array(
                        'name' => __('Handling Fee / Markup ', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'desc' => 'Amount excluding tax. Enter an amount, e.g 3.75, or a percentage, e.g, 5%. Leave blank to disable.',
                        'id' => 'wc_settings_hand_free_mark_up_wwe_small_packages'
                    ),
                    'en_wwe_spq_enable_logs' => [
                        'name' => __("Enable Logs  ", 'woocommerce-settings-fedex_ltl_quotes'),
                        'type' => 'checkbox',
                        'desc' => 'When checked, the Logs page will contain up to 25 of the most recent transactions.',
                        'id' => 'en_wwe_spq_enable_logs'
                    ],
                    // Ignore items with the following Shipping Class(es) By (K)
                    'en_ignore_items_through_freight_classification' => array(
                        'name' => __('Ignore items with the following Shipping Class(es)', 'woocommerce-settings-wwe_quetes'),
                        'type' => 'text',
                        'desc' => "Enter the <a target='_blank' href = '" . get_admin_url() . "admin.php?page=wc-settings&tab=shipping&section=classes'>Shipping Slug</a> you'd like the plugin to ignore. Use commas to separate multiple Shipping Slug.",
                        'id' => 'en_ignore_items_through_freight_classification'
                    ),
                    'allow_other_plugins_wwe_small_packages' => array(
                        'name' => __('Allow other plugins to show quotes ', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'select',
                        'default' => '3',
                        'desc' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'id' => 'wc_settings_wwe_small_allow_other_plugins',
                        'options' => array(
                            'no' => __('NO', 'NO'),
                            'yes' => __('YES', 'YES')
                        )
                    ),
                    // Error Management
                    'error_management_label_wwe_small_packages' => array(
                        'name' => __('Error Management', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'text',
                        'class' => 'hidden',
                        'id' => 'error_management_label_wwe_small_packages'
                    ),
                    'error_management_settings_wwe_small_packages' => array(
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'radio',
                        'class' => "restrict_by_calendar_days_in_transit_1st_option",
                        'default' => 'quote_shipping',
                        'options' => array(
                            'quote_shipping' => __('Quote shipping using known shipping parameters, even if other items are missing shipping parameters.', 'woocommerce'),
                            'dont_quote_shipping' => __('Don\'t quote shipping if one or more items are missing the required shipping parameters.', 'woocommerce'),
                        ),
                        'id' => 'error_management_settings_wwe_small_packages',
                    ),
                    'unable_retrieve_shipping_clear_wwe_small_packages' => array(
                        'title' => __('', 'woocommerce'),
                        'name' => __('', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'desc' => '',
                        'id' => 'woocommerce_unable_retrieve_shipping_clear_wwe_small_packages',
                        'css' => '',
                        'default' => '',
                        'type' => 'title',
                    ),
                    // Backup rates
                    'unable_retrieve_shipping_wwe_small_packages' => array(
                        'name' => __('Checkout options if the plugin fails to return a rate ', 'woocommerce-settings-wwe_small_packages_quotes'),
                        'type' => 'title',
                        'id' => 'wc_settings_unable_retrieve_shipping_wwe_small_packages'
                    ),
                    'enable_backup_rates_wwe_small' => array(
                        'name' => __('', 'woocommerce-settings-odfl-quotes'),
                        'type' => 'checkbox',
                        'desc' => __('Present the user with a backup shipping rate.', 'woocommerce-settings-odfl-quotes'),
                        'id' => 'enable_backup_rates_wwe_small',
                    ),
                    'backup_rates_label_wwe_small' => array(
                        'name' => __('', 'woocommerce-settings-odfl-quotes'),
                        'type' => 'text',
                        'desc' => 'Label for backup shipping rate (Maximum of 50 characters).',
                        'id' => 'backup_rates_label_wwe_small'
                    ),
                    'backup_rates_category_wwe_small' => array(
                        'name' => __('', 'woocommerce-settings-odfl-quotes'),
                        'type' => 'radio',
                        'default' => 'fixed_rate',
                        'options' => array(
                            'fixed_rate' => __('', 'woocommerce'),
                            'percentage_of_cart_price' => __('', 'woocommerce'),
                            'function_of_weight' => __('', 'woocommerce'),
                        ),
                        'id' => 'backup_rates_category_wwe_small',
                    ),
                    'backup_rates_carrier_fails_to_return_response_wwe_small' => array(
                        'name' => __('', 'woocommerce-settings-odfl-quotes'),
                        'type' => 'checkbox',
                        'desc' => __('Display the backup rate if the carrier fails to return a response.', 'woocommerce-settings-odfl-quotes'),
                        'id' => 'backup_rates_carrier_fails_to_return_response_wwe_small',
                    ),
                    'backup_rates_carrier_returns_error_wwe_small' => array(
                        'name' => __('', 'woocommerce-settings-odfl-quotes'),
                        'type' => 'checkbox',
                        'desc' => __('Display the backup rate if the carrier returns an error.', 'woocommerce-settings-odfl-quotes'),
                        'id' => 'backup_rates_carrier_returns_error_wwe_small',
                    ),
                    'backup_rates_display_wwe_small' => array(
                        'name' => __('', 'woocommerce-settings-odfl-quotes'),
                        'type' => 'radio',
                        'default' => 'no_other_rates',
                        'options' => array(
                            'no_plugin_rates' => __('Display the backup rate if the plugin fails to return a rate.', 'woocommerce'),
                            'no_other_rates' => __('Display the backup rate only if no rates, from any shipping method, are presented.', 'woocommerce'),
                        ),
                        'id' => 'backup_rates_display_wwe_small',
                    ),
                    'section_end_quote' => array(
                        'type' => 'sectionend',
                        'id' => 'wc_settings_quote_section_end'
                    )
                );
                break;

            case 'section-2' :
                $this->sm_warehouse();
                $settings = [];
                break;

            case 'shipping-rules' :
                include_once('shipping-rules/shipping-rules-template.php');
                $settings = [];
                break;

            case 'section-3' :
                $this->sm_user_guide();
                $settings = [];
                break;

            case 'section-4' :
                $this->freightdesk_online_section();
                $settings = [];
                break;

            case 'section-5' :
                $this->validate_addresses_section();
                $settings = [];
                break;

            case 'section-6' :
                $this->wwe_small_compare_rates_section();
                $settings = [];
                break;

            default:
                $settings = $this->speeship_con_setting();
                break;
        }
        $settings = apply_filters('en_woo_addons_settings', $settings, $section, en_woo_plugin_wwe_small_packages_quotes);
        $settings = apply_filters('en_woo_pallet_addons_settings', $settings, $section, en_woo_plugin_wwe_small_packages_quotes);
        $settings = $this->avaibility_addon($settings);
        return apply_filters('woocommerce-settings-wwe_small_packages_quotes', $settings, $section);
    }

    function avaibility_addon($settings)
    {
        if (is_plugin_active('residential-address-detection/residential-address-detection.php')) {
            unset($settings['avaibility_auto_residential']);
        }

        if (is_plugin_active('standard-box-sizes/en-standard-box-sizes.php') || is_plugin_active('standard-box-sizes/standard-box-sizes.php')) {
            unset($settings['avaibility_box_sizing']);
        }

        return $settings;
    }

    /**
     * Output
     * @global $current_section
     */
    public function output()
    {

        global $current_section;
        $settings = $this->get_settings($current_section);
        WC_Admin_Settings::output_fields($settings);
    }

    /**
     * Save
     * @global $current_section
     */
    public function save()
    {

        global $current_section;
        $settings = $this->get_settings($current_section);
        if (isset($_POST['wwe_small_orderCutoffTime']) && $_POST['wwe_small_orderCutoffTime'] != '') {
            $time24Formate = $this->getTimeIn24Hours($_POST['wwe_small_orderCutoffTime']);
            $_POST['wwe_small_orderCutoffTime'] = $time24Formate;
        }

        // backup rates
        $backup_rates_fields = ['backup_rates_fixed_rate_wwe_small', 'backup_rates_cart_price_percentage_wwe_small', 'backup_rates_weight_function_wwe_small'];
        foreach ($backup_rates_fields as $field) {
            if (isset($_POST[$field])) update_option($field, $_POST[$field]);
        }

        WC_Admin_Settings::save_fields($settings);
    }

    /**
     * @param $timeStr
     * @return false|string
     */
    public function getTimeIn24Hours($timeStr)
    {
        $cutOffTime = explode(' ', $timeStr);
        $hours = isset($cutOffTime[0]) ? $cutOffTime[0] : '';
        $separator = isset($cutOffTime[1]) ? $cutOffTime[1] : '';
        $minutes = isset($cutOffTime[2]) ? $cutOffTime[2] : '';
        $meridiem = isset($cutOffTime[3]) ? $cutOffTime[3] : '';
        $cutOffTime = "{$hours}{$separator}{$minutes} $meridiem";
        return date("H:i", strtotime($cutOffTime));
    }

    /**
     * FreightDesk Online section
     */
    public function freightdesk_online_section()
    {

        include_once('fdo/freightdesk-online-section.php');
    }

    /**
     * Validate Addresses Section
     */
    public function validate_addresses_section()
    {

        include_once('fdo/validate-addresses-section.php');
    }

    /**
     * Compare Rates Section
     */
    public function wwe_small_compare_rates_section()
    {
        include_once('template/en-wwe-small-compare-rates.php');
    }

}

return new WC_Settings_Small_Packages();
