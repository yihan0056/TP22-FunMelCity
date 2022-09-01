<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$isSCCFreeVersion = defined( 'STYLISH_COST_CALCULATOR_VERSION' );
?>
<!-- FOR LATER -->

<div class="row mt-2 scc-no-gutter">
	<div style="max-width:820px;margin-top: 50px; padding-right: 10px;" class="scc-col-xs-12 scc-col-md-7 scc-col-lg-6 clearfix">
		<!-- QUOTE FORM SECTION -->
		<div class="editing-action-cards action-quoteform">
			<div class="card-content">
				<h3>Email Quote | Form Builder (Premium Feature)</h3>
				<p>Customize your email quote form.</p>
			</div>
			<div class="card-action-btns 
			<?php
			if ( $isSCCFreeVersion ) {
				echo 'disabled use-tooltip-child-nodes';}
			?>
			">
				<?php foreach ( $formFieldsArray as $fieldIndex => $fieldValue ) : ?>
					<?php
					$fieldKey   = array_keys( $fieldValue )[0];
					$fieldProps = $fieldValue[ $fieldKey ];
					?>
					<button class="btn btn-cards disabled" data-btn-fieldtype="custom" data-field-key="<?php echo esc_attr( $fieldKey ); ?>">
						<span><?php echo esc_attr( $fieldProps['name'] ); ?></span>
						<i class="scc-icon-formbuilder material-icons" data-form-builder-action-type="edit" onclick="console.log">edit</i>
					</button>
				<?php endforeach; ?>
				<button class="btn btn-cards btn-plus 
				<?php
				if ( $isSCCFreeVersion ) {
					echo 'disabled';}
				?>
				" data-btn-fieldtype="more-fields" onclick="doFormFieldsSetup(this, event, <?php echo $isSCCFreeVersion ? 'false' : 'true'; ?>)">
					<span class="material-icons">done</span>+
				</button>
				<div class="scc-form-checkbox 
				<?php
				if ( $isSCCFreeVersion ) {
					echo 'use-tooltip-child-nodes';}
				?>
				" style="margin: 10px 0 0 0">
				<label class="scc-accordion_switch_button" for="toggle-build-quote">
					<input type="checkbox" id="toggle-build-quote" 
					<?php
					echo $ShowFormBuilderOnDetails ? 'checked' : '';
					if ( $isSCCFreeVersion ) {
						echo 'disabled';}
					?>
					 onchange="toggleFormBuilderOnDetails(this)">
					<span class="scc-accordion_toggle_button round"></span>
				</label>
				<span><label for="toggle-build-quote" class="lblExtraSettingsEditCalc">Show User Details on Detailed List & PDF
						<a href="javascript:void(0)" style="padding-left:10px;color:black;" class="tooltipadmin-right" data-tooltip="Shows the field filled by the user in the detail view"> <img src="<?php echo esc_url( SCC_URL . '/assets/images/info-icon-scc.png' ); ?>" alt="Info Icon" height="15px;"> </a></label>
				</span>
			</div>
			</div>
		</div>
		<!-- END FORM SECTION -->
		<!-- Start Payment processing section -->
		<div class="editing-action-cards action-payment">
			<div class="card-content">
				<h3>Payment Options (Premium Feature)</h3> 
				<p>This is optional and only for users who want to use this plugin to collect payments. Choose one or more.</p>
			</div>
			<div class="card-action-btns has-checkmark 
			<?php
			if ( $isSCCFreeVersion ) {
				echo 'use-tooltip-child-nodes';}
			?>
			"
>
				<button 
				<?php
				if ( $isSCCFreeVersion ) {
					echo 'disabled';}
				?>
				 class="btn btn-cards <?php echo $isPayPalEnabled ? 'active' : ''; ?>" onclick="doPaypalSetupModal(<?php echo intval( $f1->id ); ?>)" data-btn-type="paypal"><span class="material-icons">done</span>Paypal</button>
				<button 
				<?php
				if ( $isSCCFreeVersion ) {
					echo 'disabled';}
				?>
				 class="btn btn-cards <?php echo $isStripeEnabled ? 'active' : ''; ?>" onclick="<?php echo $isStripeSetupDone ? 'toggleStripe(this)' : 'stripeOptionsModal(this)'; ?>" data-btn-type="stripe" <?php echo $isStripeSetupDone ? esc_attr( $stripeDataAttr ) : ''; ?>><span class="material-icons">done</span><span>Stripe</span></button>
				<button 
				<?php
				if ( $isSCCFreeVersion ) {
					echo 'disabled';}
				?>
				 class="btn btn-cards 
								 <?php
									if ( ! $isWoocommerceActive ) {
										echo 'disabled tooltipadmin-right';}
									if ( $isWoocommerceCheckoutEnabled ) {
										echo 'active';
									}
									?>
												" onclick="javascript:(function($this) {if (!$this.classList.contains('disabled')) setWoocommerceCheckoutStatus($this.classList.toggle('active'))})(this)" 
												<?php
												if ( ! $isWoocommerceActive ) {
													echo "data-tooltip='Please enable woocommerce'";
												}
												?>
												 data-btn-type="woocommerce"><span class="material-icons">done</span>Woocommerce</button> <a href="https://designful.freshdesk.com/en/support/solutions/articles/48001076926-woocommerce-integration" target="_blank"><img src="<?php echo esc_url( SCC_URL . '/assets/images/info-icon-scc.png' ); ?>" style="height: 15px;"></a>
			</div>
			<div class="scc-form-checkbox 
			<?php
			if ( $isSCCFreeVersion ) {
				echo 'use-tooltip-child-nodes';}
			?>
			" style="margin: 10px 0 0 0" 
>
				<label class="scc-accordion_switch_button" for="force-email-quote">
					<input 
					<?php
					if ( $isSCCFreeVersion ) {
						echo 'disabled';}
					?>
					 type="checkbox" id="force-email-quote" <?php echo $isForceQuoteFormEnabled ? 'checked' : ''; ?> onchange="setForceQuoteFormStatus(this, event)">
					<span class="scc-accordion_toggle_button round"></span>
				</label>
				<span><label for="force-email-quote" class="lblExtraSettingsEditCalc">Force Email Form before Checkout
						<a href="javascript:void(0)" style="padding-left:10px;color:black;" class="tooltipadmin-right" data-tooltip="Make it mandatory for users to fillout a Email Form before they proceed to a checkout (PayPal and Stripe Only)"> <img src="<?php echo esc_url( SCC_URL . '/assets/images/info-icon-scc.png' ); ?>" alt="Info Icon" height="15px;"> </a></label>
				</span>
			</div>
		</div><!-- End Payment processing section -->
		<!-- Start New save section -->
		<div class="editing-action-cards action-save">
			<div class="card-content">
				<h3>Save</h3>
				<p>Save your current form, backup or restore an existing calculator.</p>
			</div>
			<div class="card-action-btns">
				<button class="btn btn-cards-primary" onclick="saveDataFields()" style="background-color:#314AF3;color:white">Save</button>
				<button class="btn btn-cards 
				<?php
				if ( $isSCCFreeVersion ) {
					echo 'use-premium-tooltip';}
				?>
				" onclick="downloadBackup(<?php echo $isSCCFreeVersion ? 'false' : 'true'; ?>)">Backup</button>
				<a href="javascript:void(0)">
					<button class="btn btn-cards use-premium-tooltip">Restore</button>
				</a>
			</div>
			<!-- helper elements -->
			<a id="downloadAnchorElem"></a>
		</div><!-- End New save section -->
	</div>
</div>
<div id="yourNameModal" style="display:none">
	<h4 style="font-weight: bolder;">Add New Field</h4>
	<div class="form-group">
		<label for="" style="font-weight: normal;">Field Name</label>
		<input class="from-control" type="text" name="" style="width: 100%;">
	</div>
	<div class="form-group">
		<label for="" style="font-weight: normal;">Field Description</label>
		<input class="from-control" type="text" name="" style="width: 100%;">
	</div>
	<div class="form-group">
		<label for="" style="font-weight: normal;">Field Type</label>
		<select name="form-field-type" class="df-scc-eui-Select" aria-label="Use aria labels when no actual label is in use">
			<option value="0">Select A Type</option>
			<option value="date">Date</option>
			<option value="address">Address</option>
			<option value="phone">Phone</option>
			<option value="text" selected="">Text</option>
			<option value="email">Email</option>
		</select>
	</div>
	<div class="scc-form-checkbox">
		<input type="checkbox" name="is-mandatory"><label class="df-scc-euiFormLabel df-scc-euiFormRow__label" for="is-mandatory">Make Mandatory</label>
	</div>
	<div class="row">
		<div class="btn-group col-md-12 justify-content-end">
			<button class="btn " onclick="sssclose(this)">Cancel</button>
			<button class="btn " onclick="sssclose(this)" style="background-color: #006BB4;color:white">Save</button>
		</div>
	</div>
</div>
<div id="addNewFieldModal" style="display:none" class="fade in" role="dialog">
	<div class="df-scc-euiOverlayMask df-scc-euiOverlayMask--aboveHeader">
		<div class="df-scc-euiModal df-scc-euiModal--maxWidth-default df-scc-euiModal--confirmation">
			<button class="df-scc-euiButtonIcon df-scc-euiButtonIcon--text df-scc-euiModal__closeIcon" type="button" data-dismiss="modal"><svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="df-scc-euiIcon df-scc-euiIcon--medium df-scc-euiButtonIcon__icon" focusable="false" role="img" aria-hidden="true">
					<path d="M7.293 8L3.146 3.854a.5.5 0 11.708-.708L8 7.293l4.146-4.147a.5.5 0 01.708.708L8.707 8l4.147 4.146a.5.5 0 01-.708.708L8 8.707l-4.146 4.147a.5.5 0 01-.708-.708L7.293 8z">
					</path>
				</svg></button>
			<form class="df-scc-euiModal__flex" onsubmit="addOrUpdateFormField(event, this)">
				<div class="df-scc-euiModalHeader">
					<div class="df-scc-euiModalHeader__title">Add New Field</div>
				</div>
				<div class="df-scc-euiModalBody">
					<div class="df-scc-euiModalBody__overflow">
						<div class="df-scc-euiText df-scc-euiText--medium">
							<div class="df-scc-euiFormRow">
								<div class="df-scc-euiFormRow__labelWrapper">
									<label class="trn df-scc-euiFormLabel df-scc-euiFormRow__label">Field Name</label>
								</div>
								<div class="df-scc-euiFormRow__fieldWrapper">
									<div class="df-scc-euiFormControlLayout">
										<div class="df-scc-euiFormControlLayout__childrenWrapper">
											<input type="text" name="field_name" class="df-scc-euiFieldText">
										</div>
									</div>
									<span class="text-danger" style="display: none; font-size: .75rem;">This field cannot be
										empty!</span>
								</div>
							</div>
							<div class="df-scc-euiFormRow">
								<div class="df-scc-euiFormRow__labelWrapper">
									<label class="trn df-scc-euiFormLabel df-scc-euiFormRow__label">Field
										Description</label>
								</div>
								<div class="df-scc-euiFormRow__fieldWrapper">
									<div class="df-scc-euiFormControlLayout">
										<div class="df-scc-euiFormControlLayout__childrenWrapper"><input type="text" name="field_description" class="df-scc-euiFieldText"></div>
									</div>
									<span class="text-danger" style="display: none; font-size: .75rem;">This field cannot be
										empty!</span>
								</div>
							</div>
							<div class="df-scc-euiFormRow">
								<div class="df-scc-euiFormRow__labelWrapper">
									<label class="trn df-scc-euiFormLabel df-scc-euiFormRow__label">Field Type</label>
								</div>
								<div class="df-scc-eui-FormControlLayout__childrenWrapper"><select name="form-field-type" class="df-scc-eui-Select" aria-label="Use aria labels when no actual label is in use">
										<option value="0">Select A Type</option>
										<option value="date">Date</option>
										<option value="address">Address</option>
										<option value="phone">Phone</option>
										<option value="text" selected="">Text</option>
										<option value="email">Email</option>
									</select>
									<div class="df-scc-eui-FormControlLayoutIcons df-scc-eui-FormControlLayoutIcons--right">
										<span class="df-scc-eui-FormControlLayoutCustomIcon"><svg width="16" height="16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg" class="df-scc-eui-Icon df-scc-eui-Icon--medium df-scc-eui-FormControlLayoutCustomIcon__icon" focusable="false" role="img" aria-hidden="true">
												<path fill-rule="non-zero" d="M13.069 5.157L8.384 9.768a.546.546 0 01-.768 0L2.93 5.158a.552.552 0 00-.771 0 .53.53 0 000 .759l4.684 4.61c.641.631 1.672.63 2.312 0l4.684-4.61a.53.53 0 000-.76.552.552 0 00-.771 0z">
												</path>
											</svg></span>
									</div>
								</div>
								<span class="text-danger" style="display: none; font-size: .75rem;">Please choose a field
									type!</span>
							</div>
							<div class="scc-form-checkbox">
								<input type="checkbox" name="is-mandatory"><label class="df-scc-euiFormLabel df-scc-euiFormRow__label" for="is-mandatory">Make
									Mandatory</label>
							</div>
						</div>
						<p class="trn text-danger" style="display:none;">There has been an error. Try again</p>
					</div>
				</div>
				<div class="df-scc-euiModalFooter">
					<button class="df-scc-euiButtonEmpty df-scc-euiButtonEmpty--primary" type="button" data-dismiss="modal"><span class="df-scc-euiButtonContent df-scc-euiButtonEmpty__content"><span class="trn df-scc-euiButtonEmpty__text">Cancel</span></span>
					</button>
					<button class="df-scc-euiButton df-scc-euiButton--primary df-scc-euiButton--fill" type="submit">
						<span class="df-scc-euiButtonContent df-scc-euiButton__content" style="background-color:#006BB4;border-radius:3px">
							<span class="trn df-scc-euiButton__text">Add</span>
						</span>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- user survey modal, initiates if the there are more than 5 calculators -->
<div class="modal df-scc-modal fade in" id="user-scc-sv" style="padding-right: 0px; display: none;" role="dialog">
	<div class="df-scc-euiOverlayMask df-scc-euiOverlayMask--aboveHeader">
		<div class="df-scc-euiModal df-scc-euiModal--maxWidth-default df-scc-euiModal--confirmation">
			<div class="df-scc-euiModal__flex">
				<div class="df-scc-euiModalHeader">
					<div class="df-scc-euiModalHeader__title trn">Satisfaction Survey</div>
				</div>
				<div class="df-scc-euiModalBody">
					<div class="df-scc-euiModalBody__overflow">
						<div class="df-scc-euiText df-scc-euiText--medium">
							<p>Please take a quick moment to tell us how we are doing. The development team strive to make the best plugin for you and your visitors.</p>
						</div>
					</div>
				</div>
				<div class="df-scc-euiModalFooter">
					<a class="df-scc-euiButton df-scc-euiButton--primary df-scc-euiButton--fill" style="background-color:#006BB4;border-radius: 3px;" href="https://stylishcostcalculator.com/how-can-we-be-better?utm_source=scc-free" onclick="handleFeedbackButtons(this, event)" data-btn-type="yes" target="_blank">
						<span class="df-scc-euiButtonContent df-scc-euiButton__content">
							<span class="df-scc-euiButton__text">It Could Be Better</span>
						</span>
					</a>
					<a class="df-scc-euiButton df-scc-euiButton--primary df-scc-euiButton--fill" style="background-color:#006BB4;border-radius: 3px;" href="https://stylishcostcalculator.com/please-leave-a-review?utm_source=scc-free" onclick="handleFeedbackButtons(this, event)" data-btn-type="no" target="_blank">
						<span class="df-scc-euiButtonContent df-scc-euiButton__content">
							<span class="df-scc-euiButton__text">I’m Loving It!</span>
						</span>
					</a>
					<button class="df-scc-euiButton df-scc-euiButton--primary df-scc-euiButton--fill" style="background-color:#006BB4;border-radius: 3px;" onclick="handleFeedbackButtons(this, event)" data-btn-type="skip">
						<span class="df-scc-euiButtonContent df-scc-euiButton__content">
							<span class="df-scc-euiButton__text">I’m Still Getting Used To It</span>
						</span>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- placeholder for editing existing for field. This div will be populated by template rendering -->
<div id="editFieldModal" style="display:none" class="fade in" role="dialog"></div>
<div id="paypalSetupModal" style="display:none" class="fade in" role="dialog"></div>
<div id="stripe_opts_modal" style="display:none" class="fade in" role="dialog"></div>
