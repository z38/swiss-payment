# SwissPayment Changelog

## 0.8.0 (2021-xx-yy)

  * Renamed Mixed to MixedMoney (reserved word as of PHP 7).
  * Dropped support for PHP 5.6.

## 0.7.0 (2019-01-05)

  * Support creation of IIDs from Lichtenstein IBANs.
  * Drop support for PHP 5.4 and 5.5.

## 0.6.0 (2018-03-25)

  * Enforce stricter validation of inputs.
  * Escape all inputs.
  * Added support for sanitization of user inputs.
  * Added support for 19 new currencies.
  * Set charge bearer of SEPA payments.

## 0.5.0 (2017-03-07)

  * Added support for transaction purposes.
  * Improved validation of postal account numbers.

## 0.4.1 (2016-08-31)

  * Write IID without leading zeroes (for legacy systems)

## 0.4.0 (2016-08-28)

  * Added support for general account identifiers.
  * Added support for intermediary transaction agents.
  * Allow transfers in Euro for payment types 2 and 3.
  * Deprecate `Z38\SwissPayment\BC` in favor of `Z38\SwissPayment\IID`.
  * Added support for ISR payments (type 1).
  * Deprecate setting the creditor agent BIC of SEPA payments.

## 0.3.0 (2016-01-01)

  * Added support for foreign and SEPA payments.
  * Added support for GBP, USD and JPY.
  * Renamed `Z38\SwissPayment\PostalAddress` to `Z38\SwissPayment\StructuredPostalAddress`.

## 0.2.0 (2015-03-09)

  * Added support for domestic BC numbers.
  * Improved documentation.

## 0.1.0 (2014-09-06)

  * Initial release.
