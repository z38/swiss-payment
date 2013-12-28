# SwissPayment

[![Build Status](https://travis-ci.org/z38/swiss-payment.png?branch=master)](https://travis-ci.org/z38/swiss-payment)

**SwissPayment** is a PHP library to generate Swiss pain.001 messages (complies with ISO-20022).

## Installing

- Install [Composer](http://getcomposer.org) and place the executable somewhere in your `$PATH`.

- Add `z38/swiss-payment` to your project's `composer.json`:

```json
{
    "require": {
        "z38/swiss-payment": "dev-master"
    }
}
```

- Install/update your dependencies

```bash
$ cd my_project
$ composer install
```

- And you're good to go!

## Usage

Have a look at `Z38\SwissPayment\Tests\Message\CustomerCreditTransferTest` on how to create a pain.001 XML file.

## Caveats

- At the moment only payment type 3 is supported (for details consult chapter 2.2 of the Implementation Guidelines)
- The whole project is still under development and therefore BC breaks can occur. Please contact me if you need a stable code base.

## Contributing

If you want to get your hands dirty, great! Here's a couple of steps/guidelines:

- Fork this repository
- Add your changes & tests for those changes (in `tests/`).
- Remember to stick to the existing code style as best as possible. When in doubt, follow `PSR-2`.
- Send me a pull request!

If you don't want to go through all this, but still found something wrong or missing, please
let me know, and/or **open a new issue report** so that I or others may take care of it.

## Further Resources

- [www.iso-payments.ch](http://www.iso-payments.ch) General website about the Swiss recommendations regarding ISO 20022
- [Swiss Business Rules for Customer-Bank Messages](http://www.six-interbank-clearing.com/dam/downloads/en/standardization/iso/swiss_recommendations/business_rules/standardization_isopayments_ch_business_rules.pdf)
- [Swiss Implementation Guidelines for pain.001 and pain.002 Messages](http://www.six-interbank-clearing.com/dam/downloads/en/standardization/iso/swiss_recommendations/implementation_guidelines_ct/standardization_isopayments_iso_20022_ch_implementation_guidelines_ct.pdf)
