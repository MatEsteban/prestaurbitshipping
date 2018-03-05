{**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

<script type="text/javascript">
var id_state = '{$id_state|intval}';
var moduleName = '{$module_name|escape:'htmlall':'UTF-8'}';
var path_image = '{$path_image|escape:'htmlall':'UTF-8'}';
$(document).ready(function() {
var idProduct = $('#buy_block input[name=id_product]').val();
var idProductAttribute = $('#buy_block input[name=id_product_attribute]').val();
var qty = $('#buy_block input[name=qty]').val();
var idCountry = $('#shipping_rates #id_country option:selected').val();
var idState = typeof $('#shipping_rates #id_state option:selected').val() !== 'undefined' ? $('#shipping_rates #id_state option:selected').val() : 0;
var postcode = $('#shipping_rates input[name=zipcode]').val();
var url = '{$url_ajax|escape:'htmlall':'UTF-8'}';
if (isUrl(url))
{	url = synUrl(url);
	new UrbitProductShippingCost({
		'ajaxUrl': url,
		'idProduct': idProduct,
		'idProductAttribute': idProductAttribute,
		'qty': qty,
		'idCountry': idCountry,
		'idState': idState,
		'postcode': postcode
	}).handleEvent();
}

/**
 * syn url with current location protocol
 * @param url string
 * @returns string
 */
function synUrl(url)
{
	var syn_url = '';
	if (typeof url !== 'undefined')
		syn_url = url.indexOf('https:') > -1 ?  url.replace("https:", document.location.protocol) : url.replace("http:", document.location.protocol);
	return syn_url;
}

/**
 * Check is url or not
 * @param  url string
 * @returns boolean
 */
function isUrl(url) {
	{literal}   var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/ ;	{/literal}
    return regexp.test(url);
}

});




</script>
<form class="std" id="shipping_rates" method="post" action="#" >
	<div class="logo_urbit_post">
		<img src="{$path_image|escape:'htmlall':'UTF-8'}urbit_post.jpg" />
	</div>
	<fieldset id="compare_shipping_rates">
		<p class="title">{$hs_urbit_i18n.estimate_shipping_cost|escape:'htmlall':'UTF-8'}</p>
		<p class="input">
			<label for="id_country">{$hs_urbit_i18n.country|escape:'htmlall':'UTF-8'}</label>
			<select name="id_country" id="id_country">
				{foreach from=$countries item=country}
					<option value="{$country.id_country|escape:'htmlall':'UTF-8'}" {if $id_country == $country.id_country}selected="selected"{/if}>{$country.name|escape:'htmlall':'UTF-8'}</option>
				{/foreach}
			</select>
		</p>
		<p class="input" id="states" style="display: none;">
			<label for="id_state">{$hs_urbit_i18n.state|escape:'htmlall':'UTF-8'}</label>
			<select name="id_state" id="id_state"></select>
		</p>
		<p class="input block_zip_code">
			<label for="zipcode">{$hs_urbit_i18n.zip_code|escape:'htmlall':'UTF-8'}</label>
			<input type="text" name="zipcode" id="zipcode" value="0" />
			<a href="javascript:void(0);" id="zipcode_btn">{$hs_urbit_i18n.submit|escape:'htmlall':'UTF-8'}</a>
		</p>

		<div class="list_carrier"></div>

		<p class="warning center" id="noCarrier" style="display: none;">{$hs_urbit_i18n.no_carrier_available|escape:'htmlall':'UTF-8'}</p>
	</fieldset>
</form>

<style>
	#shipping_rates {
		width: 100%;
		margin: 25px 0;
		overflow: hidden;
		background: #FBFBFB;
	}
	#shipping_rates .logo_urbit_post {
		background: #FFFFFF;
	}
	#shipping_rates fieldset {
		padding: 1.2em 0.5em 0.5em;
		margin: 0;
		border: 1px solid #ccc;
	}
	#shipping_rates .title {
		font-size: 1.2em;
		color: #000;
		margin-bottom: 20px;
		padding-bottom: 0;
		text-transform: uppercase;
		font-weight: bold;
		padding: 0 15px;
	}
	#shipping_rates p.input {
		clear: both;
		padding: 0 15px;
		margin-bottom: 10px;
	}
	#shipping_rates p.input label {
		display: block;
		margin-bottom: 5px;
	}
	#shipping_rates p.input input, #shipping_rates p.input select {
		width: 90%;
		border: 1px solid #ddd;
		padding-top: 2px;
		padding-bottom: 2px;
	}

	#shipping_rates #zipcode_btn {
		display: inline-block;
		border: none;
		padding: 5px 0;
		margin: 10px 0;
		text-align: center;
		text-decoration: none;
		cursor: pointer;
		width: 90%;
		background: #D50000;
		color: #fff;
		font-weight: bold;
	}


	#shipping_rates .list_carrier {
		text-align: center;
		margin-top: 20px;
	}
	#shipping_rates table#shipping_cost_list {
		border-collapse: collapse;
		border-spacing: 0;
		border: none;
		margin-top: 6px;
		width: 100%;
	}
	#shipping_rates table#shipping_cost_list td {
		padding: 8px 5px;
	}
	#shipping_rates table#shipping_cost_list .odd td {
		background-color: #F7F7F7;
	}
	#shipping_rates table#shipping_cost_list .even td {
		background-color: #efefef;
	}

	#shipping_rates table#shipping_cost_list td.delivery_price {
		text-align: right;
	}
	#shipping_rates table#shipping_cost_list td.delivery_price .price {
		font-weight: bold;
	}

	#shipping_rates #noCarrier {
		margin-top: 20px;
		margin-bottom: 0;
	}

</style>