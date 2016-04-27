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

/**
 * The following function extends PrestaShop's State class functionality to 
 * retrieve the appropriate United States Postal Service state code for supply
 * to Barclays ePDQ when a user specifies the state field for any address. This
 * function is only used for addresses based in the U.S.
 */

class epdqState extends State {
  static public function getISOById($id_state) {
    $result = Db::getInstance()->getRow('
      SELECT `iso_code`
      FROM `'._DB_PREFIX_.'state`
      WHERE `id_state` = '.intval($id_state).'');
    return $result['iso_code'];
  }
}

/**
 * The following function extends PrestaShop's Country class functionality to 
 * retrieve the appropriate ISO 3166 country code for supply to Barclays ePDQ
 * when a user specifies the country for any address.
 */

class epdqCountry extends Country {
  static public function getISOById($id_country) {
    $result = Db::getInstance()->getRow('
      SELECT `iso_code`
      FROM `'._DB_PREFIX_.'country`
      WHERE `id_country` = '.intval($id_country));
    return $result['iso_code'];
  }
}

