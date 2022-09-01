<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/** Utility function to set default property value for multi dimensional arrays
 * https://mekshq.com/recursive-wp-parse-args-wordpress-function/
 * @param $a - to be parsed array
 * @param $b - default array
 */
if ( ! function_exists( 'meks_wp_parse_args' ) ) {
	function meks_wp_parse_args( &$a, $b ) {
		$a      = (array) $a;
		$b      = (array) $b;
		$result = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $result[ $k ] ) ) {
				$result[ $k ] = meks_wp_parse_args( $v, $result[ $k ] );
			} else {
				$result[ $k ] = $v;
			}
		}
		return $result;
	}
}

if ( ! function_exists( 'scc_feedback_invokation' ) ) {
	/**
	* Sets feedback modal invokation to compare against 'scc_save_count' option
	*
	* @return integer
	*/
	function scc_feedback_invokation( $args ) {
		$save_count         = get_option( 'df-scc-save-count' );
		$current_invokation = get_option( 'df-scc_feedback_invoke' );
		$invoke_at          = 0;
		switch ( $args ) {
			case 'skip':
				$invoke_at = $save_count + 5;
				update_option( 'df-scc_feedback_invoke', $invoke_at );
				break;
			case 'yes':
				update_option( 'df-scc_feedback_invoke', 'disabled' );
				break;
			case 'no':
				update_option( 'df-scc_feedback_invoke', 'disabled' );
				break;
			default:
				if ( $current_invokation && $current_invokation != 'disabled' ) {
					$invoke_at = $current_invokation;
				} elseif ( $current_invokation == 'disabled' ) {
					$invoke_at = 0;
				} else {
					$invoke_at = 9;
				};
				break;
		}
		return (int) $invoke_at;
	}
}


/**
 * Recursive sanitation for text or array
 * from https://wordpress.stackexchange.com/questions/24736/wordpress-sanitize-array
 *
 * @param $array_or_string (array|string)
 * @since  0.1
 * @return mixed
 */
if ( ! function_exists( 'sanitize_text_or_array_field' ) ) {
	function sanitize_text_or_array_field( $array_or_string ) {
		if ( is_string( $array_or_string ) ) {
			$array_or_string = sanitize_text_field( $array_or_string );
		} elseif ( is_array( $array_or_string ) ) {
			foreach ( $array_or_string as $key => &$value ) {
				if ( is_array( $value ) ) {
					$value = sanitize_text_or_array_field( $value );
				} else {
					$value = sanitize_text_field( $value );
				}
			}
		}

		return $array_or_string;
	}
}

if ( ! function_exists( 'df_scc_get_currency_symbol_by_currency_code' ) ) {
	function df_scc_get_currency_symbol_by_currency_code ($currency)
	{
			$currencySymbolLabel = '$';
			switch ($currency) {
				case 'ANG':
				$currencySymbolLabel = 'ƒ';
				break;
				case 'Bs':
				$currencySymbolLabel = 'Bs';
				break;
				case 'ILS':
				$currencySymbolLabel = '₪';
				break;
				case 'INR':
				$currencySymbolLabel = '₹';
				break;
				case 'UAH':
				$currencySymbolLabel = '₴';
				break;
				case 'UGX':
				$currencySymbolLabel = 'USh';
				break;
				case 'USD':
				$currencySymbolLabel = '$';
				break;
				case 'CAD':
				$currencySymbolLabel = '$';
				break;
				case 'MNT':
				$currencySymbolLabel = '₮';
				break;
				case 'EUR':
				$currencySymbolLabel = '€';
				break;
				case 'JPY':
				$currencySymbolLabel = '¥';
				break;
				case 'KES':
				$currencySymbolLabel = '/=';
				break;
				case 'NOK':
				$currencySymbolLabel = 'kr';
				break;
				case 'RUB':
				$currencySymbolLabel = '₽';
				break;
				case 'TRY':
				$currencySymbolLabel = '₺';
				break;
				case 'CHF':
				$currencySymbolLabel = 'Fr.';
				break;
				case 'SEK':
				$currencySymbolLabel = 'kr';
				break;
				case 'BRL':
				$currencySymbolLabel = 'R$';
				break;
				case 'CNY':
				$currencySymbolLabel = '¥';
				break;
				case 'AUD':
				$currencySymbolLabel = 'A$';
				break;
				case 'DKK':
				$currencySymbolLabel = 'Kr.';
				break;
				case 'HKD':
				$currencySymbolLabel = 'HK$';
				break;
				case 'GBP':
				$currencySymbolLabel = '£';
				break;
				case 'RON':
				$currencySymbolLabel = 'RON';
				break;
				case 'NGN':
				$currencySymbol = '₦';
				break;
				case 'NZD':
				$currencySymbolLabel = 'NZ$';
				break;
				case 'ZAR':
				$currencySymbolLabel = 'R';
				break;
				case 'ZMW':
				$currencySymbolLabel = 'K';
				break;
				case '‎PKR':
				$currencySymbolLabel = 'Rs';
				break;
				case 'KRW':
				$currencySymbolLabel = '₩';
				break;
				case 'CFA':
				$currencySymbolLabel = 'FCFA';
				break;
				case 'PLN':
				$currencySymbolLabel = 'zł';
				break;
				case 'IDR':
				$currencySymbolLabel = 'Rp';
				break;
				default:
					$currencySymbolLabel = $currency;
			}
			return $currencySymbolLabel;
	}
}

