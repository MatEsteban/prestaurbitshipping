{**
 * Point of sale for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

{if sizeof($array_partly_costs) > 1}
    <div class=urbit_partly_cost>
        {$hs_urbit_i18n.in_which|escape:'htmlall':'UTF-8'}<br />
            {foreach from=$array_partly_costs item='array_partly_cost'}
                {foreach from=$array_partly_cost item='item' key='key'}
                - {$key|escape:'htmlall':'UTF-8'}: {convertPrice price=$item}<br />
                {/foreach}
            {/foreach}
    </div>
{/if}
