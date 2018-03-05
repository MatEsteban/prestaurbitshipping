/**
 * Object ExtraCoverForm
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 */

/**
 * extra cover form controller
 * @param {json} options
 * @returns {ExtraCoverForm}
 */
var ExtraCoverForm = function(options)
{
    /**
     * target of ajax request
     */
    this._ajaxUrl = typeof options.ajaxUrl !== 'undefined' ? options.ajaxUrl : null;

    /**
     * (backend) action of ajax request
     */
    this._ajaxAction = typeof options.ajaxAction !== 'undefined' ? options.ajaxAction : null;

    /**
     * DOM element where extra cover form will be appended to
     */
    this._formContainer = typeof options.formContainer !== 'undefined' ? options.formContainer : 'aus-carrier-shipping';// css class, please.

    /**
     * Position of extra cover form.
     * Possible option:
     * - Popup and center
     * - At the bottom of carrier list
     * - At the bottom of a selected carrier
     */
    this._formPosition = typeof options.formPosition !== 'undefined' ? options.formPosition : null;

    /**
     * path to the loading image indicator
     */
    this._loadingImagePath = typeof options.loadingImagePath !== 'undefined' ? options.loadingImagePath : null;

    /**
     * list of carriers which come with "extra cover" option
     */
    this._extraCoverCarriers = typeof options.extraCoverCarriers !== 'undefined' ? options.extraCoverCarriers : [];

    /**
     * DOM selector of the box "extra Carrier". This depends on Prestashop version.
     */
    this._extraCarrierContainer = typeof options.extraCarrierContainer !== 'undefined' ? options.extraCarrierContainer : '#extra_carrier';

    /**
     * Css selector of next/back button. This depends on Prestashop version.
     */
    this._nextBackSelector = typeof options.nextBackSelector !== 'undefined' ? options.nextBackSelector : '#form input.exclusive';

    /**
     * Additional close buttons, in case of popup form
     */
    this._customCloseButtonSelector = typeof options.customCloseButtonSelector !== 'undefined' ? options.customCloseButtonSelector : '.custom-close-overlay';


    /**
     * Handle event select a carrier
     * @param {int} idCarrier
     * @returns {unresolved}
     */
    this.onSelectCarrier = function(idCarrier)
    {

        ExtraCoverForm.executionCounter++;
        // the current carrier is not an extra cover carrier
        if (this._extraCoverCarriers.indexOf(parseInt(idCarrier)) < 0)
        {
	    $('.'+ this._formContainer).remove();
            return;
        }
        // can not define target of ajax request

        if (typeof this._ajaxUrl === 'undefined' || typeof this._ajaxAction === 'undefined')
        {
            return;
        }
        // can not define position of extra cover form
        if (typeof this._formPosition === 'undefined')
        {
            return;
        }
	this.toggleContainer();
        thisObject = this;
        $.ajax({
            url: this._ajaxUrl+'?ajax_token='+(new Date()).getTime(),
            cache: false,
            data: {
                ajax: 1,
                action: this._ajaxAction,
                id_carrier: parseInt(idCarrier)
            },
            success: function(htmlForm){
                thisObject.renderForm(htmlForm);
            }
          });
    };


    /**
     * render the extra cover form, after ajax-ing
     * @param {html} htmlForm
     * @returns {undefined}
     *
     */
    this.renderForm = function(htmlForm)
    {
        $('.'+this._formContainer).html(htmlForm);
        switch (this._formPosition)
        {
            case 'popup_center':
                this._attachEventCloseOverlay();
                break;
            case 'carrier_list_bottom':
                $(this._nextBackSelector).removeAttr("disabled", "disabled");
                break;
            case 'carrier_bottom':
            default:
                break;
        }
    };

    /**
     * HTML image which is considered as loading indicator
     * @returns {String}
     */
    this.getLoadingImage = function()
    {
        return typeof this._loadingImagePath !== 'undefined' ? '<img src="'+ this._loadingImagePath +'">' : '';
    };

    /**
     * Prepare a DOM where extra cover form will be appended to
     * @returns {html}
     */
    this.toggleContainer = function()
    {
        switch (this._formPosition)
        {
            case 'popup_center':
                $(".modal").remove();
                $('<div id="au_extra" class="modal"><a id="close_modal_box" class="close"></a><div class="'+this._formContainer+'"></div></div>').insertBefore($('body'));
                $('.'+this._formContainer).append(this.getLoadingImage());
				width = $(window).width();
				divWith = $('#au_extra').width();
				left = (width - divWith)/2;
                $('#au_extra').overlay({
                    mask:
                    {
                        loadSpeed: 200,
                        opactity: 0
                    },
                    load: true,
                    closeOnClick: true,
					left:left
                });
                break;
            case 'carrier_list_bottom':
                $(this._nextBackSelector).attr("disabled", "disabled");
                $('.'+this._formContainer).remove();
                $('<div class="'+this._formContainer+'"></div>').insertBefore($(this._extraCarrierContainer));
                $('.'+this._formContainer).html(this.getLoadingImage());
                break;
            case 'carrier_bottom':
            default:
                break;
        }
    };

    /**
     * bind event: click on a custom close button
     * @returns {undefined}
     */
    this._attachEventCloseOverlay = function()
    {
        $(this._customCloseButtonSelector).die('click').click(function(){
            $('#exposeMask').click();// exposeMask is fixed selector by jQuery.overlay()
        });
    };
};
ExtraCoverForm.executionCounter = 0;