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

function epdqCardTypes()
{
  /**
   * The following array comprises the different ePDQ supported card type combinations.
   * <input type="hidden" name="supportedcardtypes" value="code">
   */
  $card_types = array(
    'All cards' => '127',
    'All cards except American Express' => '125',
    'All cards except Maestro' => '119',
    'All cards except Maestro and American Express' => '117',
    'All cards except Meastro, Solo and American Express' => '113',
    'All cards except Meastro and Solo' => '115',
    'Only VISA and Electron cards' => '65' );
  return $card_types;
}

function epdqCardTypesSelect( $default )
{
  $card_types = epdqCardTypes();
  $card_types_options = '<select name="supported_card_types">' . "\r\n";
  foreach( $card_types as $name => $num )
  {
    $card_types_options .= '<option value="' . $num . '"';
    if( $num == $default ) $card_types_options .= ' selected="selected"';
    $card_types_options .= '>' . $name . '</option>' . "\r\n";
  }
  $card_types_options .= '</select>' . "\r\n";
  return $card_types_options;
}

function epdqCardType( $name )
{
  $card_types = epdqCardTypes();
  return $card_types[$name];
}
