<?php

namespace Bcash\FilamentMoneyField\Concerns;

use Closure;
use Money\Currencies\ISOCurrencies;
use Money\Currency;

/**
 * Trait providing common money-related attributes for Filament components.
 *
 * Provides currency, locale, and decimal configuration for MoneyInput,
 * MoneyColumn, and MoneyEntry components.
 */
trait HasMoneyAttributes
{
    protected ?Currency $currency = null;

    protected ?string $locale = null;

    protected ?int $decimals = null;

    protected ?int $minValue = null;

    protected ?int $maxValue = null;

    protected ?int $step = null;

    /**
     * Get the currency for this component.
     * Defaults to USD if not set.
     */
    public function getCurrency(): Currency
    {
        return $this->currency ?? new Currency(config('filament-money-field.default_currency', 'USD'));
    }

    /**
     * Get the locale for formatting.
     * Defaults to en_US if not set.
     */
    public function getLocale(): string
    {
        return $this->locale ?? config('filament-money-field.default_locale', 'en_US');
    }

    /**
     * Set the currency for this component.
     */
    public function currency(string|Closure $currencyCode): static
    {
        $code = $this->evaluate($currencyCode);

        $currencies = new ISOCurrencies();
        $currency = new Currency($code);

        if (! $currencies->contains($currency)) {
            throw new \InvalidArgumentException("Invalid currency code: {$code}");
        }

        $this->currency = $currency;

        return $this;
    }

    /**
     * Set the locale for formatting.
     */
    public function locale(string|Closure|null $locale = null): static
    {
        $this->locale = $this->evaluate($locale);

        return $this;
    }

    /**
     * Set the number of decimal places.
     */
    public function decimals(int|Closure $decimals): static
    {
        $this->decimals = $this->evaluate($decimals);

        return $this;
    }

    /**
     * Get the number of decimal places.
     */
    protected function getDecimals(): int
    {
        return $this->decimals ?? (int) config('filament-money-field.decimal_digits', 2);
    }

    /**
     * Get the minimum value in cents.
     */
    public function getMinValue(): ?int
    {
        return $this->minValue;
    }

    /**
     * Get the maximum value in cents.
     */
    public function getMaxValue(): ?int
    {
        return $this->maxValue;
    }

    /**
     * Get the step value.
     */
    public function getStep(): ?int
    {
        return $this->step;
    }

    /**
     * Get the currency symbol for display.
     */
    protected function getCurrencySymbol(): string
    {
        $formatter = new \NumberFormatter($this->getLocale(), \NumberFormatter::CURRENCY);
        $formatter->setTextAttribute(\NumberFormatter::CURRENCY_CODE, $this->getCurrency()->getCode());

        return $formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
    }

    /**
     * Format an amount in cents to a display string.
     *
     * @param int|string|null $amountInCents Amount in minor units (cents)
     */
    protected function formatMoney(int|string|null $amountInCents): string
    {
        if ($amountInCents === null || $amountInCents === '') {
            return '';
        }

        $amount = (int) $amountInCents;
        $decimals = $this->getDecimals();

        // Convert cents to dollars
        $value = $amount / pow(10, $decimals);

        $formatter = new \NumberFormatter($this->getLocale(), \NumberFormatter::DECIMAL);
        $formatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $decimals);
        $formatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);

        return $formatter->format($value);
    }

    /**
     * Parse a display string to cents.
     *
     * @param string|null $value Display value (e.g., "12.34" or "1,234.56")
     * @return int Amount in minor units (cents)
     */
    protected function parseMoney(?string $value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }

        $formatter = new \NumberFormatter($this->getLocale(), \NumberFormatter::DECIMAL);
        $parsed = $formatter->parse($value);

        if ($parsed === false) {
            // Fallback: try to parse as float
            $cleaned = preg_replace('/[^\d.\-]/', '', $value);
            $parsed = (float) $cleaned;
        }

        $decimals = $this->getDecimals();

        // Convert dollars to cents
        return (int) round($parsed * pow(10, $decimals));
    }

    // This should typically be provided by the Filament\Support\Concerns\EvaluatesClosures trait
    abstract protected function evaluate(string|Closure|null $value): mixed;
}
