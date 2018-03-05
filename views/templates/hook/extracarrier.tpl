{**
 * Point of sale for Pretashop
 * 
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

<script type="text/javascript">
    {literal}
    var ajaxExtraCoverUrl = {/literal}'{$ajax_extra_cover_url|escape:'htmlall':'UTF-8'}'{literal};
	// can not remove this variable, until we implement
    // function add breakline if carrier name contains "("
    function addBreakInDeliveryName()
    {
	$(".delivery_option_title").each(function()
	{
	    var stringName = $(this).html();
	    var arrayName = stringName.split("(");
	    if (typeof arrayName[1] !== 'undefined')
	    {
		cleanedHtml = arrayName[0].replace(/<br\s?\/?>/, '');
		stringName = cleanedHtml + "<br />(" + arrayName[1];
	    }
	    $(this).html(stringName);
	});
    }

    $(document).ready(function() {
	addBreakInDeliveryName();
        
        // delivery times and partly costs
	new ExtraCarrier({
            urbitDelays: {/literal}{$urbit_delays|escape:'htmlall':'UTF-8'}{literal},
            urbitPartlyCosts: {/literal}{$urbit_partly_costs|escape:'htmlall':'UTF-8'}{literal},
            urbitShowDelay: parseInt({/literal}{$urbit_show_delay|escape:'htmlall':'UTF-8'}{literal}),
            urbitShowPartlyCost: parseInt({/literal}{$urbit_show_partly_cost|escape:'htmlall':'UTF-8'}{literal}),
            classNameCarrier: '.delivery_options_address .delivery_option input.delivery_option_radio',
            classNameParent: '.delivery_option',
            classNameDelays: '.delivery_option_delay',
            classNamePartlyCosts: '.urbit_partly_cost',
            classPrice: '.delivery_option_price'
        }).renderMoreInformation();
            
	// object which handdle rendering extra cover form
	extraCoverForm = new ExtraCoverForm({
	    ajaxUrl: ajaxExtraCoverUrl,
	    formPosition: {/literal}'{$urbit_place_extra_cover_form|escape:'htmlall':'UTF-8'}'{literal},
	    ajaxAction: {/literal}'{$ajax_extra_cover_action|escape:'htmlall':'UTF-8'}'{literal},
	    loadingImagePath: {/literal}'{$this_path|escape:'htmlall':'UTF-8'}'{literal} + 'abstract/views/img/loading.gif',
	    extraCoverCarriers: {/literal}{$extra_cover_carriers|escape:'htmlall':'UTF-8'}{literal}
	});

	// trigger rendering extra cover form
	$('.delivery_option_radio').die('click').live('click', function() {
	    var id_carrier = $(this).val();
		if (id_carrier)
			extraCoverForm.onSelectCarrier(id_carrier);
	});

	// by default, we are rendering the extra cover form on loading, if it's not a popup
	var idCarrierSelected = parseInt($('input.delivery_option_radio:checked').val());
	if ({/literal}'{$urbit_place_extra_cover_form|escape:'htmlall':'UTF-8'}'{literal} !== 'popup_center')
	{
	    if (ExtraCoverForm.executionCounter === 0)
	    {
		extraCoverForm.onSelectCarrier(idCarrierSelected);
	    }
	}
	;
    });
    {/literal}
</script>

