<?php

namespace Bcash\FilamentMoneyField\Forms\Components;

use Bcash\FilamentMoneyField\Concerns\HasMoneyAttributes;
use Filament\Forms\Components\TextInput;

/**
 * Money input component for Filament forms.
 *
 * Handles money values stored as integers (cents) in the database.
 * Displays formatted currency values to users and converts input
 * back to cents for storage.
 *
 * Usage:
 *   MoneyInput::make('price')
 *       ->label('Price')
 *       ->currency('USD')
 *       ->required()
 *
 * The value is stored as cents (integer) in the database:
 * - User enters: $12.34
 * - Stored value: 1234
 *
 * @see \Bcash\FilamentMoneyField\Tables\Columns\MoneyColumn
 */
class MoneyInput extends TextInput
{
    use HasMoneyAttributes;

    protected ?string $symbolPlacement = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepare();

        // Format the stored cents value to display format when loading
        $this->formatStateUsing(function (MoneyInput $component, mixed $state): string {
            $this->prepare();

            \Illuminate\Support\Facades\Log::debug('MoneyInput formatStateUsing', [
                'field' => $component->getName(),
                'raw_state' => $state,
                'state_type' => gettype($state),
            ]);

            if ($state === null || $state === '') {
                return '';
            }

            $formatted = $component->formatMoney($state);

            \Illuminate\Support\Facades\Log::debug('MoneyInput formatStateUsing result', [
                'field' => $component->getName(),
                'formatted' => $formatted,
            ]);

            return $formatted;
        });

        // Convert display value back to cents when saving
        // Returns string to match varchar database columns used by MoneyPHP
        $this->dehydrateStateUsing(function (MoneyInput $component, null|int|string $state): ?string {
            \Illuminate\Support\Facades\Log::debug('MoneyInput dehydrateStateUsing', [
                'field' => $component->getName(),
                'input_state' => $state,
                'input_type' => gettype($state),
            ]);

            if ($state === null || $state === '') {
                \Illuminate\Support\Facades\Log::debug('MoneyInput dehydrateStateUsing - returning null (empty state)');
                return null;
            }

            $cents = (string) $component->parseMoney((string) $state);

            \Illuminate\Support\Facades\Log::debug('MoneyInput dehydrateStateUsing result', [
                'field' => $component->getName(),
                'cents' => $cents,
            ]);

            return $cents;
        });
    }

    /**
     * Prepare the component with currency symbol placement.
     */
    protected function prepare(): void
    {
        $symbolPlacement = $this->getSymbolPlacement();
        $getCurrencySymbol = fn (MoneyInput $component): string => $component->getCurrencySymbol();

        match ($symbolPlacement) {
            'before' => $this->prefix($getCurrencySymbol)->suffix(null),
            'after' => $this->suffix($getCurrencySymbol)->prefix(null),
            default => $this->suffix(null)->prefix(null),
        };

        $this->numeric()
            ->inputMode('decimal')
            ->extraInputAttributes(['class' => 'text-right']);
    }

    /**
     * Get the symbol placement setting.
     */
    public function getSymbolPlacement(): string
    {
        return $this->symbolPlacement ?? config('filament-money-field.form_currency_symbol_placement', 'before');
    }

    /**
     * Set the symbol placement.
     *
     * @param string $placement One of: 'before', 'after', 'hidden'
     */
    public function symbolPlacement(string $placement): static
    {
        if (! in_array($placement, ['before', 'after', 'hidden'])) {
            throw new \InvalidArgumentException(
                'Currency symbol placement must be one of: before, after, hidden'
            );
        }

        $this->symbolPlacement = $placement;

        return $this;
    }

    /**
     * Hide the currency symbol.
     */
    public function hideCurrencySymbol(): static
    {
        return $this->symbolPlacement('hidden');
    }
}
