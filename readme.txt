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

USEFUL LINKS

==== Project Homepage ====
http://www.prestashop-epdq.org/
This should be your first-stop for up-to-date documentation, troubleshooting FAQ, development roadmap etc.

==== SourceForge.net Project Homepage
https://sourceforge.net/projects/prestashop-epdq/
If you find this project useful please take time to rate and review it on SourceForge.net, donations also welcome!

==== Project Support Requests Tracker ====
https://sourceforge.net/tracker/?func=add&group_id=275774&atid=1171551
If you have difficulties with this module please open a support ticket here first before asking in forums.

==== PrestaShop Community Forums ====
http://www.prestashop.com/forums/

==== PrestaShop ====
http://www.prestashop.com/

==== Barclays ePDQ Card Payment Interface (CPI) ====
http://www.barclaycardbusiness.co.uk/accepting_cards/phone_mail_internet/end_to_end/


CHANGE HISTORY

==== Release v2.4-production (19-Feb-2011) ====
* Bug fix to epdqcpi.php file to ensure correct operation when store is configured for use with an SSL certificate.
* Bug fix to settings.php lines 125-128 to resolve issue of settings.php failing to include countries.php.
* Date range updated in Copyright notices.
* Version number updated. Apologies- this was misreported in the module configuration as v2.2 in release 2.3.
* Accepted payment card logos included at payment provider choice page as specified in module config screen.

==== Release v2.3-production (18-Aug-2010) ====
* Bug fix due to build problem with epdqcpi.php file in v2.2.
* Simple bug fix to validation.php line 78.

==== Release v2.2-production (06-Aug-2010) ====
* Bug fix to enable payment page to work properly when USA state specified within customer/shipping addresses.

==== Release v2.1-production (06-May-2010) ====
* Bug fix to protocol used for the ePDQ EncTool script which doesn't support SSL.

==== Release v2.0-production (01-May-2010) ====
* Configuration changes to enable testing/sandbox environment.
* Bug fix enabling Prestashop to validate orders with declined payment transactions and append error messages to order notes.
* New configuration option to specify whether cardholders will be required to enter CSC/CVV2 codes as mandatory for payment authorisation.
* Log files path now configurable from within the Module configuration screen.
* New dropdown options to configure ePDQ supported card types added (specifications on page 13 of cpi_integration_enhancedv6.0.pdf).
* New configuration option to specify a logo file hosted on an SSL-enabled webserver path of dimensions width 500px height 100px to appear instead of merchant display name on payment gateway pages.
* More detailed interpretation of transaction status responses added to order notes upon card payment validation - particularly helpful when payments are declined.

==== Release v1.2-stable (24-Apr-2010) ====
* Two typo bugs fixed in ecistatus.php in response to support ticket 2985332 - additional php opening tag removed from top of file, and function epdqAuthRespose renamed to epdqAuthResponse.

==== Release v1.1-stable (18-Feb-2010) ====
* Country codes and currency codes are confirmed as up to date with latest standards [http://en.wikipedia.org/wiki/ISO_3166 ISO 3166] and [http://en.wikipedia.org/wiki/ISO_4217 ISO 4217] respectively.
* For each transaction a detailed explanation of the ECI Status code will be added to the transactions XML log file and added alongside the transaction status in the order notes section or the associated order in PrestaShop.
* Dropdown selection for currency in configuration settings screen.

==== Release v1.0-stable (03-Sep-2009) ====
* First submission/release.

