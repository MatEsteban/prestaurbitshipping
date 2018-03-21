{**
* Urbit for Pretashop
*
* @author    Urb-it
* @copyright Urb-it
* @license
*}

<script>
  $(function() {
    $( "#tabs" ).tabs();
  });
</script>
<script>
  $(document).ready(function(){
   var moduleSelectWdth = $(".hp_urbit_module select").outerWidth();
   $(".hp_urbit_offer_sp_time select").outerWidth(moduleSelectWdth);
  });
</script>

<div id="tabs">
  <ul>
    <li><a href="#tabs-1">General</a></li>
    <li><a href="#tabs-2">Credentials</a></li>
    <li><a href="#tabs-3">Copy</a></li>
  </ul>
  <div id="tabs-1">
     <div id="tabs-1-inner">
        <fieldset>
        <legend>Basic configuration</legend>
        <div class="hp_urbit_module">
          <span>Urb-it module</span>
          <select>
            <option value="enabled">Enabled</option>
            <option value="disabled">Disabled</option>
          </select>
        </div>
        <div class="hp_urbit_offer_sp_time">
          <span>Offer Urb-it specific time within a time periods (days)</span>
          <select>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
          </select>
        </div>
      </fieldset>
      <input type="button" value="Save" name="save_general_data" />
    </div>
  </div>
  <div id="tabs-2">
    <div id="tabs-2-inner">

    <!-- fieldset one -->

        <fieldset id="tab-2-feild-1">
          <legend>Settings</legend>
          <h3>API Production Environment Details</h3>
          <div class="tab-2-input-area">
            <span>Customer Key</span>
            <input type="text" />
          </div>
          <div class="tab-2-input-area">
            <span>Toiken</span>
            <input type="text" />
          </div>
          <div class="tab-2-input-area">
            <span>Url</span>
            <input type="text" />
          </div>
          <h3>API Test Environment Details</h3>
          <div class="tab-2-input-area">
            <span>Customer Key</span>
            <input type="text" />
          </div>
          <div class="tab-2-input-area">
            <span>Toiken</span>
            <input type="text" />
          </div>
          <div class="tab-2-input-area">
            <span>Url</span>
            <input type="text" />
          </div>
          <h3>General API Settings</h3>
          <div>
            <input type="checkbox" name="vehicle" value="Bike">
            <span>Enable Test mode</span>
          </div>
        </fieldset>

        <!-- fieldset two -->

        <fieldset id="tab-2-feild-2">
          <legend>Notification</legend>
          <div class="tab-2-input-area">
            <span>Send order failure report to email</span>
            <input type="text" />
          </div>
        </fieldset>
        <input type="button" value="Save" name="save_credentials_data" />
    </div>
  </div>
  <div id="tabs-3">
    <div id="tabs-3-inner">

    <!-- fieldset one -->

        <fieldset id="tab-3-feild-1">
          <legend>Label</legend>
          <div class="tab-3-link-area">
              <ul>
                <li><a href="#"><span>Urb-it Shop-in-shop</span></a></li>
                <li><a href="#"><span>Urb-it Search</span></a></li>
                <li><a href="#"><span>Urb-it Catalogue</span></a></li>
                <li><a href="#"><span>Display urb-it Availability (product listing)</span></a></li>
                <li><a href="#"><span>Display Click-and-get (product listing)</span></a></li>
                <li><a href="#"><span>Display urb-it Availability (in basket)</span></a></li>
                <li><a href="#"><span>Display urb-it Delivery Option (basket)</span></a></li>
                <li><a href="#"><span>Display Click-and-Get (basket)</span></a></li>
                <li><a href="#"><span>Display Click-and-Get (product details)</span></a></li>
                <li><a href="#"><span>Display Urb-it Delivery Option (product details)</span></a></li>
                <li><a href="#"><span>Create and confirm urb-it delivery order</span></a></li>
                <li><a href="#"><span>Info about order status</span></a></li>
                <li><a href="#"><span>Display Click-and-get (standard check-out)</span></a></li>
                <li><a href="#"><span>Initiate Click & Get Window</span></a></li>
              </ul>
          </div>
        </fieldset>

        <!-- fieldset two -->

        <fieldset id="tab-3-feild-2">
          <legend>Translation</legend>
            
            <div class="tab-3-input-area">
              <span>English</span>
              <br/>
              <input type="text" placeholder="Can you shop with urb-it" />
            </div>
            
            <div class="tab-3-input-area">
              <span>French</span>
              <br/>
              <input type="text" placeholder="Can you shop with urb-it" />
            </div>

            <div class="tab-3-input-area">
              <span>Swedish</span>
              <br/>
              <input type="text" placeholder="Can you shop with urb-it" />
            </div>
        </fieldset>
        <input type="button" value="Save" name="save_copy_data" />
    </div>
  </div>
</div>