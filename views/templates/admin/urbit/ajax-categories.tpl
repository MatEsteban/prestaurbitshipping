{**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

<p class="bold">{$hs_urbit_i18n.notice_require_general_setting|escape:'htmlall':'UTF-8'}</p>  <br>
<div class="bootstrap">
	<div class="warn alert alert-danger">
		<span class="bold">{$hs_urbit_i18n.important_notes|escape:'htmlall':'UTF-8'}: </span><br />
		<ul>
			<li>{$hs_urbit_i18n.category_inherits|escape:'htmlall':'UTF-8'}</li>
			<li>{$hs_urbit_i18n.in_case_product_belong_to_category|escape:'htmlall':'UTF-8'}</li>
		</ul>
	</div>
</div>
<div class="bootstrap">
	<table class="table tableDnD" cellpadding="0" cellspacing="" width="100%">
		<thead>
			<tr class="nodrag nodrop">
				<th class="center">{$hs_urbit_i18n.id_config|escape:'htmlall':'UTF-8'}</th>
				<th class="center">{$hs_urbit_i18n.category|escape:'htmlall':'UTF-8'}</th>
				<th class="center">{$hs_urbit_i18n.type|escape:'htmlall':'UTF-8'}</th>
				<th class="center">{$hs_urbit_i18n.additional_charges|escape:'htmlall':'UTF-8'}</th>
				<th class="center">{$hs_urbit_i18n.services|escape:'htmlall':'UTF-8'}</th>
				<th class="center">{$hs_urbit_i18n.actions|escape:'htmlall':'UTF-8'}</th>
			</tr>
		</thead>
		<tbody>
			{if empty($list_category)}
				<tr>
					<td colspan="6">{$hs_urbit_i18n.message_no_specific_category|escape:'htmlall':'UTF-8'}</td>
				</tr>
			{else}
				{foreach from=$list_category item=item key=key name=cat_list}
					<tr class="{if $smarty.foreach.cat_list.index % 2 != 0 } alt_row{/if} row_hover">
						<td class="center">{$item.id_urbit_rate_config|escape:'htmlall':'UTF-8'}</td>
						<td class="center">{$item.category|escape:'htmlall':'UTF-8'}</td>
						<td class="center">{if $item.type==0}{$hs_urbit_i18n.add|escape:'htmlall':'UTF-8'}{else}{$hs_urbit_i18n.replace_type|escape:'htmlall':'UTF-8'} {/if}</td>
						<td class="center">{$item.additional_charges|escape:'htmlall':'UTF-8'}</td>
						<td class="center">{$item.services|escape:'htmlall':'UTF-8'}</td>
						<td class="center">
							<a href="javascript:void(0);" onclick="UrbitHandler.loadEditCategory({$item.id_urbit_rate_config|escape:'htmlall':'UTF-8'}, st.url.editCategory)">{$hs_urbit_i18n.edit|escape:'htmlall':'UTF-8'}</a>&nbsp;|&nbsp;
							<a href="javascript:void(0);" onclick="UrbitHandler.actionDeleteCategory({$item.id_urbit_rate_config|escape:'htmlall':'UTF-8'}, st.url.deleteCategory, '{$hs_urbit_i18n.msgConfirmDelete|escape:'htmlall':'UTF-8'})'">{$hs_urbit_i18n.delete|escape:'htmlall':'UTF-8'}</a>
						</td>
					</tr>
				{/foreach}
			{/if}
		</tbody>

	</table>
</div>
<div id="add-category">
	<div class="clearfix">
		<label>&nbsp;</label>
		<div class="margin-form">
			<h4>{$hs_urbit_i18n.add_a_rule|escape:'htmlall':'UTF-8'}</h4>
		</div>
	</div>

	<form name="category_setting" method="post" class="form validate-ajax" id="category_form">

		<div class="clearfix">
			<label>{$hs_urbit_i18n.category|escape:'htmlall':'UTF-8'} : </label>
			<div class="margin-form clearfix">
				<select name="id_category" class="required">
					<option value="">{$hs_urbit_i18n.select_a_category|escape:'htmlall':'UTF-8'}</option>
					{foreach from=$categories_select item=item key=key}
						<option value="{$item.id_category|escape:'htmlall':'UTF-8'}">{$item.name|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
			</div>
		</div>

		<div class="clearfix">
			<label>{$hs_urbit_i18n.additional_charges|escape:'htmlall':'UTF-8'} : </label>
			<div class="margin-form clearfix">
				<input type="text" size="20" name="additional_charges" value="" class="required number" />
			</div>
		</div>

		<div class="clearfix">
			<label>{$hs_urbit_i18n.add_to_or_replace|escape:'htmlall':'UTF-8'} : </label>
			<div class="margin-form clearfix">
				{foreach from=$au_post_change item=item key=key}
					<input type="radio"	name="type" value="{$key|escape:'htmlall':'UTF-8'}" checked> {$item|escape:'htmlall':'UTF-8'} <br />
				{/foreach}
			</div>
		</div>

		<div class="clearfix">
			<label>{$hs_urbit_i18n.delivery_service|escape:'htmlall':'UTF-8'}
				<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_delivery_service_info|escape:'htmlall':'UTF-8'}"></a> :
			</label>
			<div class="margin-form clearfix">
				{foreach from=$delivery_service item=item key=id}

					<input name="service[]" type="checkbox" value="{$item['id_urbit_rate_service_code']|escape:'htmlall':'UTF-8'}" class="service" /> {$item['service']|escape:'htmlall':'UTF-8'}<br />
				{/foreach}
			</div>
		</div>

		<div class="clearfix">
			<label>&nbsp;</label>
			<div class="margin-form">
				<input class="button" name="submitCategorySetting" type="button"  value="{$hs_urbit_i18n.submit|escape:'htmlall':'UTF-8'}" >
			</div>
		</div>
	</form>
</div>
<div id="edit-category">
</div>
