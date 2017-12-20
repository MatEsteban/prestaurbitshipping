{**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

{if !empty($new_versions.data)}
    <br/>
    <strong>{$hs_urbit_i18n.new_version|escape:'htmlall':'UTF-8'}</strong><br />
    {foreach from=$new_versions.data item=new_version}
		{$new_version.version|escape:'htmlall':'UTF-8'} {$hs_urbit_i18n.released|escape:'htmlall':'UTF-8'} {$new_version.release|escape:'htmlall':'UTF-8'} <a target="_blank" href="{$new_version.url|escape:'htmlall':'UTF-8'}"><span>{$hs_urbit_i18n.download|escape:'string':'UTF-8'}</span></a></br>
			{/foreach}
		{/if}
		{if !empty($news.data.news)}
    <strong>{$hs_urbit_i18n.new|escape:'htmlall':'UTF-8'}</strong><br />
    {foreach from=$news.data.news item=new}
        {$new|escape:'htmlall':'UTF-8'}<br/>
    {/foreach}
{/if}
{if !empty($news.data.promotion)}
    <strong>{$hs_urbit_i18n.promotion|escape:'htmlall':'UTF-8'}</strong><br/>
    {foreach from=$news.data.promotion item=promotion}
        {$promotion|escape:'htmlall':'UTF-8'}<br/>
    {/foreach}
{/if}
