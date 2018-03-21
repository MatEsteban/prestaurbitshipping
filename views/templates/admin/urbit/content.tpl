{**
 * Urbit for Pretashop
 * 
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

<div id="urbit_admin">
	<div class="module_info">{include file="./module-info.tpl"}</div>
	<div style="clear:both;height:10px;"></div>
	<div class="module_status">{include file="./module-status.tpl"}</div>
	<div style="clear:both;height:10px;"></div>
	{if $menu_items}
		{include file="./menu.tpl" our_items = $menu_items parents=''}
	{/if}
</div>
