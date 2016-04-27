<?php
/* ************************************************************************

   Barclaycard ePDQ CPI Prestashop Payment Module v2.4

   http://www.prestashop-epdq.org/
   http://sourceforge.net/projects/prestashop-epdq/

   Copyright:
     Copyright 2009-2011 (c) Richard Hall, United Kingdom, http://www.richardhall.me.uk/

   License:
     OSL3.0: Licensed under the Open Software License version 3.0, http://www.opensource.org/licenses/osl-3.0.php
     See the license.txt file in the project's compressed distributable for details.

   Authors:
     * Richard Hall (richardhall)

************************************************************************ */

class EpdqCPI extends PaymentModule
{
  private $_html = '';
  private $_postErrors = array();

  public static function getShopDomainSsl($http = false, $entities = false)
  {
  	if (method_exists('Tools', 'getShopDomainSsl'))
  		return Tools::getShopDomainSsl($http, $entities);
  	else
  	{
  		if (!($domain = Configuration::get('PS_SHOP_DOMAIN_SSL')))
  			$domain = self::getHttpHost();
  		if ($entities)
  			$domain = htmlspecialchars($domain, ENT_COMPAT, 'UTF-8');
  		if ($http)
  			$domain = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$domain;
  		return $domain;
  	}
  }
  
  public function __construct()
  {
    $this->name = 'epdqcpi';
    $this->tab = 'Payment';
    $this->version = '2.4';
    $this->currencies = true;
    $this->currencies_mode = 'radio';

    // The parent construct is required for translations
    parent::__construct();

    $this->page = basename(__FILE__, '.php');
    $this->displayName = $this->l('Barclaycard Business ePDQ CPI');
    $this->description = $this->l('Accept credit/debit card payments via Barclays hosted gateway');
    $this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
  }

  public function install()
  {
    if (!parent::install()
       or !Configuration::updateValue('EPDQ_MERCHANTNAME', '12345')
       or !Configuration::updateValue('EPDQ_STOREID', '12345')
       or !Configuration::updateValue('EPDQ_PASSPHRASE', 'abcdef')
       or !Configuration::updateValue('EPDQ_CURRENCYCODE', 'GBP')
       or !Configuration::updateValue('EPDQ_MERCHANTLOGOURL', '')
       or !Configuration::updateValue('EPDQ_SUPPORTEDCARDTYPES', '127')
       or !Configuration::updateValue('EPDQ_LOGFILESENABLED', '0')
       or !Configuration::updateValue('EPDQ_LOGFILESPATH', '')
       or !Configuration::updateValue('EPDQ_GATEWAYHOST', 'secure2.epdq.co.uk')
       or !Configuration::updateValue('EPDQ_MANDATECSC', '1')
       or !$this->registerHook('payment')) 
      return false;
    return true;
  }

  public function uninstall()
  {
    if (!Configuration::deleteByName('EPDQ_MERCHANTNAME')
       or !Configuration::deleteByName('EPDQ_STOREID')
       or !Configuration::deleteByName('EPDQ_PASSPHRASE')
       or !Configuration::deleteByName('EPDQ_CURRENCYCODE')
       or !Configuration::deleteByName('EPDQ_MERCHANTLOGOURL')
       or !Configuration::deleteByName('EPDQ_SUPPORTEDCARDTYPES')
       or !Configuration::deleteByName('EPDQ_LOGFILESENABLED')
       or !Configuration::deleteByName('EPDQ_LOGFILESPATH')
       or !Configuration::deleteByName('EPDQ_GATEWAYHOST')
       or !Configuration::deleteByName('EPDQ_MANDATECSC')
       or !parent::uninstall())
      return false;
    return true;
  }

  // Module Settings/Configuration
  public function getContent()
  {
    include( _PS_MODULE_DIR_ . $this->name . '/settings.php' );
    $epdqSettings = new EpdqCPISettings();
    return $epdqSettings->home();
  }

  private function pullPage( $url, $postdata ) {
    $url = parse_url($url);
    if (!isset($url['port'])) {
      if ($url['scheme'] == 'http') { $url['port']=80; }
      elseif ($url['scheme'] == 'https') { $url['port']=443; }
    }
    $url['query']=isset($url['query'])?$url['query']:'';
    $url['protocol']=$url['scheme'].'://';
    $eol="\r\n";
    $headers = "POST ".$url['protocol'].$url['host'].$url['path']." HTTP/1.0".$eol.
               "Host: ".$url['host'].$eol.
               "Referer: ".$url['protocol'].$url['host'].$url['path'].$eol.
               "Content-Type: application/x-www-form-urlencoded".$eol.
               "Content-Length: ".strlen( $postdata ).$eol.
               $eol.$postdata.$eol.$eol;
    // Open socket to filehandle(epdq encryption cgi)
    $fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
    if($fp) {
      // Write the data to the encryption cgi
      fputs($fp, $headers);
      // Clear the response data
      $result = '';
      // Read the response from the remote cgi 
      while(!feof($fp)) { $result .= fgets($fp, 128); }
      // Close the socket connection
      fclose($fp);

      // Removes headers from HTTP response
      $pattern="/^.*\r\n\r\n/s";
      $result=preg_replace($pattern,'',$result);
      return $result;
    }
  }

  private function getSupportedCardImages( $supported_card_types )
  {
    $images = array();

    // These cards always accepted: VISA, VISA Electron, V-Pay.
    $images[] = array( 'filename' => 'visa.jpg', 'width' => 39, 'height' => 24, 'alt' => 'VISA' );
    $images[] = array( 'filename' => 'visa-electron.jpg', 'width' => 39, 'height' => 24, 'alt' => 'VISA Electron' );
    $images[] = array( 'filename' => 'v-pay.jpg', 'width' => 22, 'height' => 24, 'alt' => 'V-Pay' );

    // These cards accepted except when VISA only: Mastercard
    if( intval($supported_card_types) != 65 ) { 
      $images[] = array( 'filename' => 'mastercard.jpg', 'width' => 39, 'height' => 24, 'alt' => 'MasterCard' );
    }

    // Meastro accepted for specified card type combinations 127 (all cards) and 125 (all cards except American Express).
    if(( intval($supported_card_types) == 127 ) || ( intval($supported_card_types) == 125 )) {
      $images[] = array( 'filename' => 'maestro.jpg', 'width' => 39, 'height' => 24, 'alt' => 'Maestro' );
    }

    // Solo accepted for specified card type combinations 127 (all cards), 125 (all cards except American Express),
    // 119 (all cards except Maestro) and 117 (all cards except Maestro and American Express).
    if(( intval($supported_card_types) == 127 ) || ( intval($supported_card_types) == 125 ) ||
       ( intval($supported_card_types) == 119 ) || ( intval($supported_card_types) == 117 )) {
      $images[] = array( 'filename' => 'solo.jpg', 'width' => 19, 'height' => 24, 'alt' => 'Solo' );
    }

    // American Express accepted for specified card type combinations 127 (all cards), 
    // 119 (all cards except Maestro) and 115 (all cards except Maestro and Solo).
    if(( intval($supported_card_types) == 127 ) ||
       ( intval($supported_card_types) == 119 ) || ( intval($supported_card_types) == 115 )) {
      $images[] = array( 'filename' => 'amex.jpg', 'width' => 43, 'height' => 24, 'alt' => 'American Express' );
    }

    // These cards accepted except when VISA only: JCB
    if( intval($supported_card_types) != 65 ) { 
      $images[] = array( 'filename' => 'jcb.jpg', 'width' => 19, 'height' => 24, 'alt' => 'JCB' );
    }

    // Secure verifications
    $images[] = array( 'filename' => 'verified-by-visa.jpg', 'width' => 49, 'height' => 24, 'alt' => 'Verified by VISA' );
    if( intval($supported_card_types) != 65 ) { 
      $images[] = array( 'filename' => 'mastercard-securecode.jpg', 'width' => 60, 'height' => 24, 'alt' => 'MasterCard SecureCode' );
    }

    // Render XHTML
    $imgpath = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/';
    $first = true;
    $html = '';
    foreach( $images as $image ) {
      $html .= '<img src="' . $imgpath . $image['filename'] . '" width="' . $image['width'] . '" height="' . $image['height'] . '"';
      $html .= ' alt="' . $image['alt'] . '" style="';
      if( $first === true ) { $html .= 'margin-left: 100px; '; $first = false; }
      $html .= 'margin-right: 2px;" />' . "\r\n";
    }
   
    return $html;
  }

  public function hookPayment($params)
  {
    global $smarty;
    
    if(!isset($context))
    	$context = Context::getContext();

    // Initialise arrays with ISO 3166 country codes and ISO 4217 currency codes
    include_once( _PS_MODULE_DIR_ . $this->name . '/addresses.php' );
    include_once( _PS_MODULE_DIR_ . $this->name . '/currencies.php' );

    $shop_url = EpdqCPI::getShopDomainSsl(true, true);
    
    
    $address = new Address(intval($params['cart']->id_address_invoice));
    $delivery = new Address(intval($params['cart']->id_address_delivery));
    $customer = new Customer(intval($params['cart']->id_customer));
    $currency = $this->getCurrency();

    $merchant_name = Configuration::get('EPDQ_MERCHANTNAME');
    $store_id = Configuration::get('EPDQ_STOREID');
    $cpi_passphrase = Configuration::get('EPDQ_PASSPHRASE');
    $currency_code = Configuration::get('EPDQ_CURRENCYCODE');
    $mandatecsc = Configuration::get('EPDQ_MANDATECSC');
    $gateway_host = Configuration::get('EPDQ_GATEWAYHOST');
    $supported_card_types = Configuration::get('EPDQ_SUPPORTEDCARDTYPES');
    $merchant_logo_url = Configuration::get('EPDQ_MERCHANTLOGOURL');

   /* if( $gateway_host == 'gateway.prestashop-epdq.org' ) {
      $url_enctool = 'http://' . $gateway_host . '/cgi-bin/CcxBarclaysEpdqEncTool.e';
      $url_submit = 'http://' . $gateway_host . '/cgi-bin/CcxBarclaysEpdq.e';
    } else {
      $url_enctool = 'http://' . $gateway_host . '/cgi-bin/CcxBarclaysEpdqEncTool.e';
      $url_submit = 'https://' . $gateway_host . '/cgi-bin/CcxBarclaysEpdq.e';
    }*/

    if (!Validate::isLoadedObject($address) or !Validate::isLoadedObject($customer) or !Validate::isLoadedObject($currency))
      return $this->l('ePDQ payment module error: (invalid address or customer)');

    if(isset($params['total']))
    	$epdqTotal = $params['total'];
    else
    	$epdqTotal = number_format($params['cart']->getOrderTotal(true, 3), 2, '.', '');
    $order_id = $params['cart']->id;
    
   /* if( $gateway_host == 'gateway.prestashop-epdq.org' ) {
      $validation_url = $shop_url._MODULE_DIR_.$this->name.'/validation.php';
      $epdqData .= 'clientid=12345&password=p4ssphr4s3';
      $epdqData .= '&validationurl=' . $validation_url;
    } else {
      $epdqData = 'clientid=' . $store_id . '&password=' . $cpi_passphrase;
    }
    $epdqData .= '&oid=' . $order_id . '&chargetype=Auth&currencycode=' . epdqCurrencyCode($currency_code) . '&total=' . $epdqTotal . '&mandatecsc=' . $mandatecsc;
    $encrypted_data = $this->pullPage( $url_enctool, $epdqData );
*/
    $billing_state = $address->id_state ? epdqState::getISOById(intval($address->id_state)) : false;
    $delivery_state = $delivery->id_state ? epdqState::getISOById(intval($delivery->id_state)) : false;
    $billing_country = epdqCountry::getISOById(intval($address->id_country));
    $delivery_country = epdqCountry::getISOById(intval($delivery->id_country));
    
    $data = array(
		'PSPID' => $store_id, 
		'ORDERID' => $order_id, 
		'AMOUNT' => (number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 4), $currency), 2, '.', '') * 100) , 
		'CURRENCY' => $currency->iso_code,
		'LANGUAGE' => "en_US",
		'LOGO' => $merchant_logo_url,
		'ACCEPTURL' => $context->link->getPageLink('order-follow'), 
		'CANCELURL' => $context->link->getPageLink('my-account'),
		'ECOM_BILLTO_POSTAL_STREET_LINE1' => utf8_decode($address->address1), 
		'ECOM_BILLTO_POSTAL_STREET_LINE2' => utf8_decode($address->address2), 
		'ECOM_BILLTO_POSTAL_CITY' => utf8_decode($address->city), 
		'ECOM_BILLTO_POSTAL_COUNTRYCODE' => $billing_country, 
		'ECOM_BILLTO_POSTAL_POSTALCODE' => utf8_decode($address->postcode), 
		'CN' => $customer->firstname." ".$customer->lastname, 
		'EMAIL' => utf8_decode($customer->email), 
		'OWNERADDRESS' => utf8_decode($address->address1),
		'OWNERCTY' => utf8_decode($address->city),
		'OWNERTELNO' => utf8_decode($address->phone),
		'COM' => utf8_decode("Out Of Warranty Charge"),
	);
	ksort($data);
	
	$epdqData = "";


	foreach ($data as $key => $value)
	{
		if($value != null && $value != "")
			$epdqData .= $key."=".$value.$cpi_passphrase;
	}
	
	$smarty->assign(array(
	  'epitems' => $data,
      'epdqGatewayUrl' => $gateway_host,
      'epdqSupportedCardImages' => $this->getSupportedCardImages($supported_card_types),
      'SHASIGN' => sha1($epdqData),
	));
    /*
    $smarty->assign(array(
      'epdqGatewayUrl' => $url_submit,
      'epdqEncryptedData' => $encrypted_data,
      'epdqReturnUrl' => $context->link->getPageLink('order-follow'),
      'epdqPSPID' => $store_id,
      'epdqOrderID' => $order_id,
      'epdqCurrency' => $currency;
      'epdqMerchantName' => $merchant_name,
      'epdqsha' => sha1($order_id."RAKORMA172e5afceea3970e"),
      'customer_name' => $customer->firstname." ".$customer->lastname,
      'epdqBillingAddress1' => utf8_decode($address->address1),
      'epdqBillingAddress2' => utf8_decode($address->address2),
      'epdqBillingCity' => utf8_decode($address->city),
      'epdqBillingCountry' => $billing_country,
      'epdqBillingPostcode' => utf8_decode($address->postcode),
      'epdqBillingState' => utf8_decode($billing_state),
      'epdqBillingTelephone' => utf8_decode($address->phone),
      'epdqCustomerEmail' => utf8_decode($customer->email),
      'epdqDeliveryAddress1' => utf8_decode($delivery->address1),
      'epdqDeliveryAddress2' => utf8_decode($delivery->address2),
      'epdqDeliveryCity' => utf8_decode($delivery->city),
      'epdqDeliveryCountry' => $delivery_country,
      'epdqDeliveryPostcode' => utf8_decode($delivery->postcode),
      'epdqDeliveryState' => utf8_decode($delivery_state),
      'epdqDeliveryTelephone' => utf8_decode($delivery->phone),
      'epdqSupportedCardTypes' => $supported_card_types,
      'epdqSupportedCardImages' => $this->getSupportedCardImages($supported_card_types),
      'epdqMerchantLogoURL' => $merchant_logo_url,
      'address' => $address,
      'country' => new Country(intval($address->id_country)),
      'customer' => $customer,
      'currency' => $currency,
      'amount' => number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 4), $currency), 2, '.', ''),
      'shipping' =>  number_format(Tools::convertPrice(($params['cart']->getPackageShippingCost() + $params['cart']->getOrderTotal(true, 6)), $currency), 2, '.', ''),
      'discounts' => $params['cart']->getCartRules(),
     // 'total' => number_format(Tools::convertPrice($params['cart']->getOrderTotal(true, 3), $currency), 2, '.', ''),
      //'id_cart' => intval($params['cart']->id),
      'this_path' => $this->_path,
      'this_path_ssl' => (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').htmlspecialchars($_SERVER['HTTP_HOST'], ENT_COMPAT, 'UTF-8').__PS_BASE_URI__.'modules/'.$this->name.'/'
    ));*/
    return $this->display(__FILE__, 'epdqcpi.tpl');
  }

  public function getResponseMessage($key)
  {
    $translations = array(
      'payment' => $this->l('Payment: '),
      'cart' => $this->l('Item not found'),
      'order' => $this->l('Order has already been placed'),
      'transaction' => $this->l('Transaction ID: '),
      'failed' => $this->l('The payment transaction could NOT be verified')
    );
    return $translations[$key];
  }

}
