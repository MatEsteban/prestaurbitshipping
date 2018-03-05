/**
 * Object UrbitExtraCover
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 */

/**
 * object UrbitExtraCover - manage extra cover
 * @param {int} minExtraCover
 * @param {int} maxExtraCover
 * @param {string} ajaxUrl
 * @returns {UrbitExtraCover}
 */
var UrbitExtraCover = function (minExtraCover, maxExtraCover, ajaxUrl)
{
    this._min = minExtraCover;
    this._max = maxExtraCover;
    this._extraCover = new Array();
    this._ajaxUrl = ajaxUrl;
    this._isOnAjaxRequest = false;
    this._ajaxResponse = {};
    
    
    /**
     * callback onChange - handle event onChange of extra cover
     * @param {int} idCarrier
     * @param {int} extraCover
     * @param {function} onSuccess callback on changing successfully
     * @returns {Boolean}
     */
    this.onChange = function(idCarrier, extraCover, onSuccess)
    {
        if(this.isValid(extraCover))
        {
            this._save(idCarrier, extraCover, onSuccess);
            return;
        }
        onSuccess({});
        
    };
    
    /**
     * Save extra cover of a specific carrier
     * @param {int} idCarrier
     * @param {int} extraCover
     * @param {function} onSuccess callback on changing successfully
     * @returns {unresolved}
     */
    this._save = function(idCarrier, extraCover, onSuccess)
    {
        return (
                this._saveToBackEnd(idCarrier, extraCover, onSuccess) &&
                this._saveToFrontEnd(idCarrier, extraCover)
        );
    };
    
    /**
     * Pass extra cover to backend and save it at backend side
     * @param {int} idCarrier
     * @param {int} extraCover
     * @param {function} onSuccess callback on ajax successfully
     */
    this._saveToBackEnd = function(idCarrier, extraCover, onSuccess)
    {
        // request ajax
        $.ajax({
            cache: false,
            async: false,
            url: this._ajaxUrl+'?ajax_token='+(new Date()).getTime(),
            self: this,
            dataType: 'json',
            data: {
                ajax: 1,
                action: 'GetShippingCost',
                extra_cover: extraCover,
                id_carrier: idCarrier
            },
            beforeSend: function(){
                this.self._toggleAjax();
            },
            complete: function()
            {
                this.self._toggleAjax();
            },
            success: function(response)
            {
                onSuccess(response);
            },
            error: function()
            {
                return false;
            }
        });
    };
    
    /**
     * Save extra cover to this object itself, it's probably stored in browser's memory
     * @param {int} idCarrier
     * @param {int} extraCover
     * @returns {Boolean}
     */
    this._saveToFrontEnd = function(idCarrier, extraCover)
    {
        this._extraCover[idCarrier] = extraCover;
        return true;
    };
    
    /**
     * On / Off the ajax flag. True = ajax request is in progress. False = ajax is done
     * @returns {undefined}
     */
    this._toggleAjax = function()
    {
        this._isOnAjaxRequest = !this._isOnAjaxRequest;
        if (this.isOnAjaxRequest())
        {
            this._ajaxResponse = null;
        }
    };
    
    /**
     * Check if an ajax request is in progress
     * @returns {Boolean}
     */
    this.isOnAjaxRequest = function()
    {
        return this._isOnAjaxRequest;
    };
    
    /**
     * Store locally ajax's response
     * @param {json} response
     * @returns {undefined}
     */
    this.setAjaxResponse = function(response)
    {
        this._ajaxResponse = response;
    }
    
    /**
     * Get locally stored ajax response
     * @returns {json} || null
     */
    this.getAjaxResponse = function()
    {
        return this._ajaxResponse;
    }
    
    /**
     * Get min value of extra cover
     * @returns {int}
     */
    this.getMinValue = function()
    {
        return this._min;
    };
    
    /**
     * Get max value of extra cover
     * @returns {int}
     */
    this.getMaxValue = function()
    {
        return this._max;
    };
    
    /**
     * Validate extra cover
     * @param {int} extraCover
     * @returns {Boolean}
     */
    this.isValid = function(extraCover)
    {
        return (
                !isNaN(extraCover) && 
                (parseFloat(extraCover) == parseInt(extraCover)) && 
                extraCover >= this.getMinValue() && 
                extraCover <= this.getMaxValue()
        );
    };
};

