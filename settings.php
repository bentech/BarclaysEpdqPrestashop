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

class EpdqCPISettings extends EpdqCPI
{
  public function home()
  {
    // Header name/logo/description
    $html = '<h2>Barclaycard Business ePDQ Cardholder Payment Interface (CPI)</h2>
             <img src="../modules/epdqcpi/epdq.gif" style="float:left; margin-right:15px;" />
             <b>'.$this->l('This module allows you to accept payments via Barclaycard Business ePDQ CPI.').'</b><br /><br />'.
             $this->l('If the client chooses this payment mode, your Barclays Internet merchant bank account will be automatically credited upon successful payment authorisations.').
             ' '.$this->l('You also need to configure your ePDQ CPI account using the web-based').' '.
             '<a href="https://cpiadmin.epdq.co.uk/cgi-bin/CcxBarclaysEpdqAdminTool.e" target="_edpqcpiadmin" style="color: navy; text-decoration: underline;">'.
             $this->l('CPI Admin Tool').'</a>.<br /><br /><br />';

    $html .= $this->_processFormSubmission();
    $html .= $this->_processEnvironmentCheck();

    if (isset($this->_postErrors) and sizeof($this->_postErrors)) $html .= $this->_displayErrors();      

    $html .= $this->_displayFormAccount();
    $html .= $this->_displayFormDisplay();
    $html .= $this->_displayFormLogging();
    $html .= $this->_displayFormServerEnvironment();
    $html .= $this->_displayHelpInformation();
    return $html;
  }

  private function _processEnvironmentCheck()
  {
    $logging_enabled = Configuration::get('EPDQ_LOGFILESENABLED');
    $log_dir = Configuration::get('EPDQ_LOGFILESPATH');

    // Check log files path exists
    if(( $logging_enabled == '1' ) && !file_exists( $log_dir )) {
      $this->_postErrors[] = $this->l('Log files path does not exist.');
    }
 
    // Check log files path is writable
    if(( $logging_enabled == '1' ) && !is_writable( $log_dir )) {
      $this->_postErrors[] = $this->l('Log files path does not have writable permissions.');
    }
  }

  private function _processFormSubmission()
  {
  	if(!isset($html))
  		$html = "";
    // Process Barclays ePDQ CPI Account Settings
    if (isset($_POST['submitAccount']))
    {
      if (empty($_POST['store_id']))
        $this->_postErrors[] = $this->l('ePDQ Store ID is required.');
      if (empty($_POST['cpi_passphrase']))
        $this->_postErrors[] = $this->l('ePDQ CPI Passphrase is required.');
      if (!sizeof($this->_postErrors))
      {
        Configuration::updateValue('EPDQ_STOREID', $_POST['store_id']);
        Configuration::updateValue('EPDQ_PASSPHRASE', $_POST['cpi_passphrase']);
        Configuration::updateValue('EPDQ_CURRENCYCODE', $_POST['currency_code']);
        Configuration::updateValue('EPDQ_SUPPORTEDCARDTYPES', $_POST['supported_card_types']);
        Configuration::updateValue('EPDQ_MANDATECSC', $_POST['mandatecsc']);
        $html .= $this->_displayConfirmation();
      }
    }

    // Process Display Settings
    if( isset( $_POST['submitDisplay'] ))
    {
      if (empty($_POST['merchant_name']))
        $this->_postErrors[] = $this->l('Merchant display name is required.');
      if (!sizeof($this->_postErrors))
      {
        Configuration::updateValue('EPDQ_MERCHANTNAME', $_POST['merchant_name']);
        Configuration::updateValue('EPDQ_MERCHANTLOGOURL', $_POST['merchant_logo_url']);
        $html .= $this->_displayConfirmation();
      }
    }

    // Process Payment Transactions Logging Settings
    if( isset( $_POST['submitLogging'] ))
    {
      if( empty($_POST['log_files_path']) && ( $_POST['logging_enabled'] == '1' ))
        $this->_postErrors[] = $this->l('Log files path is required when logging is enabled.');
      if (!sizeof($this->_postErrors))
      {
        Configuration::updateValue('EPDQ_LOGFILESENABLED', $_POST['logging_enabled']);
        Configuration::updateValue('EPDQ_LOGFILESPATH', $this->_trimTrailingSlash( $_POST['log_files_path'] ));
        $html .= $this->_displayConfirmation();
      }
    }

    // Process Server Environment Settings
    if( isset( $_POST['submitEnvironment'] ))
    {
      if (empty($_POST['gateway_host']))
        $this->_postErrors[] = $this->l('Payment gateway host is required.');
      if (!sizeof($this->_postErrors))
      {
        Configuration::updateValue('EPDQ_GATEWAYHOST', $_POST['gateway_host']);
        $html .= $this->_displayConfirmation();
      }
    }      
    return $html;
  }

  private function _displayFormAccount()
  {
    // Initialise arrays with ISO 4217 currency codes and ePDQ supported card types
    include_once( _PS_MODULE_DIR_ . $this->name . '/currencies.php' );
    include_once( _PS_MODULE_DIR_ . $this->name . '/cardtypes.php' );

    // Retrieve module configuration information from Prestashop
    $conf = Configuration::getMultiple(array( 'EPDQ_STOREID', 'EPDQ_PASSPHRASE', 'EPDQ_CURRENCYCODE', 'EPDQ_SUPPORTEDCARDTYPES', 'EPDQ_GATEWAYHOST', 'EPDQ_MANDATECSC' ));
    $store_id = array_key_exists('store_id', $_POST) ? $_POST['store_id'] : (array_key_exists('EPDQ_STOREID', $conf) ? $conf['EPDQ_STOREID'] : '');
    $cpi_passphrase = array_key_exists('cpi_passphrase', $_POST) ? $_POST['cpi_passphrase'] : (array_key_exists('EPDQ_PASSPHRASE', $conf) ? $conf['EPDQ_PASSPHRASE'] : '');
    $currency_code = array_key_exists('currency_code', $_POST) ? $_POST['currency_code'] : (array_key_exists('EPDQ_CURRENCYCODE', $conf) ? $conf['EPDQ_CURRENCYCODE'] : '');
    $supported_card_types = array_key_exists('supported_card_types', $_POST) ? $_POST['supported_card_types'] : (array_key_exists('EPDQ_SUPPORTEDCARDTYPES', $conf) ? $conf['EPDQ_SUPPORTEDCARDTYPES'] : '');
    $gateway_host = array_key_exists('gateway_host', $_POST) ? $_POST['gateway_host'] : (array_key_exists('EPDQ_GATEWAYHOST', $conf) ? $conf['EPDQ_GATEWAYHOST'] : '');
    $mandatecsc = array_key_exists('mandatecsc', $_POST) ? $_POST['mandatecsc'] : (array_key_exists('EPDQ_MANDATECSC', $conf) ? $conf['EPDQ_MANDATECSC'] : '');

    $html = '
      <form action="'.strval($_SERVER['REQUEST_URI']).'" method="post" style="float:left;"><fieldset style="width:400px;">
      <legend><img src="../img/admin/cog.gif" />'.$this->l('Barclays ePDQ CPI Account Settings:').'</legend>

      <label>'.$this->l('ePDQ CPI Store ID:').'</label>
      <div class="margin-form"><input type="text" size="33" name="store_id" value="'.htmlentities($store_id, ENT_COMPAT, 'UTF-8').'" />
      </div><br />

      <label>'.$this->l('ePDQ CPI Passphrase:').'</label>
      <div class="margin-form"><input type="text" size="33" name="cpi_passphrase" value="'.htmlentities($cpi_passphrase, ENT_COMPAT, 'UTF-8').'" />
      </div><br />

      <label>'.$this->l('ePDQ Account Currency:').'</label>
      <div class="margin-form">'.epdqCurrencySelect( $currency_code ).'
      </div><br />

      <label>'.$this->l('Accepted Card Types:').'</label>
      <div style="float: right; height: 40px;">'.epdqCardTypesSelect( $supported_card_types ).'
      </div><br /><br />

      <label style="width:140px;">'.$this->l('Card Security Code (CSC) Required:').'</label>
      <div class="margin-form" style="padding-left:160px;">
      <input type="radio" name="mandatecsc" value="1" '.($mandatecsc ? 'checked="checked"' : '').' /> '.$this->l('Yes').'
      <input type="radio" name="mandatecsc" value="0" '.(!$mandatecsc ? 'checked="checked"' : '').' /> '.$this->l('No').'
      <p class="hint" style="display: block;float: none;"><img src="../img/admin/unknown.gif" />'.
        $this->l('The CSC code (also known as CVV2) appears on the reverse side of credit/debit cards and is typically a 3-digit numerical code imprinted alongside the magnetic stripe, however on American Express cards this code is normally 4-digits and appears on the front of the card.').'</p>
      </div><br />

      <center style="clear:both;"><input type="submit" name="submitAccount" value="'.$this->l('Update settings').'" class="button" /></center>
      </fieldset></form>';
    return $html;
  }

  private function _displayFormDisplay()
  {
    // Retrieve module configuration information from Prestashop
    $conf = Configuration::getMultiple(array( 'EPDQ_MERCHANTNAME', 'EPDQ_MERCHANTLOGOURL' ));
    $merchant_name = array_key_exists('merchant_name', $_POST) ? $_POST['merchant_name'] : (array_key_exists('EPDQ_MERCHANTNAME', $conf) ? $conf['EPDQ_MERCHANTNAME'] : '');
    $merchant_logo_url = array_key_exists('merchant_logo_url', $_POST) ? $_POST['merchant_logo_url'] : (array_key_exists('EPDQ_MERCHANTLOGOURL', $conf) ? $conf['EPDQ_MERCHANTLOGOURL'] : '');

    $html = '
      <form action="'.strval($_SERVER['REQUEST_URI']).'" method="post" style="margin:0px 0px 0px 40px; float:right;"><fieldset style="width:428px;">
      <legend><img src="../img/admin/cog.gif" />'.$this->l('Display Settings:').'</legend>

      <label>'.$this->l('Merchant Display Name:').'</label>
      <div class="margin-form"><input type="text" size="33" name="merchant_name" value="'.htmlentities($merchant_name, ENT_COMPAT, 'UTF-8').'" />
      </div><br />

      <label>'.$this->l('Merchant Display Logo:').'</label>
      <div class="margin-form"><input type="text" size="33" name="merchant_logo_url" value="'.htmlentities($merchant_logo_url, ENT_COMPAT, 'UTF-8').'" />
      <p class="hint" style="display: block;"><img src="../img/admin/unknown.gif" /><b style="font-style: italic;">'.$this->l('(Optional)').'</b> '.
        $this->l('Specify URL of image in JPG or GIF format, should be of width 500px and height 100px and hosted on a secure (https) server.').' '.
        $this->l('If Merchant Display Logo URL is left blank, Merchant Display Name will be shown more prominently instead.').'</p>
      </div><br />

      <center style="clear:both;"><input type="submit" name="submitDisplay" value="'.$this->l('Update settings').'" class="button" /></center>
      </fieldset></form>';
    return $html;
  }

  private function _displayFormServerEnvironment()
  {
    // Initialise array with payment gateway options
    include_once( _PS_MODULE_DIR_ . $this->name . '/gateways.php' );

    // Retrieve module configuration information from Prestashop
    $conf = Configuration::getMultiple(array( 'EPDQ_GATEWAYHOST'));
    $gateway_host = array_key_exists('gateway_host', $_POST) ? $_POST['gateway_host'] : (array_key_exists('EPDQ_GATEWAYHOST', $conf) ? $conf['EPDQ_GATEWAYHOST'] : '');

    $html = '
      <form action="'.strval($_SERVER['REQUEST_URI']).'" method="post" style="margin:20px 0px 0px 0px; float:left;"><fieldset style="width:400px;">
      <legend><img src="../img/admin/cog.gif" />'.$this->l('Server Environment Settings:').'</legend>
      <label>'.$this->l('Payment Gateway Host:').'</label>
      <div style="float: right; height: 100px;">'.epdqGatewayHostsSelect( $gateway_host ).'
      <div class="margin-form" style="padding-left: 0px;">
      <p class="hint" style="display: block; float: none; clear:both; width: 400px;"><img src="../img/admin/unknown.gif" />'.$this->l('When using the test payment gateway, for reasons of information security your CPI Store ID and CPI Passphrase entered above will not used (12345 and p4ssphr4s3 will be substituted respectively).').'</p>
      </div></div><div style="clear:both; height: 5px;"></div>
      <center style="clear:both;"><input type="submit" name="submitEnvironment" value="'.$this->l('Update settings').'" class="button" /></center>
      </fieldset></form>';
    return $html;
  }

  private function _displayFormLogging()
  {
    // Retrieve module configuration information from Prestashop
    $conf = Configuration::getMultiple(array( 'EPDQ_LOGFILESENABLED', 'EPDQ_LOGFILESPATH' ));
    $logging_enabled = array_key_exists('logging_enabled', $_POST) ? $_POST['logging_enabled'] : (array_key_exists('EPDQ_LOGFILESENABLED', $conf) ? $conf['EPDQ_LOGFILESENABLED'] : '');
    $log_files_path = array_key_exists('log_files_path', $_POST) ? $_POST['log_files_path'] : (array_key_exists('EPDQ_LOGFILESPATH', $conf) ? $conf['EPDQ_LOGFILESPATH'] : '');

    $html = '
      <form action="'.strval($_SERVER['REQUEST_URI']).'" method="post" style="margin:20px 0px 0px 40px; float:right;"><fieldset style="width:428px;">
      <legend><img src="../img/admin/cog.gif" />'.$this->l('Payment Transactions Logging Settings:').'</legend>

      <label style="width:140px;">'.$this->l('Logging Enabled:').'</label>
      <div class="margin-form" style="padding-left:160px;">
      <input type="radio" name="logging_enabled" value="1" '.($logging_enabled ? 'checked="checked"' : '').' /> '.$this->l('Yes').'
      <input type="radio" name="logging_enabled" value="0" '.(!$logging_enabled ? 'checked="checked"' : '').' /> '.$this->l('No').'
      <p class="hint" style="display: block;float: none;"><img src="../img/admin/unknown.gif" />'.$this->l('When enabled this module will keep a file-based log of all ePDQ payment transactions in XML format store in the path below.').'</p>
      </div><br />

      <label style="width:140px;">'.$this->l('Log Files Path:').'</label>
      <div class="margin-form" style="padding-left:160px;"><input type="text" size="33" name="log_files_path" value="'.htmlentities($log_files_path, ENT_COMPAT, 'UTF-8').'" />
      <p class="hint" style="display: block;float: none;"><img src="../img/admin/warning.gif" />'.$this->l('Specify an absolute path with write-access, this should always be outside of your web-root directory to keep payment transaction information secure.').'</p>
      </div><br />

      <center style="clear:both;"><input type="submit" name="submitLogging" value="'.$this->l('Update settings').'" class="button" /></center>
      </fieldset></form>';
    return $html;
  }

  private function _displayHelpInformation()
  {
    $html = '
      <div style="clear:both;"><br /></div>
      <fieldset><legend><img src="../img/admin/unknown.gif" />'.$this->l('Information').'</legend>
      '.$this->l('In order to use this payment module, you need to configure your Barclays ePDQ CPI Store, follow the instructions sent to you by email from Barclaycard Business.').'<br /><br />
      <b style="color: red;">'.$this->l('PrestaShop currencies must also be configured!</b> (Profile > Financial Information > Currency balances)').'<br />
      </fieldset>';
    return $html;
  }

  private function _displayErrors()
  {
    $nbErrors = sizeof($this->_postErrors);
    $html .= '
    <div class="alert error">
      <h3>'.($nbErrors > 1 ? $this->l('There are') : $this->l('There is')).' '.$nbErrors.' '.($nbErrors > 1 ? $this->l('errors') : $this->l('error')).'</h3>
      <ol>';
    foreach ($this->_postErrors AS $error)
      $html .= '<li>'.$error.'</li>';
    $html .= '
      </ol>
    </div><div style="clear:both;"><br /></div>';
    return $html;
  }

  private function _displayConfirmation()
  {
    $html .= '
    <div class="conf confirm">
      <img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />
      '.$this->l('Settings updated').'
    </div>';
    return $html;
  }

  private function _trimTrailingSlash( $path )
  {
    $path = trim( $path );
    return $path == '/' ? $path : rtrim( $path, '/' );
  }

}