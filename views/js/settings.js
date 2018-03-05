/**
 * Object Handle event
 *
 * @author    Urb-it
 * @copyright Urb-it
 * @license 
 */

/**
 * Simply compares two string version values.
 *
 * Example:
 * versionCompare('1.1', '1.2') => -1
 * versionCompare('1.1', '1.1') =>  0
 * versionCompare('1.2', '1.1') =>  1
 * versionCompare('2.23.3', '2.22.3') => 1
 *
 * Returns:
 * -1 = left is LOWER than right
 *  0 = they are equal
 *  1 = left is GREATER = right is LOWER
 *  And FALSE if one of input versions are not valid
 *
 * @function
 * @param {String} left  Version #1
 * @param {String} right Version #2
 * @return {Integer|Boolean}
 * @author Alexey Bass (albass)
 * @since 2011-07-14
 */
versionCompare = function(left, right) {
    if (typeof left + typeof right !== 'stringstring')
        return false;

    var a = left.split('.')
    ,   b = right.split('.')
    ,   i = 0, len = Math.max(a.length, b.length);

    for (; i < len; i++) {
        if ((a[i] && !b[i] && parseInt(a[i]) > 0) || (parseInt(a[i]) > parseInt(b[i]))) {
            return 1;
        } else if ((b[i] && !a[i] && parseInt(b[i]) > 0) || (parseInt(a[i]) < parseInt(b[i]))) {
            return -1;
        }
    }

    return 0;
};

/**
 * This object bind all handler events
 */
var UrbitHandler = {
	// all settings (selector)
	selectors : {
		tabs : '.tabs', // class apply tabs of jquery
		tabContent : '.ui-tabs-panel', // class define for content of tab
		tabProduct: 'products', // define class of tab have class is: products
		tabGeneral: 'general', // define class of tab general
		tabCategories: 'categories',
		formValidate: 'form.validate-ajax', // class define for form use jquery validate
		formGeneral: 'form#configForm',
		formCategory: 'form#category_form',
		formEditCategory: 'form#edit_category_form',

		formSaveProduct: 'form#urbit_save_products_form',
		formEditProduct: 'form#urbit_edit_products_form',
		productLength: '#product_length',
                productHeight: '#product_height',
                productWidth: '#product_width',
                productWeight: '#product_weight',
                packageLength: '#package_length',
                packageHeight: '#package_height',
                packageWidth: '#package_width',
                packageMargin: '#package_margin',
		weightUnit: '#urbit_weight_unit',
		helpTip: '.tooltip', // class use for tooltip.
		editCategory: '#edit-category', // id define for div (block) edit category
		addCategory:  '#add-category', // id define for div (block) add category
		searchSelector: '#product_search', // id of input apply for autocomplete search
                searchPostCode: '#urbit_carrier_postal_code', // id of input apply to autocomplete search
		saveProductBtn: 'input[name="submitSaveProduct"]', // define button save product (case adding)
		editProductBtn: 'input[name="EditProductBtn"]', // define button save product (case editing)
		addProduct: '#add-product', // define id of div (block) add product
		editProduct: '#edit-product', // define id of div (block) edit product

		categorySettingBtn:'input[name="submitCategorySetting"]', // define element name of button save category
		categoryEditSettingBtn:'input[name="editCategorySetting"]', // define element name of button save edit category

		editProductForm: 'form[name=urbit_edit_products_form]', // define element name of form product
		saveProductForm: 'form[name=urbit_save_products_form]', // define element name of form product

		saveCategoryForm: 'form[name=category_setting]', // define element name of form category
		saveEditCategoryForm: 'form[name=edit_category_setting]', // define element name of form category
		submitgeneralSave: 'input[name="submitgeneralSave"]', // define name of input button general
		saveGeneralForm: 'form[name=general_form]', // define form element save general
		idProduct: '#product_id', // define id of id_product (use save id of product when select from list autocomplete)
		moduleStatus: '.module_status', // container of module status block
		productSaveMessageContainer: 'div#urbit_message', // define id of div that is a place holder for message when save or add product
		productSaveMessageValue: 'span#urbit_message_value', // define id of span tag to display message
		urbitPackageInput :'.urbit_package_input',
		urbitConfigForm: '#configForm .button',
		urbitTableInput: '.urbit_table input'
	},

	//menu tab
	tabs : function(){
		$(this.selectors.tabs).tabs({
			cookie: {expires: 60*30}, //cookie expire = 30 mins
			cache: false,
			ajaxOptions: {cache:false},
			load: function ( event, ui)
			{
                                tabSelector = versionCompare($.ui.version, '1.10') >= 0 ? $(ui.tab).children('a') : $(ui.tab);
				// check if tab products is click
				if ($(tabSelector).hasClass(UrbitHandler.selectors.tabProduct))
				{
					window.stUiSearchTab = ui; // global var to reload tab after searching
					// call function autocomplete products
					UrbitHandler.autoComplete(UrbitHandler.selectors.searchSelector, st.url.searchProduct);
				}

				if ($(tabSelector).hasClass(UrbitHandler.selectors.tabGeneral))
				{
                                        // Status of module, to see if it's ready to go
					UrbitHandler.updateModuleStatus();
                                        //var width_screen = document.body.clientWidth;
                                        UrbitHandler.autoEqualHeightDiv();
                                        UrbitHandler.eventPackage();
                                        UrbitHandler.getPackage();
                                        // search postcode

                                        UrbitHandler.autoCompletePostCodeSearch(UrbitHandler.selectors.searchPostCode, st.url.postCodeSearch);
				}

				if ($(tabSelector).hasClass(UrbitHandler.selectors.tabCategories))
				{
					// register category save action
					UrbitHandler.categorySettingSave();
				}
				// call tooltip action
				// UrbitHandler.activeClueTips();
				// call validate form
				UrbitHandler.formValidate();
				if ($(tabSelector).hasClass(UrbitHandler.selectors.tabGeneral))
				{
					// register general action
					UrbitHandler.generalSave();
				}
			}
		});
	},
	// trigger action save product when click button save
	productSave: function()
	{
		$(document).on('click',UrbitHandler.selectors.saveProductBtn,{},function(){
			// call validate form
			UrbitHandler.formValidate(UrbitHandler.selectors.formSaveProduct);

			if( $(UrbitHandler.selectors.formSaveProduct).valid() ) {
				$.ajax({
					url:st.url.saveProduct,
					dataType: 'json',
					data : $(UrbitHandler.selectors.saveProductForm).serialize(),
					type: 'POST',
					success: function(data)
					{
						if(data['success']){
							$(UrbitHandler.selectors.productSaveMessageContainer).hide();
						}
						// don't need message when duplicate service
						/*else{
							$(UrbitHandler.selectors.productSaveMessageValue).text(data['message']);
							$(UrbitHandler.selectors.productSaveMessageContainer).show();
						}*/

						UrbitHandler.callBackReloadTab();
					},
					error: function(data)
					{
						//alert('false');
					}
				});
			}
		});
	},

	// trigger action save product when click button save
	productEdit: function()
	{
		$(document).on('click',UrbitHandler.selectors.editProductBtn,{},function(){
			// call validate form
			UrbitHandler.formValidate(UrbitHandler.selectors.formEditProduct);

			if( $(UrbitHandler.selectors.formEditProduct).valid() ) {
				$.ajax({
					url:st.url.saveProduct,
					dataType: 'json',
					data : $(UrbitHandler.selectors.editProductForm).serialize(),
					type: 'POST',
					success: function(data)
					{
						if(data['success']){
							$(UrbitHandler.selectors.productSaveMessageContainer).hide();
							UrbitHandler.callBackReloadTab();
						}
						else{
							$(UrbitHandler.selectors.productSaveMessageValue).text(data['message']);
							$(UrbitHandler.selectors.productSaveMessageContainer).show();
						}
					},
					error: function(data)
					{
						//alert('false');
					}
				});
			}
		});
	},

	// trigger action click to button save category
	categorySettingSave : function()
	{
		$(UrbitHandler.selectors.categorySettingBtn).click(function(){
			// call validate form
			UrbitHandler.formValidate(UrbitHandler.selectors.formCategory);

			if( $(UrbitHandler.selectors.formCategory).valid() ){
				$.ajax({
					url:st.url.saveCategorySetting,
					//dataType: 'json',
					data : $(UrbitHandler.selectors.saveCategoryForm).serialize(),
					type: 'GET',
					success: function(data)
					{
						UrbitHandler.callBackReloadTab();
					},
					error: function(data)
					{
						//alert(data);
					}
				});
			}
		})
	},
// trigger action click to button save category
	categoryEditSettingSave : function()
	{
		$(document).on('click',UrbitHandler.selectors.categoryEditSettingBtn,{},function(){
			// call validate form
			UrbitHandler.formValidate(UrbitHandler.selectors.formEditCategory);

			if( $(UrbitHandler.selectors.formEditCategory).valid() ){
				$.ajax({
					url:st.url.saveCategorySetting,
					//dataType: 'json',
					data : $(UrbitHandler.selectors.saveEditCategoryForm).serialize(),
					type: 'GET',
					success: function(data)
					{
						UrbitHandler.callBackReloadTab();
					},
					error: function(data)
					{
						//alert(data);
					}
				});
			}
		})
	},

	// autocomplete
	autoComplete : function(selector, ajaxSearchUrl){
		$( selector ).autocomplete({

			source: function(value, response){
				$.ajax({
					url: ajaxSearchUrl,
					dataType: 'json',
					data: {keyword: value},
					type: 'post',
					success: function(data)
					{
						response(data);
					}
				});
			},
			focus: function(event, ui){
			   $( selector ).val(ui.item.label);
			   return false;
			},
			select: function(event, ui)
			{
				if (typeof(ui) != undefined)
				 {
					$( selector ).val(ui.item.label);
					$(UrbitHandler.selectors.idProduct).val(ui.item.value);
				 }
				return false;
		}
	 });
	},
        // autocomplete Post Code search in general tab
        autoCompletePostCodeSearch: function(selector,urlAjaxPostCode)
        {
            $(selector).autocomplete({
            minLength: 3,
            source: function( request, response ) {
                $.ajax({
                    url: urlAjaxPostCode,
                    data: {term: request.term},
                    dataType: "json",
                    success: function( data ) {
						
						var items = {};
						if (typeof data.locality[0] === 'object')
							items = data.locality;
						else
							items = data;
						
						response( $.map(items, function( item ) {
                            return {
                                label: item.postcode + " - " + item.location,
                                value: item.postcode
                            }
                        }));
                    },
                    error: function() {
                    }
                });
            }
            });
        },
	//	Call form validation plugin to validate form
	formValidate : function(selector)
	{
		if( typeof(selector) == 'undefined' ){
			selector = this.selectors.formValidate
		}
		$(selector).validate({
			rules: {
				"service[]": { required: true }
			},
			messages: {
				"service[]": "Please select at least one kind of Delivery Service"
			},
			errorPlacement: function(error, element) {
				if ($(element).hasClass("service")) {
					$(element).parent().append(error);
				} else {
					error.insertAfter(element);
				}
			}
		});
	},

	// Trigger active clue tips for instruction guide
	activeClueTips : function()
	{
		$(this.selectors.helpTip).cluetip({
			splitTitle: '|',
			showTitle: false,
			arrows: true
		});
	},
	// trigger action click edit a category
	loadEditCategory : function(id,ajax_url)
	{
		$.ajax({
			url: ajax_url,
			data: {
				id: id
			},
			type: 'post',
			success: this.callBackEditCategory,
			error: function(){
				//alert('Error when  load category');
				}
		});
	},
	// trigger action click edit a category
	actionDeleteCategory : function(id,ajax_url,msgConfirm)
	{
		confirmDeleteCategory = confirm(msgConfirm);
		if(confirmDeleteCategory)
		{
			$.ajax({
				url: ajax_url,
				data: {
					id: id
				},
				type: 'post',
				success: function(){
					UrbitHandler.callBackReloadTab();
				},
				error: function(){
					//alert('Error when  load category');
					}
			});
		}
	},
	//trigger action load edit product
	loadEditProduct : function(id_config, ajax_url)
	{
		$.ajax({
			url: ajax_url,
			data: {id_rate_config: id_config},
			type: 'post',
			success:this.callBackEditProduct,
			error: function(data){

			}
		})

	},
	loadDeleteProduct: function(id_config, ajax_url,msgConfirm)
	{
		var cf = confirm(msgConfirm);
		if(cf){
			$.ajax({
				url: ajax_url,
				data: {id_rate_config: id_config},
				type: 'post',
				success:function(data){
					UrbitHandler.callBackReloadTab();
				},
				error: function(data){

				}
			})
		}
	},

	// call back when select a element from list autocomplete
	callBackAutocompleteSearch : function(jsonData)
	{
		try
		{
			if (typeof window.stUiSearchTab != 'undefined')
				$(UrbitHandler.selectors.tabs).tabs('load',window.stUiSearchTab.index);
		}
		catch(e)
		{
			document.location.reload(true);
		};
	},
	// reload a tab
	callBackReloadTab : function()
	{
		var tabActive = versionCompare($.ui.version, '1.10') >= 0 ? 'active' : 'selected';
		var currentIndex = $(UrbitHandler.selectors.tabs).tabs("option",tabActive);
		$(UrbitHandler.selectors.tabs).tabs('load',currentIndex);

	},
	// call back when edit category -> get data of category fill to a form edit category
	callBackEditCategory : function(jsonData)
	{
		$(UrbitHandler.selectors.addCategory).hide();
		$(UrbitHandler.selectors.editCategory).html(jsonData);
	},
	// trigger action get content of product fill to form edit product
	callBackEditProduct: function(jsonData)
	{
		$(UrbitHandler.selectors.addProduct).hide();
		$(UrbitHandler.selectors.editProduct).html(jsonData);
	},
	// call back after save category
	callBackSaveCategory : function(jsonData)
	{
		UrbitHandler.callBackReloadTab();
	},
	// trigger action save general form
	generalSave: function()
	{
		$(UrbitHandler.selectors.submitgeneralSave).click(function(){
			// call validate form
			UrbitHandler.formValidate(UrbitHandler.selectors.formGeneral);

			if( $(UrbitHandler.selectors.formGeneral).valid() ) {
				$.ajax({
					url:st.url.saveGeneral,
					dataType: 'json',
					data : $(UrbitHandler.selectors.saveGeneralForm).serialize(),
					type: 'post',
					success: function(data)
					{
						UrbitHandler.callBackReloadTab();
					}
				});
			}
		})
	},
	// callback to update module status block
	updateModuleStatus: function()
	{
		$.ajax({
			url:st.url.updateModuleStatus,
			dataType: 'html',
                        beforeSend: function()
                        {
                           $(UrbitHandler.selectors.moduleStatus).html('');

                        },
			success: function(response)
			{
				$(UrbitHandler.selectors.moduleStatus).slideUp();
				$(UrbitHandler.selectors.moduleStatus).html(response);
				$(UrbitHandler.selectors.moduleStatus).slideDown();
			}
		});
	},
        // get tallest of divs befor add tallest for all div
        autoEqualHeightDiv: function()
        {
            // reset height of fieldset
            $('.urbit_genaral fieldset').height('auto');
            var tallest = 0;
            var width_screen = document.body.clientWidth;
            // 3 columns
            if (width_screen >= 974)
            {
                $('.urbit_genaral fieldset').each(function()
                {
                   thisHeight = $(this).height();
                   if(thisHeight > tallest)
                   {
                      tallest = thisHeight;
                   }
                });
                $('.urbit_genaral fieldset').height(tallest);
            }
            // 2 columns
            else if (width_screen < 974 & width_screen > 662)
            {
                $('.urbit_fieldset').each(function()
                {
                   thisHeight = $(this).height();
                   if(thisHeight > tallest)
                   {
                      tallest = thisHeight;
                   }
                });
                $('.urbit_fieldset').height(tallest);
            }
        },
        eventPackage: function()
        {
            // catch the event ajax when change the value product length
            window.package = new Package();
            var validTag = '';
            $(UrbitHandler.selectors.productLength)
                .on('blur',function()
                {
                    window.package.product_length = $(this).val();
                    validTag = window.package.isValidate();
                    UrbitHandler.displayError(this,validTag);
                })
                .on('focus',function(){
                    $(this).removeClass('error');
           });
           $(UrbitHandler.selectors.productHeight)
                .on('blur',function()
                {
                    window.package.product_height = $(this).val();
                    validTag = window.package.isValidate();
                    UrbitHandler.displayError(this,validTag);
                })
                .on('focus',function(){
                    $(this).removeClass('error');
           });
           $(UrbitHandler.selectors.productWidth)
                .on('blur',function()
                {
                    window.package.product_width = $(this).val();
                    validTag = window.package.isValidate();
                    UrbitHandler.displayError(this,validTag);
                })
                .on('focus',function(){
                    $(this).removeClass('error');
           });
           $(UrbitHandler.selectors.productWeight)
                .on('blur',function()
                {
                    window.package.product_weight = $(this).val();
                    validTag = window.package.isValidate();
                    UrbitHandler.displayError(this,validTag);
                })
                .on('focus',function(){
                    $(this).removeClass('error');
           });
           $(UrbitHandler.selectors.packageLength)
                .on('blur',function()
                {
                    window.package.package_length = $(this).val();
                    validTag = window.package.isValidate();
                    UrbitHandler.displayError(this,validTag);
                })
                .on('focus',function(){
                    $(this).removeClass('error');
           });
           $(UrbitHandler.selectors.packageWidth)
                .on('blur',function()
                {
                    window.package.package_width = $(this).val();
                    validTag = window.package.isValidate();
                    UrbitHandler.displayError(this,validTag);
                })
                .on('focus',function(){
                    $(this).removeClass('error');
           });
           $(UrbitHandler.selectors.packageHeight)
                .on('blur',function()
                {
                    window.package.package_height = $(this).val();
                    validTag = window.package.isValidate();
                    UrbitHandler.displayError(this,validTag);
                })
                .on('focus',function(){
                    $(this).removeClass('error');
           });
           $(UrbitHandler.selectors.packageMargin)
                .on('blur',function()
                {
                    window.package.package_margin = $(this).val();
                    validTag = window.package.isValidate();
                    UrbitHandler.displayError(this,validTag);
                })
                .on('focus',function(){
                    $(this).removeClass('error');
           });
		   $(UrbitHandler.selectors.weightUnit)
                .on('blur',function()
                {
                    window.package.weight_unit = $(this).val();
					validTag = window.package.isValidate();
                    UrbitHandler.displayError(this,validTag);
                })
                .on('focus',function(){
                    $(this).removeClass('error');
           });
        },
        displayError: function (idInput, validTag){
            switch (validTag)
            {
                case 1:
                    $(idInput).addClass('error');
                    $(UrbitHandler.selectors.urbitConfigForm).attr("disabled", "disabled");
                    break;
                case 2:
                    $(idInput).addClass('error');
                    $(UrbitHandler.selectors.urbitConfigForm).attr("disabled", "disabled");
                    break;
                case 3:
                    $(UrbitHandler.selectors.urbitPackageInput).addClass('error');
                    $(UrbitHandler.selectors.urbitConfigForm).attr("disabled", "disabled");
                    break;
		case 4:
                    $(UrbitHandler.selectors.weightUnit).addClass('error');
		    $(UrbitHandler.selectors.productWeight).addClass('error');
		    $(UrbitHandler.selectors.urbitConfigForm).attr("disabled", "disabled");
                    break;
                default: // vailidTag = 0
                    $(UrbitHandler.selectors.urbitTableInput).removeClass('error');
		    $(UrbitHandler.selectors.productWeight).removeClass('error');
		    $(UrbitHandler.selectors.weightUnit).removeClass('error');
                    $(UrbitHandler.selectors.urbitConfigForm).removeAttr("disabled", "");
                    break;
            }
        },
        getPackage: function()
        {
			window.package.weight_unit = $(UrbitHandler.selectors.weightUnit).val();
            if ($(UrbitHandler.selectors.productLength).val() !== 'undefined')
            {
                window.package.product_length = $(UrbitHandler.selectors.productLength).val();
            }
            if ($(UrbitHandler.selectors.productWidth).val() !== 'undefined')
            {
                window.package.product_width = $(UrbitHandler.selectors.productWidth).val();
            }
            if ($(UrbitHandler.selectors.productHeight).val() !== 'undefined')
            {
                window.package.product_height = $(UrbitHandler.selectors.productHeight).val();
            }
            if ($(UrbitHandler.selectors.productWeight).val() !== 'undefined')
            {
                window.package.product_weight = $(UrbitHandler.selectors.productWeight).val();
            }
            if ($(UrbitHandler.selectors.packageLength).val() !== 'undefined')
            {
                window.package.package_length = $(UrbitHandler.selectors.packageLength).val();
            }
            if ($(UrbitHandler.selectors.packageWidth).val() !== 'undefined')
            {
                window.package.package_width = $(UrbitHandler.selectors.packageWidth).val();
            }
            if ($(UrbitHandler.selectors.packageHeight).val() !== 'undefined')
            {
                window.package.package_height = $(UrbitHandler.selectors.packageHeight).val();
            }
            if ($(UrbitHandler.selectors.packageMargin).val() !== 'undefined')
            {
                window.package.package_margin = $(UrbitHandler.selectors.packageMargin).val();
            }
        }
};
//


// controller::init
$(document).ready(function(){
	UrbitHandler.tabs();
	// register product save action
	UrbitHandler.productSave();
	UrbitHandler.productEdit();
	UrbitHandler.categoryEditSettingSave();
});
$(window).resize(function() {
    UrbitHandler.autoEqualHeightDiv();
});