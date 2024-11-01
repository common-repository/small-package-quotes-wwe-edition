jQuery(document).ready(function () {
    // estimated delivery options
    jQuery(".wwe_small_dont_show_estimate_option").closest('tr').addClass("wwe_small_dont_show_estimate_option_tr");
    jQuery("#service_small_estimates_title").closest('tr').addClass("service_small_estimates_title_tr");
    jQuery("input[name=wwe_small_delivery_estimates]").closest('tr').addClass("wwe_small_delivery_estimates_tr");
    jQuery("#service_wwe_small_estimates_title").closest('tr').addClass("service_wwe_small_estimates_title_tr");
    jQuery(".wwe_small_shipment_day").closest('tr').addClass("wwe_small_shipment_day_tr");
    jQuery("#wwe_small_cutOffTime_shipDateOffset").closest('tr').addClass("wwe_small_cutOffTime_shipDateOffset_required_label");
    jQuery("#wwe_small_orderCutoffTime").closest('tr').addClass("wwe_small_cutOffTime_shipDateOffset");
    jQuery("#wwe_small_shipmentOffsetDays").closest('tr').addClass("wwe_small_cutOffTime_shipDateOffset");
    jQuery("#wwe_small_timeformate").closest('tr').addClass("wwe_small_timeformate");
    // Ship days
    jQuery(".wwex_small_shipment_day").closest('tr').addClass("wwex_small_shipment_day_tr");
    jQuery("#all_shipment_days_wwex_small").closest('tr').addClass("all_shipment_days_wwex_small_tr");

    // International services
    jQuery('.wwe_small_int_quotes_services').closest('tr').addClass('wwe_small_quotes_services_tr');
    jQuery('.wwe_small_int_quotes_services').closest('td').addClass('wwe_small_quotes_services_td');
    jQuery('.wwe_small_quotes_markup_right_markup').closest('tr').addClass('wwe_small_quotes_right_markup');
    jQuery('.remove_flex_display').closest('tr').addClass('remove_flex_display');
    jQuery('.wwe_small_services_hdng').closest('tr').addClass('wwe_small_services_hdng');

    const sm_int_all_checkboxes = jQuery('.wwe_international_service');
    if (sm_int_all_checkboxes.length === sm_int_all_checkboxes.filter(":checked").length) {
        jQuery('#wwe_small_select_all_int_services').prop('checked', true);
    }

    jQuery("#wwe_small_select_all_int_services").change(function () {
        if (this.checked) {
            jQuery(".wwe_international_service").each(function () {
                this.checked = true;
            })
        } else {
            jQuery(".wwe_international_service").each(function () {
                this.checked = false;
            })
        }
    });
    
    jQuery(".wwe_international_service").on('change load', function () {        
        const checkboxes = jQuery('.wwe_international_service:checked').length;
        const un_checkboxes = jQuery('.wwe_international_service').length;
        
        if (checkboxes === un_checkboxes) {
            jQuery('#wwe_small_select_all_int_services').prop('checked', true)
        } else {
            jQuery('#wwe_small_select_all_int_services').prop('checked', false);
        }
    });

    if (typeof wwe_connection_section_api_endpoint == 'function') {
        wwe_connection_section_api_endpoint();
    }

    jQuery('#api_endpoint_wwe_small_packages').on('change', function () {
        wwe_connection_section_api_endpoint();
    });

    /*
     * Uncheck Week days Select All Checkbox
     */
    jQuery(".wwex_small_shipment_day").on('change load', function () {
        var checkboxes = jQuery('.wwex_small_shipment_day:checked').length;
        var un_checkboxes = jQuery('.wwex_small_shipment_day').length;
        if (checkboxes === un_checkboxes) {
            jQuery('.all_shipment_days_wwex_small').prop('checked', true);
        } else {
            jQuery('.all_shipment_days_wwex_small').prop('checked', false);
        }
    });

    /*
     * Select All Shipment Week days
     */
    var all_int_checkboxes = jQuery('.all_shipment_days_wwex_small');
    if (all_int_checkboxes.length === all_int_checkboxes.filter(":checked").length) {
        jQuery('.all_shipment_days_wwex_small').prop('checked', true);
    }

    jQuery(".all_shipment_days_wwex_small").change(function () {
        if (this.checked) {
            jQuery(".wwex_small_shipment_day").each(function () {
                this.checked = true;
            });
        } else {
            jQuery(".wwex_small_shipment_day").each(function () {
                this.checked = false;
            });
        }
    });

    jQuery('#wwe_small_shipmentOffsetDays').attr('min', 1);
    var wweSmallCurrentTime = en_speedship_admin_script.wwe_small_order_cutoff_time;
    if (wweSmallCurrentTime != '') {
        jQuery('#wwe_small_orderCutoffTime').wickedpicker({
            now: wweSmallCurrentTime,
            title: 'Cut Off Time'
        });
    } else {
        jQuery('#wwe_small_orderCutoffTime').wickedpicker({
            now: '',
            title: 'Cut Off Time'
        });
    }

    // Estimated delivery options js
    en_wwe_cutt_off_time_radio_buttons();

    jQuery("input[name=wwe_small_delivery_estimates]").change(function () {
        en_wwe_cutt_off_time_radio_buttons();
    });

    //** Start: Validat Shipment Offset Days
    jQuery("#wwe_small_shipmentOffsetDays").keydown(function (e) {
        if (e.keyCode == 8)
            return;

        var val = jQuery("#wwe_small_shipmentOffsetDays").val();
        if (val.length > 1 || e.keyCode == 190) {
            e.preventDefault();
        }
        // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

    });
    // Allow: only positive numbers
    jQuery("#wwe_small_shipmentOffsetDays").keyup(function (e) {
        if (e.keyCode == 189) {
            e.preventDefault();
            jQuery("#wwe_small_shipmentOffsetDays").val('');
        }

    });

    // End estimated delivery options 

    jQuery('.quote_section_class_smpkg .wwe_small_markup, .wwe_small_quotes_markup_right_markup').on('click', function (event) {
        jQuery('.quote_section_class_smpkg .wwe_small_markup, .wwe_small_quotes_markup_right_markup').css('border', '');
    });

    jQuery("#wc_settings_hand_free_mark_up_wwe_small_packages, #air_hazardous_material_fee, #ground_hazardous_material_fee,#ground_transit_wwe_small_packages ").focus(function (e) {
        jQuery("#" + this.id).css({'border-color': '#ddd'});
    });
    jQuery("#wc_settings_hand_free_mark_up_wwe_small_packages").attr('maxlength', 7);

    // backup rates settings
    wweSmallBackupRatesSettings();

    var prevent_text_box = jQuery('.prevent_text_box').length;
    if (!prevent_text_box > 0) {
        jQuery("input[name*='wc_pervent_proceed_checkout_eniture']").closest('tr').addClass('wc_pervent_proceed_checkout_eniture');
        jQuery(".wc_pervent_proceed_checkout_eniture input[value*='allow']").after('<span class="wwe_small_custom_message">Allow user to continue to check out and display this message<br><textarea  name="allow_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250">' + en_speedship_admin_script.allow_proceed_checkout_eniture + '</textarea></br><span class="description"> Enter a maximum of 250 characters.</span>');
        jQuery(".wc_pervent_proceed_checkout_eniture input[value*='prevent']").after('<span class="wwe_small_custom_message">Prevent user from checking out and display this message<br><textarea name="prevent_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250">' + en_speedship_admin_script.prevent_proceed_checkout_eniture + '</textarea></br><span class="description"> Enter a maximum of 250 characters.</span>');
    }
    jQuery(".wwe_small_markup").closest('tr').addClass('wwe_small_markup_tr');
    jQuery(".wwe_small_markup_label").closest('tr').addClass('wwe_small_markup_label_tr');
    jQuery("#wc_settings_hand_free_mark_up_wwe_small_packages").closest('tr').addClass('wc_settings_hand_free_mark_up_wwe_small_packages_tr');
    jQuery("#avaibility_box_sizing").closest('tr').addClass("avaibility_box_sizing_tr");
    jQuery("#wc_settings_wwe_small_allow_other_plugins").closest('tr').addClass("wc_settings_wwe_small_allow_other_plugins_tr");
    jQuery("#ground_transit_wwe_small_packages , #ground_hazardous_material_fee , #air_hazardous_material_fee").keydown(function (e) {

        // Allow one decimal in integers values
        if (e.keyCode === 190 && this.value.split('.').length === 2) {
            e.preventDefault();
        }

        // Allow: backspace, delete, tab, escape, enter and .
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
   
    jQuery("#en_wd_origin_markup,#en_wd_dropship_markup,._en_product_markup,#wc_settings_hand_free_mark_up_wwe_small_packages").bind("cut copy paste",function(e) {
           e.preventDefault();
        });
    //** Start: Validation for domestic service level markup
    jQuery("#en_wd_origin_markup,#en_wd_dropship_markup,._en_product_markup,#wc_settings_hand_free_mark_up_wwe_small_packages").keypress(function (e) {
        if (!String.fromCharCode(e.keyCode).match(/^[-0-9\d\.%\s]+$/i)) return false;
    });
    jQuery(".wwe_small_markup,#en_wd_origin_markup,#en_wd_dropship_markup,._en_product_markup, .wwe_small_quotes_markup_right_markup").keydown(function (e) {
        if ((e.keyCode === 109 || e.keyCode === 189) && (jQuery(this).val().length>0) )  return false;
        if (e.keyCode === 53) if (e.shiftKey) if(jQuery(this).val().length==0)   return false; 
        if (jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
            // Allow: Ctrl+A, Command+A
            (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
            // Allow: home, end, left, right, down, up
            (e.keyCode >= 35 && e.keyCode <= 40)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }

        if ((jQuery(this).val().indexOf('.') != -1) && (jQuery(this).val().substring(jQuery(this).val().indexOf('.'), jQuery(this).val().indexOf('.').length).length > 2)) {
            if (e.keyCode !== 8 && e.keyCode !== 46) { //exception
                e.preventDefault();
            }
        }
        if(jQuery(this).val().length > 7){
            e.preventDefault();
        }
    });

    jQuery(".wwe_small_markup, #en_wd_origin_markup, #en_wd_dropship_markup,._en_product_markup, .wwe_small_quotes_markup_right_markup").keyup(function (e) {

        var val = jQuery(this).val();
        jQuery(this).css({"border": "1px solid #ddd"});


        if (val.split('.').length - 1 > 1) {
            var newval = val.substring(0, val.length - 1);
            var countDots = newval.substring(newval.indexOf('.') + 1).length;
            newval = newval.substring(0, val.length - countDots - 1);
            jQuery(this).val(newval);

        }

        if (val.split('%').length - 1 > 1) {
            var newval = val.substring(0, val.length - 1);
            var countPercentages = newval.substring(newval.indexOf('%') + 1).length;
            newval = newval.substring(0, val.length - countPercentages - 1);
            jQuery(this).val(newval);
        }
        if (val.split('>').length - 1 > 0) {
            var newval = val.substring(0, val.length - 1);
            var countGreaterThan = newval.substring(newval.indexOf('>') + 1).length;
            newval = newval.substring(newval, newval.length - countGreaterThan - 1);
            jQuery(this).val(newval);
        }
        if (val.split('_').length - 1 > 0) {
            var newval = val.substring(0, val.length - 1);
            var countUnderScore = newval.substring(newval.indexOf('_') + 1).length;
            newval = newval.substring(newval, newval.length - countUnderScore - 1);
            jQuery(this).val(newval);
        }
        if (val.split('-').length - 1 > 1) {
            var newval = val.substring(0, val.length - 1);
            var countPercentages = newval.substring(newval.indexOf('-') + 1).length;
            newval = newval.substring(0, val.length - countPercentages - 1);
            jQuery(this).val(newval);
        }
    });

    //** END: Validation for domestic service level markup

    jQuery(".connection_section_class .button-primary, .connection_section_class .is-primary").click(function () {
        var input = validateInput('.connection_section_class');
        if (input === false) {
            return false;
        }
    });
    jQuery(".connection_section_class .woocommerce-save-button").addClass('savebtn');
    jQuery(".connection_section_class .savebtn").before('<a href="javascript:void(0)" class="button-primary sm_test_connection">Test connection</a>');
    jQuery('.sm_test_connection').click(function (e) {

        var input = validateInput('.connection_section_class');
        if (input === false) {
            return false;
        }

        
        let api_endpoint = jQuery('#api_endpoint_wwe_small_packages').val();
        var postForm = {
            'action': 'speedship_action',
            'speed_freight_licence_key': jQuery('#wc_settings_plugin_licence_key_wwe_small_packages_quotes').val(),
            'api_end_point': api_endpoint
        };
        
        if (api_endpoint == 'wwe_small_new_api') {
            postForm.client_id = jQuery('#wwe_small_client_id').val();
			postForm.client_secret = jQuery('#wwe_small_client_secret').val();
            postForm.speed_freight_username = jQuery('#wwe_small_new_api_username').val();
			postForm.speed_freight_password = jQuery('#wwe_small_new_api_password').val();
		} else {
            postForm.world_wide_express_account_number = jQuery('#wc_settings_account_number_wwe_small_packages_quotes').val();
			postForm.speed_freight_username = jQuery('#wc_settings_username_wwe_small_packages_quotes').val();
			postForm.speed_freight_password = jQuery('#wc_settings_password_wwe_small_packages').val();
			postForm.authentication_key = jQuery(
				'#wc_settings_authentication_key_wwe_small_packages_quotes'
			).val();
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: postForm,
            dataType: 'json',
            beforeSend: function () {
                jQuery(".sm_test_connection").css("color", "#fff");
                jQuery(".connection_section_class .button-primary, .connection_section_class .is-primary").css("cursor", "pointer");
                jQuery('#wc_settings_account_number_wwe_small_packages_quotes').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_username_wwe_small_packages_quotes').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_password_wwe_small_packages').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_plugin_licence_key_wwe_small_packages_quotes').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wc_settings_authentication_key_wwe_small_packages_quotes').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wwe_small_client_id').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
                jQuery('#wwe_small_client_secret').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
            },
            success: function (data) {
                if (data.success || (data.severity && data.severity === 'SUCCESS')) {
                    jQuery(".updated").hide();
                    jQuery(".test_conn_msg").hide();
                    jQuery(".test_err_msg").remove();
                    jQuery('#wc_settings_account_number_wwe_small_packages_quotes').css('background', '#fff');
                    jQuery('#wc_settings_username_wwe_small_packages_quotes').css('background', '#fff');
                    jQuery('#wc_settings_password_wwe_small_packages').css('background', '#fff');
                    jQuery('#wc_settings_plugin_licence_key_wwe_small_packages_quotes').css('background', '#fff');
                    jQuery('#wc_settings_authentication_key_wwe_small_packages_quotes').css('background', '#fff');
                    jQuery('#wwe_small_client_id').css('background', '#fff');
                    jQuery('#wwe_small_client_secret').css('background', '#fff');
                    jQuery(".class_success_message").remove();
                    jQuery(".class_error_message").remove();
                    jQuery(".connection_section_class .button-primary, .connection_section_class .is-primary").attr("disabled", false);
                    jQuery('.warning-msg').before('<p class="test_conn_msg"><b> Success! The test resulted in a successful connection. </b></p>');
                } else {
                    jQuery(".updated").hide();
                    jQuery(".test_conn_msg").hide();
                    jQuery(".test_err_msg").remove();
                    jQuery('#wc_settings_account_number_wwe_small_packages_quotes').css('background', '#fff');
                    jQuery('#wc_settings_username_wwe_small_packages_quotes').css('background', '#fff');
                    jQuery('#wc_settings_password_wwe_small_packages').css('background', '#fff');
                    jQuery('#wc_settings_plugin_licence_key_wwe_small_packages_quotes').css('background', '#fff');
                    jQuery('#wc_settings_authentication_key_wwe_small_packages_quotes').css('background', '#fff');
                    jQuery('#wwe_small_client_id').css('background', '#fff');
                    jQuery('#wwe_small_client_secret').css('background', '#fff');
                    jQuery(".class_success_message").remove();
                    jQuery(".connection_section_class .button-primary, .connection_section_class .is-primary").attr("disabled", false);
                    if (data.error_desc) {
                        jQuery('.warning-msg').before('<p class="test_err_msg" ><b>Error! ' + data.error_desc + ' </b></p>');
                    } else {
                        jQuery('.warning-msg').before('<p class="test_err_msg" ><b>Error! The credentials entered did not result in a successful test. Confirm your credentials and try again. </b></p>');
                    }
                }
            }
        });
        e.preventDefault();
    });

    // To update packaging type
    if(en_speedship_admin_script.wwe_small_packaging_type == ''){
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: 'en_wwe_small_activate_hit_to_update_plan'},
            success: function (data_response) {}
        });
    }

    // fdo va
    jQuery('#fd_online_id_wwe_s').click(function (e) {
        var postForm = {
            'action': 'wwe_s_fd',
            'company_id': jQuery('#freightdesk_online_id').val(),
            'disconnect': jQuery('#fd_online_id_wwe_s').attr("data")
        }
        var id_lenght = jQuery('#freightdesk_online_id').val();
        var disc_data = jQuery('#fd_online_id_wwe_s').attr("data");
        if(typeof (id_lenght) != "undefined" && id_lenght.length < 1) {
            jQuery(".class_error_message").remove();
            jQuery('.user_guide_fdo').before('<div class="notice notice-error class_error_message"><p><strong>Error!</strong> FreightDesk Online ID is Required.</p></div>');
            return;
        }
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: postForm,
            beforeSend: function () {
                jQuery('#freightdesk_online_id').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
            },
            success: function (data_response) {
                if(typeof (data_response) == "undefined"){
                    return;
                }
                var fd_data = JSON.parse(data_response);
                jQuery('#freightdesk_online_id').css('background', '#fff');
                jQuery(".class_error_message").remove();
                if((typeof (fd_data.is_valid) != 'undefined' && fd_data.is_valid == false) || (typeof (fd_data.status) != 'undefined' && fd_data.is_valid == 'ERROR')) {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error class_error_message"><p><strong>Error! ' + fd_data.message + '</strong></p></div>');
                }else if(typeof (fd_data.status) != 'undefined' && fd_data.status == 'SUCCESS') {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-success class_success_message"><p><strong>Success! ' + fd_data.message + '</strong></p></div>');
                    window.location.reload(true);
                }else if(typeof (fd_data.status) != 'undefined' && fd_data.status == 'ERROR') {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error class_error_message"><p><strong>Error! ' + fd_data.message + '</strong></p></div>');
                }else if (fd_data.is_valid == 'true') {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error class_error_message"><p><strong>Error!</strong> FreightDesk Online ID is not valid.</p></div>');
                } else if (fd_data.is_valid == 'true' && fd_data.is_connected) {
                    jQuery('.user_guide_fdo').before('<div class="notice notice-error class_error_message"><p><strong>Error!</strong> Your store is already connected with FreightDesk Online.</p></div>');

                } else if (fd_data.is_valid == true && fd_data.is_connected == false && fd_data.redirect_url != null) {
                    window.location = fd_data.redirect_url;
                } else if (fd_data.is_connected == true) {
                    jQuery('#con_dis').empty();
                    jQuery('#con_dis').append('<a href="#" id="fd_online_id_wwe_s" data="disconnect" class="button-primary">Disconnect</a>')
                }
            }
        });
        e.preventDefault();
    });

    jQuery("#wc_settings_quest_as_residential_delivery_wwe_small_packages").closest('tr').addClass("wc_settings_quest_as_residential_delivery_wwe_small_packages");
    jQuery("#avaibility_auto_residential").closest('tr').addClass("avaibility_auto_residential");

    var url = getUrlVarsWWESmall()["tab"];
    if (url === 'wwe_small_packages_quotes') {
        jQuery('#footer-left').attr('id', 'wc-footer-left');
    }

    jQuery('.connection_section_class .form-table').before('<div class="warning-msg"><p> <b>Note!</b> You must have a Worldwide Express account to use this application. If you don\'t have one, click <a href="https://wwex.com/our-technology/e-commerce-solutions" target="_blank" rel="noopener noreferrer">here</a> and complete the form. </p>');
    jQuery('.warning-msg').first().show();

    // Ignore shipping calculator
    function speedship_ignore_items() {
        var en_ship_class = jQuery('#en_ignore_items_through_freight_classification').val();
        var en_ship_class_arr = en_ship_class.split(',');
        var en_ship_class_trim_arr = en_ship_class_arr.map(Function.prototype.call, String.prototype.trim);
        if (en_ship_class_trim_arr.indexOf('ltl_freight') != -1) {
            jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline wwe_small_pallet_weight_error"><p><strong>Error! </strong>Shipping Slug of <b>ltl_freight</b> can not be ignored.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.wwe_small_pallet_weight_error').position().top
            });
            jQuery("#en_ignore_items_through_freight_classification").css({'border-color': '#e81123'});
            return false;
        } else {
            return true;
        }
    }

    jQuery('.quote_section_class_smpkg .button-primary, .quote_section_class_smpkg .is-primary').on('click', function (e) {
        jQuery('.error').remove();

        if (!speedship_ignore_items()) {
            return false;
        } else if (!speedship_handling_fee_validation()) {
            return false;
        } else if (!speedship_air_hazardous_material_fee_validation()) {
            return false;
        } else if (!speedship_ground_hazardous_material_fee_validation()) {
            return false;
        } else if (!speedship_ground_transit_validation()) {
            return false;
        }

        var wwe_small_shipmentOffsetDays = jQuery("#wwe_small_shipmentOffsetDays").val();
        if (wwe_small_shipmentOffsetDays != "" && wwe_small_shipmentOffsetDays < 1) {

            jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline wwe_small_orderCutoffTime_error"><p><strong>Error! </strong>Days should not be less than 1.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.wwe_small_orderCutoffTime_error').position().top
            });
            jQuery("#wwe_small_shipmentOffsetDays").css({'border-color': '#e81123'});
            return false
        }
        if (wwe_small_shipmentOffsetDays != "" && wwe_small_shipmentOffsetDays > 8) {

            jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline wwe_small_orderCutoffTime_error"><p><strong>Error! </strong>Days should be less than or equal to 8.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.wwe_small_orderCutoffTime_error').position().top
            });
            jQuery("#wwe_small_shipmentOffsetDays").css({'border-color': '#e81123'});
            return false
        }

        var numberOnlyRegex = /^[0-9]+$/;

        if (wwe_small_shipmentOffsetDays != "" && !numberOnlyRegex.test(wwe_small_shipmentOffsetDays)) {

            jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline wwe_small_orderCutoffTime_error"><p><strong>Error! </strong>Entered Days are not valid.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.wwe_small_orderCutoffTime_error').position().top
            });
            jQuery("#wwe_small_shipmentOffsetDays").css({'border-color': '#e81123'});
            return false
        }

        let wwe_small_markup = jQuery('.wwe_small_markup, .wwe_small_quotes_markup_right_markup');
        jQuery(wwe_small_markup).each(function () {

            if (jQuery('#' + this.id).val() != '' && !markup_service(this.id)) {
                e.preventDefault();
                return false;
            }
        });

        // var handling_fee = jQuery('#wc_settings_hand_free_mark_up_wwe_small_packages').val();
        var num_of_checkboxes = jQuery('.quotes_services:checked, .wwe_international_service:checked').length;
        if (num_of_checkboxes < 1) {
            no_service_selected(num_of_checkboxes);
            return false;
        }

        // backup rates validations
        if (!wweSmallBackupRatesValidations()) return false;

        /*Custom Error Message Validation*/
        var checkedValCustomMsg = jQuery("input[name='wc_pervent_proceed_checkout_eniture']:checked").val();
        var allow_proceed_checkout_eniture = jQuery("textarea[name=allow_proceed_checkout_eniture]").val();
        var prevent_proceed_checkout_eniture = jQuery("textarea[name=prevent_proceed_checkout_eniture]").val();

        if (checkedValCustomMsg == 'allow' && allow_proceed_checkout_eniture == '') {
            jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline wwe_small_custom_error_message"><p><strong>Error! </strong>Custom message field is empty.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.wwe_small_custom_error_message').position().top
            });
            return false;
        } else if (checkedValCustomMsg == 'prevent' && prevent_proceed_checkout_eniture == '') {
            jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline wwe_small_custom_error_message"><p><strong>Error! </strong>Custom message field is empty.</p></div>');
            jQuery('html, body').animate({
                'scrollTop': jQuery('.wwe_small_custom_error_message').position().top
            });
            return false;
        }
    });

    var sm_all_checkboxes = jQuery('.quotes_services');
    if (sm_all_checkboxes.length === sm_all_checkboxes.filter(":checked").length) {
        jQuery('.sm_all_services').prop('checked', true);
    }

    jQuery(".sm_all_services").change(function () {
        if (this.checked) {
            jQuery(".quotes_services").each(function () {
                this.checked = true;
            })
        } else {
            jQuery(".quotes_services").each(function () {
                this.checked = false;
            })
        }
    });

    jQuery('.connection_section_class input[type="text"]').each(function () {
        if (jQuery(this).parent().find('.err').length < 1) {
            jQuery(this).after('<span class="err"></span>');
        }
    });

    /**
     * EN apply coupon code send an API call to FDO server
     */
     jQuery(".en_fdo_wwe_small_apply_promo_btn").on("click", function (e) {
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: 'en_wwe_small_fdo_apply_coupon',
                    coupon: this.getAttribute('data-coupon')
                    },
            success: function (resp) {
                response = JSON.parse(resp);
                if(response.status == 'error'){
                    jQuery('.en_fdo_wwe_small_apply_promo_btn').after('<p id="en_fdo_wwe_small_apply_promo_error_p" class="en-error-message">'+response.message+'</p>');
                    setTimeout(function(){
                        jQuery("#en_fdo_wwe_small_apply_promo_error_p").fadeOut(500);
                    }, 5000)
                }else{
                    window.location.reload(true);
                }
            }
        });

        e.preventDefault();
    });

    /**
     * EN apply coupon code send an API call to Validate addresses server
     */
     jQuery(".en_va_wwe_small_apply_promo_btn").on("click", function (e) {
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: 'en_wwe_small_va_apply_coupon',
                    coupon: this.getAttribute('data-coupon')
                    },
            success: function (resp) {
                response = JSON.parse(resp);
                if(response.status == 'error'){
                    jQuery('.en_va_wwe_small_apply_promo_btn').after('<p id="en_va_wwe_small_apply_promo_error_p" class="en-error-message">'+response.message+'</p>');
                    setTimeout(function(){
                        jQuery("#en_va_wwe_small_apply_promo_error_p").fadeOut(500);
                    }, 5000)
                }else{
                    window.location.reload(true);
                }
            }
        });

        e.preventDefault();
    });


    /*
     * Uncheck Select All Checkbox
     */

    jQuery(".quotes_services").on('change load', function () {
        var checkboxes = jQuery('.quotes_services:checked').length;
        var un_checkboxes = jQuery('.quotes_services').length;
        if (checkboxes === un_checkboxes) {
            jQuery('.sm_all_services').prop('checked', true);
        } else {
            jQuery('.sm_all_services').prop('checked', false);
        }
    });

    jQuery('#wc_settings_account_number_wwe_small_packages_quotes').attr('title', 'Account Number');
    jQuery('#wc_settings_username_wwe_small_packages_quotes').attr('title', 'Username');
    jQuery('#wc_settings_password_wwe_small_packages').attr('title', 'Password');
    jQuery('#wc_settings_plugin_licence_key_wwe_small_packages_quotes').attr('title', 'Eniture API Key');
    jQuery('#wc_settings_authentication_key_wwe_small_packages_quotes').attr('title', 'Authentication Key');

    jQuery('#wc_settings_hand_free_mark_up_wwe_small_packages').attr('title', 'Handling Fee / Markup');
    jQuery('.prevent_text_box').attr('title', 'Message');

    jQuery('.quotes_services').closest('tr').addClass('quotes_services_tr');
    jQuery('.quotes_services').closest('td').addClass('quotes_services_td');

    jQuery("#ground_transit_label").closest('tr').addClass("ground_transit_label");
    jQuery("#hazardous_material_settings").closest('tr').addClass("hazardous_material_settings");
    jQuery("#ground_transit_wwe_small_packages").closest('tr').addClass("ground_transit_wwe_small_packages");
    jQuery("input[name*='restrict_calendar_transit_wwe_small_packages']").closest('tr').addClass('restrict_calendar_transit_wwe_small_packages');
    jQuery("input[name*='only_quote_ground_service_for_hazardous_materials_shipments']").closest('tr').addClass('only_quote_ground_service_for_hazardous_materials_shipments');
    jQuery("input[name*='ground_hazardous_material_fee']").closest('tr').addClass('ground_hazardous_material_fee');
    jQuery("input[name*='air_hazardous_material_fee']").closest('tr').addClass('air_hazardous_material_fee');    
 
    // Nested Material
    // JS for edit product nested fields
    jQuery("._nestedMaterials").closest('p').addClass("_nestedMaterials_tr");
    jQuery("._nestedPercentage").closest('p').addClass("_nestedPercentage_tr");
    jQuery("._maxNestedItems").closest('p').addClass("_maxNestedItems_tr");
    jQuery("._nestedDimension").closest('p').addClass("_nestedDimension_tr");
    jQuery("._nestedStakingProperty").closest('p').addClass("_nestedStakingProperty_tr");

    

    if (!jQuery('._nestedMaterials').is(":checked")) {
       
        jQuery('._nestedPercentage_tr').hide();
        jQuery('._nestedDimension_tr').hide();
        jQuery('._maxNestedItems_tr').hide();
        jQuery('._nestedDimension_tr').hide();
        jQuery('._nestedStakingProperty_tr').hide();
    } else {
        jQuery('._nestedPercentage_tr').show();
        jQuery('._nestedDimension_tr').show();
        jQuery('._maxNestedItems_tr').show();
        jQuery('._nestedDimension_tr').show();
        jQuery('._nestedStakingProperty_tr').show();
    }

    jQuery("._nestedPercentage").attr('min', '0');
    jQuery("._maxNestedItems").attr('min', '0');
    jQuery("._nestedPercentage").attr('max', '100');
    jQuery("._maxNestedItems").attr('max', '100');
    jQuery("._nestedPercentage").attr('maxlength', '3');
    jQuery("._maxNestedItems").attr('maxlength', '3');

    if (jQuery("._nestedPercentage").val() == '') {
        jQuery("._nestedPercentage").val(0);
    }

    // insertion in ready function
    // Nested fields validation on product details
    jQuery("._nestedPercentage").keydown(function (eve) {
        wwe_stopSpecialCharacters(eve);
        var nestedPercentage = jQuery('._nestedPercentage').val();
        if (nestedPercentage.length == 2) {
            var newValue = nestedPercentage + '' + eve.key;
            if (newValue > 100) {
                return false;
            }
        }
    });

    jQuery("._maxNestedItems").keydown(function (eve) {
        wwe_stopSpecialCharacters(eve);
    });

    jQuery("._nestedMaterials").change(function () {
        if (!jQuery('._nestedMaterials').is(":checked")) {
            jQuery('._nestedPercentage_tr').hide();
            jQuery('._nestedDimension_tr').hide();
            jQuery('._maxNestedItems_tr').hide();
            jQuery('._nestedDimension_tr').hide();
            jQuery('._nestedStakingProperty_tr').hide();
        } else {
            jQuery('._nestedPercentage_tr').show();
            jQuery('._nestedDimension_tr').show();
            jQuery('._maxNestedItems_tr').show();
            jQuery('._nestedDimension_tr').show();
            jQuery('._nestedStakingProperty_tr').show();
        }
    });

    // New API
    jQuery("#wwe_small_client_id").attr('minlength', '1');
    jQuery("#wwe_small_client_secret").attr('minlength', '1');
    jQuery("#wwe_small_client_id").attr('maxlength', '100');
    jQuery("#wwe_small_client_secret").attr('maxlength', '100');
    jQuery('#wwe_small_client_id').attr('title', 'Client ID');
    jQuery('#wwe_small_client_secret').attr('title', 'Client Secret');
    jQuery("#wwe_small_new_api_username").attr('maxlength', '100');
    jQuery("#wwe_small_new_api_password").attr('maxlength', '100');
    jQuery('#wwe_small_new_api_username').attr('title', 'Username');
    jQuery('#wwe_small_new_api_password').attr('title', 'Password');

    // Comapre rates
    jQuery("#wwe-cr-org-state, #wwe-cr-org-country, #wwe-cr-dest-state, #wwe-cr-dest-country").keyup(function () {
        jQuery(this).val(this.value.toUpperCase());
    });
    
    jQuery('input.alphaonly').keyup(function () {
        var location_field_id = jQuery(this).attr("id");
        var location_regex = location_field_id == 'wwe-cr-org-city' || location_field_id == 'wwe-cr-dest-city' ? /[^a-zA-Z- ]/g : /[^a-zA-Z]/g;
        
        if (this.value.match(location_regex)) {
            this.value = this.value.replace(location_regex, '');
        }
    });

    jQuery("#wwe-cr-org-zip, #wwe-cr-dest-zip").keydown(function (e) {
        if (e.which != 96) {
            if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && (e.keyCode < 65 || e.keyCode > 90) && (e.keyCode < 97 || e.keyCode > 122) && e.which != 9) {
                return false;
            } else {
                if (this.id == 'wwe-cr-org-zip' && e.keyCode > 64 && e.keyCode < 91) {
                    return false;
                }

                jQuery(this).val(this.value.toUpperCase());
            }
        }
    });

    jQuery('#wwe-cr-org-zip, #wwe-cr-dest-zip').change(function () {
        if (jQuery(this).val() == '') {
            return false;
        }

        const id = this.id;
        change_cr_warehouse_zip(jQuery(this).val(), id);
    });

    jQuery('#wwe-cr-get-quotes-btn').click(function (e) {
        e.preventDefault();

        if (jQuery('#quotes-loading').val() == 1) {
            return false;
        }

        const isValid = validateInput('.wwe-cr-container', '.wwe-cr-err');
        if (!isValid) {
            jQuery('html, body').animate({'scrollTop': 0});
            return false;
        }
        
        // validate dimensions values
        const dimsArr = ['#wwe-cr-pckg-weight', '#wwe-cr-pckg-length', '#wwe-cr-pckg-width', '#wwe-cr-pckg-height'];
        const dimsRegex = /^\d+(\.\d{1,2})?$/;
        let val = '';
        
        for (const dim of dimsArr) {
            val = jQuery(dim).val();

            if (val && !dimsRegex.test(val)) {
                jQuery('.invalid_dimension').show('slow');

                setTimeout(function () {
                    jQuery('.invalid_dimension').hide('slow');
                }, 5000);

                return false;
            }
        }

        jQuery(this).css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
        jQuery('#quotes-loading').val(1);
        
        wwe_cr_get_and_populate_rates(this);
    });

    jQuery('#wwe-cr-pckg-weight, #wwe-cr-pckg-length, #wwe-cr-pckg-width, #wwe-cr-pckg-height').keypress(function (e) {
        const val = this.value

        return e.charCode === 0 || ((e.charCode >= 48 && e.charCode <= 57) || (e.charCode == 46 && val.indexOf('.') < 0));
    });

    // Product variants settings
    jQuery(document).on("click", '._nestedMaterials', function(e) {
        const checkbox_class = jQuery(e.target).attr("class");
        const name = jQuery(e.target).attr("name");
        const checked = jQuery(e.target).prop('checked');

        if (checkbox_class?.includes('_nestedMaterials')) {
            const id = name?.split('_nestedMaterials')[1];
            setNestMatDisplay(id, checked);
        }
    });

    // Callback function to execute when mutations are observed
    const handleMutations = (mutationList) => {
        let childs = [];
        for (const mutation of mutationList) {
            childs = mutation?.target?.children;
            if (childs?.length) setNestedMaterialsUI();
          }
    };
    const observer = new MutationObserver(handleMutations),
        targetNode = document.querySelector('.woocommerce_variations.wc-metaboxes'),
        config = { attributes: true, childList: true, subtree: true };
    if (targetNode) observer.observe(targetNode, config);
});

// Cuttoff time radio buttons
function en_wwe_cutt_off_time_radio_buttons() {
    var delivery_estimate_val = jQuery('input[name=wwe_small_delivery_estimates]:checked').val();
    if (delivery_estimate_val == 'dont_show_estimates') {
        jQuery("#wwe_small_orderCutoffTime").prop('disabled', true);
        jQuery("#wwe_small_shipmentOffsetDays").prop('disabled', true);
    } else {
        jQuery("#wwe_small_orderCutoffTime").prop('disabled', false);
        jQuery("#wwe_small_shipmentOffsetDays").prop('disabled', false);
    }
}

// Update plan
if (typeof en_update_plan != 'function') {
    function en_update_plan(input) {
        let action = jQuery(input).attr('data-action');
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: {action: action},
            success: function (data_response) {
                window.location.reload(true);
            }
        });
    }
}

function wwe_stopSpecialCharacters(e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if (jQuery.inArray(e.keyCode, [46, 9, 27, 13, 110, 190, 189]) !== -1 ||
        // Allow: Ctrl+A, Command+A
        (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
        // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
        // let it happen, don't do anything
        e.preventDefault();
        return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 90)) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 186 && e.keyCode != 8) {
        e.preventDefault();
    }
    if (e.keyCode == 186 || e.keyCode == 190 || e.keyCode == 189 || (e.keyCode > 64 && e.keyCode < 91)) {
        e.preventDefault();
        return;
    }
}

function isValidNumber(value, noNegative) {
    if (typeof (noNegative) === 'undefined')
        noNegative = false;
    var isValidNumber = false;
    var validNumber = (noNegative == true) ? parseFloat(value) >= 0 : true;
    if ((value == parseInt(value) || value == parseFloat(value)) && (validNumber)) {
        if (value.indexOf(".") >= 0) {
            var n = value.split(".");
            if (n[n.length - 1].length <= 4) {
                isValidNumber = true;
            } else {
                isValidNumber = 'decimal_point_err';
            }
        } else {
            isValidNumber = true;
        }
    }
    return isValidNumber;
}

/**
 * Read a page's GET URL variables and return them as an associative array.
 */
function getUrlVarsWWESmall() {
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for (var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}

function speedship_handling_fee_validation() {
    var handling_fee = jQuery('#wc_settings_hand_free_mark_up_wwe_small_packages').val();
    var handling_fee_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
    var numeric_values_regex = /^[0-9]{1,7}$/;
    if (handling_fee != '' && numeric_values_regex.test(handling_fee)) {
        return true;
    } else if (handling_fee != '' && !handling_fee_regex.test(handling_fee) || handling_fee.split('.').length - 1 > 1) {
        jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline wwe_handlng_fee_error"><p><strong>Error! </strong>Handling fee format should be 100.20 or 10%.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.wwe_handlng_fee_error').position().top
        });
        jQuery("#wc_settings_hand_free_mark_up_wwe_small_packages").css({'border-color': '#e81123'});
        return false;
    } else {
        return true;
    }
}

function speedship_air_hazardous_material_fee_validation() {
    var air_hazardous_fee = jQuery('#air_hazardous_material_fee').val();
    var air_hazardous_fee_regex = /^([0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
    if (air_hazardous_fee != '' && !air_hazardous_fee_regex.test(air_hazardous_fee) || air_hazardous_fee.split('.').length - 1 > 1) {
        jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline wwe_small_air_hazardous_fee_error"><p><strong>Error! </strong>Air hazardous material fee format should be 100.20</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.wwe_small_air_hazardous_fee_error').position().top
        });
        jQuery("#air_hazardous_material_fee").css({'border-color': '#e81123'});
        return false;
    } else {
        return true;
    }
}

function speedship_ground_hazardous_material_fee_validation() {
    var ground_hazardous_fee = jQuery('#ground_hazardous_material_fee').val();
    var ground_hazardous_regex = /^([0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
    if (ground_hazardous_fee != '' && !ground_hazardous_regex.test(ground_hazardous_fee) || ground_hazardous_fee.split('.').length - 1 > 1) {
        jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline ground_ground_hazardous_fee_error"><p><strong>Error! </strong>Ground  hazardous material  fee format should be 100.20</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.ground_ground_hazardous_fee_error').position().top
        });
        jQuery("#ground_hazardous_material_fee").css({'border-color': '#e81123'});
        return false;
    } else {
        return true;
    }
}

function speedship_ground_transit_validation() {
    var ground_transit_value = jQuery('#ground_transit_wwe_small_packages').val();
    var ground_transit_regex = /^[0-9]{1,2}$/;
    if (ground_transit_value != '' && !ground_transit_regex.test(ground_transit_value)) {
        jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline ground_transit_error"><p><strong>Error! </strong>Maximum 2 numeric characters are allowed for transit day field.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.ground_transit_error').position().top
        });
        jQuery("#ground_transit_wwe_small_packages").css({'border-color': '#e81123'});
        return false;
    } else {
        return true;
    }
}

function markup_service(id) {

    var wwe_small_markup = jQuery('#' + id).val();
    var wwe_small_markup_service_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;

    if (!wwe_small_markup_service_regex.test(wwe_small_markup)) {
        jQuery("#mainform .quote_section_class_smpkg").prepend('<div id="message" class="error inline smpkg_small_dom_markup_service_error"><p><strong>Error! </strong>Service Level Markup fee format should be 100.20 or 10%.</p></div>');
        jQuery('html, body').animate({
            'scrollTop': jQuery('.smpkg_small_dom_markup_service_error').position().top
        });
        jQuery("#" + id).css({'border-color': '#e81123'});
        return false;
    } else {
        return true;
    }
}

/**
 * validation
 * @param form_id
 * @returns {Boolean}             */
function validateInput(form_id, err_class = '') {
    var has_err = true;
    jQuery(form_id + " input[type='text']").each(function () {
        var input = jQuery(this).val();
        var response = validateString(input);

        var errorElement = jQuery(this).parent().find(err_class == '' ? '.err' : err_class);
        jQuery(errorElement).html('');
        var errorText = jQuery(this).attr('title');
        var optional = jQuery(this).data('optional');
        optional = (optional === undefined) ? 0 : 1;
        errorText = (errorText != undefined) ? errorText : '';
        if ((optional == 0) && (response == false || response == 'empty')) {
            errorText = (response == 'empty') ? errorText + ' is required.' : 'Invalid input.';
            jQuery(errorElement).html(errorText);
        }
        has_err = (response != true && optional == 0) ? false : has_err;
    });
    return has_err;
}

/**
 * validate string
 * @param string
 * @returns {String|Boolean}         */
function validateString(string) {
    if (string == '') {
        return 'empty';
    } else {
        return true;
    }
}

/**
 * if No Service selected
 * @param num_of_checkboxes
 * @returns {Boolean}             */
function no_service_selected(num_of_checkboxes) {
    jQuery(".updated").hide();
    jQuery(".quote_section_class_smpkg h2:first-child").after('<div id="message" class="error inline no_srvc_select"><p><strong>Error! </strong>Please select at least one quote service.</p></div>');
    jQuery('html, body').animate({
        'scrollTop': jQuery('.no_srvc_select').position().top
    });
    return false;
}


function en_wwe_small_fdo_connection_status_refresh(input) {
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: {action: 'en_wwe_small_fdo_connection_status_refresh'},
        success: function (data_response) {
            window.location.reload(true);
        }
    });
}

function en_wwe_small_va_connection_status_refresh(input) {
    jQuery.ajax({
        type: "POST",
        url: ajaxurl,
        data: {action: 'en_wwe_small_va_connection_status_refresh'},
        success: function (data_response) {
            window.location.reload(true);
        }
    });
}

/**
 * Hide and show test connection fields based on API selection
 */
function wwe_connection_section_api_endpoint() {
    jQuery("#wwe_small_new_api_username").data('optional', '1');
    jQuery("#wwe_small_new_api_password").data('optional', '1');

    const api_endpoint = jQuery('#api_endpoint_wwe_small_packages').val();

    if (api_endpoint == 'wwe_small_old_api') {
        jQuery('.wwe_small_new_api_field').closest('tr').hide();
        jQuery('.wwe_small_old_api_field').closest('tr').show();

        jQuery("#wwe_small_client_id").data('optional', '1');
        jQuery("#wwe_small_client_secret").data('optional', '1');

        jQuery("#wc_settings_username_wwe_small_packages_quotes").removeData('optional');
        jQuery("#wc_settings_password_wwe_small_packages").removeData('optional');
        jQuery("#wc_settings_authentication_key_wwe_small_packages_quotes").removeData('optional');
        jQuery("#wc_settings_account_number_wwe_small_packages_quotes").removeData('optional');

    } else {
        jQuery('.wwe_small_old_api_field').closest('tr').hide();
        jQuery('.wwe_small_new_api_field').closest('tr').show();

        jQuery("#wc_settings_username_wwe_small_packages_quotes").data('optional', '1');
        jQuery("#wc_settings_password_wwe_small_packages").data('optional', '1');
        jQuery("#wc_settings_authentication_key_wwe_small_packages_quotes").data('optional', '1');
        jQuery("#wc_settings_account_number_wwe_small_packages_quotes").data('optional', '1');

        jQuery("#wwe_small_client_id").removeData('optional');
        jQuery("#wwe_small_client_secret").removeData('optional');
    }
}

if (typeof wwe_connection_section_api_endpoint == 'function') {
    wwe_connection_section_api_endpoint();
}

if (typeof change_cr_warehouse_zip != 'function') {
    function change_cr_warehouse_zip(origin_zip, origin_id) {
        const type = origin_id == 'wwe-cr-org-zip' ? 'org' : 'dest';
        const cityId = '#wwe-cr-' + type + '-city';
        const stateId = '#wwe-cr-' + type + '-state';
        const countryId = '#wwe-cr-' + type + '-country';
        const citySelectId = type == 'org' ? '#wwe-cr-org-select' : '#wwe-cr-dest-select';

        jQuery(cityId).css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
        jQuery(citySelectId).css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
        jQuery(stateId).css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');
        jQuery(countryId).css('background', 'rgba(255, 255, 255, 1) url("' + en_speedship_admin_script.plugins_url + '/small-package-quotes-wwe-edition/asset/processing.gif") no-repeat scroll 50% 50%');

        var postForm = {
            action: 'en_wd_get_address',
            origin_zip: origin_zip,
        };
    
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: postForm,
            dataType: 'json',
            beforeSend: function () {
                jQuery('.en_wd_zip_validation_err').hide();
                jQuery('.en_wd_city_validation_err').hide();
                jQuery('.en_wd_state_validation_err').hide();
                jQuery('.en_wd_country_validation_err').hide();

                type == 'org' ? jQuery('#wwe-cr-dest-zip').prop('disabled', true) :
                    jQuery('#wwe-cr-org-zip').prop('disabled', true);
            },
            success: function (data) {
                jQuery('#wwe-cr-org-zip').prop('disabled', false);
                jQuery('#wwe-cr-dest-zip').prop('disabled', false);

                if (data) {
                    if (data.country === 'US' || data.country === 'CA') {
                        
                        if (data.postcode_localities == 1) {
                            if (type == 'org') {
                                jQuery('.wwe-cr-org-city-input').hide();
                                jQuery('.wwe-cr-org-city-select').css('display', 'flex');
                                jQuery('#wwe-cr-org-select').replaceWith(data.city_option);
                                jQuery('#_city').attr('id', 'wwe-cr-org-select');

                                jQuery('#wwe-cr-org-select').change(function () {
                                    jQuery('#wwe-cr-org-city').val(this.value);
                                });
                            } else {
                                jQuery('.wwe-cr-dest-city-input').hide();
                                jQuery('.wwe-cr-dest-city-select').css('display', 'flex');
                                jQuery('#wwe-cr-dest-select').replaceWith(data.city_option);
                                jQuery('#_city').attr('id', 'wwe-cr-dest-select');

                                jQuery('#wwe-cr-dest-select').change(function () {
                                    jQuery('#wwe-cr-dest-city').val(this.value);
                                });
                            }
                        
                            jQuery(cityId).val(data.first_city);
                            jQuery(cityId).css('background', '#fff');
                        } else {
                            if (type == 'org') {
                                jQuery('.wwe-cr-org-city-input').css('display', 'flex');
                                jQuery('.wwe-cr-org-city-select').hide();
                                jQuery('#wwe-cr-org-city').removeAttr('value');
                            } else {
                                jQuery('.wwe-cr-dest-city-input').css('display', 'flex');
                                jQuery('.wwe-cr-dest-city-select').hide();
                                jQuery('#wwe-cr-dest-city').removeAttr('value');
                            }

                        
                            jQuery(cityId).val(data.city);
                            jQuery(cityId).css('background', '#fff');
                        }

                        jQuery(stateId).val(data.state);
                        jQuery(stateId).css('background', '#fff');
                        jQuery(countryId).val(data.country);
                        jQuery(countryId).css('background', '#fff');

                    } else if (data.result === 'ZERO_RESULTS') {
                        
                        jQuery('.zero_results').show('slow');
                        jQuery(cityId).css('background', '#fff');
                        jQuery(stateId).css('background', '#fff');
                        jQuery(countryId).css('background', '#fff');
                        jQuery(citySelectId).css('background', '#fff');
                        
                        setTimeout(function () {
                            jQuery('.zero_results').hide('slow');
                        }, 5000);

                    } else if (data.result === 'false') {
                        
                        jQuery('.zero_results').show('slow').delay(5000).hide('slow');

                        jQuery(cityId).css('background', '#fff');
                        jQuery(cityId).val('');
                        jQuery(citySelectId).css('background', '#fff');
                        jQuery(citySelectId).val('');
                        jQuery(stateId).css('background', '#fff');
                        jQuery(stateId).val('');
                        jQuery(countryId).css('background', '#fff');
                        jQuery(countryId).val('');

                    } else if (data.apiResp === 'apiErr') {
                        
                        jQuery('.wrng_credential').show('slow');
                        jQuery(cityId).css('background', '#fff');
                        jQuery(citySelectId).css('background', '#fff');
                        jQuery(stateId).css('background', '#fff');
                        jQuery(countryId).css('background', '#fff');
                        
                        setTimeout(function () {
                            jQuery('.wrng_credential').hide('slow');
                        }, 5000);
                    } else {
                        
                        jQuery('.not_allowed').show('slow');
                        jQuery(cityId).css('background', '#fff');
                        jQuery(citySelectId).css('background', '#fff');
                        jQuery(stateId).css('background', '#fff');
                        jQuery(countryId).css('background', '#fff');
                        
                        setTimeout(function () {
                            jQuery('.not_allowed').hide('slow');
                        }, 5000);
                    }
                }
            },
            error: function (err) {
                jQuery('#wwe-cr-org-zip').prop('disabled', false);
                jQuery('#wwe-cr-dest-zip').prop('disabled', false);
            }
        });
        
        return false;
    }
}

if (typeof set_comp_rates_city != 'function') {
    function set_comp_rates_city(e) {
        var city = jQuery(e).val();
        var id = e.id;
        jQuery(id).val(city);
    }
}

if (typeof wwe_cr_get_and_populate_rates != 'function') {
    function wwe_cr_get_and_populate_rates(quotes_btn) {
        let api_endpoint = jQuery('#api_endpoint_wwe_small_packages').val();
        
        let api_request = {
            'platform': 'wordpress',
            'carrierName': 'WWE SmPkg',
            'plugin_licence_key': jQuery('#wc_settings_plugin_licence_key_wwe_small_packages_quotes').val(),
            'speed_ship_senderZip': jQuery('#wwe-cr-org-zip').val(),
            'speed_ship_senderCity': jQuery('#wwe-cr-org-city').val(),
            'speed_ship_senderState': jQuery('#wwe-cr-org-state').val(),
            'speed_ship_senderCountryCode': jQuery('#wwe-cr-org-country').val(),
            'speed_ship_receiver_zip_code': jQuery('#wwe-cr-dest-zip').val(),
            'speed_ship_reciver_city': jQuery('#wwe-cr-dest-city').val(),
            'speed_ship_receiver_state': jQuery('#wwe-cr-dest-state').val(),
            'receiverCountryCode': jQuery('#wwe-cr-dest-country').val(),
            'residentials_delivery': (jQuery('#wwe-cr-residential-delivery').prop('checked')) ? 'yes' : 'no',
            'speed_ship_product_weight': [jQuery('#wwe-cr-pckg-weight').val()],
            'product_length_array': [jQuery('#wwe-cr-pckg-length').val()],
            'product_width_array': [jQuery('#wwe-cr-pckg-width').val()],
            'product_height_array': [jQuery('#wwe-cr-pckg-height').val()],
            'speed_ship_title_array': ['Product for CR'],
            'speed_ship_quantity_array': ['1'],
            'ship_item_alone': ['1']
        };

        let postForm = {
            'action': 'wwe_cr_get_rates',
            'api_request': api_request
        };

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: postForm,
            dataType: 'json',
            beforeSend: function () {
                jQuery('#wwe-cr-output').html('');
            },
            success: function (data) {                
                jQuery(quotes_btn).css('background', '');
                jQuery(quotes_btn).prop('disabled', false);
                jQuery('#quotes-loading').val(0);

                if (data.license_error === '1') {
                    jQuery('.wrng_credential').show('slow');
                    jQuery(cityId).css('background', '#fff');
                    jQuery(citySelectId).css('background', '#fff');
                    jQuery(stateId).css('background', '#fff');
                    jQuery(countryId).css('background', '#fff');
                    
                    setTimeout(function () {
                        jQuery('.wrng_credential').hide('slow');
                    }, 5000);
                }else{
                    populate_cr_results(data);
                }
                
            },
            error: function (err) {                
                jQuery(quotes_btn).css('background', '');
                jQuery(quotes_btn).prop('disabled', false);
                jQuery('#quotes-loading').val(0);
            }
        });
        
        return false;
    }
}

if (typeof populate_cr_results != 'function') {
    function populate_cr_results(results) {
        let crHtml = '<table class="widefat"><thead><tr><th>Worldwide Express (Parcel):</th><th>ShipEngine UPS:</th></tr></thead><tbody>';
        
        if (results.wwe == '' && results.shipengine == '') {
			crHtml += '<tr><td>No results found</td><td>No results found</td></tr>';
		} else {
			let rowCount =
				Array.isArray(results.wwe) && results.wwe.length > 0 ? results.wwe.length : 0;
			rowCount = Array.isArray(results.shipengine) && results.shipengine.length > results.wwe.length > 0
					? results.shipengine.length
					: rowCount;
			// exceptional handling
            if (rowCount > 50) return false;
            
			for (let i = 0; i < rowCount; i++) {
                crHtml += '<tr>';
                
				if (Array.isArray(results.wwe) && results.wwe[i] !== undefined) {
					crHtml +=
						'<td>' + results.wwe[i].service_name + ' <strong>$' + results.wwe[i].price +
						'</strong><br/>Delivery ' + results.wwe[i].estimated_delivery +
						'</td>';
				} else {
					crHtml += '<td></td>';
                }
                
				if (Array.isArray(results.shipengine) && results.shipengine[i] !== undefined) {
					crHtml +=
						'<td>' +
						results.shipengine[i].service_name + ' <strong>$' + Number(results.shipengine[i].price).toFixed(2) +
						'</strong><br/>Delivery ' + results.shipengine[i].estimated_delivery +
						'</td>';
				} else {
					crHtml += '<td></td>';
                }
                
				crHtml += '</tr>';
			}

			jQuery('html, body').animate({ scrollTop: jQuery(document).height() }, 1000);
		}

        crHtml += '</tbody></table>';
        jQuery('#wwe-cr-output').html(crHtml);
    }
}

if (typeof setNestedMaterialsUI != 'function') {
    function setNestedMaterialsUI() {
        const nestedMaterials = jQuery('._nestedMaterials');
        const productMarkups = jQuery('._en_product_markup');
        
        if (productMarkups?.length) {
            for (const markup of productMarkups) {
                jQuery(markup).attr('maxlength', '7');

                jQuery(markup).keypress(function (e) {
                    if (!String.fromCharCode(e.keyCode).match(/^[0-9.%-]+$/))
                        return false;
                });
            }
        }

        if (nestedMaterials?.length) {
            for (let elem of nestedMaterials) {
                const className = elem.className;

                if (className?.includes('_nestedMaterials')) {
                    const checked = jQuery(elem).prop('checked'),
                        name = jQuery(elem).attr('name'),
                        id = name?.split('_nestedMaterials')[1];
                    setNestMatDisplay(id, checked);
                }
            }
        }
    }
}

if (typeof setNestMatDisplay != 'function') {
    function setNestMatDisplay (id, checked) {
        
        jQuery(`input[name="_nestedPercentage${id}"]`).attr('min', '0');
        jQuery(`input[name="_nestedPercentage${id}"]`).attr('max', '100');
        jQuery(`input[name="_nestedPercentage${id}"]`).attr('maxlength', '3');
        jQuery(`input[name="_maxNestedItems${id}"]`).attr('min', '0');
        jQuery(`input[name="_maxNestedItems${id}"]`).attr('max', '100');
        jQuery(`input[name="_maxNestedItems${id}"]`).attr('maxlength', '3');

        jQuery(`input[name="_nestedPercentage${id}"], input[name="_maxNestedItems${id}"]`).keypress(function (e) {
            if (!String.fromCharCode(e.keyCode).match(/^[0-9]+$/))
                return false;
        });

        jQuery(`input[name="_nestedPercentage${id}"]`).closest('p').css('display', checked ? '' : 'none');
        jQuery(`select[name="_nestedDimension${id}"]`).closest('p').css('display', checked ? '' : 'none');
        jQuery(`input[name="_maxNestedItems${id}"]`).closest('p').css('display', checked ? '' : 'none');
        jQuery(`select[name="_nestedStakingProperty${id}"]`).closest('p').css('display', checked ? '' : 'none');
    }
}

if (typeof wweSmallBackupRatesSettings != 'function') {
    function wweSmallBackupRatesSettings() {
        jQuery('input[name*="backup_rates_category_wwe_small"]').closest('tr').addClass("backup_rates_category_wwe_small");
        // backup rates as a fixed rate
        jQuery(".backup_rates_category_wwe_small input[value*='fixed_rate']").after('Backup rate as a fixed rate. <br /><input type="text" style="margin-top: 10px" name="backup_rates_fixed_rate_wwe_small" id="backup_rates_fixed_rate_wwe_small" title="Backup Rates" maxlength="50" value="' + en_speedship_admin_script.backup_rates_fixed_rate_wwe_small + '"> <br> <span class="description"> Enter a value for the fixed rate. (e.g. 10.00)</span><br />');
        // backup rates as a percentage of cart price
        jQuery(".backup_rates_category_wwe_small input[value*='percentage_of_cart_price']").after('Backup rate as a percentage of Cart price. <br /><input type="text" style="margin-top: 10px" name="backup_rates_cart_price_percentage_wwe_small" id="backup_rates_cart_price_percentage_wwe_small" title="Backup Rates" maxlength="50" value="' + en_speedship_admin_script.backup_rates_cart_price_percentage_wwe_small + '"> <br> <span class="description"> Enter a percentage for the backup rate. (e.g. 10.0%)</span><br />');
        // backup rates as a function of weight
        jQuery(".backup_rates_category_wwe_small input[value*='function_of_weight']").after('Backup rate as a function of the Cart weight. <br /><input type="text" style="margin-top: 10px" name="backup_rates_weight_function_wwe_small" id="backup_rates_weight_function_wwe_small" title="Backup Rates" maxlength="50" value="' + en_speedship_admin_script.backup_rates_weight_function_wwe_small + '"> <br> <span class="description"> Enter a rate per pound to use for the backup rate. (e.g. 2.00)</span><br />');

        jQuery('#backup_rates_label_wwe_small').attr('maxlength', '50');
        jQuery('#backup_rates_fixed_rate_wwe_small, #backup_rates_cart_price_percentage_wwe_small, #backup_rates_weight_function_wwe_small').attr('maxlength', '10');
        jQuery('#backup_rates_carrier_fails_to_return_response_wwe_small, #backup_rates_carrier_returns_error_wwe_small').closest('td').css('padding', '0px 10px');

        jQuery("#backup_rates_fixed_rate_wwe_small, #backup_rates_weight_function_wwe_small").keypress(function (e) {
            if (!String.fromCharCode(e.keyCode).match(/^[0-9\d\.\s]+$/i)) return false;
        });
        jQuery("#backup_rates_cart_price_percentage_wwe_small").keypress(function (e) {
            if (!String.fromCharCode(e.keyCode).match(/^[0-9\d\.%\s]+$/i)) return false;
        });
        jQuery('#backup_rates_fixed_rate_wwe_small, #backup_rates_cart_price_percentage_wwe_small, #backup_rates_weight_function_wwe_small').keyup(function () {
            var val = jQuery(this).val();
            var regex = /\./g;
            var count = (val.match(regex) || []).length;
            
            if (count > 1) {
                val = val.replace(/\.+$/, '');
                jQuery(this).val(val);
            }
        });
    }
}

if (typeof wweSmallBackupRatesValidations != 'function') {
    function wweSmallBackupRatesValidations() {
        if (jQuery('#enable_backup_rates_wwe_small').is(':checked')) {
            let error_msg = '', field_id = '';
            if (jQuery('#backup_rates_label_wwe_small').val() == '') {
                error_msg = 'Backup rates label field is empty.';
                field_id = 'backup_rates_label_wwe_small';
            }

            const number_regex = /^([0-9]{1,4})$|(\.[0-9]{1,2})$/;
            const cart_price_regex = /^([0-9]{1,3}%?)$|(\.[0-9]{1,2})%?$/;
    
            if (!error_msg) {
                const backup_rates_type = jQuery('input[name="backup_rates_category_wwe_small"]:checked').val();
                if (backup_rates_type == 'fixed_rate' && jQuery('#backup_rates_fixed_rate_wwe_small').val() == '') {
                    error_msg = 'Backup rates as a fixed rate field is empty.';
                    field_id = 'backup_rates_fixed_rate_wwe_small';
                } else if (backup_rates_type == 'percentage_of_cart_price' && jQuery('#backup_rates_cart_price_percentage_wwe_small').val() == '') {
                    error_msg = 'Backup rates as a percentage of cart price field is empty.';
                    field_id = 'backup_rates_cart_price_percentage_wwe_small';
                } else if (backup_rates_type == 'function_of_weight' && jQuery('#backup_rates_weight_function_wwe_small').val() == '') {
                    error_msg = 'Backup rates as a function of weight field is empty.';
                    field_id = 'backup_rates_weight_function_wwe_small';
                } else if (jQuery('#backup_rates_fixed_rate_wwe_small').val() != '' && !number_regex.test(jQuery('#backup_rates_fixed_rate_wwe_small').val())) {
                    error_msg = 'Backup rates as a fixed rate format should be 100.20 or 10.';
                    field_id = 'backup_rates_fixed_rate_wwe_small';
                } else if (jQuery('#backup_rates_cart_price_percentage_wwe_small').val() != '' && !cart_price_regex.test(jQuery('#backup_rates_cart_price_percentage_wwe_small').val())) {
                    error_msg = 'Backup rates as a percentage of cart price format should be 100.20 or 10%.';
                    field_id = 'backup_rates_cart_price_percentage_wwe_small';
                } else if (jQuery('#backup_rates_weight_function_wwe_small').val() != '' && !number_regex.test(jQuery('#backup_rates_weight_function_wwe_small').val())) {
                    error_msg = 'Backup rates as a function of weight format should be 100.20 or 10.';
                    field_id = 'backup_rates_weight_function_wwe_small';
                }
            }
    
            if (error_msg) {
                jQuery('#mainform .quote_section_class_smpkg').prepend('<div id="message" class="error inline wwe_handlng_fee_error"><p><strong>Error! </strong>' + error_msg + '</p></div>');
                jQuery('html, body').animate({
                    'scrollTop': jQuery('.wwe_handlng_fee_error').position().top
                }, 100);
                jQuery('#' + field_id).css({ 'border-color': '#e81123' });
                
                return false;
            }
        }

        return true;
    }
}