# SwissPayment Changelog

## 0.X.X (2017-XX-XX)

  * Added support for transaction purposes.

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
