<?php

namespace Bcash\FilamentMoneyField\Infolists\Components;

use Bcash\FilamentMoneyField\Concerns\HasMoneyAttributes;
use Filament\Infolists\Components\TextEntry;

/**
 * Money entry component for Filament infolists.
 *
 * Displays money values stored as integers (cents) in formatted currency.
 *
 * Usage:
 *   MoneyEntry::make('price')
 *       ->label('Price')
 *       ->currency('USD')
 *
 * The value is stored as cents (integer) in the database:
 * - Stored value: 1234
 * - Displayed as: $12.34
 *
 * @see \Bcash\FilamentMoneyField\Forms\Components\MoneyInput
 * @see \Bcash\FilamentMoneyField\Tables\Columns\MoneyColumn
 */
class MoneyEntry extends TextEntry
{
    use HasMoneyAttributes;

    protected bool $showCurrencySymbol = true;

    protected bool $isShortFormat = false;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formatStateUsing(function (MoneyEntry $component, int|string|null $state): string {
            if ($state === null || $state === '') {
                return '';
            }

            $amount = (int) $state;
            $decimals = $component->getDecimals();
            $value = $amount / pow(10, $decimals);

            if ($component->isShortFormat) {
                return $component->formatShort($value);
            }

            $formatter = new \NumberFormatter($component->getLocale(), \NumberFormatter::CURRENCY);
            $result = $formatter->formatCurrency($value, $component->getCurrency()->getCode());

            if (! $component->showCurrencySymbol) {
                $decimalFormatter = new \NumberFormatter($component->getLocale(), \NumberFormatter::DECIMAL);
                $decimalFormatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
                $decimalFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
                $result = $decimalFormatter->format($value);
            }

            return $result;
        });
    }

    /**
     * Format value in short notation.
     */
    protected function formatShort(float $value): string
    {
        $suffixes = ['', 'K', 'M', 'B', 'T'];
        $suffixIndex = 0;

        while (abs($value) >= 1000 && $suffixIndex < count($suffixes) - 1) {
            $value /= 1000;
            $suffixIndex++;
        }

        $decimals = $this->getDecimals();
        $formatter = new \NumberFormatter($this->getLocale(), \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

        $formatted = $formatter->format($value);

        if ($this->showCurrencySymbol) {
            $symbol = $this->getCurrencySymbol();

            return $symbol.$formatted.$suffixes[$suffixIndex];
        }

        return $formatted.$suffixes[$suffixIndex];
    }

    /**
     * Use short format for large numbers.
     */
    public function short(): static
    {
        $this->isShortFormat = true;

        return $this;
    }

    /**
     * Hide the currency symbol.
     */
    public function hideCurrencySymbol(bool $hide = true): static
    {
        $this->showCurrencySymbol = ! $hide;

        return $this;
    }
}
