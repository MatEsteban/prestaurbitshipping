{**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

{if !empty($configuration_status)}
<fieldset>
	<legend><img src="{$module_logo|escape:'htmlall':'UTF-8'}" alt="" />{$version_name|escape:'htmlall':'UTF-8'} - {$hs_urbit_i18n.configuration_status|escape:'htmlall':'UTF-8'}</legend>
	{if !empty($configuration_status.success)}
		<ul>
			{foreach from=$configuration_status.success item=msg}
				<li><img src="{$img_dir|escape:'htmlall':'UTF-8'}admin/module_install.png" />{$msg|escape:'htmlall':'UTF-8'}</li>
			{/foreach}
		</ul>
	{elseif !empty($configuration_status.fail)}
		<label>{$hs_urbit_i18n.remind_to_review_setting|escape:'htmlall':'UTF-8'}</label><br />
		<div class="clear"></div>
		<ul class="conf_sts_list">
			{foreach from=$configuration_status.fail item=msg}
				<li><img src="{$img_dir|escape:'htmlall':'UTF-8'}admin/warn2.png" />{$msg|escape:'htmlall':'UTF-8'}</li>
			{/foreach}
		</ul>
	{else}
		<p>{$hs_urbit_i18n.notice_exception|escape:'htmlall':'UTF-8'}</p>
	{/if}
</fieldset>
{/if}