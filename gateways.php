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

function epdqGatewayHosts()
{ 
  /**
   * The following array comprises the different ePDQ gateway hosts available for use.
   */
  $gateway_hosts = array(
    'MDE' => 'https://payments.epdq.co.uk/ncol/prod/orderstandard.asp',
    'MDE TEST' => 'https://mdepayments.epdq.co.uk/ncol/test/orderstandard.asp' );
  return $gateway_hosts;
}

function epdqGatewayHostsSelect( $default )
{
  $gateway_hosts = epdqGatewayHosts();
  $gateway_hosts_options = '<select name="gateway_host">' . "\r\n";
  foreach( $gateway_hosts as $name => $hostname )
  {
    $gateway_hosts_options .= '<option value="' . $hostname . '"';
    if( $hostname == $default ) $gateway_hosts_options .= ' selected="selected"';
    $gateway_hosts_options .= '>' . $name . '</option>' . "\r\n";
  }
  $gateway_hosts_options .= '</select>' . "\r\n";
  return $gateway_hosts_options;
}

function epdqGatewayHost( $name )
{
  $gateway_hosts = epdqGatewayHosts();
  return $gateway_hosts[$name];
}
