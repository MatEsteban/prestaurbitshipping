{**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

<p class="bold">{$hs_urbit_i18n.message_specific|escape:'htmlall':'UTF-8'}</p>
<br>
<div class="bootstrap">
	<table class="table tableDnD" cellpadding="0" cellspacing="0" width="100%">
		<thead>
			<tr class="nodrag nodrop">
				<th class="center">{$hs_urbit_i18n.id_config|escape:'htmlall':'UTF-8'}</th>
				<th class="center">{$hs_urbit_i18n.product|escape:'htmlall':'UTF-8'}</th>
				<th class="center">{$hs_urbit_i18n.add_or_replace|escape:'htmlall':'UTF-8'}</th>
				<th class="center">{$hs_urbit_i18n.addition_charges|escape:'htmlall':'UTF-8'}</th>
				<th class="center">{$hs_urbit_i18n.services|escape:'htmlall':'UTF-8'}</th>
				<th class="center">{$hs_urbit_i18n.actions|escape:'htmlall':'UTF-8'}</th>
			</tr>
		</thead>
		<tbody>
			{if empty($au_product_lists)}
				<tr>
					<td colspan="6">{$hs_urbit_i18n.message_no_specific|escape:'htmlall':'UTF-8'}</td>
				</tr>
			{else}
				{foreach from=$au_product_lists item=item key=key name=prd_list}
					<tr class="{if $smarty.foreach.prd_list.index % 2 != 0 } alt_row{/if} row_hover">
						<td class="center">{$item.id_urbit_rate_config|escape:'htmlall':'UTF-8'}</td>
						<td class="center">{$item.product_name|escape:'htmlall':'UTF-8'}</td>
						<td class="center">{if $item.type==0}{$hs_urbit_i18n.add|escape:'htmlall':'UTF-8'}{else}{$hs_urbit_i18n.replace_type|escape:'htmlall':'UTF-8'} {/if}</td>
						<td class="center">{$item.additional_charges|escape:'htmlall':'UTF-8'}</td>
						<td class="center">{$item.services|escape:'htmlall':'UTF-8'}</td>
						<td class="center">
							<a href="javascript:void(0)" onclick="UrbitHandler.loadEditProduct({$item.id_urbit_rate_config|escape:'htmlall':'UTF-8'}, st.url.editProduct)">{$hs_urbit_i18n.edit|escape:'string':'UTF-8'}</a>&nbsp;|&nbsp;
							<a href="javascript:void(0)" onclick="UrbitHandler.loadDeleteProduct({$item.id_urbit_rate_config|escape:'htmlall':'UTF-8'}, st.url.deleteProduct, '{$hs_urbit_i18n.msgConfirmDelete|escape:'string':'UTF-8'}')">{$hs_urbit_i18n.delete|escape:'htmlall':'UTF-8'}</a>
						</td>
					</tr>
				{/foreach}
			{/if}
		</tbody>
	</table>
</div>
<div id="add-product">
	<div class="clearfix">
		<label>&nbsp;</label>
		<div class="margin-form">
			<h4>{$hs_urbit_i18n.add_a_rule|escape:'htmlall':'UTF-8'}</h4>
		</div>
		<div id="urbit_message" style="display: none" class="margin-form">
			<span id="urbit_message_value"></span>
		</div>
	</div>

	<form  method="post" class="form validate-ajax bootstrap" name="urbit_save_products_form" id="urbit_save_products_form">


		<div class="clearfix">
			<label>{$hs_urbit_i18n.product|escape:'htmlall':'UTF-8'} : </label>
			<div class="margin-form clearfix">
				<input id= "product_search" type="text" value="" placeholder="{$hs_urbit_i18n.search_for_a_product|escape:'htmlall':'UTF-8'}" name="search_query" id="product_search" class="search_query ac_input">
				<input id= "product_id" type="hidden" value="" name="product_id"/>
			</div>
		</div>

		<div class="clearfix">
			<label>{$hs_urbit_i18n.additional_charges|escape:'htmlall':'UTF-8'} : </label>
			<div class="margin-form clearfix">
				<input type="text" size="20" name="additional_charges" value="" class="required number">
			</div>
		</div>

		<div class="clearfix">
			<label>{$hs_urbit_i18n.add_to_or_replace|escape:'htmlall':'UTF-8'} : </label>
			<div class="margin-form clearfix">
				{foreach from=$au_post_charge item=post_charge key=id}
					{if $id == 0}
						<input type="radio" name="au_post_charge" value="{$id|escape:'htmlall':'UTF-8'}" checked/> {$post_charge|escape:'htmlall':'UTF-8'} <br />
					{else}
						<input type="radio" name="au_post_charge" value="{$id|escape:'htmlall':'UTF-8'}"/> {$post_charge|escape:'htmlall':'UTF-8'} <br />
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
					<input name="service[]" type="checkbox" value="{$delivery_service['id_urbit_rate_service_code']|escape:'htmlall':'UTF-8'}" class="service" /> {$delivery_service['service']|escape:'htmlall':'UTF-8'}<br />
				{/foreach}
			</div>
		</div>

		<div class="clearfix">
			<label>&nbsp;</label>
			<div class="margin-form">
				<input class="button" name="submitSaveProduct" type="button" value="{$hs_urbit_i18n.submit|escape:'htmlall':'UTF-8'}">
			</div>
		</div>
	</form>
</div>
<div id="edit-product">

</div>