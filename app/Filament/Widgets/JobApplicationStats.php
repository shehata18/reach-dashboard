<?php

namespace App\Filament\Widgets;

use App\Models\JobApplication;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class JobApplicationStats extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return request()->routeIs('filament.admin.resources.job-applications.*');
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Applications', JobApplication::count())->description('All job applications')->descriptionIcon('heroicon-m-document-text')->color('gray'),

            Stat::make('Pending Applications', JobApplication::pending()->count())
                ->description('Waiting for review')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Accepted Applications', JobApplication::accepted()->count())
                ->description('Successfully accepted')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Rejected Applications', JobApplication::rejected()->count())
                ->description('Not accepted')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}
