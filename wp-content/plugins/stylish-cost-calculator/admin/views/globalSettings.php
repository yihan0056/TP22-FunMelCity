<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
class Stylish_Cost_Calculator_Settings {

	protected $page;
	protected $isSCCFreeVersion;
	protected $privKeyPlaceHolder;
	protected $pubKeyPlaceHolder;
	public function __construct() {
		wp_localize_script(
			'scc-backend',
			'SCC_Settings',
			array(
				'security' => wp_create_nonce( 'scostc-admin-settings-referer' ),
			)
		);
		wp_localize_script( 'scc-backend', 'pageGlobalSettings', array( 'nonce' => wp_create_nonce( 'global-settings-page' ) ) );
		$this->isSCCFreeVersion   = defined( 'STYLISH_COST_CALCULATOR_VERSION' );
		$stripe_opts              = wp_parse_args(
			get_option( 'df_scc_stripe_keys' ),
			array(
				'privKey' => null,
				'pubKey'  => null,
			)
		);
		$this->privKeyPlaceHolder = $stripe_opts['privKey'] ? substr( $stripe_opts['privKey'], 0, - ( strlen( $stripe_opts['privKey'] ) * .7 ) ) . '*****' . substr( $stripe_opts['privKey'], - ( strlen( $stripe_opts['privKey'] ) * .2 ) ) : 'Please enter Stripe API Private Key';
		$this->pubKeyPlaceHolder  = $stripe_opts['pubKey'] ? substr( $stripe_opts['pubKey'], 0, - ( strlen( $stripe_opts['pubKey'] ) * .7 ) ) . '*****' . substr( $stripe_opts['pubKey'], - ( strlen( $stripe_opts['pubKey'] ) * .2 ) ) : 'Please enter Stripe API Public Key';
		$this->pageTwo();
		$this->pageScript();
	}

	private function pageTwo() {
		// $isSCCFreeVersion = defined('STYLISH_COST_CALCULATOR_VERSION');
		// $currency_style = get_option('df_scc_currency_style', 'default'); // dot or comma

		// $currency_conversion_mode = get_option('df_scc_currency_coversion_mode', 'off');
		// $currency_conversion_selection = get_option('df_scc_currency_coversion_manual_selection');
		// $scc_emailsender = get_option('df_scc_emailsender');
		// $scc_emailsubject = get_option('df_scc_emailsubject');
		// $scc_sendername = get_option('df_scc_sendername');
		// $scc_messageform = get_option('df_scc_messageform');
		$currencyFields              = array(
			'name'       => 'Currency Settings',
			"helpdesk_link" => "<a class='material-icons-outlined text-decoration-none' target='_blank' href=\"https://designful.freshdesk.com/a/solutions/articles/48001143319\">info</a>",
			'fields'     => array(
				'currency',
				'currency_num_format',
				'currency_conversion_type',
				'currency_for_autoconv',
			),
			'notes'      => 'Select a currency: will automatically show the selected currency to convert
            Auto detect: will use the users current location to automatically detect their currency.',
			'action_btn' => 'Save',
			'action_cb'  => 'saveCurencySettings(this)',
		);
		$stripeFields                = array(
			'name'       => 'Stripe Settings',
			"helpdesk_link" => "<a class='material-icons-outlined text-decoration-none' target='_blank' href=\"https://designful.freshdesk.com/a/solutions/articles/48001167920\">info</a>",
			'fields'     => array(
				'stripe_secret_key',
				'stripe_public_key',
			),
			'action_btn' => 'Save',
			'action_cb'  => 'updateStripeKey(this)',
		);
		$pdfFooterField              = array(
			'name'       => 'Detailed List/PDF Settings',
			'fields'     => array(
				'pdf_footer_notes',
			),
			'action_btn' => 'Save',
			'action_cb'  => 'saveSCCEmailSetting(this)',
		);
		$detailListBannerLogo        = array(
			'name'   => 'Header: Detailed List & PDF',
			'fields' => array(
				'banner_and_logo',
			),
		);
		$email_quote_settings_fields = array(
			'name'          => 'Email Quote Settings',
			'fields'        => array(
				'email_quote_settings_form',
			),
			'action_btn'    => 'Save',
			'action_cb'     => 'saveSCCEmailSetting(this)',
			'hasShortcodes' => true,
		);
		$pdf_settings_fields         = array(
			'name'       => 'PDF Settings',
			'fields'     => array(
				'pdf_settings_form',
			),
			'action_btn' => 'Save',
			'action_cb'  => 'sccPDFSettings(this)',
		);
		$recaptcha_settings_fields   = array(
			'name'       => 'reCaptcha Settings',
			'fields'     => array(
				'recaptcha_settings_form',
			),
			'action_btn' => 'Save',
			'action_cb'  => 'sccSaveRecaptchaKeys(this)',
		);
		$restore_calc_fields         = array(
			'name'       => 'Restore SCC',
			'fields'     => array(
				'restore_calc_form',
			),
			'action_btn' => 'Restore',
			'action_cb'  => 'sscUploadSccBackup()',
		);
		?>
		<div class="container-fluid" id="scc-global-settings">
			<h1>Global Settings</h1>
			<a class="lead text-dark text-decoration-none" id="coupon-page" href="<?php echo esc_url(get_admin_url() . 'admin.php?page=Stylish_Cost_Calculator_Coupon'); ?>">Add or View Coupons</a>
			<?php
				$this->outputCard( $currencyFields );
				$this->outputCard( $stripeFields );
				$this->outputCard( $pdfFooterField );
				$this->outputCard( $detailListBannerLogo );
				$this->outputCard( $email_quote_settings_fields );
				$this->outputCard( $pdf_settings_fields );
				$this->outputCard( $recaptcha_settings_fields );
				$this->outputCard( $restore_calc_fields );
			?>
		</div>
		<?php
	}

	private function pageScript() {
		?>
		<style>
			.card.mb-3.p-4 .a-over {
				background-color: orange;
				border-radius: 5px;
				color: #FFF;
				padding: 5px 15px 5px 15px;
				text-transform: uppercase;
			}
		</style>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				// Upload button click
				$('.scc-media-upload-button').click(function(event) {
					event.preventDefault();
					// var formField = $(this).parents('.scc-form-field');
					sourceBtn = $(event.target);
					buttonWrapper = sourceBtn.closest('.col-sm-6');

					if (typeof(mediaUploader) !== 'undefined') {
						mediaUploader.open();
						return;
					}

					mediaUploader = wp.media.frames.file_frame = wp.media({
						title: 'Choose Image',
						button: {
							text: 'Choose Image'
						},
						multiple: false
					});

					mediaUploader.on("select", onPDFLogoMediaImageSelect);

					mediaUploader.open();
				});

				// Remove button click
				$('.scc-media-uploader-remove').click(function(event) {
					var sourceBtn = $(event.target);
					var buttonWrapper = sourceBtn.closest('.col-sm-6');

					var name = buttonWrapper.find('.scc-media-upload-field').attr('name');

					setUploadedImage(name, '', function(response) {
						var image = buttonWrapper.find('.scc-media-uploader-image');
						image.hide();
						image.find('img').remove();
						// buttons.show();
						buttonWrapper.find('.text-center').show();
					});
				});
			})
			function handleEditBox() {
				window.setTimeout(function () {
					if (tinyMCE.activeEditor.isDirty()) {
						jQuery('.scc_save_emdl:eq(1)').addClass('button-glow');
					}
					handleEditBox();
				}, 300)
			}
			handleEditBox();

			function toggleShortCodes() {
				if (jQuery('#email_form_short_codes_section').css('display') === 'none') {
					jQuery('#email_form_short_codes_section').css('display', 'block')
				} else {
					jQuery('#email_form_short_codes_section').css('display', 'none')
				}
			}

			/**
			 * *Handles the old backup
			 */
			function showConfirmOld(next) {
				Swal.fire({
					title: 'Are you sure?',
					text: "Are you sure you want to restore old backup",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, continue!'
				}).then((result) => {
					if (result.isConfirmed) {
						console.log("backup old")
						next()
					}
				})
			}

			/**
			 * *Handles backup restore from json file
			 */
			function sscUploadSccBackup() {
				// restore2()
				// return
				showLoadingChanges()
				// jQuery('#scc_backup_message').html('Wait... Analyzing your file and uploading it...');
				// jQuery('#scc_backup_message').css('color', 'orange')
				var files = jQuery('#backup_scc_file')[0].files[0];
				if (!files) {
					console.log("no hay archivo ")
					return
				}
				var reader = new FileReader()
				reader.readAsText(files, "UTF-8")
				reader.onload = function(ee) {
					let json = JSON.parse(ee.target.result)
					var o = ("scc_form" in json)
					if (o) {
						showConfirmOld(restore2)
					} else {
						const fdata = new FormData()
						fdata.append('file', files)
						fdata.append('action', "sccRestoreBackup")
						fdata.append('nonce', pageGlobalSettings.nonce)
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: fdata,
							contentType: false,
							processData: false,
							success: function(data) {
								if (data.passed) {
									showSweet(true, 'SCC restored successfully.')
									// jQuery('#scc_backup_message').html('SCC restored successfully.');
									// jQuery('#scc_backup_message').css('color', 'green')
								} else {
									showSweet(false, data.msj)
									// jQuery('#scc_backup_message').html(data.msj);
									// jQuery('#scc_backup_message').css('color', 'red')
								}

							}
						})
					}
				}
				// console.log(files)

			}

			/**
			 * Handle upload settings functionalty
			 */
			function setUploadedImage(name, url, callback) {
				debugger
				var data = {
					action: "pdf_logo_settings_ajax",
					security: SCC_Settings.security,
					method: "pdf_logo_save_uploaded_image",
					data: {
						name: name,
						value: url,
					}
				};

				jQuery.post(ajaxurl, data, callback);
			}
			/**
			 * On media image select
			 */
			function onPDFLogoMediaImageSelect() {
				var attachment = mediaUploader.state().get('selection').first().toJSON();
				var field = buttonWrapper.find('.scc-media-upload-field');
				field.val(attachment.url);
				// spinner.addClass('is-active');

				setUploadedImage(field.attr('name'), attachment.url, function(response) {
					// spinner.removeClass('is-active');
					var html = '<img src="' + response.data.url + '" />';
					var image = buttonWrapper.find('div.scc-media-uploader-image');
					image.append(html);
					image.show();
					field.parent().hide();
				});
			}

			function saveCurencySettings($btn) {
				$btn = jQuery($btn);
				var currency = jQuery("#currency_code").val()
				var style = jQuery("#currency-style").val()
				var mode = jQuery("#scc_currency_coversion_mode").val()
				var manual = jQuery("#scc_currency_coversion_manual_selection").val()
				var originalText = $btn.text()
				jQuery.ajax({
					url: wp.ajax.settings.url,
					data: {
						action: 'sccGlobalSettings',
						currency: currency,
						format: style,
						mode: mode,
						manual_select: manual,
						nonce: pageGlobalSettings.nonce
					},
					beforeSend: function() {
						$btn.text('saving...');
					},
					success: function(data) {
						var datajson = JSON.parse(data)
						if (datajson.passed == true) {
							// showSweet(true, "The changes have been saved.")
						} else {
							// showSweet(false, "An error occurred, please try again")
						}
					}
				}).done(function( data ) {
					$btn.text(originalText);
					$btn.prev('.notice-text').addClass('text-primary').text('Saved Successfully').show().delay(5000).queue(function(n) {
						let element = jQuery(this);
						element.removeClass('text-primary');
						element.text(''); n();
					});
				});
			}

			function showSweet(respuesta, message) {
				if (respuesta) {
					Swal.fire({
						toast: true,
						title: message,
						icon: "success",
						background: 'orange',
						showConfirmButton: false,
						timer: 1000000,
						position: 'top-end',
					})
				} else {
					Swal.fire({
						toast: true,
						title: message,
						icon: "error",
						background: 'orange',
						showConfirmButton: false,
						timer: 5000,
						position: 'top-end',
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

			/**
			 * *Handles the old backup
			 */
			function showConfirmOld(next) {
				Swal.fire({
					title: 'Are you sure?',
					text: "Are you sure you want to restore old backup",
					icon: 'warning',
					showCancelButton: true,
					confirmButtonColor: '#3085d6',
					cancelButtonColor: '#d33',
					confirmButtonText: 'Yes, continue!'
				}).then((result) => {
					if (result.isConfirmed) {
						console.log("backup old")
						next()
					}
				})
			}

			function saveSCCEmailSetting($btn) {
				event.preventDefault();
				$btn = jQuery($btn);
				var sendername = jQuery('#sendername').val();
				var senderemail = jQuery('#senderemail').val();
				var originalText = $btn.text();
				<?php
				if ( get_option( 'df_scc_licensed' ) != 0 ) {
					?>
					var messageform = jQuery('#messagetemplate').val();
					<?php
				} else {
					?>
					var messageform = "Hello <customer-name>, <br><br> Attached to this email is a PDF file that contains your quote. <br> If you have any further questions please call us email us here ____. <br><br> Sincerely,<br> Your Company Name<br><br> <hr><br> <b>Customer's Name</b> l <customer-name> <b>Customer's Phone</b> l <customer-phone> <b>Customer's Emai</b> l <customer-email> <b>Customer's IP</b> l <customer-ip-address> <b>Browser Info</b> l <customer-browser-info ><b>Device</b> l <device> <b> Referral </b> | <customer-referral>";
					<?php
				}
				?>
				var sccemailfooter = jQuery('#sccemailfooter').val();
				var scc_emailsubject = jQuery('#emailsubject').val();
				var scc_email_banner_image = jQuery('#scc_email_banner_image').val();
				var scc_email_logo_image = jQuery('#scc_email_logo_image').val()
				var scc_email_send_copy = jQuery('#scc_email_send_copy').val();
				if (location.protocol === 'https:') {
					if (scc_email_banner_image) {
						if (scc_email_banner_image.indexOf('http://') != -1) {
							scc_email_banner_image = scc_email_banner_image.replace('http://', 'https://')
						}
					}
					if (scc_email_logo_image) {
						if (scc_email_logo_image.indexOf('http://') != -1) {
							scc_email_logo_image = scc_email_logo_image.replace('http://', 'https://')
						}
					}
				} else {
					// is http
				}
				messageform = messageform.replace(/(\r\n|\n|\r)/gm, "<br>")
				if (senderemail == '') {
					showSweet(false, 'Sender Email is Mandatory. Please, add a valid email. Thank you ')
					// alert('Sender Email is Mandatory. Please, add a valid email. Thank you ')
				} else if (sendername == '') {
					showSweet(false, 'Sender Name is Mandatory. Please, add a valid email. Thank you ')
					// alert('Sender Name is Mandatory. Please, add a valid email. Thank you ')
				} else if (scc_emailsubject == '') {
					showSweet(false, 'Email subject is Mandatory. Please, add a valid email. Thank you ')
					// alert('Email subject is Mandatory. Please, add a valid email. Thank you ')
				} else {
					$fragment_refresh = {
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'sccSaveEmailSettings',
							sender_name: sendername,
							sender_email: senderemail,
							emailsubject_testing: scc_emailsubject,
							scc_email_send_copy: scc_email_send_copy,
							message_form: messageform,
							sccemailfooter: sccemailfooter,
							nonce: pageGlobalSettings.nonce
						},
						beforeSend: function() {
							$btn.text('saving...');
						},
						success: function(data) {
							showSweet(true, 'Email Quote & Detailed List has been saved!')
							// jQuery(".scc_save_emdl").removeClass('scc_save_flash');
							// jQuery('#savingemailresult').html('Email Quote & Detailed List has been saved!').show();
							// jQuery('#savingemailresult').hide(3000);
						}
					};
					jQuery.ajax($fragment_refresh).done(function( data ) {
						$btn.removeClass('button-glow');
						$btn.text(originalText);
						$btn.prev('.notice-text').addClass('text-primary').text('Saved Successfully').show().delay(5000).queue(function(n) {
							let element = jQuery(this);
							element.removeClass('text-primary');
							element.text(''); n();
						});
					});
				}
			}

			function sccPDFSettings($btn) {
				$btn = jQuery($btn);
				// jQuery('#scc_pdf_font_message').html('Saving... ');
				// jQuery('#scc_pdf_font_message').css('color', 'orange')
				var sccPDFFont = jQuery('#pdf_font').children("option:selected").val();
				var sccPDFDateFmt = jQuery('#pdf_datefmt').children("option:selected").val();
				var originalText = $btn.text();
				$fragment_refresh = {
					url: ajaxurl,
					type: 'POST',
					data: {
						action: 'sccPDFSettings',
						pdfSettings: {
							sccPDFFont,
							sccPDFDateFmt
						},
						nonce: pageGlobalSettings.nonce
					},
					beforeSend: function() {
						$btn.text('saving...');
					},
					success: function(data) {
						showSweet(true, 'Saved successfully.')
						// jQuery('#scc_pdf_font_message').html('Saved successfully.');
						// jQuery('#scc_pdf_font_message').css('color', 'white')
					},
					error: function(err) {}
				};
				jQuery.ajax($fragment_refresh).done(function( data ) {
					$btn.text(originalText);
					$btn.prev('.notice-text').addClass('text-primary').text('Saved Successfully').show().delay(5000).queue(function(n) {
						let element = jQuery(this);
						element.removeClass('text-primary');
						element.text(''); n();
					});
				});
			}

			/**
			 * *Handles backup restore from json file
			 */
			function sscUploadSccBackup() {
				// restore2()
				// return
				showLoadingChanges()
				// jQuery('#scc_backup_message').html('Wait... Analyzing your file and uploading it...');
				// jQuery('#scc_backup_message').css('color', 'orange')
				var files = jQuery('#backup_scc_file')[0].files[0];
				if (!files) {
					console.log("no hay archivo ")
					return
				}
				var reader = new FileReader()
				reader.readAsText(files, "UTF-8")
				reader.onload = function(ee) {
					let json = JSON.parse(ee.target.result)
					var o = ("scc_form" in json)
					if (o) {
						showConfirmOld(restore2)
					} else {
						const fdata = new FormData()
						fdata.append('file', files)
						fdata.append('action', "sccRestoreBackup")
						fdata.append('nonce', pageGlobalSettings.nonce)
						jQuery.ajax({
							type: 'POST',
							url: ajaxurl,
							data: fdata,
							contentType: false,
							processData: false,
							success: function(data) {
								if (data.passed) {
									showSweet(true, 'SCC restored successfully.')
									// jQuery('#scc_backup_message').html('SCC restored successfully.');
									// jQuery('#scc_backup_message').css('color', 'green')
								} else {
									showSweet(false, data.msj)
									// jQuery('#scc_backup_message').html(data.msj);
									// jQuery('#scc_backup_message').css('color', 'red')
								}

							}
						})
					}
				}
			}

			function handleCurrencyCoversionMode(element) {
				var selection = jQuery(element).val()
				if (selection == "manual_selection") {
					jQuery("#scc_currency_coversion_manual_selection_container").css("display", "")
				} else {
					jQuery("#scc_currency_coversion_manual_selection_container").css("display", "none")
				}
			}

			function handleCurrency() {
				if (jQuery('#currency_code').val() == 'AED' ||
					jQuery('#currency_code').val() == 'COP' ||
					jQuery('#currency_code').val() == 'ANG' ||
					jQuery('#currency_code').val() == 'PKR' ||
					jQuery('#currency_code').val() == 'TWD' ||
					jQuery('#currency_code').val() == 'ZMW' ||
					jQuery('#currency_code').val() == 'CFA') {
					jQuery('#currency_conversion_incompatibility_message').html('The current selected currency is not supported for the "Currency conversion feature"')
					jQuery('#scc_currency_coversion_global_container').css('display', 'none')
				} else {
					jQuery('#currency_conversion_incompatibility_message').html('')
					jQuery('#scc_currency_coversion_global_container').css('display', 'inline')
				}
			}

		</script>
		<?php
	}

	private function outputCard( $cardProps ) {
		?>
		<div class="card mb-3 p-4" style="max-width: 40rem;">
			<div class="card-header bg-transparent">
				<div class="d-flex w-100 justify-content-between pb-2">
					<p class="mb-0 scc-vcenter use-tooltip lead fw-bold"><?php echo sanitize_text_field($cardProps["name"]); echo isset($cardProps['helpdesk_link']) ? ' ' . wp_kses_post($cardProps['helpdesk_link']) : '' ?></p>
					<?php if ( isset( $cardProps['hasShortcodes'] ) && $cardProps['hasShortcodes'] ) : ?>
					<div class="text-primary mb-0" role="button" onclick="toggleShortCodes()">Shortcodes</div>
					<?php endif ?>
				</div>
			</div>
			<div class="card-body text-secondary">
				<?php
				for ( $i = 0; $i < count( $cardProps['fields'] ); $i++ ) {
					call_user_func( array( $this, 'field_' . $cardProps['fields'][ $i ] ) );
				}
				?>
				<?php if ( isset( $cardProps['notes'] ) ) : ?>
					<p><?php echo esc_attr( $cardProps['notes'] ); ?></p>
				<?php endif; ?>
				<?php if ( isset( $cardProps['action_btn'] ) && $cardProps['action_cb'] ) : ?>
				<div class="d-flex w-100 justify-content-between">
					<p class="mb-0 notice-text"></p>
					<button type="button" class="btn btn-primary btn-lg" onclick="<?php echo esc_attr( $cardProps['action_cb'] ); ?>"><?php echo esc_attr( $cardProps['action_btn'] ); ?></button>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	private function field_currency() {
		 $currency = get_option( 'df_scc_currency', 'USD' );
		?>
		 <div class="mb-3 row">
			<label for="currency_code" class="col-sm-5 col-form-label use-tooltip" title="Choose your calculator forms currency. You can choose to have a symbol ($) or intials (USD) at the calculator settings level.">Currency</label>
			<div class=" col-sm-7">
			<select class="form-select form-select-lg mb-3" name="currency_code" id="currency_code" onchange="handleCurrency()">
				<option value="">Select one currency</option>
				<option value="AUD" <?php echo ( $currency == 'AUD' ) ? 'selected' : ''; ?>>Australian Dollar</option>
				<option value="AED" <?php echo ( $currency == 'AED' ) ? 'selected' : ''; ?>>Arab Emirates Dirhams (PayPal not Supported)</option>
				<option value="Bs" <?php echo ( $currency == 'Bs' ) ? 'selected' : ''; ?>>Bolivian Boliviano (Symbol After) (No PayPal)</option>
				<option value="BRL" <?php echo ( $currency == 'BRL' ) ? 'selected' : ''; ?>>Brazilian Real </option>
				<option value="CAD" <?php echo ( $currency == 'CAD' ) ? 'selected' : ''; ?>>Canadian Dollar</option>
				<option value="CNY" <?php echo ( $currency == 'CNY' ) ? 'selected' : ''; ?>>Chinese Yuan (PayPal not supported)</option>
				<option value="COP" <?php echo ( $currency == 'COP' ) ? 'selected' : ''; ?>>Colombian Peso (PayPal not supported)</option>
				<option value="CZK" <?php echo ( $currency == 'CZK' ) ? 'selected' : ''; ?>>Czech Koruna</option>
				<option value="DKK" <?php echo ( $currency == 'DKK' ) ? 'selected' : ''; ?>>Danish Krone</option>
				<option value="EUR" <?php echo ( $currency == 'EUR' ) ? 'selected' : ''; ?>>Euro</option>
				<option value="HKD" <?php echo ( $currency == 'HKD' ) ? 'selected' : ''; ?>>Hong Kong Dollar</option>
				<option value="HUF" <?php echo ( $currency == 'HUF' ) ? 'selected' : ''; ?>>Hungarian Forint (PayPal not supported)</option>
				<option value="IDR" <?php echo ( $currency == 'IDR' ) ? 'selected' : ''; ?>>Indonesian Rupiah (PayPal not supported)</option>
				<option value="ILS" <?php echo ( $currency == 'ILS' ) ? 'selected' : ''; ?>>Israeli New Sheqel</option>
				<option value="INR" <?php echo ( $currency == 'INR' ) ? 'selected' : ''; ?>>Indian Rupee</option>
				<option value="JPY" <?php echo ( $currency == 'JPY' ) ? 'selected' : ''; ?>>Japanese Yen</option>
				<option value="KES" <?php echo ( $currency == 'KES' ) ? 'selected' : ''; ?>>Kenyan Shilling</option>
				<option value="MYR" <?php echo ( $currency == 'MYR' ) ? 'selected' : ''; ?>>Malaysian Ringgit</option>
				<option value="MXN" <?php echo ( $currency == 'MXN' ) ? 'selected' : ''; ?>>Mexican Peso</option>
				<option value="MDL" <?php echo ( $currency == 'MDL' ) ? 'selected' : ''; ?>>Moldovan leu</option>
				<option value="MNT" <?php echo ( $currency == 'MNT' ) ? 'selected' : '' ?>>Mongolian tögrög</option>
				<option value="ANG" <?php echo ( $currency == 'ANG' ) ? 'selected' : ''; ?>>Netherlands Antillean Guilder (PayPal not Supported)</option>
				<option value="NGN" <?php echo ( $currency == 'NGN' ) ? 'selected' : ''; ?>>Nigerian naira</option>
				<option value="NOK" <?php echo ( $currency == 'NOK' ) ? 'selected' : ''; ?>>Norwegian Krone</option>
				<option value="NZD" <?php echo ( $currency == 'NZD' ) ? 'selected' : ''; ?>>New Zealand Dollar</option>
				<option value="‎PKR" <?php echo ( $currency == '‎PKR' ) ? 'selected' : ''; ?>>Pakistani Rupee (PayPal not Supported)</option>
				<option value="PHP" <?php echo ( $currency == 'PHP' ) ? 'selected' : ''; ?>>Philippine Peso</option>
				<option value="PLN" <?php echo ( $currency == 'PLN' ) ? 'selected' : ''; ?>>Polish Zloty</option>
				<option value="GBP" <?php echo ( $currency == 'GBP' ) ? 'selected' : ''; ?>>British Pound Sterling</option>
				<option value="RON" <?php echo ( $currency == 'RON' ) ? 'selected' : ''; ?>>Romanian leu</option>
				<option value="RUB" <?php echo ( $currency == 'RUB' ) ? 'selected' : ''; ?>>Russian Rubles</option>
				<option value="ZAR" <?php echo ( $currency == 'ZAR' ) ? 'selected' : ''; ?>>South African Rand (PayPal not supported)</option>
				<option value="SGD" <?php echo ( $currency == 'SGD' ) ? 'selected' : ''; ?>>Singapore Dollar</option>
				<option value="SEK" <?php echo ( $currency == 'SEK' ) ? 'selected' : ''; ?>>Swedish Krona</option>
				<option value="KRW" <?php echo ( $currency == 'KRW' ) ? 'selected' : ''; ?>>South Korean won</option>
				<option value="CHF" <?php echo ( $currency == 'CHF' ) ? 'selected' : ''; ?>>Swiss Franc</option>
				<option value="TWD" <?php echo ( $currency == 'TWD' ) ? 'selected' : ''; ?>>Taiwan New Dollar</option>
				<option value="THB" <?php echo ( $currency == 'THB' ) ? 'selected' : ''; ?>>Thai Baht</option>
				<option value="TRY" <?php echo ( $currency == 'TRY' ) ? 'selected' : ''; ?>>Turkish Lira (PayPal not supported)</option>
				<option value="UAH" <?php echo ( $currency == 'UAH' ) ? 'selected' : ''; ?>>Ukrainian hryvnia</option>
				<option value="UGX" <?php echo ( $currency == 'UGX' ) ? 'selected' : ''; ?>>Ugandan Shilling</option>
				<option value="USD" <?php echo ( $currency == 'USD' ) ? 'selected' : ''; ?>>U.S. Dollar</option>
				<option value="ZMW" <?php echo ( $currency == 'ZMW' ) ? 'selected' : ''; ?>>Zambian Kwancha (PayPal not supported)</option>
				<option value="CFA" <?php echo ( $currency == 'CFA' ) ? 'selected' : ''; ?>>Central African CFA franc (PayPal not supported)</option>
			</select>
			</div>
		</div>
		<?php
	}
	private function field_currency_for_autoconv() {
		$currency_conversion_selection = get_option( 'df_scc_currency_coversion_manual_selection' );
		$currency_conversion_mode      = get_option( 'df_scc_currency_coversion_mode', 'off' );
		?>
		<div class="mb-3 row 
		<?php
		if ( $currency_conversion_mode !== 'manual_selection' ) {
			echo 'd-none';}
		?>
		" id="scc_currency_coversion_manual_selection_container">
		<label class="col-sm-5 col-form-label" for="scc_currency_coversion_manual_selection">Select your currency for automatic conversion: </label>
			<div class=" col-sm-7">
			<select class="form-select form-select-lg mb-3" name="scc_currency_coversion_manual_selection" id="scc_currency_coversion_manual_selection">
				<option value="">Select currency</option>
				<option value="EUR" <?php echo ( $currency_conversion_selection == 'EUR' ) ? 'selected' : ''; ?>>EUR</option>
				<option value="CAD" <?php echo ( $currency_conversion_selection == 'CAD' ) ? 'selected' : ''; ?>>CAD</option>
				<option value="HKD" <?php echo ( $currency_conversion_selection == 'HKD' ) ? 'selected' : ''; ?>>HKD</option>
				<option value="ISK" <?php echo ( $currency_conversion_selection == 'ISK' ) ? 'selected' : ''; ?>>ISK</option>
				<option value="PHP" <?php echo ( $currency_conversion_selection == 'PHP' ) ? 'selected' : ''; ?>>PHP</option>
				<option value="DKK" <?php echo ( $currency_conversion_selection == 'DKK' ) ? 'selected' : ''; ?>>DKK</option>
				<option value="HUF" <?php echo ( $currency_conversion_selection == 'HUF' ) ? 'selected' : ''; ?>>HUF</option>
				<option value="CZK" <?php echo ( $currency_conversion_selection == 'CZK' ) ? 'selected' : ''; ?>>CZK</option>
				<option value="AUD" <?php echo ( $currency_conversion_selection == 'AUD' ) ? 'selected' : ''; ?>>AUD</option>
				<option value="RON" <?php echo ( $currency_conversion_selection == 'RON' ) ? 'selected' : ''; ?>>RON</option>
				<option value="SEK" <?php echo ( $currency_conversion_selection == 'SEK' ) ? 'selected' : ''; ?>>SEK</option>
				<option value="IDR" <?php echo ( $currency_conversion_selection == 'IDR' ) ? 'selected' : ''; ?>>IDR</option>
				<option value="INR" <?php echo ( $currency_conversion_selection == 'INR' ) ? 'selected' : ''; ?>>INR</option>
				<option value="BRL" <?php echo ( $currency_conversion_selection == 'BRL' ) ? 'selected' : ''; ?>>BRL</option>
				<option value="RUB" <?php echo ( $currency_conversion_selection == 'RUB' ) ? 'selected' : ''; ?>>RUB</option>
				<option value="HRK" <?php echo ( $currency_conversion_selection == 'HRK' ) ? 'selected' : ''; ?>>HRK</option>
				<option value="JPY" <?php echo ( $currency_conversion_selection == 'JPY' ) ? 'selected' : ''; ?>>JPY</option>
				<option value="KES" <?php echo ( $currency_conversion_selection == 'KES' ) ? 'selected' : ''; ?>>KES</option>
				<option value="THB" <?php echo ( $currency_conversion_selection == 'THB' ) ? 'selected' : ''; ?>>THB</option>
				<option value="CHF" <?php echo ( $currency_conversion_selection == 'CHF' ) ? 'selected' : ''; ?>>CHF</option>
				<option value="SGD" <?php echo ( $currency_conversion_selection == 'SGD' ) ? 'selected' : ''; ?>>SGD</option>
				<option value="PLN" <?php echo ( $currency_conversion_selection == 'PLN' ) ? 'selected' : ''; ?>>PLN</option>
				<option value="BGN" <?php echo ( $currency_conversion_selection == 'BGN' ) ? 'selected' : ''; ?>>BGN</option>
				<option value="TRY" <?php echo ( $currency_conversion_selection == 'TRY' ) ? 'selected' : ''; ?>>TRY</option>
				<option value="CNY" <?php echo ( $currency_conversion_selection == 'CNY' ) ? 'selected' : ''; ?>>CNY</option>
				<option value="MDL" <?php echo ( $currency_conversion_selection == 'MDL' ) ? 'selected' : ''; ?>>MDL</option>
				<option value="MNT" <?php echo ( $currency_conversion_selection == 'MNT' ) ? 'selected' : ''; ?>>MNT</option>
				<option value="NOK" <?php echo ( $currency_conversion_selection == 'NOK' ) ? 'selected' : ''; ?>>NOK</option>
				<option value="NGN" <?php echo ( $currency_conversion_selection == 'NGN' ) ? 'selected' : ''; ?>>NGN</option>
				<option value="NZD" <?php echo ( $currency_conversion_selection == 'NZD' ) ? 'selected' : ''; ?>>NZD</option>
				<option value="ZAR" <?php echo ( $currency_conversion_selection == 'ZAR' ) ? 'selected' : ''; ?>>ZAR</option>
				<option value="UAH" <?php echo ( $currency_conversion_selection == 'UAH' ) ? 'selected' : ''; ?>>UAH</option>
				<option value="UGX" <?php echo ( $currency_conversion_selection == 'UGX' ) ? 'selected' : ''; ?>>UGX</option>
				<option value="USD" <?php echo ( $currency_conversion_selection == 'USD' ) ? 'selected' : ''; ?>>USD</option>
				<option value="MXN" <?php echo ( $currency_conversion_selection == 'MXN' ) ? 'selected' : ''; ?>>MXN</option>
				<option value="ILS" <?php echo ( $currency_conversion_selection == 'ILS' ) ? 'selected' : ''; ?>>ILS</option>
				<option value="GBP" <?php echo ( $currency_conversion_selection == 'GBP' ) ? 'selected' : ''; ?>>GBP</option>
				<option value="KRW" <?php echo ( $currency_conversion_selection == 'KRW' ) ? 'selected' : ''; ?>>KRW</option>
				<option value="MYR" <?php echo ( $currency_conversion_selection == 'MYR' ) ? 'selected' : ''; ?>>MYR</option>
				<option value="KRW" <?php echo ( $currency_conversion_selection == 'KRW' ) ? 'selected' : ''; ?>>KRW</option>
			</select>
			</div>
		</div>
		<?php
	}
	private function field_currency_num_format() {
		$currency_style = get_option( 'df_scc_currency_style', 'default' ); // dot or comma
		?>
		<div class="mb-3 row">
			<label class="col-sm-5 col-form-label use-tooltip" title="Choose your calculator forms currency. You can choose to have a symbol ($) or intials (USD) at the calculator settings level." for="currency-style">Currency Format:</label>
			<div class=" col-sm-7">
				<select class="form-select form-select-lg mb-3" name="currency-style" id="currency-style">
					<option value="default" <?php echo ( $currency_style == 'default' ) ? 'selected' : ''; ?>>Browser Locale</option>
					<option value="comma" <?php echo ( $currency_style == 'comma' ) ? 'selected' : ''; ?>>Comma separated</option>
				</select>
			</div>
		</div>
		<?php
	}
	private function field_currency_conversion_type() {
		 $currency_conversion_mode = get_option( 'df_scc_currency_coversion_mode', 'off' );
		?>
		<div class="mb-3 row <?php if ( $this->isSCCFreeVersion ) { echo "use-tooltip-child-nodes";} ?>" data-tooltip-image="<?php echo esc_url( SCC_TOOLTIP_BASEURL . '/infographic-feat-live-conversion.png' ) ?>" >
			<label class="col-sm-5 col-form-label" for="scc_currency_coversion_mode">Currency Conversion: </label>
			<span class=" col-sm-7">
				<select class="form-select form-select-lg mb-3" 
				<?php
				if ( $this->isSCCFreeVersion ) {
					echo 'disabled';}
				?>
				 name="scc_currency_coversion_mode" id="scc_currency_coversion_mode" onchange="handleCurrencyCoversionMode(this)">
					<option value="off" <?php echo ( $currency_conversion_mode == 'off' ) ? 'selected' : ''; ?>>OFF (default)</option>
					<option value="manual_selection" <?php echo ( $currency_conversion_mode == 'manual_selection' ) ? 'selected' : ''; ?>>Select a currency</option>
					<option value="auto_detect" <?php echo ( $currency_conversion_mode == 'auto_detect' ) ? 'selected' : ''; ?>>Auto detect currency</option>
				</select>
				</span>
		</div>
		<?php
	}
	private function field_pdf_footer_notes() {
		 $disclaimer = wp_kses_post( get_option( 'df_scc_footerdisclaimer' ) );
		?>
		<div class="mb-3">
			<label for="sccemailfooter" class="form-label">Footer/Desclaimer Notes</label>
			<textarea class="form-control" id="sccemailfooter" rows="3"><?php echo wp_kses_post(str_replace( '\\', '', stripslashes( $disclaimer )) ); ?></textarea>
		</div>
		<?php
	}
	private function field_banner_and_logo() {
		$disclaimer   = wp_kses_post( get_option( 'df_scc_footerdisclaimer' ) );
		$banner_image = get_option( 'df_scc_email_banner_image', false );
		$logo_image   = get_option( 'df_scc_email_logo_image', false );
		?>
		<div class="mb-3 row scc-form-field">
			<div class=" col-sm-6">
				<label class="col-form-label">Banner <span 
					class="tooltipadmin-right"
					data-tooltip="Add a banner to the PDF/Detail View pop-up. Make sure you have GD library installed on your server. Choose a compressed/optimized image to conserve RAM usage">
					<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
				</span></label>
				<div class="text-center upload-btn-wrapper" style="display: <?php echo $banner_image ? 'none' : 'block'; ?>;">
					<button type="button" class="btn btn-primary scc-media-upload-button">Upload Banner</button>
					<input type="hidden" name="df_scc_email_banner_image" class="scc-media-upload-field" value="<?php echo esc_attr( $banner_image ); ?>">
				</div>
				<div class="scc-media-uploader-image" style="display: <?php echo $banner_image ? 'block' : 'none'; ?>;">
					<?php
					if ( $banner_image ) {
						echo '<img src="' . esc_attr( $banner_image ) . '" />';
					}
					?>
					<span class="scc-media-uploader-remove">&times;</span>
				</div>
			</div>
			<div class=" col-sm-6">
				<label class="col-form-label">Logo <span 
					class="tooltipadmin-right"
					data-tooltip="Add a logo to the PDF/Detail View pop-up. Make sure you have GD library installed on your server. Choose a compressed/optimized image to conserve RAM usage">
					<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
				</span></label>
				<div class="text-center upload-btn-wrapper" style="display: <?php echo $logo_image ? 'none' : 'block'; ?>;">
					<button type="button" class="btn btn-primary scc-media-upload-button">Upload Logo</button>
					<input type="hidden" name="df_scc_email_logo_image" class="scc-media-upload-field" value="<?php echo esc_attr( $logo_image ); ?>">
				</div>
				<div class="scc-media-uploader-image" style="display: <?php echo $logo_image ? 'block' : 'none'; ?>;">
					<?php
					if ( $logo_image ) {
						echo '<img src="' . esc_attr( $logo_image ) . '" />';
					}
					?>
					<span class="scc-media-uploader-remove">&times;</span>
				</div>
			</div>
		</div>
		<?php
	}
	private function field_email_quote_settings_form() {
		$scc_sendername   = get_option( 'df_scc_sendername' );
		$scc_emailsender  = get_option( 'df_scc_emailsender' );
		$scc_emailsubject = get_option( 'df_scc_emailsubject' );
		?>
		<div id="email_form_short_codes_section" style="display: none; background: rgb(221, 223, 248); padding: 20px; margin-bottom: 10px;">
					<strong>
						<p>NOTE: Use these shortcodes to customize your email template to your customers.</p>
					</strong>
					<p>Customer's Email &lt;customer-email&gt;</p>
					<p>Customer Name &lt;customer-name&gt;</p>
					<p>Customer Phone &lt;customer-phone&gt;</p>
					<p>Customer Broswer &lt;customer-browser-info&gt;</p>
					<p>Customer Device &lt;device&gt;</p>
					<p>Customer IP &lt;customer-ip-address&gt;</p>
					<p>Sender (Your) Name &lt;sender&gt;</p>
				</div>
		<div class="row">
			<div class="col-sm-6 col-form-label">
				<input type="text" class="form-control" id="sendername" placeholder="Sender Name (You)" value="<?php echo esc_attr(sanitize_text_field( $scc_sendername )); ?>">
			</div>
			<div class=" col-sm-6 col-form-label">
				<input type="text" class="form-control" id="senderemail" placeholder="Sender Email (Your Email)" value="<?php echo esc_attr(sanitize_text_field( $scc_emailsender )); ?>">
			</div>
		</div>
		<div class="row">
			<div class="col-sm-6 col-form-label">
				<input type="text" class="form-control" id="scc_email_send_copy" placeholder="CC Email Address (optional)" value="<?php echo esc_attr(get_option( 'df_scc_email_send_copy' )); ?>">
			</div>
		</div>
		<div class="mb-3 row">
			<div class="col-sm-12 col-form-label">
				<input type="text" class="form-control" id="emailsubject" placeholder="Email Subject" value="<?php echo esc_attr(stripslashes( htmlspecialchars( $scc_emailsubject ) )); ?>">
			</div>
		</div>
		<?php
		$scc_messageform = get_option( 'df_scc_messageform' );
		$tags            = array( '<customer-name>', '<customer-phone>', '<customer-email>', '<customer-ip-address>', '<customer-browser-info>', '<device>', '<customer-referral>' );
		$tags_htmlified  = array_map(
			function ( $a ) {
				return htmlentities( $a );
			},
			$tags
		);
		$scc_messageform = str_replace( $tags, $tags_htmlified, $scc_messageform );
		wp_editor( stripslashes( str_replace( array( '<br/>', '<br>', '< br/>' ), array( "\r\n", "\r", "\n" ), $scc_messageform ) ), 'messagetemplate', array() );
	}
	private function field_pdf_settings_form() {
		?>
		<p class="mb-3">Select the font and date format you would like to use for email quote and detailed list view.</p>
		<div class="mb-3 row  <?php if ( $this->isSCCFreeVersion ) { echo "use-premium-tooltip";} ?>">
			<label class="col-sm-5 col-form-label use-tooltip" title="Choose the font family you want to use. Depending on the language you use, choosing a different font can improve the texts on the PDF">PDF Format</label>
			<div class=" col-sm-7">
				<select class="form-select form-select-lg mb-3" 
				<?php
				if ( $this->isSCCFreeVersion ) {
					echo 'disabled';}
				?>
				 id="pdf_font">
					<option value="regular" 
					<?php
					if ( get_option( 'sccPDFFont' ) == 'regular' ) {
						echo 'selected';}
					?>
					>Regular</option>
					<option value="cid0jp" 
					<?php
					if ( get_option( 'sccPDFFont' ) == 'cid0jp' ) {
						echo 'selected';}
					?>
					>cid0jp (Japanese and Russian support)</option>
					<option value="dejavusans" 
					<?php
					if ( get_option( 'sccPDFFont' ) == 'dejavusans' ) {
						echo 'selected';}
					?>
					>DejaVuSans</option>
					<option value="dejavusansb" 
					<?php
					if ( get_option( 'sccPDFFont' ) == 'dejavusansb' ) {
						echo 'selected';}
					?>
					>DejaVuSans-Bold</option>
					<option value="dejavusansbi" 
					<?php
					if ( get_option( 'sccPDFFont' ) == 'dejavusansbi' ) {
						echo 'selected';}
					?>
					>DejaVuSans-BoldOblique</option>
					<option value="helvetica" 
					<?php
					if ( get_option( 'sccPDFFont' ) == 'helvetica' ) {
						echo 'selected';}
					?>
					>Helvetica</option>
					<option value="Helvetica-Bold" 
					<?php
					if ( get_option( 'sccPDFFont' ) == 'Helvetica-Bold' ) {
						echo 'selected';}
					?>
					>Helvetica-Bold</option>
					<option value="Helvetica-BoldOblique" 
					<?php
					if ( get_option( 'sccPDFFont' ) == 'Helvetica-BoldOblique' ) {
						echo 'selected';}
					?>
					>Helvetica-BoldOblique</option>
					<option value="Helvetica-Italic" 
					<?php
					if ( get_option( 'sccPDFFont' ) == 'Helvetica-Italic' ) {
						echo 'selected';}
					?>
					>Helvetica-Italic</option>
				</select>
			</div>
		</div>
		<div class="mb-3 row">
			<label class="col-sm-5 col-form-label use-tooltip" title="Choose the date format you want to use.">Date Format</label>
			<div class=" col-sm-7">
				<select class="form-select form-select-lg mb-3" id="pdf_datefmt">
					<option value="mm-dd-yyyy" 
					<?php
					if ( get_option( 'scc_pdf_datefmt' ) == 'mm-dd-yyyy' ) {
						echo 'selected';}
					?>
					>mm-dd-yyyy</option>
					<option value="dd-mm-yyyy" 
					<?php
					if ( get_option( 'scc_pdf_datefmt' ) == 'dd-mm-yyyy' ) {
						echo 'selected';}
					?>
					>dd-mm-yyyy</option>
				</select>
			</div>
		</div>
		<?php
	}
	private function field_recaptcha_settings_form() {
		?>
		<div id="recaptcha">
			<p class="mb-3">Please enter the recaptcha keys in the following fields.</p>
			<div class="">
				<input class="form-check-input" type="checkbox" name="captcha-enablement-status" role="switch" id="captcha-enablement-status" 
				<?php
				if ( get_option( 'df_scc-captcha-enablement-status', false ) ) {
					echo 'checked';}
				?>
				>
				<label class="form-check-label" for="captcha-enablement-status">Enable reCaptcha v2</label>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-5 col-form-label">Site Key</label>
				<div class=" col-sm-7">
					<input 
					<?php
					if ( $this->isSCCFreeVersion ) {
						echo 'disabled';}
					?>
					 type="text" class="form-control" name="site-key-recaptcha" placeholder="" value="<?php echo esc_attr(get_option( 'df_scc-recaptcha-site-key' )); ?>">
				</div>
			</div>
			<div class="mb-3 row">
				<label class="col-sm-5 col-form-label">Secret Key</label>
				<div class=" col-sm-7">
					<input 
					<?php
					if ( $this->isSCCFreeVersion ) {
						echo 'disabled';}
					?>
					 type="text" class="form-control" name="secret-key-recaptcha" placeholder="" value="<?php echo esc_attr(get_option( 'df_scc-recaptcha-secret-key' )); ?>">
				</div>
			</div>
		</div>
		<?php
	}
	private function field_restore_calc_form() {
		?>
		<div class="mb-3">
			<label for="backup_scc_file" class="form-label">Please select a backup file for restoration</label>
			<input class="form-control" type="file" id="backup_scc_file" onclick="sscUploadSccBackup()" accept=".json">
		</div>
		<?php
	}


	/** stripe settings fields */

	private function field_stripe_secret_key() {
		?>
		<div class="mb-3 row">
			<label class="col-sm-5 col-form-label">Stripe Secret Key: </label>
			<div class=" col-sm-7">
				<input type="text" class="form-control" name="stripe-api-priv-key" placeholder="<?php echo esc_attr( $this->pubKeyPlaceHolder ); ?>">
			</div>
		</div>
		<?php
	}
	private function field_stripe_public_key() {
		?>
		<div class="mb-3 row">
			<label class="col-sm-5 col-form-label">Stripe Public Key: </label>
			<div class=" col-sm-7">
				<input type="text" class="form-control" name="stripe-api-pub-key" placeholder="<?php echo esc_attr( $this->privKeyPlaceHolder ); ?>">
			</div>
		</div>
		<?php
	}

	private function formFields( $fieldLabel, $fieldType, $fieldName, $choices = null, $currentValue ) {
		?>
		<div class="mb-3 row">
			<label for="<?php echo esc_attr( $fieldName ); ?>" class="col-sm-5 col-form-label"><?php echo esc_attr( $fieldLabel ); ?></label>
			<div class="col-sm-7">
				<?php
				switch ( $fieldType ) {
					case 'select':
						$this->renderChoices( $choices, $currentValue );
						break;

					default:
						echo '<input type="<?php echo esc_attr($fieldType) ?>" readonly class="form-control-plaintext" id="<?php echo esc_attr($fieldName); ?>" value="email@example.com">';
						break;
				}
				?>
			</div>
		</div>
		<?php
	}

	private function renderChoices() {
		$currency_conversion_selection = get_option( 'df_scc_currency_coversion_manual_selection' );
		?>
		<select class="form-select form-select-lg mb-3" name="scc_currency_coversion_manual_selection" id="scc_currency_coversion_manual_selection">
		<option value="EUR" <?php echo ( $currency_conversion_selection == 'EUR' ) ? 'selected' : ''; ?>>EUR</option>
							<option value="CAD" <?php echo ( $currency_conversion_selection == 'CAD' ) ? 'selected' : ''; ?>>CAD</option>
							<option value="HKD" <?php echo ( $currency_conversion_selection == 'HKD' ) ? 'selected' : ''; ?>>HKD</option>
							<option value="ISK" <?php echo ( $currency_conversion_selection == 'ISK' ) ? 'selected' : ''; ?>>ISK</option>
		</select>
		<?php
	}

	/**
	 * HTML for email template banner and logo
	 */
	private function email_template_banner_and_logo() {
		 $disclaimer  = wp_kses_post( get_option( 'df_scc_footerdisclaimer' ) );
		$banner_image = get_option( 'df_scc_email_banner_image', false );
		$logo_image   = get_option( 'df_scc_email_logo_image', false );
		?>
		<h2 class="scc-settings-card-title"><span class="highlighted">EMAIL, PDF & DETAILED LIST</span> SETTINGS <a href="https://designful.freshdesk.com/a/solutions/articles/48001167920" target="_blank"><i class="fa fa-book" aria-hidden="true"></i></a></h2>
		</span>
		<div class="scc-settings-card-inner" id="scc_email_quote_settings">
			<div class="scc-form-field" style="padding: 5px;border: 2px solid #2271b1;">
				<label for="sccemailfooter" style="margin-bottom: 15px;">
					<span style="font-weight:800;color:#314bf8;">FOOTER/DISCLAIMER</span> NOTES
				</label>
				<textarea class="scc-textarea-field" onkeyup="jQuery('.scc_save_emdl:eq(0)').addClass('button-glow')" placeholder="" id="sccemailfooter" rows="14"><?php echo wp_kses_post(str_replace( '\\', '', stripslashes( $disclaimer ) )); ?></textarea>
				<input class="sccbutton scc_save_emdl" style="width:100%;height:45px;font-size:18px" type="submit" name="Save" value="SAVE FOOTER SETTINGS" onclick="saveSCCEmailSetting(this)">
			</div>
			<?php if ( $this->isSCCFreeVersion ) : ?>
			<div class="blocked" style="width: 100%; padding: 5px; background-color: black; opacity: 0.85; display: inline-block; position: relative;"><div style="position:absolute;left:0px;right:0px;top:0px;bottom:0px;z-index:99999;opacity:1">	<center><h5 style="color:white;margin-top:50px">THIS FEATURE IS AVAILABLE IN THE PREMIUM VERSION</h5></center>	<div style="margin-left:40%;margin-top:20px;background-color:#314af3;padding:5px;max-width:100px;text-align:center">		<a target="_blank" href="https://stylishcostcalculator.com/" style="z-index:99999;opacity:1; color: white">BUY NOW		</a>	</div></div>
			<?php endif; ?>
			<div class="scc-form-field" style="padding: 5px;border: 2px solid #2271b1;">
				<label style="margin-bottom: 15px;">
					<span style="font-weight:800;color:#314bf8;">DETAIL LIST</span> HEADER
				</label>
				<label>Banner <span 
					class="tooltipadmin-right"
					data-tooltip="Add a banner to the PDF/Detail View pop-up. Make sure you have GD library installed on your server. Choose a compressed/optimized image to conserve RAM usage">
					<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
				</span></label>
				<div class="scc-media-uploader-buttons" style="display: <?php echo $banner_image ? 'none' : 'block'; ?>;">
					<input type="button" class="sccbutton scc-media-upload-button" value="Upload Banner">
					<input type="hidden" name="df_scc_email_banner_image" class="scc-media-upload-field" value="<?php echo esc_attr( $banner_image ); ?>">
					<span class="spinner"></span>
				</div>
				<div class="scc-media-uploader-image" style="display: <?php echo $banner_image ? 'block' : 'none'; ?>;">
					<?php
					if ( $banner_image ) {
						echo '<img src="' . esc_attr( $banner_image ) . '" />';
					}
					?>
					<span class="scc-media-uploader-remove">&times;</span>
				</div>
				<label>Logo <span 
					class="tooltipadmin-right"
					data-tooltip="Add a logo to the PDF/Detail View pop-up. Make sure you have GD library installed on your server. Choose a compressed/optimized image to conserve RAM usage">
					<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px" fill="#000000"><path d="M0 0h24v24H0z" fill="none"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
				</span></label>
				<div class="scc-media-uploader-buttons" style="display: <?php echo $logo_image ? 'none' : 'block'; ?>;">
					<input type="button" class="sccbutton scc-media-upload-button" value="Upload Logo">
					<input type="hidden" name="df_scc_email_logo_image" class="scc-media-upload-field" value="<?php echo esc_attr( $logo_image ); ?>">
					<span class="spinner"></span>
				</div>
				<div class="scc-media-uploader-image" style="display: <?php echo $logo_image ? 'block' : 'none'; ?>;">
					<?php
					if ( $logo_image ) {
						echo '<img src="' . esc_attr( $logo_image ) . '" />';
					}
					?>
					<span class="scc-media-uploader-remove">&times;</span>
				</div>
			</div>
				</div>
		<?php
	}
}

new Stylish_Cost_Calculator_Settings();

?>
