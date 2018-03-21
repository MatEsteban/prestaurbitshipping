{**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

<div class="clearfix">
	<label>&nbsp;</label>
	<div class="margin-form">
		<h4>{$hs_urbit_i18n.update_a_rule|escape:'htmlall':'UTF-8'}</h4>
	</div>
	<div id="urbit_message" style="display: none" class="margin-form">
		<span id="urbit_message_value"></span>
	</div>
</div>

<form  method="post" class="form validate-ajax" name="urbit_edit_products_form" id="urbit_edit_products_form">
	<div class="clearfix">
		<input id="urbit_rate_config" type="hidden" value="{$product.id_urbit_rate_config|escape:'htmlall':'UTF-8'}" name="id_urbit_rate_config"/>
		<label>{$hs_urbit_i18n.product|escape:'htmlall':'UTF-8'} : </label>
		<div class="margin-form clearfix">
			<label id="product_name">{$product.product_name|escape:'htmlall':'UTF-8'}</label>
			<input id="product_id" type="hidden" value="{$product.product_id|escape:'htmlall':'UTF-8'}" name="product_id"/>
		</div>
	</div>

	<div class="clearfix">
		<label>{$hs_urbit_i18n.additional_charges|escape:'htmlall':'UTF-8'} : </label>
		<div class="margin-form clearfix">
			<input type="text" size="20" name="additional_charges" value="{$product.additional_charges|escape:'htmlall':'UTF-8'}" class="required number">
		</div>
	</div>

	<div class="clearfix">
		<label>{$hs_urbit_i18n.add_to_or_replace|escape:'htmlall':'UTF-8'} : </label>
		<div class="margin-form clearfix">
			{foreach from=$au_post_charge item=post_charge key=id}
				{if $id == $product.type}
					<input type="radio" name="au_post_charge" value="{$id|escape:'htmlall':'UTF-8'}" checked/> {$post_charge|escape:'htmlall':'UTF-8'} <br />
				{else}
					<input type="radio" name="au_post_charge" value="{$id|escape:'htmlall':'UTF-8'}" /> {$post_charge|escape:'htmlall':'UTF-8'} <br />
				{/if}
			{/foreach}
		</div>
	</div>

	<div class="clearfix">
		<label>{$hs_urbit_i18n.delivery_service|escape:'htmlall':'UTF-8'}
			<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_delivery_info|escape:'htmlall':'UTF-8'}"></a> :
		</label>
		<div class="margin-form clearfix">
			{foreach from=$au_delivery_service item=delivery_service key=id}
				{if in_array($delivery_service['id_urbit_rate_service_code'], $product.services)}
					<input name="service[]" type="checkbox" value="{$delivery_service['id_urbit_rate_service_code']|escape:'htmlall':'UTF-8'}" class="service" checked/> {$delivery_service['service']|escape:'htmlall':'UTF-8'}<br />
				{else}
					<input name="service[]" type="checkbox" value="{$delivery_service['id_urbit_rate_service_code']|escape:'htmlall':'UTF-8'}" class="service" /> {$delivery_service['service']|escape:'htmlall':'UTF-8'}<br />
				{/if}
			{/foreach}
		</div>
	</div>

	<div class="clearfix">
		<label>&nbsp;</label>
		<div class="margin-form">
			<input class="button" name="EditProductBtn" type="button" value="{$hs_urbit_i18n.submit|escape:'htmlall':'UTF-8'}">
		</div>
	</div>
</form>