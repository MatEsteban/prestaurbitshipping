/**
 * Object ExtraCarrier
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 */


/**
 * extra carrier controller
 * @param {json} options
 * @returns object ExtraCarrier
 */
var ExtraCarrier = function(options)
{
	/**
	 * list of delivery time which come with "Delays" option
	 */
	this._urbitDelays = typeof options.urbitDelays !== 'undefined' ? options.urbitDelays : new Array();

	/**
	 * list of partly costs which come with "partly costs" option
	 */
	this._urbitPartlyCosts = typeof options.urbitPartlyCosts !== 'undefined' ? options.urbitPartlyCosts : new Array();

	/**
	 * get configuration show delivery time which come with "Delays" option
	 */
	this._urbitShowDelay = typeof options.urbitShowDelay !== 'undefined' ? options.urbitShowDelay : 0;

	/**
	 * get configuration show partly costs which come with "partly costs" option
	 */
	this._urbitShowPartlyCost = typeof options.urbitShowPartlyCost !== 'undefined' ? options.urbitShowPartlyCost : 0;

	/**
	 * Css selector of element of carrier name
	 */
	this._classNameCarrier = typeof options.classNameCarrier !== 'undefined' ? options.classNameCarrier : '.delivery_options_address .delivery_option input.delivery_option_radio';

	/**
	 * Css selector of parent element of carrier name
	 */
	this._classNameParent = typeof options.classNameParent !== 'undefined' ? options.classNameParent : '.delivery_option';

	/**
	 * Css selector of element of delay info
	 */
	this._classNameDelays = typeof options.classNameDelays !== 'undefined' ? options.classNameDelays : '.delivery_option_delay';

	/**
	 * Css selector of element of Partly costs
	 */
	this._classNamePartlyCosts = typeof options.classNameParttlyCost !== 'undefined' ? options.classNameParttlyCost : '.urbit_partly_cost';

	/**
	 * Css selector of element of carrier price
	 */
	this._classPrice = typeof options.classPrice !== 'undefined' ? options.classPrice : '.delivery_option_price';
	
	ExtraCarrier.instance = this;
	
	/**
	 * Add more information in list carrier(partly costs, delivery times ....)
	 * @returns {html}
	 */
	this.renderMoreInformation = function()
	{
		$(ExtraCarrier.instance._classNameCarrier).each(function()
		{	
			var idCarrier = parseInt($(this).val());
			if (idCarrier <= 0)
				return;
			
			var children_seletors = $(this).parents('tr').children();
			if (ExtraCarrier.instance._urbitShowDelay === 1 && typeof ExtraCarrier.instance._urbitDelays[idCarrier] !== 'undefined')
				$(children_seletors[2]).html(ExtraCarrier.instance._urbitDelays[idCarrier]);
			
			if (ExtraCarrier.instance._urbitShowPartlyCost === 1 && typeof ExtraCarrier.instance._urbitPartlyCosts[idCarrier] !== 'undefined')
			{
				$(children_seletors[3]).find(ExtraCarrier.instance._classNamePartlyCosts).html('');
				$(children_seletors[3]).append(ExtraCarrier.instance._urbitPartlyCosts[idCarrier]);
			}
		});
	};
};

   