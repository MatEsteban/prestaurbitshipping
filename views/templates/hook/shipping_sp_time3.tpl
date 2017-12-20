{**
* Urbit for Pretashop
*
* @author    Urb-it
* @copyright Urb-it
* @license 
*}


<script type="text/javascript">
    var sp_time_carrier_val = '{$sp_time_val|escape:'htmlall':'UTF-8'}';
    var urb_carrier_id = '{$carrier_id|escape:'htmlall':'UTF-8'}';
    var user = '{$logged_user_id|escape:'htmlall':'UTF-8'}';
    var zip_code_deliverable = '{$zip_code_deliverable|escape:'htmlall':'UTF-8'}';
    $(document).ready(function () {
        var selected = $("input[type='radio']:checked");
        if (selected.length > 0) {
            if (urb_carrier_id == selected.val()) {
                $("#urb_options").css("display", "block");

                $.ajax({
                    url: "{$base_dir|escape:'htmlall':'UTF-8'}module/urbit/ShippingOptions",
                    type: 'post',
                    data: 'ajax=true&id_data=' + 123,
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

    {* gift check box check *}

        $('#send_as_gift').click(function () {
            if ($("#send_as_gift").toggle(this.checked)) {
            }
        });

    {* processCarrier button click *}
{*        $('[name=processCarrier]').click(function () {
            $.ajax({
                url: '{$base_dir}module/urbit/UrbitCart',
                type: 'post',
                data: 'ajax=true&id_data=' + 123,
                success: function (data) {
                    var cart_options = JSON.parse(data);
                    alert(cart_options.now);
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }

            });
        });*}

    {* gray out the carrier options if zip code fails. *}
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
    {*       $(".delivery_option_radio").click(function () {
    var selected_carrier_val2 = $(this).val();

    if (selected_carrier_val2 == urb_carrier_id) {
    $("#").css("display", "block");
    } else {
    $("#urb_options").css("display", "none");
    }
    });*}

        $('#validate_zip').click(function () {
            var val = $('#user_post_code').val()
            alert(val);

        });


    });
</script>

{assign var="back_order_page" value="order.php"}
<div class="col-lg-12" id="urb_options">
    {* *** Check your postcode *** *}
    {*    <div class="box" class="col-lg-12">
    <div class="form-group">
    <div class="col-lg-2">
    <span class="label-tooltip" toggle="tooltip" data-original-title=""> Check your postcode </span>
    <input id="user_post_code" class="form-control " type="text" value="{$user.postcode|escape:'htmlall':'UTF-8'}" name="">
    </div> <br>
    <button id="validate_zip" class="btn btn-default" type="button"> <i class="icon-random"></i> Check postcode </button>
    </div>
    </div>*}

    {* *** Urbit payment options [urb now][urb sp time] *** *}
    <div class="box">
        <span id="urb_options_now"> <input type="radio" name="urb_options" id="urb_options_now" value="urb_it_now" checked> urb-it now </span> 
        <span id="msg_urb_now_not_available">urb-it now - Not available at this moment.</span>
        <br>
        <span><input type="radio" name="urb_options" value="urb_it_sp_time" id="urb_options_sp_time"> Urb-it specific time</span> 
    </div>

    {* *** Urbit sp time options [day][hour][minutes] *** *}
    <div class="col-lg-12" id="sp_time_options">
        <div class="box" class="col-lg-12">
            <p>Urb-it specific time</p>
            <select  class=" fixed-width-xl" name="">
                {foreach from=$days item=day}
                    <option value="{$day|escape:'htmlall':'UTF-8'}">{$day|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            <select class="fixed-width-xl" name="">
                {foreach from=$hours item=hour}
                    <option value="{$hour|escape:'htmlall':'UTF-8'}">{$hour|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            <select class=" fixed-width-xl" name="">
                {foreach from=$minutes item=minute}
                    <option value="{$minute|escape:'htmlall':'UTF-8'}">{$minute|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
        </div>
    </div>

    <div class="checkbox">
        <div  class="">
            <span class="checked">
                <input  type="checkbox" value="1" name="gift2">
            </span>
        </div>
        <label for=""> Do you want to send as a gift? </label>


    </div>   <br>

    <p class="address_add submit">
        <a href="{$link->getPageLink('address', true, NULL, "back={$back_order_page}?step=1{if $back}&mod={$back}{/if}")|escape:'html':'UTF-8'}" title="Add" class="button button-small btn btn-default">
            <span>Add gift address<i class="icon-chevron-right right"></i></span>
        </a>
    </p>

    <div class="row">
        <div id="center_column" class="center_column col-xs-12 col-sm-12">

            <div class="box">
                <h1 class="page-subheading">Where will we get?</h1>

                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <ul id="address_delivery" class="address item box">
                            <li class="address_title">
                                <h3 class="page-subheading">Your delivery address</h3>
                            </li>
                            <li class="address_firstname address_lastname">{$user_delivery_address.firstname|escape:'htmlall':'UTF-8'} {$user_delivery_address.lastname|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_company">{$user_delivery_address.company|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_address1">{$user_delivery_address.address1|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_address2">{$user_delivery_address.address2|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_postcode address_city">{$user_delivery_address.postcode|escape:'htmlall':'UTF-8'} {$user_delivery_address.city|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_country_name">{$user_delivery_address.country|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_phone">{$user_delivery_address.phone|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_phone_mobile">{$user_delivery_address.phone_mobile|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_update">
                                <a class="btn btn-default button button-small" href="{$link->getPageLink('address', true, null, "id_address={$user_delivery_address.id|intval}")|escape:'html':'UTF-8'}" title="Update">
                                    <span>Update
                                        <i class="icon-chevron-right right"> </i></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <ul id="address_invoice" class="address alternate_item box">
                            <li class="address_title">
                                <h3 class="page-subheading">Your billing address</h3>
                            </li>
                            <li class="address_firstname address_lastname">{$user_billing_address.firstname|escape:'htmlall':'UTF-8'} {$user_billing_address.lastname|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_company">{$user_billing_address.company|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_address1">{$user_billing_address.address1|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_address2">{$user_billing_address.address2|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_postcode address_city">{$user_billing_address.postcode|escape:'htmlall':'UTF-8'} {$user_billing_address.city|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_country_name">{$user_billing_address.country|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_phone">{$user_billing_address.phone|escape:'htmlall':'UTF-8'}</li>
                            <li class="address_phone_mobile">{$user_billing_address.phone_mobile|escape:'htmlall':'UTF-8'}</li>


                            <li class="address_update">
                                <a class="btn btn-default button button-small" href="{$link->getPageLink('address', true, null, "id_address={$user_billing_address.id|intval}")|escape:'html':'UTF-8'}" title="Update">
                                    <span>Update
                                        <i class="icon-chevron-right right"> </i></span>
                                </a>
                            </li>


                        </ul>
                    </div>
                </div>
            </div> 


        </div>
    </div>
</div> 




{* *** Check your postcode *** *}
<div id="zip_code_fail_msg">
    <p> {$zip_code_deliverable_msg|escape:'htmlall':'UTF-8'}</p><br>
</div>






<style>
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
        //        display: none;
    }
    .gray-out{
        opacity: 0.4;
        filter: alpha(opacity=40);
    }
    .dissapear{
        display: none;
    }
    #zip_code_fail_msg{
        display: none;
        color: #FF0000;
    }
</style>