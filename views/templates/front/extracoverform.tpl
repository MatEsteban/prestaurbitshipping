{**
 * Point of sale for Pretashop
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 *}

<script>
    /**
     *
     * @int min value of extra cover
     */
    var extraCoverMin = {$extra_cover_min|escape:'htmlall':'UTF-8'};
    /**
     *
     * @int max value of extra cover
     */
    var extraCoverMax = {$extra_cover_max|escape:'htmlall':'UTF-8'};
    /**
     * Handler: on Blur of the field extra cover
     * @param event e
     * @returns undefined
     */
    var extraCoverBlurHandler = function(e){
        window.urbitExtraCoverObject = (typeof window.urbitExtraCoverObject !== 'undefined') ? window.urbitExtraCoverObject : new UrbitExtraCover(extraCoverMin, extraCoverMax,ajaxExtraCoverUrl);
        $('#loading').show();
        // setTimeout, fix bug: on IE/Chrome, event blur is not handled perfectly
        setTimeout(function(){
            idCarrier = parseInt($(e.target).attr('rel'));
            extraCover = $(e.target).val();
            window.urbitExtraCoverObject.onChange(idCarrier, extraCover, function(response){
                if(typeof response.success !== 'undefined' && response.success){
                    $('.extra_cover_msg').css('color','');
                } else {
                    $('.extra_cover_msg').css('color','red');
                }
                $('#loading').hide();
            });
        },1);
    };
    {literal}
    /**
     * Controller::Init
     */
    $(document).ready(function(){
        $('#extra_cover')
            .focus(function(){
                $('.extra_cover_msg').css('color','');
            })
            .blur(function(e){extraCoverBlurHandler(e);}
        );
    });
    {/literal}
</script>
<label><h3>{$hs_urbit_i18n.your_expected_extra_cover|escape:'htmlall':'UTF-8'}:</h3></label>
<input type="text" value="{$extra_cover|escape:'htmlall':'UTF-8'}" name="extra_cover" id="extra_cover" size="4" rel="{$id_carrier|escape:'htmlall':'UTF-8'}" />
<span class="extra_cover_confirm_msg">{$hs_urbit_i18n.your_will_see_shipping_cost|escape:'htmlall':'UTF-8'}</span>
<ul class="extra_cover_msg">
    <li>- {$hs_urbit_i18n.up_to|escape:'htmlall':'UTF-8'}{convertPrice price=$extra_cover_max} {$hs_urbit_i18n.but_no_less_than|escape:'htmlall':'UTF-8'}{convertPrice price=$extra_cover_min}</li>
    <li>- {$hs_urbit_i18n.integer_only|escape:'htmlall':'UTF-8'}</li>
    <li>- {$hs_urbit_i18n.integer_only|escape:'htmlall':'UTF-8'}{$hs_urbit_i18n.no_dollar_sign|escape:'htmlall':'UTF-8'}</li>
</ul>
<div class="extra_cover_hint">{$hs_urbit_i18n.extra_cover_covers_against_loss|escape:'htmlall':'UTF-8'}</div>



