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
	<h4>{$hs_urbit_i18n.edit_a_rule|escape:'htmlall':'UTF-8'}</h4>
    </div>
</div>

<form name="edit_category_setting" method="post" class="form validate-ajax" id="edit_category_form">
    <input type="hidden" name="id" value="{$obj_rate_config->id|escape:'htmlall':'UTF-8'}">
    <div class="clearfix">
	<label>{$hs_urbit_i18n.category|escape:'htmlall':'UTF-8'} : </label>
	<div class="margin-form clearfix">
	    <input type="hidden" value="{$obj_rate_config->id_category|escape:'htmlall':'UTF-8'}" name="id_category" />
	    <select name="id_category" class="required" disabled>
		<option value="">{$hs_urbit_i18n.select_a_category|escape:'htmlall':'UTF-8'}</option>
		{foreach from=$categories_select item=item key=key}
		    {if $item.id_category == $obj_rate_config->id_category}
			{$select = 'selected '}
		    {else}
			{$select = ''}
		    {/if}
		    <option value="{$item.id_category|escape:'htmlall':'UTF-8'}" {$select|escape:'htmlall':'UTF-8'}>{$item.name|escape:'htmlall':'UTF-8'}</option>
		{/foreach}
	    </select>
	</div>
    </div>

    <div class="clearfix">
	<label>{$hs_urbit_i18n.addition_charges|escape:'htmlall':'UTF-8'} : </label>
	<div class="margin-form clearfix">
	    <input type="text" size="20" name="additional_charges" value="{$obj_rate_config->additional_charges|escape:'htmlall':'UTF-8'}" class="required number" />
	</div>
    </div>

    <div class="clearfix">
	<label>{$hs_urbit_i18n.add_to_or_replace|escape:'htmlall':'UTF-8'} : </label>
	<div class="margin-form clearfix">
	    {foreach from=$au_post_change item=item key=key}
		{if $key == $obj_rate_config->type} {$checked='checked'}
		{else} {$checked=''}
		{/if}
		<input type="radio"	name="type" value="{$key|escape:'htmlall':'UTF-8'}" {$checked|escape:'htmlall':'UTF-8'} > {$item|escape:'htmlall':'UTF-8'} <br />
	    {/foreach}
	</div>
    </div>

    <div class="clearfix">
	<label>{$hs_urbit_i18n.delivery_service|escape:'htmlall':'UTF-8'}
	    <a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_delivery_service_info|escape:'htmlall':'UTF-8'}"></a> :
			</label>
			<div class="margin-form clearfix">
	       {foreach from=$delivery_service item=item key=id}
		   {if in_array($item['id_urbit_rate_service_code'], $service)} {$checkbox ='checked'}
		   {else}  {$checkbox =''}
		   {/if}
					<input name="service[]" type="checkbox" value="{$item['id_urbit_rate_service_code']|escape:'htmlall':'UTF-8'}" class="service" {$checkbox|escape:'htmlall':'UTF-8'}/> {$item['service']|escape:'htmlall':'UTF-8'}<br />
		   {/foreach}

			</div>
		</div>

		<div class="clearfix">
			<label>&nbsp;</label>
			<div class="margin-form">
				<input class="button" name="editCategorySetting" type="button"  value="{$hs_urbit_i18n.submit|escape:'htmlall':'UTF-8'}" >
			</div>
		</div>
	</form>