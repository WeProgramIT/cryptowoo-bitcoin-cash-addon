<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Binance Exchange Rates Class
 *
 * @category CryptoWoo
 * @package Exchange
 * @subpackage ExchangeBase
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */
class CW_Exchange_Binance_BCH extends CW_Exchange_Binance {
	/** Get the exchange rate pair (base/currency)
	 *
	 * @return string
	 */
	protected function get_search_pair() : string {
		return str_replace( 'BCH', 'BCHABC', parent::get_search_pair() );
	}
}
