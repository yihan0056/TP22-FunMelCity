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
<div class="row">
	<div class="scc_title_bar">Start a New Cost Calculator Form</div>
</div>
<div class="row ms-3">
	<!-- Demo Load by Mike -->
	<div style="background: #fff;border-radius: 4px;max-width: 500px;height:160px;padding:30px">
		<h1 style="font-size:16px;padding-bottom:10px;color:#314af3;font-weight: 600; text-transform: capitalize;">Option 1 | One-click Template Load</h1>
		<?php
		//echo "<span><span style='margin-left:-5px; ; padding: 0px; color: #555;line-height: 11px;float: left;'>";
		?>
		<select style="margin-top:15px;width:100%;max-width:unset;border: 2px solid #E8E8E8;" class="sccloaddemo" onChange='loadExample(this)'>
			<option value="null"> [Chooce a template] </option>
			<?php
			for ( $i = 0; $i < count( $options ); $i++ ) {
				?>
			<option value=<?php echo esc_attr( $i ); ?> <?php
			if ( $i == 15 ) {
				echo 'disabled';
			}
			?>
			><?php echo intval( $i ) + 1 . ' - ' . esc_attr( $options[ $i ] ); ?></option>
				<?php
			}
			?>
		</select>
		<!--DONE PASTE-->
	</div>
</div>

<!-- END Demo Load by Mike -->

<div class="row m-3" style="max-width: 400px;text-align: center;">
	<h3 style="margin-top:10px"> or </h3>
</div>
<div class="row ms-3">

	<div  style="background: #fff;border-radius: 4px;max-width: 500px;height:210px;padding:30px; margin-top:0px">
		<h1 style="padding-bottom:18px;font-size:16px;color:#314af3;font-weight: 600; text-transform: capitalize;">Option 2 | Start a New Calculator</h1>
		<!-- <label class="scc_label col-xs-12 col-md-1" style="text-align: left;"><a class="scc_button" href="javascript:void(0)" onClick="resetFields()">RESET</a></label>-->
		<input type="text" class=" input_pad" style="border: 2px solid #E8E8E8!important;" placeholder="Calculator name (mandatory)" id="costcalculatorname" value="" />
		<!--<label class="scc_label" style="text-align: left;"><a class="scc_button" href="javascript:void(0)" onClick="saveFields()">SAVE</a></label>-->
		<!-- SHOW ONLY FOR LICENCE USERS -->
		<label class="scc_label" style="text-align: center;padding-left:0px">
			<a style="padding:10px!important;margin-left:0" class="scc_button save_button " onClick="scc_create_new_calculator()">START NEW</a>
		</label>
		<!-- SHOW ONLY FOR LICENCE USERS -->
		<!-- SHOW NOTICE NEEDS LICENCE -->
	</div>
</div>
<script>
	/**
	 * *Creates new calculator with name
	 * @param name_calculator
	 */
	function scc_create_new_calculator() {
		showLoadingChanges()
		var name = jQuery("#costcalculatorname").val()
		if (!name) {
			showSweet(false, "The calculator name is mandatory")
			return
		}

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
