<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$options = array(
	'Venue Rental (template)',
	'Website Designer (template)',
	'Wedding Photographer (template)',
	'Car Rental (template)',
	'T-Shirt Printing (template)',
	'Cleaning Company (template)',
	'Funeral Home Company (template)',
	'Content Writing Agency (template)',
	'Audio Editing Services (template)',
	'Social Media Management (template)',
	'Student Fees (template)',
	'Digital Print and Lamination (template)',
	'Kitchens Renovations (template)',
	'Simple Video Budget (template)',
	'Food Catering (template)',
	'Pest Control Services (template)',
	'Book Publisher Service (template)',
);

wp_localize_script( 'scc-backend', 'pageAddCalculator', array( 'nonce' => wp_create_nonce( 'add-calculator-page' ) ) );
?>
<div class="container-fluid">
    <div class="row mb-3">
        <h1 class="display-6 fw-bold lh-1 mb-3">Welcome! 👋</h1>
        <p class="lead">Let's create a new Cost Calculator</p>
    </div>
    <div class="row row-cols-1 row-cols-sm-2 g-3">
        <div class="col" id="template-loader">
            <div class="bg-white">
                <div class="px-4 py-3">
                    <div class="head">
                        <div class="text-muted text-uppercase">Option A</div>
                        <strong>Ready-to-Play template</strong>
                    </div>
                    <div class="body">
                        <div class="input-group my-3">
                            <select class="form-select" id="choose-a-template">
                                <option value="null"> [Chooce a template] </option>
                                <?php for ($i = 0; $i < count($options); $i++) { ?>
                                    <option data-preview-image="<?php echo esc_html( $options[ $i ] ) . '.png'; ?>" value=<?php echo intval($i); ?> <?php if ($i == 15) echo "disabled"; ?>><?php echo intval($i) + 1 . " - " . esc_html($options[$i]) ?></option>
                                <?php
                                } ?>
                            </select>
                        </div>
                        <p>Select and lead a fully customizable template to start building with the first blocks</p>
						<p class="text-danger d-none">You have to choose an option</p>
                    </div>
                    <div class="action-btn">
						<button type="button" class="btn scc-btn-primary" data-relative-field="choose-a-template">Create calculator</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" id="new-calc-creator">
        	<div class="bg-white">
                <div class="px-4 py-3">
                    <div class="head">
                        <div class="text-muted text-uppercase">Option B</div>
                        <strong>Start from scratch</strong>
                    </div>
                    <div class="body">
                        <div class="input-group my-3">
                            <input type="text" class="form-control" id="new-calc-name" placeholder="Calculator name">
                        </div>
                        <p>Create a new calculator from scratch with your own layout and style.</p>
						<p class="text-danger d-none">Please enter a name for the calculator</p>
                    </div>
                    <div class="action-btn">
                        <button type="button" data-relative-field="new-calc-name" class="btn scc-btn-primary">Create calculator</button>
                    </div>
                </div>
            </div>
        </div>
		<div class="text-center d-none" id="calc-preview-wrapper">
			<img src="<?php echo esc_url( SCC_TEMPLATE_PREVIEW_BASEURL . '/audio_editing_template.png' ); ?>" alt="">
		</div>
    </div>
</div>
<script>
	const previewImagesBaseUrl = "<?php echo esc_url( SCC_TEMPLATE_PREVIEW_BASEURL ); ?>";
	/**
	 * *Creates new calculator with name
	 * @param name_calculator
	 */
	function scc_create_new_calculator() {
		var name = jQuery("#new-calc-name").val()
		if (!name) {
			document.querySelector('#new-calc-creator .text-danger').classList.remove('d-none')
            setTimeout(() => {
                document.querySelector('#new-calc-creator .text-danger').classList.add('d-none')
            }, 5000);
			return
		}

		showLoadingChanges()

		if (name) {
			jQuery.ajax({
				url: ajaxurl,
				data: {
					action: 'sccCalculatorOp',
					op: "add",
					calculator_name: name,
					nonce: pageAddCalculator.nonce
				},
				success: function(data) {
					console.log(data)
					var response = JSON.parse(data);
					if (response.passed == true) {
						window.location.href = window.location.pathname + "?page=Stylish_Cost_Calculator_EditItems&id_form=" + response.data
					} else {
						showSweet(false, "An error occured, please try again")
					}
				}
			})
		}
	}
	/**
	 * *Creates calculator with template
	 */

	function loadExample(element) {
		var el = jQuery(element).val()
		if (el == "null") {
			document.querySelector('#template-loader .text-danger').classList.remove('d-none')
            setTimeout(() => {
                document.querySelector('#template-loader .text-danger').classList.add('d-none')
            }, 5000);
			return
		}
		showLoadingChanges()
		jQuery.ajax({
			url: ajaxurl,
			data: {
				action: 'sscLoadExample',
				el: el,
				nonce: pageAddCalculator.nonce
			},
			success: function(data) {
				console.log(data)
				if (data.passed == true) {
					window.location.href = window.location.pathname + "?page=Stylish_Cost_Calculator_EditItems&id_form=" + data.data
				} else {
					showSweet(false, "An error occured, please try again")
				}
			}
		})

	}


    document.querySelector('body').classList.remove('wp-core-ui');

    const chooseATemplate = document.querySelector('#choose-a-template');
    const chooseATemplateBtn = document.querySelector('[data-relative-field="choose-a-template"]');
    const newCalcName = document.querySelector('#new-calc-name');
    const newCalcNameBtn = document.querySelector('[data-relative-field="new-calc-name"]');
    const calcPreview = document.querySelector('#calc-preview-wrapper');
	const newCalcCreateBox = document.querySelector('#new-calc-creator');
    chooseATemplate.addEventListener('change', evt => {
        let selectedValue = evt.target.value;
        if (selectedValue !== 'null') {
            // chooseATemplateBtn.removeAttribute('disabled');
			newCalcCreateBox.classList.add('d-none');
			calcPreview.classList.remove('d-none');
			calcPreview.querySelector('img').setAttribute('src', previewImagesBaseUrl + '/' + chooseATemplate.querySelector(`[value="${chooseATemplate.value}"]`).getAttribute('data-preview-image'))
			// console.log(previewImagesBaseUrl + chooseATemplate.options[selectedValue].textContent);
        } else {
            // chooseATemplateBtn.setAttribute('disabled', 1);
			newCalcCreateBox.classList.remove('d-none');
			calcPreview.classList.add('d-none');
        }
    })

    newCalcName.addEventListener('keyup', evt => {
        let currentValue = evt.target.value;
        // if(currentValue.length > 0 ) {
        //     newCalcNameBtn.removeAttribute('disabled');
        // } else {
        //     newCalcNameBtn.setAttribute('disabled', 1);
        // }
    })

    newCalcNameBtn.addEventListener('click', evt => {
        scc_create_new_calculator();
    })

    chooseATemplateBtn.addEventListener('click', evt => {
        loadExample(chooseATemplate);
    })

	function showSweet(respuesta, message) {
		if (respuesta) {
			Swal.fire({
				toast: true,
				title: message,
				icon: "success",
				showConfirmButton: false,
				timer: 2000,
				position: 'top-end',
				background: 'orange'
			})
		} else {
			Swal.fire({
				toast: true,
				title: message,
				icon: "error",
				showConfirmButton: false,
				timer: 2000,
				position: 'top-end',
				background: 'orange'
			})
		}
	}

	function showLoadingChanges() {
		let timerInterval
		Swal.fire({
			toast: true,
			showConfirmButton: false,
			timer: 5000,
			background: 'orange',
			didOpen: () => {
				Swal.showLoading()
			},
			willClose: () => {
				clearInterval(timerInterval)
			}
		})
	}
</script>
