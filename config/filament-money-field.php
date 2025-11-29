<?php

return [
    /*
    |---------------------------------------------------------------------------
    | Default Currency
    |---------------------------------------------------------------------------
    |
    | The default currency code to use if not specified on the field.
    | Uses ISO 4217 currency codes (e.g., USD, EUR, GBP).
    |
    */
    'default_currency' => env('MONEY_DEFAULT_CURRENCY', 'USD'),

    /*
    |---------------------------------------------------------------------------
    | Default Locale
    |---------------------------------------------------------------------------
    |
    | The default locale for number formatting.
    | Uses ICU locale format (e.g., en_US, en_GB, de_DE).
    |
    */
    'default_locale' => env('MONEY_DEFAULT_LOCALE', 'en_US'),

    /*
    |---------------------------------------------------------------------------
    | Decimal Digits
    |---------------------------------------------------------------------------
    |
    | The number of decimal places to display.
    | Most currencies use 2 decimal places.
    |
    */
    'decimal_digits' => env('MONEY_DECIMAL_DIGITS', 2),

    /*
    |---------------------------------------------------------------------------
    | Currency Symbol Placement
    |---------------------------------------------------------------------------
    |
    | Where the currency symbol should appear on form fields.
    | Options: 'before' (prefix), 'after' (suffix), 'hidden'
    |
    | Note: In most non-English speaking European countries,
    | the currency symbol is after the amount (e.g., "10 €")
    |
    */
    'form_currency_symbol_placement' => env('MONEY_SYMBOL_PLACEMENT', 'before'),
];
