function getCalcId() {
	const urlParams = new URLSearchParams(window.location.search);
    const calcId = urlParams.get('id_form');
	return calcId;
}
function sccGetOffset(el) {
	const rect = el.getBoundingClientRect();
	return {
	  left: rect.left + window.scrollX,
	  top: rect.top + window.scrollY
	};
}
function isInsideEditingPage() {
	const urlParams = new URLSearchParams(window.location.search);
	const pageName = urlParams.get('page');
	if (pageName == 'Stylish_Cost_Calculator_EditItems') {
		return true;
	} else {
		return false;
	}
}

/* Tool Tip for added Elements*/
const elementTooltips = {
	dropdown: {
	  msg: `<h4 class='text-start'><b>Dropdown</b> Element</h4>
				  <p class='text-start mt-2'>The dropdown element is used to create a drop-down list.</p>
				  <p class='text-start mb-0'>1. Use it when a user is only allowed to pick one selection. Use the checkbox element if the user is allowed to pick more than one.</p>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Quantity Multiplier</h5>
					  <p>You can attach a slider element below a dropdown menu to act as a multiplier.</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001207045-element-dropdown-menu" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'slider-element': {
	  msg: `<h4 class='text-start'><strong>Slider</strong> Element</h4>
				  <p class='text-start mt-2'>Sliders are linked to any elements in the same subsection.</p>
				  <p class='text-start mb-0'>1. It can act as a multiplier of other elements</p>
				  <p class='text-start mb-0'>2. It can act as a product selector. Buy X amount of item</p>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Example</h5>
					  <p>If you select 10 units with the slider, it will add 10 units to any dropdown
					  or checkbox element above it. If you want to unlink them, make sure the slider sits in it's
					  own subsection.</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001207026-element-slider" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'checkbox-buttons': {
	  msg: `<h4 class='text-start'><b>Checkbox</b> Element</h4>
				  <p class='text-start mt-2'>The checkbox is shown as a square box that is ticked (checked) when activated.
				  Checkboxes are used to let a user select one or more options of a limited number of choices.</p>
				  <div class="example-description text-start">
				  <h5 class="mt-3">Other Styles</h5>
				  <p class='text-start mb-0'>1. Circle & Square Checkboxes - more than one is allowed</p>
				  <p class='text-start mb-0'>2. Simple Buttons - more than one option is allowed</p>
				  <p class='text-start mb-0'>3. Toggle Switches -  more than one option is allowed</p>
				  <p class='text-start mb-0'>5. Radio Buttons - only one option is allowed</p>
				  <p class='text-start mb-0'>6. Image Buttons -  more than one option is allowed</p>
			  </div>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Quantity Multiplier</h5>
					  <p>You can attach a slider element below a checkbox to act as a multiplier of the checkbox.</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001207024-checkbox-toggle-switches-simple-buttons-element" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'radio-buttons': {
	  msg: `<h4 class='text-start'><b>Radio Buttons</b> Element</h4>
				  <p class='text-start mt-2'>Radio buttons allow the user to select one option from a list of mutually exclusive options. Radio buttons are unique in that users cannot select or deselect any quantity of items, unlike checkboxes. </p>
				  <div class="example-description text-start">
				  <h5 class="mt-3">Other Styles</h5>
				  <p class='text-start mb-0'>1. Circle & Square Checkboxes - more than one is allowed</p>
				  <p class='text-start mb-0'>2. Simple Buttons - more than one option is allowed</p>
				  <p class='text-start mb-0'>3. Toggle Switches -  more than one option is allowed</p>
				  <p class='text-start mb-0'>5. Radio Buttons - only one option is allowed</p>
				  <p class='text-start mb-0'>6. Image Buttons -  more than one option is allowed</p>
			  </div>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Quantity Multiplier</h5>
					  <p>You can attach a slider element below to act as a multiplier of the radio button.</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001207024-checkbox-toggle-switches-simple-buttons-element" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'simple-buttons': {
	  msg: `<h4 class='text-start'><b>Simple Buttons</b> Element</h4>
				  <p class='text-start mt-2'>Looks just like a regular CTA button on your website. These buttons are displayed in-line (side by side). </p>
				  <div class="example-description text-start">
				  <h5 class="mt-3">Other Styles</h5>
				  <p class='text-start mb-0'>1. Circle & Square Checkboxes - more than one is allowed</p>
				  <p class='text-start mb-0'>2. Simple Buttons - more than one option is allowed</p>
				  <p class='text-start mb-0'>3. Toggle Switches -  more than one option is allowed</p>
				  <p class='text-start mb-0'>5. Radio Buttons - only one option is allowed</p>
				  <p class='text-start mb-0'>6. Image Buttons -  more than one option is allowed</p>
			  </div>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Quantity Multiplier</h5>
					  <p>You can attach a slider element below to act as a multiplier of any selected simple button.</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001207024-checkbox-toggle-switches-simple-buttons-element" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'toggle-switches': {
	  msg: `<h4 class='text-start'><b>Toggle Switches</b> Element</h4>
				  <p class='text-start mt-2'>A toggle switch allows users to choose between two opposing states, such as on or off. If there are multiple options, its best to use something else.Another benefit to toggle switches is that they work immediately. Radio buttons or checkboxes need users to hit a submit button before the choice goes into effect. </p>
				  <div class="example-description text-start">
				  <h5 class="mt-3">Other Styles</h5>
				  <p class='text-start mb-0'>1. Circle & Square Checkboxes - more than one is allowed</p>
				  <p class='text-start mb-0'>2. Simple Buttons - more than one option is allowed</p>
				  <p class='text-start mb-0'>3. Toggle Switches -  more than one option is allowed</p>
				  <p class='text-start mb-0'>5. Radio Buttons - only one option is allowed</p>
				  <p class='text-start mb-0'>6. Image Buttons -  more than one option is allowed</p>
				  </div>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Quantity Multiplier</h5>
					  <p>You can attach a slider element below to act as a multiplier of any selected toggle switch.</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001207024-checkbox-toggle-switches-simple-buttons-element" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'image-buttons': {
	  msg: `<h4 class='text-start'><b>Image Buttons</B> Element</h4>
				  <p class='text-start mt-2'>Create a button using an image instead of text.</p>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Other Styles</h5>
					  <p class='text-start mb-0'>1. Circle & Square Checkboxes - more than one is allowed</p>
					  <p class='text-start mb-0'>2. Simple Buttons - more than one option is allowed</p>
					  <p class='text-start mb-0'>3. Toggle Switches -  more than one option is allowed</p>
					  <p class='text-start mb-0'>5. Radio Buttons - only one option is allowed</p>
					  <p class='text-start mb-0'>6. Image Buttons -  more than one option is allowed</p>
				  </div>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Quantity Multiplier</h5>
					  <p>You can attach a slider element below to act as a multiplier of any clicked Image Button.</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001207056-element-image-buttons" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'default-checkbox': {
	  msg: `<h4 class='text-start'><b>Checkbox</b> Element</h4>
				  <p class='text-start mt-2'>Sliders are linked to any elements in the same subsection.</p>
				  <p class='text-start mb-0'>1. It can act as a multiplier of other elements</p>
				  <p class='text-start mb-0'>2. It can act as a product selector. Buy X amount of item</p>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Example</h5>
					  <p>If you select 10 units with the slider, it will add 10 units to any dropdown
					  or checkbox element above it. If you want to unlink them, make sure the slider sits in it's
					  own subsection.</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001207024-checkbox-toggle-switches-simple-buttons-element" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'comment-box': {
	  msg: `<h4 class='text-start'><b>Comment Box</b> Element</h4>
				  <p class='text-start mt-2'>The Comment Box is basically any user input field.</p>
				  <p class='text-start mb-0'>1. Collect a date</p>
				  <p class='text-start mb-0'>2. Collect name, address, phone numbers</p>
				  <p class='text-start mb-0'>3. Ask for more information for a interested product or service</p>
				  <div class="example-description text-start">
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001207024-checkbox-toggle-switches-simple-buttons-element" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'quantity-input-box': {
		msg: `<h4 class='text-start'><b>Number Input </b> Element</h4>
					<p class='text-start mt-2'>Add a number (quantity) input field to your calculator form. </p>
					<p class='text-start mb-0'>1. Sliders will multiply the Number Input quantity if they are in the same subsection.</p>
					<div class="example-description text-start">
						<h5 class="mt-3">Example</h5>
						<p>For example, if a user enter 10 quantity in the number input box, and the slider is set to 5, it will 5 x 10 = 50. If you don't want a slider to affect this input, just make sure no slider is in the same subsection</p>
						<br>
						<a href="https://designful.freshdesk.com/en/support/solutions/articles/48001081164-quantity-box-element-how-does-it-work-" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
					</div>`,
		coverImage:
		  'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	  },
	'custom-math': {
	  msg: `<h4 class='text-start'><b>Custom Math</b></h4>
				  <p class='text-start mt-2'>This is not a user element, but math that works in the background. Use this to control the math of the subsections total price.</p>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Use Cases</h5>
					  <p class='text-start mb-0'>1. Add shipping cost</p>
					  <p class='text-start mb-0'>2. Add admin fee</p>
					  <p class='text-start mb-0'>3. Give a bundle discount</p>
					  <p class='text-start mb-0'>4. Trigger a fee if another item is select</p>
					  <p class='text-start mb-0'>5. Trigger a discount under certain conditions</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001142309-custom-math-learn-more-about-this-feature-and-function" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'variable-math': {
	  msg: `<h4 class='text-start'><b>Variable Math</b> Element</h4>
				  <p class='text-start mt-2'>This element comes in the form of a Slider or Quantity Box, however, it gives you the ability to add variables to control the final price.</p>
				  <div class="example-description text-start">
				  <h5 class="mt-3">How to Use</h5>
					  <p class='text-start mb-0'>For the total calculation. You must use the exact words of Input1, Input2, Input3, etc. Do not use the name of the item</p>
					  <p class='text-start mb-0'>Example 1: Input 1 / Input 2</p>
					  <p class='text-start mb-0'>Example 2: Input 1 + Input 2 + Input3 </p>
					  <p class='text-start mb-0'>Example 3: (Input 1 * Input 2) / 2</p>
					  <p class='text-start mb-0'>Example 4: (Input 1 / Input 2) * 2</p>
				  </div>
        		  <div class="example-description text-start">
					  <h5 class="mt-3">Use Cases</h5>
					  <p class='text-start mb-0'>1. Bundle discounts</p>
					  <p class='text-start mb-0'>2. Shipping Cost</p>
					  <p class='text-start mb-0'>3. Length X Width X Height</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001202286-element-variable-math" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'file-upload': {
	  msg: `<h4 class='text-start'><b>File Upload</b> Element</h4>
				  <p class='text-start mt-2'>Add a file upload element to your calculator forms.</p>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Use Cases</h5>
					  <p class='text-start mb-0'>1. Request images</p>
					  <p class='text-start mb-0'>1. Request documents</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001166819-element-file-upload" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'text-html-field': {
	  msg: `<h4 class='text-start'><b>Text/HTML</b> Field</h4>
				  <p class='text-start mt-2'>Add raw text or HTML to your calculator form.</p>
				  <div class="example-description text-start">
					  <h5 class="mt-3">Use Cases</h5>
					  <p class='text-start mb-0'>1. Add a title for checkboxes</p>
					  <p class='text-start mb-0'>2. Display a message in red writing</p>
			<p class='text-start mb-0'>3. Use conditional logic to alert people under certain conditions</p>
					  <br>
					  <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001207057-element-text-html-field" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
				  </div>`,
	  coverImage:
		'images/tooltip-images/for-elements/infographic-elmnt-custom-math.png',
	},
	'slider-type-bulk': {
		msg: `<h4 class='text-start'><b>Bulk Quantity Discounts</b></h4>
		<p class='text-start mt-2'>Set one or more price ranges and apply discount for bulk purchases</p>
		<div class="example-description text-start">
						<h5 class="mt-3">Example</h5>
						<h5 class='mt-2'>Price ranges:</h5>
						<p class='text-start mb-0'>Between 1 and 5 units = $50 (per unit price)</p>
						<p class='text-start mb-0'>Between 6 and 10 units = $45 (per unit price)</p>
						<p class='text-start mb-0'>Between 11 and 20 units = $45 (per unit price)</p>
						<h5 class='mt-2'>Prices:</h5>
						<p class='text-start mb-0'>3 Units = $150 (total price)</p>
						<p class='text-start mb-0'>8 Units = $360 (total price)</p>
						<p class='text-start mb-0'>15 Units = $675 (total price)</p>
						<br>
						<a href="https://designful.freshdesk.com/a/solutions/articles/48001079780" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
					</div>`
	  },
	  'slider-type-default': {
		msg: `<h4 class='text-start'><b>Default</b></h4>
		<p class='text-start mt-2'>Multiply the product quantity by the slider number</p>
		<div class="example-description text-start">
			  
						<h5 class="mt-3">Example</h5>
						<h5 class='mt-2'>Product price:$100</h5>
						<p class='text-start mb-0'>3 Units = $300 total price</p>
						<p class='text-start mb-0'>4 Units = $400 total price</p>
						<p class='text-start mb-0'>5 Units = $500 total price</p>
						<br>
						<a href="https://designful.freshdesk.com/a/solutions/articles/48001079780" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
					</div>`
	  },
	  'slider-type-quantity_mod': {
		msg: `<h4 class='text-start'><b>Element Quantity modifier</b></h4>
		<p class='text-start mt-2'>Modifies quantity value of the elements available on the subsection</p>
		<div class="example-description text-start">
			  <h5 class="mt-3">Example</h5>
			  <p class='text-start mb-0'>If you have a dropdown on the subsection, the slider will multiply the quantity by the value returned by the slider</p>
						<br>
						<a href="https://designful.freshdesk.com/a/solutions/articles/48001079780" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
					</div>`
	  },
	  'slider-type-sliding': {
		msg: `<h4 class='text-start'><b>Sliding Quantity Discounts</b></h4>
		<p class='text-start mt-2'>Set a definite price for a set of quantity ranges</p>
		<div class="example-description text-start">
			  <p class='text-start mb-0'>Between 1 and 10 = $100 (flat price)</p>
			  <p class='text-start mb-0'>Between 11 and 20 = $200 (flat price)</p>
			  <p class='text-start mb-0'>Between 21 and 30 = $300 (flat price)</p>
	
			  <h5 class="mt-3">Example</h5>
			  <p class='text-start mb-0'>3 Units = $100 (total price)</p>
			  <p class='text-start mb-0'>15 Units =$200 (total price)</p>
			  <p class='text-start mb-0'>25 Units= $300 (total price)</p>
						<br>
						<a href="https://designful.freshdesk.com/a/solutions/articles/48001079780" target="_blank"><div class="btn btn-primary btn-lg">Learn more</div></a>
					</div>`
	  }
  }

  const needLicenseKeyTooltip = `You need to purchase a <a href="https://stylishcostcalculator.com/" target="_blank">premium license</a> to use this feature.`
  
// Upload button click
var handleDropdownLogoSetup = function ($this ) {
    event.preventDefault();
    formField = jQuery($this);
    if ( window.hasOwnProperty('mediaUploader') ) {
        mediaUploader.open();
        return;
    }
    mediaUploader = wp.media.frames.file_frame = wp.media( {
        title: 'Choose Image',
        button: {
            text: 'Choose Image'
        }, multiple: false
    } );
    mediaUploader.on( "select", onMediaImageSelect );
    mediaUploader.open();
}
function removeDropdownImage($this, idFromDataAttribute = false) {
    $this = jQuery($this);
	var imgPlaceholder = $this.prev('.scc-image-picker');
	imgPlaceholder.attr('src', df_scc_resources.dropdownTumbnailDefaultImage);
	var id_elementitem = $this.closest('.selopt3').find(".swichoptionitem_id").val();
	if (idFromDataAttribute) {
		id_elementitem = $this.closest(".dd-item-field-container").data('elementItemId')
	}
	jQuery.ajax({
		url: ajaxurl,
		cache: false,
		data: {
			action: 'sccUpElementItemSwichoption',
			id_elementitem: id_elementitem,
			image: '',
			nonce: pageEditCalculator.nonce
		},
		success: function(data) {
			if (data.passed == true) {
				showSweet(true, "The changes have been saved.")
			} else {
				showSweet(false, "There was an error, please try again")
			}
		}
	})
}
function resizeImage( url, callback ) {
    var data = {
        action: "scc_handle_dropdown_logo",
        data: url
    };
    jQuery.post( ajaxurl, data, callback );
}
/**
 * On media image select
 */
function onMediaImageSelect() {
    var attachment = mediaUploader.state().get('selection').first().toJSON()
    var field = formField;
    field.attr('src', attachment.sizes.thumbnail.url );
    resizeImage(attachment.sizes.thumbnail.url, (data) => {
        var element = field.attr('src', data.link ).data('hasImage', true);
        if (!element.next('span').length) element.after(jQuery('<span class="scc-dropdown-image-remove" onclick="removeDropdownImage(this)">x</span>'));
    });
}
// Functions to change the backend title while typing.
function changeElementTitleSlider($this){
    var changeBackendTitleElement = jQuery($this).closest(".slider-section-container").prev().find(".scc-element-title")
    var currentBackendInputTitle = truncateElementTitle(jQuery($this).val(),20)
    changeBackendTitleElement.text(currentBackendInputTitle)
}
function changeElementTitle($this){
    var changeBackendTitleElement = jQuery($this).closest(".elements_added").find(".scc-element-title")
    var currentBackendInputTitle = truncateElementTitle(jQuery($this).val(),20)
    changeBackendTitleElement.text(currentBackendInputTitle)
}
function changeElementTitleCustomMath($this) {
    var changeBackendTitleElement = jQuery($this).closest('.scc_custom_math').find('.scc-element-title')
    var currentBackendInputTitle = truncateElementTitle(jQuery($this).val(),20)
    changeBackendTitleElement.text(currentBackendInputTitle)
}
function truncateElementTitle(str, n){
  return (str.length > n) ? str.substr(0, n-1) + '..' : str;
};
/** receives the source element and event from the HTML tag it
 * was referrenced from
 * @param {Object} element  - this is the source HTML tag from where the function is initiated
 * @param {Object} event - this is event caused by the click
 */
function doFormFieldsSetup(element, event, isPremium) {
	if (!isPremium) {
		return
	}
    return
}
function addEventsToQuoteFormBtns(elements) {
    elements.click(($this) => {
		if (jQuery($this).closest('.card-action-btns').hasClass('disabled')) {
			return
		}
        switch (jQuery($this.target).data('formBuilderActionType')) {
            case 'edit':
            console.log('edit action');
            doFormFieldsSetup($this.currentTarget, $this, true);
            break;
            case 'delete':
            console.log('delete action');
            let fieldKey = jQuery($this.currentTarget).data('fieldKey');
            if (fieldKey) delete formFieldsArray[_.findKey(formFieldsArray, fieldKey)];
            jQuery($this.currentTarget).remove();
            break;
            default:
            // jQuery($this.currentTarget).toggleClass('active');
            break;
        }
    })
}
// handles quote custom field setup form's delete button
function handleQuoteFieldDeletion($this) {
    $this = jQuery($this);
    let fieldKey = $this.data('fieldKey');
    const urlParams = new URLSearchParams(window.location.search);
    const calcId = urlParams.get('id_form');
    let data = {
        action : 'sccQuoteFieldDeletion',
        id_form: calcId,
        fieldKey: fieldKey
    }
    jQuery.ajax({
        url: ajaxurl,
        data: data,
        type: 'POST',
        context: $this,
		beforeSend: function() {
			this.find(".df-scc-euiButtonContent.df-scc-euiButtonEmpty__content").html(`<div>
			<svg aria-hidden="true" style="width: 1em" focusable="false" data-prefix="fas" data-icon="spinner" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-inline--fa fa-spinner fa-w-16 fa-spin"><path fill="currentColor" d="M304 48c0 26.51-21.49 48-48 48s-48-21.49-48-48 21.49-48 48-48 48 21.49 48 48zm-48 368c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zm208-208c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.49-48-48-48zM96 256c0-26.51-21.49-48-48-48S0 229.49 0 256s21.49 48 48 48 48-21.49 48-48zm12.922 99.078c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.491-48-48-48zm294.156 0c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48c0-26.509-21.49-48-48-48zM108.922 60.922c-26.51 0-48 21.49-48 48s21.49 48 48 48 48-21.49 48-48-21.491-48-48-48z" class=""></path></svg>
			</div>
			<span class="trn df-scc-euiButtonEmpty__text">Deleting...</span>`);
			debugger
		},
        success: function(data) {
            if (data.passed == true) {
                this.closest('[role=dialog]').modal('hide');
                let fieldKey = data.key;
                jQuery(`[data-field-key=${fieldKey}]`, '.editing-action-cards.action-quoteform').remove();
                showSweet(true, "The changes have been saved.")
            } else {
                showSweet(false, data.message);
            }
        }
    });
}
function paypalFormValidation() {
	var paypalEmail = jQuery("#paypal_email_form").val();
	var paypalShoppingCartName = jQuery("#paypal_shopping_cart_name_form").val();
	var paypalCurrency = "";
	jQuery.each(jQuery("#paypal_currency_form option:selected"), function () {
	  paypalCurrency = jQuery(this).val();
	});
	var paypalSuccessURL = jQuery("#paypal_shopping_cart_success_url_form").val();
	var paypalCancelURL = jQuery("#paypal_shopping_cart_cancel_url_form").val();
	var paypalIncludeTax = Boolean(
	  jQuery("#paypal_tax_inclusion_settings_form").prop("checked")
	);
	var noValidMessage = false;
	if (
	  paypalEmail == null ||
	  typeof paypalEmail == "undefined" ||
	  paypalEmail.length < 5 ||
	  paypalEmail.indexOf("@") == -1 ||
	  paypalEmail.indexOf(".") == -1
	) {
	  noValidMessage = "Invalid Email!";
	  jQuery("#paypal_email_form")
		.closest(".df-scc-euiFormRow__fieldWrapper")
		.find("span.text-danger")
		.show();
	}
	if (
	  paypalShoppingCartName == null ||
	  typeof paypalShoppingCartName == "undefined" ||
	  paypalShoppingCartName.length < 2
	) {
	  noValidMessage === false
		? (noValidMessage = " | Invalid Shopping card Name!")
		: (noValidMessage += " | Invalid Shopping card Name!");
	  jQuery("#paypal_shopping_cart_name_form")
		.closest(".df-scc-euiFormRow__fieldWrapper")
		.find("span.text-danger")
		.show();
	}
	if (
	  paypalCurrency == null ||
	  typeof paypalCurrency == "undefined" ||
	  paypalCurrency == "0"
	) {
	  noValidMessage === false
		? (noValidMessage = " | Select a Currency!")
		: (noValidMessage += " | Select a Currency!");
	  jQuery("#paypal_currency_form")
		.closest(".df-scc-euiFormRow")
		.find("span.text-danger")
		.show();
	}
	// https://stackoverflow.com/questions/5717093/check-if-a-javascript-string-is-a-url
	var pattern = new RegExp(
	  "^(https?:\\/\\/)?" + // protocol
		"((([a-z\\d]([a-z\\d-]*[a-z\\d])*)\\.)+[a-z]{2,}|" + // domain name
		"((\\d{1,3}\\.){3}\\d{1,3}))" + // OR ip (v4) address
		"(\\:\\d+)?(\\/[-a-z\\d%_.~+]*)*" + // port and path
		"(\\?[;&a-z\\d%_.~+=-]*)?" + // query string
		"(\\#[-a-z\\d_]*)?$",
	  "i"
	); // fragment locator
	if (paypalSuccessURL.length && !pattern.test(paypalSuccessURL)) {
	  noValidMessage === false
		? (noValidMessage = " | Invalid URL!")
		: (noValidMessage += " | Invalid URL!");
	  jQuery("#paypal_shopping_cart_success_url_form")
		.closest(".df-scc-euiFormRow__fieldWrapper")
		.find("span.text-danger")
		.show();
	}
	if (paypalCancelURL.length && !pattern.test(paypalCancelURL)) {
	  noValidMessage === false
		? (noValidMessage = " | Invalid URL!")
		: (noValidMessage += " | Invalid URL!");
	  jQuery("#paypal_shopping_cart_cancel_url_form")
		.closest(".df-scc-euiFormRow__fieldWrapper")
		.find("span.text-danger")
		.show();
	}
	if (!noValidMessage) {
	  return true;
	} else {
	  // jQuery('#paypal_modal_form_no_valid_fields_message').html(noValidMessage.replace('false', ''))
	  return false;
	}
  }
function doPaypalSetupModal(calcId) {
	return
}
// in progress code not being used
function registerWebhookActions (calcId) {
	let {webhookConfig} = sccData[calcId].config;
    webhookConfig.map(e => Object.keys(e)[0]).forEach(webhookCtx => {
        let { enabled, webhook } = webhookConfig.filter(ee => (Object.keys(ee)[0] == webhookCtx))[0][webhookCtx];
        let webhookCtxNode = jQuery(`#${webhookCtx}`);
        webhookCtxNode.prop('checked', enabled);
        let webhookCtxLink = jQuery(`[data-event-type=${webhookCtxNode.data('target')}`);
        webhookCtxLink.data('webhook', webhook);
    })
}
// handles on/ off actions of the webhook setup section
jQuery('#calc-settings-webhook input').on('click', (event) => {
	var $this = jQuery(event.target);
	var target = $this.data('target');
	var webhookEditBtn = jQuery(`[data-event-type=${target}`);
	if (webhookEditBtn.data('webhook')) {
		saveWebhookSettings();
	} else {
		event.preventDefault();
		// prompt for a webhook form
		webhookEditBtn.click();
	}
})
jQuery('#calc-settings-webhook i.material-icons:not(.disabled)').on('click', (event) => {
	let eventTitles = {
		"quote-fillup": "Quote Fillup Webhook",
		"detail-btn": "Detailed View Button Webhook",
		"payment-btn": "Payment Button Webhook"
	}
	let eventType = jQuery(event.target).data('eventType');
	let currentWebhookEndpoint = jQuery(event.target).data('webhook');
	let webhookSetupForm = wp.template('scc-webhook-setup')({
		title: eventTitles[eventType],
		webhookEndPoint: currentWebhookEndpoint
	});
	jQuery('#webhook-setup-placeholder').html(webhookSetupForm).modal('show');
	jQuery('#webhook-setup-placeholder').find('form').data('modalSource', event.target);
});
function handleWebHookSetup($this) {
	$this = jQuery($this);
	var modalSource = jQuery($this.data('modalSource'));
	var data = new FormData($this[0]);
	var webhook = data.get('webhook-link');
	modalSource.data('webhook', webhook);
	if (!webhook.length) {
		let relatedSwitchBtn = modalSource.data('eventType');
		jQuery(`[data-target=${relatedSwitchBtn}]`).prop('checked', false);
	}
	modalSource.trigger('webhookSetupDone');
	setTimeout(() => {
		saveWebhookSettings();
	}, 300);
	jQuery('#webhook-setup-placeholder').modal('hide');
}
function saveWebhookSettings () {
	const urlParams = new URLSearchParams(window.location.search);
    const calcId = urlParams.get('id_form');
	let newWebhookConfig = [
		{
			'scc_set_webhook_quote' : {
				enabled: jQuery('#scc_set_webhook_quote').prop('checked'),
				webhook: jQuery('[data-event-type="quote-fillup"]').data('webhook')
			}
		},
		{
			'scc_set_webhook_detail_view' : {
				enabled: jQuery('#scc_set_webhook_detail_view').prop('checked'),
				webhook: jQuery('[data-event-type="detail-btn"]').data('webhook')
			}
		}
	];
	jQuery.ajax({
		url: ajaxurl + '?action=sccSaveWebhookConfig' + '&id=' + calcId,
		contentType: 'json',
		type: 'POST',
		calcId,
		data: JSON.stringify(newWebhookConfig),
		success: () => {
			showSweet(true, "The changes have been saved.");
		}
	});
}
function stripeOptionsModal($this) {
	return
}
function setupStripeKey($this) {
	const urlParams = new URLSearchParams(window.location.search);
    const calcId = urlParams.get('id_form');
	var modalObject = jQuery($this).closest('.df-scc-euiModal');
	var privKey = jQuery('[name="stripe-api-priv-key"]').val();
	var pubKey = jQuery('[name="stripe-api-pub-key"]').val();
	if (privKey.length == 0) {
		jQuery('[name="stripe-api-priv-key"]').closest('.df-scc-euiFormControlLayout').next('.text-danger').show().hide(3000);
	}
	if (pubKey.length == 0) {
		jQuery('[name="stripe-api-pub-key"]').closest('.df-scc-euiFormControlLayout').next('.text-danger').show().hide(3000);
	}
	if(privKey.length == 0 || pubKey.length == 0) {
		return;
	}
	var keyInputVal = {
		privKey: privKey,
		pubKey: pubKey,
		enabled: true
	};
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		context: modalObject,
		calcId,
		data: {
			action: 'scc_set_stripe_key',
			data: keyInputVal
		},
		beforeSend: function(xhr) {
			this.find('.df-scc-euiButtonContent.df-scc-euiButton__content').html('<i class="fa fa-circle-o-notch fa-spin"></i><span class="trn df-scc-euiButton__text">Saving...</span>')
		},
		success: function(data) {
			this.find('.df-scc-euiModalFooter').hide();
			this.find('.df-scc-euiText.df-scc-euiText--medium').html("<p>The Stripe API key has been saved. You can change the Stripe API key from the 'Global Settings' menu</p>")
			jQuery('#stripe_checkbox').prop('checked', true);
			var sourceBtn = jQuery('.editing-action-cards.action-payment [data-btn-type="stripe"]');
			var hasStripeKeys = null;
			if (sourceBtn  && privKey.length && pubKey.length) {
				jQuery(sourceBtn)
					.addClass('active')
					.attr('onclick', 'toggleStripe(this)')
					.attr('data-pub-key', pubKey)
					.attr('data-priv-key', privKey);
				hasStripeKeys = true;
			}
			hasStripeKeys && loadPreviewForm(this.calcId);
			// close the success notice
			setTimeout(() => {
				this.find('.df-scc-euiButtonIcon.df-scc-euiButtonIcon--text.df-scc-euiModal__closeIcon').click();
			}, 5000);
		},
		error: function(error){
			console.log(error);
			this.find('.text-danger').show();
			setTimeout(() => {
				this.find('.text-danger').hide();
			}, 3000);
		}
	});
}
function toggleStripe($this) {
	const urlParams = new URLSearchParams(window.location.search);
    const calcId = urlParams.get('id_form')
	$this = jQuery($this);
	let privKey = $this.data('privKey');
	let pubKey = $this.data('pubKey');
	let newStatus = !$this.hasClass('active');
	let keyInputVal = {
		privKey: privKey,
		pubKey: pubKey,
		enabled: newStatus,
		calcId
	};
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		context: $this,
		calcId,
		data: {
			action: 'scc_set_stripe_key',
			data: keyInputVal
		},
		success: function(data) {
			showSweet(true, "The changes have been saved.");
			newStatus ? this.addClass('active') : this.removeClass('active');
		}
	})
}
function setForceQuoteFormStatus($this, event) {
}
function toggleFormBuilderOnDetails(element){
	return
}
function setWoocommerceCheckoutStatus(status) {
}
function attachProductId($this, id = null, elementType = null) {
	return
}
function attachSliderProductId($this) {
	$this = jQuery($this);
	let target = $this.data('target')
	let id_elementitems = $this.closest('.' + target).find(".swichoptionitem_id").val();
	let woocomerce_product_id = $this.val();
	if (woocomerce_product_id) {
		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: 'sccUpElementItemSwichoption',
				id_elementitems,
				woocomerce_product_id
			},
			success: function(datajson) {
				if (datajson.passed == true) {
					showSweet(true, "The changes have been saved.")
				} else {
					showSweet(false, datajson.msj)
				}
			}
		});
	}
}
function sccSaveRecaptchaKeys() {
	// var msgElement = jQuery('#scc_recaptcha_message');
	// msgElement.html('Saving... ').css('color', 'orange');
	var sccPDFFont = jQuery('#pdf_font').children("option:selected").val()
	var recaptchaKeys = jQuery('#recaptcha').find('input').serializeArray();
	$fragment_refresh = {
		url: ajaxurl,
		type: 'POST',
		data: {
			action: 'sccSaveRecaptchaKeys',
			recaptchakeys: JSON.stringify(recaptchaKeys)
		},
		success: function(data) {
			showSweet(true,'Saved successfully.')
			// msgElement.html('Saved successfully.').css('color', 'green');
		},
		error: function(err) {
			// msgElement.html('Saved successfully.').css('color', 'green');
		}
	};
	jQuery.ajax($fragment_refresh);
}
function updateStripeKey() {
	var keyInputVal = {
		privKey: jQuery('[name="stripe-api-priv-key"]').val(),
		pubKey: jQuery('[name="stripe-api-pub-key"]').val(),
		enabled: jQuery('[name="is-stripe-enabled"]').prop('checked')
	};
	jQuery.ajax({
		url: ajaxurl,
		type: 'POST',
		data: {
			action: 'scc_set_stripe_key',
			data: keyInputVal
		},
		success: function(data) {
			showSweet(true,'Saved Successfully!')
		}
	});
}
function changeShowSectionTotalOnPdf(element) {
	var id = jQuery(element).parentsUntil(".addedFieldsStyle").parent().find(".id_section_class").val();
    var show = jQuery(element).is(":checked");
	jQuery.ajax({
		url: ajaxurl,
		cache: false,
		data: {
			action: 'sccUpSection',
			id_section: id,
			showSectionTotalOnPdf: show,
			nonce: pageEditCalculator.nonce
		},
		success: function(data) {
			var datajson = JSON.parse(data)
			if (datajson.passed == true) {
				showSweet(true, "The changes have been saved.")
			} else {
				showSweet(false, "There was an error, please try again.")
			}
		}
	})
}
function truncateElementTitle(str, n){
	return (str.length > n) ? str.substr(0, n-1) + '..' : str;
  };
function settings_scc_(){
	let menuclass = document.querySelector('.scc-edit-nav-items')
	menuclass.prepend(settings_el())
	
}
function settings_el(){
	let u = document.querySelector('.scc-footer.logo').getAttribute('href') + '?utm_source=inside-plugin&utm_medium=buy-premium-cta-banner'
	let cont = document.createElement('li')
	let p = document.createElement('span')
	p.classList.add('free_version')
	p.innerHTML = ''
	let s1 = document.createElement('a')
	s1.classList.add('highlighted')
	s1.innerText = 'Buy Premium'
	s1.setAttribute('href', u)
	s1.setAttribute('target','_blank')
	p.appendChild(s1)
	p.innerHTML += ''
	cont.appendChild(p)
	return cont
}
settings_scc_()
document.querySelectorAll('[id^=scc_calculator_]').forEach(element => {
	element.querySelectorAll('a').forEach(element => {
		let inner =  element.innerHTML
		if(inner == 'Duplicate'|| inner == 'Export' || inner == 'URLs'){
			element.style.boxShadow = 'none'
			element.classList.add('use-premium-tooltip')
			// element.setAttribute('data-tooltip','You need to purchase a premium license to use this feature.')
		}
	});
})
function disableGlobalSettingsSection(arr){
	let param = new URLSearchParams(window.location.search)
	if(param.get('page')!='stylish_cost_calculator_settings') return
	// set tooltips at the right
	document.querySelectorAll('.mb-3.row[title]').forEach(e => new bootstrap.Tooltip(e, {placement: 'right'}))
	let couponLink = document.querySelector('#coupon-page');
	couponLink.setAttribute('href', 'javascript:void(0)');
	couponLink.setAttribute('title', needLicenseKeyTooltip);
	new bootstrap.Tooltip(couponLink, {placement: 'right'})

	let style = {
		"background-color":"rgba(0,0,0,0.75)",
		"position":"absolute",
		"width":"100%",
		"height":"100%",
		"top":"0",
		"left":"0",
		"right":"0",
		"bottom":"0",
		"z-index":"100",
		"display":"flex",
		"align-items":"center",
		"justify-content":"center"
	}
	let u = document.querySelector('.scc-footer.logo').getAttribute('href') + '?utm_source=inside-plugin&utm_medium=buy-premium-cta-banner'
	arr.forEach(e => {
	var cont = document.querySelectorAll('.card.mb-3.p-4')[e]
	cont.style.position = 'relative'
	// let cont = document.querySelectorAll('.row.settings-section')[1].style.color = 'red'
	///////
	// let el = document.createElement('div')
	// el.classList.add('ssssssss')
	let frag = document.createDocumentFragment()
	let content = document.createElement('div')
	let div = document.createElement('div')
	Object.assign(content.style,style)
	let text = document.createElement('h5')
	text.style.color = '#FFF'
	text.innerText = 'THIS FEATURE IS AVAILABLE IN THE PREMIUM VERSION'
	let link_cont = document.createElement('center')
	let link = document.createElement('a')
	link.classList.add('a-over')
	link_cont.appendChild(link)
	link.setAttribute('target','_blank')
	link.setAttribute('href',u)
	link.innerText = 'Buy Now'
	div.appendChild(text)
	div.appendChild(link_cont)
	content.appendChild(div)
	frag.appendChild(content)
	cont.appendChild(frag)
	});
}
disableGlobalSettingsSection([1,2,3,4,6,7])
document.querySelectorAll('.add-element-btn.save_button').forEach(element => {
	element.addEventListener('click',function(){
		element.closest('.boardOption').querySelectorAll('.scc_button.btn-backend').forEach(e=>{
			unabled(e)
		})
	})
})
function unabled(e){
	let o = e.innerText.trim()
	if(o=='File Upload'||o=='Custom Math'||o=='Image Button'||o=='Text/HTML Field'||o=='Variable Math'||o=='Date Picker'){
		let p = ''
		let tooltipImageUrl = ''
		switch(o){
			case 'File Upload':
				p = 'https://designful.freshdesk.com/a/solutions/articles/48001166819'
				break
			case 'Custom Math':
				// tooltipImageUrl = df_scc_resources.assetsPath + '/images/tooltip-images/infographic-elmnt-custom-math-1.png'
				p = 'https://designful.freshdesk.com/a/solutions/articles/48001142309'
				break
			case 'Image Button':
				p = 'https://designful.freshdesk.com/a/solutions/articles/48001207056'
				break
			case 'Text/HTML Field':
				p = 'https://designful.freshdesk.com/a/solutions/articles/48001207057'
				break
			case 'Variable Math':
				p = 'https://designful.freshdesk.com/a/solutions/articles/48001202286'
				break
			case 'Date Picker':
				p = 'https://designful.freshdesk.com/a/solutions/articles/48001216557'
				break
		}
		let inner = ['File Upload','Custom Math','Variable Math','Image Button','Text/HTML Field','Date Picker'].includes(o) ? 'More':''
		e.classList.add('premium-tooltips2','ed-btn-disabled')
		let title = `You need to purchase a premium license to use this feature. <a target="_blank" href="${p}">${inner}</a>`;
		if (tooltipImageUrl.length > 0 ) {
			title = `<p class="mt-3">${title}</p>` + '<img class="mx-3" src=\'' + tooltipImageUrl + '\'/>';
		}
		// e.setAttribute('title',`You need to purchase a premium license to use this feature. <a target="_blank" href="${p}">${inner}</a>`)
		e.removeAttribute('onclick')
		jQuery(e).tooltip({
			placement: 'right',
			html: true,
			title,
			customClass: tooltipImageUrl.length ? 'tooltip-img-dark' : '',
			delay: {show: 0, hide: 100}
		})
	}
}
function toolPrem(){
	let ooo = function (e){
		let p = ''
		let inner = ''
		e.classList.add('premium-tooltips2')
		e.setAttribute('title',`You need to purchase a <a href="https://stylishcostcalculator.com/">premium license</a> to use this feature. <a target="_blank" href="${p}">${inner}</a>`)
		e.removeAttribute('onclick')
		jQuery(e).tooltip({
			placement: 'right',
			html: true,
			delay: {show: 0, hide: 100}
		})
	}
	document.querySelectorAll('.tool-premium').forEach(function(e){
		ooo(e)
	})
	
}

  // tooltips
  jQuery('.material-icons-outlined.with-tooltip')
    .add('.btnn.material-icons')
    .add('.scc_accordion_conditional.with-tooltip')
    .add('.use-premium-tooltip, .use-tooltip')
    .each((index, element) => {
      new bootstrap.Tooltip(element, {
        delay: { hide: 300 },
        trigger: 'hover focus',
        html: true,
        placement: 'right',
      })
    })

function handleDiagRemove($this) {
	let ignoredMsgKey = $this.getAttribute('data-diag-key');
	jQuery.ajax({
			url: ajaxurl,
			cache: false,
			data: {
			action: 'scc_get_debug_items',
			nonce: pageEditCalculator.nonce,
			method: 'set_ignore',
			value: ignoredMsgKey
		},
		success: function(data) {
			$this.closest('.alert.alert-warning').remove();
		}
	})
}

document.querySelectorAll('#close-btn').forEach(element =>{
	element.addEventListener('click',function(){
		if(this.innerText!='-') return
		element.closest('.elements_added').querySelector('.first-conditional-step')?.setAttribute('disabled','true')
		let first = element.closest('.elements_added').querySelector('.first-conditional-step')
		let second = element.closest('.elements_added').querySelector('.second-conditional-step')
		element.closest('.elements_added').querySelector('.item_conditionals').querySelectorAll('button').forEach(e => {
			e.removeAttribute('onclick')
		})
		let cond = element.closest('.elements_added').querySelector('.item_conditionals')
		cond.style.display = 'inline'
		cond.classList.add('use-premium-tooltip')
		// cond.setAttribute('data-tooltip','You need to purchase a premium license to use this feature.')
		first.removeAttribute('onchange')
		first.removeAttribute('onfocus')
		second.removeAttribute('onchange')
	})
})
let p = document.querySelectorAll('#paybuttonhovereffect,#scc_send_quote,#turn_off_coupon,#scc_remove_total_price_frntd,#scc_remove_detailed_list_title,#scc_no_unit_col,#scc_no_qty_col,#scc_save_icon,#turn_off_tax,#scc_show_taxvat,#scc_set_webhook_quote,#scc_set_webhook_detail_view,#show_invoice_number').forEach(element => {
	element.setAttribute('disabled','true')
	if(element.getAttribute('id') == 'turn_off_coupon' || element.getAttribute('id') == 'scc_detailed_list'|| element.getAttribute('id') == 'scc_send_quote') element.setAttribute('checked','true')
	if(element.getAttribute('id') == 'scc_show_taxvat') element.removeAttribute('checked')
	if(element.nextElementSibling) element.nextElementSibling.style.backgroundColor = 'rgba(211, 211, 211, 0.4)'
	if(element.nextElementSibling) element.nextElementSibling.style.cursor = 'not-allowed'
	element.closest('.scc-switch')?.classList.add('use-premium-tooltip')
	// element.closest('.scc-switch')?.setAttribute('data-tooltip','You need to purchase a premium license to use this feature.')
});
document.querySelectorAll('[name=scc_wc_cart_btn_action],[name=email_quote_recipients]').forEach(element => {
	element.setAttribute('disabled','true')
	element.parentElement.classList.add('use-premium-tooltip')
	element.style.cursor = 'not-allowed'
	// element.parentElement.setAttribute('data-tooltip','You need to purchase a premium license to use this feature.')
})
document.querySelectorAll('#scc_minimum-total,#scc_tax_amount').forEach(element=>{
	if(element.getAttribute('id') == 'scc_tax_amount') element.setAttribute('value','0')
	element.setAttribute('disabled','true')
	element.parentElement.classList.add('use-premium-tooltip')
	// element.parentElement.setAttribute('data-tooltip','You need to purchase a premium license to use this feature.')
})
document.querySelectorAll('#label_footern,#label_autocurr,#label_pdffont,#label_pdfdate,#label_emailbody').forEach(element=>{
	element.closest('.scc-vcenter').querySelector('a').classList.add('use-premium-tooltip')
	// element.closest('.scc-vcenter').querySelector('a').setAttribute('data-tooltip','You need to purchase a premium license to use this feature.')
})
/**
 * 
 * @param {The button DOM object} btn 
 * @param {The onClick event} event 
 */
 function handleFeedbackButtons(btn, event) {
	event.preventDefault();
	jQuery.post(ajaxurl, {
	  'action': 'scc_feedback_manage',
	  'btn-type': jQuery(btn).data('btnType'),
	  'nonce' : pageEditCalculator.nonce
  }, function(response) {
	  jQuery('#user-scc-sv').modal('hide');
	  var link = jQuery(btn).attr('href');
	  if (link) {
		  window.open(link, '_blank');
	  }
  });
}

function processTooltipContent(elementType) {
	if (typeof(elementTooltips[elementType]) == 'string') {
		return elementTooltips[elementType];
	}
	if (typeof(elementTooltips[elementType]) == 'object') {
		return elementTooltips[elementType].msg;
	}
	return elementType;
}


function getTooltipCoverImage(elementType) {
	if (typeof(elementTooltips[elementType]) == 'object') {
		return df_scc_resources.assetsPath + '/' + elementTooltips[elementType].coverImage;
	}
	return 'https://picsum.photos/200/100';
}

function applyElementTooltip(node) {
	let elementType = node.getAttribute('data-element-tooltip-type')
	new bootstrap.Tooltip(node, {
	  delay: { hide: 300 },
	  trigger: 'hover focus',
	  template: `<div class="tooltip opacity-100 bg-dark" role="tooltip">
		<div class="tooltip-arrow"></div>
		<div class="card tooltip-element">
		<div class="card-body bg-dark tooltip-inner">
		</div>
		</div>
	  </div>`,
	  title: processTooltipContent(elementType),
	  html: true,
	  placement: 'bottom',
	})
  }

function preDeletionDialog(type, callbackFn, ...cbArg) {
	Swal.fire({
	  title: `Do you want remove this ${type}?`,
	  showDenyButton: true,
	  showCancelButton: false,
	  confirmButtonText: 'Yes',
	  denyButtonText: `No`,
	}).then((result) => {
	  if (result.isConfirmed) {
		// Swal.fire('Saved!', '', 'success')
		callbackFn(cbArg)
	  } else if (result.isDenied) {
		// Swal.fire('Changes are not saved', '', 'info')
	  }
	})
	return 0;
  }
jQuery(document).ready(function(){
	toolPrem()
	//keeps tooltip on mouseover
	var cx, cy, tip, waiting;
	var old_hide = bootstrap.Tooltip.prototype.hide

	var isOutside = function() {
		return ((cx < tip.left || cx > tip.left + tip.width) || (cy < tip.top || cy > tip.top + tip.height))
	}


	document.addEventListener('mousemove', function(e) {
		cx = e.clientX
		cy = e.clientY
		if (waiting && isOutside()) {
			waiting.f.call(waiting.context)
			waiting = null
		}		
	})

	bootstrap.Tooltip.prototype.hide = function(args) {
		tip = this.getTipElement().getBoundingClientRect()
		if (isOutside()) {
			old_hide.call(this)
		} else {
			waiting = { f: old_hide, context: this }
		}
	}

	// tooltip for editing page
	jQuery('.material-icons-outlined.with-tooltip').add('.btnn.material-icons').add('.scc_accordion_conditional.with-tooltip').each((index,element) => {
		new bootstrap.Tooltip(element, {
			delay: { "hide": 300 },
			trigger: 'hover focus',
			html: true,
			placement: 'right'
		})
	})
	document.querySelectorAll('.use-premium-tooltip').forEach(node => {
		let tooltipImageUrl = node.getAttribute('data-tooltip-image');
		let tooltipStr = needLicenseKeyTooltip;
		if (tooltipImageUrl) {
			tooltipStr = `<p class="mt-3">${needLicenseKeyTooltip}</p>` + '<img class="mx-3" src=\'' + tooltipImageUrl + '\'/>';
		}
		new bootstrap.Tooltip(node, {
			delay: { "hide": 300 },
			trigger: 'hover focus',
			html: true,
			title: tooltipStr,
			placement: 'right',
			customClass: tooltipImageUrl ? 'tooltip-img-dark' : ''
		})
	})
	// tooltip for elements
	document.querySelectorAll('[data-element-tooltip-type]').forEach((node) => {
		applyElementTooltip(node)
	})
	// tooltips at the bottom
	document.querySelectorAll('.use-tooltip-child-nodes').forEach(e => {
		let tooltipImageUrl = '';
		let tooltipStr = needLicenseKeyTooltip;
		let relevantNodes = [...e.childNodes].filter(e => e.nodeName !== '#text' && e.nodeName !== 'DIV');
		if ( e.getAttribute( 'data-tooltip-image' ) ) {
			tooltipImageUrl = e.getAttribute( 'data-tooltip-image' );
		}
		if ( e.classList.contains('scc_accordion_conditional') ) {
			tooltipImageUrl = df_scc_resources.assetsPath + '/images/tooltip-images/infographic-feat-conditional-logic.png';
		}
		relevantNodes.forEach(ee => {
		  ee.removeAttribute( 'disabled' )
		  ee.removeAttribute( 'onclick' )
		  ee.classList.remove('disabled')
		  if ( ee.getAttribute('data-btn-type') == 'paypal' ) {
			  tooltipImageUrl = df_scc_resources.assetsPath + '/images/tooltip-images/infographic-pay-paypal.png';
		  }
		  if (tooltipImageUrl.length) {
				tooltipStr = `<p class="mt-3">${needLicenseKeyTooltip}</p>` + '<img class="mx-3" src=\'' + tooltipImageUrl + '\'/>';
		  }
		  new bootstrap.Tooltip(ee, {
			delay: { hide: 300 },
			trigger: 'hover focus',
			html: true,
			title: tooltipStr,
			placement: 'right',
			customClass: tooltipImageUrl.length ? 'tooltip-img-dark' : '',
		  })
		})
	  })

	const notificationContainer = document.getElementById('scc-notifications');
	if (notificationContainer) {
		let notficationMessagesWrapper = notificationContainer.querySelector('.notification-message-wrapper');
		let notficationMessageNodes = notficationMessagesWrapper.children;
		let nextBtn = notificationContainer.querySelector('.next');
		let prevBtn = notificationContainer.querySelector('.prev');
		let dismissBtn = notificationContainer.querySelector('.scc-dismiss');
		dismissBtn.addEventListener('click', function (event) {
			let messageNode = notificationContainer.querySelector('.scc-notifications-message.current');
			let messageId = messageNode.getAttribute('data-message-id');
			// if here are only one notification left, remove the notification box
			if (notficationMessagesWrapper.childElementCount <= 1) {
				notificationContainer.parentElement.remove();
			}
			if (notficationMessagesWrapper.childElementCount > 1) {
				// if notifications are more than 1, activate the next notification item, and remove the current message node
				let isLastMessageNode = notficationMessageNodes[notficationMessagesWrapper.childElementCount - 1] == messageNode;
				if (isLastMessageNode) {
				  messageNode.previousElementSibling.classList.add('current');
				} else {
				  messageNode.nextElementSibling.classList.add('current');
				}
				messageNode.remove();
			}
			// if last message, hide the previous, next navigation buttons
			if (notficationMessagesWrapper.childElementCount == 1 && document.body.contains(notficationMessagesWrapper)) {
				prevBtn.remove();
				nextBtn.remove();
			}
			var data = {
				action: 'scc_notification_dismiss',
				nonce: notificationsNonce.nonce,
				id: messageId,
			};
			jQuery.post(wp.ajax.settings.url, data, function (res) {

				if (!res.success) {
					console.log(res);
				}
			}).fail(function (xhr, textStatus, e) {
				console.log(xhr.responseText);
			});
		})
		nextBtn && nextBtn.addEventListener('click', function (event) {
			if (event.currentTarget.classList.contains('disabled')) return;
			for (let index = 0; index < notficationMessageNodes.length; index++) {
				let msgNode = notficationMessageNodes[index];
				if (msgNode.classList.contains('current')) {
					msgNode.classList.remove('current');
					msgNode.nextElementSibling.classList.add('current');
					if (!msgNode.nextElementSibling.nextElementSibling) {
						nextBtn.classList.add('disabled');
					}
					if (msgNode.nextElementSibling.previousElementSibling) {
						prevBtn.classList.remove('disabled');
					}
					return;
				}
			}
		})
		prevBtn && prevBtn.addEventListener('click', function (event) {
			if (event.currentTarget.classList.contains('disabled')) return;
			for (let index = 0; index < notficationMessageNodes.length; index++) {
				let msgNode = notficationMessageNodes[index];
				if (msgNode.classList.contains('current')) {
					msgNode.classList.remove('current');
					msgNode.previousElementSibling.classList.add('current');
					if (!msgNode.previousElementSibling.previousElementSibling) {
						prevBtn.classList.add('disabled');
					}
					if (msgNode.previousElementSibling.nextElementSibling) {
						nextBtn.classList.remove('disabled');
					}
					return;
				}
			}
		})
	}

})