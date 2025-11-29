<?php

namespace Bcash\FilamentMoneyField;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

/**
 * Service provider for the Filament Money Field package.
 *
 * Registers the package configuration and provides money field components
 * for Filament forms, tables, and infolists.
 *
 * @see \Bcash\FilamentMoneyField\Forms\Components\MoneyInput
 * @see \Bcash\FilamentMoneyField\Tables\Columns\MoneyColumn
 */
class FilamentMoneyFieldServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-money-field';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasTranslations();
    }
}
