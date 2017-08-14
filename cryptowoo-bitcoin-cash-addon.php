<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Plugin Name: CryptoWoo Bitcoin Cash Add-on
 * Plugin URI: https://www.cryptowoo.com/shop/cryptowoo-bitcoin-cash-addon
 * Description: Accept BCC payments in WooCommerce. Requires CryptoWoo main plugin and CryptoWoo HD Wallet Add-on.
 * Version: 0.1.0
 * Author: Olsm|OlavOlsm|Keys4Coins
 * Author URI: https://www.keys4coins.com
 * License: GPLv2
 * Text Domain: cryptowoo-bcc-addon
 * Domain Path: /lang
 *
 */

define( 'CWBCC_VER', '0.1.0' );
define( 'CWBCC_FILE', __FILE__ );

// Load the plugin update library if it is not already loaded
/* ToDo: add license
if ( ! class_exists( 'CWBCC_License_Menu' ) && file_exists( plugin_dir_path( CWBCC_FILE ) . 'am-license-menu.php' ) ) {
	require_once( plugin_dir_path( CWBCC_FILE ) . 'am-license-menu.php' );
	CWBCC_License_Menu::instance( CWBCC_FILE, 'CryptoWoo Bitcoin Cash Addon', CWBCC_VER, 'plugin', 'https://www.cryptowoo.com/' );
}
*/

/**
 * Plugin activation
 */
function cryptowoo_bcc_addon_activate() {

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	$hd_add_on_file = 'cryptowoo-hd-wallet-addon/cryptowoo-hd-wallet-addon.php';
	if ( ! file_exists( WP_PLUGIN_DIR . '/' . $hd_add_on_file ) || ! file_exists( WP_PLUGIN_DIR . '/cryptowoo/cryptowoo.php' ) ) {

		// If WooCommerce is not installed then show installation notice
		add_action( 'admin_notices', 'cryptowoo_bcc_notinstalled_notice' );

		return;
	} elseif ( ! is_plugin_active( $hd_add_on_file ) ) {
		add_action( 'admin_notices', 'cryptowoo_bcc_inactive_notice' );

		return;
	}
}

register_activation_hook( __FILE__, 'cryptowoo_bcc_addon_activate' );
add_action( 'admin_init', 'cryptowoo_bcc_addon_activate' );

/**
 * CryptoWoo inactive notice
 */
function cryptowoo_bcc_inactive_notice() {

	?>
    <div class="error">
        <p><?php _e( '<b>CryptoWoo Bitcoin Cash add-on error!</b><br>It seems like the CryptoWoo HD Wallet add-on has been deactivated.<br>
       				Please go to the Plugins menu and make sure that the CryptoWoo HD Wallet add-on is activated.', 'cryptowoo-bcc-addon' ); ?></p>
    </div>
	<?php
}


/**
 * CryptoWoo HD Wallet add-on not installed notice
 */
function cryptowoo_bcc_notinstalled_notice() {
	$addon_link = '<a href="https://www.cryptowoo.com/shop/cryptowoo-hd-wallet-addon/" target="_blank">CryptoWoo HD Wallet add-on</a>';
	?>
    <div class="error">
        <p><?php printf( __( '<b>CryptoWoo Bitcoin Cash add-on error!</b><br>It seems like the CryptoWoo HD Wallet add-on is not installed.<br>
					The CryptoWoo Bitcoin Cash add-on will only work in combination with the CryptoWoo main plugin and the %s.', 'cryptowoo-bcc-addon' ), $addon_link ); ?></p>
    </div>
	<?php
}

function cwbcc_hd_enabled() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

	return is_plugin_active( 'cryptowoo-hd-wallet-addon/cryptowoo-hd-wallet-addon.php' ) && is_plugin_active( 'cryptowoo/cryptowoo.php' );
}

if ( cwbcc_hd_enabled() ) {
	// Coin symbol and name
	add_filter( 'woocommerce_currencies', 'cwbcc_woocommerce_currencies', 10, 1 );
	add_filter( 'cw_get_currency_symbol', 'cwbcc_get_currency_symbol', 10, 2 );
	add_filter( 'cw_get_enabled_currencies', 'cwbcc_add_coin_identifier', 10, 1 );

	// BIP32 prefixes
	add_filter( 'address_prefixes', 'cwbcc_address_prefixes', 10, 1 );

	// Custom block explorer URL
	add_filter( 'cw_link_to_address', 'cwbcc_link_to_address', 10, 4 );

	// Options page validations
	add_filter( 'validate_custom_api_genesis', 'cwbcc_validate_custom_api_genesis', 10, 2 );
	add_filter( 'validate_custom_api_currency', 'cwbcc_validate_custom_api_currency', 10, 2 );
	add_filter( 'cryptowoo_is_ready', 'cwbcc_cryptowoo_is_ready', 10, 3 );
	add_filter( 'cw_get_shifty_coins', 'cwbcc_cw_get_shifty_coins', 10, 1 );
	add_filter( 'cw_misconfig_notice', 'cwbcc_cryptowoo_misconfig_notice', 10, 2 );

	// HD wallet management
	add_filter( 'index_key_ids', 'cwbcc_index_key_ids', 10, 1 );
	add_filter( 'mpk_key_ids', 'cwbcc_mpk_key_ids', 10, 1 );
	add_filter( 'get_mpk_data_mpk_key', 'cwbcc_get_mpk_data_mpk_key', 10, 3 );
	add_filter( 'get_mpk_data_network', 'cwbcc_get_mpk_data_network', 10, 3 );
	//ToDo: add_filter( 'cw_blockcypher_currencies', 'cwbcc_add_currency_to_array', 10, 1 );
	add_filter( 'cw_discovery_notice', 'cwbcc_add_currency_to_array', 10, 1 );
	add_filter( 'cw_discovery_notice_action', 'cwbcc_add_currency_to_array', 10, 1 );

	// Currency params
	add_filter( 'cw_get_currency_params', 'cwbcc_get_currency_params', 10, 2 );
	add_filter( 'cw_sort_unpaid_addresses', 'cwbcc_sort_unpaid_addresses', 10, 2 );
	add_filter( 'cw_prioritize_unpaid_addresses', 'cwbcc_prioritize_unpaid_addresses', 10, 2);

	// Add discovery button to currency option
	//add_filter( "redux/options/cryptowoo_payments/field/cryptowoo_bcc_mpk", 'hd_wallet_discovery_button' );
	add_filter( "redux/options/cryptowoo_payments/field/cryptowoo_bcc_mpk", 'hd_wallet_discovery_button' );
	add_filter( 'cw_cron_update_altcoin_fiat_rates', 'cwbcc_cw_update_exchange_data', 10, 2 );

	// Catch failing processing API (only if processing_fallback is enabled)
	add_filter( 'cw_get_tx_api_config', 'cwbcc_cw_get_tx_api_config', 10, 3 );

	// Insight API URL
	add_filter( 'cw_prepare_insight_api', 'cwbcc_override_insight_url', 10, 4 );

	// Wallet config
	add_filter( 'wallet_config', 'cwbcc_wallet_config', 10, 3 );
	add_filter( 'cw_get_processing_config', 'cwbcc_processing_config', 10, 3 );

	// Options page
	add_action( 'plugins_loaded', 'cwbcc_add_fields', 10 );


}

/**
 * Bitcoin Cash font color for aw-cryptocoins
 * see cryptowoo/assets/fonts/aw-cryptocoins/cryptocoins-colors.css
 */
function cwbcc_coin_icon_color( ) { ?>
	<style type="text/css">
		i.cc.BCC:before, i.cc.BCC-alt:before {
			content: "\e9a6";
		}
		i.cc.BCC, i.cc.BCC-alt {
			color: #F7931A;
		}
	</style>
<?php }
add_action('wp_head', 'cwbcc_coin_icon_color');

/**
 * Processing API configuration error
 *
 * @param $enabled
 * @param $options
 *
 * @return mixed
 */
function cwbcc_cryptowoo_misconfig_notice( $enabled, $options ) {
	$enabled['BCC'] = $options['processing_api_bcc'] === 'disabled' && ( (bool) CW_Validate::check_if_unset( 'cryptowoo_bcc_mpk', $options ) || (bool) CW_Validate::check_if_unset( 'cryptowoo_bcc_mpk', $options ) );

	return $enabled;
}

/**
 * Add currency name
 *
 * @param $currencies
 *
 * @return mixed
 */
function cwbcc_woocommerce_currencies( $currencies ) {
	$currencies['BCC'] = __( 'Bitcoin Cash', 'cryptowoo' );

	return $currencies;
}


/**
 * Add currency symbol
 *
 * @param $currency_symbol
 * @param $currency
 *
 * @return string
 */
function cwbcc_get_currency_symbol( $currency_symbol, $currency ) {
	return $currency === 'BCC' ? 'BCC' : $currency_symbol;
}


/**
 * Add coin identifier
 *
 * @param $coin_identifiers
 *
 * @return array
 */
function cwbcc_add_coin_identifier( $coin_identifiers ) {
	$coin_identifiers['BCC'] = 'bcc';

	return $coin_identifiers;
}


/**
 * Add address prefix
 *
 * @param $prefixes
 *
 * @return array
 */
function cwbcc_address_prefixes( $prefixes ) {
	$prefixes['BCC'] = '00';
	$prefixes['BCC_MULTISIG'] = '05';

	return $prefixes;
}


/**
 * Add wallet config
 *
 * @param $wallet_config
 * @param $currency
 * @param $options
 *
 * @return array
 */
function cwbcc_wallet_config( $wallet_config, $currency, $options ) {
	if ( $currency === 'BCC' ) {
		$wallet_config                       = array(
			'coin_client'   => 'bitcoincash',
			'request_coin'  => 'BCC',
			'multiplier'    => (float) $options['multiplier_bcc'],
			'safe_address'  => false,
			'decimals'      => 8,
			'mpk_key'       => ! CW_Validate::check_if_unset( 'cryptowoo_bcc_mpk', $options, false ) ? 'cryptowoo_bcc_mpk' : 'cryptowoo_bcc_mpk',
			'fwd_addr_key'  => 'safe_bcc_address',
			'threshold_key' => 'forwarding_threshold_bcc'
		);
		$wallet_config['hdwallet']           = CW_Validate::check_if_unset( $wallet_config['mpk_key'], $options, false );
		$wallet_config['coin_protocols'][]   = 'bcc';
		$wallet_config['forwarding_enabled'] = false;
	}

	return $wallet_config;
}

/**
 * Add InstantSend and "raw" zeroconf settings to processing config
 *
 * @param $pc_conf
 * @param $currency
 * @param $options
 *
 * @return array
 */
function cwbcc_processing_config( $pc_conf, $currency, $options ) {
	if ( $currency === 'BCC' ) {
		$pc_conf['instant_send']       = isset( $options['bcc_instant_send'] ) ? (bool) $options['bcc_instant_send'] : false;
		$pc_conf['instant_send_depth'] = 5; // TODO Maybe add option

		// Maybe accept "raw" zeroconf
		$pc_conf['min_confidence'] = isset( $options['cryptowoo_bcc_min_conf'] ) && (int) $options['cryptowoo_bcc_min_conf'] === 0 && isset( $options['bcc_raw_zeroconf'] ) && (bool) $options['bcc_raw_zeroconf'] ? 0 : 1;
	}

	return $pc_conf;
}


/**
 * Override links to payment addresses
 *
 * @param $url
 * @param $address
 * @param $currency
 * @param $options
 *
 * @return string
 */
function cwbcc_link_to_address( $url, $address, $currency, $options ) {
	if ( $currency === 'BCC' ) {
		$url = "http://blockdozer.com/insight/address/{$address}";
		if ( $options['preferred_block_explorer_bcc'] === 'custom' && isset( $options['custom_block_explorer_bcc'] ) ) {
			$url = preg_replace( '/{{ADDRESS}}/', $address, $options['custom_block_explorer_bcc'] );
			if ( ! wp_http_validate_url( $url ) ) {
				$url = '#';
			}
		}
	}

	return $url;
}


/**
 * Override genesis block
 *
 * @param $genesis
 * @param $field_id
 *
 * @return string
 */
function cwbcc_validate_custom_api_genesis( $genesis, $field_id ) {
	if ( in_array( $field_id, array( 'custom_api_bcc', 'processing_fallback_url_bcc' ) ) ) {
        $genesis = '000000000019d6689c085ae165831e934ff763ae46a2a6c172b3f1b60a8ce26f';
        //$genesis  = '00000000839a8e6886ab5951d76f411475428afc90947ee320161bbf18eb6048'; // 1
	}

	return $genesis;
}


/**
 * Override custom API currency
 *
 * @param $currency
 * @param $field_id
 *
 * @return string
 */
function cwbcc_validate_custom_api_currency( $currency, $field_id ) {
	if ( in_array( $field_id, array( 'custom_api_bcc', 'processing_fallback_url_bcc' ) ) ) {
		$currency = 'BCC';
	}

	return $currency;
}


/**
 * Add currency to cryptowoo_is_ready
 *
 * @param $enabled
 * @param $options
 * @param $changed_values
 *
 * @return array
 */
function cwbcc_cryptowoo_is_ready( $enabled, $options, $changed_values ) {
	$enabled['BCC']           = (bool) CW_Validate::check_if_unset( 'cryptowoo_bcc_mpk', $options, false ) ?: (bool) CW_Validate::check_if_unset( 'cryptowoo_bcc_mpk', $options, false );
	$enabled['BCC_transient'] = (bool) CW_Validate::check_if_unset( 'cryptowoo_bcc_mpk', $changed_values, false ) ?: (bool) CW_Validate::check_if_unset( 'cryptowoo_bcc_mpk', $changed_values, false );

	return $enabled;
}


/**
 * Add currency to is_cryptostore check
 *
 * @param $cryptostore
 * @param $woocommerce_currency
 *
 * @return bool
 */
function cwbcc_is_cryptostore( $cryptostore, $woocommerce_currency ) {
	return (bool) $cryptostore ?: $woocommerce_currency === 'BCC';
}

add_filter( 'is_cryptostore', 'cwbcc_is_cryptostore', 10, 2 );

/**
 * Add currency to Shifty button option field
 *
 * @param $select
 *
 * @return array
 */
function cwbcc_cw_get_shifty_coins( $select ) {
	$select['BCC'] = sprintf( __( 'Display only on %s payment pages', 'cryptowoo' ), 'Bitcoin Cash' );

	return $select;
}


/**
 * Add HD index key id for currency
 *
 * @param $index_key_ids
 *
 * @return array
 */
function cwbcc_index_key_ids( $index_key_ids ) {
	$index_key_ids['BCC'] = 'cryptowoo_bcc_index';

	return $index_key_ids;
}


/**
 * Add HD mpk key id for currency
 *
 * @param $mpk_key_ids
 *
 * @return array
 */
function cwbcc_mpk_key_ids( $mpk_key_ids ) {
	$mpk_key_ids['BCC'] = 'cryptowoo_bcc_mpk';
	$mpk_key_ids['BCC_E'] = 'cryptowoo_bcc_mpk';

	return $mpk_key_ids;
}


/**
 * Override mpk_key
 *
 * @param $mpk_key
 * @param $currency
 * @param $options
 *
 * @return string
 */
function cwbcc_get_mpk_data_mpk_key( $mpk_key, $currency, $options ) {
	if ( $currency === 'BCC' ) {
		if ( isset( $options['cryptowoo_bcc_mpk'] ) && $options['cryptowoo_bcc_mpk'] !== '' ) {
			$mpk_key = "cryptowoo_bcc_mpk";
		} else {
			$mpk_key = "cryptowoo_bcc_mpk";
		}
	}

	return $mpk_key;
}


/**
 * Override mpk_data->network
 *
 * @param $mpk_data
 * @param $currency
 * @param $options
 *
 * @return object
 */
function cwbcc_get_mpk_data_network( $mpk_data, $currency, $options ) {
	if ( $currency === 'BCC' ) {
		$mpk_data->network = BitWasp\Bitcoin\Network\NetworkFactory::bitcoin();
	}

	return $mpk_data;
}


/**
 * Add currency to background exchange rate update
 *
 * @param $data
 * @param $options
 *
 * @return array
 */
function cwbcc_cw_update_exchange_data($data, $options) {
	$bcc = CW_ExchangeRates::update_altcoin_fiat_rates('BCC', $options);

	// Maybe log exchange rate updates
	if((bool)$options['logging']['rates']) {
		if($bcc['status'] === 'not updated' || strpos($bcc['status'], 'disabled')) {
			$data['bcc'] = strpos($bcc['status'], 'disabled') ? $bcc['status'] : $bcc['last_update'];
		} else {
			$data['bcc'] = $bcc;
		}
	}
	return $data;
}


/*
 * Add currency to currencies array
 *
 * @param $currencies
 *
 * @return array
 **/
function cwbcc_add_currency_to_array( $currencies ) {
	$currencies[] = 'BCC';

	return $currencies;
}



/**
 * Override currency params in xpub validation
 *
 * @param $currency_params
 * @param $field_id
 *
 * @return object
 */
function cwbcc_get_currency_params( $currency_params, $field_id ) {
	if ( strcmp( $field_id, 'cryptowoo_bcc_mpk' ) === 0 ) {
		$currency_params = new stdClass();
		$currency_params->strlen = 111;
        $currency_params->mand_mpk_prefix    = 'xpub';   // bip32.org & Electrum prefix
        $currency_params->mand_base58_prefix = '0488b21e'; // Bitcoin Cash
        $currency_params->currency           = 'BCC';
        $currency_params->index_key          = 'cryptowoo_bcc_index';
	}

	return $currency_params;
}

/**
 * Override sort unpaid addresses to add BCC
 *
 * @param array $unpaid_addresses
 * @param array $unpaid_addresses_raw
 *
 * @return array
 */
function cwbcc_sort_unpaid_addresses($unpaid_addresses, $unpaid_addresses_raw ) {
	$address_batch = array();

	// Order the items according to their currencies' average blocktime
	foreach ($unpaid_addresses_raw as $address) {
		$payment_currency = $address->payment_currency;
		if (strcmp($payment_currency, 'BCC') == 0) {
			$top_n[0]['BCC'][]      = $address;
			$address_batch['BCC'][] = $address->address;
			$batch = array_merge(array('batches' => $address_batch), $top_n[0]);
			$unpaid_addresses = array_merge($unpaid_addresses, $batch);
		}
	}

	return $unpaid_addresses;
}

/**
 * Override prioritize unpaid addresses to add BCC
 *
 * @param array $unpaid_addresses
 * @param array $unpaid_addresses_raw
 * @param int $number
 *
 * @return array
 */
function cwbcc_prioritize_unpaid_addresses($unpaid_addresses, $unpaid_addresses_raw) {
    // Order the items according to their currencies' average blocktime
    foreach ($unpaid_addresses_raw as $address) {
        $payment_currency = $address->payment_currency;
        if (strcmp($payment_currency, 'BCC') == 0) {
            $unpaid_addresses = array_merge($unpaid_addresses, [$address]);
        }
    }

	return $unpaid_addresses;
}

/**
 * Fallback on failing API
 *
 * @param $api_config
 * @param $currency
 *
 * @return array
 */
function cwbcc_cw_get_tx_api_config( $api_config, $currency ) {
    // ToDo: add Blockcypher
	if ( $currency === 'BCC' ) {
		if ( $api_config->tx_update_api === 'blockdozer' ) {
			$api_config->tx_update_api   = 'insight';
			$api_config->skip_this_round = false;
		} else {
			$api_config->tx_update_api   = 'blockdozer';
			$api_config->skip_this_round = false;
		}
	}

	return $api_config;
}

/**
 * Override Insight API URL if no URL is found in the settings
 *
 * @param $insight
 * @param $endpoint
 * @param $currency
 * @param $options
 *
 * @return mixed
 */
function cwbcc_override_insight_url( $insight, $endpoint, $currency, $options ) {
	if ( $currency === 'BCC' && isset( $options['processing_fallback_url_bcc'] ) && wp_http_validate_url( $options['processing_fallback_url_bcc'] ) ) {
		$fallback_url = $options['processing_fallback_url_bcc'];
		$urls         = $endpoint ? CW_Formatting::format_insight_api_url( $fallback_url, $endpoint ) : CW_Formatting::format_insight_api_url( $fallback_url, '' );
		$insight->url = $urls['surl'];
	}

	return $insight;
}

/**
 * Add Redux options
 */
function cwbcc_add_fields() {
	$woocommerce_currency = get_option( 'woocommerce_currency' );

	/*
	 * Required confirmations
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'processing-confirmations',
		'id'         => 'cryptowoo_bcc_min_conf',
		'type'       => 'spinner',
		'title'      => sprintf( __( '%s Minimum Confirmations', 'cryptowoo' ), 'Bitcoin Cash' ),
		'desc'       => sprintf( __( 'Minimum number of confirmations for <strong>%s</strong> transactions - %s Confirmation Threshold', 'cryptowoo' ), 'Bitcoin Cash', 'Bitcoin Cash' ),
		'default'    => 5,
		'min'        => 1,
		'step'       => 1,
		'max'        => 100,
	) );

	// ToDo: Enable raw zeroconf
	/*
	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'processing-confirmations',
		'id'         => 'bcc_raw_zeroconf',
		'type'       => 'switch',
		'title'      => __( 'Bitcoin Cash "Raw" Zeroconf', 'cryptowoo' ),
		'subtitle'   => __( 'Accept unconfirmed Bitcoin Cash transactions as soon as they are seen on the network.', 'cryptowoo' ),
		'desc'       => sprintf( __( '%sThis practice is generally not recommended. Only enable this if you know what you are doing!%s', 'cryptowoo' ), '<strong>', '</strong>' ),
		'default'    => false,
		'required'   => array(
			//array('processing_api_bcc', '=', 'custom'),
			array( 'cryptowoo_bcc_min_conf', '=', 0 )
		),
	) );
	*/


	/*
	 * ToDo: Zeroconf order amount threshold
	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'processing-zeroconf',
		'id'         => 'cryptowoo_max_unconfirmed_bcc',
		'type'       => 'slider',
		'title'      => sprintf( __( '%s zeroconf threshold (%s)', 'cryptowoo' ), 'Bitcoin Cash', $woocommerce_currency ),
		'desc'       => '',
		'required'   => array( 'cryptowoo_bcc_min_conf', '<', 1 ),
		'default'    => 100,
		'min'        => 0,
		'step'       => 10,
		'max'        => 500,
	) );

	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'processing-zeroconf',
		'id'         => 'cryptowoo_bcc_zconf_notice',
		'type'       => 'info',
		'style'      => 'info',
		'notice'     => false,
		'required'   => array( 'cryptowoo_bcc_min_conf', '>', 0 ),
		'icon'       => 'fa fa-info-circle',
		'title'      => sprintf( __( '%s Zeroconf Threshold Disabled', 'cryptowoo' ), 'Bitcoin Cash' ),
		'desc'       => sprintf( __( 'This option is disabled because you do not accept unconfirmed %s payments.', 'cryptowoo' ), 'Bitcoin Cash' ),
	) );
	 */


	/*
	// Remove 3rd party confidence
	Redux::removeField( 'cryptowoo_payments', 'custom_api_confidence', false );

	/*
	 * Confidence warning
	 * /
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'processing-confidence',
			'id'    => 'bcc_confidence_warning',
			'type'  => 'info',
			'title' => __('Be careful!', 'cryptowoo'),
			'style' => 'warning',
			'desc'  => __('Accepting transactions with a low confidence value increases your exposure to double-spend attacks. Only proceed if you don\'t automatically deliver your products and know what you\'re doing.', 'cryptowoo'),
			'required' => array('min_confidence_bcc', '<', 95)
	));

	/*
	 * Transaction confidence
	 * /

	Redux::setField( 'cryptowoo_payments', array(
			'section_id'        => 'processing-confidence',
			'id'      => 'min_confidence_bcc',
			'type'    => 'switch',
			'title'   => sprintf(__('%s transaction confidence (%s)', 'cryptowoo'), 'Bitcoin Cash', '%'),
			//'desc'    => '',
			'required' => array('cryptowoo_bcc_min_conf', '<', 1),

	));


	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'processing-confidence',
		'id'      => 'min_confidence_bcc_notice',
		'type'    => 'info',
		'style' => 'info',
		'notice'    => false,
		'required' => array('cryptowoo_bcc_min_conf', '>', 0),
		'icon'  => 'fa fa-info-circle',
		'title'   => sprintf(__('%s "Raw" Zeroconf Disabled', 'cryptowoo'), 'Bitcoin Cash'),
		'desc'    => sprintf(__('This option is disabled because you do not accept unconfirmed %s payments.', 'cryptowoo'), 'Bitcoin Cash'),
	));

	// Re-add 3rd party confidence
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'processing-confidence',
		'id'       => 'custom_api_confidence',
		'type'     => 'switch',
		'title'    => __('Third Party Confidence Metrics', 'cryptowoo'),
		'subtitle' => __('Enable this to use the chain.so confidence metrics when accepting zeroconf transactions with your custom Bitcoin, Litecoin, or Dogecoin API.', 'cryptowoo'),
		'default'  => false,
	));
    */

	// Remove blockcypher token field
	Redux::removeField( 'cryptowoo_payments', 'blockcypher_token', false );

	/*
	 * Processing API
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'processing-api',
		'id'                => 'processing_api_bcc',
		'type'              => 'select',
		'title'             => sprintf( __( '%s Processing API', 'cryptowoo' ), 'Bitcoin Cash' ),
		'subtitle'          => sprintf( __( 'Choose the API provider you want to use to look up %s payments.', 'cryptowoo' ), 'Bitcoin Cash' ),
		'options'           => array(
			'blockdozer' => 'Blockdozer.com',
			'custom'      => __( 'Custom (no testnet)', 'cryptowoo' ),
			'disabled'    => __( 'Disabled', 'cryptowoo' ),
		),
		'desc'              => '',
		'default'           => 'disabled',
		'ajax_save'         => false, // Force page load when this changes
		'validate_callback' => 'redux_validate_processing_api',
		'select2'           => array( 'allowClear' => false ),
	) );

	/*
	 * Processing API custom URL warning
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'processing-api',
		'id'         => 'processing_api_bcc_info',
		'type'       => 'info',
		'style'      => 'critical',
		'icon'       => 'el el-warning-sign',
		'required'   => array(
			array( 'processing_api_bcc', 'equals', 'custom' ),
			array( 'custom_api_bcc', 'equals', '' ),
		),
		'desc'       => sprintf( __( 'Please enter a valid URL in the field below to use a custom %s processing API', 'cryptowoo' ), 'Bitcoin Cash' ),
	) );

	/*
	 * Custom processing API URL
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'processing-api',
		'id'                => 'custom_api_bcc',
		'type'              => 'text',
		'title'             => sprintf( __( '%s Insight API URL', 'cryptowoo' ), 'Bitcoin Cash' ),
		'subtitle'          => sprintf( __( 'Connect to any %sInsight API%s instance.', 'cryptowoo' ), '<a href="https://github.com/bitpay/insight-api/" title="Insight API" target="_blank">', '</a>' ),
		'desc'              => sprintf( __( 'The root URL of the API instance:%sLink to address:%shttp://blockdozer.com/insight-api/txs?address=%sRoot URL: %shttp://blockdozer.com/insight-api/%s', 'cryptowoo-bcc-addon' ), '<p>', '<code>', '</code><br>', '<code>', '</code></p>' ),
		'placeholder'       => 'http://blockdozer.com/insight-api/',
		'required'          => array( 'processing_api_bcc', 'equals', 'custom' ),
		'validate_callback' => 'redux_validate_custom_api',
		'ajax_save'         => false,
		'msg'               => __( 'Invalid BCC Insight API URL', 'cryptowoo' ),
		'default'           => '',
		'text_hint'         => array(
			'title'   => 'Please Note:',
			'content' => __( 'Make sure the root URL of the API has a trailing slash ( / ).', 'cryptowoo' ),
		)
	) );

	// Re-add blockcypher token field
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'processing-api',
		'id'                => 'blockcypher_token',
		'type'              => 'text',
		'ajax_save'         => false, // Force page load when this changes
		'desc'              => sprintf( __( '%sMore info%s', 'cryptowoo' ), '<a href="http://dev.blockcypher.com/#rate-limits-and-tokens" title="BlockCypher Docs: Rate limits and tokens" target="_blank">', '</a>' ),
		'title'             => __( 'BlockCypher Token (optional)', 'cryptowoo' ),
		'subtitle'          => sprintf( __( 'Use the API token from your %sBlockCypher%s account.', 'cryptowoo' ), '<strong><a href="https://accounts.blockcypher.com/" title="BlockCypher account bccboard" target="_blank">', '</a></strong>' ),
		'validate_callback' => 'redux_validate_token'
	) );

	// API Resource control information
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'processing-api-resources',
		'id'                => 'processing_fallback_url_bcc',
		'type'              => 'text',
		'title'             => sprintf( __( 'Blockdozer Bitcoin Cash API Fallback', 'cryptowoo' ), 'Bitcoin Cash' ),
		'subtitle'          => sprintf( __( 'Fallback to any %sInsight API%s instance in case the Blockdozer API fails. Retry Blockdozer upon beginning of the next hour. Leave empty to disable.', 'cryptowoo' ), '<a href="https://github.com/bitpay/insight-api/" title="Insight API" target="_blank">', '</a>' ),
		'desc'              => sprintf( __( 'The root URL of the API instance:%sLink to address:%shttp://blockdozer.com/insight-api/txs?address=XtuVUju4Baaj7YXShQu4QbLLR7X2aw9Gc8%sRoot URL: %shttp://blockdozer.com/insight-api/%s', 'cryptowoo-bcc-addon' ), '<p>', '<code>', '</code><br>', '<code>', '</code></p>' ),
		'placeholder'       => 'http://blockdozer.com/insight-api/',
		'required'          => array( 'processing_api_bcc', 'equals', 'blockcypher' ),
		'validate_callback' => 'redux_validate_custom_api',
		'ajax_save'         => false,
		'msg'               => __( 'Invalid BCC Insight API URL', 'cryptowoo' ),
		'default'           => 'http://blockdozer.com/insight-api/',
		'text_hint'         => array(
			'title'   => 'Please Note:',
			'content' => __( 'Make sure the root URL of the API has a trailing slash ( / ).', 'cryptowoo' ),
		)
	) );
	/*
	 * Preferred exchange rate provider
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'rates-exchange',
		'id'                => 'preferred_exchange_bcc',
		'type'              => 'select',
		'title'             => 'Bitcoin Cash Exchange (BCC/BTC)',
		'subtitle'          => sprintf( __( 'Choose the exchange you prefer to use to calculate the %sBitcoin Cash to Bitcoin exchange rate%s', 'cryptowoo' ), '<strong>', '</strong>.' ),
		'desc'              => sprintf( __( 'Cross-calculated via BTC/%s', 'cryptowoo' ), $woocommerce_currency ),
		'options'           => array(
			'bittrex'    => 'Bittrex',
			'poloniex'    => 'Poloniex',
			'shapeshift' => 'ShapeShift'
		),
		'default'           => 'poloniex',
		'ajax_save'         => false, // Force page load when this changes
		'validate_callback' => 'redux_validate_exchange_api',
		'select2'           => array( 'allowClear' => false )
	) );

	/*
	 * Exchange rate multiplier
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'    => 'rates-multiplier',
		'id'            => 'multiplier_bcc',
		'type'          => 'slider',
		'title'         => sprintf( __( '%s exchange rate multiplier', 'cryptowoo' ), 'Bitcoin Cash' ),
		'subtitle'      => sprintf( __( 'Extra multiplier to apply when calculating %s prices.', 'cryptowoo' ), 'Bitcoin Cash' ),
		'desc'          => '',
		'default'       => 1,
		'min'           => .01,
		'step'          => .01,
		'max'           => 2,
		'resolution'    => 0.01,
		'validate'      => 'comma_numeric',
		'display_value' => 'text'
	) );

	/*
	 * Preferred blockexplorer
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'rewriting',
		'id'         => 'preferred_block_explorer_bcc',
		'type'       => 'select',
		'title'      => sprintf( __( '%s Block Explorer', 'cryptowoo' ), 'Bitcoin Cash' ),
		'subtitle'   => sprintf( __( 'Choose the block explorer you want to use for links to the %s blockchain.', 'cryptowoo' ), 'Bitcoin Cash' ),
		'desc'       => '',
		'options'    => array(
			'autoselect'        => __( 'Autoselect by processing API', 'cryptowoo' ),
			'blockdozer' => 'blockdozer.com',
			'custom'            => __( 'Custom (enter URL below)' ),
		),
		'default'    => 'blockdozer',
		'select2'    => array( 'allowClear' => false )
	) );

	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'rewriting',
		'id'         => 'preferred_block_explorer_bcc_info',
		'type'       => 'info',
		'style'      => 'critical',
		'icon'       => 'el el-warning-sign',
		'required'   => array(
			array( 'preferred_block_explorer_bcc', '=', 'custom' ),
			array( 'custom_block_explorer_bcc', '=', '' ),
		),
		'desc'       => sprintf( __( 'Please enter a valid URL in the field below to use a custom %s block explorer', 'cryptowoo' ), 'Bitcoin Cash' ),
	) );
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'rewriting',
		'id'                => 'custom_block_explorer_bcc',
		'type'              => 'text',
		'title'             => sprintf( __( 'Custom %s Block Explorer URL', 'cryptowoo' ), 'Bitcoin Cash' ),
		'subtitle'          => __( 'Link to a block explorer of your choice.', 'cryptowoo' ),
		'desc'              => sprintf( __( 'The URL to the page that displays the information for a single address.%sPlease add %s{{ADDRESS}}%s as placeholder for the cryptocurrency address in the URL.%s', 'cryptowoo' ), '<br><strong>', '<code>', '</code>', '</strong>' ),
		'placeholder'       => 'http://blockdozer.com/insight-api/txs?address={$address}',
		'required'          => array( 'preferred_block_explorer_bcc', '=', 'custom' ),
		'validate_callback' => 'redux_validate_custom_blockexplorer',
		'ajax_save'         => false,
		'msg'               => __( 'Invalid custom block explorer URL', 'cryptowoo' ),
		'default'           => '',
	) );

	/*
	 * Currency Switcher plugin decimals
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'rewriting-switcher',
		'id'         => 'decimals_BCC',
		'type'       => 'select',
		'title'      => sprintf( __( '%s amount decimals', 'cryptowoo' ), 'Bitcoin Cash' ),
		'subtitle'   => '',
		'desc'       => __( 'This option overrides the decimals option of the WooCommerce Currency Switcher plugin.', 'cryptowoo' ),
		'required'   => array( 'add_currencies_to_woocs', '=', true ),
		'options'    => array(
			2 => '2',
			4 => '4',
			6 => '6',
			8 => '8'
		),
		'default'    => 4,
		'select2'    => array( 'allowClear' => false )
	) );


	// Remove Bitcoin testnet
	Redux::removeSection( 'cryptowoo_payments', 'wallets-hdwallet-testnet', false );

	/*
	 * HD wallet section start
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'wallets-hdwallet',
		'id'         => 'wallets-hdwallet-bcc',
		'type'       => 'section',
		'title'      => __( 'Bitcoin Cash', 'cryptowoo-hd-wallet-addon' ),
		//'required' => array('testmode_enabled','equals','0'),
		'icon'       => 'cc-BCC',
		//'subtitle' => __('Use the field with the correct prefix of your Litecoin MPK. The prefix depends on the wallet client you used to generate the key.', 'cryptowoo-hd-wallet-addon'),
		'indent'     => true,
	) );

	/*
	 * Extended public key
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'wallets-hdwallet',
		'id'                => 'cryptowoo_bcc_mpk',
		'type'              => 'text',
		'ajax_save'         => false,
		'username'          => false,
		'title'             => sprintf( __( '%sprefix%s', 'cryptowoo-hd-wallet-addon' ), '<b>BCC "xpub..." ', '</b>' ),
		'desc'              => sprintf(__('Remove this key to use the %s prefix format.', 'cryptowoo-hd-wallet-addon'), 'drkv'),
		'validate_callback' => 'redux_validate_mpk',
		//'required' => array('cryptowoo_bcc_mpk', 'equals', ''),
		'placeholder'       => 'xpub...',
		// xpub format
		'text_hint'         => array(
			'title'   => 'Please Note:',
			'content' => sprintf( __( 'If you enter a used key you will have to run the address discovery process after saving this setting.%sUse a dedicated HD wallet (or at least a dedicated xpub) for your store payments to prevent address reuse.', 'cryptowoo-hd-wallet-addon' ), '<br>' ),
		)
	) );
	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'wallets-hdwallet',
		'id'         => 'derivation_path_bcc',
		'type'       => 'select',
		'subtitle'   => '',
		'title'      => sprintf( __( '%s Derivation Path', 'cryptowoo-hd-wallet-addon' ), 'Bitcoin Cash' ),
		'desc'       => __('Change the derivation path to match the derivation path of your wallet client.', 'cryptowoo-hd-wallet-addon'),
		'validate_callback' => 'redux_validate_derivation_path',
		'options'    => array(
			'0/' => __('m/0/i (e.g. Electrum Standard Wallet)', 'cryptowoo-hd-wallet-addon'),
			'' => __('m/i (BIP44 Account)', 'cryptowoo-hd-wallet-addon'),
		),
		'default'    => '0/',
		'select2'    => array( 'allowClear' => false )
    ));

	/*
	 * HD wallet section end
	 */
	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'wallets-hdwallet',
		'id'         => 'section-end',
		'type'       => 'section',
		'indent'     => false,
	) );

	// Re-add Bitcoin testnet section
	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'wallets-hdwallet',
		'id'         => 'wallets-hdwallet-testnet',
		'type'       => 'section',
		'title'      => __( 'TESTNET', 'cryptowoo-hd-wallet-addon' ),
		//'required' => array('testmode_enabled','equals','0'),
		'icon'       => 'fa fa-flask',
		'desc'       => __( 'Accept BTC testnet coins to addresses created via a "tpub..." extended public key. (testing purposes only!)<br><b>Depending on the position of the first unused address, it could take a while until your changes are saved.</b>', 'cryptowoo-hd-wallet-addon' ),
		'indent'     => true,
	) );

	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'wallets-hdwallet',
		'id'                => 'cryptowoo_btc_test_mpk',
		'type'              => 'text',
		'ajax_save'         => false,
		'username'          => false,
		'desc'              => __( 'Bitcoin TESTNET extended public key (tpub...)', 'cryptowoo-hd-wallet-addon' ),
		'title'             => __( 'Bitcoin TESTNET HD Wallet Extended Public Key', 'cryptowoo-hd-wallet-addon' ),
		'validate_callback' => 'redux_validate_mpk',
		'placeholder'       => 'tpub...',
		'text_hint'         => array(
			'title'   => 'Please Note:',
			'content' => sprintf( __( 'If you enter a used key you will have to run the address discovery process after saving this setting.%sUse a dedicated HD wallet (or at least a dedicated xpub) for your store payments to prevent address reuse.', 'cryptowoo-hd-wallet-addon' ), '<br>' ),
		)
	) );

	Redux::setField( 'cryptowoo_payments', array(
		'section_id'        => 'wallets-hdwallet',
			'id'         => 'derivation_path_btctest',
			'type'       => 'select',
			'subtitle'   => '',
			'title'             => sprintf( __( '%s Derivation Path', 'cryptowoo-hd-wallet-addon' ), 'BTCTEST' ),
			'desc'              => __('Change the derivation path to match the derivation path of your wallet client.', 'cryptowoo-hd-wallet-addon'),
			'validate_callback' => 'redux_validate_derivation_path',
			'options'    => array(
				'0/' => __('m/0/i (e.g. Electrum Standard Wallet)', 'cryptowoo-hd-wallet-addon'),
				'' => __('m/i (BIP44 Account)', 'cryptowoo-hd-wallet-addon'),
			),
			'default'    => '0/',
			'select2'    => array( 'allowClear' => false )
		));

	Redux::setField( 'cryptowoo_payments', array(
		'section_id' => 'wallets-hdwallet',
		'id'         => 'section-end',
		'type'       => 'section',
		'indent'     => false,
	) );

}

