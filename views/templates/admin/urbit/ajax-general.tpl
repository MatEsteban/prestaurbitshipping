{**
 * Urbit for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

 <form action="#" method="post" class="form validate-ajax" id="configForm" name="general_form">
    <div class="urbit_genaral">
        <fieldset class='urbit_fieldset urbit_column1'>
			<legend><img class="urbit_icon_top" src="{$img_path|escape:'htmlall':'UTF-8'}icons/advanced-setting.png" alt="" />{$hs_urbit_i18n.basic_configuration|escape:'htmlall':'UTF-8'}</legend>
			<div class="clearfix">
				<label>{$hs_urbit_i18n.weight_unit|escape:'htmlall':'UTF-8'}
					<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_weight_unit_info|escape:'htmlall':'UTF-8'}"></a> :
				</label>
				<div class="margin-form clearfix">
					<select name="PS_WEIGHT_UNIT" tabindex=1 id="urbit_weight_unit">
						{foreach from=$weight_unit item=weight_un key=key }
							{assign var="weight" value=$config['PS_WEIGHT_UNIT']}
							{if $key == $weight}
								{assign var='select' value="selected"}
							{else}
								{assign var='select' value=""}
							{/if}
							<option value="{$key|escape:'htmlall':'UTF-8'}" {$select|escape:'htmlall':'UTF-8'}> {$weight_un|escape:'htmlall':'UTF-8'} </option>
						{/foreach}
					</select>

					<input type="hidden" size="15" name="PS_DIMENSION_UNIT" value="cm" />
				</div>
			</div>
			{if isset($config['URBIT_CARRIER_POSTAL_CODE'])}
				<div class="clearfix">
					<label>{$hs_urbit_i18n.pick_up_location_post_code|escape:'htmlall':'UTF-8'}
						<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_post_code_info|escape:'htmlall':'UTF-8'}"></a> :
					</label>
					<div class="margin-form clearfix">
						<input class="required digits" type="text" size="5" ui-autocomplete="urbit_carrier_postal_code" id="urbit_carrier_postal_code" name="URBIT_CARRIER_POSTAL_CODE" value="{$config['URBIT_CARRIER_POSTAL_CODE']|escape:'htmlall':'UTF-8'}" tabindex=2/>
					</div>
				</div>
			{/if}
			{if isset($config['URBIT_GST'])}
				<div class="clearfix">
					<label>{$hs_urbit_i18n.tax_consideration|escape:'htmlall':'UTF-8'}
						<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_tax_consider_info|escape:'htmlall':'UTF-8'}"></a> :
					</label>
					<div class="margin-form clearfix">
						<input type="checkbox" name="URBIT_GST" value="1" {if $config['URBIT_GST'] == '1'} checked="checked" {/if} tabindex=3 />
						<span> </span>
					</div>
				</div>
			{/if}
			{if isset($config['URBIT_SHOW_DELAY'])}
				<div class="clearfix">
					<label>{$hs_urbit_i18n.show_delivery_time|escape:'htmlall':'UTF-8'}
                        <a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_delivery_time_info|escape:'htmlall':'UTF-8'}"></a> :
					</label>
					<div class="margin-form clearfix">
                        <input type="radio" name="URBIT_SHOW_DELAY" value="0" {if $config['URBIT_SHOW_DELAY']=='0'} checked {/if} tabindex=4 /> &nbsp;{$hs_urbit_i18n.no|escape:'htmlall':'UTF-8'} &nbsp; &nbsp; &nbsp;
                        <input type="radio" name="URBIT_SHOW_DELAY" value="1" {if $config['URBIT_SHOW_DELAY']=='1'} checked {/if} tabindex=5 /> &nbsp;{$hs_urbit_i18n.yes|escape:'htmlall':'UTF-8'}
					</div>
				</div>
			{/if}
			{if isset($config['URBIT_SHOW_PARTLY_COST'])}
				<div class="clearfix">
					<label>{$hs_urbit_i18n.show_partly_costs|escape:'htmlall':'UTF-8'}
                        <a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_partly_costs_info|escape:'htmlall':'UTF-8'}"></a> :
					</label>
					<div class="margin-form clearfix">
                        <input type="radio" name="URBIT_SHOW_PARTLY_COST" value="0" {if $config['URBIT_SHOW_PARTLY_COST']=='0'} checked {/if} tabindex=6 /> &nbsp;{$hs_urbit_i18n.no|escape:'htmlall':'UTF-8'} &nbsp; &nbsp; &nbsp;
                        <input type="radio" name="URBIT_SHOW_PARTLY_COST" value="1" {if $config['URBIT_SHOW_PARTLY_COST']=='1'} checked {/if} tabindex=7 /> &nbsp;{$hs_urbit_i18n.yes|escape:'htmlall':'UTF-8'}
					</div>
				</div>
			{/if}
			{if isset($config['DEBUG_MODE'])}
				<div class="clearfix">
					<label>{$hs_urbit_i18n.debug_mode|escape:'htmlall':'UTF-8'} :
					</label>
					<div class="margin-form clearfix">
                        <input type="radio" name="DEBUG_MODE" value="0" {if $config['DEBUG_MODE']=='0'} checked {/if} tabindex=8 /> &nbsp;{$hs_urbit_i18n.no|escape:'htmlall':'UTF-8'} &nbsp; &nbsp; &nbsp;
                        <input type="radio" name="DEBUG_MODE" value="1" {if $config['DEBUG_MODE']=='1'} checked {/if} tabindex=9 /> &nbsp;{$hs_urbit_i18n.yes|escape:'htmlall':'UTF-8'}
					</div>
				</div>
			{/if}
			{if isset($config['URBIT_PLACE_EXTRA_COVER_FORM'])}

				<div class="clearfix">
					<label>{$hs_urbit_i18n.position_extra_cover_form|escape:'htmlall':'UTF-8'}
                        <a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_positiont_extra_cover_form|escape:'htmlall':'UTF-8'}"></a> :
					</label>
					<div class="margin-form clearfix urbit_extra_cover">
                        <input type="radio" name="URBIT_PLACE_EXTRA_COVER_FORM" value="popup_center" {if $config['URBIT_PLACE_EXTRA_COVER_FORM']=='popup_center'} checked {/if} tabindex=10 /> &nbsp;{$hs_urbit_i18n.popup_and_center|escape:'htmlall':'UTF-8'}<br />
                        <input type="radio" name="URBIT_PLACE_EXTRA_COVER_FORM" value="carrier_list_bottom" {if $config['URBIT_PLACE_EXTRA_COVER_FORM']=='carrier_list_bottom'} checked {/if} tabindex=11 /> &nbsp;{$hs_urbit_i18n.bottom_of_carrier_list|escape:'htmlall':'UTF-8'}<br />
					</div>
				</div>
			{/if}
        </fieldset>
    </div>
    {assign var="is_default_product" value="{isset($config['URBIT_DEFAULT_PRODUCT_LENGTH']) &&
		    isset($config['URBIT_DEFAULT_PRODUCT_HEIGHT']) &&
		    isset($config['URBIT_DEFAULT_PRODUCT_WEIGHT']) &&
		    isset($config['URBIT_DEFAULT_PRODUCT_WIDTH'])}"}
    <div class="urbit_genaral urbit_package">
        <fieldset class='urbit_fieldset'>
            <legend>
                <img class="urbit_icon_top" src="{$img_path|escape:'htmlall':'UTF-8'}icons/package.png" alt="" />
                {if $is_default_product}
                    {$hs_urbit_i18n.package_and_product|escape:'htmlall':'UTF-8'}
                {else}
                    {$hs_urbit_i18n.package|escape:'htmlall':'UTF-8'}
                {/if}
            </legend>
            <table class="urbit_table">
                {if $is_default_product}
                    <tr class="urbit_theader">
                        <td>&nbsp;</td>
                        <td>{$hs_urbit_i18n.product|escape:'htmlall':'UTF-8'}</td>
                        <td>{$hs_urbit_i18n.package|escape:'htmlall':'UTF-8'}</td>
                    </tr>
                {/if}
				<tr>
					<td class="urbit_title">
						{$hs_urbit_i18n.length_in_cm|escape:'htmlall':'UTF-8'} :
					</td>
					{if $is_default_product}
						<td>
							<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_length_info|escape:'htmlall':'UTF-8'}"></a>

						</td>
					{/if}
					{if isset($config['URBIT_FLENGTH'])}
						<td>
							<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_length_maxi_info|escape:'htmlall':'UTF-8'}"></a>
							<input tabindex=150 type="text" size="5" name="URBIT_FLENGTH" id="package_length" class="required number digits urbit_package_input"
								   {if $config['URBIT_FLENGTH']>0}
									   value="{$config['URBIT_FLENGTH']|escape:'htmlall':'UTF-8'}"
								   {/if}
								   />
						</td>
					{/if}

				</tr>
				<tr>
					<td class="urbit_title">
						{$hs_urbit_i18n.height_in_cm|escape:'htmlall':'UTF-8'} :
					</td>
					{if $is_default_product}
						<td>
							<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_height_info|escape:'htmlall':'UTF-8'}"></a>
							</td>
					{/if}
					{if isset($config['URBIT_FHEIGHT'])}
						<td>
							<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_height_maxi_info|escape:'htmlall':'UTF-8'}"></a>
							<input tabindex=151 type="text" size="5" name="URBIT_FHEIGHT" id="package_height" class="required number digits urbit_package_input"
								   {if $config['URBIT_FHEIGHT']>0}
									   value="{$config['URBIT_FHEIGHT']|escape:'htmlall':'UTF-8'}"
								   {/if}
								   />
						</td>
					{/if}
				</tr>
				<tr>
					<td class="urbit_title">{$hs_urbit_i18n.width_in_cm|escape:'htmlall':'UTF-8'} :</td>
					{if $is_default_product}
						<td>
							<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_weight_in_cm_info|escape:'htmlall':'UTF-8'}"></a>
							<input tabindex=102 type="text" size="5" name="URBIT_DEFAULT_PRODUCT_WIDTH" id="product_width" value="{if $config['URBIT_DEFAULT_PRODUCT_WIDTH'] != "" }{$config['URBIT_DEFAULT_PRODUCT_WIDTH']|escape:'htmlall':'UTF-8'}{else}2{/if}" class="required number urbit_product">
						</td>
					{/if}
					{if isset($config['URBIT_FWIDTH'])}
						<td>
							<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_weight_maxi_info|escape:'htmlall':'UTF-8'}"></a>
							<input tabindex=152 type="text" size="5" name="URBIT_FWIDTH"  id="package_width" class="required number digits urbit_package_input"
								   {if $config['URBIT_FWIDTH']>0}
									   value="{$config['URBIT_FWIDTH']|escape:'htmlall':'UTF-8'}"
								   {/if}
								   />

						</td>
					{/if}
				</tr>
				{if $is_default_product}
					<tr>
						<td class="urbit_title">
							{$hs_urbit_i18n.weight|escape:'htmlall':'UTF-8'} :
						</td>
						<td>
							<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_weight_info|escape:'htmlall':'UTF-8'}"></a>
							<input tabindex=103 type="text" size="5" name="URBIT_DEFAULT_PRODUCT_WEIGHT" id="product_weight" value="{if $config['URBIT_DEFAULT_PRODUCT_WEIGHT']!=""}{$config['URBIT_DEFAULT_PRODUCT_WEIGHT']|escape:'htmlall':'UTF-8'}{else}1{/if}" class="required number urbit_product">
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
				{/if}

				{if isset($config['URBIT_FLEXIBLE_PACKAGE'])}
					<tr>
						<td class="urbit_title">
							{$hs_urbit_i18n.flexible_package|escape:'htmlall':'UTF-8'} :
						</td>
						<td colspan='2' class="urbit_flexible">

							<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_flexible_package_info|escape:'htmlall':'UTF-8'} "></a>
							<input type="radio" name="URBIT_FLEXIBLE_PACKAGE" value="0" {if $config['URBIT_FLEXIBLE_PACKAGE']=='0'} checked {/if} tabindex='180' /> &nbsp;{$hs_urbit_i18n.no|escape:'htmlall':'UTF-8'}&nbsp; &nbsp; &nbsp; &nbsp;
							<input type="radio" name="URBIT_FLEXIBLE_PACKAGE" value="1" {if $config['URBIT_FLEXIBLE_PACKAGE']=='1'} checked {/if} tabindex= '181' /> &nbsp;{$hs_urbit_i18n.yes|escape:'htmlall':'UTF-8'}
						</td>
					</tr>
				{/if}
				{if isset($config['URBIT_PACKAGE_MARGIN'])}
					<tr>
						<td class="urbit_title">
							{$hs_urbit_i18n.package_margin|escape:'htmlall':'UTF-8'} :
						</td>
						<td colspan='2'>
							<a href="javascript:void(0);" class="tooltip tt-icon" title="{$hs_urbit_i18n.tooltip_package_margin_info|escape:'htmlall':'UTF-8'}"></a>
							<input tabindex=183 type="text" size="5" name="URBIT_PACKAGE_MARGIN" id="package_margin" value="{if $config['URBIT_PACKAGE_MARGIN'] != ""}{$config['URBIT_PACKAGE_MARGIN']|escape:'htmlall':'UTF-8'}{else}0{/if}" class="required urbit_package_input digits">
						</td>
					</tr>
				{/if}
			</table>
		</fieldset>
    </div>
    <div class="urbit_genaral service_list">
		<fieldset class="urbit_fieldset3">
            <legend><img class="urbit_icon_top" src="{$img_path|escape:'htmlall':'UTF-8'}icons/service.jpg" alt="" />{$hs_urbit_i18n.favourite_services|escape:'htmlall':'UTF-8'}</legend>
            <div class="clearfix">

                <div class="margin-form urbit_services clearfix">
                    {foreach from=$service_lists item=service_list key=key name = name}
                        <input type="hidden" name="service_all[]" value="{$service_list.id_urbit_rate_service_code|escape:'htmlall':'UTF-8'}-{$service_list.id_carrier|escape:'htmlall':'UTF-8'}">
                        {if $service_list.active == 1}
							{$checkbox = 'checked'}
                        {else}
							{$checkbox = ''}
                        {/if}
                        <img class="urbit_icon_services" src="{$img_path|escape:'htmlall':'UTF-8'}services/{$service_list.code|escape:'htmlall':'UTF-8'}.png" alt="" /><input tabindex=200{$smarty.foreach.name.index|escape:'htmlall':'UTF-8'} type="checkbox" name="service[]" value="{$service_list.id_urbit_rate_service_code|escape:'htmlall':'UTF-8'}-{$service_list.id_carrier|escape:'htmlall':'UTF-8'}"  /> {$service_list.service|escape:'htmlall':'UTF-8'}<br />
                    {/foreach}

                </div>
                <div class="clear:both"></div>
            </div>
        </fieldset>
    </div>
    <div class='urbit_submit'>
		<div class="clearfix">
            <input class="button" name="submitgeneralSave" type="button" value="{$hs_urbit_i18n.submit|escape:'htmlall':'UTF-8'}">
		</div>
    </div>
</form>