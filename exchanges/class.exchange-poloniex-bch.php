<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Poloniex Exchange Rates Class
 *
 * @category CryptoWoo
 * @package Exchange
 * @subpackage ExchangeBase
 * Author: We Program IT | legal company name: OS IT Programming AS | Company org nr: NO 921 074 077
 * Author URI: https://weprogram.it
 */
class CW_Exchange_Poloniex_BCH extends CW_Exchange_Poloniex {
	/** Get the exchange rate pair (base/currency)
	 *
	 * @return string
	 */
	protected function get_search_pair() : string {
		return str_replace( 'BCH', 'BCHABC', parent::get_search_pair() );
	}
}
