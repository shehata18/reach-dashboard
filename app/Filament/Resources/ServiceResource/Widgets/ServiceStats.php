<?php

namespace App\Filament\Resources\ServiceResource\Widgets;

use App\Models\Service;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServiceStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Services', Service::count()), Stat::make('Active Services', Service::where('is_active', true)->count()), Stat::make('Inactive Services', Service::where('is_active', false)->count())];
    }
}
