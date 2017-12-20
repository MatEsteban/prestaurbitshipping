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
                url: "{$base_url|escape:'htmlall':'UTF-8'}ajax.php",
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
                data: 'ajax=true&' + data ,
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

			 if(returnval['status']=='success'){
                        $("#alert2").show();
                        $("#alert2").hide(5000);
                    }else{
                         $("#alert_fail").show();
                         $("#alert_fail").hide(5000);

                     }
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

            if(returnval.URBIT_API_TEST_TOKEN !=false){
                $("#URBIT_API_TEST_TOKEN").val(returnval.URBIT_API_TEST_TOKEN);
            }else{
                $("#URBIT_API_TEST_TOKEN").val("");
            }

            if(returnval.URBIT_API_URL !=false){
                $("#URBIT_API_URL").val(returnval.URBIT_API_URL);
            }else{
                $("#URBIT_API_URL").val("");
            }

            if(returnval.URBIT_API_TOKEN !=false){
                $("#URBIT_API_TOKEN").val(returnval.URBIT_API_TOKEN);
            }else{
                $("#URBIT_API_TOKEN").val("");
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

            }else{
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
</style>
<div id="tabs">
    <ul class="tab_headings">
        <li><a href="#tabs-1">{l s='General' mod='urbit'}</a></li>
        <li><a href="#tabs-2">{l s='Credentials' mod='urbit'}</a></li>
    </ul>

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
                        <select  id="module_period" name="module_period" >
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
                </fieldset>
                <button  type="button" class="btn btn-info" id="save_general_data" name="save_general_data" class="btn btn-primary" />Save</button>
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
                        <span>{l s='Store Key (Oauth token)' mod='urbit'}</span><br>
                        <input style="width: 30%; padding: 5px 20px;" type="text" name="URBIT_API_CUSTOMER_KEY"  id="URBIT_API_CUSTOMER_KEY"/>
                    </div>
                    <div class="tab-2-input-area">
                        <span>{l s='Shared Secret (Consumer secret)' mod='urbit'}</span><br>
                        <input  style="width: 30%; padding: 5px 20px;" type="text" name="URBIT_API_TOKEN" id="URBIT_API_TOKEN" />
                    </div>
                    <div class="tab-2-input-area">
                        <span>{l s='API URL' mod='urbit'}</span><br>
                        <input  style="width: 30%; padding: 5px 20px;" type="text" name="URBIT_API_URL"  id="URBIT_API_URL"/>
                    </div>
                    <h3>{l s='API Test Environment Details' mod='urbit'}</h3>
                    <div class="tab-2-input-area">
                        <span>{l s='Store Key (Oauth token)' mod='urbit'}</span><br>
                        <input style="width: 30%; padding: 5px 20px;" type="text" name="URBIT_API_TEST_CUSTOMER_KEY"  id="URBIT_API_TEST_CUSTOMER_KEY" />
                    </div>
                    <div class="tab-2-input-area">
                        <span>{l s='Shared Secret (Consumer secret)' mod='urbit'}</span><br>
                        <input style="width: 30%; padding: 5px 20px;" type="text" name="URBIT_API_TEST_TOKEN"  id="URBIT_API_TEST_TOKEN"/>
                    </div>
                    <div class="tab-2-input-area">
                        <span>{l s='API URL' mod='urbit'}</span><br>
                        <input style="width: 30%; padding: 5px 20px;" type="text"  name="URBIT_API_TEST_URL"  id="URBIT_API_TEST_URL" />
                    </div>
                    <h3>{l s='General API Settings' mod='urbit'}</h3>
                    <div>
                        <input type="checkbox" name="URBIT_ENABLE_TEST_MOD"  id="URBIT_ENABLE_TEST_MOD" value="enable_test">
                        <span>{l s='Enable test mode' mod='urbit'}</span>
                    </div>

                </fieldset>
                <input  type="hidden" value="validate" name="validate" id="validate" />
                <button type="button" name="save_credentials_data" id="save_credentials_data" />Save</button>
            </form>
        </div>
		<div style="display:none" class="alert" id="alert2">Success</div>
        <div style="display:none" class="fail2" id="alert_fail">Connection to urb-it failed. Please check your credentials and try again.</div>

    </div>

</div>
