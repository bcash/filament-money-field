# Filament Money Field

Money field components for Filament 4 powered by MoneyPHP.

This is a fork of [pelmered/filament-money-field](https://github.com/pelmered/filament-money-field), simplified to remove the `larapara` dependency and work directly with MoneyPHP.

## Features

- **MoneyInput**: Form input that stores values as integers (cents) in the database
- **MoneyColumn**: Table column that displays cents as formatted currency
- **MoneyEntry**: Infolist entry that displays cents as formatted currency
- Configurable currency and locale
- Currency symbol placement (before, after, or hidden)

## Installation

Add the repository to your `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/bcash/filament-money-field"
        }
    ]
}
```

Then require the package:

```bash
composer require bcash/filament-money-field:dev-filament-4
```

## Usage

### Form Input

```php
use Bcash\FilamentMoneyField\Forms\Components\MoneyInput;

MoneyInput::make('price')
    ->label('Price')
    ->currency('USD')  // Optional, defaults to USD
    ->locale('en_US')  // Optional, defaults to en_US
    ->required();
```

The value is stored as cents (integer) in the database:
- User enters: `$12.34`
- Stored value: `1234`

### Table Column

```php
use Bcash\FilamentMoneyField\Tables\Columns\MoneyColumn;

MoneyColumn::make('price')
    ->label('Price')
    ->currency('USD')
    ->sortable();

// Short format for large numbers
MoneyColumn::make('revenue')
    ->short();  // Displays $1.23M instead of $1,234,567.89

// Hide currency symbol
MoneyColumn::make('price')
    ->hideCurrencySymbol();
```

### Infolist Entry

```php
use Bcash\FilamentMoneyField\Infolists\Components\MoneyEntry;

MoneyEntry::make('price')
    ->label('Price')
    ->currency('USD');
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=filament-money-field-config
```

Available options:

```php
return [
    'default_currency' => env('MONEY_DEFAULT_CURRENCY', 'USD'),
    'default_locale' => env('MONEY_DEFAULT_LOCALE', 'en_US'),
    'decimal_digits' => env('MONEY_DECIMAL_DIGITS', 2),
    'form_currency_symbol_placement' => env('MONEY_SYMBOL_PLACEMENT', 'before'),
];
```

## Database Storage

Values are stored as integers representing the smallest currency unit (cents for USD):

| Display Value | Stored Value |
|--------------|--------------|
| $12.34       | 1234         |
| $1,000.00    | 100000       |
| $0.99        | 99           |

## License

MIT License - see [LICENSE](LICENSE) for details.
