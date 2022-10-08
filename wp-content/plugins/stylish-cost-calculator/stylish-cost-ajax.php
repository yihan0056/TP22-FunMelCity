<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use JetBrains\PhpStorm\Internal\ReturnTypeContract;

/**
 * *Handles all ajaxes request
 * ?all ajax request must be coded here
 Testing by Mike second version.
 */
class ajaxRequest {

	public function __construct() {
		if ( current_user_can( 'manage_options' ) ) {
			add_action( 'wp_ajax_sccCalculatorOp', array( $this, 'scc_calculator_op' ) );
			/**
			 * *Handles restore backup from json file
			 * !each time is added a column o modified a table, must be modified here as well
			 */
			//load examples
			add_action( 'wp_ajax_sscLoadExample', array( $this, 'ssc_loadExample' ) );
			//ajax of elements
			add_action( 'wp_ajax_sccAddCheckboxItems', array( $this, 'scc_addCheckboxItems' ) );
			add_action( 'wp_ajax_sccAddElementCheckbox', array( $this, 'scc_addElementCheckbox' ) );
			add_action( 'wp_ajax_sccAddElementCommentBox', array( $this, 'scc_addElementCommentBox' ) );
			add_action( 'wp_ajax_sccAddElementQuantityBox', array( $this, 'scc_addElementQuantityBox' ) );
			add_action('wp_ajax_sccAddElementFileUpload', array($this, 'scc_addElementFileUpload'));

			add_action( 'wp_ajax_sccAddElementTextHtml', array( $this, 'scc_addElementTextHtml' ) );
			add_action( 'wp_ajax_sccAddElementSlider', array( $this, 'scc_addElementSlider' ) );
			add_action( 'wp_ajax_sccSaveSection', array( $this, 'scc_saveSection' ) );
			add_action( 'wp_ajax_sccDelSection', array( $this, 'scc_delSection' ) );
			add_action( 'wp_ajax_sccUpSection', array( $this, 'scc_upSection' ) );
			add_action( 'wp_ajax_sccDelSubsection', array( $this, 'scc_delSubsection' ) );
			add_action( 'wp_ajax_sccAddSubsection', array( $this, 'scc_addSubsection' ) );
			add_action( 'wp_ajax_sccDelElement', array( $this, 'scc_delElement' ) );
			add_action( 'wp_ajax_sccDelElementItem', array( $this, 'scc_delElementItem' ) );
			add_action( 'wp_ajax_sccAddElementSwichoption', array( $this, 'scc_addElementSwichoption' ) );
			add_action( 'wp_ajax_sccUpElement', array( $this, 'scc_upElement' ) );
			add_action( 'wp_ajax_sccUpElementOrder', array( $this, 'sccUpElementOrder' ) );
			add_action( 'wp_ajax_sccUpElementItemSwichoption', array( $this, 'scc_upElementItemSwichoption' ) );
			add_action( 'wp_ajax_sccAddElementDropdownMenu', array( $this, 'scc_addsElementDropdownMenu' ) );
			add_action( 'wp_ajax_sccUpElementItemSlider', array( $this, 'scc_upElementItemSlider' ) );
			add_action( 'wp_ajax_sccAddElementItemSlider', array( $this, 'scc_addElementItemSlider' ) );
			//saves setting and translations of calculator
			add_action( 'wp_ajax_sccSaveForm', array( $this, 'scc_saveFormNameSettings' ) );
			//shows shortcode in backend
			add_action( 'wp_ajax_sccPreviewOneForm', array( $this, 'scc_previewOneForm' ) );
			//duplicate element function
			add_action( 'wp_ajax_sccDuplicateElement', array( $this, 'scc_duplicateElement' ) );
			add_action( 'wp_ajax_sccGlobalSettings', array( $this, 'scc_globalSettings' ) );
			// migration ajax automatic
			// add_action('wp_ajax_sccMigrateAuto', array($this, 'scc_migrateAuto'));
			add_action( 'wp_ajax_sccMigrateAuto2', array( $this, 'scc_migrateAuto2' ) );
			// migration ajax automatic
			// add_action('wp_ajax_sccMigrateManual', array($this, 'scc_migrateManual'));
			// update section order
			add_action( 'wp_ajax_sccUpdateSectionOrder', array( $this, 'scc_updateSectionOrder' ) );

			add_action( 'wp_ajax_sccPDFSettings', array( $this, 'sccPDFSettings' ) );
			add_action( 'wp_ajax_scc_feedback_manage', array( $this, 'sccFeedbackManage' ) );
			add_action( 'wp_ajax_scc_get_debug_items', array( $this, 'get_debug_items' ) );
		}

		// public ajax calls
		add_action( 'wp_ajax_nopriv_sccUpdateUrlStats', array( $this, 'sccUpdateUrlStats' ) );
		add_action( 'wp_ajax_sccUpdateUrlStats', array( $this, 'sccUpdateUrlStats' ) );
	}

	function sccUpElementOrder() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
		$e    = new elementController();
		$data = sanitize_text_or_array_field( $_GET['arr'] );
		foreach ( $data as $v ) {
			$el['id']            = intval( $v['id_element'] );
			$el['orden']         = intval( $v['order'] );
			$el['subsection_id'] = intval( $v['subsection'] );
			$e->update( $el );
		}
		wp_send_json( array( 'passed' => true ) );
	}

	/**
	 * *Handles migration of coupons
	 * ?is used in manual and auto updates
	 * @param $migration migration controller instance
	 */
	public static function migrate_coupon( $migration ) {
		require_once dirname( __FILE__ ) . '/admin/controllers/couponController.php';
		$cc  = new \couponController();
		$ccc = $migration->getAllOldCoupons();
		foreach ( $ccc as $c ) {
			$cc->create( (array) $c );
		}
	}
	/**
	 * *Handles migration of global settings
	 * ?is use in manual and auto updates
	 * @param $migration migration controller instance
	 */
	public function scc_migrateAuto2() {
		global $wpdb;
		$wpdb->query( "DELETE FROM {$wpdb->prefix}df_scc_forms" );
		self::scc_migrateAuto();
	}
	public static function migrate_global( $migration ) {
		$migration::update_wpOptions();
	}
	public static function scc_migrateAuto() {
		require_once dirname( __FILE__ ) . '/admin/controllers/migrateController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/formController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/conditionController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/sectionController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/subsectionController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/quoteSubmissionsController.php';
		$calculatorC  = new formController();
		$conditionalC = new conditionController();
		$sectionC     = new sectionController();
		$subsectionC  = new subsectionController();
		$elementC     = new elementController();
		$elementitemC = new elementitemController();
		$quoteC       = new quoteSubmissionsController();
		$m            = new migrateController();
		$cals         = $m->getAllOldCalulator();
		foreach ( $cals as $c ) {
			$json_data_ = $m->getCalculatorData( $c->id );
			self::migrate_to( $json_data_, $calculatorC, $conditionalC, $sectionC, $subsectionC, $elementC, $elementitemC, true, $quoteC, false );
		}
		self::migrate_coupon( $m );
		// self::migrate_global($m);
		wp_send_json( array( 'passed' => true ) );
		die();
	}
	/**
	 * *Handles migration
	 * ?used multiple times
	 * @param array $json_data data of items
	 * @param mixed $calculatorC form Controller instance
	 * @param mixed $sectionC section Controller instance
	 * @param mixed $subsectionC subsection Controller instance
	 * @param mixed $elementC element Controller instance
	 * @param mixed $elementitemC elementItem Controller instance
	 * @param bool $quote send true or false to backup quotes
	 * @param bool $restore_ dont send the id if is restored json
	 * @return bool true to backup quotes
	 * ?for automatic backup send true for the $quotes param
	 */
	public static function migrate_to( $json_data, $calculatorC, $conditionalC, $sectionC, $subsectionC, $elementC, $elementitemC, $quote, $quoteC, $restore_ ) {
		if ( $json_data['scc_form'] ) {
			$json_data['scc_form']                          = json_decode( json_encode( $json_data['scc_form'] ), true );
			$json_data['scc_form_parameters']               = json_decode( json_encode( $json_data['scc_form_parameters'] ), true );
			$json_data['scc_form_parameters']['parameters'] = json_decode( json_encode( $json_data['scc_form_parameters']['parameters'] ), true );
			if ( ! $restore_ ) {
				$c['id'] = $json_data['scc_form']['id'];
			}
			$paraa                   = $json_data['scc_form_parameters']['parameters'];
			$c['formname']           = sanitize_text_field( $json_data['scc_form']['formname'] );
			$c['fontType']           = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['fontType'] );
			$c['fontWeight']         = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['fontTypeVariant'] );
			$c['ServiceColorPicker'] = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['colorPicker'] );
			$c['ServicefontSize']    = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['servicepricefontsize'] );
			$c['objectColorPicker']  = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['objectColorPicker'] );
			$c['objectSize']         = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['objectServicepricefontsize'] );
			$c['titleFontType']      = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['titleFontType'] );
			$c['titleFontWeight']    = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['titleFontTypeVariant'] );
			$c['titleColorPicker']   = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['titleColorPicker'] );
			$c['titleFontSize']      = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['titleServicepricefontsize'] );
			$c['titleFontSize']      = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['titleServicepricefontsize'] );
			$c['barstyle']           = ( $paraa['objecttotalpricestyle'] !== 'scc_tp_style1' && $paraa['objecttotalpricestyle'] !== 'scc_tp_style2' &&
			$paraa['objecttotalpricestyle'] !== 'scc_tp_style3' && $paraa['objecttotalpricestyle'] !== 'scc_tp_style4' ) ? 'scc_tp_style1' : sanitize_text_field( $paraa['objecttotalpricestyle'] );
			$c['elementSkin']        = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['form_field_style'] );
			$c['symbol']             = sanitize_text_field( $json_data['scc_form_parameters']['parameters']['currency_style'] );
			if (isset($paraa["objectDisableQtyCol"]) && $paraa["objectDisableQtyCol"] == "turn_off_save_icon") $c["turnoffQty"] = "true";
			//?Extra parameters
			if ( isset( $paraa['inheritFontType'] ) ) {
				$c['inheritFontType'] = sanitize_text_field( $paraa['inheritFontType'] );
			}
			if ( isset( $paraa['objectsendquote'] ) && $paraa['objectsendquote'] == 'turn_off_send_quote' ) {
				$c['turnoffemailquote'] = 'true';
			}
			if ( isset( $paraa['objectdetailedlist'] ) && $paraa['objectdetailedlist'] == 'turn_off_viewed_detailed_list' ) {
				$c['turnviewdetails'] = 'true';
			}
			if ( isset( $paraa['objectscccoupon'] ) && $paraa['objectscccoupon'] == 'turn_off_coupon' ) {
				$c['turnoffcoupon'] = 'true';
			}
			if ( isset( $paraa['objectscctotalprice'] ) && $paraa['objectscctotalprice'] == 'scc_turn_off_total_price_view' ) {
				$c['removeTotal'] = 'true';
			}
			if ( isset( $paraa['objectscctitlelabel'] ) && $paraa['objectscctitlelabel'] == 'scc_remove_detailed_list_title' ) {
				$c['removeTitle'] = 'true';
			}
			if ( isset( $paraa['objectDisableUnitCol'] ) && $paraa['objectDisableUnitCol'] == 'turn_off_save_icon' ) {
				$c['turnoffUnit'] = 'true';
			}
			if ( isset( $paraa['objectsaveicon'] ) && $paraa['objectsaveicon'] == 'turn_off_save_icon' ) {
				$c['turnoffSave'] = 'true';
			}
			if ( isset( $paraa['objectscctax'] ) && $paraa['objectscctax'] == 'turn_off_tax' ) {
				$c['turnoffTax'] = 'true';
			}
			if ( isset( $paraa['woocommerce_checked'] ) ) {
				$c['isWoocommerceCheckoutEnabled'] = sanitize_text_field( $paraa['woocommerce_checked'] );
			}
			if ( isset( $paraa['isStripeEnabled'] ) ) {
				$c['isStripeEnabled'] = sanitize_text_field( $paraa['isStripeEnabled'] );
			}
			if ( isset( $paraa['isPayBtnHoverEffectEnabled'] ) ) {
				$c['turnoffborder'] = sanitize_text_field( $paraa['isPayBtnHoverEffectEnabled'] );
			}
			if ( isset( $paraa['formFieldsArray'] ) ) {
				$c['formFieldsArray'] = sanitize_text_field( $paraa['formFieldsArray'] );
			}
			$pp = $json_data['scc_form_parameters']['parameters'];
			if (
				isset( $pp['paypal_email'] ) && isset( $pp['paypal_shopping_cart_name'] ) && isset( $pp['paypal_checked'] )
				&& isset( $pp['paypalSuccessURL'] ) && isset( $pp['paypalCancelURL'] ) && isset( $pp['objectTaxInclusionInPayPal'] )
				&& isset( $pp['paypal_currency'] )
			) {
				$payPalJson             = array(
					'paypal_email'               => sanitize_email( $json_data['scc_form_parameters']['parameters']['paypal_email'] ),
					'paypal_shopping_cart_name'  => sanitize_text_field( $json_data['scc_form_parameters']['parameters']['paypal_shopping_cart_name'] ),
					'paypal_checked'             => sanitize_text_field( $json_data['scc_form_parameters']['parameters']['paypal_checked'] ),
					'paypalSuccessURL'           => sanitize_text_field( $json_data['scc_form_parameters']['parameters']['paypalSuccessURL'] ),
					'paypalCancelURL'            => sanitize_text_field( $json_data['scc_form_parameters']['parameters']['paypalCancelURL'] ),
					'objectTaxInclusionInPayPal' => sanitize_text_field( $json_data['scc_form_parameters']['parameters']['objectTaxInclusionInPayPal'] ),
					'paypal_currency'            => sanitize_text_field( $json_data['scc_form_parameters']['parameters']['paypal_currency'] ),
				);
				$c['paypalConfigArray'] = json_encode( $payPalJson );
			}
			//handles webhook migration TESTING
			if ( isset( $json_data['scc_form_parameters']['parameters']['webhookSettings'] ) ) {
				$c['webhookSettings'] = json_encode( json_decode( stripslashes( $json_data['scc_form_parameters']['parameters']['webhookSettings'] ) ) );
			}
			$c['translation'] = json_encode( $json_data['scc_form']['formtranslate'] );
			$id_c             = $calculatorC->create( $c );
			foreach ( $json_data['scc_form']['formstored'] as $section ) {
				$section           = json_decode( json_encode( $section ), true );
				$sc['name']        = sanitize_text_field( $section['name'] );
				$sc['description'] = sanitize_text_field( $section['desc'] );
				$sc['order']       = intval( $section['section'] );
				if ( isset( $section['accordion'] ) && $section['accordion'] == true ) {
					$sc['accordion'] = 'true';
				}
				if ( isset( $section['showSectionTotal'] ) && $section['showSectionTotal'] == true ) {
					$sc['showSectionTotal'] = 'true';
				}
				$sc['form_id'] = intval( $id_c );
				$id_sec        = $sectionC->create( $sc );
				foreach ( $section['value'] as $subsection ) {
					$sb['order']      = intval( $subsection['subsection'] );
					$sb['section_id'] = intval( $id_sec );
					$id_sub           = $subsectionC->create( $sb );
					if ( isset( $subsection['minmax'] ) && count( $subsection['minmax'] ) > 0 ) {
						$e['type']          = 'slider';
						$e['orden']         = count( $subsection['Nooptions'] ) + 1;
						$e['titleElement']  = wp_kses( $subsection['minmax'][0]['title'], SCC_ALLOWTAGS );
						$e['mandatory']     = sanitize_text_field( $subsection['minmax'][0]['mandatory'] );
						$e['value2']        = intval( $subsection['step'] );
						$e['value3']        = intval( $subsection['defaultValue'] );
						$e['showPriceHint'] = sanitize_text_field( $subsection['showPriceHint'] );
						if ( isset( $subsection['uniqueID'] ) ) {
							$e['uniqueId'] = $subsection['uniqueID'] . $id_c;
						}
						if ( isset( $subsection['minmax'][0]['column_dskp'] ) ) {
							$e['titleColumnDesktop'] = intval( $subsection['minmax'][0]['column_dskp'] );
						}
						if ( isset( $subsection['minmax'][0]['column_mobl'] ) ) {
							$e['titleColumnMobile'] = intval( $subsection['minmax'][0]['column_mobl'] );
						}
						( $subsection['isSlidingScale'] ) ? $e['value1'] = 'sliding' : $e['value1'] = 'bulk';

						$e['subsection_id'] = $id_sub;
						$id_eemin           = $elementC->create( $e );
						foreach ( $subsection['minmax'] as $minmax ) {
							$mi['orden']  = sanitize_text_field( $minmax['No'] );
							$mi['value1'] = sanitize_text_field( $minmax['name'] );
							$mi['value2'] = sanitize_text_field( $minmax['value1'] );
							$mi['value3'] = sanitize_text_field( $minmax['value2'] );
							//?not needed for slider
							if ( isset( $minmax['scc_woo_commerce_product_id'] ) ) {
								$e['woocomerce_product_id'] = intval( $minmax['scc_woo_commerce_product_id'] );
							}
							$mi['element_id'] = $id_eemin;
							$elementitemC->create( $mi );
						}
					}
					foreach ( $subsection['Nooptions'] as  $element ) {
						//!add conditions
						$el['orden'] = intval( $element['element'] );
						switch ( $element['type'] ) {
							case 'selectoption':
								$el['type'] = 'Dropdown Menu';
								( $element['value'][0]['mandatory'] == 'yes' ) ? $el['mandatory'] = '1' : $el['mandatory'] = '0';
								break;
							case 'switchoption':
								$el['type']   = 'checkbox';
								$el['value1'] = sanitize_text_field( $element['value'][0]['value2'] );
								( $element['value'][0]['mandatory'] == 'yes' ) ? $el['mandatory'] = '1' : $el['mandatory'] = '0';
								break;
							case 'comment_option':
								$el['type']   = 'comment box';
								$el['value2'] = sanitize_text_field( $element['value'][0]['value1'] );
								$el['value3'] = sanitize_text_field( $element['value'][0]['value2'] );
								( $element['value'][0]['mandatory'] == 'yes' ) ? $el['mandatory'] = '1' : $el['mandatory'] = '0';
								break;
							case 'number_option':
								$el['type']   = 'quantity box';
								$el['value2'] = sanitize_text_field( $element['value'][0]['value1'] );
								$el['value1'] = sanitize_text_field( $element['value'][0]['inputBoxVariant'] );
								break;
							case 'fileupload_option':
								$el['type']   = 'file upload';
								$el['value2'] = sanitize_text_field( $element['value'][0]['value2'][0]['fileUploadPlaceholderText'] );
								$el['value3'] = sanitize_text_field( $element['value'][0]['value2'][0]['fileUploadAllowedTypes'] );
								$el['value4'] = sanitize_text_field( $element['value'][0]['value1'] );
								break;
							case 'custom_code':
								$el['type']   = 'texthtml';
								$el['value2'] = wp_kses( $element['value'][0]['name'], SCC_ALLOWTAGS );
								break;
							case 'scc_custom_math':
								$el['type']              = 'custom math';
								$el['value1']            = sanitize_text_field( $element['value'][0]['name'] );
								$el['value2']            = sanitize_text_field( $element['value'][0]['value2'] );
								$el['displayFrontend']   = '1';
								$el['displayDetailList'] = '1';
								break;
							case "'scc_custom_math'":
								$el['type']              = 'custom math';
								$el['value1']            = sanitize_text_field( $element['value'][0]['name'] );
								$el['value2']            = sanitize_text_field( $element['value'][0]['value2'] );
								$el['displayFrontend']   = '1';
								$el['displayDetailList'] = '1';
								break;
						}
						if ( isset( $element['uniqueId'] ) ) {
							$el['uniqueId'] = sanitize_text_field( $element['uniqueId'] ) . $id_c;
						}
						$el['orden']        = intval( $element['element'] );
						$el['titleElement'] = wp_kses( $element['value'][0]['title'], SCC_ALLOWTAGS );
						if ( isset( $element['value'][0]['column_dskp'] ) ) {
							$el['titleColumnDesktop'] = sanitize_text_field( $element['value'][0]['column_dskp'] );
						}
						if ( isset( $element['value'][0]['column_mobl'] ) ) {
							$el['titleColumnMobile'] = sanitize_text_field( $element['value'][0]['column_mobl'] );
						}
						$el['subsection_id'] = $id_sub;
						$ell_id              = $elementC->create( $el );
						foreach ( $element['value'] as $items ) {
							if ( $element['type'] === 'selectoption' ) {
								if ( isset( $items['uniqueId'] ) ) {
									$eli['uniqueId'] = sanitize_text_field( $items['uniqueId'] ) . $id_c;
								}
								$eli['order']       = intval( $items['No'] );
								$eli['name']        = sanitize_text_field( $items['name'] );
								$eli['price']       = sanitize_text_field( $items['value1'] );
								$eli['description'] = sanitize_text_field( $items['value2'] );
								( $items['opt_default'] == 'true' ) ? $eli['opt_default'] = '1' : $eli['opt_default'] = '0';
								if ( isset( $items['dropdownLogo'] ) ) {
									$eli['value1'] = urldecode( $items['dropdownLogo'] );
								}
								$eli['element_id'] = $ell_id;
								$elementitemC->create( $eli );
							}
							if ( $element['type'] === 'switchoption' ) {
								if ( isset( $items['uniqueId'] ) ) {
									$eli['uniqueId'] = sanitize_text_field( $items['uniqueId'] ) . $id_c;
								}
								$eli['order'] = intval( $items['No'] );
								$eli['name']  = sanitize_text_field( $items['name'] );
								$eli['price'] = sanitize_text_field( $items['value1'] );
								( $items['opt_default'] == 'true' ) ? $eli['opt_default'] = '1' : $eli['opt_default'] = '0';
								$eli['element_id']                                        = $ell_id;
								$elementitemC->create( $eli );
							}
						}
					}
				}
			}
			//!Recently done, must be tested
			if ( isset( $json_data['scc_form_parameters']['parameters']['conditionObject'] ) ) {
				$conditonObj = json_decode( stripslashes( stripslashes( $json_data['scc_form_parameters']['parameters']['conditionObject'] ) ), true );
				$conditonals = array();
				foreach ( $conditonObj as $key => $con ) {
					$result = $elementC->getByUniqueId( $key . $id_c );
					foreach ( $con[0] as $key22 => $ccc ) {
						$cond = array();
						//insert in element
						$cond['element_id'] = $result->id;
						$cond['op']         = sanitize_text_field( $ccc['op'] );
						if ( $cond['op'] == 'gt' ) {
							$cond['op'] = 'gr';
						}
						if ( $ccc['val'] != 'unset' ) {
							$cond['value'] = sanitize_text_field( $ccc['val'] );
						}
						if ( $ccc['val'] == 'true' ) {
							$cond['value'] = 'chec';
						}
						if ( $ccc['val'] == 'false' ) {
							$cond['value'] = 'unc';
						}
						$resulte = $elementC->getByUniqueId( $key22 . $id_c );
						if ( $resulte ) {
							$cond['condition_element_id'] = $resulte->id;
							//if dropdown search with order and insert in elementitem :/
							if ( $resulte->type == 'Dropdown Menu' ) {
								$rrr = $elementitemC->readOfElement( $resulte->id );
								unset( $cond['value'] );
								$pppp = '';
								foreach ( $rrr as $ii ) {
									if ( $ii->order == intval( $ccc['val'] ) - 1 ) {
										$pppp = $ii->id;
									}
								}
								if ( $pppp != '' ) {
									$cond['elementitem_id'] = $pppp;
								}
							}
						}
						$resultei = $elementitemC->getByUniqueId( $key22 . $id_c );
						if ( $resultei ) {
							$cond['elementitem_id'] = $resultei->id;
						}
						array_push( $conditonals, $cond );
					}
				}
				foreach ( $conditonals as $i ) {
					$conditionalC->create( (array) $i );
				}
			}
			if ( $quote ) {
				foreach ( $json_data['scc_form']['quotes'] as $q ) {
					$q['calc_id'] = $id_c;
					$quoteC->create( (array) $q );
				}
			}
			return true;
		} else {
			return false;
		}
	}
	function scc_globalSettings() {
		check_ajax_referer( 'global-settings-page', 'nonce' );
		$currency = sanitize_text_field( $_GET['currency'] );
		update_option( 'df_scc_currency', $currency );
		$format = sanitize_text_field( $_GET['format'] );
		update_option( 'df_scc_currency_style', $format );
		$mode_convertion = sanitize_text_field( $_GET['mode'] );
		update_option( 'df_scc_currency_coversion_mode', 'off' );

		echo json_encode( array( 'passed' => true ) );
		die();
	}
	function scc_duplicateElement() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/conditionController.php';
		$e          = new elementController();
		$ei         = new elementitemController();
		$con        = new conditionController();
		$id_element = intval( $_GET['id_element'] );
		$el         = $e->read( $id_element );
		if ( $el->type == 'slider' ) {
			wp_send_json(
				array(
					'passed' => false,
					'error'  => 'You can only have one slider per subsection',
				)
			);
			return;
		}
		$iserted = $e->create( (array) $el );
		$items   = $ei->readOfElement( $id_element );
		$condts  = $con->readOfElement( $id_element );
		$idsEl   = array();
		foreach ( $items as $i ) {
			$i->element_id = $iserted;
			$idItemsResult = $ei->create( (array) $i );
			array_push( $idsEl, $idItemsResult );
		}
		$idCon = array();
		foreach ( $condts as $c ) {
			$c->element_id = $iserted;
			$idContiResult = $con->create( (array) $c );
			array_push( $idCon, $idContiResult );
		}
		wp_send_json(
			array(
				'passed' => true,
				'id'     => $iserted,
				'ids'    => $idsEl,
				'ids_c'  => $idCon,
			)
		);
		die();
	}
	function scc_calculator_op() {
		require_once dirname( __FILE__ ) . '/admin/controllers/formController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/sectionController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/subsectionController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
		$formC        = new formController();
		$sectionC     = new sectionController();
		$subsectionC  = new subsectionController();
		$elementC     = new elementController();
		$elementitemC = new elementitemController();
		switch ( sanitize_text_field( $_GET['op'] ) ) {
			case 'del':
				check_ajax_referer( 'all-calculators-page', 'nonce' );
				$id       = intval( $_GET['id_form'] );
				$response = $formC->delete( $id );
				if ( $response ) {
					echo json_encode( array( 'passed' => true ) );
				} else {
					echo json_encode( array( 'passed' => false ) );
				}
				break;
			case 'add':
				check_ajax_referer( 'add-calculator-page', 'nonce' );
				$form['formname']    = sanitize_text_field( $_GET['calculator_name'] );
				$response            = $formC->create( $form );
				$sec['form_id']      = $response;
				$secid               = $sectionC->create( $sec );
				$sub['section_id']   = $secid;
				$subid               = $subsectionC->create( $sub );
				$el['subsection_id'] = $subid;
				$el['type']          = 'Dropdown Menu';
				$el['titleElement']  = 'Dropdown';
				$elid                = $elementC->create( $el );
				$eli['name']         = 'Name';
				$eli['description']  = 'Description';
				$eli['price']        = '10';
				$eli['element_id']   = $elid;
				$elementitemC->create( $eli );
				echo json_encode(
					array(
						'passed' => true,
						'data'   => $response,
					)
				);
				break;
		}
		die();
	}
	function ssc_loadExample() {
		check_ajax_referer( 'add-calculator-page', 'nonce' );
		$data_example = intval( $_GET['el'] );
		$json1        = json_decode( file_get_contents( dirname( __FILE__ ) . '/assets/templates/' . $data_example . '.json' ), true );
		function scc_insert_db_( $json ) {
			require_once dirname( __FILE__ ) . '/admin/controllers/formController.php';
			require_once dirname( __FILE__ ) . '/admin/controllers/sectionController.php';
			require_once dirname( __FILE__ ) . '/admin/controllers/subsectionController.php';
			require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
			require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
			$sectionC     = new sectionController();
			$subsectionC  = new subsectionController();
			$elementC     = new elementController();
			$elementitemC = new elementitemController();
			$formC        = new formController();
			//calculator name and settings
			$f['formname']           = sanitize_text_field( $json['name'] );
			$f['showTaxBeforeTotal'] = 'false';
			$f['turnoffTax']         = 'false';
			$f['turnoffborder']      = 'true';
			$f['turnviewdetails']    = 'false';
			$f['turnoffcoupon']      = 'true';
			$f['removeTotal']        = 'false';
			$f['removeTitle']        = 'false';
			$f['turnoffUnit']        = 'false';
			$f['turnoffSave']        = 'false';
			$f['turnoffTax']         = 'false';
			$f['turnoffemailquote']  = 'true';
			$f['titleFontType']      = sanitize_text_field( $json['settings']['titleFontType'] );
			$f['titleColorPicker']   = sanitize_text_field( $json['settings']['titleColorPicker'] );
			$f['titleFontSize']      = sanitize_text_field( $json['settings']['titleServicepricefontsize'] );
			$f['fontType']           = sanitize_text_field( $json['settings']['fontType'] );
			$f['ServiceColorPicker'] = sanitize_text_field( $json['settings']['colorPicker'] );
			$f['ServiceFontSize']    = sanitize_text_field( $json['settings']['servicepricefontsize'] );
			$f['objectColorPicker']  = sanitize_text_field( $json['settings']['objectColorPicker'] );
			$f['inheritFontType']    = 'false';
			$id_c                    = $formC->create( $f );
			//sections
			foreach ( $json['content'] as $key => $section ) {
				$s['name']        = sanitize_text_field( $section['name'] );
				$s['description'] = wp_kses( $section['desc'], SCC_ALLOWTAGS );
				$s['order']       = intval( $section['section'] );
				$s['form_id']     = $id_c;
				if ( isset( $section['accordion'] ) ) {
					$s['accordion'] = sanitize_text_field( $section['accordion'] );
				}
				$id_sec = $sectionC->create( $s );
				//subsections
				foreach ( $section['value'] as $key => $sub ) {
					$sb['order']      = intval( $sub['subsection'] );
					$sb['section_id'] = $id_sec;
					$sb_id            = $subsectionC->create( $sb );
					//elements
					foreach ( $sub['Nooptions'] as $element ) {
						$ell['order'] = intval( $element['element'] );
						$ell['type']  = sanitize_text_field( $element['type'] );
						if ( $element['type'] == 'custom math' || $element['type'] == 'file upload' || $element['type'] == 'texthtml' ) {
							continue;
						}
						if ( $element['type'] == 'checkbox' && $element['value1'] == '8' ) {
							continue;
						}
						$ell['subsection_id'] = $sb_id;
						if ( isset( $element['title'] ) ) {
							$ell['titleElement'] = wp_kses( $element['title'], SCC_ALLOWTAGS );
						}
						if ( isset( $element['value1'] ) ) {
							$ell['value1'] = sanitize_text_field( $element['value1'] );
						}
						if ( isset( $element['value2'] ) ) {
							$ell['value2'] = sanitize_text_field( $element['value2'] );
						}
						if ( isset( $element['value3'] ) ) {
							$ell['value3'] = sanitize_text_field( $element['value3'] );
						}
						if ( isset( $element['value4'] ) ) {
							$ell['value4'] = sanitize_text_field( $element['value4'] );
						}
						if ( isset( $element['mandatory'] ) ) {
							$ell['mandatory'] = sanitize_text_field( $element['mandatory'] );
						}
						if ( isset( $element['displayFrontend'] ) ) {
							$ell['displayf'] = sanitize_text_field( $element['displayFrontend'] );
						}
						$id_el = $elementC->create( $ell );
						foreach ( $element['value'] as $key => $e ) {
							//check if is element only or if its elementitem
							//element
							$ellit['order']      = intval( $e['No'] );
							$ellit['element_id'] = $id_el;
							if ( isset( $e['name'] ) ) {
								$ellit['name'] = sanitize_text_field( $e['name'] );
							}
							if ( isset( $e['price'] ) ) {
								$ellit['price'] = sanitize_text_field( $e['price'] );
							}
							if ( isset( $e['description'] ) ) {
								$ellit['description'] = sanitize_text_field( $e['description'] );
							}
							if ( isset( $e['value1'] ) ) {
								$ellit['value1'] = sanitize_text_field( $e['value1'] );
							}
							if ( isset( $e['value2'] ) ) {
								$ellit['value2'] = sanitize_text_field( $e['value2'] );
							}
							if ( isset( $e['value3'] ) ) {
								$ellit['value3'] = sanitize_text_field( $e['value3'] );
							}
							if ( isset( $e['value4'] ) ) {
								$ellit['value4'] = sanitize_text_field( $e['value4'] );
							}
							if ( isset( $e['opt_default'] ) ) {
								$ellit['opt_default'] = sanitize_text_field( $e['opt_default'] );
							}
							$elementitemC->create( $ellit );
						}
					}
				}
			}
			return $id_c;
		}
		$i = scc_insert_db_( $json1 );
		wp_send_json(
			array(
				'passed' => true,
				'data'   => $i,
			)
		);
		die();
	}
	function scc_addCheckboxItems()
    {
        check_ajax_referer('edit-calculator-page', 'nonce');
        require_once dirname(__FILE__) . '/admin/controllers/elementitemController.php';
        require_once dirname(__FILE__) . '/admin/models/editElementModel.php';
        $elementitemC = new elementitemController();
        $eit["name"] = "Name";
        $eit["price"] = "10";
        $eit["element_id"] = intval($_GET["element_id"]);
        $element_id = $elementitemC->create($eit);
        $load_woocommerce_products = esc_attr($_GET['enableWoocommerce']) === "true";
        $count = intval($_GET['count']);
        $is_image_checkbox = esc_attr($_GET['is_image_checkbox']) === "true";
        $edit_page_func = new Stylish_Cost_Calculator_Edit_Page(0, true, $load_woocommerce_products);
        $element_item_html = $edit_page_func->checkbox_setup_checkbox_item( $count, array_merge( $eit, array('id' => $element_id) ), $is_image_checkbox );
        echo ($element_id) ? json_encode(["msj" => "The element was created", "passed" => true, "id_element" => $element_id, "DOMhtml" => $element_item_html]) : json_encode(["msj" => "There was an error, please try again", "passed" => false]);
        die();
    }
	function scc_addElementFileUpload()
    {
        check_ajax_referer('edit-calculator-page', 'nonce');
        require_once dirname(__FILE__) . '/admin/controllers/elementController.php';
        require_once dirname(__FILE__) . '/admin/models/editElementModel.php';
        $edit_page_func = new Stylish_Cost_Calculator_Edit_Page(0, true);
        $elementC = new elementController();
        $el["orden"] = intval($_GET["order"]);
        $el["type"] = "file upload";
        $el["subsection_id"] = intval($_GET["id_sub"]);
        $html = $edit_page_func->renderAdvancedOptions((object) $el);
        $element_id = $elementC->create($el);
        $eli["value1"] = "1";
         $eli["value2"] = "Please choose a file";
         $eli["value3"] = "png,pdf,jpeg,jpg";
         $eli["element_id"] = intval($element_id);
         $body_html = $edit_page_func->renderFileUploadSetupBody2((object) array_merge($eli, $el, array("elementItem_id" => $elementItem_id)), array(1 => array()));
        echo ($element_id)  ? json_encode(["msj" => "The element was created", "passed" => true, "id_element" => $element_id, "DOMhtml" => [ "advanced_settings" => $html, 'fileupload_body' => $body_html ] ]) : json_encode(["msj" => "There was an error, please try again", "passed" => false]);
        die();
    
    }
	function scc_addElementCheckbox()
    {
        check_ajax_referer('edit-calculator-page', 'nonce');
        require_once dirname(__FILE__) . '/admin/controllers/elementController.php';
        require_once dirname(__FILE__) . '/admin/controllers/elementitemController.php';
        require_once dirname(__FILE__) . '/admin/models/editElementModel.php';
        $edit_page_func = new Stylish_Cost_Calculator_Edit_Page(0, true);
        $elementC = new elementController();
        $elementitemC = new elementitemController();
        $el["orden"] = intval($_GET["order"]);
        $el["value1"] = sanitize_text_field($_GET["type"]);
        $el["type"] = "checkbox";
        $el["subsection_id"] = intval($_GET["id_sub"]);
        $el['titleElement']  = "New element";
        $html = $edit_page_func->renderAdvancedOptions((object) $el);
        $element_id = $elementC->create($el);
        $elit["order"] = "0";
        $elit["name"] = "Name";
        $elit["price"] = "10";
        $elit["element_id"] = intval($element_id);
        $elementItem_id = $elementitemC->create($elit);
        // TODO add type of checkbox on ajax response
        $type = "";
        $elit["id"] = $elementItem_id;
        $body_html = $edit_page_func->renderCheckboxSetupBody((object) array_merge($el, [ "elementitems" => [ 0 => (object) $elit ] ]), array(1 => array()));
        echo ($element_id) ? json_encode(["msj" => "The element was created", "passed" => true, "id_element" => $element_id, "id_element_item" => $elementItem_id, "type" => $type, "DOMhtml" => [ "advanced_settings" => $html, 'checkbox_body' => $body_html ]]) : json_encode(["msj" => "There was an error, please try again", "passed" => false]);
        die();
    }
	function scc_addElementCommentBox() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
		require_once dirname( __FILE__ ) . '/admin/models/editElementModel.php';
		$edit_page_func      = new Stylish_Cost_Calculator_Edit_Page(0, true);
		$elementC            = new elementController();
		$el['orden']         = intval( $_GET['order'] );
		$el['type']          = 'comment box';
		$el['subsection_id'] = intval( $_GET['id_sub'] );
		$html                = $edit_page_func->renderAdvancedOptions( (object) $el );
		$element_id          = $elementC->create( $el );
		$eli["value1"] = "1";
        $eli["value2"] = "10";
        $eli["value3"] = "2";
        $eli["element_id"] = intval($element_id);
        $body_html = $edit_page_func->renderCommentBoxSetupBody2((object) array_merge($eli, $el, array("elementItem_id" => $elementItem_id)), array(1 => array()));
        $element_id ? wp_send_json(["msj" => "The element was created", "passed" => true, "id_element" => $element_id, "DOMhtml" => [ "advanced_settings" => $html, 'commentbox_body' => $body_html ]]) : wp_send_json(["msj" => "There was an error, please try again", "passed" => false]);
		die();
	}
	function scc_addElementQuantityBox() {
         check_ajax_referer('edit-calculator-page', 'nonce');
         require_once dirname(__FILE__) . '/admin/controllers/elementController.php';
         require_once dirname(__FILE__) . '/admin/models/editElementModel.php';
         $edit_page_func = new Stylish_Cost_Calculator_Edit_Page(0, true);
         $elementC = new elementController();
         $el["orden"] = intval($_GET["order"]);
         $el["type"] = "quantity box";
         $el["value1"] = "default";
         $el["value2"] = "0";
         $el["subsection_id"] = intval($_GET["id_sub"]);
         $html = $edit_page_func->renderAdvancedOptions((object) $el);
         $element_id = $elementC->create($el);
         $eli["value1"] = "1";
         $eli["value2"] = "10";
         $eli["value3"] = "2";
         $eli["element_id"] = intval($element_id);
         $body_html = $edit_page_func->renderQuantityBoxSetupBody2((object) array_merge($eli, $el, array("elementItem_id" => $elementItem_id)), array(1 => array()));
         echo ($element_id) ? json_encode(["msj" => "The element was created", "passed" => true, "id_element" => $element_id, "DOMhtml" => [ "advanced_settings" => $html, 'quantitybox_body' => $body_html ]]) : json_encode(["msj" => "There was an error, please try again", "passed" => false]);
         die();
    }
	function scc_addElementTextHtml() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
		require_once dirname( __FILE__ ) . '/admin/models/editElementModel.php';
		$edit_page_func      = new Stylish_Cost_Calculator_Edit_Page(0, true);
		$elementC            = new elementController();
		$el['orden']         = intval( $_GET['order'] );
		$el['type']          = 'texthtml';
		$el['subsection_id'] = intval( $_GET['id_sub'] );
		$html                = $edit_page_func->renderAdvancedOptions( (object) $el );
		$element_id          = $elementC->create( $el );
		echo ( $element_id ) ? json_encode(
			array(
				'msj'        => 'The element was created',
				'passed'     => true,
				'id_element' => $element_id,
				'DOMhtml'    => array( 'advanced_settings' => $html ),
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error, please try again',
				'passed' => false,
			)
		);
		die();
	}
	function scc_addElementSlider() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
		require_once dirname( __FILE__ ) . '/admin/models/editElementModel.php';
		$edit_page_func      = new Stylish_Cost_Calculator_Edit_Page(0, true);
		$elementC            = new elementController();
		$elementitemC        = new elementitemController();
		$el['orden']         = intval( $_GET['order'] );
		$el['type']          = 'slider';
		$el['value2']        = '1';
		$el['value3']        = '1';
		$el['subsection_id'] = intval( $_GET['id_sub'] );
		$html                = $edit_page_func->renderAdvancedOptions( (object) $el );
		$elmts               = $elementC->getBySubsection( intval( $_GET['id_sub'] ) );
		foreach ( $elmts as $e ) {
			if ( $e->type == 'slider' ) {
				echo json_encode(
					array(
						'passed' => false,
						'msj'    => 'slider already',
					)
				);
				die();
			}
		}
		$element_id        = $elementC->create( $el );
		$eli['value1']     = '1';
		$eli['value2']     = '10';
		$eli['value3']     = '2';
		$eli['element_id'] = $element_id;
		$elementItem_id    = $elementitemC->create( $eli );
		$body_html = $edit_page_func->renderSliderSetupBody2((object) array_merge($eli, $el, array("elementItem_id" => $elementItem_id)), array(1 => array()));
		echo ( $element_id ) ? json_encode(
			array(
				'msj'            => 'The element was created',
				'passed'         => true,
				'id_element'     => $element_id,
				'id_elementitem' => $elementItem_id,
				'DOMhtml' => [ 'slider_body' => $body_html ]
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error, please try again',
				'passed' => false,
			)
		);
		die();
	}
	// ADD ONE SECTION
	function scc_saveSection() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/sectionController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/subsectionController.php';
		$sectionC         = new sectionController();
		$subsectionC      = new subsectionController();
		$s['form_id']     = intval( $_GET['id_form'] );
		$s['order']       = intval( $_GET['order'] );
		$section_id       = $sectionC->create( $s );
		$sb['section_id'] = $section_id;
		$subsection_id    = $subsectionC->create( $sb );
		if ( $section_id && $subsection_id ) {
			echo json_encode(
				array(
					'msj'           => 'The section was created',
					'passed'        => true,
					'id_section'    => $section_id,
					'id_subsection' => $subsection_id,
				)
			);
		} else {
			echo json_encode(
				array(
					'msj'    => 'There was an error, please try again',
					'passed' => false,
				)
			);
		}
		die();
	}
	// DELETES ONE SECTION
	function scc_delSection() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/sectionController.php';
		$sectionC = new sectionController();
		$id       = intval( $_GET['id_section'] );
		$request  = $sectionC->delete( $id );
		echo ( $request ) ? json_encode(
			array(
				'msj'    => 'the section was deleted',
				'passed' => true,
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error',
				'passed' => false,
			)
		);
		die();
	}
	function scc_upSection() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/sectionController.php';
		$sectionC = new sectionController();
		$s['id']  = intval( $_GET['id_section'] );
		if ( isset( $_GET['accordion'] ) ) {
			$s['accordion'] = sanitize_text_field( $_GET['accordion'] );
		}
		if ( isset( $_GET['showTotal'] ) ) {
			$s['showSectionTotal'] = sanitize_text_field( $_GET['showTotal'] );
		}
		if ( isset( $_GET['title'] ) ) {
			$s['name'] = sanitize_text_field( $_GET['title'] );
		}
		if ( isset( $_GET['description'] ) ) {
			$s['description'] = wp_kses( $_GET['description'], SCC_ALLOWTAGS );
		}
		if ( isset( $_GET['showSectionTotalOnPdf'] ) ) {
			$s['showSectionTotalOnPdf'] = sanitize_text_field( $_GET['showSectionTotalOnPdf'] );
		}
		$request = $sectionC->update( $s );
		echo ( $request ) ? json_encode(
			array(
				'msj'    => 'the section was updated',
				'passed' => true,
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error',
				'passed' => false,
			)
		);
		die();
	}
	function scc_delSubsection() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/subsectionController.php';
		$subsectionC = new subsectionController();
		$id          = intval( $_GET['id_subsection'] );
		$request     = $subsectionC->delete( $id );
		echo ( $request ) ? json_encode(
			array(
				'msj'    => 'the subsection was deleted',
				'passed' => true,
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error',
				'passed' => false,
			)
		);
		die();
	}
	function scc_addSubsection() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/subsectionController.php';
		$subsectionC      = new subsectionController();
		$sb['order']      = intval( $_GET['order'] );
		$sb['section_id'] = intval( $_GET['section_id'] );
		$subsection_id    = $subsectionC->create( $sb );
		echo ( $subsection_id ) ? json_encode(
			array(
				'msj'           => 'The subsection was created',
				'passed'        => true,
				'id_subsection' => $subsection_id,
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error, please try again',
				'passed' => false,
			)
		);

		die();
	}
	function scc_delElementItem() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
		$elementitemC   = new elementitemController();
		$id_elementitem = intval( $_GET['element_id'] );
		$request        = $elementitemC->delete( $id_elementitem );
		echo ( $request ) ? json_encode(
			array(
				'msj'    => 'The element was deleted',
				'passed' => true,
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error',
				'passed' => false,
			)
		);
		die();
	}
	function scc_addElementSwichoption() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
		require_once dirname(__FILE__) . '/admin/models/editElementModel.php';
        $elementitemC = new elementitemController();
        $load_woocommerce_products = esc_attr($_GET['enableWoocommerce']) === "true";
        $edit_page_func = new Stylish_Cost_Calculator_Edit_Page(0, true, $load_woocommerce_products);
		$eli['order']       = '0';
		$eli['name']        = 'Name of product';
		$eli['price']       = '10';
		$eli['description'] = 'Description example';
		$eli['element_id']  = intval( $_GET['element_id'] );
		$items_count = intval($_GET["itemCount"]) - 1;
		$element_id         = $elementitemC->create( $eli );
		$element_item_html = $edit_page_func->element_setup_part_dropdown_item_beta( $items_count, array_merge( $eli, array('id' => $element_id) ) );
		echo ( $element_id ) ? json_encode(
			array(
				'msj'        => 'The element was created',
				'passed'     => true,
				'element_id' => $element_id,
				"html" => $element_item_html
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error, please try again',
				'passed' => false,
			)
		);
		die();
	}
	function scc_upElement() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
		$elementC = new elementController();
		$el['id'] = intval( $_GET['id_element'] );
		if ( isset( $_GET['title'] ) ) {
			$el['titleElement'] = wp_kses( $_GET['title'], SCC_ALLOWTAGS );
		}
		if ( isset( $_GET['typecheckbox'] ) ) {
			$el['value1'] = sanitize_text_field( $_GET['typecheckbox'] );
		}
		if ( isset( $_GET['value2'] ) ) {
			$el['value2'] = ( isset( $_GET['tt'] ) && $_GET['tt'] == 'texthtml' ) ? wp_kses( $_GET['value2'], SCC_ALLOWTAGS ) : sanitize_text_field( $_GET['value2'] );
		}
		if ( isset( $_GET['value3'] ) ) {
			$el['value3'] = sanitize_text_field( $_GET['value3'] );
		}
		if ( isset( $_GET['value4'] ) ) {
			$el['value4'] = sanitize_text_field( $_GET['value4'] );
		}
		if ( isset( $_GET['mandatory'] ) ) {
			$el['mandatory'] = sanitize_text_field( $_GET['mandatory'] );
		}
		if ( isset( $_GET['desktop'] ) ) {
			$el['titleColumnDesktop'] = sanitize_text_field( $_GET['desktop'] );
		}
		if ( isset( $_GET['mobile'] ) ) {
			$el['titleColumnMobile'] = sanitize_text_field( $_GET['mobile'] );
		}
		if ( isset( $_GET['pricehint'] ) ) {
			$el['showPriceHint'] = sanitize_text_field( $_GET['pricehint'] );
		}
		if ( isset( $_GET['displayFront'] ) ) {
			$el['displayFrontend'] = sanitize_text_field( $_GET['displayFront'] );
		}
		if ( isset( $_GET['displayDetail'] ) ) {
			$el['displayDetailList'] = sanitize_text_field( $_GET['displayDetail'] );
		}
		if ( isset( $_GET['order'] ) ) {
			$el['orden'] = intval( $_GET['order'] );
		}
		if ( isset( $_GET['subsection'] ) ) {
			$el['subsection_id'] = sanitize_text_field( $_GET['subsection'] );
		}
		if ( isset( $_GET['showTitlePdf'] ) ) {
			$el['showTitlePdf'] = sanitize_text_field( $_GET['showTitlePdf'] );
		}
		if ( isset( $_GET['element_woocomerce_product_id'] ) ) {
			$el['element_woocomerce_product_id'] = intval( $_GET['element_woocomerce_product_id'] );
		}
		if (isset($_GET["showInputBoxSlider"])) $el["showInputBoxSlider"] = sanitize_text_field($_GET["showInputBoxSlider"]);


		$request = $elementC->update( $el );
		echo json_encode( array( 'passed' => true ) );
		die();
	}
	function scc_upElementItemSwichoption() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
		$elementitemC = new elementitemController();
		$eli['id']    = intval( $_GET['id_elementitem'] );
		if ( isset( $_GET['name'] ) ) {
			$eli['name'] = sanitize_text_field( $_GET['name'] );
		}
		if ( isset( $_GET['description'] ) ) {
			$eli['description'] = sanitize_text_field( $_GET['description'] );
		}
		if ( isset( $_GET['price'] ) ) {
			$eli['price'] = sanitize_text_field( $_GET['price'] );
		}
		if ( isset( $_GET['image'] ) ) {
			$eli['value1'] = esc_url_raw( $_GET['image'] );
		}
		if ( isset( $_GET['default'] ) ) {
			$eli['opt_default'] = sanitize_text_field( $_GET['default'] );
		}
		if ( isset( $_GET['woocomerce_product_id'] ) ) {
			$eli['woocomerce_product_id'] = intval( $_GET['woocomerce_product_id'] );
		}
		$request = $elementitemC->update( $eli );
		//set to 0 opt_default to rest of elementitem of the same element
		if ( isset( $_GET['id_element'] ) && $_GET['default'] == 1 ) {
			$eli2 = $elementitemC->readOfElement( intval( $_GET['id_element'] ) );
			foreach ( $eli2 as $e ) {
				if ( $e->id != intval( $_GET['id_elementitem'] ) ) {
					$ee['opt_default'] = 0;
					$ee['id']          = $e->id;
					$elementitemC->update( $ee );
				}
			}
		}
		( $request ) ? wp_send_json(
			array(
				'msj'    => 'The element has changed',
				'passed' => true,
			)
		) : wp_send_json(
			array(
				'msj'    => 'There was an error, please try again',
				'passed' => false,
			)
		);
		die();
	}
	function scc_addsElementDropdownMenu() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
		require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
		require_once dirname( __FILE__ ) . '/admin/models/editElementModel.php';
		$edit_page_func      = new Stylish_Cost_Calculator_Edit_Page(0, true);
		$elementC            = new elementController();
		$elementitemC        = new elementitemController();
		$el['orden']         = intval( $_GET['order'] );
		$el['titleElement']  = 'Title';
		$el['type']          = 'Dropdown Menu';
		$el['subsection_id'] = intval( $_GET['id_sub'] );
		$html                = $edit_page_func->renderAdvancedOptions( (object) $el );
		$element_id          = $elementC->create( $el );
		$eli['order']        = '0';
		$eli['name']         = 'Name';
		$eli['price']        = '10';
		$eli['description']  = 'Description';
		$eli['element_id']   = $element_id;
		$elementItem_id      = $elementitemC->create( $eli );
		$body_html = $edit_page_func->renderDropdownSetupBody((object) array_merge($eli, $el, array("elementItem_id" => $elementItem_id)), array(1 => array()));
		echo ( $element_id ) ? json_encode(
			array(
				'msj'             => 'The element was created',
				'passed'          => true,
				'id_element'      => $element_id,
				'id_element_item' => $elementItem_id,
				'DOMhtml'         => array( 'advanced_settings' => $html, 'slider_body' => $body_html ),
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error, please try again',
				'passed' => false,
			)
		);
		die();
	}
	function scc_upElementItemSlider() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
		$elementitemC = new elementitemController();
		$eli['id']    = intval( $_GET['id_element'] );
		if ( isset( $_GET['value1'] ) ) {
			$eli['value1'] = sanitize_text_field( $_GET['value1'] );
		}
		if ( isset( $_GET['value2'] ) ) {
			$eli['value2'] = sanitize_text_field( $_GET['value2'] );
		}
		if ( isset( $_GET['value3'] ) ) {
			$eli['value3'] = sanitize_text_field( $_GET['value3'] );
		}
		$request = $elementitemC->update( $eli );
		echo ( $request ) ? json_encode(
			array(
				'msj'    => 'The title has changed',
				'passed' => true,
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error, please try again',
				'passed' => false,
			)
		);
		die();
	}
	function scc_addElementItemSlider() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementitemController.php';
		$elementitemC      = new elementitemController();
		$previous_row_maxvalue = sanitize_text_field($_GET["value1"]);
        $eli["value1"] = $previous_row_maxvalue;
        $eli["value2"] = $previous_row_maxvalue + 1;
		$eli['value3']     = '2';
		$eli['element_id'] = intval( $_GET['id_element'] );
		$elementitem_id    = $elementitemC->create( $eli );
		echo ( $elementitem_id ) ? json_encode(
			array(
				'msj'            => 'The element was created',
				'passed'         => true,
				'id_elementitem' => $elementitem_id
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error, please try again',
				'passed' => false,
			)
		);
		die();
	}
	function scc_delElement() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/elementController.php';
		$el         = new elementController();
		$id_element = intval( $_GET['element_id'] );
		$request    = $el->delete( $id_element );
		echo ( $request ) ? json_encode(
			array(
				'msj'    => 'The element was deleted',
				'passed' => true,
			)
		) : json_encode(
			array(
				'msj'    => 'There was an error',
				'passed' => false,
			)
		);
		die();
	}
	// CHANGES NAME OF FORM
	function scc_saveFormNameSettings() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require_once dirname( __FILE__ ) . '/admin/controllers/formController.php';
		$formC                  = new formController();
		$f['id']                = intval( $_POST['id_form'] );
		$f['formname']          = sanitize_text_field( $_POST['data']['formname'] );
		$f['elementSkin']       = sanitize_text_field( $_POST['data']['elementSkin'] );
		$f['addContainer']      = sanitize_text_field( $_POST['data']['addContainer'] );
		$f['buttonStyle']       = sanitize_text_field( $_POST['data']['buttonStyle'] );
		$f['turnoffemailquote'] = sanitize_text_field( $_POST['data']['turnoffemailquote'] );
		$f['turnviewdetails']   = sanitize_text_field( $_POST['data']['turnviewdetails'] );
		$f['turnoffcoupon']     = sanitize_text_field( $_POST['data']['turnoffcoupon'] );
		$f['barstyle']          = sanitize_text_field( $_POST['data']['barstyle'] );
		$f['turnofffloating']   = sanitize_text_field( $_POST['data']['turnofffloating'] );
		$f['removeTitle']       = sanitize_text_field( $_POST['data']['removeTitle'] );
		$f['turnoffUnit']       = sanitize_text_field( $_POST['data']['turnoffUnit'] );
		$f["turnoffQty"] = sanitize_text_field($_POST["data"]["turnoffQty"]);
		$f['turnoffSave']       = sanitize_text_field( $_POST['data']['turnoffSave'] );
		$f['turnoffTax']        = sanitize_text_field( $_POST['data']['turnoffTax'] );
		$f['symbol']            = sanitize_text_field( $_POST['data']['symbol'] );
		$f['removeCurrency']    = sanitize_text_field( $_POST['data']['removeCurrency'] );
		$f['userCompletes']     = sanitize_text_field( $_POST['data']['userCompletes'] );
		$f['userClicksf']       = sanitize_text_field( $_POST['data']['userClicksf'] );
		$f['translation']       = sanitize_text_field( $_POST['translations'] );
		$f['wrapper_max_width'] = absint( $_POST['data']['calcWrapperMaxWidth'] );
		$request                = $formC->update( $f );
		echo ( $request ) ? json_encode( array( 'passed' => true ) ) : json_encode(
			array(
				'msj'    => 'There was an error',
				'passed' => false,
			)
		);
		die();
	}
	function scc_previewOneForm() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		echo do_shortcode( "[scc_calculator type=' text' idvalue='" . intval( $_GET['id_form'] ) . "' ]" );
		die();
	}
	/**
	 * @return array
	 */
	function custom_mails( $args ) {
		$sender    = get_option( 'df_scc_emailsender', get_option( 'admin_email' ) );
		$sender    = empty( $sender ) ? get_option( 'admin_email' ) : $sender;
		$bcc_email = sanitize_email( $sender );
		if ( is_array( $args['headers'] ) ) {
			$args['headers'][] = 'Bcc: ' . $bcc_email;
		} else {
			$args['headers'] .= 'Bcc: ' . $bcc_email . "\r\n";
		}
		return $args;
	}
	/**
	 * @return string
	 */
	function new_mail_from() {
		$sender = get_option( 'df_scc_emailsender' );
		$sender = empty( $sender ) ? 'wordpress@' . parse_url( get_site_url() )['host'] : sanitize_email( $sender );
		return $sender;
	}
	/**
	 * Save image URL to options
	 */
	public function pdf_logo_save_uploaded_image( $data ) {
		update_option( sanitize_text_field( $data['name'] ), sanitize_text_field( $data['value'] ) );
		wp_send_json_success(
			array(
				'url' => sanitize_text_field( $data['value'] ),
			)
		);
	}
	/**
	 * Manage admin ajax functionality
	 */

	function scc_updateSectionOrder() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		require dirname( __FILE__ ) . '/admin/controllers/sectionController.php';
		$sectionC  = new sectionController();
		$sections2 = sanitize_text_or_array_field( $_GET['sections'] );
		foreach ( $sections2 as $s ) {
			$e['id']    = intval( $s['id'] );
			$e['order'] = intval( $s['order'] );
			$sectionC->update( $e );
		}
		wp_send_json( array( 'passed' => true ) );
	}
	function sccPDFSettings() {
		check_ajax_referer( 'global-settings-page', 'nonce' );
		$pdf_font    = sanitize_text_field( stripslashes( $_POST['pdfSettings']['sccPDFFont'] ) );
		$pdf_datefmt = sanitize_text_field( stripslashes( $_POST['pdfSettings']['sccPDFDateFmt'] ) );
		// echo $pdf_font;
		update_option( 'sccPDFFont', $pdf_font );
		update_option( 'scc_pdf_datefmt', $pdf_datefmt );
	}
	function sccUpdateUrlStats() {
		require dirname( __FILE__ ) . '/admin/controllers/urlStatsController.php';
		$url    = sanitize_text_field( $_POST['url'] );
		$calcId = intval( $_POST['calcId'] );
		check_ajax_referer( 'calculator-front-page' . $calcId, 'nonce' );
		$stats  = new urlStatsController( $calcId, $url );
		$result = $stats->update( $url );
		if ( $result ) {
			wp_send_json_success( array( 'ok' => 'ok' ) );
		} else {
			wp_send_json_error( array( 'error' => 'something didn\'t work' ) );
		}
	}
	function sccFeedbackManage() {
		check_ajax_referer( 'edit-calculator-page', 'nonce' );
		$args = isset( $_POST['btn-type'] ) ? sanitize_text_field( $_POST['btn-type'] ) : false;
		$data = null;
		if ( $args ) {
			$data = scc_feedback_invokation( $args );
		}
		wp_send_json(
			array(
				'ok' => $data,
			)
		);
	}
	function get_debug_items()
    {
        check_ajax_referer('edit-calculator-page', 'nonce');
        require dirname( __FILE__ ) . '/admin/views/diagnostic.php';
        $existing_ignore_list = get_option( 'scc-diag-dissmissed', array() );
        if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'set_ignore') {
            $ignored_param = sanitize_text_field( $_REQUEST['value'] );
            array_push( $existing_ignore_list, $ignored_param );
            $existing_ignore_list = array_unique($existing_ignore_list);
            update_option( 'scc-diag-dissmissed', $existing_ignore_list );
        }
        $diag_page = new Stylish_Cost_Calculator_Diagnostic();
        $res = $diag_page->diagnostic_page(true);
        wp_send_json(array( "diag_items" => $res, "exclusions" => $existing_ignore_list));
    }
}
new ajaxRequest();
