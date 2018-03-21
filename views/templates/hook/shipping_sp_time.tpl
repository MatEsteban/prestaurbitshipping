{**
* Urbit for Pretashop
*
* @author    Urb-it
* @copyright Urb-it
* @license
*}

<script type="text/javascript">
    var urb_carrier_id = "{$carrier_id|escape:'htmlall':'UTF-8'}";
    var user = "{$logged_user_id|escape:'htmlall':'UTF-8'}";
    var zip_code_deliverable = "{$zip_code_deliverable|escape:'htmlall':'UTF-8'}";

    $(function($) {
        var radio_selected = 0;
        var ret_field_validate, ret_field_validate_ajax, del_is_gift, del_gift_receiver_phone, del_time, del_name, del_first_name, del_last_name, del_street, del_zip_code, del_city, del_contact_phone, del_contact_mail, del_advise_message, del_type;
        var firstDeliveryMinutes= "00";
        var lastDeliveryMinutes= "00";
        var nearestHour;
        var nearestMinute;
        var endHour;
        var endMinute;
        var currentdate = new Date();

        var utcDate = currentdate.getTime() + (currentdate.getTimezoneOffset() * 60000);

        currentdate = new Date(utcDate);

        var datetime = currentdate.getFullYear() + "-" +
            ((currentdate.getMonth() + 1)<10?'0':'') + (currentdate.getMonth() + 1) + "-" +
            (currentdate.getDate()<10?'0':'') + currentdate.getDate() + " "  +
            (currentdate.getHours()<10?'0':'') + currentdate.getHours() + ":" +
            (currentdate.getMinutes()<10?'0':'') + currentdate.getMinutes() + ":" +
            (currentdate.getSeconds()<10?'0':'') +  currentdate.getSeconds();

        //get nearest possible now time (now time + order preparation time + Urb-it standard process time)
        $.ajax({
            url: "{$base_url|escape:'htmlall':'UTF-8'}",
            type: 'post',
            data: {
                ajax       : true,
                nearest_possible: true,
                module     : 'urbit',
                fc         : 'module',
                controller : 'ShippingOptions'
            },
            success: function (nearest) {
              datetime = nearest;
              initCart();
            }
        });

        var newLogic = currentdate.getFullYear() + "-" + (currentdate.getMonth() + 1) + "-" + currentdate.getDate() + " " + (currentdate.getHours()) + ":" + (currentdate.getMinutes()<10?'0':'') + currentdate.getMinutes() + ":" + (currentdate.getSeconds()<10?'0':'') +  currentdate.getSeconds();

        var validate_error;
        $('[name=processCarrier]').removeClass('gray-out');

        function emptyMessage(error_id) {
            $(error_id).html("This field cannot be empty!");
            $(error_id).css("display", "block");
            $('[name=processCarrier]').addClass('gray-out');
            validate_error = 1;

            return false;
        }

        function numericMessage(error_id) {
            $(error_id).html("This value is not valid!");
            $(error_id).css("display", "block");
            $('[name=processCarrier]').addClass('gray-out');
            validate_error = 1;

            return false;
        }

        function phoneValidationErrorMessage(error_id) {
            $(error_id).html("Invalid mobile number. Please, set phone number with country code");
            $(error_id).css("display", "block");
            $('[name=processCarrier]').addClass('gray-out');
            validate_error = 1;

            return false;
        }

        function noScpDateSelect() {
            $("#del_spdate_error").html("Please Select Date and Time!");
            $('[name=processCarrier]').addClass('gray-out');
            validate_error = 1;

            return false;
        }

        function apiErrorMessage(error_class, error_id, error_message) {
            $(error_class).css("display", "block");
            $(error_id).html(error_message);
            $('[name=processCarrier]').addClass('gray-out');
            validate_error = 1;

            return false;
        }

        function addressValidationError(show) {
            if (show) {
                $('#hp_urbit_address_validation_error').css("display", "block");
            } else {
                $('#hp_urbit_address_validation_error').css("display", "none");
            }
        }

        function phoneNumberValidation(value, error_empty_id, error_format_id) {
            if (value == "") {
                emptyMessage(error_empty_id);
            } else if (!value.match(/^[+][0-9]/)){
                phoneValidationErrorMessage(error_format_id);
            }
        }
       

        function fieldValidation() {
            validate_error = 0;

            $(".hp_urbit_validation_error").css("display", "none");

            var requiredInputs = [
                    { input_id:"#hp_urbit_del_first_name", error_id:"#del_first_name_error" },
                    { input_id:"#hp_urbit_del_last_name", error_id:"#del_last_name_error" },
                    { input_id:"#hp_urbit_del_street", error_id:"#del_street_error" },
                    { input_id:"#hp_urbit_del_city", error_id:"#del_city_error" },
                    { input_id:"#hp_urbit_del_postcode", error_id:"#del_zip_error" },
                    { input_id:"#contact_mobile_number", error_id:"#del_contact_mobile_number_error" },
                    { input_id:"#contact_email_address", error_id:"#del_contact_email_address_error" }
            ];

            $.each(requiredInputs, function(key, value ) {
                if ($(value.input_id).val() == "") {
                    emptyMessage(value.error_id);
                }
            });

            if ($("#urb_options_now i").hasClass('icon-check')) {
                del_time = datetime;

            } else if ($("#sp_time i").hasClass('icon-check')) {
                var spDate = $("#sp_time_date").val();
                var spHour = $("#sp_time_hour").val();
                var spMinute = $("#sp_time_minute").val();

                if (spDate == "" || spHour == "" || spMinute == "") {
                    return noScpDateSelect();
                }

                del_time = spDate + " " + spHour + ":" + spMinute + ":00";
            }

            //validate phone numbers
            del_contact_phone = $("#contact_mobile_number").val();
            phoneNumberValidation(del_contact_phone, "#del_gift_phone_error", "#del_contact_mobile_number_error" )

            if ($("#hp_urbit_check_box_1 i").hasClass('icon-check-square-o')) {
                del_gift_receiver_phone = $("#hp_urbit_del_phone").val();
                phoneNumberValidation(del_gift_receiver_phone, "#del_gift_phone_error", "#del_gift_phone_format_error" )
            }

            //validate zipcode
            if (!$.isNumeric($("#hp_urbit_del_postcode").val())){
                numericMessage("#del_zip_error");
            }

            if (validate_error == 1) {
                if (!$("[name=processCarrier]").hasClass('gray-out')) {
                    $('[name=processCarrier]').addClass('gray-out');
                }
            }

            return validate_error !== 1;
        }

        function fieldValidationAjax() {
            del_is_gift = 0;
            del_gift_receiver_phone = "";

            if (!window.__fieldValidationAjax_Flag) {
                window.__fieldValidationAjax_Flag = 1;
            }

            if ($("#urb_options_now i").hasClass('icon-check')) {
                del_time = datetime;
                del_type = 'OneHour';
            }

            if ($("#sp_time i").hasClass('icon-check')) {
                del_time = $("#sp_time_date").val() + " " + $("#sp_time_hour").val() + ":" + $("#sp_time_minute").val() + ":00";
                del_type = 'Specific';
            }

            if ($("#hp_urbit_check_box_1 i").hasClass('icon-check-square-o')) {
                del_is_gift = 1;
                del_gift_receiver_phone = $("#hp_urbit_del_phone").val();
            }

            del_name = $("#hp_urbit_del_name").val();
            del_first_name = $("#hp_urbit_del_first_name").val();
            del_last_name = $("#hp_urbit_del_last_name").val();
            del_street = $("#hp_urbit_del_street").val();
            del_zip_code = $("#hp_urbit_del_postcode").val();
            del_city = $("#hp_urbit_del_city").val();
            del_contact_phone = $("#contact_mobile_number").val();
            del_contact_mail = $("#contact_email_address").val();
            del_advise_message = $("#hp_urbit_ship_extra_msg").val();

            window.__fieldValidationAjax_Flag = Math.random();

            var local__fieldValidationAjax_Flag = window.__fieldValidationAjax_Flag;

            $.ajax({
                url: "{$base_url|escape:'htmlall':'UTF-8'}",
                type: 'post',
                data: {
                    ajax       : true,
                    validate_delivery : 1,
                    del_is_gift : del_is_gift,
                    del_gift_receiver_phone : del_gift_receiver_phone,
                    del_time : del_time,
                    del_name : del_name,
                    del_first_name : del_first_name,
                    del_last_name : del_last_name,
                    del_street : del_street,
                    del_zip_code : del_zip_code,
                    del_city : del_city,
                    del_contact_phone : del_contact_phone,
                    del_contact_mail : del_contact_mail,
                    del_advise_message : del_advise_message,
                    del_type : del_type,
                    module     : 'urbit',
                    fc         : 'module',
                    controller : 'ShippingOptions'
                },

                success: function (data) {
                    if (window.__fieldValidationAjax_Flag !== local__fieldValidationAjax_Flag) {
                        return;
                    }

                    var validate_delivery = JSON.parse(data);

                    if (validate_delivery.possible_hours == true && validate_delivery.ajaxCheckValidateDelivery == "true") {
                        validate_error = 0;

                        addressValidationError(false);

                        if ($("[name=processCarrier]").hasClass('gray-out')) {
                            $('[name=processCarrier]').removeClass('gray-out');
                        }
                    } else if (validate_delivery.ajaxCheckValidateDelivery == "false") {
                        if (!$("[name=processCarrier]").hasClass('gray-out')) {
                            $('[name=processCarrier]').addClass('gray-out');
                        }

                        addressValidationError(true);
                    } else {
                        $('[name=processCarrier]').addClass('gray-out');
                        //shop specific
                        if ($("#urb_options_now i").hasClass('icon-check')) {

                            $('#urb_options_now').addClass('gray-out');
                            $('#urb_options_now i').removeClass('icon-check');
                            $('#sp_time i').addClass('icon-check');
                            $('.hp_urbit_sp_time').stop(true, true).slideDown('slow');

                            getdeliveryDates();

                        }
                    }
                },
                error: function (errorThrown) {
                    if (window.__fieldValidationAjax_Flag !== local__fieldValidationAjax_Flag) {
                        return;
                    }

                    console.log(errorThrown);
                }
            });

            return true;
        }

        function initCart() {
            var selected = $("input[type='radio']:checked");

            if (selected.length > 0) {
                if (urb_carrier_id == selected.val()) {
                    $(".delivery_option_logo").nextAll().eq(0).append("<span id='hp_urbit_spinner' style='float:right; font-size:25px; display:inline; margin-top:-25px !important; margin-left:200px !important;'><i class='icon-spinner icon-pulse'></i></span>");
                    $("#urb_options").css("display", "block");

                    $("#hp_urbit_del_first_name").focus();
                    $("body, html").animate({
                        scrollTop: $("#urb_options")[0].scrollHeight
                    }, 1000);
                    $("#urb_options").ready(function () {
                        $("#hp_urbit_spinner").remove();
                    });

                    if (fieldValidation()){
                        fieldValidationAjax();
                    }

                    $.ajax({
                        url: "{$base_url|escape:'htmlall':'UTF-8'}",
                        type: 'post',
                        data: {
                            ajax       : true,
                            id_data : 123,
                            del_is_gift : del_is_gift,
                            del_gift_receiver_phone : del_gift_receiver_phone,
                            del_time : del_time,
                            del_name : del_name,
                            del_first_name : del_first_name,
                            del_last_name : del_last_name,
                            del_street : del_street,
                            del_city : del_city,
                            del_zip_code : del_zip_code,
                            del_contact_phone : del_contact_phone,
                            del_contact_mail : del_contact_mail,
                            del_advise_message : del_advise_message,
                            del_type : del_type,
                            module     : 'urbit',
                            fc         : 'module',
                            controller : 'ShippingOptions'
                        },

                        success: function (data) {
                            var shipping_options = JSON.parse(data);
                            if (shipping_options.store_available_now == 'false') {
                                $("#urb_options_now").css("display", "none");
                                $("#msg_urb_now_not_available").css("display", "block");
                            }
                        },
                        error: function (errorThrown) {
                            console.log(errorThrown);
                        }
                    });
                }
            }
        }

        $(".urbit_del_validate").on('input', function () {

            radio_selected = $("input[type='radio']:checked");

            if (urb_carrier_id == radio_selected.val()) {
                if (!$("[name=processCarrier]").hasClass('gray-out')) {
                    $('[name=processCarrier]').addClass('gray-out');
                }
            }

            if (fieldValidation()) {
                fieldValidationAjax();
            }
        });

        $("#sp_time_minute").change(function () {
            if (fieldValidation()) {
                fieldValidationAjax();
            }
        });

        $("#sp_time_date").change(function () {
            var min_val = $("#sp_time_minute").val();

            if(min_val) {
                if (fieldValidation()) {
                    fieldValidationAjax();
                }
            }
        });

        $("#sp_time_hour").change(function () {
            var min_val = $("#sp_time_minute").val();

            if(min_val) {
                if (fieldValidation()) {
                    fieldValidationAjax();
                }
            }
        });

        // *** Do you want to send as a gift? click function ****
        $("#hp_urbit_check_box_1").on("click", function () {

            if ($(this).find('i.icon').hasClass("icon-square-o")) {
                $(this).find('i.icon').removeClass("icon-square-o");
                $(this).find('i.icon').addClass("icon-check-square-o");
                $(".hp_urbit_ship_send").find('input[type="text"]').val("");
                if (del_gift_receiver_phone == "") {
                    $('[name=processCarrier]').addClass('gray-out');
                    validate_error = 1;
                }
            } else {
                $(this).find('i.icon').removeClass("icon-check-square-o");
                $(this).find('i.icon').addClass("icon-square-o");
                del_name = $("#hp_urbit_del_name").val("{$user_delivery_address.firstname|escape:'htmlall':'UTF-8'} {$user_delivery_address.lastname|escape:'htmlall':'UTF-8'}");
                del_first_name = $("#hp_urbit_del_first_name").val("{$user_delivery_address.firstname|escape:'htmlall':'UTF-8'}");
                del_last_name = $("#hp_urbit_del_last_name").val("{$user_delivery_address.lastname|escape:'htmlall':'UTF-8'}");
                del_street = $("#hp_urbit_del_street").val("{$user_delivery_address.address1|escape:'htmlall':'UTF-8'}");
                del_city = $("#hp_urbit_del_city").val("{$user_delivery_address.city|escape:'htmlall':'UTF-8'}");
                del_zip_code = $("#hp_urbit_del_postcode").val({$user_delivery_address.postcode|escape:'htmlall':'UTF-8'});
                            }
                            $("#hp_urbit_del_phone").stop(true, true).slideToggle('fast');

            if (fieldValidation()) {
                fieldValidationAjax();
            }
        });

        $('[name=processCarrier]').click(function (e) {

            radio_selected = $("input[type='radio']:checked");

            if (urb_carrier_id == radio_selected.val()) {

                if (fieldValidation()){
                    fieldValidationAjax();
                }

                if (validate_error == 1) {
                    e.preventDefault();
                }

                updateCart();
            }
        });

        function updateCart()
        {
            radio_selected = $("input[type='radio']:checked");

            var mobile = $("#contact_mobile_number").val();

            if (!mobile.match(/^[+][0-9]/)) {
                $("#mobile_no_error").css("display", "block");
                $("#mobile_no_error").html("{l s='Invalid Mobile Number' mod='urbit'}");
            } else {
                $("#mobile_no_error").css("display", "none");
                $("#mobile_no_error").html("");
            }

            if (urb_carrier_id == radio_selected.val()) {
                if (validate_error==1) {
                }
                del_is_gift = 0;
                del_gift_receiver_phone = "";

                if ($("#urb_options_now i").hasClass('icon-check')) {
                    del_time = datetime;
                    del_type = 'OneHour';
                } else if ($("#sp_time i").hasClass('icon-check')) {
                    del_time = $("#sp_time_date").val() + " " + $("#sp_time_hour").val() + ":" + $("#sp_time_minute").val() + ":00";
                    del_type = 'Specific';
                }

                if ($("#hp_urbit_check_box_1 i").hasClass('icon-check-square-o')) {
                    del_is_gift = 1;
                    del_gift_receiver_phone = $("#hp_urbit_del_phone").val();
                }

                del_name = $("#hp_urbit_del_name").val();
                del_first_name = $("#hp_urbit_del_first_name").val();
                del_last_name = $("#hp_urbit_del_last_name").val();
                del_street = $("#hp_urbit_del_street").val();
                del_city = $("#hp_urbit_del_city").val();
                del_zip_code = $("#hp_urbit_del_postcode").val();
                del_contact_phone = $("#contact_mobile_number").val();
                del_contact_mail = $("#contact_email_address").val();
                del_advise_message = $("#hp_urbit_ship_extra_msg").val();

                $.ajax({
                    url: "{$base_url|escape:'htmlall':'UTF-8'}",
                    type: 'post',
                    data: {
                        ajax       : true,
                        process_carrier : 1,
                        del_is_gift : del_is_gift,
                        del_gift_receiver_phone : del_gift_receiver_phone,
                        del_time : del_time,
                        del_name : del_name,
                        del_first_name : del_first_name,
                        del_last_name : del_last_name,
                        del_street : del_street,
                        del_city : del_city,
                        del_zip_code : del_zip_code,
                        del_contact_phone : del_contact_phone,
                        del_contact_mail : del_contact_mail,
                        del_advise_message : del_advise_message,
                        del_type : del_type,
                        module     : 'urbit',
                        fc         : 'module',
                        controller : 'ShippingOptions'
                    },

                    success: function (data) {

                    },
                    error: function (errorThrown) {

                    }
                });
            }
        };

        $('input[type="radio"]:checked').each(function () {
            if ($(this).val() == 'urb_it_sp_time') {
                $("#sp_time_options").css("display", "block");
            }
        });

        $("#urb_options_sp_time").click(function () {
            $('input[type="radio"]:checked').each(function () {
                if ($(this).val() == 'urb_it_sp_time') {
                    $("#sp_time_options").css("display", "block");
                } else {
                    $("#sp_time_options").css("display", "none");
                }
            });
        });

        $("#urb_options_now").click(function () {
            $('input[type="radio"]:checked').each(function () {
                if ($(this).val() == 'urb_it_sp_time') {
                    $("#sp_time_options").css("display", "block");
                } else {
                    $("#sp_time_options").css("display", "none");
                }
            });
        });

        // gray out the carrier options if zip code fails.
        if (!zip_code_deliverable) {
            $('input[type="radio"]').each(function () {
                if ($(this).val() == urb_carrier_id) {
                    $(this).closest('table').addClass('gray-out');
                    $(this).addClass('dissapear');
                    $("#zip_code_fail_msg").css("display", "block");
                    $("#urb_options").css("display", "none");
                }
            });
        }

        //**** shipping option check icon *******
        $('.hp_urbit_ship_p').on('click', function () {
            var icon = $(this).find('i');
            var allIcons = $('.hp_urbit_ship_p').find('i');
            $.each(allIcons, function (key, value) {
                allIcons.removeClass("icon-check");
            });
            icon.addClass("icon-check");
            if ($(this).attr('id') == "sp_time") {
                $('.hp_urbit_sp_time').stop(true, true).slideDown('slow');
            } else {
                $('.hp_urbit_sp_time').stop(true, true).slideUp('slow');
            }

            if (fieldValidation()) {
                fieldValidationAjax();
            }
        });

        //***** discount display *****
        $("#hp_urbit_display_discount").on('click', function () {
            $('.hp_urbit_ship_discount').stop(true, true).slideToggle("slow");
        });

        /*get time for the date*/
        $('#sp_time_date').change(function () {
            var selectDate = $(this).val();
            var d = new Date();
            var today =  d.toISOString().substring(0, 10);
            var nowTime = d.getHours();

            var $hour = $('#sp_time_hour').html("").prop("disabled", true);
            var $minute = $('#sp_time_minute').html("").prop("disabled", true);

            $.ajax({
                url: "{$base_url|escape:'htmlall':'UTF-8'}",
                type: 'post',
                data: {
                    ajax       : true,
                    selectDate : selectDate,
                    module     : 'urbit',
                    fc         : 'module',
                    controller : 'ShippingOptions'
                },

                success: function (data) {
                    if(data) {
                        var deliveryTimeInfo = JSON.parse(data);

                        if (deliveryTimeInfo.hasOwnProperty("status") && deliveryTimeInfo.status == false) {
                            return;
                        }

                        firstDeliveryMinutes = deliveryTimeInfo.minutes.start;
                        lastDeliveryMinutes = deliveryTimeInfo.minutes.end;

                        nearestHour = deliveryTimeInfo.nearestHour;
                        nearestMinute = deliveryTimeInfo.nearestMinute;
                        endHour = deliveryTimeInfo.endHour;
                        endMinute = deliveryTimeInfo.endMinute;

                        var open_hours = deliveryTimeInfo.hours;
                        var options = '<option value="">HH</option>';

                        if (selectDate == today) {
                            for (var x = 0; x < open_hours.length; x++) {
                                if (nearestHour <= open_hours[x]) {
                                    options += '<option value="' + open_hours[x] + '">' + open_hours[x] + '</option>';
                                }
                            }
                        } else {
                            for (var x = 0; x < open_hours.length; x++) {
                                options += '<option value="' + open_hours[x] + '">' + open_hours[x] + '</option>';
                            }
                        }

                        $hour.prop( "disabled", false ).html(options);
                        $minute.prop( "disabled", false );
                    }
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
        });

        /*get the minutes*/
        $("#sp_time_hour").change(function() {
            var selectHour = $(this).val();
            var selectDate = $("#sp_time_date").val();
            var someDate = new Date(selectDate).toDateString();
            var today = new Date().toDateString();
            var minutes = ["00", "05", "10", "15", "20", "25", "30", "35", "40", "45", "50", "55"];
            var openMin = '<option value="">MM</option>';
            var selectedHourIndex = $("#sp_time_hour option:selected").index();
            var hoursOptionsCount = $('#sp_time_hour option').size();

            if((today == someDate) && (parseInt(selectHour) == parseInt(nearestHour))) {

                if (parseInt(nearestHour) == parseInt(endHour)) {
                    for (var min = 0; min < minutes.length; min++) {
                        if (endMinute >= minutes[min]) {
                            openMin += '<option value="' + minutes[min] + '">' + minutes[min] + '</option>';
                        }
                    }
                } else {
                    for (var min = 0; min < minutes.length; min++) {
                        if (parseInt(nearestMinute) <= parseInt(minutes[min])) {
                            openMin += '<option value="' + minutes[min] + '">' + minutes[min] + '</option>';
                        }
                    }
                }
            } else if (selectedHourIndex == 1 && hoursOptionsCount > 2) {
                var fDeliveryMin = parseInt(firstDeliveryMinutes);

                for (var min = 0; min < minutes.length; min++) {
                    if (parseInt(minutes[min]) >= fDeliveryMin) {
                        openMin += '<option value="' + minutes[min] + '">' + minutes[min] + '</option>';
                    }
                }
            } else if (selectedHourIndex == (hoursOptionsCount - 1) ) {
                var fDeliveryMin = parseInt(lastDeliveryMinutes);

                for (var min = 0; min < minutes.length; min++) {
                    if (parseInt(minutes[min]) <= fDeliveryMin) {
                        openMin += '<option value="' + minutes[min] + '">' + minutes[min] + '</option>';
                    }
                }
            } else {
                for(var min = 0; min < minutes.length; min++) {
                    openMin += '<option value="'+ minutes[min] +'">'+ minutes[min] +'</option>';
                }
            }
            $('#sp_time_minute').html(openMin);
        });


        $("#sp_time").click(function() {
            getdeliveryDates();
        });

        /*get the dates*/
        function getdeliveryDates() {
            var d = new Date();
            var weekday = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

            $.ajax({
                url: "{$base_url|escape:'htmlall':'UTF-8'}",
                type: 'post',
                data: {
                    ajax       : true,
                    selectOffTime : 'off',
                    nowTime     :   newLogic,
                    module     : 'urbit',
                    fc         : 'module',
                    controller : 'ShippingOptions'
                },

                 success: function (data) {
                     if(data) {
                         var open_dates = $.parseJSON(data);
                         var options = '<option value="">Select Date</option>';
                         for (var x = 0; x < open_dates.length; x++) {
                            var days = new Date(open_dates[x]);
                            options += '<option value="' + open_dates[x] + '">' + weekday[days.getDay()] + '</option>';
                         }
                         $('#sp_time_date').html(options);
                         $("#sp_time_date").prop("disabled", false);
                     }
                 }
            });
        }
    });
</script>

<div id="urb_options">

    <div id="hp_urbit_ship_title" class="text-center">
        <img src="{$carrier_img_id|escape:'htmlall':'UTF-8'}"  class=" img-responsive center-block" />
    </div>
    <div class="row">
        <div class="col-sm-6 col-md-6 hp_urbit_ship_where_do_go">
            <h4 class="hp_urbit_ship_h4 hp_ub_now">{l s='When would you like your purchase?' mod='urbit'}</h4>
            <p  class="hp_urbit_validation_error" id="del_time_error"></p>
            <p class="hp_urbit_ship_p" id="urb_options_now">{l s='Now' mod='urbit'} <i class="icon-check"></i></p>
            <p id="sp_time" class="hp_urbit_ship_p">{l s='Specific time (CET)' mod='urbit'} <i class=""></i></p>
            <div class="row hp_urbit_sp_time">
                <p class="hp_urbit_validation_error" style="padding-left: 15px;" id="del_spdate_error"></p>
                <div class="col-xs-6">
                    <select class="fixed-width-xl" id="sp_time_date" disabled>
                        <option value="">Select Date</option>
                    </select>
                </div>
                <div class="col-xs-3">
                    <select class="fixed-width-xl" id="sp_time_hour" disabled>
                        <option value="">HH</option>
                    </select>
                </div>
                <div class="col-xs-3">
                    <select class=" fixed-width-xl" id="sp_time_minute" disabled>
                        <option value="">MM</option>
                    </select>
                </div>
            </div>
            <div class="hp_urbit_ship_send">
                <h4 class="hp_urbit_ship_h4">{l s='Where would you like to recieve your purchase?' mod='urbit'}</h4>
                <label id="hp_urbit_check_box_1" class="hp_urbit_ship_blue_p" for="c1"><i style="padding-right: 10px;" class="icon-gift"></i>{l s='Would you like to send your purchase as a gift?' mod='urbit'} <i class="icon icon-square-o"></i></label>
                <input type="checkbox" id="c1" name="cc" />

                <p class="hp_urbit_validation_error" id="del_first_name_error"></p>
                <div class="form-group">
                    <input type="text" class="form-control urbit_del_validate" id="hp_urbit_del_first_name"
                           placeholder="{l s='First Name' mod='urbit'}"
                           value="{$user_delivery_address.firstname|escape:'htmlall':'UTF-8'}">
                </div>

                <p class="hp_urbit_validation_error" id="del_last_name_error"></p>
                <div class="form-group">
                    <input type="text" class="form-control urbit_del_validate" id="hp_urbit_del_last_name"
                           placeholder="{l s='Last Name' mod='urbit'}"
                           value="{$user_delivery_address.lastname|escape:'htmlall':'UTF-8'}">
                </div>

                <p class="hp_urbit_validation_error" id="del_street_error"></p>
                <div class="form-group">
                    <input type="text" class="form-control urbit_del_validate" id="hp_urbit_del_street"
                           placeholder="{l s='Address' mod='urbit'}"
                           value="{$user_delivery_address.address1|escape:'htmlall':'UTF-8'}">
                </div>

                <p class="hp_urbit_validation_error" id="del_zip_error"></p>
                <div class="form-group">
                    <input type="text" class="form-control urbit_del_validate" id="hp_urbit_del_postcode"
                           placeholder="{l s='Postal Code' mod='urbit'}"
                           value="{$user_delivery_address.postcode|escape:'htmlall':'UTF-8'}">
                </div>
                <p class="hp_urbit_validation_error" id="del_city_error"></p>
                <div class="form-group">
                    <input type="text" class="form-control urbit_del_validate" id="hp_urbit_del_city"
                           placeholder="{l s='City' mod='urbit'}"
                           value="{$user_delivery_address.city|escape:'htmlall':'UTF-8'}">
                </div>
                <p class="hp_urbit_validation_error" id="del_gift_phone_error"></p>
                <p class="hp_urbit_validation_error" id="del_gift_phone_format_error"></p>
                <div class="form-group">
                    <input type="text" class="form-control urbit_del_validate" id="hp_urbit_del_phone"
                           placeholder="{l s='Recipient\'s mobile number' mod='urbit'}">
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-md-6 hp_urbit_ship_contact">
            <h4 class="hp_urbit_ship_h4 mobile_title">{l s='How can we best get in touch with you?' mod='urbit'}</h4>
            <p class=" hp_urbit_validation_error" id="del_contact_mobile_number_error" style=""></p>
            <p class="hp_urbit_validation_error" id="del_contact_mobile_number_format_error"></p>
            <div class="form-group">
                <input type="text" class="form-control urbit_del_validate" id="contact_mobile_number"
                       placeholder="{l s='Please enter International Format ex: +336XXXXXXXXX' mod='urbit'}"
                       value="{$user_delivery_address.phone|escape:'htmlall':'UTF-8'}"  required>
            </div>

            <p class="hp_urbit_validation_error" id="del_contact_email_address_error"></p>
            <div class="form-group">
                <input type="email" class="form-control urbit_del_validate" id="contact_email_address"
                       placeholder="{l s='Email' mod='urbit'}"
                       value="{$user_email|escape:'htmlall':'UTF-8'}" required>
            </div>

            <div class="row">
                <div class="col-xs-12">
                    <h4 class="hp_urbit_ship_h4">{l s='Do you have a message for your urber?' mod='urbit'}</h4>
                    <div class="form-group">
                        <textarea class="form-control urbit_del_validate" id="hp_urbit_ship_extra_msg"
                                  placeholder="{l s='Your message' mod='urbit'}"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="hp_urbit_validation_error_message" id="hp_urbit_address_validation_error">
        <p>Malformed address / Address outside the delivery area</p>
    </div>
</div>

<style>
    #hp_urbit_address_validation_error {
        display: none;
        color:#F13340;
        font-size: 16px;
        padding:5px 0px;
    }

    #urb_options{
        display: none;
    }
    #msg_urb_now_not_available{
        display: none;

    }
    #sp_time_options{
        display: none;
    }
    .hook_extracarrier{
        // display: none;
    }
    .gray-out{
        opacity: 0.4;
        filter: alpha(opacity=40);
        cursor: default;
        pointer-events: none;
    }
    .dissapear{
        display: none;
    }

    .hp_urbit_ship_p{
        height: 40px;
        background-color: #F6F7F9;
        padding: 10px;
        color: #000;
        font-size: 15px;
        color:#7A7A7B;
        font-weight: 200;
        margin-bottom: 15px !important;
    }

    .hp_urbit_ship_p:hover{
        cursor: pointer;
    }

    .hp_urbit_ship_p i{
        color:#5EC7D1;
        float:right;
    }

    .hp_urbit_ship_p#sp_time{
        margin-bottom: 0px !important;
    }

    .hp_urbit_ship_h4{
        margin: 9px 0 20px 0;
        color:#000;
    }
    .hp_ub_now.hp_urbit_ship_h4 { margin-bottom: 0; }
    .hp_urbit_ship_send { margin-top: 25px; }

    #hp_urbit_ship_title {
        margin: 10px 0 20px 0;
    }
    #hp_urbit_ship_title img{
        margin: 0 auto;
    }

    .hp_urbit_ship_contact div input, .hp_urbit_ship_send input{
        height: 40px;
        background-color:#F6F7F9;
        border: none;
        font-size: 15px;
        padding: 10px;
    }

    .hp_urbit_ship_blue_p{
        font-size: 15px;
        /*margin-top: 10px;*/
        margin-bottom: 10px !important;
        color:#5EC7D1;
        font-weight: 200;
    }

    .hp_urbit_ship_blue_p:hover{
        cursor: pointer;
    }

    .hp_urbit_ship_where_do_go select{
        height: 40px;
        background-color:#F6F7F9;
        border: none;
        font-size: 15px;
        padding: 10px;
        width: 100%;
        margin-top:10px;
    }

    .hp_urbit_sp_time{
        display: none;
    }
    .hp_urbit_ship_h4.mobile_title { margin-bottom: 0; }

    .hp_urbit_ship_discount{
        display: none;
    }

    .hp_urbit_ship_discount input[type="button"]{
        width: 100%;
        margin-bottom: 10px;
        color: #fff;
        border-radius: 0px;
    }

    .hp_urbit_ship_discount input[value="Cancel"]{
        background-color: #B1B7BE;
    }
    .hp_urbit_ship_discount input[value="Continue"]{
        background-color: #373D49;
    }

    .icon{
        font-size: 20px;
        float: right;
    }

    #urb_options{
        border:2px solid #2c2e2f;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        -webkit-animation: borderBlink 1s linear 5;
        animation: borderBlink 1s linear 5;
    }

    #hp_urbit_ship_title p{
        font-size: 1.5em;
        margin-top: 5px;
        font-weight: bold;
    }

    #hp_urbit_ship_title hr{
        border-color: #FDC400;
    }

    #hp_urbit_ship_send_phone{
        display: none;
    }

    #hp_urbit_ship_extra_msg{
        max-height: 100px;
        max-width: 100%;
        min-height: 100px;
        min-width: 100%;
        background-color: #F6F7F9;
        border:none;
        font-size: 15px;
        padding: 10px;
    }

    @-webkit-keyframes borderBlink {
        from, to {
            border-color: transparent
        }
        50% {
            border-color: #2c2e2f

        }

    }
    @keyframes borderBlink {
        from, to {
            border-color: transparent
        }
        50% {
            border-color: #2c2e2f
        }
    }

    label.hp_urbit_ship_blue_p{
        display: inline-block;
        width: 100% !important;
    }

    input[type=checkbox]#c1{
        display: none;
    }

    .hp_urbit_validation_error {
        color:#F13340;
        line-height: 12px;
        font-size: 10px;
        height: 20px;
        padding:4px 0px;
    }

    #hp_urbit_del_phone{
        display: none;
    }
    .hp_urbit_ship_send .form-group {
        margin-bottom: 5px;
    }

    #urb_options_now, #sp_time, #hp_urbit_del_first_name,
     #hp_urbit_del_last_name, #hp_urbit_del_postcode,
     #hp_urbit_del_citym #hp_urbit_del_city, #hp_urbit_del_city,
     #contact_mobile_number,#contact_email_address,
     #hp_urbit_ship_extra_msg, #hp_urbit_del_street,
      #sp_time_date, #sp_time_hour, #sp_time_minute{
      background-color: #fff;
    }
</style>
