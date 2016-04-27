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

/* ************************************************************************
       NOTE: This file is called directly by Barclays ePDQ servers
************************************************************************ */
$cpi_passphrase = "<your cpi_passphrase>";
file_put_contents('postdata'.time().'.txt', var_export($_POST, true));


$epdqData = "";
$data = array_change_key_case($_POST, CASE_UPPER);
ksort($data);

foreach ($data as $key => $value)
{
	if($value != null && $value != "" && $key != "SHASIGN")
		$epdqData .= $key."=".$value.$cpi_passphrase;
}
if($_POST["SHASIGN"] == strtoupper(sha1($epdqData))){
	//VALID
	
	echo "VALID";
}else{
	//INVALID
	exit();
}

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../init.php');
include(dirname(__FILE__).'/epdqcpi.php');
include(dirname(__FILE__).'/ecistatus.php');

/**
 * Retrieving the logging configuration from Prestashop. This directory will 
 * store a record of ePDQ transaction details and results so it is of utmost
 * importance that this path not be within your webroot (i.e. not accessible
 * to the general public over the Internet via unauthenticated or unsecure
 * protocols).
 */
$logging_enabled = Configuration::get('EPDQ_LOGFILESENABLED');
$log_dir = Configuration::get('EPDQ_LOGFILESPATH');

$errors = '';
$result = false;
$epdq = new EpdqCPI();
$ecidetail = epdqAuthResponse($data['STATUS']);
//$statusdetail = epdqTransactionStatusResponse($data['transactionstatus']);

/**
 * Add a log file entry into a file storing records of all transactions
 * for that day, in the $log_dir folder specified above. Entries are
 * written in XML format and include all information Barclays ePDQ
 * sends to this script about the transaction.
 *//*
if( $logging_enabled == '1' ) {


  $entry = '<payment received="' . $_POST["datetime"] . '" store_id="' . $_POST['clientid'] . '" order_id="' . $_POST['oid'];
  $entry .= '" transaction_status="' . $_POST['transactionstatus'] . '" transaction_detail="' . $statusdetail;
  $entry .= '" eci_status="' . $_POST['ecistatus'] . '" eci_detail="' . $ecidetail . '" total="' . $_POST['total'] . '" />'; 
  $log_file = 'epdqcpi_' . date('Y-m-d') . '.log';
  $handle = fopen( "$log_dir/$log_file", "a");
  fwrite( $handle, $entry."\n" );
  fclose( $handle );
}*/

/**
 * Process the information received from Barclays ePDQ and update the
 * order to show details of the transaction, whether the payment was
 * successful, and including the transaction ID into the order notes
 * so that if a refund is required in future for whatever reason, the
 * PrestaShop order can be matched to the Barclays ePDQ transaction.
 */
 if($data['STATUS'] == "5" || $data['STATUS'] == "9"){

      // Payment Authorised
      $cart = new Cart(intval($data['ORDERID']));
      if (!$cart->id){
        $errors = $epdq->getResponseMessage('cart').'<br />';
      }else{
      	echo "<br />ORDER";

        $epdq->validateOrder($data['ORDERID'], _PS_OS_PAYMENT_, floatval( $data['AMOUNT'] ), $epdq->displayName, $ecidetail);
      }
}