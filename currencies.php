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

function epdqCurrencies()
{
  /**
   * The following array comprises all the different ISO 4217 currency codes for
   * supply to Barclays ePDQ when specifying the currency of a transaction.
   */
  $currency_codes = array(
    'AED'=>'784', 'AFA'=>'4', 'ALL'=>'8', 'AMD'=>'51', 'ANG'=>'532', 'AON'=>'24', 'ARS'=>'32', 'ATS'=>'40',
    'AUD'=>'36', 'AWG'=>'533', 'AZM'=>'31', 'BAM'=>'977', 'BBD'=>'52', 'BDT'=>'50', 'BEF'=>'56', 'BGL'=>'100',
    'BHD'=>'48', 'BIF'=>'108', 'BMD'=>'60', 'BND'=>'96', 'BRL'=>'986', 'BSD'=>'44', 'BWP'=>'72', 'BYR'=>'974',
    'BZD'=>'84', 'CAD'=>'124', 'CDF'=>'976', 'CHF'=>'756', 'CLP'=>'152', 'CNY'=>'156', 'COP'=>'170', 'CRC'=>'188',
    'CUP'=>'192', 'CVE'=>'132', 'CYP'=>'196', 'CZK'=>'203', 'DEM'=>'276', 'DJF'=>'262', 'DKK'=>'208', 'DOP'=>'214',
    'DZD'=>'12', 'ECS'=>'218', 'EEK'=>'233', 'EGP'=>'818', 'ERN'=>'232', 'ESP'=>'724', 'ETB'=>'230', 'EUR'=>'978',
    'FIM'=>'246', 'FJD'=>'242', 'FKP'=>'238', 'FRF'=>'250', 'GBP'=>'826', 'GEL'=>'981', 'GHC'=>'288', 'GIP'=>'292',
    'GMD'=>'270', 'GNF'=>'324', 'GRD'=>'300', 'GTQ'=>'320', 'GWP'=>'624', 'GYD'=>'328', 'HKD'=>'344', 'HNL'=>'340',
    'HRK'=>'191', 'HTG'=>'332', 'HUF'=>'348', 'IDR'=>'360', 'IEP'=>'372', 'ILS'=>'376', 'INR'=>'356', 'IQD'=>'368',
    'IRR'=>'364', 'ISK'=>'352', 'ITL'=>'380', 'JMD'=>'388', 'JOD'=>'400', 'JPY'=>'392', 'KES'=>'404', 'KGS'=>'417',
    'KHR'=>'116', 'KMF'=>'174', 'KPW'=>'408', 'KRW'=>'410', 'KWD'=>'414', 'KYD'=>'136', 'KZT'=>'398', 'LAK'=>'418',
    'LBP'=>'422', 'LKR'=>'144', 'LRD'=>'430', 'LSL'=>'426', 'LTL'=>'440', 'LUF'=>'442', 'LVL'=>'428', 'LYD'=>'434',
    'MAD'=>'504', 'MDL'=>'498', 'MGF'=>'450', 'MKD'=>'807', 'MMK'=>'104', 'MNT'=>'496', 'MOP'=>'446', 'MRO'=>'478',
    'MTL'=>'470', 'MUR'=>'480', 'MVR'=>'462', 'MWK'=>'454', 'MXN'=>'484', 'MXV'=>'979', 'MYR'=>'458', 'MZM'=>'508',
    'NAD'=>'516', 'NGN'=>'566', 'NIO'=>'558', 'NLG'=>'528', 'NOK'=>'578', 'NPR'=>'524', 'NZD'=>'554', 'OMR'=>'512',
    'PAB'=>'590', 'PEN'=>'604', 'PGK'=>'598', 'PHP'=>'608', 'PKR'=>'586', 'PLN'=>'985', 'PTE'=>'620', 'PYG'=>'600',
    'QAR'=>'634', 'ROL'=>'642', 'RUB'=>'643', 'RUR'=>'810', 'RWF'=>'646', 'SAR'=>'682', 'SBD'=>'90', 'SCR'=>'690',
    'SDD'=>'736', 'SEK'=>'752', 'SGD'=>'702', 'SHP'=>'654', 'SIT'=>'705', 'SKK'=>'703', 'SLL'=>'694', 'SOS'=>'706',
    'SRG'=>'740', 'STD'=>'678', 'SVC'=>'222', 'SYP'=>'760', 'SZL'=>'748', 'THB'=>'764', 'TJR'=>'762', 'TJS'=>'972',
    'TMM'=>'795', 'TND'=>'788', 'TOP'=>'776', 'TPE'=>'626', 'TRL'=>'792', 'TTD'=>'780', 'TWD'=>'901', 'TZS'=>'834',
    'UAH'=>'980', 'UGX'=>'800', 'USD'=>'840', 'UYU'=>'858', 'UZS'=>'860', 'VEB'=>'862', 'VND'=>'704', 'VUV'=>'548',
    'WST'=>'882', 'XAF'=>'950', 'XCD'=>'951', 'XDR'=>'960', 'XOF'=>'952', 'XPF'=>'953', 'YER'=>'886', 'YUM'=>'891',
    'ZAL'=>'991', 'ZAR'=>'710', 'ZMK'=>'894', 'ZRN'=>'180', 'ZWD'=>'716' );
  return $currency_codes;
}

function epdqCurrencySelect($default)
{
  $currency_codes = epdqCurrencies();
  $currency_options = '<select name="currency_code">' . "\r\n";
  foreach( $currency_codes as $code => $num )
  {
    $currency_options .= '<option value="' . $code . '"';
    if( $code == $default ) $currency_options .= ' selected="selected"';
    $currency_options .= '>' . $code . '</option>' . "\r\n";
  }
  $currency_options .= '</select>' . "\r\n";
  return $currency_options;
}

function epdqCurrencyCode( $code )
{
  $currency_codes = epdqCurrencies();
  return $currency_codes[$code];
}

