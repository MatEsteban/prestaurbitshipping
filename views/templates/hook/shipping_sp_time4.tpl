
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
            	//new js
            	$(".delivery_option_logo").nextAll().eq(0).append("<span id='hp_urbit_spinner' style='float:right; font-size:25px; display:inline; margin-top:-25px !important; margin-left:200px !important;'><i class='icon-spinner icon-pulse'></i></span>");
            	// end new js
                $("#urb_options").css("display", "block");
                $("body, html").animate({ scrollTop: $("#urb_options")[0].scrollHeight }, 1000);
  
  				//new js
                $("#urb_options").ready(function (){
                	$("#hp_urbit_spinner").remove();
                });
                //end new js

                $.ajax({
                    url: "{$base_dir|escape:'htmlall':'UTF-8'}module/urbit/ShippingOptions",
                    type: 'get',
                    data: 'ajax=true',
                    success: function (data) {
                        var shipping_options = JSON.parse(data);
                        if (shipping_options.now == 'false') {
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
                alert('dddddd2');
            }
        });

    {* processCarrier button click *}
        $('[name=processCarrier]').click(function () {
            alert('clicked');
        });

    {* gray out the carrier options. *}
        if (!zip_code_deliverable) {
            $('input[type="radio"]').each(function () {
                if ($(this).val() == urb_carrier_id) {
                    $(this).closest('table').addClass('gray-out');
                    $(this).addClass('dissapear');
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

    
    {* newly added jquery functions - Hasitha Priyasad - *}
    {* shipping option check icon *}

    $('.hp_urbit_ship_p').on('click',function(){
        var icon = $(this).find('i');
        var allIcons = $('.hp_urbit_ship_p').find('i');
        $.each( allIcons, function( key, value ) {
          allIcons.removeClass("icon-check");
        });

        icon.addClass("icon-check");

        if($(this).attr('id') == "sp_time"){
            $('.hp_urbit_sp_time').stop( true, true ).slideDown('slow');
        }else{
            $('.hp_urbit_sp_time').stop( true, true ).slideUp('slow');
        }
    });

    {* discount display *}
    $("#hp_urbit_display_discount").on('click',function(){
        $('.hp_urbit_ship_discount').stop( true, true ).slideToggle( "slow");
        console.log("clicked");
    });

    $("#hp_urbit_check_box_1").on("click", function(){
        if($(this).find('i.icon').hasClass("icon-square-o")){
            $(this).find('i.icon').removeClass("icon-square-o");
            $(this).find('i.icon').addClass("icon-check-square-o");
            $(".hp_urbit_ship_send").find('input[type="text"]').val("");
        }else{
            $(this).find('i.icon').removeClass("icon-check-square-o");
            $(this).find('i.icon').addClass("icon-square-o");
        }
        $("#hp_urbit_ship_send_phone").stop(true,true).slideToggle('fast');
        $(".hp_urbit_ship_send").find('input[type="text"]#hp_urbit_ship_send_phone').val("");

    });

    });
</script>
<div id="urb_options">

<div id="hp_urbit_ship_title" class="text-center">
    <img src="/prestashop/prestashop/img/s/4.jpg"  class="img-thumbnail img-responsive center-block" />
    <hr>
</div>

<div class="row">

    <div class="col-sm-6 col-md-6 hp_urbit_ship_where_do_go">
        <h4 class="hp_urbit_ship_h4">When do you want us going?</h4>
        <p class="hp_urbit_ship_p">Now <i class="icon-check"></i></p>
        <p id="sp_time" class="hp_urbit_ship_p">Specific time <i class=""></i></p>
        <div class="row hp_urbit_sp_time">
            <div class="col-xs-6">
                 <select id="" class=" fixed-width-xl" name="">
                {foreach from=$days item=day}
                    <option value="{$day|escape:'htmlall':'UTF-8'}">{$day|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            </div>
            <div class="col-xs-3">
                <select id="" class="fixed-width-xl" name="">
                {foreach from=$hours item=hour}
                    <option value="{$hour|escape:'htmlall':'UTF-8'}">{$hour|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            </div>
            <div class="col-xs-3">
                <select id="" class=" fixed-width-xl" name="">
                {foreach from=$minutes item=minute}
                    <option value="{$minute|escape:'htmlall':'UTF-8'}">{$minute|escape:'htmlall':'UTF-8'}</option>
                {/foreach}
            </select>
            </div>     
        </div>
        <div class="hp_urbit_ship_send">
                <h4 class="hp_urbit_ship_h4">Where will we get?</h4>
                <!-- new html -->
                <label id="hp_urbit_check_box_1" class="hp_urbit_ship_blue_p" for="c1"><i class="icon-gift"></i> Do you want to send as a gift? <i class="icon icon-square-o"></i></label>
                <input type="checkbox" id="c1" name="cc" />
                <!-- end new html -->
                <div class="form-group">
                    <input type="text" class="form-control" id="hp_urbit_ship_send_address1" placeholder="Full Name">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="hp_urbit_ship_send_address2" placeholder="Street Address">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="hp_urbit_ship_send_postcode" placeholder="Zip code">
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" id="hp_urbit_ship_send_phone" placeholder="Receivers phone no">
                </div>
            </div> 
    </div>
    <div class="col-sm-6 col-md-6 hp_urbit_ship_contact">
        <h4 class="hp_urbit_ship_h4">How do we contact you?</h4>
        <div class="form-group">
            <input type="text" class="form-control" id="contact_mobile_number" placeholder="Mobile Number">
        </div>
        <div class="form-group">
            <input type="text" class="form-control" id="contact_email_address" placeholder="E mail address">
        </div>
        <div class="row">
            <div class="col-xs-12">
                <h4 class="hp_urbit_ship_h4">Would you advise your urber something?</h4>                
                <div class="form-group">
                    <textarea type="text" class="form-control" id="hp_urbit_ship_extra_msg" placeholder="Your message"></textarea> 
                </div>
            </div>
        </div>
    </div>
</div>
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

    .hp_urbit_ship_p{
        height: 40px;
        background-color: #F6F7F9;
        padding: 10px;
        color: #000;
        font-size: 15px;
        color:#7A7A7B;
        font-weight: 200;
    }

    .hp_urbit_ship_p:hover{
        cursor: pointer;
    }

    .hp_urbit_ship_p i{
        color:#5EC7D1;
        float:right;
    }

    .hp_urbit_ship_h4{
        color:#000;
    }


    .hp_urbit_ship_contact div input, .hp_urbit_ship_send input{
        height: 40px;
        background-color:#F6F7F9;
        border: none;
        font-size: 15px;
        padding: 10px;
    }

    /* new css */
    .hp_urbit_ship_blue_p{
        font-size: 15px;
        margin-top: 10px;
        margin-bottom: 10px !important;
        color:#5EC7D1;
        font-weight: 200;
    }
    /* end new css */
    
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
        border:2px solid #FDC400;
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
            border-color: #FDC400    
        }

    }    
    @keyframes borderBlink {    
        from, to {    
            border-color: transparent    
        }    
        50% {    
            border-color: #FDC400    
        }    
    }    

    label.hp_urbit_ship_blue_p{
        display: inline-block;
        width: 100% !important;
    }

    input[type=checkbox]#c1{
        display: none;
    }

}

</style>