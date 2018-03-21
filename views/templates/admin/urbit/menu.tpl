{**
* Urbit for Pretashop
*
* @author    Urb-it
* @copyright Urb-it
* @license
*}

<style>
    .test_api{
        float: right;
        height: 40px;
        width: 100px;
        background-color:#FDC400;
        border:none;
        color: #ffffff;
        border-radius: 5px;
        margin-right: 10px;
    }

    .fail2 {
        background-color: #FFF3D7;
        border-color: #D2A63C;
        color: #D2A63C;
    }
    #URBIT_ADMIN_EMAIL {
        background-color: #fff;
        border: 1px solid #ccc;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1) inset;
        padding: 5px 20px;
    }
</style>

<script>
    $(function() {
        $( "#tabs" ).tabs();
    });

</script>
<script>
    $(document).ready(function(){

        urbit_retrive_data();

        var moduleSelectWdth = $(".hp_urbit_module select").outerWidth();
        $(".hp_urbit_offer_sp_time select").outerWidth(moduleSelectWdth);

        // urbit_retrive_data();

        //triger click function in UI
        $( "#save_general_data" ).click(function() {
            urbit_data_update();
        });

        function urbit_retrive_data(){
            var query = $.ajax({
                type: 'POST',
                url:  "{$base_url|escape:'htmlall':'UTF-8'}ajax.php",
                data:'ajax=true&mod=get_default_data',
                dataType: 'json',
                success: function(returnval) {
                     if(returnval=="success"){
                        $("#alert").show();
                        $("#alert").hide(5000);
                    }else{
                         set_variables(returnval);
                     }
                }
            });
        }

        function urbit_data_update(){
            var data = $( "#tab1_frm" ).serialize();
            var query = $.ajax({
                type: 'POST',
                url:  "{$base_url|escape:'htmlall':'UTF-8'}ajax.php",
                //url: "{$ajax_url|escape:'htmlall':'UTF-8'}module/urbit/ShippingOptions",
                data: 'ajax=true&' + data ,
               // data:$( "#tab1_frm" ).serialize(),
                dataType: 'json',
                success: function(returnval) {
                    if(returnval=="success"){
                        $("#alert").show();
                        $("#alert").hide(5000);
                    }
                }
            });
        }

        $( "#save_credentials_data" ).click(function() {
            save_credentials_data();
        });

        function save_credentials_data(){
            var data = $( "#api_credntials" ).serialize();
            var query = $.ajax({
                type: 'POST',
                url:  "{$base_url|escape:'htmlall':'UTF-8'}ajax.php",
                data: 'ajax=true&' + data ,
                dataType: 'json',
                success: function(returnval) {
                    set_variables(returnval);

			        if(returnval['status']=='success') {
                        $("#alert2").show();
                        $("#alert2").hide(5000);
                    } else {
                         $("#alert_fail").show();
                         $("#alert_fail").hide(5000);
                    }

                    urbit_retrive_data();
                }
            });
        }


        function set_variables(returnval){
            if(returnval.URBIT_API_CUSTOMER_KEY != false ){
                $("#URBIT_API_CUSTOMER_KEY").val(returnval.URBIT_API_CUSTOMER_KEY);
            }else{
                $("#URBIT_API_CUSTOMER_KEY").val("");
            }
            if(returnval.URBIT_API_TEST_CUSTOMER_KEY !=false){
                $("#URBIT_API_TEST_CUSTOMER_KEY").val(returnval.URBIT_API_TEST_CUSTOMER_KEY);
            }else{
                $("#URBIT_API_TEST_CUSTOMER_KEY").val("");
            }

            if(returnval.URBIT_API_TEST_BEARER_JWT_TOKEN !=false){
                $("#URBIT_API_TEST_BEARER_JWT_TOKEN").val(returnval.URBIT_API_TEST_BEARER_JWT_TOKEN);
            }else{
                $("#URBIT_API_TEST_BEARER_JWT_TOKEN").val("");
            }

            if(returnval.URBIT_API_URL !=false){
                $("#URBIT_API_URL").val(returnval.URBIT_API_URL);
            }else{
                $("#URBIT_API_URL").val("");
            }

            if(returnval.URBIT_API_BEARER_JWT_TOKEN !=false){
                $("#URBIT_API_BEARER_JWT_TOKEN").val(returnval.URBIT_API_BEARER_JWT_TOKEN);
            }else{
                $("#URBIT_API_BEARER_JWT_TOKEN").val("");
            }

            if(returnval.URBIT_API_TEST_URL != false){
                $("#URBIT_API_TEST_URL").val(returnval.URBIT_API_TEST_URL);
              }else{
                $("#URBIT_API_TEST_URL").val("");
            }

            if(returnval.URBIT_ENABLE_TEST !=false){
                $("#URBIT_ENABLE_TEST").val(returnval.URBIT_ENABLE_TEST);
            }else{
                $("#URBIT_ENABLE_TEST").val("");
            }

            if(returnval.URBIT_SEND_FAILIOR_REPORT != false){
                $("#URBIT_SEND_FAILIOR_REPORT").val(returnval.URBIT_SEND_FAILIOR_REPORT);
            }else{
                $("#URBIT_SEND_FAILIOR_REPORT").val("");
            }

            if(returnval.URBIT_ADMIN_EMAIL != false ){
                $("#URBIT_ADMIN_EMAIL").val(returnval.URBIT_ADMIN_EMAIL);

            }else{
                $("#URBIT_ADMIN_EMAIL").val("");

            }

            if(returnval.URBIT_ADMIN_AUTO_VALIDATION_TIME != false ){
                $("#URBIT_ADMIN_AUTO_VALIDATION_TIME").val(returnval.URBIT_ADMIN_AUTO_VALIDATION_TIME);

            }else{
                $("#URBIT_ADMIN_AUTO_VALIDATION_TIME").val("");

            }

            if(returnval.URBIT_ADMIN_FLAT_FEE_EUR != false ){
                $("#URBIT_ADMIN_FLAT_FEE_EUR").val(returnval.URBIT_ADMIN_FLAT_FEE_EUR);

            }else{
                $("#URBIT_ADMIN_FLAT_FEE_EUR").val("");

            }

            if(returnval.URBIT_ADMIN_FLAT_FEE_SEK != false ){
                $("#URBIT_ADMIN_FLAT_FEE_SEK").val(returnval.URBIT_ADMIN_FLAT_FEE_SEK);

            }else{
                $("#URBIT_ADMIN_FLAT_FEE_SEK").val("");

            }

            if(returnval.URBIT_ADMIN_FLAT_FEE_GBP != false ){
                $("#URBIT_ADMIN_FLAT_FEE_GBP").val(returnval.URBIT_ADMIN_FLAT_FEE_GBP);

            }else{
                $("#URBIT_ADMIN_FLAT_FEE_GBP").val("");

            }

            if (returnval.URBIT_ADMIN_STATUS_TRIGGER_OPTIONS != false ) {
                if (returnval.URBIT_ADMIN_STATUS_TRIGGER_OPTIONS) {
                    $('#URBIT_ADMIN_STATUS_TRIGGER').append($('<option/>', {
                        value: "",
                        text : "None"
                    }));
                    $.each(returnval.URBIT_ADMIN_STATUS_TRIGGER_OPTIONS, function (index, value) {
                        $('#URBIT_ADMIN_STATUS_TRIGGER').append($('<option/>', {
                            value: value.id_order_state,
                            text : value.name
                        }));
                    });
                }
            }

            if(returnval.URBIT_ADMIN_STATUS_TRIGGER != false ){
                $("#URBIT_ADMIN_STATUS_TRIGGER").val(returnval.URBIT_ADMIN_STATUS_TRIGGER);

            }else{
                $("#URBIT_ADMIN_STATUS_TRIGGER").val("");

            }

            //  $("#URBIT_ENABLE_TEST_MOD").val(returnval.URBIT_ENABLE_TEST_MOD);
            if(returnval.URBIT_ENABLE_TEST_MOD){
                $('#URBIT_ENABLE_TEST_MOD').prop('checked', true);
            }else{
                $('#URBIT_ENABLE_TEST_MOD').prop('checked', false);

            }

            if(returnval.URBIT_API_CUSTOMER_KEY || (returnval.URBIT_API_TEST_CUSTOMER_KEY && returnval.URBIT_ENABLE_TEST_MOD !=null )){

            $('#module_status').prop('disabled', false);
                $('#module_status').val(returnval.URBIT_MODULE_STATUS);
                $('#module_period').val(returnval.URBIT_MODULE_TIME_SPECIFIED);

                if(returnval.URBIT_API_TEST_CUSTOMER_KEY && returnval.URBIT_ENABLE_TEST_MOD !=null ){
                    $('#URBIT_ENABLE_TEST_MOD').prop('checked', true);
                      }else{
                    $('#URBIT_ENABLE_TEST_MOD').prop('checked', false);

                }

            } else{
                $("#module_status").val("disabled");
                $('#module_status').prop('disabled', 'disabled');

            }
        }
    });
</script>
<style type="text/css">
    .alert {
        background-color: #dcf4f9;
        border-color: #25b9d7;
        color: #1e94ab;
        height: 25px;
        width: 582px;

    }
#presentation-content-play{
 float: left;
 width: 350px;
 margin-right: 10px;
}

#presentation-content-satisfaction{
  float: left;
  width: 350px;
  margin-right: 20px;
}
#presentation-content-rotation{
  float: left;
  width: 350px;
}
#presentation-title{
  text-align: center;
  margin-bottom: 90px;
}
.clearfix {
  clear: both;
}
#presentation-content-onboarding_1{
  float: left;
  width: 350px;
  margin-right: 10px;
}

#presentation-content-onboarding_2{
  float: left;
  width: 350px;
  margin-right: 20px;
}
#presentation-content-onboarding_3{
  float: left;
  width: 350px;

}

#module_period{
  width: 100px !important;
}
.contact-btn{
  text-align: center;

}
#tabs-0 > div.contact-btn > a > button{
  width: 200px;
  height: 50px;
  background-color: #40A497;
  color: #ffffff;
  border-radius: 3px;
}
#tabs-0 > div.contact-btn > a > button:hover{
    background-color: #4097A4;
    color: #ffffff;
}
.text-center{
  text-align: center;
}

p.num{
  text-align: center;
}
#tabs-0{
  border: 1px solid #ccc;
}
</style>

<div id="tabs">
    <ul class="tab_headings">
        <li><a href="#tabs-0">{l s='Presentation' mod='urbit'}</a></li>
        <li><a href="#tabs-1">{l s='General' mod='urbit'}</a></li>
        <li><a href="#tabs-2">{l s='Credentials' mod='urbit'}</a></li>
    </ul>
<div id="tabs-0">
    <div id="presentation-title">
      <h2>{l s='Main benefit of being our partner ?' mod='urbit'}</h2>
    </div>
    <div class="presentation-content">
      <div id="presentation-content-play">
        <div class="image-wrapper">
           <div style="text-align:center;"><img src="{$urbit_img_path|escape:'html':'utf-8'}Play.png" id="play"  width="150px"></div>
        </div>
        <div id="row-play">
          <h3 class="text-center">{l s='MOVE PRODCUTS FASTER' mod='urbit'}</h3>
          <br>
          <p>{l s='Adding Urb-it as a sales channel for your physical store will move your inventory faster as it lower the barrier to purchase. Checking out with urb-it is seamless for customers and handover often happens within an hour of purchase.' mod='urbit'}</p>
        </div>
      </div>
      <div id="presentation-content-satisfaction">
        <div class="image-wrapper">
           <div style="text-align:center;"><img src="{$urbit_img_path|escape:'html':'utf-8'}Satisfaction.png"  width="150px"></div>
        </div>
        <div id="row-satisfaction">
            <h3 class="text-center">{l s='SATISFY THE ON-DEMAND NEED OF YOUR CUSTOMER' mod='urbit'}</h3>
            <p>{l s='Nowadays customers get inspired, shop and share their experience from all possible places and platforms; social media, the web, stores etc. Urb-it helps you to meet the exceeding expectations for “on demand” shopping this entails, by being accessible when and where they want to be inspired, shop, and receive their purchase.' mod='urbit'}</p>
        </div>
      </div>
      <div id="presentation-content-rotation">
        <div class="image-wrapper">
           <div style="text-align:center;"><img src="{$urbit_img_path|escape:'html':'utf-8'}Rotation.png"  width="150px"></div>
        </div>
        <div id="row-rotation">
            <h3 class="text-center">{l s='OFFER EXTRAORDINARY CUSTOMER EXPERIENCE FROM START TO FINISH' mod='urbit'}</h3>
            <p>{l s='Nowadays customers get inspired, shop and share their experience from all possible places and platforms; social media, the web, stores etc. Urb-it helps you to meet the exceeding expectations for “on demand” shopping this entails, by being accessible when and where they want to be inspired, shop, and receive their purchase.' mod='urbit'}</p>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <br><br>
    <div id="presentation-onboarding">
      <div id="presentation-content-onboarding_1">
        <div class="image-wrapper">
           <div style="text-align:center;"><img src="{$urbit_img_path|escape:'html':'utf-8'}Onboarding_1.gif" alt="Un client achète un produit de votre boutique sur l’application, sur votre site e-commerce ou directement dans votre magasin." width="200px"></div>
        </div>
        <div id="Onboarding_1">
           <p class="num">1</p>
          <p>{l s='SHOP FOR YOURSELF, OR CHOOSE THE PERFECT GIFT FOR SOMEONE ELSE' mod='urbit'}</p>
        </div>
      </div>
      <div id="presentation-content-onboarding_2">
        <div class="image-wrapper">
           <div style="text-align:center;"><img src="{$urbit_img_path|escape:'html':'utf-8'}Onboarding_2.gif" alt="Un Urber va chercher vos achats dans votre boutique." width="200px"></div>
        </div>
        <div id="Onboarding_2">
           <p class="num">2</p>
          <p>{l s='CHOOSE A TIME AND PLACE YOU WANT IT. IF IT’S A GIFT, YOU CAN LET THE GIFT RECIPIENT CHOOSE OR EVEN SEND IT AS A SURPRISE.' mod='urbit'}</p>
        </div>
      </div>

      <div id="presentation-content-onboarding_3">
        <div class="image-wrapper">
           <div style="text-align:center;"><img src="{$urbit_img_path|escape:'html':'utf-8'}Onboarding_3.gif" alt="Votre Urber apporte vos produits à vos clients exactement à l’heure et à l’endroit qu’ils ont choisi." width="200px"></div>
        </div>
        <div id="Onboarding_3">
           <p class="num">3</p>
          <p>{l s='AN URBER WILL BRING IT TO YOU, OR TO THE GIFT RECIPIENT' mod='urbit'}</p>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
    <br><br>
    <div class="contact-btn">
        <a href="https://addons.prestashop.com/en/contact-us?id_product=29320" target="_blank"><button type="button">{l s='CONTACT US' mod='urbit'}</button></a>
    </div>
  </div>
    <form  name="tab1_frm" id="tab1_frm">
        <div id="tabs-1">
            <div id="tabs-1-inner">
                <fieldset>
                    <legend>{l s='Basic configuration' mod='urbit'}</legend>
                    <div class="hp_urbit_module">
                        <span>{l s='Urb-it module' mod='urbit'}</span>
                        <select id="module_status" name="module_status">
                            <option value="enabled">{l s='Enabled' mod='urbit'}</option>
                            <option value="disabled">{l s='Disabled' mod='urbit'}</option>
                        </select>
                    </div>
                    <div class="hp_urbit_offer_sp_time">
                        <span>{l s='Enable urb-it Specific Time for no of days:' mod='urbit'}</span>
                        <select  id="module_period" name="module_period">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3" selected>3</option>
                            <option value="4">4</option>
                        </select>
                    </div>
                    <div class="tab-2-input-area admin_mail">
                        <span style="width: 34%;">{l s='Send order failure report to email' mod='urbit'}</span>
                        <input id="URBIT_ADMIN_EMAIL" type="email" name="URBIT_ADMIN_EMAIL">
                    </div>
                    <div class="admin_order_preparation_block">
                        <div class="tab-2-input-area admin_order_preparation">
                            <span style="width: 34%;">{l s='Now order auto-validation time' mod='urbit'}</span>
                            <input id="URBIT_ADMIN_AUTO_VALIDATION_TIME" type="text" name="URBIT_ADMIN_AUTO_VALIDATION_TIME">
                        </div>
                        <div class="tab-2-input-area admin_order_preparation">
                            <span style="width: 34%;">{l s='Order status trigger for confirmation' mod='urbit'}</span>
                            <select id="URBIT_ADMIN_STATUS_TRIGGER" name="URBIT_ADMIN_STATUS_TRIGGER"></select>
                        </div>
                    </div>
                    <div class="admin_fees_block">
                        <div class="tab-2-input-area admin_flat_fee">
                            <span style="width: 34%;">{l s='Urb-it Flat Fee (Eur)' mod='urbit'}</span>
                            <input id="URBIT_ADMIN_FLAT_FEE_EUR" type="text" name="URBIT_ADMIN_FLAT_FEE_EUR">
                        </div>
                        <div class="tab-2-input-area admin_flat_fee">
                            <span style="width: 34%;">{l s='Urb-it Flat Fee (SEK)' mod='urbit'}</span>
                            <input id="URBIT_ADMIN_FLAT_FEE_SEK" type="text" name="URBIT_ADMIN_FLAT_FEE_SEK">
                        </div>
                        <div class="tab-2-input-area admin_flat_fee">
                            <span style="width: 34%;">{l s='Urb-it Flat Fee (GBP)' mod='urbit'}</span>
                            <input id="URBIT_ADMIN_FLAT_FEE_GBP" type="text" name="URBIT_ADMIN_FLAT_FEE_GBP">
                        </div>
                    </div>
                </fieldset>
                <input type="button" value="Save" id="save_general_data" name="save_general_data" />
            </div>
            <div style="display:none" class="alert" id="alert">Success</div>

        </div>

    </form>

    <div id="tabs-2">
        <div id="tabs-2-inner">
            <form name="api_credntials"  id="api_credntials">
                <!-- fieldset one -->

                <fieldset id="tab-2-feild-1">
                    <legend>{l s='Settings' mod='urbit'}</legend>
                    <h3>{l s='API Production Environment Details' mod='urbit'}</h3>
                    <div class="tab-2-input-area">
                        <span>{l s='Urb-it API Key' mod='urbit'}</span>
                        <input type="text" name="URBIT_API_CUSTOMER_KEY"  id="URBIT_API_CUSTOMER_KEY"/>
                    </div>
                    <div class="tab-2-input-area">
                        <span>{l s='Bearer JWT token' mod='urbit'}</span>
                        <input type="text" name="URBIT_API_BEARER_JWT_TOKEN" id="URBIT_API_BEARER_JWT_TOKEN" />
                    </div>
                    <div class="tab-2-input-area">
                        <span>{l s='API URL' mod='urbit'}</span>
                        <input type="text" name="URBIT_API_URL"  id="URBIT_API_URL"/>
                    </div>
                    <h3>{l s='API Test Environment Details' mod='urbit'}</h3>
                    <div class="tab-2-input-area">
                        <span>{l s='Urb-it API Key' mod='urbit'}</span>
                        <input type="text" name="URBIT_API_TEST_CUSTOMER_KEY"  id="URBIT_API_TEST_CUSTOMER_KEY" />
                    </div>
                    <div class="tab-2-input-area">
                        <span>{l s='Bearer JWT token' mod='urbit'}</span>
                        <input type="text" name="URBIT_API_TEST_BEARER_JWT_TOKEN"  id="URBIT_API_TEST_BEARER_JWT_TOKEN"/>
                    </div>
                    <div class="tab-2-input-area">
                        <span>{l s='API URL' mod='urbit'}</span>
                        <input type="text"  name="URBIT_API_TEST_URL"  id="URBIT_API_TEST_URL" />
                    </div>
                    <h3>{l s='General API Settings' mod='urbit'}</h3>
                    <div>
                        <input type="checkbox" name="URBIT_ENABLE_TEST_MOD"  id="URBIT_ENABLE_TEST_MOD" value="enable_test">
                        <span>{l s='Enable test mode' mod='urbit'}</span>
                    </div>

                </fieldset>

                <!-- fieldset two -->

                <!--  <fieldset id="tab-2-feild-2" style="border: 0 solid #ccced7;background-color: #ffffff;">
                   <!--  <legend>Notification</legend>
                     <div class="tab-2-input-area">
                         <span>Send order failure report to email</span>
                         <input type="text" name="URBIT_SEND_FAILIOR_REPORT" />
                     </div>
                </fieldset>-->
                <input   type="hidden" value="validate" name="validate" id="validate" />
                <input type="button" value="Save" name="save_credentials_data" id="save_credentials_data" />
            </form>
        </div>
		<div style="display:none" class="alert" id="alert2">Success</div>
        <div style="display:none" class="fail2" id="alert_fail">Connection to urb-it failed. Please check your credentials and try again.</div>

    </div>

</div>
