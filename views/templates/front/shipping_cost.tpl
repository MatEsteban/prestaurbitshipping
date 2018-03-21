{**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

{if !empty($shipping_costs)}
    <table id="shipping_cost_list">
		{foreach from=$shipping_costs item='shipping'}
			<tr class="{cycle values="odd,even"}">
				<td width="65%" class="carrier_name">{$shipping.name|escape:'htmlall':'UTF-8'}</td>
				<td width="35%" class="delivery_price">
					<span class="price">{displayPrice price=$shipping.price}</span> {$hs_urbit_i18n.tax_incl|escape:'htmlall':'UTF-8'}
				</td>
			</tr>
		{/foreach}
    </table>
{/if}