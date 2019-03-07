<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

if ( ! class_exists( CW_Exchange_Poloniex_BCH::class ) ) {
	/**
	 * Poloniex Exchange Rates Class
	 *
	 * @category CryptoWoo
	 * @package Exchange
	 * @subpackage ExchangeBase
	 * @author Name: Olav Småriset, Company Name: We Program IT
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
}
