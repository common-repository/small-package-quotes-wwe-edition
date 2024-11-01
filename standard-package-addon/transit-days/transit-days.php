<?php

/**
 * transit days
 */
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists("EnWweSmallTransitDays")) {

    class EnWweSmallTransitDays
    {

        public function __construct()
        {
        }

        /**
         *
         * @param array type $result
         * @return json_encode type
         */
        public function wwe_small_enable_disable_ups_ground($result)
        {
            $transit_day_type = get_option('restrict_calendar_transit_wwe_small_packages');
            $response = (isset($result->q)) ? $result->q : [];
            $days_to_restrict = get_option('ground_transit_wwe_small_packages');

            $package = apply_filters('wwe_small_packages_quotes_quotes_plans_suscription_and_features', 'transit_days');
            if (!is_array($package) && strlen($days_to_restrict) > 0 && strlen($transit_day_type) > 0) {
                foreach ($response as $row => $service) {
                    // old api service code check
                    $service_code = isset($service->serviceCode) ? $service->serviceCode : '';

                    // Check for new API response
                    if (isset($service->timeInTransit) && !empty($service->timeInTransit)) {
                        $service->TransitTimeInDays = isset($service->timeInTransit->transitDays) ? $service->timeInTransit->transitDays : '';
                        $service->CalenderDaysInTransit = isset($service->timeInTransit->CalenderDaysInTransit) ? $service->timeInTransit->CalenderDaysInTransit : '';
                        $service_code = isset($service->timeInTransit->upsServiceCode) ? $service->timeInTransit->upsServiceCode : $service_code;
                    }
                    
                    if ($service_code == "GND" &&
                        (isset($service->$transit_day_type)) &&
                        ($service->$transit_day_type > $days_to_restrict))

                        unset($result->q[$row]);
                }

            }

            return json_encode($result);
        }
    }
}
        

