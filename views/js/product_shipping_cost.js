/**
 * Object product shipping cost
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 */

/**
 * 
 * @param {json} options
 * @returns {UrbitProductShippingCost}
 */
var UrbitProductShippingCost = function(options) {
	/**
	 * url action to server
	 */
	this._ajaxUrl = typeof options.ajaxUrl !== 'undefined' ? options.ajaxUrl : null;

	/**
	 * action to controller
	 */
	this._action = {
		getShippingCostProduct: 'getShippingCostProduct',
		getState: 'getState'
	};

	/**
	 * id product
	 */
	this._idProduct = typeof options.idProduct !== 'undefined' ? options.idProduct : 0;

	/**
	 * id product attribute
	 */
	this._idProductAttribute = typeof options.idProductAttribute !== 'undefined' ? options.idProductAttribute : 0;

	/**
	 * id country
	 */
	this._idCountry = typeof options.idCountry !== 'undefined' ? options.idCountry : 0;

	/**
	 * post code
	 */
	this._postcode = typeof options.postcode !== 'undefined' ? options.postcode : 0;

	/**
	 * id state
	 */
	this._idState = typeof options.idState !== 'undefined' ? options.idState : 0;

	/**
	 * quantity product
	 */
	this._qty = typeof options.qty !== 'undefined' ? options.qty : 0;

	/**
	 * Ajax request
	 */
	this._jqXHR = null;

	/**
	 * all selector
	 */
	this.selectors = {
		zipcode: '#shipping_rates input[name=zipcode]',
		zipcodeBtn: '#shipping_rates #zipcode_btn',
		country: '#shipping_rates #id_country option:selected',
		idCountry: '#id_country',
		stateOption: '#shipping_rates #id_state option:selected',
		qty: '#buy_block input[name=qty]',
		idState: '#id_state',
		states: '#states',
		listCarrier: '#shipping_rates .list_carrier',
		warning: '#shipping_rates .warning',
		blockZipCode: '#shipping_rates .block_zip_code',
		loadingData: '#shipping_rates .loading_data'
	};
	object = this;

	/**
	 * Handle event form get shipping rates.
	 */
	this.handleEvent = function()
	{
		object.updateStateByIdCountry();
		$(object.selectors.zipcode).val('');
		this.showZipCode();
		$(this.selectors.idCountry).on('change', function()
		{
			$(object.selectors.zipcode).val('');
			$(object.selectors.idState).children().remove();
			$(object.selectors.listCarrier).slideUp('fast');
			$(object.selectors.states).slideUp('fast');
			object.updateValue();
			object.updateStateByIdCountry();

		});
		$(this.selectors.idState).on('change', function()
		{
			$(object.selectors.zipcode).val('');
			object.updateValue();
			$(object.selectors.zipcode).focus();
			object.getProductShippingCost();
		});
		$(this.selectors.zipcode).keypress( function(e)
		{
			// Invoke "Enter" key
			if (e.which === 13) {
				object.updateValue();
				object.getProductShippingCost();
				return false;
			}
		});
		$(this.selectors.zipcodeBtn).on('click', function(e)
		{
			e.preventDefault();
			object.updateValue();
			object.getProductShippingCost();
		});
	};

	/**
	 * update idCountry, idState, qty, postcode
	 * update id
	 * @returns {undefined}
	 */
	this.updateValue = function()
	{
		this._idCountry = parseInt($(this.selectors.country).val());
		this._idState = parseInt(typeof $(this.selectors.stateOption).val() !== 'undefined' ? $(this.selectors.stateOption).val() : 0);
		this._qty = parseInt($(this.selectors.qty).val());
		this._postcode = $(this.selectors.zipcode).val();
		this.showZipCode();
	};

	/**
	 * show zip code only autralia
	 */
	this.showZipCode = function()
	{
		if (this._idCountry === 24)
		{
			$(this.selectors.blockZipCode).css('display', 'block');
		}
		else
		{
			$(this.selectors.blockZipCode).css('display', 'none');
		}
	};

	/**
	 * Get states by id country
	 */
	this.updateStateByIdCountry = function()
	{
		$.ajax({
			type: 'POST',
			url: this._ajaxUrl,
			data: {
				ajax: true,
				action: this._action.getState,
				id_country: this._idCountry
			},
			dataType: 'json',
			success: function(json) {

				if (json.length > 0)
				{

					for (state in json)
					{
						$(object.selectors.idState).append('<option value=\'' + json[state].id_state + '\' ' + (id_state === json[state].id_state ? 'selected="selected"' : '') + '>' + json[state].name + '</option>');
						$(object.selectors.states).slideDown('fast');
					}


					object.updateValue();
					object.getProductShippingCost();
					$(object.selectors.idState).slideDown('fast');

				}
				else
				{
					object.updateValue();
					object.getProductShippingCost();
					$(object.selectors.zipcode).focus();

				}
			}
		});
	};

	/**
	 * Get shipping rates of product
	 */
	this.getProductShippingCost = function()
	{
		if (this._idCountry === 24 && this._postcode === '')
		{
			return;
		}
		if (this._jqXHR !== null) {
			this._jqXHR.abort();
		}
		this._jqXHR = $.ajax({
			url: this._ajaxUrl,
			dataType: 'html',
			type: 'post',
			data: {
				ajax: true,
				id_product: this._idProduct,
				id_product_attribute: this._idProductAttribute,
				qty: this._qty,
				id_country: this._idCountry,
				id_state: this._idState,
				action: this._action.getShippingCostProduct,
				postcode: this._postcode
			},
			beforeSend: function()
			{
				$(object.selectors.listCarrier).css('display', 'block');
				$(object.selectors.listCarrier).html('<img src="' + path_image + 'loading.gif" />');
			},
			success: function(data)
			{

				if (data)
				{
					$(object.selectors.listCarrier).slideDown('fast');
					$(object.selectors.listCarrier).html(data);
					$(object.selectors.warning).slideUp('fast');
				}
				else
				{
					$(object.selectors.listCarrier).slideUp('fast');
					$(object.selectors.warning).slideDown('fast');
				}

				this._jqXHR = null;

			}
		});

	};
};

